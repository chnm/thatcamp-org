<?php

class YOP_POLL_Maintenance
{

    private function network_propagate($pfunction, $networkwide)
    {
        error_reporting(0);
        if (function_exists('is_multisite') && is_multisite()) {

            if ($networkwide) {

                $old_blog = $GLOBALS['wpdb']->blogid;

                $blogids = $GLOBALS['wpdb']->get_col("SELECT blog_id FROM {$GLOBALS['wpdb']->blogs}");

                foreach ($blogids as $blog_id) {

                    switch_to_blog($blog_id);

                    yop_poll_create_table_names($GLOBALS['wpdb']->prefix);
                    $version = get_option("yop_poll_version");

                    /* if ( version_compare( $version, '4.9.2', '<=' ) &&isset($version)) {
                         yop_poll_dump(12);
                         $current_options=get_option( 'yop_poll_options' );
                         self:: some_function(1,$current_options);


                     }      */


                    $poll_archive_page = get_page_by_path('yop-poll-archive', ARRAY_A);

                    if (!$poll_archive_page) {

                        $_p = array();

                        $_p['post_title'] = 'Yop Poll Archive';

                        $_p['post_content'] = "[yop_poll_archive]";

                        $_p['post_status'] = 'publish';

                        $_p['post_type'] = 'page';

                        $_p['comment_status'] = 'open';

                        $_p['ping_status'] = 'open';

                        $_p['post_category'] = array(1); // the default 'Uncategorised'


                        $poll_archive_page_id = wp_insert_post($_p);


                    } else {

                        $poll_archive_page_id = $poll_archive_page['ID'];

                    }


                    call_user_func(array($this, $pfunction), $networkwide);

                    $version = get_option("yop_poll_version");


                    $default_options = get_option('yop_poll_options');

                    $default_options['archive_url'] = get_permalink($poll_archive_page_id);

                    $default_options['yop_poll_archive_page_id'] = $poll_archive_page_id;


                    //addind default options

                    update_option('yop_poll_options', $default_options);


                }

                switch_to_blog($old_blog);

                yop_poll_create_table_names($GLOBALS['wpdb']->prefix);

                return;

            }
        }

        $version = get_option("yop_poll_version");

        /* if ( version_compare( $version, '4.9.2', '<=' ) &&isset($version)) {
             yop_poll_dump(12);
             $current_options=get_option( 'yop_poll_options' );
             self:: some_function(1,$current_options);



         }        */


        $poll_archive_page = get_page_by_path('yop-poll-archive', ARRAY_A);

        if (!$poll_archive_page) {

            $_p = array();

            $_p['post_title'] = 'Yop Poll Archive';

            $_p['post_content'] = "[yop_poll_archive]";

            $_p['post_status'] = 'publish';

            $_p['post_type'] = 'page';

            $_p['comment_status'] = 'open';

            $_p['ping_status'] = 'open';

            $_p['post_category'] = array(1); // the default 'Uncategorised'


            $poll_archive_page_id = wp_insert_post($_p);


        } else {

            $poll_archive_page_id = $poll_archive_page['ID'];

        }


        //addind default options

        call_user_func(array($this, $pfunction), $networkwide);


        $default_options = get_option('yop_poll_options');

        $default_options['archive_url'] = get_permalink($poll_archive_page_id);

        $default_options['yop_poll_archive_page_id'] = $poll_archive_page_id;


        //addind default options

        update_option('yop_poll_options', $default_options);


    }

    function propagate_activation($networkwide)
    {
        if (version_compare(phpversion(), '5.3', '<')) {
            wp_die("Your server is running php lower than 5.3. Please update your Php version and try again!");
        }

        $this->network_propagate('activate', $networkwide);

    }

    function propagate_deactivation($networkwide)
    {
        global $wpdb;
        //   $wpdb->query( "DROP TABLE `" . $wpdb->prefix . "yop_pollmeta`, `" . $wpdb->prefix . "yop_polls`, `" . $wpdb->prefix . "yop_poll_answermeta`, `" . $wpdb->prefix . "yop_poll_answers`, `" . $wpdb->prefix . "yop_poll_custom_fields`, `" . $wpdb->prefix . "yop_poll_logs`, `" . $wpdb->prefix . "yop_poll_voters`, `" . $wpdb->prefix . "yop_poll_bans`, `" . $wpdb->prefix . "yop_poll_templates`, `" . $wpdb->prefix . "yop_poll_votes_custom_fields`, `" . $wpdb->prefix . "yop_poll_facebook_users`" );
        $this->network_propagate('deactivate', $networkwide);
    }

    public function some_function($x = 0, $current_options = array())
    {
        $installed_version = get_option("yop_poll_version");
        global $wpdb;

        if (version_compare($installed_version, '4.9.3', '<=') || $x == 1) {
            if (!(version_compare(phpversion(), '5.3', '<'))) {
                ini_set("memory_limit", "1024M");
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                require_once(YOP_POLL_INC . 'db_schema.php');
                if (empty($current_options)) {
                    $current_options = get_option('yop_poll_options');

                    $default = get_option('yop_poll_options');
                }
                $capObj = YOP_POLL_Capabilities::get_instance();
                $capObj->install_capabilities();
                Yop_Poll_DbSchema::create_poll_database_tables();
                $wpdb->query('ALTER TABLE `' . $wpdb->yop_polls . '` CHANGE `ID` `ID` INT( 11 ) NOT NULL ');
                $this->install_default_options();

                $new_options = array(

                    'user_interface_type' => 'beginner',
                    'is_default_answer' => $current_options['is_default_answer'],
                    'poll_start_date' => current_time('mysql'),
                    'poll_end_date' => '01-01-2038 23:59:59',
                    'view_results' => array($current_options['view_results']),
                    'view_results_start_date' => convert_date($current_options['view_results_start_date'], 'd-m-Y H:i:s', 1),
                    'view_results_permissions' => array('guest', 'registered'),
                    'view_results_type' => $current_options['view_results_type'],
                    'answer_result_label' => $current_options['answer_result_label'],
                    'vote_button_label' => $current_options['vote_button_label'],
                    'template_width' => $current_options['template_width'],
                    'widget_template_width' => $current_options['widget_template_width'],
                    'view_results_link' => $current_options['view_results_link'],
                    'view_results_link_label' => $current_options['view_results_link_label'],
                    'view_back_to_vote_link' => $current_options['view_back_to_vote_link'],
                    'view_back_to_vote_link_label' => $current_options['view_back_to_vote_link_label'],
                    'view_total_votes' => $current_options['view_total_votes'],
                    'view_total_votes_label' => $current_options['view_total_votes_label'],
                    'view_total_answers' => $current_options['view_total_answers'],
                    'view_total_answers_label' => $current_options['view_total_answers_label'],
                    'message_after_vote' => $current_options['message_after_vote'],
                    'vote_permisions' => array('guest', 'registered'),
                    'vote_permisions_wordpress' => $current_options['vote_permisions_wordpress'],
                    'vote_permisions_wordpress_label' => $current_options['vote_permisions_wordpress_label'],
                    'vote_permisions_anonymous' => $current_options['vote_permisions_anonymous'],
                    'vote_permisions_anonymous_label' => $current_options['vote_permisions_anonymous_label'],
                    'blocking_voters' => array($current_options['blocking_voters']),
                    'blocking_voters_interval_value' => $current_options['blocking_voters_interval_value'],
                    'blocking_voters_interval_unit' => $current_options['blocking_voters_interval_unit'],
                    'limit_number_of_votes_per_user' => $current_options['limit_number_of_votes_per_user'],
                    'number_of_votes_per_user' => $current_options['number_of_votes_per_user'],
                    'percentages_decimals' => $current_options['percentages_decimals'],
                    'use_default_loading_image' => $current_options['use_default_loading_image'],
                    'loading_image_url' => $current_options['loading_image_url'],
                    'redirect_after_vote' => $current_options['redirect_after_vote'],
                    'redirect_after_vote_url' => $current_options['redirect_after_vote_url'],
                    'date_format' => 'UE',
                    'view_poll_archive_link' => $current_options['view_poll_archive_link'],
                    'auto_generate_poll_page' => $current_options['auto_generate_poll_page'],
                    'has_auto_generate_poll_page' => $current_options['has_auto_generate_poll_page'],
                    'use_captcha' => $current_options['use_captcha'],
                    'send_email_notifications' => $current_options['send_email_notifications'],
                    'allow_other_answers' => $current_options['allow_other_answers'],
                    'other_answers_label' => $current_options['other_answers_label'],
                    'is_default_other_answer' => $current_options['is_default_other_answer'],
                    'add_other_answers_to_default_answers' => $current_options['add_other_answers_to_default_answers'],
                    'display_other_answers_values' => $current_options['display_other_answers_values'],
                    'allow_multiple_answers' => $current_options['allow_multiple_answers'],
                    'allow_multiple_answers_number' => $current_options['allow_multiple_answers_number'],
                    'allow_multiple_answers_min_number' => $current_options['allow_multiple_answers_min_number'],
                    'display_answers' => $current_options['display_answers'],
                    'display_answers_tabulated_cols' => $current_options['display_answers_tabulated_cols'],
                    'sorting_results' => 'as_defined',
                    'sorting_answers' => 'as_defined',
                    'sorting_results_direction' => $current_options['sorting_results_direction'],
                    'sorting_answers_direction' => $current_options['sorting_answers_direction'],
                    'singular_answer_result_votes_number_label' => $current_options['singular_answer_result_votes_number_label'],
                    'plural_answer_result_votes_number_label' => $current_options['plural_answer_result_votes_number_label'],
                    'display_results' => $current_options['display_results'],
                    'display_results_tabulated_cols' => $current_options['display_results_tabulated_cols'],
                    'bar_background' => $current_options['bar_background'],
                    'bar_height' => $current_options['bar_height'],
                    'bar_border_color' => $current_options['bar_border_color'],
                    'bar_border_width' => $current_options['bar_border_width'],
                    'bar_border_style' => $current_options['bar_border_style'],
                    'sorting_archive_polls' => 'votes',
                    'sorting_archive_polls_rule' => 'asc',
                    'archive_url' => $current_options['archive_url'],
                    'archive_link_label' => $current_options['view_poll_archive_link_label'],
                    'show_poll_in_archive' => $current_options['show_in_archive'],
                    'poll_archive_order' => $current_options['archive_order'],
                    'archive_polls_per_page' => $current_options['archive_polls_per_page'],
                    'email_notifications_from_name' => $current_options['email_notifications_from_name'],
                    'email_notifications_from_email' => $current_options['email_notifications_from_email'],
                    'email_notifications_recipients' => $current_options['email_notifications_recipients'],
                    'email_notifications_subject' => $current_options['email_notifications_subject'],
                    'email_notifications_body' => '<p>A new vote was registered on %VOTE_DATE% for %POLL_NAME%</p>

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
                    'schedule_reset_poll_stats' => $current_options['schedule_reset_poll_stats'],
                    'schedule_reset_poll_date' => current_time('mysql'),
                    'schedule_reset_poll_recurring_value' => $current_options['schedule_reset_poll_recurring_value'],
                    'schedule_reset_poll_recurring_unit' => $current_options['schedule_reset_poll_recurring_unit'],
                    'singular_answer_result_votes_number_label' => __yop_poll("vote"),
                    'plural_answer_result_votes_number_label' => __yop_poll("votes"),
                    'start_scheduler' => $current_options['start_scheduler'],
                    'use_the_same_template_for_widget' => 'yes',
                    'vote_permisions_facebook' => 'no',

                    'vote_permisions_facebook_label' => __yop_poll('Vote as Facebook User'),

                    'facebook_share_after_vote' => 'no',

                    'facebook_share_description' => __yop_poll('Just casted an YOP Poll vote on ') . get_bloginfo('name'),

                    'vote_permisions_google' => 'no',

                    'vote_permisions_google_label' => __yop_poll('Vote as G+ User'),

                    'show_google_share_button' => 'no',
                    'google_integration' => 'no',
                    'facebook_integration' => 'no',
                    'facebook_show_comments_widget' => "no"
                    // 'view_total_answers_label'                  => __yop_poll( 'Poll total answers: %POLL-TOTAL-ANSWERS%' ),

                );
                if ($current_options['blocking_voters'] == 'cookie-ip')
                    $new_options['blocking_voters'] = array("cookie", 'ip');
                else {
                    if ($current_options['blocking_voters'] == 'username')
                        $new_options['blocking_voters'] = array('user_id');
                }
                list($g1, $d) = explode('-', $current_options['view_results_permissions']);
                $new_options['view_results_permissions'] = array($g1, $d);
                list($g1, $d) = explode('-', $current_options['view_results_link']);
                $new_options['view_results_link'] = array($g1, $d);
                update_option('yop_poll_options', $new_options);
                update_option("yop_poll_version", YOP_POLL_VERSION);
                YOP_POLL_Maintenance::activation_hook($default);
                $wpdb->query('ALTER TABLE `' . $wpdb->yop_polls . '` CHANGE `ID` `ID` INT( 11 ) NOT NULL AUTO_INCREMENT ');

            }
        }
        $installed_version = get_option("yop_poll_version");

        if (version_compare($installed_version, '5.2', '<=')) {
            ini_set('max_execution_time', 700);
            ini_set("memory_limit", "512M");
            $default_poll_options = get_option('yop_poll_options');
            $default_poll_options['email_notifications_body'] = '<p>A new vote was registered on %VOTE_DATE% for %POLL_NAME%</p>

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

                                                            <p><b>Vote ID:</b> <br />%VOTE_ID%</p>';
            update_option('yop_poll_options', $default_poll_options);
            $templates = self::yop_poll_get_templates_new_version_from_db();
            foreach ($templates as $template) {
                $template['js'] = <<<NOWDOC
function stripBorder_%POLL-ID%(object) {
	object.each(function() {
		if( parseInt(jQuery(this).width() ) > 0) {
			jQuery(this).width(
				parseInt(
					jQuery(this).width() ) -
					parseInt(jQuery(this).css("border-left-width")) -
					parseInt(jQuery(this).css("border-right-width"))
			);
			}
		else {
		jQuery(this).css("border-left-width", "0px");
		jQuery(this).css("border-right-width", "0px");
		}
	});
}
function stripPadding_%POLL-ID%(object) {
	object.each(function() {
		jQuery(this).width(
		parseInt( jQuery(this).width() ) -
		parseInt(jQuery(this).css("padding-left")) -
		parseInt(jQuery(this).css("padding-left"))
		);
	});
}

function strip_results_%POLL-ID%() {
	stripPadding_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%") );
	stripBorder_%POLL-ID%(  jQuery("#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%") );
}

jQuery(document).ready(function(e) {
	if(typeof window.strip_results_%POLL-ID% == "function")
		strip_results_%POLL-ID%();
	if(typeof window.tabulate_answers_%POLL-ID% == "function")
		tabulate_answers_%POLL-ID%();
	if(typeof window.tabulate_results_%POLL-ID% == "function")
		tabulate_results_%POLL-ID%();
});

function equalWidth_%POLL-ID%(obj, cols, findWidest ) {
	findWidest  = typeof findWidest  !== "undefined" ? findWidest  : false;
	if ( findWidest ) {
		obj.each(function() {
			var thisWidth = jQuery(this).width();
			width = parseInt(thisWidth / cols);
			jQuery(this).width(width);
			jQuery(this).css("float", "left");
		});
	}
	else {
		var widest = 0;
		obj.each(function() {
			var thisWidth = jQuery(this).width();
			if(thisWidth > widest) {
				widest = thisWidth;
			}
		});
		width = parseInt( %POLL-WIDTH% / cols[0]);
		obj.width(width-20);
		obj.css("float", "left");
	}
}

function equalWidth2_%POLL-ID%(obj, cols, findWidest ) {
	findWidest  = typeof findWidest  !== "undefined" ? findWidest  : false;
	if ( findWidest ) {
		obj.each(function() {
			var thisWidth = jQuery(this).width();
			width = parseInt(thisWidth / cols);
			jQuery(this).width(width);
			jQuery(this).css("float", "left");
		});
	}
	else {
		var widest = 0;
		obj.each(function() {
			var thisWidth = jQuery(this).width();
			if(thisWidth > widest) {
				widest = thisWidth;
			}
		});
		width = parseInt( %POLL-WIDTH% / cols[1]);
		obj.width(width-20);
		obj.css("float", "left");
	}
}
function tabulate_answers_%POLL-ID%() {

	equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%"), %ANSWERS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div "), %ANSWERS-TABULATED-COLS%, true );
}

function tabulate_results_%POLL-ID%() {
	equalWidth2_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%"), %RESULTS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div "), %RESULTS-TABULATED-COLS%, true );
	}

jQuery(document).ready(function(){
	runOnPollStateChange_%POLL-ID%();
});

function runOnPollStateChange_%POLL-ID%() {

};
NOWDOC;
                self::update_poll_template_in_database(($template));
            }
            $polls = self::yop_poll_get_polls_for_body_mail_update();
            foreach ($polls as $poll) {
                $current = new YOP_POLL_Poll_Model($poll['ID']);
                $current->email_notifications_body = '<p>A new vote was registered on %VOTE_DATE% for %POLL_NAME%</p>

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

                                                            <p><b>Vote ID:</b> <br />%VOTE_ID%</p>';
                $current->save();

            }
            update_option("yop_poll_version", YOP_POLL_VERSION);
        }
        $installed_version = get_option("yop_poll_version");
        if (version_compare($installed_version, '5.3', '<=')) {
            update_option("yop_poll_version", YOP_POLL_VERSION);
        }
        $installed_version = get_option("yop_poll_version");
        if (version_compare($installed_version, '5.5', '<=')) {
            update_option("yop_poll_version", YOP_POLL_VERSION);
        }
        $installed_version = get_option("yop_poll_version");

        if (version_compare($installed_version, '5.6', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", YOP_POLL_VERSION);
            $default_poll_options = get_option('yop_poll_options');
            $default_poll_options['show_results_in'] = "bar";
            update_option('yop_poll_options', $default_poll_options);

            $wpdb->query('ALTER TABLE `' . $wpdb->yop_poll_templates . '` ADD `after_vote_template_chart` text');
            update_option("yop_poll_version", '5.7');

        }
        if (version_compare($installed_version, '5.7', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", YOP_POLL_VERSION);

            $templates = self::yop_poll_get_templates_new_version_from_db();
            foreach ($templates as $template) {
                $template['js'] = <<<NOWDOC
	function stripBorder_%POLL-ID%(object) {
	object.each(function() {
		if( parseInt(jQuery(this).width() ) > 0) {
			jQuery(this).width(
				parseInt(
					jQuery(this).width() ) -
					parseInt(jQuery(this).css("border-left-width")) -
					parseInt(jQuery(this).css("border-right-width"))
			);
			}
		else {
		jQuery(this).css("border-left-width", "0px");
		jQuery(this).css("border-right-width", "0px");
		}
	});
}
function stripPadding_%POLL-ID%(object) {
	object.each(function() {
		jQuery(this).width(
		parseInt( jQuery(this).width() ) -
		parseInt(jQuery(this).css("padding-left")) -
		parseInt(jQuery(this).css("padding-left"))
		);
	});
}

function strip_results_%POLL-ID%() {
	stripPadding_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%") );
	stripBorder_%POLL-ID%(  jQuery("#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%") );
}

jQuery(document).ready(function(e) {
   jQuery('.yop-poll-forms').removeClass('yop-poll-forms-display');
	if(typeof window.strip_results_%POLL-ID% == "function")
		strip_results_%POLL-ID%();
	if(typeof window.tabulate_answers_%POLL-ID% == "function")
		tabulate_answers_%POLL-ID%();
	if(typeof window.tabulate_results_%POLL-ID% == "function")
		tabulate_results_%POLL-ID%();



});

function equalWidth_%POLL-ID%(obj, cols, findWidest ) {

    findWidest  = typeof findWidest  !== "undefined" ? findWidest  : false;
    var quest=0;
    if ( findWidest ) {
        obj.each(function() {
            var thisWidth = jQuery(this).width();
            width = parseInt(thisWidth / cols);
            jQuery(this).width(width);
            jQuery(this).css("float", "left");
        });
    }
    else {
        var widest = 0;
        var count  = 0;
        var poz_each_question=0;

        obj.each(function() {

            count++;
            cols[quest][2]=(jQuery('#yop-poll-answers-%POLL-ID%-'+ cols[quest][3] +' li').length);
            var thisWidth = jQuery(this).width();
            if(thisWidth > widest) {
                widest = thisWidth;
            }
            if(count<cols[quest][2])
            { width = parseInt( %POLL-WIDTH% / cols[quest][0]);
             if(cols[quest][0]==1)
                        jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).css("width","100%");
             else
                         jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).width(width-20);
             jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).css("float", "left");
            }
            else
            {
                count=0;


                width = parseInt( %POLL-WIDTH% / cols[quest][0]);
                jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).width(width-20);
                jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).css("float", "left");
                quest++;
            }

        });
}
}

function equalWidth2_%POLL-ID%(obj, cols, findWidest ) {
    findWidest  = typeof findWidest  !== "undefined" ? findWidest  : false;
    var quest=0;

    if ( findWidest ) {
        obj.each(function() {
            var thisWidth = jQuery(this).width();
            width = parseInt(thisWidth / cols);
            jQuery(this).width(width);
            jQuery(this).css("float", "left");
        });
    }
    else {
        var widest = 0;
        var count  = 0;
        var poz_each_question=0;

        obj.each(function() {
            count++;
            cols[quest][2]=(jQuery('#yop-poll-answers-%POLL-ID%-'+ cols[quest][3] +' li').length);
            var thisWidth = jQuery(this).width();
            if(thisWidth > widest) {
                widest = thisWidth;
            }
            if(count<cols[quest][2])
            { width = parseInt( %POLL-WIDTH% / cols[quest][1]);
             jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).width(width-20);
             jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).css("float", "left");
            }
            else
            {
                count=0;


                width = parseInt( %POLL-WIDTH% / cols[quest][1]);
                jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).width(width-20);
                jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).css("float", "left");
                quest++;
            }

        });
}
}
function tabulate_answers_%POLL-ID%() {

	equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%"), %ANSWERS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div "), %ANSWERS-TABULATED-COLS%, true );
}

function tabulate_results_%POLL-ID%() {
	equalWidth2_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%"), %RESULTS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div "), %RESULTS-TABULATED-COLS%, true );
	}

jQuery(document).ready(function(){
	runOnPollStateChange_%POLL-ID%();
});

function runOnPollStateChange_%POLL-ID%() {

};
NOWDOC;
                $template['after_vote_template_chart'] = <<<NOWDOC
	[QUESTION_CONTAINER]
	<div id = "yop-poll-question-container-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-question-container-%POLL-ID%">
		<div id = "yop-poll-question-%POLL-ID%-%QUESTION-ID%"
             class = "yop-poll-question-%POLL-ID%">%POLL-QUESTION%</div>
		<div id = "yop-poll-answers-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-answers-%POLL-ID%">
                       <div id = "yop-poll-answers-chart-canvas-%POLL-ID%-%QUESTION-ID%" style="text-align:center;"  class="yop-poll-answers-container-chart-%POLL-ID% yop-poll-center-chart">
			<canvas id="yop-poll-answers-chart-%POLL-ID%-%QUESTION-ID%" class="yop-poll-answers-chart"></canvas>
                       </div>
		</div>
	</div>
	<div class = "yop-poll-clear-%POLL-ID%"></div>
	[/QUESTION_CONTAINER]
<div id = "yop-poll-vote-%POLL-ID%" class = "yop-poll-footer">
	<div>%POLL-TOTAL-ANSWERS-LABEL%</div>
	<div>%POLL-TOTAL-VOTES-LABEL%</div>
	<div id = "yop-poll-back-%POLL-ID%">%POLL-BACK-TO-VOTE-LINK%</div>
	<div id = "yop-poll-archive-%POLL-ID%">%POLL-VIEW-ARCHIVE-LINK%</div>
	%SHARE-BUTTON%
</div>
NOWDOC;
                self::update_poll_template_in_database2($template);
            }
            update_option("yop_poll_version", '5.7.1');

        }
        if (version_compare($installed_version, '5.7.1', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.2');
        }
        if (version_compare($installed_version, '5.7.2', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.3');
        }
        if (version_compare($installed_version, '5.7.3', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.4');
        }
        if (version_compare($installed_version, '5.7.4', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.5');
        }if (version_compare($installed_version, '5.7.5', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.6');
        }
        if (version_compare($installed_version, '5.7.6', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.7');
        }
        if (version_compare($installed_version, '5.7.7', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.8');
        }
        if (version_compare($installed_version, '5.7.8', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.7.9');
        }
        if (version_compare($installed_version, '5.7.9', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.8.0');
        }
        if (version_compare($installed_version, '5.8.0', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.8.1');
        }
        if (version_compare($installed_version, '5.8.1', '<=')) {
            global $wpdb;
            update_option("yop_poll_version", '5.8.2');
        }
    }

    private static function update_poll_template_in_database2($template)
    {
        global $wpdb;
        $sql = $wpdb->query($wpdb->prepare("
					UPDATE " . $wpdb->yop_poll_templates . "
					SET name = %s,
					before_vote_template = %s,
					after_vote_template = %s,
					after_vote_template_chart = %s,
					before_start_date_template = %s,
					after_end_date_template = %s,
					css = %s,
					js = %s,
					last_modified = %s
					WHERE
					id = %d
					", $template['name'], $template['before_vote_template'], $template['after_vote_template'], $template['after_vote_template_chart'], $template['before_start_date_template'], $template['after_end_date_template'], $template['css'], $template['js'], current_time('mysql'), $template['id']));
        return $sql;
    }

    public function yop_poll_get_polls_for_body_mail_update()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT ID
                            FROM   " . $wpdb->yop_polls . " ORDER BY ID ASC
                            "), ARRAY_A);
        return $result;
    }

    private function install_default_options()
    {
        $default_poll_options = get_option('yop_poll_options');

        $default_poll_options['is_default_answer'] = 'no';
        $default_poll_options['poll_start_date'] = current_time('mysql');
        $default_poll_options['poll_end_date'] = '01-01-2038 23:59:59';

        update_option('yop_poll_options', $default_poll_options);
    }

    private function uninstall_default_options()
    {
        //    delete_option( "yop_poll_version" );
        //  delete_option( "yop_poll_options" );
    }

    public function activate($networkwide)
    {
        if (!current_user_can('activate_plugins')) {
            $error = new WP_Error ('Wordpress_version_error', __yop_poll('You need permissions to activate this plugin'), __yop_poll('Error: Wordpress Activation Permissions Problem'));
        }

        if (!version_compare($GLOBALS['wp_version'], YOP_POLL_WP_VERSION, '>=')) {
            $error = new WP_Error ('Wordpress_version_error', sprintf(__yop_poll('You need at least Wordpress version %s to use this plugin'), YOP_POLL_WP_VERSION), __yop_poll('Error: Wordpress Version Problem'));
            $error = new WP_Error ('Wordpress_version_error', sprintf(__yop_poll('You need at least Wordpress version %s to use this plugin'), YOP_POLL_WP_VERSION), __yop_poll('Error: Wordpress Version Problem'));
        }

        if (isset ($error) && is_wp_error($error)) {
            wp_die($error->get_error_message(), $error->get_error_data());
        }


        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        require_once(YOP_POLL_INC . 'db_schema.php');
        $this->install_default_options();
        $capObj = YOP_POLL_Capabilities::get_instance();
        $capObj->install_capabilities();
        Yop_Poll_DbSchema::install_database();
    }

    public function deactivatedelete($networkwide)
    {
        require_once(YOP_POLL_INC . 'db_schema.php');
        Yop_Poll_DbSchema::delete_database_tables();
        $capObj = YOP_POLL_Capabilities::get_instance();
        $capObj->uninstall_capabilities();
        $this->uninstall_default_options();
    }

    function new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta)
    {
        if (!function_exists('is_plugin_active_for_network')) {
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        }
        if (is_plugin_active_for_network(YOP_POLL_SHORT_PLUGIN_FILE)) {
            $old_blog = $GLOBALS['wpdb']->blogid;
            switch_to_blog($blog_id);
            yop_poll_create_table_names($GLOBALS['wpdb']->prefix);
            $this->activate(null);
            switch_to_blog($old_blog);
            yop_poll_create_table_names($GLOBALS['wpdb']->prefix);
        }
    }

    function delete_blog($blog_id)
    {
        $old_blog = $GLOBALS['wpdb']->blogid;
        switch_to_blog($blog_id);
        yop_poll_create_table_names($GLOBALS['wpdb']->prefix);
        $this->deactivatedelete(null);
        require_once(YOP_POLL_INC . 'db_schema.php');
        // Yop_Poll_DbSchema::delete_database_tables();
        $capObj = YOP_POLL_Capabilities::get_instance();
        $capObj->uninstall_capabilities();
        $this->uninstall_default_options();
        switch_to_blog($old_blog);
        yop_poll_create_table_names($GLOBALS['wpdb']->prefix);
    }

    function update()
    {
    }

    public function activation_hook($default)
    {
        ini_set('max_execution_time', 700);
        $current_options = get_option('yop_poll_options');
        $polls = self::yop_poll_get_polls_from_db();
        $answers = self::yop_poll_get_answers_from_db();
        $logs = self::yop_poll_get_logs_from_db();
        $bans = self::yop_poll_get_bans_from_db();
        $custom_fields = self::yop_poll_get_custom_fields_from_db();
        $custom_votes = self::yop_poll_get_custom_fields_votes_from_db();
        $metas = self::yop_poll_get_polls_meta_from_db();
        $answers_meta = self::yop_poll_get_answers_meta_from_db();
        $templates = self::yop_poll_get_templates_from_db();
        foreach ($templates as $template) {
            $template['before_vote_template'] = "[QUESTION_CONTAINER]" . $template['before_vote_template'];
            $template['before_vote_template'] = str_replace('%POLL-ANSWER-LABEL%', '%POLL-ANSWER-LABEL% [ANSWER_RESULT_CONTAINER]', $template['before_vote_template']);
            $template['before_vote_template'] = str_replace('%POLL-TOTAL-ANSWERS%', '%POLL-TOTAL-ANSWERS-LABEL%', $template['before_vote_template']);
            $template['before_vote_template'] = str_replace('%POLL-TOTAL-VOTES%', '%POLL-TOTAL-VOTES-LABEL%', $template['before_vote_template']);
            $template['after_vote_template'] = str_replace('%POLL-TOTAL-VOTES%', '%POLL-TOTAL-VOTES-LABEL%', $template['after_vote_template']);
            $template['after_vote_template'] = str_replace('%POLL-TOTAL-ANSWERS%', '%POLL-TOTAL-ANSWERS-LABEL%', $template['after_vote_template']);
            $template['after_end_date_template'] = str_replace('%POLL-TOTAL-ANSWERS%', '%POLL-TOTAL-ANSWERS-LABEL%', $template['after_end_date_template']);
            $template['after_end_date_template'] = str_replace('%POLL-TOTAL-VOTES%', '%POLL-TOTAL-VOTES-LABEL%', $template['after_end_date_template']);
            $template['before_vote_template'] = preg_replace('/%POLL-ANSWER-RESULT-BAR%/', '%POLL-ANSWER-RESULT-BAR%  [/ANSWER_RESULT_CONTAINER]', $template['before_vote_template'], 1);
            $find = '[/ANSWER_RESULT_CONTAINER]';
            $replace = '';
            //   $template['before_vote_template']= preg_replace(strrev("/$find/"),strrev($replace),strrev( $template['before_vote_template']),1);
            $template['after_vote_template'] = "[QUESTION_CONTAINER]" . $template['after_vote_template'];
            $template['after_end_date_template'] = "[QUESTION_CONTAINER]" . $template['after_end_date_template'];
            /*  if(strpos($template['before_vote_template'],"[CAPTCHA_CONTAINER]"))
                  $template['before_vote_template']=str_replace('[CAPTCHA_CONTAINER]','[/QUESTION_CONTAINER][CAPTCHA_CONTAINER]',$template['before_vote_template']);
              else{
                  $template['before_vote_template']=str_replace('<div id="yop-poll-vote-%POLL-ID%" class="yop-poll-footer">','[/QUESTION_CONTAINER]<div id="yop-poll-vote-%POLL-ID%" class="yop-poll-footer">',$template['before_vote_template']);
              } */
            $template['before_vote_template'] = $template['before_vote_template'] . '[/QUESTION_CONTAINER]';
            if (strpos($template['before_vote_template'], "%POLL-QUESTION%") && strpos($template['before_vote_template'], '%POLL-NAME%')) {
                $template['before_vote_template'] = str_replace('%POLL-QUESTION%', '', $template['before_vote_template']);
                $template['before_vote_template'] = str_replace('%POLL-NAME%', '%POLL-QUESTION%', $template['before_vote_template']);
            }
            if (strpos($template['after_vote_template'], "%POLL-QUESTION%") && strpos($template['after_vote_template'], '%POLL-NAME%')) {
                $template['after_vote_template'] = str_replace('%POLL-QUESTION%', '', $template['after_vote_template']);
                $template['after_vote_template'] = str_replace('%POLL-NAME%', '%POLL-QUESTION%', $template['after_vote_template']);
            }
            $template['after_vote_template'] = $template['after_vote_template'] . '[/QUESTION_CONTAINER]';
            $template['after_end_date_template'] = $template['after_end_date_template'] . '[/QUESTION_CONTAINER]';
            $template['js'] = <<<NOWDOC
function stripBorder_%POLL-ID%(object) {
	object.each(function() {
		if( parseInt(jQuery(this).width() ) > 0) {
			jQuery(this).width(
				parseInt(
					jQuery(this).width() ) -
					parseInt(jQuery(this).css("border-left-width")) -
					parseInt(jQuery(this).css("border-right-width"))
			);
			}
		else {
		jQuery(this).css("border-left-width", "0px");
		jQuery(this).css("border-right-width", "0px");
		}
	});
}
function stripPadding_%POLL-ID%(object) {
	object.each(function() {
		jQuery(this).width(
		parseInt( jQuery(this).width() ) -
		parseInt(jQuery(this).css("padding-left")) -
		parseInt(jQuery(this).css("padding-left"))
		);
	});
}

function strip_results_%POLL-ID%() {
	stripPadding_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%") );
	stripBorder_%POLL-ID%(  jQuery("#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%") );
}

jQuery(document).ready(function(e) {
   jQuery('.yop-poll-forms').removeClass('yop-poll-forms-display');
	if(typeof window.strip_results_%POLL-ID% == "function")
		strip_results_%POLL-ID%();
	if(typeof window.tabulate_answers_%POLL-ID% == "function")
		tabulate_answers_%POLL-ID%();
	if(typeof window.tabulate_results_%POLL-ID% == "function")
		tabulate_results_%POLL-ID%();



});

function equalWidth_%POLL-ID%(obj, cols, findWidest ) {

    findWidest  = typeof findWidest  !== "undefined" ? findWidest  : false;
    var quest=0;
    if ( findWidest ) {
        obj.each(function() {
            var thisWidth = jQuery(this).width();
            width = parseInt(thisWidth / cols);
            jQuery(this).width(width);
            jQuery(this).css("float", "left");
        });
    }
    else {
        var widest = 0;
        var count  = 0;
        var poz_each_question=0;

        obj.each(function() {

            count++;
            cols[quest][2]=(jQuery('#yop-poll-answers-%POLL-ID%-'+ cols[quest][3] +' li').length);
            var thisWidth = jQuery(this).width();
            if(thisWidth > widest) {
                widest = thisWidth;
            }
            if(count<cols[quest][2])
            { width = parseInt( %POLL-WIDTH% / cols[quest][0]);
             if(cols[quest][0]==1)
                        jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).css("width","100%");
             else
                         jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).width(width-20);
             jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).css("float", "left");
            }
            else
            {
                count=0;


                width = parseInt( %POLL-WIDTH% / cols[quest][0]);
                jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).width(width-20);
                jQuery(".yop-poll-li-answer-%POLL-ID%-"+cols[quest][3]).css("float", "left");
                quest++;
            }

        });
}
}

function equalWidth2_%POLL-ID%(obj, cols, findWidest ) {
    findWidest  = typeof findWidest  !== "undefined" ? findWidest  : false;
    var quest=0;

    if ( findWidest ) {
        obj.each(function() {
            var thisWidth = jQuery(this).width();
            width = parseInt(thisWidth / cols);
            jQuery(this).width(width);
            jQuery(this).css("float", "left");
        });
    }
    else {
        var widest = 0;
        var count  = 0;
        var poz_each_question=0;

        obj.each(function() {
            count++;
            cols[quest][2]=(jQuery('#yop-poll-answers-%POLL-ID%-'+ cols[quest][3] +' li').length);
            var thisWidth = jQuery(this).width();
            if(thisWidth > widest) {
                widest = thisWidth;
            }
            if(count<cols[quest][2])
            { width = parseInt( %POLL-WIDTH% / cols[quest][1]);
             jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).width(width-20);
             jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).css("float", "left");
            }
            else
            {
                count=0;


                width = parseInt( %POLL-WIDTH% / cols[quest][1]);
                jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).width(width-20);
                jQuery(".yop-poll-li-result-%POLL-ID%-"+cols[quest][3]).css("float", "left");
                quest++;
            }

        });
}
}
function tabulate_answers_%POLL-ID%() {

	equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%"), %ANSWERS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div "), %ANSWERS-TABULATED-COLS%, true );
}

function tabulate_results_%POLL-ID%() {
	equalWidth2_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%"), %RESULTS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div "), %RESULTS-TABULATED-COLS%, true );
	}

jQuery(document).ready(function(){
	runOnPollStateChange_%POLL-ID%();
});

function runOnPollStateChange_%POLL-ID%() {

};

NOWDOC;
            $template['js'] = addslashes($template['js']);
            $templates_ids[$template['id']] = self::insert_template_in_databease(($template));
        }

        foreach ($polls as $poll) {

            foreach ($answers as $answer) {
                if ($answer['poll_id'] == $poll['id']) {
                    $answers_ordonate[$poll['id']][] = (array)$answer;
                }
                if ($answer['poll_id'] > $poll['id']) {
                    break;
                }

                foreach ($logs as $log) {
                    if ($log['poll_id'] == $poll['id'] && $log['answer_id'] == $answer['id']) {
                        $logs_ordonate[$poll['id']][] = (array)$log;
                        $logs_ordonate_details[$log['id']][] = $answer['answer'];
                    }
                }
                foreach ($answers_meta as $answer_meta) {
                    if ($answer_meta['yop_poll_answer_id'] == $answer['id']) {
                        $answer_meta_ordonate[$answer['id']][] = (array)$answer_meta;
                    }
                }
            }

            foreach ($bans as $ban) {
                if ($ban['poll_id'] == $poll['id']) {
                    $bans_ordonate[$poll['id']][] = (array)$ban;
                }
                if ($ban['poll_id'] > $poll['id']) {
                    break;
                }
            }

            foreach ($metas as $meta) {
                if ($meta['yop_poll_id'] == $poll['id']) {
                    $metas_ordonate[$poll['id']][] = (array)$meta;
                }
                if ($meta['yop_poll_id'] > $poll['id']) {
                    break;
                }
            }

            foreach ($custom_fields as $custom_field) {
                if ($custom_field['poll_id'] == $poll['id']) {
                    $custom_fields_ordonate[$poll['id']][] = (array)$custom_field;
                    if ($custom_field['poll_id'] > $poll['id']) {
                        break;
                    }

                    foreach ($custom_votes as $custom_vote) {
                        if ($custom_field['id'] == $custom_vote['custom_field_id']) {
                            $custom_votes_fields_ordonate[$custom_field['id']][] = (array)$custom_vote;
                        }
                    }
                }
            }
        }


        foreach ($polls as $poll) {
            $current_poll = new YOP_POLL_Poll_Model();
            $current_poll->poll_author = $poll['poll_author'];
            $current_poll->ID = $poll['id'];
            $current_poll->poll_title = $poll['name'];
            $current_poll->poll_name = $poll['name'];
            $current_poll->poll_date = $poll['date_added'];
            $current_poll->poll_modified = $poll['last_modified'];
            $current_poll->poll_status = $poll['status'];
            $current_poll->poll_type = "poll";
            $current_poll->poll_status = $poll['status'];
            $current_poll->email_notifications_body = '<p>A new vote was registered on %VOTE_DATE% for %POLL_NAME%</p>

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

                                                            <p><b>Vote ID:</b> <br />%VOTE_ID%</p>';
            if ($poll['end_date'] <= "2038-01-18 23:59:59") {

                $current_poll->poll_end_date = convert_date($poll['end_date'], 'd-m-Y H:i:s', 1);

            } else {

                $current_poll->poll_end_date = "01-01-2038 23:59:59";

            }

            if ($poll['start_date'] <= "2038-01-18 23:59:59") {

                $current_poll->poll_start_date = convert_date($poll['start_date'], 'd-m-Y H:i:s', 1);

            } else {

                $current_poll->poll_start_date = "01-01-2038 23:59:59";

            }
            $current_poll->poll_total_votes = $poll['total_votes'];
            $question = new YOP_POLL_Question_Model();
            $question->type = "text";
            $question->question = $poll['question'];
            $question->question_date = $current_poll->poll_date;
            $question->question_author = $current_poll->poll_author;
            $question->question_modified = $current_poll->poll_modified;
            $question->question_status = $current_poll->poll_status;
            $question->poll_order = 1;
            $i = 0;


            foreach ($answers_ordonate[$poll['id']] as $answer_ordonate) {

                if ($answer_ordonate['type'] == "other") {
                    $current_poll->allow_other_answers = 'yes';
                    $question->allow_other_answers = 'yes';
                }
            }
            $q[] = $question;
            // yop_poll_dump($current_poll);
            $current_poll->questions = $q;
            $current_poll->auto_generate_poll_page = "no";
            $current_poll_id = $current_poll->insert();
            $current_poll_id = $current_poll->ID;
            // yop_poll_dump($current_poll->save())
            $no_modif = 0;
            $no_modif2 = 0;
            $current_poll_meta = $current_options;
            if (isset($metas_ordonate[$poll['id']])) {
                $poll_option = maybe_unserialize($metas_ordonate[$poll['id']][0]['meta_value']);
                foreach ($current_options as $key => $value) {
                    if (isset($poll_option[$key])) {

                        if ($key == 'view_results_permissions') {

                            list($g1, $d) = explode('-', $poll_option[$key]);
                            if ($g1 == "quest") {
                                $g1 = "guest";

                            }

                            $current_poll_meta[$key] = array($g1, $d);
                            $no_modif = 1;

                        } else
                            if ($key == 'vote_permisions') {
                                list($g1, $d) = explode('-', $poll_option['vote_permisions']);
                                if ($g1 == "quest") {
                                    $g1 = "guest";

                                }
                                $no_modif2 = 1;
                                $current_poll_meta[$key] = array($g1, $d);
                            } else
                                $current_poll_meta[$key] = $poll_option[$key];


                    } else
                        $current_poll_meta[$key] = $default[$key];
                }
            }
            $current_poll_meta['view_results'] = array($poll_option['view_results']);
            $current_poll_meta['sorting_results'] = $default['sorting_answers'];
            if (!isset($poll_option['view_results_link'])) {
                $current_poll_meta['view_results_link'] = "yes";
            } else
                $current_poll_meta['view_results_link'] = $poll_option['view_results_link'];

            if ($poll_option['view_results'] == 'custom-date')
                $current_poll_meta['view_results_start_date'] = $poll_option['view_results_start_date'];
            if (empty($current_poll_meta['view_results'][0])) {
                $current_poll_meta['view_results'] = array('after');
            }
            if ($no_modif == 0 || empty($current_poll_meta['view_results'])) {
                $current_poll_meta['view_results_permissions'] = array('guest', 'registered');

            }

            if ($no_modif2 == 0 || empty($current_poll_meta['vote_permisions'])) {
                $current_poll_meta['vote_permisions'] = array('guest', 'registered');
            }

            if ($poll_option['blocking_voters'] == 'cookie-ip')
                $current_poll_meta['blocking_voters'] = array("cookie", 'ip');
            else {
                if ($poll_option['blocking_voters'] == 'username')
                    $current_poll_meta['blocking_voters'] = array("user_id");
                else
                    $current_poll_meta['blocking_voters'] = array('dont-block');
            }
            $current_poll_meta['facebook_share_after_vote'] = "no";
            $current_poll_meta['show_google_share_button'] = "no";
            $current_poll_meta['facebook_show_comments_widget'] = "no";
            $current_poll_meta['facebook_share_description'] = __yop_poll('Just casted an YOP Poll vote on ') . get_bloginfo('name');
            if (isset($poll_option['show_in_archive']))
                $current_poll_meta['show_poll_in_archive'] = $poll_option['show_in_archive'];
            if (isset($poll_option['schedule_reset_poll_date']))
                $current_poll_meta['schedule_reset_poll_date'] = $poll_option['schedule_reset_poll_date'];
            if (isset($poll_option['template'])) {
                $current_poll_meta['template'] = $templates_ids[$poll_option['template']];
            } else  $current_poll_meta['template'] = 1;
            if (isset($poll_option['widget_template'])) {
                $current_poll_meta['widget_template'] = $templates_ids[$poll_option['widget_template']];
            } else        $current_poll_meta['widget_template'] = 1;
            if (isset($poll_option['widget_template']) && isset($poll_option['template']) && $poll_option['template'] != $poll_option['widget_template']) {
                $current_poll_meta['use_the_same_template_for_widget'] = "no";
            }
            if (isset($poll_option['view_total_answers_label'])) {
                $current_poll_meta['view_total_answers_label'] = $default['view_total_answers_label'];
            }
            if (isset($poll_option['answer_result_label'])) {
                $current_poll_meta['answer_result_label'] = $default['answer_result_label'];
            }
            if ($current_poll_meta['vote_permisions_anonymous'] == "no" && $current_poll_meta['vote_permisions_wordpress'] == "no") {
                $current_poll_meta['vote_permisions_anonymous'] = "yes";
            }
            update_yop_poll_question_meta($current_poll_id, 'options', $current_poll_meta);
            update_yop_poll_meta($current_poll_id, 'options', $current_poll_meta);
            if ($poll['show_in_archive'] == "yes") {
                self::save_poll_order($current_poll_id, $poll['archive_order']);
            }

            if ($default['sorting_answers'] == "votes") {
                $answers_ordonate[$poll['id']] = self::get_answers_order_by_votes($poll['id'], $default['sorting_answers_direction']);
            }
            if ($default['sorting_answers'] == "alphabetical") {
                $answers_ordonate[$poll['id']] = self::get_answers_order_by_alph($poll['id'], $default['sorting_answers_direction']);
            }
            foreach ($answers_ordonate[$poll['id']] as $answer_ordonate) {
                $ans = new YOP_POLL_Answer_Model();
                $ans->answer = $answer_ordonate['answer'];
                $ans->answer_author = $current_poll->poll_author;
                $ans->answer_date = $current_poll->poll_date;
                $ans->answer_modified = $poll['last_modified'];
                if ($answer_ordonate['type'] != "other") {
                    $ans->type = "text";
                } else {
                    $current_poll->allow_other_answers = 'yes';
                    $question->allow_other_answers = 'yes';
                    $ans->type = "other";
                }
                $ans->answer_status = $answer['status'];
                $ans->votes = $answer_ordonate['votes'];
                $ans->question_order = $i++;

                if ($answer_ordonate['type'] != "other") {
                    $answersa[] = $ans;
                    if (isset($answer_meta_ordonate[$answer_ordonate['id']])) {
                        $answer_option = maybe_unserialize($answer_meta_ordonate[$answer_ordonate['id']][0]['meta_value']);
                        $answer_options = $ans->options;
                        foreach ($ans->options as $key => &$value) {
                            if (isset($answer_option[$key])) {
                                $ans->$key = $answer_option[$key];
                            }

                        }
                        $ans->options = $answer_options;
                    }

                    $question->addAnswer($ans);

                    $question->save_answers();
                    $answersids[$answer_ordonate['id']] = $ans->ID;
                }

            }
            if (!empty($custom_fields_ordonate[$poll['id']]))
                foreach ($custom_fields_ordonate[$poll['id']] as $custom_ordonate) {
                    $oldid = $custom_ordonate['id'];
                    //   $question                       = $current_poll->questions;
                    $custom_ordonate['question_id'] = $current_poll_id;
                    $custom_ordonate['poll_id'] = $current_poll_id;
                    $newid = insert_custom_field_in_db($custom_ordonate);
                    foreach ($custom_votes_fields_ordonate[$oldid] as $votes) {
                        $votes['custom_field_id'] = $newid;
                        $votes['question_id'] = $current_poll_id;
                        $votes['poll_id'] = $current_poll_id;
                        $votes['id'] = insert_votes_custom_in_db($votes);
                        $custom_field_vote[$poll['id']][] = $votes;
                    }
                }

            if (!empty($logs_ordonate[$poll['id']]))
                foreach ($logs_ordonate[$poll['id']] as $log_ordonate) {
                    $log['poll_id'] = $current_poll_id;
                    $log['vote_id'] = $log_ordonate['vote_id'];
                    $log['ip'] = $log_ordonate['ip'];
                    $log['user_id'] = $log_ordonate['user_id'];
                    $log['user_type'] = $log_ordonate['user_type'];
                    $log['vote_date'] = $log_ordonate['vote_date'];
                    $log['tr_id'] = $log_ordonate['tr_id'];
                    $vote_details[1]["q-" . $current_poll_id]['question'] = $poll['question'];
                    $vote_details[1]["q-" . $current_poll_id]['id'] = $current_poll_id;
                    $vote_details[1]["q-" . $current_poll_id]['a'][] = $answersids[$log_ordonate['answer_id']];
                    if ($log_ordonate['other_answer_value'] != "" && isset($log_ordonate['other_answer_value'])) {
                        $ans = new YOP_POLL_Answer_Model();
                        $ans->answer = $answer_ordonate['answer'];
                        $ans->answer_author = $current_poll->poll_author;
                        $ans->answer_date = $current_poll->poll_date;
                        $ans->answer_modified = $poll['last_modified'];
                        $ans->answer = $log_ordonate['other_answer_value'];
                        $ans->answer_status = 'active';
                        $ans->votes = 1;
                        $ans->type = 'other';
                        $ans->question_order = $i++;
                        $question->addAnswer($ans);
                        $question->save_answers();
                    }
                    if (!empty($custom_field_vote[$poll['id']]))
                        foreach ($custom_field_vote[$poll['id']] as $vote) {
                            if ($vote['vote_id'] == $log_ordonate['vote_id']) {
                                $vote_details[1]["q-" . $current_poll_id]['cf'][] = $vote['id'];
                            }

                        }
                    foreach ($logs_ordonate_details[$log_ordonate['id']] as $a) {
                        $vote_details[1]["q-" . $current_poll_id]['answers'][] = $a;
                        $log['vote_details'] = json_encode($vote_details[1]);
                        $log['message'] = "Succes";
                        insert_result_in_db($log);
                        insert_log_in_db($log);
                        unset($vote_details[1]["q-" . $current_poll_id]['answers']);
                    }
                    unset($vote_details);
                }
            if (!empty($bans_ordonate[$poll['id']]))
                foreach ($bans_ordonate[$poll['id']] as $ban_ordonate) {
                    $ban = $ban_ordonate;
                    $ban['poll_id'] = $current_poll_id;
                    self::insert_ban_in_db($ban);
                }


        }

    }

    public function insert_template_in_databease($template)
    {
        global $wpdb;
        $sql = $wpdb->query($wpdb->prepare("
					INSERT INTO " . $wpdb->yop_poll_templates . "
					SET
					template_author = %d,
					name = %s,
					before_vote_template = %s,
					after_vote_template = %s,
					before_start_date_template = %s,
					after_end_date_template = %s,
					css = %s,
					js = %s,
					date_added = %s,
					last_modified = %s,
					status = %s
					", $template['template_author'], $template['name'], $template['before_vote_template'], $template['after_vote_template'], $template['before_start_date_template'], $template['after_end_date_template'], $template['css'], $template['js'], current_time('mysql'), current_time('mysql'), $template['status']));
        return $GLOBALS['wpdb']->insert_id;
    }

    private static function update_poll_template_in_database($template)
    {
        global $wpdb;
        $sql = $wpdb->query($wpdb->prepare("
					UPDATE " . $wpdb->yop_poll_templates . "
					SET name = %s,
					before_vote_template = %s,
					after_vote_template = %s,
					before_start_date_template = %s,
					after_end_date_template = %s,
					css = %s,
					js = %s,
					last_modified = %s
					WHERE
					id = %d
					", $template['name'], $template['before_vote_template'], $template['after_vote_template'], $template['before_start_date_template'], $template['after_end_date_template'], $template['css'], $template['js'], current_time('mysql'), $template['id']));
        return $sql;
    }

    public function yop_poll_get_polls_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM   " . $wpdb->prefix . "yop_polls ORDER BY id ASC
                            "), ARRAY_A);
        return $result;

    }

    public function yop_poll_get_polls_meta_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM " . $wpdb->prefix . "yop_pollmeta ORDER BY yop_poll_id ASC
                            "), ARRAY_A);
        return $result;

    }

    public function yop_poll_get_answers_meta_from_db()
    {
        global $wpdb;

        $result = $wpdb->get_results(("
                            SELECT *
                            FROM " . $wpdb->prefix . "yop_poll_answermeta
                            "), ARRAY_A);
        return $result;

    }

    public function yop_poll_get_templates_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_templates
                            "), ARRAY_A);
        return $result;
    }

    public function yop_poll_get_templates_new_version_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM  " . $wpdb->yop_poll_templates
        ), ARRAY_A);
        return $result;
    }

    public function yop_poll_get_custom_fields_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM " . $wpdb->prefix . "yop_poll_custom_fields ORDER BY poll_id ASC
                            "), ARRAY_A);
        return $result;
    }

    public function yop_poll_get_custom_fields_votes_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_votes_custom_fields
                            "), ARRAY_A);
        return $result;
    }

    public function yop_poll_get_bans_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM   " . $wpdb->prefix . "yop_poll_bans ORDER BY poll_id ASC
                            "), ARRAY_A);
        return $result;
    }

    public function yop_poll_get_answers_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_answers ORDER BY poll_id ASC
                            "), ARRAY_A);
        return $result;
    }

    public function get_answers_order_by_votes($poll_id, $dir)
    {
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare("
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_answers WHERE poll_id=%d  ORDER BY votes $dir
                            ", $poll_id), ARRAY_A);
        return $result;
    }

    public function get_answers_order_by_alph($poll_id, $dir)
    {
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare("
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop_poll_answers WHERE poll_id=%d  ORDER BY answer $dir
                            ", $poll_id), ARRAY_A);
        return $result;
    }

    public function yop_poll_get_logs_from_db()
    {
        global $wpdb;
        $result = $wpdb->get_results(("
                            SELECT *
                            FROM " . $wpdb->prefix . "yop_poll_logs
                            "), ARRAY_A);
        return $result;
    }

    private static function insert_ban_in_db($ban)
    {
        global $wpdb;
        $sql = $wpdb->query($wpdb->prepare("
	                INSERT INTO $wpdb->yop_poll_bans
                              ( poll_id,type,value,period ,unit)
		  	                    VALUES(%d,%s,%s,%d,%s)
	                        ", $ban['poll_id'], $ban['type'], $ban['value'], intval($ban['period']), $ban['unit']));
        return $wpdb->get_results($sql);
    }

    private function save_poll_order($poll, $poll_order)
    {
        $poll_archive_order = get_option('yop_poll_archive_order', array());
        if ($poll_archive_order == "") {
            $poll_archive_order = array();
        }
        if (trim($poll_order) <= 0) {
            $poll_order = 1;
        }
        $key = array_search($poll, $poll_archive_order);
        if ($key !== false) {
            unset($poll_archive_order[$key]);
        }
        if ($poll_order > count($poll_archive_order)) {
            array_push($poll_archive_order, $poll);
        } else {
            array_splice($poll_archive_order, trim($poll_order) - 1, 0, array($poll));
        }
        update_option('yop_poll_archive_order', $poll_archive_order);
    }

}
