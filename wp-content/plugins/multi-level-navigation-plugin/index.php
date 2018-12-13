<?php
/*

Plugin Name: Multi-level Navigation Plugin
Plugin URI: https://geek.hellyer.kiwi/multi-level-navigation/
Description: A WordPress plugin which adds a multi-level CSS based dropdown/flyout/slider menu to your WordPress blog. Visit the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">WP Multi-level Navigation Plugin page</a> for more information about the plugin.
Author: Ryan Hellyer
Version: 2.3.8
Author URI: https://geek.hellyer.kiwi/

Copyright (c) 2008 Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.


So yer readin' the source code eh? Apologies if things are a bit messy in here. This is 
one of the first plugins I ever released, and it showed in the source code. The plugin was
somewhat of a disaster code wise and I did a massive upgrade of it on April 4th 2012. In
the interests of not breaking things, I kept a lot of legacy code around which I knew wasn't 
likely to case any performance or security issues, but has left a huge chunk of ugly looking 
code in the legacy.php file. There are also bits of similarly ugly code scattered about the
plugin code. Sorry about that, but I figured it was best to leave it ugly than risk breaking
things by messing with it. As the saying goes, "If it aint broke, don't fix it!".

If you find any bug fixes, please let me know via ryanhellyer@gmail.com, but please be aware that
I am not intending to ever add any more features to this plugin. I'm purely maintaining to ensure
that existing users have a good experience and don't lose access to the plugin.

*/


/**
 * Get options from array in database
 * Convenient wrapper for getting specific key values from arrays in options
 * 
 * Must be loaded early for use in dynamic CSS file
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @params $which  Allows selection of which menu to display
 * @return string
 */
function get_mlnmenu_option( $option ) {
	$options = get_option( 'pixopoint-menu' );
	if ( isset( $options[$option] ) )
		return $options[$option];
}

/**
 * Define constants
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
define( 'MULTILEVELNAVIGATION_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'MULTILEVELNAVIGATION_URL', WP_PLUGIN_URL . '/' . basename( MULTILEVELNAVIGATION_DIR ) ); // Plugin folder URL
define( 'MULTILEVELNAVIGATION_VERSION', '2.3.6' );
define( 'MULTILEVELNAVIGATION_AD', "\n<!-- Multi-level Navigation Plugin v" . MULTILEVELNAVIGATION_VERSION ." by Ryan Hellyer ... https://geek.hellyer.kiwi/multi-level-navigation/ -->\n" );

/**
 * Serve CSS
 * 
 * Must be loaded early to ensure rapid creation of CSS file
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
if ( isset( $_GET['mlnmenu'] ) ) {
	if ( 'css' == $_GET['mlnmenu'] ) {
		header( 'Content-Type: text/css; charset=UTF-8' ); // Setting http headers
		$css = get_mlnmenu_option( 'css' ); // Grabbing CSS from DB
		$css = str_replace( '../multi-level-navigation-plugin/images', MULTILEVELNAVIGATION_URL . '/images', $css ); // Add image URLs into place
		$css = str_replace( '(images/', '(' . MULTILEVELNAVIGATION_URL . '/images/', $css ); // Add image URLs into place
		echo $css; // Spit out CSS onto page
		die; // Kill execution since only CSS is needed
	}
}

/**
 * Including required files
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
require( 'core.php' );
require( 'legacy.php' );
require( 'admin_page.php' );

/**
 * Output HTML to theme
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @params $which  Allows selection of which menu to display
 */
function pixopoint_menu( $which = 1 ) {
	echo MULTILEVELNAVIGATION_AD;
	mln_legacy_menu( $which );
}

/**
 * Instantiate classes
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
if ( !is_admin() )
	new MultiLevelNavigationCore();
else
	new MultiLevelNavigationAdmin();
