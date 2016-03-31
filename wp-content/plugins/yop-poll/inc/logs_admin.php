<?php

class YOP_POLL_Logs_Admin extends YOP_POLL_Abstract_Admin{
    private static $_instance = NULL;

    protected function __construct() {
        parent::__construct( 'logs' );
    }

    public static function get_instance() {
        if ( self::$_instance == NULL ){
            $class           = __CLASS__;
            self::$_instance = new $class;
        }
        return self::$_instance;
    }

    public function manage_logs() {

        switch ( $GLOBALS['page'] ) {
            default:
                $this->manage_options();
                break;
        }

    }

    public function admin_loader() {

    }


    public function manage_load_logs() {

        wp_enqueue_style( 'yop-poll-timepicker', YOP_POLL_URL . "css/timepicker.css", array(), YOP_POLL_VERSION );
        wp_enqueue_style( 'yop-poll-jquery-ui', YOP_POLL_URL . "css/jquery-ui.css", array(), YOP_POLL_VERSION );

        if(isset($_REQUEST['a']))
        {if($_REQUEST['a']=="Export")
            self::export_logs();

        }

    }

    private function manage_options() {
        global $page, $action, $message;
        switch ( $action ) {

            case 'delete_group':
            {
                $message = $this->delete_logs();
                break;
            }
        }


        $this->view_logs();
    }
    public function export_logs(){
        global $wpdb, $page, $action, $orderby, $order, $current_user,$message;
        $data['title']                               = __yop_poll( "Logs" );
        $data['request']                             = $_REQUEST;
        $data['s']                         =( isset ( $_REQUEST ['s'] ) ? intval( $_REQUEST ['s'] ) :"" );
        $data['per_page']                   = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
        $data['page_no']                    = isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1;
        $data['order']                    = isset ( $_REQUEST ['order'] ) ? ( int )$_REQUEST ['order'] : 'ASC';
        $order_fields = array( 'vote_id','vote_date' );
        $orderby  = ( empty ( $GLOBALS['orderby'] ) ) ? 'vote_id' : $GLOBALS['orderby'];
        $orderby  = ( empty ( $GLOBALS['orderby'] ) ) ? 'name' : $GLOBALS['orderby'];
        $order    = ( empty ( $GLOBALS['order'] ) ) ? 'desc' : $GLOBALS['order'];

        $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );
        $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );
        $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );
        $data['poll_id']                   = isset ( $_REQUEST ['poll_id'] ) ? ( int )$_REQUEST ['poll_id'] : NULL;
        $args['limit']         = ( ($data['page_no'] )-1) * $data['per_page'] . ', ' . $data['per_page'];
        $data['log_sdate'] = ( isset ( $_GET ['log_sdate'] ) ? $_GET ['log_sdate'] : '' );
        $data['log_edate'] = ( isset ( $_GET ['log_edate'] ) ? $_GET ['log_edate'] : '' );
        $data['group_by']  = ( isset ( $_GET ['group_by'] ) ? $_GET ['group_by'] : 'vote' );
        $data['message']= $message ;
        $filters   = array();
        $filters[] = array( 'field' => 'poll_id', 'value' => '0', 'operator' => '=' );

        if ( $_REQUEST ['export']!="all"  )
        {
            $filters   = array();
            $filters[] = array( 'field' => 'poll_id', 'value' => '0', 'operator' => '=' );
            if($data['log_sdate']!=""&&$data['log_edate']!="")
                $poll_id=   self::get_poll_id_by_start_date_end_date($data['log_sdate'],$data['log_edate']);
            else
                if($data['log_sdate']!="")
                    $poll_id=self::get_poll_id_by_start_date_end_date($data['log_sdate']);
                else
                    $poll_id=   self::get_poll_id_by_start_date_end_date("0000-00-00",$data['log_edate']);
            if(isset($_REQUEST['s'])){
                $poll_id=self::get_poll_id_by_name($_REQUEST['s']);

            }

            if(isset($data['poll_id']))
                $filters[0]['value']=$_REQUEST['poll_id'];
            if($_REQUEST['s']!=""||$data['log_sdate']!=""||$data['log_edate']!=""){

                $args = array(
                    'search'        => array( 'fields' => array( 'poll_id' ), 'value' => isset ( $poll_id ) ? $poll_id : '' ), 'orderby' => $orderby, 'order' => $order );

            }

            else
                $args = array(
                    'filters'       => $filters,'orderby' => $orderby, 'order' => $order);

            $args['limit']= ( $data['page_no'] - 1 ) * $data['per_page']  .',' .$data['per_page'];

            $data['logs']=self::get_polls_logs_filter_search($args);
        }
        else
            $data['logs']=self::get_polls_logs_filter_search("ID");

        foreach ($data['logs'] as &$user_detail){
            if($user_detail['user_type']!="anonymous")
            {
                if($user_detail['user_type']=="wordpress"){
                    $details=self::get_polls_user_details_from_db($user_detail['user_id']);
                    $user_detail['user_nicename']=$details[0]['user_nicename'];
                    $user_detail['user_email']=$details[0]['user_email'];
                }
                else{
                    $user_detail['user_nicename']="";
                    $user_detail['user_email']="";
                    $user_details=json_decode($user_detail['user_details']);
                    $detail=$user_details->user;
                    $user_detail['user_nicename'].=$detail->user_name;
                    $user_detail['user_email'].=$detail->user_email;}

            }else{
                $user_detail['user_nicename']="";
                $user_detail['user_email']="";
            }
            $user_detail['answer']="";
            $vote_details=json_decode($user_detail['vote_details']);
            foreach ($vote_details as $question)
                foreach($question->a as $answers)
                    $user_detail['answer'].=$answers."\n";
            $detail=self::get_poll_name_from_db($user_detail['poll_id']);
            $user_detail['name']= $detail[0]->poll_name;

        }
        $args['return_fields']= 'COUNT(*) as total_logs';
        $args['limit']="";
        $data['yop_polls'] =self::get_yop_polls_filter_search( 'id', 'asc' );
        $total_logs=self::get_polls_logs_filter_search($args);
        if ( isset ( $_REQUEST ['export'] ) ) {
            global $wpdb;
            $csv_file_name    = 'logs_export.' . date( 'YmdHis' ) . '.csv';
            $csv_header_array = array( __( '#', 'yop_poll' ), __( 'Vote ID', 'yop_poll' ), __( 'POLL Name', 'yop_poll' ), __( 'Answer', 'yop_poll' ), __( 'User Type', 'yop_poll' ), __( 'User', 'yop_poll' ), __( 'User Email', 'yop_poll' ), __( 'Tracking ID', 'yop_poll' ), __( 'IP', 'yop_poll' ), __( 'Vote Date', 'yop_poll' ) );
            header("Content-Type: text/csv");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Content-Transfer-Encoding: binary\n");
            header( 'Content-Disposition: attachment; filename="' . $csv_file_name . '"' );
            ob_start();
            $f = fopen( 'php://output', 'w' ) or show_error( __( "Can't open php://output!", 'yop_poll' ) );

            if ( !fputcsv( $f, $csv_header_array ) ) _e( "Can't write header!", 'yop_poll' );

            if ( count( $data['logs'] ) > 0 ){
                $index = 1;
                foreach ( $data['logs'] as $log ) {
                    $logs_data = array( $index, $log ['vote_id'], stripslashes( $log ['name'] ), ( 'Other' == $log ['answer'] ) ? 'Other - ' . stripslashes( $log ['other_answer_value'] ) : stripslashes( $log ['answer'] ), stripslashes( $log ['user_type'] ), stripslashes( $log ['user_nicename'] ), stripslashes( $log ['user_email'] ), stripslashes( $log ['tr_id'] ), stripslashes( $log ['ip'] ), stripslashes( $log['vote_date'] ) );
                    if ( !fputcsv( $f, $logs_data ) ) _e( "Can't write header!", 'yop_poll' );
                    $index++;
                }
            }

            fclose( $f ) or show_error( __( "Can't close php://output!", 'yop_poll' ) );
            $csvStr = ob_get_contents();
            ob_end_clean();

            echo $csvStr;
            exit ();


        }



    }
    private function view_logs(){
        $time_format="H:i:s";
        $options                     = get_option('yop_poll_options' );
        if($options['date_format']=="UE")
            $date_format="d-m-Y";            else{
            $date_format="m-d-Y";
        }
        $data['date_format']=$date_format.' '.$time_format;
        global $wpdb, $page, $action, $orderby, $order, $current_use,$message;
        $data['title']                               = __yop_poll( "Logs" );
        $data['request']                             = $_REQUEST;
        $data['s']                         =( isset ( $_REQUEST ['s'] ) ? intval( $_REQUEST ['s'] ) :"" );
        $data['per_page']                   = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
        $data['page_no']                    = isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1;
        $data['order']                    = isset ( $_REQUEST ['order'] ) ? ( int )$_REQUEST ['order'] : 'ASC';
        $order_fields = array( 'vote_id','vote_date' );
        $orderby  = ( empty ( $GLOBALS['orderby'] ) ) ? 'vote_id' : $GLOBALS['orderby'];
        $orderby  = ( empty ( $GLOBALS['orderby'] ) ) ? 'name' : $GLOBALS['orderby'];
        $order    = ( empty ( $GLOBALS['order'] ) ) ? 'desc' : $GLOBALS['order'];

        $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );
        $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );
        $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );
        $data['poll_id']                   = isset ( $_REQUEST ['poll_id'] ) ? ( int )$_REQUEST ['poll_id'] : NULL;
        $args['limit']         = ( ($data['page_no'] )-1) * $data['per_page'] . ', ' . $data['per_page'];
        $data['log_sdate'] = ( isset ( $_GET ['log_sdate'] ) ? $_GET ['log_sdate'] : '' );
        $data['log_edate'] = ( isset ( $_GET ['log_edate'] ) ? $_GET ['log_edate'] : '' );
        $data['group_by']  = ( isset ( $_GET ['group_by'] ) ? $_GET ['group_by'] : 'vote' );
        $order_fields = array( 'ID', 'poll_id', 'vote_id', 'ip', 'user_id','tr_id','vote_details','user_details','vote_date' );
        $data['message']= $message ;
        $filters   = array();
        $filters[] = array( 'field' => 'poll_id', 'value' => '0', 'operator' => '=' );
        if($data['log_sdate']!=""&&$data['log_edate']!="")
            $poll_id=   self::get_poll_id_by_start_date_end_date($data['log_sdate'],$data['log_edate']);
        else
            if($data['log_sdate']!="")
                $poll_id=self::get_poll_id_by_start_date_end_date($data['log_sdate']);
            else
                $poll_id=   self::get_poll_id_by_start_date_end_date("0000-00-00",$data['log_edate']);
        if(isset($_REQUEST['s'])){
            $poll_id=self::get_poll_id_by_name($_REQUEST['s']);

        }

        if(isset($data['poll_id']))
            $filters[0]['value']=$_REQUEST['poll_id'];
        if( ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] !="" ) || $data['log_sdate'] !="" || $data['log_edate']!="" ){

            $args = array(
                'search'        => array( 'fields' => array( 'poll_id' ), 'value' => isset ( $poll_id ) ? $poll_id : '' ), 'orderby' => $orderby, 'order' => $order );

        }
        else
            $args = array(
                'filters'       => $filters, 'orderby' => $orderby, 'order' => $order);
        $total_logs=self::get_polls_logs_filter_search($args);
        $data['total_logs']= count($total_logs);
        $data['total_logs_pages'] = ceil(   $data['total_logs'] / $data['per_page'] );
        if ( intval( $data['page_no'] ) > intval( $data['total_logs_pages'] ) )
            $data['page_no'] = 1;
        $args['limit']= ( $data['page_no'] - 1 ) * $data['per_page']  .',' .$data['per_page'];
        $data['logs']=self::get_polls_logs_filter_search($args);

        foreach ($data['logs'] as &$user_detail){
            if($user_detail['user_type']!="anonymous")
            {
                if($user_detail['user_type']=="wordpress"){
                    $details=self::get_polls_user_details_from_db($user_detail['user_id']);
                    $user_detail['user_nicename']=$details[0]['user_nicename'];
                    $user_detail['user_email']=$details[0]['user_email'];
                }
                else{
                    $user_detail['user_nicename']="";
                    $user_detail['user_email']="";
                    $user_details=json_decode($user_detail['user_details']);
                    $detail=$user_details->user;
                    $user_detail['user_nicename'].=$detail->user_name;
                    $user_detail['user_email'].=$detail->user_email;}


            }
            else{
                $user_detail['user_nicename']="";
                $user_details=json_decode($user_detail['user_details']);
                if(isset($user_details['email']))
                $user_detail['user_email']=$user_details['email'];
            }
            $user_detail['answer']="";
            $vote_details=json_decode($user_detail['vote_details']);
            if(isset($vote_details))
            foreach ($vote_details as $question){
                $user_detail['answer'].= $question->question;
                $user_detail['answer'].=": ";
                foreach($question->answers as $answers){
                    $user_detail['answer'].=$answers;
                    $user_detail['answer'].=', ';
                }
                nl2br($user_detail['answer'].'\r\n',true);
                nl2br($user_detail['answer'].'\r\n',true);
            }
            $detail=self::get_poll_name_from_db($user_detail['poll_id']);
            $user_detail['name']= $detail[0]->poll_name;

        }

        $args['return_fields']= 'COUNT(*) as total_logs';
        $args['limit']="";
        $data['yop_polls'] =self::get_yop_polls_filter_search( 'id', 'asc' );

        $total_logs=self::get_polls_logs_filter_search($args);



        $data['total_logs']= $total_logs[0]['total_logs'];
        $data['total_logs_pages'] = ceil(  $total_logs[0]['total_logs'] / $data['per_page'] );
        if ( intval( $data['page_no'] ) > intval( $data['total_logs_pages'] ) )
            $data['page_no'] = 1;

        $paginate_args           = array( 'base' => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&page_no=%#%', 'total' => $data['total_logs_pages'], 'current' => max( 1, $data['page_no'] ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
        $_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );
        $data['pagination'] = paginate_links( $paginate_args );
        $this->display('logs.html',$data);
    }

    public static function get_yop_polls_filter_search( $orderby = 'id', $order = 'desc', $filter = array( 'field' => NULL, 'value' => NULL, 'operator' => '=' ), $search = array( 'fields' => array(), 'value' => NULL ) ) {
        global $wpdb;
        $sql        = "SELECT * FROM " . $wpdb->yop_polls;
        $sql_filter = '';
        $sql_search = '';
        if ( $filter['field'] && $filter['value'] ){
            $sql_filter .= $wpdb->prepare( ' `' . esc_attr( $filter['field'] ) . '` ' . esc_attr( $filter['operator'] ) . ' %s ', esc_attr( $filter['value'] ) );
        }
        if ( count( $search['fields'] ) > 0 ){
            if ( $filter['field'] && $filter['value'] ) $sql_search = ' AND ';
            $sql_search .= ' ( ';
            foreach ( $search['fields'] as $field ) {
                $sql_search .= $wpdb->prepare( ' `' . esc_attr( $field ) . '` like \'%%%s%%\' OR', $search['value'] );
            }
            $sql_search = trim( $sql_search, 'OR' );
            $sql_search .= ' ) ';
        }
        if ( ( $filter['field'] && $filter['value'] ) || count( $search['fields'] ) > 0 ) $sql .= ' WHERE ' . $sql_filter . $sql_search;
        $sql .= ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order );
        return $wpdb->get_results( $sql, ARRAY_A );
    }

    private function delete_logs(){
        global $message;
        $message['success'] = "";
        $message['error']   = "";
        if( check_admin_referer('yop-poll-logs','yop-poll-log') ) {
            if ( isset ( $_REQUEST ['yoppolllogscheck'] ) ){
                $bulklogs = ( array )$_REQUEST ['yoppolllogscheck'];
                foreach ( $bulklogs as $log_id ) {
                    $log_id = ( int )$log_id;
                    $message     = self::delete_log_from_db( $log_id );
                }
                return $message;
            }

            else {

                if ( isset ( $_REQUEST ['id'] ) ){
                    return self::delete_log_from_db( $_REQUEST['id'] );
                }
            }


            return $message;
        }
        else {
            wp_die( __yop_poll('You do not have enough permission to delete a log'));

        }
    }
    private function delete_log_from_db( $log_id ) {
        global $wpdb;
        $response['success'] = "";
        $response['error']   = "";

        if ( current_user_can( 'delete_yop_polls_logs' ) ){
            $sql = $wpdb->query( $wpdb->prepare( "
						DELETE FROM  $wpdb->yop_poll_logs
						WHERE id = %d",
                $log_id ) );
            if ( $sql ){
                $response['success'] = __yop_poll( 'Log deleted' );

            }

            else {
                $response['error'] = __yop_poll( 'Could not delete log from database! Please try again!' );
            }
        }
        else {
            $response['error'] = __yop_poll( 'You do not have enough permission to delete a log!' );
        }



        return $response;


    }

    public static function get_polls_logs_filter_search( $args ) {
        $return_fields = '*';
        $filters       = NULL;
        $search        = NULL;
        $orderby       = 'ID';
        $order         = 'ASC';
        $limit         = NULL;
        if ( isset( $args['return_fields'] ) ){
            $return_fields = trim( $args['return_fields'], ',' );
        }
        if ( isset( $args['filters'] ) ){
            $filters = $args['filters'];
        }
        if ( isset( $args['search'] ) ){
            $search = $args['search'];
        }
        if ( isset( $args['orderby'] ) ){
            $orderby = $args['orderby'];
        }
        if ( isset( $args['order'] ) ){
            if ( in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ) ) ){
                $order = $args['order'];
            }
        }
        if ( isset( $args['limit'] ) ){
            $limit = $args['limit'];
        }

        $sql        = 'SELECT ' . $return_fields . ' FROM ' . $GLOBALS['wpdb']->yop_poll_logs . ' WHERE 1=1 ';
        $sql_filter = '';

        if ( count( $filters)>0  ){
            foreach ( $filters as $filter ) {
                if($filter['value']>0)
                    $sql_filter .= ' AND ' . $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $filter['field'] ) . '` ' . $filter['operator'] . ' %s ', esc_attr( $filter['value'] ) ) . ' ';

            }
        }

        $sql_search = '';
        if ( count( $search['fields'] ) > 0 ){
            foreach ( $search['value'] as $field ) {
                $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $search['fields'][0] ) . '` ' . "=" . ' %s', esc_attr($field->ID) ) . ' '.'OR';

            }
            $sql_search = ' AND ( ' . trim( $sql_search, 'OR' ) . ' ) ';
        }

        $sql_order_by = ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order ) . ' ';
        $sql_limit    = '';
        if ( $limit ){
            $sql_limit = ' LIMIT ' . $limit . ' ';
        }
        return $GLOBALS['wpdb']->get_results( $sql . $sql_filter . $sql_search . $sql_order_by . $sql_limit, ARRAY_A );
    }

    public function  get_polls_user_details_from_db($user_id)
    {                       global $wpdb;
        return $wpdb->get_results($wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->users . "
					WHERE ID = %d
					", $user_id . '%' ),ARRAY_A);


    }
    public function get_poll_name_from_db($poll_id){
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare( "
					SELECT poll_name
					FROM " . $wpdb->yop_polls . "
					WHERE ID = %d
					", $poll_id . '%' ));

    }
    public function get_poll_id_by_name($poll_name)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare( "
					SELECT ID
					FROM " . $wpdb->yop_polls . "
					WHERE poll_name LIKE %s
					", $poll_name . '%' ));
    }
    public function get_poll_id_by_start_date_end_date($start_date="2014-02-03",$end_date="01-01-2038 23:59:59")
    {$start_date.=" 00:00:00";
        $end_date.=" 23:59:59";
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare( "
					SELECT ID
					FROM " . $wpdb->yop_polls . "
					WHERE poll_start_date>=%s AND poll_end_date<=%s
					", $start_date,$end_date .'%' ));
    }

}

?>