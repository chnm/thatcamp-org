<?php
/*
Plugin Name: Show authors without posts
Plugin URI: http://wordpress.org/support/topic/221525
Description: Shows the authors page even if the author has no post.
Version: 1.0.1
Author: Simon Br&uuml;chner
Author URI: http://www.bruechner.de
*/

if (!function_exists('show_authors_without_posts')) {
	
	function show_authors_without_posts($template) {
		global $wp_query;
		if( !is_author() && get_query_var('author') && (0 == $wp_query->posts->post) ) {
			// debug
			// echo 'Overwrite default 404 template...';
			return get_author_template();
		}
		return $template;
	}
	
	add_filter('404_template', 'show_authors_without_posts');
}
