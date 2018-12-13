<?php
/**
 * Multi-level Navigation plugin core
 * 
 * @package    WordPress
 * @subpackage Multi-level Navigation
 */



// If old CSS from previous version is NOT present, then bail out now.
$css = get_option( 'suckerfish_css' );
if ( empty( $css ) )
	return;

// If new data from current version is present, then bail out now
$new_data = get_option( 'pixopoint-menu' );
if ( !empty( $new_data ) )
	return;



// Adds various options for admin page menu
$array = array(
	'css'                      => get_option( 'suckerfish_css' ),
	'superfish'                => get_option( 'suckerfish_superfish' ),
	'superfish_speed'          => get_option( 'suckerfish_superfish_speed' ),
	'superfish_time'           => get_option( 'suckerfish_superfish_time' ),
	'superfish_timeout'        => get_option( 'suckerfish_superfish_timeout' ),
	'menuitem1'                => get_option( 'suckerfish_menuitem1' ),
	'menuitem2'                => get_option( 'suckerfish_menuitem2' ),
	'menuitem3'                => get_option( 'suckerfish_menuitem3' ),
	'menuitem4'                => get_option( 'suckerfish_menuitem4' ),
	'menuitem5'                => get_option( 'suckerfish_menuitem5' ),
	'menuitem6'                => get_option( 'suckerfish_menuitem6' ),
	'menuitem7'                => get_option( 'suckerfish_menuitem7' ),
	'menuitem8'                => get_option( 'suckerfish_menuitem8' ),
	'menuitem9'                => get_option( 'suckerfish_menuitem9' ),
	'menuitem10'               => get_option( 'suckerfish_menuitem10' ),
	'hometitle'                => get_option( 'suckerfish_hometitle' ),
	'pagestitle'               => get_option( 'suckerfish_pagestitle' ),
	'categoriestitle'          => get_option( 'suckerfish_categoriestitle' ),
	'archivestitle'            => get_option( 'suckerfish_archivestitle' ),
	'blogrolltitle'            => get_option( 'suckerfish_blogrolltitle' ),
	'recentcommentstitle'      => get_option( 'suckerfish_recentcommentstitle' ),
	'recentpoststitle'         => get_option( 'suckerfish_recentpoststitle' ),
	'keyboard'                 => get_option( 'suckerfish_keyboard' ),
	'disablecss'               => get_option( 'suckerfish_disablecss' ),
	'inlinecss'                => get_option( 'suckerfish_inlinecss' ),
	'superfish_delaymouseover' => get_option( 'suckerfish_superfish_delaymouseover' ),
	'superfish_sensitivity'    => get_option( 'suckerfish_superfish_sensitivity' ),
	'maintenance'              => get_option( 'suckerfish_maintenance' ),
	'2_css'                    => get_option( 'suckerfish_2_css' ),
	'2_menuitem1'              => get_option( 'suckerfish_2_menuitem1' ),
	'2_menuitem2'              => get_option( 'suckerfish_2_menuitem2' ),
	'2_menuitem3'              => get_option( 'suckerfish_2_menuitem3' ),
	'2_menuitem4'              => get_option( 'suckerfish_2_menuitem4' ),
	'2_menuitem5'              => get_option( 'suckerfish_2_menuitem5' ),
	'2_menuitem6'              => get_option( 'suckerfish_2_menuitem6' ),
	'2_menuitem7'              => get_option( 'suckerfish_2_menuitem7' ),
	'2_menuitem8'              => get_option( 'suckerfish_2_menuitem8' ),
	'2_menuitem9'              => get_option( 'suckerfish_2_menuitem9' ),
	'2_menuitem10'             => get_option( 'suckerfish_2_menuitem10' ),
	'categoryorder'            => get_option( 'suckerfish_categoryorder' ),
	'categoryorder'            => get_option( 'suckerfish_categoryorder' ),
	'categoryorder'            => get_option( 'suckerfish_categoryorder' ),
	'categorycount'            => get_option( 'suckerfish_categorycount' ),
	'titletags'                => get_option( 'suckerfish_titletags' ),
	'recentpostsnumber'        => get_option( 'suckerfish_recentpostsnumber' ),
	'recentcommentsnumber'     => get_option( 'suckerfish_recentcommentsnumber' ),
	'includeexcludecategories' => get_option( 'suckerfish_includeexcludecategories' ),
	'delay'                    => get_option( 'suckerfish_delay' ),
	'displaycss'               => get_option( 'suckerfish_displaycss' ),
);
add_option( 'pixopoint-menu', $array );
