<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 */
/*
Plugin Name: Add Multiple Users
Plugin URI: http://addmultipleusers.happynuclear.com/
Description: This plugin allows you to add multiple user accounts to your Wordpress blog using a range of tools.
Version: 2.0.0
Author: HappyNuclear
Author URI: http://www.happynuclear.com
Text Domain: amulang
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

define('AMU_VERSION', '2.0.0');

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}
function amu_menu() {
	add_menu_page('Add Multiple Users', 'AMU', 'manage_options', 'addmultiple', 'add_multiple_users');
	add_submenu_page('addmultiple',__('AMU Settings','amulang'),__('Plugin Settings','amulang'),'manage_options','amusettings','amu_settings');
	add_submenu_page('addmultiple',__('AMU Manual Entry Form','amulang'),__('Manual Entry','amulang'),'manage_options','amumanual','amu_manual');
	add_submenu_page('addmultiple',__('AMU Import CSV Data','amulang'),__('Import CSV Data','amulang'),'manage_options','amucsvimport','amu_csvimport');
	add_submenu_page('addmultiple',__('AMU Import Email List','amulang'),__('Import Email List','amulang'),'manage_options','amuemaillist','amu_emaillist');
	if ( is_multisite() ) {
		add_submenu_page('addmultiple',__('AMU Add from Network','amulang'),__('Add from Network','amulang'),'manage_options','amuaddfromnet','amu_addfromnet');
	}
}
function amu_network_menu() {
	add_users_page(__('Add Multiple Users Network','amulang'), __('AMU Network','amulang'), 'manage_network', 'addmultiple', 'amu_networksite');
}
function on_screen_validation() {
	wp_enqueue_script( "field_validation", plugins_url( "js/field-validation.js", __FILE__ ), array( 'jquery' ) );
	wp_localize_script( "field_validation", "MySecureAjax", array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
function multiadd_actions() {
	wp_enqueue_script( "multiadd_actions", plugins_url( "js/multiadd-actions.js", __FILE__ ), array( 'jquery' ) );
	wp_localize_script( "multiadd_actions", "MySecureAjax", array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
function addmultiuser_style() {
	wp_register_style($handle = 'amu_css_style', $src = plugins_url('amustyle.css', __FILE__), $deps = array(), $ver = '2.0.0', $media = 'all');
    wp_enqueue_style('amu_css_style');
}
function amu_admin_init() {
    wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-datepicker');
}

// <=========== ACTIVATE DEFAULT SETTINGS ==============================================>

function amu_set_defaultoptions() {
	global $current_user, $wpdb;
    get_currentuserinfo();
	$defaultAdminEmail = $current_user->user_email;
	$sitelogurl = site_url();
	$defaultUserEmailHead = __('Your New User Account Information on','amulang').' [sitename]';
	$defaultUserEmailText = '<h1>'.__('You have been registered as a user on','amulang').' [sitename]</h1>
<p>'.__('You may now log into the site at','amulang').' [siteloginurl]</p>
<p>'.__('Your username is','amulang').' [username] '.__('and your password is','amulang').' [password]</p>
<p>'.__('Regards','amulang').',<br>
[sitename] '.__('Admin','amulang').'</p>
<p>[siteurl]</p>';
	//update options
	if(!get_option('amu_usernotify')) {
		update_option( 'amu_usernotify', 'yes' );
	}
	if(!get_option('amu_confirmation')) {
		update_option( 'amu_confirmation', 'yes' );
	}
	if(!get_option('amu_setallroles')) {
		update_option( 'amu_setallroles', 'notset' );
	}
	if(!get_option('amu_validatestrict')) {
		update_option( 'amu_validatestrict', 'no' );
	}
	if(!get_option('amu_validatemail')) {
		update_option( 'amu_validatemail', 'yes' );
	}
	if(!get_option('amu_forcefill')) {
		update_option( 'amu_forcefill', 'no' );
	}
	if(!get_option('amu_defadminemail')) {
		update_option( 'amu_defadminemail', $defaultAdminEmail );
	}
	if(!get_option('amu_siteloginurl')) {
		update_option( 'amu_siteloginurl', $sitelogurl );
	}
	if(!get_option('amu_useremailhead')) {
		update_option( 'amu_useremailhead', $defaultUserEmailHead );
	}
	if(!get_option('amu_useremailtext')) {
		update_option( 'amu_useremailtext', $defaultUserEmailText );
	}
	if(!get_option('amu_showblankmeta')) {
		update_option( 'amu_showblankmeta', '' );
	}
	if(!get_option('amu_dispnamedef')) {
		update_option( 'amu_dispnamedef', 'userlogin' );
	}
	if(!get_option('amu_extrameta')) {
		update_option( 'amu_extrameta', '' );
	}
	if(!get_option('amu_colorderpref')) {
		update_option( 'amu_colorderpref', 'dynamic' );
	}
	if(!get_option('amu_colorderpredef')) {
		update_option( 'amu_colorderpredef', '' );
	}
	
}

// <=========== ACTIVATE DEFAULT NETWORK SETTINGS =========================================>

function amu_set_default_network_options() {
	global $wpdb;
	if(!get_site_option('amu_is_network')) {
		update_site_option( 'amu_is_network', 'yes' );
	}
	if(!get_site_option('amu_subadminaccess')) {
		update_site_option( 'amu_subadminaccess', 'yes' );
	}
	if(!get_site_option('amu_addexistingaccess')) {
		update_site_option( 'amu_addexistingaccess', 'yes' );
	}
	if(!get_site_option('amu_emailcopies')) {
		update_site_option( 'amu_emailcopies', '' );
	}
}

// <=========== RUN ACTIONS ===============================================================>

if ( is_multisite() ) {
     add_action( 'network_admin_menu', 'amu_network_menu' );
}
add_action( 'admin_menu', 'amu_menu' );
add_action('admin_init', 'amu_admin_init');
add_action( 'admin_print_styles', 'addmultiuser_style' );
add_action( 'admin_print_scripts', 'on_screen_validation' );
add_action( 'admin_print_scripts', 'multiadd_actions' );
add_action( 'wp_ajax_UserNameValidation', 'validateUserName' );
add_action( 'wp_ajax_EmailValidation', 'validateEmail' );
add_action( 'wp_ajax_OptionTestEmail', 'sendTestEmail' );
register_activation_hook( __FILE__, 'amu_set_defaultoptions' );
if ( is_multisite() ) {
	register_activation_hook( __FILE__, 'amu_set_default_network_options' );
}

// <=========== INCLUDE FUNCTION FILES ====================================================>

include('functions/plugininfo.php');
include('functions/networkoptions.php');
include('functions/manualentry.php');
include('functions/addemaillist.php');
include('functions/commonfn.php');
include('functions/helpfiles.php');
include('functions/settings.php');
include('functions/csvimport.php');
include('functions/ajaxfunctions.php');

// <=========== LOCALIZATION ====================================================>

load_plugin_textdomain('amulang', false, dirname(plugin_basename(__FILE__)) . '/lang');
?>