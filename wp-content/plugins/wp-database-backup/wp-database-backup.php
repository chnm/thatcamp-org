<?php
/*
Plugin Name: WP Database Backup
Plugin URI:walkeprashant.wordpress.com/wp-database-backup
Description: This plugin helps you to create/restore wordpress database backup. (Tools->WP-DB-Backup)
Version: 3.4
Author:Prashant Walke
Author URI:walkeprashant.wordpress.com

This plugin helps you to create Database Backup easily.

License: GPL v3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPDatabaseBackup' ) ) :

/**
 * Main WPDatabaseBackup Class
 *
 * @class WPDatabaseBackup
 * @version	1.1
 */
final class WPDatabaseBackup {

	public $version = '3.3';

	protected static $_instance = null;

	public $query = null;

		public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0' );
	}

	public function __construct() {
		// Define constants
		$this->define_constants();
		// Include required files
		$this->includes();
	
	}
	

	/**
	 * Define Constants
	 */
	private function define_constants() {
	 if ( ! defined( 'WPDB_PLUGIN_URL'  ) ) 
	        define( 'WPDB_PLUGIN_URL',  WP_CONTENT_URL. '/plugins/wp-database-backup' );
		define( 'WPDB_PLUGIN_FILE', __FILE__ );
		define( 'WPDB_VERSION', $this->version );
		define( 'WPDBPLUGIN_VERSION', WPDB_VERSION ); // Backwards compat
		}

	private function includes() {
				include_once( 'includes/admin/class-wpdb-admin.php' );

}

	public function logger() {
		_deprecated_function( 'Wpekaplugin->logger', '1.0', 'new WPDB_Logger()' );
		return new WPDB_Logger();
	}

	}

endif;

/**
 * Returns the main instance of WP to prevent the need to use globals.

 */
function WPDB() {
	return WPDatabaseBackup::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpdbplugin'] = WPDB();
