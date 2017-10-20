<?php
/*
 * Plugin Name: Very Simple Contact Form
 * Description: This is a very simple contact form. Use shortcode [contact] to display form on page or use the widget. For more info please check readme file.
 * Version: 7.1
 * Author: Guido van der Leest
 * Author URI: http://www.guidovanderleest.nl
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: very-simple-contact-form
 * Domain Path: /translation
 */

// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// load plugin text domain
function vscf_init() { 
	load_plugin_textdomain( 'very-simple-contact-form', false, dirname( plugin_basename( __FILE__ ) ) . '/translation' );
}
add_action('plugins_loaded', 'vscf_init');

// enqueues plugin scripts
function vscf_scripts() {	
	if(!is_admin())	{
		wp_enqueue_style('vscf_style', plugins_url('/css/vscf-style.css',__FILE__));
	}
}
add_action('wp_enqueue_scripts', 'vscf_scripts');

// the sidebar widget
function register_vscf_widget() {
	register_widget( 'vscf_widget' );
}
add_action( 'widgets_init', 'register_vscf_widget' );

// function to get ip of user
function vscf_get_the_ip() {
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	else {
		return $_SERVER["REMOTE_ADDR"];
	}
}

// function to create from email header
function vscf_from_header() {
	if ( !isset( $from_email ) ) {
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		return 'wordpress@' . $sitename;
	}
}

// include form and widget files
include 'vscf-form.php';
include 'vscf-widget-form.php';
include 'vscf-widget.php';
