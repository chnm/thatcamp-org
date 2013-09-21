<?php
/**
 * Graphene WordPress Theme, Copyright 2010-2013 Syahir Hakim
 * Graphene is distributed under the terms of the GNU GPL version 3
 *
 * Graphene functions and definitions
 *
 * @package Graphene
 * @since Graphene 1.0
 */
define( 'GRAPHENE_ROOTDIR', dirname( __FILE__ ) );
define( 'GRAPHENE_ROOTURI', get_template_directory_uri() ); 

 
/**
 * Before we do anything, let's get the mobile extension's init file if it exists
*/
$mobile_path = dirname( dirname( __FILE__ ) ) . '/graphene-mobile/includes/theme-plugin.php';
if ( file_exists( $mobile_path ) ) { include( $mobile_path ); }


/**
 * Load the various theme files
*/
global $graphene_settings;
require( GRAPHENE_ROOTDIR . '/admin/options-init.php' );					// Theme options and admin interface setup
require( GRAPHENE_ROOTDIR . '/includes/theme-scripts.php' );		// Theme stylesheets and scripts
require( GRAPHENE_ROOTDIR . '/includes/theme-utils.php' );		// Theme utilities
require( GRAPHENE_ROOTDIR . '/includes/theme-head.php' );			// Functions for output into the HTML <head> element
require( GRAPHENE_ROOTDIR . '/includes/theme-menu.php' );			// Functions for navigation menus
require( GRAPHENE_ROOTDIR . '/includes/theme-loop.php' );			// Functions for posts/pages loops
require( GRAPHENE_ROOTDIR . '/includes/theme-comments.php' );		// Functions for comments
require( GRAPHENE_ROOTDIR . '/includes/theme-slider.php' );		// Functions for the slider
require( GRAPHENE_ROOTDIR . '/includes/theme-panes.php' );		// Functions for the homepage panes
require( GRAPHENE_ROOTDIR . '/includes/theme-plugins.php' );		// Native plugins support
require( GRAPHENE_ROOTDIR . '/includes/theme-shortcodes.php' );	// Theme shortcodes
require( GRAPHENE_ROOTDIR . '/includes/theme-webfonts.php' );		// Theme webfonts
require( GRAPHENE_ROOTDIR . '/includes/theme-compat.php' );		// For backward compatibility
require( GRAPHENE_ROOTDIR . '/includes/theme-functions.php' );	// Other functions that are not categorised above
require( GRAPHENE_ROOTDIR . '/includes/theme-setup.php' );		// Theme setup