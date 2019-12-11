<?php
 /**
 * Plugin Name:    WP Mailto Links - Hide & Protect Emails
 * Version:        3.1.0
 * Plugin URI:     https://wordpress.org/plugins/wp-mailto-links/
 * Description:    Protect & encode email addresses and mailto links from spambots & spamming. Easy to use - encodes emails out-of-the-box.
 * Author:         Ironikus
 * Author URI:     https://ironikus.com/
 * License:        Dual licensed under the MIT and GPLv2+ licenses
 * Text Domain:    wp-mailto-links
 * 
 * License: GPL2
 *
 * You should have received a copy of the GNU General Public License
 * along with TMG User Filter. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

// Plugin name.
define( 'WPMT_NAME',           'WP Mailto Links' );

// Plugin version.
define( 'WPMT_VERSION',        '3.1.0' );

// Determines if the plugin is loaded
define( 'WPMT_SETUP',          true );

// Plugin Root File.
define( 'WPMT_PLUGIN_FILE',    __FILE__ );

// Plugin base.
define( 'WPMT_PLUGIN_BASE',    plugin_basename( WPMT_PLUGIN_FILE ) );

// Plugin Folder Path.
define( 'WPMT_PLUGIN_DIR',     plugin_dir_path( WPMT_PLUGIN_FILE ) );

// Plugin Folder URL.
define( 'WPMT_PLUGIN_URL',     plugin_dir_url( WPMT_PLUGIN_FILE ) );

// Plugin Root File.
define( 'WPMT_TEXTDOMAIN',     'wp-mailto-links' );

/**
 * Load the main instance for our core functions
 */
require_once WPMT_PLUGIN_DIR . 'core/class-wp-mailto-links.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @return object|WP_Mailto_Links
 */
function WPMT() {
	return WP_Mailto_Links::instance();
}

WPMT();