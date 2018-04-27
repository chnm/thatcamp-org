<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$keep = get_option( 'vscf-setting' );
if ( $keep != 'yes' ) {
	// Delete option
	delete_option( 'widget_vscf-widget');
	delete_option( 'vscf-setting' );
	delete_option( 'vscf-setting-2' );
	delete_option( 'vscf-setting-3' );
	delete_option( 'vscf-setting-4' );
	delete_option( 'vscf-setting-5' );
	delete_option( 'vscf-setting-6' );
	delete_option( 'vscf-setting-7' );
	delete_option( 'vscf-setting-8' );
	delete_option( 'vscf-setting-9' );
	delete_option( 'vscf-setting-10' );
	delete_option( 'vscf-setting-11' );
	delete_option( 'vscf-setting-12' );
	delete_option( 'vscf-setting-13' );
	delete_option( 'vscf-setting-14' );
	delete_option( 'vscf-setting-15' );
	delete_option( 'vscf-setting-16' );
	delete_option( 'vscf-setting-17' );
	delete_option( 'vscf-setting-18' );
	delete_option( 'vscf-setting-19' );

	// For site options in Multisite
	delete_site_option( 'widget_vscf-widget' );
	delete_site_option( 'vscf-setting' );
	delete_site_option( 'vscf-setting-2' );
	delete_site_option( 'vscf-setting-3' );
	delete_site_option( 'vscf-setting-4' );
	delete_site_option( 'vscf-setting-5' );
	delete_site_option( 'vscf-setting-6' );
	delete_site_option( 'vscf-setting-7' );
	delete_site_option( 'vscf-setting-8' );
	delete_site_option( 'vscf-setting-9' );
	delete_site_option( 'vscf-setting-10' );
	delete_site_option( 'vscf-setting-11' );
	delete_site_option( 'vscf-setting-12' );
	delete_site_option( 'vscf-setting-13' );
	delete_site_option( 'vscf-setting-14' );
	delete_site_option( 'vscf-setting-15' );
	delete_site_option( 'vscf-setting-16' );
	delete_site_option( 'vscf-setting-17' );
	delete_site_option( 'vscf-setting-18' );
	delete_site_option( 'vscf-setting-19' );

	// Set global
	global $wpdb;

	// Delete submissions
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'submission'" ); 
}
