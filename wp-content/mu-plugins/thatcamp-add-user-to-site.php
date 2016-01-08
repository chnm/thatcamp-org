<?php

/**
 * For users who register via BuddyPress (/signup/), ensure that they are members of the main site so that they
 * are able to edit their profiles.
 */
function thatcamp_add_user_to_main_site_on_activation( $user_id ) {
	add_user_to_blog( bp_get_root_blog_id(), $user_id, 'subscriber' );
}
add_action( 'bp_core_activated_user', 'thatcamp_add_user_to_main_site_on_activation' );
