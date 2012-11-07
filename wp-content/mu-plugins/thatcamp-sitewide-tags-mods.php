<?php

/**
 * Modifications related to Sitewide Tags
 */

/**
 * Prevent normal users from accessing the Tags blog
 */
function thatcamp_protect_st_blog() {
	if ( function_exists( 'get_sitewide_tags_option' ) ) {
		if ( ! is_super_admin() && get_current_blog_id() == get_sitewide_tags_option( 'tags_blog_id' ) ) {
			wp_redirect( get_blog_option( 1, 'siteurl' ) );
		}
	}
}
add_action( 'wp', 'thatcamp_protect_st_blog', 1 );
