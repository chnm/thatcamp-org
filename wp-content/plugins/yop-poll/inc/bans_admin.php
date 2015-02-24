<?php

    class YOP_POLL_Ban_Admin extends YOP_POLL_Abstract_Admin{
        private static $_instance = NULL;

        protected function __construct() {
            parent::__construct( 'bans' );
        }

        public static function get_instance() {
            if ( self::$_instance == NULL ){
                $class           = __CLASS__;
                self::$_instance = new $class;
            }
            return self::$_instance;
        }

        public function manage_bans() {
            switch ( $GLOBALS['page'] ) {
                default:
                    $this->manage_options();
                    break;
            }
        }

        public function manage_load_bans() {
            wp_enqueue_script( 'yop-poll-admin-bans-js', YOP_POLL_URL . "js/yop-poll-admin-bans.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'yop-poll-edit-ban-js', YOP_POLL_URL . "js/yop-poll-edit-ban.js", array( 'jquery' ), YOP_POLL_VERSION, true );
            wp_enqueue_script( 'yop-poll-add-edit-ban-js', YOP_POLL_URL . 'js/polls/yop-poll-add-edit.js', array( 'jquery', 'jquery-ui-sortable', 'yop-poll-admin-js' ), YOP_POLL_VERSION, true );
            wp_enqueue_style( 'yop-poll-global-admin-bans-css', YOP_POLL_URL . "css/yop-poll-admin.css", array(), YOP_POLL_VERSION );
            wp_enqueue_style( 'yop-poll-timepicker', YOP_POLL_URL . "css/timepicker.css", array(), YOP_POLL_VERSION );
            wp_enqueue_style( 'yop-poll-jquery-ui', YOP_POLL_URL . "css/jquery-ui.css", array(), YOP_POLL_VERSION );
        }

        private function manage_options() {
            global $page, $action, $message;
            switch ( $action ) {
                case 'add-ban':
                {
                    $message = $this->add_bans( $_POST );
                    break;
                }
                case 'delete':
                {
                    $message = $this->delete_bans( $_GET );
                    break;
                }
                case 'edit-ban':
                {
                    $message = $this->edit_bans( $_POST );
                    break;
                }
            }
            $this->view_bans();
        }

        private function view_bans() {
            global $wpdb, $page, $action, $orderby, $order, $period, $message;
            //load all options and display them
            $order_fields = array( 'name', 'type', 'period', 'unit' );
            $orderby      = ( empty ( $GLOBALS['orderby'] ) ) ? 'name' : $GLOBALS['orderby'];
            $order        = ( empty ( $GLOBALS['order'] ) ) ? 'desc' : $GLOBALS['order'];

            $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );
            $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );
            $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );

            $data['title']                               = __yop_poll( "Poll Bans" );
            $data['REQUEST']                             = $_REQUEST;
            $data['per_page']                            = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
            $data['page_no']                             = isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1;
            $data['orderby']                             = ( empty ( $data['REQUEST']['orderby'] ) ) ? 'name' : $data['REQUEST']['orderby'];
            $data['order_direction'] ['orderby']         = ( isset( $data['REQUEST']['order'] ) && 'desc' == $data['REQUEST']['order'] ) ? 'asc' : 'desc';
            $data['order_direction_reverse'] ['orderby'] = ( isset( $data['REQUEST']['order'] ) && 'desc' == $data['REQUEST']['order'] ) ? 'desc' : 'asc';
            $data['order_sortable']['orderby']           = 'sorted';
            $data['poll_id']                             = isset ( $_REQUEST ['poll_id'] ) ? ( int )$_REQUEST ['poll_id'] : NULL;
            $data['type']                                = isset ( $_REQUEST ['type'] ) ? $_REQUEST ['type'] : NULL;
            $data['period']                              = isset ( $_REQUEST ['period'] ) ? $_REQUEST ['period'] : NULL;
            $data['id']                                  = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : NULL;
            $data['yop_polls']                           = Yop_Poll_Model::get_polls_filter_search( 'ID', 'asc' );
            $data['search']                              = array( 'fields' => array( $wpdb->yop_poll_bans . '.value' ), 'value' => isset ( $_REQUEST ['s'] ) ? trim( $_REQUEST ['s'] ) : '' );
            $data['total_bans']                          = count( Yop_Poll_Model::get_bans_filter_search( $orderby, $order, $data['search'], $data['type'], $data['poll_id'] ) );
            $data['total_bans_pages']                    = ceil( $data['total_bans'] / $data['per_page'] );
            if ( intval( $data['page_no'] ) > intval( $data['total_bans_pages'] ) ){
                $page_no = 1;
            }
            $data['bans'] = Yop_Poll_Model::get_bans_filter_search( $orderby, $order, $data['search'], $data['type'], $data['poll_id'], ( $data['page_no'] - 1 ) * $data['per_page'], $data['per_page'] );

            $args                    = array( 'base' => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&page_no=%#%', 'total' => $data['total_bans_pages'], 'current' => max( 1, $data['page_no'] ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
            $data['pagination']      = paginate_links( $args );
            $_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );
            $data['request']['uri']  = $_SERVER["REQUEST_URI"];
            //display
            $data['message'] = array( 'error' => $message['error'], 'succes' => $message['success'] );
            $this->display( 'bans.html', $data );
        }

        public function add_bans( $request ) {
            global $wpdb, $message;
            $message['success'] = "";
            $message['error']   = "";
            if ( !isset( $request['ban_poll_id'] ) ){
                $message['error'] = __yop_poll( 'You must choose a yop poll! ' );
            }
            else {
                if ( !ctype_digit( $request['ban_poll_id'] ) ){
                    $message['error'] = __yop_poll( 'Invalid Yop Poll! Please try again! ' );
                }
                elseif ( !in_array( $request['ban_type'], array( 'ip', 'username', 'email' ) ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban type!', 'yop_poll' );
                }
                elseif ( !isset( $request['ban_period'] ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban period!', 'yop_poll' );
                }
                elseif ( !in_array( $request['ban_unit'], array( 'hours', 'days', 'weeks', 'months' ) ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban unit!', 'yop_poll' );
                }
                elseif ( '' == trim( $request['ban_value'] ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban value!', 'yop_poll' );
                }
                else {
                    $ban_textarea = nl2br( $request['ban_value'] );
                    $values       = explode( '<br />', $ban_textarea );

                    if ( count( $values ) > 0 ){
                        foreach ( $values as $value ) {
                            if ( '' != trim( $value ) ){

                                $ban = array( 'poll_id' => trim( $request['ban_poll_id'] ), 'type' => trim( $request['ban_type'] ), 'period' => trim( $request['ban_period'] ), 'unit' => trim( $request['ban_unit'] ), 'value' => trim( $value ) );
                                if ( !( self::exists( $ban ) ) ){


                                    $ban = self::insert_ban_to_database( $ban );

                                    return $ban;
                                }
                            }
                        }
                    }

                }
            }
            return $message;
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


        private static function insert_ban_to_database( $ban ) {
            global $wpdb;
            $response['success'] = "";
            $response['error']   = "";
            if ( check_admin_referer( 'yop-poll-add-edit-ban', 'yop-poll-add-edit-ban' ) ){
                if ( current_user_can( 'manage_yop_polls_bans' ) ){
                    $sql = $wpdb->query( $wpdb->prepare( "
	                INSERT INTO $wpdb->yop_poll_bans
                              ( poll_id,type,value,period ,unit)
		  	                    VALUES(%d,%s,%s,%d,%s)
	                        ", $ban['poll_id'], $ban['type'], $ban['value'], intval( $ban['period'] ), $ban['unit'] ) );
                    if ( $sql ){
                        $response['success']   = __yop_poll( 'Ban added' );
                        $response['insert_id'] = __yop_poll( 'Ban added' );

                    }
                    else {
                        $response['error'] = __yop_poll( 'Could not insert ban into database! Please try again!' );
                    }
                }
                else {
                    $response['error'] = __yop_poll( 'You do not have enough permission to add a ban' );
                }

                return $response;
            }
            else {
                wp_die( __yop_poll( 'You do not have enough permission to add a ban' ) );
            }

        }

        public function delete_bans( $request ) {
            global $message;
            $message['success'] = "";
            $message['error']   = "";

            if ( isset ( $_REQUEST ['yoppollbanscheck'] ) ){
                $bulkbans = ( array )$_REQUEST ['yoppollbanscheck'];
                foreach ( $bulkbans as $ban_id ) {
                    $ban_id  = ( int )$ban_id;
                    $message = self::delete_poll_ban_from_db( $ban_id );
                }
                return $message;
            }
            else {
                if ( isset ( $_REQUEST ['id'] ) ){
                    return self::delete_poll_ban_from_db( $request['id'] );
                }
            }

            return $message;
        }


        public function delete_poll_ban_from_db( $ban_id ) {
            global $wpdb;
            $response['success'] = "";
            $response['error']   = "";

            if ( check_admin_referer( 'yop-poll-bans', 'yop-poll-ban' ) ){
                if ( current_user_can( 'manage_yop_polls_bans' ) ){
                    $sql = $wpdb->query( $wpdb->prepare( "
					DELETE FROM  $wpdb->yop_poll_bans
					WHERE id = %d
					", $ban_id ) );
                    if ( $sql ){
                        $response['success'] = __yop_poll( 'Ban deleted' );
                        //$response['insert_id'] = __yop_poll('Ban deleted');

                    }
                    else {
                        $response['error'] = __yop_poll( 'Could not delete ban from database! Please try again!' );
                    }
                }
                else {
                    $response['error'] = __yop_poll( 'You do not have enough permission to delete a ban' );
                }
            }
            else {
                wp_die( __yop_poll( 'You do not have enough permission to delete a ban' ) );
            }
            return $response;


        }

        public function edit_bans( $request ) {
            global $wpdb, $message;
            $message['success'] = "";
            $message['error']   = "";
            if ( !isset( $request['ban_poll_id'] ) ){
                $message['error'] = __yop_poll( 'You must choose a yop poll! ' );
            }
            else {
                if ( !ctype_digit( $request['ban_poll_id'] ) ){
                    $message['error'] = __yop_poll( 'Invalid Yop Poll! Please try again! ' );
                }
                elseif ( !in_array( $request['ban_type'], array( 'ip', 'username', 'email' ) ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban type!', 'yop_poll' );
                }
                elseif ( !isset( $request['ban_period'] ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban period!', 'yop_poll' );
                }
                elseif ( !in_array( $request['ban_unit'], array( 'hours', 'days', 'weeks', 'months' ) ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban unit!', 'yop_poll' );
                }
                elseif ( '' == trim( $request['ban_value'] ) ) {
                    $message['error'] = __yop_poll( 'You must choose a ban value!', 'yop_poll' );
                }
                else {
                    $ban_textarea = nl2br( $request['ban_value'] );
                    $values       = explode( '<br />', $ban_textarea );

                    if ( count( $values ) > 0 ){
                        foreach ( $values as $value ) {
                            if ( '' != trim( $value ) ){

                                $ban = array( 'poll_id' => trim( $request['ban_poll_id'] ), 'id' => trim( $request['retain_id'] ), 'type' => trim( $request['ban_type'] ), 'period' => trim( $request['ban_period'] ), 'unit' => trim( $request['ban_unit'] ), 'value' => trim( $value ) );
                                $ban = self::edit_ban_from_database( $ban );

                                return $ban;

                            }
                        }
                    }

                }
            }
            return $message;
        }


        public function edit_ban_from_database( $ban ) {
            global $wpdb;
            $response['success'] = "";
            $response['error']   = "";

            if ( check_admin_referer( 'yop-poll-add-edit-ban', 'yop-poll-add-edit-ban' ) ){
                if ( current_user_can( 'manage_yop_polls_bans' ) ){
                    $sql = $wpdb->query( $wpdb->prepare( "UPDATE  $wpdb->yop_poll_bans SET type = %s ,value = %s ,period= %d  ,unit=%s,poll_id = %d WHERE id = %d", $ban['type'], $ban['value'], intval( $ban['period'] ), $ban['unit'], $ban['poll_id'], $ban['id'] ) );
                    if ( $sql == "" | $sql ){
                        $response['success']   = __yop_poll( 'Ban edited' );
                        $response['insert_id'] = __yop_poll( 'Ban edited' );

                    }
                    else {
                        $response['error'] = __yop_poll( 'Could not edit ban from database! Please try again!' );
                    }
                }
                else {
                    $response['error'] = __yop_poll( 'You do not have enough permission to edit a ban' );
                }

                return $response;


            }
            else {
                wp_die( __yop_poll( 'You do not have enough permission to edit a ban' ) );
            }


        }
    }