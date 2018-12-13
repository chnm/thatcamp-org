<?php
/**
 * Multi-level Navigation plugin uninstaller
 * Deletes all of the data stored in the database
 * 
 * Includes both current data and historical leftover data
 * 
 * @package    WordPress
 * @subpackage Multi-level Navigation
 */



/**
 * Ensures that no one can access this script directly
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

/**
 * Delete database option
 * Used as of version 2.3
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
delete_option( 'pixopoint-menu' );

/**
 * Deletes legacy options
 * 
 * Deletes data from versions of plugin older than 2.3
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
// Main menu items
delete_option( 'suckerfish_menuitem1' );
delete_option( 'suckerfish_menuitem2' );
delete_option( 'suckerfish_menuitem3' );
delete_option( 'suckerfish_menuitem4' );
delete_option( 'suckerfish_menuitem5' );
delete_option( 'suckerfish_menuitem6' );
delete_option( 'suckerfish_menuitem7' );
delete_option( 'suckerfish_menuitem8' );
delete_option( 'suckerfish_menuitem9' );
delete_option( 'suckerfish_menuitem10' );

// Second menu items
delete_option( 'suckerfish_2_menuitem1' );
delete_option( 'suckerfish_2_menuitem2' );
delete_option( 'suckerfish_2_menuitem3' );
delete_option( 'suckerfish_2_menuitem4' );
delete_option( 'suckerfish_2_menuitem5' );
delete_option( 'suckerfish_2_menuitem6' );
delete_option( 'suckerfish_2_menuitem7' );
delete_option( 'suckerfish_2_menuitem8' );
delete_option( 'suckerfish_2_menuitem9' );
delete_option( 'suckerfish_2_menuitem10' );

// Everything else
delete_option( 'suckerfish_css' );
delete_option( 'suckerfish_superfish' );
delete_option( 'suckerfish_superfish_speed' );
delete_option( 'suckerfish_superfish_time' );
delete_option( 'suckerfish_superfish_timeout' );
delete_option( 'suckerfish_pagestitle' );
delete_option( 'suckerfish_keyboard' );
delete_option( 'suckerfish_excludepages' );
delete_option( 'suckerfish_excludecategories' );
delete_option( 'suckerfish_hometitle' );
delete_option( 'suckerfish_pagestitle' );
delete_option( 'suckerfish_categoriestitle' );
delete_option( 'suckerfish_archivestitle' );
delete_option( 'suckerfish_blogrolltitle' );
delete_option( 'suckerfish_recentcommentstitle' );
delete_option( 'suckerfish_recentpoststitle' );
delete_option( 'suckerfish_disablecss' );
delete_option( 'suckerfish_custommenu' );
delete_option( 'suckerfish_custommenu2' );
delete_option( 'suckerfish_custommenu3' );
delete_option( 'suckerfish_custommenu4' );
delete_option( 'suckerfish_inlinecss' );
delete_option( 'suckerfish_includeexcludepages' );
delete_option( 'suckerfish_2_css' );
delete_option( 'suckerfish_2_pagestitle' );
delete_option( 'suckerfish_2_excludepages' );
delete_option( 'suckerfish_2_excludecategories' );
delete_option( 'suckerfish_2_hometitle' );
delete_option( 'suckerfish_2_pagestitle' );
delete_option( 'suckerfish_2_categoriestitle' );
delete_option( 'suckerfish_2_archivestitle' );
delete_option( 'suckerfish_2_blogrolltitle' );
delete_option( 'suckerfish_2_recentcommentstitle' );
delete_option( 'suckerfish_2_disablecss' );
delete_option( 'suckerfish_2_custommenu' );
delete_option( 'suckerfish_2_custommenu2' );
delete_option( 'suckerfish_2_inlinecss' );
delete_option( 'suckerfish_2_includeexcludepages' );
delete_option( 'suckerfish_generator' );
delete_option( 'suckerfish_delay' );
delete_option( 'suckerfish_superfish_shadows' );
delete_option( 'suckerfish_superfish_arrows' );
delete_option( 'suckerfish_showdelay' );
delete_option( 'suckerfish_displaycss' );
delete_option( 'suckerfish_secondmenu' );
delete_option( 'osort_order' );
delete_option( 'suckerfish_superfish_delaymouseover' );
delete_option( 'suckerfish_superfish_hoverintent' );
delete_option( 'suckerfish_superfish_sensitivity' );
delete_option( 'suckerfish_maintenance' );
delete_option( 'suckerfish_categoryorder' );
delete_option( 'suckerfish_homeurl' );
delete_option( 'suckerfish_pagesurl' );
delete_option( 'suckerfish_categoriesurl' );
delete_option( 'suckerfish_archivesurl' );
delete_option( 'suckerfish_blogrollurl' );
delete_option( 'suckerfish_recentcommentsurl' );
delete_option( 'suckerfish_recentpostsurl' );
delete_option( 'suckerfish_depthcategories' );
delete_option( 'suckerfish_depthpages' );
delete_option( 'suckerfish_categorycount' );
delete_option( 'suckerfish_categoryshowempty' );
delete_option( 'suckerfish_delay' );
delete_option( 'suckerfish_titletags' );
delete_option( 'suckerfish_recentpostsnumber' );
delete_option( 'suckerfish_recentcommentsnumber' );
