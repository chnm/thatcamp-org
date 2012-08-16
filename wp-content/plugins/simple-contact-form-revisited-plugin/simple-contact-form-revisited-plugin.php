<?php

/*
Plugin Name: Simple Contact Form Revisited Widget
Plugin URI: http://initbinder.com/plugins
Description: A simple, yet elegant email contact form plugin that installs as a widget. The contact form widget is using jQuery for validation. In addition, the widget makes it difficult for email bots to harvest your email address by encrypting it before rendering the HTML form. This sidebar widget is particularly useful when you want to allow your visitors to contact you without forcing them to navigate away from the current page. In addition to the widget, the plugin also installs contact form short code that can be used in pages, posts and text widgets. The short code generates a contact form that functions just like the form in the widget.
Version: 2.0.9
Author: Alexander Zagniotov
Author URI: http://initbinder.com
License: GPLv2
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

define('SCFR_VERSION', '2.0.9');
define('CUSTOM_EMAIL_SEP', '[^-*-^]');
define('SCFR_PLUGIN_URI', plugin_dir_url( __FILE__ ));
define('SCFR_PLUGIN_ASSETS_URI', SCFR_PLUGIN_URI.'assets');
define('SCFR_PLUGIN_CSS', SCFR_PLUGIN_ASSETS_URI . '/css');
define('SCFR_PLUGIN_JS', SCFR_PLUGIN_ASSETS_URI . '/js');

require_once (dirname(__FILE__) . '/widget.php');
require_once (dirname(__FILE__) . '/shortcode.php');

function add_script() {
	wp_register_script('jquery-validator', SCFR_PLUGIN_JS .'/jquery.tools.min.js', array('jquery'), '1.2.5', true);
	wp_enqueue_script('jquery-validator');

	wp_register_script( 'simple-form-revisited-plugin-init', SCFR_PLUGIN_JS .'/simple-contact-form-revisited-plugin.js', 
			array('jquery', 'jquery-validator'), SCFR_VERSION, true);
	wp_enqueue_script('simple-form-revisited-plugin-init');
}

function add_style()  {
	wp_enqueue_style('simple-form-revisited-plugin-style', SCFR_PLUGIN_CSS . '/style.css', false, SCFR_VERSION, "screen");
}

add_action('widgets_init', create_function('', 'return register_widget("SimpleContactFormRevisited_Widget");'));

add_action( 'wp_print_scripts', 'add_script');
add_action( 'wp_print_styles', 'add_style');

add_shortcode('contactform', 'shortcode_simplecontactformrevisited_handler');
//add_filter('widget_text', 'shortcode_simplecontactformrevisited_handler');

?>
