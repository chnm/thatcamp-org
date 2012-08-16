<?php
/*
Plugin Name: THATCamp User Profile Sites
Plugin URI: http://thatcampdev.info
Description: Manage site membership for user from their user profile edit page.
Author: Jeremy Boggs
Version: 0.1
Network: true
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

if ( !class_exists( 'Thatcamp_User_Profile_Sites' ) ) :

class Thatcamp_User_Profile_Sites {

	/**
	* The main loader. The heavyweight. Hooks our stuff into WP
	*/
	function thatcamp_user_profile_sites() {

		add_action( 'init', array ( $this, 'init' ) );
		add_action( 'plugins_loaded', array ( $this, 'loaded' ) );
		add_action( 'wpmu_new_blog', array ( $this, 'new_blog' ) ); 		
		add_action( 'thatcamp_user_profile_sites_loaded', array ( $this, 'includes' ) );
		add_action( 'thatcamp_user_profile_sites_init', array ( $this, 'textdomain' ) );
		
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
	}

	// Let plugins know that we're initializing
	function init() {
		do_action( 'thatcamp_user_profile_sites_init' );
	}
	
	// Let plugins know that we're done loading
	function loaded() {
		do_action( 'thatcamp_user_profile_sites_loaded' );
	}

	function includes() {
        //      require( dirname( __FILE__ ) . '/thatcamp-registrations-functions.php' );
        //      require( dirname( __FILE__ ) . '/thatcamp-registrations-public-registration.php' );
        // if ( is_admin() ) {
        //  require( dirname( __FILE__ ) . '/thatcamp-registrations-admin.php' );
        //         }
	}
	
	// Allow this plugin to be translated by specifying text domain
	// Todo: Make the logic a bit more complex to allow for custom text within a given language
	function textdomain() {
        // $locale = get_locale();
        // 
        // // First look in wp-content/thatcamp-registration-files/languages, where custom language files will not be overwritten by THATCamp Registrations upgrades. Then check the packaged language file directory.
        // $mofile_custom = WP_CONTENT_DIR . "/thatcamp-registrations-files/languages/thatcamp-registration-$locale.mo";
        // $mofile_packaged = WP_PLUGIN_DIR . "/thatcamp-registrations/languages/thatcamp-registration-$locale.mo";
        // 
        //      if ( file_exists( $mofile_custom ) ) {
        //              load_textdomain( 'thatcamp-registrations', $mofile_custom );
        //              return;
        //          } else if ( file_exists( $mofile_packaged ) ) {
        //              load_textdomain( 'thatcamp-registrations', $mofile_packaged );
        //              return;
        //          }
	}

    function activation() {

    }
    
    function deactivation() {}
}

endif; // class exists

$thatcamp_user_profile_sites_loader = new Thatcamp_User_Profile_Sites();