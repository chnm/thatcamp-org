<?php

    class Yop_Poll_DbSchema {

        public static function install_database() {

            self::create_poll_database_tables();

            self::add_defaults_to_database();
        }

        public static function create_poll_database_tables() {

            self::create_polls_table();

            self::create_poll_logs_table();

            self::create_poll_meta_table();

            self::create_poll_questions_table();

            self::create_poll_question_meta_table();

            self::create_poll_answers_table();

            self::create_poll_votes_custom_fields_table();

            self::create_poll_answer_meta_table();

            self::create_poll_bans_table();

            self::create_poll_results_table();

            self:: create_polls_templates_table();

            self::create_poll_custom_fields_table();



        }

        public static function add_defaults_to_database() {


            //Add default templates
            $version = get_option( "yop_poll_version" );

            if(!isset($version)||version_compare($version, YOP_POLL_VERSION, '<')) {
            $templates = array(
                'White',
                'Grey',
                'Dark',
                'Blue v1',
                'Blue v2',
                'Blue v3',
                'Red v1',
                'Red v2',
                'Red v3',
                'Green v1',
                'Green v2',
                'Green v3',
                'Orange v1',
                'Orange v2',
                'Orange v3'

            );
            foreach( $templates as $template ) {
                $is_template_in_database = self::is_template_in_database( $template );
                if( $is_template_in_database ) {
                    self::add_update_templates_in_database( 'update', $template );
                }
                else {
                    self::add_update_templates_in_database( 'add', $template );
                }
            }


            update_option( 'yop_poll_options', yop_poll_default_options() );
                update_option( "yop_poll_version", YOP_POLL_VERSION );
            }

        }

        public static function is_template_in_database( $template_name ) {
            global $wpdb;
            $is_template = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM " . $wpdb->yop_poll_templates . " WHERE name = %s  LIMIT 0, 1", $template_name ) );
            return $is_template;
        }

        public static function delete_database_tables() {

            $GLOBALS['wpdb']->query(

                            "DROP TABLE

				`{$GLOBALS['wpdb']->prefix}yop_polls`,

				`{$GLOBALS['wpdb']->prefix}yop_pollmeta`,

				`{$GLOBALS['wpdb']->prefix}yop_poll_questions`,

				`{$GLOBALS['wpdb']->prefix}yop_poll_questionmeta`,

				`{$GLOBALS['wpdb']->prefix}yop_poll_logs`,

				`{$GLOBALS['wpdb']->prefix}yop_poll_answers`,

				`{$GLOBALS['wpdb']->prefix}yop_poll_answermeta`,

				`{$GLOBALS['wpdb']->prefix}yop_poll_custom_fields`"

            );

        }
        public static function delete_database_tables_2() {

            $delete=$GLOBALS['wpdb']->query(

                "DROP TABLE

				`{$GLOBALS['wpdb']->prefix}yop2_polls`,

				`{$GLOBALS['wpdb']->prefix}yop2_pollmeta`,

				`{$GLOBALS['wpdb']->prefix}yop2_poll_questions`,

				`{$GLOBALS['wpdb']->prefix}yop2_poll_questionmeta`,

				`{$GLOBALS['wpdb']->prefix}yop2_poll_logs`,

				`{$GLOBALS['wpdb']->prefix}yop2_poll_answers`,

				`{$GLOBALS['wpdb']->prefix}yop2_poll_answermeta`,

				`{$GLOBALS['wpdb']->prefix}yop2_poll_custom_fields`,
				`{$GLOBALS['wpdb']->prefix}yop2_poll_votes_custom_fields`,

				`{$GLOBALS['wpdb']->prefix}yop2_poll_results`,
				`{$GLOBALS['wpdb']->prefix}yop2_poll_templates`,
				`{$GLOBALS['wpdb']->prefix}yop2_poll_bans`"

            );


        }

        /**
         * Creating polls table
         *
         * @static
         *

         */
        public static function create_polls_table() {
            $optin_box_modal_options = get_option( 'yop_poll_optin_box_modal_options_yop' );
            if ( !isset($optin_box_modal_options)||$optin_box_modal_options['show']=="" ){
            $optin_box_modal_options	= array(
                'modal_had_submit'		=> 'no',
                'sidebar_had_submit'	=> 'no',
                'show'					=> 'yes',
            );
            update_option( 'yop_poll_optin_box_modal_options_yop', $optin_box_modal_options );
            }

            $create_poll_table_sql = "CREATE TABLE " . $GLOBALS['wpdb']->yop_polls . " (

			ID int(11) NOT NULL AUTO_INCREMENT,

			poll_title text NOT NULL,

			poll_name varchar(255) NOT NULL,

			poll_author bigint(20) NOT NULL DEFAULT '0',

			poll_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			poll_status varchar(20) NOT NULL,

			poll_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			poll_type varchar(20) NOT NULL,

			poll_start_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			poll_end_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			poll_total_votes int(11) NOT NULL,

			PRIMARY KEY  (ID)) CHARSET=" . self::getCharset() . " ;";


            dbDelta( $create_poll_table_sql );

        }

        /**
         * Creating polls questions table
         *
         * @static
         *

         */
        public static function create_poll_bans_table() {

            $create_poll_bans_table_sql = "CREATE TABLE " . $GLOBALS['wpdb']->yop_poll_bans . " (
			id int(11) NOT NULL AUTO_INCREMENT,
			poll_id int(11) NOT NULL,
			type varchar(255) NOT NULL,
			value varchar(255) NOT NULL,
			period int(11) NOT NULL,
			unit ENUM('hours','days','weeks','months'),
			PRIMARY KEY  (id),
			KEY poll_id (poll_id)) CHARSET=" . self::getCharset() . " ;";

            dbDelta( $create_poll_bans_table_sql );
        }

        public static function create_poll_questions_table() {

            $create_poll_table_sql = "CREATE TABLE " . $GLOBALS['wpdb']->yop_poll_questions . " (

			ID int(11) NOT NULL AUTO_INCREMENT,

			poll_id int(11) NOT NULL,

			question text NOT NULL,

			type text NOT NULL,

			question_author bigint(20) NOT NULL DEFAULT '0',

			question_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			question_status varchar(20) NOT NULL,

			question_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			poll_order INT NOT NULL DEFAULT '0',

			PRIMARY KEY  (ID),

			KEY poll_id (poll_id)) CHARSET=" . self::getCharset() . " ;";


            dbDelta( $create_poll_table_sql );

        }

        /**
         * Creating polls answers table
         *
         * @static
         *

         */
        public static function create_poll_answers_table() {

            $create_poll_table_sql = "CREATE TABLE " . $GLOBALS['wpdb']->yop_poll_answers . " (

			ID int(11) NOT NULL AUTO_INCREMENT,

			poll_id int(11) NOT NULL,

			question_id int(11) NOT NULL,

			answer text NOT NULL,

	    	description varchar(255) DEFAULT '',

			type varchar(255) NOT NULL DEFAULT 'default',

			answer_author bigint(20) NOT NULL DEFAULT '0',

			answer_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			answer_status varchar(20) NOT NULL,

			answer_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',

			question_order INT NOT NULL DEFAULT '0',

			votes INT( 4 ) NOT NULL DEFAULT 0,

			PRIMARY KEY  (ID),

			KEY poll_id (poll_id),

			KEY question_id (question_id)) CHARSET=" . self::getCharset() . " ;";


            dbDelta( $create_poll_table_sql );

        }

        /**
         * creating poll meta table
         *
         * @static
         *

         */

        public static function create_poll_meta_table() {

            $create_poll_meta_table_sql = "CREATE TABLE " . $GLOBALS['wpdb']->yop_pollmeta . " (

			meta_id int(11) NOT NULL AUTO_INCREMENT,

			yop_poll_id int(11) NOT NULL,

			meta_key varchar(255) NOT NULL,

			meta_value longtext NOT NULL,

			PRIMARY KEY  (meta_id),

			KEY yop_poll_id (yop_poll_id)) CHARSET=" . self::getCharset() . " ;";


            dbDelta( $create_poll_meta_table_sql );

        }

        /**
         * creating polls_custom_fields table
         *
         * @static
         *
         */
        public static function create_poll_custom_fields_table() {
            global $wpdb;
            $create_poll_answers_table_sql = "CREATE TABLE " . $wpdb->yop_poll_custom_fields . " (
			ID int(11) NOT NULL AUTO_INCREMENT,
			poll_id int(11) NOT NULL,
			question_id int(11) NOT NULL,
			custom_field varchar(255) NOT NULL,
			required ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
			status varchar(255) NOT NULL,
			PRIMARY KEY  (id),
			KEY poll_id (poll_id)) CHARSET=" . self::getCharset() . " ;";

            dbDelta( $create_poll_answers_table_sql );
        }

        /**
         * creating poll question meta table
         *
         * @static
         *

         */

        public static function create_poll_question_meta_table() {

            $create_poll_question_meta_table_sql = "CREATE TABLE " . $GLOBALS['wpdb']->yop_poll_questionmeta . " (

			meta_id int(11) NOT NULL AUTO_INCREMENT,

			yop_poll_question_id int(11) NOT NULL,

			meta_key varchar(255) NOT NULL,

			meta_value longtext NOT NULL,

			PRIMARY KEY  (meta_id),

			KEY yop_poll_question_id (yop_poll_question_id)) CHARSET=" . self::getCharset() . " ;";


            dbDelta( $create_poll_question_meta_table_sql );

        }

        public static function create_poll_votes_custom_fields_table() {
            global $wpdb;
            $create_poll_votes_custom_fields_table_sql = "CREATE TABLE " . $wpdb->yop_poll_votes_custom_fields . " (
			ID int(11) NOT NULL AUTO_INCREMENT,
			poll_id int(11) NOT NULL,
			question_id int(11) NOT NULL,
			vote_id varchar(255) NOT NULL,
			custom_field_id int(11) NOT NULL,
			user_id int(11) NOT NULL,
			user_type ENUM( 'facebook', 'wordpress', 'anonymous', 'default' ) NOT NULL DEFAULT 'default',
			custom_field_value text NOT NULL,
			tr_id varchar(255) NOT NULL,
			vote_date datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY poll_id (poll_id)) CHARSET=" . self::getCharset() . " ;";

            dbDelta( $create_poll_votes_custom_fields_table_sql );
        }

        public static function create_poll_logs_table() {
            global $wpdb;

            $create_poll_logs_table_sql = "CREATE TABLE " . $wpdb->yop_poll_logs . " (
			ID 				int(11) NOT NULL AUTO_INCREMENT,
			poll_id 		int(11) NOT NULL,
			vote_id 		varchar(255) NOT NULL,
			ip 				varchar(100) NOT NULL,
			user_id 		varchar(25) DEFAULT NULL,
			user_type 		ENUM('facebook','wordpress','anonymous','google','default' ) NOT NULL DEFAULT 'default',
			tr_id 			varchar(255) DEFAULT NULL,
			vote_details 	longtext DEFAULT NULL,
			user_details 	longtext DEFAULT NULL,
			vote_date 		datetime NOT NULL,
			message 		text DEFAULT NULL,
			PRIMARY KEY  (ID),
			KEY poll_id (poll_id)) CHARSET=" . self::getCharset() . " ;";
            dbDelta( $create_poll_logs_table_sql );

        }

        public static function create_poll_results_table() {
            global $wpdb;
            $create_poll_results_table_sql = "CREATE TABLE " . $wpdb->yop_poll_results . " (
			ID 				int(11) NOT NULL AUTO_INCREMENT,
			poll_id 		int(11) NOT NULL,
			vote_id 		varchar(256) NOT NULL,
			ip 				varchar(100) NOT NULL,
			country 	    varchar(100) NOT NULL,
			user_id 		varchar(25) DEFAULT NULL,
			user_type 		ENUM('facebook','google','admin','wordpress','anonymous','default' ) NOT NULL DEFAULT 'default',
			tr_id 			varchar(256) DEFAULT NULL,
			result_details 	longtext DEFAULT NULL,
			user_details 	longtext DEFAULT NULL,
			vote_date 		datetime NOT NULL,
			PRIMARY KEY  (ID),
			KEY poll_id (poll_id)) CHARSET=" . self::getCharset() . " ;";
            dbDelta( $create_poll_results_table_sql );
        }

        /**
         * creating poll answer meta table
         *
         * @static
         *

         */
        public static function create_poll_answer_meta_table() {

            $create_poll_answer_meta_table_sql = "CREATE TABLE " . $GLOBALS['wpdb']->yop_poll_answermeta . " (

			meta_id int(11) NOT NULL AUTO_INCREMENT,

			yop_poll_answer_id int(11) NOT NULL,

			meta_key varchar(255) NOT NULL,

			meta_value longtext NOT NULL,

			PRIMARY KEY  (meta_id),

			KEY yop_poll_answer_id (yop_poll_answer_id)) CHARSET=" . self::getCharset() . " ;";


            dbDelta( $create_poll_answer_meta_table_sql );

        }

        /**
         * creating poll templates table
         *
         * @static
         *

         */
        public static function create_polls_templates_table() {
            global $wpdb;
            $create_poll_table_sql = "CREATE TABLE " . $wpdb->yop_poll_templates . " (
			id int(11) NOT NULL AUTO_INCREMENT,
			template_author bigint(20) NOT NULL DEFAULT '0',
			name varchar(255) NOT NULL,
			before_vote_template text NOT NULL,
			after_vote_template text NOT NULL,
			after_vote_template_chart text NOT NULL,
			before_start_date_template text NOT NULL,
			after_end_date_template text NOT NULL,
			css text NOT NULL,
			js text NOT NULL,
			status varchar(255) NOT NULL,
			last_modified datetime NOT NULL,
			date_added datetime NOT NULL,
			PRIMARY KEY  (id)) CHARSET=" . self::getCharset() . " ;";

            dbDelta( $create_poll_table_sql );
        }
        /**
         * creating poll poll_votes cutom field table
         *
         * @static
         *

         */


        /**
         * add default templates in database
         */
        public static function add_update_templates_in_database( $action = 'add', $template_name ) {
            global $wpdb;
            global $current_user;
            wp_get_current_user();
            if( 'add' == $action ) {
                $sql = "INSERT INTO `" . $wpdb->yop_poll_templates . "` SET ";
                $sql .= $wpdb->prepare( "`name`				= %s, ", $template_name );
                $sql .= $wpdb->prepare( "`template_author`	= %d, ", ( $current_user->ID > 0 ) ? $current_user->ID : 0 );
            }
            else {
                $sql = "UPDATE `" . $wpdb->yop_poll_templates . "` SET ";
            }

            $before_vote_template = <<<NOWDOC
<div id = "yop-poll-questions-container-%POLL-ID%">
	[QUESTION_CONTAINER]
	<div id = "yop-poll-question-container-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-question-container-%POLL-ID%">
		<div id = "yop-poll-question-%POLL-ID%-%QUESTION-ID%"
             class = "yop-poll-question-%POLL-ID%">%POLL-QUESTION%</div>
		<div id = "yop-poll-answers-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-answers-%POLL-ID%">
			<ul>
				[ANSWER_CONTAINER]
				<li id = "yop-poll-li-answer-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-li-answer-%POLL-ID%">
					%POLL-ANSWER-CHECK-INPUT% %POLL-ANSWER-LABEL%
                    [ANSWER_DESCRIPTION_CONTAINER]
                    <span class = "yop-poll-description-%POLL-ID%">%ANSWER-DESCRIPTION%</span>
                    [/ANSWER_DESCRIPTION_CONTAINER]

                    [ANSWER_RESULT_CONTAINER]
					<span class = "yop-poll-results-text-%POLL-ID%-%QUESTION-ID%">%POLL-ANSWER-RESULT-LABEL%</span>
					%POLL-ANSWER-RESULT-BAR%
                    [/ANSWER_RESULT_CONTAINER]
				</li>
				[/ANSWER_CONTAINER]
				[OTHER_ANSWER_CONTAINER]
				<li id = "yop-poll-li-answer-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-li-answer-%POLL-ID%">
					%POLL-OTHER-ANSWER-CHECK-INPUT%
					%POLL-OTHER-ANSWER-LABEL%
					<span id = "yop-poll-results-text-%POLL-ID%-%QUESTION-ID%"
                          class = "yop-poll-results-text-%POLL-ID%">%POLL-ANSWER-RESULT-LABEL%</span>

					%POLL-OTHER-ANSWER-TEXT-INPUT%
					%POLL-ANSWER-RESULT-BAR%
				</li>
				[/OTHER_ANSWER_CONTAINER]
			</ul>
		</div>
		<div id = "yop-poll-customs-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-customs-%POLL-ID%">
			<ul>
				[CUSTOM_FIELD_CONTAINER]
				<li id = "yop-poll-li-custom-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-li-custom-%POLL-ID%">
					%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%
				</li>
				[/CUSTOM_FIELD_CONTAINER]
			</ul>
		</div>
	</div>
	<div class = "yop-poll-clear-%POLL-ID%"></div>
	[/QUESTION_CONTAINER]
</div>
[CAPTCHA_CONTAINER]
<div id = "yop-poll-captcha-%POLL-ID%">
	<div class = "yop-poll-captcha-image-div" id = "yop-poll-captcha-image-div-%POLL-ID%">
		%CAPTCHA-IMAGE%
		<div class = "yop-poll-captcha-helpers-div" id = "yop-poll-captcha-helpers-div-%POLL-ID%">
			%RELOAD-CAPTCHA-IMAGE%
		</div>
		<div class = "yop-poll-clear-%POLL-ID%"></div>
	</div>
	%CAPTCHA-LABEL%
	<div class = "yop-poll-captcha-input-div" id = "yop-poll-captcha-input-div-%POLL-ID%">%CAPTCHA-INPUT%</div>
</div>
[/CAPTCHA_CONTAINER]
<div id = "yop-poll-vote-%POLL-ID%" class = "yop-poll-footer">
	<div>%POLL-VOTE-BUTTON%</div>
	<div id = "yop-poll-results-%POLL-ID%">%POLL-VIEW-RESULT-LINK%</div>
	<div>%POLL-TOTAL-ANSWERS-LABEL%</div>   <div>%POLL-TOTAL-VOTES-LABEL%</div>
</div>
NOWDOC;

            $after_vote_template = <<<NOWDOC
<div id = "yop-poll-questions-container-%POLL-ID%" class = "yop-poll-questions-container-%POLL-ID%">
	[QUESTION_CONTAINER]
	<div id = "yop-poll-question-container-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-question-container-%POLL-ID%">
		<div id = "yop-poll-question-%POLL-ID%-%QUESTION-ID%"
             class = "yop-poll-question-%POLL-ID%">%POLL-QUESTION%</div>
		<div id = "yop-poll-answers-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-answers-%POLL-ID%">
			<ul>
				[ANSWER_RESULT_CONTAINER]
				<li id = "yop-poll-li-result-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-li-result-%POLL-ID%">
					<label id = "pds-feedback-label-%POLL-ID%-%QUESTION-ID%" class = "pds-feedback-label-%POLL-ID%">
                        [ANSWER_DESCRIPTION_CONTAINER]
                        <span class = "yop-poll-description-%POLL-ID%">%ANSWER-DESCRIPTION%</span>
                        [/ANSWER_DESCRIPTION_CONTAINER]
						<span id = "pds-answer-text-%POLL-ID%-%QUESTION-ID%" class = "pds-answer-text-%POLL-ID%">
							%POLL-ANSWER-LABEL%
						</span>
						<span id = "pds-feedback-result-%POLL-ID%-%QUESTION-ID%"
                              class = "pds-feedback-result-%POLL-ID%">
							<span id = "pds-feedback-per-%POLL-ID%-%QUESTION-ID%" class = "pds-feedback-per-%POLL-ID%">
								%POLL-ANSWER-RESULT-LABEL%
							</span>
						</span>
					</label>
                    <div>
                        %POLL-ANSWER-RESULT-BAR%
                    </div>
				</li>
				[/ANSWER_RESULT_CONTAINER]
			</ul>
		</div>
	</div>
	<div class = "yop-poll-clear-%POLL-ID%"></div>
	[/QUESTION_CONTAINER]
</div>
<div id = "yop-poll-vote-%POLL-ID%" class = "yop-poll-footer">
	<div>%POLL-TOTAL-ANSWERS-LABEL%</div>
	<div>%POLL-TOTAL-VOTES-LABEL%</div>
	<div id = "yop-poll-back-%POLL-ID%">%POLL-BACK-TO-VOTE-LINK%</div>
	<div id = "yop-poll-archive-%POLL-ID%">%POLL-VIEW-ARCHIVE-LINK%</div>
	%SHARE-BUTTON%
</div>
NOWDOC;
            $after_vote_template = addslashes( $after_vote_template );

            $after_vote_template_chart = <<<NOWDOC
[QUESTION_CONTAINER]
	<div id = "yop-poll-question-container-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-question-container-%POLL-ID%">
		<div id = "yop-poll-question-%POLL-ID%-%QUESTION-ID%"
             class = "yop-poll-question-%POLL-ID%">%POLL-QUESTION%</div>
		<div id = "yop-poll-answers-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-answers-%POLL-ID%">
                       <div id = "yop-poll-answers-chart-canvas-%POLL-ID%-%QUESTION-ID%" style="text-align:center;" class="yop-poll-answers-container-chart-%POLL-ID% yop-poll-center-chart">
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

            $after_vote_template_chart= addslashes( $after_vote_template_chart );

            $before_start_date_template = <<<NOWDOC
This poll is about to<br>
 start on %POLL-START-DATE%<br>
and will end on %POLL-END-DATE%<br>
NOWDOC;
            $before_start_date_template = addslashes( $before_start_date_template );

            $after_end_date_template = <<<NOWDOC
This poll is closed!
Poll activity: <br>
Start date %POLL-START-DATE%<br>
End date %POLL-END-DATE%<br>

Poll Results:
<div id="yop-poll-questions-container-%POLL-ID%" class="yop-poll-questions-container-%POLL-ID%">
	[QUESTION_CONTAINER]
	<div id = "yop-poll-question-container-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-question-container-%POLL-ID%">
		<div id = "yop-poll-question-%POLL-ID%-%QUESTION-ID%"
             class = "yop-poll-question-%POLL-ID%">%POLL-QUESTION%</div>
		<div id = "yop-poll-answers-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-answers-%POLL-ID%">
			<ul>
				[ANSWER_RESULT_CONTAINER]
				<li id = "yop-poll-li-result-%POLL-ID%-%QUESTION-ID%" class = "yop-poll-li-result-%POLL-ID%">
					<label id = "pds-feedback-label-%POLL-ID%-%QUESTION-ID%" class = "pds-feedback-label-%POLL-ID%">
                        [ANSWER_DESCRIPTION_CONTAINER]
                        <span class = "yop-poll-description-%POLL-ID%">%ANSWER-DESCRIPTION%</span>
                        [/ANSWER_DESCRIPTION_CONTAINER]
						<span id = "pds-answer-text-%POLL-ID%-%QUESTION-ID%" class = "pds-answer-text-%POLL-ID%">
							%POLL-ANSWER-LABEL%
						</span>
						<span id = "pds-feedback-result-%POLL-ID%-%QUESTION-ID%"
                              class = "pds-feedback-result-%POLL-ID%">
							<span id = "pds-feedback-per-%POLL-ID%-%QUESTION-ID%" class = "pds-feedback-per-%POLL-ID%">
								%POLL-ANSWER-RESULT-LABEL%
							</span>
						</span>
					</label>
                    <div>
                        %POLL-ANSWER-RESULT-BAR%
                    </div>
				</li>
				[/ANSWER_RESULT_CONTAINER]
			</ul>
		</div>
	</div>
	<div class = "yop-poll-clear-%POLL-ID%"></div>
	[/QUESTION_CONTAINER]
</div>
NOWDOC;
            $after_end_date_template = addslashes( $after_end_date_template );

            $js = <<<NOWDOC
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
            $js = addslashes( $js );
            $css = "";

            switch( $template_name ) {
                case 'White':
                { //White
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;
}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  color: #555;
  display: inline-block;
  font-size: 14px;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Grey':
                { //Grey
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#eee;
  box-sizing: border-box;
  color: #fff;
  display: inline-block;
  font-size: 14px;
  color:#000;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }







NOWDOC;
                    break;
                }
                case 'Dark':
                { //Dark
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#555;
  box-sizing: border-box;
  color: #fff;
  display: inline-block;
  font-size: 14px;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }








NOWDOC;
                    break;
                }
                case 'Blue v1':
                { //Blue v1
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#327BD6;
  box-sizing: border-box;
  color: #fff;
  display: inline-block;
  font-size: 14px;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Blue v2':
                { //Blue v2
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  background:#327BD6;
  color:#fff;
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Blue v3':
                { //Blue v3
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
  border: 5px solid #327BD6;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Red v1':
                { //Red v1
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#B70004;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:white;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Red v2':
                { //Red v2
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  background:#B70004;
  color:#fff;
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Red v3':
                { //Red v3
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
  border:5px solid #B70004;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Green v1':
                { //Green v1
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#3F8B43;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:white;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Green v2':
                { //Green v2
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  background:#3F8B43;
  color:#fff;
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }

NOWDOC;
                    break;
                }
                case 'Green v3':
                { //Green v3
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
  border:5px solid #3F8B43;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Orange v1':
                { //Orange v1
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#FB6911;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:white;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }

NOWDOC;
                    break;
                }
                case 'Orange v2':
                { //Orange v2
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  background:#FB6911;
  color:#fff;
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {

}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }
NOWDOC;
                    break;
                }
                case 'Orange v3':
                { //Orange v3
                    $css = <<<NOWDOC
/*
  Main Container
  ---------------------------------------------------------------------------
*/
#yop-poll-other-answer-%POLL-ID%-other {
    border:1px solid #000000;
    color:#000000;

}
.yop-poll-customfield-%POLL-ID% {
border:1px solid #000000;
    color:#000000;
}
div#yop-poll-container-%POLL-ID% {
  background:#fff;
  box-sizing: border-box;
  display: inline-block;
  font-size: 14px;
  color:#555;
  padding: 10px;
  width: %POLL-WIDTH%;
  zoom: 1;
  border:5px solid #FB6911;
}
/* A nice little reset */
div.yop-poll-container * {

}

form#yop-poll-form-%POLL-ID% {

}
/*
  Error message
  ..................................
*/
div#yop-poll-container-error-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:red;
  text-transform:lowercase;
  text-align:center;
}
/*
  Success message
  ..................................
*/
div#yop-poll-container-success-%POLL-ID% {
  font-size:.8em;
  font-style:italic;
  color:green;
  text-align:center;
}
/*
  Poll Question
  ---------------------------------------------------------------------------
*/
div#yop-poll-questions-container-%POLL-ID% { margin:.5em; }
div.yop-poll-question-container-%POLL-ID% { margin: 0; }
div.yop-poll-question-%POLL-ID% {
  border: 0 none;
  margin: 0 0 2em;
  padding: .25em;
  text-align: center;
  font-size: 14px;
}
/*
  Poll Options / Answers
  ---------------------------------------------------------------------------
*/
div.yop-poll-answers-%POLL-ID% {}
div.yop-poll-answers-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-answer-%POLL-ID%,
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 2em;

}
div.yop-poll-answers-%POLL-ID% ul li.yop-poll-li-result-%POLL-ID% {padding-bottom: 1em;}
/*
  Label
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label {
  margin:0px;
  font-style:normal;
  font-weight:normal;
  font-size:14px;
  float:none;
  display:inline-block;
}
/* Actual text label of the answer */
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(2) {
}
/*
  Custom answer / Other
  ..................................
*/
div.yop-poll-answers-%POLL-ID%  ul li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) {
  width: 100%;
  overflow: hidden;
}
div#yop-poll-container-%POLL-ID%  li.yop-poll-li-answer-%POLL-ID% label:nth-of-type(3) input[type="text"] {
  width: 94%;
  overflow: hidden;
}
/*
  Custom checkbox and radio button style
  ---------------------------------------------------------------------------
*/


/*
  Custom fields
  ---------------------------------------------------------------------------
*/
div.yop-poll-customs-%POLL-ID% {
  clear: both;

}
div.yop-poll-customs-%POLL-ID% ul {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  width: 100%;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% {
  display: block;
  font-size:14px;
  list-style: none;
}
div.yop-poll-customs-%POLL-ID% ul li.yop-poll-li-custom-%POLL-ID% label {
  display: block;
}
div#yop-poll-container-%POLL-ID% li.yop-poll-li-custom-%POLL-ID% input[type="text"] {
  width: 94%;
}
/*
  Vote Button
  ---------------------------------------------------------------------------
*/
.yop-poll-footer { /*height: 4em;*/ }

.yop-poll-vote-centered-%POLL-ID% { text-align:center; }

#yop-poll-vote-%POLL-ID% div button {

  cursor:pointer;
  display: block;
  font-size:14px;
  margin: 0 auto;
  }


#yop-poll-vote-%POLL-ID% div button.yop_poll_vote_button {}
#yop-poll-vote-%POLL-ID% div button.yop_poll_wordpress_vote_button { margin: 1em auto 0; }
#yop-poll-vote-%POLL-ID% div button.yop_poll_anonymous_vote_button { margin: 1em auto 0; }

/*
  Results
  ---------------------------------------------------------------------------
*/

li.yop-poll-results-%POLL-ID% {
  font-size: 12px;
  font-style: italic;
  font-weight: normal;
  margin-left: 15px;
  color: #38595E;
}
/*
  Style for results that are displayed on columns
  ..................................
*/
li.yop-poll-li-answer-%POLL-ID% {
  float: left;
  display: inline-block;
  width: 100%;
  padding: 1%;
  margin: 1%;
}

#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {
  float: right;
  width: auto;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div#yop-poll-archive-%POLL-ID% a {
  color:#000;
  text-decoration:underline;
  font-size:12px;
}
#yop-poll-vote-%POLL-ID% div {
  float:left;
  width:100%;
}
/*
  Result Bar
  ..................................
*/

.yop-poll-results-bar-%POLL-ID% div { background:#555; width:90%;}


/*
  ---------------------------------------------------------------------------
  EXTRAS
  ---------------------------------------------------------------------------
*/
/*
  CAPTCHA
  ..................................
*/
#yop-poll-captcha-input-div-%POLL-ID% { margin-top:5px; }
#yop-poll-captcha-helpers-div-%POLL-ID% {
  width:30px;
  float:left;
  margin-left:5px;
  height:0px;
}
#yop-poll-captcha-helpers-div-%POLL-ID% img { margin-bottom:2px; }
#yop-poll-captcha-image-div-%POLL-ID% { margin-bottom:5px; }
#yop_poll_captcha_image_%POLL-ID% { float:left; }

.yop-poll-clear-%POLL-ID% { clear:both; }

.yop-poll-name-%POLL-ID% { color:#38595E; font-size: 18px; }


.yop-poll-li-answer-%POLL-ID% label  img     { max-width: 98%; }
.yop-poll-li-result-%POLL-ID% label span img { max-width: 98%; }

.content { position :unset !important; }

div.yop-poll-container br,
div#yop-poll-container-%POLL-ID% br { display: none; }

div#yop_poll_vote_options_div-%POLL-ID% div:nth-last-of-type(1) { display: none; }








NOWDOC;
                    break;
                }
            }

            $css.= "#yop-poll-container-%POLL-ID% img {
    max-width: %POLL-WIDTH%;
}";
            $css = addslashes($css);
            $before_vote_template = addslashes( $before_vote_template );

            $sql .= "`before_vote_template` = '{$before_vote_template}',
			`after_vote_template`           = '{$after_vote_template}',
			`after_vote_template_chart`     = '{$after_vote_template_chart}',
			`before_start_date_template`    = '{$before_start_date_template}',
			`after_end_date_template`       = '{$after_end_date_template}',
			`js`                            = '$js',
			`css`                           = '{$css}',
			`last_modified`                 = '" . current_time( 'mysql' ) . "'";


            if( 'add' == $action ) {
                $sql .= ", ";
                $sql .= "`status` = 'default', ";

                $sql .= "`date_added`		= '" . current_time( 'mysql' ) . "' ";
            }
            else {
                $sql .= $wpdb->prepare( ' WHERE name = %s', $template_name );
            }
            $wpdb->query( $sql );
        }

        public static function getCharset() {
            if( defined( 'DB_CHARSET' ) ) {
                return DB_CHARSET;
            }
            else {
                return 'utf8';
            }

        }
    }