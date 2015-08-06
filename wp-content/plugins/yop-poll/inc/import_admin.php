<?php

    class YOP_POLL_Imports_Admin extends YOP_POLL_Abstract_Admin{

        private static $_instance = NULL;



        protected function __construct() {

            parent::__construct( 'imports' );

        }



        public static function get_instance() {

            if ( self::$_instance == NULL ){

                $class           = __CLASS__;

                self::$_instance = new $class;

            }

            return self::$_instance;

        }



        public function manage_imports() {

            switch ( $GLOBALS['page'] ) {

                default:

                    $this->manage_options();

                    break;

            }



        }

        public function manage_load_imports() {

            wp_enqueue_style( 'yop-poll-timepicker', YOP_POLL_URL . "css/timepicker.css", array(), YOP_POLL_VERSION );

            wp_enqueue_style( 'yop-poll-jquery-ui', YOP_POLL_URL . "css/jquery-ui.css", array(), YOP_POLL_VERSION );

        }



        private function manage_options() {

            global $page, $action, $message;

            switch ( $action ) {



                case 'import-all':

                {

                    $this->import_wp_polls();


                    break;

                }

                case 'import':

                {

                    $this->import_wp_poll();

                    break;

                }

                default:

                    {
                    self:: view_wp_polls();

                    }





            }



        }

        private function import_wp_poll() {


            if ( !current_user_can( 'edit_own_yop_polls' ) && ( !current_user_can( 'edit_yop_polls' ))  ){

                wp_die( __yop_poll( 'You are not allowed to edit this item.' ) );

            }

            else

                if( check_admin_referer('yop-poll-import', 'yop-poll-imports') ) {

                    if (isset ($_REQUEST['id']))

                    {

                        self::import_wp_poll_fom_db($_REQUEST['id']);

                    }

                    else if ( isset ( $_REQUEST ['yoppollwppollcheck'] ) ){

                        $wp_poll = ( array )$_REQUEST ['yoppollwppollcheck'];

                        foreach ( $wp_poll as $wp_id ) {

                            $wp_id   = ( int )$wp_id ;

                            $message =  self::import_wp_poll_fom_db($wp_id);

                        }

                    }

                }

                else {

                    wp_die( __yop_poll('You do not have enough permission to import a Wp-poll'));

                }

            self:: view_wp_polls();

        }

        private  function import_wp_poll_fom_db($id)

        {
            $current_date= date('Y/m/d H:i:s');

            $wp_polls=self::get_wp_poll_search_by_id($id );
            global $message;


            foreach ($wp_polls as $wp_poll)

            {
                $poll   = new YOP_POLL_Poll_Model(0);

                $answers= self::get_wp_poll_answers_from_db_by_id($wp_poll['pollq_id']);

                $poll->poll_title=$wp_poll['pollq_question'];

                $poll->poll_name=$wp_poll['pollq_question'];

                $poll->poll_author=get_current_user_id();

                $poll->poll_start_date=date("Y-m-d H:i:s", $wp_poll['pollq_timestamp']);
                if($wp_poll['pollq_multiple']>0){
                    $poll->allow_multiple_answers="yes";
                    $poll->allow_multiple_answers_max_number=$wp_poll['pollq_multiple'];

                }

                if($wp_poll['pollq_expiry']!="")

                    $poll->poll_end_date= date("Y-m-d H:i:s",  $wp_poll['pollq_expiry']);

                else

                    $poll->poll_end_date="01-01-2038 23:59:59";

                $poll->poll_status="active";

                $poll->poll_date=$current_date;

                $poll->poll_modified=$current_date;

                $poll->poll_total_votes=$wp_poll['pollq_totalvotes'];

                $question= new YOP_POLL_Question_Model();



                $poll_id=$poll->save();



                $question->poll_id=$poll_id;

                $question->question=$wp_poll['pollq_question'];

                $question->question_status="active";

                $question->type="text";
                if($wp_poll['pollq_multiple']>0){
                    $question->allow_multiple_answers="yes";
                    $question->allow_multiple_answers_max_number=$wp_poll['pollq_multiple'];

                }

                $question->question_author=get_current_user_id();

                $question->poll_order=1;

                $question->question_date =$current_date;

                $question->question_modified=$current_date;



                $question->save();



                foreach($answers as $answer){



                    $poll_answer =new YOP_POLL_Answer_Model();

                    $poll_answer->poll_id=$poll_id;

                    $poll_answer->question_id=$question->ID;

                    $poll_answer->answer=$answer['polla_answers'];

                    $poll_answer->answer_status="active";

                    $poll_answer->type="text";

                    $poll_answer->question_order=1;

                    $poll_answer->answer_author=get_current_user_id();

                    $poll_answer->votes=$answer['polla_votes'];

                    $poll_answer->answer_date=$current_date;

                    $poll_answer->answer_modified=$current_date;



                    $poll_answer->save();

                    unset($log_details);

                    $log_details= self::get_log_from_db_by_poll_id($wp_poll['pollq_id'],$answer['polla_aid']);

                    foreach($log_details as $log)

                    {

                        $arg['poll_id']=$poll->ID;

                        $arg['ip']=$log['pollip_ip'];

                        if($log['pollip_user']=='Guest'){

                            $arg['user_type']='anonymous';

                        }else

                            $arg['user_type']="wordpress";

                        $arg['user_id']=$log['pollip_userid'];

                        $arg['vote_date']=$log['pollip_timestamp'];

                        $a  = self::get_answer_from_db_by_id($log['pollip_aid']);

                        $arg[1]["q-".$question->ID ]['a'][0]= $poll_answer->ID;

                        $arg[1]["q-".$question->ID ]['answers'][0]=$a[0]['polla_answers'];

                        $q  = self::get_question_from_db_by_id($log['pollip_qid']);

                        $arg[1]["q-".$question->ID ]['question']=$q[0]['pollq_question'];

                        $arg['vote_details']=json_encode($arg[1]);

                        $this->insert_log_in_db($arg);


                        unset($arg);

                    }

                }



            }
            if($poll_id){
                $message['success']="Poll  imported!" ;
            }
            else
                $message['error']="Could not import ban from database! Please try again!"   ;

        }



        private function  import_wp_polls()

        {

            if( check_admin_referer('yop-poll-import', 'yop-poll-imports') ) {

                if(current_user_can('import_wp_polls')){

                    $current_date= date('Y/m/d H:i:s');

                    $wp_polls=self::get_wp_polls_from_db();

                    foreach ($wp_polls as $wp_poll)

                    {

                        $poll              = new YOP_POLL_Poll_Model(0);

                        $answers           = self::get_wp_poll_answers_from_db_by_id($wp_poll->pollq_id);

                        $poll->poll_title  =$wp_poll->pollq_question;

                        $poll->poll_name   =$wp_poll->pollq_question;

                        $poll->poll_author =get_current_user_id();

                        $poll->poll_start_date=date("Y-m-d H:i:s", $wp_poll['pollq_timestamp']);

                        if($wp_poll['pollq_expiry']!="")

                            $poll->poll_end_date = date("Y-m-d H:i:s",  $wp_poll['pollq_expiry']);

                        else

                            $poll->poll_end_date  ="01-01-2038 23:59:59";

                        $poll->poll_status        ="active";

                        $poll->poll_date          =$current_date;

                        $poll->poll_modified      =$current_date;

                        $poll->poll_total_votes   =$wp_poll->pollq_totalvotes;

                        $question                 = new YOP_POLL_Question_Model();



                        $poll_id=$poll->save();



                        $question->poll_d                =$poll_id;

                        $question->question              =$wp_poll->pollq_question;

                        $question->question_status       ="active";

                        $question->question_author       =get_current_user_id();

                        $question->poll_order            =1;

                        $question->question_date         =$current_date;

                        $question->question_modified     =$current_date;



                        $question->save();



                        foreach($answers as $answer){

                            $poll_answer                 =new YOP_POLL_Answer_Model();

                            $poll_answer->poll_id        =$poll_id;

                            $poll_answer->question_id    =$question->ID;

                            $poll_answer->answer         =$answer['polla_answers'];

                            $poll_answer->answer_status  ="active";

                            $poll_answer->question_order =1;

                            $poll_answer->answer_author  =get_current_user_id();

                            $poll_answer->votes          =$answer['polla_votes'];

                            $poll_answer->answer_date     =$current_date;

                            $poll_answer->answer_modified =$current_date;



                            $poll_answer->save();

                            unset($log_details);

                            $log_details= self::get_log_from_db_by_poll_id($wp_poll->pollq_id,$answer['polla_aid']);

                            foreach($log_details as $log)

                            {

                                $arg['poll_id']   =$poll->ID;

                                $arg['ip']        =$log['pollip_ip'];

                                if($log['pollip_user']=='Guest'){

                                    $arg['user_type']='anonymous';

                                }else

                                    $arg['user_type']  ="wordpress";

                                $arg['user_id']        =$log['pollip_userid'];

                                $arg['vote_date']      =$log['pollip_timestamp'];

                                $a                     =self::get_answer_from_db_by_id($log['pollip_aid']);

                                $arg[1]["q-".$question->ID ]['a'][0]= $poll_answer->ID;

                                $arg[1]["q-".$question->ID ]['answers'][0]=$a[0]['polla_answers'];

                                $q                     = self::get_question_from_db_by_id($log['pollip_qid']);

                                $arg[1]["q-".$question->ID ]['question']=$q[0]['pollq_question'];

                                $arg['vote_details']     =json_encode($arg[1]);

                                $message=$this->insert_log_in_db($arg);


                                if($message['error']!=""|| $message['success']!="")

                                    $data['message']=$message;


                                unset($arg);

                            }

                        }

                    }

                }

                else

                    wp_die( __yop_poll('You do not have enough permission to import a Wp-poll'));

            }

            else {

                wp_die( __yop_poll('You do not have enough permission to import a Wp-poll'));

            }



            $data['title']=__yop_poll("Import Polls from Wp-Poll");



            $this->display('imports.html',$data);

        }

        public function get_wp_polls_from_db(){



            global $wpdb;

            return $wpdb->get_results($wpdb->prepare( "

					SELECT *

					FROM " . $wpdb->pollsq . "

					WHERE 1=1 ORDER BY pollq_id

					"));

        }



        public function get_wp_poll_answers_from_db_by_id($poll_id)

        {    global $wpdb;

            return $wpdb->get_results($wpdb->prepare( "

					SELECT *

					FROM " . $wpdb->pollsa . "

					WHERE polla_qid = %d

					", $poll_id . '%' ),ARRAY_A);



        }



        public function get_log_from_db_by_poll_id($poll_id,$a_id)

        {global $wpdb;

            return $wpdb->get_results($wpdb->prepare( "

					SELECT *

					FROM " . $wpdb->pollsip . "

					WHERE pollip_qid = %d AND pollip_aid =%d

					", $poll_id ,$a_id . '%' ),ARRAY_A);



        }



        public function get_answer_from_db_by_id($poll_id)

        {global $wpdb;

            return $wpdb->get_results($wpdb->prepare( "

					SELECT *

					FROM " . $wpdb->pollsa . "

					WHERE polla_aid = %d

					", $poll_id . '%' ),ARRAY_A);



        }

        public function get_question_from_db_by_id($poll_id)

        {global $wpdb;

            return $wpdb->get_results($wpdb->prepare( "

					SELECT *

					FROM " . $wpdb->pollsq . "

					WHERE pollq_id = %d

					", $poll_id . '%' ),ARRAY_A);



        }

        public function insert_log_in_db( $log ){

            global $wpdb;



            $response['success'] = "";

            $response['error']   = "";

            define( 'DIEONDBERROR', true );



            $sql = $wpdb->query( $wpdb->prepare( "

				INSERT INTO {$wpdb->yop_poll_logs} (

				poll_id,

				ip,

				user_id,

				user_type,

				vote_details,

				vote_date

				) VALUES ( %d, %s, %d, %s, %s, %s )",

                                                 $log['poll_id'],

                                                 $log['ip'],

                                                 $log['user_id'],

                                                 $log['user_type'],

                                                 $log['vote_details'],

                                                 $log['vote_date']

                                 ));

            if ( $sql ){

                $response['success']   = __yop_poll( 'Polls imported!' );

                $response['insert_id'] = $wpdb->insert_id;

            }

            else {

                $response['error'] = __yop_poll( 'Could not import polls into database!' );

            }

            return $response;





        }

        private function view_wp_polls(){
            global $wpdb;

            if( $wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "pollsq'") === $wpdb->prefix . 'pollsq' ) {
                $data['REQUEST']  = $_REQUEST;


                $voter['poll_id']=7;

                $voter['user_id']=1;

                $voter['user_type']="wordpress";

                global $wpdb, $message;

                global $page, $action, $orderby, $order, $current_user;

                $orderby = ( empty ( $GLOBALS['orderby'] ) ) ? 'pollq_question' : $GLOBALS['orderby'];

                $order   = ( empty ( $GLOBALS['order'] ) ) ? 'desc' : $GLOBALS['order'];

                $data['per_page']                            = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );

                $data['page_no']                             = isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1;

                $order_fields     = array( 'pollq_question', 'pollq_totalvoters' );



                $data['orderby']                 = ( empty ( $data['REQUEST']['orderby'] ) ) ? 'pollq_question' : $data['REQUEST']['orderby'];

                $data['order']                   = ( empty ( $data['REQUEST']['order'] ) ) ? 'desc' : $data['REQUEST']['order'];

                $data['order_direction']         = $this->make_order_array( $order_fields, 'asc', $orderby, ( 'desc' == $order ) ? 'asc' : 'desc' );

                $data['order_sortable']          = $this->make_order_array( $order_fields, 'sortable', $orderby, 'sorted' );

                $data['order_direction_reverse'] = $this->make_order_array( $order_fields, 'desc', $orderby, ( 'desc' == $order ) ? 'desc' : 'asc' );

                $data['search']                              = array( 'fields' => array( 'pollq_question' ), 'value' => isset ( $_REQUEST ['s'] ) ? trim( $_REQUEST ['s'] ) : '' );

                $data['wp_polls']      = self::get_wp_poll_search( $data['orderby'], $data['order'], $data['search'], $data['page_no'], $data['per_page'] );

                foreach($data['wp_polls']as &$wp_poll)

                {

                    $wp_poll['pollq_timestamp']=date("Y-m-d H:i:s", $wp_poll['pollq_timestamp']);

                    $wp_poll['pollq_expiry']=date("Y-m-d H:i:s",  $wp_poll['pollq_expiry']);



                }

                $data['total_wp_polls']              = self::count_wp_search( $data['orderby'], $data['order'], $data['search'] );

                $data['total_polls']              =  $data['total_wp_polls'][0]['poll_no'];

                $data['message']                 = array( 'error' => $message['error'], 'success' => $message['success'] );

                if ( intval( $data['page_no'] ) > intval( $data['total_polls'] ) ){

                    $data['page_no'] = 1;

                }

                $args = array(

                    'base'      => remove_query_arg(

                            'page_no',

                            $_SERVER ['REQUEST_URI'] ) . '%_%',

                    'format'    => '&page_no=%#%',

                    'current'   => max( 1, $data['page_no'] ),

                    'total'     => ceil( $data['total_wp_polls'][0]['poll_no'] / $data['per_page'] ),

                    'prev_next' => true,

                    'prev_text' => __( '&laquo;' ),

                    'next_text' => __( '&raquo;' )

                );



                $data['pagination']      = paginate_links( $args );



                $_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );

                $data['request']['uri']  = $_SERVER["REQUEST_URI"];


            }
            $data['title']=__yop_poll("Import Polls from Wp-Poll");

            $this->display('imports.html',$data);

        }

        public static function get_wp_poll_search( $orderby = 'pollq_question', $order = 'desc', $search = array( 'fields' => array(), 'value' => NULL ), $offset = 0 , $per_page = 100 ,$poll_id = NULL ) {

            global $wpdb;

            $sql        = "SELECT * FROM " . $wpdb->pollsq;



            $sql_search = '';

            if ( $poll_id ){

                $sql_search .= $wpdb->prepare( ' WHERE pollq_id = %d', $poll_id );

            }





            if ( count( $search['fields'] ) > 0 ){



                $sql_search .= ' WHERE (';



                foreach ( $search['fields'] as $field ) {

                    $sql_search .= $wpdb->prepare( ' ' . esc_attr( $field ) . ' like \'%%%s%%\' OR', $search['value'] );

                }

                $sql_search = trim( $sql_search, 'OR' );

                $sql_search .= ' ) ';

            }

            $sql .= $sql_search;

            $sql_order_by = ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order ) . ' ';

            $sql .= $sql_order_by;

            $sql .= $wpdb->prepare( ' LIMIT %d, %d', (($offset-1) * $per_page), $per_page );

            return $wpdb->get_results( $sql, ARRAY_A );

        }

        public static function get_wp_poll_search_by_id($poll_id = NULL ) {

            global $wpdb;

            $sql        = "SELECT * FROM " . $wpdb->pollsq;



            $sql_search = '';

            if ( $poll_id ){

                $sql_search .= $wpdb->prepare( ' WHERE pollq_id = %d', $poll_id );

            }





            $sql .= $sql_search;



            return $wpdb->get_results( $sql, ARRAY_A );

        }

        public static function count_wp_search( $orderby = 'pollq_question', $order = 'desc', $search = array( 'fields' => array(), 'value' => NULL ) ) {

            global $wpdb;

            $sql        = "SELECT COUNT(*) AS poll_no   FROM " . $wpdb->pollsq;

            $sql_search = '';

            if ( count( $search['fields'] ) > 0 ){



                $sql_search .= ' WHERE (';



                foreach ( $search['fields'] as $field ) {

                    $sql_search .= $wpdb->prepare( ' ' . esc_attr( $field ) . ' like \'%%%s%%\' OR', $search['value'] );

                }

                $sql_search = trim( $sql_search, 'OR' );

                $sql_search .= ' ) ';

            }

            $sql .= $sql_search;

            $sql_order_by = ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order ) . ' ';

            $sql .= $sql_order_by;

            return $wpdb->get_results( $sql, ARRAY_A );

        }



        public function yop_poll_get_polls_meta_from_db(){

            global $wpdb;

            $result = $wpdb->get_results( "

                            SELECT *

                            FROM  wp_yop_pollmeta ORDER BY yop_poll_id ASC

                            ", ARRAY_A );

            return $result;



        }

        public function yop_poll_get_polls_from_db(){

            global $wpdb;

            $result = $wpdb->get_results( ( "

                            SELECT *

                            FROM   ".$wpdb->prefix."yop_polls   ORDER BY id ASC

                            "), ARRAY_A );

            return $result;



        }




        public function yop_poll_get_answers_meta_from_db() {
            global $wpdb;

            $result = $wpdb->get_results( "
                            SELECT *
                            FROM " . $wpdb->prefix . "yop_poll_answermeta

                            " , ARRAY_A );
            return $result;

        }

        public function yop_poll_get_templates_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( "
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_templates
                            " , ARRAY_A );
            return $result;
        }

        public function yop_poll_get_custom_fields_from_db() {
            global $wpdb;
            $result = $wpdb->get_results("
                            SELECT *
                            FROM " . $wpdb->prefix . "yop_poll_custom_fields ORDER BY poll_id ASC
                            " , ARRAY_A );
            return $result;
        }

        public function yop_poll_get_custom_fields_votes_from_db() {
            global $wpdb;
            $result = $wpdb->get_results(  "
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_votes_custom_fields
                            " , ARRAY_A );
            return $result;
        }

        public function yop_poll_get_bans_from_db() {
            global $wpdb;
            $result = $wpdb->get_results(  "
                            SELECT *
                            FROM   " . $wpdb->prefix . "yop_poll_bans ORDER BY poll_id ASC
                            " , ARRAY_A );
            return $result;
        }

        public function yop_poll_get_answers_from_db() {
            global $wpdb;
            $result = $wpdb->get_results(  "
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_answers  ORDER BY poll_id ASC
                            ", ARRAY_A );
            return $result;
        }

        public function yop_poll_get_logs_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( ( "
                            SELECT *
                            FROM " . $wpdb->prefix . "yop_poll_logs  WHERE ID>%d
                            " ), ARRAY_A );
            return $result;
        }

        private static function insert_ban_in_db( $ban ) {
            global $wpdb;
            $sql = $wpdb->query( $wpdb->prepare( "
	                INSERT INTO $wpdb->yop_poll_bans
                              ( poll_id,type,value,period ,unit)
		  	                    VALUES(%d,%s,%s,%d,%s)
	                        ", $ban['poll_id'], $ban['type'], $ban['value'], intval( $ban['period'] ), $ban['unit'] ) );
            return $wpdb->get_results( $sql );
        }
        private function save_poll_order( $poll, $poll_order ) {
            $poll_archive_order = get_option( 'yop_poll_archive_order', array() );
            if( $poll_archive_order == "" ) {
                $poll_archive_order = array();
            }if( trim( $poll_order ) <= 0 ) {
                $poll_order = 1;
            }
            $key = array_search( $poll, $poll_archive_order );
            if( $key !== false ) {
                unset( $poll_archive_order[$key] );
            }
            if( $poll_order > count( $poll_archive_order ) ) {
                array_push( $poll_archive_order, $poll );
            }
            else {
                array_splice( $poll_archive_order, trim( $poll_order ) - 1, 0, array( $poll ) );
            }
            update_option( 'yop_poll_archive_order', $poll_archive_order );
        }


    }
