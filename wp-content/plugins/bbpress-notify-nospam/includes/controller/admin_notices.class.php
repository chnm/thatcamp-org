<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Controls Admin Notices
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_Controller_Admin_Notices extends bbPress_Notify_noSpam {

	/**
	 * Holds our notices
	 * @var array
	 */
	protected $notices = array();
	
	
	/**
	 * All available messages 
	 * @var array
	 */
	private static $msg_pool;
	
	
	/**
	 * The query string element that holds the messages
	 * @var unknown
	 */
	private $query_element;
	
	
	#############################
	
	
	public function __construct( $params=array() )
	{
		if ( ! parent::is_admin() )
			return;
		
		$this->query_element = get_parent_class( $this );
		
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		
		// Captures redirects after posts like when saving metaboxes
// 		add_filter( 'redirect_post_location', array( $this, 'capture_redirect' ) );
		add_filter( 'wp_redirect', array( $this, 'capture_redirect' ) );
		
		if ( isset( $_GET[$this->query_element] ) )
		{
			add_filter( 'post_updated_messages', array( $this, 'show_notices' ) );
		}
		
		add_filter( $this->query_element . '_notice_pool', array( $this, 'get_notice_pool' ) );
		
		// Dismiss notice
		add_action('wp_ajax_' . 'bbpnns-notice-handler', array( $this, 'handle_notice_dismissal' ) );
	}
	

	/**
	 * Wrapper for _set_msg
	 * @param string $code
	 * @param bool $die_on_error
	 */
	public function set_notice( $code, $die_on_error=false )
	{
		$msg = $this->get_message( $code );
		
		if ( true === $die_on_error )
		{
			wp_die( $msg->msg );
		}
		else
		{
			$this->_set_msg( $code );
		}
	}
	
	
	/**
	 * Internal Notice setter. $code is added to settings API message div
	 * @param string $msg
	 * @param boolean $is_error
	 * @param boolean $is_nag
	 * @param string $code
	 */
	private function _set_msg( $code )
	{
		global $pagenow;
		
		// Maybe use Settings API
		if ( ( 'options.php' === $pagenow ||  
		       'options-general.php' === $pagenow ) /* && 
		       isset( $_GET['page'] ) && $this->domain === $_GET['page'] */ )
		{
			$msg = $this->get_message( $code );
			
			// Maybe defer setting the error
			if ( ! function_exists( 'add_settings_error' ) )
			{
				add_action( 'admin_notices', function() use ($code, $msg){ 
					add_settings_error( $this->settings_name, $code, $msg->msg, $msg->type );
				}, -100);
			}
			else {
				add_settings_error( $this->settings_name, $code, $msg->msg, $msg->type );
			}
		}
		// Make sure we get unique notices
		$this->notices[$code] = true;
	}
	
	
	/**
	 * Displays any cached notices
	 */
	public function show_notices( $messages=array() )
	{
		if ( isset( $_GET[$this->query_element] ) )
		{
			$keys = explode( ',', trim( $_GET[$this->query_element] ) );
			$this->notices = array_combine( $keys, $keys );
		}
		
		foreach ( array_keys( $this->notices ) as $code )
		{
			$msg         = $this->get_message( $code );
			$dismissable = isset( $msg->is_dismissible ) && $msg->is_dismissible ? ' is-dismissible' : '';
			$nonce       = wp_create_nonce( $code );
			$div         = sprintf( '<div id="%s" class="%s" data-nonce="%s"><p>%s</p></div>', $code, $msg->type . $dismissable , $nonce, $msg->msg );
			
			if ( doing_filter( 'post_updated_messages' ) )
			{
				$messages[] = $div;
			}
			else 
			{
				echo $div;
				unset( $this->notices[$code] );
			}
		}
		
		return $messages;
	}
	
	
	
	/**
	 * Clears notices
	 */
	public function clear_notices()
	{
		$this->notices = array();
	}
	
	
	/**
	 * Keeps state between redirects 
	 * @param string $location
	 * @return string
	 */
	public function capture_redirect( $location )
	{
		if ( ! $this->has_notices() )
			return esc_url_raw( remove_query_arg( $this->query_element, $location ) );

		
		$keys = join( ',', array_keys( $this->notices ) );
		return esc_url_raw( add_query_arg( $this->query_element, $keys, $location ) );
	}
	
	
	/**
	 * Access to the message pool
	 * @param string $code
	 * @return multitype:StdClass
	 */
	public function get_message( $code )
	{
		if ( ! isset( self::$msg_pool ) )
		{
			self::$msg_pool = apply_filters( $this->query_element . '_notice_pool', array() );
		}
		
		if ( ! isset( self::$msg_pool[$code] ) )
		{
			wp_die( sprintf( __( 'Invalid message code %s', 'bbPress_Notify_noSpam' ) , $code ) );
		}
		
		return self::$msg_pool[$code];
		
	}
	
	
	/**
	 * Allows checking if there are notices 
	 * @return boolean
	 */
	public function has_notices()
	{
		return ! empty( $this->notices );
	}
	
	
	
	/**
	 * Dismiss a notice
	 */
	public function handle_notice_dismissal()
	{
		if ( isset( $_POST['notice_id'] ) )
		{
			$notice_id = sanitize_text_field( $_POST['notice_id'] );
		}
	
		if ( ! $notice_id || ! isset( $this->notices[$notice_id] ) )
		{
			wp_die( 'I don\'t recognize that notice!' );
		}
	
		if ( check_ajax_referer( 'bbpnns-notice-nonce_' . $notice_id, 'nonce' ) )
		{
			$dismissed = get_option( 'bbpnns_dismissed_admin_notices', array() );
			$dismissed[$notice_id] = true;
				
			update_option( 'bbpnns_dismissed_admin_notices', $dismissed );
		}
	
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		{
			exit(0);
		}
	}
	
	
	/**
	 * Returns array of common notice objects
	 * @param array $notices
	 * @return array
	 */
	public function get_notice_pool( $notices=array() )
	{
		// Not all classes get reloaded after wp_redirect, so add those messages here.
		return array_merge( $notices, array( 
				'invalid-postid' => ( object ) array( 'type' => 'error', 'msg' => __( 'Invalid post_id.', 'bbPress_Notify_noSpam' )  ),
				'bad-params'     => ( object ) array( 'is_dismissible' => true, 'type' => 'notice notice-warning', 'msg' => __( 'Invalid parameter type.', 'bbPress_Notify_noSpam' ) ),
				'old-notify-deactivated' => ( object ) array( 'type' => 'error', 'msg' => __( 'The old bbpress-notify plugin has been deactivated in favor of bbpnns.', 'bbPress_Notify_noSpam' )  ),
				'bbpnns_v2_conversion_needed' => ( object ) array( 'type' => 'error', 'msg' => __( '<div><strong>We need to convert your bbpnns v1.x data into the v2.x format.</strong> 
						<a href="#" id="bbpnns-convert-v1-to-v2" class="button button-primary">Run Update</a>
						<div class="bbpnns_spinner"></div></div>', 'bbPress_Notify_noSpam' ), 'is_dismissible' => true  ),
				
				
		 ) );
	}
	
}

/* End of file admin_notices.class.php */
/* Location: bbpress-notify-nospam/includes/controller/admin_notices.class.php */

