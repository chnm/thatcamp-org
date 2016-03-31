<?php

/**
 * Plugin Name: Multisite Maintenance Mode
 * Plugin URI: https://github.com/channeleaton/Multisite-Maintenance-Mode
 * Description: Disables logins for all WordPress users except network administrators
 * Version: 0.2.2
 * Author: J. Aaron Eaton
 * Author URI: http://channeleaton.com
 * Author Email: aaron@channeleaton.com
 * Text Domain: multisite-maintenance-mode
 * Domain Path: /lang
 * License: GPL2
 *
 * Copyright 2015 J. Aaron Eaton (aaron@channeleaton.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */
class MultisiteMaintenanceMode {

	// The current version number
	private $version = '0.2.2';

	// The plugin screen slug
	private $plugin_slug = 'multisite-maintenance-mode';

	// The plugin path for easy retrieval
	private $path = null;

	// Sets the current MMM status
	private $status = false;

	public function __construct() {

		// Save the plugin path
		$this->path = plugin_dir_path( __FILE__ );

		// Get the current MMM status
		$this->status = get_site_option( 'mmm-status' );

		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// Add the options page and menu item.
		add_action( 'network_admin_menu', array( $this, 'plugin_admin_menu' ) );

		// Save the settings
		add_action( 'network_admin_edit_mmm_save', array( $this, 'save_settings' ), 10, 0 );

		// If maintenance mode is on, block the admin area and notify users.
		if ( true == $this->status ) {
			add_action( 'admin_init', array( $this, 'disable_logins' ), 1, 2 );
			add_action( 'admin_bar_menu', array( $this, 'admin_notice' ) );
		}

	}

	/**
	 * Loads the plugin text domain for translation
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), $this->plugin_slug );

		load_textdomain( $this->plugin_slug, WP_LANG_DIR . '/' . $this->plugin_slug . '/' . $this->plugin_slug . '-' . $locale . '.mo' );
		load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	}

	/**
	 * Registers the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function plugin_admin_menu() {

		add_submenu_page(
			'settings.php',
			__( 'Multisite Maintenance Mode', 'multisite-maintenance-mode' ),
			__( 'Multisite Maintenance Mode', 'multisite-maintenance-mode' ),
			'update_core',
			$this->plugin_slug,
			array( $this, 'plugin_admin_page' )
		);

	}

	/**
	 * Renders the options page for this plugin.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function plugin_admin_page() {

		// Get the current settings from the database
		$status  = get_site_option( 'mmm-status', false );
		$message = get_site_option( 'mmm-message', __( 'This network is in maintenance mode.', 'multisite-maintenance-mode' ) );
		$link    = get_site_option( 'mmm-link', '' );

		// Render the options page. The variables above are passed to the view.
		ob_start();
		include_once( 'views/admin.php' );
		echo ob_get_clean();

	}

	/**
	 * Saves the settings from the plugin options page
	 *
	 * @since 0.2.0
	 * @return void
	 */
	public function save_settings() {

		// Check the nonce
		check_admin_referer( 'mmm-settings' );

		// Save the status
		$mmm_status = ( isset( $_POST['mmm-status'] ) ) ? stripslashes_deep( $_POST['mmm-status'] ) : 0;
		update_site_option( 'mmm-status', $mmm_status );

		// Save the message
		$mmm_message = ( isset( $_POST['mmm-message'] ) ) ? stripslashes_deep( $_POST['mmm-message'] ) : '';
		update_site_option( 'mmm-message', $mmm_message );

		// Save the link
		$mmm_link = ( isset( $_POST['mmm-link'] ) ) ? stripslashes_deep( $_POST['mmm-link'] ) : '';
		update_site_option( 'mmm-link', $mmm_link );

		// Perform the proper redirect and exit
		wp_redirect(
			add_query_arg(
				array( 'page' => $this->plugin_slug, 'updated' => 'true' ),
				network_admin_url( 'settings.php' )
			)
		);
		exit;

	}

	/**
	 * Disables non-super admins from logging in
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function disable_logins() {

		global $current_user;

		if ( ! current_user_can( apply_filters( 'mmm_allow_user_with_capability', 'manage_network' ) ) ) {
			wp_redirect( home_url() );
		}

	}

	/**
	 * Creates the admin bar node to notify users
	 *
	 * @since 0.1.0
	 * @param WP_Admin_Bar $wp_admin_bar
	 * @return void
	 */
	public function admin_notice( WP_Admin_Bar $wp_admin_bar ) {

		// Get the settings from the database
		$message = get_site_option( 'mmm-message', __( 'This network is in maintenance mode.', 'multisite-maintenance-mode' ) );
		$link    = get_site_option( 'mmm-link', '' );

		// Setup the new node arguments
		$args    = array(
			'id'    => 'maintenance_notice',
			'title' => $message,
			'href'  => $link,
			'meta'  => array( 'class' => 'maintenance-mode' ),
		);

		// Add the node
		$wp_admin_bar->add_node( $args );

	}

}

// Instantiate the plugin
new MultisiteMaintenanceMode();