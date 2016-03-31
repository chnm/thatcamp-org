<?php defined('ABSPATH') OR die('No direct access.');
/*
Plugin Name:    WP Mailto Links - Manage Email Links
Plugin URI:     http://www.freelancephp.net/wp-mailto-links-plugin
Description:    Manage mailto links on your site and protect email addresses from spambots, set mail icon and more.
Author:         Victor Villaverde Laan
Version:        2.0.1
Author URI:     http://www.freelancephp.net
License:        Dual licensed under the MIT and GPL licenses
Text Domain:    wp-mailto-links
Domain Path:    /languages
*/
if (!defined('WP_MAILTO_LINKS_FILE')) {
    define('WP_MAILTO_LINKS_FILE', __FILE__);
}

// autoloader
if (!class_exists('WPDev_Loader')) {
    require_once __DIR__ . '/classes/WPDev/Loader.php';
}
WPDev_Loader::register();
WPDev_Loader::addPath(__DIR__ . '/classes');

// start plugin
WPML::create(array(
    'key'   => 'wp-mailto-links',
    'FILE'  => WP_MAILTO_LINKS_FILE,
    'DIR'   => __DIR__,
    'URL'   => plugins_url('', WP_MAILTO_LINKS_FILE),
));

/*?>*/
