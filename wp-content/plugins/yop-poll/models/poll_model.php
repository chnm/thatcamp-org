<?php

Class YOP_POLL_Poll_Model extends YOP_POLL_Abstract_Model
{

    protected $type = 'poll';

    function __construct($id = 0, $is_view_results = 'no', $question_sort = "poll_order", $question_sort_rule = "ASC", $answer_sort = "question_order", $answer_sort_rule = "ASC")
    {

        parent::__construct($id, $is_view_results, $question_sort, $question_sort_rule, $answer_sort, $answer_sort_rule);

    }

    public static function return_template_preview_html($template_id = '', $loc = 1)
    {

        if ('' == $template_id) {
            return "";
        }
        else {
            $uID = uniqid('t');
            $poll = new YOP_POLL_Poll_Model();
            $template_details = self::get_poll_template_from_database(intval($template_id));
            $template = $template_details['before_vote_template'];
            $template = stripslashes_deep($template);

            $template = str_ireplace('%POLL-NAME%', "Poll Name", $template);
            $template = str_ireplace('%POLL-VOTE-BUTTON%', '<button class="yop_poll_vote_button" onclick="return false;">Vote</button>', $template);

            $question = new YOP_POLL_Question_Model();

            $question->question = "Poll Question";
            $question->allow_multiple_answers = "no";
            $question->allow_other_answers = "no";

            for ($i = 0; $i < 5; $i++) {
                $a = new YOP_POLL_Answer_Model();
                $j = $i + 1;
                $a->ID = $i + 1;
                $a->answer = "Answer {$j}";

                $question->addAnswer($a);
                unset($a);
            }
            $poll->questions = $question;
            $t = $template;
            $pattern = '\[(\[?)(QUESTION_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            preg_match("/$pattern/s", $t, $m);
            //print_r($m);
            $m = $m[5];

            $m = str_ireplace("%POLL-QUESTION%", $question->question, $m);

            $pattern = '/\[(\[?)(ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/';
            preg_match($pattern, $m, $m1);
            $m1 = $m1[5];

            $ts = "";

            /** Start Answer Description replace */
            $pattern = '\[(\[?)(ANSWER_DESCRIPTION_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            $m1 = preg_replace("/$pattern/s", "", $m1);
            /** End Answer Description replace */

            /** Start Answer Result replace */
            $pattern = '\[(\[?)(ANSWER_RESULT_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            $m1 = preg_replace("/$pattern/s", "", $m1);
            /** End Answer Result replace */

            foreach ($question->answers as $answer) {
                $temps = str_ireplace('%POLL-ANSWER-CHECK-INPUT%', '<input type="radio" value="' . $answer->ID . '" name="yop_poll_answer-' . $uID . '" id="yop-poll-answer-' . $uID . '-' . $answer->ID . '" />', $m1);
                $temps = str_ireplace('%POLL-ANSWER-LABEL%', '<label>' . $answer->answer . '</label>', $temps);
                $ts .= $temps;
            }

            $pattern = '/\[(\[?)(ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/';
            $m = preg_replace($pattern, $ts, $m);

            $pattern = '\[(\[?)(QUESTION_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            $template = preg_replace("/$pattern/s", $m, $template);

            $pattern = array(
                '/\[(\[?)(OTHER_ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/',
                '/\[(\[?)(CUSTOM_FIELD_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/',
                '/\[(\[?)(ANSWER_RESULT_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/',
                '/\[(\[?)(CAPTCHA_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/'
            );
            $template = preg_replace($pattern, "", $template);
            $template = preg_replace('/\[\/?QUESTION_CONTAINER\]/', "", $template);
            $template = str_ireplace("%POLL-ID%", "preview-" . $uID, $template);
            $template = self::strip_all_tags($template);

            $t = '<style type="text/css">' . $poll->return_poll_css($template_details['css'], array(
                    "location" => 'page',
                    'preview' => true,
                    'template_id' => $uID,
                    'loc' => $loc
                )) . '</style>';
            $t .= '<div id="yop-poll-container-preview-' . $uID . '" class="yop-poll-container" style="position: relative; z-index: 1;">';
            $t .= '' . $template . '</div>';

            $qID = uniqid('q');
            $t = str_ireplace("%QUESTION-ID%", $qID, $t);
            return $t;
        }
    }

    private static function strip_all_tags($template)
    {

        $tags = array(
            '%CAPTCHA-PLAY%',
            '%CAPTCHA-LABEL%',
            '%RELOAD-CAPTCHA-IMAGE%',
            '%CAPTCHA-IMAGE%',
            '%CAPTCHA-INPUT%',
            '%POLL-VIEW-ARCHIVE-LINK%',
            '%POLL-PAGE-URL%',
            '%POLL-VOTE-BUTTON%',
            '%POLL-START-DATE%',
            '%POLL-END-DATE%',
            '%POLL-ANSWER-RESULT-LABEL%',
            '%POLL-BACK-TO-VOTE-LINK%',
            '%POLL-VIEW-RESULT-LINK%',
            '%POLL-TOTAL-VOTERS%',
            '%POLL-TOTAL-ANSWERS%',
            '%POLL-TOTAL-VOTES%',
            '%POLL-ID%',
            '%POLL-NAME%',
            '%POLL-QUESTION%',
            '%POLL-ANSWER-RESULT-VOTES%',
            '%POLL-ANSWER-RESULT-PERCENTAGES%',
            '%POLL-ANSWER-RESULT-LABEL%',
            '%POLL-ANSWER-LABEL%',
            '%POLL-ANSWER-RESULT-BAR%',
            '%POLL-CUSTOM-FIELD-LABEL%',
            '%POLL-CUSTOM-FIELD-TEXT-INPUT%',
            '%POLL-OTHER-ANSWER-CHECK-INPUT%',
            '%POLL-OTHER-ANSWER-LABEL%',
            '%POLL-OTHER-ANSWER-TEXT-INPUT%',
            '%POLL-ANSWER-RESULT%',
            '%POLL-OTHER-ANSWER-RESULT%',
            '%POLL-ANSWER-RESULT-VOTES%',
            '%POLL-OTHER-ANSWER-RESULT-VOTES%',
            '%POLL-ANSWER-RESULT-PERCENTAGES%',
            '%POLL-OTHER-ANSWER-RESULT-PERCENTAGES%',
            '%POLL-ANSWER-CHECK-INPUT%',
            '%POLL-ANSWER-LABEL%',
            '%POLL-ANSWER-RESULT%',
            '%POLL-TOTAL-ANSWERS-LABEL%',
            '%POLL-TOTAL-VOTES-LABEL%',
            '%SHARE-BUTTON%'
        );

        foreach ($tags as $tag) {
            $template = str_ireplace($tag, '', $template);
        }
        return $template;
    }

    public function return_poll_css(
        $css = "", $attr = array(
                     'location' => 'page',
                     'preview' => false,
                     'template_id' => '',
                     'loc' => 1
                 )
    )
    {
        $preview = isset($attr['preview']) ? $attr['preview'] : false;
        $location = isset($attr['location']) ? $attr['location'] : 'page';
        if ($preview) {
            $template = $css;
            $template .= "li.yop-poll-li-answer-%POLL-ID% {width:100%}";
            $template .= "div.yop-poll-answers-%POLL-ID% ul{width:100%}";
            $template = str_ireplace("%POLL-ID%", 'preview-' . $attr['template_id'] . '', $template);
            $template = str_ireplace("%POLL-WIDTH%", '200px', $template);
            return stripslashes($template);
        } else {
            $unique_id = $this->ID . $this->unique_id;

            /*if ( !$poll_id ){
            return '';
            }
            if ( 'widget' == $location ){
            $template_id = $this->widget_template;
            }
            else {
            $template_id = $this->template;
            }

            if ( '' == $template_id ){
            $default_template = self::get_poll_template_from_database();
            $template_id      = $default_template['id'] ? $default_template['id'] : 0;
            }
            $template_details = self::get_poll_template_from_database( $template_id );
            $template         = $template_details['css'];*/

            $template = str_ireplace('%POLL-ID%', $unique_id, $css);
            if ('widget' == $location) {
                $template = str_ireplace('%POLL-WIDTH%', $this->widget_template_width, $template);
            } else {
                $template = str_ireplace('%POLL-WIDTH%', $this->template_width, $template);
            }
            return stripslashes($template);
        }
    }

    private static function count_other_answers($question)
    {
        $n = count($question->answers);
        $nr = 0;
        for ($i = 0; $i < $n; $i++) {
            if ($question->answers[$i]->type == "other") {
                $nr++;
            }
        }
        return $nr;
    }

    public function return_poll_js($attr = array('location' => 'page'))
    {
        $poll_id = $this->ID;
        $location = isset($attr['location']) ? $attr['location'] : 'page';
        $unique_id = $this->unique_id;

        if (!$poll_id) {
            return '';
        }

        if ('widget' == $location) {
            $template_id = $this->widget_template;
        } else {
            $template_id = $this->template;
        }

        if ('' == $template_id) {
            //get default template
            $template_details = self::get_poll_template_from_database();
        } else {
            $template_details = self::get_poll_template_from_database($template_id);
        }

        $tabulate = array();

        foreach ($this->questions as $question) {
            $answers_tabulated_cols = 1; //vertical display
            $results_tabulated_cols = 1;

            $include_others = false;
            $display_answers = array('text', 'image', 'video');

            if (isset($question->allow_other_answers) && 'yes' == $question->allow_other_answers) {

                if (isset($question->display_other_answers_values) && 'yes' == $question->display_other_answers_values) {
                    $include_others = true;
                    $display_answers = array('text', 'image', 'video', 'other');
                }
            }


            if ('orizontal' == $question->display_answers) {
                $ans_no = $question->countanswers($display_answers, $include_others);
                if ($ans_no > 0) {
                    $answers_tabulated_cols = $ans_no;
                }
                if (isset($question->allow_other_answers) && 'yes' == $question->allow_other_answers) {
                    $answers_tabulated_cols++;
                }
            } else
                if ('tabulated' == $question->display_answers) {
                    $answers_tabulated_cols = $question->display_answers_tabulated_cols;
                    //yop_poll_dump($answers_tabulated_cols);

                }

            if ('orizontal' == $question->display_results) {
                $ans_no = $question->countanswers($display_answers, $include_others);
                if ($ans_no > 0) {
                    $results_tabulated_cols = $ans_no;
                }
            }
            else if ('tabulated' == $question->display_results) {
                $results_tabulated_cols = $question->display_results_tabulated_cols;
            }
            if( !isset($ans_per_question) ){
              $ans_per_question = '';
            }
            array_push($tabulate, array($answers_tabulated_cols, $results_tabulated_cols, $ans_per_question, $question->ID));
        }

        $template = $template_details['js'];
        if ('vertical' == $question->display_answers) {
            $template .= "jQuery(document).ready(function(){ jQuery('.yop-poll-li-answer-%POLL-ID%').css('float','none');});";

        }
        $template = str_ireplace('%POLL-ID%', $poll_id . $unique_id, $template);
        $template = str_ireplace('%ANSWERS-TABULATED-COLS%', json_encode($tabulate), $template);
        $template = str_ireplace('%POLL-WIDTH%', str_replace("px", "", $this->template_width), $template);
        $template = str_ireplace('%RESULTS-TABULATED-COLS%', json_encode($tabulate), $template);
        return stripslashes($template);
    }

    public function question_replace_callback($m)
    {
        $is_voted = $this->is_voted();
        $return_string = "";
        $that = $this;
        foreach ($this->questions as $question) {

            $qunique_id = $question->ID;
            $temp = str_ireplace('%QUESTION-ID%', $qunique_id, $m[5]);
            $temp = str_ireplace('%POLL-QUESTION%', $question->question, $temp);

            $temp = str_ireplace('class = ' . '"' . 'yop-poll-li-answer-' . $this->ID . $this->unique_id, 'class=' . '"' . 'yop-poll-li-answer-' . $this->ID . $this->unique_id . ' yop-poll-li-answer-' . $this->ID . $this->unique_id . "-" . $question->ID, $temp);

            if (!$is_voted) {
                /** Start Anwer replace */
                if ($this->count_answers($question) > 0) {
                    $pattern = '\[(\[?)(ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
                    $temp = preg_replace_callback("/$pattern/s", function ($m) use ($that, $question) {
                        return $that->answer_replace_callback($m[5], $question);
                    }, $temp);
                }
                /** End Anwer replace */

                /** Start Other Answer replace */
                $pattern = '\[(\[?)(OTHER_ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
                $temp = str_ireplace('class = ' . '"' . 'yop-poll-li-answer-' . $this->ID . $this->unique_id, 'class=' . '"' . 'yop-poll-li-answer-' . $this->ID . $this->unique_id . ' yop-poll-li-answer-' . $this->ID . $this->unique_id . "-" . $question->ID, $temp);

                $temp = preg_replace_callback("/$pattern/s", function ($m) use ($that, $question) {
                    return $that->other_answer_replace_callback($m[5], $question);
                }, $temp);
                /** End Other Answer replace */

                /** Start Custom Fields replace*/
                $pattern = '\[(\[?)(CUSTOM_FIELD_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
                $temp = preg_replace_callback("/$pattern/s", function ($m) use ($that, $question) {
                    return $that->custom_field_replace_callback($m[5], $question);
                }, $temp);
                /** End Custom Fields replace*/
            }
            $pattern = '\[(\[?)(ANSWER_RESULT_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            $temp = preg_replace_callback("/$pattern/s", function ($m) use ($that, $question) {
                return $that->answer_result_replace_callback($m[5], $question);
            }, $temp);

            $return_string .= $temp;
        }
        return $return_string;
    }

    private static function count_answers($question)
    {
        $n = count($question->answers);
        $nr = 0;
        for ($i = 0; $i < $n; $i++) {
            if (in_array($question->answers[$i]->type, array('text'))) {
                $nr++;
            }
        }
        return $nr;
    }

    public function answer_replace_callback($m, $question)
    {
        $unique_id = $this->unique_id;

        $multiple_answers = false;
        if ($question->allow_multiple_answers == 'yes') {
            $multiple_answers = true;
        }

        $model = "";

        /** Get question total votes( default + other ) */

        $total_votes = $this->get_question_votes($question);

        /**Is allowed to display other answers?*/
        $display_other_answers = false;

        if (isset($question->allow_other_answers) && 'yes' == $question->allow_other_answers) {
            if (isset($question->display_other_answers_values) && 'yes' == $question->display_other_answers_values) {
                $display_other_answers = true;
            }
        }

        $percentages_decimals = 0;
        if (isset($this->percentages_decimals)) {
            $percentages_decimals = $this->percentages_decimals;
        }

        $id = $this->ID;

        $view_results = $this->is_view_poll_results();

        $that = $this;
        foreach ($question->answers as $answer) {
            /**Check if is allowed to display current answers*/
            if (($answer->type == "other") && !$display_other_answers) {
                continue;
            }
            if ($view_results) {
                if ($answer->votes > 0) {
                    if (isset($total_votes) && $total_votes > 0) {
                        $percentages = floatval($answer->votes * 100 / $total_votes);
                    }
                } else {
                    $percentages = 0;
                }
            }
            if (function_exists('icl_translate')) {
                $answer->answer = icl_translate('yop_poll', $answer->ID . '_answer', $answer->answer);
            }
            if ($multiple_answers) {
                if (isset($answer->is_default_answer) && $answer->is_default_answer == "yes") {
                    $temp_answer_model = str_ireplace('%POLL-ANSWER-CHECK-INPUT%', '<input type="checkbox" checked="checked" value="' . $answer->ID . '" name="yop_poll_answer[' . $question->ID . '][]" id="yop-poll-answer-' . $this->ID . $unique_id . '-' . $answer->ID . '" />', $m);
                } else {
                    $temp_answer_model = str_ireplace('%POLL-ANSWER-CHECK-INPUT%', '<input type="checkbox" value="' . $answer->ID . '" name="yop_poll_answer[' . $question->ID . '][]" id="yop-poll-answer-' . $this->ID . $unique_id . '-' . $answer->ID . '" />', $m);
                }
            } else {
                if (isset($answer->is_default_answer) && $answer->is_default_answer == "yes") {
                    $temp_answer_model = str_ireplace('%POLL-ANSWER-CHECK-INPUT%', '<input type="radio" checked="checked" value="' . $answer->ID . '" name="yop_poll_answer[' . $question->ID . ']" id="yop-poll-answer-' . $this->ID . $unique_id . '-' . $answer->ID . '" />', $m);
                } else {
                    $temp_answer_model = str_ireplace('%POLL-ANSWER-CHECK-INPUT%', '<input type="radio" value="' . $answer->ID . '" name="yop_poll_answer[' . $question->ID . ']" id="yop-poll-answer-' . $this->ID . $unique_id . '-' . $answer->ID . '" />', $m);
                }
            }

            /** Start Answer Description replace */
            $pattern = '\[(\[?)(ANSWER_DESCRIPTION_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            $temp_answer_model = preg_replace_callback("/$pattern/s", function ($m) use ($that, $answer, $id, $unique_id) {
                return $that->answer_description_replace_callback($m[5], $answer, $id, $unique_id);
            }, $temp_answer_model);
            /** End Answer Description replace */
            if ($view_results) {
                /** Start Answer Result replace */
                $pattern = '\[(\[?)(ANSWER_RESULT_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
                $temp_answer_model = preg_replace_callback("/$pattern/s", function ($m) use ($that, $answer, $view_results, $id, $unique_id, $percentages, $percentages_decimals) {
                    return $that->answer_result_bar_callback($m[5], $answer, $view_results, $id, $unique_id, $percentages, $percentages_decimals);
                }, $temp_answer_model);
                /** End Answer Result replace */
            }
            if ($answer->type == "text") {
                $temp_answer_model = str_ireplace('%POLL-ANSWER-LABEL%', '<label for="yop-poll-answer-' . $this->ID . $unique_id . '-' . $answer->ID . '">' . yop_poll_kses(stripslashes($answer->answer)) . '</label>', $temp_answer_model);
            }
            if ($answer->type == "other") {
                $temp_answer_model = str_ireplace('%POLL-ANSWER-LABEL%', '<label  style="    cursor: pointer;" for="yop-poll-answer-' . $this->ID . $unique_id . '-' . $answer->ID . '">' . yop_poll_kses(stripslashes($answer->answer)) . '</label>', $temp_answer_model);
            }

            $model .= $temp_answer_model;
        }

        return $model;
    }

    private static function get_question_votes($question)
    {
        $total_votes = 0;
        foreach ($question->answers as $answer) {
            $total_votes += intval($answer->votes);
        }
        return $total_votes;
    }

    private function is_view_poll_results()
    {
        $is_voted = $this->is_voted();
        if (((in_array('before', $this->view_results)) || (in_array('after', $this->view_results) && $is_voted) || (in_array('custom-date', $this->view_results) && self::get_mysql_curent_date() >= convert_date($this->view_results_start_date, 'Y-m-d H:i:s', 1)) || (in_array('after-poll-end-date', $this->view_results) && self::get_mysql_curent_date() >= convert_date($this->poll_end_date, 'Y-m-d H:i:s'))) && !in_array('never', $this->view_results) && ((in_array('guest', $this->view_results_permissions) && !is_user_logged_in()) || (in_array('registered', $this->view_results_permissions) && is_user_logged_in()))
        ) {
            return true;
        }
        return false;
    }

    public function answer_description_replace_callback($m, $answer, $id, $unique_id)
    {
        if ("" == $answer->description) {
            return "";
        }
        return str_ireplace("%ANSWER-DESCRIPTION%", '<label for="yop-poll-answer-' . $id . $unique_id . '-' . $answer->ID . '">' . yop_poll_kses(stripslashes($answer->description)) . '</label>', $m);
    }

    public function answer_result_bar_callback($m, $answer, $view_results, $id, $unique_id, $percentages, $percentages_decimals)
    {
        if ($view_results) {
            $tmp = str_ireplace('%POLL-ANSWER-RESULT-BAR%', self::display_poll_result_bar($answer->ID, $percentages, $this->options, $id . $unique_id), $m);
            $tmp = str_ireplace('%POLL-ANSWER-RESULT-VOTES%', self::display_poll_result_votes($answer->votes, $this->options), $tmp);
            $tmp = str_ireplace('- (  )', self::display_poll_result_votes($answer->votes, $this->options), $tmp);
            $tmp = str_ireplace('%POLL-ANSWER-RESULT-PERCENTAGES%', self::display_poll_result_percentages(round($percentages, $percentages_decimals), $this->options), $tmp);
            return $tmp;
        } else {
            return "";
        }
    }

    private static function display_poll_result_bar($answer_id = 0, $procent = 0, $options = array(), $unique_id = '')
    {
        $result_bar = ' <div class="yop-poll-results-bar-' . $unique_id . '" ';
        $result_bar .= ' ';
        $result_bar .= '><div>';
        if (floatval($procent) > 0) {

            $result_bar .= '<div style="' . 'width:' . $procent . '%; ';
            $result_bar .= 'height:' . $options['bar_height'] . 'px; ' . 'background-color:#' . $options['bar_background'] . '; ' . 'border-style:' . $options['bar_border_style'] . '; ' . 'border-width:' . $options['bar_border_width'] . 'px; ' . 'border-color:#' . $options['bar_border_color'] . '; ';
            $result_bar .= '" ' . 'id="yop-poll-result-bar-div-' . $answer_id . '" ' . 'class="yop-poll-result-bar-div-' . $unique_id . '"' . '>' . '</div>';
        }
        $result_bar .= '</div></div>';
        return $result_bar;
    }

    private static function display_poll_result_votes($votes = 0, $options = array())
    {

        if ('votes-number' == $options['view_results_type'] || 'votes-number-and-percentages' == $options['view_results_type']) {
            if ('1' == $votes) {
                $options = get_option('yop_poll_options');
                return $votes . ' ' . $options['singular_answer_result_votes_number_label'];
            } else {
                $options = get_option('yop_poll_options');
                return $votes . ' ' . $options['plural_answer_result_votes_number_label'];
            }
        }
    }

    private static function display_poll_result_percentages($votes, $options = array())
    {
        if ('percentages' == $options['view_results_type'] || 'votes-number-and-percentages' == $options['view_results_type']) {
            return $votes . '%';
        } else {
            return '';
        }
    }

    public function other_answer_replace_callback($m, $question)
    {
        $unique_id = $this->unique_id;

        $multiple_answers = false;
        if ($question->allow_multiple_answers == 'yes') {
            $multiple_answers = true;
        }

        $model = "";

        $allow_other_answers = false;
        $display_other_answers = false;
        if (isset($question->allow_other_answers) && $question->allow_other_answers == 'yes') {
            $allow_other_answers = true;
            if (isset($question->add_other_answers_to_default_answers) && ('yes' == $question->add_other_answers_to_default_answers)) {
                $display_other_answers = true;
            }
        }

        if ($allow_other_answers) {
            /**Display other answer input */
            if (function_exists('icl_translate')) {
                $other_answer_label = icl_translate('yop_poll', $this->ID . '_other_answer_label', yop_poll_kses($question->other_answers_label));
            } else {
                $other_answer_label = yop_poll_kses($question->other_answers_label);
            }

            if ($multiple_answers) {
                $temp_answer_model = str_ireplace('%POLL-OTHER-ANSWER-CHECK-INPUT%', '<input type="checkbox" value="other" name="yop_poll_answer[' . $question->ID . '][]" id="yop-poll-answer-' . $this->ID . $unique_id . '-' . $question->ID . '-other" />', $m);
            } else {
                $temp_answer_model = str_ireplace('%POLL-OTHER-ANSWER-CHECK-INPUT%', '<input type="radio" value="other" name="yop_poll_answer[' . $question->ID . ']" id="yop-poll-answer-' . $this->ID . $unique_id . '-' . $question->ID . '-other" />', $m);
            }
            $temp_answer_model = str_ireplace('%POLL-OTHER-ANSWER-LABEL%', '<label  for="yop-poll-answer-' . $this->ID . $unique_id . '-' . $question->ID . '-other">' . $other_answer_label . '</label>', $temp_answer_model);
            $temp_answer_model = str_ireplace('%POLL-OTHER-ANSWER-TEXT-INPUT%', '<label style="width:100%;"><input onclick="document.getElementById(\'yop-poll-answer-' . $this->ID . $unique_id . '-' . $question->ID . '-other' . '\').checked=true;" type="text" value="" name="yop_poll_other_answer[' . $question->ID . ']" id="yop-poll-other-answer-' . $this->ID . $unique_id . '-other" /></label>', $temp_answer_model);

            if ($this->is_view_poll_results()) {
                /**Display only if other answers were not displayed */
                if (!$display_other_answers) {
                    /** Count question total votes( default + other ) */
                    $total_votes = $this->get_question_votes($question);

                    /** Count other answers votes*/
                    $other_votes = $this->get_question_other_votes($question);


                    $percentages_decimals = 0;
                    if (isset($this->percentages_decimals)) {
                        $percentages_decimals = $this->percentages_decimals;
                    }

                    if ($other_votes > 0) {
                        $percentages = floatval($other_votes * 100 / $total_votes);
                    } else {
                        $percentages = 0;
                    }
                    if ($this->is_view_poll_results()) {
                        $temp_answer_model = str_ireplace('%POLL-OTHER-ANSWER-RESULT-BAR%', self::display_poll_result_bar('other', $percentages, $this->options, $this->ID . $unique_id), $temp_answer_model);
                        $temp_answer_model = str_ireplace('%POLL-ANSWER-RESULT-VOTES%', self::display_poll_result_votes($other_votes, $this->options), $temp_answer_model);
                        $temp_answer_model = str_ireplace('- (  )', self::display_poll_result_votes($other_votes, $this->options), $temp_answer_model);

                        $temp_answer_model = str_ireplace('%POLL-ANSWER-RESULT-PERCENTAGES%', self::display_poll_result_percentages(round($percentages, $percentages_decimals), $this->options), $temp_answer_model);
                    }
                }
            }
            $model = $temp_answer_model;
        }
        return $model;
    }

    private static function get_question_other_votes($question)
    {
        $total_votes = 0;
        foreach ($question->answers as $answer) {
            if ($answer->type == 'other') {
                $total_votes += intval($answer->votes);
            }
        }
        return $total_votes;
    }

    public function custom_field_replace_callback($m, $question)
    {
        $unique_id = $this->unique_id;
        $is_voted = $this->is_voted();
        $model = "";
        if (!$is_voted) {
            if (count($question->custom_fields) > 0) {
                foreach ($question->custom_fields as $custom_field) {
                    if (function_exists('icl_translate')) {
                        $custom_field['custom_field'] = icl_translate('yop_poll', $custom_field->ID . '_custom_field', $custom_field->custom_field);
                    }
                    $temp_string = str_ireplace('%POLL-CUSTOM-FIELD-LABEL%', '<label for="yop-poll-customfield-' . $this->ID . $unique_id . '-' . $custom_field->ID . '">' . yop_poll_kses($custom_field->custom_field) . '</label>', $m);

                    $temp_string = str_ireplace('%POLL-CUSTOM-FIELD-TEXT-INPUT%', '<input type="text" value="" name="yop_poll_customfield[' . $question->ID . '][' . $custom_field->ID . ']" id="yop-poll-customfield-' . $this->ID . $unique_id . '-' . $custom_field->ID . '" class=' . '"yop-poll-customfield-' . $this->ID . $unique_id . '"/>', $temp_string);
                    $model .= $temp_string;
                }
            }
        }
        return $model;
    }

    public function answer_result_replace_callback($m, $question)
    {
        $unique_id = $this->unique_id;
        $return_string = '';
        $is_voted = $this->is_voted();
        $id = $this->ID;
        if ($this->is_view_poll_results()) {
            $display_other_answers = false;
            if ('yes' == $question->allow_other_answers) {
                if ('yes' == $question->display_other_answers_values) {
                    $display_other_answers = true;
                }
            }

            $percentages_decimals = 0;
            if (isset($this->percentages_decimals)) {
                $percentages_decimals = $this->percentages_decimals;
            }
            if (isset($this->sorting_results)) {
                error_log('maybe here');
                if ('as_defined' == $this->sorting_results) {
                    $question->sortAnswers('question_order', 'asc');
                } elseif ('database' == $this->sorting_results) {
                    $order_dir = 'asc';
                    if (isset($this->sorting_results_direction)) {
                        $order_dir = ('asc' == $this->sorting_results_direction) ? 'asc' : 'desc';
                    }
                    $question->sortAnswers('ID', $order_dir);
                } elseif ('alphabetical' == $this->sorting_results) {
                    $order_dir = 'asc';
                    if (isset($this->sorting_results_direction)) {
                        $order_dir = ('asc' == $this->sorting_results_direction) ? 'asc' : 'desc';
                    }
                    $question->sortAnswers('alphabetical', $order_dir);
                } elseif ('votes' == $this->sorting_results) {
                    $order_dir = 'asc';
                    if (isset($this->sorting_results_direction)) {
                        $order_dir = ('asc' == $this->sorting_results_direction) ? 'asc' : 'desc';
                    }
                    $question->sortAnswers('votes', $order_dir);
                } else {
                    $order_dir = 'asc';
                    if (isset($this->sorting_results_direction)) {
                        $order_dir = ('asc' == $this->sorting_results_direction) ? 'asc' : 'desc';
                    }
                    $question->sortAnswers('question_order', $order_dir);
                }
            } else {
                $order_dir = 'asc';
                if (isset($this->sorting_results_direction)) {
                    $order_dir = ('asc' == $this->sorting_results_direction) ? 'asc' : 'desc';
                }
                $question->sortAnswers('question_order', $order_dir);
            }


            $total_votes = $this->get_question_votes($question);

            foreach ($question->answers as $ans) {
                if (($ans->type == "other") && !$display_other_answers) {
                    continue;
                }
                if ($ans->votes > 0) {
                    $percentages = floatval($ans->votes * 100 / $total_votes);
                } else {
                    $percentages = 0;
                }

                if (function_exists('icl_translate')) {
                    $ans->answer = icl_translate('yop_poll', $ans->ID . '_answer', $ans->answer);
                }

                /** Start Answer Description replace */
                $pattern = '\[(\[?)(ANSWER_DESCRIPTION_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
                $temp_string = preg_replace("/$pattern/s", "", $m);
                /** End Answer Description replace */
                $ans->type = "text";
                if ($ans->type == "text") {
                    $temp_string = str_ireplace('%POLL-ANSWER-LABEL%', '<span>' . yop_poll_kses(stripslashes($ans->answer)) . '</span>', $temp_string);
                }
                $temp_string = str_ireplace('class = ' . '"' . 'yop-poll-li-result-' . $this->ID . $this->unique_id, 'class=' . '"' . 'yop-poll-li-result-' . $this->ID . $this->unique_id . ' yop-poll-li-result-' . $this->ID . $this->unique_id . "-" . $question->ID, $temp_string);


                $temp_string = str_ireplace('%POLL-ANSWER-RESULT-VOTES%', self::display_poll_result_votes($ans->votes, $this->options), $temp_string);
                $temp_string = str_ireplace('- (  )', self::display_poll_result_votes($ans->votes, $this->options), $temp_string);
                $temp_string = str_ireplace('%POLL-ANSWER-RESULT-PERCENTAGES%', self::display_poll_result_percentages(round($percentages, $percentages_decimals), $this->options), $temp_string);
                $temp_string = str_ireplace('%POLL-ANSWER-RESULT-BAR%', self::display_poll_result_bar($ans->ID, $percentages, $this->options, $this->ID . $unique_id), $temp_string);
                $return_string .= $temp_string;
            }
        }

        return $return_string;
    }

    public function register_vote($request)
    {

        global $current_user;
        $poll_id = $this->id;
        $unique_id = strip_tags(xss_clean($this->unique_id));
        $location = strip_tags(xss_clean($request['location']));
        $vote_id = uniqid('vote_id_');
        $vote_type = $request['vote_type'];
        $SuperCookie = strip_tags(xss_clean($request['supercookie']));
        $tr_id = strip_tags(xss_clean($request['yop_poll_tr_id']));
        $votes = 0;
        $user_id = 0;
        $user_type = 'default';
        $user_details = '';
        if (wp_verify_nonce($request['yop-poll-nonce-' . $poll_id . $unique_id], 'yop_poll-' . $this->ID . $unique_id . '-user-actions')) {
            switch ($vote_type) {
                default:
                    {
                    $user_id = ($current_user->ID != null) ? $current_user->ID : 0;
                    $user_type = 'default';
                    break;
                    }
                case 'wordpress':
                {
                    $user_id = $current_user->ID;
                    $user_type = 'wordpress';
                    break;
                }
                case 'anonymous':
                {
                    $user_type = 'anonymous';
                    break;
                }

            }
            $log_to_add = array(
                'poll_id' => $this->ID,
                'vote_id' => $vote_id,
                'ip' => yop_poll_get_ip(),
                'user_id' => $user_id,
                'user_type' => $user_type,
                'user_details' => json_encode($user_details),
                'tr_id' => $tr_id,
            );
        } else {
            $this->error = __yop_poll('Bad Request!');
            $log_to_add ['message'] = __yop_poll('Bad request');
            insert_log_in_db($log_to_add);
            return false;
        }
        if (wp_verify_nonce($request['yop-poll-nonce-' . $this->ID . $unique_id], 'yop_poll-' . $this->ID . $unique_id . '-user-actions')) {

            $tr_id = $request['yop_poll_tr_id'];
            $super_perm = true;
            if (in_array("supercookie", $this->blocking_voters)) {
                $super_perm = $this->is_voted_supercookie($SuperCookie);
            }
            if ($super_perm == true) {

                $current_date = yop_poll_get_mysql_curent_date();
                if ($this->is_allowed_to_vote($vote_type)) {
                    if ($current_date >= convert_date($this->poll_start_date, 'Y-m-d H:i:s')) {
                        if ($current_date <= convert_date($this->poll_end_date, 'Y-m-d H:i:s')) {
                            if ('closed' == $this->status) {
                                $this->error = __yop_poll('This poll is closed!');
                                $log_to_add ['message'] = __yop_poll('Poll Closed');
                                insert_log_in_db($log_to_add);
                                return false;
                            } else {
                                if (!$this->is_voted($vote_type, $facebook_user_details, $google_user_details, true, $SuperCookie)) {
                                    $voter = array();
                                    $voter['poll_id'] = $poll_id;
                                    $voter['user_id'] = $current_user->ID;
                                    $voter['user_type'] = $vote_type;

                                    if ($this->user_have_votes_to_vote($voter, $SuperCookie)) {
                                        $i = 1;
                                        $cookie = '';
                                        $log = array();
                                        foreach ($this->questions as &$question) {
                                            $answers = array();
                                            if (isset ($request['yop_poll_answer'][$question->ID])) {
                                                if ('yes' == $question->allow_multiple_answers) {
                                                    if (count($request['yop_poll_answer'][$question->ID]) <= intval($question->allow_multiple_answers_number)) {
                                                        if (count($request['yop_poll_answer'][$question->ID]) >= intval($question->allow_multiple_answers_min_number)) {
                                                            $answers = array();
                                                            foreach ($request['yop_poll_answer'][$question->ID] as $answer) {
                                                                $new_answer = array();
                                                                $new_answer['answer_id'] = $answer;
                                                                $new_answer['type'] = 'default';
                                                                if ('other' == $answer) {
                                                                    $a = new YOP_POLL_Answer_Model();
                                                                    if (isset($request['yop_poll_other_answer'][$question->ID]) && '' != strip_tags(trim($request['yop_poll_other_answer'][$question->ID]))) {
                                                                        $a->type = 'other';
                                                                        $a->poll_id = $poll_id;
                                                                        $a->question_id = $question->ID;
                                                                        $a->answer = strip_tags(trim($request['yop_poll_other_answer'][$question->ID]));
                                                                        $a->answer_date = current_time('mysql');
                                                                        $a->answer_modified = current_time('mysql');
                                                                        $a->status = 'active';
                                                                        $a->question_order = $question->countanswers() + 1;
                                                                        $a->answer_author = $current_user->ID;
                                                                        $a->save();

                                                                        if (!$a->id) {
                                                                            $this->error = __yop_poll('Other answer for question ') . $i . __yop_poll(' could not be inserted!');
                                                                            $log_to_add ['message'] = __yop_poll('Other answer for question ') . $i . __yop_poll(' could not be inserted');
                                                                            insert_log_in_db($log_to_add);
                                                                            return false;
                                                                        }
                                                                    } else {
                                                                        $this->error = __yop_poll('Other answer from question ') . $i . __yop_poll(' is empty');
                                                                        $log_to_add ['message'] = __yop_poll('Other answer from question ') . $i . __yop_poll(' is empty');
                                                                        insert_log_in_db($log_to_add);
                                                                        return false;
                                                                    }
                                                                    $question->addAnswer($a);
                                                                    $new_answer['answer_id'] = $a->id;
                                                                    $new_answer['type'] = 'other';
                                                                    unset($a);
                                                                }
                                                                $new_answer['poll_id'] = $poll_id;
                                                                $new_answer['vote_id'] = $vote_id;
                                                                $new_answer['ip'] = yop_poll_get_ip();
                                                                $new_answer['user_id'] = $current_user->ID;

                                                                $new_answer['user_type'] = 'default';
                                                                if ($vote_type == 'anonymous' || $vote_type == 'wordpress') {
                                                                    $new_answer['user_type'] = $vote_type;
                                                                }

                                                                $new_answer['http_referer'] = $_SERVER['HTTP_REFERER'];
                                                                $new_answer['tr_id'] = $tr_id;
                                                                $new_answer['host'] = esc_attr(@gethostbyaddr(yop_poll_get_ip()));
                                                                $new_answer['other_answer_value'] = '';

                                                                $answers[] = $new_answer;
                                                            }
                                                        } else {
                                                            $this->error = __yop_poll("Too few answers selected for question ") . $i . __yop_poll("! Only more than ") . $question->allow_multiple_answers_min_number . __yop_poll(" answers allowed!");
                                                            $log_to_add ['message'] = __yop_poll("Too few answers selected for question ") . $i;
                                                            insert_log_in_db($log_to_add);
                                                            return false;
                                                        }
                                                    } else {
                                                        $this->error = __yop_poll("Too many answers selected for question ") . $i . __yop_poll("! Only ") . $question->allow_multiple_answers_number . __yop_poll(" answers allowed!");
                                                        $log_to_add ['message'] = __yop_poll("Too many answers selected for question ") . $i;
                                                        insert_log_in_db($log_to_add);
                                                        return false;
                                                    }
                                                } else {
                                                    $new_answer = array();
                                                    $new_answer['answer_id'] = $request['yop_poll_answer'][$question->ID];
                                                    $new_answer['type'] = 'default';
                                                    if ('other' == $request['yop_poll_answer'][$question->ID]) {
                                                        $a = new YOP_POLL_Answer_Model();
                                                        if (isset($request['yop_poll_other_answer'][$question->ID]) && '' != strip_tags(trim($request['yop_poll_other_answer'][$question->ID]))) {
                                                            $a->type = 'other';
                                                            $a->poll_id = $poll_id;
                                                            $a->question_id = $question->ID;
                                                            $a->answer = strip_tags(trim($request['yop_poll_other_answer'][$question->ID]));
                                                            $a->answer_date = current_time('mysql');
                                                            $a->question_order = $question->countanswers();
                                                            $a->answer_author = $current_user->ID;
                                                            $a->votes = 0;
                                                            $a->save();

                                                            if (!$a->id) {
                                                                $this->error = __yop_poll("Other answer for question ") . $i . __yop_poll(" could not be inserted!");
                                                                $log_to_add ['message'] = __yop_poll("Other answer for question ") . $i . __yop_poll(" could not be inserted");
                                                                insert_log_in_db($log_to_add);
                                                                return false;
                                                            }
                                                        } else {
                                                            $this->error = __yop_poll("Other answer from question ") . $i . ' ' . __yop_poll('is empty');
                                                            $log_to_add ['message'] = __yop_poll("Other answer from question ") . $i . ' ' . __yop_poll('empty');
                                                            insert_log_in_db($log_to_add);
                                                            return false;
                                                        }

                                                        $question->addAnswer($a);
                                                        $new_answer['answer_id'] = $a->id;
                                                        $new_answer['type'] = 'other';
                                                        unset($a);
                                                    }

                                                    $new_answer['poll_id'] = $poll_id;
                                                    $new_answer['vote_id'] = $vote_id;
                                                    $new_answer['ip'] = yop_poll_get_ip();
                                                    $new_answer['user_id'] = $current_user->ID;

                                                    $new_answer['user_type'] = 'default';


                                                    $new_answer['http_referer'] = $_SERVER['HTTP_REFERER'];
                                                    $new_answer['tr_id'] = $tr_id;
                                                    $new_answer['host'] = esc_attr(@gethostbyaddr(yop_poll_get_ip()));
                                                    $new_answer['other_answer_value'] = '';
                                                    $answers[] = $new_answer;
                                                }

                                                if (count($answers) > 0) {
                                                    $custom_fields = array();
                                                    $poll_custom_fields = $question->custom_fields;

                                                    if (count($poll_custom_fields) > 0) {

                                                        if (isset($request['yop_poll_customfield'][$question->ID])) {

                                                            foreach ($poll_custom_fields as $custom_field) {

                                                                if (isset($request['yop_poll_customfield'][$question->ID][$custom_field->ID])) {

                                                                    if ('' == trim(strip_tags($request['yop_poll_customfield'][$question->ID][$custom_field->ID])) && 'yes' == $custom_field->required) {
                                                                        $this->error = __yop_poll("Custom field ") . $custom_field->custom_field . __yop_poll(" from question ") . $i . ' ' . __yop_poll("is required") . "!";
                                                                        $log_to_add ['message'] = __yop_poll("Custom field ") . $custom_field->custom_field . __yop_poll(" from question ") . $i . ' ' . __yop_poll("required");
                                                                        insert_log_in_db($log_to_add);
                                                                        return false;
                                                                    } else {
                                                                        if (trim(strip_tags($request['yop_poll_customfield'][$question->ID][$custom_field->ID])) != '') {
                                                                            $new_custom_field = array();
                                                                            $new_custom_field['poll_id'] = $poll_id;
                                                                            $new_custom_field['question_id'] = $question->ID;
                                                                            $new_custom_field['vote_id'] = $vote_id;
                                                                            $new_custom_field['custom_field_id'] = $custom_field->ID;
                                                                            $new_custom_field['user_id'] = $current_user->ID;

                                                                            $new_custom_field['user_type'] = 'default';

                                                                            if ($vote_type == 'wordpress' || $vote_type == 'anonymous') {
                                                                                $new_custom_field['user_type'] = $vote_type;
                                                                            }

                                                                            $new_custom_field['custom_field_value'] = strip_tags(trim($request['yop_poll_customfield'][$question->ID][$custom_field->ID]));
                                                                            $custom_fields[] = $new_custom_field;

                                                                        }
                                                                    }
                                                                } else {
                                                                    $this->error = __yop_poll("Custom field ") . '"' . $custom_field->custom_field . '"' . __yop_poll(" from question ") . $i . ' ' . __yop_poll("is missing ") . '!';

                                                                    $log_to_add ['message'] = __yop_poll("Custom field ") . '"' . $custom_field->custom_field . '"' . __yop_poll(" from question ") . $i . ' ' . __yop_poll("missing");
                                                                    insert_log_in_db($log_to_add);
                                                                    return false;
                                                                }
                                                            }
                                                        } else {
                                                            $this->error = __yop_poll("Custom fields from question ") . $i . ' ' . __yop_poll("are missing") . '!';
                                                            $log_to_add ['message'] = __yop_poll("Custom fields from question ") . $i . ' ' . __yop_poll("missing") . '!';
                                                            insert_log_in_db($log_to_add);
                                                            return false;
                                                        }
                                                    }

                                                    if ('yes' == $this->use_captcha) {
                                                        require_once(YOP_POLL_INC . '/securimage.php');
                                                        $img = new Yop_Poll_Securimage();
                                                        $img->namespace = 'yop_poll_' . $poll_id . $unique_id;
                                                        if ($img->check($_REQUEST['yop_poll_captcha_input'][$poll_id])) {
                                                            $mail_notifications_answers[$question->ID] = array();

                                                            $add_to_log = $this->update_votes($question, $answers, $votes, $mail_notifications_answers[$question->ID], $facebook_user_details['id'], $google_user_details['id']);

                                                            $log["q-" . $question->ID]['question'] = $question->question;
                                                            $log["q-" . $question->ID]['a'] = $add_to_log['a'];
                                                            $log["q-" . $question->ID]['answers'] = $add_to_log['answers'];

                                                            $mail_notifications_answers[$question->ID] = trim($mail_notifications_answers[$question->ID], '<br>');

                                                            $mail_notifications_custom_fields[$question->ID] = '';
                                                            foreach ($custom_fields as $custom_field) {

                                                                if ('anonymous' == $vote_type) {
                                                                    $custom_field['user_id'] = 0;
                                                                }
                                                                $custom_field['tr_id'] = $tr_id;
                                                                self::insert_vote_custom_field_in_database($custom_field);

                                                                $cf = $question->getCustomFieldById($custom_field['custom_field_id']);

                                                                $mail_notifications_custom_fields[$question->ID][$cf->custom_field] = $custom_field['custom_field_value'];
                                                            }

                                                            if ('yes' == $this->number_of_votes_per_user) {
                                                                $this->success = str_replace('%USER-VOTES-LEFT%', intval($this->number_of_votes_per_user) - $this->get_voter_number_of_votes($voter), $poll_options['message_after_vote']);
                                                            } else {
                                                                $this->success = str_replace('%USER-VOTES-LEFT%', '', $this->message_after_vote);
                                                            }
                                                        } else {
                                                            $this->error = __yop_poll("Incorrect security code entered!");
                                                            $log_to_add ['message'] = __yop_poll("Incorrect security code entered");
                                                            insert_log_in_db($log_to_add);
                                                            return false;
                                                        }
                                                    } else {
                                                        $mail_notifications_answers[$question->ID] = array();

                                                        $add_to_log = $this->update_votes($question, $answers, $votes, $mail_notifications_answers[$question->ID], $vote_type, $facebook_user_details['id'], $google_user_details['id']);

                                                        $log["q-" . $question->ID]['question'] = $question->question;
                                                        $log["q-" . $question->ID]['id'] = $question->ID;
                                                        $log["q-" . $question->ID]['a'] = $add_to_log['a'];
                                                        $log["q-" . $question->ID]['answers'] = $add_to_log['answers'];


                                                        $mail_notifications_custom_fields[$question->ID] = array();
                                                        $add_to_log = array();
                                                        foreach ($custom_fields as $custom_field) {

                                                            if ('google' == $vote_type) {
                                                                $custom_field['user_id'] = $google_user_details['id'];
                                                            }
                                                            if ('anonymous' == $vote_type) {
                                                                $custom_field['user_id'] = 0;
                                                            }
                                                            $custom_field['tr_id'] = $tr_id;

                                                            $cf_id = self::insert_vote_custom_field_in_database($custom_field);

                                                            $add_to_log[] = $cf_id;

                                                            $cf = $question->getCustomFieldById($custom_field['custom_field_id']);

                                                            $mail_notifications_custom_fields[$question->ID][$cf->custom_field] = $custom_field['custom_field_value'];
                                                        }
                                                        $log["q-" . $question->ID]['cf'] = $add_to_log;

                                                        if ('yes' == $this->number_of_votes_per_user) {
                                                            $this->success = str_replace('%USER-VOTES-LEFT%', intval($this->number_of_votes_per_user) - $this->get_voter_number_of_votes($voter), $this->message_after_vote);
                                                        } else {
                                                            $this->success = str_replace('%USER-VOTES-LEFT%', '', $this->message_after_vote);
                                                        }
                                                    }
                                                } else {
                                                    $this->error = __yop_poll("No vote registered!");
                                                    $log_to_add ['message'] = __yop_poll("No vote registered");
                                                    insert_log_in_db($log_to_add);
                                                    return false;
                                                }
                                            } else {
                                                $this->error = __yop_poll("No answer selected for question ") . $i;
                                                $log_to_add ['message'] = __yop_poll("No answer selected for question ") . $i;
                                                insert_log_in_db($log_to_add);
                                                return false;
                                            }
                                            $i++;
                                        }
                                    } else {
                                        $this->error = __yop_poll("You have run out of votes!");
                                        $log_to_add ['message'] = __yop_poll("Run out of votes");
                                        insert_log_in_db($log_to_add);
                                        return false;
                                    }
                                } else {
                                    $this->error = __yop_poll("You Already voted!");
                                    $log_to_add ['message'] = __yop_poll("Already Voted");
                                    insert_log_in_db($log_to_add);
                                    return false;
                                }
                            }
                        } else {
                            $this->error = __yop_poll("This poll is closed!");
                            $log_to_add ['message'] = __yop_poll("Poll Closed");
                            insert_log_in_db($log_to_add);
                            return false;
                        }
                    } else {
                        $this->error = __yop_poll("You can vote once the poll starts!");
                        $log_to_add ['message'] = __yop_poll("Poll not started");
                        insert_log_in_db($log_to_add);
                        return false;
                    }
                } else {
                    $this->error = __yop_poll("You are not allowed to vote!");
                    $log_to_add ['message'] = __yop_poll("Not allowed to vote");
                    insert_log_in_db($log_to_add);
                    return false;
                }
            } else {
                $this->error = __yop_poll("You are not allowed to vote!");
                return false;
            }

        } else {
            $this->error = __yop_poll("Bad Request!");
            $log_to_add ['message'] = __yop_poll("Bad request");
            insert_log_in_db($log_to_add);
            return false;
        }

        if ($this->send_email_notifications == "yes") {
            $this->sendMail($mail_notifications_answers, $mail_notifications_custom_fields, $vote_id);
        }

        $this->update_poll_total_votes(1);

        $log_to_add ['message'] = __yop_poll('Success');
        $log_to_add ['vote_details'] = json_encode($log);

        insert_log_in_db($log_to_add);
        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $log_to_add['ip']));
        $log_to_add['country'] = $ip_data->geoplugin_countryName;
        insert_result_in_db($log_to_add);

        $this->set_vote_cookie(json_encode($log), $vote_type, $facebook_user_details, $google_user_details);

        $this->vote = true;
        $this->poll_total_votes += 1;

        return do_shortcode($this->return_poll_html(array('tr_id' => $tr_id, 'location' => $location)));
    }

    function is_voted_supercookie($SuperCookie)
    {
        $current_time = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s", mktime()) . " + 365 day"));
        $cookie = explode("=", $SuperCookie);
        $num_votes = explode(";", $cookie[1]);
        if ($num_votes[0] == 0) {
            return true;
        }
        $cookie_expire = convert_date($cookie[2], "Y-m-d H:i:s");


        if (isset($this->blocking_voters_interval_value)) {
            $value = $this->blocking_voters_interval_value;
        }
        $unit = 'days';
        if (isset($this->blocking_voters_interval_unit)) {
            $unit = $this->blocking_voters_interval_unit;
        }

        switch ($unit) {
            case 'seconds' :
                $expire_cookie = $value;
                break;
            case 'minutes' :
                $expire_cookie = (60 * $value);
                break;
            case 'hours' :
                $expire_cookie = (60 * 60 * $value);
                break;
            case 'days' :
                $expire_cookie = (60 * 60 * 24 * $value);
                break;
        }
        $timeFirst = strtotime($current_time);
        $timeSecond = strtotime($cookie_expire);
        $differenceInSeconds = $timeFirst - $timeSecond;
        if ($differenceInSeconds >= 31556926) {
            return true;
        }
        if ($expire_cookie > $differenceInSeconds) {
            return false;
        }
        return true;
    }

    private function update_votes(&$question, $answers, &$votes, &$mail_notification = '', $vote_type, $facebook_user_id, $google_user_id)
    {

        foreach ($answers as $answer) {

            $answer_to_update = & $question->getAnswerById($answer['answer_id']);


            if ('anonymous' == $vote_type) {
                $answer['user_id'] = 0;
            }

            if ($answer['type'] == 'other') {
                if ('yes' == $question->add_other_answers_to_default_answers) {
                    $answer_to_update->type = 'text';
                    $answer['type'] = 'text';
                }
            }
            $votes++;

            $answer_to_update->answer_modified = current_time('mysql');
            $answer_to_update->answer_status = 'active';
            $answer_to_update->votes = intval($answer_to_update->votes) + 1;

            $answer_to_update->save();

            $add_to_log['a'][] = $answer_to_update->ID;
            $add_to_log['answers'][] = $answer_to_update->answer;
            $mail_notification[] = $answer_to_update->answer;

            unset($answer_to_update);
        }
        return $add_to_log;
    }

    private static function insert_vote_custom_field_in_database($custom_field = array())
    {
        global $wpdb;

        $custom_field['custom_field_value'] = strip_tags($custom_field['custom_field_value']);
        $wpdb->query($wpdb->prepare("
					INSERT INTO " . $wpdb->yop_poll_votes_custom_fields . "
					SET
					poll_id                = %d,
					question_id            = %d,
					vote_id                = %s,
					custom_field_id        = %d,
					user_id                = %s,
					user_type            = %s,
					custom_field_value    = %s,
					tr_id                = %s,
					vote_date            = %s
					", $custom_field['poll_id'], $custom_field['question_id'], $custom_field['vote_id'], $custom_field['custom_field_id'], $custom_field['user_id'], $custom_field['user_type'], $custom_field['custom_field_value'], $custom_field['tr_id'], current_time('mysql')));
        return $wpdb->insert_id;
    }

    private function update_poll_total_votes($votes = 0)
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("
					UPDATE {$wpdb->yop_polls}
					SET
					poll_total_votes = poll_total_votes + %d
					WHERE
					ID = %d
					", $votes, $this->ID));

    }

    public function return_poll_html(
        $attr = array(
            'tr_id' => '',
            'location' => 'page',
            'load_css' => false,
            'load_js' => false,
            'show_results' => ''
        )
    )
    {
        $time_format = "H:i:s";
        $options = get_option('yop_poll_options');
        if ($options['date_format'] == "UE")
            $date_format = "d-m-Y"; else {
            $date_format = "m-d-Y";
        }
        $date_format = $date_format . ' ' . $time_format;
        $tr_id = isset($attr['tr_id']) ? $attr['tr_id'] : '';
        $show_results = isset($attr['show_results']) ? $attr['show_results'] : '';
        $location = isset($attr['location']) ? $attr['location'] : 'page';
        $load_css = isset($attr['load_css']) ? $attr['load_css'] : false;
        $unique_id = $this->unique_id;
        $poll_id = $this->ID;
        if (!$poll_id) {
            return '';
        }

        //Get template id based on location(widget/page)
        if ('widget' == $location) {
            $template_id = $this->widget_template;
        } else {
            $template_id = $this->template;
        }


        /**
         * Init Options
         */
        $general_options = get_option("yop_poll_options");
        foreach ($general_options as $key => $value) {
            if (!isset($this->$key)) {
                $this->$key = $value;
            }
        }

        if ('' == $template_id) {
            //get default template
            $template_details = self::get_poll_template_from_database();
        } else {
            $template_details = self::get_poll_template_from_database($template_id);
        }

        $template_css = $template_details['css'];
        $template_details['before_vote_template'] = str_replace('id = "yop-poll-li-answer-%POLL-ID%-%QUESTION-ID%"', '', $template_details['before_vote_template']);
        $template_details['before_vote_template'] = str_replace('id = "yop-poll-li-custom-%POLL-ID%-%QUESTION-ID%"', '', $template_details['before_vote_template']);
        $template_details['before_vote_template'] = str_replace('id = "yop-poll-customs-%POLL-ID%-%QUESTION-ID%"', '', $template_details['before_vote_template']);
        $template_details['after_end_date_template'] = str_replace('id = "pds-feedback-label-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_end_date_template']);
        $template_details['after_vote_template'] = str_replace('id = "pds-feedback-label-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_vote_template']);
        $template_details['after_end_date_template'] = str_replace('id = "yop-poll-li-result-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_end_date_template']);
        $template_details['after_vote_template'] = str_replace('id = "yop-poll-li-result-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_vote_template']);
        $template_details['after_end_date_template'] = str_replace('id = "pds-answer-text-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_end_date_template']);
        $template_details['after_vote_template'] = str_replace('id = "pds-answer-text-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_vote_template']);
        $template_details['after_end_date_template'] = str_replace('id = "pds-feedback-result-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_end_date_template']);
        $template_details['after_vote_template'] = str_replace('id = "pds-feedback-result-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_vote_template']);
        $template_details['after_end_date_template'] = str_replace('id = "pds-feedback-per-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_end_date_template']);
        $template_details['after_vote_template'] = str_replace('id = "pds-feedback-per-%POLL-ID%-%QUESTION-ID%"', '', $template_details['after_vote_template']);
        $poll_details = $this;
        $this->vote_button_label = empty($this->vote_button_label) ? $general_options['vote_button_label'] : $this->vote_button_label;
        $this->vote_permisions_wordpress_label = empty($this->vote_permisions_wordpress_label) ? $general_options['vote_permisions_wordpress_label'] : $this->vote_permisions_wordpress_label;
        $this->vote_permisions_anonymous_label = empty($this->vote_permisions_anonymous_label) ? $general_options['vote_permisions_anonymous_label'] : $this->vote_permisions_anonymous_label;

        //Translate labels
        if (function_exists('icl_translate')) {
            // $poll_details = icl_translate( 'yop_poll', $poll_details->ID . '_poll_title', $poll_details->name );
            $this->singular_answer_result_votes_number_label = icl_translate('yop_poll', $poll_details->ID . '_singular_answer_result_votes_number_label', $general_options['singular_answer_result_votes_number_label']);
            $this->plural_answer_result_votes_number_label = icl_translate('yop_poll', $poll_details->ID . '_plural_answer_result_votes_number_label', $general_options['plural_answer_result_votes_number_label']);
            $this->vote_button_label = icl_translate('yop_poll', $poll_details->ID . '_vote_button_label', empty($this->vote_button_label) ? $general_options['vote_button_label'] : $this->vote_button_label);
            $this->view_results_link_label = icl_translate('yop_poll', $poll_details->ID . '_view_results_link_label', $this->view_results_link_label);
            $this->view_back_to_vote_link_label = icl_translate('yop_poll', $poll_details->ID . '_view_back_to_vote_link_label', $this->view_back_to_vote_link_label);
            $this->view_total_votes_label = icl_translate('yop_poll', $poll_details->ID . '_view_total_votes_label', $this->view_total_votes_label);
            $this->view_total_answers_label = icl_translate('yop_poll', $poll_details->ID . '_view_total_answers_label', $this->view_total_answers_label);
            $this->view_total_voters_label = icl_translate('yop_poll', $poll_details->ID . '_view_total_voters_label', $this->view_total_voters_label);
            $this->archive_link_label = icl_translate('yop_poll', $poll_details->ID . '_archive_link_label', $this->archive_link_label);
            $this->answer_result_label = icl_translate('yop_poll', $poll_details->ID . '_answer_result_label', $this->answer_result_label);
            $this->vote_permisions_wordpress_label = icl_translate('yop_poll', $poll_details->ID . '_vote_permisions_wordpress_label', $this->vote_permisions_wordpress_label);
            $this->vote_share_google_label = icl_translate('yop_poll', $poll_details->ID . '_vote_share_google_label', $this->vote_share_google_label);
            $this->vote_permisions_anonymous_label = icl_translate('yop_poll', $poll_details->ID . '_vote_permisions_anonymous_label', $this->vote_permisions_anonymous_label);
        }

        $is_voted = $this->is_voted();
        $current_date = self::get_mysql_curent_date();
        if ($current_date >= convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1)) {

            if ($current_date <= convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1)) {
                //poll is active
                if (!$is_voted) {
                    //user hasn't voted yet
                    $template = $template_details['before_vote_template'];
                    if (isset($show_results) && $show_results == 1) {

                        $template = $template_details['after_vote_template'];
                        $this->view_results = array('before');
                    }
                    if (in_array('before', $this->view_results)) {
                        if ($this->is_view_poll_results()) {
                            $template = str_ireplace('%POLL-ANSWER-RESULT-LABEL%', $this->answer_result_label, $template);
                        }
                    }
                    $template = str_ireplace('%POLL-VOTE-BUTTON%', '<button class="yop_poll_vote_button" id="yop_poll_vote-button-' . $this->ID . $unique_id . '" onclick="yop_poll_register_vote(\'' . $this->ID . '\', \'' . $location . '\', \'' . $unique_id . '\'); return false;">' . $this->vote_button_label . '</button>', $template);
                } else {
                    //user has voted

                    if (
                        in_array('after', $this->view_results) ||
                        in_array('before', $this->view_results) ||
                        (in_array('custom-date', $this->view_results) && self::get_mysql_curent_date() >= convert_date($this->view_results_start_date, 'Y-m-d H:i:s'))
                    ) {

                        $template = $template_details['after_vote_template'];

                        if ($this->is_view_poll_results()) {
                            $template = str_ireplace('%POLL-ANSWER-RESULT-LABEL%', $this->answer_result_label, $template);
                        }
                    }

                    if ('yes' == $this->view_back_to_vote_link) {
                        $vote = $this->vote;
                        $this->vote = false;
                        if (!$this->is_voted()) {
                            $template = str_ireplace('%POLL-BACK-TO-VOTE-LINK%', '<a href="javascript:void(0)" class="yop_poll_back_to_vote_link" id="yop_poll_back_to_vote_link' . $this->ID . $unique_id . '" onClick="yop_poll_back_to_vote(\'' . $this->ID . '\', \'' . $location . '\', \'' . $unique_id . '\')">' . $this->view_back_to_vote_link_label . '</a>', $template);
                        }
                        $this->vote = $vote;
                    }

                }
            } else {
                //poll has ended
                $template = $template_details['after_end_date_template'];
                if (in_array('after-poll-end-date', $this->view_results) || in_array('before', $this->view_results) || in_array('after', $this->view_results)) {
                    if ($this->is_view_poll_results()) {
                        $template = str_ireplace('%POLL-ANSWER-RESULT-LABEL%', $this->answer_result_label, $template);
                        $template = str_ireplace('%POLL-END-DATE%', convert_date($this->poll_end_date, $date_format, 1), $template);
                        $template = str_ireplace('%POLL-START-DATE%', convert_date($this->poll_start_date, $date_format, 1), $template);
                    }
                }

            }
        } else {
            //poll hasn't started
            $template = $template_details['before_start_date_template'];
            $template = str_ireplace('%POLL-END-DATE%', convert_date($this->poll_end_date, $date_format, 1), $template);
            $template = str_ireplace('%POLL-START-DATE%', convert_date($this->poll_start_date, $date_format, 1), $template);
            if (in_array('before', $this->view_results)) {
                if ($this->is_view_poll_results()) {
                    $template = str_ireplace('%POLL-ANSWER-RESULT-LABEL%', $this->answer_result_label, $template);

                }

            }
        }

        if (in_array('custom-date', $this->view_results)) {
            $template = str_ireplace('%POLL-END-DATE%', $this->poll_end_date, $template);
            $template = str_ireplace('%POLL-START-DATE%', $this->poll_start_date, $template);
            if (self::get_mysql_curent_date() >= convert_date($this->view_results_start_date, 'Y-m-d H:i:s', 1)) {

                if ($this->is_view_poll_results()) {
                    $template = str_ireplace('%POLL-ANSWER-RESULT-LABEL%', $this->answer_result_label, $template);
                }
            }
        }
        $template = str_ireplace('%POLL-END-DATE%', convert_date($this->poll_end_date, $date_format, 1), $template);
        $template = str_ireplace('%POLL-START-DATE%', convert_date($this->poll_start_date, $date_format, 1), $template);
        $template = stripslashes_deep($template);

        $template = str_ireplace('%POLL-ID%', $this->ID . $unique_id, $template);
        $template = str_ireplace("%POLL-NAME%", yop_poll_kses(stripslashes($this->poll_title)), $template);
        $template = str_ireplace('%POLL-START-DATE%', esc_html(stripslashes(convert_date($this->start_date, $date_format))), $template);
        $template = str_ireplace('%POLL-PAGE-URL%', esc_html(yop_poll_kses(stripslashes($this->poll_page_url))), $template);

        if ('01-01-2038 23:59:59' == convert_date($this->poll_end_date, 'Y-m-d H:i:s')) {
            $template = str_ireplace('%POLL-END-DATE%', __yop_poll('Never Expire'), $template);
        } else {
            $template = str_ireplace('%POLL-END-DATE%', esc_html(stripslashes(convert_date($this->end_date, $date_format))), $template);
        }

        if ('yes' == $this->view_results_link) {
            $template = str_ireplace('%POLL-VIEW-RESULT-LINK%', '<a href="javascript:void(0)" class="yop_poll_result_link" id="yop_poll_result_link' . $this->ID . $unique_id . '" onClick="yop_poll_view_results(\'' . $this->ID . '\', \'' . $location . '\', \'' . $unique_id . '\')">' . $this->view_results_link_label . '</a>', $template);
        }
        if ('yes' == $this->view_poll_archive_link) {
            $template = str_ireplace('%POLL-VIEW-ARCHIVE-LINK%', '<a href="' . get_permalink($this->yop_poll_archive_page_id) . '" class="yop_poll_archive_link" id="yop_poll_archive_link_' . $this->ID . $unique_id . '" >' . $this->archive_link_label . '</a>', $template);
        }
        $poll_total_answers = 0;
        foreach ($this->questions as $q) {
            foreach ($q->answers as $a)
                $poll_total_answers += $a->votes;
        }


        if ('yes' == $this->view_total_answers) {
            $template = str_ireplace('%POLL-TOTAL-ANSWERS-LABEL%', $this->view_total_answers_label, $template);
            $template = str_ireplace('%POLL-TOTAL-ANSWERS%', $poll_total_answers, $template);
        }
        if ('yes' == $this->view_total_votes) {
            $template = str_ireplace('%POLL-TOTAL-VOTES-LABEL%', $this->view_total_votes_label, $template);
            $template = str_ireplace('%POLL-TOTAL-VOTES%', $this->poll_total_votes, $template);
        }

        $msgDivS = false;
        $msgDivE = false;

        if (strpos($template, "%POLL-SUCCESS-MSG%") != false) {
            $msgDivS = true;
            $template = str_ireplace('%POLL-SUCCESS-MSG%', '<div id="yop-poll-container-success-' . $this->ID . $unique_id . '" class="yop-poll-container-success"></div>', $template);
        }
        if (strpos($template, "%POLL-ERROR-MSG%") != false) {
            $msgDivE = true;
            $template = str_ireplace('%POLL-ERROR-MSG%', '<div id="yop-poll-container-error-' . $this->ID . $unique_id . '" class="yop-poll-container-error"></div>', $template);
        }

        $that = $this;

        /** Start Question replace*/
        $pattern = '\[(\[?)(QUESTION_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
        $template = preg_replace_callback("/$pattern/s", function ($m) use ($that) {
            return $that->question_replace_callback($m);
        }, $template);
        /** End Question replace*/


        /** Start CAPTCHA replace*/
        $pattern = '\[(\[?)(CAPTCHA_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
        $template = preg_replace_callback("/$pattern/s", function ($m) use ($that) {
            return $that->captcha_replace_callback($m);
        }, $template);
        /** End CAPTCHA replace*/


        $temp = self::strip_all_tags($template);

        $template = "";
        if ($load_css) {
            $template .= '<style scoped>' . self::return_poll_css($template_css, array("location" => $location)) . ' .yop-poll-forms-display{}' . '</style>';

        }


        $template .= '<div id="yop-poll-container-' . $this->ID . $unique_id . '" class="yop-poll-container">';
        if (!$msgDivS) {
            $template .= '<div id="yop-poll-container-success-' . $this->ID . $unique_id . '" class="yop-poll-container-success"></div>';
        }
        if (!$msgDivE) {
            $template .= '<div id="yop-poll-container-error-' . $this->ID . $unique_id . '" class="yop-poll-container-error"></div>';
        }

        $template .= '<form id="yop-poll-form-' . $this->ID . $unique_id . '" class="yop-poll-forms yop-poll-forms-display">' . $temp . '<input type="hidden" id="yop-poll-tr-id-' . $this->ID . $unique_id . '" name="yop_poll_tr_id" value="' . $tr_id . '"/>' . wp_nonce_field('yop_poll-' . $this->ID . $unique_id . '-user-actions', 'yop-poll-nonce-' . $this->ID . $unique_id, false, false) . '</form></div>';


        return $template;
    }

    public function captcha_replace_callback($m)
    {
        $unique_id = $this->unique_id;
        $return_string = '';
        $temp_string = '';

        if ('yes' == $this->use_captcha) {
            $sid = md5(uniqid());
            $temp_string = str_ireplace('%CAPTCHA-IMAGE%', '<img class="yop_poll_captcha_image" id="yop_poll_captcha_image_' . $this->ID . $unique_id . '" src="' . admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')) . '?action=yop_poll_show_captcha&poll_id=' . $this->ID . '&sid=' . $sid . '&unique_id=' . $this->unique_id . '" />', $m[5]);
            $temp_string = str_ireplace('%CAPTCHA-INPUT%', '<input type="text" value="" name="yop_poll_captcha_input[' . $this->ID . ']" id="yop-poll-captcha-input-' . $this->ID . $unique_id . '" />', $temp_string);
            $temp_string = str_ireplace('%RELOAD-CAPTCHA-IMAGE%', '<a href="javascript:void(0)"><img src="' . YOP_POLL_URL . 'images/captcha_reload.png' . '" alt="' . __('Reload', 'yop_poll') . '" onClick="yop_poll_reloadCaptcha(' . "'" . $this->ID . "', '" . $this->unique_id . "'" . ')" /></a>', $temp_string);
            $temp_string = str_ireplace('%CAPTCHA-LABEL%', "<br>" . __yop_poll('Enter the code'), $temp_string);
            $temp_string = str_ireplace('%CAPTCHA-PLAY%', '<object type="application/x-shockwave-flash" data="' . YOP_POLL_URL . 'captcha/securimage_play.swf?bgcol=#ffffff&amp;icon_file=' . YOP_POLL_URL . 'images/captcha-audio.gif&amp;audio_file=' . urlencode(admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')) . '?action=yop_poll_play_captcha&poll_id=' . $this->ID . '&unique_id=' . $this->unique_id) . '" height="30" width="30">
					<param name="movie" value="' . YOP_POLL_URL . 'captcha/securimage_play.swf?bgcol=#ffffff&amp;icon_file=' . YOP_POLL_URL . 'images/captcha-audio.gif&amp;audio_file=' . urlencode(admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')) . '?action=yop_poll_play_captcha&poll_id=' . $this->ID . '&unique_id=' . $this->unique_id) . '" />
					</object>', $temp_string);
        }
        $return_string .= $temp_string;

        return $return_string;
    }
}

?>
