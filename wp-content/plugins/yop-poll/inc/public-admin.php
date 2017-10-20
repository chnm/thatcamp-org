<?php

    class Yop_Poll_Public_Admin extends Yop_Poll_Plugin {

        protected function init() {
            $this->add_action( 'init', 'load_translation_file', 1 );
            $this->add_action( 'init', 'public_loader', 1 );
            $this->add_filter( 'the_content', 'yop_poll_do_shortcode_the_content_filter', 1 );
            $this->add_action( 'widgets_init', 'widget_init' );
            $this->add_filter( 'widget_text', 'do_shortcode' );
            $this->add_action( 'init', 'yop_poll_setup_schedule' );
            $this->add_action( 'yop_poll_hourly_event', 'yop_poll_do_scheduler' );
        }

        public function yop_poll_setup_schedule() {
            $schedule_timestamp = wp_next_scheduled( 'yop_poll_hourly_event', array() );
            $yop_poll_options   = get_option( 'yop_poll_options', false );
            if( 'yes' == $yop_poll_options['start_scheduler'] ) {
                if( ! $schedule_timestamp ) {
                    wp_schedule_event( strtotime( substr( current_time( 'mysql' ), 0, 14 ) . '00:01' ), 'hourly', 'yop_poll_hourly_event', array() );
                }
            }
            else {
                wp_unschedule_event( $schedule_timestamp, 'yop_poll_hourly_event', array() );
            }
        }

        public function yop_poll_do_scheduler() {
            require_once( YOP_POLL_MODELS . 'yop_poll_model.php' );
            $yop_polls = Yop_Poll_Model::get_polls_for_view( array( 'return_fields' => 'ID' ) );
            if( count( $yop_polls ) > 0 ) {
                foreach( $yop_polls as $yop_poll_id ) {

                    $yop_poll_options = Yop_Poll_Model::get_poll_options_by_id( $yop_poll_id['ID'] );

                    if( 'yes' == $yop_poll_options['schedule_reset_poll_stats'] ) {
                    	$reset_time = new DateTime( $yop_poll_options['schedule_reset_poll_date'] );
                    	$now_date = new DateTime(date("Y-m-d H:i:s", current_time( 'timestamp' )));
                    	if( $reset_time -> format("Y-m-d H") == $now_date -> format("Y-m-d H") ){
                            $unit_multiplier = 0;

                            switch( strtolower( trim( $yop_poll_options['schedule_reset_poll_recurring_unit'] ) ) ) {
                                case 'hour':
                                    $unit_multiplier = 60 * 60;
                                    break;
                                case 'day' :
                                    $unit_multiplier = 60 * 60 * 24;
                                    break;
                            }

                            $next_reset_date = strtotime( $yop_poll_options['schedule_reset_poll_date'] ) + ( intval( $yop_poll_options['schedule_reset_poll_recurring_value'] ) * $unit_multiplier );
                            if( $next_reset_date <= current_time( 'timestamp' ) ) {
                                $next_reset_date = strtotime( substr( current_time( 'mysql' ), 0, 11 ) . substr( date( 'Y-m-d H:i:s', $yop_poll_options['schedule_reset_poll_date'] ), 11, 2 ) . ':00:00' ) + intval( $yop_poll_options['schedule_reset_poll_recurring_value'] ) * $unit_multiplier;
                            }
                            $poll_options                             = get_yop_poll_meta( $yop_poll_id['ID'], 'options', true );
                            $poll_options['schedule_reset_poll_date'] = date( 'd-m-Y H:i:s', $next_reset_date );
                            update_yop_poll_meta( $yop_poll_id['ID'], 'options', $poll_options );
                            //Call reset stats function
                            YOP_POLL_Abstract_Model::reset_poll_stats_from_database( $yop_poll_id['ID'] );
                        }
                    }
                }
            }
        }

        /**
         * this file initialize the text domain for translation file
         *
         */
        public function load_translation_file() {
            $plugin_path = YOP_POLL_PLUGIN_DIR . '/languages';
            load_plugin_textdomain( 'yop_poll', false, $plugin_path );
        }

        public function yop_poll_do_shortcode_the_content_filter( $content ) {
            global $shortcode_tags;
            // Backup current registered shortcodes and clear them all out
            $orig_shortcode_tags = $shortcode_tags;
            $shortcode_tags      = array();

            // Do the shortcode (only the one above is registered)
            $content = do_shortcode( $content );

            // Put the original shortcodes back
            $shortcode_tags = $orig_shortcode_tags;

            return $content;
        }

        public function do_shortcode( $content ) {
            return do_shortcode( $content );
        }

        public function public_loader() {
            add_shortcode( 'yop_poll', array( &$this, 'yop_poll_shortcode_function' ) );
            add_shortcode( 'yop_poll_archive', array( &$this, 'yop_poll_archive_shortcode_function' ) );
        }

        /**
         * Start shortcodes
         */

        public function yop_poll_archive_shortcode_function( $atts, $content = null ) {
            extract( shortcode_atts( array( 'results' => 0 ), $atts ) );
            $template      = '';
            $yop_poll_page = 1;
            $big           = 99999;
            if( isset( $_REQUEST['yop_poll_page'] ) ) {
                $yop_poll_page = $_REQUEST['yop_poll_page'];
            }
            $general_default_options = get_option( 'yop_poll_options', false );
            $archive                 = get_option( 'yop_poll_archive_order', array() );
            $archives                = $archive;
            $ok=0;
            if( ( $general_default_options['sorting_archive_polls'] == "database" ) && ( $general_default_options['sorting_archive_polls_rule'] == "asc" ) ) {
                $archives = yop_poll_sort_asc_database( $archive );
                $ok=1;
            }

            else if( ( $general_default_options['sorting_archive_polls'] == "database" ) && ( $general_default_options['sorting_archive_polls_rule'] == "desc" ) ) {
                $archives = yop_poll_sort_desc_database( $archive );
                $ok=1;
            }

            if( ( $general_default_options['sorting_archive_polls'] == "votes" ) && ( $general_default_options['sorting_archive_polls_rule'] == "asc" ) ) {
                $archives = yop_poll_ret_poll_by_votes_asc( $archive );
                $ok=1;
            }

            else if( ( $general_default_options['sorting_archive_polls'] == "votes" ) && ( $general_default_options['sorting_archive_polls_rule'] == "desc" ) ) {
                $archives = yop_poll_ret_poll_by_votes_desc( $archive );
                $ok=1;
            }

            if($ok==1){
            if( count( $archives ) > 0 ) {
                foreach( $archives as $poll ){

                    $template [] = $this->return_yop_poll( $poll['ID'], $results );
                }
            }
            }else{
                if( count( $archives ) > 0 ) {
                    foreach( $archives as $poll ){

                        $template [] = $this->return_yop_poll( $poll, $results );
                    }
                }
            }
            $total_per_page = 1;
            if( $general_default_options['archive_polls_per_page'] > 0 && $general_default_options['archive_polls_per_page'] < count( $archive ) ) {
                $total_per_page = round( count( $archive ) / $general_default_options['archive_polls_per_page'] );
                $per_page       = $general_default_options['archive_polls_per_page'];

            }
            if( $general_default_options['archive_polls_per_page'] >= count( $archive ) ) {
                $total_per_page = 1;
                $per_page       = count( $archive );
            }

            $query_arg = remove_query_arg( 'yop_poll_page', $_SERVER['REQUEST_URI'] );
            $query_url = parse_url($query_arg, PHP_URL_QUERY);
            if( isset($query_url) && ($query_url != "")) {
                $query_char = "&";
            }
            else {
                $query_char = "?";
            }
            $args = array(
                'base'      => $query_arg . '%_%',
                'format'    => $query_char . 'yop_poll_page=%#%',
                'total'     => $total_per_page,
                'current'   => max( 1, $yop_poll_page ),
                'prev_next' => true,
                'prev_text' => __( '&laquo; Previous', 'yop_poll' ),
                'next_text' => __( 'Next &raquo;', 'yop_poll' )
            );

            $temp = "<style> .yop-poll-container{display:block !important;}.yop-poll-footer{display: inline-block;margin: auto;text-align: center;width: 100%;}</style>";
            if($ok==1){
                for( $i = ( $args['current'] - 1 ) * $per_page; $i <= ( $args['current'] - 1 ) * $per_page + $per_page - 1; $i ++ ) {
                  if(isset($archives[$i]['ID']))
                    $temp .= $this->return_yop_poll( $archives[$i]['ID'], $results );
                }
            }else{
                for( $i = ( $args['current'] - 1 ) * $per_page; $i <= ( $args['current'] - 1 ) * $per_page + $per_page - 1; $i ++ ) {
                    if(isset($archives[$i]['ID']))
                    $temp .= $this->return_yop_poll( $archives[$i], $results );
                }
            }
            return $temp . paginate_links( $args );



        }




        public function return_yop_poll( $id, $results, $tr_id = '',$show_results="", $offset = 0 ) {
            //$pro_options = get_option( 'yop_poll_pro_options' );
            $options = get_option( 'yop_poll_options' );

            require_once( YOP_POLL_MODELS . "poll_model.php" );

            $poll_unique_id            = uniqid( '_yp' );
            $yop_poll_model            = new YOP_POLL_Poll_Model( $id );
            $yop_poll_model->unique_id = $poll_unique_id;

            if( ! $yop_poll_model->ID ) {
                return '';
            }

            $yop_poll_public_config_general = array(
                'ajax'                          => array(
                    'url'                        => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
                    'vote_action'                => 'yop_poll_do_vote',
                    'yop_poll_show_vote_options' => 'yop_poll_show_vote_options',
                    'captcha_action'             => 'yop_poll_show_captcha',
                    'view_results_action'        => 'yop_poll_view_results',
                    'back_to_vote_action'        => 'yop_poll_back_to_vote',
                    'is_wordpress_user_action'   => 'yop_poll_is_wordpress_user',
                    'is_facebook_user_action'    => 'yop_poll_is_facebook_user',
                ),
                'pro'                           => array( /*'api_key'        => $pro_options['pro_api_key'],
					'pro_user'       => $pro_options['pro_user'],
					'api_server_url' => $pro_options['pro_api_server_url'],
					'pro_token'      => md5( $_SERVER['HTTP_HOST'] . $pro_options['pro_key'] )*/
                ),
                'yop_poll_version'              => YOP_POLL_VERSION,
                'vote_with_wordpress_login_url' => wp_login_url( admin_url( 'admin-ajax.php?action=yop_poll_set_wordpress_vote', ( is_ssl() ? 'https' : 'http' ) ) ),
                'vote_with_facebook_ajax_url'   => admin_url( 'admin-ajax.php?action=yop_poll_set_facebook_vote', ( is_ssl() ? 'https' : 'http' ) ),
            );
            $vote_permisions_types = 0;
            if( in_array( 'registered', $yop_poll_model->vote_permisions ) ) {
            	if( 'yes' == $yop_poll_model->vote_permisions_wordpress ) {
                    $vote_permisions_types += 1;
                }
                if( 'yes' == $yop_poll_model->vote_permisions_anonymous &&in_array( 'guest', $yop_poll_model->vote_permisions)|| 'yes' == $yop_poll_model->vote_permisions_anonymous &&in_array( 'registered', $yop_poll_model->vote_permisions)) {
                    $vote_permisions_types += 2;
                }
            }
            else
            	if( in_array( 'guest', $yop_poll_model->vote_permisions ) ) {
                	if( 'yes' == $yop_poll_model->vote_permisions_anonymous &&in_array( 'guest', $yop_poll_model->vote_permisions)) {
                    	$vote_permisions_types += 2;
                    }
                }
            if( 'yes' == $options["use_default_loading_image"] ) {
            	$loading_image_src = YOP_POLL_URL . 'images/loading36x36.gif';
        	}
        	else {
        	    $loading_image_src = $options["loading_image_url"];
            }

            if( $results ) {
                $yop_poll_model->vote = true;
            }
            $template               =  $yop_poll_model->return_poll_html( array( 'tr_id'    => $tr_id,

                'location' => 'page',
                'load_css' => true,
                'load_js' => true ,
                'show_results'=>$show_results
            ) );
            $yop_poll_public_config = array(
                'poll_options'      => array(
                    'vote_permisions'                 => $yop_poll_model->vote_permisions,
                    'vote_permisions_facebook_label'  => $yop_poll_model->vote_permisions_facebook_label,
                    'vote_permisions_wordpress_label' => $yop_poll_model->vote_permisions_wordpress_label,
                    'vote_permisions_anonymous_label' => $yop_poll_model->vote_permisions_anonymous_label,
                    'vote_permisions_google_label'    => $yop_poll_model->vote_permisions_google_label,
                    'vote_permisions_types'           => $vote_permisions_types,
                    'share_after_vote'                => $yop_poll_model->share_after_vote,
                    'share_name'                      => $yop_poll_model->share_name,
                    'share_caption'                   => $yop_poll_model->share_caption,
                    'share_description'               => $yop_poll_model->share_description,
                    'share_picture'                   => $yop_poll_model->share_picture,
                    'share_poll_name'                 => $yop_poll_model->poll['name'],
                    'share_link'                      => $yop_poll_model->poll_page_url == '' ? site_url() : $yop_poll_model->poll_page_url,
                    'redirect_after_vote'             => $yop_poll_model->redirect_after_vote,
                    'redirect_after_vote_url'         => $yop_poll_model->redirect_after_vote_url,
                    'facebook_share_after_vote'       => $yop_poll_model->facebook_share_after_vote,
                ),
                'loading_image_src' => $loading_image_src,
                'loading_image_alt' => __yop_poll( 'Loading' ),
            );

			      $tabulate['results']=false;
            $tabulate['answers']=false;
            $tabulate['orizontal_answers']=0;
            $tabulate['orizontal_results']=0;
            wp_enqueue_script( 'yop-poll-public-js', YOP_POLL_URL . "js/yop-poll-public.js", array( 'jquery' ), YOP_POLL_VERSION, true );
                foreach($yop_poll_model->questions as $question){


                    $answers_tabulated_cols = 1; //vertical display
                    $results_tabulated_cols = 1;

                    $include_others  = false;
                    $display_answers = array( 'text', 'image', 'video' );

                    if( isset( $question->allow_other_answers ) && 'yes' == $question->allow_other_answers ) {

                        if( isset( $question->display_other_answers_values ) && 'yes' == $question->display_other_answers_values ) {
                            $include_others  = true;
                            $display_answers = array( 'text', 'image', 'video', 'other' );
                        }
                    }



                    if( 'orizontal' == $question->display_answers ) {
                        $ans_no = $question->countanswers( $display_answers, $include_others );
                        if( $ans_no > 0 ) {
                            $tabulate['orizontal_answers'] = $ans_no;

                        }
                        if( isset( $question->allow_other_answers ) && 'yes' == $question->allow_other_answers ) {
                            $tabulate['orizontal_answers'] ++;
                        }
                    }

                    if( 'orizontal' == $question->display_results ) {
                        $ans_no = $question->countanswers( $display_answers, $include_others );
                        if( $ans_no > 0 ) {
                            $tabulate['orizontal_results'] = $ans_no;
                        }
                    }
                    if( 'tabulated' == $question->display_answers )
                        $tabulate['answers']=true;
                    if( 'tabulated' == $question->display_results )
                        $tabulate['results']=true;
                }


            wp_enqueue_script( 'yop-poll-public-js', YOP_POLL_URL . "js/yop-poll-public.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_localize_script( 'yop-poll-public-js', 'tabulate', $tabulate );
            wp_enqueue_script( 'yop-poll-supercookie-js', YOP_POLL_URL . "js/yop-poll-supercookie.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'superCookie-min-js', YOP_POLL_URL . "js/super-cookie/superCookie-min.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'swfobject-js', YOP_POLL_URL . "js/super-cookie/swfobject/swfobject.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'yop-poll-jquery-popup-windows', YOP_POLL_URL . "js/jquery.popupWindow.js", array(), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'yop-poll-jquery-base64', YOP_POLL_URL . "js/yop-poll-jquery.base64.min.js", array(), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'yop-poll-json-2', YOP_POLL_URL . "js/yop-poll-json2.js", array(), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'yop-poll-user-defined_' . $yop_poll_model->ID . $poll_unique_id, add_query_arg( array( 'id'        => $id,
                                                                                                                       'location'  => 'page',
                                                                                                                       'unique_id' => $poll_unique_id
                                                                                                                ), admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_load_js' ), array( 'jquery' ), YOP_POLL_VERSION, true );

            wp_localize_script( 'yop-poll-public-js', 'yop_poll_public_config_general', $yop_poll_public_config_general );
            wp_localize_script( 'yop-poll-public-js', 'yop_poll_public_config_' . $yop_poll_model->ID . $poll_unique_id, $yop_poll_public_config );

           // wp_enqueue_style( 'yop-poll-public-css', "{$this->_config->plugin_url}css/yop-poll-admin.css", array(), YOP_POLL_VERSION );

            /*wp_enqueue_style( 'yop-poll-public-css', "{$this->_config->plugin_url}/css/yop-poll-public.css", array(), $this->_config->version );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'yop-poll-jquery-popup-windows', "{$this->_config->plugin_url}/js/jquery.popupWindow.js", array(), $this->_config->version, true );
            wp_enqueue_script( 'yop-poll-user-defined_' . $id . $poll_unique_id, add_query_arg( array( 'id' => $id, 'location' => 'page', 'unique_id' => $poll_unique_id ), admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_load_js' ), array( 'jquery' ), $this->_config->version, true );
            wp_enqueue_script( 'yop-poll-public-js', "{$this->_config->plugin_url}js/yop-poll-public.js", array(), $this->_config->version, true );
            wp_enqueue_script( 'yop-poll-json2', "{$this->_config->plugin_url}/js/yop-poll-json2.js", array(), $this->_config->version, true );
            wp_enqueue_script( 'yop-poll-jquery-base64', "{$this->_config->plugin_url}/js/yop-poll-jquery.base64.min.js", array(), $this->_config->version, true );
            */

            return $template;
        }

        public function yop_poll_shortcode_function( $atts, $content = null ) {

            extract( shortcode_atts( array(
                'id'      => - 1,
                'results' => 0,
                'tr_id'   => '',
                'offset'  => 0,
                'show_results'=>''
            ), $atts ) );
            return $this->return_yop_poll( $id, $results, $tr_id,$show_results, $offset );

        }

        public function widget_init() {
            register_widget( 'Yop_Poll_Widget' );
        }

        /**
         * End shortcodes
         */


    }
