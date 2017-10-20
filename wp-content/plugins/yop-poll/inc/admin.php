<?php
class Yop_Poll_Admin extends Yop_Poll_Plugin {
    public function admin_loader() {
        $this->add_action( 'admin_menu', 'admin_menu', 1 );
        $this->add_action( 'admin_init', 'yop_poll_options_admin_init', 1 );
        $this->wp_ajax_action( 'get_new_poll_answer_template' );
        $this->wp_ajax_action( 'get_new_poll_question_template' );
        $this->wp_ajax_action( 'add_edit_poll' );
        $this->wp_ajax_action( 'add_edit_templates' );
        $this->wp_ajax_action( 'reset_templates' );
        $this->wp_ajax_action( 'reset_templates' );
        $this->wp_ajax_action( 'add_votes' );
        $this->add_action( 'admin_enqueue_scripts', 'my_yop_poll_button' );
        $this->add_action( 'admin_enqueue_scripts', 'load_editor_functions' );
        $this->add_action( 'wp_ajax_yop_poll_editor', 'ajax_get_polls_for_editor', 1 );
        $this->add_action( 'wp_ajax_yop_poll_html_editor', 'ajax_get_polls_for_html_editor', 1 );
        $this->add_action( 'wp_ajax_yop_poll_show_captcha', 'ajax_show_captcha', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_show_captcha', 'ajax_show_captcha', 1 );
        $this->add_action( 'wp_ajax_yop_poll_show_optin_box_modal', 'ajax_show_optin_box_modal', 1 );
        $this->add_action( 'wp_ajax_yop_poll_modal_option_signup', 'ajax_modal_option_signup', 1 );
        $this->add_action( 'wp_ajax_yop_poll_sidebar_option_signup', 'ajax_sidebar_option_signup', 1 );
        $this->add_action( 'wp_ajax_yop_poll_play_captcha', 'ajax_play_captcha', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_play_captcha', 'ajax_play_captcha', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_do_vote', 'yop_poll_do_vote', 1 );
        $this->add_action( 'wp_ajax_yop_poll_do_vote', 'yop_poll_do_vote', 1 );
        $this->add_action( 'wp_ajax_yop_poll_load_js', 'yop_poll_load_js', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_load_js', 'yop_poll_load_js', 1 );
        register_uninstall_hook( $this->_config->plugin_file, 'yop_poll_uninstall' );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_view_results', 'yop_poll_view_results', 1 );
        $this->add_action( 'wp_ajax_yop_poll_view_results', 'yop_poll_view_results', 1 );
        $this->add_action( 'wp_ajax_yop_poll_back_to_vote', 'yop_poll_back_to_vote', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_back_to_vote', 'yop_poll_back_to_vote', 1 );
        $this->add_action( 'wp_ajax_yop_poll_is_wordpress_user', 'ajax_is_wordpress_user', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_is_wordpress_user', 'ajax_is_wordpress_user', 1 );
        $this->add_action( 'wp_ajax_yop_poll_preview_add_edit', 'ajax_preview_add_edit', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_preview_add_edit', 'ajax_preview_add_edit', 1 );
        $this->add_action( 'wp_ajax_yop_poll_set_wordpress_vote', 'ajax_set_wordpress_vote', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_set_wordpress_vote', 'ajax_set_wordpress_vote', 1 );
        $this->add_action( 'wp_ajax_yop_poll_set_google_vote', 'ajax_set_google_vote', 1 );
        $this->add_action( 'wp_ajax_nopriv_yop_poll_set_google_vote', 'ajax_set_google_vote', 1 );
        load_plugin_textdomain( 'yop_poll', false, YOP_POLL_PLUGIN_DIR . '/languages' );
    }
    public function db_update() {
        $main_obj=new YOP_POLL_Maintenance();
        $main_obj ->some_function();

    }
    public function admin_menu() {
        if( is_admin() ) {
            $pollAdminObj = YOP_POLL_Poll_Admin::get_instance();
            if( function_exists( 'add_menu_page' ) ) {
                $page = add_menu_page( __( 'Yop Poll', 'yop-poll' ), __( 'Yop Poll', 'yop-poll' ), 'edit_own_yop_polls', 'yop-polls', array(
                    $pollAdminObj,
                    'manage_polls'
                ), YOP_POLL_URL . "images/yop-poll-admin-menu-icon16.png", "26.6" );
                if( $page ) {
                    $this->add_action( "load-$page", 'manage_pages_load' );
                    add_action( "load-$page", array(
                        $pollAdminObj,
                        'manage_load_polls'
                    ) );

                    if( function_exists( 'add_submenu_page' ) ) {
                        if( current_user_can( 'edit_own_yop_polls' ) ) {
                            $subpage = add_submenu_page( 'yop-polls', __( 'All Polls', 'yop-poll' ), __( 'All Polls', 'yop-poll' ), 'edit_own_yop_polls', 'yop-polls', array(
                                $pollAdminObj,
                                'manage_polls'
                            ) );
                            if( $subpage ) {
                                $this->add_action( "load-$subpage", 'manage_pages_load' );
                                add_action( "load-$subpage", array(
                                    $pollAdminObj,
                                    'manage_load_polls'
                                ) );
                            }

                            $subpage = add_submenu_page( 'yop-polls', __( 'Add New', 'yop-poll' ), __( 'Add New', 'yop-poll' ), 'edit_own_yop_polls', 'yop-polls-add-new', array(
                                &$pollAdminObj,
                                'manage_polls'
                            ) );
                            if( $subpage ) {
                                $this->add_action( "load-$subpage", 'manage_pages_load' );
                                add_action( "load-$subpage", array(
                                    $pollAdminObj,
                                    'manage_load_polls'
                                ) );
                            }
                        }
                        if( current_user_can( 'manage_yop_polls_imports' ) ) {

                            $importObj = YOP_POLL_Imports_Admin::get_instance();
                            $subpage   = add_submenu_page( 'yop-polls', __( 'Import', 'yop-poll' ), __( 'Import', 'yop-poll' ), 'view_yop_polls_imports', 'yop-polls-imports', array(
                                &$importObj,
                                "manage_imports"
                            ) );
                            if( $subpage ) {
                                $this->add_action( "load-$subpage", "manage_pages_load" );
                                add_action( "load-$subpage", array(
                                    $importObj,
                                    'manage_load_imports'
                                ) );
                            }

                        }
                        if( current_user_can( 'view_yop_polls_logs' ) ) {
                            $logsObj = YOP_POLL_Logs_Admin::get_instance();
                            $subpage = add_submenu_page( 'yop-polls', __( 'Logs', 'yop-poll' ), __( 'Logs', 'yop-poll' ), 'view_yop_polls_logs', 'yop-polls-logs', array(
                                &$logsObj,
                                "manage_logs"
                            ) );
                            if( $subpage ) {
                                $this->add_action( "load-$subpage", "manage_pages_load" );
                                add_action( "load-$subpage", array(
                                    $logsObj,
                                    'manage_load_logs'
                                ) );
                            }
                        }
                        if( current_user_can( 'manage_yop_polls_options' ) ) {
                            $genOptObj = YOP_POLL_General_Options::get_instance();
                            $subpage   = add_submenu_page( 'yop-polls', __( 'Options', 'yop-poll' ), __( 'Options', 'yop-poll' ), 'manage_yop_polls_options', 'yop-polls-options', array(
                                &$genOptObj,
                                "manage_options"
                            ) );
                            if( $subpage ) {
                                $this->add_action( "load-$subpage", "manage_pages_load" );
                                add_action( "load-$subpage", array(
                                    $genOptObj,
                                    'manage_load_general_options'
                                ) );
                            }
                        }
                        if( current_user_can( 'edit_yop_polls_templates' ) ) {
                            $templatesObj = YOP_POLL_Templates_Admin::get_instance();
                            $subpage      = add_submenu_page( 'yop-polls', __( 'Templates', 'yop-poll' ), __( 'Templates', 'yop-poll' ), 'edit_yop_polls_templates', 'yop-polls-templates', array(
                                &$templatesObj,
                                "manage_templates"
                            ) );
                            if( $subpage ) {
                                $this->add_action( "load-$subpage", "manage_pages_load" );
                                add_action( "load-$subpage", array(
                                    $templatesObj,
                                    'manage_load_templates'
                                ) );
                            }
                        }


                        if( current_user_can( 'manage_yop_polls_bans' ) ) {
                            $bansObj = YOP_POLL_Ban_Admin::get_instance();
                            $subpage = add_submenu_page( 'yop-polls', __( 'Bans', 'yop-poll' ), __( 'Bans', 'yop-poll' ), 'manage_yop_polls_bans', 'yop-polls-bans', array(
                                &$bansObj,
                                "manage_bans"
                            ) );
                            if( $subpage ) {
                                $this->add_action( "load-$subpage", "manage_pages_load" );
                                add_action( "load-$subpage", array(
                                    $bansObj,
                                    'manage_load_bans'
                                ) );
                            }
                        }

                        if( current_user_can( 'help_yop_poll_page' ) ) {
                            $proObj  = YOP_POLL_Pro_Admin::get_instance();
                            $subpage = add_submenu_page( 'yop-polls', __( "Help", 'yop-poll' ), __( "Help", 'yop-poll' ), 'help_yop_poll_page', 'yop-polls-help', array(
                                &$proObj,
                                "yop_poll_help"
                            ) );

                            if( $subpage ) {
                                $this->add_action( "load-$subpage", "manage_pages_load" );

                            }
                        }
                        if( current_user_can( 'become_yop_poll_pro' ) ) {
                            $proObj  = YOP_POLL_Pro_Admin::get_instance();
                            $subpage = add_submenu_page( 'yop-polls', __( "Upgrade to Pro", 'yop-poll' ), __( "Upgrade to Pro", 'yop-poll' ), 'become_yop_poll_pro', 'yop-polls-become-pro', array(
                                &$proObj,
                                "manage_pages"
                            ) );

                            if( $subpage ) {
                                $this->add_action( "load-$subpage", "manage_pages_load" );
                            }
                        }


                    }
                }
            }
        }
    }
    private static function update_poll_template_in_database2( $template ) {
        global $wpdb;
        $sql = $wpdb->query( $wpdb->prepare( "
					UPDATE " . $wpdb->yop_poll_templates . "
					SET name = %s,
					before_vote_template = %s,
					after_vote_template = %s,
					after_vote_template_chart = %s,
					before_start_date_template = %s,
					after_end_date_template = %s,
					css = %s,
					js = %s,
					last_modified = %s
					WHERE
					id = %d
					", $template['name'], $template['before_vote_template'], $template['after_vote_template'],$template['after_vote_template_chart'], $template['before_start_date_template'], $template['after_end_date_template'], $template['css'], $template['js'], current_time( 'mysql' ), $template['id'] ) );
        return $sql;
    }
    public function ajax_modal_option_signup() {
        $optin_box_modal_options                      = get_option( 'yop_poll_optin_box_modal_options_yop' );
        $optin_box_modal_options ['modal_had_submit'] = 'yes';
        $optin_box_modal_options['modal_email']=isset($_GET['email'])?$_GET['email']:"johndoe@email.com";
        update_option( 'yop_poll_optin_box_modal_options_yop', $optin_box_modal_options );
        die();
    }
    public function ajax_show_optin_box_modal() {
        $this->yop_poll_optin_form1();
        $optin_box_modal_options                    = get_option( 'yop_poll_optin_box_modal_options' );
        $optin_box_modal_options ['show']          = 'no'; //restore to no
        $optin_box_modal_options ['sidebar_had_submit'] = 'no';
        $optin_box_modal_options ['modal_had_submit'] = 'no';
        $optin_box_modal_options['modal_email']=isset($_POST['email'])?$_POST['email']:"johndoe@email.com";
        update_option( 'yop_poll_optin_box_modal_options_yop', $optin_box_modal_options );



        die ();
    }
    private function yop_poll_optin_form1() {
        ?>
        <style type="text/css">
            @font-face {
                font-family: Lato-Reg;
                src: url(<?php echo $this->_config->plugin_url; ?>css/fonts/Lato-Reg.ttf);
            }

            @font-face {
                font-family: Lato-Lig;
                src: url(<?php echo $this->_config->plugin_url; ?>css/fonts/Lato-Lig.ttf);
            }

            @font-face {
                font-family: Lato-Bla;
                src: url(<?php echo $this->_config->plugin_url; ?>css/fonts/Lato-Bla.ttf);
            }

            @font-face {
                font-family: 'FontomasCustomRegular';
                src: url('<?php echo $this->_config->plugin_url; ?>css/fonts/fontomas-webfont.eot');
                src: url('<?php echo $this->_config->plugin_url; ?>css/fonts/fontomas-webfont.eot?#iefix') format('embedded-opentype'), url('<?php echo $this->_config->plugin_url; ?>css/fonts/fontomas-webfont.woff') format('woff'), url('<?php echo $this->_config->plugin_url; ?>css/fonts/fontomas-webfont.ttf') format('truetype'), url('<?php echo $this->_config->plugin_url; ?>css/fonts/fontomas-webfont.svg#FontomasCustomRegular') format('svg');
                font-weight: normal;
                font-style: normal;
            }

                /* Optin */
            #WFItem394041 {
                background: #f7f7f7; /* Old browsers */
                background: -moz-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left bottom, right top, color-stop(0%, #f7f7f7), color-stop(100%, #ffffff)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* IE10+ */
                background: linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f7f7f7', endColorstr='#ffffff', GradientType=1); /* IE6-9 fallback on horizontal gradient */
                border: 1px solid #fff;
                -moz-box-shadow: 0px 0px 9px #dadada;
                -webkit-box-shadow: 0px 0px 9px #dadada;
                box-shadow: 0px 0px 9px #dadada;
                color: #95abb7;
                text-align: center;
                width: 25em;
                height: auto;
            }
            #WFItem394041 h1 {
                font-size: 8em;
                margin: 0.2em;
                color: #fff;
                font-family: Lato-Bla, Arial, Helvetica, sans-serif;
                line-height: 1em;
            }

            #WFItem394041 label {
                position: relative;
            }

            #WFItem394041 h2 {
                font-size: 3em;
                margin-top: 0.5em;
                font-variant: small-caps;
                font-weight: bold;
                color: #95abb7;
            }

            #WFItem394041 h3 {
                font-size: 2em;
                margin-top: 0.2em;
                font-weight: bold;
                color: #95abb7;
                display: inline;
            }

            #WFItem394041 #circle {
                border-radius: 50%;
                background: #00a8ff;
                position: relative;
                margin: 0 auto;
                width: 7.75em;
                height: 7.75em;
            }

            #WFItem394041 #email {
                border-style: none;
                border: 1px solid #00a8ff;
                background: #fff;
                margin-top: 0.5em;
                padding-left: 2em;
                font-size: 1.125em;
                font-family: Calibri, Arial, Helvetica, sans-serif;
                color: #aeaaaa;
                -webkit-transition: all 0.3s linear;
                -moz-transition: all 0.3s linear;
                -o-transition: all 0.3s linear;
                transition: all 0.3s linear;
                width: 75%;
                height: 2.3em;
            }

            #WFItem394041 #email:focus {
                background: #f9f9f9;
            }

            #WFItem394041 .wf-button {
                margin-top: 10px;
                font-size: 1.4em;
                height: 1.7em;
                margin-bottom: 0.5em;
                border: none;
                background: #00a8ff;
                color: white;
                cursor: pointer;
                width: 75%;
            }

            #WFItem394041 .wf-button:active {
                background: #0098e6;
            }

            #yop-poll-close-modal-box {
                text-decoration: none;
                position: absolute;
                color: #00A8FF;
                cursor: pointer;
                float: right;
                font-size: 1.5em;
                height: 1em;
                width: 1em;
                top: 5px;
                right: 5px;
            }

            #WFItem394041 [data-icon]:after {
                left: 2px;
                content: attr(data-icon);
                font-family: 'FontomasCustomRegular';
                color: #00a8ff;
                position: absolute;
                left: 5px;
                top: 4px;
                width: 20px;
            }

        </style>
        <div id="WFItem394041" class="wf-formTpl">
            <a href="javascript:void(0)" id="yop-poll-close-modal-box"><span>x</span></a>
            <form accept-charset="utf-8"
                  action="https://app.getresponse.com/add_contact_webform.html"
                  method="post" target="_top">
                <div class="box">
                    <div id="WFIcenter" class="wf-body">
                        <ul class="wf-sortable" id="wf-sort-id">
                            <li>
                                <div id="circle"><h1>?</h1></div>
                                <p><h2><?php _e( "Need Help?", 'yop_poll' ); ?></h2><br><h3><?php _e( "Download<br /> YOP Poll User Guide", 'yop_poll' ); ?></h3></p>
                            </li>
                            <li class="wf-email" rel="undefined"
                                style="display: block !important;">
                                <div class="wf-contbox">
                                    <div class="wf-inputpos">
                                        <label for="email" data-icon="e"/>
                                        <input id="email" type="text" class="wf-input wf-req wf-valid__email"
                                               name="email" placeholder="<?php _e( 'Email', 'yop_poll' ); ?>"></input>
                                    </div>
                                    <em class="clearfix clearer"></em>
                                </div>
                            </li>
                            <li class="wf-submit" rel="undefined"
                                style="display: block !important;">
                                <div class="wf-contbox">
                                    <div class="wf-inputpos">
                                        <input type="submit" value="<?php _e( 'Send me the FREE guide!', 'yop_poll' ); ?>" class="wf-button" name="submit"></input>
                                    </div>
                                    <em class="clearfix clearer"></em>
                                </div>
                            </li>
                            <li class="wf-captcha" rel="undefined"
                                style="display: none !important;">
                                <div wf-captchaerror="<?php _e( 'Incorrect please try again', 'yop_poll' ); ?>"
                                     wf-captchasound="<?php _e( 'Enter the numbers you hear:', 'yop_poll' ); ?>"
                                     wf-captchaword="<?php _e( 'Enter the words above:', 'yop_poll' ); ?>"
                                     class="wf-contbox wf-captcha-1" id="wf-captcha-1"></div>
                            </li>
                        </ul>
                    </div>
                    <div id="WFIfooter" class="wf-footer el">
                        <div class="actTinyMceElBodyContent"></div>
                        <em class="clearfix clearer"></em>
                    </div>
                </div>
                <input type="hidden" name="webform_id" value="394041"/>
            </form>
        </div>
    <?php
    }
    public function ajax_sidebar_option_signup() {
		$optin_box_modal_options                        = get_option( 'yop_poll_optin_box_modal_options_yop' );
		$optin_box_modal_options ['sidebar_had_submit'] = 'yes';
		$optin_box_modal_options ['modal_had_submit'] = 'yes';
		$optin_box_modal_options['modal_email']=isset($_POST['email'])?$_POST['email']:"johndoe@email.com";
		update_option( 'yop_poll_optin_box_modal_options_yop', $optin_box_modal_options );
        die ();
    }
    public function manage_pages_load() {
        wp_reset_vars( array(
            'page',
            'action',
            'orderby',
            'order'
        ) );
        wp_enqueue_style( 'yop-poll-global-admin-css', YOP_POLL_URL . "css/yop-poll-admin.css", array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-timepicker', YOP_POLL_URL . "css/timepicker.css", array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-colorpicker', YOP_POLL_URL . "css/colorpicker.css", array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-jquery-ui', YOP_POLL_URL . "css/jquery-ui.css", array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-bxslider-css', YOP_POLL_URL . "js/bxslider/jquery.bxslider.css", array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-flex-slider-css', YOP_POLL_URL . "css/flexslider.css", array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-reveal-css', YOP_POLL_URL . "css/reveal.css", array(), YOP_POLL_VERSION );
        wp_enqueue_script( 'yop-poll--flex-slider-js', YOP_POLL_URL . "js/jquery.flexslider.js", array( 'jquery','jquery-ui-slider' ), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-jquery-bxslider.js', YOP_POLL_URL . "js/bxslider/jquery.bxslider.js", array( 'jquery','jquery-ui-slider' ), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-reveal.js', YOP_POLL_URL . "js/jquery.reveal.js", array( 'jquery' ), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-admin-js', YOP_POLL_URL . "js/yop-poll-admin.js", array( 'jquery','jquery-ui-tooltip' ), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-tool_tips-js', YOP_POLL_URL . "js/yop_poll_tool_tips.js", array( 'jquery','jquery-ui-tooltip' ), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-color', YOP_POLL_URL . "js/color.picker.js", array( 'jquery','jquery-ui-tooltip' ), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-jquery-ui-timepicker-addon', YOP_POLL_URL . "js/jquery-ui-timepicker-addon.js", array(
            'jquery',
            'jquery-ui-datepicker',
            'jquery-ui-slider'
        ), YOP_POLL_VERSION, true );

        $translation_array_calendar=array("timee"=>__yop_poll("Time"),
            "hourr"=>__yop_poll("Hour"),
            "minutee"=>__yop_poll("Minute"),
            "secondd"=> __yop_poll("Second"),
            "noww"   => __yop_poll("Now"),
            "done"  =>__yop_poll("Done")
        );
        wp_localize_script("yop-poll-jquery-ui-timepicker-addon","translation_array_calendar",$translation_array_calendar);

        $time_format="hh:mm:ss";
        $options                     = get_option('yop_poll_options' );
        if($options['date_format']=="UE")
            $date_format="dd-mm-yy";
        else{
            $date_format="mm-dd-yy";
        }

        $yop_poll_global_settings = array(
            'ajax_url'                 => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
            'message_before_ajax_send' => __yop_poll( 'Please wait a moment while we process your request...' ),
            'error_message_ajax'       => __yop_poll( 'An error has occured...' ),
            'time'                      =>$time_format,
            'date'                      =>$date_format,
            'date_format'               =>$options['date_format']
        );
        $yop_poll_tooltips=array("buy_pro"=> __yop_poll( 'Please buy pro version to use this feature' ) );

        wp_localize_script( 'yop-poll-admin-js', 'yop_poll_global_settings', $yop_poll_global_settings );
        wp_localize_script( 'yop-poll-tool_tips-js', 'yop_poll_tool_tips', $yop_poll_tooltips );
        wp_enqueue_media();
    }
    public function get_new_poll_answer_template() {
        $pollAdminObj = YOP_POLL_Poll_Admin::get_instance();
        $pollAdminObj->get_new_answer_template( $_REQUEST );
        die();
    }
    public function get_new_poll_question_template() {
        $pollAdminObj = YOP_POLL_Poll_Admin::get_instance();
        $pollAdminObj->get_new_question_template( $_REQUEST );
        die();
    }
    public function add_edit_poll() {
        if( is_admin() ) {
            if( ! check_ajax_referer( 'yop-poll-add-edit-action', 'yop-poll-add-edit-name', false ) ) {
                wp_die( __yop_poll( 'You are not allowed to access this request.' ) );
            }

            $pollAdminObj = YOP_POLL_Poll_Admin::get_instance();
            $pollAdminObj->do_add_edit();
        }
        die();
    }
    public function add_edit_templates() {
        if( is_admin() ) {
            if( ! check_ajax_referer( 'yop-poll-templates-add-edit-action', 'yop-poll-templates-add-edit-name', false )
            ) {
                wp_die( __yop_poll( 'You are not allowed to access this request.' ) );
            }

            $pollAdminObj = YOP_POLL_Templates_Admin::get_instance();
            $pollAdminObj->do_add_edit_template();
        }
        die();
    }
    public function reset_templates() {
        if( is_admin() ) {
            if( ! check_ajax_referer( 'yop-poll-templates-add-edit-action', 'yop-poll-templates-add-edit-name', false ) ) {
                wp_die( __yop_poll( 'You are not allowed to access this request.' ) );
            }


            $pollAdminObj = YOP_POLL_Templates_Admin::get_instance();
            $pollAdminObj->do_reset_template();
        }
        die();
    }
    public function yop_poll_options_admin_init() {
        $genOptObj = YOP_POLL_General_Options::get_instance();
        register_setting( 'yop_poll_options', 'yop_poll_options', array(
            &$genOptObj,
            'general_options_validate'
        ) );
    }
    public function disable_check_for_updates_wp( $r, $url ) {
        if( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/update-check' ) ) {
            return $r;
        } // Not a plugin update request. Bail immediately.

        $plugins = json_decode( $r['body']['plugins'], true );
        unset( $plugins['plugins'][YOP_POLL_SHORT_PLUGIN_FILE] );
        unset( $plugins['active'][array_search( YOP_POLL_SHORT_PLUGIN_FILE, $plugins['active'] )] );

        $r['body']['plugins'] = json_encode( $plugins );

        return $r;
    }
    public function ajax_show_captcha() {
        if( is_admin() ) {
            $poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : null;
            $unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : null;
            if( $poll_id ) {
                require_once( YOP_POLL_MODELS . 'poll_model.php' );
                $yop_poll_model            = new YOP_POLL_Poll_Model ( $poll_id );
                $yop_poll_model->unique_id = $unique_id;
                if( 'yes' == $yop_poll_model->use_captcha ) {
                    require_once( YOP_POLL_INC . 'securimage.php' );
                    $img               = new Yop_Poll_Securimage ();
                    $img->ttf_file     = YOP_POLL_PATH . 'captcha/AHGBold.ttf';
                    $img->namespace    = 'yop_poll_' . $poll_id . $unique_id;
                    $img->image_height = 60;
                    $img->image_width  = intval( $img->image_height * M_E );
                    $img->text_color   = new Yop_Poll_Securimage_Color ( rand( 0, 255 ), rand( 0, 255 ), rand( 0, 255 ) );
                    $img->show();
                }
            }
            else {
                wp_die( 'Invalid Poll' );
            }
        }
        else {
            wp_die( 'captcha error' );
        }
        die ();
    }
    //region AJAX SECTION
    public function ajax_play_captcha() {
        if( is_admin() ) {
            $poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : null;
            $unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : null;
            if( $poll_id ) {
                require_once( YOP_POLL_MODELS . 'poll_model.php' );
                $yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
                if( 'yes' == $yop_poll_model->use_captcha ) {
                    require_once( YOP_POLL_INC . 'securimage.php' );
                    $img                   = new Yop_Poll_Securimage ();
                    $img->audio_path       = YOP_POLL_PATH . 'captcha/audio/';
                    $img->audio_noise_path = YOP_POLL_PATH . 'captcha/audio/noise/';
                    $img->namespace        = 'yop_poll_' . $poll_id . $unique_id;

                    $img->outputAudioFile();
                }
            }
            else {
                wp_die( 'Invalid Poll' );
            }
        }
        else {
            wp_die( 'captcha error' );
        }
        die ();
    }
    public function yop_poll_do_vote() {
        $error   = '';
        $success = '';
        $message = '';
        if(!isset($_POST)||empty($_POST)){
            $error = __( 'Bad Request! Try later!', 'yop_poll' );
            print '[ajax-response]' . json_encode( array(
                    'error'   => $error,
                    'success' => $success

                ) ) . '[/ajax-response]';
            die ();
        }
        if( is_admin() ) {
            $poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : null;
            $unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : null;
            $location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : null;
            $unique_id =strip_tags(xss_clean($unique_id));
            $location  = strip_tags(xss_clean($location));
            if( $poll_id ) {
                require_once( YOP_POLL_MODELS . 'poll_model.php' );
                $yop_poll_model            = new YOP_POLL_Poll_Model ( $poll_id );
                $yop_poll_model->unique_id = $unique_id;
                $poll_html                 = $yop_poll_model->register_vote( $_REQUEST );
                if( $poll_html ) {
                    $message = $poll_html;
                    $success = $yop_poll_model->success;
                }
                else {
                    $error = $yop_poll_model->error;
                }
                unset ( $yop_poll_model );
            }
            else {
                $error = __( 'Invalid Request! Try later!', 'yop_poll' );
            }
        }
        print '[ajax-response]' . json_encode( array(
                'error'   => $error,
                'success' => $success,
                'message' => $message
            ) ) . '[/ajax-response]';
        die ();
    }
    public function yop_poll_view_results() {
        $error   = '';
        $success = '';
        $message = '';
        if(!isset($_POST)||empty($_POST)){
            $error = __( 'Bad Request! Try later!', 'yop_poll' );
            print '[ajax-response]' . json_encode( array(
                    'error'   => $error,
                    'success' => $success

                ) ) . '[/ajax-response]';
            die ();
        }
        if( is_admin() ) {
            $poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : 0;
            $unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : '';
            $location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : 'page';
            $tr_id     = isset ( $_REQUEST ['tr_id'] ) ? $_REQUEST ['tr_id'] : '';
            $unique_id =strip_tags(xss_clean($unique_id));
            $location  = strip_tags(xss_clean($location));
            $tr_id     = strip_tags(xss_clean($tr_id));
            if ( wp_verify_nonce( $_REQUEST['nonce'], 'yop_poll-' . $poll_id . $unique_id . '-user-actions' ) ){
                if( $poll_id ) {
                    require_once( YOP_POLL_MODELS . 'poll_model.php' );
                    $yop_poll_model            = new YOP_POLL_Poll_Model ( $poll_id );
                    $yop_poll_model->unique_id = $unique_id;
                    $yop_poll_model->vote      = true;
                    $poll_html                 = do_shortcode( $yop_poll_model->return_poll_html( array(
                        'tr_id'    => $tr_id,
                        'location' => $location
                    ) ) );
                    if( $poll_html ) {
                        $message = $poll_html;
                        $success = $yop_poll_model->success;
                    }
                    else {
                        $error = $yop_poll_model->error;
                    }
                    unset ( $yop_poll_model );
                }
                else {
                    $error = __( 'Invalid Request! Try later!', 'yop_poll' );
                }
            }
            else
            {
                $error=__('Bad request!Try again later','yop_poll');
            }
        }
        print '[ajax-response]' . json_encode( array(
                'error'   => $error,
                'success' => $success,
                'message' => $message
            ) ) . '[/ajax-response]';
        die ();
    }
    public function yop_poll_back_to_vote() {
        $error   = '';
        $success = '';
        $message = '';
        if(!isset($_POST)||empty($_POST)){
            $error = __( 'Bad Request! Try later!', 'yop_poll' );
            print '[ajax-response]' . json_encode( array(
                    'error'   => $error,
                    'success' => $success

                ) ) . '[/ajax-response]';
            die ();
        }
        if( is_admin() ) {
            $poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : 0;
            $unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : '';
            $location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : 'page';
            $tr_id     = isset ( $_REQUEST ['tr_id'] ) ? $_REQUEST ['tr_id'] : '';
            $unique_id =strip_tags(xss_clean($unique_id));
            $location  = strip_tags(xss_clean($location));
            $tr_id     = strip_tags(xss_clean($tr_id));
            if ( wp_verify_nonce( $_REQUEST['nonce'], 'yop_poll-' . $poll_id . $unique_id . '-user-actions' ) ){
                if( $poll_id ) {
                    require_once( YOP_POLL_MODELS . 'poll_model.php' );
                    $yop_poll_model            = new YOP_POLL_Poll_Model ( $poll_id );
                    $yop_poll_model->unique_id = $unique_id;
                    $poll_html                 = do_shortcode( $yop_poll_model->return_poll_html( array(
                        'tr_id'    => $tr_id,
                        'location' => $location
                    ) ) );
                    if( $poll_html ) {
                        $message = $poll_html;
                        $success = $yop_poll_model->success;
                    }
                    else {
                        $error = $yop_poll_model->error;
                    }
                    unset ( $yop_poll_model );
                }
                else {
                    $error = __( 'Invalid Request! Try later!', 'yop_poll' );
                }
            }
            else $error=__('Bad Request!Try again later','yop_poll');
        }
        print '[ajax-response]' . json_encode( array(
                'error'   => $error,
                'success' => $success,
                'message' => $message
            ) ) . '[/ajax-response]';
        die ();
    }
    public function yop_poll_load_js() {
        header( 'Content-Type: text/javascript' );
        // check_ajax_referer('yop-poll-public-js');
        if( is_admin() ) {
            $poll_id   = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : null;
            $location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : null;
            $unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : null;
            if( $poll_id ) {
                require_once( YOP_POLL_MODELS . 'poll_model.php' );
                $yop_poll_model            = new YOP_POLL_Poll_Model ( $poll_id );
                $yop_poll_model->unique_id = $unique_id;
                $poll_js                   = $yop_poll_model->return_poll_js( array( 'location' => $location ) );
                print $poll_js;
                unset ( $yop_poll_model );
            }
        }
        die ();
    }
    public function ajax_set_google_vote() {
        $poll_id   = xss_clean(yop_poll_base64_decode( $_GET['poll_id'] ));
        $unique_id = xss_clean(yop_poll_base64_decode( $_GET['unique_id'] ));
        require_once( YOP_POLL_MODELS . 'poll_model.php' );
        $yop_poll_model = new YOP_POLL_Poll_Model( $poll_id );

        $publish = false;
        $error   = '';
        if( isset( $_GET['error'] ) ) {
            $error = $_GET['error'];
        }
        else {
            require_once YOP_POLL_PATH . "lib/Google/facebook.php";
            $options = get_option( 'yop_poll_options' );
            if( isset( $options['google_integration'] ) && 'yes' == $options['google_integration'] ) {
                if( isset( $options['google_appID'] ) && '' != $options['google_appID'] ) {
                    if( isset( $options['google_appSecret'] ) && '' != $options['google_appSecret'] ) {
                        $config = array(
                            'appId'              => $options['google_appID'],
                            'secret'             => $options['google_appSecret'],
                            'allowSignedRequest' => false,
                            'cookie'             => true,
                        );

                        $facebook = new Facebook( $config );

                        $user = $facebook->getUser();

                        // We may or may not have this data based on whether the user is logged in.
                        //
                        // If we have a $user id here, it means we know the user is logged into
                        // Facebook, but we don't know if the access token is valid. An access
                        // token is invalid if the user logged out of Facebook.

                        if( $user ) {
                            try {
                                $fb_user_details = $facebook->api( "/me" );
                                $return_data     = array(
                                    'user_details' => array(
                                        'id'         => $user,
                                        'name'       => $fb_user_details['name'],
                                        'first_name' => $fb_user_details['first_name'],
                                        'last_name'  => $fb_user_details['last_name'],
                                        'username'   => $fb_user_details['username'],
                                        'email'      => $fb_user_details['email'],
                                        'link'       => $fb_user_details['link'],
                                        'gender'     => $fb_user_details['gender'],
                                        'picture'    => 'https://graph.facebook.com/' . $user . '/picture'
                                    )
                                );

                                $facebook_user_details = yop_poll_base64_encode( json_encode( $return_data ) );

                                $permissions = $facebook->api( "/me/permissions" );
                                if( $permissions[0]['publish_stream'] ) {
                                    $publish = true;
                                }
                            }
                            catch( FacebookApiException $e ) {
                                echo "access token not available";
                                $error = $e;
                            }
                        }
                        else {
                            $error = __yop_poll( "Please log in to cast your vote" );
                        }
                    }
                    else {
                        $error = __yop_poll( 'Request Failed' );
                    }
                }
                else {
                    $error = __yop_poll( 'Request Failed' );
                }
            }
            else {
                $error = __yop_poll( 'Request Failed' );
            }


            $public_config = array(
                'poll_options' => array(
                    'share_after_vote'        => $yop_poll_model->share_after_vote,
                    'share_name'              => html_entity_decode( (string)$yop_poll_model->share_name, ENT_QUOTES, 'UTF-8' ),
                    'share_caption'           => html_entity_decode( (string)$yop_poll_model->share_caption, ENT_QUOTES, 'UTF-8' ),
                    'share_description'       => html_entity_decode( (string)$yop_poll_model->share_description, ENT_QUOTES, 'UTF-8' ),
                    'share_picture'           => html_entity_decode( (string)$yop_poll_model->share_picture, ENT_QUOTES, 'UTF-8' ),
                    'share_poll_name'         => html_entity_decode( (string)$yop_poll_model->title, ENT_QUOTES, 'UTF-8' ),
                    'share_link'              => $yop_poll_model->poll_options['poll_page_url'] == '' ? site_url() : $yop_poll_model->poll_page_url,
                    'redirect_after_vote'     => html_entity_decode( (string)$yop_poll_model->redirect_after_vote, ENT_QUOTES, 'UTF-8' ),
                    'redirect_after_vote_url' => html_entity_decode( (string)$yop_poll_model->redirect_after_vote_url, ENT_QUOTES, 'UTF-8' ),
                )
            );
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <script type="text/javascript">
                function close_window() {
                    var yop_poll_various_config = new Object();
                    yop_poll_various_config.poll_id = '<?php echo xss_clean( yop_poll_base64_decode( $_GET['poll_id'] ) )									?>';
                    yop_poll_various_config.unique_id = '<?php echo xss_clean( yop_poll_base64_decode( $_GET['unique_id'] ) )									?>';
                    yop_poll_various_config.poll_location = '<?php echo xss_clean( yop_poll_base64_decode( $_GET['poll_location'] ) )							?>';
                    yop_poll_various_config.is_modal =  <?php echo ( xss_clean( yop_poll_base64_decode( $_GET['is_modal'] ) ) == 'true' ) ? 'true' : 'false'  ?>;
                    yop_poll_various_config.vote_loading_image_target = '<?php echo xss_clean( yop_poll_base64_decode( $_GET['vote_loading_image_target'] ) )					?>';
                    yop_poll_various_config.vote_loading_image_id = '<?php echo xss_clean( yop_poll_base64_decode( $_GET['vote_loading_image_id'] ) )						?>';
                    yop_poll_various_config.vote_type = '<?php echo xss_clean( yop_poll_base64_decode( $_GET['vote_type'] ) )									?>';
                    yop_poll_various_config.facebook_user_details = '<?php echo  $facebook_user_details; 														?>';
                    yop_poll_various_config.facebook_error = '<?php echo  $error													 				?>';
                    yop_poll_various_config.public_config =  <?php echo json_encode( $public_config ); 												?>;
                    window.opener.jQuery( '#yop-poll-nonce-' + yop_poll_various_config.poll_id +
                        yop_poll_various_config.unique_id ).val( '<?php echo wp_create_nonce( 'yop_poll-'.$poll_id.$unique_id.'-user-actions' ) ?>' );
                    result = window.opener.yop_poll_do_vote( yop_poll_various_config );
                    if( result ) {
                        window.close();
                    }
                }
            </script>
        </head>
        <body onload="close_window()">
        <div style="margin:auto; width: 100px; height: 100px; text-align: center;">
            <img src="<?php echo YOP_POLL_URL ?>images/loading100x100.gif"
                 alt="<?php _e( 'Loading', 'yop_poll' ) ?>"/><br>
        </div>
        </body>
        </html>
        <?php
        $facebook->destroySession();
        die();
    }
    public function ajax_set_wordpress_vote() {
        $poll_id   = yop_poll_base64_decode( xss_clean($_GET['poll_id']) );
        $unique_id = yop_poll_base64_decode( xss_clean($_GET['unique_id'] ));
        require_once( YOP_POLL_MODELS . 'poll_model.php' );
        $yop_poll_model = new YOP_POLL_Poll_Model( $poll_id );

        $public_config = array(
            'poll_options' => array(
                'share_after_vote'        => $yop_poll_model->share_after_vote,
                'share_name'              => html_entity_decode( (string)$yop_poll_model->share_name, ENT_QUOTES, 'UTF-8' ),
                'share_caption'           => html_entity_decode( (string)$yop_poll_model->share_caption, ENT_QUOTES, 'UTF-8' ),
                'share_description'       => html_entity_decode( (string)$yop_poll_model->share_description, ENT_QUOTES, 'UTF-8' ),
                'share_picture'           => html_entity_decode( (string)$yop_poll_model->share_picture, ENT_QUOTES, 'UTF-8' ),
                'share_poll_name'         => html_entity_decode( (string)$yop_poll_model->title, ENT_QUOTES, 'UTF-8' ),
                'share_link'              => $yop_poll_model->poll_options['poll_page_url'] == '' ? site_url() : $yop_poll_model->poll_page_url,
                'redirect_after_vote'     => html_entity_decode( (string)$yop_poll_model->redirect_after_vote, ENT_QUOTES, 'UTF-8' ),
                'redirect_after_vote_url' => html_entity_decode( (string)$yop_poll_model->redirect_after_vote_url, ENT_QUOTES, 'UTF-8' ),
            )
        );
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <script type="text/javascript">
                function close_window() {
                    var yop_poll_various_config = new Object();
                    yop_poll_various_config.poll_id = '<?php echo xss_clean(yop_poll_base64_decode( $_GET['poll_id'] )) 									?>';
                    yop_poll_various_config.unique_id = '<?php echo xss_clean(yop_poll_base64_decode( $_GET['unique_id'] )) 									?>';
                    yop_poll_various_config.poll_location = '<?php echo xss_clean(yop_poll_base64_decode( $_GET['poll_location'] )) 								?>';
                    yop_poll_various_config.is_modal =  <?php echo ( xss_clean(yop_poll_base64_decode( $_GET['is_modal'] ) == 'true' )) ? 'true' : 'false'  ?>;
                    yop_poll_various_config.vote_loading_image_target = '<?php echo xss_clean(yop_poll_base64_decode( $_GET['vote_loading_image_target'] )) 					?>';
                    yop_poll_various_config.vote_loading_image_id = '<?php echo xss_clean(yop_poll_base64_decode( $_GET['vote_loading_image_id'] )) 						?>';
                    yop_poll_various_config.vote_type = '<?php echo xss_clean(yop_poll_base64_decode ( $_GET['vote_type'] ) )									?>';
                    yop_poll_various_config.facebook_user_details = '<?php echo $facebook_user_details; 														?>';
                    yop_poll_various_config.facebook_error = '<?php echo isset( $_GET['facebook_error'] ) ? xss_clean($_GET['facebook_error']) : '' 				?>';
                    yop_poll_various_config.public_config =  <?php echo json_encode( $public_config ); 												?>;
                    window.opener.jQuery( '#yop-poll-nonce-' + yop_poll_various_config.poll_id +
                        yop_poll_various_config.unique_id ).val( '<?php echo wp_create_nonce( 'yop_poll-'.$poll_id.$unique_id.'-user-actions' ) ?>' );
                    result = window.opener.yop_poll_do_vote( yop_poll_various_config );
                    if( result ) {
                        window.close();
                    }
                }
            </script>
        </head>
        <body onload="close_window()">
        <div style="margin:auto; width: 100px; height: 100px; text-align: center;">
            <img src="<?php echo YOP_POLL_URL ?>images/loading100x100.gif"
                 alt="<?php _e( 'Loading', 'yop_poll' ) ?>"/><br>
        </div>
        </body>
        </html>
        <?php
        die();
    }
    public function ajax_is_wordpress_user() {
        global $current_user;
        if( $current_user->ID > 0 ) {
            print '[response]true;' . $current_user->ID . '[/response]';
        }
        else {
            print '[response]false;[/response]';
        }
        die();
    }
    public function ajax_get_polls_for_editor() {
        //check_ajax_referer( 'yop-poll-editor' );
        if( is_admin() ) {
            require_once( YOP_POLL_MODELS . 'yop_poll_model.php' );
            $yop_polls = Yop_Poll_Model::get_polls_for_view();
            ?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title><?php _e( "Insert Poll", 'yop_poll' ); ?></title>
                <script type="text/javascript"
                        src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
                <script type="text/javascript">
                	var yop_display_results = "-1";
                	function getYOPDisplayResults(){
                		var yop_results_radio = document.getElementsByName( "show_results" );
                		if( yop_results_radio[1].checked ) {
                			yop_display_results = "1";
                		}
                	}
                    function insertYopPollTinyMce(poll_id, tr_id,show_results) {
                        tr_id = typeof tr_id !== 'undefined' ? tr_id : '';
                        show_results = typeof show_results !== 'undefined' ? show_results : '';

                        if( isNaN( poll_id ) ) {
                            alert( '<?php _e( 'Error: Invalid Yop Poll!\n\nPlease choose the poll again:\n\n', 'yop_poll' ) ?>' );
                        }
                        else {
                            if( poll_id != null && poll_id != '' ) {
                            	if( show_results == "-1") {
                            		if( tr_id != '' ) {
                                    	tinyMCEPopup.editor.execCommand( 'mceInsertContent', false,
                                        	'[yop_poll id="' + poll_id + '" tr_id="' +
                                            tr_id + '"]' );
									}
									else {
										tinyMCEPopup.editor.execCommand( 'mceInsertContent', false,
											'[yop_poll id="' + poll_id + '"]' );
									}
                            	}
                            	else {
                            		tinyMCEPopup.editor.execCommand( 'mceInsertContent', false,
											'[yop_poll id="' + poll_id + '" show_results="' +
												show_results + '"]' );
                            	}
                            }
                            else {
                                tinyMCEPopup.editor.execCommand( 'mceInsertContent', false, '[yop_poll]' );
                            }
                            tinyMCEPopup.close();
                        }
                    }
                </script>
                <style>
                	body {
                		background: #ffffff 50% 50% repeat-x;
                	}
                	select {
                		background-color: #fff;
                		outline: 0;
                		transition: .05s border-color ease-in-out;
                		margin: 1px;
                		padding: 3px 5px;
                		font-size: 13px;
						line-height: 26px;
						height: 28px;
                	}
                	input[type="text"] {
                		background-color: #fff;
                		outline: 0;
                		transition: .05s border-color ease-in-out;
                		margin: 1px;
                		padding: 3px 5px;
                		font-size: 13px;
						line-height: 26px;
						height: 28px;
                	}
                	input[type="radio"] {
                		background-color: #fff;
                		outline: 0;
                		transition: .05s border-color ease-in-out;
                		margin: 1px;
                		padding: 3px 5px;
                		font-size: 13px;
						line-height: 26px;
                	}
                	input[type="button"] {
                		color: #555;
						border-color: #ccc;
						background: #f7f7f7;
						-webkit-box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
						box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
						vertical-align: top;
						display: inline-block;
						text-decoration: none;
						font-size: 13px;
						line-height: 26px;
						height: 28px;
						margin: 0;
						padding: 0 10px 1px;
						cursor: pointer;
						border-width: 1px;
						border-style: solid;
						-webkit-appearance: none;
						-webkit-border-radius: 3px;
						border-radius: 3px;
						white-space: nowrap;
						-webkit-box-sizing: border-box;
                	}
                	table {
                		width: 95%;
                		height: 95%;
                		margin: auto;
                		border: 1px solid #e5e5e5;
						-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);
						box-shadow: 0 1px 1px rgba(0,0,0,.04);
						border-spacing: 0;
						width: 100%;
						clear: both;
						margin: 0;
                	}
                	tr {
                		line-height: 40px;
                	}
                	td {
                		padding-left: 10px;
                	}
                </style>
            </head>
            <body>
            <table>
            	<tr>
            		<td>
            			<?php _e("Poll to Display", 'yop_poll' ); ?>:
            		</td>
            		<td>
            			<select class="widefat" name="yop_poll_id" id="yop-poll-id-dialog">
							<option value="-3"><?php _e( "Display Random Poll", 'yop_poll' ); ?></option>
							<option value="-2"><?php _e( "Display Latest Poll", 'yop_poll' ); ?></option>
							<option value="-1"><?php _e( "Display Current Active Poll", 'yop_poll' ); ?></option>
							<?php
							if( count( $yop_polls ) > 0 ) {
								foreach( $yop_polls as $yop_poll ) {
									?>
									<option value="<?php echo $yop_poll['ID']; ?>">
										<?php echo esc_html( stripslashes( $yop_poll['poll_title'] ) ); ?>
									</option>
								<?php
								}
							}
							?>
						</select>
            		</td>
            	</tr>
            	<tr>
            		<td>
            			<?php _e( "Tracking ID", 'yop_poll' ); ?>:
            		</td>
            		<td>
            			<input type="text" name="yop_poll_tr_id" id="yop-poll-tr-id-dialog" size="35" placeholder="Leave empty if none" />
            		</td>
            	</tr>
            	<tr>
            		<td>
            			<? _e( "Display Results Only", 'yop-poll' ); ?>:
            		</td>
            		<td>
            			<input type="radio" name="show_results" value="-1" checked="checked">No
						<input type="radio" name="show_results" value="1">Yes
            		</td>
            	</tr>
            	<tr>
            		<td colspan="2" style="text-align:center;">
            			<input type="button"
                           class="button button-primary input-design"
                           value="<?php _e( "Insert Poll", 'yop_poll' ); ?>"
                           onclick="getYOPDisplayResults(); insertYopPollTinyMce( document.getElementById('yop-poll-id-dialog').value, document.getElementById('yop-poll-tr-id-dialog').value, yop_display_results);"/>
            		</td>
            	</tr>
            </table>
            </body>
            </html>
        <?php
        }
        die ();
    }
    public function ajax_get_polls_for_html_editor() {
        check_ajax_referer( 'yop-poll-html-editor' );
        if( is_admin() ) {
            require_once( YOP_POLL_MODELS . 'yop_poll_model.php' );
            $yop_polls = Yop_Poll_Model::get_polls_for_view();
            ?>
            <title><?php _e( "Insert Poll", 'yop_poll' ); ?></title>
             <style>
                	body {
                		background: #ffffff 50% 50% repeat-x;
                	}
                	select {
                		background-color: #fff;
                		outline: 0;
                		transition: .05s border-color ease-in-out;
                		margin: 1px;
                		padding: 3px 5px;
                		font-size: 13px;
						line-height: 26px;
						height: 28px;
                	}
                	input[type="text"] {
                		background-color: #fff;
                		outline: 0;
                		transition: .05s border-color ease-in-out;
                		margin: 1px;
                		padding: 3px 5px;
                		font-size: 13px;
						line-height: 26px;
						height: 28px;
                	}
                	input[type="radio"] {
                		background-color: #fff;
                		outline: 0;
                		transition: .05s border-color ease-in-out;
                		margin: 1px;
                		padding: 3px 5px;
                		font-size: 13px;
						line-height: 26px;
                	}
                	input[type="button"] {
                		color: #555;
						border-color: #ccc;
						background: #f7f7f7;
						-webkit-box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
						box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
						vertical-align: top;
						display: inline-block;
						text-decoration: none;
						font-size: 13px;
						line-height: 26px;
						height: 28px;
						margin: 0;
						padding: 0 10px 1px;
						cursor: pointer;
						border-width: 1px;
						border-style: solid;
						-webkit-appearance: none;
						-webkit-border-radius: 3px;
						border-radius: 3px;
						white-space: nowrap;
						-webkit-box-sizing: border-box;
                	}
                	table {
                		width: 95%;
                		height: 95%;
                		margin: auto;
                		border: 1px solid #e5e5e5;
						-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);
						box-shadow: 0 1px 1px rgba(0,0,0,.04);
						border-spacing: 0;
						width: 100%;
						clear: both;
						margin: 0;
                	}
                	tr {
                		line-height: 40px;
                	}
                	td {
                		padding-left: 10px;
                	}
                </style>
                <script>
                	var yop_display_results = "-1";
                	function getYOPDisplayResults(){
                		var yop_results_radio = document.getElementsByName( "show_results" );
                		if( yop_results_radio[1].checked ) {
                			yop_display_results = "1";
                		}
                	}
                </script>
                <table>
            	<tr>
            		<td>
            			<?php _e("Poll to Display", 'yop_poll' ); ?>:
            		</td>
            		<td>
            			<select class="widefat" name="yop_poll_id" id="yop-poll-id-dialog">
							<option value="-3"><?php _e( "Display Random Poll", 'yop_poll' ); ?></option>
							<option value="-2"><?php _e( "Display Latest Poll", 'yop_poll' ); ?></option>
							<option value="-1"><?php _e( "Display Current Active Poll", 'yop_poll' ); ?></option>
							<?php
							if( count( $yop_polls ) > 0 ) {
								foreach( $yop_polls as $yop_poll ) {
									?>
									<option value="<?php echo $yop_poll['ID']; ?>">
										<?php echo esc_html( stripslashes( $yop_poll['poll_title'] ) ); ?>
									</option>
								<?php
								}
							}
							?>
						</select>
            		</td>
            	</tr>
            	<tr>
            		<td>
            			<?php _e( "Tracking ID", 'yop_poll' ); ?>:
            		</td>
            		<td>
            			<input type="text" name="yop_poll_tr_id" id="yop-poll-tr-id-dialog" size="35" placeholder="Leave empty if none" />
            		</td>
            	</tr>
            	<tr>
            		<td>
            			<? _e( "Display Results Only", 'yop-poll' ); ?>:
            		</td>
            		<td>
            			<input type="radio" name="show_results" value="-1" checked="checked">No
						<input type="radio" name="show_results" value="1">Yes
            		</td>
            	</tr>
            	<tr>
            		<td colspan="2" style="text-align:center;">
            			<input type="button"
                           class="button"
                           value="<?php _e( "Insert Poll", 'yop_poll' ); ?>"
                           onclick="getYOPDisplayResults(); insertYopPoll( edCanvas, document.getElementById('yop-poll-id-dialog').value, document.getElementById('yop-poll-tr-id-dialog').value, yop_display_results);" />
            		</td>
            	</tr>
            </table>

            <!--
            <p style="text-align: center;">
                <label for="yop-poll-id-html-dialog"> <span><?php _e( 'Pollsss to Display', 'yop_poll' ); ?>:</span>
                    <select class="widefat" name="yop_poll_id" id="yop-poll-id-html-dialog">
                        <option value="-3"><?php _e( 'Display Random Poll', 'yop_poll' ); ?></option>
                        <option value="-2"><?php _e( 'Display Latest Poll', 'yop_poll' ); ?></option>
                        <option value="-1"><?php _e( 'Display Current Active Poll', 'yop_poll' ); ?></option>
                        <?php
                        if( count( $yop_polls ) > 0 ) {
                            foreach( $yop_polls as $yop_poll ) {
                                ?>
                                <option value="<?php echo $yop_poll['ID']; ?>">
                                	<?php echo esc_html( stripslashes( $yop_poll['poll_title'] ) ); ?>
                                </option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                </label>

                <br/><br/>
                <label for="yop-poll-tr-id-html-dialog"> <span><?php _e( 'Tracking ID', 'yop_poll' ); ?>:</span>
                    <input type="text"
                           name="yop_poll_tr_id"
                           id="yop-poll-tr-id-html-dialog"
                           class="widefat"
                           value=""/>
                </label>
                <br/><br/>
                <label for="yop-poll-tr-results-html-dialog"> <span><?php _e( 'Display results only', 'yop_poll' ); ?>:</span>
                    <select
                        name="show_results"
                        id="yop-poll-tr-results-html-dialog"
                        class="widefat"
                        >
                        <option value="-1">-1</option>
                        <option value="1">1</option>
                    </select>
                </label>


                <br/> <br/> <input type="button"
                                   class=""
                                   value="<?php _e( 'Insert Poll', 'yop_poll' ); ?>"
                                   onclick=" insertYopPoll( edCanvas, document.getElementById('yop-poll-id-html-dialog').value, document.getElementById('yop-poll-tr-id-html-dialog').value ,document.getElementById('yop-poll-tr-results-html-dialog'));"/>
                <br/>
            </p>
			-->
        <?php
        }
        die ();
    }
    public function ajax_preview_add_edit() {
        if( is_admin() ) {
            if( ! check_ajax_referer( 'yop-poll-add-edit-action', 'yop-poll-add-edit-name', false ) ) {
                wp_die( __yop_poll( 'You are not allowed to access this request.' ) );
            }
            print Yop_Poll_Model::save_poll( true );
        }
        die();
    }
    function load_editor_functions( $hook ) {
        global $post;

        if( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
            $yop_poll_editor_config = array(
                'dialog_url'                  => wp_nonce_url( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_editor', 'yop-poll-editor' ),
                'dialog_html_url'             => wp_nonce_url( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_html_editor', 'yop-poll-html-editor' ),
                'name'                        => __( 'Yop Poll', 'yop_poll' ),
                'title'                       => __( 'Insert Poll', 'yop_poll' ),
                'prompt_insert_poll_id'       => __( 'Please insert the poll ID:\n\n', 'yop_poll' ),
                'prompt_insert_again_poll_id' => __( 'Error: Poll Id must be numeric!\n\nPlease insert the poll ID Again:\n\n', 'yop_poll' )
            );
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_script( 'yop-poll-editor-functions', YOP_POLL_URL . "tinymce/yop-poll-editor-functions.js", 'jquery', YOP_POLL_VERSION, true );
            wp_localize_script( 'yop-poll-editor-functions', 'yop_poll_editor_config', $yop_poll_editor_config );
        }
    }
    //endregion
    // region TINYMCE
    function register_button( $buttons ) {
        array_push( $buttons, "separator", "yoppoll" );

        return $buttons;
    }
    function add_plugin( $plugin_array ) {
        $plugin_array ['yoppoll'] = YOP_POLL_URL . "tinymce/yop-poll-editor.js";
        return $plugin_array;
    }
    function my_yop_poll_button( $hook ) {
        if( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
            if( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
                return;
            }

            if( get_user_option( 'rich_editing' ) == 'true' ) {
                add_filter( 'mce_external_plugins', array(
                    &$this,
                    'add_plugin'
                ) );
                add_filter( 'mce_buttons', array(
                    &$this,
                    'register_button'
                ) );
            }
        }
    }
    public function add_votes(){
        if( is_admin() ) {

            if( ! check_ajax_referer( 'yop-poll-results_vote_add_vote','yop-poll-results_votes_add_votes', false ) ) {

                wp_die( __yop_poll( 'You are not allowed to access this request.' ) );

            }
            $pollAdminObj = YOP_POLL_Poll_Admin::get_instance();
            $pollAdminObj->add_votes();
        }

        die();

    }
    protected function init() {
        global $maintenance;
        $this->add_action( 'plugins_loaded', 'db_update' );
        $this->add_action( 'init', 'admin_loader', 1 );
        add_action( 'wpmu_new_blog', array(
            &$maintenance,
            'new_blog'
        ), 10, 6 );
        add_action( 'delete_blog', array(
            &$maintenance,
            'delete_blog'
        ), 10, 2 );
        add_action( 'plugins_loaded', array(
            &$maintenance,
            'update'
        ) );
    }
    //endregion
}
;
?>
