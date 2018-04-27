<?php
/*
 * Plugin Name: Very Simple Contact Form
 * Description: This is a very simple contact form. Use shortcode [contact] to display form on page or use the widget. For more info please check readme file.
 * Version: 8.4
 * Author: Guido van der Leest
 * Author URI: https://www.guidovanderleest.nl
 * License: GNU General Public License v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
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

// form submissions
$list_submissions_setting = esc_attr(get_option('vscf-setting-2'));
if ($list_submissions_setting == "yes") {
	// create submission post type
		function vscf_custom_postype() { 
			$vscf_args = array( 
				'labels' => array('name' => __( 'Submissions', 'very-simple-contact-form' )), 
				'public' => false, 
				'can_export' => true, 
				'show_in_nav_menus' => false, 
				'show_ui' => true, 
				'show_in_rest' => true, 
				'capability_type' => 'post', 
				'capabilities' => array('create_posts' => 'do_not_allow'), 
				'map_meta_cap' => true, 
 				'supports' => array('title', 'editor'), 
			); 
			register_post_type( 'submission', $vscf_args); 
		}
		add_action( 'init', 'vscf_custom_postype' ); 

	// dashboard submission columns
	function vscf_custom_columns( $columns ) { 
		$columns['name_column'] = __( 'Name', 'very-simple-contact-form' ); 
		$columns['email_column'] = __( 'Email', 'very-simple-contact-form' ); 
		$custom_order = array('cb', 'title', 'name_column', 'email_column', 'date');
		foreach ($custom_order as $colname) {
			$new[$colname] = $columns[$colname];
		}
		return $new;
	} 
	add_filter( 'manage_submission_posts_columns', 'vscf_custom_columns', 10 );

	function vscf_custom_columns_content( $column_name, $post_id ) { 
		if ( 'name_column' == $column_name ) { 
			$name = get_post_meta( $post_id, 'name_sub', true ); 
			echo $name; 
		} 
		if ( 'email_column' == $column_name ) { 
			$email = get_post_meta( $post_id, 'email_sub', true ); 
			echo $email; 
		} 
	} 
	add_action( 'manage_submission_posts_custom_column', 'vscf_custom_columns_content', 10, 2 );

	// make name and email column sortable
	function vscf_column_register_sortable( $columns ) {
		$columns['name_column'] = 'name_sub';
		$columns['email_column'] = 'email_sub';
		return $columns;
	}
	add_filter( 'manage_edit-submission_sortable_columns', 'vscf_column_register_sortable' );

	function vscf_name_column_orderby( $vars ) {
		if(is_admin()) {
			if ( isset( $vars['orderby'] ) && 'name_sub' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => 'name_sub',
					'orderby' => 'meta_value'
				) );
			}
		}
		return $vars;
	}
	add_filter( 'request', 'vscf_name_column_orderby' );

	function vscf_email_column_orderby( $vars ) {
		if(is_admin()) {
			if ( isset( $vars['orderby'] ) && 'email_sub' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => 'email_sub',
					'orderby' => 'meta_value'
				) );
			}
		}
		return $vars;
	}
	add_filter( 'request', 'vscf_email_column_orderby' );
}

// add settings link
function vscf_action_links ( $links ) { 
	$settingslink = array( '<a href="'. admin_url( 'options-general.php?page=vscf' ) .'">'. __('Settings', 'very-simple-contact-form') .'</a>', ); 
	return array_merge( $links, $settingslink ); 
} 
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'vscf_action_links' ); 

// function to get ip of user
function vscf_get_the_ip() {
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
		return $_SERVER["HTTP_CLIENT_IP"];
	} else {
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
include 'vscf-options.php';
