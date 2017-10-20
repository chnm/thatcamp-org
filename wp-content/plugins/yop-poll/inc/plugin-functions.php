<?php
//
// Yop poll meta functions
//
    /**
     * Update yop poll meta field based on yop poll ID.
     *
     * Use the $prev_value parameter to differentiate between meta fields with the
     * same key and yop poll ID.
     *
     * If the meta field for the yop poll does not exist, it will be added.
     *
     * @param int    $yop_poll_id Yop Poll ID.
     * @param string $meta_key    Metadata key.
     * @param mixed  $meta_value  Metadata value.
     * @param mixed  $prev_value  Optional. Previous value to check before removing.
     *
     * @return bool False on failure, true if success.
     */
    function update_yop_poll_meta( $poll_id, $meta_key, $meta_value, $prev_value = '' ) {
        return update_metadata( 'yop_poll', $poll_id, $meta_key, $meta_value, $prev_value );
    }

    /**
     * Add yop poll data field to a yop poll.
     *
     * @param int    $yop_poll_id yop poll ID.
     * @param string $meta_key    Metadata name.
     * @param mixed  $meta_value  Metadata value.
     * @param bool   $unique      Optional, default is false. Whether the same key should not be added.
     *
     * @return bool False for failure. True for success.
     */
    function add_yop_poll_meta( $poll_id, $meta_key, $meta_value, $unique = false ) {
        return add_metadata( 'yop_poll', $poll_id, $meta_key, $meta_value, $unique );
    }

    /**
     * Remove metadata matching criteria from a yop_poll.
     *
     * You can match based on the key, or key and value. Removing based on key and
     * value, will keep from removing duplicate metadata with the same key. It also
     * allows removing all metadata matching key, if needed.
     *
     * @param int    $poll_id    post ID
     * @param string $meta_key   Metadata name.
     * @param mixed  $meta_value Optional. Metadata value.
     *
     * @return bool False for failure. True for success.
     */
    function delete_yop_poll_meta( $poll_id, $meta_key, $meta_value = '' ) {
        return delete_metadata( 'yop_poll', $poll_id, $meta_key, $meta_value );
    }

    /**
     * Retrieve yop_poll meta field for a yop poll.
     *
     * @param int    $poll_id Yop poll ID.
     * @param string $key     Optional. The meta key to retrieve. By default, returns data for all keys.
     * @param bool   $single  Whether to return a single value.
     *
     * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
     *  is true.
     */
    function get_yop_poll_meta( $poll_id, $key = '', $single = false ) {
        return get_metadata( 'yop_poll', $poll_id, $key, $single );
    }

    function get_yop_poll_meta_from_previos_version( $poll_id, $key = '', $single = false ) {
        return get_metadata( 'yop_pollmeta', $poll_id, $key, $single );
    }

    /**
     * Delete everything from yop_poll meta matching meta key.
     *
     * @param string $poll_meta_key Key to search for when deleting.
     *
     * @return bool Whether the poll meta key was deleted from the database
     */
    function delete_yop_poll_meta_by_key( $poll_meta_key ) {
        return delete_metadata( 'yop_poll', null, $poll_meta_key, '', true );
    }

    /**
     * Yop Poll Default Options
     */
function yop_poll_default_options() {

    return array(

        'user_interface_type'                       => 'beginner',

        'is_default_answer'                         => 'no',

        'poll_start_date'                           => current_time( 'mysql' ),

        'poll_end_date'                             => '01-01-2038 23:59:59',

        'view_results'                              => array( 'after' ),

        'view_results_start_date'                   => '',

        'view_results_permissions'                  => array( 'guest', 'registered' ),

        'view_results_type'                         => 'votes-number-and-percentages',

        'answer_result_label'                       => '%POLL-ANSWER-RESULT-PERCENTAGES% - ( %POLL-ANSWER-RESULT-VOTES% )',

        'vote_button_label'                         => 'vote',

        'template_width'                            => '200px',

        'widget_template_width'                     => '200px',

        'view_results_link'                         => 'no',

        'view_results_link_label'                   => __yop_poll( 'View Results' ),

        'view_back_to_vote_link'                    => 'no',

        'view_back_to_vote_link_label'              => __yop_poll( 'Back to vote' ),

        'view_total_votes'                          => 'no',

        'view_total_votes_label'                    => __yop_poll( 'Poll total votes: %POLL-TOTAL-VOTES%' ),

        'view_total_answers'                        => 'no',

        'view_total_answers_label'                  => __yop_poll( 'Poll total answers: %POLL-TOTAL-ANSWERS%' ),

        'message_after_vote'                        => __yop_poll( 'Thank you for voting!' ),

        'vote_permisions'                           => array( 'guest', 'registered' ),

        'vote_permisions_wordpress'                 => 'yes',

        'vote_permisions_wordpress_label'           => __yop_poll( 'Vote as Wordpress User' ),

        'vote_permisions_anonymous'                 => 'yes',

        'vote_permisions_anonymous_label'           => __yop_poll( 'Vote as Anonymous User' ),

        'vote_permisions_facebook'                  => 'no',

        'vote_permisions_facebook_label'            => __yop_poll( 'Vote as Facebook User' ),

        'facebook_share_after_vote'                 => 'no',

        'facebook_share_description'                => __yop_poll( 'Just casted an YOP Poll vote on ' ) . get_bloginfo( 'name' ),

        'facebook_show_comments_widget'                => "no",

        'vote_permisions_google'                    => 'no',

        'vote_permisions_google_label'              => __yop_poll( 'Vote as G+ User' ),

        'show_google_share_button'                  => 'no',

        'blocking_voters'                           => array( 'dont-block' ),

        'blocking_voters_interval_value'            => 24,

        'blocking_voters_interval_unit'             => 'hours',

        'limit_number_of_votes_per_user'            => 'yes',

        'number_of_votes_per_user'                  => 3,

        'percentages_decimals'                      => 2,

        'use_default_loading_image'                 => 'yes',

        'loading_image_url'                         => '',

        'redirect_after_vote'                       => 'no',

        'redirect_after_vote_url'                   => '',

        'date_format'                               => 'UE',

        'view_poll_archive_link'                    => 'no',

        'auto_generate_poll_page'                   => 'no',

        'has_auto_generate_poll_page'               => 'no',

        'use_captcha'                               => 'no',

        'send_email_notifications'                  => 'yes',

        'allow_other_answers'                       => 'no',

        'other_answers_label'                       => __yop_poll( 'Other' ),

        'is_default_other_answer'                   => 'no',

        'add_other_answers_to_default_answers'      => 'no',

        'display_other_answers_values'              => 'no',

        'allow_multiple_answers'                    => 'no',

        'allow_multiple_answers_number'             => 3,

        'allow_multiple_answers_min_number'         => 1,

        'display_answers'                           => 'vertical',

        'display_answers_tabulated_cols'            => 2,

        'sorting_results'                           => 'as_defined',

        'sorting_results_direction'                 => 'asc',

        'singular_answer_result_votes_number_label' => 'vote',

        'plural_answer_result_votes_number_label'   => 'votes',

        'display_results'                           => 'vertical',

        'display_results_tabulated_cols'            => 2,

        'bar_background'                            => 'FBD55E',

        'bar_height'                                => 10,

        'bar_border_color'                          => 'EDB918',

        'show_results_in'                           => 'bar',

        'bar_border_width'                          => 2,

        'bar_border_style'                          => 'solid',

        'sorting_archive_polls'                     => 'votes',

        'sorting_archive_polls_rule'                => 'asc',

        'archive_url'                               => '',

        'archive_link_label'                        => __yop_poll( 'Archive' ),

        'show_poll_in_archive'                      => 'yes',

        'poll_archive_order'                        => 1,

        'archive_polls_per_page'                    => 5,

        'email_notifications_from_name'             => 'Yop Poll',

        'email_notifications_from_email'            => 'yop-poll@' . preg_replace( '/^www\./', '', $_SERVER['SERVER_NAME'] ),

        'email_notifications_recipients'            => '',

        'email_notifications_subject'               => __yop_poll( 'New Yop Poll Vote' ),

        'email_notifications_body'                  => '<p>A new vote was registered on %VOTE_DATE% for %POLL_NAME%</p>

                                                            <p>Vote Details:</p>

                                                            [QUESTION]

                                                            <p><b>Question:</b> %QUESTION_TEXT%</p>

                                                            <p><b>Answers:</b> <br />

                                                            [ANSWERS]

                                                            %ANSWER_VALUE%

                                                            [/ANSWERS]

                                                            </p>

                                                            <p><b>Custom Fields:</b> <br />

                                                            [CUSTOM_FIELDS]

                                                            %CUSTOM_FIELD_NAME% - %CUSTOM_FIELD_VALUE%

                                                            [/CUSTOM_FIELDS]

                                                            </p>

                                                            [/QUESTION]

                                                            <p><b>Vote ID:</b> <br />%VOTE_ID%</p>',

        'schedule_reset_poll_stats'                 => "no",

        'schedule_reset_poll_date'                  => current_time( 'mysql' ),

        'schedule_reset_poll_recurring_value'       => '30',

        'schedule_reset_poll_recurring_unit'        => 'day',

        'singular_answer_result_votes_number_label' => __yop_poll( "vote" ),

        'plural_answer_result_votes_number_label'   => __yop_poll( "votes" ),

        'start_scheduler'                           => 'yes',

        'use_the_same_template_for_widget'          =>'yes',
        'google_integration'                        =>'no',
        'facebook_integration'                        =>'no'
    );

}



/**

 * Poll Default Options

 */

function yop_poll_poll_default_options() {

    return array(

        'poll_start_date'                           => current_time( 'mysql' ),

        'poll_end_date'                             => '01-01-2038 23:59:59',

        'view_results'                              => array( 'after' ),

        'view_results_start_date'                   => '',

        'view_results_permissions'                  => array( 'guest', 'registered' ),

        'view_results_type'                         => 'votes-number-and-percentages',

        'answer_result_label'                       => '%POLL-ANSWER-RESULT-PERCENTAGES% - ( %POLL-ANSWER-RESULT-VOTES% )',

        'template_width'                            => '200px',

        'widget_template_width'                     => '200px',

        'view_results_link'                         => 'no',

        'view_back_to_vote_link'                    => 'no',

        'view_total_votes'                          => 'no',

        'view_total_answers'                        => 'no',

        'vote_permisions'                           => array( 'guest', 'registered' ),

        'vote_permisions_facebook'                  => 'no',

        'vote_permisions_google'                    => 'no',

        'vote_permisions_wordpress'                 => 'yes',

        'vote_permisions_anonymous'                 => 'yes',

        'blocking_voters'                           => array( 'dont-block' ),

        'blocking_voters_interval_value'            => 24,

        'blocking_voters_interval_unit'             => 'hours',

        'limit_number_of_votes_per_user'            => 'yes',

        'number_of_votes_per_user'                  => 3,

        'use_default_loading_image'                 => 'yes',

        'redirect_after_vote'                       => 'no',

        'view_poll_archive_link'                    => 'no',

        'auto_generate_poll_page'                   => 'no',

        'has_auto_generate_poll_page'               => 'no',

        'poll_page_url'                             => '',

        'use_captcha'                               => 'no',

        'send_email_notifications'                  => 'yes',

        'allow_other_answers'                       => 'no',

        'other_answers_label'                       => '',

        'add_other_answers_to_default_answers'      => 'no',

        'display_other_answers_values'              => 'no',

        'allow_multiple_answers'                    => 'no',

        'allow_multiple_answers_number'             => 3,

        'allow_multiple_answers_min_number'         => 1,

        'display_answers'                           => 'vertical',

        'display_answers_tabulated_cols'            => 2,

        'sorting_results'                           => 'as_defined',

        'sorting_results_direction'                 => 'asc',

        'display_results'                           => 'vertical',

        'display_results_tabulated_cols'            => 2,

        'bar_background'                            => 'FBD55E',

        'bar_height'                                => 10,

        'bar_border_color'                          => 'EDB918',

        'show_results_in'                           => 'bar',

        'bar_border_width'                          => 2,

        'bar_border_style'                          => 'solid',

        'sorting_archive_polls'                     => 'votes',

        'sorting_archive_polls_rule'                => 'asc',

        'show_poll_in_archive'                      => 'yes',

        'poll_archive_order'                        => 1,

        'singular_answer_result_votes_number_label' => __yop_poll( "vote" ),

        'plural_answer_result_votes_number_label'   => __yop_poll( "votes" ),

        'schedule_reset_poll_stats'                 => "no",

        'schedule_reset_poll_date'                  => current_time( 'mysql' ),

        'schedule_reset_poll_recurring_value'       => '30',

        'schedule_reset_poll_recurring_unit'        => 'day',

        'use_the_same_template_for_widget'          =>'yes'

    );

}


//
// Yop poll question meta functions
//
    /**
     * Update yop poll question meta field based on yop poll ID.
     *
     * Use the $prev_value parameter to differentiate between meta fields with the
     * same key and yop poll question ID.
     *
     * If the meta field for the yop poll question does not exist, it will be added.
     *
     * @param int    $yop_poll_question_id Yop Poll ID.
     * @param string $meta_key             Metadata key.
     * @param mixed  $meta_value           Metadata value.
     * @param mixed  $prev_value           Optional. Previous value to check before removing.
     *
     * @return bool False on failure, true if success.
     */
    function update_yop_poll_question_meta( $poll_question_id, $meta_key, $meta_value, $prev_value = '' ) {
        return update_metadata( 'yop_poll_question', $poll_question_id, $meta_key, $meta_value, $prev_value );
    }

    /**
     * Add yop poll question data field to a yop poll question.
     *
     * @param int    $yop_poll_question_id yop poll question ID.
     * @param string $meta_key             Metadata name.
     * @param mixed  $meta_value           Metadata value.
     * @param bool   $unique               Optional, default is false. Whether the same key should not be added.
     *
     * @return bool False for failure. True for success.
     */
    function add_yop_poll_question_meta( $poll_question_id, $meta_key, $meta_value, $unique = false ) {
        return add_metadata( 'yop_poll_question', $poll_question_id, $meta_key, $meta_value, $unique );
    }

    /**
     * Remove metadata matching criteria from a yop_question_poll.
     *
     * You can match based on the key, or key and value. Removing based on key and
     * value, will keep from removing duplicate metadata with the same key. It also
     * allows removing all metadata matching key, if needed.
     *
     * @param int    $poll_question_id post ID
     * @param string $meta_key         Metadata name.
     * @param mixed  $meta_value       Optional. Metadata value.
     *
     * @return bool False for failure. True for success.
     */
    function delete_yop_poll_question_meta( $poll_question_id, $meta_key, $meta_value = '' ) {
        return delete_metadata( 'yop_poll_question', $poll_question_id, $meta_key, $meta_value );
    }

    /**
     * Retrieve yop_poll_question meta field for a yop poll question.
     *
     * @param int    $poll_question_id Yop poll question ID.
     * @param string $key              Optional. The meta key to retrieve. By default, returns data for all keys.
     * @param bool   $single           Whether to return a single value.
     *
     * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
     *  is true.
     */
    function get_yop_poll_question_meta( $poll_question_id, $key = '', $single = false ) {
        return get_metadata( 'yop_poll_question', $poll_question_id, $key, $single );
    }

    /**
     * Delete everything from yop_poll_question meta matching meta key.
     *
     * @param string $poll_meta_key Key to search for when deleting.
     *
     * @return bool Whether the poll meta key was deleted from the database
     */
    function delete_yop_poll_question_meta_by_key( $poll_question_meta_key ) {
        return delete_metadata( 'yop_poll_question', null, $poll_question_meta_key, '', true );
    }

//
// Yop poll answer meta functions
//
    /**
     * Update yop poll answer meta field based on yop poll ID.
     *
     * Use the $prev_value parameter to differentiate between meta fields with the
     * same key and yop poll answer ID.
     *
     * If the meta field for the yop poll answer does not exist, it will be added.
     *
     * @param int    $yop_poll_answer_id Yop Poll ID.
     * @param string $meta_key           Metadata key.
     * @param mixed  $meta_value         Metadata value.
     * @param mixed  $prev_value         Optional. Previous value to check before removing.
     *
     * @return bool False on failure, true if success.
     */
    function update_yop_poll_answer_meta( $poll_answer_id, $meta_key, $meta_value, $prev_value = '' ) {
        return update_metadata( 'yop_poll_answer', $poll_answer_id, $meta_key, $meta_value, $prev_value );
    }

    /**
     * Add yop poll answer data field to a yop poll answer.
     *
     * @param int    $yop_poll_answer_id yop poll answer ID.
     * @param string $meta_key           Metadata name.
     * @param mixed  $meta_value         Metadata value.
     * @param bool   $unique             Optional, default is false. Whether the same key should not be added.
     *
     * @return bool False for failure. True for success.
     */
    function add_yop_poll_answer_meta( $poll_answer_id, $meta_key, $meta_value, $unique = false ) {
        return add_metadata( 'yop_poll_answer', $poll_answer_id, $meta_key, $meta_value, $unique );
    }

    /**
     * Remove metadata matching criteria from a yop_answer_poll.
     *
     * You can match based on the key, or key and value. Removing based on key and
     * value, will keep from removing duplicate metadata with the same key. It also
     * allows removing all metadata matching key, if needed.
     *
     * @param int    $poll_answer_id post ID
     * @param string $meta_key       Metadata name.
     * @param mixed  $meta_value     Optional. Metadata value.
     *
     * @return bool False for failure. True for success.
     */
    function delete_yop_poll_answer_meta( $poll_answer_id, $meta_key, $meta_value = '' ) {
        return delete_metadata( 'yop_poll_answer', $poll_answer_id, $meta_key, $meta_value );
    }

    /**
     * Retrieve yop_poll_answer meta field for a yop poll answer.
     *
     * @param int    $poll_answer_id Yop poll answer ID.
     * @param string $key            Optional. The meta key to retrieve. By default, returns data for all keys.
     * @param bool   $single         Whether to return a single value.
     *
     * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
     *  is true.
     */
    function get_yop_poll_answer_meta( $poll_answer_id, $key = '', $single = false ) {
        return get_metadata( 'yop_poll_answer', $poll_answer_id, $key, $single );
    }

    /**
     * Delete everything from yop_poll_answer meta matching meta key.
     *
     * @param string $poll_meta_key Key to search for when deleting.
     *
     * @return bool Whether the poll meta key was deleted from the database
     */
    function delete_yop_poll_answer_meta_by_key( $poll_answer_meta_key ) {
        return delete_metadata( 'yop_poll_answer', null, $poll_answer_meta_key, '', true );
    }

//
// Yop poll translate functions
//

    function __yop_poll( $text ) {
        return __( $text, YOP_POLL_DOMAIN );
    }

    function _e_yop_poll( $text ) {
        _e( $text, YOP_POLL_DOMAIN );
    }

//
// Yop poll other functions
//

    function yop_poll_create_table_names( $prefix ) {
        $GLOBALS['wpdb']->yop_polls                    = $prefix . 'yop2_polls';
        $GLOBALS['wpdb']->yop_poll_questions           = $prefix . 'yop2_poll_questions';
        $GLOBALS['wpdb']->yop_poll_answers             = $prefix . 'yop2_poll_answers';
        $GLOBALS['wpdb']->yop_pollmeta                 = $prefix . 'yop2_pollmeta';
        $GLOBALS['wpdb']->yop_poll_questionmeta        = $prefix . 'yop2_poll_questionmeta';
        $GLOBALS['wpdb']->yop_poll_answermeta          = $prefix . 'yop2_poll_answermeta';
        $GLOBALS['wpdb']->yop_poll_templates           = $prefix . 'yop2_poll_templates';
        $GLOBALS['wpdb']->yop_poll_custom_fields       = $prefix . 'yop2_poll_custom_fields';
        $GLOBALS['wpdb']->yop_poll_bans                = $prefix . 'yop2_poll_bans';
        $GLOBALS['wpdb']->yop_poll_votes_custom_fields = $prefix . 'yop2_poll_votes_custom_fields';
        $GLOBALS['wpdb']->yop_poll_logs                = $prefix . 'yop2_poll_logs';
        $GLOBALS['wpdb']->yop_poll_results             = $prefix . 'yop2_poll_results';
    }

    function is_foreach_array( $array ) {
        if( is_array( $array ) ) {
            if( count( $array ) > 0 ) {
                return true;
            }
        }

        return false;
    }

    function yop_poll_new_obj( $obj_type, $id, $args ) {
        $obj = null;
        if( $obj_type = 'poll' ) {
            $obj = new YOP_POLL_Poll_Model( $id );
        }
        if( $obj_type = 'question' ) {
            $obj = new YOP_POLL_Question_Model( $id );
        }
        if( $obj_type = 'answer' ) {
            $obj = new YOP_POLL_Answer_Model( $id );
        }

        if( ! isset( $obj->ID ) ) {
            if( isset( $args ) && is_array( $args ) && count( $args ) > 0 ) {
                foreach( $args as $arg_field => $arg_value ) {
                    $obj->$arg_field = $arg_value;
                }
            }
        }

        return $obj;
    }

    function yop_poll_dump( $str ) {
        print "<pre>";
        print_r( $str );
        print "</pre>";
    }

    function yop_poll_kses( $string ) {
        $pt = array(
            'a'   => array(
                'href'   => array(),
                'title'  => array(),
                'target' => array()
            ),
            'img' => array(
                'src'   => array(),
                'title' => array()
            ),
            'br'  => array()
        );

        return wp_kses( stripslashes($string), $pt );
    }

    function yop_poll_base64_encode( $str ) {
        $str = base64_encode( $str );
        $str = str_replace( '/', '-', $str );
        $str = str_replace( '+', '_', $str );

        return $str;
    }

    function yop_poll_base64_decode( $str ) {
        $str = str_replace( '-', '/', $str );
        $str = str_replace( '_', '+', $str );

        return base64_decode( $str );
    }

    function yop_poll_get_mysql_curent_date() {
        return current_time( 'mysql' );
    }

    function yop_poll_get_ip() {
        $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        //check for user proxies
        if( ! empty( $_SERVER['X_FORWARDED_FOR'] ) ) {
            $X_FORWARDED_FOR = explode( ',', $_SERVER['X_FORWARDED_FOR'] );
            if( ! empty( $X_FORWARDED_FOR ) ) {
                $REMOTE_ADDR = trim( $X_FORWARDED_FOR[0] );
            }
        }
        //check for server proxies
        elseif( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $HTTP_X_FORWARDED_FOR = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
            if( ! empty( $HTTP_X_FORWARDED_FOR ) ) {
                $REMOTE_ADDR = trim( $HTTP_X_FORWARDED_FOR[0] );
            }
        }

        return preg_replace( '/[^0-9a-f:\., ]/si', '', $REMOTE_ADDR );
    }

    function yop_poll_set_html_content_type() {
        return 'text/html';
    }

    function insert_log_in_db( $log ) {
        global $wpdb;
        $response['success'] = "";
        $response['error']   = "";

        $sql = $wpdb->query( $wpdb->prepare( "
				INSERT INTO {$wpdb->yop_poll_logs} (
				poll_id,
				vote_id,
				ip ,
				user_id,
				user_type,
				tr_id,
				vote_details,
				user_details,
				vote_date,
				message
				) VALUES ( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s )", $log['poll_id'], $log['vote_id'], $log['ip'], $log['user_id'], $log['user_type'], $log['tr_id'], $log['vote_details'], $log['user_details'],  isset ( $log['vote_date'] ) ? $log['vote_date'] :YOP_POLL_Poll_Model:: get_mysql_curent_date(),$log['message'] ) );
        if( $sql ) {
            $response['success']   = __yop_poll( 'Log added' );
            $response['insert_id'] = $wpdb->insert_id;
        }
        else {
            $response['error'] = __yop_poll( 'Could not insert log into database!' );
        }

        return $response;
    }

    function  insert_custom_field_in_db( $custom ) {
        global $wpdb;
        $wpdb->insert( $wpdb->yop_poll_custom_fields, array(
            'poll_id'      => $custom['poll_id'],
            'question_id'  => $custom['question_id'],
            'custom_field' => $custom['custom_field'],
            'required'     => $custom['required'],
            'status'       => $custom['status']
        ) );

        return $wpdb->insert_id;
    }

    function insert_votes_custom_in_db( $vote ) {
        global $wpdb;
        $wpdb->insert( $wpdb->yop_poll_votes_custom_fields, array(
            'poll_id'            => $vote['poll_id'],
            'question_id'        => $vote['question_id'],
            'custom_field_value' => $vote['custom_field_value'],
            'vote_id'            => $vote['vote_id'],
            'custom_field_id'    => $vote['custom_field_id'],
            'user_id'            => $vote['user_id'],
            'tr_id'              => $vote['tr_id'],
            'vote_date'          => $vote['vote_date']
        ) );
        return $GLOBALS['wpdb']->insert_id;
    }


    function insert_result_in_db( $result ) {
        global $wpdb;
        $response['success'] = "";
        $response['error']   = "";

        $sql = $wpdb->query( $wpdb->prepare( "
				INSERT INTO {$wpdb->yop_poll_results} (
				poll_id,
				vote_id,
				ip ,
				user_id,
				user_type,
				tr_id,
				result_details,
				user_details,
				country,
				vote_date
				) VALUES ( %d, %s, %s, %s, %s, %s, %s, %s,%s, %s)", $result['poll_id'], $result['vote_id'], $result['ip'], $result['user_id'], $result['user_type'], $result['tr_id'], $result['vote_details'], $result['user_details'], $result['country'], isset ( $result ['vote_date'] ) ? $result ['vote_date'] :YOP_POLL_Poll_Model:: get_mysql_curent_date() ) );
        if( $sql ) {
            $response['success']   = __yop_poll( 'Votes added' );
            $response['insert_id'] = $wpdb->insert_id;
        }
        else {
            $response['error'] = __yop_poll( 'Could not insert log into database!' );
        }

        return $response;
    }

    function insert_answer( YOP_POLL_Answer_Model $answer ) {
        $GLOBALS['wpdb']->query( $GLOBALS['wpdb']->prepare( "
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
					", $answer->poll_id, $answer->question_id, $answer->answer, $answer->type, $answer->description, $answer->answer_author, $answer->answer_date, $answer->answer_status, $answer->answer_modified, $answer->question_order, $answer->votes ) );
        $answer->id = $GLOBALS['wpdb']->insert_id;


    }

function convert_date( $original_date, $new_format = '',$c=0 ) {
    if($c==1)
        $original_date=str_replace('-', '/', $original_date);
    return date($new_format, strtotime($original_date));
}

    function quick_sort_asc( $array ) {
        // find array size
        $length = count( $array );

        // base case test, if array of length 0 then just return array to caller
        if( $length <= 1 ) {
            return $array;
        }
        else {

            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $array[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for( $i = 1; $i < count( $array ); $i ++ ) {
                if( $array[$i] < $pivot ) {
                    $left[] = $array[$i];
                }
                else {
                    $right[] = $array[$i];
                }
            }

            // use recursion to now sort the left and right lists
            return array_merge( quick_sort_asc( $left ), array( $pivot ), quick_sort_asc( $right ) );
        }
    }

    function quick_sort_desc( $array ) {
        // find array size
        $length = count( $array );

        // base case test, if array of length 0 then just return array to caller
        if( $length <= 1 ) {
            return $array;
        }
        else {

            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $array[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for( $i = 1; $i < count( $array ); $i ++ ) {
                if( $array[$i] > $pivot ) {
                    $left[] = $array[$i];
                }
                else {
                    $right[] = $array[$i];
                }
            }

            // use recursion to now sort the left and right lists
            return array_merge( quick_sort_desc( $left ), array( $pivot ), quick_sort_desc( $right ) );
        }
    }
function xss_clean($data)
{
// Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

// Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

// Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

// Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);
    $data=filter_var($data, FILTER_SANITIZE_STRING);
// we are done...
    return $data;
}
    function quick_sort_desc_by_votes( $array ) {
        // find array size
        $length = count( $array );

        // base case test, if array of length 0 then just return array to caller
        if( $length <= 1 ) {
            return $array;
        }
        else {

            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $array[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for( $i = 1; $i < count( $array ); $i ++ ) {
                $poll_i   = new YOP_POLL_Poll_Model( $array[$i] );
                $poll_piv = new YOP_POLL_Poll_Model( $pivot );
                if( $poll_i->poll_total_votes > $poll_piv->poll_total_votes ) {
                    $left[] = $array[$i];
                }
                else {
                    $right[] = $array[$i];
                }
            }

            // use recursion to now sort the left and right lists
            return array_merge( quick_sort_desc_by_votes( $left ), array( $pivot ), quick_sort_desc_by_votes( $right ) );
        }
    }
    function yop_poll_ret_poll_by_votes_asc ($array){
        if(count($array)>0){
        $sql        = 'SELECT ID FROM ' . $GLOBALS['wpdb']->yop_polls . ' WHERE (ID='.$array[0].')';

        for( $i = 1; $i < count( $array ); $i ++ ) {
            $sql.='OR (ID= '. $array[$i]. ')';
        }
        $sql.=' ORDER BY poll_total_votes ASC ';
        return $GLOBALS['wpdb']->get_results( $sql ,ARRAY_A);
        }
    }
    function yop_poll_ret_poll_by_votes_desc ($array){
       if(count($array)>0) {
        $sql        = 'SELECT ID, poll_total_votes FROM ' . $GLOBALS['wpdb']->yop_polls . ' WHERE (ID='.$array[0].')';

        for( $i = 1; $i < count( $array ); $i ++ ) {
            $sql.=' OR (ID='. $array[$i]. ')';
        }
        $sql.=' ORDER BY poll_total_votes DESC ';
           return $GLOBALS['wpdb']->get_results( $sql, ARRAY_A );
       }
    }
    function yop_poll_sort_desc_database ($array){
        if(count($array)>0) {
        $sql        = 'SELECT ID FROM ' . $GLOBALS['wpdb']->yop_polls . ' WHERE (ID='.$array[0].')';

        for( $i = 1; $i < count( $array ); $i ++ ) {
            $sql.='OR (ID= '. $array[$i]. ')';
        }
        $sql.=' ORDER BY ID DESC ';
            return $GLOBALS['wpdb']->get_results( $sql, ARRAY_A );
        }
    }
    function yop_poll_sort_asc_database ($array){
        if(count($array)>0)  {
        $sql        = 'SELECT ID FROM ' . $GLOBALS['wpdb']->yop_polls . ' WHERE (ID='.$array[0].')';

        for( $i = 1; $i < count( $array ); $i ++ ) {
            $sql.='OR (ID= '. $array[$i]. ')';
        }
        $sql.=' ORDER BY ID ASC ';
        return $GLOBALS['wpdb']->get_results( $sql, ARRAY_A );
        }
    }
    function quick_sort_asc_by_votes( $array ) {
        // find array size
        $length = count( $array );

        // base case test, if array of length 0 then just return array to caller
        if( $length <= 1 ) {
            return $array;
        }
        else {

            // select an item to act as our pivot point, since list is unsorted first position is easiest
            $pivot = $array[0];

            // declare our two arrays to act as partitions
            $left = $right = array();

            // loop and compare each item in the array to the pivot value, place item in appropriate partition
            for( $i = 1; $i < count( $array ); $i ++ ) {
                $poll_i   = new YOP_POLL_Poll_Model( $array[$i] );
                $poll_piv = new YOP_POLL_Poll_Model( $pivot );
                if( $poll_i->poll_total_votes < $poll_piv->poll_total_votes ) {
                    $left[] = $array[$i];
                }
                else {
                    $right[] = $array[$i];
                }
            }

            // use recursion to now sort the left and right lists
            return array_merge( quick_sort_asc_by_votes( $left ), array( $pivot ), quick_sort_asc_by_votes( $right ) );
        }
    }
function widgets_init(){
    add_action( 'widgets_init', function () {
    return widget_init();
} );
}
