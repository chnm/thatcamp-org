<?php

// @todo not use create_function()
function thatcamp_blogs_register_widgets() {
	global $wpdb;

	if ( bp_is_active( 'activity' ) && bp_is_active( 'blogs' ) && class_exists( 'BP_Blogs_Recent_Posts_Widget' ) && (int) $wpdb->blogid == bp_get_root_blog_id() ) {
		include( __DIR__ . '/includes/posts-widget.php' );
		add_action( 'widgets_init', create_function( '', 'unregister_widget( "BP_Blogs_Recent_Posts_Widget" ); return register_widget("THATCamp_Blogs_Recent_Posts_Widget");' ) );
	}

	if ( bp_is_active( 'groups' ) && (int) $wpdb->blogid == bp_get_root_blog_id() ) {
		include( __DIR__ . '/includes/groups-widget.php' );
		add_action( 'widgets_init', create_function( '', 'unregister_widget( "BP_Groups_Widget" ); return register_widget("THATCamp_Groups_Widget");' ) );
	}
}
add_action( 'bp_register_widgets', 'thatcamp_blogs_register_widgets', 20 );
