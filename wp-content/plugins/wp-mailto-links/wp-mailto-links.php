<?php defined('ABSPATH') OR die('No direct access.');
/*
Plugin Name: WP Mailto Links - Manage Email Links
Plugin URI: http://www.freelancephp.net/wp-mailto-links-plugin
Description: Manage mailto links on your site and protect email addresses from spambots, set mail icon and more.
Author: Victor Villaverde Laan
Version: 1.4.1
Author URI: http://www.freelancephp.net
License: Dual licensed under the MIT and GPL licenses
Text Domain: wp-mailto-links
Domain Path: /languages
*/

// includes
require_once(dirname(__FILE__) . '/classes/WP/Plugin/Abstract.php');
require_once(dirname(__FILE__) . '/classes/WPML.php');

// init plugin
WPML::init(array(
    'version' => '1.4.1',
    'key' => 'WP_Mailto_Links',
    'domain' => 'wp-mailto-links',
    'optionName' => 'WP_Mailto_Links_options',
    'adminPage' => 'wp-mailto-links-settings',
    'file' => __FILE__,
    'dir' => dirname(__FILE__),
    'pluginUrl' => plugins_url() . '/wp-mailto-links',
    'wpVersion' => $wp_version,
    'minPhpVersion' => '5.2.4',
    'minWpVersion' => '3.4.0',
));
