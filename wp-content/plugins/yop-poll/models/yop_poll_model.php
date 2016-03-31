<?php

    class Yop_Poll_Model {

        /**
         * @param mixed $args
         * args[return_fields] default is "*". You can use what fields you want to return like ID,poll_name
         * args[filters] use to filter polls by certain variables like start date, end date etc; its an array of filters with this attributes field, operator, value
         * args[search] use to search into poll fields with like, it's an array of more fields and a value args[search] = array( 'fields' => array('ID', 'poll_name'), 'value' => 'a_val' )
         * args[orderby] field to order your polls. default ID
         * args[order] ASC or DESC
         */
        public static function get_polls_filter_search( $args ) {
            $return_fields = '*';
            $filters       = null;
            $search        = null;
            $orderby       = 'ID';
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
                if( in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ) ) ) {
                    $order = $args['order'];
                }
            }
            if( isset( $args['limit'] ) ) {
                $limit = $args['limit'];
            }

            $sql        = 'SELECT ' . $return_fields . ' FROM ' . $GLOBALS['wpdb']->yop_polls . ' WHERE 1=1 ';
            $sql_filter = '';
            if( count( $filters ) > 0 ) {
                foreach( $filters as $filter ) {
                    $sql_filter .= ' AND ' . $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $filter['field'] ) . '` ' . $filter['operator'] . ' %s ', esc_attr( $filter['value'] ) ) . ' ';
                }
            }

            $sql_search = '';
            if( isset( $search['fields']) && count( $search['fields'] ) > 0 ) {
                foreach( $search['fields'] as $field ) {
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $field ) . '` like \'%%%s%%\' OR', $search['value'] );
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

        public static function init_polls_filter_search( $args ) {
            $polls                 = array();
            $args['return_fields'] = 'id';
            $poll_ids              = self::get_polls_filter_search( $args );
            if( count( $poll_ids ) > 0 ) {
                foreach( $poll_ids as $poll ) {
                    $new_poll = new YOP_POLL_Poll_Model( $poll['id'] );
                    if( $new_poll ) {
                        $polls[] = $new_poll;
                    }
                }
            }
            return $polls;
        }

        public static function save_poll( $preview = false ) {

            $new_poll                   = new YOP_POLL_Poll_Model( $_POST['poll_id'] );
            $new_poll->poll_title       = stripslashes(trim( $_POST['poll_title'] ));
            $new_poll->poll_name        = sanitize_title( $new_poll->poll_title );
            $new_poll->poll_author      = $GLOBALS['current_user']->ID;
            $new_poll->poll_date        = current_time( 'mysql' );
            $new_poll->poll_status      = 'active';
            $new_poll->poll_modified    = current_time( 'mysql' );
            $new_poll->poll_start_date  = $_POST['poll_start_date'];
            $new_poll->poll_end_date    = ( isset( $_POST['poll_never_expire'] ) && 'yes' == $_POST['poll_never_expire'] ) ? '01-01-2038 23:59:59' : $_POST['poll_end_date'];
            $new_poll->poll_total_votes = 0;

            //poll options area
            if( isset( $_POST['yop_poll_options'] ) && is_array( $_POST['yop_poll_options'] ) && count( $_POST['yop_poll_options'] ) > 0 ) {
                foreach( $_POST['yop_poll_options'] as $poll_option_field => $poll_option_value ) {
                    $new_poll->$poll_option_field = $poll_option_value;
                }
            }

            if(isset($_POST['yop_poll_options']['use_the_same_template_for_widget']))
            {  $new_poll->use_the_same_template_for_widget='yes';
                $new_poll->widget_template_width=$new_poll->template_width;
                $new_poll->widget_template=$new_poll->template;
            }

            else
                $new_poll->use_the_same_template_for_widget='no';

            $questions = array();
            if( isset( $_POST['yop_poll_question'] ) && is_array( $_POST['yop_poll_question'] ) && count( $_POST['yop_poll_question'] ) > 0 ) {
                foreach( $_POST['yop_poll_question'] as $question_args ) {
                    $new_question                    = new YOP_POLL_Question_Model( $question_args['question_id'] );
                    $new_question->question          = trim( $question_args['question'] );
                    $new_question->type              = trim( $question_args['type'] );
                    $new_question->question_modified = current_time( 'mysql' );
                    $new_question->poll_order        = intval( $question_args['poll_order'] );

                    if( ! $new_question->exists() ) { //for insert
                        $new_question->question_author = $GLOBALS['current_user']->ID;
                        $new_question->question_date   = current_time( 'mysql' );
                        $new_question->question_status = 'active';
                    }

                    //question options area
                    if( isset( $question_args['options'] ) && is_array( $question_args['options'] ) && count( $question_args['options'] ) > 0 ) {
                        foreach( $question_args['options'] as $question_option_field => $question_option_value ) {
                            $new_question->$question_option_field = $question_option_value;
                        }
                    }

                    $custom_fields = array();
                    //question custom fields area
                    if( isset( $question_args['custom_fields'] ) && is_array( $question_args['custom_fields'] ) && count( $question_args['custom_fields'] ) > 0 ) {
                        foreach( $question_args['custom_fields'] as $question_custom_field ) {
                            $cf_id                          = isset( $question_custom_field['id'] ) ? trim( $question_custom_field['id'] ) : 0;
                            $new_custom_field               = new YOP_POLL_Custom_Field_Model( $cf_id );
                            $new_custom_field->custom_field = trim( $question_custom_field['custom_field'] );
                            $new_custom_field->required     = isset( $question_custom_field['required'] ) ? trim( $question_custom_field['required'] ) : 'no';
                            $new_custom_field->status       = "active";

                            if( $new_custom_field->custom_field == "" ) {

                                $response['success'] = 0;

                                $response['id']      = - 1;

                                $response['error']   = 1;

                                $response['message'] = __yop_poll( 'Custom Field' ) . ' ' . $question_custom_field['order'] . ' ' . __yop_poll( 'from Question' ) . ' ' . $new_question->poll_order . ' ' . __yop_poll( 'is empty!' );

                                wp_die( json_encode( $response ) );

                            }
                            $custom_fields[] = $new_custom_field;
                        }
                    }
                    $new_question->custom_fields = $custom_fields;

                    $answers = array();

                    if( isset( $_POST['yop_poll_answer'][$question_args['question_id']] ) && is_array( $_POST['yop_poll_answer'][$question_args['question_id']] ) && count( $_POST['yop_poll_answer'][$question_args['question_id']] ) > 0 ) {
                        foreach( $_POST['yop_poll_answer'][$question_args['question_id']] as $answer_args ) {
                            $new_answer         = new YOP_POLL_Answer_Model( $answer_args['answer_id'] );
                            $new_answer->answer = trim( $answer_args['answer'] );
                            $new_answer->type   = trim( $answer_args['type'] );
                            if( isset( $answer_args['is_default_answer'] ) ) {
                                $new_answer->is_default_answer = 'yes';
                            }
                            else {
                                $new_answer->is_default_answer = 'no';
                            }
                            $new_answer->description = "";
                            if( $new_answer->type == 'video' ) {
                                $new_answer->description = trim( $answer_args['description_video'] );
                            }
                            else if( $new_answer->type == 'image' ) {
                                $new_answer->description = trim( $answer_args['description_image'] );
                            }
                            $new_answer->question_order  = trim( $answer_args['question_order'] );
                            $new_answer->answer_modified = current_time( 'mysql' );

                            if( ! $new_answer->exists() ) { //for insert
                                $new_answer->answer_author = $GLOBALS['current_user']->ID;
                                $new_answer->answer_date   = current_time( 'mysql' );
                                $new_answer->answer_status = 'active';
                            }

                            if( $new_answer->answer == '' ) {

                                $response['success'] = 0;

                                $response['id']      = - 1;

                                $response['error']   = 1;

                                $response['message'] = __yop_poll( 'Answer' ) . ' ' . $new_answer->question_order . ' ' . __yop_poll( 'from Question' ) . ' ' . $new_question->poll_order . ' ' . __yop_poll( 'is empty!' );

                                wp_die( json_encode( $response ) );

                            }
                            $answers[] = $new_answer;
                        }
                    }

                    $new_question->answers = $answers;

                    if( $new_question->question == '' ) {

                        $response['success'] = 0;

                        $response['id']      = - 1;

                        $response['error']   = 1;

                        $response['message'] = __yop_poll( 'Question' ) . ' ' . $new_question->poll_order . ' ' . __yop_poll( 'is empty!' );

                        wp_die( json_encode( $response ) );

                    }

                    $questions[] = $new_question;
                }
            }

            $new_poll->questions = $questions;

            if( $preview ) {
                $new_poll->ID = 'preview';
                return $new_poll->return_poll_html( array(
                                                        'tr_id'    => '',
                                                        'location' => 'page',
                                                        'load_css' => true,
                                                        'load_js'  => false
                                                    ) );
            }
            else {
                if( ! $new_poll->exists() && YOP_POLL_Poll_Model::get_data_by( 'name', $new_poll->poll_name ) ) {

                    $response['success'] = 0;

                    $response['id']      = - 1;

                    $response['error']   = 1;

                    $response['message'] = __yop_poll( 'This poll already exists! Please choose another name!' );

                    wp_die( json_encode( $response ) );

                }

                if( $new_poll->exists() && YOP_POLL_Poll_Model::get_other_model_data_by( 'name', $new_poll->poll_name, $new_poll->id ) ) {

                    $response['success'] = 0;

                    $response['id']      = - 1;

                    $response['error']   = 1;

                    $response['message'] = __yop_poll( 'This poll already exists! Please choose another name!' );

                    wp_die( json_encode( $response ) );

                }

                if( $new_poll->save() ) {

                    self::save_poll_order( $new_poll->get( 'ID' ), $_POST['poll_archive_order'] );

                    $response['success'] = 1;

                    $response['id']      = $new_poll->ID;

                    $response['error']   = 0;

                    $response['message'] = __yop_poll( 'Poll successfully saved!' );

                    wp_die( json_encode( $response ) );

                }

                else {

                    $response['success'] = 0;

                    $response['id']      = - 1;

                    $response['error']   = 1;

                    $response['message'] = __yop_poll( 'Poll couldn`t be saved!' );

                    wp_die( json_encode( $response ) );

                }
            }
        }

        private static function save_poll_order( $poll, $poll_order ) {
            $poll_archive_order = get_option( 'yop_poll_archive_order', array() );
            if( $poll_archive_order == "" ) {
                $poll_archive_order = array();
            }
            if( isset( $_POST['yop_poll_options']['show_poll_in_archive'] ) ) {
                if( trim( $_POST['yop_poll_options']['show_poll_in_archive'] == 'yes' ) ) {
                    if( isset( $poll_order ) && is_numeric( trim( $poll_order ) ) ) {
                        if( trim( $poll_order ) <= 0 ) {
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

                    }
                    else {
                        wp_die( __( 'Option "Poll Archive Order" NOT Updated! You must fill in with a numeric value!' ) );
                    }
                }
                else {
                    $key = array_search( $poll, $poll_archive_order );

                    if( $key !== null ) {
                        unset( $poll_archive_order[$key] );
                    }
                }
            }
            else {
                wp_die( __( 'Option "Show Poll in Archive" NOT Updated! Please choose between \'yes\' or \'no\'!' ) );
            }
            $poll_archive_order = array_values( $poll_archive_order );
            update_option( 'yop_poll_archive_order', $poll_archive_order );
        }

        public static function get_yop_poll_templates_search(
            $orderby = 'last_modified', $order = 'desc', $search = array(
                                          'fields' => array(),
                                          'value'  => null
                                      ), $offset = 0, $per_page = 100
        ) {
            global $wpdb;
            $sql        = "SELECT * FROM " . $wpdb->yop_poll_templates;
            $sql_search = '';
            if( count( $search['fields'] ) > 0 ) {
                $sql_search .= ' ( ';
                foreach( $search['fields'] as $field ) {
                    $sql_search .= $wpdb->prepare( ' `' . $field . '` like \'%%%s%%\' OR', $search['value'] );
                }
                $sql_search = trim( $sql_search, 'OR' );
                $sql_search .= ' ) ';
            }
            if( count( $search['fields'] ) > 0 ) {
                $sql .= ' WHERE ' . $sql_search;
            }
            $sql_order_by = ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order ) . ' ';
            $sql .= $sql_order_by;
            $sql .= $wpdb->prepare( ' LIMIT %d, %d', $offset * $per_page, $per_page );
            return $wpdb->get_results( $sql, ARRAY_A );
        }

        public static function get_polls_for_view( $args = array() ) {
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
                if( in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC' ) ) ) {
                    $order = $args['order'];
                }
            }
            if( isset( $args['limit'] ) ) {
                $limit = $args['limit'];
            }

            $sql        = 'SELECT ' . $return_fields . ' FROM ' . $GLOBALS['wpdb']->yop_polls . ' AS polls WHERE 1=1 ';
            $sql_filter = '';
            if( count( $filters ) > 0 ) {
                foreach( $filters as $filter ) {
                    $sql_filter .= ' AND ' . $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $filter['field'] ) . '` ' . $filter['operator'] . ' %s ', esc_attr( $filter['value'] ) ) . ' ';
                }
            }

            $sql_search = '';
            if( count( $search['fields'] ) > 0 ) {
                foreach( $search['fields'] as $field ) {
                    $sql_search .= $GLOBALS['wpdb']->prepare( ' `' . esc_attr( $field ) . '` like \'%%%s%%\' OR', $search['value'] );
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

        public static function get_bans_filter_search(
            $orderby = 'name', $order = 'desc', $search = array(
                                 'fields' => array(),
                                 'value'  => null
                             ), $type = null, $poll_id = null, $offset = 0, $per_page = 99999999
        ) {
            global $wpdb;
			$allowed_order = array (
								0 => "id",
								1 => "name",
								2 => "type",
								3 => "value",
								4 => "period",
								5 => "unit"
							);
			
			if( !in_array( $orderby, $allowed_order ) ) {
				$order_bye = "id";
			}
			
			if( $order != "desc" && $order != "asc") {
				$order = "desc";
			}
			
            if( 'id' == $orderby ) {
                $orderby = $wpdb->yop_poll_bans . ".id";
            }

            $sql_search = ' ';

            if( $poll_id ) {
                $sql_search .= $wpdb->prepare( 'WHERE ' . $wpdb->yop_poll_bans . '.poll_id = %d', $poll_id );
            }
            if( $type ) {
                if( $poll_id ) {
                    $sql_search .= ' AND  ';
                }
                else {
                    $sql_search .= ' WHERE ';
                }
                $sql_search .= $wpdb->prepare( $wpdb->yop_poll_bans . '.type= %s', $type );
            }
            if( count( $search['fields'] ) > 0 ) {
                if( $poll_id || $type ) {
                    $sql_search .= ' AND ( ';
                }
                else {
                    $sql_search .= ' WHERE (';
                }
                foreach( $search['fields'] as $field ) {
                    $sql_search .= $wpdb->prepare( ' ' . esc_attr( $field ) . ' like \'%%%s%%\' OR', $search['value'] );
                }
                $sql_search = trim( $sql_search, 'OR' );
                $sql_search .= ' ) ';
            }

            $sql = "SELECT
			" . $wpdb->yop_poll_bans . ".id,
			" . $wpdb->yop_poll_bans . ".value,
			" . $wpdb->yop_poll_bans . ".type,
			" . $wpdb->yop_poll_bans . ".period,
			" . $wpdb->yop_poll_bans . ".unit,
			" . $wpdb->yop_poll_bans . ".poll_id,

			IFNULL( " . $wpdb->yop_polls . ".poll_title, '" . __( 'All Polls', 'yop_poll' ) . "' ) as name

			FROM " . $wpdb->yop_poll_bans . "
			LEFT JOIN (" . $wpdb->yop_polls . ")
			ON (
			" . $wpdb->yop_poll_bans . ".poll_id = " . $wpdb->yop_polls . ".ID
			)
			";
            $sql .= $sql_search;
            $sql .= ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order );
            $sql .= $wpdb->prepare( ' LIMIT %d, %d', $offset, $per_page );
            return $wpdb->get_results( $sql, ARRAY_A );
        }

        public static function get_poll_options_by_id( $poll_id = 0 ) {

            $poll_options    = get_yop_poll_meta( $poll_id, 'options', true );

            $default_options = get_option( 'yop_poll_options', false );

            if( is_array( $default_options ) ) {

                if( count( $default_options ) > 0 ) {

                    foreach( $default_options as $option_name => $option_value ) {

                        if( ! isset( $poll_options [$option_name] ) ) {

                            $poll_options    [$option_name] = $option_value;

                        }

                    }

                }

            }

            return $poll_options;

        }
    }