<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Controls settings display
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_View_Settings extends bbPress_Notify_noSpam {

	private $pagehook;
	
	public function __construct()
	{
		// We only allow instantiation in admin
		if ( ! parent::is_admin() )
			return;
		
		$this->enqueue_tabs();
		
		add_filter( 'bbpnns_settings_registered_tabs', array( $this, 'get_nav_tabs' ), 1, 1 );
// 		add_filter( 'bbpnns_settings_registered_addons', array( $this, 'get_registered_addons' ), 1, 1 );
	}
	
	/**
	 * Loads our own tabs
	 */
	public function enqueue_tabs()
	{
		$count = 1;
		foreach ( array( 'general' => __( 'General', 'bbPress_Notify_noSpam' ) , 
				         'topics'  => __( 'Topics', 'bbPress_Notify_noSpam' ) , 
						 'replies' => __( 'Replies', 'bbPress_Notify_noSpam' ) , 
// 						 'addons'  => __( 'Add-ons', 'bbPress_Notify_noSpam' ) ,
						 'support' => __( 'Support', 'bbPress_Notify_noSpam' ) ,
				
				) as $tab => $text )
		{
			$this->nav_bar[$tab] = $text;
			add_action( 'bbpnns_settings_nav_' . $tab, array( $this, 'render_tab' ), ($count+=10), 3 );
		}
	}
	
	/**
	 * Fetches our registered tabs
	 * @param array $tabs
	 * @return array
	 */
	public function get_nav_tabs( $tabs=array() )
	{
		return $this->nav_bar + $tabs;
	}
	
	/**
	 * Renders the tab <a> tag
	 * @param object $stash
	 * @param string $tab
	 * @param string $text
	 */
	public function render_tab( $stash, $tab, $text )
	{
		?>
		<a class="nav-tab <?php echo $tab === $stash->active_tab ? 'nav-tab-active' : ''; ?>"  rel="bbpnns_" . <?php echo $tab; ?> href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->domain . '&tab=' . $tab ) ); ?>"><?php echo $text ?></a>
		<?php
		
		if ( $tab === $stash->active_tab )
		{
			$this->add_meta_box( $stash->active_tab, $stash );
		}
	}
	
	
	public function add_admin_css()
	{
		wp_enqueue_style( $this->domain . '-settings-admin', $this->get_env()->css_url . 'plugin_settings.css', array(), self::VERSION );
	}
	
	public function add_admin_js()
	{
		wp_enqueue_script( $this->domain . '-settings-admin', $this->get_env()->js_url . 'plugin_settings.js', array( 'jquery' ), self::VERSION );
	}
	
	
	public function show_admin()
	{
		// Get stash items
		$stash = array( 'settings' => apply_filters( $this->domain . '_settings', array() ) );
		
		$stash['active_tab']  = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
		$stash['sidebar']     = '';
		$stash['pagehook']    = $this->pagehook = apply_filters('bbpnns_settings_pagehook', null);
		$stash['has_sidebar'] = false; // start out as false. hooks may change this later.
		
		// Use bbpnns_settings_registered_tabs filter and bbpnns_settings_nav_$tab to add more elements to the screen
		$this->render_template( 'plugin_settings', $stash );
	}
	
	public function show_addons_page()
	{
		$stash = array( 'settings' => apply_filters( $this->domain . '_settings', array() ) );
		$stash['addons'] = $this->load_lib('dal/addons_dao')->get_products();
		
		$this->render_template( 'addons_page', $stash );
	}
	
	
	public function add_meta_box( $tab, $stash )
	{
		$id = $this->pagehook . '-' . $tab;
		
		switch( $tab )
		{
			case 'general':
				$title = __( 'Global Settings', 'bbPress_Notify_noSpam' ) ;
				break;
			case 'topics':
				$title = __( 'Topics Settings', 'bbPress_Notify_noSpam' ) ;
				break;
			case 'replies':
				$title = __( 'Replies Settings', 'bbPress_Notify_noSpam' ) ;
				break;
			case 'support':
				$title = __( 'Support Info', 'bbPress_Notify_noSpam' ) ;
				break;
// 			case 'addons':
// 				$title = __( 'Add-ons', 'bbPress_Notify_noSpam' ) ;
// 				break;
			default:
		}
		
		$warnings = apply_filters( 'bbpnns-warnings', array() );
		if ( $warnings )
		{
			$stash->has_sidebar = true;	
			$stash->warnings    = $warnings;
		}
		
		add_meta_box( $id, $title, array( $this, "render_{$tab}_box" ),
					  $id, 'normal', 'core' );
	}
	
	/**
	 * Renders the General Box
	 * $stash param comes from do_meta_boxes called in the settings body
	 */
	public function render_general_box( $stash )
	{
		if ( isset( $stash->has_sidebar ) && $stash->has_sidebar )
		{
			ob_start();
			
			$this->render_template( 'settings/global_sidebar', $stash );
			
			$stash->sidebar = ob_get_clean();
		}
		
		$this->render_template( 'settings/general_body', $stash );
	}
	
	/**
	 * Renders the Topics Box
	 * $stash param comes from do_meta_boxes called in the settings body
	 */
	public function render_topics_box( $stash )
	{
		wp_enqueue_script( 'bbpnns-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery' ), $ver=false, $footer=true );
		wp_enqueue_style( 'bbpnns-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', array(), $ver=false );
		
		if ( isset( $stash->has_sidebar ) && $stash->has_sidebar )
		{
			ob_start();
			
			$this->render_template( 'settings/global_sidebar', $stash );
			
			$stash->sidebar = ob_get_clean();
		}
		
		$this->render_template( 'settings/topics_body', $stash );
	}
	
	
	/**
	 * Renders the Replies Box
	 * $stash param comes from do_meta_boxes called in the settings body
	 */
	public function render_replies_box( $stash )
	{
		wp_enqueue_script( 'bbpnns-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery' ), $ver=false, $footer=true );
		wp_enqueue_style( 'bbpnns-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', array(), $ver=false );
		
		if ( isset( $stash->has_sidebar ) && $stash->has_sidebar )
		{
			ob_start();
		
			$this->render_template( 'settings/global_sidebar', $stash );
		
			$stash->sidebar = ob_get_clean();
		}
		
		$this->render_template( 'settings/replies_body', $stash );
	}
	
	
	/**
	 * Renders the Support Box
	 * $stash param comes from do_meta_boxes called in the settings body
	 */
	public function render_support_box( $stash )
	{
	    wp_enqueue_script( 'bbpnns-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery' ), $ver=false, $footer=true );
	    wp_enqueue_style( 'bbpnns-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', array(), $ver=false );
	    
		if ( isset( $stash->has_sidebar ) && $stash->has_sidebar ) 
		{
			ob_start();
		
			$this->render_template( 'settings/global_sidebar', $stash );
		
			$stash->sidebar = ob_get_clean();
		}
		
		$stash->support_vars = apply_filters( 'bbpnns_support_info', $this->get_support_info() );
		
		$this->render_template( 'settings/support/support_box', $stash );
		$this->render_template( 'settings/support/dry_run_box', $stash );
	}
	
	
	
	private function get_support_info()
	{
		$settings = apply_filters( 'bbPress_Notify_noSpam_settings', array() );
		$theme    = wp_get_theme();
		$plugins  = get_plugins();
		$active_plugins = array();
		foreach ( get_option( 'active_plugins' ) as $ap )
		{
			$active_plugins[$ap] = $plugins[$ap]['Version'];	
		}
		
		// Settings
		// Active Plugins
		// Active Theme
		$info = array( 
				'BBPNNS Settings' => $settings->as_array(),
				'Active Plugins'  => $active_plugins,
				'Network Active Plugins' => get_site_option('active_sitewide_plugins'),
				'Active Theme'    => array( 'Theme Name' => $theme->name, 'Theme Version' => $theme->version, 'Theme URI' => $theme->ThemeURI ),
		 );

		return $info;
	}
}

/* End of file settings.class.php */
/* Location: bbpress-notify-nospam/includes/view/settings.class.php */
