<?php

/**
 * In WP 3.5+, get_blogs_of_user() is much slower than in previous versions. As
 * a result, if you have a certain number of blogs, running get_blogs_of_user()
 * will create a memory timeout. This is a particular problem because
 * get_blogs_of_user() is called on every page, because of the toolbar.
 *
 * Ideally, there would be a way to short-circuit get_blogs_of_user(), or even
 * to prevent WordPress from calling get_blogs_of_user() while loading the
 * toolbar. But there is not. As a workaround, this function intercepts a key
 * step in get_blogs_of_user() - the get_user_meta() call that gets all of a
 * user's metadata. If we determine that this request is coming from
 * get_blogs_of_user() (which we do by examining the debug_backtrace(), a truly
 * awful technique), AND that it's one of the generic meta queries used by
 * get_blogs_of_user(), AND that the current user has more than 75 blogs, THEN
 * we strip all of the blog capability keys from the array of metadata, 
 * tricking get_blogs_of_user() into thinking that the current user has no
 * blogs at all.
 */
function baruch_fucking_remove_fucking_hack_garbage( $check, $object_id, $meta_key, $single ) {
        // Only fire when looking at get_user_meta() with no params
        if ( ! $meta_key ) {

                // check to see whether this came from get_blogs_of_user()
                $db = debug_backtrace();
                $is_get_blogs_of_user = false;
                foreach ( $db as $dbk => $dbv ) {
                        if ( 'get_blogs_of_user' == $dbv['function'] ) {
                                $is_get_blogs_of_user = true;
                                break;
                        }
                }

                if ( $is_get_blogs_of_user ) {
                        // Get the real metadata, but don't recurse
                        remove_filter( 'get_user_metadata', 'baruch_fucking_remove_fucking_hack_garbage', 10, 4 );
                        $meta = get_user_meta( $object_id );
                        add_filter( 'get_user_metadata', 'baruch_fucking_remove_fucking_hack_garbage', 10, 4 );

                        // How many blogs does this user have?
                        static $blog_count_of_user;
                        if ( ! isset( $blog_count_of_user ) && is_user_logged_in() ) {
                                $blog_count_of_user = 0;
                                foreach ( $meta as $mk => $mv ) {
                                        if ( 'capabilities' === substr( $mk, -12 ) ) {
                                                $blog_count_of_user++;
                                        }
                                }
                        }

                        // We only care about those with counts > 75
                        if ( $blog_count_of_user > 75 ) {
                                static $clean_keys;
                                if ( isset( $clean_keys ) ) {
                                        return $clean_keys;
                                } else {
                                        foreach ( $meta as $mk => $mv ) {
                                                if ( 'capabilities' === substr( $mk, -12 ) ) {
                                                       unset( $meta[ $mk ] ); 
                                                }
                                        }

                                        $clean_keys = $meta;
                                        return $meta;
                                }
                        }
                }
        
        }

        return $check;
}
add_filter( 'get_user_metadata', 'baruch_fucking_remove_fucking_hack_garbage', 10, 4 );

