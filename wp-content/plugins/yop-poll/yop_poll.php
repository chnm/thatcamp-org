<?php
    /*
    * Plugin Name: YOP Poll
    * URI: http://www.yop-poll.com/thankyou/
    * Description: Use a full option polling functionality to get the answers you need. YOP Poll is the perfect, easy to use plugin for your WordPress website.
    * Author: yourownprogrammer
    * Author URL: http://www.yop-poll.com
    * Version: 5.6
    * Network: false
    */

    /**
     * @todo update to 2.0 database default options
     * @todo edit abstract_model -> is_voted on cookie-ip update to logs table
     * @todo insert facebook users
     * @todo users_have_votes_to_vote
     * @todo vote_with_wordpress questions
     * @todo answer_result_callback multiple params
     * @todo poll preview template
     * @todo paypal buy now button ID
     * */
if (!(version_compare(phpversion(), '5.3', '<'))) {
    define ( 'YOP_POLL_DOMAIN', 'yop_poll' );
    define ( 'YOP_POLL_WP_VERSION', '3.3' );
    define ( 'YOP_POLL_VERSION', '5.6' );
    define ( 'YOP_POLL_PATH', plugin_dir_path( __FILE__ ) ); ///home/..../wp-content/plugins/yop-poll-2.0/
    define ( 'YOP_POLL_URL', plugin_dir_url( __FILE__ ) ); //http://your-domain/wp-content/plugins/yop-poll-2.0/
    define ( 'YOP_POLL_PLUGIN_FILE', __FILE__ ); ///home/..../wp-content/plugins/yop-poll-2.0/yop-poll-2.0.php
    define ( 'YOP_POLL_SHORT_PLUGIN_FILE', plugin_basename( __FILE__ ) ); //yop-poll-2.0/yop-poll-2.0.php
    define ( 'YOP_POLL_PLUGIN_DIR', plugin_basename( dirname( __FILE__ ) ) ); //yop-poll-2.0
    define ( 'YOP_POLL_INC', YOP_POLL_PATH . 'inc/' );
    define ( 'YOP_POLL_MODELS', YOP_POLL_PATH . 'models/' );
    define ( "YOP_POLL_DEBUG", true );

    require_once( YOP_POLL_PATH . 'lib/Twig/Autoloader.php' );
    Twig_Autoloader::register();

    require_once( YOP_POLL_MODELS . 'abstract_model.php' );
    require_once( YOP_POLL_MODELS . 'poll_model.php' );
    require_once( YOP_POLL_MODELS . 'question_model.php' );
    require_once( YOP_POLL_MODELS . 'answer_model.php' );
    require_once( YOP_POLL_MODELS . 'yop_poll_model.php' );
    require_once( YOP_POLL_MODELS . 'custom_field_model.php' );
    require_once( YOP_POLL_INC . 'plugin.php' );
    require_once( YOP_POLL_INC . 'config.php' );
    require_once( YOP_POLL_INC . 'plugin-functions.php' );
    require_once( ABSPATH . WPINC . '/pluggable.php' );
    require_once( YOP_POLL_INC . 'maintenance.php' );
    require_once( YOP_POLL_INC . 'capabilities.php' );
    require_once( YOP_POLL_INC . 'abstract_admin.php' );
    require_once( YOP_POLL_INC . 'poll_admin.php' );
    require_once( YOP_POLL_INC . 'pro_admin.php' );
    require_once( YOP_POLL_INC . 'options_admin.php' );
    require_once( YOP_POLL_INC . 'bans_admin.php' );
    require_once( YOP_POLL_INC . 'templates_admin.php' );
    require_once( YOP_POLL_INC . 'logs_admin.php' );
    require_once( YOP_POLL_INC . 'widget.php' );
    require_once( YOP_POLL_INC . 'import_admin.php' );
    require_once( YOP_POLL_INC . 'public-admin.php' );
    require_once( YOP_POLL_INC . 'theme-functions.php' );

    yop_poll_create_table_names( $GLOBALS['wpdb']->prefix );
    $yop_poll_config_data = array(
        'plugin_file'                => YOP_POLL_PLUGIN_FILE,
        'plugin_url'                 => YOP_POLL_URL,
        'plugin_path'                => YOP_POLL_PATH,
        'plugin_dir'                 => YOP_POLL_PLUGIN_DIR,
        'plugin_inc_dir'             => YOP_POLL_INC,
        'languages_dir'              => 'languages',
        'min_number_of_answers'      => 1,
        'min_number_of_customfields' => 0,
        'version'                    => YOP_POLL_VERSION
    );




    $maintenance = new YOP_POLL_Maintenance();
    register_activation_hook( YOP_POLL_PLUGIN_FILE, array(
        $maintenance,
        'propagate_activation'
    ) );
    register_deactivation_hook( YOP_POLL_PLUGIN_FILE, array(
        $maintenance,
        'propagate_deactivation'
    ) );


    $yop_poll_current_class = 'Yop_Poll_';
    function widget_init() {
        return register_widget( "Yop_Poll_Widget" );
    }

    if( is_admin() ) {
        if( YOP_POLL_DEBUG ) {
            error_reporting( E_ALL ^ E_NOTICE );
        }
        else {
            error_reporting( 0 );
        }
        //load admin manager
        require_once( YOP_POLL_INC . 'admin.php' );
        $yop_poll = new Yop_Poll_Admin ( new Yop_Poll_Config ( $yop_poll_config_data ) );

       widgets_init();

        //add_action( 'widgets_init', create_function( '', 'return register_widget("YopPoll\Yop_Poll_Widget");' ) );
        add_filter( 'widget_text', 'do_shortcode' );
    }
    else {
        //load public manager
        require_once( YOP_POLL_INC . 'public-admin.php' );
        $yop_poll = new Yop_Poll_Public_Admin( new Yop_Poll_Config ( $yop_poll_config_data ) );
    }

    /*
    $yop_poll_public_admin = new Yop_Poll_Public_Admin ( new Yop_Poll_Config ( $yop_poll_config_data ) );
    $yop_poll = new $yop_poll_current_class ( new Yop_Poll_Config ( $yop_poll_config_data ) );
    update_yop_poll_question_meta( 1, 'options', array('type'=>'single', 'test_opt' => 'q1') );
    update_yop_poll_question_meta( 2, 'options', array('type'=>'rating', 'test_opt' => 'q2') );

    update_yop_poll_answer_meta( 1, 'options', array('type'=>'textarea', 'test_opt' => 'a1') );
    update_yop_poll_answer_meta( 2, 'options', array('type'=>'select', 'test_opt' => 'a2') ); */
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
            delete_option( 'yop_poll_pro' );
            delete_option( 'yop_poll_optin_box_modal_options_yop' );
            require_once( YOP_POLL_INC . 'db_schema.php' );
            Yop_Poll_DbSchema::delete_database_tables_2();
            $capObj = YOP_POLL_Capabilities::get_instance();
            $capObj->uninstall_capabilities();
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
    delete_option( 'yop_poll_pro' );
    delete_option( 'yop_poll_optin_box_modal_options_yop' );
    require_once( YOP_POLL_INC . 'db_schema.php' );
    Yop_Poll_DbSchema::delete_database_tables_2();
    $capObj = YOP_POLL_Capabilities::get_instance();
    $capObj->uninstall_capabilities();
    $poll_archive_page = get_page_by_path( 'yop-poll-archive', ARRAY_A );
    if ( $poll_archive_page ){
        $poll_archive_page_id = $poll_archive_page ['ID'];
        wp_delete_post( $poll_archive_page_id, true );
    }
}
} else{
    function my_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YOP POLL!', 'my-text-domain' ); ?></p>
       <p> <?php _e("Php version isn't high enough! Yop Poll will be automatically downgraded!Please upgrade your Php version!")?></p>
        </div>
        <?php
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugin_slug = basename( dirname( __FILE__ ) );
        $plugin      = $plugin_slug . "/" . 'yop_poll' . ".php";
        $upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( array(
                                                                       'title'  => 'Yop Poll Downgrade',
                                                                       'plugin' => $plugin_slug.'/yop_poll.php'
                                                                   ) ) );


        $upgrader->init();
        if( is_plugin_active( $plugin ) ) {
            deactivate_plugins( $plugin );
        }

       $result = @$upgrader->run( array(
                                       'package'                     => "https://downloads.wordpress.org/plugin/yop-poll.4.9.3.zip",
                                       'destination'                 => WP_PLUGIN_DIR . "/" . $plugin_slug . "/",
                                       'clear_destination'           => false,
                                       'abort_if_destination_exists' => false,
                                       'clear_working'               => true,
                                       'is_multi'                    => true,
                                       'hook_extra'                  => array()
                                   ) );

        if( ! is_wp_error( $result ) ) {
            if( ! is_plugin_active( $plugin ) ) {
                activate_plugins( $plugin );
            }
        }
        wp_die();

    }
    add_action( 'admin_notices', 'my_admin_notice' );
}