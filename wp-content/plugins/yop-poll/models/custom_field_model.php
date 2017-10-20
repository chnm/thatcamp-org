<?php
    class YOP_POLL_Custom_Field_Model{

        protected $data = NULL;
        protected $ID = NULL;
        private $default_fields = array( 'id', 'poll_id', 'question_id', 'custom_field', 'required', 'status' );

        function __construct( $id = 0 ) {
            if ( $id instanceof YOP_POLL_Custom_Field_Model ){
                $this->init( $id->data );
                return;
            }
            elseif ( is_object( $id ) ) {
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
            $this->ID   = (int)$data->ID;
        }

        function default_init() {
            $this->ID = NULL;
        }

        function _unset() {
            $this->data = NULL;
            $this->ID   = NULL;
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
                    $custom_field_id = $value;
                    $db_field        = 'id';
                    break;
                default:
                    return false;
            }

            if ( false !== $custom_field_id ){
                if ( $custom_field = wp_cache_get( $custom_field_id, 'yop_poll_custom_field' ) ){
                    return $custom_field;
                }
            }

            if ( !$custom_field = $GLOBALS['wpdb']->get_row( $GLOBALS['wpdb']->prepare(
                                                                             "SELECT
																				*
																			 FROM
																				{$GLOBALS['wpdb']->yop_poll_custom_fields}
																			 WHERE
																				$db_field = %s",
                                                                             $value
                                                             )
            )
            ){
                return false;
            }

            wp_cache_add( $custom_field->ID, $custom_field, 'yop_poll_custom_field' );
            return stripslashes_deep($custom_field);
        }

        function __isset( $key ) {
            if ( 'id' == strtolower( $key ) ){
                $key = 'id';
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

            $answer_options = get_yop_poll_answer_meta( $this->ID, 'options', true );

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
            $answer_options = get_yop_poll_answer_meta( $this->ID, 'options', true );
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
                $this->ID       = $value;
                $this->data->ID = $value;
                return;
            }

            if ( in_array( $key, $this->default_fields ) ){ //this is not an option
                $this->data->$key = $value;
            }
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

        function insert() {
            $this->custom_field = sanitize_text_field( $this->custom_field );
            $GLOBALS['wpdb']->insert(
                            $GLOBALS['wpdb']->yop_poll_custom_fields,
                            array(
                                'poll_id'      => $this->poll_id,
                                'question_id'  => $this->question_id,
                                'custom_field' => $this->custom_field,
                                'required'     => $this->required,
                                'status'       => $this->status
                            )
            );
            $this->ID = $GLOBALS['wpdb']->insert_id;

            if ( intval( $this->ID ) > 0 ){
                wp_cache_delete( $this->ID, 'yop_poll_custom_field' );
                return $this->ID;
            }
            return false;
        }

        function update() {
            $this->custom_field = sanitize_text_field( $this->custom_field );
            $GLOBALS['wpdb']->update(
                            $GLOBALS['wpdb']->yop_poll_custom_fields,
                            array(
                                'custom_field' => $this->custom_field,
                                'required'     => $this->required,
                                'status'       => $this->status
                            ),
                            array(
                                'ID' => $this->ID
                            )
            );
            wp_cache_delete( $this->ID, 'yop_poll_custom_field' );
            return $this->ID;
        }

        function delete() {
            $GLOBALS['wpdb']->delete(
                            $GLOBALS['wpdb']->yop_poll_custom_fields,
                            array(
                                'ID' => $this->ID
                            )
            );
            wp_cache_delete( $this->ID, 'yop_poll_custom_field' );
            $this->_unset();
        }
    }
