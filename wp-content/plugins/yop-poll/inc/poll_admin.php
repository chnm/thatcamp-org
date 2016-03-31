<?php

class YOP_POLL_Poll_Admin extends YOP_POLL_Abstract_Admin {

    private static $_instance = null;

    public static function get_instance() {
        if( self::$_instance == null ) {
            $class           = __CLASS__;
            self::$_instance = new $class;
        }

        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct( 'polls' );
    }

    private function __clone() {
    }

    public function manage_polls() {
        switch( $GLOBALS['page'] ) {
            case 'yop-polls':
                switch( $GLOBALS['action'] ) {
                    case 'edit':
                        $this->manage_edit_poll();
                        break;
                    case 'delete':
                        $this->delete_poll();
                        $this->view_polls();
                        break;
                    case 'clone':
                        $this->clone_poll();
                        $this->view_polls();
                        break;;
                    case "addvote":
                        self::add_vote();
                        break;
                    case "print_votes":
                        $this->print_votes();
                        break;
                    case "reset_votes":
                        self::reset_stats();
                        break;
                    case "resultvotes":
                        $this->view_results_votes();
                        break;
                    case "delete_result":
                        self::delete_results();
                        break;
                    case "after-buy":
                        YOP_POLL_Pro_Admin::after_buy();
                        break;
                    case "do-buy":
                        YOP_POLL_Pro_Admin::do_buy();
                        break;

                    default:
                        $this->view_polls();
                        break;
                }
                break;

            case 'yop-polls-add-new' :
                switch( $GLOBALS['action'] ) {
                    case "after-buy":
                        YOP_POLL_Pro_Admin::after_buy();
                        break;
                    case "do-buy":
                        YOP_POLL_Pro_Admin::do_buy();
                        break;

                    default:
                        $this->manage_add_new();
                        break;
                }

        }
    }

    public function manage_load_polls() {

        if( isset( $_REQUEST['a'] ) ) {
            if( $_REQUEST['a'] == "Export" ) {
                self::export_custom_field();
            }

        }

        if( isset( $_REQUEST['a_v'] ) ) {
            if( $_REQUEST['a_v'] == "Export" ) {
                self::export_results_votes();
            }

        }

        switch( $GLOBALS['page'] ) {
            case 'yop-polls':
            {
                switch( $GLOBALS['action'] ) {
                    case 'edit':
                        $this->manage_load_add_edit( 'edit' );
                        break;
                    case "results":
                        $this->manage_load_results();
                        break;
                    default:
                        $this->view_all_polls_operations();
                        break;
                }
                break;
            }
            case 'yop-polls-add-new' :
            {
                $this->manage_load_add_edit();
                break;
            }
        }
    }

    private function manage_add_new() {
        $this->view_add_edit( 'add' );
    }

    private function manage_edit_poll() {
        $this->view_add_edit( 'edit' );
    }

    private function manage_load_add_edit( $action_type = 'add' ) {

        wp_enqueue_style( 'yop-poll-add-edit-css', YOP_POLL_URL . 'css/polls/add-edit.css', array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-admin-css', YOP_POLL_URL . 'css/yop-poll-admin.css', array(), YOP_POLL_VERSION );

        wp_enqueue_style( 'yop-poll-slider-css', YOP_POLL_URL . 'css/yop-poll-slider.css', array(), YOP_POLL_VERSION );

        wp_enqueue_script( 'yop-poll-add-edit-js', YOP_POLL_URL . 'js/polls/yop-poll-add-edit.js', array(
            'jquery',
            'jquery-ui-sortable',
            'jquery-ui-dialog',
        ), YOP_POLL_VERSION, true );

        wp_enqueue_script( 'yop-poll-slider-js', YOP_POLL_URL . 'js/yop-poll-slider.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );


        $poll_options = get_option( 'yop_poll_options', array() );
        if( isset( $poll_options['user_interface_type'] ) && $poll_options['user_interface_type'] == 'beginner' ) {
            wp_enqueue_style( 'yop-poll-wizard-css', YOP_POLL_URL . 'css/yop-poll-wizard.css', array(), YOP_POLL_VERSION );
            wp_enqueue_script( 'yop-poll-wizard-js', YOP_POLL_URL . 'js/polls/yop-poll-wizard.js', array( 'jquery' ), YOP_POLL_VERSION, true );
            $translation_array = array(
                'next_next' => __( "Next" ),
                'prev_prev' => __yop_poll( "Previous" ),
                'savee' => __('Save'),
                'empty_answer' => __yop_poll( "Please fill in empty answers from Question" )
            );
            wp_localize_script( 'yop-poll-wizard-js', 'button_yop', $translation_array );
            wp_enqueue_script( 'jquery-ui-dialog' );
            $isdone = array( - 1 );
            if( 'edit' == $action_type ) {
                $isdone = array( 1 );
            }
            wp_localize_script( 'yop-poll-wizard-js', 'isdone', $isdone );
        }

    }

    private function manage_load_results() {
        wp_enqueue_script( 'yop-poll-gJsApi', "https://www.google.com/jsapi", array(), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-charts', YOP_POLL_URL . 'js/yop-poll-charts.js', array( 'jquery' ), YOP_POLL_VERSION, true );
    }

    private function view_polls() {
        $data     = array();
        $data['ok']=0;
        $time_format="H:i:s";
        $options                     = get_option('yop_poll_options' );
        if($options['date_format']=="UE")
            $date_format="d-m-Y";            else{
            $date_format="m-d-Y";
        }
        $data['date_format']=$date_format.' '.$time_format;


        wp_enqueue_style( 'yop-poll-slider-css', YOP_POLL_URL . 'css/yop-poll-slider.css', array(), YOP_POLL_VERSION );

        wp_enqueue_script( 'yop-poll-add-edit-js', YOP_POLL_URL . 'js/polls/yop-poll-add-edit.js', array(
            'jquery',
            'jquery-ui-sortable',
            'jquery-ui-dialog',
        ), YOP_POLL_VERSION, true );

        wp_enqueue_script( 'yop-poll-slider-js', YOP_POLL_URL . 'js/yop-poll-slider.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );
        $data['poll_url']= YOP_POLL_URL;
        $optin_box_modal_options = get_option( 'yop_poll_optin_box_modal_options_yop' );
        wp_enqueue_script( 'yop-poll-slider-pro-js', YOP_POLL_URL . 'js/yop-poll-slider-pro.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );
        
        if ( $optin_box_modal_options['show'] == 'yes' ){
            wp_enqueue_script( 'yop-poll-modal-box-js', YOP_POLL_URL."js/custombox.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'yop-poll-modal-box-js-leg', YOP_POLL_URL."js/legacy.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_enqueue_style( 'yop-poll-view-poll-css', YOP_POLL_URL . 'css/custombox.css', array(), YOP_POLL_VERSION );
            
            wp_enqueue_script( 'yop-poll-modal-functions',YOP_POLL_URL."js/yop-poll-modal-functions.js", array( 'jquery', 'yop-poll-modal-box-js' ), YOP_POLL_VERSION, true );
            $yop_poll_modal_functions_config = array( 'ajax' => array( 'url' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), 'action' => 'yop_poll_modal_option_signup' ) );
            wp_localize_script( 'yop-poll-modal-functions', 'yop_poll_modal_functions_config', $yop_poll_modal_functions_config );
            $data['optin_box_modal_query']   = admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) );
            $optin_box_modal_query   = admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) );
            $data['optin_box_modal_query']   = add_query_arg( 'action', 'yop_poll_show_optin_box_modal', $optin_box_modal_query );
            wp_enqueue_script( 'link' );
            wp_enqueue_script( 'xfn' );

			$optin_box_modal_options ['show']          = 'no'; //restore to no
			$optin_box_modal_options ['sidebar_had_submit'] = 'no';
			$optin_box_modal_options ['modal_had_submit'] = 'no';
			$optin_box_modal_options['modal_email']="";
			update_option( 'yop_poll_optin_box_modal_options_yop', $optin_box_modal_options );
        }
        else{
			if($optin_box_modal_options['sidebar_had_submit'] == 'no'){
				$data['ok']=1;
				wp_enqueue_script( 'yop-poll-sidebar-option-functions',  YOP_POLL_URL."js/yop-poll-sidebar-optin-functions.js", array( 'jquery' ), YOP_POLL_VERSION, true );
				$yop_poll_sidebar_functions_config = array( 'ajax' => array( 'url' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), 'action' => 'yop_poll_sidebar_option_signup' ) );
				wp_localize_script( 'yop-poll-sidebar-option-functions', 'yop_poll_sidebar_functions_config', $yop_poll_sidebar_functions_config );

				wp_enqueue_style( 'yop-poll-view-poll-css', YOP_POLL_URL . 'css/polls/view-poll.css', array(), YOP_POLL_VERSION );
				wp_enqueue_style( 'yop-poll-donate-css', YOP_POLL_URL . 'css/yop-poll-donate.css', array(), YOP_POLL_VERSION );
				wp_enqueue_script( 'link' );
				wp_enqueue_script( 'xfn' );
				//wp_enqueue_script( 'yop-poll-optin-form', "http://app.getresponse.com/view_webform.js?wid=394041&mg_param1=1", NULL, YOP_POLL_VERSION, true );
			}
        }

        $per_page = ( isset( $_REQUEST ['per_page'] ) && intval( $_REQUEST ['per_page'] ) > 0 ) ? intval( $_REQUEST ['per_page'] ) : 100;
        $page_no  = ( isset( $_REQUEST ['page_no'] ) && intval( $_REQUEST ['page_no'] ) > 0 ) ? intval( $_REQUEST ['page_no'] ) : 1;
        $orderby  = ( empty ( $GLOBALS['orderby'] ) ) ? 'ID' : $GLOBALS['orderby'];
        $order    = ( empty ( $GLOBALS['order'] ) ) ? 'asc' : $GLOBALS['order'];

        wp_enqueue_style( 'yop-poll-view-poll-css', YOP_POLL_URL . 'css/polls/view-poll.css', array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-donate-css', YOP_POLL_URL . 'css/yop-poll-donate.css', array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-optin2-css', YOP_POLL_URL . 'css/yop-poll-optin2.css', array(), YOP_POLL_VERSION );
        wp_enqueue_script( 'link' );
        wp_enqueue_script( 'xfn' );

        $order_fields = array(
            'ID',
            'poll_title',
            'poll_start_date',
            'poll_end_date',
            'poll_total_votes'
        );
        wp_enqueue_script( 'yop-poll-add-edit-js', YOP_POLL_URL . '/js/polls/yop-poll-add-edit.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );
        $filters   = array();
        $filters[] = array(
            'field'    => 'poll_type',
            'value'    => 'poll',
            'operator' => '='
        );

        if( isset ( $_REQUEST ['filters'] ) ) {
            switch( $_REQUEST ['filters'] ) {
                case 'started' :
                    $filters[] = array(
                        'field'    => 'poll_start_date',
                        'value'    => current_time( 'mysql' ),
                        'operator' => '<='
                    );
                    break;
                case 'not_started' :
                    $filters[] = array(
                        'field'    => 'poll_start_date',
                        'value'    => current_time( 'mysql' ),
                        'operator' => '>='
                    );
                    break;
                case 'never_expire' :
                    $filters[] = array(
                        'field'    => 'poll_end_date',
                        'value'    => '01-01-2038 23:59:59',
                        'operator' => '='
                    );
                    break;
                case 'expired' :
                    $filters[] = array(
                        'field'    => 'poll_end_date',
                        'value'    => current_time( 'mysql' ),
                        'operator' => '<='
                    );
                    break;
            }
        }

        $args = array(
            'return_fields' => 'COUNT(*) as total_polls',
            'filters'       => $filters,
            'search'        => array(
                'fields' => array( 'poll_title' ),
                'value'  => isset ( $_REQUEST ['s'] ) ? $_REQUEST ['s'] : ''
            ),
            'orderby'       => $orderby,
            'order'         => $order
        );

        $total_polls = Yop_Poll_Model::get_polls_filter_search( $args );
        if( ! isset( $total_polls[0]['total_polls'] ) ) {
            $total_polls[0]['total_polls'] = 0;
        }

        $total_polls_pages = ceil( $total_polls[0]['total_polls'] / $per_page );
        if( intval( $page_no ) > intval( $total_polls_pages ) ) {
            $page_no = 1;
        }

        $args['limit']         = ( $page_no - 1 ) * $per_page . ', ' . $per_page;
        $args['return_fields'] = "
			ID,
			poll_title,
			(SELECT user_nicename
			FROM {$GLOBALS['wpdb']->users} WHERE polls.poll_author = ID) as \"poll_author\",
			poll_total_votes,
			poll_start_date,
			poll_end_date ";

        $data['REQUEST']                 = $_REQUEST;
        $data['orderby']                 = $orderby;
        $data['order']                   = $order;
        $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );
        $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );
        $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );
        $data['title']                   = __yop_poll( 'Yop Poll' );
        $data['polls']                   = Yop_Poll_Model::get_polls_for_view( $args );
        $data['total_items']             = $total_polls[0]['total_polls'];
        $data['current_user']            = $GLOBALS['current_user'];

        $paginate_args           = array(
            'base'      => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%',
            'format'    => '&page_no=%#%',
            'total'     => $total_polls_pages,
            'current'   => max( 1, $page_no ),
            'prev_next' => true,
            'prev_text' => __( '&laquo; Previous' ),
            'next_text' => __( 'Next &raquo;' )
        );
        $_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );

        $data['pagination'] = paginate_links( $paginate_args );

        $this->display( 'view.html', $data );

    }

    private function view_all_polls_operations() {

    }
    public  function print_votes() {
        global $message;
        $time_format="H:i:s";
        $options                     = get_option('yop_poll_options' );
        if($options['date_format']=="UE")
            $date_format="d-m-Y";            else{
            $date_format="m-d-Y";
        }
        $data['date_format']=$date_format.' '.$time_format;
        wp_enqueue_style( 'yop-poll-slider-css', YOP_POLL_URL . 'css/yop-poll-slider.css', array(), YOP_POLL_VERSION );

        wp_enqueue_script( 'yop-poll-add-edit-js', YOP_POLL_URL . 'js/polls/yop-poll-add-edit.js', array(
            'jquery',
            'jquery-ui-sortable',
            'jquery-ui-dialog',
        ), YOP_POLL_VERSION, true );

        wp_enqueue_script( 'yop-poll-slider-js', YOP_POLL_URL . 'js/yop-poll-slider.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );

        wp_enqueue_script( 'yop-poll-results-votes-js', YOP_POLL_URL . '/js/polls/yop-poll-results-votes.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );
        wp_enqueue_style( 'yop-poll-add-edit-css', YOP_POLL_URL . 'css/polls/add-edit.css', array(), YOP_POLL_VERSION );

        $data                    = array();
        if(isset($_POST['question_print']))
            $data['question_print']=true;
        if(isset($_POST['other_print']))
            $data['other_print']=true;
        if(isset($_POST['custom_print']))
            $data['custom_print']=true;

        if(isset($_POST['votes_print']))
            $data['votes_print']=true;
        if(isset($_POST['votes_id']))
            $data['votes_id']=true;
        if(isset($_POST['user_type']))
            $data['user_type']=true;

        if(isset($_POST['track_id']))
            $data['track_id']=true;
        if(isset($_POST['ip_print']))
            $data['ip_print']=true;
        if(isset($_POST['date_print']))
            $data['date_print']=true;
        $data['poll_url']= YOP_POLL_URL;
        $data['poll_id']         = $_REQUEST['id'];
        $current_poll            = new YOP_POLL_Poll_Model( $data['poll_id'] );
        $data['poll_title']      = $current_poll->poll_name;
        $data['per_page']        = ( isset( $_REQUEST ['per_page'] ) && intval( $_REQUEST ['per_page'] ) > 0 ) ? intval( $_REQUEST ['per_page'] ) : 100;
        $data['page_no']         = ( isset( $_REQUEST ['page_no'] ) && intval( $_REQUEST ['page_no'] ) > 0 ) ? intval( $_REQUEST ['page_no'] ) : 1;
        $orderby                 = ( empty ( $GLOBALS['orderby'] ) ) ? 'ip' : $GLOBALS['orderby'];
        $order                   = ( empty ( $GLOBALS['order'] ) ) ? 'desc' : $GLOBALS['order'];
        $data['request']['s_ip'] = ( isset ( $_REQUEST ['s_ip'] ) ? $_REQUEST ['s_ip'] : '' );
        $order_fields            = array(
            'vote_date',
            'user_type',
            'ip'
        );
        $args                    = array(
            'poll_id'       => $_REQUEST['id'],
            'return_fields' => 'COUNT(*) as total_results',
            'search'        => array(
                'fields' => array( 'ip' ),
                'value'  => isset ( $_REQUEST ['s_ip'] ) ? $_REQUEST ['s_ip'] : ''
            ),
            'orderby'       => $orderby,
            'order'         => $order
        );
        $total_results           = self::get_polls_results_filter_search( $args );
        if( ! isset( $total_results[0]['total_results'] ) ) {
            $total_results[0]['total_results'] = 0;
        }
        $data['total_results'] = $total_results[0]['total_results'];
        $total_results_pages   = ceil( $total_results[0]['total_results'] / $data['per_page'] );
        if( intval( $data['page_no'] ) > intval( $total_results_pages ) ) {
            $data['page_no'] = 1;
        }
        $args['limit']                   = ( $data['page_no'] - 1 ) * $data['per_page'] . ', ' . $data['per_page'];
        $args['return_fields']           = "*";
        $data['REQUEST']                 = $_REQUEST;
        $data['orderby']                 = $orderby;
        $data['order']                   = $order;
        $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );
        $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );
        $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );
        $data['results']                 = self::get_polls_results_for_view( $args );
        $data['total_items']             = $total_results[0]['total_results'];
        $data['current_user']            = $GLOBALS['current_user'];
        $data['message']['success']      = $message['success'];
        $data['message']['error']        = $message['error'];
        foreach( $data['results'] as &$result ) {
            $result['votes_details'] = json_decode( $result['result_details'], true );
            $result['vote_answers'] = '';
            foreach( $result['votes_details'] as $question ) {
                $vote_answer = $question['question'] . ": ";
                foreach( $question['answers'] as $answer ) {
                    $vote_answer .= $answer . ", ";
                }
                $result['vote_answers'] .= $vote_answer . ".\n";
                $result['vote_answers']=str_replace(", .",".",$result['vote_answers']);
                if( isset( $question['cf'] ) ) {
                    $custom_fields_details = "";
                    foreach( $question['cf'] as $cf_id ) {
                        $custom_field_log = self::get_custom_field_log_by_id( $cf_id );
                        $custom_field     = self::get_custom_field_by_id( $custom_field_log[0]['custom_field_id'] );
                        if($custom_field_log[0]['custom_field_value'])
                            $custom_fields_details .= $custom_field[0]['custom_field'] . ": " . $custom_field_log[0]['custom_field_value'] . ".\n";

                    }
                    $result['custom_fields'] = $custom_fields_details;
                }
            }
        }
        $paginate_args           = array(
            'base'      => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%',
            'format'    => '&page_no=%#%',
            'total'     => $total_results_pages,
            'current'   => max( 1, $data['page_no'] ),
            'prev_next' => true,
            'prev_text' => __yop_poll( '&laquo; Previous' ),
            'next_text' => __yop_poll( 'Next &raquo;' )
        );
        $_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );
        $data['pagination']      = paginate_links( $paginate_args );
        $data['poll']            = new YOP_POLL_Poll_Model( $data['poll_id'] );
        $data['title']           = "Votes";








        wp_enqueue_script( 'yop-poll-results-votes-js', YOP_POLL_URL . '/js/polls/yop-poll-results-votes.js', array( 'jquery' ), YOP_POLL_VERSION, true );
        $index=1;$data['title']            = __( "Results" );
        $data['poll_id']          = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
        $data['results_order_by'] = ( isset ( $_GET ['results_order_by'] ) ? $_GET ['results_order_by'] : 'id' );
        $data['results_order']    = ( isset ( $_GET ['results_order'] ) ? $_GET ['results_order'] : 'ASC' );
        $data['soav']             = ( isset ( $_GET ['soav'] ) ? $_GET ['soav'] : 'no' );
        $data['a']                = ( isset ( $_GET ['a'] ) ? $_GET ['a'] : 'no' );
        $current_poll             = new YOP_POLL_Poll_Model( $data['poll_id'], $is_view_results = "no", $question_sort = "poll_order", $question_sort_rule = "ASC", $answer_sort = $data['results_order_by'], $answer_sort_rule = $data['results_order'] );
        $data['poll_details']     = array( 'name' => $current_poll->poll_title, 'question' => $current_poll->questions );
        if ( 'yes' == $data['soav'] ){
            $data['display_other_answers_values'] = true;
        }
        else {
            $data['display_other_answers_values'] = false;
        }
        $percentages = array();
        $total_votes = array();
        $i           = 0;
        foreach ( $current_poll->questions as $question ) {
            $total_votes[$i] = 0;
            foreach ( $question->answers as $answer ) {
                $total_votes[$i] += floatval( $answer->votes );
            }
            $i++;
        }
        $i = 0;

        foreach ( $current_poll->questions as $question ) {
            foreach ( $question->answers as $answer ) {
                if ( $answer->votes > 0 ){
                    $answer->status = round( ( $answer->votes * 100 ) / $total_votes[$i], 1 );
                }
                else {
                    $percentages[$i][] = 0;
                    $answer->status    = 0;
                }
            }
            $i++;
        }


        $data['cf_sdate']      = ( isset ( $_GET ['cf_sdate'] ) ? $_GET ['cf_sdate'] : '' );
        $data['cf_edate']      = ( isset ( $_GET ['cf_edate'] ) ? $_GET ['cf_edate'] : '' );
        $data['title']         = "View Votes";
        $data['custom_fields'] = array();

        foreach ( $current_poll->questions as $question ) {
            $data['cf_per_page'] = ( isset ( $_REQUEST ['cf_per_page'] ) ? intval( $_REQUEST ['cf_per_page'] ) : 100 );
            $data['cf_page_no']  = ( isset ( $_REQUEST ['cf_page_no'] ) ? ( int )$_REQUEST ['cf_page_no'] : 1 );

            $poll_custom_fields = self::get_poll_customfields( $data['poll_id'], $question->ID );
            $custom_fields_logs = self::get_poll_customfields_logs( $data['poll_id'], $question->ID, 'vote_id', 'asc', ( $data['cf_page_no'] - 1 ) * $data['cf_per_page'], $data['cf_per_page'], $data['cf_sdate'], $data['cf_edate'] );
            unset( $column_custom_fields_ids );
            foreach ( $poll_custom_fields as $custom_field ) {
                $column_custom_fields_ids [] = $custom_field ['ID'];

            }
            if ( count( $custom_fields_logs ) > 0 ){
                foreach ( $custom_fields_logs as &$logs ) {
                    foreach ( $column_custom_fields_ids as $custom_field_id ) {
                        $vote_log_values = array();
                        $vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
                        if ( count( $vote_logs ) > 0 ){
                            foreach ( $vote_logs as $vote_log ) {
                                $temp                        = explode( '<#!->', $vote_log );
                                $vote_log_values [$temp [1]] = stripslashes( $temp [0] );
                            }
                        }
                    }
                    $custom_fields_logs_details[] = array( 'vote_id' => $logs['vote_id'], "tr_id" => $logs['tr_id'], "vote_date" => $logs['vote_date'], "custom_fields_value" => $vote_log_values, 'column_custom_fields_ids' => $column_custom_fields_ids, );
                }

            }
            $data['total_custom_fields_logs'] = self::get_poll_total_customfields_logs( $data['poll_id'], $question->ID, $data['cf_sdate'], $data['cf_edate'] );
            $data['total_custom_fields_logs_pages'] = ceil( $data['total_custom_fields_logs'] / $data['cf_per_page'] );
            $data['column_custom_fields_ids']       = array();

            if ( intval( $data['cf_page_no'] ) > intval( $data['total_custom_fields_logs_pages'] ) ){
                $data['cf_page_no'] = 1;
            }
            $data['cf_args']           = array( 'base' => remove_query_arg( 'cf_page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&cf_page_no=%#%', 'total' => $data['total_custom_fields_logs_pages'], 'current' => max( 1, $data['cf_page_no'] ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
            $data['cf_pagination']     = paginate_links( $data['cf_args'] );
            $chart_answer[$index][0][]="Answer";$i=1;
            $chart_answer[$index][0][]="Votes";
            foreach ( $question->answers as $answer ) {
                if(($answer->type=="other"&& $data['display_other_answers_values'] ==1)||$answer->type!="other"){
                    $chart_answer[$index][$i][0]=$answer->answer;
                    $chart_answer[$index][$i][1]=(int)$answer->votes;
                    $i++;
                }
            }


            $question_detail[]         = array( 'other_answer' => $question->other_answers_label, 'name' => $question->question, 'answers' => $question->answers, 'custom_fields' => self::get_poll_customfields( $data['poll_id'], $question->ID ), 'custom_fields_logs_details' => isset($custom_fields_logs_details)?$custom_fields_logs_details:'', 'q_id' => $question->ID, 'total_custom_fields_logs' => $data['total_custom_fields_logs'], 'cf_pagination' => $data['cf_pagination'] );
            $data['questions_details'] = $question_detail;

            unset( $custom_fields_logs_details );
            unset( $column_custom_fields_ids );
            $index++;
        }
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_answer',  $chart_answer );

        $data['total_logs_other_answers'] = 0;
        foreach ( $current_poll->questions as $question ) {
            foreach ( $question->answers as $other_answer ) {
                if ( $other_answer->type == 'other' ){
                    $data['total_logs_other_answers']++;
                }
            }
        }


        $this->display( 'results_print.html', $data );

    }


    private function export_answers() {
        $data['title']            = __( "View Votes" );
        $data['poll_id']          = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
        $data['results_order_by'] = ( isset ( $_GET ['results_order_by'] ) ? $_GET ['results_order_by'] : 'id' );
        $data['results_order']    = ( isset ( $_GET ['results_order'] ) ? $_GET ['results_order'] : 'ASC' );
        $data['soav']             = ( isset ( $_GET ['soav'] ) ? $_GET ['soav'] : 'no' );
        $data['a']                = ( isset ( $_GET ['a'] ) ? $_GET ['a'] : 'no' );
        $current_poll             = new YOP_POLL_Poll_Model( $data['poll_id'], $is_view_results = "no", $question_sort = "poll_order", $question_sort_rule = "ASC", $answer_sort = $data['results_order_by'], $answer_sort_rule = $data['results_order'] );
        $data['poll_details']     = array(
            'name'     => $current_poll->poll_title,
            'question' => $current_poll->questions
        );
        if( 'yes' == $data['soav'] ) {
            $data['display_other_answers_values'] = true;
        }
        else {
            $data['display_other_answers_values'] = false;
        }
        $percentages = array();
        $total_votes = array();
        $i           = 0;
        foreach( $current_poll->questions as $question ) {
            $total_votes[$i] = 0;
            foreach( $question->answers as $answer ) {
                $total_votes[$i] += floatval( $answer->votes );
            }
            $i ++;
        }
        $i = 0;

        foreach( $current_poll->questions as &$question ) {
            foreach( $question->answers as &$answer ) {
                if( $answer->votes > 0 ) {
                    $answer->percentage = round( ( $answer->votes * 100 ) / $total_votes[$i], 1 );
                }
                else {
                    $percentages[$i][] = 0;
                    $answer->percenage = 0;
                }
            }
            $i ++;
        }

        $data['cf_sdate']      = ( isset ( $_GET ['cf_sdate'] ) ? $_GET ['cf_sdate'] : '' );
        $data['cf_edate']      = ( isset ( $_GET ['cf_edate'] ) ? $_GET ['cf_edate'] : '' );
        $data['custom_fields'] = array();
        foreach( $current_poll->questions as $question ) {
            $question_detail[]         = array( 'answers' => $question->answers );
            $data['questions_details'] = $question_detail;
        }

        if( isset ( $_REQUEST ['export'] ) ) {
            global $wpdb;
            $csv_file_name       = 'answers.' . date( 'YmdHis' ) . '.csv';
            $csv_header_array    = array( __( '#', 'yop_poll' ) );
            $csv_header_array [] = 'Answer';
            $csv_header_array [] = 'Votes';
            $csv_header_array [] = 'Percentages';
            header( "Content-Type: text/csv" );
            header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
            header( "Content-Transfer-Encoding: binary\n" );
            header( 'Content-Disposition: attachment; filename="' . $csv_file_name . '"' );
            ob_start();
            $f = fopen( 'php://output', 'w' ) or show_error( __( "Can't open php://output!", 'yop_poll' ) );
            $n = 0;
            if( isset ( $csv_header_array ) ) {
                if( ! fputcsv( $f, $csv_header_array ) ) {
                    _e( "Can't write header!", 'yop_poll' );
                }
            }
            foreach( $current_poll->questions as $question ) {
                $index = 1;
                if( $question->ID == $_REQUEST['q_id'] ) {
                    foreach( $question->answers as $answer ) {
                        if( $answer->type == "other" && $_REQUEST['soav'] == "yes" ) {
                            $column_answer_values   = array( $index );
                            $column_answer_values[] = $answer->answer;
                            $column_answer_values[] = $answer->votes;
                            $column_answer_values[] = $answer->percentage;
                            if( ! fputcsv( $f, $column_answer_values ) ) {
                                _e( "Can't write record!", 'yop_poll' );
                            }
                            $index ++;
                        }
                        else {
                            if( $answer->type != "other" ) {
                                $column_answer_values   = array( $index );
                                $column_answer_values[] = $answer->answer;
                                $column_answer_values[] = $answer->votes;
                                $column_answer_values[] = $answer->percentage;
                                if( ! fputcsv( $f, $column_answer_values ) ) {
                                    _e( "Can't write record!", 'yop_poll' );
                                }
                                $index ++;
                            }
                        }
                    }
                }
            }
            fclose( $f ) or show_error( __( "Can't close php://output!", 'yop_poll' ) );
            $csvStr = ob_get_contents();
            ob_end_clean();
            echo $csvStr;
            exit ();
        }
    }

    private function export_custom_field() {
        $data['poll_id']                        = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
        $data['soav']                           = ( isset ( $_GET ['soav'] ) ? $_GET ['soav'] : 'no' );
        $data['a']                              = ( isset ( $_GET ['a'] ) ? $_GET ['a'] : 'no' );
        $current_poll                           = new YOP_POLL_Poll_Model( $data['poll_id'], $is_view_results = "no", $question_sort = "poll_order", $question_sort_rule = "ASC", $answer_sort = $data['results_order_by'], $answer_sort_rule = $data['results_order'] );
        $data['poll_details']                   = array(
            'name'     => $current_poll->poll_title,
            'question' => $current_poll->questions
        );
        $data['q_id']                           = ( isset ( $_GET ['q_id'] ) ? intval( $_GET ['q_id'] ) : 100 );
        $data['total_custom_fields_logs']       = self::get_poll_total_customfields_logs( $data['poll_id'], $data['cf_sdate'], $data['cf_edate'] );
        $data['total_custom_fields_logs_pages'] = ceil( $data['total_custom_fields_logs'] / $data['cf_per_page'] );
        if( intval( $data['cf_page_no'] ) > intval( $data['total_custom_fields_logs_pages'] ) ) {
            $data['cf_page_no'] = 1;
        }
        $data['custom_fields'] = array();
        $data['cf_per_page']   = ( isset ( $_GET ['cf_per_page'] ) ? intval( $_GET ['cf_per_page'] ) : 100 );
        $data['cf_page_no']    = ( isset ( $_REQUEST ['cf_page_no'] ) ? ( int )$_REQUEST ['cf_page_no'] : 1 );
        $poll_custom_fields    = self::get_poll_customfields( $data['poll_id'], $data['q_id'] );
        if( $_REQUEST ['export'] != "all" ) {
            $custom_fields_logs = self::get_poll_customfields_logs( $data['poll_id'], $data['q_id'], 'vote_id', 'asc', ( $data['cf_page_no'] - 1 ) * $data['cf_per_page'], $data['cf_per_page'], $data['cf_sdate'], $data['cf_edate'] );
        }
        else {
            $custom_fields_logs = self::get_poll_customfields_logs( $data['poll_id'], $data['q_id'], 'vote_id', 'asc' );
        }
        $data['custom_fields_number'] = count( $poll_custom_fields );
        if( isset ( $_REQUEST ['export'] ) ) {
            global $wpdb;
            $csv_file_name    = 'custom_fields_export.' . date( 'YmdHis' ) . '.csv';
            $csv_header_array = array( __( '#', 'yop_poll' ) );
            foreach( $poll_custom_fields as $custom_field ) {
                $column_custom_fields_ids [] = $custom_field ['ID'];
                $csv_header_array []         = ucfirst( $custom_field ['custom_field'] );
            }
            $csv_header_array [] = __( 'Vote Date', 'yop_poll' );
            header( "Content-Type: text/csv" );
            header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
            header( "Content-Transfer-Encoding: binary\n" );
            header( 'Content-Disposition: attachment; filename="' . $csv_file_name . '"' );
            ob_start();
            $f = fopen( 'php://output', 'w' ) or show_error( __( "Can't open php://output!", 'yop_poll' ) );
            $n = 0;
            if( isset ( $csv_header_array ) ) {
                if( ! fputcsv( $f, $csv_header_array ) ) {
                    _e( "Can't write header!", 'yop_poll' );
                }
            }
            if( count( $custom_fields_logs ) > 0 ) {
                $index = 1;
                foreach( $custom_fields_logs as $logs ) {
                    $column_custom_fields_values = array( $index );
                    foreach( $column_custom_fields_ids as $custom_field_id ) {
                        $vote_log_values = array();
                        $vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
                        if( count( $vote_logs ) > 0 ) {
                            foreach( $vote_logs as $vote_log ) {
                                $temp                        = explode( '<#!->', $vote_log );
                                $vote_log_values [$temp [1]] = stripslashes( $temp [0] );
                            }
                        }
                        $column_custom_fields_values [] = isset ( $vote_log_values [$custom_field_id] ) ? $vote_log_values [$custom_field_id] : '';
                    }
                    $column_custom_fields_values [] = stripslashes( $logs ['vote_date'] );
                    if( ! fputcsv( $f, $column_custom_fields_values ) ) {
                        _e( "Can't write record!", 'yop_poll' );
                    }
                    $index ++;
                }
            }
            fclose( $f ) or show_error( __( "Can't close php://output!", 'yop_poll' ) );
            $csvStr = ob_get_contents();
            ob_end_clean();
            echo $csvStr;
            exit ();
        }
    }

    private function view_add_edit( $action_type = 'add' ) {
        $poll_id             = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
        $data                = array();
        $data['action_type'] = $action_type;
        $data['title']       = __yop_poll( "Add New Poll" );
        $data['templates']   = YOP_POLL_MODEL::get_yop_poll_templates_search( 'id', 'asc' );
        $data['poll_url']=YOP_POLL_URL;
        $n                   = count( $data['templates'] );
        $time_format="H:i:s";
        $options                     = get_option('yop_poll_options' );
        if($options['date_format']=="UE")
            $date_format="d-m-Y";            else{
            $date_format="m-d-Y";
        }
        $data['date_format']=$date_format.' '.$time_format;
        $data ['format']=$date_format." ".$time_format;
        for( $i = 0; $i < $n; $i ++ ) {
            $data['templates'][$i]['preview'] = YOP_POLL_Poll_Model::return_template_preview_html( $data['templates'][$i]['id'] );
        }
        $current =new YOP_POLL_Poll_Model( $poll_id );
        if('add' == $action_type){
            $current->email_notifications_from_name=$options['email_notifications_from_name'];
            $current->email_notifications_from_email=$options['email_notifications_from_email'];
            $current->email_notifications_recipients=$options['email_notifications_recipients'];
            $current->email_notifications_subject=$options['email_notifications_subject'];
            $current->email_notifications_body=$options['email_notifications_body'];
            $current->vote_permisions_wordpress_label=$options['vote_permisions_wordpress_label'];
            $current->vote_permisions_anonymous_label=$options['vote_permisions_anonymous_label'];
            $current->vote_button_label=$options['vote_button_label'];
        }
        $data['current_poll'] = $current;
        if( 'edit' == $action_type ) {
            if(empty($current->vote_button_label)){

                $current->vote_button_label=$options['vote_button_label'];


            }
            if(empty(   $current->vote_permisions_wordpress_label)){
                $current->vote_permisions_wordpress_label=$options['vote_permisions_wordpress_label'];
            }
            if(empty(  $current->vote_permisions_anonymous_label)){
                $current->vote_permisions_anonymous_label=$options['vote_permisions_anonymous_label'];
            }

            $data['title'] = __yop_poll( 'Edit Poll' );
        }

        if( $data['current_poll']->get( 'template' ) ) {
            //$data['template_preview'] = Yop_Poll_Model::return_template_preview_html( $data['current_poll']['template'] );
        }
        if( $data['current_poll']->get( 'widget_template' ) ) {
            //$data['widget_template_preview'] = Yop_Poll_Model::return_template_preview_html( $data['current_poll']['widget_template'] );
        }
        $data['user_is_pro']     = true;
        $yop_poll_add_new_config = array(
            'ajax'                            => array(
                'url'               => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
                'action'            => 'yop_poll_edit_add_new_poll',
                'beforeSendMessage' => __yop_poll( 'Please wait a moment while we process your request...' ),
                'errorMessage'      => __yop_poll( 'An error has occured...' )
            ),
            'text_answer'                     => __yop_poll( 'Answer' ),
            'text_customfield'                => __yop_poll( 'Custom Text Field' ),
            'text_requiered_customfield'      => __yop_poll( 'Required' ),
            'text_remove_answer'              => __yop_poll( 'Delete' ),
            'text_remove_customfield'         => __yop_poll( 'Delete' ),
            'text_customize_answer'           => __yop_poll( 'More Options' ),
            'text_change_votes_number_answer' => __yop_poll( 'Change Number Of Votes' ),
            'text_change_votes_number_poll'   => __yop_poll( 'Change Number Of Total Votes' ),
            'text_change_answers_number_poll' => __yop_poll( 'Change Number Of Total Answers' ),
            'plugin_url'                      => YOP_POLL_URL,
            'default_number_of_answers'       => 1,
            'default_number_of_customfields'  => 1,
            'text_is_default_answer'          => __yop_poll( 'Make this the default answer' ) . '<br><small>(' . __yop_poll( 'if "yes", answer will be autoselected when poll is displayed' ) . ')</small>',
            'text_poll_bar_style'             => array(
                'use_template_bar_label'            => __yop_poll( 'Use Template Result Bar' ),
                'use_template_bar_yes_label'        => __yop_poll( 'Yes' ),
                'use_template_bar_no_label'         => __yop_poll( 'No' ),
                'poll_bar_style_label'              => __yop_poll( 'Yop Poll Bar Style' ),
                'poll_bar_preview_label'            => __yop_poll( 'Yop Poll Bar Preview' ),
                'poll_bar_style_background_label'   => __yop_poll( 'Background Color' ),
                'poll_bar_style_height_label'       => __yop_poll( 'Height' ),
                'poll_bar_style_border_color_label' => __yop_poll( 'Border Color' ),
                'poll_bar_style_border_width_label' => __yop_poll( 'Border Width' ),
                'poll_bar_style_border_style_label' => __yop_poll( 'Border Style' )
            ),
            'poll_bar_default_options'        => array(
                'use_template_bar' => isset ( $default_options ['use_template_bar'] ) ? $default_options ['use_template_bar'] : 'yes',
                'height'           => isset ( $default_options ['bar_height'] ) ? $default_options ['bar_height'] : 10,
                'background_color' => isset ( $default_options ['bar_background'] ) ? $default_options ['bar_background'] : 'd8e1eb',
                'border'           => isset ( $default_options ['bar_border_style'] ) ? $default_options ['bar_border_style'] : 'solid',
                'border_width'     => isset ( $default_options ['bar_border_width'] ) ? $default_options ['bar_border_width'] : 1,
                'border_color'     => isset ( $default_options ['bar_border_color'] ) ? $default_options ['bar_border_color'] : 'c8c8c8'
            )
        );

        wp_localize_script( 'yop-poll-add-edit-js', 'yop_poll_add_new_config', $yop_poll_add_new_config );

        $poll_options                = get_option( 'yop_poll_options', array() );
        $data['user_interface_type'] = $poll_options['user_interface_type'];
        if( isset( $poll_options['user_interface_type'] ) && $poll_options['user_interface_type'] == 'beginner' ) {
            $this->display( 'add_edit_beginner.html', $data );
        }
        else {
            $this->display( 'add_edit_advanced.html', $data );
        }
    }

    public function do_add_edit() {
        if( 'add' == $_POST['action_type'] ) {
            if( ( ! current_user_can( 'edit_own_yop_polls' ) ) && ( ! current_user_can( 'edit_yop_polls' ) ) ) {
                wp_die( __yop_poll( 'You are not allowed to edit this item.' ) );
            }
        }
        else {
            if( 'edit' == $_POST['action_type'] ) {
                $poll_details = new YOP_POLL_Poll_Model( $_POST['poll_id'] );
                if( ( ! current_user_can( 'edit_own_yop_polls' ) || $poll_details->poll_author != $GLOBALS['current_user']->ID ) && ( ! current_user_can( 'edit_yop_polls' ) ) ) {
                    wp_die( __yop_poll( 'You are not allowed to edit this item.' ) );
                }
            }
            else {
                wp_die( __yop_poll( 'You are not allowed to access this request.' ) );
            }
        }

        Yop_Poll_Model::save_poll();
    }

    private function delete_poll() {
        // if(check_admin_referer('yop-poll-view-action','yop-poll-view-name')){
        if( current_user_can( 'delete_own_yop_polls' ) ) {
            if( isset( $_REQUEST['id'] ) ) {
                self:: delete_poll_from_db( $_REQUEST['id'] );
                $archive                 = get_option( 'yop_poll_archive_order', array() );
                if(($key = array_search($_REQUEST['id'], $archive)) !== false) {
                    unset($archive[$key]);
                    $archive=array_values($archive);
                    update_option( 'yop_poll_archive_order', $archive );

                }
            }
            else {
                if( isset ( $_REQUEST['yoppollcheck'] ) ) {
                    $polls = ( array )$_REQUEST ['yoppollcheck'];
                    foreach( $polls as $poll_id ) {
                        $poll_id = ( int )$poll_id;
                        self::delete_poll_from_db( $poll_id );
                        $archive                 = get_option( 'yop_poll_archive_order', array() );
                        if(($key = array_search($poll_id, $archive)) !== false) {
                            unset($archive[$key]);
                            $archive=array_values($archive);
                            update_option( 'yop_poll_archive_order', $archive );
                        }
                    }
                }
            }
        }
        else {
            wp_die( __yop_poll( 'You are not allowed to edit this item.' ) );
        }
        /*}
        else
        wp_die( __yop_poll( 'You are not allowed to edit this item.' ) );
        }*/
    }

    private static function delete_poll_from_db( $id_poll ) {
        $poll = new YOP_POLL_Poll_Model( $id_poll );
        $poll->delete();
    }

    private function clone_poll() {

        if( check_admin_referer( 'yop-poll-view-action', 'yop-poll-view-name' ) ) {
            if( current_user_can( 'delete_own_yop_polls' ) ) {
                if( isset( $_REQUEST['id'] ) ) {
                    self:: clone_poll_from_db( $_REQUEST['id'] );
                }
                else {
                    if( isset ( $_REQUEST['yoppollcheck'] ) ) {
                        $polls = ( array )$_REQUEST ['yoppollcheck'];
                        foreach( $polls as $poll_id ) {
                            $poll_id = ( int )$poll_id;
                            self::clone_poll_from_db( $poll_id );
                        }
                    }
                }
            }
            else {
                wp_die( __yop_poll( 'You are not allowed to clone this item.' ) );
            }
        }
        else {
            wp_die( __yop_poll( 'You are not allowed to edit this item.' ) );
        }
    }

    private static function clone_poll_from_db( $poll_id ) {
        global $wpdb, $current_user;
        $poll_details = self::get_poll_from_database_by_id( $poll_id );
        $clone_number = self::count_poll_from_database_like_name( $poll_details['poll_name'] . ' - clone' );
        $meta                             = get_yop_poll_meta( $poll_id, 'options', true );
        $current_poll   = new YOP_POLL_Poll_Model( $poll_id );
        $current_poll->poll_total_votes=0;
        $poll_clone     =$current_poll;
        $poll_clone->ID = 0;
        if( $poll_details ) {
            $poll                   = array(
                'title'       => $poll_details['poll_title'] . ' - clone' . ( 0 == $clone_number ? '' : $clone_number ),
                'start_date'  => $poll_details['poll_start_date'],
                'end_date'    => $poll_details['poll_end_date'],
                'status'      => $poll_details['poll_status'],
                'poll_date'   => $poll_details['poll_date'],
                'poll_total_votes' => 0,
                'author'      => $poll_details['poll_author'],
                'name'        => $poll_details['poll_name'] . ' - clone' . ( 0 == $clone_number ? '' : $clone_number ),
                'type'        => $poll_details['poll_type'],
                'modified'    => $poll_details['poll_modified']
            );
            $poll_clone->poll_name  = $poll['name'];
            $poll_clone->poll_title = $poll['title'];

            foreach( $poll_clone->questions as $question ) {
                $question->ID = 0;
                foreach( $question->answers as $answer ) {
                    $answer->ID = 0;
                    $answer->votes = 0;
                }
                if(isset($question->custom_fields))
                foreach($question->custom_fields as $custom){
                    $custom->ID=0;
                    $custom->poll_id=0;
                    $custom->question_id=0;
                }
            }
            $is_clone=true;
            $id_poll=$poll_clone->save($is_clone);
            self::save_poll_order($poll_clone,$poll_clone->poll_archive_order);
            update_yop_poll_meta( $id_poll, 'options', $meta );

        }
    }

    private static function save_poll_order( $poll, $poll_order ) {
        $poll_archive_order = get_option( 'yop_poll_archive_order', array() );
        if( $poll_archive_order == "" ) {
            $poll_archive_order = array();
        }
        if( isset( $poll->show_poll_in_archive ) ) {
            if(  $poll->show_poll_in_archive  == 'yes'  ) {
                if( isset( $poll_order ) && is_numeric( trim( $poll_order ) ) ) {
                    if( trim( $poll_order ) <= 0 ) {
                        $poll_order = 1;
                    }
                    $key = array_search( $poll->ID, $poll_archive_order );
                    if( $key !== false ) {
                        unset( $poll_archive_order[$key] );
                    }
                    if( $poll_order > count( $poll_archive_order ) ) {
                        array_push( $poll_archive_order, $poll->ID );
                    }
                    else {
                        array_splice( $poll_archive_order, trim( $poll_order ) - 1, 0, array( $poll->ID ) );
                    }

                }

            }
            else {
                $key = array_search( $poll->ID, $poll_archive_order );

                if( $key !== null ) {
                    unset( $poll_archive_order[$key] );
                }
            }
        }

        $poll_archive_order = array_values( $poll_archive_order );
        update_option( 'yop_poll_archive_order', $poll_archive_order );
    }
    public static function get_poll_total_customfields_logs( $poll_id, $quest_id, $sdate = '', $edate = '' ) {
        global $wpdb;
        $sdatesql = '';
        $edatesql = '';
        if( $sdate != '' ) {
            $sdatesql = $wpdb->prepare( ' AND vote_date >= %s ', $sdate . ' 00:00:00 ' );
        }
        if( $edate != '' ) {
            $edatesql = $wpdb->prepare( ' AND vote_date <= %s ', $edate . ' 23:59:59 ' );
        }
        $wpdb->query( $wpdb->prepare( "
					SELECT count(*)
					FROM " . $wpdb->yop_poll_votes_custom_fields . "
					WHERE poll_id = %d AND question_id = %d " . $sdatesql . $edatesql . "GROUP BY vote_id
					", $poll_id, $quest_id ) );

        return $wpdb->get_var( 'SELECT FOUND_ROWS()' );
    }

    private static function get_poll_from_database_by_id( $poll_id ) {
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_polls . "
					WHERE ID = %d
					LIMIT 0,1
					", $poll_id ), ARRAY_A );

        return $result;
    }

    private static function count_poll_from_database_like_name( $poll_name ) {
        global $wpdb;
        $result = $wpdb->get_var( $wpdb->prepare( "
					SELECT count(*)
					FROM " . $wpdb->yop_polls . "
					WHERE poll_name like %s
					", $poll_name . '%' ) );

        return $result;
    }


    public static function get_poll_customfields( $poll_id, $question_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_custom_fields . "
					WHERE poll_id = %d AND question_id=%d
					", $poll_id, $question_id ), ARRAY_A );

        return $result;
    }

    public static function get_poll_customfields_logs( $poll_id, $quest_id = 0, $orderby = 'vote_date', $order = 'desc', $offset = 0, $per_page = 99999999, $sdate = '', $edate = '' ) {
        global $wpdb;
        $sdatesql = '';
        $edatesql = '';
        if( $sdate != '' ) {
            $sdatesql = $wpdb->prepare( ' AND vote_date >= %s ', $sdate . ' 00:00:00 ' );
        }
        if( $edate != '' ) {
            $edatesql = $wpdb->prepare( ' AND vote_date <= %s ', $edate . ' 23:59:59 ' );
        }
        $result = $wpdb->get_results( $wpdb->prepare( "
					SELECT group_concat( CONCAT( `custom_field_value`, '<#!->', `custom_field_id` ) SEPARATOR '<#!,>' ) as vote_log, vote_id, vote_date, user_id, id, tr_id
					FROM " . $wpdb->yop_poll_votes_custom_fields . "
					WHERE poll_id = %d  AND question_id = %d GROUP BY vote_id
					ORDER BY " . esc_attr( $orderby ) . " " . esc_attr( $order ) . "
					LIMIT %d, %d
					", $poll_id, $quest_id, $offset, $per_page ), ARRAY_A );
        return $result;
    }

    public function get_poll_id_by_start_date_end_date( $start_date = "2014-02-03", $end_date = "01-01-2038 23:59:59" ) {
        $start_date .= " 00:00:00";
        $end_date .= " 23:59:59";
        global $wpdb;

        return $wpdb->get_results( $wpdb->prepare( "
					", $start_date, $end_date . '%' ) );
    }
    
    private function view_results_votes() {
        global $message;
        $time_format="H:i:s";
        $options = get_option('yop_poll_options' );
        if( $options['date_format'] == "UE" )
            $date_format="d-m-Y";            
        else{
            $date_format="m-d-Y";
        }
        $data['date_format']=$date_format.' '.$time_format;
        wp_enqueue_style( 'yop-poll-slider-css', YOP_POLL_URL . 'css/yop-poll-slider.css', array(), YOP_POLL_VERSION );

        wp_enqueue_script( 'yop-poll-add-edit-js', YOP_POLL_URL . 'js/polls/yop-poll-add-edit.js', array(
            'jquery',
            'jquery-ui-sortable',
            'jquery-ui-dialog',
        ), YOP_POLL_VERSION, true );

        wp_enqueue_script( 'yop-poll-slider-js', YOP_POLL_URL . 'js/yop-poll-slider.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );

        wp_enqueue_script( 'yop-poll-results-votes-js', YOP_POLL_URL . '/js/polls/yop-poll-results-votes.js', array(
            'jquery',
            'jquery-ui-dialog'
        ), YOP_POLL_VERSION, true );
        wp_enqueue_style( 'yop-poll-add-edit-css', YOP_POLL_URL . 'css/polls/add-edit.css', array(), YOP_POLL_VERSION );

        $data                    = array();
        $data['poll_url']= YOP_POLL_URL;
        $data['poll_id']         = $_REQUEST['id'];
        $current_poll            = new YOP_POLL_Poll_Model( $data['poll_id'] );
        $data['poll_title']      = $current_poll->poll_name;
        $data['per_page']        = ( isset( $_REQUEST ['per_page'] ) && intval( $_REQUEST ['per_page'] ) > 0 ) ? intval( $_REQUEST ['per_page'] ) : 100;
        $data['page_no']         = ( isset( $_REQUEST ['page_no'] ) && intval( $_REQUEST ['page_no'] ) > 0 ) ? intval( $_REQUEST ['page_no'] ) : 1;
        $orderby                 = ( empty ( $GLOBALS['orderby'] ) ) ? 'vote_date' : $GLOBALS['orderby'];
        $order                   = ( empty ( $GLOBALS['order'] ) ) ? 'desc' : $GLOBALS['order'];
        $data['request']['s_ip'] = ( isset ( $_REQUEST ['s_ip'] ) ? $_REQUEST ['s_ip'] : '' );
        $order_fields            = array(
            'vote_date',
            'user_type',
            'ip'
        );
        $args                    = array(
            'poll_id'       => $_REQUEST['id'],
            'return_fields' => 'COUNT(*) as total_results',
            'search'        => array(
                'fields' => array( isset( $_REQUEST['searchbox'] ) ? $_REQUEST['searchbox']: ''   ),
                'value'  => isset ( $_REQUEST ['s_ip'] ) ? $_REQUEST ['s_ip'] : ''
            ),
            'orderby'       => $orderby,
            'order'         => $order
        );
        $total_results           = self::get_polls_results_filter_search( $args );
        if( ! isset( $total_results[0]['total_results'] ) ) {
            $total_results[0]['total_results'] = 0;
        }
        $data['total_results'] = $total_results[0]['total_results'];
        $total_results_pages   = ceil( $total_results[0]['total_results'] / $data['per_page'] );
        if( intval( $data['page_no'] ) > intval( $total_results_pages ) ) {
            $data['page_no'] = 1;
        }
        $args['limit']                   = ( $data['page_no'] - 1 ) * $data['per_page'] . ', ' . $data['per_page'];
        $args['return_fields']           = "*";
        $data['REQUEST']                 = $_REQUEST;
        $data['orderby']                 = $orderby;
        $data['order']                   = $order;
        $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );
        $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );
        $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );
        $data['results']                 = self::get_polls_results_for_view( $args );
        $data['total_items']             = $total_results[0]['total_results'];
        $data['current_user']            = $GLOBALS['current_user'];
        $data['message']['success']      = $message['success'];
        $data['message']['error']        = $message['error'];
        $data['vote_answers']            = '';
        foreach( $data['results'] as &$result ) {
            $result['votes_details'] = json_decode( $result['result_details'], true );
            $result['vote_answers'] = '';
            foreach( $result['votes_details'] as $question ) {
                $vote_answer = $question['question'] . ": ";
                foreach( $question['answers'] as $answer ) {
                    $vote_answer .= $answer . ", ";
                }
                $result['vote_answers'] .= $vote_answer . ".\n";
                $result['vote_answers']=str_replace(", .",".",$result['vote_answers']);
                if( isset( $question['cf'] ) ) {
                    $custom_fields_details = "";
                    foreach( $question['cf'] as $cf_id ) {
                        $custom_field_log = self::get_custom_field_log_by_id( $cf_id );
                        $custom_field     = self::get_custom_field_by_id( $custom_field_log[0]['custom_field_id'] );
                        if($custom_field_log[0]['custom_field_value'])
                            $custom_fields_details .= $custom_field[0]['custom_field'] . ": " . $custom_field_log[0]['custom_field_value'] . ".\n";

                    }
                    $result['custom_fields'] = $custom_fields_details;
                }
            }
        }
        $paginate_args           = array(
            'base'      => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%',
            'format'    => '&page_no=%#%',
            'total'     => $total_results_pages,
            'current'   => max( 1, $data['page_no'] ),
            'prev_next' => true,
            'prev_text' => __yop_poll( '&laquo; Previous' ),
            'next_text' => __yop_poll( 'Next &raquo;' )
        );
        $_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );
        $data['pagination']      = paginate_links( $paginate_args );
        $data['poll']            = new YOP_POLL_Poll_Model( $data['poll_id'] );
        $data['title']           = "Votes";


        wp_enqueue_script( 'yop-poll-results-votes-js', YOP_POLL_URL . '/js/polls/yop-poll-results-votes.js', array( 'jquery' ), YOP_POLL_VERSION, true );
        $index=1;
        $data['title']            = __( "Results" );
        $data['poll_id']          = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
        $data['results_order_by'] = ( isset ( $_GET ['results_order_by'] ) ? $_GET ['results_order_by'] : 'id' );
        $data['results_order']    = ( isset ( $_GET ['results_order'] ) ? $_GET ['results_order'] : 'ASC' );
        $data['soav']             = ( isset ( $_GET ['soav'] ) ? $_GET ['soav'] : 'no' );
        $data['a']                = ( isset ( $_GET ['a'] ) ? $_GET ['a'] : 'no' );
        $current_poll             = new YOP_POLL_Poll_Model( $data['poll_id'], $is_view_results = "no", $question_sort = "poll_order", $question_sort_rule = "ASC", $answer_sort = $data['results_order_by'], $answer_sort_rule = $data['results_order'] );
        $data['poll_details']     = array( 'name' => $current_poll->poll_title, 'question' => $current_poll->questions );
        if ( 'yes' == $data['soav'] ){
            $data['display_other_answers_values'] = true;
        }
        else {
            $data['display_other_answers_values'] = false;
        }
        $percentages = array();
        $total_votes = array();
        $i           = 0;
        foreach ( $current_poll->questions as $question ) {
            $total_votes[$i] = 0;
            foreach ( $question->answers as $answer ) {
                $total_votes[$i] += floatval( $answer->votes );
            }
            $i++;
        }
        $i = 0;

        foreach ( $current_poll->questions as $question ) {
            foreach ( $question->answers as $answer ) {
                if ( $answer->votes > 0 ){
                    $answer->status = round( ( $answer->votes * 100 ) / $total_votes[$i], 1 );
                }
                else {
                    $percentages[$i][] = 0;
                    $answer->status    = 0;
                }
            }
            $i++;
        }


        $data['cf_sdate']      = ( isset ( $_GET ['cf_sdate'] ) ? $_GET ['cf_sdate'] : '' );
        $data['cf_edate']      = ( isset ( $_GET ['cf_edate'] ) ? $_GET ['cf_edate'] : '' );
        $data['title']         = "View Votes";
        $data['custom_fields'] = array();

        foreach ( $current_poll->questions as $question ) {
            $data['cf_per_page'] = ( isset ( $_REQUEST ['cf_per_page'] ) ? intval( $_REQUEST ['cf_per_page'] ) : 100 );
            $data['cf_page_no']  = ( isset ( $_REQUEST ['cf_page_no'] ) ? ( int )$_REQUEST ['cf_page_no'] : 1 );

            $poll_custom_fields = self::get_poll_customfields( $data['poll_id'], $question->ID );
            $custom_fields_logs = self::get_poll_customfields_logs( $data['poll_id'], $question->ID, 'vote_id', 'asc', ( $data['cf_page_no'] - 1 ) * $data['cf_per_page'], $data['cf_per_page'], $data['cf_sdate'], $data['cf_edate'] );
            unset( $column_custom_fields_ids );
            foreach ( $poll_custom_fields as $custom_field ) {
                $column_custom_fields_ids [] = $custom_field ['ID'];

            }
            if ( count( $custom_fields_logs ) > 0 ){
                foreach ( $custom_fields_logs as &$logs ) {
                    foreach ( $column_custom_fields_ids as $custom_field_id ) {
                        $vote_log_values = array();
                        $vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
                        if ( count( $vote_logs ) > 0 ){
                            foreach ( $vote_logs as $vote_log ) {
                                $temp                        = explode( '<#!->', $vote_log );
                                $vote_log_values [$temp [1]] = stripslashes( $temp [0] );
                            }
                        }
                    }
                    $custom_fields_logs_details[] = array( 'vote_id' => $logs['vote_id'], "tr_id" => $logs['tr_id'], "vote_date" => $logs['vote_date'], "custom_fields_value" => $vote_log_values, 'column_custom_fields_ids' => $column_custom_fields_ids, );
                }

            }
            $data['total_custom_fields_logs'] = self::get_poll_total_customfields_logs( $data['poll_id'], $question->ID, $data['cf_sdate'], $data['cf_edate'] );
            $data['total_custom_fields_logs_pages'] = ceil( $data['total_custom_fields_logs'] / $data['cf_per_page'] );
            $data['column_custom_fields_ids']       = array();

            if ( intval( $data['cf_page_no'] ) > intval( $data['total_custom_fields_logs_pages'] ) ){
                $data['cf_page_no'] = 1;
            }
            $data['cf_args']           = array( 'base' => remove_query_arg( 'cf_page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&cf_page_no=%#%', 'total' => $data['total_custom_fields_logs_pages'], 'current' => max( 1, $data['cf_page_no'] ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
            $data['cf_pagination']     = paginate_links( $data['cf_args'] );
            $chart_answer[$index][0][]="Answer";$i=1;
            $chart_answer[$index][0][]="Votes";
            foreach ( $question->answers as $answer ) {
                if(($answer->type=="other"&& $data['display_other_answers_values'] ==1)||$answer->type!="other"){
                    $chart_answer[$index][$i][0]=$answer->answer;
                    $chart_answer[$index][$i][1]=(int)$answer->votes;
                    $i++;
                }
            }


            $question_detail[] = array( 'other_answer' => $question->other_answers_label, 'name' => $question->question, 'answers' => $question->answers, 'custom_fields' => self::get_poll_customfields( $data['poll_id'], $question->ID ), 'custom_fields_logs_details' => isset($custom_fields_logs_details)?$custom_fields_logs_details:'', 'q_id' => $question->ID, 'total_custom_fields_logs' => $data['total_custom_fields_logs'], 'cf_pagination' => $data['cf_pagination'] );
            $data['questions_details'] = $question_detail;
            if( isset( $custom_fields_logs_details ) ){
	            unset( $custom_fields_logs_details );
	        }
            unset( $column_custom_fields_ids );
            $index++;
        }
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_answer',  $chart_answer );

        $data['total_logs_other_answers'] = 0;
        foreach ( $current_poll->questions as $question ) {
            foreach ( $question->answers as $other_answer ) {
                if ( $other_answer->type == 'other' ){
                    $data['total_logs_other_answers']++;
                }
            }
        }
        $this->display( 'results_votes.html', $data );

    }


    public function view_poll_results() {
        global $page, $action, $current_user;
        $poll_id          = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
        $results_order_by = ( isset ( $_GET ['results_order_by'] ) ? $_GET ['results_order_by'] : 'id' );
        $results_order    = ( isset ( $_GET ['results_order'] ) ? $_GET ['results_order'] : 'ASC' );
        $soav             = ( isset ( $_GET ['soav'] ) ? $_GET ['soav'] : 'no' );
        require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
        $poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
        if ( ( !$this->current_user_can( 'view_own_polls_results' ) || $poll_author != $current_user->ID ) && ( !$this->current_user_can( 'view_polls_results' ) ) ){
            wp_die( __( 'You are not allowed to view results for this item.', 'yop_poll' ) );
        }
        $poll_details = YOP_POLL_MODEL::get_poll_from_database_by_id( $poll_id );
        if ( 'yes' == $soav ){
            $display_other_answers_values = true;
        }
        else {
            $display_other_answers_values = false;
        }
        $poll_answers      = YOP_POLL_MODEL::get_poll_answers( $poll_id, array( 'default', 'other' ), $results_order_by, $results_order, $display_other_answers_values );
        $poll_other_answer = YOP_POLL_MODEL::get_poll_answers( $poll_id, array( 'other' ) );

        // other-answers
        $oa_per_page                    = ( isset ( $_GET ['oa_per_page'] ) ? intval( $_GET ['oa_per_page'] ) : 100 );
        $oa_page_no                     = ( isset ( $_REQUEST ['oa_page_no'] ) ? ( int )$_REQUEST ['oa_page_no'] : 1 );
        $total_logs_other_answers       = count( YOP_POLL_MODEL::get_other_answers_votes( isset ( $poll_other_answer [0] ['id'] ) ? $poll_other_answer [0] ['id'] : 0 ) );
        $total_logs_other_answers_pages = ceil( $total_logs_other_answers / $oa_per_page );
        if ( intval( $oa_page_no ) > intval( $total_logs_other_answers_pages ) ){
            $oa_page_no = 1;
        }
        $logs_other_answers = YOP_POLL_MODEL::get_other_answers_votes( isset ( $poll_other_answer [0] ['id'] ) ? $poll_other_answer [0] ['id'] : 0, ( $oa_page_no - 1 ) * $oa_per_page, $oa_per_page );

        $oa_args       = array( 'base' => remove_query_arg( 'oa_page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&oa_page_no=%#%', 'total' => $total_logs_other_answers_pages, 'current' => max( 1, $oa_page_no ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
        $oa_pagination = paginate_links( $oa_args );
        // other-answers

        // custom-fields
        $cf_per_page                    = ( isset ( $_GET ['cf_per_page'] ) ? intval( $_GET ['cf_per_page'] ) : 100 );
        $cf_page_no                     = ( isset ( $_REQUEST ['cf_page_no'] ) ? ( int )$_REQUEST ['cf_page_no'] : 1 );
        $cf_sdate                       = ( isset ( $_GET ['cf_sdate'] ) ? $_GET ['cf_sdate'] : '' );
        $cf_edate                       = ( isset ( $_GET ['cf_edate'] ) ? $_GET ['cf_edate'] : '' );
        $poll_custom_fields             = YOP_POLL_MODEL::get_poll_customfields( $poll_id );
        $custom_fields_number           = count( $poll_custom_fields );
        $total_custom_fields_logs       = YOP_POLL_MODEL::get_poll_total_customfields_logs( $poll_id, $cf_sdate, $cf_edate );
        $total_custom_fields_logs_pages = ceil( $total_custom_fields_logs / $cf_per_page );
        if ( intval( $cf_page_no ) > intval( $total_custom_fields_logs_pages ) ){
            $cf_page_no = 1;
        }
        $custom_fields_logs = YOP_POLL_MODEL::get_poll_customfields_logs( $poll_id, 'vote_id', 'asc', ( $cf_page_no - 1 ) * $cf_per_page, $cf_per_page, $cf_sdate, $cf_edate );

        $column_custom_fields_ids = array();
        $cf_args                  = array( 'base' => remove_query_arg( 'cf_page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&cf_page_no=%#%', 'total' => $total_custom_fields_logs_pages, 'current' => max( 1, $cf_page_no ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
        $cf_pagination            = paginate_links( $cf_args );
        // custom-fields
        ?>
        <div class="wrap">
        <div class="icon32 icon32-yop-poll">
            <br>

        </div>
        <h2><?php _e( 'Yop Poll Results', 'yop_poll' ); ?><a class="add-new-h2"
                                                             href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls' ), remove_query_arg( array( 'action', 'id' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) ) ); ?>"><?php _e( 'All Polls', 'yop_poll' ); ?></a>
        </h2>
        <?php
        if ( $poll_details ){
            ?>
            <h3>Name: <?php echo esc_html( stripslashes( $poll_details['name'] ) ) ?></h3>
            <h4>Question: <?php echo esc_html( stripslashes( $poll_details['question'] ) ) ?></h4>
            <form method="get">
                <input type="hidden" name="page" value="yop-polls"/>
                <input type="hidden" name="action" value="results"/>
                <input type="hidden" name="id" value="<?php echo $poll_id; ?>"/>
                <input type="hidden" name="oa_page_no"
                       value="<?php echo $oa_page_no; ?>"/>
                <input type="hidden" name="cf_page_no"
                       value="<?php echo $cf_page_no; ?>"/>
                <input type="hidden" name="oa_per_page"
                       value="<?php echo $oa_per_page; ?>"/>

                <div class="tablenav top">
                    <div class="alignleft actions">
                        <div style="display:inline; float:left; margin:7px;"><?php _e( 'Order By', 'yop_poll' ); ?></div>
                        <select name="results_order_by">
                            <option <?php selected( $results_order_by, 'id' ) ?> value="id"><?php _e( 'Answer ID', 'yop_poll' ); ?></option>
                            <option <?php selected( $results_order_by, 'answer' ) ?> value="answer"><?php _e( 'Answer Value', 'yop_poll' ); ?></option>
                            <option <?php selected( $results_order_by, 'votes' ) ?> value="votes"><?php _e( 'Votes', 'yop_poll' ); ?></option>
                        </select>
                        <select name="results_order">
                            <option <?php selected( $results_order, 'ASC' ) ?> value="ASC"><?php _e( 'ASC', 'yop_poll' ); ?></option>
                            <option <?php selected( $results_order, 'DESC' ) ?> value="DESC"><?php _e( 'DESC', 'yop_poll' ); ?></option>
                        </select>
                        &nbsp;| &nbsp;
                        <input type="checkbox" value="yes" <?php checked( $soav, 'yes' ); ?> name="soav" id="yop-poll-show_other_answers_values"/>
                        <label for="yop-poll-show_other_answers_values"><?php _e( 'Show Other Answers Values', 'yop_poll' ); ?></label>
                        <input type="submit"
                               value="<?php _e( 'Filter', 'yop_poll' ); ?>"
                               class="button-secondary action" id="doaction" name="a">
                    </div>
                    <br class="clear">
                </div>
            </form>
            <table class="wp-list-table widefat fixed" cellspacing="0">
                <thead>
                <tr>
                    <th id="" class="column-answer" style="width: 40%;" scope="col"><?php _e( 'Answer', 'yop_poll' ); ?></th>
                    <th id="" class="column-votes" style="width: 5%;" scope="col"><?php _e( 'Votes', 'yop_poll' ); ?></th>
                    <th id="" class="column-percent" style="width: 5%;" scope="col"><?php _e( 'Percent', 'yop_poll' ); ?></th>
                    <th id="" class="column-bar" style="width: 45%;" scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ( count( $poll_answers ) > 0 ){
                    foreach ( $poll_answers as $answer ) {
                        ?>
                        <tr>
                            <th><?php echo esc_html( stripslashes( $answer['answer'] ) ); ?></th>
                            <td><?php echo esc_html( stripslashes( $answer['votes'] ) ); ?></td>
                            <td><?php echo esc_html( stripslashes( $answer['procentes'] ) ); ?>%</td>
                            <td><span class="yop-poll-admin-result-bar" style="width: <?php echo esc_html( stripslashes( $answer['procentes'] ) ); ?>%;">
										</span></td>
                        </tr>
                    <?php
                    }
                }
                else {
                    ?>
                    <tr>
                        <th colspan="4"><?php _e( 'No answers defined!', 'yop_poll' ); ?></th>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <br> <br>
            <div style="width: 30%; float: left;">
                <h3><?php _e( 'Poll Other Answers', 'yop_poll' ); ?></h3>
                <form method="get">
                    <input type="hidden" name="page" value="yop-polls"/>
                    <input type="hidden" name="action" value="results"/>
                    <input type="hidden" name="id" value="<?php echo $poll_id; ?>"/>
                    <input type="hidden" name="cf_page_no"
                           value="<?php echo $cf_page_no; ?>"/>
                    <input type="hidden" name="oa_page_no"
                           value="<?php echo $oa_page_no; ?>"/>
                    <input type="hidden" name="cf_per_page"
                           value="<?php echo $cf_per_page; ?>"/>
                    <input type="hidden" name="results_order_by" value="<?php echo $results_order_by; ?>"/>
                    <input type="hidden" name="results_order" value="<?php echo $results_order; ?>"/>
                    <input type="hidden" name="soav" value="<?php echo $soav; ?>"/>
                    <div class="tablenav top">
                        <div class="tablenav-pages one-page">
                            <label for="yop-poll-oa-items-per-page" class="displaying-num"><?php _e( 'Items Per Page', 'yop_poll' ); ?>
                                :</label><input
                                id="yop-poll-oa-items-per-page" type="text" name="oa_per_page"
                                value="<?php echo $oa_per_page; ?>"/> <input name="a"
                                                                             value="<?php _e( 'Set', 'yop_poll' ); ?>" type="submit"/>&nbsp;&nbsp;<span
                                class="displaying-num"><?php echo count( $logs_other_answers ); ?>
                                / <?php echo $total_logs_other_answers; ?> items</span>
                            <?php print $oa_pagination; ?>
                        </div>
                        <br class="clear">
                    </div>
                    <table class="wp-list-table widefat fixed" cellspacing="0">
                        <thead>
                        <tr>
                            <th id="" class="column-answer" style="width: 40%;" scope="col"><?php _e( 'Other Answers', 'yop_poll' ); ?></th>
                            <th id="" class="column-votes" style="width: 5%;" scope="col"><?php _e( 'Votes', 'yop_poll' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ( count( $logs_other_answers ) > 0 ){
                            foreach ( $logs_other_answers as $answer ) {
                                ?>
                                <tr>
                                    <td><?php echo esc_html( stripslashes( $answer['other_answer_value'] ) ); ?></td>
                                    <td><?php echo esc_html( stripslashes( $answer['votes'] ) ); ?></td>
                                </tr>
                            <?php
                            }
                        }
                        else {
                            ?>
                            <tr>
                                <td colspan="2"><?php _e( 'No other answers defined!', 'yop_poll' ); ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="tablenav top">
                        <div class="tablenav-pages one-page">
                            <?php print $oa_pagination; ?>
                        </div>
                    </div>
                    <br class="clear">
                </form>
            </div>
            <div style="width: 69%; float: right;">
                <h3><?php _e( 'Custom Fields', 'yop_poll' ); ?></h3>
                <form method="get">
                    <?php wp_nonce_field( 'yop-poll-custom-fields' ); ?>
                    <input type="hidden" name="page" value="yop-polls"/>
                    <input type="hidden" name="action" value="results"/>
                    <input type="hidden" name="id" value="<?php echo $poll_id; ?>"/>
                    <input type="hidden" name="oa_page_no"
                           value="<?php echo $oa_page_no; ?>"/>
                    <input type="hidden" name="cf_page_no"
                           value="<?php echo $cf_page_no; ?>"/>
                    <input type="hidden" name="oa_per_page"
                           value="<?php echo $oa_per_page; ?>"/>
                    <input type="hidden" name="results_order_by" value="<?php echo $results_order_by; ?>"/>
                    <input type="hidden" name="results_order" value="<?php echo $results_order; ?>"/>
                    <input type="hidden" name="soav" value="<?php echo $soav; ?>"/>

                    <div class="tablenav top">
                        <div class="alignleft actions">
                            <select name="export">
                                <option value="page"><?php _e( 'This Page', 'yop_poll' ); ?></option>
                                <option value="all"><?php _e( 'All Pages', 'yop_poll' ); ?></option>
                            </select> <input type="submit"
                                             value="<?php _e( 'Export', 'yop_poll' ); ?>"
                                             class="button-secondary action" id="doaction" name="a">
                            &nbsp;&nbsp;&nbsp; <label
                                for="yop-poll-custom-field-start-date-input"><?php _e( 'Start Date', 'yop_poll' ); ?>
                                :</label>
                            <input id="yop-poll-custom-field-start-date-input" type="text"
                                   name="cf_sdate" value="<?php echo $cf_sdate; ?>"/>&nbsp;&nbsp; <label
                                for="yop-poll-custom-field-end-date-input"><?php _e( 'End Date', 'yop_poll' ); ?>
                                :</label>
                            <input id="yop-poll-custom-field-end-date-input" type="text"
                                   name="cf_edate" value="<?php echo $cf_edate; ?>"/>&nbsp;&nbsp; <input
                                value="<?php _e( 'Filter', 'yop_poll' ); ?>" type="submit"
                                name="a"/>
                        </div>
                        <div class="tablenav-pages one-page">
                            <label for="yop-poll-items-per-page" class="displaying-num"><?php _e( 'Items Per Page', 'yop_poll' ); ?>
                                :</label><input
                                id="yop-poll-items-per-page" type="text" name="cf_per_page"
                                value="<?php echo $cf_per_page; ?>"/> <input name="a"
                                                                             value="<?php _e( 'Set', 'yop_poll' ); ?>" type="submit"/>&nbsp;&nbsp;<span
                                class="displaying-num"><?php echo count( $custom_fields_logs ); ?>
                                / <?php echo $total_custom_fields_logs; ?> items</span>
                            <?php print $cf_pagination; ?>
                        </div>
                        <br class="clear">
                    </div>
                    <table class="wp-list-table widefat fixed" cellspacing="0">
                        <thead>
                        <tr>
                            <th id="" class="column-answer" style="width: 5%" scope="col"><?php _e( '#', 'yop_poll' ); ?></th>
                            <?php
                            foreach ( $poll_custom_fields as $custom_field ) {
                                $column_custom_fields_ids [] = $custom_field ['id'];
                                ?>
                                <th id="custom_field_<?php echo $custom_field['id']; ?>" class="column-custom-field" style="width:<?php echo intval( 80 / intval( $custom_fields_number ) ); ?>%" scope="col"><?php echo ucfirst( $custom_field['custom_field'] ); ?></th>
                            <?php
                            }
                            ?>
                            <th id="" class="column-vote-id" style="width:20%"
                                scope="col"><?php _e( 'Vote ID', 'yop_poll' ); ?></th>
                            <th id="" class="column-tr-id" style="width:15%"
                                scope="col"><?php _e( 'Tracking ID', 'yop_poll' ); ?></th>
                            <th id="" class="column-vote-date" style="width:15%"
                                scope="col"><?php _e( 'Vote Date', 'yop_poll' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ( count( $custom_fields_logs ) > 0 ){
                            $index = ( $cf_page_no - 1 ) * $cf_per_page + 1;
                            foreach ( $custom_fields_logs as $logs ) {
                                ?>
                                <tr>
                                    <td><?php echo $index; ?></td>
                                    <?php
                                    foreach ( $column_custom_fields_ids as $custom_field_id ) {
                                        $vote_log_values = array();
                                        $vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
                                        if ( count( $vote_logs ) > 0 ){
                                            foreach ( $vote_logs as $vote_log ) {
                                                $temp                        = explode( '<#!->', $vote_log );
                                                $vote_log_values [$temp [1]] = stripslashes( $temp [0] );
                                            }
                                        }
                                        ?>
                                        <td><?php echo isset( $vote_log_values[$custom_field_id] ) ? $vote_log_values[$custom_field_id] : ''; ?></td>
                                    <?php
                                    }
                                    ?>
                                    <td><?php echo $logs['vote_id']; ?></td>
                                    <td><?php echo $logs['tr_id']; ?></td>
                                    <td><?php echo $logs['vote_date']; ?></td>
                                </tr>
                                <?php
                                $index++;
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="tablenav top">
                        <div class="tablenav-pages one-page">
                            <?php print $cf_pagination; ?>
                        </div>
                        <br class="clear">
                    </div>
                </form>
            </div>
            <div style="clear: both;"></div>
            </div>
        <?php
        }
        else {
            ?>
            <h3><?php _e( 'Your poll doesn`t exist!', 'yop_poll' ); ?></h3>
        <?php
        }
    }

    public static function get_polls_results_filter_search( $args ) {
        $return_fields = '*';
        $filters       = null;
        $search        = null;
        $orderby       = 'vote_date';
        $order         = 'DESC';
        $limit         = null;

        if( isset( $args['return_fields'] ) ) {
            $return_fields = trim( $args['return_fields'], ',' );
        }
        if( isset( $args['filters'] ) ) {
            $filters = $args['filters'];
        }
        if( isset( $args['search'] ) ) {
            $search = $args['search'];
        }
        if( isset( $args['orderby'] ) ) {
            $orderby = $args['orderby'];
        }
        if( isset( $args['order'] ) ) {
            if( in_array( strtoupper( $args['order'] ), array(
                'ASC',
                'DESC'
            ) )
            ) {
                $order = $args['order'];
            }
        }
        if( isset( $args['limit'] ) ) {
            $limit = $args['limit'];
        }

        $sql        = 'SELECT ' . $return_fields . ' FROM ' . $GLOBALS['wpdb']->yop_poll_results . ' WHERE poll_id =' . $args['poll_id'];
        $sql_filter = '';
        if( count( $filters ) > 0 ) {
            foreach( $filters as $filter ) {
                $sql_filter .= ' AND ' . $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $filter['field'] ) . '` ' . $filter['operator'] . ' %s ', esc_attr( $filter['value'] ) ) . ' ';
            }
        }

        $sql_search = '';
        if( ( count( $search['fields'] ) ) > 0 && ( $search['fields'][0] != "" ) ) {
            foreach( $search['fields'] as $field ) {
                if(!isset($field) || $field=='all'){
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr( 'ip' ) . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('vote_id') . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('user_type') . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('tr_id') . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('vote_date') . '` like \'%%%s%%\' OR', $search['value'] );
                }
                else
                {
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $field ) . '` like \'%%%s%%\' OR', $search['value'] );
                }
            }
            $sql_search = ' AND ( ' . trim( $sql_search, 'OR' ) . ' ) ';
        }
        $sql_order_by = ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order ) . ' ';
        $sql_limit    = '';
        if( $limit ) {
            $sql_limit = ' LIMIT ' . $limit . ' ';
        }
        return $GLOBALS['wpdb']->get_results( $sql . $sql_filter . $sql_search . $sql_order_by . $sql_limit, ARRAY_A );
    }


    public static function get_polls_results_for_view( $args = array() ) {
        $return_fields = '*';
        $filters       = null;
        $search        = null;
        $orderby       = 'id';
        $order         = 'ASC';
        $limit         = null;

        if( isset( $args['return_fields'] ) ) {
            $return_fields = trim( $args['return_fields'], ',' );
        }
        if( isset( $args['filters'] ) ) {
            $filters = $args['filters'];
        }
        if( isset( $args['search'] ) ) {
            $search = $args['search'];
        }
        if( isset( $args['orderby'] ) ) {
            $orderby = $args['orderby'];
        }
        if( isset( $args['order'] ) ) {
            if( in_array( strtoupper( $args['order'] ), array(
                'ASC',
                'DESC'
            ) )
            ) {
                $order = $args['order'];
            }
        }
        if( isset( $args['limit'] ) ) {
            $limit = $args['limit'];
        }

        $sql        = 'SELECT ' . $return_fields . ' FROM ' . $GLOBALS['wpdb']->yop_poll_results . ' AS results WHERE poll_id=' . $args['poll_id'];
        $sql_filter = '';
        if( count( $filters ) > 0 ) {
            foreach( $filters as $filter ) {
                $sql_filter .= ' AND ' . $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $filter['field'] ) . '` ' . $filter['operator'] . ' %s ', esc_attr( $filter['value'] ) ) . ' ';
            }
        }

        $sql_search = '';
        if( ( count( $search['fields'] ) > 0 ) && ( $search['fields'][0] != "" ) ) {
            foreach( $search['fields'] as $field ) {
                if(!isset($field) || $field=='all'){
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr( 'ip' ) . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('vote_id') . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('user_type') . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('tr_id') . '` like \'%%%s%%\' OR', $search['value'] );
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr('vote_date') . '` like \'%%%s%%\' OR', $search['value'] );
                }
                else
                {
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $field ) . '` like \'%%%s%%\' OR', $search['value'] );
                }
            }
            $sql_search = ' AND ( ' . trim( $sql_search, 'OR' ) . ' ) ';
        }

        $sql_order_by = ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order ) . ' ';
        $sql_limit    = '';
        if( $limit ) {
            $sql_limit = ' LIMIT ' . $limit . ' ';
        }

        return $GLOBALS['wpdb']->get_results( $sql . $sql_filter . $sql_search . $sql_order_by . $sql_limit, ARRAY_A );
    }

    private function delete_results() {
        global $message;
        if( check_admin_referer( 'yop-poll-results_vote', 'yop-poll-results_votes' ) ) {
            if( isset ( $_REQUEST ['yoppollresultscheck'] ) ) {
                $bulkresults = ( array )$_REQUEST ['yoppollresultscheck'];
                foreach( $bulkresults as $result_id ) {
                    $result_id      = (int)$result_id;
                    $answer_details = self:: yop_poll_get_result_by_id( $result_id );
                    $message        = self::delete_result_from_db( $result_id );
                    if( $message['success'] != "" ) {
                        self::yop_poll_change_no_of_votes( $answer_details );
                        unset( $message );
                    }

                    unset( $answer_details );
                }
            }
            else
                if( isset( $_REQUEST['resultid'] ) ) {
                    $answer_details = self:: yop_poll_get_result_by_id( $_REQUEST['resultid'] );
                    $message        = self::delete_result_from_db( $_REQUEST['resultid'] );
                    if( $message['success'] != "" ) {
                        self::yop_poll_change_no_of_votes( $answer_details );
                    }
                }


        }
        else {
            wp_die( __yop_poll( 'You do not have enough permission to delete a result!' ) );
        }
        self::view_results_votes();

    }

    public function delete_result_from_db( $res_id ) {
        global $wpdb;
        $response['success'] = "";
        $response['error']   = "";

        $sql = $wpdb->query( $wpdb->prepare( "
					DELETE FROM  $wpdb->yop_poll_results
					WHERE ID = %d
					", $res_id ) );
        if( $sql ) {
            $response['success'] = __yop_poll( 'Result deleted' );

        }
        else {
            $response['error'] = __yop_poll( 'Could not delete result from database! Please try again!' );
        }

        return $response;
    }

    public function export_results_votes() {
        $data                    = array();
        $data['poll_id']         = $_REQUEST['id'];
        $per_page                = ( isset( $_REQUEST ['per_page'] ) && intval( $_REQUEST ['per_page'] ) > 0 ) ? intval( $_REQUEST ['per_page'] ) : 100;
        $data['per_page']        = ( isset( $_REQUEST ['per_page'] ) && intval( $_REQUEST ['per_page'] ) > 0 ) ? intval( $_REQUEST ['per_page'] ) : 100;
        $page_no                 = ( isset( $_REQUEST ['page_no'] ) && intval( $_REQUEST ['page_no'] ) > 0 ) ? intval( $_REQUEST ['page_no'] ) : 1;
        $per_page                = ( isset( $_REQUEST ['per_page'] ) && intval( $_REQUEST ['per_page'] ) > 0 ) ? intval( $_REQUEST ['per_page'] ) : 100;
        $data['page_no']         = ( isset( $_REQUEST ['page_no'] ) && intval( $_REQUEST ['page_no'] ) > 0 ) ? intval( $_REQUEST ['page_no'] ) : 1;
        $orderby                 = ( empty ( $GLOBALS['orderby'] ) ) ? 'ip' : $GLOBALS['orderby'];
        $order                   = ( empty ( $GLOBALS['order'] ) ) ? 'desc' : $GLOBALS['order'];
        $data['request']['s_ip'] = ( isset ( $_REQUEST ['s_ip'] ) ? $_REQUEST ['s_ip'] : '' );

        $order_fields = array(
            'vote_date',
            'user_type',
            'ip'
        );

        $args = array(
            'poll_id'       => $_REQUEST['id'],
            'return_fields' => 'COUNT(*) as total_results',
            'search'        => array(
                'fields' => array( 'ip' ),
                'value'  => isset ( $_REQUEST ['s_ip'] ) ? $_REQUEST ['s_ip'] : ''
            ),
            'orderby'       => $orderby,
            'order'         => $order
        );

        $total_results = self::get_polls_results_filter_search( $args );

        if( ! isset( $total_results[0]['total_results'] ) ) {
            $total_results[0]['total_results'] = 0;
        }

        $total_results_pages = ceil( $total_results[0]['total_results'] / $per_page );
        if( intval( $page_no ) > intval( $total_results_pages ) ) {
            $page_no = 1;
        }


        $args['return_fields'] = "*";

        $data['REQUEST']                 = $_REQUEST;
        $data['orderby']                 = $orderby;
        $data['order']                   = $order;
        $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );
        $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );
        $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );

        $csv_file_name    = 'votes_export.' . date( 'YmdHis' ) . '.csv';
        $csv_header_array = array(

            __( 'Vote details', 'yop_poll' ),
            __( 'Ip', 'yop_poll' ),
            __( 'Vote date', 'yop_poll' ),

        );

        header( "Content-Type: text/csv" );
        header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
        header( "Content-Transfer-Encoding: binary\n" );
        header( 'Content-Disposition: attachment; filename="' . $csv_file_name . '"' );
        ob_start();
        $f = fopen( 'php://output', 'w' ) or show_error( __( "Can't open php://output!", 'yop_poll' ) );
        $custom=self::get_custom_field_by_poll_id(  $data['poll_id'] );
        $custom_to_export=array();
        foreach($custom as $cust){
            array_push($csv_header_array,$cust['custom_field']);
            array_push($custom_to_export,$cust['ID']);
        }
        if( ! fputcsv( $f, $csv_header_array ) ) {
            _e( "Can't write header!", 'yop_poll' );
        }

        if( $_REQUEST ['export'] != "all" ) {
            $votes = array();

            $args['limit']        = ( $page_no - 1 ) * $per_page . ', ' . $per_page;
            $data['results']      = self::get_polls_results_for_view( $args );
            $data['total_items']  = $total_results[0]['total_results'];
            $data['current_user'] = $GLOBALS['current_user'];
            foreach( $data['results'] as &$result ) {
                $result['votes_details'] = json_decode( $result['result_details'], true );
                foreach( $result['votes_details'] as $question ) {
                    $vote_answer = "";
                    foreach( $question['answers'] as $answer ) {
                        $vote_answer .= $answer . "\n";
                    }
                    $result['vote_answers'] .= $vote_answer . ".\n";
                    if( isset( $question['cf'] ) ) {
                        $custom_fields_details = "";
                        foreach( $question['cf'] as $cf_id ) {
                                $custom_field_log = self::get_custom_field_log_by_id($cf_id);
                                $custom_field = self::get_custom_field_by_id($custom_field_log[0]['custom_field_id']);

                             $custom_fields_details[$custom_field_log[0]['custom_field_id']]=$custom_field_log[0]['custom_field_value'];

                        }

                        $result['custom_fields'] = $custom_fields_details;

                    }
                }
            }
            foreach( $data['results'] as $detail ) {

                $votes[] = $detail['vote_answers'];

                $votes[] = $detail['ip'];
                $votes[] = $detail['vote_date'];

                foreach($custom_to_export as $export)

                    if(isset($detail['custom_fields'][$export]))
                        $votes[] = $detail['custom_fields'][$export];
                    else
                        $votes[] = " ";
                if( ! fputcsv( $f, $votes ) ) {
                    _e( "Can't write header!", 'yop_poll' );
                }
                unset( $votes );
            }

        }
        else {
            $votes                = array();
            $data['results']      = self::get_polls_results_for_view( $args );
            $data['total_items']  = $total_results[0]['total_results'];
            $data['current_user'] = $GLOBALS['current_user'];
            foreach( $data['results'] as &$result ) {
                $result['votes_details'] = json_decode( $result['result_details'], true );
                foreach( $result['votes_details'] as $question ) {
                    $vote_answer = $question['question'] . ": ";
                    foreach( $question['answers'] as $answer ) {
                        $vote_answer .= $answer . ", ";
                    }
                    $result['vote_answers'] .= $vote_answer . ".\n";
                    if( isset( $question['cf'] ) ) {
                        $custom_fields_details = "";
                        foreach( $question['cf'] as $cf_id ) {
                            $custom_field_log = self::get_custom_field_log_by_id($cf_id);
                            $custom_field = self::get_custom_field_by_id($custom_field_log[0]['custom_field_id']);
                            $custom_fields_details[$custom_field_log[0]['custom_field_id']]=$custom_field_log[0]['custom_field_value'];

                        }
                        $result['custom_fields'] = $custom_fields_details;

                    }
                }
            }

            foreach( $data['results'] as $detail ) {

                $votes[] = $detail['vote_answers'];
                $votes[] = $detail['ip'];
                $votes[] = $detail['vote_date'];

                foreach($custom_to_export as $export)
                    if(isset($detail['custom_fields'][$export]))
                        $votes[] = $detail['custom_fields'][$export];
                else
                    $votes[] = " ";
                if( ! fputcsv( $f, $votes ) ) {
                    _e( "Can't write header!", 'yop_poll' );
                }
                unset( $votes );
            }


        }
        fclose( $f ) or show_error( __( "Can't close php://output!", 'yop_poll' ) );
        $csvStr = ob_get_contents();
        ob_end_clean();

        echo $csvStr;
        exit ();
    }

    public function yop_poll_change_no_of_votes( $answer_details ) {
        $index                   = 0;
        $current_poll            = new YOP_POLL_Poll_Model( $answer_details[0]['poll_id'] );
        $result['votes_details'] = json_decode( $answer_details[0]['result_details'], true );
        foreach( $result['votes_details'] as $question ) {
            foreach( $question['answers'] as $answer ) {
                $index ++;
            }
            foreach( $question['a'] as $answer_id ) {
                $poll_answer = new YOP_POLL_Answer_Model( $answer_id );
                $poll_answer->votes --;
                $poll_answer->update();
            }
        }
        $current_poll->poll_total_votes -= $index;

        $current_poll->update_no_votes();
    }

    public function yop_poll_get_result_by_id( $r_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
					SELECT *
					FROM   $wpdb->yop_poll_results
					WHERE ID = %d
					", $r_id ), ARRAY_A );

        return $result;
    }

    public function add_votes() {
        $ok = 1;
        global $current_user;
        get_currentuserinfo();
        $cheked=0;
        global $message;
        $poll_total_votes = 0;
        if( isset( $_POST['total_number_of_votes'] ) && $_POST['total_number_of_votes'] != 0 ) {

            $no_of_votes = $_REQUEST['total_number_of_votes'];
        }
        $add_to_results = "";
        $current_poll   = new YOP_POLL_Poll_Model( $_POST['poll_id'] );
        if( current_user_can( 'add_yop_poll_votes' ) ) {
            $index=0;
            $i=0;
            $message['append_row']="";
            $append_row="";
            while($ok==1){

                // yop_poll_dump($answer_details);
                $ok                                               = 0;
                $i=0;
                $details="";
                $max=100;
                $question_count=0;
                foreach( $current_poll->questions as $question ) {


                    $bulkanswers                                      = $_POST ['yoppollresultsanswerscheck'][$question->ID];
                    $details.=$question->question.": ";
                    $answer_count=0;

                    foreach( $question->answers as $answer ) {
                        $answer_count++;
                    if(isset($bulkanswers))
                        foreach( $bulkanswers as $bulkanswer ) {


                            if( $bulkanswer == $answer->ID ) {
                                $cheked=1;
                                if($_POST['yop_poll_no_of_votes_'.$answer->ID.'_per_answer']!=""){
                                    if($_POST['yop_poll_no_of_votes_'.$answer->ID.'_per_answer']!=0){

                                        if($_POST['yop_poll_no_of_votes_'.$answer->ID.'_per_answer']>$index){
                                            $answer_details["q-" . $question->ID]['question'] = $question->question . ": ";
                                            $answer_details["q-" . $question->ID]['id'] = $question->ID ;
                                            $ok=1;
                                            $details.=$answer->answer.",";
                                            $add_to_results .= $answer->answer;
                                            $answer->votes=  $answer->votes+1;
                                            $answer_details["q-" . $question->ID]['a'][]       = $answer->ID;
                                            $answer_details["q-" . $question->ID]['answers'][] = $answer->answer;
                                            $a=new YOP_POLL_Answer_Model($answer->ID);
                                            $a->votes++;
                                            $a->update();
                                            $poll_total_votes ++;
                                        }
                                    }else {
                                        $message['error']= "Answer input from answer ".$answer_count." Question ".($question_count+1)." is zero!";
                                        wp_die( json_encode( $message ) );
                                    }

                                }
                                else {
                                    $message['error']= "Answer input from answer ".$answer_count." Question ".($question_count+1)." is not set!";
                                    wp_die( json_encode( $message ) );
                                }

                            }

                        }

                    }
                    $details.="<br>";
                    $question_count++;
                }

                if($ok==1){
                    $current_poll->poll_total_votes += $poll_total_votes;
                    $poll_total_votes=0;
                    $current_poll->update_no_votes();
                    $current_poll->save();
                    $result['vote_details'] = json_encode( $answer_details );
                    $result['poll_id']      = $_POST['poll_id'];
                    $result['ip']           = yop_poll_get_ip();
                    $result['user_type']    = "admin";
                    $result['vote_id']=uniqid( 'vote_id_' );
                    $result['user_id']=$current_user->ID;
                    $message = insert_result_in_db( $result );

                    $index++;
                    $append_row[$i].= " <tr valign=".'middle'." class=".
                        'alternate'."
                    id=".'yop-poll-log{{log.id}}'.">
                    <th class=".'check-column'." scope=".'row'.">
                        <input type=".'checkbox'." value=".$message['insert_id']."
                               name=".'yoppollresultscheck[]'.">
                    </th>
                    <td><strong>".$result['vote_id']."</strong>
                        <br>
                        <div class=".'row-actions'.">
                                                    <span class=".'delete'.">
                                                       <a	onclick=".'return confirm( '.' "You are about to delete this result" '.') :  "Cancel"   "to stop" ,  "OK"  "to delete'.')'. "

                                                              href=".'?page=yop-polls&action=delete_result&resultid='.$message['insert_id']."

                                                            class=".'submitdelete'.">Delete</a></span>|<span class=".'delete'.">
                                                         <a
                                                              onclick=".'show_pop_up_ban('.($max+1).')'."
                                                            class=".'submitdelete'.">Ban</a></span>
                        </div></td>
                    <td style=".'display:none;'." class=".'hidden_tds'."><input  id=".'yop-poll-results-ip_'.($max+1)." value=". $result['ip'] ."><input></td>
                    <td style=".'display: none'." class=".'hidden_tds'."><input  id=".'yop-poll-results-userid_'.($max+1)." value=".$current_user->ID."><input></td>
                    <td>
                        Admin
                    </td>
                    <td>
                    </td>
                    <td>
                ".
                        $result['ip']
                        ."
                    </td>
                    <td>
                     ".  current_time( 'mysql' )."
                    </td>
                    <td class=".'more_details'." style=".'"'.'cursor:pointer;'.'"'.">
                    </td>
                      <td class=".'less_details'." style=".'"'.'cursor:pointer;'.'"'." >
                     </td>
                </tr>
                <tr  class=".'results_details'." class=".'hidden_tds'.">
                    <td></td> <td></td>
                    <td>  Questions<br><strong>".$details."</strong><br></td>
                </tr>";
                    //   yop_poll_dump($i);
                    $i=$i+1;

                    unset($answer_details);
                }
                if($cheked==0){
                    $message['error']= "You must select at least one answer!";
                    wp_die( json_encode( $message ) );
                }
            }

        }
        else {
            $message['error']= "You don't have enough permissions do add a vote!";
            wp_die( json_encode( $message ) );
        }

        //yop_poll_dump($append_row);
        foreach($append_row as $a){
            $message['append_row'].=$a;
        }
        wp_die( json_encode( $message ) );
        // self::view_results_votes();
    }

    public function yop_poll_get_percentages_for_age_gender_charts() {
        $poll_id = $_REQUEST['id'];

        $data         = self::yop_poll_age_gender_type_of_voters( $poll_id );
        $current_poll = new YOP_POLL_Poll_Model( $poll_id );
        foreach( $current_poll->questions as $question ) {
            $chart_age[$question->ID]    = array(
                array(
                    "Answers",
                    "Voters"
                ),
                array(
                    '0-20',
                    $data[$question->ID]['under_20']
                ),
                array(
                    '21-35',
                    $data[$question->ID]['under_35']
                ),
                array(
                    '36-50',
                    $data[$question->ID]['under_50']
                ),
                array(
                    'Over 50 years',
                    $data[$question->ID]['over_50']
                ),
                array(
                    'Undefined',
                    $data[$question->ID]['no_undefined_age']
                ),
                array(
                    'Admin Votes',
                    $data[$question->ID]['no_admin_age']
                )
            );
            $chart_gender[$question->ID] = array(
                array(
                    "Answers",
                    "Voters"
                ),
                array(
                    'females',
                    $data[$question->ID]['no_females']
                ),
                array(
                    'males',
                    $data[$question->ID]['no_males']
                ),
                array(
                    'undefined',
                    $data[$question->ID]['no_undefined_gender']
                ),
                array(
                    'admin votes',
                    $data[$question->ID]['no_admin_gender']
                )
            );

            $chart_type[$question->ID] = array(
                array(
                    "Answers",
                    "Voters"
                ),
                array(
                    'Wordpress',
                    $data[$question->ID]['no_wordpress']
                ),
                array(
                    'Facebook',
                    $data[$question->ID]['no_facebook']
                ),
                array(
                    'Undefined',
                    $data[$question->ID]['no_undefined_type']
                ),
                array(
                    'Google',
                    $data[$question->ID]['no_google']
                ),
                array(
                    'Anonymous',
                    $data[$question->ID]['no_anonymous']
                ),
                array(
                    'Admin votes',
                    $data[$question->ID]['no_admin_type']
                )
            );
        }
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_age', $chart_age );
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_gender', $chart_gender );
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_type', $chart_type );
    }

    public function yop_poll_age_gender_type_of_voters( $poll_id ) {
        $results         = self::yop_poll_get_results_by_poll_id( $poll_id );
        $number_of_votes = count( $results );
        $data            = array();
        foreach( $results as $result ) {
            $vote_details = json_decode( $result['result_details'], true );
            foreach( $vote_details as $vote_detail ) {

                if( $result['user_type'] == "admin" ) {
                    $data[$vote_detail['id']]['no_admin_age'] ++;
                }
                else {
                    $user_details = json_decode( $result['user_details'], true );
                    if( isset( $user_details[0]['age'] ) ) {
                        if( $user_details[0]['age'] <= 20 ) {
                            $data[$vote_detail['id']]['under_20'] += 1;
                        }
                        else if( $user_details[0]['age'] <= 35 ) {
                            $data[$vote_detail['id']]['under_35'] += 1;
                        }
                        else if( $user_details[0]['age'] <= 50 ) {
                            $data[$vote_detail['id']]['under_50'] += 1;
                        }
                        else if( $user_details[0]['age'] > 50 ) {
                            $data[$vote_detail['id']]['over_50'] += 1;
                        }
                        else {
                            $data[$vote_detail['id']]['no_undefined_age'] ++;

                        }
                    }
                    else  $data[$vote_detail['id']]['no_undefined_age'] ++;
                }

                if( $result['user_type'] == "admin" ) {
                    $data[$vote_detail['id']]['no_admin_gender'] ++;
                }
                else {

                    if( isset( $user_details[0]['gender'] ) ) {
                        if( $user_details[0]['gender'] == "male" ) {
                            $data[$vote_detail['id']]['no_males'] += 1;
                        }
                        else if( $user_details[0]['age'] == "female" ) {
                            $data[$vote_detail['id']]['no_females'] += 1;
                        }
                        else {
                            $data[$vote_detail['id']]['no_undefined_gender'] ++;

                        }
                    }
                    else  $data[$vote_detail['id']]['no_undefined_gender'] ++;
                }
                if( $result['user_type'] == "admin" ) {
                    $data[$vote_detail['id']]['no_admin_type'] ++;
                }
                else {
                    if( $result['user_type'] == "wordpress" ) {
                        $data[$vote_detail['id']]['no_wordpress'] ++;
                    }
                    else if( $result['user_type'] == "google" ) {
                        $data[$vote_detail['id']]['no_google'] ++;
                    }
                    else if( $result['user_type'] == "facebook" ) {
                        $data[$vote_detail['id']]['no_facebook'] ++;
                    }
                    else if( $result['user_type'] == "anonymous" ) {
                        $data[$vote_detail['id']]['no_anonymous'] ++;
                    }
                    else  $data[$vote_detail['id']]['no_undefined_type'] ++;
                }
            }
        }

        return $data;
    }

    public function get_number_of_males_or_females( $poll_id, $type ) {
        global $wpdb;
        $sql = 'SELECT count(*) FROM ' . $GLOBALS['wpdb']->yop_poll_results . ' WHERE poll_id =' . $poll_id;
        $sql .= ' AND' . $GLOBALS['wpdb']->prepare( ' `user_details` like \'%%%s%%\'', $type );

        return $wpdb->get_var( $sql );
    }

    public function get_number_of_males_or_females_age( $poll_id, $type ) {
        global $wpdb;
        $sql = 'SELECT count(*) FROM ' . $GLOBALS['wpdb']->yop_poll_results . ' WHERE poll_id =' . $poll_id;
        $sql .= ' AND' . $GLOBALS['wpdb']->prepare( ' `user_details` like \'%%%s%%\' ', $type, $type );

        return $sql;
    }

    public function get_number_of_votes_inserted_by_admin( $poll_id ) {
        global $wpdb;
        $result = $wpdb->get_var( $wpdb->prepare( "
                        SELECT count(*)
                        FROM   $wpdb->yop_poll_results
                        WHERE poll_id = %d AND user_type = %s
                        ", $poll_id, 'admin' ) );

        return $result;
    }

    public function get_custom_field_log_by_id( $cf_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
                        SELECT *
                        FROM   $wpdb->yop_poll_votes_custom_fields
                        WHERE ID = %d
                        ", $cf_id ), ARRAY_A );

        return $result;
    }

    public function get_custom_field_by_id( $cf_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
                        SELECT *
                        FROM   $wpdb->yop_poll_custom_fields
                        WHERE ID = %d
                        ", $cf_id ), ARRAY_A );

        return $result;
    }
    public function get_custom_field_by_poll_id( $poll_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
                        SELECT *
                        FROM   $wpdb->yop_poll_custom_fields
                        WHERE poll_id = %d ORDER  BY ID Asc
                        ", $poll_id ), ARRAY_A );

        return $result;
    }
    public function yop_poll_get_results_by_poll_id( $poll_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
                        SELECT *
                        FROM   $wpdb->yop_poll_results
                        WHERE poll_id = %d GROUP BY vote_id
                        ", $poll_id ), ARRAY_A );

        return $result;
    }

    public function yop_poll_get_results_vote_date_by_poll_id( $poll_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
                        SELECT DATE(vote_date) v_data ,count(*) as no_votes
                        FROM   $wpdb->yop_poll_results
                        WHERE poll_id = %d GROUP BY v_data
                        ", $poll_id ), ARRAY_A );

        return $result;
    }

    private function exists( $ban ) {
        global $wpdb, $message;
        return $wpdb->get_var( $wpdb->prepare( "
									SELECT id
									FROM  $wpdb->yop_poll_bans
									WHERE poll_id in( 0, %d) AND
									(type = %s and period = %s and unit = %s and value = %s )
									LIMIT 0,1
									", $ban['poll_id'], $ban['type'], $ban['period'], $ban['unit'], $ban['value'] ) );
    }


    public function yop_poll_get_country_date_by_ip( $poll_id ) {
        global $wpdb;
        $result = $wpdb->get_results( $wpdb->prepare( "
                        SELECT  country  ,count(*) as no_country
                        FROM   $wpdb->yop_poll_results
                        WHERE poll_id = %d GROUP BY country
                        ", $poll_id ), ARRAY_A );

        return $result;
    }

    private function reset_stats() {
        if( check_admin_referer( 'yop-poll-view-action', 'yop-poll-view-name' ) ) {
            // if( current_user_can( 'delete_own_yop_polls' ) ) {
            if( isset( $_REQUEST['id'] ) ) {
                YOP_POLL_Abstract_Model:: reset_poll_stats_from_database( $_REQUEST['id'] );
            }
            else {
                if( isset ( $_REQUEST['yoppollcheck'] ) ) {
                    $polls = ( array )$_REQUEST ['yoppollcheck'];
                    foreach( $polls as $poll ) {
                        YOP_POLL_Abstract_Model:: reset_poll_stats_from_database( $poll );
                    }
                }
            }
            //}
        }
        /* else {
             wp_die( __yop_poll( 'You are not allowed to clone this item.' ) );
         }
        }*/
        else {
            wp_die( __yop_poll( 'You are not allowed to reset votes for this poll.' ) );
        }
        self::view_polls();
    }
    private function view_results() {

        wp_enqueue_script( 'yop-poll-results-votes-js', YOP_POLL_URL . '/js/polls/yop-poll-results-votes.js', array(
            'jquery',
            'jquery-ui-resizable'
        ), YOP_POLL_VERSION, true );
        //self::yop_poll_get_percentages_for_age_gender_charts();

        $index                    = 1;
        $data['title']            = __yop_poll( "Results" );
        $data['poll_id']          = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
        $data['results_order_by'] = ( isset ( $_GET ['results_order_by'] ) ? $_GET ['results_order_by'] : 'id' );
        $data['results_order']    = ( isset ( $_GET ['results_order'] ) ? $_GET ['results_order'] : 'ASC' );
        $data['soav']             = ( isset ( $_GET ['soav'] ) ? $_GET ['soav'] : 'no' );
        $data['a']                = ( isset ( $_GET ['a'] ) ? $_GET ['a'] : 'no' );
        $current_poll             = new YOP_POLL_Poll_Model( $data['poll_id'], $is_view_results = "no", $question_sort = "poll_order", $question_sort_rule = "ASC", $answer_sort = $data['results_order_by'], $answer_sort_rule = $data['results_order'] );
        $data['poll_details']     = array(
            'name'     => $current_poll->poll_title,
            'question' => $current_poll->questions
        );
        if( 'yes' == $data['soav'] ) {
            $data['display_other_answers_values'] = true;
        }
        else {
            $data['display_other_answers_values'] = false;
        }
        $percentages = array();
        $total_votes = array();
        $i           = 0;
        foreach( $current_poll->questions as $question ) {
            $total_votes[$i] = 0;
            foreach( $question->answers as $answer ) {
                $total_votes[$i] += floatval( $answer->votes );
            }
            $i ++;
        }
        $i = 0;

        foreach( $current_poll->questions as $question ) {
            foreach( $question->answers as $answer ) {
                if( $answer->votes > 0 ) {
                    $answer->status = round( ( $answer->votes * 100 ) / $total_votes[$i], 1 );
                }
                else {
                    $percentages[$i][] = 0;
                    $answer->status    = 0;

                }
            }
            $i ++;
        }



        $data['cf_sdate']      = ( isset ( $_GET ['cf_sdate'] ) ? $_GET ['cf_sdate'] : '' );
        $data['cf_edate']      = ( isset ( $_GET ['cf_edate'] ) ? $_GET ['cf_edate'] : '' );
        $data['title']         = "Results";
        $data['custom_fields'] = array();

        foreach( $current_poll->questions as $question ) {
            $data['cf_per_page'] = ( isset ( $_REQUEST ['cf_per_page'] ) ? intval( $_REQUEST ['cf_per_page'] ) : 100 );
            $data['cf_page_no']  = ( isset ( $_REQUEST ['cf_page_no'] ) ? ( int )$_REQUEST ['cf_page_no'] : 1 );

            $poll_custom_fields = self::get_poll_customfields( $data['poll_id'], $question->ID );
            $custom_fields_logs = self::get_poll_customfields_logs( $data['poll_id'], $question->ID, 'vote_id', 'asc', ( $data['cf_page_no'] - 1 ) * $data['cf_per_page'], $data['cf_per_page'], $data['cf_sdate'], $data['cf_edate'] );
            unset( $column_custom_fields_ids );
            foreach( $poll_custom_fields as $custom_field ) {
                $column_custom_fields_ids [] = $custom_field ['ID'];

            }
            if( count( $custom_fields_logs ) > 0 ) {
                foreach( $custom_fields_logs as &$logs ) {
                    foreach( $column_custom_fields_ids as $custom_field_id ) {
                        $vote_log_values = array();
                        $vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
                        if( count( $vote_logs ) > 0 ) {
                            foreach( $vote_logs as $vote_log ) {
                                $temp                        = explode( '<#!->', $vote_log );
                                $vote_log_values [$temp [1]] = stripslashes( $temp [0] );
                            }
                        }
                    }
                    $custom_fields_logs_details[] = array(
                        'vote_id'                  => $logs['vote_id'],
                        "tr_id"                    => $logs['tr_id'],
                        "vote_date"                => $logs['vote_date'],
                        "custom_fields_value"      => $vote_log_values,
                        'column_custom_fields_ids' => $column_custom_fields_ids,
                    );
                }

            }
            $data['total_custom_fields_logs']       = self::get_poll_total_customfields_logs( $data['poll_id'], $question->ID, $data['cf_sdate'], $data['cf_edate'] );
            $data['total_custom_fields_logs_pages'] = ceil( $data['total_custom_fields_logs'] / $data['cf_per_page'] );
            $data['column_custom_fields_ids']       = array();

            if( intval( $data['cf_page_no'] ) > intval( $data['total_custom_fields_logs_pages'] ) ) {
                $data['cf_page_no'] = 1;
            }
            $data['cf_args']           = array(
                'base'      => remove_query_arg( 'cf_page_no', $_SERVER ['REQUEST_URI'] ) . '%_%',
                'format'    => '&cf_page_no=%#%',
                'total'     => $data['total_custom_fields_logs_pages'],
                'current'   => max( 1, $data['cf_page_no'] ),
                'prev_next' => true,
                'prev_text' => __yop_poll( '&laquo; Previous' ),
                'next_text' => __yop_poll( 'Next &raquo;' )
            );
            $data['cf_pagination']     = paginate_links( $data['cf_args'] );
            $chart_answer[$index][0][] = "Answer";
            $i                         = 1;
            $chart_answer[$index][0][] = "Votes";
            foreach( $question->answers as $answer ) {
                if( ( $answer->type == "other" && $data['display_other_answers_values'] == 1 ) || $answer->type != "other" ) {
                    if($answer->description=="")
                        $chart_answer[$index][$i][0] = $answer->answer;
                    else
                        $chart_answer[$index][$i][0] = $answer->description;
                    $chart_answer[$index][$i][1] = (int)$answer->votes;
                    $i ++;
                }
            }


            $question_detail[]         = array(
                'other_answer'               => $question->other_answers_label,
                'name'                       => $question->question,
                'answers'                    => $question->answers,
                'custom_fields'              => self::get_poll_customfields( $data['poll_id'], $question->ID ),
                'custom_fields_logs_details' => $custom_fields_logs_details,
                'q_id'                       => $question->ID,
                'total_custom_fields_logs'   => $data['total_custom_fields_logs'],
                'cf_pagination'              => $data['cf_pagination']
            );
            //yop_poll_dump($question_detail);
            $data['questions_details'] = $question_detail;
            unset( $custom_fields_logs_details );
            unset( $column_custom_fields_ids );
            $index ++;
        }
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_answer', $chart_answer );

        $data['total_logs_other_answers'] = 0;
        foreach( $current_poll->questions as $question ) {
            foreach( $question->answers as $other_answer ) {
                if( $other_answer->type == 'other' ) {
                    $data['total_logs_other_answers'] ++;
                }
            }
        }
        $countries           = self::yop_poll_get_country_date_by_ip( $data['poll_id'] );
        $vote_dates          = self::yop_poll_get_results_vote_date_by_poll_id( $data['poll_id'] );
        $chart_country[0][0] = "Country";
        $i                   = 1;
        $chart_country[0][1] = "Voters";
        foreach( $countries as $country ) {
            $chart_country[$i][0] = $country['country'];
            $chart_country[$i][1] = (int)$country['no_country'];
            $i ++;
        }
        $chart_evolution[0][0] = "Date";
        $i                     = 1;
        $chart_evolution[0][1] = "Voters";
        foreach( $vote_dates as $vote_date ) {
            $chart_evolution[$i][0] = convert_date( $vote_date['v_data'], 'm-d-Y' );
            $chart_evolution[$i][1] = (int)$vote_date['no_votes'];
            $i ++;
        }
        if(!isset($chart_evolution[$i][0])){
            $chart_evolution[1][0]= convert_date( yop_poll_get_mysql_curent_date(), 'm-d-Y' );
        }
        if(!isset($chart_evolution[1][1])){
            $chart_evolution[$i][1]=0;
        }
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_evolution', $chart_evolution );
        wp_localize_script( 'yop-poll-results-votes-js', 'charts_country', $chart_country );

        $this->display( 'results.html', $data );
    }

}
