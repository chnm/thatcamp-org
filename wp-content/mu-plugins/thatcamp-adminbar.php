<?php

/**
 * Mods the admin bar
 */

function thatcamp_clear_boone() {
	if ( is_super_admin() && ! empty( $_GET['clear_boone'] ) ) {
		global $wpdb;

		$blogs = $wpdb->get_results( "SELECT blog_id, domain FROM $wpdb->blogs" );
		$boone = $wpdb->get_var( "SELECT ID FROM $wpdb->users WHERE user_login = 'boone'" );

		$boone_camps = array( 2, 3, 21, 57, 77, 122, );
		foreach ( $blogs as $blog ) {
			if ( ! in_array( $blog->blog_id, $boone_camps ) ) {
				remove_user_from_blog( $boone, $blog->blog_id );
				echo $blog->domain . '<br />';
			}
		}
	}
}
//add_action( 'admin_init', 'thatcamp_clear_boone' );

function thatcamp_modify_admin_bar( $admin_bar ) {

	remove_filter( 'edit_profile_url', 'bp_members_edit_profile_url', 10, 3 );

	$admin_bar->add_node( array(
		'id'     => 'edit-profile',
		'href'   => get_edit_profile_url( get_current_user_id() ),
	) );

	$admin_bar->add_node( array(
		'id'     => 'user-info',
		'href'   => get_edit_profile_url( get_current_user_id() ),
	) );

	$admin_bar->add_node( array(
		'id'     => 'my-account',
		'href'   => get_edit_profile_url( get_current_user_id() ),
	) );

	add_filter( 'edit_profile_url', 'bp_members_edit_profile_url', 10, 3 );

	$admin_bar->add_node( array(
		'id'     => 'my-account-activity',
		'title'  => 'About Me',
	) );

	$admin_bar->add_node( array(
		'id'     => 'my-account-groups',
		'title'  => 'My THATCamps',
	) );

	$admin_bar->add_node( array(
		'id'     => 'my-account-friends',
		'title'  => 'My Friends',
	) );

	$admin_bar->remove_node( 'my-account-activity-mentions' );
	$admin_bar->remove_node( 'my-account-activity-personal' );
	$admin_bar->remove_node( 'my-account-activity-favorites' );
	$admin_bar->remove_node( 'my-account-activity-friends' );
	$admin_bar->remove_node( 'my-account-activity-groups' );

	$admin_bar->remove_node( 'my-account-blogs' );
	$admin_bar->remove_node( 'my-account-forums' );

	$admin_bar->remove_node( 'my-account-friends-friendships' );
	$admin_bar->remove_node( 'my-account-friends-requests' );

	$admin_bar->remove_node( 'my-account-groups-memberships' );
	$admin_bar->remove_node( 'my-account-groups-invites' );

	$admin_bar->remove_node( 'my-account-messages' );
	$admin_bar->remove_node( 'my-account-settings' );
	$admin_bar->remove_node( 'my-account-xprofile' );
}
add_action( 'admin_bar_menu', 'thatcamp_modify_admin_bar', 1000 );
