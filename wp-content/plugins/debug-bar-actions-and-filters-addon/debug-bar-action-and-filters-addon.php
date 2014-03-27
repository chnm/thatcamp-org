<?php
/**
 * Plugin Name: Debug Bar Actions and Filters Addon
 * Plugin URI: http://wordpress.org/extend/plugins/debug-bar-actions-and-filters-addon/
 * Description: This plugin add two more tabs in the Debug Bar to display hooks(Actions and Filters) attached to the current request. Actions tab displays the actions hooked to current request. Filters tab displays the filter tags along with the functions attached to it with priority.
 * Version: 1.4.1
 * Author: Subharanjan
 * Author Email: subharanjanmantri@gmail.com
 * Author URI: http://www.subharanjan.in/
 * License: GPLv2
 *  
 * @author  subharanjan
 * @package debug-bar-actions-and-filters-addon
 * @version 1.4.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
 
/**
 * Function to hook with debug_bar_panels filter.
 *
 * @param array $panels list of all the panels in debug bar.
 *
 * @return array $panels modified panels list
 */
if ( !function_exists( 'debug_bar_action_and_filters_addon_panel' ) ) {
    function debug_bar_action_and_filters_addon_panel( $panels ) {
        require_once( plugin_dir_path( __FILE__ ) . 'class-debug-bar-action-and-filters-addon.php' );
        $wp_actions = new Debug_Bar_Actions_Addon_Panel();
        $wp_actions->set_tab( 'Action Hooks', 'debug_bar_action_and_filters_addon_display_actions' );
        $panels[] = $wp_actions;
        $wp_filters = new Debug_Bar_Filters_Addon_Panel();
        $wp_filters->set_tab( 'Filter Hooks', 'debug_bar_action_and_filters_addon_display_filters' );
        $panels[] = $wp_filters;
        return $panels;
    }
}
add_filter( 'debug_bar_panels', 'debug_bar_action_and_filters_addon_panel' );

/**
 * Function to display the Actions attached to current request.
 *
 * @return string $output display output for the actions panel
 */
function debug_bar_action_and_filters_addon_display_actions() {
    global $wp_actions;
    $output = '';
    $output .= '<div class="hooks_listing_container">' . "\n";
    $output .= '<h2>List of Action Hooks</h2><br />' . "\n";
    $output .= "<ul>\n";
    foreach ( $wp_actions as $action_key => $action_val ) {
        $output .= '<li>' . $action_key . "</li>\n";
    }
    $output .= '<li><strong>Total Count: </strong>' . count( $wp_actions ) . "</li>\n";
    $output .= "</ul>\n";
    $output .= "</div>\n";
    return $output;
}

/**
 * Function to to check for closures
 *
 * @param   mixed $arg function name
 *
 * @return  boolean $closurecheck return whether or not a closure
 */
function dbafa_is_closure( $arg ) {
	if( version_compare( PHP_VERSION, '5.3', '<' ) ) {
		return false;
	}

    include_once( plugin_dir_path( __FILE__ ) . 'php5.3-closure-test.php' );
    return debug_bar_action_and_filters_addon_is_closure( $arg );
}

/**
 * Function to display the Filters applied to current request.
 *
 * @return string $output display output for the filters panel
 */
function debug_bar_action_and_filters_addon_display_filters() {
    global $wp_filter;
    $output = '';
    $output .= '<div class="hooks_listing_container">' . "\n";
    $output .= '<h2>List of Filter Hooks (with functions)</h2><br />' . "\n";
    $output .= "<ul>\n";
    foreach ( $wp_filter as $filter_key => $filter_val ) {
        $output .= '<li>';
        $output .= '<strong>' . $filter_key . "</strong><br />\n";
        $output .= "<ul>\n";
        ksort( $filter_val );
        foreach ( $filter_val as $priority => $functions ) {
            $output .= '<li>';
            $output .= 'Priority: ' . $priority . "<br />\n";
            $output .= "<ul>\n";
            foreach ( $functions as $single_function ) {
                if ( ( !is_string( $single_function['function'] ) && !is_object( $single_function['function'] ) ) && ( !is_array( $single_function['function'] ) || ( is_array( $single_function['function'] ) && ( !is_string( $single_function['function'][0] ) && !is_object( $single_function['function'][0] ) ) ) ) ) {
                    // Type 1 - not a callback
                    continue;
                }
                elseif ( dbafa_is_closure( $single_function['function'] ) ) {
                    // Type 2 - closure
                    $output .= '<li>[<em>closure</em>]</li>';
                }
                elseif ( ( is_array( $single_function['function'] ) || is_object( $single_function['function'] ) ) && dbafa_is_closure( $single_function['function'][0] ) ) {
                    // Type 3 - closure within an array
                    $output .= '<li>[<em>closure</em>]</li>';
                }
                elseif ( is_string( $single_function['function'] ) && strpos( $single_function['function'], '::' ) === false ) {
                    // Type 4 - simple string function (includes lambda's)
                    $output .= '<li>' . sanitize_text_field( $single_function['function'] ) . '</li>';
                }
                elseif ( is_string( $single_function['function'] ) && strpos( $single_function['function'], '::' ) !== false ) {
                    // Type 5 - static class method calls - string
                    $output .= '<li>[<em>class</em>] ' . str_replace( '::', ' :: ', sanitize_text_field( $single_function['function'] ) ) . '</li>';
                }
                elseif ( is_array( $single_function['function'] ) && ( is_string( $single_function['function'][0] ) && is_string( $single_function['function'][1] ) ) ) {
                    // Type 6 - static class method calls - array
                    $output .= '<li>[<em>class</em>] ' . sanitize_text_field( $single_function['function'][0] ) . ' :: ' . sanitize_text_field( $single_function['function'][1] ) . '</li>';
                }
                elseif ( is_array( $single_function['function'] ) && ( is_object( $single_function['function'][0] ) && is_string( $single_function['function'][1] ) ) ) {
                    // Type 7 - object method calls
                    $output .= '<li>[<em>object</em>] ' . get_class( $single_function['function'][0] ) . ' -> ' . sanitize_text_field( $single_function['function'][1] ) . '</li>';
                }
                else {
                    // Type 8 - undetermined
                    $output .= '<li><pre>' . var_export( $single_function, true ) . '</pre></li>';
                }
            }
            $output .= "</ul>\n";
            $output .= "</li>\n";
        }
        $output .= "</ul>\n";
        $output .= "</li>\n";
    }
    $output .= "</ul>\n";
    $output .= "</div>\n";
    return $output;
}