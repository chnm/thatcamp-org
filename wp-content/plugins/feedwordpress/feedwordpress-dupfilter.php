<?php
/*
Plugin Name: FeedWordPress Duplicate Post Filter
Plugin URI: http://www.byte-me.org/
Description: Checks DB to see if any previous posts have the same calculated hash
Author: Mark Allen
Version: 1.5
*/

/* 

Copyright (C) 2012 by Mark R. Allen
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted 
provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of 
      conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list 
      of conditions and the following disclaimer in the documentation and/or other 
      materials provided with the distribution.
    * Neither the name of Mark Allen nor the names of any contributors may be used to 
      endorse or promote products derived from this software without specific prior written 
      permission.

*/

function fwpdpf_check_duplicate ( $post ) 
{
    global $wpdb;

    $handle;
    $debug = 0;

    if ( $debug ) 
    {
        $wpdb->show_errors();

        $handle = fopen("duplicate_post_filter.log", "a+"); 
    }

    # strip whitespace and HTML/PHP tags
    $stripped_content = preg_replace('/\s+/', '', strip_tags($post['post_content']));

    # grab (at most) 1024 bytes and hash it
    $hash = sha1(substr($stripped_content, 0, 1024));

    $sql = $wpdb->prepare( "
            SELECT post_id FROM $wpdb->postmeta
            WHERE meta_key = '_dpf' AND meta_value = '%s'",
            $hash
    );     

    if ( $debug )
    {
        fprintf($handle, "%s", $sql);
    }

    $wpdb->query( $sql );

    if($wpdb->num_rows) //Already posted; discard
    {
        if ( $debug ) 
        {
            fprintf($handle, "%s", "...skipped\n\n");
            fclose($handle);
        }
        return NULL;
    }

    // Otherwise continue to process.

    return $post;
}

function fwpdpf_make_hash ( $postID ) 
{
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    if (!current_user_can('edit_post', $postID)) return;

    $post = get_post($postID);

    # strip whitespace, grab (at most) 1024 bytes and hash it
    $hash = sha1(substr(preg_replace('/\s+/', '', strip_tags($post->post_content)), 0, 1024));

    update_post_meta($postID, '_dpf', $hash);

}

add_action('syndicated_post', 'fwpdpf_check_duplicate');
add_action('save_post', 'fwpdpf_make_hash');
?>
