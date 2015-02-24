<?php
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    $plugin_slug = basename( dirname( __FILE__ ) );
    $plugin      = $plugin_slug . "/" . 'yop_poll' . ".php";
$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( array(
    'title'  => 'Yop Poll 2.0 Plugin',
    'plugin' => $plugin_slug.'/yop_poll.php'
) ) );

$options = get_option( 'yop_poll_options' );
$options ['vote_permisions_facebook'] = "no";
$options ['vote_permisions_google'] = "no";
$options ['vote_permisions_facebook_label']            = __yop_poll( 'Vote as Facebook User' );
$options ['vote_permisions_google_label']            = __yop_poll( 'Vote as G+ User' );
$options ['facebook_share_description']            = __yop_poll( 'Just casted an YOP Poll vote on ' ) . get_bloginfo( 'name' );
$options ['show_google_share_button']            ="no";
$options ['facebook_share_after_vote']            ="no";
$options ['google_integration']            ="no";
$options ['facebook_integration']            ="no";
$options ['user_interface_type']            ="beginner";
$options ['is_default_other_answer']            ="no";
$options ['facebook_show_comments_widget']            ="no";

update_option( 'yop_poll_options', $options );
$upgrader->init();
    if( is_plugin_active( $plugin ) ) {
        deactivate_plugins( $plugin );
    }

    $result = @$upgrader->run( array(
                                   'package'                     => $download_link,
                                   'destination'                 => WP_PLUGIN_DIR . "/" . $plugin_slug . "/",
                                   'clear_destination'           => false,
                                   'abort_if_destination_exists' => false,
                                   'clear_working'               => true,
                                   'is_multi'                    => true,
                                   'hook_extra'                  => array()
                               ) );

    if( ! is_wp_error( $result ) ) {
        if( ! is_plugin_active( $plugin ) ) {
            $pro_options = get_option( "yop_poll_pro" );
            unset( $pro_options['rand_number'] );
            update_option( "yop_poll_pro", $pro_options );
            activate_plugins( $plugin );

        }
    }
Yop_Poll_DbSchema::add_defaults_to_database();
    wp_die();