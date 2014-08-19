<?php
    /*
     * Plugin Name: Yop Poll Plugin
     * URI: http://www.yop-poll.com/thankyou/
     * Description: Use a full option polling functionality to get the answers you need. YOP Poll is the perfect, easy to use plugin for your WordPress website.
     * Author: yourownprogrammer
     * Author URL: http://www.yop-poll.com/thankyou/
     * Version: 4.9.2
     * Network: false
     */
    define ( 'YOP_POLL_WP_VERSION', '3.3' );
    define ( 'YOP_POLL_VERSION', '4.9.2' );
    define ( 'YOP_POLL_PATH', plugin_dir_path( __FILE__ ) );
    define ( 'YOP_POLL_URL', plugins_url( '', __FILE__ ) );
    define ( 'YOP_POLL_PLUGIN_FILE', plugin_basename( __FILE__ ) );
    define ( 'YOP_POLL_PLUGIN_DIR', plugin_basename( dirname( __FILE__ ) ) );
    define ( 'YOP_POLL_INC', YOP_POLL_PATH . 'inc' );

    require_once( YOP_POLL_INC . '/plugin.php' );
    require_once( YOP_POLL_INC . '/config.php' );
    require_once( YOP_POLL_INC . '/plugin-functions.php' );
    require_once( ABSPATH . WPINC . '/pluggable.php' );
    require_once( YOP_POLL_INC . '/public-admin.php' );
    require_once( YOP_POLL_INC . '/widget.php' );
    require_once( YOP_POLL_INC . '/theme-functions.php' );

// Yop Poll Tables Name
    global $wpdb;
    $wpdb->yop_poll_version             = YOP_POLL_VERSION;
    $wpdb->yop_polls                    = $wpdb->prefix . 'yop_polls';
    $wpdb->yop_poll_answers             = $wpdb->prefix . 'yop_poll_answers';
    $wpdb->yop_poll_templates           = $wpdb->prefix . 'yop_poll_templates';
    $wpdb->yop_poll_custom_fields       = $wpdb->prefix . 'yop_poll_custom_fields';
    $wpdb->yop_pollmeta                 = $wpdb->prefix . 'yop_pollmeta';
    $wpdb->yop_poll_answermeta          = $wpdb->prefix . 'yop_poll_answermeta';
    $wpdb->yop_poll_logs                = $wpdb->prefix . 'yop_poll_logs';
    $wpdb->yop_poll_voters              = $wpdb->prefix . 'yop_poll_voters';
    $wpdb->yop_poll_bans                = $wpdb->prefix . 'yop_poll_bans';
    $wpdb->yop_poll_votes_custom_fields = $wpdb->prefix . 'yop_poll_votes_custom_fields';
    $wpdb->yop_poll_facebook_users      = $wpdb->prefix . 'yop_poll_facebook_users';

    $yop_poll_current_class = 'Yop_Poll_';

    if ( is_admin() ){
        $yop_poll_current_class .= 'Admin';
        require_once( YOP_POLL_INC . '/admin.php' );
    }
    else {
        $yop_poll_current_class .= 'Public';
        require_once( YOP_POLL_INC . '/public.php' );
    }
    $yop_poll_config_data = array(
        'plugin_file' => YOP_POLL_PLUGIN_FILE,
        'plugin_url' => YOP_POLL_URL,
        'plugin_path' => YOP_POLL_PATH,
        'plugin_dir' => YOP_POLL_PLUGIN_DIR,
        'plugin_inc_dir' => YOP_POLL_INC,
        'languages_dir' => 'languages',
        'min_number_of_answers' => 1,
        'min_number_of_customfields' => 0,
        'version' => YOP_POLL_VERSION
    );

    $yop_poll_public_admin = new Yop_Poll_Public_Admin ( new Yop_Poll_Config ( $yop_poll_config_data ) );
    $yop_poll              = new $yop_poll_current_class ( new Yop_Poll_Config ( $yop_poll_config_data ) );
    function yop_poll_uninstall() {
        global $wpdb;
        if ( function_exists( 'is_multisite' ) && is_multisite() ){
            $old_blog = $wpdb->blogid;
            $blogids  = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
            foreach ( $blogids as $blog_id ) {
                switch_to_blog( $blog_id );
                delete_option( 'yop_poll_version' );
                delete_option( 'yop_poll_options' );
                delete_option( 'yop_poll_first_install_date' );
                delete_option( 'yop_poll_admin_notices_donate' );
                delete_option( 'yop_poll_optin_box_modal_options' );
                delete_option( 'yop_poll_pro_options' );
                $wpdb->query( "DROP TABLE `" . $wpdb->prefix . "yop_pollmeta`, `" . $wpdb->prefix . "yop_polls`, `" . $wpdb->prefix . "yop_poll_answermeta`, `" . $wpdb->prefix . "yop_poll_answers`, `" . $wpdb->prefix . "yop_poll_custom_fields`, `" . $wpdb->prefix . "yop_poll_logs`, `" . $wpdb->prefix . "yop_poll_voters`, `" . $wpdb->prefix . "yop_poll_bans`, `" . $wpdb->prefix . "yop_poll_templates`, `" . $wpdb->prefix . "yop_poll_votes_custom_fields`, `" . $wpdb->prefix . "yop_poll_facebook_users`" );
                $poll_archive_page = get_page_by_path( 'yop-poll-archive', ARRAY_A );
                if ( $poll_archive_page ){
                    $poll_archive_page_id = $poll_archive_page ['ID'];
                    wp_delete_post( $poll_archive_page_id, true );
                }
            }
            switch_to_blog( $old_blog );
            return;
        }
        delete_option( 'yop_poll_version' );
        delete_option( 'yop_poll_options' );
        delete_option( 'yop_poll_first_install_date' );
        delete_option( 'yop_poll_admin_notices_donate' );
        delete_option( 'yop_poll_optin_box_modal_options' );
        delete_option( 'yop_poll_pro_options' );
        $wpdb->query( "DROP TABLE `" . $wpdb->prefix . "yop_pollmeta`, `" . $wpdb->prefix . "yop_polls`, `" . $wpdb->prefix . "yop_poll_answermeta`, `" . $wpdb->prefix . "yop_poll_answers`, `" . $wpdb->prefix . "yop_poll_custom_fields`, `" . $wpdb->prefix . "yop_poll_logs`, `" . $wpdb->prefix . "yop_poll_voters`, `" . $wpdb->prefix . "yop_poll_bans`, `" . $wpdb->prefix . "yop_poll_templates`, `" . $wpdb->prefix . "yop_poll_votes_custom_fields`, `" . $wpdb->prefix . "yop_poll_facebook_users`" );
        $poll_archive_page = get_page_by_path( 'yop-poll-archive', ARRAY_A );
        if ( $poll_archive_page ){
            $poll_archive_page_id = $poll_archive_page ['ID'];
            wp_delete_post( $poll_archive_page_id, true );
        }
    }