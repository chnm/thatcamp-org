<?php

abstract class YOP_POLL_Abstract_Model
{


    protected $data = null;


    protected $options = null;


    protected $questions = null;


    protected $ID = null;


    protected $unique_id = null;


    protected $vote = false;


    protected $type = 'quiz';


    protected $error = "";


    protected $success = "";


    private $default_fields = array(

        'ID',

        'poll_title',

        'poll_name',

        'poll_author',

        'poll_date',

        'poll_status',

        'poll_password',

        'poll_modified',

        'poll_type',

        'archive_order',

        'poll_start_date',

        'poll_end_date',

        'poll_total_votes'

    );


    function __construct($id = 0, $is_view_results = "no", $question_sort = "poll_order", $question_sort_rule = "ASC", $answer_sort = "question_order", $answer_sort_rule = "ASC")
    {

        if ($id instanceof YOP_POLL_Abstract_Model) {

            $this->init($id->data, $is_view_results, $question_sort, $question_sort_rule, $answer_sort, $answer_sort_rule);

            return;

        } elseif (is_object($id)) {

            $this->init($id, $is_view_results, $question_sort, $question_sort_rule, $answer_sort, $answer_sort_rule);

            return;

        }


        if (!empty($id) && !is_numeric($id)) {

            $id = 0;

        }

        if ($id == "-3") {


            $filters = array();
            $filters[] = array(
                'field' => 'poll_type',
                'value' => 'poll',
                'operator' => '='
            );


            $filters[] = array(
                'field' => 'poll_start_date',
                'value' => current_time('mysql'),
                'operator' => '<='
            );
            $args = array(
                'return_fields' => 'ID ',
                'filters' => $filters,
                'search' => array(),
                'orderby' => "poll_start_date",
                'order' => 'DESC'
            );

            $total_polls = Yop_Poll_Model::get_polls_filter_search($args);
            $ok = 0;
            $current_date = yop_poll_get_mysql_curent_date();

            if (count($total_polls) > 1) {
                while ($ok == 0) {
                    $id = rand(1, count($total_polls));
                    $poll = new YOP_POLL_Poll_Model($id);
                    if ($current_date <= convert_date($poll->poll_end_date, 'Y-m-d H:i:s')) {
                        $ok = 1;
                    }
                }
            } else
                $id = rand(1, count($total_polls));
        }
        if ($id == "-2") {


            $filters = array();
            $filters[] = array(
                'field' => 'poll_type',
                'value' => 'poll',
                'operator' => '='
            );

            $filters[] = array(
                'field' => 'poll_start_date',
                'value' => current_time('mysql'),
                'operator' => '<='
            );

            $args = array(
                'return_fields' => 'ID ',
                'filters' => $filters,
                'search' => array(),
                'orderby' => "poll_date",
                'order' => 'ASC'
            );

            $total_polls = Yop_Poll_Model::get_polls_filter_search($args);
            $id = $total_polls[count($total_polls) - 1]['ID'];
        }
        if ($id == "-1") {


            $poll = self::get_current_active_poll();
            $id = $poll['ID'];
        }


        $data = self::get_data_by('id', $id);

        if ($data) {

            $this->init($data, $is_view_results, $question_sort, $question_sort_rule, $answer_sort, $answer_sort_rule);

        } else {

            $this->default_init();

        }


        $this->poll_start_date = convert_date($this->poll_start_date, "Y-m-d H:i:s");

        $this->poll_end_date = convert_date($this->poll_end_date, "Y-m-d  H:i:s");

    }

    public static function get_current_active_poll($offset = 0)
    {
        global $wpdb;
        $current_date = self::get_mysql_curent_date();
        return $wpdb->get_row($wpdb->prepare("
					SELECT * FROM " . $wpdb->yop_polls . "
					WHERE
					%s    >= poll_start_date AND
					%s  <= poll_end_date
					ORDER BY
					poll_date ASC
					", $current_date, $current_date), ARRAY_A, $offset);
    }


    function init($data, $is_view_results, $question_sort, $question_sort_rule, $answer_sort, $answer_sort_rule)
    {
        $this->data = $data;
        //$this->options =

        $this->ID = (int)$data->ID;

        $this->init_options();
        if ( ( 'yes' == $is_view_results ) || ( 'before' == $this->options['view_results'][0] )) {
            switch ($this->sorting_results) {

                case "database":

                {

                    $answer_sort = 'ID';

                    $answer_sort_rule = $this->sorting_results_direction;

                    break;

                }

                case "alphabetical":

                {

                    $answer_sort = 'answer';

                    $answer_sort_rule = $this->sorting_results_direction;

                    break;

                }

                case "votes":

                {

                    $answer_sort = 'votes';

                    $answer_sort_rule = $this->sorting_results_direction;

                    break;

                }

                default:

                    {

                    $answer_sort = "question_order";

                    $answer_sort_rule = "ASC";

                    break;

                    }

            }

        }

        $this->questions = array();

        $questions_ids = $this->load_questions_ids($question_sort, $question_sort_rule);

        if ($questions_ids && count($questions_ids) > 0) {

            foreach ($questions_ids as $question_id) {

                $new_question = new YOP_POLL_Question_Model($question_id, $answer_sort, $answer_sort_rule);

                if ($new_question) {

                    $this->questions[] = $new_question;

                }

            }

        }

    }


    function default_init()
    {

        $this->data = new stdClass();

        $this->ID = null;

        $this->init_options();

        $this->questions = array();

        $this->data->poll_start_date = $this->options['poll_start_date'];

        $this->data->poll_end_date = $this->options['poll_end_date'];

    }


    function init_options()
    {

        $this->options = array();

        $poll_default_options = yop_poll_poll_default_options();


        $poll_archive_order = get_option('yop_poll_archive_order', array());

        $key = array_search($this->ID, $poll_archive_order);

        if ($key !== false) {

            $this->options['poll_archive_order'] = $key + 1;

        }


        if ($poll_default_options && count($poll_default_options) > 0) {

            foreach ($poll_default_options as $option_name => $option_value) {

                $this->options[$option_name] = $this->get_option($option_name);

            }

        }

    }


    function _unset()
    {

        $this->data = null;

        $this->ID = null;

        $this->questions = null;

        $this->options = null;

        $this->unique_id = null;

        $this->error = null;

        $this->success = null;


    }


    static function get_data_by($field, $value)
    {

        if ('id' == $field) {

            if (!is_numeric($value)) {

                return false;

            }

            $value = intval($value);

            if ($value < 1) {

                return false;

            }

        } else {

            $value = trim($value);

        }


        if (!$value) {

            return false;

        }


        switch ($field) {

            case 'id':

                $model_id = $value;

                $db_field = 'ID';

                break;

            case 'name':

                $model_id = $value;

                $db_field = 'poll_name';

                break;

            default:

                return false;

        }


        if (false !== $model_id) {

            if ($model = wp_cache_get($model_id, 'yop_poll_model')) {

                return $model;

            }

        }


        if (!$model = $GLOBALS['wpdb']->get_row($GLOBALS['wpdb']->prepare("SELECT * FROM {$GLOBALS['wpdb']->yop_polls} WHERE $db_field = %s", $value))) {

            return false;

        }


        wp_cache_add($model_id, $model, 'yop_poll_model');


        return $model;

    }


    static function get_other_model_data_by($field, $value, $current_model_id)
    {

        $value = trim($value);


        if (!$value) {

            return false;

        }

        if (intval($current_model_id) <= 0) {

            return false;

        }


        switch ($field) {

            case 'name':

                $model_id = $value;

                $db_field = 'poll_name';

                break;

            default:

                return false;

        }


        if (!$model = $GLOBALS['wpdb']->get_row($GLOBALS['wpdb']->prepare("SELECT * FROM {$GLOBALS['wpdb']->yop_polls} WHERE $db_field = %s AND ID != %d", $value, $current_model_id))) {

            return false;

        }


        return $model;

    }


    function __isset($key)
    {

        if ('id' == strtolower($key)) {

            $key = 'ID';

        }

        if ('type' == $key) {

            $key = 'type';

        }


        if (isset($this->$key)) {

            return true;

        }

        if (isset($this->data->$key)) {

            return true;

        }


        return $this->isset_option($key);

    }


    function isset_option($key)
    {


        if (isset($this->options[$key])) {

            return true;

        }


        $poll_options = get_yop_poll_meta($this->ID, 'options', true);

        if (isset($poll_options[$key])) {

            return true;

        }


        return false;

    }


    function __get($key)
    {

        $value = null;

        if ('id' == strtolower($key)) {

            return $this->ID;

        }

        if ('type' == $key) {

            return $this->type;

        }

        if ('questions' == $key) {

            return $this->questions;

        }

        if ('unique_id' == $key) {

            return $this->unique_id;

        }

        if ('error' == $key) {

            return $this->error;

        }

        if ('success' == $key) {

            return $this->success;

        }

        if ('vote' == $key) {

            return $this->vote;

        }


        if (isset($this->data->$key)) {

            $value = $this->data->$key;

        } elseif (in_array($key, $this->default_fields)) {

            $value = $this->data->$key;

        } else {

            $value = $this->get_option($key);

        }


        return $value;

    }


    function get_option($key)
    {


        if (isset($this->options[$key])) {

            return $this->options[$key];

        }


        $poll_options = get_yop_poll_meta($this->ID, 'options', true);

        if (isset($poll_options[$key])) {

            return $poll_options[$key];

        }


        $default_options = get_option('yop_poll_options');

        if (isset($default_options[$key])) {

            return $default_options[$key];

        }

        return false;

    }


    function __set($key, $value)
    {

        if ('id' == strtolower($key)) {

            $this->ID = $value;

            $this->data->ID = $value;

            return;

        }

        if ('type' == $key) {

            $this->type = $value;

            return;

        }

        if ('questions' == $key) {

            $this->questions = $value;

            return;

        }

        if ('unique_id' == $key) {

            $this->unique_id = $value;

            return;

        }

        if ('error' == $key) {

            $this->error = $value;

            return;

        }

        if ('success' == $key) {

            $this->success = $value;

            return;

        }

        if ('vote' == $key) {

            $this->vote = $value;

            return;

        }


        if (in_array($key, $this->default_fields)) //this is not an option

        {

            $this->data->$key = $value;

        } else {

            $this->_set_option($key, $value);

        }

    }


    function _set_option($key, $value)
    {

        $this->options[$key] = $value;

    }


    function exists()
    {

        return !empty($this->ID);

    }


    function get($key)
    {

        return $this->__get($key);

    }


    function has_prop($key)
    {

        return $this->__isset($key);

    }


    function to_array()
    {

        return get_object_vars($this->data);

    }


    function load_questions_ids($question_sort, $question_sort_rule)
    {

        if (!$questions = $GLOBALS['wpdb']->get_col($GLOBALS['wpdb']->prepare("SELECT ID FROM {$GLOBALS['wpdb']->yop_poll_questions} WHERE poll_id = %s ORDER BY {$question_sort} {$question_sort_rule}", $this->id))) {

            return false;

        }

        return $questions;

    }


    function save($is_clone = false)
    {
        if (!$this->exists()) {

            return $this->insert($is_clone);

        } else {

            return $this->update();

        }

    }


    function save_options()
    {

        $poll_options = get_yop_poll_meta($this->ID, 'options', true);

        if ($this->options && count($this->options) > 0) {

            foreach ($this->options as $option_name => $option_value) {

                if ($option_name == "view_results_start_date" || $option_name == "schedule_reset_poll_date") {
                    $options1 = get_option('yop_poll_options');
                    if ($options1['date_format'] == "US") {
                        $original1 = explode(' ', $option_value);
                        $original = explode('-', $original1[0]);
                        $option_value = $original[1] . '-' . $original[0] . '-' . $original[2] . ' ' . $original1[1];


                    }
                    if (convert_date($option_value, 'Y-m-d H:i:s', 1) == '1970-01-01 00:00:00')
                        $option_value = convert_date($option_value, 'Y-m-d H:i:s');
                    else
                        $option_value = convert_date($option_value, 'Y-m-d H:i:s', 1);
                    $original = explode(' ', $option_value);
                    $original = explode('-', $original[0]);

                    if ($original[2] > 12) {
                        $poll_options[$option_name] = convert_date($option_value, 'Y-m-d H:i:s', 1);
                    } else
                        $poll_options[$option_name] = convert_date($option_value, 'Y-d-m H:i:s', 1);
                } else


                    $poll_options[$option_name] = $option_value;

            }

        }

        update_yop_poll_meta($this->id, 'options', $poll_options);

    }


    function insert($is_clone = false)
    {
        $options = get_option('yop_poll_options');
        if ($options['date_format'] == "US") {
            $original1 = explode(' ', $this->poll_start_date);
            $original = explode('-', $original1[0]);
            $this->poll_start_date = $original[0] . '-' . $original[1] . '-' . $original[2] . ' ' . $original1[1];
            $original1 = explode(' ', $this->poll_end_date);
            $original = explode('-', $original1[0]);
            $this->poll_end_date = $original[0] . '-' . $original[1] . '-' . $original[2] . ' ' . $original1[1];
        }
            if ($this->poll_end_date == "01-01-2038 23:59:59")
                $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s');
            else {
                if (convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1) == '1970-01-01 00:00:00')
                    $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s');
                else
                    if ($options['date_format'] == "US")
                        $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1);
                    else
                        if(!$is_clone)
                            $this->poll_end_date = convert_date($this->poll_end_date, 'Y-d-m H:i:s', 1);
            }
            if (convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1) == '1970-01-01 00:00:00')
                $this->poll_start_date = convert_date($this->poll_start_date, 'Y-m-d H:i:s');
            else
                if ($options['date_format'] == "US")
                    $this->poll_start_date = convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1);
                else
                      if(!$is_clone)
                      $this->poll_start_date = convert_date($this->poll_start_date, 'Y-d-m H:i:s', 1);
            $original = explode(' ', $this->poll_start_date);
            $original = explode('-', $original[0]);
            if ($original[2] > 12) {
                $this->poll_start_date = convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1);
            } else
                $this->poll_start_date = convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1);
            $original = explode(' ', $this->poll_end_date);
            $original = explode('-', $original[0]);
            if ($original[2] > 12) {
                $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1);
            } else
                $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1);
        if (isset($this->ID)) {


            $GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("

					INSERT INTO " . $GLOBALS['wpdb']->yop_polls . "

					SET
                    ID             =%d,
					poll_title			= %s,

					poll_name			= %s,

					poll_author			= %d,

					poll_date			= %s,

					poll_status			= %s,

					poll_modified		= %s,

					poll_type			= %s,

					poll_start_date		= %s,

					poll_end_date		= %s,

					poll_total_votes	= %d

						", $this->ID, $this->poll_title, $this->poll_name, $this->poll_author, $this->poll_date, $this->poll_status, $this->poll_modified, $this->type, $this->poll_start_date, $this->poll_end_date, $this->poll_total_votes));

            $this->id = $GLOBALS['wpdb']->insert_id;

        } else {
            $GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("

					INSERT INTO " . $GLOBALS['wpdb']->yop_polls . "

					SET
					poll_title			= %s,

					poll_name			= %s,

					poll_author			= %d,

					poll_date			= %s,

					poll_status			= %s,

					poll_modified		= %s,

					poll_type			= %s,

					poll_start_date		= %s,

					poll_end_date		= %s,

					poll_total_votes	= %d

						", $this->poll_title, $this->poll_name, $this->poll_author, $this->poll_date, $this->poll_status, $this->poll_modified, $this->type, $this->poll_start_date, $this->poll_end_date, $this->poll_total_votes));

            $this->id = $GLOBALS['wpdb']->insert_id;
        }


        if (intval($this->id) > 0) {

            wp_cache_delete($this->id, 'yop_poll_model');


            if (isset($this->auto_generate_poll_page) && "yes" == $this->auto_generate_poll_page && "yes" != $this->has_auto_generate_poll_page) {

                $_p = array();

                $_p['post_title'] = $this->poll_title;

                $_p['post_content'] = "[yop_poll id='" . $this->ID . "']";

                $_p['post_status'] = 'publish';

                $_p['post_type'] = 'page';

                $_p['comment_status'] = 'open';

                $_p['ping_status'] = 'open';

                $_p['post_category'] = array(1); // the default 'Uncategorised'


                $poll_page_id = wp_insert_post($_p);


                $this->poll_page_url = get_permalink($poll_page_id);

                $this->has_auto_generate_poll_page = 'yes';

                $this->auto_generate_poll_page = 'no';

            }

            $this->save_options();

            $this->save_questions();

            return $this->id;

        }

        return false;

    }


    function update()
    {
        $options = get_option('yop_poll_options');
        if ($options['date_format'] == "US") {
            $original1 = explode(' ', $this->poll_start_date);
            $original = explode('-', $original1[0]);
            $this->poll_start_date = $original[1] . '-' . $original[0] . '-' . $original[2] . ' ' . $original1[1];
            $original1 = explode(' ', $this->poll_end_date);
            $original = explode('-', $original1[0]);
            $this->poll_end_date = $original[1] . '-' . $original[0] . '-' . $original[2] . ' ' . $original1[1];

        }
        if ($this->poll_end_date == "01-01-2038 23:59:59")
            $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s');
        else {
            if (convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1) == '1970-01-01 00:00:00')
                $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s');
            else
                $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1);
        }
        if (convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1) == '1970-01-01 00:00:00')
            $this->poll_start_date = convert_date($this->poll_start_date, 'Y-m-d H:i:s');
        else
            $this->poll_start_date = convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1);

        $original = explode(' ', $this->poll_start_date);
        $original = explode('-', $original[0]);
        if ($original[2] > 12) {
            $this->poll_start_date = convert_date($this->poll_start_date, 'Y-m-d H:i:s', 1);
        } else
            $this->poll_start_date = convert_date($this->poll_start_date, 'Y-d-m H:i:s', 1);
        $original = explode(' ', $this->poll_end_date);
        $original = explode('-', $original[0]);

        if ($original[2] > 12) {
            $this->poll_end_date = convert_date($this->poll_end_date, 'Y-m-d H:i:s', 1);
        } else
            $this->poll_end_date = convert_date($this->poll_end_date, 'Y-d-m H:i:s', 1);
        $GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("

					UPDATE " . $GLOBALS['wpdb']->yop_polls . "

					SET

					poll_title			= %s,

					poll_name			= %s,

					poll_status			= %s,

					poll_modified		= %s,

					poll_start_date		= %s,

					poll_end_date		= %s

					WHERE

					ID					= %d

					", $this->poll_title, $this->poll_name, $this->poll_status, $this->poll_modified, $this->poll_start_date, $this->poll_end_date, $this->id));
        if (isset($this->auto_generate_poll_page) && "yes" == $this->auto_generate_poll_page && "yes" != $this->has_auto_generate_poll_page) {

            $_p = array();

            $_p['post_title'] = $this->poll_title;

            $_p['post_content'] = "[yop_poll id='" . $this->ID . "']";

            $_p['post_status'] = 'publish';

            $_p['post_type'] = 'page';

            $_p['comment_status'] = 'open';

            $_p['ping_status'] = 'open';

            $_p['post_category'] = array(1); // the default 'Uncategorised'


            $poll_page_id = wp_insert_post($_p);


            $this->poll_page_url = get_permalink($poll_page_id);

            $this->has_auto_generate_poll_page = 'yes';

            $this->auto_generate_poll_page = 'no';

        }

        $this->save_options();

        $this->save_questions();

        wp_cache_delete($this->id, 'yop_poll_model');

        return true;

    }


    function update_no_votes()
    {

        $GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("

					UPDATE " . $GLOBALS['wpdb']->yop_polls . "

					SET

                    poll_total_votes =%d

					WHERE

					ID= %d

					", $this->poll_total_votes, $this->id));


        wp_cache_delete($this->id, 'yop_poll_model');

        return true;

    }


    function delete()
    {

        if (count($this->questions) > 0) {

            foreach ($this->questions as $question) {

                $question->delete();

            }

        }

        delete_yop_poll_meta($this->id, 'options');

        $GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("

					DELETE FROM " . $GLOBALS['wpdb']->yop_polls . "

					WHERE ID = %d

					", $this->id));

        wp_cache_delete($this->id, 'yop_poll_model');

        $this->_unset();

    }


    function save_questions()
    {


        $current_questions_ids = array();

        if (is_array($this->questions) && count($this->questions) > 0) {

            foreach ($this->questions as $question) {

                $question->poll_id = $this->id;

                $question->save();

                $current_questions_ids[] = $question->id;

            }

        }

        $this->remove_deleted_questions($current_questions_ids);

    }


    function remove_deleted_questions($current_questions_ids = array())
    {

        if (count($current_questions_ids) == 0) {

            $current_questions_ids[] = 0;

        }

        $questions_for_delete = $GLOBALS['wpdb']->get_col($GLOBALS['wpdb']->prepare(" SELECT ID FROM " . $GLOBALS['wpdb']->yop_poll_questions . " WHERE ID NOT IN ( " . implode(',', $current_questions_ids) . " ) AND poll_id = %d ", $this->id));

        if ($questions_for_delete) {

            foreach ($questions_for_delete as $question_id) {

                $question = new YOP_POLL_Question_Model($question_id);

                $question->delete();

            }

        }

    }


    protected static function get_poll_template_from_database($id = 0)
    {

        global $wpdb;

        if (!$id) {

            $request = $wpdb->prepare("status = %s ", "default");

        } else {

            $request = $wpdb->prepare("id = %d ", $id);

        }

        $result = $wpdb->get_row("

				SELECT *

				FROM " . $wpdb->yop_poll_templates . "

				WHERE " . $request . "

				LIMIT 0,1

				", ARRAY_A);

        return $result;

    }


    public static function get_mysql_curent_date()
    {

        return current_time('mysql');

    }


    public static function get_mysql_custom_date($interval_value = 0, $interval_unit = 'DAY')
    {

        global $wpdb;

        return $wpdb->get_var($wpdb->prepare("SELECT %s + INTERVAL %d " . esc_sql($interval_unit), current_time('mysql'), $interval_value));

    }


    protected function is_voted($vote_type = 'default', $facebook_user_details = null, $from_register = null, $SuperCookie = null, $google_user_details = null)
    {
        if ($this->vote) {

            return true;

        }


        if (isset($this->blocking_voters)) {

            $is_voted = false;

            if (in_array('dont-block', $this->blocking_voters)) {

                return false;

            }

            if (in_array("ip", $this->blocking_voters)) {

                $is_voted = $is_voted || $this->is_voted_ip();

            }

            if (in_array("cookie", $this->blocking_voters)) {


                $is_voted = $is_voted || $this->is_voted_cookie();

            }

            if (in_array("user_id", $this->blocking_voters)) {

                $is_voted = $is_voted || $this->is_voted_username($vote_type = 'default', $facebook_user_details = null, $google_user_details, $from_register);
            }

            if (in_array("supercookie", $this->blocking_voters)) {

            }

            return $is_voted;

        }

        return true;

    }


    private function is_ban($vote_type = 'default', $facebook_user_details = null, $google_user_details = null)
    {

        global $wpdb, $current_user;


        $username = $current_user->data->user_login;

        $email = $current_user->data->user_email;


        if ('facebook' == $vote_type) {

            $username = $facebook_user_details['username'];

            $email = $facebook_user_details['email'];

        }

        if ('google' == $vote_type) {

            $username = $google_user_details['displayName'];

            $id = $google_user_details['id'];

            $email = '';

        }

        if ('anonymous' == $vote_type) {

            $username = '';

            $email = '';

        }

        $ip = yop_poll_get_ip();

        $sql = $wpdb->prepare("

				SELECT id

				FROM " . $wpdb->yop_poll_bans . "

				WHERE poll_id in( 0, %d) AND

				(

				(type = 'ip' and value = %s ) OR

				(type = 'username' and value = %s ) OR

				(type = 'email' and value = %s )OR

				(type = 'userid' and value = %s )

				)

				LIMIT 0,1

				", $this->ID, $ip, $username, $email, $id);

        return $wpdb->get_var($sql);

    }


    private function is_voted_ip()
    {

        global $wpdb;

        $unit = 'DAY';

        if (isset($this->blocking_voters_interval_unit)) {

            switch ($this->blocking_voters_interval_unit) {

                case 'seconds' :

                    $unit = 'SECOND';

                    break;

                case 'minutes' :

                    $unit = 'MINUTE';

                    break;

                case 'hours' :

                    $unit = 'HOUR';

                    break;

                case 'days' :

                    $unit = 'DAY';

                    break;

            }

        }

        $value = 30;

        if (isset($this->blocking_voters_interval_value)) {

            $value = $this->blocking_voters_interval_value;

        }

        $ip = yop_poll_get_ip();

        $log_id = $wpdb->get_var($wpdb->prepare("

					SELECT id

					FROM " . $wpdb->yop_poll_results . "

					WHERE poll_id = %d AND

					ip = %s AND

					vote_date >= DATE_ADD( %s, INTERVAL -%d " . $unit . ")

					", $this->ID, $ip, current_time('mysql'), $value));


        return $log_id;

    }


    private function is_voted_cookie()
    {

        if (isset($_COOKIE['yop_poll_voted_' . $this->ID])) {

            return true;

        }

        return false;

    }


    protected function is_allowed_to_vote($vote_type = 'default', $facebook_user_details = null, $google_user_details = null)
    {

        global $current_user;

        if (self::is_ban($vote_type, $facebook_user_details, $google_user_details)) {

            return false;

        }

        if (isset($this->vote_permisions)) {


            if (in_array('guest', $this->vote_permisions) && in_array('registered', $this->vote_permisions)) {

                if ($vote_type == "wordpress" && $this->vote_permisions_wordpress == "yes") {

                    if ($current_user->ID > 0) {


                        return true;

                    }

                }

                if ($vote_type == "anonymous" && $this->vote_permisions_anonymous == "yes") {


                    return true;


                }


                return false;

            }
            if (in_array('guest', $this->vote_permisions) && !in_array('registered', $this->vote_permisions)) {

                if ($vote_type == "wordpress" && $this->vote_permisions_wordpress == "yes") {

                    if ($current_user->ID > 0) {


                        return false;

                    }

                }

                if ('facebook' == $vote_type) {


                    return false;

                }

                if ('google' == $vote_type) {

                    return false;

                }

                if ($vote_type == "anonymous" && $this->vote_permisions_anonymous != "yes") {


                    return false;


                }

                return true;

            }


            //registered only

            if (in_array('registered', $this->vote_permisions) && !in_array('guest', $this->vote_permisions)) {

                if ('anonymous' == $vote_type) {

                    return true;

                }

                if ($current_user->ID > 0 && "wordpress" == $vote_type && $this->vote_permisions_wordpress == "yes") {
                    return true;

                }

                if ('facebook' == $vote_type) {

                    if ($facebook_user_details) {

                        if ($facebook_user_details['id'] != '') {

                            return true;

                        }

                        return false;

                    }

                    return false;

                }

                if ('google' == $vote_type) {

                    if ($google_user_details) {

                        if ($google_user_details['id'] != '') {

                            return true;

                        }

                        return false;

                    }

                    return false;

                }

                return false;

            }

        }

        //guest or registered

        return true;

    }


    protected function set_vote_cookie($vote_details = array(), $vote_type = 'default', $facebook_user_details = null, $google_user_details = null)
    {

        $expire_cookie = 0;

        $value = 30;


        if (isset($this->blocking_voters_interval_value)) {

            $value = $this->blocking_voters_interval_value;

        }

        $unit = 'days';

        if (isset($this->blocking_voters_interval_unit)) {

            $unit = $this->blocking_voters_interval_unit;

        }


        switch ($unit) {

            case 'seconds' :

                $expire_cookie = time() + $value;

                break;

            case 'minutes' :

                $expire_cookie = time() + (60 * $value);

                break;

            case 'hours' :

                $expire_cookie = time() + (60 * 60 * $value);

                break;

            case 'days' :

                $expire_cookie = time() + (60 * 60 * 24 * $value);

                break;

        }

        setcookie('yop_poll_voted_' . $this->ID, $vote_details, $expire_cookie, COOKIEPATH, COOKIE_DOMAIN, false);

        setcookie('yop_poll_vote_type_' . $this->ID, $vote_type, $expire_cookie, COOKIEPATH, COOKIE_DOMAIN, false);

        if ('facebook' == $vote_type) {

            setcookie('yop_poll_vote_facebook_user_' . $this->ID, $facebook_user_details['id'], $expire_cookie, COOKIEPATH, COOKIE_DOMAIN, false);

        }

        if ('google' == $vote_type) {

            setcookie('yop_poll_vote_google_user_' . $this->ID, $google_user_details['id'], $expire_cookie, COOKIEPATH, COOKIE_DOMAIN, false);

        }

    }


    private function is_voted_username($vote_type = 'default', $facebook_user_details = null, $google_user_details, $from_register = null)
    {

        global $current_user, $wpdb;


        if (!$from_register) {
            if (isset($_COOKIE['yop_poll_vote_type_' . $this->poll['id']]))
                $vote_type = in_array($_COOKIE['yop_poll_vote_type_' . $this->poll['id']], array("anonymous")) ? $_COOKIE['yop_poll_vote_type_' . $this->poll['id']] : 'default';
            if (isset($_COOKIE['yop_poll_vote_facebook_user_' . $this->poll['id']]))
                $facebook_user_details['id'] = $_COOKIE['yop_poll_vote_facebook_user_' . $this->poll['id']];
            if (isset($_COOKIE['yop_poll_vote_google_user_' . $this->poll['id']]))
                $google_user_details['id'] = $_COOKIE['yop_poll_vote_google_user_' . $this->poll['id']];

        }


        $unit = 'DAY';

        if (isset($this->options['blocking_voters_interval_unit'])) {

            switch ($this->options['blocking_voters_interval_unit']) {

                case 'seconds' :

                    $unit = 'SECOND';

                    break;

                case 'minutes' :

                    $unit = 'MINUTE';

                    break;

                case 'hours' :

                    $unit = 'HOUR';

                    break;

                case 'days' :

                    $unit = 'DAY';

                    break;

            }

        }

        //user is guest

        if ('default' == $vote_type || 'anonymous' == $vote_type) {

            if (!is_user_logged_in()) {

                return $this->is_voted_ip();

            }

        }


        $value = 30;

        if (isset($this->options['blocking_voters_interval_value'])) {

            $value = $this->options['blocking_voters_interval_value'];

        }

        $ip = yop_poll_get_ip();

        $user_id = $current_user->ID;


        if ('facebook' == $vote_type) {

            $user_id = $facebook_user_details['id'];

            if (!$user_id) {

                return false;

            }

        }

        if ('google' == $vote_type) {

            $user_id = $google_user_details['id'];

            if (!$user_id) {

                return false;

            }

        }


        $sql = $wpdb->prepare("
					SELECT * FROM " . $wpdb->yop_poll_results . "

					WHERE poll_id = %d AND

					user_id = %d AND

					vote_date >= DATE_SUB( NOW(), INTERVAL %d " . $unit . ")

   					LIMIT 1", $this->ID, $user_id, $value);
        $result = $wpdb->get_results($sql, ARRAY_A);

        if (isset($result[0]['vote_date']))
            return true;
        else
            return false;

    }

    protected function get_voter_number_of_votes($voter)
    {

        global $wpdb;

        $result = $wpdb->get_results($wpdb->prepare("

					SELECT  *

					FROM " . $wpdb->yop_poll_results . "

					WHERE

					poll_id = %d AND

					user_id = %s AND

					user_type = %s GROUP BY vote_id

					", $voter['poll_id'], $voter['user_id'], $voter['user_type']));

        return count($result);


    }


    protected function get_votes_number_from_supercookie($cookie)
    {

        $details = explode("=", $cookie);

        $votes = explode(";", $details[1]);

        return (int)$votes[0];

    }


    public static function convert_date($original_date, $new_format = '')
    {
        $original_date = convert_date($original_date, 'Y-m-d H:i:s');
        $original_date = str_replace('-', '/', $original_date);
        return date_i18n($new_format, strtotime($original_date));

    }


    protected function user_have_votes_to_vote($voter, $cookie)
    {

        $poll_options = get_yop_poll_meta($voter['poll_id'], "options", true);
        ///yop_poll_dump($cookie);
        if ($voter['user_type'] == "anonymous") {
            if ('yes' == $poll_options['limit_number_of_votes_per_user']) {

                if ($this->get_votes_number_from_supercookie($cookie) >= $poll_options['number_of_votes_per_user']) {

                    return false;

                }
            }

        } else if ($voter['user_id'] > 0) {

            if ('yes' == $poll_options['limit_number_of_votes_per_user']) {

                if ($this->get_voter_number_of_votes($voter) >= $poll_options['number_of_votes_per_user']) {

                    return false;

                }

                if ($this->get_votes_number_from_supercookie($cookie) >= $poll_options['number_of_votes_per_user'] && in_array("cookie", $this->blocking_voters)) {

                    return false;

                }

            }

        } else {

            return false;

        }

        return true;

    }


    protected function sendMail($mail_notifications_answers, $mail_notifications_custom_fields, $vote_id)
    {

        //$options = get_option( 'yop_poll_options' );


        $headers = 'From: ' . $this->email_notifications_from_name . ' <' . $this->email_notifications_from_email . '>';

        $subject = str_replace('[POLL_NAME]', $this->poll_title, $this->email_notifications_subject);


        $body = stripslashes_deep($this->email_notifications_body);

        $regex = '/\[(\[?)(QUESTION)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/';

        $q = $this->questions;

        $body = preg_replace_callback($regex, function ($m) use ($q, $mail_notifications_answers, $mail_notifications_custom_fields) {

            $template = '';

            foreach ($q as $question) {
                //yop_poll_dump($m[5]);
                $temp_question_body = str_ireplace("%QUESTION_TEXT%", $question->question, $m[5]);


                $regex = '/\[(\[?)(ANSWERS)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/';


                $answers = $mail_notifications_answers[$question->ID];

                $temp_question_body = preg_replace_callback($regex, function ($m) use ($answers) {

                    $temp = "";

                    foreach ($answers as $a) {

                        $temp_answer_body = str_ireplace("%ANSWER_VALUE%", $a, $m[5]);

                        $temp .= $temp_answer_body;

                    }

                    return $temp;

                }, $temp_question_body);


                $regex = '/\[(\[?)(CUSTOM_FIELDS)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/';


                $custom_fields = $mail_notifications_custom_fields[$question->ID];

                $temp_question_body = preg_replace_callback($regex, function ($m) use ($custom_fields) {

                    $temp = "";

                    if (count($custom_fields) > 0) {

                        foreach ($custom_fields as $key => $value) {

                            $temp_cf_body = str_ireplace("%CUSTOM_FIELD_NAME%", $key, $m[5]);

                            $temp_cf_body = str_ireplace("%CUSTOM_FIELD_VALUE%", $value, $temp_cf_body);

                            $temp .= $temp_cf_body;

                        }

                    }

                    if ("" == $temp) {

                        $temp = "No custom fields defined";

                    }

                    return $temp;

                }, $temp_question_body);

                $template .= $temp_question_body;

            }

            return $template;

        }, $body);

        global $current_user;

        get_currentuserinfo();

        $body = str_replace('%POLL_NAME%', $this->poll_title, $body);

        $body = str_replace('%VOTE_ID%', $vote_id, $body);

        $body = str_replace( '%WP_USERNAME%', $current_user->user_login , $body );

        $body = str_replace( '%WP_FIRST_NAME%', $current_user->user_firstname , $body );

        $body = str_replace( '%POLL_TOTAL_VOTES%', $this->poll_total_votes , $body );

        $body = str_replace( '%WP_LAST_NAME%', $current_user->user_lastname , $body );

        $body = str_replace('%VOTE_DATE%', current_time('mysql'), $body);


        add_filter('wp_mail_content_type', 'yop_poll_set_html_content_type');

        wp_mail($this->email_notifications_recipients, $subject, $body, $headers);


        remove_filter('wp_mail_content_type', 'yop_poll_set_html_content_type');

    }


    public static function reset_poll_stats_from_database($poll_id)
    {
        global $message;
        $current_poll = new YOP_POLL_Poll_Model($poll_id);
        $message = self::delete_result_from_db_by_poll_id($poll_id);
        $current_poll->poll_total_votes = 0;
        foreach ($current_poll->questions as &$question) {
            foreach ($question->answers as &$answer) {
                $answer->votes = 0;
            }
        }
        $current_poll->update_no_votes();
        $current_poll->save();
    }


    private static function delete_result_from_db_by_poll_id($poll_id)
    {
        global $wpdb;
        $response['success'] = "";
        $response['error'] = "";
        $sql = $wpdb->query($wpdb->prepare("
					DELETE FROM  $wpdb->yop_poll_results
					WHERE poll_id = %d
					", $poll_id));
		$sqls = $wpdb->query($wpdb->prepare("
					DELETE FROM  $wpdb->yop_poll_votes_custom_fields
					WHERE poll_id = %d
					", $poll_id));
        if ( $sql && $sqls ) {
            $response['success'] = __yop_poll('Result deleted');
        }
        else {
            $response['error'] = __yop_poll('Could not delete result from database! Please try again!');
        }
        return $response;
    }

}
