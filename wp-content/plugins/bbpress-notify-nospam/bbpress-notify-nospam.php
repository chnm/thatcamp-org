<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/*
* Plugin Name: bbPress Notify (No-Spam)
* Description: Sends email notifications upon topic/reply creation, as long as it's not flagged as spam. If you like this plugin, <a href="https://wordpress.org/support/view/plugin-reviews/bbpress-notify-nospam#postform" target="_new">help share the trust and rate it!</a> 
* Version:	   2.8.1
* Author: 	   <a href="http://usestrict.net" target="_new">Vinny Alves (UseStrict Consulting)</a>
* License:     GNU General Public License, v2 ( or newer )
* License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
* Domain Path: /lang
* Text Domain: bbPress_Notify_noSpam
* 
* 
* Based on MVC Starter Plugin v1.2.4 by UseStrict Consulting
*
* Copyright (C) 2012-2020 usestrict.net, released under the GNU General Public License.
*/

class bbPress_Notify_noSpam 
{
	const VERSION = '2.8.1';
	
	/**
	 * The singletons
	 * @var array
	 */
	public static $instances = array();
	
	/**
	 * The domain to be used for l10n. Defaults to the parent class name
	 * @var string
	 */
	public $domain = __CLASS__;
	
	/**
	 * Holds the environment object once set_env() is called
	 * @var object
	 */
	protected static $env;
	
	/**
	 * The name of the key in wp_options table that holds our settings
	 * @var string
	 */
	protected $settings_name = __CLASS__;
	
	/**
	 * Holds library singletons
	 * @var object
	 */
	protected $libs;
	
	#########################
	
	
	public function __construct( $params=array() )
	{
		$this->set_env();
		
		if ( self::is_admin() )
		{
			$notices = $this->load_lib( 'controller/admin_notices' );
			$this->load_lib( 'controller/ajax' );
			
			register_activation_hook( __FILE__, array( $this, 'do_activation') );
			register_deactivation_hook( __FILE__, array( $this, 'do_deactivation') );
		}
		else 
		{
			// Stop timeouts if doing cron.
			if ( defined('DOING_CRON') && DOING_CRON )
			{
				set_time_limit(0);
			}
		}
		
		// Always Load the settings
		$this->load_lib( 'controller/settings' );

		// Load this first so it can play nicely with Moderation plugins.
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'plugins_loaded', [$this, 'load_textdomain'] );
	}
	
	public function load_textdomain()
	{
	    load_plugin_textdomain( 'bbPress_Notify_noSpam', false, dirname( untrailingslashit( plugin_basename( __FILE__ ) ) ) . '/lang' );
	}
	
	public function init()
	{
	    $this->load_lib( 'controller/login' );
		$this->load_lib( 'controller/common_core' );
		
		if ( self::is_admin() )
		{
			$did_v2_conversion = get_option( 'bbpnns_v2_conversion_complete', false );
			
			if ( false === $did_v2_conversion )
			{
				$has_v1_data = get_option( 'bbpress_notify_newtopic_email_subject', false );
				
				if ( false !== $has_v1_data )
				{
					// Kick off the converter
					$converter = $this->load_lib( 'helper/converter' );
					
					/**
					 * Allow forcing the conversion via query string.
					 * @since 2.1.3
					 */
					if ( isset( $_GET['bbpnns_force_convert'] ) && $_GET['bbpnns_force_convert'] && current_user_can('manage_options') )
					{
						$status = $converter->do_db_upgrade();
						
						if ( true === $status )
						{
							wp_die( sprintf( __( 'bbPress Notify (No-Spam) 1.x -> 2.x conversion was successful. Click <a href="%s">here</a> to go back to your WP Admin.', 'bbPress_Notify_noSpam' ), esc_attr( admin_url('/') ) ), 200 );
						}
					}
					else 
					{
						$notices = $this->load_lib( 'controller/admin_notices' );
						$notices->set_notice( 'bbpnns_v2_conversion_needed' );
					}
				}
			}
			
			$this->load_lib( 'controller/admin_core' );
		}
	}
	
	
	/**
	 * Backwards compatibility
	 */
	public function send_notification( $recipients, $subject, $body, $type='', $post_id='', $forum_id='' )
	{
		return $this->load_lib('controller/common_core')->send_notification( $recipients, $subject, $body, $type, $post_id, $forum_id );
	}
	
	
	public function get_topic_post_type()
	{
		static $topic_post_type;
		if ( ! $topic_post_type )
		{
			$topic_post_type = bbp_get_topic_post_type(); 
		}
		
		return $topic_post_type;
	}
	
	public function get_reply_post_type()
	{
		static $reply_post_type;
		if ( ! $reply_post_type )
		{
			$reply_post_type = bbp_get_reply_post_type();
		}
		
		return $reply_post_type;
	}
	
	/**
	 * Activation method for register_activation_hook
	 */
	public function do_activation()
	{
		# TODO: What happens if doing a bulk activation of bbpress and bbpnns?
		/* Checks whether bbPress is active because we need it. If bbPress isn't active, we are going to disable ourself */
		if( ! class_exists( 'bbPress' ) )
		{
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Sorry, you need to activate bbPress first.', 'bbpnns' ) );
		}
	}
	
	
	/**
	 * Deactivation method for register_deactivation_hook
	 */
	public function do_deactivation()
	{
		return;
	}
	
	
	/**
	 * The singleton method
	 * @return object
	 */
	public static function bootstrap( $params=array() ) 
	{
		// Make sure bbPress is still installed and avoid race conditions
		if ( ! class_exists( 'bbPress' ) )
		{
			if ( 'plugins_loaded' !== current_filter() )
			{
				add_action( 'plugins_loaded', array( 'bbPress_Notify_NoSpam', 'bootstrap' ), 100000 );
			}
			else
			{
				add_action( 'admin_notices', array( 'bbPress_Notify_NoSpam', 'missing_bbpress_notice' ) );
			}
				
			return false;
		}
		
		$class = function_exists( 'get_called_class' ) ? get_called_class() : self::get_called_class();  
		
		if ( ! isset( self::$instances[$class] ) ) {
			self::$instances[$class] = new $class( $params );
        }
		
		return self::$instances[$class];
	}
	
	
	/**
	 * @since 1.5.4
	 */
	public static function missing_bbpress_notice()
	{
		?>
		<div class="error">
			<p>
				<?php _e( '<strong>bbPress Notify (No-Spam)</strong> could not find an active bbPress plugin. It will not load until bbPress is installed and active.' ); ?>
			</p>
		</div>
		<?php 
	}
	
	
	
	/**
	 * Workaround for PHP < 5.3 that doesn't have get_called_class
	 * @return boolean
	 */
	private static function get_called_class()
	{
		$bt = debug_backtrace();
	
		if ( is_array( $bt[2]['args'] ) && 2 === count( $bt[2]['args'] ) )
		{
			return $bt[2]['args'][0][0];
		}
	
		return $bt[1]['class'];
	}
	
	
	/**
	 * Sets some needed variables
	 */
	protected function set_env()
	{
		$root = trailingslashit( dirname( __FILE__ ) );
		$plugin_url = trailingslashit( plugins_url( 'assets', __FILE__ ) );
	
		self::$env = ( object ) array( 
				'root_dir' => $root,
				'inc_dir'  => $root . 'includes/',
				'tmpl_dir' => $root . 'includes/view/templates/',
				'js_url'   => $plugin_url . 'js/',
				'css_url'  => $plugin_url . 'css/',
				'img_url'  => $plugin_url . 'img/',
				'plugin_file' => __FILE__,
		 );
	}
	
	
	/**
	 * Gets the env vars we set earlier
	 * @return StdClass
	 */
	protected function get_env()
	{
		if ( ! isset( self::$env ) )
			$this->set_env();
	
		return self::$env;
	}
	
	
	/**
	 * Wrapper for requiring libraries
	 * @param string $name
	 * @param array $params
	 * @param bool $force_reload
	 * @return object
	 */
	protected function load_lib( $name, $params = array(), $force_reload = false )
	{
		if ( isset( $this->libs ) && isset( $this->libs->$name ) && false === $force_reload )
			return $this->libs->$name;
	
		$filename = $this->get_env()->inc_dir . $name . '.class.php';
		if ( ! file_exists( $filename ) )
		{
			$bt = debug_backtrace();
			wp_die( 'Cannot find Lib file: ' . $filename. ' Debug:<pre>' . print_r( array( 'file' => $bt[0]['file'], 'line' => $bt[0]['line'], 'method' => $bt[0]['function'] ),1 ) . '</pre>' );
		}
	
		require_once( $filename );
	
		$classname = __CLASS__ . '_' . join( '_', explode( '/', $name ) );
	
		// Only require abstraction classes
		if ( false !== strstr( $filename, 'abstract/' ) )
			return;
	
		if ( ! isset( $this->libs ) )
			$this->libs = ( object ) array();
	
		if ( false === $force_reload && method_exists( $classname, 'bootstrap' ) && is_callable( array( $classname, 'bootstrap' ) ) )
			$this->libs->$name = call_user_func( array( $classname, 'bootstrap' ), $params );
		else
			$this->libs->$name = new $classname( $params );
	
		return $this->libs->$name;
	}
	
	/**
	 * Bulk class loading.
	 * @param string $dir
	 * @return array of objects
	 */
	protected function load_all( $dir )
	{
		$inc_dir = $this->get_env()->inc_dir;
	
		if ( false === ( strstr( $dir, $inc_dir ) ) )
			$dir = $inc_dir . '/' . $dir;
		
		$dir = str_replace( '//', '/', $dir );
		$dir = preg_replace( ',/$,', '', $dir );
			
		$loaded = array();
	
		foreach ( glob( $dir . '/*.class.php' ) as $file )
		{
			preg_match( '|/includes/( .* )+?\.class\.php|', $file, $matches );
				
			$loaded[$matches[1]] = $this->load_lib( $matches[1] );
		}
	
		return $loaded;
	}
	
	
	/**
	 * Renders a template
	 * @param string $name
	 * @param array $stash
	 * @param bool $debug
	 */
	public function render_template( $name, $stash=array(), $debug=false )
	{
		$env = $this->get_env();
	
		if ( '.tmpl.php' !== substr( $name,-9 ) )
			$name .= '.tmpl.php';
	
		if ( ! file_exists( $env->tmpl_dir . $name ) )
			wp_die( 'Bad template request: ' . $env->tmpl_dir . $name );
	
		$stash = ( object ) $stash;
		
		if ( true === $debug )
			echo "$env->tmpl_dir/$name";
	
			include ( $env->tmpl_dir . $name );
	}
	
	
	/**
	 * @since 0.1
	 * @desc Custom is_admin method for testing
	 */
	public static function is_admin()
	{
		if ( has_filter( __CLASS__ . '_is_admin' ) )
			return apply_filters( __CLASS__ . '_is_admin', false );
		else
			return is_admin();
	}
	
	
	/**
	 * Logging
	 * @param string $msg
	 */
	public function log_msg( $msg )
	{
		error_log( '[' . date('d/m/Y H:i:s') . '] ' . print_r( $msg ,1 ) . PHP_EOL, 3, dirname( __FILE__ ) . '/log.txt' );
	}
}

// Kick off the plugin
$bbPress_Notify_noSpam = bbPress_Notify_noSpam::bootstrap();


/* End of file bbpress-notify-nospam.php */
/* Location: bbpress-notify-nospam/bbpress-notify-nospam.php */
