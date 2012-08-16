<?php
/* 
 * Plugin Name: Multisite Global Search
 * Plugin URI: http://grial.usal.es/agora/pfcgrial/multisite-search
 * Description: Adds the ability to search through blogs into your WordPress Multisite installation. Based on my other plugin WPMU GLobal Search.
 * Version: 1.2.8
 * Requires at least: WordPress 3.0
 * Tested up to: WordPress 3.3
 * Author: Alicia García Holgado
 * Author URI: http://grial.usal.es/agora/mambanegra
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Network: true
*/

/*  Copyright 2010  Alicia García Holgado  ( email : aliciagh@usal.es )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define ( 'MSGLOBALSEARCH_URL', plugins_url('', __FILE__) );
define ( 'MSGLOBALSEARCH_DIR', dirname(__FILE__) );

require( MSGLOBALSEARCH_DIR . '/inc/init.php'); // Init functions

if ( !is_multisite() ) {
    add_action( 'admin_notices', 'ms_global_search_install_multisite_notice' );
    return;
}

$option = get_option( 'permalink_structure' );
if ( empty ( $option ) ) {
    add_action( 'admin_notices', 'ms_global_search_active_widget_notice' );
    return;
}

require( MSGLOBALSEARCH_DIR . '/inc/class.widgets.php'); // Widget definition
require( MSGLOBALSEARCH_DIR . '/inc/shortcodes.php'); // Shortcodes definition
require( MSGLOBALSEARCH_DIR . '/inc/views.php' );

// Activation, deactivation
register_activation_hook( __FILE__, 'ms_global_search_build_views_add' );
register_deactivation_hook( __FILE__, 'ms_global_search_drop_views');

/**
 * Init Multisite Global Search.
 */
function ms_global_search_init() {
    load_plugin_textdomain( 'ms-global-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    
    add_action( 'wp_print_styles', 'ms_global_search_style' ); // Add style file if it exists
    add_filter( 'query_vars', 'ms_global_search_queryvars' ); // Init search variables
    
    add_shortcode( 'multisite_search_form', 'ms_global_search_form' );
    add_shortcode( 'multisite_search_result', 'ms_global_search_page' );
    
    /**
     * Builds a view that contains posts from all blogs.
     * Views are built by activate_blog, desactivate_blog, archive_blog, unarchive_blog, delete_blog and wpmu_new_blog hooks.
     */
    add_action ( 'wpmu_new_blog', 'ms_global_search_build_views_add' );
    add_action ( 'delete_blog', 'ms_global_search_build_views_drop', 10, 1 );
    add_action ( 'archive_blog', 'ms_global_search_build_views_drop', 10, 1 );
    add_action ( 'unarchive_blog', 'ms_global_search_build_views_unarchive', 10, 1 );
    add_action ( 'activate_blog', 'ms_global_search_build_views_activate', 10, 1 );
    add_action ( 'deactivate_blog', 'ms_global_search_build_views_drop', 10, 1 );
}
add_action( 'plugins_loaded', 'ms_global_search_init' );

