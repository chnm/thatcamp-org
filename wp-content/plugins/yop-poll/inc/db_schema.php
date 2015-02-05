<?php
	class Yop_Poll_DbSchema
	{
	/**
	* creating all poll tables
	* @static
	*
	*/
	public static function create_poll_database_tables() {
		global $wpdb;
		global $current_user;

		Yop_Poll_DbSchema::create_polls_table();
		Yop_Poll_DbSchema::create_polls_templates_table();
		Yop_Poll_DbSchema::create_poll_answers_table();
		Yop_Poll_DbSchema::create_poll_custom_fields_table();
		Yop_Poll_DbSchema::create_poll_meta_table();
		Yop_Poll_DbSchema::create_poll_answer_meta_table();
		Yop_Poll_DbSchema::create_poll_logs_table();
		Yop_Poll_DbSchema::create_poll_voters_table();
		Yop_Poll_DbSchema::create_poll_bans_table();
		Yop_Poll_DbSchema::create_poll_votes_custom_fields_table();
		Yop_Poll_DbSchema::create_poll_facebook_users_table();

		$optin_box_modal_options	= array(
			'show'					=> 'yes',
			'last_show_date'		=> Yop_Poll_Model::get_mysql_curent_date(),
			'modal_had_submit'		=> 'no',
			'sidebar_had_submit'	=> 'no',
		);

		$pro_options	= array(
			'pro_key'				=> '',
			'pro_api_key'			=> '',
			'pro_api_server_url'	=> 'http://www.yop-poll.com/pro',
			'pro_user'				=> 'no'
		);

		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$default_options = array(
			'poll_name_html_tags'						=> 'yes',
			'poll_question_html_tags'					=> 'yes',
			'poll_answer_html_tags'						=> 'yes',
			'poll_custom_field_html_tags'				=> 'yes',
			'create_poll_page'							=> 'yes',
			'auto_generate_poll_page'                   => 'no',
			'has_auto_generate_poll_page'               => 'no',
			'poll_page_url'								=> '',
			'allow_other_answers'						=> 'yes',
			'other_answers_label'						=> 'Other',
			'display_other_answers_values'				=> 'no',
			'allow_multiple_answers'					=> 'no',
			'allow_multiple_answers_number'				=> 1,
			'allow_multiple_answers_min_number'			=> 1,
			'display_answers'							=> 'vertical',
			'display_answers_tabulated_cols'			=> 1,
			'display_results'							=> 'vertical',
			'display_results_tabulated_cols'			=> 1,
			'is_default_answer'							=> 'no',
			'use_template_bar'							=> 'yes',
			'bar_background'							=> 'FBD55E',
			'bar_height'								=> 10,
			'bar_border_color'							=> 'EDB918',
			'bar_border_width'							=> 1,
			'bar_border_style'							=> 'solid',
			'sorting_answers'							=> 'exact',
			'sorting_answers_direction'					=> 'asc',
			'sorting_results'							=> 'exact',
			'sorting_results_direction'					=> 'asc',
			'start_date'								=> Yop_Poll_Model::get_mysql_curent_date(),
			'end_date'									=> '9999-12-31 23:59:59',
			'never_expire'								=> 'yes',
			'view_results'								=> 'after',
			'answer_result_label'						=> '- %POLL-ANSWER-RESULT-PERCENTAGES% ( %POLL-ANSWER-RESULT-VOTES% )',
			'view_results_type'							=> 'votes-number',
			'view_results_start_date'					=> '',
			'view_results_link'							=> 'yes',
			'view_back_to_vote_link'					=> 'yes',
			'singular_answer_result_votes_number_label'	=> 'vote',
			'plural_answer_result_votes_number_label'	=> 'votes',
			'vote_button_label'							=> 'Vote',
			'view_results_link_label'					=> 'View Results',
			'view_back_to_vote_link_label'				=> 'Back To Vote',
			'view_total_votes_label'					=> 'Total Votes %POLL-TOTAL-VOTES%',
			'view_total_answers_label'					=> 'Total Answers %POLL-TOTAL-ANSWERS%',
			'view_total_voters_label'					=> 'Total Answers %POLL-TOTAL-VOTERS%',
			'view_total_answers'						=> 'yes',
			'view_total_voters'							=> 'yes',
			'view_total_votes'							=> 'yes',
			'view_poll_archive_link'					=> 'yes',
			'view_poll_archive_link_label'				=> 'View Poll Archive',
			'poll_archive_url'							=> home_url().'/yop-poll-archive',
			'vote_permisions'							=> 'guest-registered',
			'vote_permisions_facebook'					=> 'no',
			'vote_permisions_facebook_label'			=> 'Vote as Facebook User',
			'vote_permisions_wordpress'					=> 'no',
			'vote_permisions_wordpress_label'			=> 'Vote as Wordpress User',
			'vote_permisions_anonymous'					=> 'no',
			'vote_permisions_anonymous_label'			=> 'Vote as Anonymous User',
			'blocking_voters'							=> 'dont-block',
			'blocking_voters_interval_value'			=> 30,
			'blocking_voters_interval_unit'				=> 'days',
			'template'									=> '1',
			'widget_template'							=> '1',
			'template_width'							=> '200px',
			'widget_template_width'						=> '200px',
			'show_in_archive'							=> 'yes',
			'archive_order'								=> '1',
			'archive_polls_per_page'					=> '5',
			'percentages_decimals'						=> '0',
			'use_default_loading_image'					=> 'yes',
			'loading_image_url'							=> '',
			'use_captcha'								=> 'no',
			'share_after_vote'							=> 'no',
			'share_picture'								=> '',
			'share_name'								=> '',
			'share_caption'								=> '',
			'share_description'							=> '',
			'redirect_after_vote'						=> 'no',
			'redirect_after_vote_url'					=> '',
			'limit_number_of_votes_per_user'			=> 'no',
			'number_of_votes_per_user'					=> 1,
			'message_after_vote'						=> 'Thank you for your vote!',
			'start_scheduler'							=> 'no',
			'schedule_reset_poll_stats'					=> 'no',
			'schedule_reset_poll_date'					=> current_time( 'timestamp' ),
			'schedule_reset_poll_recurring_value'		=> 9999,
			'schedule_reset_poll_recurring_unit'		=> 'day',
			'view_results_permissions'					=> 'guest-registered',
			'date_format'								=> 'd/m/Y H:i:s',
			'add_other_answers_to_default_answers'		=> 'no',
			'send_email_notifications'					=> 'no',
			'email_notifications_from_name'				=> 'Yop Poll',
			'email_notifications_from_email'			=> 'yop-poll@' . $sitename,
			'email_notifications_recipients'			=> '',
			'email_notifications_subject'				=> 'New Vote',
			'email_notifications_body'					=> '<p>A new vote was registered on [VOTE_DATE] for [POLL_NAME]</p>
			<p>Vote Details:</p>
			<p><b>Question:</b> [QUESTION]</p>
			<p><b>Answers:</b> <br />[ANSWERS]</p>
			<p><b>Custom Fields:</b> <br />[CUSTOM_FIELDS]</p>
			<p><b>Vote ID:</b> <br />[VOTE_ID]</p>',
		);

		if ( ! get_option( "yop_poll_version" ) ) {

			$poll_archive_page	= get_page_by_path('yop-poll-archive', ARRAY_A );
			if ( ! $poll_archive_page ) {
				$_p = array();
				$_p['post_title'] = 'Yop Poll Archive';
				$_p['post_content'] = "[yop_poll_archive]";
				$_p['post_status'] = 'publish';
				$_p['post_type'] = 'page';
				$_p['comment_status'] = 'open';
				$_p['ping_status'] = 'open';
				$_p['post_category'] = array(1); // the default 'Uncategorised'

				$poll_archive_page_id	= wp_insert_post( $_p );

			}
			else {
				$poll_archive_page_id	= $poll_archive_page['ID'];
			}

			$default_options['poll_archive_url']	= get_permalink( $poll_archive_page_id );

			//addind default options
			update_option( 'yop_poll_options', $default_options );
			update_option( 'yop_poll_optin_box_modal_options', $optin_box_modal_options );
			update_option( 'yop_poll_pro_options', $pro_options );

			wp_get_current_user();
			//adding default poll
			$poll_author	= $current_user->ID > 0 ? $current_user->ID : 0;
			$wpdb->query(
				"
				INSERT INTO `". $wpdb->yop_polls . "` (`poll_author`, `name`, `question`, `start_date`, `end_date`, `total_votes`, `total_answers`, `status`, `date_added`, `last_modified`, `show_in_archive`) VALUES
				(". $poll_author .", 'Default Yop Poll', 'How is my plugin?', '".current_time( 'mysql' )."', '9999-12-31 23:59:59', 0, 0, 'open', '".current_time( 'mysql' )."', '".current_time( 'mysql' )."', '".$default_options['show_in_archive']."')
				"
			);
			$poll_id = $wpdb->insert_id;
			$wpdb->query(
				"
				INSERT INTO `". $wpdb->yop_poll_answers . "` (`poll_id`, `answer`, `type`, `votes`, `status`) VALUES
				(" . $poll_id . ", 'Good', 'default', 0, 'active'),
				(" . $poll_id . ", 'Very Good', 'default', 0, 'active'),
				(" . $poll_id . ", 'Bad', 'default', 0, 'active'),
				(" . $poll_id . ", 'Other', 'other', 0, 'active')
				"
			);

			$templates	= array( 'White', 'Grey', 'Dark', 'Blue v1', 'Blue v2', 'Blue v3', 'Red v1', 'Red v2', 'Red v3', 'Green v1', 'Green v2', 'Green v3', 'Orange v1', 'Orange v2', 'Orange v3' );
			foreach( $templates as $template ) {
				$is_template_in_database	= self::is_template_in_database( $template );
				if ( $is_template_in_database )
					self::add_update_templates_in_database( 'update', $template );
				else
					self::add_update_templates_in_database( 'add', $template );
			}

			update_option( "yop_poll_first_install_date", Yop_Poll_Model::get_mysql_curent_date() );
			update_option( "yop_poll_admin_notices_donate", 'yes' );
		}

		update_option( "yop_poll_version", $wpdb->yop_poll_version );
	}

	public static function is_template_in_database( $template_name ) {
		global $wpdb;
		$is_template = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM ". $wpdb->yop_poll_templates . " WHERE name = %s  LIMIT 0, 1", $template_name ) );
		return $is_template;
	}

	public static function add_update_templates_in_database( $action = 'add', $template_name ) {
		global $wpdb;
		global $current_user;
		wp_get_current_user();
		if ( 'add' == $action ) {
			$sql	= "INSERT INTO `" . $wpdb->yop_poll_templates . "` SET ";
			$sql	.= $wpdb ->prepare( "`name`				= %s, ", $template_name );
			$sql	.= $wpdb ->prepare( "`template_author`	= %d, ", ( $current_user->ID > 0 ) ? $current_user->ID : 0 );
		}
		else
			$sql	= "UPDATE `" . $wpdb->yop_poll_templates . "` SET ";

		$before_vote_template           = <<<NOWDOC
<div id="yop-poll-name-%POLL-ID%" class="yop-poll-name">%POLL-NAME%</div>
	<div id="yop-poll-question-%POLL-ID%" class="yop-poll-question">%POLL-QUESTION%</div>
	<div id="yop-poll-answers-%POLL-ID%" class="yop-poll-answers">
	<ul>
		[ANSWER_CONTAINER]
		<li class="yop-poll-li-answer-%POLL-ID%">
			%POLL-ANSWER-CHECK-INPUT% 
			%POLL-ANSWER-LABEL%
			<span class="yop-poll-results-text-%POLL-ID%">%POLL-ANSWER-RESULT-LABEL%</span>
			%POLL-ANSWER-RESULT-BAR%
		</li>
		[/ANSWER_CONTAINER]       
		[OTHER_ANSWER_CONTAINER]
		<li class="yop-poll-li-answer-%POLL-ID%">
			%POLL-OTHER-ANSWER-CHECK-INPUT% 
			%POLL-OTHER-ANSWER-LABEL% 
			<span class="yop-poll-results-text-%POLL-ID%">%POLL-ANSWER-RESULT-LABEL%</span>
			%POLL-OTHER-ANSWER-TEXT-INPUT% 
			%POLL-ANSWER-RESULT-BAR%
		</li>
		[/OTHER_ANSWER_CONTAINER]
	</ul>
</div>
<div id="yop-poll-custom-%POLL-ID%">
	<ul>
		[CUSTOM_FIELD_CONTAINER]
		<li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>
		[/CUSTOM_FIELD_CONTAINER]
		</ul>
</div>    
[CAPTCHA_CONTAINER]
<div id="yop-poll-captcha-%POLL-ID%">
	<div class="yop-poll-captcha-image-div" id="yop-poll-captcha-image-div-%POLL-ID%">
		%CAPTCHA-IMAGE%       
		<div class="yop-poll-captcha-helpers-div" id="yop-poll-captcha-helpers-div-%POLL-ID%">
			%RELOAD-CAPTCHA-IMAGE%
		</div>
		<div class="yop_poll_clear"></div>
	</div>
	%CAPTCHA-LABEL%
	<div class="yop-poll-captcha-input-div" id="yop-poll-captcha-input-div-%POLL-ID%">%CAPTCHA-INPUT%</div>
</div>
[/CAPTCHA_CONTAINER]
<div id="yop-poll-vote-%POLL-ID%" class="yop-poll-footer">
	<div>%POLL-VOTE-BUTTON%</div>
	<div id="yop-poll-results-%POLL-ID%">%POLL-VIEW-RESULT-LINK%</div>
	<div>%POLL-TOTAL-ANSWERS%</div>   <div>%POLL-TOTAL-VOTES%</div>
</div>
NOWDOC;
		$before_vote_template           = addslashes($before_vote_template);

		$after_vote_template            = <<<NOWDOC
<div id="yop-poll-name-%POLL-ID%" class="yop-poll-name">%POLL-NAME%</div>
<div id="yop-poll-question-%POLL-ID%" class="yop-poll-question">%POLL-QUESTION%</div>
<div id="yop-poll-answers-%POLL-ID%" class="yop-poll-answers">
	<ul>
		[ANSWER_RESULT_CONTAINER]
		<li class="yop-poll-li-result-%POLL-ID%">
			<label class="pds-feedback-label-%POLL-ID%">
				<span class="pds-answer-text-%POLL-ID%">%POLL-ANSWER-LABEL%</span>
				<span class="pds-feedback-result-%POLL-ID%">
					<span class="pds-feedback-per-%POLL-ID%"> %POLL-ANSWER-RESULT-LABEL%</span>
				</span>
			</label>
			<div>
			 %POLL-ANSWER-RESULT-BAR%
		   </div>
		</li>
		[/ANSWER_RESULT_CONTAINER]
	</ul>
</div>
<div id="yop-poll-vote-%POLL-ID%" class="yop-poll-footer">
	<div>%POLL-TOTAL-ANSWERS%</div>
	<div>%POLL-TOTAL-VOTES%</div>
	<div id="yop-poll-back-%POLL-ID%">%POLL-BACK-TO-VOTE-LINK%</div>
</div>
NOWDOC;
		$after_vote_template            = addslashes($after_vote_template);

		$before_start_date_template     = <<<NOWDOC
This poll is about to<br>
start at %POLL-START-DATE%<br>
and finish at %POLL-END-DATE%<br>
NOWDOC;
		$before_start_date_template     = addslashes($before_start_date_template);

		$after_end_date_template        =  <<<NOWDOC
This poll is closed!
Poll activity: <br>
start_date %POLL-START-DATE%<br>
end_date %POLL-END-DATE%<br>

Poll Results:
<div id="yop-poll-question-%POLL-ID%" class="yop-poll-question">%POLL-QUESTION%</div>
<div id="yop-poll-answers-1" class="yop-poll-answers">
	<ul>
			[ANSWER_RESULT_CONTAINER]
					<li class="yop-poll-li-result-%POLL-ID%">
						<label class="pds-feedback-label">
							<span class="pds-answer-text">%POLL-ANSWER-LABEL%</span>
							<span class="pds-feedback-result">
								<span class="pds-feedback-per"> %POLL-ANSWER-RESULT-LABEL%</span>
							</span>
						</label>
						<span class="pds-clear" style="display: block;clear: both;height:1px;line-height:1px;"> </span>
						%POLL-ANSWER-RESULT-BAR%
					</li>
			[/ANSWER_RESULT_CONTAINER]
	</ul>
</div>
%POLL-VOTE-BUTTON%
NOWDOC;
		$after_end_date_template        = addslashes($after_end_date_template);

		$js                             = <<<NOWDOC
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
		width = parseInt( widest / cols); 
		obj.width(width);    
		obj.css("float", "left");    
	}    
}

function tabulate_answers_%POLL-ID%() {
	equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%"), %ANSWERS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery(\"#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \"), %ANSWERS-TABULATED-COLS%, true );
}

function tabulate_results_%POLL-ID%() {
	equalWidth_%POLL-ID%( jQuery("#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%"), %RESULTS-TABULATED-COLS% );
	//equalWidth_%POLL-ID%( jQuery(\"#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \"), %RESULTS-TABULATED-COLS%, true );
	}

jQuery(document).ready(function(){
	runOnPollStateChange_%POLL-ID%();
});

function runOnPollStateChange_%POLL-ID%() {

};
NOWDOC;
		$js                             = addslashes($js);

		$sql .= "`before_vote_template` = '$before_vote_template',
		`after_vote_template`           = '$after_vote_template',
		`before_start_date_template`    = '$before_start_date_template',
		`after_end_date_template`       = '$after_end_date_template',";

		switch ( $template_name ) {
			case 'White':    {	//White
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n	width:%POLL-WIDTH%;\r\n	background:#fff;\r\n	padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#555; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#555; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;       
			}
			case 'Grey':     {//Grey
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#eee;\r\n    padding:10px;\r\n    color:#000;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#000; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#000; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;     
			}
			case 'Dark':     {	//Dark
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n	width:%POLL-WIDTH%;\r\n	background:#555;\r\n	padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#333333; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;  
			}
			case 'Blue v1':	 {//Blue v1
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#327BD6;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;  
			}
			case 'Blue v2':	 {//Blue v2
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-weight:bold;\r\n	background:#327BD6;\r\n	color:#fff;\r\n	padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;  
			}
			case 'Blue v3':	 {//Blue v3
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #327BD6;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px;  }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;  
			}
			case 'Red v1':   {	//Red v1
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#B70004;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;   
			}
			case 'Red v2':	 {//Red v2
				$sql	.= "`css` = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-weight:bold;\r\n	background:#B70004;\r\n	color:#fff;\r\n	padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;   
			}
			case 'Red v3':	 {//Red v3
				$sql	.= "`css`= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #B70004;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;
			}
			case 'Green v1': {	//Green v1
				$sql	.= "`css`= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#3F8B43;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:00FF00;\r\n}',";
				break;      
			}
			case 'Green v2': {	//Green v2
				$sql	.= "`css`= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-weight:bold;\r\n	background:#3F8B43;\r\n	color:#fff;\r\n	padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;    
			}
			case 'Green v3': {	//Green v3
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #3F8B43;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n} \r\n jQuery(document).ready(function(){  \r\n runOnPollStateChange_%POLL-ID%(); \r\n }); \r\n function runOnPollStateChange_%POLL-ID%() {};',";
				break;   
			}
			case 'Orange v1':{	//Orange v1
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#FB6911;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;     
			}
			case 'Orange v2':{	//Orange v2
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-weight:bold;\r\n	background:#FB6911;\r\n	color:#fff;\r\n	padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;       
			}
			case 'Orange v3':{	//Orange v3
				$sql	.= "`css`							= '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #FB6911;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n	font-size:14px;\r\n	font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n	font-size:14px;\r\n	margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n	font-style:normal;\r\n	margin:0px 0px 10px 0px;\r\n	padding:0px;\r\n	font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n	margin:0px; \r\n	float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n	margin:0px; \r\n	font-style:normal; \r\n	font-weight:normal; \r\n	font-size:12px; \r\n	float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n	padding:0px;\r\n	margin:0px;	\r\n	font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:red;\r\n	text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}',";
				break;      
			}
		}

		$sql .= "`js`                            = '$js',
		`last_modified`                    = '".current_time( 'mysql' )."'";



		if ( 'add' == $action ) {
			$sql	.= ", ";
			if ( 'White' == $template_name )
				$sql	.= "`status`			= 'default', ";
			else
				$sql	.= "`status`			= 'other', ";
			$sql	.= "`date_added`		= '".current_time( 'mysql' )."' ";
		}
		else
			$sql	.= $wpdb->prepare( ' WHERE name = %s', $template_name );
	   
		$wpdb->query( $sql );
	}

	/**
	* Creating polls table
	* @static
	*
	*/
	public static function create_polls_table() {
		global $wpdb;
		$create_poll_table_sql = "CREATE TABLE " . $wpdb->yop_polls . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		poll_author bigint(20) NOT NULL DEFAULT '0',
		name varchar(255) NOT NULL,
		question text NOT NULL,
		start_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		end_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		total_votes int(11) NOT NULL,
		total_answers int(11) NOT NULL,
		status varchar(255) NOT NULL,
		last_modified datetime NOT NULL,
		date_added datetime NOT NULL,
		show_in_archive ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes',
		archive_order INT NOT NULL DEFAULT '1',
		PRIMARY KEY  (id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_table_sql );
	}

	public static function create_polls_templates_table() {
		global $wpdb;
		$create_poll_table_sql = "CREATE TABLE " . $wpdb->yop_poll_templates . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		template_author bigint(20) NOT NULL DEFAULT '0',
		name varchar(255) NOT NULL,
		before_vote_template text NOT NULL,
		after_vote_template text NOT NULL,
		before_start_date_template text NOT NULL,
		after_end_date_template text NOT NULL,
		css text NOT NULL,
		js text NOT NULL,
		status varchar(255) NOT NULL,
		last_modified datetime NOT NULL,
		date_added datetime NOT NULL,
		PRIMARY KEY  (id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_table_sql );
	}

	/**
	* creating polls_answers table
	* @static
	*
	*/
	public static function create_poll_answers_table() {
		global $wpdb;
		$create_poll_answers_table_sql = "CREATE TABLE " .$wpdb->yop_poll_answers . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		poll_id int(11) NOT NULL,
		answer varchar(255) NOT NULL,
		type ENUM( 'default', 'other' ) NOT NULL DEFAULT 'default',
		votes int(11) NOT NULL,
		status varchar(255) NOT NULL,
		PRIMARY KEY  (id),
		KEY poll_id (poll_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_answers_table_sql );
	}

	/**
	* creating polls_custom_fields table
	* @static
	*
	*/
	public static function create_poll_custom_fields_table() {
		global $wpdb;
		$create_poll_answers_table_sql = "CREATE TABLE " . $wpdb->yop_poll_custom_fields . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		poll_id int(11) NOT NULL,
		custom_field varchar(255) NOT NULL,
		required ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
		status varchar(255) NOT NULL,
		PRIMARY KEY  (id),
		KEY poll_id (poll_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_answers_table_sql );
	}

	/**
	* creating poll meta table
	* @static
	*
	*/
	public static function create_poll_meta_table() {
		global $wpdb;
		$create_poll_meta_table_sql = "CREATE TABLE " . $wpdb->yop_pollmeta . " (
		meta_id int(11) NOT NULL AUTO_INCREMENT,
		yop_poll_id int(11) NOT NULL,
		meta_key varchar(255) NOT NULL,
		meta_value longtext NOT NULL,
		PRIMARY KEY  (meta_id),
		KEY yop_poll_id (yop_poll_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_meta_table_sql );
	}

	/**
	* creating poll answer meta table
	* @static
	*
	*/
	public static function create_poll_answer_meta_table() {
		global $wpdb;
		$create_poll_answer_meta_table_sql = "CREATE TABLE " . $wpdb->yop_poll_answermeta . " (
		meta_id int(11) NOT NULL AUTO_INCREMENT,
		yop_poll_answer_id int(11) NOT NULL,
		meta_key varchar(255) NOT NULL,
		meta_value longtext NOT NULL,
		PRIMARY KEY  (meta_id),
		KEY yop_poll_answer_id (yop_poll_answer_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_answer_meta_table_sql );
	}

	/**
	* creating poll logs table
	* @static
	*
	*/
	public static function create_poll_logs_table() {
		global $wpdb;
		$create_poll_logs_table_sql = "CREATE TABLE " . $wpdb->yop_poll_logs . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		poll_id int(11) NOT NULL,
		vote_id varchar(255) NOT NULL,
		answer_id int(11) NOT NULL,
		ip varchar(100) NOT NULL,
		user_id int(11) NOT NULL,
		user_type ENUM( 'facebook', 'wordpress', 'anonymous', 'default' ) NOT NULL DEFAULT 'default',
		http_referer varchar(255) NOT NULL,
		tr_id varchar(255) NOT NULL,
		other_answer_value text NOT NULL,
		host varchar(200) NOT NULL,
		vote_date datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY poll_id (poll_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_logs_table_sql );
	}

	/**
	* creating poll voters table
	* @static
	*
	*/
	public static function create_poll_voters_table() {
		global $wpdb;
		$create_poll_voters_table_sql = "CREATE TABLE " . $wpdb->yop_poll_voters . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		poll_id int(11) NOT NULL,
		user_id int(11) NOT NULL,
		user_type ENUM( 'facebook', 'wordpress', 'anonymous', 'default' ) NOT NULL DEFAULT 'default',
		PRIMARY KEY  (id),
		KEY poll_id (poll_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_voters_table_sql );
	}

	/**
	* creating poll bans table
	* @static
	*
	*/
	public static function create_poll_bans_table() {
		global $wpdb;
		$create_poll_bans_table_sql = "CREATE TABLE " . $wpdb->yop_poll_bans . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		poll_id int(11) NOT NULL,
		type varchar(255) NOT NULL,
		value varchar(255) NOT NULL,
		PRIMARY KEY  (id),
		KEY poll_id (poll_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_bans_table_sql );
	}

	/**
	* creating poll votes custom_fields
	* @static
	*
	*/
	public static function create_poll_votes_custom_fields_table() {
		global $wpdb;
		$create_poll_votes_custom_fields_table_sql = "CREATE TABLE " . $wpdb->yop_poll_votes_custom_fields . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		poll_id int(11) NOT NULL,
		vote_id varchar(255) NOT NULL,
		custom_field_id int(11) NOT NULL,
		user_id int(11) NOT NULL,
		user_type ENUM( 'facebook', 'wordpress', 'anonymous', 'default' ) NOT NULL DEFAULT 'default',
		custom_field_value text NOT NULL,
		tr_id varchar(255) NOT NULL,
		vote_date datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY poll_id (poll_id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_votes_custom_fields_table_sql );
	}

	/**
	* Creating polls table
	* @static
	*
	*/
	public static function create_poll_facebook_users_table() {
		global $wpdb;
		$create_poll_table_sql = "CREATE TABLE " . $wpdb->yop_poll_facebook_users . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		fb_id varchar(255) NOT NULL,
		name varchar(255) NOT NULL,
		first_name varchar(255) NOT NULL,
		last_name varchar(255) NOT NULL,
		username varchar(255) NOT NULL,
		email varchar(255) NOT NULL,
		gender varchar(255) NOT NULL,
		date_added datetime NOT NULL,
		PRIMARY KEY  (id)) CHARSET=".self::getCharset()." ;";

		dbDelta( $create_poll_table_sql );
	}

	public static function getCharset() {
		if ( defined( 'DB_CHARSET' ) )
			return DB_CHARSET;
		else
			return 'utf8';
	}
}