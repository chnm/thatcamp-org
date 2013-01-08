<?php
/**
 * Builds a view that contains posts from all blogs.
 */

function ms_global_search_build_views_drop( $trigger ) {
    global $wpdb;

    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE blog_id != {$trigger} AND site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC" ) );
	ms_global_search_v_query( $blogs );
}

function ms_global_search_build_views_add() {
    global $wpdb;
	
    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC" ) );
    ms_global_search_v_query( $blogs );
}

function ms_global_search_build_views_activate( $trigger ) {
    global $wpdb;

    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE ( blog_id = {$trigger} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ) OR ( site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ) ORDER BY registered DESC" ) );
    
    ms_global_search_v_query( $blogs );
}

function ms_global_search_build_views_unarchive( $trigger ) {
    global $wpdb;

    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE ( blog_id = {$trigger} AND public = '1' AND deleted = '0' AND mature = '0' AND spam = '0' ) OR ( site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ) ORDER BY registered DESC" ) );
    ms_global_search_v_query( $blogs );
}

/*
 * Build search views.
 */
function ms_global_search_v_query( $blogs ) {
    global $wpdb;
    
    $i = 0;
    $posts_select_query = '';
    $postmeta_select_query = '';
    $comments_select_query = '';
    foreach ( $blogs as $blog ) {
        if ( $i != 0 ) {
            $posts_select_query    .= ' UNION ';
            $postmeta_select_query .= ' UNION ';
            $comments_select_query .= ' UNION ';
        }
        
        // Get blog prefix
        $blogprefix = $wpdb->get_blog_prefix( $blog->blog_id );
        
        $posts_select_query .= " ( SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, posts{$blog->blog_id}.ID,
            posts{$blog->blog_id}.post_author, posts{$blog->blog_id}.post_date, posts{$blog->blog_id}.post_date_gmt, posts{$blog->blog_id}.post_content, 
            posts{$blog->blog_id}.post_title, posts{$blog->blog_id}.post_excerpt, posts{$blog->blog_id}.post_status, posts{$blog->blog_id}.comment_status,
            posts{$blog->blog_id}.ping_status, posts{$blog->blog_id}.post_password, posts{$blog->blog_id}.post_name, posts{$blog->blog_id}.to_ping,
            posts{$blog->blog_id}.pinged, posts{$blog->blog_id}.post_modified, posts{$blog->blog_id}.post_modified_gmt, posts{$blog->blog_id}.post_content_filtered,
            posts{$blog->blog_id}.post_parent, posts{$blog->blog_id}.guid, posts{$blog->blog_id}.menu_order, posts{$blog->blog_id}.post_type, 
            posts{$blog->blog_id}.post_mime_type, posts{$blog->blog_id}.comment_count
        FROM {$blogprefix}posts posts{$blog->blog_id} 
        WHERE posts{$blog->blog_id}.post_type != 'revision' AND (posts{$blog->blog_id}.post_status = 'publish' OR posts{$blog->blog_id}.post_status = 'private')) ";
        
        $postmeta_select_query .= " ( SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, postmeta{$blog->blog_id}.meta_id,
            postmeta{$blog->blog_id}.post_id, postmeta{$blog->blog_id}.meta_key, postmeta{$blog->blog_id}.meta_value
        FROM {$blogprefix}postmeta postmeta{$blog->blog_id} ) ";
        
        $comments_select_query .= " ( SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, comments{$blog->blog_id}.comment_ID,
            comments{$blog->blog_id}.comment_post_ID, comments{$blog->blog_id}.comment_author, comments{$blog->blog_id}.comment_author_email,
            comments{$blog->blog_id}.comment_author_url, comments{$blog->blog_id}.comment_author_IP, comments{$blog->blog_id}.comment_date,
            comments{$blog->blog_id}.comment_date_gmt, comments{$blog->blog_id}.comment_content, comments{$blog->blog_id}.comment_karma,
            comments{$blog->blog_id}.comment_approved, comments{$blog->blog_id}.comment_agent, comments{$blog->blog_id}.comment_type, 
            comments{$blog->blog_id}.comment_parent, comments{$blog->blog_id}.user_id 
       FROM {$blogprefix}comments comments{$blog->blog_id} ) ";
        
        $i++;
    }
    
    // Create or replace views.
    if( $blogs != null ) {
        // $wpdb->query("SET collation_connection = utf8_general_ci"); 
	    $v_query1  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_posts` AS ".$posts_select_query;
		if ( $wpdb->query( $wpdb->prepare( $v_query1 ) ) === false ) {
			wp_die( __( 'Error creating search views in the database. <a href="plugins.php">Deactivate Multisite Global Search</a> and check you have create views privilege in your WordPress database.', 'ms-global-search' ).'<br />'. $wpdb->last_error );
		}
		
		$v_query2  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_postmeta` AS ".$postmeta_select_query;
		if ( $wpdb->query( $wpdb->prepare( $v_query2 ) ) === false ) {
			wp_die( __( 'Error creating search views in the database. <a href="plugins.php">Deactivate Multisite Global Search</a> and check you have create views privilege in your WordPress database.', 'ms-global-search' ).'<br />'. $wpdb->last_error );
		}
		
		$v_query3  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_comments` AS ".$comments_select_query;
		if ( $wpdb->query( $wpdb->prepare( $v_query3 ) ) === false ) {
			wp_die( __( 'Error creating search views in the database. <a href="plugins.php">Deactivate Multisite Global Search</a> and check you have create views privilege in your WordPress database.', 'ms-global-search' ).'<br />'. $wpdb->last_error );
		}
    } else {
    	wp_die( __( '<strong>Multisite Global Search</strong></a> requires multisite installation. Please <a href="http://codex.wordpress.org/Create_A_Network">create a network</a> first, or <a href="plugins.php">deactivate Multisite Global Search</a>.', 'ms-global-search' ) );
    }
}

/*
 * Delete search views.
 */
function ms_global_search_drop_views() {
	global $wpdb;
	
	$wpdb->query( $wpdb->prepare( "DROP VIEW `{$wpdb->base_prefix}v_posts`" ) );
	$wpdb->query( $wpdb->prepare( "DROP VIEW `{$wpdb->base_prefix}v_postmeta`" ) );
	$wpdb->query( $wpdb->prepare( "DROP VIEW `{$wpdb->base_prefix}v_comments`" ) );
}
