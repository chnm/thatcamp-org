<?php
/*
Plugin Name: THATCamp Registrations
Plugin URI: http://thatcamp.org
Description: Manages registrations for events.
Version: 1.1
Author: Roy Rosenzweig Center for History and New Media
Author URI: http://chnm.gmu.edu
*/

/*
Copyright (C) 2010 Center for History and New Media, George Mason University

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !class_exists( 'Thatcamp_Registrations_Loader' ) ) :

class Thatcamp_Registrations_Loader {

	/**
	* The main loader. The heavyweight. Hooks our stuff into WP
	*/
	function thatcamp_registrations_loader() {

		add_action( 'init', array ( $this, 'init' ) );
		add_action( 'plugins_loaded', array ( $this, 'loaded' ) );
		add_action( 'wpmu_new_blog', array ( $this, 'new_blog' ) );
		add_action( 'thatcamp_registrations_loaded', array ( $this, 'includes' ) );
		add_action( 'thatcamp_registrations_init', array ( $this, 'textdomain' ) );

		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
	}

	// Let plugins know that we're initializing
	function init() {
		$this->activation();
		do_action( 'thatcamp_registrations_init' );
	}

	// Let plugins know that we're done loading
	function loaded() {
		do_action( 'thatcamp_registrations_loaded' );
	}

	function includes() {
	    require( dirname( __FILE__ ) . '/thatcamp-registrations-profile-fields.php' );
	    require( dirname( __FILE__ ) . '/thatcamp-registrations-functions.php' );
	    require( dirname( __FILE__ ) . '/thatcamp-registrations-public-registration.php' );
		if ( is_admin() ) {
			require( dirname( __FILE__ ) . '/thatcamp-registrations-admin.php' );
        }
	}

	// Allow this plugin to be translated by specifying text domain
	// Todo: Make the logic a bit more complex to allow for custom text within a given language
	function textdomain() {
		$locale = get_locale();

		// First look in wp-content/thatcamp-registration-files/languages, where custom language files will not be overwritten by THATCamp Registrations upgrades. Then check the packaged language file directory.
		$mofile_custom = WP_CONTENT_DIR . "/thatcamp-registrations-files/languages/thatcamp-registration-$locale.mo";
		$mofile_packaged = WP_PLUGIN_DIR . "/thatcamp-registrations/languages/thatcamp-registration-$locale.mo";

    	if ( file_exists( $mofile_custom ) ) {
      		load_textdomain( 'thatcamp-registrations', $mofile_custom );
      		return;
      	} else if ( file_exists( $mofile_packaged ) ) {
      		load_textdomain( 'thatcamp-registrations', $mofile_packaged );
      		return;
      	}
	}

    function activation() {
        global $wpdb;
    	if (function_exists('is_multisite') && is_multisite()) {
    		// check if it is a network activation - if so, run the activation function for each blog id
    		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
    	        $old_blog = $wpdb->blogid;
    			// Get all blog ids
    			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
    			foreach ($blogids as $blog_id) {
    				switch_to_blog($blog_id);
    				$this->_create_table();
    			}
    			switch_to_blog($old_blog);
    			return;
    		}
    	}
    	$this->_create_table();
        // First-Run-Only parameters: Check if schedule table exists:

    }

    function _create_table() {
    	global $wpdb;
        $table_name = $wpdb->prefix . 'thatcamp_registrations';
    	if ($wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            if (!empty ($wpdb->charset))
        		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        	if (!empty ($wpdb->collate))
        		$charset_collate .= " COLLATE {$wpdb->collate}";
        		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        		  	id bigint(20) NOT NULL AUTO_INCREMENT,
        		  	date datetime NOT NULL,
        		  	user_id bigint(2) NULL,
        		  	applicant_email varchar(255) NOT NULL,
        		  	applicant_info text NULL,
        			application_text text NOT NULL,
        			status varchar(255) NOT NULL,
        		  	UNIQUE KEY id (id)
        		) {$charset_collate};";

        	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        	dbDelta($sql);
        }
    }

    function new_blog($newBlogId) {
    	global $wpdb;
    	if (is_plugin_active_for_network('thatcamp-registrations/thatcamp-registrations.php')) {
    		$oldBlogId = $wpdb->blogid;
    		switch_to_blog($newBlogId);
    		$this->_create_table();
    		switch_to_blog($oldBlogId);
    	}
    }

    function deactivation() {}
}

endif; // class exists

$thatcamp_registrations_loader = new Thatcamp_Registrations_Loader();
