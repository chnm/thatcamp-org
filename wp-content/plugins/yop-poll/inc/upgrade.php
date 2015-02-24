<?php
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( array( 'title'  => 'Update Yop Poll',
                                                                      'plugin' => 'yop_poll_2.0'
                                                               ) ) );

    $plugin_slug = basename( dirname( __FILE__ ) );
    $plugin      = $plugin_slug . "/" . $plugin_slug . ".php";
    if( is_plugin_active( $plugin ) ) {
        deactivate_plugins( $plugin );
    }

  /*  $plugin_url = @$upgrader->run( array(
                                       'package'                     => $download_link,
                                       'destination'                 => WP_PLUGIN_DIR . "/" . $plugin_slug . "/",
                                       'clear_destination'           => false,
                                       'abort_if_destination_exists' => false,
                                       'clear_working'               => true,
                                       'is_multi'                    => true,
                                       'hook_extra'                  => array()
                                   ) ); */
    if( ! is_plugin_active( $plugin ) ) {
        activate_plugins( $plugin );
    }