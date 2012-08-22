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

		// Assemble some data to create the group
		$create_args = array();

		$blog_admin = get_user_by( 'email', get_blog_option( $blog_id, 'admin_email' ) );
		$create_args['creator_id'] = is_a( $blog_admin, 'WP_User' ) ? $blog_admin->ID : '7';

		$create_args['name'] = get_blog_option( $blog_id, 'blogname' );
		$create_args['description'] = $create_args['name'];

		$create_args['slug'] = sanitize_title( $create_args['name'] );

		$create_args['status'] = 'public';
		$create_args['enable_forum'] = false;
		$create_args['date_created'] = bp_core_current_time();

		$group_id = groups_create_group( $create_args );

		groups_update_groupmeta( $group_id, 'total_member_count', 1 );
		groups_update_groupmeta( $group_id, 'invite_status', 'members' );

		// Get a real last activity time. Tricky.
		$last_post = $wpdb->get_var( $wpdb->prepare( "SELECT post_modified FROM {$wpdb->get_blog_prefix( $blog_id )}posts ORDER BY post_modified DESC LIMIT 1" ) );
		$last_comment = $wpdb->get_var( $wpdb->prepare( "SELECT comment_date FROM {$wpdb->get_blog_prefix( $blog_id )}comments ORDER BY comment_date DESC LIMIT 1" ) );
		$last_activity = strtotime( $last_post ) > strtotime( $last_comment ) ? $last_post : $last_comment;
		groups_update_groupmeta( $group_id, 'last_activity', $last_activity );
	}
}
add_action( 'admin_init', 'thatcamp_migrate_existing_blogs' );

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
?>
