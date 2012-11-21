<?php
/*
Plugin Name: BP Import Blog Activity
Plugin URI: http://teleogistic.net/code/buddypress/bp-import-blog-activity
Description: Updates BuddyPress activity streams with missing blog comments and posts
Version: 0.2
Author: Boone Gorges
Author URI: http://boone.gorg.es
*/

/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function bp_import_blog_activity_init() {
	require( dirname( __FILE__ ) . '/bp-import-blog-activity-bp-functions.php' );
}
add_action( 'bp_include', 'bp_import_blog_activity_init' );

?>
