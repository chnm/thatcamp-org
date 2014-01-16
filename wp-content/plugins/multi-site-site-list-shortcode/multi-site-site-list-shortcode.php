<?php
/*
	Plugin Name: Multi-Site Site List Shortcode
	Plugin URI: http://bit51.com/software/multi-site-site-list-shortcode/
	Description: Displays the site list with a shortcode for network installations.
	Version: 5.4.2
	Domain Path: /languages
	Author: Bit51
	Author URI: http://bit51.com
	License: GPLv2
	Copyright 2013  Bit51  (email : info@bit51.com)
*/

//load the text domain
load_plugin_textdomain( 'multi_site_site_list_shortcode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

//Require common Bit51 library
require_once( plugin_dir_path( __FILE__ ) . 'lib/bit51/bit51.php' );

if ( !class_exists( 'bit51_mssls' ) ) {

	class bit51_mssls extends Bit51 {

		public $pluginversion = '5.4.2'; //current plugin version

		//important plugin information
		public $hook = 'multi_site_site_list_shortcode';
		public $pluginbase = 'multi-site-site-list-shortcode/multi-site-site-list-shortcode.php';
		public $pluginname = 'Multi-Site Site List Shortcode';
		public $homepage = 'http://bit51.com/software/multi-site-site-list-shortcode/';
		public $supportpage = 'http://wordpress.org/support/plugin/multi-site-site-list-shortcode';
		public $wppage = 'http://wordpress.org/extend/plugins/multi-site-site-list-shortcode/';
		public $accesslvl = 'manage_network_options';
		public $paypalcode = '7GDQDFENDBDAA';
		public $plugindata = 'bit51_mssls_data';
		public $primarysettings = 'bit51_mssls';
		public $settings = array(
			'bit51_mssls_options' => array(
				'bit51_mssls' => array(
					'callback' => 'mssls_val_options',
					'sortby' => '0',
					'openin' => '0',
					'limit' => '0',
					'showtag' => '0',
					'excluded' => array()
				)
			)
		);

		/**
		 * Register the shortcode in instantiation
		 */
		function __construct() {

			//set path information
			define( 'MSSLS_PP', plugin_dir_path( __FILE__ ) );
			define( 'MSSLS_PU', plugin_dir_url( __FILE__ ) );

			//require admin page
			require_once( plugin_dir_path( __FILE__ ) . 'inc/admin.php' );
			new mssls_admin( $this );

			//require setup information
			require_once( plugin_dir_path( __FILE__ ) . 'inc/setup.php' );
			register_activation_hook( __FILE__, array( 'mssls_setup', 'on_activate' ) );
			register_deactivation_hook( __FILE__, array( 'mssls_setup', 'on_deactivate' ) );
			register_uninstall_hook( __FILE__, array( 'mssls_setup', 'on_uninstall' ) );

			add_shortcode( 'site-list', array( $this, 'display_site_list' ) );
			if ( !is_admin() ) {
				add_filter( 'widget_text', 'do_shortcode' );
			}
		}

		/**
		 * Create site list
		 */
		function display_site_list( $attr ) {
			global $wpdb;
			global $table_prefix;
			$output = '';

			//this is a workaround for lack of settings api with multi-site
			$options = $wpdb->get_results( 'SELECT option_value FROM `' . $wpdb->base_prefix . 'options` WHERE option_name IN (\'' . $this->primarysettings . '\') ORDER BY option_name DESC' );

			//convert options to array so we can use it like normal
			$options = unserialize( $options[0]->option_value );

			//get blog list
			$blogs = $wpdb->get_col( "SELECT blog_id FROM `" . $wpdb->blogs . "` WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ORDER BY blog_id DESC" );

			//make sure there are blogs
			if ( $blogs ) {

				$output .= '<div id="mssls"><ul>'; //assign a div to make styling easier
				$siteArray = array(); //initial array of sites to display

				foreach ( $blogs as $blog ) {
					if ( $blog == '1' ) { //get blog options from the right table (blog 1 needs a special tablename)
						$table = $wpdb->base_prefix . 'options';
					} else {
						$table = $wpdb->base_prefix . $blog . '_options';
					}

					if ( is_array( $options['excluded'] ) ) { //get array of excluded blog id's
						$excluded = $options['excluded'];
					} else {
						$excluded = unserialize( $options['excluded'] );
					}

					$sitedetails = $wpdb->get_results( 'SELECT option_value FROM `' . $table . '` WHERE option_name IN (\'siteurl\',\'blogname\',\'blogdescription\') ORDER BY option_name DESC' ); //get blog details

					if ( isset( $attr['sort'] ) ) {
						if ( strtolower( $attr['sort'] ) == 'alpha' ) {
							$sortby = 0;
						} else {
							$sortby = 1;
						}
					} else {
						$sortby = $options['sortby'];
					}

					if ( $sitedetails && !in_array( $blog, $excluded ) ) { //if the blog exists and isn't on the exclusion list add it to array
						if ( $sortby == 0 ) { //proper array construction depending on sort
							$siteArray[$sitedetails[1]->option_value]['url'] = $sitedetails[0]->option_value;
							$siteArray[$sitedetails[1]->option_value]['id'] = $blog;
							$siteArray[$sitedetails[1]->option_value]['desc'] = $sitedetails[2]->option_value;
						} else {
							$siteArray[$blog]['url'] = $sitedetails[0]->option_value;
							$siteArray[$blog]['title'] = $sitedetails[1]->option_value;
							$siteArray[$blog]['desc'] = $sitedetails[2]->option_value;
						}
					}
				}

				ksort( $siteArray ); //sort array

			}

			//create link target if necessary

			if ( isset( $attr['newwin'] ) ) {
				$openin = $attr['newwin'];
			} else {
				$openin = $options['openin'];
			}

			if ( $openin == 1 ) {
				$target = ' target="_blank" ';
			} else {
				$target = ' ';
			}

			$count = 0;

			//add sites to output string
			foreach ( $siteArray as $site => $value ) {

				if ( isset( $attr['limit'] ) ) {
					$limit = $attr['limit'];
				} else {
					$limit = $options['limit'];
				}

				if ( $count < $limit || $limit == 0 ) {

					if ( isset( $attr['showtag'] ) ) {
						$showtag = $attr['showtag'];
					} else {
						$showtag = $options['showtag'];
					}

					if ( $showtag == 1 && strlen( $value['desc'] ) > 0 ) {
						$desc = '<br /><p>' . $value['desc'] . '</p>';
					} else {
						$desc = '';
					}

					if ( $sortby == 0 ) {
						$output .= '<li><a href="' . $value['url'] . '"' . $target . '>' . $site . '</a>' . $desc . '</li>';
					} else {
						$output .= '<li><a href="' . $value['url'] . '"' . $target . '>' . $value['title'] . '</a>' . $desc . '</li>';
					}

					$count++;

				} else {
					break;
				}
			}

			return $output . '</ul></div>';

		}
	}
}

//create plugin object
new bit51_mssls();
