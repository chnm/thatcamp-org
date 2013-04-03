<?php

/**
 * Misc tools
 *
 * DON'T TOUCH
 */

function thatcamp_remove_users_from_blog() {
        if ( ! is_super_admin() ) {
                return;
        }

        if ( ! empty( $_GET['bbg_remove_users_from_blog'] ) ) {
                $blog_id = 168;
                $users_to_keep = array( 852 );

                $users = get_users( array(
                        'blog_id' => $blog_id,
                        'number' => 220,
                        'fields' => '',
                ) );

                foreach ( $users as $user ) {
                        if ( in_array( $user, $users_to_keep ) ) {
                                echo 'Keeping ' . $user;
                        } else {
                                echo 'Dumping ' . $user;
                                remove_user_from_blog( $user, $blog_id );
//                                $user_object = new WP_User( $user );
 //                               $user_object->set_role( '' );
                        }

                        echo '<br />';
                }
        }
}
//add_action( 'admin_head', 'thatcamp_remove_users_from_blog' );

function thatcamp_remove_users_from_blog_i() {
        $blog_id = 168;
        $users_to_keep = array( 852 );

        $users = get_users( array(
                'blog_id' => $blog_id,
                'number' => 5,
                'fields' => '',
        ) );

        foreach ( $users as $user ) {
                if ( in_array( $user, $users_to_keep ) ) {
//                        echo 'Keeping ' . $user;
                } else {
//                        echo 'Dumping ' . $user;
                        remove_user_from_blog( $user, $blog_id );
//                                $user_object = new WP_User( $user );
//                               $user_object->set_role( '' );
                }

                echo '<br />';
        }
}
//register_shutdown_function( 'thatcamp_remove_users_from_blog_i' );
