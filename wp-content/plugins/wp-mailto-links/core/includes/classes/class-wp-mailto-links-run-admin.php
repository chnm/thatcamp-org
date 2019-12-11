<?php

/**
 * Class WP_Mailto_Links_Run
 *
 * Thats where we bring the plugin to life
 *
 * @since 3.0.0
 * @package WPMT
 * @author Ironikus <info@ironikus.com>
 */

class WP_Mailto_Links_Run{

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_name;

	/**
	 * The main page title for our admin page
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_title;

	/**
	 * The page hook itself for registering the meta boxes
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $pagehook;

	/**
	 * Our WP_Mailto_Links_Run constructor.
	 */
	function __construct(){
		$this->page_name    = WPMT()->settings->get_page_name();
		$this->page_title   = WPMT()->settings->get_page_title();
		$this->settings_key = WPMT()->settings->get_settings_key();
		$this->display_notices = array();
		$this->add_hooks();
	}

	/**
	 * Define all of our necessary hooks
	 */
	private function add_hooks(){

		add_action( 'plugin_action_links_' . WPMT_PLUGIN_BASE, array($this, 'plugin_action_links') );
		add_action( 'admin_enqueue_scripts',    array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_user_submenu' ), 150 );

	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->page_name ), WPMT()->helpers->translate('Settings', 'plugin-page') );

		array_unshift( $links, $settings_link );

		$links['visit_us'] = sprintf( '<a href="%s" target="_blank" style="font-weight:700;color:#f1592a;">%s</a>', 'https://ironikus.com/?utm_source=wp-mailto-links&utm_medium=plugin-overview-website-button&utm_campaign=WP%20Mailto%20Links', WPMT()->helpers->translate('Visit us', 'plugin-page') );

		return $links;
	}

	/**
	 * ######################
	 * ###
	 * #### SCRIPTS & STYLES
	 * ###
	 * ######################
	 */

	/**
	 * Register all necessary scripts and styles
	 *
	 * @since    3.0.0
	 */
	public function enqueue_scripts_and_styles() {
		if( WPMT()->helpers->is_page( $this->page_name ) ) {
			$js_version  = date( "ymd-Gis", filemtime( WPMT_PLUGIN_DIR . 'core/includes/assets/js/custom-admin.js' ));
			$css_version = date( "ymd-Gis", filemtime( WPMT_PLUGIN_DIR . 'core/includes/assets/css/style-admin.css' ));


			wp_enqueue_script( 'wpmt-admin-scripts', WPMT_PLUGIN_URL . 'core/includes/assets/js/custom-admin.js', array( 'jquery' ), $js_version, true );
			wp_register_style( 'wpmt-css-backend',    WPMT_PLUGIN_URL . 'core/includes/assets/css/style-admin.css', false, $css_version );
			wp_enqueue_style ( 'wpmt-css-backend' );
		}
	}

	/**
	 * ######################
	 * ###
	 * #### MENU TEMPLATE ITEMS
	 * ###
	 * ######################
	 */

	/**
	 * Add our custom admin user page
	 */
	public function add_user_submenu(){

		if( (string) WPMT()->settings->get_setting( 'own_admin_menu', true ) !== '1' ){
			$this->pagehook = add_submenu_page( 'options-general.php', WPMT()->helpers->translate( $this->page_title, 'admin-add-submenu-page-title' ), WPMT()->helpers->translate( $this->page_title, 'admin-add-submenu-page-site-title' ), WPMT()->settings->get_admin_cap( 'admin-add-submenu-page-item' ), $this->page_name, array( $this, 'render_admin_menu_page' ) );
		} else {
			$this->pagehook = add_menu_page( WPMT()->helpers->translate( $this->page_title, 'admin-add-menu-page-title' ), WPMT()->helpers->translate( $this->page_title, 'admin-add-menu-page-site-title' ), WPMT()->settings->get_admin_cap( 'admin-add-menu-page-item' ), $this->page_name, array( $this, 'render_admin_menu_page' ), 'dashicons-email' );
		}
		
		add_action( 'load-' . $this->pagehook, array( $this, 'add_help_tabs' ) );
	}

	/**
	 * Render the admin submenu page
	 *
	 * You need the specified capability to edit it.
	 */
	public function render_admin_menu_page(){
		if( ! current_user_can( WPMT()->settings->get_admin_cap('admin-menu-page') ) ){
			wp_die( WPMT()->helpers->translate( WPMT()->settings->get_default_string( 'insufficient-permissions' ), 'admin-submenu-page-insufficient-permissions' ) );
		}

		include( WPMT_PLUGIN_DIR . 'core/includes/partials/wpmt-page-display.php' );

	}

	/**
	 * ######################
	 * ###
	 * #### HELP TABS TEMPLATE ITEMS
	 * ###
	 * ######################
	 */
	public function add_help_tabs(){
		$screen = get_current_screen();

        $defaults = array(
            'content'   => '',
            'callback'  => array( $this, 'load_help_tabs' ),
        );

        $screen->add_help_tab(wp_parse_args(array(
            'id'        => 'general',
            'title'     => __('General', 'wp-mailto-links'),
        ), $defaults));

        $screen->add_help_tab(wp_parse_args(array(
            'id'        => 'shortcodes',
            'title'     => __('Shortcode', 'wp-mailto-links'),
        ), $defaults));

        $screen->add_help_tab(wp_parse_args(array(
            'id'        => 'template-tags',
            'title'     => __('Template Tags', 'wp-mailto-links'),
        ), $defaults));
	}

	public function load_help_tabs($screen, array $args){
		
		if( ! empty( $args['id'] ) ){
			include( WPMT_PLUGIN_DIR . 'core/includes/partials/help-tabs/' . $args['id'] . '.php' );
		}

    }

}
