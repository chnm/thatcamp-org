<?php

/**
 * Mods the admin bar
 */


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
