<?php

/**
 * This file provides the core functionality for the linking of BuddyPress
 * groups to WP sites
 *
 * Using this custom method because bp-groupblog provides too much overhead,
 * and doesn't really reflect the correct workflow anyway
 *
 * @author Boone Gorges
 */

/**
 * On blog creation, create a new group
 */
function thatcamp_create_group_for_new_blog( $blog_id  ) {
	// Assemble some data to create the group
	$create_args = array();

	// The group admin should be the blog admin. If no blog admin is found, default to Amanda (id 7)
	$blog_admin = get_user_by( 'email', get_blog_option( $blog_id, 'admin_email' ) );
	$create_args['creator_id'] = is_a( $blog_admin, 'WP_User' ) ? $blog_admin->ID : '7';

	$create_args['name'] = get_blog_option( $blog_id, 'blogname' );
	$create_args['description'] = $create_args['name'];

	$create_args['slug'] = sanitize_title( $create_args['name'] );

	$create_args['status'] = 'public';
	$create_args['enable_forum'] = false;
	$create_args['date_created'] = bp_core_current_time();

	$group_id = groups_create_group( $create_args );

	groups_update_groupmeta( $group_id, 'blog_id', $blog_id );

	groups_update_groupmeta( $group_id, 'total_member_count', 1 );
	groups_update_groupmeta( $group_id, 'invite_status', 'members' );

	return $group_id;
}
add_action( 'wpmu_new_blog', 'thatcamp_create_group_for_new_blog' );

/**
 * Get a blog's group_id
 *
 * @param int $blog_id
 * @return int|bool Returns a blog id if one is found; otherwise returns false
 */
function thatcamp_get_blog_group( $blog_id = 0 ) {
	global $wpdb, $bp;

	$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'blog_id' AND meta_value = %d", $blog_id ) );

	$retval = $group_id ? (int) $group_id : false;
	return $retval;
}

/**************************************************
 * MIGRATION
 *************************************************/

/**
 * Create groups for existing blogs
 *
 * This is for migrating existing blogs to the new group setup. To use, visit
 * wp-admin/network/?migrate_existing_blogs=1 as a super admin
 */
function thatcamp_migrate_existing_blogs() {
	global $wpdb;

	if ( ! is_network_admin() || ! is_super_admin() ) {
		return;
	}

	if ( empty( $_GET['migrate_existing_blogs'] ) ) {
		return;
	}

	$blog_ids = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->blogs" ) );

	// Skip the main site (and maybe others?)
	$exclude_blog_ids = array( 1 );
	foreach ( $blog_ids as $blog ) {
		$blog_id = $blog->blog_id;

		if ( in_array( $blog_id, $exclude_blog_ids ) ) {
			continue;
		}

		// If there's already a group, skip it
		if ( thatcamp_get_blog_group( $blog_id ) ) {
			continue;
		}

		$group_id = thatcamp_create_group_for_new_blog( $blog_id );

		// Get a real last activity time
		$last_post = $wpdb->get_var( $wpdb->prepare( "SELECT post_modified FROM {$wpdb->get_blog_prefix( $blog_id )}posts ORDER BY post_modified DESC LIMIT 1" ) );
		$last_comment = $wpdb->get_var( $wpdb->prepare( "SELECT comment_date FROM {$wpdb->get_blog_prefix( $blog_id )}comments ORDER BY comment_date DESC LIMIT 1" ) );
		$last_activity = strtotime( $last_post ) > strtotime( $last_comment ) ? $last_post : $last_comment;
		groups_update_groupmeta( $group_id, 'last_activity', $last_activity );

		// Run the member sync
		thatcamp_group_blog_member_sync( $blog_id );
	}
}
add_action( 'admin_init', 'thatcamp_migrate_existing_blogs' );

/**
 * Sync blog membership to group
 */
function thatcamp_group_blog_member_sync( $blog_id = 0 ) {
	$group_id = thatcamp_get_blog_group( $blog_id );

	if ( ! $group_id ) {
		return;
	}

	// Call up blog users
	$users = new WP_User_Query( array( 'blog_id' => $blog_id ) );

	foreach ( $users->results as $user ) {
		$caps_key = 'wp_' . $blog_id . '_capabilities';
		$caps = get_user_meta( $user->ID, $caps_key, true );

		$new_member                = new BP_Groups_Member;
		$new_member->group_id      = $group_id;
		$new_member->user_id       = $user->ID;
		$new_member->inviter_id    = 0;
		$new_member->is_admin      = 0;
		$new_member->user_title    = '';
		$new_member->date_modified = bp_core_current_time();
		$new_member->is_confirmed  = 1;
		$new_member->save();

		groups_update_groupmeta( $group_id, 'total_member_count', (int) groups_get_groupmeta( $group_id, 'total_member_count') + 1 );

		if ( isset( $caps['administrator'] ) ) {
			groups_promote_member( $user->ID, $group_id, 'admin' );
		} else if ( isset( $caps['editor'] ) ) {
			groups_promote_member( $user->ID, $group_id, 'mod' );
		}
	}
}
