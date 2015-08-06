<?php

class YOP_POLL_General_Options extends YOP_POLL_Abstract_Admin {

    private static $_instance = null;

    protected function __construct() {
        parent::__construct( 'options' );
    }

    public static function get_instance() {
        if( self::$_instance == null ) {
            $class           = __CLASS__;
            self::$_instance = new $class;
        }
        return self::$_instance;
    }

    /** Manage options page actions */

    public function manage_options() {
        switch( $GLOBALS['page'] ) {
            default:
                $this->manage_general_options();
                break;
        }
    }

    private function manage_general_options() {
        global $yop_poll_global_settings;
        switch( $GLOBALS['action'] ) {
            case "after-buy":
                YOP_POLL_Pro_Admin::after_buy();
                break;

            case "do-buy":
                YOP_POLL_Pro_Admin::do_buy();
                break;

            default:
                $this->view_options();
                break;}
    }

    private function view_options() {
        require_once( ABSPATH . '/wp-admin/options-head.php' );
        wp_enqueue_style( 'yop-poll-wizard-css', YOP_POLL_URL . 'css/yop-poll-wizard.css', array(), YOP_POLL_VERSION );
        wp_enqueue_script( 'yop-poll-wizard-js', YOP_POLL_URL . 'js/polls/wizard-options.js', array( 'jquery' ), YOP_POLL_VERSION, true );
        $translation_array = array(
            'next_next' => __( "Next" ),
            'prev_prev' => __yop_poll( "Previous" ),
            'savee' => __('Save'),
            'empty_answer' => __yop_poll( "Please fill in empty answers from Question" )
        );
        wp_localize_script( 'yop-poll-wizard-js', 'button_yop', $translation_array );
        wp_enqueue_script( 'jquery-ui-dialog' );
        $isdone = array( 1 );

        wp_localize_script( 'yop-poll-wizard-js', 'isdone', $isdone );

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
        wp_enqueue_style( 'yop-poll-add-edit-css', YOP_POLL_URL . 'css/polls/add-edit.css', array(), YOP_POLL_VERSION );
        global $page;
        //load all options and display them
        $time_format="H:i:s";
        $options                     = get_option('yop_poll_options' );
        if($options['date_format']=="UE")
            $date_format="d-m-Y";            else{
            $date_format="m-d-Y";
        }
        $data['date_format']=$date_format.' '.$time_format;

        $data['yop_poll_options'] = get_option( 'yop_poll_options', array() );
        $data['title']            = __yop_poll( "General Options" );
        $options                  = $data['yop_poll_options'];
        $args                     = array( 'name'     => 'yop_poll_options[yop_poll_archive_page_id]',
            'selected' => $options['yop_poll_archive_page_id'], 'echo' => false
        );
        $data['poll_archive']     = wp_dropdown_pages( $args );
        $this->display( 'general.html', $data );
    }

    public function manage_load_general_options() {
        wp_enqueue_script( 'yop-poll-general-options', YOP_POLL_URL . 'js/yop-poll-general-options.js', array( 'jquery','jquery-ui-dialog', 'jquery-ui-autocomplete'), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-genesral-options', YOP_POLL_URL . 'js/jquery-textcomplete-master/jquery.textcomplete.js', array( ), YOP_POLL_VERSION, true );
        wp_enqueue_script( 'yop-poll-genesral-options', YOP_POLL_URL . 'js/jquery-textcomplete-master/jquery.js', array( ), YOP_POLL_VERSION, true );
        wp_enqueue_style( 'yop-poll-global-admin-css', YOP_POLL_URL . "css/yop-poll-admin.css", array(), YOP_POLL_VERSION );

    }

    public function general_options_validate( $input ) {

        $default_options   = get_option( 'yop_poll_options', array() );
        $newinput          = $default_options;
        $errors            = '';
        $updated           = '';
        $message_delimiter = '<br>';

        if( current_user_can( 'manage_yop_polls_options' ) ) {

            //<editor-fold desc="User Interface Options">


            //</editor-fold>


            //<editor-fold desc="Poll Options">
            //<editor-fold desc="Poll Start/End Date">
            if( isset( $input['date_format'] ) ) {
                if( $default_options ['date_format'] != trim( $input ['date_format'] ) ) {

                    $newinput ['date_format']=  $input ['date_format'];
                    $updated .= __yop_poll( 'Option "Poll  Date Format" Updated!'  ) . $message_delimiter;


                }
            }
            if( isset( $input['poll_start_date'] ) ) {

                if( '' != $input['poll_start_date'] ) {

                    if( $default_options ['poll_start_date'] != trim( $input ['poll_start_date'] ) ) {
                        $options                     = get_option('yop_poll_options' );
                        if($options['date_format']=="US"){
                            $original1=explode(' ',$input ['poll_start_date']);
                            $original=explode('-',$original1[0]);
                            $input ['poll_start_date']=$original[1].'-'.$original[0].'-'.$original[2].' '.$original1[1];


                        }
                        $newinput ['poll_start_date']=  $input ['poll_start_date'];
                        $updated .= __yop_poll( 'Option "Poll Start Date" Updated!'  ) . $message_delimiter;


                    }
                }
                else {
                    $newinput ['poll_start_date'] = $default_options ['poll_start_date'];

                }
            }
            else {
                $newinput ['poll_start_date'] = $default_options ['poll_start_date'];
            }

            if( isset( $input['poll_never_expire'] ) && 'yes' == $input['poll_never_expire'] ) {
                if( $default_options['poll_end_date'] != $input['poll_end_date'] ) {
                    $newinput['poll_end_date'] = "01-01-2038 23:59:59";
                    $updated .= __yop_poll( 'Option "Poll End Date" Updated!' ) . $message_delimiter;
                }
            }
            else {
                if( isset( $input['poll_end_date'] ) ) {
                    if( '' != $input['poll_end_date'] ) {
                        if( $default_options['poll_end_date'] != $input['poll_end_date'] ) {
                            $options                     = get_option('yop_poll_options' );
                            if($options['date_format']=="US"){
                                $original1=explode(' ',$input ['poll_end_date']);
                                $original=explode('-',$original1[0]);
                                $input ['poll_end_date']=$original[1].'-'.$original[0].'-'.$original[2].' '.$original1[1];


                            }
                            $newinput ['poll_end_date']=$input['poll_end_date'];
                            $updated .= __yop_poll( 'Option "Poll End Date" Updated!' ) . $message_delimiter;
                        }
                    }
                    else {
                        $newinput ['poll_end_date'] = $default_options ['poll_end_date'];
                    }
                }
                else {
                    $newinput ['poll_end_date'] = $default_options ['poll_end_date'];
                }
            }
            //</editor-fold>

            //<editor-fold desc="View Results">
            if( isset ( $input ['view_results'] ) ) {
                if( count( array_intersect( array(
                        'before',
                        'after',
                        'after-poll-end-date',
                        'never',
                        'custom-date'
                    ), $input ['view_results'] ) ) > 0
                ) {
                    if( $default_options ['view_results'] != $input ['view_results'] ) {
                        $newinput ['view_results'] = $input ['view_results'];
                        $updated .= __yop_poll( 'Option "View Results" Updated!' ) . $message_delimiter;
                    }

                    if( in_array( 'custom-date', $newinput ['view_results'] ) ) {
                        if( isset ( $input ['view_results_start_date'] ) ) {
                            if( $default_options ['view_results_start_date'] != trim( $input ['view_results_start_date'] ) ) {
                                $options                     = get_option('yop_poll_options' );
                                if($options['date_format']=="US"){
                                    $original1=explode(' ',$input ['view_results_start_date']);
                                    $original=explode('-',$original1[0]);
                                    $input ['view_results_start_date']=$original[1].'-'.$original[0].'-'.$original[2].' '.$original1[1];


                                }
                                $newinput ['view_results_start_date'] = $input ['view_results_start_date'];
                                $options                     = get_option('yop_poll_options' );

                                $updated .= __yop_poll( 'Option "View Results Custom Date" Updated!' ) . $message_delimiter;
                            }
                        }

                    }
                }
                else {
                    $newinput ['view_results'] = $default_options ['view_results'];
                }
            }
            else {
                $newinput ['view_results'] = $default_options ['view_results'];
            }
            //</editor-fold>

            //<editor-fold desc="View Results Permissions">
            if( isset ( $input ['view_results_permissions'] ) ) {
                if( count( array_intersect( array(
                        'guest',
                        'registered'
                    ), $input ['view_results_permissions'] ) ) > 0
                ) {
                    if( $default_options ['view_results_permissions'] != $input ['view_results_permissions'] ) {
                        $newinput ['view_results_permissions'] = $input ['view_results_permissions'];
                        $updated .= __yop_poll( 'Option "View Results Permissions" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_results_permissions'] = $default_options ['view_results_permissions'];
                }
            }
            else {
                $newinput ['view_results_permissions'] = $default_options ['view_results_permissions'];
            }
            //</editor-fold>

            if( isset ( $input ['view_results_type'] ) ) {
                if( in_array( $input ['view_results_type'], array(
                    'votes-number',
                    'percentages',
                    'votes-number-and-percentages'
                ) )
                ) {
                    if( $default_options ['view_results_type'] != trim( $input ['view_results_type'] ) ) {
                        $newinput ['view_results_type'] = trim( $input ['view_results_type'] );
                        $updated .= __yop_poll( 'Option "View Results Type" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_results_type'] = $default_options ['view_results_type'];
                }
            }
            else {
                $newinput ['view_results_type'] = $default_options ['view_results_type'];
            }

            if( isset ( $input ['answer_result_label'] ) ) {
                if( 'votes-number' == $input ['view_results_type'] ) {
                    if( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-VOTES%' ) === false ) {
                        $newinput ['answer_result_label'] = $default_options ['answer_result_label'];
                    }
                    else {
                        if( $default_options ['answer_result_label'] != trim( $input ['answer_result_label'] ) ) {
                            $newinput ['answer_result_label'] = trim( $input ['answer_result_label'] );
                            $updated .= __yop_poll( 'Option "Poll Answer Result Label" Updated!' ) . $message_delimiter;
                        }
                    }
                }

                if( 'percentages' == $input ['view_results_type'] ) {
                    if( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-PERCENTAGES%' ) === false ) {
                        $newinput ['answer_result_label'] = $default_options ['answer_result_label'];
                    }
                    else {
                        if( $default_options ['answer_result_label'] != trim( $input ['answer_result_label'] ) ) {
                            $newinput ['answer_result_label'] = trim( $input ['answer_result_label'] );
                            $updated .= __yop_poll( 'Option "Poll Answer Result Label" Updated!' ) . $message_delimiter;
                        }
                    }
                }

                if( 'votes-number-and-percentages' == $input ['view_results_type'] ) {
                    if( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-PERCENTAGES%' ) === false ) {
                        $newinput ['answer_result_label'] = $default_options ['answer_result_label'];
                    }
                    elseif( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-VOTES%' ) === false ) {
                        $newinput ['answer_result_label'] = $default_options ['answer_result_label'];
                    }
                    else {
                        if( $default_options ['answer_result_label'] != trim( $input ['answer_result_label'] ) ) {
                            $newinput ['answer_result_label'] = trim( $input ['answer_result_label'] );
                            $updated .= __yop_poll( 'Option "Poll Answer Result Label" Updated!' ) . $message_delimiter;
                        }
                    }
                }
            }
            else {
                $newinput ['answer_result_label'] = $default_options ['answer_result_label'];
            }

            if( isset ( $input ['vote_button_label'] ) ) {
                if( '' != $input ['vote_button_label'] ) {
                    if( $default_options ['vote_button_label'] != trim( $input ['vote_button_label'] ) ) {
                        $newinput ['vote_button_label'] = trim( $input ['vote_button_label'] );
                        $updated .= __yop_poll( 'Option "Vote Button Label" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['vote_button_label'] = $default_options ['vote_button_label'];
                }
            }
            else {
                $newinput ['vote_button_label'] = $default_options ['vote_button_label'];
            }

            if( isset ( $input ['view_results_link'] ) ) {
                if( in_array( $input ['view_results_link'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['view_results_link'] != trim( $input ['view_results_link'] ) ) {
                        $newinput ['view_results_link'] = trim( $input ['view_results_link'] );
                        $updated .= __yop_poll( 'Option "View Results Link" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_results_link'] = $default_options ['view_results_link'];
                }
            }
            else {
                $newinput ['view_results_link'] = $default_options ['view_results_link'];
            }

            if( isset ( $input ['view_results_link_label'] ) ) {
                if( '' != $input ['view_results_link_label'] ) {
                    if( $default_options ['view_results_link_label'] != trim( $input ['view_results_link_label'] ) ) {
                        $newinput ['view_results_link_label'] = trim( $input ['view_results_link_label'] );
                        $updated .= __yop_poll( 'Option "View Results Link Label" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_results_link_label'] = $default_options ['view_results_link_label'];
                }
            }
            else {
                $newinput ['view_results_link_label'] = $default_options ['view_results_link_label'];
            }

            if( isset ( $input ['view_back_to_vote_link'] ) ) {
                if( in_array( $input ['view_back_to_vote_link'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['view_back_to_vote_link'] != trim( $input ['view_back_to_vote_link'] ) ) {
                        $newinput ['view_back_to_vote_link'] = trim( $input ['view_back_to_vote_link'] );
                        $updated .= __yop_poll( 'Option "View Back To Vote Link" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_back_to_vote_link'] = $default_options ['view_back_to_vote_link'];
                }
            }
            else {
                $newinput ['view_back_to_vote_link'] = $default_options ['view_back_to_vote_link'];
            }

            if( isset ( $input ['view_back_to_vote_link_label'] ) ) {
                if( '' != $input ['view_back_to_vote_link_label'] ) {
                    if( $default_options ['view_back_to_vote_link_label'] != trim( $input ['view_back_to_vote_link_label'] ) ) {
                        $newinput ['view_back_to_vote_link_label'] = trim( $input ['view_back_to_vote_link_label'] );
                        $updated .= __yop_poll( 'Option "View Back to Vote Link Label" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_back_to_vote_link_label'] = $default_options ['view_back_to_vote_link_label'];
                }
            }
            else {
                $newinput ['view_back_to_vote_link_label'] = $default_options ['view_back_to_vote_link_label'];
            }

            if( isset ( $input ['view_total_votes'] ) ) {
                if( in_array( $input ['view_total_votes'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['view_total_votes'] != trim( $input ['view_total_votes'] ) ) {
                        $newinput ['view_total_votes'] = trim( $input ['view_total_votes'] );
                        $updated .= __yop_poll( 'Option "View Total Votes" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_total_votes'] = $default_options ['view_total_votes'];
                }
            }
            else {
                $newinput ['view_total_votes'] = $default_options ['view_total_votes'];
            }

            if( isset ( $input ['view_total_votes_label'] ) ) {
                if( stripos( $input ['view_total_votes_label'], '%POLL-TOTAL-VOTES%' ) === false ) {
                    $newinput ['view_total_votes_label'] = $default_options ['view_total_votes_label'];
                }
                else {
                    if( $default_options ['view_total_votes_label'] != trim( $input ['view_total_votes_label'] ) ) {
                        $newinput ['view_total_votes_label'] = trim( $input ['view_total_votes_label'] );
                        $updated .= __yop_poll( 'Option "View Total Votes Label" Updated!' ) . $message_delimiter;
                    }
                }
            }
            else {
                $newinput ['view_total_votes_label'] = $default_options ['view_total_votes_label'];
            }

            if( isset ( $input ['view_total_answers'] ) ) {
                if( in_array( $input ['view_total_answers'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['view_total_answers'] != trim( $input ['view_total_answers'] ) ) {
                        $newinput ['view_total_answers'] = trim( $input ['view_total_answers'] );
                        $updated .= __yop_poll( 'Option "View Total Answers" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_total_answers'] = $default_options ['view_total_answers'];
                }
            }
            else {
                $newinput ['view_total_answers'] = $default_options ['view_total_answers'];
            }

            if( isset ( $input ['view_total_answers_label'] ) ) {
                if( stripos( $input ['view_total_answers_label'], '%POLL-TOTAL-ANSWERS%' ) === false ) {
                    $newinput ['view_total_answers_label'] = $default_options ['view_total_answers_label'];
                }
                else {
                    if( $default_options ['view_total_answers_label'] != trim( $input ['view_total_answers_label'] ) ) {
                        $newinput ['view_total_answers_label'] = trim( $input ['view_total_answers_label'] );
                        $updated .= __yop_poll( 'Option "View Total Answers Label" Updated!' ) . $message_delimiter;
                    }
                }
            }
            else {
                $newinput ['view_total_answers_label'] = $default_options ['view_total_answers_label'];
            }

            if( isset ( $input ['message_after_vote'] ) ) {
                if( $default_options ['message_after_vote'] != trim( $input ['message_after_vote'] ) ) {
                    $newinput ['message_after_vote'] = trim( $input ['message_after_vote'] );
                    $updated .= __yop_poll( 'Option "Message After Vote" Updated!' ) . $message_delimiter;
                }
            }

            if( isset ( $input ['vote_permisions'] ) ) {
                if( count( array_intersect( array(
                        'guest',
                        'registered'
                    ), $input ['vote_permisions'] ) ) > 0
                ) {
                    if( $default_options ['vote_permisions'] != $input ['vote_permisions'] ) {
                        $newinput ['vote_permisions'] = $input ['vote_permisions'];
                        $updated .= __yop_poll( 'Option "Vote Permissions" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['vote_permisions'] = $default_options ['vote_permisions'];
                }
            }
            else {
                $newinput ['vote_permisions'] = $default_options ['vote_permisions'];
            }

            //<editor-fold desc="Vote As Facebook">

            //</editor-fold>

            //<editor-fold desc="Vote as Wordpress">
            if( isset( $input['vote_permisions_wordpress'] ) && in_array( $input['vote_permisions_wordpress'], array(
                    'yes',
                    'no'
                ) )
            ) {
                if( $default_options ['vote_permisions_wordpress'] != trim( $input ['vote_permisions_wordpress'] ) ) {
                    $newinput ['vote_permisions_wordpress'] = trim( $input ['vote_permisions_wordpress'] );
                    $updated .= __yop_poll( 'Option "Vote as Wordpress User" Updated!' ) . $message_delimiter;
                }
            }
            else {
                $newinput['vote_permisions_wordpress'] = $default_options['vote_permisions_wordpress'];
            }

            if( isset( $input ['vote_permisions_wordpress_label'] ) && '' != trim( $input ['vote_permisions_wordpress_label'] ) ) {
                if( $default_options ['vote_permisions_wordpress_label'] != trim( $input ['vote_permisions_wordpress_label'] ) ) {
                    $newinput ['vote_permisions_wordpress_label'] = trim( $input ['vote_permisions_wordpress_label'] );
                    $updated .= __yop_poll( 'Option "Vote as Wordpress User Buton Label" Updated!' ) . $message_delimiter;
                }
            }
            else {
                $newinput['vote_permisions_wordpress_label'] = $default_options['vote_permisions_wordpress_label'];
            }
            //</editor-fold>

            //<editor-fold desc="Vote as Anonymous">
            if( isset( $input['vote_permisions_anonymous'] ) && in_array( $input['vote_permisions_anonymous'], array(
                    'yes',
                    'no'
                ) )
            ) {
                if( $default_options ['vote_permisions_anonymous'] != trim( $input ['vote_permisions_anonymous'] ) ) {
                    $newinput ['vote_permisions_anonymous'] = trim( $input ['vote_permisions_anonymous'] );
                    $updated .= __yop_poll( 'Option "Vote as Anonymous User" Updated!' ) . $message_delimiter;
                }
            }
            else {
                $newinput['vote_permisions_anonymous'] = $default_options['vote_permisions_anonymous'];
            }

            if( isset( $input ['vote_permisions_anonymous_label'] ) && '' != trim( $input ['vote_permisions_anonymous_label'] ) ) {
                if( $default_options ['vote_permisions_anonymous_label'] != trim( $input ['vote_permisions_anonymous_label'] ) ) {
                    $newinput ['vote_permisions_anonymous_label'] = trim( $input ['vote_permisions_anonymous_label'] );
                    $updated .= __yop_poll( 'Option "Vote as Anonymous User Buton Label" Updated!' ) . $message_delimiter;
                }
            }
            else {
                $newinput['vote_permisions_anonymous_label'] = $default_options['vote_permisions_anonymous_label'];
            }
            //</editor-fold>

            if( isset ( $input ['blocking_voters'] ) ) {
                if( count( array_intersect( array(
                        'dont-block',
                        'cookie',
                        'ip',
                        'user_id',
                        'supercookie'
                    ), $input ['blocking_voters'] ) ) > 0
                ) {
                    if( $default_options ['blocking_voters'] != $input ['blocking_voters'] ) {
                        $newinput ['blocking_voters'] = $input ['blocking_voters'];
                        $updated .= __( 'Option "Blocking Voters" Updated!', 'yop_poll' ) . $message_delimiter;
                    }

                    if( ! in_array( 'dont-block', $newinput ['blocking_voters'] ) ) {
                        // blocking_voters_interval_value
                        if( isset ( $input ['blocking_voters_interval_value'] ) ) {
                            if( ctype_digit( $input ['blocking_voters_interval_value'] ) ) {
                                if( $default_options ['blocking_voters_interval_value'] != trim( $input ['blocking_voters_interval_value'] ) ) {
                                    $newinput ['blocking_voters_interval_value'] = trim( $input ['blocking_voters_interval_value'] );
                                    $updated .= __yop_poll( 'Option "Blocking Voters Interval Value" Updated!' ) . $message_delimiter;
                                }
                            }
                            else {
                                $newinput ['blocking_voters_interval_value'] = $default_options ['blocking_voters_interval_value'];
                            }
                        }

                        // blocking_voters_interval_unit
                        if( isset ( $input ['blocking_voters_interval_unit'] ) ) {
                            if( in_array( $input ['blocking_voters_interval_unit'], array(
                                'seconds',
                                'minutes',
                                'hours',
                                'days'
                            ) )
                            ) {
                                if( $default_options ['blocking_voters_interval_unit'] != trim( $input ['blocking_voters_interval_unit'] ) ) {
                                    $newinput ['blocking_voters_interval_unit'] = trim( $input ['blocking_voters_interval_unit'] );
                                    $updated .= __yop_poll( 'Option "Blocking Voters Interval Unit" Updated!' ) . $message_delimiter;
                                }
                            }
                            else {
                                $newinput ['blocking_voters_interval_unit'] = $default_options ['blocking_voters_interval_unit'];
                            }
                        }
                    }
                }
                else {
                    $newinput ['blocking_voters'] = $default_options ['blocking_voters'];
                }
            }
            else {
                $newinput ['blocking_voters'] = $default_options ['blocking_voters'];
            }
            if( isset ( $input ['limit_number_of_votes_per_user'] ) ) {
                if( in_array( $input ['limit_number_of_votes_per_user'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['limit_number_of_votes_per_user'] != trim( $input ['limit_number_of_votes_per_user'] ) ) {
                        $newinput ['limit_number_of_votes_per_user'] = trim( $input ['limit_number_of_votes_per_user'] );
                        $updated .= __yop_poll( 'Option "Limit Number of Votes per User" Updated!' ) . $message_delimiter;
                    }

                    if( 'yes' == $input ['limit_number_of_votes_per_user'] ) {
                        if( isset ( $input ['number_of_votes_per_user'] ) ) {
                            if( intval( $input ['number_of_votes_per_user'] ) <= 0 ) {
                                $newinput ['number_of_votes_per_user'] = $default_options ['number_of_votes_per_user'];
                            }
                            else {
                                if( $default_options ['number_of_votes_per_user'] != $input ['number_of_votes_per_user'] ) {
                                    $newinput ['number_of_votes_per_user'] = trim( $input ['number_of_votes_per_user'] );
                                    $updated .= __yop_poll( 'Option "Number of Votes per User" Updated!' ) . $message_delimiter;
                                }
                            }
                        }
                        else {
                            $newinput ['number_of_votes_per_user'] = $default_options ['number_of_votes_per_user'];
                        }
                    }
                }
                else {
                    $newinput ['limit_number_of_votes_per_user'] = $default_options ['limit_number_of_votes_per_user'];
                }
            }
            else {
                $newinput ['limit_number_of_votes_per_user'] = $default_options ['limit_number_of_votes_per_user'];
            }

            if( isset ( $input ['percentages_decimals'] ) && '' != trim( $input ['percentages_decimals'] ) ) {
                if( ctype_digit( $input ['percentages_decimals'] ) ) {
                    if( $default_options ['percentages_decimals'] != trim( $input ['percentages_decimals'] ) ) {
                        $newinput ['percentages_decimals'] = trim( $input ['percentages_decimals'] );
                        $updated .= __yop_poll( 'Option "Percentages Decimals" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['percentages_decimals'] = $default_options ['percentages_decimals'];
                }
            }
            else {
                $newinput ['percentages_decimals'] = $default_options ['percentages_decimals'];
            }

            if( isset ( $input ['use_default_loading_image'] ) && in_array( $input ['use_default_loading_image'], array( 'yes',
                    'no'
                ) )
            ) {
                if( $default_options ['use_default_loading_image'] != trim( $input ['use_default_loading_image'] ) ) {
                    $newinput ['use_default_loading_image'] = trim( $input ['use_default_loading_image'] );
                    $updated .= __yop_poll( 'Option "Use Default Loading Image" Updated!' ) . $message_delimiter;
                }

                if( 'no' == $input ['use_default_loading_image'] ) {
                    if( isset ( $input ['loading_image_url'] ) ) {
                        if( stripos( $input ['loading_image_url'], 'http' ) === false ) {
                            $newinput ['loading_image_url'] = $default_options ['loading_image_url'];
                        }
                        else {
                            if( $default_options ['loading_image_url'] != trim( $input ['loading_image_url'] ) ) {
                                $newinput ['loading_image_url'] = trim( $input ['loading_image_url'] );
                                $updated .= __yop_poll( 'Option "Loading Image Url" Updated!' ) . $message_delimiter;
                            }
                        }
                    }
                }
            }
            else {
                $newinput ['use_default_loading_image'] = $default_options ['use_default_loading_image'];
            }

            if( isset ( $input ['redirect_after_vote'] ) ) {
                if( in_array( $input ['redirect_after_vote'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['redirect_after_vote'] != trim( $input ['redirect_after_vote'] ) ) {
                        $newinput ['redirect_after_vote'] = trim( $input ['redirect_after_vote'] );
                        $updated .= __yop_poll( 'Option "Redirect After Vote" Updated!' ) . $message_delimiter;
                    }

                    if( 'yes' == $input ['redirect_after_vote'] ) {
                        // archive_order
                        if( isset ( $input ['redirect_after_vote_url'] ) && '' != trim( $input ['redirect_after_vote_url'] ) ) {
                            if( $default_options ['redirect_after_vote_url'] != trim( $input ['redirect_after_vote_url'] ) ) {
                                $newinput ['redirect_after_vote_url'] = trim( $input ['redirect_after_vote_url'] );
                                $updated .= __yop_poll( 'Option "Redirect After Vote Url" Updated!' ) . $message_delimiter;
                            }
                        }
                        else {
                            $newinput ['redirect_after_vote_url'] = $default_options ['redirect_after_vote_url'];
                        }
                    }
                }
                else {
                    $newinput ['redirect_after_vote'] = $default_options ['redirect_after_vote'];
                }
            }
            else {
                $newinput ['redirect_after_vote'] = $default_options ['redirect_after_vote'];
            }

            if( isset ( $input ['date_format'] ) && '' != trim( $input ['date_format'] ) ) {
                if( $default_options ['date_format'] != trim( $input ['date_format'] ) ) {
                    $newinput ['date_format'] = trim( $input ['date_format'] );
                    $updated .= __yop_poll( 'Option "Poll Date Format" Updated!' ) . $message_delimiter;
                }
            }


            if( isset ( $input ['view_poll_archive_link'] ) ) {
                if( in_array( $input ['view_poll_archive_link'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['view_poll_archive_link'] != trim( $input ['view_poll_archive_link'] ) ) {
                        $newinput ['view_poll_archive_link'] = trim( $input ['view_poll_archive_link'] );
                        $updated .= __yop_poll( 'Option "View Poll Archive Link" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['view_poll_archive_link'] = $default_options ['view_poll_archive_link'];
                }
            }
            else {
                $newinput ['view_poll_archive_link'] = $default_options ['view_poll_archive_link'];
            }

            if( isset ( $input ['auto_generate_poll_page'] ) ) {
                if( in_array( $input['auto_generate_poll_page'], array( 'yes', 'no' ) ) ) {
                    if( $default_options['auto_generate_poll_page'] != trim( $input['auto_generate_poll_page'] ) ) {
                        $newinput ['auto_generate_poll_page'] = trim( $input ['auto_generate_poll_page'] );
                        $updated .= __yop_poll( 'Option "Auto Generate Poll Page" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['auto_generate_poll_page'] = $default_options ['auto_generate_poll_page'];
                }
            }
            else {
                $newinput ['auto_generate_poll_page'] = $default_options ['auto_generate_poll_page'];
            }

            if( isset ( $input ['use_captcha'] ) ) {
                if( in_array( $input ['use_captcha'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['use_captcha'] != trim( $input ['use_captcha'] ) ) {
                        $newinput ['use_captcha'] = trim( $input ['use_captcha'] );
                        $updated .= __yop_poll( 'Option "Use CAPTCHA" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['use_captcha'] = $default_options ['use_captcha'];
                }
            }
            else {
                $newinput ['use_captcha'] = $default_options ['use_captcha'];
            }

            if( isset ( $input ['send_email_notifications'] ) ) {
                if( in_array( $input ['send_email_notifications'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['send_email_notifications'] != trim( $input ['send_email_notifications'] ) ) {
                        $newinput ['send_email_notifications'] = trim( $input ['send_email_notifications'] );
                        $updated .= __( 'Option "Send Email Notifications" Updated!', 'yop_poll' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['send_email_notifications'] = $default_options ['send_email_notifications'];
                }
            }
            else {
                $newinput ['send_email_notifications'] = $default_options ['send_email_notifications'];
            }
            //</editor-fold>

            /* Start Questions Options */
            if( isset ( $input ['allow_other_answers'] ) ) {
                if( in_array( $input ['allow_other_answers'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['allow_other_answers'] != trim( $input ['allow_other_answers'] ) ) {
                        $newinput ['allow_other_answers'] = trim( $input ['allow_other_answers'] );
                        $updated .= __yop_poll( 'Option "Allow Other Answer" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['allow_other_answers'] = $default_options ['allow_other_answers'];
                }

                if( 'yes' == $input ['allow_other_answers'] ) {
                    // other_answers_label
                    if( isset ( $input ['other_answers_label'] ) ) {
                        if( $default_options ['other_answers_label'] != trim( $input ['other_answers_label'] ) ) {
                            $newinput ['other_answers_label'] = trim( $input ['other_answers_label'] );
                            $updated .= __yop_poll( 'Option "Other Answer Label" Updated!' ) . $message_delimiter;
                        }
                    }

                    if( isset( $input ['is_default_other_answer'] ) ) {
                        if( in_array( $input ['is_default_other_answer'], array( 'yes', 'no' ) ) ) {
                            if( $default_options ['is_default_other_answer'] != trim( $input ['is_default_other_answer'] ) ) {
                                $newinput ['is_default_other_answer'] = trim( $input ['is_default_other_answer'] );
                                $updated .= __yop_poll( 'Option "Make \'Other answer\' default answer" Updated!' ) . $message_delimiter;
                            }
                        }
                        else {
                            $newinput ['is_default_other_answer'] = $default_options ['is_default_other_answer'];
                        }
                    }

                    if( isset ( $input ['add_other_answers_to_default_answers'] ) ) {
                        if( in_array( $input ['add_other_answers_to_default_answers'], array( 'yes', 'no' ) ) ) {
                            if( $default_options ['add_other_answers_to_default_answers'] != trim( $input ['add_other_answers_to_default_answers'] ) ) {
                                $newinput ['add_other_answers_to_default_answers'] = trim( $input ['add_other_answers_to_default_answers'] );
                                $updated .= __yop_poll( 'Option "Add the values submitted in \'Other\' as answers" Updated!' ) . $message_delimiter;
                            }
                        }
                        else {
                            $newinput ['add_other_answers_to_default_answers'] = $default_options ['add_other_answers_to_default_answers'];
                        }
                    }

                    if( isset ( $input ['display_other_answers_values'] ) ) {
                        if( in_array( $input ['display_other_answers_values'], array( 'yes', 'no' ) ) ) {
                            if( $default_options ['display_other_answers_values'] != trim( $input ['display_other_answers_values'] ) ) {
                                $newinput ['display_other_answers_values'] = trim( $input ['display_other_answers_values'] );
                                $updated .= __yop_poll( 'Option "Display Other Answers Values" Updated!' ) . $message_delimiter;
                            }
                        }
                        else {
                            $newinput ['display_other_answers_values'] = $default_options ['display_other_answers_values'];
                        }
                    }
                }
            }

            if( isset ( $input ['allow_multiple_answers'] ) ) {
                if( in_array( $input ['allow_multiple_answers'], array( 'yes', 'no' ) ) ) {
                    if( $default_options ['allow_multiple_answers'] != trim( $input ['allow_multiple_answers'] ) ) {
                        $newinput ['allow_multiple_answers'] = trim( $input ['allow_multiple_answers'] );
                        $updated .= __yop_poll( 'Option "Allow Multiple Answers" Updated!' ) . $message_delimiter;
                    }

                    // allow_multiple_answers_number
                    if( 'yes' == $input ['allow_multiple_answers'] ) {
                        if( isset ( $input ['allow_multiple_answers_number'] ) ) {
                            if( ctype_digit( $input ['allow_multiple_answers_number'] ) ) {
                                if( $default_options ['allow_multiple_answers_number'] != trim( $input ['allow_multiple_answers_number'] ) ) {
                                    $newinput ['allow_multiple_answers_number'] = trim( $input ['allow_multiple_answers_number'] );
                                    $updated .= __yop_poll( 'Option "Max Number of allowed answers" Updated!' ) . $message_delimiter;
                                }
                            }
                            else {
                                $newinput ['allow_multiple_answers_number'] = $default_options ['allow_multiple_answers_number'];
                            }
                        }
                        if( isset ( $input ['allow_multiple_answers_min_number'] ) ) {
                            if( ctype_digit( $input ['allow_multiple_answers_min_number'] ) ) {
                                if( $default_options ['allow_multiple_answers_min_number'] != trim( $input ['allow_multiple_answers_min_number'] ) ) {
                                    $newinput ['allow_multiple_answers_min_number'] = trim( $input ['allow_multiple_answers_min_number'] );
                                    $updated .= __yop_poll( 'Option "Min Number of allowed answers" Updated!' ) . $message_delimiter;
                                }
                            }
                            else {
                                $newinput ['allow_multiple_answers_min_number'] = $default_options ['allow_multiple_answers_min_number'];
                            }
                        }
                    }
                }
                else {
                    $newinput ['allow_multiple_answers'] = $default_options ['allow_multiple_answers'];
                }
            }

            if( isset ( $input ['display_answers'] ) ) {
                if( in_array( $input ['display_answers'], array( 'vertical', 'orizontal', 'tabulated' ) ) ) {
                    if( $default_options ['display_answers'] != trim( $input ['display_answers'] ) ) {
                        $newinput ['display_answers'] = trim( $input ['display_answers'] );
                        $updated .= __yop_poll( 'Option "Display Answers" Updated!' ) . $message_delimiter;
                    }

                    if( 'tabulated' == $input ['display_answers'] ) {
                        // display_answers_tabulated_cols
                        if( isset ( $input ['display_answers_tabulated_cols'] ) ) {
                            if( ctype_digit( $input ['display_answers_tabulated_cols'] ) ) {
                                if( $default_options ['display_answers_tabulated_cols'] != trim( $input ['display_answers_tabulated_cols'] ) ) {
                                    $newinput ['display_answers_tabulated_cols'] = trim( $input ['display_answers_tabulated_cols'] );
                                    $updated .= __yop_poll( 'Option "Columns for Tabulated Display Answers" Updated!' ) . $message_delimiter;
                                }
                            }
                            else {
                                $newinput ['display_answers_tabulated_cols'] = $default_options ['display_answers_tabulated_cols'];
                            }
                        }
                    }
                }
                else {
                    $newinput ['display_answers'] = $default_options ['display_answers'];
                }
            }
            /* End Questions Options */


            /* Start Results Options */
            if( isset ( $input ['sorting_results'] ) ) {
                if( in_array( $input ['sorting_results'], array(
                    'as_defined',
                    'alphabetical',
                    'votes'
                ) )
                ) {
                    if( $default_options ['sorting_results'] != trim( $input ['sorting_results'] ) ) {
                        $newinput ['sorting_results'] = trim( $input ['sorting_results'] );
                        $updated .= __yop_poll( 'Option "Sort Results" Updated!' ) . $message_delimiter;
                    }

                    // sorting_results_direction
                    if( isset ( $input ['sorting_results_direction'] ) ) {
                        if( in_array( $input ['sorting_results_direction'], array( 'asc', 'desc' ) ) ) {
                            if( $default_options ['sorting_results_direction'] != trim( $input ['sorting_results_direction'] ) ) {
                                $newinput ['sorting_results_direction'] = trim( $input ['sorting_results_direction'] );
                                $updated .= __yop_poll( 'Option "Sort Results Direction" Updated!' ) . $message_delimiter;
                            }
                        }
                        else {
                            $newinput ['sorting_results_direction'] = $default_options ['sorting_results_direction'];
                        }
                    }
                }
                else {
                    $newinput ['sorting_results'] = $default_options ['sorting_results'];
                }
            }

            if( isset ( $input ['singular_answer_result_votes_number_label'] ) ) {
                if( '' != $input ['singular_answer_result_votes_number_label'] ) {
                    if( $default_options ['singular_answer_result_votes_number_label'] != trim( $input ['singular_answer_result_votes_number_label'] ) ) {
                        $newinput ['singular_answer_result_votes_number_label'] = trim( $input ['singular_answer_result_votes_number_label'] );
                        $updated .= __yop_poll( 'Option "Poll Answer Result Votes Number Singular Label" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['singular_answer_result_votes_number_label'] = $default_options ['singular_answer_result_votes_number_label'];
                }
            }

            if( isset ( $input ['plural_answer_result_votes_number_label'] ) ) {
                if( '' != $input ['plural_answer_result_votes_number_label'] ) {
                    if( $default_options ['plural_answer_result_votes_number_label'] != trim( $input ['plural_answer_result_votes_number_label'] ) ) {
                        $newinput ['plural_answer_result_votes_number_label'] = trim( $input ['plural_answer_result_votes_number_label'] );
                        $updated .= __yop_poll( 'Option "Poll Answer Result Votes Number Plural Label" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['plural_answer_result_votes_number_label'] = $default_options ['plural_answer_result_votes_number_label'];
                }
            }

            if( isset ( $input ['display_results'] ) ) {
                if( in_array( $input ['display_results'], array( 'vertical', 'orizontal', 'tabulated' ) ) ) {
                    if( $default_options ['display_results'] != trim( $input ['display_results'] ) ) {
                        $newinput ['display_results'] = trim( $input ['display_results'] );
                        $updated .= __yop_poll( 'Option "Display Results" Updated!' ) . $message_delimiter;
                    }

                    if( 'tabulated' == $input ['display_results'] ) {
                        // display_results_tabulated_cols
                        if( isset ( $input ['display_results_tabulated_cols'] ) ) {
                            if( ctype_digit( $input ['display_results_tabulated_cols'] ) ) {
                                if( $default_options ['display_results_tabulated_cols'] != trim( $input ['display_results_tabulated_cols'] ) ) {
                                    $newinput ['display_results_tabulated_cols'] = trim( $input ['display_results_tabulated_cols'] );
                                    $updated .= __yop_poll( 'Option "Columns for Tabulated Display Results" Updated!' ) . $message_delimiter;
                                }
                            }
                            else {
                                $newinput ['display_results_tabulated_cols'] = $default_options ['display_results_tabulated_cols'];
                            }
                        }
                    }
                }
                else {
                    $newinput ['display_results'] = $default_options ['display_results'];
                }
            }
            /* Start Poll bar style*/
            if( isset ( $input ['bar_background'] ) ) {
                if( ctype_alnum( $input ['bar_background'] ) ) {
                    if( $default_options ['bar_background'] != trim( $input ['bar_background'] ) ) {
                        $newinput ['bar_background'] = trim( $input ['bar_background'] );
                        $updated .= __yop_poll( 'Option "Result Bar Background Color" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['bar_background'] = $default_options ['bar_background'];
                }
            }
            if( isset ( $input ['bar_height'] ) ) {
                if( ctype_digit( $input ['bar_height'] ) ) {
                    if( $default_options ['bar_height'] != trim( $input ['bar_height'] ) ) {
                        $newinput ['bar_height'] = trim( $input ['bar_height'] );
                        $updated .= __yop_poll( 'Option "Result Bar Height" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['bar_height'] = $default_options ['bar_height'];
                }
            }
            if( isset ( $input ['bar_border_color'] ) ) {
                if( ctype_alnum( $input ['bar_border_color'] ) ) {
                    if( $default_options ['bar_border_color'] != trim( $input ['bar_border_color'] ) ) {
                        $newinput ['bar_border_color'] = trim( $input ['bar_border_color'] );
                        $updated .= __yop_poll( 'Option "Result Bar Border Color" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['bar_border_color'] = $default_options ['bar_border_color'];
                }
            }
            if( isset ( $input ['bar_border_width'] ) ) {
                if( ctype_digit( $input ['bar_border_width'] ) ) {
                    if( $default_options ['bar_border_width'] != trim( $input ['bar_border_width'] ) ) {
                        $newinput ['bar_border_width'] = trim( $input ['bar_border_width'] );
                        $updated .= __yop_poll( 'Option "Result Bar Border Width" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['bar_border_width'] = $default_options ['bar_border_width'];
                }
            }
            if( isset ( $input ['bar_border_style'] ) ) {
                if( ctype_alpha( $input ['bar_border_style'] ) ) {
                    if( $default_options ['bar_border_style'] != trim( $input ['bar_border_style'] ) ) {
                        $newinput ['bar_border_style'] = trim( $input ['bar_border_style'] );
                        $updated .= __yop_poll( 'Option "Result Bar Border Style" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['bar_border_style'] = $default_options ['bar_border_style'];
                }
            }
            /* End Poll bar style*/
            /* End Results Options */


            /* Start Archive Options */
            if( isset( $input['sorting_archive_polls'] ) ) {
                if( in_array( $input['sorting_archive_polls'], array( 'as_defined', 'database', 'votes' ) ) ) {
                    if( $default_options['sorting_archive_polls'] != trim( $input['sorting_archive_polls'] ) ) {
                        $newinput['sorting_archive_polls'] = trim( $input['sorting_archive_polls'] );
                        $updated .= __yop_poll( 'Option "Sort Archive Polls!" Updated' ) . $message_delimiter;
                    }
                    if( 'as_defined' != trim( $input['sorting_archive_polls'] ) ) {
                        if( isset( $input['sorting_archive_polls_rule'] ) ) {
                            if( $default_options['sorting_archive_polls_rule'] != trim( $input['sorting_archive_polls_rule'] ) ) {
                                $newinput['sorting_archive_polls_rule'] = trim( $input['sorting_archive_polls_rule'] );
                                $updated .= __yop_poll( 'Option "Sort Archive Polls Rule!" Updated' ) . $message_delimiter;
                            }
                        }
                        else {
                            $newinput = $default_options['sorting_archive_polls_rule'];
                        }
                    }
                }
                else {
                    $newinput ['sorting_archive_polls'] = $default_options ['sorting_archive_polls'];
                }
            }

            if( isset ( $input ['yop_poll_archive_page_id'] ) ) {
                if( '' != $input ['yop_poll_archive_page_id'] ) {
                    if( $default_options ['yop_poll_archive_page_id'] != trim( $input ['yop_poll_archive_page_id'] ) ) {
                        $newinput ['yop_poll_archive_page_id'] = trim( $input ['yop_poll_archive_page_id'] );
                        $updated .= __yop_poll( 'Option "Archive Page" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['yop_poll_archive_page_id'] = $default_options ['yop_poll_archive_page_id'];
                }
            }

            if( isset ( $input ['archive_link_label'] ) ) {
                if( '' != $input ['archive_link_label'] ) {
                    if( $default_options ['archive_link_label'] != trim( $input ['archive_link_label'] ) ) {
                        $newinput ['archive_link_label'] = trim( $input ['archive_link_label'] );
                        $updated .= __yop_poll( 'Option "Poll Archive Link Label" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['archive_link_label'] = $default_options ['archive_link_label'];
                }
            }

            if( isset ( $input ['archive_polls_per_page'] ) ) {
                if( false != ( intval( trim( $input ['archive_polls_per_page'] ) ) ) ) {
                    if( $default_options ['archive_polls_per_page'] != intval( trim( $input ['archive_polls_per_page'] ) ) ) {
                        $newinput ['archive_polls_per_page'] = intval( trim( $input ['archive_polls_per_page'] ) );
                        $updated .= __yop_poll( 'Option "Polls Per Page" Updated' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['archive_polls_per_page'] = $default_options ['archive_polls_per_page'];
                }
            }
            /* End Archive Options */

            /* Start Email Options */
            if( isset ( $input ['email_notifications_from_name'] ) ) {
                if( '' != $input ['email_notifications_from_name'] ) {
                    if( $default_options ['email_notifications_from_name'] != trim( $input ['email_notifications_from_name'] ) ) {
                        $newinput ['email_notifications_from_name'] = trim( $input ['email_notifications_from_name'] );
                        $updated .= __yop_poll( 'Option "Notifications From Name" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['email_notifications_from_name'] = $default_options ['email_notifications_from_name'];
                }
            }

            if( isset ( $input ['email_notifications_from_email'] ) ) {
                if( '' != $input ['email_notifications_from_email'] ) {
                    if( $default_options ['email_notifications_from_email'] != trim( $input ['email_notifications_from_email'] ) ) {
                        $newinput ['email_notifications_from_email'] = trim( $input ['email_notifications_from_email'] );
                        $updated .= __yop_poll( 'Option "Notifications From Email" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['email_notifications_from_email'] = $default_options ['email_notifications_from_email'];
                }
            }

            if( isset ( $input ['email_notifications_recipients'] ) ) {
                if( '' != $input ['email_notifications_recipients'] ) {
                    if( $default_options ['email_notifications_recipients'] != trim( $input ['email_notifications_recipients'] ) ) {
                        $newinput ['email_notifications_recipients'] = trim( $input ['email_notifications_recipients'] );
                        $updated .= __yop_poll( 'Option "Email Notifications Recipients" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['email_notifications_recipients'] = $default_options ['email_notifications_recipients'];
                }
            }

            if( isset ( $input ['email_notifications_subject'] ) ) {
                if( '' != $input ['email_notifications_subject'] ) {
                    if( $default_options ['email_notifications_subject'] != trim( $input ['email_notifications_subject'] ) ) {
                        $newinput ['email_notifications_subject'] = trim( $input ['email_notifications_subject'] );
                        $updated .= __yop_poll( 'Option "Email Notifications Subject" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['email_notifications_subject'] = $default_options ['email_notifications_subject'];
                }
            }

            if( isset ( $input ['email_notifications_body'] ) ) {
                if( '' != $input ['email_notifications_body'] ) {
                    if( $default_options ['email_notifications_body'] != trim( $input ['email_notifications_body'] ) ) {
                        $newinput ['email_notifications_body'] = trim( $input ['email_notifications_body'] );
                        $updated .= __yop_poll( 'Option "Email Notifications Body" Updated!' ) . $message_delimiter;
                    }
                }
                else {
                    $newinput ['email_notifications_body'] = $default_options ['email_notifications_body'];
                }
            }
            /* End Email Options */
        }
        else {
            $errors .= __yop_poll( 'Bad Request!' ) . $message_delimiter;
        }

        if( '' != $errors ) {
            add_settings_error( 'general', 'yop-poll-errors', $errors, 'error' );
        }
        if( '' != $updated ) {
            add_settings_error( 'general', 'yop-poll-updates', "Changes saved!", 'updated' );
        }

        return $newinput;
    }
}

?>
