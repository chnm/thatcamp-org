<?php
/*
Plugin Name: Yet Another Related Posts Plugin
Plugin URI: http://yarpp.org/
Description: Returns a list of related entries based on a unique algorithm for display on your blog and RSS feeds. Now with custom post type support!
Version: 3.5.1
Author: mitcho (Michael Yoshitaka Erlewine)
Author URI: http://mitcho.com/
Donate link: http://tinyurl.com/donatetomitcho
*/

define('YARPP_VERSION', '3.5.1');
define('YARPP_DIR', dirname(__FILE__));
define('YARPP_NO_RELATED', ':(');
define('YARPP_RELATED', ':)');
define('YARPP_NOT_CACHED', ':/');
define('YARPP_DONT_RUN', 'X(');

require_once(YARPP_DIR.'/class-core.php');
require_once(YARPP_DIR.'/related-functions.php');
require_once(YARPP_DIR.'/template-functions.php');
require_once(YARPP_DIR.'/class-widget.php');

if ( !defined('WP_CONTENT_URL') )
	define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content');

// New in 3.2: load YARPP cache engine
// By default, this is tables, which uses custom db tables.
// Use postmeta instead and avoid custom tables by adding the following to wp-config:
//   define('YARPP_CACHE_TYPE', 'postmeta');
if (!defined('YARPP_CACHE_TYPE'))
	define('YARPP_CACHE_TYPE', 'tables');
	
// New in 3.5: YARPP extra weight multiplier
if ( !defined('YARPP_EXTRA_WEIGHT') )
	define( 'YARPP_EXTRA_WEIGHT', 3 );

// new in 3.3.3: init yarpp on init
add_action( 'init', 'yarpp_init' );
function yarpp_init() {
	global $yarpp;
	$yarpp = new YARPP;

	// new in 3.3: include BlogGlue meta box
	if ( file_exists( YARPP_DIR . '/blogglue.php' ) && date('Ym') < 201204 )
		include_once( YARPP_DIR . '/blogglue.php' );
}

function yarpp_set_option($options, $value = null) {
	global $yarpp;
	$yarpp->set_option($options, $value);
}

function yarpp_get_option($option = null) {
	global $yarpp;
	return $yarpp->get_option($option);
}

// since 3.3.2: fix for WP 3.0.x
if ( !function_exists( 'self_admin_url' ) ) {
	function self_admin_url($path = '', $scheme = 'admin') {
		if ( defined( 'WP_NETWORK_ADMIN' ) && WP_NETWORK_ADMIN )
			return network_admin_url($path, $scheme);
		elseif ( defined( 'WP_USER_ADMIN' ) && WP_USER_ADMIN )
			return user_admin_url($path, $scheme);
		else
			return admin_url($path, $scheme);
	}
}

function yarpp_plugin_activate() {
	update_option( 'yarpp_activated', true );
}
add_action( 'activate_' . plugin_basename(__FILE__), 'yarpp_plugin_activate' );
