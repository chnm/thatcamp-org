<?php

    Class YOP_POLL_Answer_Model{

        protected $data = NULL;

        protected $options = NULL;

        protected $ID = NULL;

        protected $type = 'default';

        private $default_fields = array( 'ID', 'poll_id', 'question_id', 'answer', 'type','description', 'answer_author', 'answer_date', 'answer_status', 'answer_modified', 'question_order', 'votes' );

        function __construct( $id = 0 ) {

            if ( $id instanceof YOP_POLL_Answer_Model ){

                $this->init( $id->data );

                return;

            }
            else
                if ( is_object( $id ) ) {

                $this->init( $id );

                return;

            }


            if ( !empty( $id ) && !is_numeric( $id ) ){

                $id = 0;

            }

            $data = self::get_data_by( 'id', $id );

            if ( $data ){

                $this->init( $data );

            }

            else {

                $this->default_init();

            }

        }

        function init( $data ) {

            $this->data = $data;

            $this->ID = (int)$data->ID;

            $this->type = $data->type;

            $this->init_options();

        }

        function default_init() {

            $this->ID = NULL;
            $data = new stdClass();

            $data->ID=NULL;
            $data->answer=NULL;
            $this->data=$data;

            $this->type = "default";

            $this->init_options();

        }

        function init_options() {

            $this->options = array();

            $default_options = get_option( 'yop_poll_options' );

            if ( $default_options && count( $default_options ) > 0 ){

                foreach ( $default_options as $option_name => $option_value ) {

                    $this->options[$option_name] = $this->get_option( $option_name );

                }

            }

        }

        function _unset() {

            $this->data = NULL;

            $this->ID = NULL;

            $this->options = NULL;

        }

        static function get_data_by( $field, $value ) {

            if ( 'id' == $field ){

                if ( !is_numeric( $value ) ){
                    return false;
                }

                $value = intval( $value );

                if ( $value < 1 ){
                    return false;
                }

            }

            else {

                $value = trim( $value );

            }


            if ( !$value ){
                return false;
            }


            switch ( $field ) {

                case 'id':

                    $answer_id = $value;

                    $db_field = 'ID';

                    break;

                default:

                    return false;

            }


            if ( false !== $answer_id ){

                if ( $answer = wp_cache_get( $answer_id, 'yop_poll_answer' ) ){
                    return $answer;
                }

            }


            if ( !$answer = $GLOBALS['wpdb']->get_row( $GLOBALS['wpdb']->prepare(

                                                                       "SELECT * FROM {$GLOBALS['wpdb']->yop_poll_answers} WHERE $db_field = %s", $value

                                                       ) )
            ){
                return false;
            }

            $answer->answer = stripslashes( $answer->answer );
            wp_cache_add( $answer->ID, $answer, 'yop_poll_answer' );


            return $answer;

        }
        function get_data_for_preview(){
            $answer = $GLOBALS['wpdb']->get_row( $GLOBALS['wpdb']->prepare(

                "SELECT * FROM {$GLOBALS['wpdb']->yop_poll_answers} WHERE ID > %d", 0));
            return $answer;
        }
        function __isset( $key ) {

            if ( 'id' == strtolower( $key ) ){

                $key = 'ID';

            }


            if ( isset( $this->data->$key ) ){
                return true;
            }


            //return metadata_exists( 'yop_poll_answer', $this->ID, $key );

            return $this->isset_option( $key );

        }

        function isset_option( $key ) {


            if ( in_array( $key, $this->default_fields ) ) //this is not an option

            {
                return false;
            }


            if ( isset( $this->options[$key] ) ){
                return true;
            }


            $answer_options = get_yop_poll_answer_meta( $this->id, 'options', true );

            if ( isset( $answer_options[$key] ) ){
                return true;
            }


            $question_options = get_yop_poll_question_meta( $this->question_id, 'options', true );

            if ( isset( $question_options[$key] ) ){
                return true;
            }


            $poll_options = get_yop_poll_meta( $this->poll_id, 'options', true );

            if ( isset( $poll_options[$key] ) ){
                return true;
            }


            $default_options = get_option( 'yop_poll_options' );

            if ( isset( $default_options[$key] ) ){
                return true;
            }

            return false;

        }

        function __get( $key ) {

            $value = NULL;

            if ( 'id' == strtolower( $key ) ){

                return $this->ID;

            }


            if ( 'options' == strtolower( $key ) ){
                return $this->options;
            }

            if ( 'type' == strtolower( $key ) ){
                return $this->type;
            }


            if ( isset( $this->data->$key ) ){

                $value = $this->data->$key;

            }

            else {

                //$value = get_yop_poll_answer_meta( $this->ID, $key, true );

                $value = $this->get_option( $key );

            }


            return $value;

        }

        function get_option( $key ) {

            if ( in_array( $key, $this->default_fields ) ) //this is not an option
            {
                return false;
            }


            if ( isset( $this->options[$key] ) ){
                return $this->options[$key];
            }


            $answer_options = get_yop_poll_answer_meta( $this->id, 'options', true );

            if ( isset( $answer_options[$key] ) ){
                return $answer_options[$key];
            }


            $question_options = get_yop_poll_question_meta( $this->question_id, 'options', true );

            if ( isset( $question_options[$key] ) ){
                return $question_options[$key];
            }


            $poll_options = get_yop_poll_meta( $this->poll_id, 'options', true );

            if ( isset( $poll_options[$key] ) ){
                return $poll_options[$key];
            }


            $default_options = get_option( 'yop_poll_options' );

            if ( isset( $default_options[$key] ) ){
                return $default_options[$key];
            }

            return false;

        }

        function __set( $key, $value ) {


            if ( 'id' == strtolower( $key ) ){

                $this->ID = $value;

                $this->data->ID = $value;
                return;

            }

            if( 'type' == strtolower( $key ) ) {
                $this->type = $value;
                $this->data->type = $value;
                return;
            }


            if ( in_array( $key, $this->default_fields ) ){ //this is not an option
                $this->data->$key = $value;
            }
            else {
                $this->_set_option( $key, $value );
            }

        }

        function _set_option( $key, $value ) {
            $this->options[$key] = $value;
        }

        function exists() {

            return !empty( $this->ID );

        }

        function get( $key ) {

            return $this->__get( $key );

        }

        function has_prop( $key ) {

            return $this->__isset( $key );

        }

        function to_array() {

            return get_object_vars( $this->data );

        }

        function save() {

            if ( $this->exists() ){ // update
                $this->update();
            }

            else { //insert

                $this->insert();

            }

        }

        function save_options() {

            $answer_options = get_yop_poll_answer_meta( $this->id, 'options', true );

            if ( $this->options && count( $this->options ) > 0 ){

                foreach ( $this->options as $option_name => $option_value ) {

                    $answer_options[$option_name] = $option_value;

                }

            }


            update_yop_poll_answer_meta( $this->id, 'options', $answer_options );

        }

        function insert() {

            $GLOBALS['wpdb']->query(
                            $GLOBALS['wpdb']->prepare( "
					INSERT INTO " . $GLOBALS['wpdb']->yop_poll_answers . "
					SET
					poll_id				= %d,
					question_id			= %d,
					answer				= %s,
					type                = %s,
					description         = %s,
					answer_author		= %d,
					answer_date			= %s,
					answer_status		= %s,
					answer_modified		= %s,
					question_order		= %s,
					votes				= %d
					",
                                                       $this->poll_id,
                                                       $this->question_id,
                                                       $this->answer,
                                                       $this->type,
                                                       $this->description,
                                                       $this->answer_author,
                                                       $this->answer_date,
                                                       $this->answer_status,
                                                       $this->answer_modified,
                                                       $this->question_order,
                                                       $this->votes
                            )
            );
            $this->id = $GLOBALS['wpdb']->insert_id;

            $this->save_options();

            if ( intval( $this->id ) > 0 ){

                wp_cache_delete( $this->id, 'yop_poll_answer' );

                return $this->id;

            }

            return false;

        }

        function update() {
            global $wpdb;
            $wpdb->query(
                 $GLOBALS['wpdb']->prepare( "
					UPDATE  {$wpdb->yop_poll_answers}
					SET
					answer				= %s,
					type                = %s,
					description         =%s,
					answer_status		= %s,
					answer_modified		= %s,
					question_order		= %s,
					votes				= %d
					WHERE
					ID					= %d
					",
                                            $this->answer,
                                            $this->type,
                                            $this->description,
                                            $this->answer_status,
                                            $this->answer_modified,
                                            $this->question_order,
                                            $this->votes,
                                            $this->ID
                 )
            );
            $this->save_options();

            wp_cache_delete( $this->id, 'yop_poll_answer' );

            return $this->id;

        }





        function delete() {

            $GLOBALS['wpdb']->query(

                            $GLOBALS['wpdb']->prepare(

                                            "

                                            DELETE FROM " . $GLOBALS['wpdb']->yop_poll_answers . "

					WHERE ID = %d

					",

                                            $this->id

                            )

            );

            delete_yop_poll_answer_meta( $this->id, 'options' );

            wp_cache_delete( $this->id, 'yop_poll_answer' );

            $this->_unset();

        }


    }