<?php

	class Yop_Poll_Admin extends Yop_Poll_Plugin{
		protected function init() {
			$this->add_action( 'init', 'admin_loader' );
			$this->add_action( 'admin_notices', 'popup_bar' );
			$this->add_action( 'admin_init', 'popup_bar_operations' );
			$this->add_action( 'admin_enqueue_scripts', 'my_yop_poll_button' );
			$this->add_action( 'wpmu_new_blog', 'new_blog', 10, 6 );
			$this->add_action( 'delete_blog', 'delete_blog', 10, 2 );
			register_activation_hook( $this->_config->plugin_file, array( $this, 'yop_poll_activate' ) );
			register_deactivation_hook( $this->_config->plugin_file, array( $this, 'yop_poll_deactivate' ) );
			register_uninstall_hook( $this->_config->plugin_file, 'yop_poll_uninstall' );
			$this->add_action( 'admin_enqueue_scripts', 'load_editor_functions' );
			$this->add_action( 'plugins_loaded', 'db_update' );
		}

		function new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
			global $wpdb;
			if ( !function_exists( 'is_plugin_active_for_network' ) ){
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			if ( is_plugin_active_for_network( 'yop-poll/yop_poll.php' ) ){
				$old_blog = $wpdb->blogid;
				switch_to_blog( $blog_id );
				$wpdb->yop_polls                    = $wpdb->prefix . 'yop_polls';
				$wpdb->yop_poll_answers             = $wpdb->prefix . 'yop_poll_answers';
				$wpdb->yop_poll_templates           = $wpdb->prefix . 'yop_poll_templates';
				$wpdb->yop_poll_custom_fields       = $wpdb->prefix . 'yop_poll_custom_fields';
				$wpdb->yop_pollmeta                 = $wpdb->prefix . 'yop_pollmeta';
				$wpdb->yop_poll_answermeta          = $wpdb->prefix . 'yop_poll_answermeta';
				$wpdb->yop_poll_logs                = $wpdb->prefix . 'yop_poll_logs';
				$wpdb->yop_poll_voters              = $wpdb->prefix . 'yop_poll_voters';
				$wpdb->yop_poll_bans                = $wpdb->prefix . 'yop_poll_bans';
				$wpdb->yop_poll_votes_custom_fields = $wpdb->prefix . 'yop_poll_votes_custom_fields';
				$wpdb->yop_poll_facebook_users      = $wpdb->prefix . 'yop_poll_facebook_users';
				$this->activate( NULL );
				switch_to_blog( $old_blog );
			}
		}

		function delete_blog( $blog_id ) {
			global $wpdb;
			$old_blog = $wpdb->blogid;
			switch_to_blog( $blog_id );
			$wpdb->query( "DROP TABLE `" . $wpdb->prefix . "yop_pollmeta`, `" . $wpdb->prefix . "yop_polls`, `" . $wpdb->prefix . "yop_poll_answermeta`, `" . $wpdb->prefix . "yop_poll_answers`, `" . $wpdb->prefix . "yop_poll_custom_fields`, `" . $wpdb->prefix . "yop_poll_logs`, `" . $wpdb->prefix . "yop_poll_voters`, `" . $wpdb->prefix . "yop_poll_bans`, `" . $wpdb->prefix . "yop_poll_templates`, `" . $wpdb->prefix . "yop_poll_votes_custom_fields`, `" . $wpdb->prefix . "yop_poll_facebook_users`" );
			switch_to_blog( $old_blog );
		}

		function yop_poll_network_propagate( $pfunction, $networkwide ) {
			global $wpdb;

			if ( function_exists( 'is_multisite' ) && is_multisite() ){
				// check if it is a network activation - if so, run the activation function
				// for each blog id
				if ( $networkwide ){
					$old_blog = $wpdb->blogid;
					// Get all blog ids
					$blogids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
					foreach ( $blogids as $blog_id ) {
						switch_to_blog( $blog_id );
						$wpdb->yop_polls                    = $wpdb->prefix . 'yop_polls';
						$wpdb->yop_poll_answers             = $wpdb->prefix . 'yop_poll_answers';
						$wpdb->yop_poll_templates           = $wpdb->prefix . 'yop_poll_templates';
						$wpdb->yop_poll_custom_fields       = $wpdb->prefix . 'yop_poll_custom_fields';
						$wpdb->yop_pollmeta                 = $wpdb->prefix . 'yop_pollmeta';
						$wpdb->yop_poll_answermeta          = $wpdb->prefix . 'yop_poll_answermeta';
						$wpdb->yop_poll_logs                = $wpdb->prefix . 'yop_poll_logs';
						$wpdb->yop_poll_voters              = $wpdb->prefix . 'yop_poll_voters';
						$wpdb->yop_poll_bans                = $wpdb->prefix . 'yop_poll_bans';
						$wpdb->yop_poll_votes_custom_fields = $wpdb->prefix . 'yop_poll_votes_custom_fields';
						$wpdb->yop_poll_facebook_users      = $wpdb->prefix . 'yop_poll_facebook_users';
						call_user_func( array( $this, $pfunction ), $networkwide );
					}
					switch_to_blog( $old_blog );
					return;
				}
			}
			call_user_func( array( $this, $pfunction ), $networkwide );
		}

		function yop_poll_activate( $networkwide ) {
			$this->yop_poll_network_propagate( 'activate', $networkwide );
		}

		function yop_poll_deactivate( $networkwide ) {
			$this->yop_poll_network_propagate( 'deactivate', $networkwide );
		}

		public function db_update() {
			global $wpdb;
			global $current_user;

			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$installed_version = get_option( "yop_poll_version" );

			// update for version 1.5
			if ( version_compare( $installed_version, '1.5', '<=' ) ){
				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['vote_button_label'] ) ){
					$default_options ['vote_button_label'] = 'Vote';
				}
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
				update_option( 'yop_poll_options', $default_options );
			}

			// update for version 1.6
			if ( version_compare( $installed_version, '1.6', '<=' ) ){
				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['display_other_answers_values'] ) ){
					$default_options ['display_other_answers_values'] = 'no';
				}
				if ( !isset ( $default_options ['percentages_decimals'] ) ){
					$default_options ['percentages_decimals'] = '0';
				}
				if ( !isset ( $default_options ['plural_answer_result_votes_number_label'] ) ){
					$default_options ['singular_answer_result_votes_number_label'] = 'vote';
				}
				if ( !isset ( $default_options ['plural_answer_result_votes_number_label'] ) ){
					$default_options ['plural_answer_result_votes_number_label'] = 'votes';
				}
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
				update_option( 'yop_poll_options', $default_options );
			}

			// update for version 2.0
			if ( version_compare( $installed_version, '2.0', '<=' ) ){
				$wpdb->query( 'ALTER TABLE `' . $wpdb->yop_polls . '` CHANGE `total_votes` `total_answers` INT( 11 ) NOT NULL ' );
				$wpdb->query( 'ALTER TABLE `' . $wpdb->yop_polls . '` CHANGE `total_voters` `total_votes` INT( 11 ) NOT NULL ' );
				$wpdb->query( "
					UPDATE " . $wpdb->yop_poll_templates . "
					SET
					before_vote_template = REPLACE( before_vote_template, 'POLL-TOTAL-VOTERS', 'POLL-TOTAL-ANSWERS'),
					after_vote_template = REPLACE( after_vote_template, 'POLL-TOTAL-VOTERS', 'POLL-TOTAL-ANSWERS'),
					before_start_date_template = REPLACE( before_start_date_template, 'POLL-TOTAL-VOTERS', 'POLL-TOTAL-ANSWERS'),
					after_end_date_template = REPLACE( after_end_date_template, 'POLL-TOTAL-VOTERS', 'POLL-TOTAL-ANSWERS'),
					css = REPLACE( css, 'POLL-TOTAL-VOTERS', 'POLL-TOTAL-ANSWERS'),
					js = REPLACE( js, 'POLL-TOTAL-VOTERS', 'POLL-TOTAL-ANSWERS')
				" );
				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['view_total_answers'] ) ){
					$default_options ['view_total_answers'] = $default_options ['view_total_voters'];
				}
				if ( !isset ( $default_options ['view_total_answers_label'] ) ){
					$default_options ['view_total_answers_label'] = 'Total Answers %POLL-TOTAL-ANSWERS%';
				}
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
				update_option( 'yop_poll_options', $default_options );
			}

			// update for version 3.2
			if ( version_compare( $installed_version, '3.2', '<=' ) ){
				$wpdb->query( "
					UPDATE " . $wpdb->yop_poll_templates . "
					SET
					js = REPLACE( js, 'findWidest = false ) {\r\n', 'findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    ')
				" );

				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['auto_generate_poll_page'] ) ){
					$default_options ['auto_generate_poll_page'] = 'no';
				}
				if ( !isset ( $default_options ['has_auto_generate_poll_page'] ) ){
					$default_options ['has_auto_generate_poll_page'] = 'no';
				}
				update_option( 'yop_poll_options', $default_options );

				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			// update for version 3.3
			if ( version_compare( $installed_version, '3.3', '<=' ) ){
				$yop_poll_first_install_date = get_option( 'yop_poll_first_install_date' );
				if ( !$yop_poll_first_install_date ){
					$oldest_yop_poll = Yop_Poll_Model::get_oldest_poll_from_database();
					update_option( "yop_poll_first_install_date", $oldest_yop_poll ['date_added'] );
				}
				$yop_poll_admin_notices_donate = get_option( 'yop_poll_admin_notices_donate' );
				if ( !$yop_poll_admin_notices_donate ){
					update_option( "yop_poll_admin_notices_donate", 'yes' );
				}
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			// update for version 3.7
			if ( version_compare( $installed_version, '3.7', '<=' ) ){
				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['poll_name_html_tags'] ) ){
					$default_options ['poll_name_html_tags'] = 'no';
				}
				if ( !isset ( $default_options ['poll_question_html_tags'] ) ){
					$default_options ['poll_question_html_tags'] = 'no';
				}
				if ( !isset ( $default_options ['poll_answer_html_tags'] ) ){
					$default_options ['poll_answer_html_tags'] = 'no';
				}
				if ( !isset ( $default_options ['poll_custom_field_html_tags'] ) ){
					$default_options ['poll_custom_field_html_tags'] = 'no';
				}
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
				update_option( 'yop_poll_options', $default_options );
			}

			if ( version_compare( $installed_version, '3.9', '<=' ) ){
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				require_once( YOP_POLL_INC . '/' . 'db_schema.php' );
				Yop_Poll_DbSchema::create_polls_table();
				Yop_Poll_DbSchema::create_polls_templates_table();

				wp_get_current_user();
				if ( $current_user->ID > 0 ){
					$wpdb->query( "UPDATE " . $wpdb->yop_polls . " SET poll_author = " . $current_user->ID . " WHERE poll_author = 0" );
					$wpdb->query( "UPDATE " . $wpdb->yop_poll_templates . " SET template_author = " . $current_user->ID . " WHERE template_author = 0" );
				}

				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['use_default_loading_image'] ) ){
					$default_options ['use_default_loading_image'] = 'yes';
				}
				if ( !isset ( $default_options ['loading_image_url'] ) ){
					$default_options ['loading_image_url'] = '';
				}
				update_option( 'yop_poll_options', $default_options );

				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.1', '<=' ) ){

				$this->update_to_4_2();
				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['use_captcha'] ) ){
					$default_options ['use_captcha'] = 'no';
				}

				update_option( 'yop_poll_options', $default_options );
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.2', '<=' ) ){

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				require_once( YOP_POLL_INC . '/' . 'db_schema.php' );
				Yop_Poll_DbSchema::create_poll_facebook_users_table();
				Yop_Poll_DbSchema::create_poll_logs_table();
				Yop_Poll_DbSchema::create_poll_votes_custom_fields_table();

				$optin_box_modal_options = get_option( 'yop_poll_optin_box_modal_options' );
				if ( !isset ( $optin_box_modal_options ['show'] ) ){
					$optin_box_modal_options ['show'] = 'yes';
				}
				if ( !isset ( $optin_box_modal_options ['last_show_date'] ) ){
					$optin_box_modal_options ['last_show_date'] = Yop_Poll_Model::get_mysql_curent_date();
				}
				if ( !isset ( $optin_box_modal_options ['modal_had_submit'] ) ){
					$optin_box_modal_options ['modal_had_submit'] = 'no';
				}
				if ( !isset ( $optin_box_modal_options ['sidebar_had_submit'] ) ){
					$optin_box_modal_options ['sidebar_had_submit'] = 'no';
				}
				update_option( 'yop_poll_optin_box_modal_options', $optin_box_modal_options );

				$pro_options = get_option( 'yop_poll_pro_options' );
				if ( !isset ( $pro_options ['pro_key'] ) ){
					$pro_options ['pro_key'] = '';
				}
				if ( !isset ( $pro_options ['pro_api_key'] ) ){
					$pro_options ['pro_api_key'] = '';
				}
				if ( !isset ( $pro_options ['pro_api_server_url'] ) ){
					$pro_options ['pro_api_server_url'] = 'http://www.yop-poll.com/pro';
				}
				if ( !isset ( $pro_options ['pro_user'] ) ){
					$pro_options ['pro_user'] = 'no';
				}

				$default_options = get_option( 'yop_poll_options' );
				if ( !isset ( $default_options ['vote_permisions_facebook'] ) ){
					$default_options ['vote_permisions_facebook'] = 'no';
				}
				if ( !isset ( $default_options ['vote_permisions_facebook_label'] ) ){
					$default_options ['vote_permisions_facebook_label'] = 'Vote as Facebook User';
				}
				if ( !isset ( $default_options ['vote_permisions_wordpress'] ) ){
					$default_options ['vote_permisions_wordpress'] = 'no';
				}
				if ( !isset ( $default_options ['vote_permisions_wordpress_label'] ) ){
					$default_options ['vote_permisions_wordpress_label'] = 'Vote as Wordpress User';
				}
				if ( !isset ( $default_options ['vote_permisions_anonymous'] ) ){
					$default_options ['vote_permisions_anonymous'] = 'no';
				}
				if ( !isset ( $default_options ['vote_permisions_anonymous_label'] ) ){
					$default_options ['vote_permisions_anonymous_label'] = 'Vote as Anonymous User';
				}

				if ( !isset ( $default_options ['share_after_vote'] ) ){
					$default_options ['share_after_vote'] = 'no';
				}
				if ( !isset ( $default_options ['share_picture'] ) ){
					$default_options ['share_picture'] = '';
				}
				if ( !isset ( $default_options ['share_name'] ) ){
					$default_options ['share_name'] = '';
				}
				if ( !isset ( $default_options ['share_caption'] ) ){
					$default_options ['share_caption'] = '';
				}
				if ( !isset ( $default_options ['share_description'] ) ){
					$default_options ['share_description'] = '';
				}
				if ( !isset ( $default_options ['redirect_after_vote'] ) ){
					$default_options ['redirect_after_vote'] = 'no';
				}
				if ( !isset ( $default_options ['redirect_after_vote_url'] ) ){
					$default_options ['redirect_after_vote_url'] = '';
				}
				if ( !isset ( $default_options ['allow_multiple_answers_min_number'] ) ){
					$default_options ['allow_multiple_answers_min_number'] = '1';
				}
				if ( !isset ( $default_options ['is_default_answer'] ) ){
					$default_options ['is_default_answer'] = 'no';
				}
				if ( !isset ( $default_options ['template_width'] ) ){
					$default_options ['template_width'] = '200px';
				}
				if ( !isset ( $default_options ['widget_template'] ) ){
					$default_options ['widget_template'] = $default_options['template'];
				}
				if ( !isset ( $default_options ['widget_template_width'] ) ){
					$default_options ['widget_template_width'] = '200px';
				}

				$wpdb->query( "
					UPDATE " . $wpdb->yop_poll_templates . "
					SET
					css = REPLACE( css, 'width:200px;', 'width:%POLL-WIDTH%;')
				" );

				update_option( 'yop_poll_options', $default_options );

				update_option( 'yop_poll_pro_options', $pro_options );

				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.3', '<=' ) ){
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				require_once( YOP_POLL_INC . '/' . 'db_schema.php' );
				Yop_Poll_DbSchema::create_poll_voters_table();

				$wpdb->query( "
					UPDATE " . $wpdb->yop_poll_templates . "
					SET
					css = CONCAT( css, '\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n	font-size:12px;\r\n	font-style:italic;\r\n	color:green;\r\n}' )
					WHERE
					css NOT LIKE '%#yop-poll-container-success-%'
				" );

				$default_options = get_option( 'yop_poll_options' );

				if ( !isset ( $default_options ['limit_number_of_votes_per_user'] ) ){
					$default_options ['limit_number_of_votes_per_user'] = 'no';
				}

				if ( !isset ( $default_options ['number_of_votes_per_user'] ) ){
					$default_options ['number_of_votes_per_user'] = 1;
				}

				if ( !isset ( $default_options ['message_after_vote'] ) ){
					$default_options ['message_after_vote'] = 'Thank you for your vote!';
				}

				if ( !isset ( $default_options ['start_scheduler'] ) ){
					$default_options ['start_scheduler'] = 'no';
				}
				if ( !isset ( $default_options ['schedule_reset_poll_stats'] ) ){
					$default_options ['schedule_reset_poll_stats'] = 'no';
				}
				if ( !isset ( $default_options ['schedule_reset_poll_date'] ) ){
					$default_options ['schedule_reset_poll_date'] = current_time( 'timestamp' );
				}
				if ( !isset ( $default_options ['schedule_reset_poll_recurring_value'] ) ){
					$default_options ['schedule_reset_poll_recurring_value'] = '9999';
				}
				if ( !isset ( $default_options ['schedule_reset_poll_recurring_unit'] ) ){
					$default_options ['schedule_reset_poll_recurring_unit'] = 'DAY';
				}

				update_option( 'yop_poll_options', $default_options );
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.4', '<=' ) ){
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				require_once( YOP_POLL_INC . '/' . 'db_schema.php' );
				Yop_Poll_DbSchema::create_poll_logs_table();
				Yop_Poll_DbSchema::create_poll_votes_custom_fields_table();

				$default_options = get_option( 'yop_poll_options' );

				if ( !isset ( $default_options ['view_results_permissions'] ) ){
					$default_options ['view_results_permissions'] = 'guest-registered';
				}

				if ( !isset ( $default_options ['date_format'] ) ){
					$default_options ['date_format'] = 'd/m/Y H:i:s';
				}

				if ( !isset ( $default_options ['add_other_answers_to_default_answers'] ) ){
					$default_options ['add_other_answers_to_default_answers'] = 'no';
				}

				update_option( 'yop_poll_options', $default_options );
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.5', '<=' ) ){

				$default_options = get_option( 'yop_poll_options' );

				if ( !isset ( $default_options ['send_email_notifications'] ) ){
					$default_options ['send_email_notifications'] = 'no';
				}

				if ( !isset ( $default_options ['email_notifications_from_name'] ) ){
					$default_options ['email_notifications_from_name'] = 'Yop Poll';
				}

				$sitename = strtolower( $_SERVER['SERVER_NAME'] );
				if ( substr( $sitename, 0, 4 ) == 'www.' ){
					$sitename = substr( $sitename, 4 );
				}

				if ( !isset ( $default_options ['email_notifications_from_email'] ) ){
					$default_options ['email_notifications_from_email'] = 'yop-poll@' . $sitename;
				}

				if ( !isset ( $default_options ['email_notifications_recipients'] ) ){
					$default_options ['email_notifications_recipients'] = '';
				}

				if ( !isset ( $default_options ['email_notifications_subject'] ) ){
					$default_options ['email_notifications_subject'] = 'New Vote';
				}

				if ( !isset ( $default_options ['email_notifications_body'] ) ){
					$default_options ['email_notifications_body'] = '<p>A new vote was registered on [VOTE_DATE] for [POLL_NAME]</p>
					<p>Vote Details:</p>
					<p><b>Question:</b> [QUESTION]</p>
					<p><b>Answers:</b> <br />[ANSWERS]</p>
					<p><b>Custom Fields:</b> <br />[CUSTOM_FIELDS]</p>
					<p><b>Vote ID:</b> <br />[VOTE_ID]</p>';
				}

				update_option( 'yop_poll_options', $default_options );
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.7', '<=' ) ){
				$this->update_to_4_8();
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.8', '<=' ) ){
				$this->update_to_4_9();
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.9', '<=' ) ){
				$this->update_to_4_9_1();
				update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}

			if ( version_compare( $installed_version, '4.9.1', '<=' ) ){

			update_option( "yop_poll_version", $wpdb->yop_poll_version );
			}
		}

		public function update_to_4_2() {
			global $wpdb;

			$saved_templates = $wpdb->get_results( "
				SELECT id FROM  " . $wpdb->yop_poll_templates . "
				WHERE
				before_vote_template LIKE '%<div id=\\" . '"' . "yop-poll-vote-\%POLL-ID\%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">%' AND
				before_vote_template NOT LIKE '%[CAPTCHA_CONTAINER]%'
				", ARRAY_A );

			$updated_templates = $wpdb->get_results( "
				SELECT id FROM  " . $wpdb->yop_poll_templates . "
				WHERE
				before_vote_template LIKE '%<div id=\\\\\\\\" . '"' . "yop-poll-vote-\%POLL-ID\%\\\\\\\\" . '"' . " class=\\\\\\\\" . '"' . "yop-poll-footer\\\\\\\\" . '"' . ">%'  AND
				before_vote_template NOT LIKE '%[CAPTCHA_CONTAINER]%'
				", ARRAY_A );
			if ( count( $saved_templates ) > 0 ){
				foreach ( $saved_templates as $template ) {
					$wpdb->query( "
						UPDATE " . $wpdb->yop_poll_templates . " SET
						before_vote_template =	REPLACE( before_vote_template, '<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">', '[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">')
						WHERE
						id = " . $template ['id'] . " AND
						before_vote_template NOT LIKE '%[CAPTCHA_CONTAINER]%'
					" );
				}
			}
			if ( count( $updated_templates ) > 0 ){
				foreach ( $updated_templates as $template ) {
					$wpdb->query( "
						UPDATE " . $wpdb->yop_poll_templates . " SET
						before_vote_template =	REPLACE( before_vote_template, '<div id=\\\\" . '"' . "yop-poll-vote-%POLL-ID%\\\\" . '"' . " class=\\\\" . '"' . "yop-poll-footer\\\\" . '"' . ">', '[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">')
						WHERE
						id = " . $template ['id'] . " AND
						before_vote_template NOT LIKE '%[CAPTCHA_CONTAINER]%'
					" );
				}
			}

			$css_templates = $wpdb->get_results( "
				SELECT id FROM  " . $wpdb->yop_poll_templates . "
				WHERE
				css LIKE '%#yop-poll-custom-%POLL-ID% ul li input { margin:0px 0px 5px 0px; padding:2\%; width:96\%; text-indent:2\%; font-size:12px; }%' AND
				css NOT LIKE '%yop-poll-captcha%'
				", ARRAY_A );

			if ( count( $css_templates ) > 0 ){
				foreach ( $css_templates as $template ) {
					$wpdb->query( "
						UPDATE " . $wpdb->yop_poll_templates . " SET
						css =	REPLACE( css, '#yop-poll-custom-%POLL-ID% ul li input { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }', '#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n')
						WHERE
						id = " . $template ['id'] . " AND
						css NOT LIKE '%yop-poll-captcha%'
					" );
				}
			}

			$css_templates_1 = $wpdb->get_results( "
				SELECT id FROM  " . $wpdb->yop_poll_templates . "
				WHERE
				css LIKE '%#yop-poll-custom-%POLL-ID% ul li input { margin:0px 0px 5px 0px; padding:2\%; width:95\%; text-indent:2\%; font-size:12px; }%' AND
				css NOT LIKE '%yop-poll-captcha%'
				", ARRAY_A );

			if ( count( $css_templates_1 ) > 0 ){
				foreach ( $css_templates_1 as $template ) {
					$wpdb->query( "
						UPDATE " . $wpdb->yop_poll_templates . " SET
						css =	REPLACE( css, '#yop-poll-custom-%POLL-ID% ul li input { margin:0px 0px 5px 0px; padding:2%; width:95%; text-indent:2%; font-size:12px; }', '#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n')
						WHERE
						id = " . $template ['id'] . " AND
						css NOT LIKE '%yop-poll-captcha%'
					" );
				}
			}
		}

		public function update_to_4_8() {
			global $wpdb;
			$sql               = <<<EOT
		UPDATE $wpdb->yop_poll_templates
		SET
		js = CONCAT( js, '
			jQuery(document).ready(function(){
				runOnPollStateChange_%POLL-ID%();
				});

			function runOnPollStateChange_%POLL-ID%() {};'
			)
		WHERE js NOT LIKE '%runOnPollStateChange_%'
EOT;
			$updated_templates = $wpdb->query( $sql );
		}

		public function update_to_4_9() {
			global $wpdb;
			$sql = <<<EOT
					UPDATE $wpdb->yop_poll_templates
					SET `before_vote_template` =
					REPLACE( `before_vote_template` ,
					'<div id="yop-poll-answers-%POLL-ID"',
					'<div id="yop-poll-answers-%POLL-ID%"' )
					WHERE `before_vote_template` LIKE '%<div id="yop-poll-answers-%POLL-ID"%'
EOT;
			$wpdb->query( $sql );
		}

		public function update_to_4_9_1() {
			global $wpdb;
			$sql = <<<EOT
					UPDATE $wpdb->yop_poll_templates
					SET `before_vote_template` =
					REPLACE( `before_vote_template` ,
					'<div id="yop-poll-answers-%POLL-ID"',
					'<div id="yop-poll-answers-%POLL-ID%"' )
					WHERE `before_vote_template` LIKE '%<div id="yop-poll-answers-\%POLL-ID"%'
EOT;
			$wpdb->query( $sql );

			$sql = <<<EOT
					UPDATE $wpdb->yop_poll_templates
					SET `before_vote_template` =
					REPLACE( `before_vote_template` ,
					'<li class="yop-poll-li-answer-%POLL-ID"',
					'<li class="yop-poll-li-answer-%POLL-ID%"' )
					WHERE `before_vote_template` LIKE '%<li class="yop-poll-li-answer-\%POLL-ID"%'
EOT;
			$wpdb->query( $sql );
		}

		public function admin_loader() {
			//	$this->add_action ( 'network_admin_menu', 'admin_menu', 1 );
			$this->add_action( 'admin_init', 'yop_poll_options_admin_init', 1 );
			$this->add_action( 'admin_menu', 'admin_menu', 1 );
			$this->add_action( 'wp_ajax_yop_poll_editor', 'ajax_get_polls_for_editor', 1 );
			$this->add_action( 'wp_ajax_yop_poll_preview_template', 'ajax_preview_template', 1 );
			$this->add_action( 'wp_ajax_yop_poll_html_editor', 'ajax_get_polls_for_html_editor', 1 );
			$this->add_action( 'wp_ajax_yop_poll_edit_add_new_poll', 'ajax_edit_add_new_poll', 1 );
			$this->add_action( 'wp_ajax_yop_poll_edit_add_new_poll_template', 'ajax_edit_add_new_poll_template', 1 );
			$this->add_action( 'wp_ajax_yop_poll_reset_poll_template', 'ajax_reset_poll_template', 1 );
			$this->add_action( 'wp_ajax_yop_poll_show_optin_box_modal', 'ajax_show_optin_box_modal', 1 );
			$this->add_action( 'wp_ajax_yop_poll_modal_option_signup', 'ajax_modal_option_signup', 1 );
			$this->add_action( 'wp_ajax_yop_poll_sidebar_option_signup', 'ajax_sidebar_option_signup', 1 );
			$this->add_action( 'wp_ajax_yop_poll_show_change_votes_number_answer', 'ajax_show_change_votes_number_answer', 1 );
			$this->add_action( 'wp_ajax_yop_poll_do_change_votes_number_answer', 'ajax_do_change_votes_number_answer', 1 );

			$this->add_action( 'wp_ajax_yop_poll_show_change_total_number_poll', 'ajax_show_change_total_number_poll', 1 );
			$this->add_action( 'wp_ajax_yop_poll_do_change_total_number_poll', 'ajax_do_change_total_number_poll', 1 );

			$this->add_action( 'wp_ajax_yop_poll_show_change_poll_author', 'ajax_show_change_poll_author', 1 );
			$this->add_action( 'wp_ajax_yop_poll_do_change_poll_author', 'ajax_do_change_poll_author', 1 );

			$this->add_action( 'wp_ajax_yop_poll_show_change_template_author', 'ajax_show_change_template_author', 1 );
			$this->add_action( 'wp_ajax_yop_poll_do_change_template_author', 'ajax_do_change_template_author', 1 );

			$this->add_action( 'wp_ajax_nopriv_yop_poll_do_vote', 'yop_poll_do_vote', 1 );
			$this->add_action( 'wp_ajax_yop_poll_do_vote', 'yop_poll_do_vote', 1 );

			$this->add_action( 'wp_ajax_nopriv_yop_poll_view_results', 'yop_poll_view_results', 1 );
			$this->add_action( 'wp_ajax_yop_poll_view_results', 'yop_poll_view_results', 1 );

			$this->add_action( 'wp_ajax_yop_poll_back_to_vote', 'yop_poll_back_to_vote', 1 );
			$this->add_action( 'wp_ajax_nopriv_yop_poll_back_to_vote', 'yop_poll_back_to_vote', 1 );

			$this->add_action( 'wp_ajax_yop_poll_load_css', 'yop_poll_load_css', 1 );
			$this->add_action( 'wp_ajax_nopriv_yop_poll_load_css', 'yop_poll_load_css', 1 );

			$this->add_action( 'wp_ajax_yop_poll_load_js', 'yop_poll_load_js', 1 );
			$this->add_action( 'wp_ajax_nopriv_yop_poll_load_js', 'yop_poll_load_js', 1 );

			$this->add_action( 'wp_ajax_yop_poll_show_captcha', 'ajax_show_captcha', 1 );
			$this->add_action( 'wp_ajax_nopriv_yop_poll_show_captcha', 'ajax_show_captcha', 1 );

			$this->add_action( 'wp_ajax_yop_poll_play_captcha', 'ajax_play_captcha', 1 );
			$this->add_action( 'wp_ajax_nopriv_yop_poll_play_captcha', 'ajax_play_captcha', 1 );

			$this->add_action( 'wp_ajax_yop_poll_is_wordpress_user', 'ajax_is_wordpress_user', 1 );
			$this->add_action( 'wp_ajax_nopriv_yop_poll_is_wordpress_user', 'ajax_is_wordpress_user', 1 );

			$this->add_action( 'wp_ajax_yop_poll_set_wordpress_vote', 'ajax_set_wordpress_vote', 1 );
			$this->add_action( 'wp_ajax_nopriv_yop_poll_set_wordpress_vote', 'ajax_set_wordpress_vote', 1 );
		}

		/**
		* this file is executed on activation
		* it creates the database and adds some data to database's table
		*/
		public function activate( $networkwide ) {
			global $wp_version;
			if ( !version_compare( $wp_version, YOP_POLL_WP_VERSION, '>=' ) ){
				$error = new WP_Error ( 'Wordpress_version_error', sprintf( __( 'You need at least Wordpress version %s to use this plugin', 'yop_poll' ), YOP_POLL_WP_VERSION ), __( 'Error: Wordpress Version Problem', 'yop_poll' ) );

				// die & print error message & code - for admins only!
				if ( isset ( $error ) && is_wp_error( $error ) && current_user_can( 'manage_options' ) ){
					wp_die( $error->get_error_message(), $error->get_error_data() );
				}
			}
			else {
				if ( !extension_loaded( 'json' ) ){
					$error = new WP_Error ( 'Wordpress_json_error', __( 'You need the  json php extension for this plugin', 'yop_poll' ), __( 'Error: Wordpress Extension Problem', 'yop_poll' ) );

					// die & print error message & code - for admins only!
					if ( isset ( $error ) && is_wp_error( $error ) && current_user_can( 'manage_options' ) ){
						wp_die( $error->get_error_message(), $error->get_error_data() );
					}
				}
				// including upgrade.php for using dbDelta()
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				// including db_schema class file
				require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
				require_once( YOP_POLL_INC . '/' . 'db_schema.php' );
				// create tables
				Yop_Poll_DbSchema::create_poll_database_tables();
			}
		}

		public function deactivate( $networkwide ) {
			/*
			* global $wpdb; delete_option( 'yop_poll_version' ); delete_option( 'yop_poll_options' ); delete_option( 'yop_poll_first_install_date' ); delete_option( 'yop_poll_admin_notices_donate' ); $wpdb->query("DROP TABLE `". $wpdb->prefix ."yop_pollmeta`, `". $wpdb->prefix ."yop_polls`, `". $wpdb->prefix ."yop_poll_answermeta`, `". $wpdb->prefix ."yop_poll_answers`, `". $wpdb->prefix ."yop_poll_custom_fields`, `". $wpdb->prefix ."yop_poll_logs`, `". $wpdb->prefix ."yop_poll_bans`, `". $wpdb->prefix ."yop_poll_templates`, `". $wpdb->prefix ."yop_poll_votes_custom_fields`");
			*/
			$poll_archive_page = get_page_by_path( 'yop-poll-archive', ARRAY_A );
			if ( $poll_archive_page ){
				$poll_archive_page_id = $poll_archive_page ['ID'];
				wp_delete_post( $poll_archive_page_id, true );
			}
			$schedule_timestamp = wp_next_scheduled( 'yop_poll_hourly_event', array() );
			if ( $schedule_timestamp ){
				wp_unschedule_event( $schedule_timestamp, 'yop_poll_hourly_event', array() );
			}
		}

		public function admin_menu() {
			if ( is_admin() && $this->current_user_can( 'edit_own_polls' ) ){
				if ( function_exists( 'add_menu_page' ) ){
					if ( $this->current_user_can( 'edit_own_polls' ) ){
						$page = add_menu_page( __( 'Yop Poll', 'yop_poll' ), __( 'Yop Poll', 'yop_poll' ), 'read', 'yop-polls', array( $this, 'manage_pages' ), "{$this->_config->plugin_url}/images/yop-poll-admin-menu-icon16.png", '28.734' );
					}
				}
				if ( $this->current_user_can( 'edit_own_polls' ) ){
					add_action( "load-$page", array( &$this, 'manage_pages_load' ) );
				}
				if ( function_exists( 'add_submenu_page' ) ){

					if ( $this->current_user_can( 'edit_own_polls' ) ){
						$subpage = add_submenu_page( 'yop-polls', __( 'All Polls', 'yop_poll' ), __( 'All Polls', 'yop_poll' ), 'read', 'yop-polls', array( &$this, 'manage_pages' ) );
						add_action( "load-$subpage", array( &$this, 'manage_pages_load' ) );
					}
					if ( $this->current_user_can( 'edit_own_polls' ) ){
						$subpage = add_submenu_page( 'yop-polls', __( 'Add New', 'yop_poll' ), __( 'Add New', 'yop_poll' ), 'read', 'yop-polls-add-new', array( &$this, 'manage_pages' ) );
						add_action( "load-$subpage", array( &$this, 'manage_pages_load' ) );
					}
					if ( $this->current_user_can( 'manage_polls_options' ) ){
						$subpage = add_submenu_page( 'yop-polls', __( 'Options', 'yop_poll' ), __( 'Options', 'yop_poll' ), 'read', 'yop-polls-options', array( &$this, 'manage_pages' ) );
						add_action( "load-$subpage", array( &$this, 'manage_pages_load' ) );
					}
					if ( $this->current_user_can( 'edit_own_polls_templates' ) ){
						$subpage = add_submenu_page( 'yop-polls', __( 'Templates', 'yop_poll' ), __( 'Templates', 'yop_poll' ), 'read', 'yop-polls-templates', array( &$this, 'manage_pages' ) );
						add_action( "load-$subpage", array( &$this, 'manage_pages_load' ) );
					}
					if ( $this->current_user_can( 'view_own_polls_logs' ) ){
						$subpage = add_submenu_page( 'yop-polls', __( 'Logs', 'yop_poll' ), __( 'Logs', 'yop_poll' ), 'read', 'yop-polls-logs', array( &$this, 'manage_pages' ) );
						add_action( "load-$subpage", array( &$this, 'manage_pages_load' ) );
					}
					if ( $this->current_user_can( 'manage_polls_bans' ) ){
						$subpage = add_submenu_page( 'yop-polls', __( 'Bans', 'yop_poll' ), __( 'Bans', 'yop_poll' ), 'read', 'yop-polls-bans', array( &$this, 'manage_pages' ) );
						add_action( "load-$subpage", array( &$this, 'manage_pages_load' ) );
					}
					if ( $this->current_user_can( 'become_pro' ) ){
						$subpage = add_submenu_page( 'yop-polls', __( 'Become Pro', 'yop_poll' ), __( 'Become Pro', 'yop_poll' ), 'read', 'yop-polls-become-pro', array( &$this, 'manage_pages' ) );
						add_action( "load-$subpage", array( &$this, 'manage_pages_load' ) );
					}
				}
			}
		}

		public function manage_pages() {
			global $page, $action;
			switch ( $page ) {
				case 'yop-polls' :
					if ( 'custom-fields' == $action ){
						$this->view_poll_custom_fields();
						break;
					}
					if ( 'results' == $action ){
						$this->view_poll_results();
						break;
					}
					elseif ( 'edit' == $action ) {
					}
					else {
						$this->view_all_polls();
						break;
					}
				case 'yop-polls-add-new' :
					$this->view_add_edit_new_poll();
					break;
				case 'yop-polls-options' :
					$this->view_yop_poll_options();
					break;
				case 'yop-polls-logs' :
					$this->view_yop_poll_logs();
					break;
				case 'yop-polls-bans' :
					$this->view_yop_poll_bans();
					break;
				case 'yop-polls-become-pro' :
					$this->view_yop_poll_become_pro();
					break;
				case 'yop-polls-templates' :
					if ( 'add-new' == $action || 'edit' == $action ){
						$this->view_add_edit_poll_template();
					}
					else {
						$this->view_yop_poll_templates();
					}
					break;
				default :
					$this->view_all_polls();
			}
		}

		public function manage_pages_load() {
			wp_reset_vars( array( 'page', 'action', 'orderby', 'order' ) );
			global $page, $action, $orderby, $order, $yop_poll_add_new_config;
			$default_options = get_option( 'yop_poll_options', array() );

			wp_enqueue_style( 'yop-poll-admin', "{$this->_config->plugin_url}/css/yop-poll-admin.css", array(), $this->_config->version );
			$answers_number      = $this->_config->min_number_of_answers + 1; // total +1
			$customfields_number = $this->_config->min_number_of_customfields + 1; // total +1

			wp_enqueue_script( 'yop-poll-admin', "{$this->_config->plugin_url}/js/yop-poll-admin.js", array( 'jquery' ), $this->_config->version, true );
			$doScroll = 0;
			switch ( $page ) {
				case 'yop-polls' :
					if ( 'results' == $action ){
						wp_enqueue_style( 'yop-poll-admin-results', "{$this->_config->plugin_url}/css/yop-poll-admin-results.css", array(), $this->_config->version );
						wp_enqueue_style( 'yop-poll-timepicker', "{$this->_config->plugin_url}/css/timepicker.css", array(), $this->_config->version );
						wp_enqueue_style( 'yop-poll-jquery-ui', "{$this->_config->plugin_url}/css/jquery-ui.css", array(), $this->_config->version );
						wp_enqueue_script( 'yop-poll-jquery-ui-timepicker', "{$this->_config->plugin_url}/js/jquery-ui-timepicker-addon.js", array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->_config->version, true );
						wp_enqueue_style( 'yop-poll-admin-custom-fields', "{$this->_config->plugin_url}/css/yop-poll-admin-custom-fields.css", array(), $this->_config->version );
						wp_enqueue_script( 'yop-poll-admin-custom-fields', "{$this->_config->plugin_url}/js/yop-poll-admin-custom-fields.js", array( 'jquery', 'yop-poll-jquery-ui-timepicker' ), $this->_config->version, true );
						$this->yop_poll_custom_fields_results_operations();
						break;
					}
					if ( 'custom-fields' == $action ){
						wp_enqueue_style( 'yop-poll-timepicker', "{$this->_config->plugin_url}/css/timepicker.css", array(), $this->_config->version );
						wp_enqueue_style( 'yop-poll-jquery-ui', "{$this->_config->plugin_url}/css/jquery-ui.css", array(), $this->_config->version );
						wp_enqueue_script( 'yop-poll-jquery-ui-timepicker', "{$this->_config->plugin_url}/js/jquery-ui-timepicker-addon.js", array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->_config->version, true );
						wp_enqueue_style( 'yop-poll-admin-custom-fields', "{$this->_config->plugin_url}/css/yop-poll-admin-custom-fields.css", array(), $this->_config->version );
						wp_enqueue_script( 'yop-poll-admin-custom-fields', "{$this->_config->plugin_url}/js/yop-poll-admin-custom-fields.js", array( 'jquery', 'yop-poll-jquery-ui-timepicker' ), $this->_config->version, true );
						$this->yop_poll_custom_fields_operations();
						break;
					}
					elseif ( 'edit' == $action ) {
						$doScroll = 1;
						require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
						$poll_id             = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
						$yop_poll_model      = new Yop_Poll_Model ( $poll_id );
						$answers             = Yop_Poll_Model::get_poll_answers( $poll_id );
						$answers_number      = count( $answers ) + 1; // total +1
						$custom_fields       = Yop_Poll_Model::get_poll_customfields( $poll_id );
						$customfields_number = count( $custom_fields ) + 1; // total +1
					}
					else {
						$this->view_all_polls_operations();
						wp_enqueue_script( 'link' );
						wp_enqueue_script( 'xfn' );
						wp_enqueue_script( 'yop-poll-optin-form', "http://app.getresponse.com/view_webform.js?wid=394041&mg_param1=1", NULL, $this->_config->version, true );
						$this->load_optin_box();
						break;
					}

				case 'yop-polls-add-new' :
					$doScroll                = 1;
					$yop_poll_add_new_config = array( 'ajax' => array( 'url' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), 'action' => 'yop_poll_edit_add_new_poll', 'beforeSendMessage' => __( 'Please wait a moment while we process your request...', 'yop_poll' ), 'errorMessage' => __( 'An error has occured...', 'yop_poll' ) ), 'text_answer' => __( 'Answer', 'yop_poll' ), 'text_customfield' => __( 'Custom Text Field', 'yop_poll' ), 'text_requiered_customfield' => __( 'Required', 'yop_poll' ), 'text_remove_answer' => __( 'Remove', 'yop_poll' ), 'text_remove_customfield' => __( 'Remove', 'yop_poll' ), 'text_customize_answer' => __( 'More Options', 'yop_poll' ), 'text_change_votes_number_answer' => __( 'Change Number Of Votes', 'yop_poll' ), 'text_change_votes_number_poll' => __( 'Change Number Of Total Votes', 'yop_poll' ), 'text_change_answers_number_poll' => __( 'Change Number Of Total Answers', 'yop_poll' ), 'plugin_url' => $this->_config->plugin_url, 'default_number_of_answers' => $answers_number, 'default_number_of_customfields' => $customfields_number, 'text_is_default_answer' => __( 'Make this the default answer', 'yop_poll' ) . '<br><font size="0">(' . __( 'if "yes", answer will be autoselected when poll is displayed', 'yop_poll' ) . ')</font>', 'text_poll_bar_style' => array( 'use_template_bar_label' => __( 'Use Template Result Bar', 'yop_poll' ), 'use_template_bar_yes_label' => __( 'Yes', 'yop_poll' ), 'use_template_bar_no_label' => __( 'No', 'yop_poll' ), 'poll_bar_style_label' => __( 'Yop Poll Bar Style', 'yop_poll' ), 'poll_bar_preview_label' => __( 'Yop Poll Bar Preview', 'yop_poll' ), 'poll_bar_style_background_label' => __( 'Background Color', 'yop_poll' ), 'poll_bar_style_height_label' => __( 'Height', 'yop_poll' ), 'poll_bar_style_border_color_label' => __( 'Border Color', 'yop_poll' ), 'poll_bar_style_border_width_label' => __( 'Border Width', 'yop_poll' ), 'poll_bar_style_border_style_label' => __( 'Border Style', 'yop_poll' ) ), 'poll_bar_default_options' => array( 'use_template_bar' => isset ( $default_options ['use_template_bar'] ) ? $default_options ['use_template_bar'] : 'yes', 'height' => isset ( $default_options ['bar_height'] ) ? $default_options ['bar_height'] : 10, 'background_color' => isset ( $default_options ['bar_background'] ) ? $default_options ['bar_background'] : 'd8e1eb', 'border' => isset ( $default_options ['bar_border_style'] ) ? $default_options ['bar_border_style'] : 'solid', 'border_width' => isset ( $default_options ['bar_border_width'] ) ? $default_options ['bar_border_width'] : 1, 'border_color' => isset ( $default_options ['bar_border_color'] ) ? $default_options ['bar_border_color'] : 'c8c8c8' ) );
					wp_enqueue_style( 'yop-poll-admin-add-new', "{$this->_config->plugin_url}/css/yop-poll-admin-add-new.css", array(), $this->_config->version );
					wp_enqueue_style( 'yop-poll-timepicker', "{$this->_config->plugin_url}/css/timepicker.css", array(), $this->_config->version );
					wp_enqueue_style( 'yop-poll-jquery-ui', "{$this->_config->plugin_url}/css/jquery-ui.css", array(), $this->_config->version );

					wp_enqueue_script( 'yop-poll-admin-add-new', "{$this->_config->plugin_url}/js/yop-poll-admin-add-new.js", array( 'jquery', 'yop-poll-jquery-ui-timepicker' ), $this->_config->version, true );
					wp_enqueue_script( 'yop-poll-jquery-ui-timepicker', "{$this->_config->plugin_url}/js/jquery-ui-timepicker-addon.js", array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->_config->version, true );
					wp_enqueue_script( 'yop-poll-modal-box-js', "{$this->_config->plugin_url}/modal/js/jquery.modalbox-1.5.0-min.js", array( 'jquery' ), $this->_config->version, true );
					wp_enqueue_style( 'yop-poll-modal-box-css', "{$this->_config->plugin_url}/modal/css/jquery.modalbox-skin-precious-white.css", array(), $this->_config->version );
					wp_localize_script( 'yop-poll-admin-add-new', 'yop_poll_add_new_config', $yop_poll_add_new_config );
					wp_enqueue_script( 'link' );
					wp_enqueue_script( 'xfn' );
					break;
				case 'yop-polls-logs' :
					wp_enqueue_style( 'yop-poll-timepicker', "{$this->_config->plugin_url}/css/timepicker.css", array(), $this->_config->version );
					wp_enqueue_style( 'yop-poll-jquery-ui', "{$this->_config->plugin_url}/css/jquery-ui.css", array(), $this->_config->version );
					wp_enqueue_script( 'yop-poll-jquery-ui-timepicker', "{$this->_config->plugin_url}/js/jquery-ui-timepicker-addon.js", array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->_config->version, true );
					wp_enqueue_script( 'yop-poll-admin-logs', "{$this->_config->plugin_url}/js/yop-poll-admin-logs.js", array( 'jquery', 'yop-poll-jquery-ui-timepicker' ), $this->_config->version, true );
					$this->view_yop_poll_logs_operations();
					break;
				case 'yop-polls-bans' :
					wp_enqueue_script( 'yop-poll-admin-bans', "{$this->_config->plugin_url}/js/yop-poll-admin-bans.js", array( 'jquery' ), $this->_config->version, true );
					$this->view_yop_poll_bans_operations();
					break;
				case 'yop-polls-options' :
					$doScroll = 1;
					wp_enqueue_style( 'yop-poll-admin-options', "{$this->_config->plugin_url}/css/yop-poll-admin-options.css", array(), $this->_config->version );
					wp_enqueue_style( 'yop-poll-timepicker', "{$this->_config->plugin_url}/css/timepicker.css", array(), $this->_config->version );
					wp_enqueue_style( 'yop-poll-jquery-ui', "{$this->_config->plugin_url}/css/jquery-ui.css", array(), $this->_config->version );
					wp_enqueue_script( 'yop-poll-admin-options', "{$this->_config->plugin_url}/js/yop-poll-admin-options.js", array( 'jquery', 'yop-poll-jquery-ui-timepicker' ), $this->_config->version, true );
					wp_enqueue_script( 'yop-poll-jquery-ui-timepicker', "{$this->_config->plugin_url}/js/jquery-ui-timepicker-addon.js", array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->_config->version, true );
					wp_enqueue_script( 'link' );
					wp_enqueue_script( 'xfn' );
					break;
				case 'yop-polls-templates' :
					if ( 'edit' == $action || 'add-new' == $action ){
						$doScroll = 1;
					}
					add_filter( 'user_can_richedit', create_function( '$a', 'return false;' ), 1 );
					wp_enqueue_script( array( 'editor', 'thickbox' ) );
					wp_enqueue_style( 'thickbox' );
					wp_enqueue_script( 'yop-poll-admin-templates', "{$this->_config->plugin_url}/js/yop-poll-admin-templates.js", array( 'jquery' ), $this->_config->version, true );
					$yop_poll_add_new_template_config = array( 'ajax' => array( 'url' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), 'action' => 'yop_poll_edit_add_new_poll_template', 'reset_action' => 'yop_poll_reset_poll_template', 'beforeSendMessage' => __( 'Please wait a moment while we process your request...', 'yop_poll' ), 'errorMessage' => __( 'An error has occured...', 'yop_poll' ) ) );
					wp_enqueue_script( 'yop-poll-modal-box-js', "{$this->_config->plugin_url}/modal/js/jquery.modalbox-1.5.0-min.js", array( 'jquery' ), $this->_config->version, true );
					wp_enqueue_style( 'yop-poll-modal-box-css', "{$this->_config->plugin_url}/modal/css/jquery.modalbox-skin-precious-white.css", array(), $this->_config->version );
					wp_localize_script( 'yop-poll-admin-templates', 'yop_poll_add_new_template_config', $yop_poll_add_new_template_config );
					$this->view_yop_poll_templates_operations();
					break;
				default :
					$this->view_all_polls_operations();
					break;
			}
			wp_localize_script( 'yop-poll-admin', "yop_poll_do_scroll", array( 'doScroll' => $doScroll ) );
		}

		/**
		* Start operations section
		*/
		public function view_yop_poll_logs_operations() {
			global $page, $action, $order, $orderby, $current_user;
			if ( '-1' != $action && isset ( $_REQUEST ['yoppolllogscheck'] ) ){
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-logs' );
					$bulklogs = ( array )$_REQUEST ['yoppolllogscheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_deleted = 0;
					foreach ( $bulklogs as $log_id ) {
						$log_id      = ( int )$log_id;
						$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_id( 'poll_id', $log_id );
						$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
						if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
							Yop_Poll_Model::delete_poll_log_from_db( $log_id );
						}
						else {
							$total_deleted++;
						}
					}
					wp_redirect( add_query_arg( 'deleted', count( $bulklogs ) - $total_deleted, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'yoppolllogscheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}

				if ( 'delete_group' == $action ){
					check_admin_referer( 'yop-poll-logs' );
					$bulklogs = ( array )$_REQUEST ['yoppolllogscheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_deleted_group = 0;
					foreach ( $bulklogs as $vote_id ) {
						$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_vote_id( 'poll_id', $vote_id );
						$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
						if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
							Yop_Poll_Model::delete_group_poll_log_from_db( $vote_id );
						}
						else {
							$total_deleted_group++;
						}
					}
					wp_redirect( add_query_arg( 'deleted', count( $bulklogs ) - $total_deleted_group, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'yoppolllogscheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( '-1' != $action && isset ( $_REQUEST ['id'] ) ) {
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-logs-delete' );
					$log_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_id( 'poll_id', $log_id );
					$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
					if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
						Yop_Poll_Model::delete_poll_log_from_db( $log_id );
					}
					else {
						wp_die( __( 'You are not allowed to delete this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'deleted', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
				if ( 'delete_group' == $action ){
					check_admin_referer( 'yop-poll-logs-delete' );
					$vote_id = $_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_vote_id( 'poll_id', $vote_id );
					$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
					if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
						Yop_Poll_Model::delete_group_poll_log_from_db( $vote_id );
					}
					else {
						wp_die( __( 'You are not allowed to delete this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'deleted', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( isset ( $_REQUEST ['export'] ) ) {
				global $wpdb;
				if ( isset( $_REQUEST ['a'] ) && __( 'Export', 'yop_poll' ) == $_REQUEST ['a'] ){
					check_admin_referer( 'yop-poll-logs' );
					$per_page  = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
					$page_no   = isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1;
					$orderby   = ( empty ( $orderby ) ) ? 'name' : $orderby;
					$poll_id   = isset ( $_REQUEST ['poll_id'] ) ? ( int )$_REQUEST ['poll_id'] : NULL;
					$log_sdate = ( isset ( $_GET ['log_sdate'] ) ? $_GET ['log_sdate'] : '' );
					$log_edate = ( isset ( $_GET ['log_edate'] ) ? $_GET ['log_edate'] : '' );
					$group_by  = ( isset ( $_GET ['group_by'] ) ? $_GET ['group_by'] : 'vote' );

					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
					if ( $this->current_user_can( 'view_own_polls_logs' ) && $poll_id ){
						if ( $poll_author != $current_user->ID && !$this->current_user_can( 'view_polls_logs' ) ){
							wp_die( __( 'You are not allowed to access this section.', 'yop_poll' ) );
						}
					}
					elseif ( !$this->current_user_can( 'view_polls_logs' ) ) {
						wp_die( __( 'You are not allowed to access this section.', 'yop_poll' ) );
					}

					$yop_polls = Yop_Poll_Model::get_yop_polls_filter_search( 'id', 'asc' );
					if ( $group_by == 'vote' ){
						$search = array( 'fields' => array( 'name', 'ip', 'user_nicename', 'user_email', 'user_from', 'tr_id' ), 'value' => isset ( $_REQUEST ['s'] ) ? trim( $_REQUEST ['s'] ) : '' );
					}
					else {
						$search = array( 'fields' => array( 'name', 'answer', 'ip', 'other_answer_value', 'user_nicename', 'user_from', 'user_email', 'tr_id' ), 'value' => isset ( $_REQUEST ['s'] ) ? trim( $_REQUEST ['s'] ) : '' );
					}
					$filter = array( 'field' => NULL, 'value' => NULL, 'operator' => '=' );
					if ( 'all' == $_REQUEST ['export'] ){
						if ( $group_by == 'vote' ){
							$logs = Yop_Poll_Model::get_group_logs_filter_search( $orderby, $order, $search, $poll_id, 0, 99999999, $log_sdate, $log_edate );
						}
						else {
							$logs = Yop_Poll_Model::get_logs_filter_search( $orderby, $order, $search, $poll_id, 0, 99999999, $log_sdate, $log_edate );
						}
					}
					if ( 'page' == $_REQUEST ['export'] ){
						if ( $group_by == 'vote' ){
							$logs = Yop_Poll_Model::get_group_logs_filter_search( $orderby, $order, $search, $poll_id, ( $page_no - 1 ) * $per_page, $per_page, $log_sdate, $log_edate );
						}
						else {
							$logs = Yop_Poll_Model::get_logs_filter_search( $orderby, $order, $search, $poll_id, ( $page_no - 1 ) * $per_page, $per_page, $log_sdate, $log_edate );
						}
					}

					$csv_file_name    = 'logs_export.' . date( 'YmdHis' ) . '.csv';
					$csv_header_array = array( __( '#', 'yop_poll' ), __( 'Vote ID', 'yop_poll' ), __( 'POLL Name', 'yop_poll' ), __( 'Answer', 'yop_poll' ), __( 'User Type', 'yop_poll' ), __( 'User', 'yop_poll' ), __( 'User Email', 'yop_poll' ), __( 'Tracking ID', 'yop_poll' ), __( 'IP', 'yop_poll' ), __( 'Vote Date', 'yop_poll' ) );

					header( 'Content-type: application/csv' );
					header( 'Content-Disposition: attachment; filename="' . $csv_file_name . '"' );
					ob_start();
					$f = fopen( 'php://output', 'w' ) or show_error( __( "Can't open php://output!", 'yop_poll' ) );

					if ( !fputcsv( $f, $csv_header_array ) ){
						_e( "Can't write header!", 'yop_poll' );
					}

					if ( count( $logs ) > 0 ){
						$index = 1;
						foreach ( $logs as $log ) {
							$logs_data = array( $index, $log ['vote_id'], stripslashes( $log ['name'] ), ( 'Other' == $log ['answer'] ) ? 'Other - ' . stripslashes( $log ['other_answer_value'] ) : stripslashes( $log ['answer'] ), stripslashes( $log ['user_from'] ), stripslashes( $log ['user_nicename'] ), stripslashes( $log ['user_email'] ), stripslashes( $log ['tr_id'] ), stripslashes( $log ['ip'] ), stripslashes( $log ['vote_date'] ) );
							if ( !fputcsv( $f, $logs_data ) ){
								_e( "Can't write header!", 'yop_poll' );
							}
							$index++;
						}
					}

					fclose( $f ) or show_error( __( "Can't close php://output!", 'yop_poll' ) );
					$csvStr = ob_get_contents();
					ob_end_clean();

					echo $csvStr;
					exit ();
				}

				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'export', 'a' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
			elseif ( !empty ( $_GET ['_wp_http_referer'] ) ) {
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
		}

		public function yop_poll_custom_fields_operations() {
			global $page, $action;
			if ( isset ( $_REQUEST ['export'] ) ){
				check_admin_referer( 'yop-poll-custom-fields' );
				if ( '' != $_REQUEST ['export'] ){
					$per_page = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
					$page_no  = ( isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1 );
					$poll_id  = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
					$sdate    = ( isset ( $_GET ['sdate'] ) ? $_GET ['sdate'] : '' );
					$edate    = ( isset ( $_GET ['edate'] ) ? $_GET ['edate'] : '' );
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_details                   = YOP_POLL_MODEL::get_poll_from_database_by_id( $poll_id );
					$poll_custom_fields             = YOP_POLL_MODEL::get_poll_customfields( $poll_id );
					$custom_fields_number           = count( $poll_custom_fields );
					$column_custom_fields_ids       = array();
					$total_custom_fields_logs       = YOP_POLL_MODEL::get_poll_total_customfields_logs( $poll_id, $sdate, $edate );
					$total_custom_fields_logs_pages = ceil( $total_custom_fields_logs / $per_page );
					if ( intval( $page_no ) > intval( $total_custom_fields_logs_pages ) ){
						$page_no = 1;
					}

					if ( 'all' == $_REQUEST ['export'] ){
						$custom_fields_logs = YOP_POLL_MODEL::get_poll_customfields_logs( $poll_id, 'vote_id', 'asc', 0, 99999999, $sdate, $edate );
					}
					if ( 'page' == $_REQUEST ['export'] ){
						$custom_fields_logs = YOP_POLL_MODEL::get_poll_customfields_logs( $poll_id, 'vote_id', 'asc', ( $page_no - 1 ) * $per_page, $per_page, $sdate, $edate );
					}

					$csv_file_name    = 'custom_fields_export.' . date( 'YmdHis' ) . '.csv';
					$csv_header_array = array( __( '#', 'yop_poll' ) );
					foreach ( $poll_custom_fields as $custom_field ) {
						$column_custom_fields_ids [] = $custom_field ['id'];
						$csv_header_array []         = ucfirst( $custom_field ['custom_field'] );
					}
					$csv_header_array [] = __( 'Vote Date', 'yop_poll' );

					header( 'Content-type: application/csv' );
					header( 'Content-Disposition: attachment; filename="' . $csv_file_name . '"' );
					ob_start();
					$f = fopen( 'php://output', 'w' ) or show_error( __( "Can't open php://output!", 'yop_poll' ) );
					$n = 0;
					if ( isset ( $csv_header_array ) ){
						if ( !fputcsv( $f, $csv_header_array ) ){
							_e( "Can't write header!", 'yop_poll' );
						}
					}

					if ( count( $custom_fields_logs ) > 0 ){
						$index = 1;
						foreach ( $custom_fields_logs as $logs ) {
							$column_custom_fields_values = array( $index );
							foreach ( $column_custom_fields_ids as $custom_field_id ) {
								$vote_log_values = array();
								$vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
								if ( count( $vote_logs ) > 0 ){
									foreach ( $vote_logs as $vote_log ) {
										$temp                        = explode( '<#!->', $vote_log );
										$vote_log_values [$temp [1]] = stripslashes( $temp [0] );
									}
								}
								$column_custom_fields_values [] = isset ( $vote_log_values [$custom_field_id] ) ? $vote_log_values [$custom_field_id] : '';
							}
							$column_custom_fields_values [] = $logs ['vote_date'];
							if ( !fputcsv( $f, $column_custom_fields_values ) ){
								_e( "Can't write record!", 'yop_poll' );
							}
							$index++;
						}
					}
					fclose( $f ) or show_error( __( "Can't close php://output!", 'yop_poll' ) );
					$csvStr = ob_get_contents();
					ob_end_clean();

					echo $csvStr;
					exit ();
				}
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'export', 'a' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
			elseif ( !empty ( $_GET ['_wp_http_referer'] ) ) {
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'a' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
		}

		public function yop_poll_custom_fields_results_operations() {
			global $page, $action;
			if ( isset ( $_REQUEST ['export'] ) ){
				check_admin_referer( 'yop-poll-custom-fields' );
				if ( __( 'Export', 'yop_poll' ) == $_REQUEST ['a'] ){
					$cf_per_page = ( isset ( $_GET ['cf_per_page'] ) ? intval( $_GET ['cf_per_page'] ) : 100 );
					$cf_page_no  = ( isset ( $_REQUEST ['cf_page_no'] ) ? ( int )$_REQUEST ['cf_page_no'] : 1 );
					$poll_id     = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
					$cf_sdate    = ( isset ( $_GET ['cf_sdate'] ) ? $_GET ['cf_sdate'] : '' );
					$cf_edate    = ( isset ( $_GET ['cf_edate'] ) ? $_GET ['cf_edate'] : '' );
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_details                   = YOP_POLL_MODEL::get_poll_from_database_by_id( $poll_id );
					$poll_custom_fields             = YOP_POLL_MODEL::get_poll_customfields( $poll_id );
					$custom_fields_number           = count( $poll_custom_fields );
					$column_custom_fields_ids       = array();
					$total_custom_fields_logs       = YOP_POLL_MODEL::get_poll_total_customfields_logs( $poll_id, $cf_sdate, $cf_edate );
					$total_custom_fields_logs_pages = ceil( $total_custom_fields_logs / $cf_per_page );
					if ( intval( $cf_page_no ) > intval( $total_custom_fields_logs_pages ) ){
						$cf_page_no = 1;
					}

					if ( 'all' == $_REQUEST ['export'] ){
						$custom_fields_logs = YOP_POLL_MODEL::get_poll_customfields_logs( $poll_id, 'vote_id', 'asc', 0, 99999999, $cf_sdate, $cf_edate );
					}
					if ( 'page' == $_REQUEST ['export'] ){
						$custom_fields_logs = YOP_POLL_MODEL::get_poll_customfields_logs( $poll_id, 'vote_id', 'asc', ( $cf_page_no - 1 ) * $cf_per_page, $cf_per_page, $cf_sdate, $cf_edate );
					}

					$csv_file_name    = 'custom_fields_export.' . date( 'YmdHis' ) . '.csv';
					$csv_header_array = array( __( '#', 'yop_poll' ) );
					foreach ( $poll_custom_fields as $custom_field ) {
						$column_custom_fields_ids [] = $custom_field ['id'];
						$csv_header_array []         = ucfirst( $custom_field ['custom_field'] );
					}
					$csv_header_array [] = __( 'Vote ID', 'yop_poll' );
					$csv_header_array [] = __( 'Traking ID', 'yop_poll' );
					$csv_header_array [] = __( 'Vote Date', 'yop_poll' );

					header( 'Content-type: application/csv' );
					header( 'Content-Disposition: attachment; filename="' . $csv_file_name . '"' );
					ob_start();
					$f = fopen( 'php://output', 'w' ) or show_error( __( "Can't open php://output!", 'yop_poll' ) );
					$n = 0;
					if ( isset ( $csv_header_array ) ){
						if ( !fputcsv( $f, $csv_header_array ) ){
							_e( "Can't write header!", 'yop_poll' );
						}
					}

					if ( count( $custom_fields_logs ) > 0 ){
						$index = 1;
						foreach ( $custom_fields_logs as $logs ) {
							$column_custom_fields_values = array( $index );
							foreach ( $column_custom_fields_ids as $custom_field_id ) {
								$vote_log_values = array();
								$vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
								if ( count( $vote_logs ) > 0 ){
									foreach ( $vote_logs as $vote_log ) {
										$temp                        = explode( '<#!->', $vote_log );
										$vote_log_values [$temp [1]] = stripslashes( $temp [0] );
									}
								}
								$column_custom_fields_values [] = isset ( $vote_log_values [$custom_field_id] ) ? $vote_log_values [$custom_field_id] : '';
							}
							$column_custom_fields_values [] = $logs ['vote_id'];
							$column_custom_fields_values [] = $logs ['tr_id'];
							$column_custom_fields_values [] = $logs ['vote_date'];
							if ( !fputcsv( $f, $column_custom_fields_values ) ){
								_e( "Can't write record!", 'yop_poll' );
							}
							$index++;
						}
					}
					fclose( $f ) or show_error( __( "Can't close php://output!", 'yop_poll' ) );
					$csvStr = ob_get_contents();
					ob_end_clean();

					echo $csvStr;
					exit ();
				}
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'export', 'a' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
			elseif ( !empty ( $_GET ['_wp_http_referer'] ) ) {
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'a' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
		}

		public function view_yop_poll_bans_operations() {
			global $page, $action;
			if ( '-1' != $action && isset ( $_REQUEST ['yoppollbanscheck'] ) ){
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-bans' );
					$bulkbans = ( array )$_REQUEST ['yoppollbanscheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					foreach ( $bulkbans as $ban_id ) {
						$ban_id = ( int )$ban_id;
						Yop_Poll_Model::delete_poll_ban_from_db( $ban_id );
					}
					wp_redirect( add_query_arg( 'deleted', count( $bulkbans ), remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'yoppollbanscheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( '-1' != $action && isset ( $_REQUEST ['id'] ) ) {
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-bans-delete' );
					$ban_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					Yop_Poll_Model::delete_poll_ban_from_db( $ban_id );
					wp_redirect( add_query_arg( 'deleted', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( 'add-ban' == $action ) {
				check_admin_referer( 'yop-poll-add-ban' );
				require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
				$bans = Yop_Poll_Model::add_bans( $_REQUEST );
				if ( $bans ['error'] != '' ){
					wp_redirect( add_query_arg( 'bans-error', urlencode( $bans ['error'] ), remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
				else {
					wp_redirect( add_query_arg( 'bans-added', urlencode( ( int )$bans ['success'] ), remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( !empty ( $_GET ['_wp_http_referer'] ) ) {
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
		}

		public function view_yop_poll_templates_operations() {
			global $page, $action, $yop_poll_add_new_config, $current_user;
			if ( '-1' != $action && isset ( $_REQUEST ['templatecheck'] ) ){
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-templates' );
					$bulktemplates = ( array )$_REQUEST ['templatecheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_deleted = 0;
					foreach ( $bulktemplates as $template_id ) {
						$template_id     = ( int )$template_id;
						$template_author = Yop_Poll_Model::get_poll_template_field_from_database_by_id( 'template_author', $template_id );
						if ( ( $this->current_user_can( 'delete_own_polls_templates' ) && $template_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_templates' ) ) ){
							Yop_Poll_Model::delete_poll_template_from_db( $template_id );
						}
						else {
							$total_deleted++;
						}
					}
					wp_redirect( add_query_arg( 'deleted', count( $bulktemplates ) - $total_deleted, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'templatecheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
				if ( 'clone' == $action ){
					check_admin_referer( 'yop-poll-templates' );
					$bulktemplates = ( array )$_REQUEST ['templatecheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_cloned = 0;
					foreach ( $bulktemplates as $template_id ) {
						$template_id     = ( int )$template_id;
						$template_author = Yop_Poll_Model::get_poll_template_field_from_database_by_id( 'template_author', $template_id );
						if ( ( $this->current_user_can( 'clone_own_polls_templates' ) && $template_author == $current_user->ID ) || ( $this->current_user_can( 'clone_polls_templates' ) ) ){
							Yop_Poll_Model::clone_poll_template( $template_id );
						}
						else {
							$total_cloned++;
						}
					}
					wp_redirect( add_query_arg( 'cloned', count( $bulktemplates ) - $total_cloned, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'templatecheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( '-1' != $action && isset ( $_REQUEST ['id'] ) ) {
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-templates' );
					$template_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$template_author = Yop_Poll_Model::get_poll_template_field_from_database_by_id( 'template_author', $template_id );
					if ( ( $this->current_user_can( 'delete_own_polls_templates' ) && $template_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_templates' ) ) ){
						Yop_Poll_Model::delete_poll_template_from_db( $template_id );
					}
					else {
						wp_die( __( 'You are not allowed to delete this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'deleted', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
				if ( 'clone' == $action ){
					check_admin_referer( 'yop-poll-templates' );
					$template_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$template_author = Yop_Poll_Model::get_poll_template_field_from_database_by_id( 'template_author', $template_id );
					if ( ( $this->current_user_can( 'clone_own_polls_templates' ) && $template_author == $current_user->ID ) || ( $this->current_user_can( 'clone_polls_templates' ) ) ){
						Yop_Poll_Model::clone_poll_template( $template_id );
					}
					else {
						wp_die( __( 'You are not allowed to clone this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'cloned', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( !empty ( $_GET ['_wp_http_referer'] ) ) {
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
		}

		public function view_all_polls_operations() {
			global $page, $action, $yop_poll_add_new_config, $current_user;
			if ( '-1' != $action && isset ( $_REQUEST ['yoppollcheck'] ) ){
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-view' );
					$bulkyoppolls = ( array )$_REQUEST ['yoppollcheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_undeleted = 0;
					foreach ( $bulkyoppolls as $yoppoll_id ) {
						$yoppoll_id  = ( int )$yoppoll_id;
						$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
						if ( ( $this->current_user_can( 'delete_own_polls' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls' ) ) ){
							Yop_Poll_Model::delete_poll_from_db( $yoppoll_id );
						}
						else {
							$total_undeleted++;
						}
					}
					wp_redirect( add_query_arg( 'deleted', count( $bulkyoppolls ) - $total_undeleted, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'yoppollcheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
				if ( 'clone' == $action ){
					check_admin_referer( 'yop-poll-view' );
					$bulkyoppolls = ( array )$_REQUEST ['yoppollcheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_uncloned = 0;
					foreach ( $bulkyoppolls as $yoppoll_id ) {
						$yoppoll_id  = ( int )$yoppoll_id;
						$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
						if ( ( $this->current_user_can( 'clone_own_polls' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'clone_polls' ) ) ){
							Yop_Poll_Model::clone_poll( $yoppoll_id );
						}
						else {
							$total_uncloned++;
						}
					}
					wp_redirect( add_query_arg( 'cloned', count( $bulkyoppolls ) - $total_uncloned, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'yoppollcheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
				if ( 'reset_votes' == $action ){
					check_admin_referer( 'yop-poll-view' );
					$bulkyoppolls = ( array )$_REQUEST ['yoppollcheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_unreseted = 0;
					foreach ( $bulkyoppolls as $yoppoll_id ) {
						$yoppoll_id  = ( int )$yoppoll_id;
						$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
						if ( ( $this->current_user_can( 'reset_own_polls_stats' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'reset_polls_stats' ) ) ){
							Yop_Poll_Model::reset_votes_for_poll( $yoppoll_id );
						}
						else {
							$total_unreseted++;
						}
					}
					wp_redirect( add_query_arg( 'reseted_votes', count( $bulkyoppolls ) - $total_unreseted, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'yoppollcheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
				if ( 'delete_logs' == $action ){
					check_admin_referer( 'yop-poll-view' );
					$bulkyoppolls = ( array )$_REQUEST ['yoppollcheck'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$total_undeleted_logs = 0;
					foreach ( $bulkyoppolls as $yoppoll_id ) {
						$yoppoll_id  = ( int )$yoppoll_id;
						$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
						if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
							Yop_Poll_Model::delete_all_poll_logs( $yoppoll_id );
						}
						else {
							$total_undeleted_logs++;
						}
					}
					wp_redirect( add_query_arg( 'deleted_logs', count( $bulkyoppolls ) - $total_undeleted_logs, remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'yoppollcheck' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( '-1' != $action && isset ( $_REQUEST ['id'] ) ) {
				if ( 'delete' == $action ){
					check_admin_referer( 'yop-poll-delete' );
					$yoppoll_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
					if ( ( $this->current_user_can( 'delete_own_polls' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls' ) ) ){
						Yop_Poll_Model::delete_poll_from_db( $yoppoll_id );
					}
					else {
						wp_die( __( 'You are not allowed to delete this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'deleted', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}

				if ( 'clone' == $action ){
					check_admin_referer( 'yop-poll-clone' );
					$yoppoll_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
					if ( ( $this->current_user_can( 'clone_own_polls' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'clone_polls' ) ) ){
						Yop_Poll_Model::clone_poll( $yoppoll_id );
					}
					else {
						wp_die( __( 'You are not allowed to clone this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'cloned', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}

				if ( 'reset_votes' == $action ){
					check_admin_referer( 'yop-poll-reset-votes' );
					$yoppoll_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
					if ( ( $this->current_user_can( 'reset_own_polls_stats' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'reset_polls_stats' ) ) ){
						Yop_Poll_Model::reset_votes_for_poll( $yoppoll_id );
					}
					else {
						wp_die( __( 'You are not allowed to reset stats for this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'reseted_votes', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}

				if ( 'delete_logs' == $action ){
					check_admin_referer( 'yop-poll-delete-logs' );
					$yoppoll_id = ( int )$_REQUEST ['id'];
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $yoppoll_id );
					if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
						Yop_Poll_Model::delete_all_poll_logs( $yoppoll_id );
					}
					else {
						wp_die( __( 'You are not allowed to delete logs for this item.', 'yop_poll' ) );
					}
					wp_redirect( add_query_arg( 'deleted_logs', 1, remove_query_arg( array( '_wpnonce', 'id', 'action' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) ) );
					exit ();
				}
			}
			elseif ( !empty ( $_GET ['_wp_http_referer'] ) ) {
				wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ) );
				exit ();
			}
		}

		/**
		* End operations section
		*/

		/**
		* Start Views section
		*/
		public function view_all_polls() {
			global $page, $action, $orderby, $order, $current_user;
			$orderby                    = ( empty ( $orderby ) ) ? 'name' : $orderby;
			$order_direction            = array( 'id' => 'asc', 'name' => 'asc', 'question' => 'asc', 'start_date' => 'asc', 'end_date' => 'asc', 'total_votes' => 'asc', 'total_answers' => 'asc' );
			$order_direction [$orderby] = ( 'desc' == $order ) ? 'asc' : 'desc';

			$order_direction_reverse            = array( 'id' => 'desc', 'name' => 'desc', 'question' => 'desc', 'start_date' => 'desc', 'end_date' => 'desc', 'total_votes' => 'desc', 'total_answers' => 'desc' );
			$order_direction_reverse [$orderby] = ( 'desc' == $order ) ? 'desc' : 'asc';

			$order_sortable            = array( 'id' => 'sortable', 'name' => 'sortable', 'question' => 'sortable', 'start_date' => 'sortable', 'end_date' => 'sortable', 'total_votes' => 'sortable', 'total_answers' => 'sortable' );
			$order_sortable [$orderby] = 'sorted';
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$filter = array( 'field' => NULL, 'value' => NULL, 'operator' => '=' );
			if ( isset ( $_REQUEST ['filters'] ) ){
				switch ( $_REQUEST ['filters'] ) {
					case 'started' :
						$filter = array( 'field' => 'start_date', 'value' => YOP_POLL_MODEL::get_mysql_curent_date(), 'operator' => '<=' );
						break;
					case 'not_started' :
						$filter = array( 'field' => 'start_date', 'value' => YOP_POLL_MODEL::get_mysql_curent_date(), 'operator' => '>=' );
						break;
					case 'never_expire' :
						$filter = array( 'field' => 'end_date', 'value' => '9999-12-31 23:59:59', 'operator' => '=' );
						break;
					case 'expired' :
						$filter = array( 'field' => 'end_date', 'value' => YOP_POLL_MODEL::get_mysql_curent_date(), 'operator' => '<=' );
						break;
				}
			}
			$search                  = array(
				'fields' => array( 'name', 'question' ),
				'value'  => isset ( $_REQUEST ['s'] ) ? $_REQUEST ['s'] : ''
			);
			$yop_polls               = Yop_Poll_Model::get_yop_polls_filter_search( $orderby, $order, $filter, $search );
			$optin_box_modal_options = get_option( 'yop_poll_optin_box_modal_options' );
			$optin_box_modal_query   = admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) );
			$optin_box_modal_query   = add_query_arg( 'action', 'yop_poll_show_optin_box_modal', $optin_box_modal_query );
		?>
		<?php if ( $optin_box_modal_options['show'] == 'yes' ){ ?>
			<a id="yop-poll-show-modal-box"
				href="<?php echo $optin_box_modal_query; ?>" style="display: none;"></a>
			<?php } ?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Yop Poll', 'yop_poll' ); ?><a class="add-new-h2"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls-add-new', 'id' => false, 'deleted' => false, 'cloned' => false ) ) ); ?>"><?php _e( 'Add New', 'yop_poll' ); ?></a>
			</h2>
			<?php
				if ( isset ( $_REQUEST ['deleted'] ) ){
					echo '<div id="message" class="updated"><p>';
					$deleted = ( int )$_REQUEST ['deleted'];
					printf( _n( '%s Poll deleted.', '%s Polls deleted.', $deleted ), $deleted );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<?php
				if ( isset ( $_REQUEST ['cloned'] ) ){
					echo '<div id="message" class="updated"><p>';
					$cloned = ( int )$_REQUEST ['cloned'];
					printf( _n( '%s Poll cloned.', '%s Polls cloned.', $cloned ), $cloned );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'cloned' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<?php
				if ( isset ( $_REQUEST ['reseted_votes'] ) ){
					echo '<div id="message" class="updated"><p>';
					$reseted_votes = ( int )$_REQUEST ['reseted_votes'];
					printf( _n( 'Vote reseted for %s Poll.', 'Votes reseted for %s Poll.', $reseted_votes ), $reseted_votes );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'reseted_votes' ), $_SERVER ['REQUEST_URI'] );
				}
			?>

			<?php
				if ( isset ( $_REQUEST ['deleted_logs'] ) ){
					echo '<div id="message" class="updated"><p>';
					$deleted_logs = ( int )$_REQUEST ['deleted_logs'];
					printf( _n( 'Log deleted for %s Poll.', 'Log deleted for %s Polls.', $deleted_logs ), $deleted_logs );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'deleted_logs' ), $_SERVER ['REQUEST_URI'] );
				}
			?>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<form action="" method="get">
						<div id="post-body-content">
							<?php wp_nonce_field( 'yop-poll-view' ); ?>
							<input type="hidden" name="order" value="<?php echo $order ?>"/>
							<input type="hidden" name="orderby" value="<?php echo $orderby ?>"/>
							<input type="hidden" name="page" value="yop-polls"/>
							<p class="search-box">
								<label class="screen-reader-text" for="yop-poll-search-input"><?php _e( 'Search Polls', 'yop_poll' ) ?></label>
								<input id="yop-poll-search-input" type="search"
									value="<?php if ( isset( $_REQUEST['s'] ) ): echo esc_html( stripslashes( $_REQUEST['s'] ) ); endif; ?>"
									name="s"/> <input id="search-submit" class="button"
									type="submit" value="<?php _e( 'Search Polls', 'yop_poll' ); ?>"
									name=""/>
							</p>
							<div class="tablenav top">
								<div class="alignleft actions">
									<select name="action">
										<option selected="selected" value="-1"><?php _e( 'Bulk Actions', 'yop_poll' ); ?></option>
										<option value="delete"><?php _e( 'Delete', 'yop_poll' ); ?></option>
										<option value="clone"><?php _e( 'Clone', 'yop_poll' ); ?></option>
										<option value="reset_votes"><?php _e( 'Reset Votes', 'yop_poll' ); ?></option>
										<option value="delete_logs"><?php _e( 'Delete Logs', 'yop_poll' ); ?></option>
									</select>
									<input type="submit"
										value="<?php _e( 'Apply', 'yop_poll' ); ?>"
										class="button-secondary action" id="doaction" name="">
								</div>
								<div class="alignleft actions">
									<select name="filters">
										<option value="0"><?php _e( 'View All Polls', 'yop_poll' ); ?></option>
										<option
											<?php echo isset( $_REQUEST['filters'] ) ? ( 'never_expire' == $_REQUEST['filters'] ? 'selected="selected"' : '' ) : '' ?>
											value="never_expire"><?php _e( 'No end date', 'yop_poll' ); ?></option>
										<option
											<?php echo isset( $_REQUEST['filters'] ) ? ( 'expired' == $_REQUEST['filters'] ? 'selected="selected"' : '' ) : '' ?>
											value="expired"><?php _e( 'Expired', 'yop_poll' ); ?></option>
										<option
											<?php echo isset( $_REQUEST['filters'] ) ? ( 'started' == $_REQUEST['filters'] ? 'selected="selected"' : '' ) : '' ?>
											value="started"><?php _e( 'Started', 'yop_poll' ); ?></option>
										<option
											<?php echo isset( $_REQUEST['filters'] ) ? ( 'not_started' == $_REQUEST['filters'] ? 'selected="selected"' : '' ) : '' ?>
											value="not_started"><?php _e( 'Not Started', 'yop_poll' ); ?></option>
									</select>
									<input type="submit"
										value="<?php _e( 'Filter', 'yop_poll' ); ?>"
										class="button-secondary" id="post-query-submit" name="">
								</div>
								<br class="clear">
							</div>
							<table class="wp-list-table widefat fixed" cellspacing="0">
								<thead>
									<tr>
										<th id="cb" class="manage-column column-cb check-column"
											style="width: 3%;" scope="col"><input type="checkbox"></th>
										<th id="name"
											class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
											style="width: 30%" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
												<span><?php _e( 'Name', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="total_votes"
											class="manage-column <?php echo $order_sortable['total_votes'] ?> <?php echo $order_direction_reverse['total_votes'] ?>"
											style="width: 6%" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'total_votes', 'order' => $order_direction['total_votes'] ) ) ); ?>">
												<span><?php _e( 'Total Votes', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="total_answers"
											class="manage-column <?php echo $order_sortable['total_answers'] ?> <?php echo $order_direction_reverse['total_answers'] ?>"
											style="width: 7%" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'total_answers', 'order' => $order_direction['total_answers'] ) ) ); ?>">
												<span><?php _e( 'Total Answers', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="question"
											class="manage-column <?php echo $order_sortable['question'] ?> <?php echo $order_direction_reverse['question'] ?>"
											style="width: 24%" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'question', 'order' => $order_direction['question'] ) ) ); ?>">
												<span><?php _e( 'Question', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="poll_author" class="manage-column" style="width: 8%"
											scope="col"><span><?php _e( 'Author', 'yop_poll' ); ?></span></th>
										<th id="start-date"
											class="manage-column <?php echo $order_sortable['start_date'] ?> <?php echo $order_direction_reverse['start_date'] ?>"
											style="width: 10%" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'start_date', 'order' => $order_direction['start_date'] ) ) ); ?>">
												<span><?php _e( 'Start Date', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="end-date"
											class="manage-column <?php echo $order_sortable['end_date'] ?> <?php echo $order_direction_reverse['end_date'] ?>"
											style="width: 10%" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'end_date', 'order' => $order_direction['end_date'] ) ) ); ?>">
												<span><?php _e( 'End Date', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
									</tr>
								</thead>
								<?php
									if ( count( $yop_polls ) > 0 ){
										foreach ( $yop_polls as $yop_poll ) {
										?>
										<tbody id="the-list">
											<tr valign="middle" class="alternate"
												id="yop-poll-<?php echo $yop_poll['id']; ?>">
												<th class="check-column" scope="row">
													<?php if ( ( $this->current_user_can( 'delete_own_polls' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'delete_polls' ) ) ){ ?>
														<input type="checkbox"
															value="<?php echo $yop_poll['id']; ?>" name="yoppollcheck[]">
														<?php } ?>
												</th>
												<td><strong>
														<?php if (( $this->current_user_can( 'edit_own_polls' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls' ) )) { ?>
															<a
																title="<?php echo esc_html( stripslashes( $yop_poll['name'] ) ); ?>"
																href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'id' => $yop_poll['id'] ) ) ); ?>"
																class="row-title">
																<?php } ?>
															<?php echo esc_html( stripslashes( $yop_poll['name'] ) ); ?>
															<?php if (( $this->current_user_can( 'edit_own_polls' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls' ) )) { ?>
															</a>
															<?php } ?>
													</strong><br>
													<div class="row-actions">
														<?php if ( ( $this->current_user_can( 'edit_own_polls' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls' ) ) ){ ?>
															<span class="edit"><a
																	href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'id' => $yop_poll['id'] ) ) ); ?>"><?php _e( 'Edit', 'yop_poll' ) ?></a>
																| </span>
															<?php } ?>
														<?php if ( ( $this->current_user_can( 'view_own_polls_logs' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'view_polls_logs' ) ) ){ ?>
															<span class="edit"><a
																	href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls-logs', 'poll_id' => $yop_poll['id'] ) ) ); ?>"><?php _e( 'Logs', 'yop_poll' ) ?></a>
																| </span>
															<?php } ?>
														<?php if ( ( $this->current_user_can( 'delete_own_polls' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'delete_polls' ) ) ){ ?>
															<span class="delete"><a
																	onclick="if ( confirm( '<?php echo __( "You are about to delete this poll", 'yop_poll' ) . ": \'" . esc_html( $yop_poll['name'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to delete', 'yop_poll' ); ?>' ) ) { return true;}return false;"
																	href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'id' => $yop_poll['id'] ) ), 'yop-poll-delete' ); ?>"
																	class="submitdelete"><?php _e( 'Delete', 'yop_poll' ) ?></a>
																| </span>
															<?php } ?>
														<?php if ( ( $this->current_user_can( 'clone_own_polls' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'clone_polls' ) ) ){ ?>
															<span class="clone"><a
																	onclick="if ( confirm( '<?php echo __( "You are about to clone this poll", 'yop_poll' ) . ": \'" . esc_html( $yop_poll['name'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to clone', 'yop_poll' ); ?>' ) ) { return true;}return false;"
																	href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'clone', 'id' => $yop_poll['id'] ) ), 'yop-poll-clone' ); ?>"
																	class="submitclone"><?php _e( 'Clone', 'yop_poll' ) ?></a> |
															</span>
															<?php } ?>
														<?php if ( ( $this->current_user_can( 'view_own_polls_results' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'view_polls_results' ) ) ){ ?>
															<span class="results"><a
																	href="<?php echo esc_url( add_query_arg( array( 'action' => 'results', 'id' => $yop_poll['id'] ) ) ); ?>"><?php _e( 'Results', 'yop_poll' ) ?></a>
																| </span>
															<?php } ?>
														<?php if ( ( $this->current_user_can( 'reset_own_polls_stats' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'reset_polls_stats' ) ) ){ ?>
															<span class="delete"><a
																	onclick="if ( confirm( '<?php echo __( "You are about to reset votes for this poll", 'yop_poll' ) . ": \'" . esc_html( $yop_poll['name'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to reset votes', 'yop_poll' ); ?>' ) ) { return true;}return false;"
																	href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'reset_votes', 'id' => $yop_poll['id'] ) ), 'yop-poll-reset-votes' ); ?>"
																	class="submitresetvotes"><?php _e( 'Reset Stats', 'yop_poll' ) ?></a>
																| </span>
															<?php } ?>
														<?php if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $yop_poll['poll_author'] == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){ ?>
															<span class="delete"><a
																	onclick="if ( confirm( '<?php echo __( "You are about to delete logs for this poll", 'yop_poll' ) . ": \'" . esc_html( $yop_poll['name'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to delete logs', 'yop_poll' ); ?>' ) ) { return true;}return false;"
																	href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'delete_logs', 'id' => $yop_poll['id'] ) ), 'yop-poll-delete-logs' ); ?>"
																	class="submitresetvotes"><?php _e( 'Delete Logs', 'yop_poll' ) ?></a></span>
															<?php } ?>
													</div></td>
												<td>
													<?php echo esc_html( stripslashes( $yop_poll['total_votes'] ) ); ?>
												</td>
												<td>
													<?php echo esc_html( stripslashes( $yop_poll['total_answers'] ) ); ?>
												</td>
												<td>
													<?php echo esc_html( stripslashes( $yop_poll['question'] ) ); ?>
												</td>
												<td>
													<?php
														$user_info = get_userdata( $yop_poll ['poll_author'] );
														if ( $user_info ){
															echo esc_html( stripslashes( $user_info->user_login ) );
														}
														else {
															echo '';
														}
													?>
												</td>
												<td>
													<?php echo esc_html( stripslashes( $yop_poll['start_date'] ) ); ?>
												</td>
												<td>
													<?php
														if ( YOP_POLL_MODEL::get_mysql_curent_date() > $yop_poll ['end_date'] ){
															echo '<font style="color:#CC0000;"><b>';
														}
														echo ( '9999-12-31 23:59:59' == $yop_poll ['end_date'] ) ? __( 'No end date', 'yop_poll' ) : esc_html( stripslashes( $yop_poll ['end_date'] ) );
														if ( YOP_POLL_MODEL::get_mysql_curent_date() > $yop_poll ['end_date'] ){
															echo '</b></font>';
													}?>
												</td>
											</tr>
										</tbody>
										<?php
										}
									}
									else {
									?>
									<tbody id="the-list">
										<tr valign="middle" class="alternate" id="yop-poll-<?php ?>">
											<th colspan="8">
												<?php _e( 'No poll found!', 'yop_poll' ); ?>
											</th>
										</tr>
									</tbody>
									<?php
									}
								?>

								<tfoot>
									<tr>
										<th id="cb" class="manage-column column-cb check-column"
											style="" scope="col"><input type="checkbox"></th>
										<th id="name"
											class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
											style="" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
												<span><?php _e( 'Name', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="total_votes"
											class="manage-column <?php echo $order_sortable['total_votes'] ?> <?php echo $order_direction_reverse['total_votes'] ?>"
											style="" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'total_votes', 'order' => $order_direction['total_votes'] ) ) ); ?>">
												<span><?php _e( 'Total Votes', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="total_answers"
											class="manage-column <?php echo $order_sortable['total_answers'] ?> <?php echo $order_direction_reverse['total_answers'] ?>"
											style="" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'total_answers', 'order' => $order_direction['total_answers'] ) ) ); ?>">
												<span><?php _e( 'Total Answers', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="question"
											class="manage-column <?php echo $order_sortable['question'] ?> <?php echo $order_direction_reverse['question'] ?>"
											style="" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'question', 'order' => $order_direction['question'] ) ) ); ?>">
												<span><?php _e( 'Question', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="poll_author" class="manage-column" style="width: 5%"
											scope="col"><span><?php _e( 'Author', 'yop_poll' ); ?></span></th>
										<th id="start-date"
											class="manage-column <?php echo $order_sortable['start_date'] ?> <?php echo $order_direction_reverse['start_date'] ?>"
											style="" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'start_date', 'order' => $order_direction['start_date'] ) ) ); ?>">
												<span><?php _e( 'Start Date', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
										<th id="end-date"
											class="manage-column <?php echo $order_sortable['end_date'] ?> <?php echo $order_direction_reverse['end_date'] ?>"
											style="" scope="col"><a
												href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'end_date', 'order' => $order_direction['end_date'] ) ) ); ?>">
												<span><?php _e( 'End Date', 'yop_poll' ); ?></span> <span
													class="sorting-indicator"></span>
											</a></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</form>
					<?php $this->yop_poll_donate(); ?>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
		}

		public function view_poll_results() {
			global $page, $action, $current_user;
			$poll_id          = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
			$results_order_by = ( isset ( $_GET ['results_order_by'] ) ? $_GET ['results_order_by'] : 'id' );
			$results_order    = ( isset ( $_GET ['results_order'] ) ? $_GET ['results_order'] : 'ASC' );
			$soav             = ( isset ( $_GET ['soav'] ) ? $_GET ['soav'] : 'no' );
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
			if ( ( !$this->current_user_can( 'view_own_polls_results' ) || $poll_author != $current_user->ID ) && ( !$this->current_user_can( 'view_polls_results' ) ) ){
				wp_die( __( 'You are not allowed to view results for this item.', 'yop_poll' ) );
			}
			$poll_details = YOP_POLL_MODEL::get_poll_from_database_by_id( $poll_id );
			if ( 'yes' == $soav ){
				$display_other_answers_values = true;
			}
			else {
				$display_other_answers_values = false;
			}
			$poll_answers      = YOP_POLL_MODEL::get_poll_answers( $poll_id, array( 'default', 'other' ), $results_order_by, $results_order, $display_other_answers_values );
			$poll_other_answer = YOP_POLL_MODEL::get_poll_answers( $poll_id, array( 'other' ) );

			// other-answers
			$oa_per_page                    = ( isset ( $_GET ['oa_per_page'] ) ? intval( $_GET ['oa_per_page'] ) : 100 );
			$oa_page_no                     = ( isset ( $_REQUEST ['oa_page_no'] ) ? ( int )$_REQUEST ['oa_page_no'] : 1 );
			$total_logs_other_answers       = count( YOP_POLL_MODEL::get_other_answers_votes( isset ( $poll_other_answer [0] ['id'] ) ? $poll_other_answer [0] ['id'] : 0 ) );
			$total_logs_other_answers_pages = ceil( $total_logs_other_answers / $oa_per_page );
			if ( intval( $oa_page_no ) > intval( $total_logs_other_answers_pages ) ){
				$oa_page_no = 1;
			}
			$logs_other_answers = YOP_POLL_MODEL::get_other_answers_votes( isset ( $poll_other_answer [0] ['id'] ) ? $poll_other_answer [0] ['id'] : 0, ( $oa_page_no - 1 ) * $oa_per_page, $oa_per_page );

			$oa_args       = array( 'base' => remove_query_arg( 'oa_page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&oa_page_no=%#%', 'total' => $total_logs_other_answers_pages, 'current' => max( 1, $oa_page_no ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
			$oa_pagination = paginate_links( $oa_args );
			// other-answers

			// custom-fields
			$cf_per_page                    = ( isset ( $_GET ['cf_per_page'] ) ? intval( $_GET ['cf_per_page'] ) : 100 );
			$cf_page_no                     = ( isset ( $_REQUEST ['cf_page_no'] ) ? ( int )$_REQUEST ['cf_page_no'] : 1 );
			$cf_sdate                       = ( isset ( $_GET ['cf_sdate'] ) ? $_GET ['cf_sdate'] : '' );
			$cf_edate                       = ( isset ( $_GET ['cf_edate'] ) ? $_GET ['cf_edate'] : '' );
			$poll_custom_fields             = YOP_POLL_MODEL::get_poll_customfields( $poll_id );
			$custom_fields_number           = count( $poll_custom_fields );
			$total_custom_fields_logs       = YOP_POLL_MODEL::get_poll_total_customfields_logs( $poll_id, $cf_sdate, $cf_edate );
			$total_custom_fields_logs_pages = ceil( $total_custom_fields_logs / $cf_per_page );
			if ( intval( $cf_page_no ) > intval( $total_custom_fields_logs_pages ) ){
				$cf_page_no = 1;
			}
			$custom_fields_logs = YOP_POLL_MODEL::get_poll_customfields_logs( $poll_id, 'vote_id', 'asc', ( $cf_page_no - 1 ) * $cf_per_page, $cf_per_page, $cf_sdate, $cf_edate );

			$column_custom_fields_ids = array();
			$cf_args                  = array( 'base' => remove_query_arg( 'cf_page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&cf_page_no=%#%', 'total' => $total_custom_fields_logs_pages, 'current' => max( 1, $cf_page_no ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
			$cf_pagination            = paginate_links( $cf_args );
			// custom-fields
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Yop Poll Results', 'yop_poll' ); ?><a class="add-new-h2"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls' ), remove_query_arg( array( 'action', 'id' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) ) ); ?>"><?php _e( 'All Yop Polls', 'yop_poll' ); ?></a>
			</h2>
			<?php
				if ( $poll_details ){
				?>
				<h3>Name: <?php echo esc_html( stripslashes( $poll_details['name'] ) ) ?></h3>
				<h4>Question: <?php echo esc_html( stripslashes( $poll_details['question'] ) ) ?></h4>
				<form method="get">
					<input type="hidden" name="page" value="yop-polls"/>
					<input type="hidden" name="action" value="results"/>
					<input type="hidden" name="id" value="<?php echo $poll_id; ?>"/>
					<input type="hidden" name="oa_page_no"
						value="<?php echo $oa_page_no; ?>"/>
					<input type="hidden" name="cf_page_no"
						value="<?php echo $cf_page_no; ?>"/>
					<input type="hidden" name="oa_per_page"
						value="<?php echo $oa_per_page; ?>"/>

					<div class="tablenav top">
						<div class="alignleft actions">
							<div style="display:inline; float:left; margin:7px;"><?php _e( 'Order By', 'yop_poll' ); ?></div>
							<select name="results_order_by">
								<option <?php selected( $results_order_by, 'id' ) ?> value="id"><?php _e( 'Answer ID', 'yop_poll' ); ?></option>
								<option <?php selected( $results_order_by, 'answer' ) ?> value="answer"><?php _e( 'Answer Value', 'yop_poll' ); ?></option>
								<option <?php selected( $results_order_by, 'votes' ) ?> value="votes"><?php _e( 'Votes', 'yop_poll' ); ?></option>
							</select>
							<select name="results_order">
								<option <?php selected( $results_order, 'ASC' ) ?> value="ASC"><?php _e( 'ASC', 'yop_poll' ); ?></option>
								<option <?php selected( $results_order, 'DESC' ) ?> value="DESC"><?php _e( 'DESC', 'yop_poll' ); ?></option>
							</select>
							&nbsp;| &nbsp;
							<input type="checkbox" value="yes" <?php checked( $soav, 'yes' ); ?> name="soav" id="yop-poll-show_other_answers_values"/>
							<label for="yop-poll-show_other_answers_values"><?php _e( 'Show Other Answers Values', 'yop_poll' ); ?></label>
							<input type="submit"
								value="<?php _e( 'Filter', 'yop_poll' ); ?>"
								class="button-secondary action" id="doaction" name="a">
						</div>
						<br class="clear">
					</div>
				</form>
				<table class="wp-list-table widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th id="" class="column-answer" style="width: 40%;" scope="col"><?php _e( 'Answer', 'yop_poll' ); ?></th>
							<th id="" class="column-votes" style="width: 5%;" scope="col"><?php _e( 'Votes', 'yop_poll' ); ?></th>
							<th id="" class="column-percent" style="width: 5%;" scope="col"><?php _e( 'Percent', 'yop_poll' ); ?></th>
							<th id="" class="column-bar" style="width: 45%;" scope="col"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if ( count( $poll_answers ) > 0 ){
								foreach ( $poll_answers as $answer ) {
								?>
								<tr>
									<th><?php echo esc_html( stripslashes( $answer['answer'] ) ); ?></th>
									<td><?php echo esc_html( stripslashes( $answer['votes'] ) ); ?></td>
									<td><?php echo esc_html( stripslashes( $answer['procentes'] ) ); ?>%</td>
									<td><span class="yop-poll-admin-result-bar" style="width: <?php echo esc_html( stripslashes( $answer['procentes'] ) ); ?>%;">
										</span></td>
								</tr>
								<?php
								}
							}
							else {
							?>
							<tr>
								<th colspan="4"><?php _e( 'No answers defined!', 'yop_poll' ); ?></th>
							</tr>
							<?php
							}
						?>
					</tbody>
				</table>
				<br> <br>
				<div style="width: 30%; float: left;">
					<h3><?php _e( 'Poll Other Answers', 'yop_poll' ); ?></h3>
					<form method="get">
						<input type="hidden" name="page" value="yop-polls"/>
						<input type="hidden" name="action" value="results"/>
						<input type="hidden" name="id" value="<?php echo $poll_id; ?>"/>
						<input type="hidden" name="cf_page_no"
							value="<?php echo $cf_page_no; ?>"/>
						<input type="hidden" name="oa_page_no"
							value="<?php echo $oa_page_no; ?>"/>
						<input type="hidden" name="cf_per_page"
							value="<?php echo $cf_per_page; ?>"/>
						<input type="hidden" name="results_order_by" value="<?php echo $results_order_by; ?>"/>
						<input type="hidden" name="results_order" value="<?php echo $results_order; ?>"/>
						<input type="hidden" name="soav" value="<?php echo $soav; ?>"/>
						<div class="tablenav top">
							<div class="tablenav-pages one-page">
								<label for="yop-poll-oa-items-per-page" class="displaying-num"><?php _e( 'Items Per Page', 'yop_poll' ); ?>
									:</label><input
									id="yop-poll-oa-items-per-page" type="text" name="oa_per_page"
									value="<?php echo $oa_per_page; ?>"/> <input name="a"
									value="<?php _e( 'Set', 'yop_poll' ); ?>" type="submit"/>&nbsp;&nbsp;<span
									class="displaying-num"><?php echo count( $logs_other_answers ); ?>
									/ <?php echo $total_logs_other_answers; ?> items</span>
								<?php print $oa_pagination; ?>
							</div>
							<br class="clear">
						</div>
						<table class="wp-list-table widefat fixed" cellspacing="0">
							<thead>
								<tr>
									<th id="" class="column-answer" style="width: 40%;" scope="col"><?php _e( 'Other Answers', 'yop_poll' ); ?></th>
									<th id="" class="column-votes" style="width: 5%;" scope="col"><?php _e( 'Votes', 'yop_poll' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ( count( $logs_other_answers ) > 0 ){
										foreach ( $logs_other_answers as $answer ) {
										?>
										<tr>
											<td><?php echo esc_html( stripslashes( $answer['other_answer_value'] ) ); ?></td>
											<td><?php echo esc_html( stripslashes( $answer['votes'] ) ); ?></td>
										</tr>
										<?php
										}
									}
									else {
									?>
									<tr>
										<td colspan="2"><?php _e( 'No other answers defined!', 'yop_poll' ); ?></td>
									</tr>
									<?php
									}
								?>
							</tbody>
						</table>
						<div class="tablenav top">
							<div class="tablenav-pages one-page">
								<?php print $oa_pagination; ?>
							</div>
						</div>
						<br class="clear">
					</form>
				</div>
				<div style="width: 69%; float: right;">
					<h3><?php _e( 'Custom Fields', 'yop_poll' ); ?></h3>
					<form method="get">
						<?php wp_nonce_field( 'yop-poll-custom-fields' ); ?>
						<input type="hidden" name="page" value="yop-polls"/>
						<input type="hidden" name="action" value="results"/>
						<input type="hidden" name="id" value="<?php echo $poll_id; ?>"/>
						<input type="hidden" name="oa_page_no"
							value="<?php echo $oa_page_no; ?>"/>
						<input type="hidden" name="cf_page_no"
							value="<?php echo $cf_page_no; ?>"/>
						<input type="hidden" name="oa_per_page"
							value="<?php echo $oa_per_page; ?>"/>
						<input type="hidden" name="results_order_by" value="<?php echo $results_order_by; ?>"/>
						<input type="hidden" name="results_order" value="<?php echo $results_order; ?>"/>
						<input type="hidden" name="soav" value="<?php echo $soav; ?>"/>

						<div class="tablenav top">
							<div class="alignleft actions">
								<select name="export">
									<option value="page"><?php _e( 'This Page', 'yop_poll' ); ?></option>
									<option value="all"><?php _e( 'All Pages', 'yop_poll' ); ?></option>
								</select> <input type="submit"
									value="<?php _e( 'Export', 'yop_poll' ); ?>"
									class="button-secondary action" id="doaction" name="a">
								&nbsp;&nbsp;&nbsp; <label
									for="yop-poll-custom-field-start-date-input"><?php _e( 'Start Date', 'yop_poll' ); ?>
									:</label>
								<input id="yop-poll-custom-field-start-date-input" type="text"
									name="cf_sdate" value="<?php echo $cf_sdate; ?>"/>&nbsp;&nbsp; <label
									for="yop-poll-custom-field-end-date-input"><?php _e( 'End Date', 'yop_poll' ); ?>
									:</label>
								<input id="yop-poll-custom-field-end-date-input" type="text"
									name="cf_edate" value="<?php echo $cf_edate; ?>"/>&nbsp;&nbsp; <input
									value="<?php _e( 'Filter', 'yop_poll' ); ?>" type="submit"
									name="a"/>
							</div>
							<div class="tablenav-pages one-page">
								<label for="yop-poll-items-per-page" class="displaying-num"><?php _e( 'Items Per Page', 'yop_poll' ); ?>
									:</label><input
									id="yop-poll-items-per-page" type="text" name="cf_per_page"
									value="<?php echo $cf_per_page; ?>"/> <input name="a"
									value="<?php _e( 'Set', 'yop_poll' ); ?>" type="submit"/>&nbsp;&nbsp;<span
									class="displaying-num"><?php echo count( $custom_fields_logs ); ?>
									/ <?php echo $total_custom_fields_logs; ?> items</span>
								<?php print $cf_pagination; ?>
							</div>
							<br class="clear">
						</div>
						<table class="wp-list-table widefat fixed" cellspacing="0">
							<thead>
								<tr>
									<th id="" class="column-answer" style="width: 5%" scope="col"><?php _e( '#', 'yop_poll' ); ?></th>
									<?php
										foreach ( $poll_custom_fields as $custom_field ) {
											$column_custom_fields_ids [] = $custom_field ['id'];
										?>
										<th id="custom_field_<?php echo $custom_field['id']; ?>" class="column-custom-field" style="width:<?php echo intval( 80 / intval( $custom_fields_number ) ); ?>%" scope="col"><?php echo ucfirst( $custom_field['custom_field'] ); ?></th>
										<?php
										}
									?>
									<th id="" class="column-vote-id" style="width:20%"
										scope="col"><?php _e( 'Vote ID', 'yop_poll' ); ?></th>
									<th id="" class="column-tr-id" style="width:15%"
										scope="col"><?php _e( 'Tracking ID', 'yop_poll' ); ?></th>
									<th id="" class="column-vote-date" style="width:15%"
										scope="col"><?php _e( 'Vote Date', 'yop_poll' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ( count( $custom_fields_logs ) > 0 ){
										$index = ( $cf_page_no - 1 ) * $cf_per_page + 1;
										foreach ( $custom_fields_logs as $logs ) {
										?>
										<tr>
											<td><?php echo $index; ?></td>
											<?php
												foreach ( $column_custom_fields_ids as $custom_field_id ) {
													$vote_log_values = array();
													$vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
													if ( count( $vote_logs ) > 0 ){
														foreach ( $vote_logs as $vote_log ) {
															$temp                        = explode( '<#!->', $vote_log );
															$vote_log_values [$temp [1]] = stripslashes( $temp [0] );
														}
													}
												?>
												<td><?php echo isset( $vote_log_values[$custom_field_id] ) ? $vote_log_values[$custom_field_id] : ''; ?></td>
												<?php
												}
											?>
											<td><?php echo $logs['vote_id']; ?></td>
											<td><?php echo $logs['tr_id']; ?></td>
											<td><?php echo $logs['vote_date']; ?></td>
										</tr>
										<?php
											$index++;
										}
									}
								?>
							</tbody>
						</table>
						<div class="tablenav top">
							<div class="tablenav-pages one-page">
								<?php print $cf_pagination; ?>
							</div>
							<br class="clear">
						</div>
					</form>
				</div>
				<div style="clear: both;"></div>
			</div>
			<?php
			}
			else {
			?>
			<h3><?php _e( 'Your poll doesn`t exist!', 'yop_poll' ); ?></h3>
			<?php
			}
		}

		public function view_poll_custom_fields() {
			global $page, $action;
			$per_page = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
			$page_no  = ( isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1 );
			$poll_id  = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
			$sdate    = ( isset ( $_GET ['sdate'] ) ? $_GET ['sdate'] : '' );
			$edate    = ( isset ( $_GET ['edate'] ) ? $_GET ['edate'] : '' );
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$poll_details                   = YOP_POLL_MODEL::get_poll_from_database_by_id( $poll_id );
			$poll_custom_fields             = YOP_POLL_MODEL::get_poll_customfields( $poll_id );
			$custom_fields_number           = count( $poll_custom_fields );
			$total_custom_fields_logs       = YOP_POLL_MODEL::get_poll_total_customfields_logs( $poll_id, $sdate, $edate );
			$total_custom_fields_logs_pages = ceil( $total_custom_fields_logs / $per_page );
			if ( intval( $page_no ) > intval( $total_custom_fields_logs_pages ) ){
				$page_no = 1;
			}
			$custom_fields_logs = YOP_POLL_MODEL::get_poll_customfields_logs( $poll_id, 'vote_id', 'asc', ( $page_no - 1 ) * $per_page, $per_page, $sdate, $edate );

			$column_custom_fields_ids = array();
			$args                     = array( 'base' => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&page_no=%#%', 'total' => $total_custom_fields_logs_pages, 'current' => max( 1, $page_no ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
			$pagination               = paginate_links( $args );
			$_SERVER ['REQUEST_URI']  = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Custom Fields', 'yop_poll' ); ?><a class="add-new-h2"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls' ), remove_query_arg( array( 'action', 'id' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) ) ); ?>"><?php _e( 'All Yop Polls', 'yop_poll' ); ?></a>
			</h2>
			<?php
				if ( $poll_details ){
					if ( $poll_custom_fields ){
					?>
					<h3>Name: <?php echo esc_html( stripslashes( $poll_details['name'] ) ) ?></h3>
					<h4>Question: <?php echo esc_html( stripslashes( $poll_details['question'] ) ) ?></h4>
					<form method="get">
						<?php wp_nonce_field( 'yop-poll-custom-fields' ); ?>
						<input type="hidden" name="page" value="yop-polls"/>
						<input type="hidden" name="action" value="custom-fields"/>
						<input type="hidden" name="id" value="<?php echo $poll_id; ?>"/>
						<input type="hidden" name="page_no" value="<?php echo $page_no; ?>"/>
						<table cellspacing="5" align=" center">
							<tbody>
								<tr>
									<th><label for="yop-poll-custom-field-start-date-input"><?php _e( 'Start Date', 'yop_poll' ); ?>
											:</label>
									</th>
									<td><input id="yop-poll-custom-field-start-date-input" type="text"
											name="sdate" value="<?php echo $sdate; ?>"/></td>
								</tr>
								<tr>
									<th><label for="yop-poll-custom-field-end-date-input"><?php _e( 'End Date', 'yop_poll' ); ?>
											:</label>
									</th>
									<td><input id="yop-poll-custom-field-end-date-input" type="text"
											name="edate" value="<?php echo $edate; ?>"/></td>
								</tr>
								<tr>
									<th colspan="2"><input value="<?php _e( 'Filter', 'yop_poll' ); ?>"
											type="submit" name="a"/></th>
								</tr>
							</tbody>
						</table>
						<div class="tablenav top">
							<div class="alignleft actions">
								<select name="export">
									<option selected="selected" value=""><?php _e( 'Do Not Export', 'yop_poll' ); ?></option>
									<option value="page"><?php _e( 'This Page', 'yop_poll' ); ?></option>
									<option value="all"><?php _e( 'All Pages', 'yop_poll' ); ?></option>
								</select> <input type="submit"
									value="<?php _e( 'Export', 'yop_poll' ); ?>"
									class="button-secondary action" id="doaction" name="a">
							</div>
							<div class="tablenav-pages one-page">
								<label for="yop-poll-items-per-page" class="displaying-num"><?php _e( 'Items Per Page', 'yop_poll' ); ?>
									:</label><input
									id="yop-poll-items-per-page" type="text" name="per_page"
									value="<?php echo $per_page; ?>"/> <input name="a"
									value="<?php _e( 'Set', 'yop_poll' ); ?>" type="submit"/>&nbsp;&nbsp;<span
									class="displaying-num"><?php echo count( $custom_fields_logs ); ?>
									/ <?php echo $total_custom_fields_logs; ?> items</span>
								<?php print $pagination; ?>
							</div>
							<br class="clear">
						</div>
						<table class="wp-list-table widefat fixed" cellspacing="0">
							<thead>
								<tr>
									<th id="" class="column-answer" style="width: 5%" scope="col"><?php _e( '#', 'yop_poll' ); ?></th>
									<?php
										foreach ( $poll_custom_fields as $custom_field ) {
											$column_custom_fields_ids [] = $custom_field ['id'];
										?>
										<th id="custom_field_<?php echo $custom_field['id']; ?>" class="column-custom-field" style="width:<?php echo intval( 80 / intval( $custom_fields_number ) ); ?>%" scope="col"><?php echo ucfirst( $custom_field['custom_field'] ); ?></th>
										<?php
										}
									?>
									<th id="" class="column-vote-date" style="width: 15%"
										scope="col"><?php _e( 'Vote Date', 'yop_poll' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ( count( $custom_fields_logs ) > 0 ){
										$index = ( $page_no - 1 ) * $per_page + 1;
										foreach ( $custom_fields_logs as $logs ) {
										?>
										<tr>
											<td><?php echo $index; ?></td>
											<?php
												foreach ( $column_custom_fields_ids as $custom_field_id ) {
													$vote_log_values = array();
													$vote_logs       = explode( '<#!,>', $logs ['vote_log'] );
													if ( count( $vote_logs ) > 0 ){
														foreach ( $vote_logs as $vote_log ) {
															$temp                        = explode( '<#!->', $vote_log );
															$vote_log_values [$temp [1]] = stripslashes( $temp [0] );
														}
													}
												?>
												<td><?php echo isset( $vote_log_values[$custom_field_id] ) ? $vote_log_values[$custom_field_id] : ''; ?></td>
												<?php
												}
											?>
											<td><?php echo $logs['vote_date']; ?></td>
										</tr>
										<?php
											$index++;
										}
									}
								?>
							</tbody>
						</table>
						<div class="tablenav top">
							<div class="tablenav-pages one-page">
								<?php print $pagination; ?>
							</div>
							<br class="clear">
						</div>
					</form>
				</div>
				<?php
				}
				else {
				?>
				<h3><?php _e( 'This poll doesn\'t have set custom fields!', 'yop_poll' ); ?></h3>
				<?php
				}
			}
			else {
			?>
			<h3><?php _e( 'Your Poll doesn`t exist!', 'yop_poll' ); ?></h3>
			<?php
			}
		}

		public function view_yop_poll_templates() {
			global $page, $action, $orderby, $order, $current_user;
			$orderby                    = ( empty ( $orderby ) ) ? 'last_modified' : $orderby;
			$order                      = ( empty ( $order ) ) ? 'desc' : $order;
			$order_direction            = array( 'id' => 'asc', 'name' => 'asc', 'last_modified' => 'desc' );
			$order_direction [$orderby] = ( 'desc' == $order ) ? 'asc' : 'desc';

			$order_direction_reverse            = array( 'id' => 'desc', 'name' => 'desc', 'last_modified' => 'desc' );
			$order_direction_reverse [$orderby] = ( 'desc' == $order ) ? 'desc' : 'asc';

			$order_sortable            = array( 'id' => 'sortable', 'name' => 'sortable', 'last_modified' => 'sortable' );
			$order_sortable [$orderby] = 'sorted';
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$search             = array( 'fields' => array( 'name', 'last_modified' ), 'value' => isset ( $_REQUEST ['s'] ) ? $_REQUEST ['s'] : '' );
			$yop_poll_templates = Yop_Poll_Model::get_yop_poll_templates_search( $orderby, $order, $search );
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Yop Poll Templates', 'yop_poll' ); ?><a
					class="add-new-h2"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls-templates', 'action' => 'add-new', 'id' => false, 'deleted' => false, 'cloned' => false ) ) ); ?>"><?php _e( 'Add New', 'yop_poll' ); ?></a>
			</h2>
			<?php
				if ( isset ( $_REQUEST ['deleted'] ) ){
					echo '<div id="message" class="updated"><p>';
					$deleted = ( int )$_REQUEST ['deleted'];
					printf( _n( '%s Poll template deleted.', '%s Poll templates deleted.', $deleted ), $deleted );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<?php
				if ( isset ( $_REQUEST ['cloned'] ) ){
					echo '<div id="message" class="updated"><p>';
					$cloned = ( int )$_REQUEST ['cloned'];
					printf( _n( '%s Poll template cloned.', '%s Poll templates cloned.', $cloned ), $cloned );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'cloned' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'yop-poll-templates' ); ?>
				<input type="hidden" name="order" value="<?php echo $order ?>"/>
				<input type="hidden" name="orderby" value="<?php echo $orderby ?>"/>
				<input type="hidden" name="page" value="yop-polls-templates"/>
				<p class="search-box">
					<label class="screen-reader-text" for="yop-poll-search-input"><?php _e( 'Search Polls', 'yop_poll' ) ?></label>
					<input id="yop-poll-search-input" type="search"
						value="<?php if ( isset( $_REQUEST['s'] ) ): echo esc_html( stripslashes( $_REQUEST['s'] ) ); endif; ?>"
						name="s"/> <input id="search-submit" class="button" type="submit"
						value="<?php _e( 'Search Polls', 'yop_poll' ); ?>" name=""/>
				</p>
				<div class="tablenav top">
					<div class="alignleft actions">
						<select name="action">
							<option selected="selected" value="-1"><?php _e( 'Bulk Actions', 'yop_poll' ); ?></option>
							<option value="delete"><?php _e( 'Delete', 'yop_poll' ); ?></option>
							<option value="clone"><?php _e( 'Clone', 'yop_poll' ); ?></option>
						</select> <input type="submit"
							value="<?php _e( 'Apply', 'yop_poll' ); ?>"
							class="button-secondary action" id="doaction" name="">
					</div>
					<br class="clear">
				</div>
				<table class="wp-list-table widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column"
								scope="col" style="width: 2%;"><input type="checkbox"></th>
							<th id="id"
								class="manage-column <?php echo $order_sortable['id'] ?> <?php echo $order_direction_reverse['id'] ?>"
								style="width: 10%;" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'id', 'order' => $order_direction['id'] ) ) ); ?>">
									<span><?php _e( 'ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="name"
								class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
								style="width: 38%;" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
									<span><?php _e( 'Name', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="template_author" class="manage-column" style="width: 10%;"
								scope="col"><span><?php _e( 'Author', 'yop_poll' ); ?></span></th>
							<th id="last_modified"
								class="manage-column <?php echo $order_sortable['last_modified'] ?> <?php echo $order_direction_reverse['last_modified'] ?>"
								style="width: 40%;" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'last_modified', 'order' => $order_direction['last_modified'] ) ) ); ?>">
									<span><?php _e( 'Last Modified', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
						</tr>
					</thead>
					<?php
						if ( count( $yop_poll_templates ) > 0 ){
							foreach ( $yop_poll_templates as $template ) {
							?>
							<tbody id="the-list">
								<tr valign="middle" class="alternate"
									id="yop-poll-<?php echo $template['id']; ?>">
									<th class="check-column" scope="row">
										<?php if ( ( $this->current_user_can( 'delete_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_templates' ) ) ){ ?>
											<input type="checkbox" value="<?php echo $template['id']; ?>"
												name="templatecheck[]">
											<?php } ?>
									</th>
									<td><strong>
											<?php if (( $this->current_user_can( 'edit_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls_templates' ) )) { ?>
												<a title="<?php echo $template['id']; ?>"
													href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'id' => $template['id'] ) ) ); ?>"
													class="row-title">
													<?php } ?>
												<?php echo $template['id']; ?>
												<?php if (( $this->current_user_can( 'edit_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls_templates' ) )) { ?>
												</a>
												<?php } ?>
										</strong><br>
										<div class="row-actions">
											<?php if ( ( $this->current_user_can( 'edit_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls_templates' ) ) ){ ?>
												<span class="edit"><a
														href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'id' => $template['id'] ) ) ); ?>"><?php _e( 'Edit', 'yop_poll' ) ?></a>
													| </span>
												<?php } ?>
											<?php if ( ( $this->current_user_can( 'delete_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_templates' ) ) ){ ?>
												<span class="delete"><a
														onclick="if ( confirm( '<?php echo __( "You are about to delete this poll template", 'yop_poll' ) . ": \'" . esc_html( $template['name'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to delete', 'yop_poll' ); ?>' ) ) { return true;}return false;"
														href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'id' => $template['id'] ) ), 'yop-poll-templates' ); ?>"
														class="submitdelete"><?php _e( 'Delete', 'yop_poll' ) ?></a> | </span>
												<?php } ?>
											<?php if ( ( $this->current_user_can( 'clone_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'clone_polls_templates' ) ) ){ ?>
												<span class="clone"><a
														onclick="if ( confirm( '<?php echo __( "You are about to clone this poll template", 'yop_poll' ) . ": \'" . esc_html( $template['name'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to clone', 'yop_poll' ); ?>' ) ) { return true;}return false;"
														href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'clone', 'id' => $template['id'] ) ), 'yop-poll-templates' ); ?>"
														class="submitdelete"><?php _e( 'Clone', 'yop_poll' ) ?></a></span>
												<?php } ?>
										</div></td>
									<td><strong>
											<?php if (( $this->current_user_can( 'edit_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls_templates' ) )) { ?>
												<a
													title="<?php echo esc_html( stripslashes( $template['name'] ) ); ?>"
													href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'id' => $template['id'] ) ) ); ?>"
													class="row-title">
													<?php } ?>
												<?php echo esc_html( stripslashes( $template['name'] ) ); ?>
												<?php if (( $this->current_user_can( 'edit_own_polls_templates' ) && $template['template_author'] == $current_user->ID ) || ( $this->current_user_can( 'edit_polls_templates' ) )) { ?>
												</a>
												<?php } ?>
										</strong><br></td>
									<td>
										<?php
											$user_info = get_userdata( $template ['template_author'] );
											if ( $user_info ){
												echo esc_html( stripslashes( $user_info->user_login ) );
											}
											else {
												echo '';
											}
										?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $template['last_modified'] ) ); ?>
									</td>
								</tr>
							</tbody>
							<?php
							}
						}
						else {
						?>
						<tbody id="the-list">
							<tr valign="middle" class="alternate" id="yop-poll-<?php ?>">
								<td id="empty-set" colspan="5">
									<h3 style="margin-bottom: 0px;"><?php _e( " You haven't used our template editor to create any yop poll templates!", 'yop_poll' ); ?> </h3>
									<p style="margin-bottom: 20px;"><?php _e( "Please create your poll template first.", 'yop_poll' ); ?></p>
									<a class="button-primary"
										href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls-templates', 'action' => 'add-new', 'id' => false, 'deleted' => false, 'cloned' => false ) ) ); ?>"><?php _e( "Create a poll template now", 'yop_poll' ); ?></a>
									<br/> <br/>
								</td>
							</tr>
						</tbody>
						<?php
						}
					?>

					<tfoot>
						<tr>
							<th id="cb" class="manage-column column-cb check-column" style=""
								scope="col"><input type="checkbox"></th>
							<th id="id"
								class="manage-column <?php echo $order_sortable['id'] ?> <?php echo $order_direction_reverse['id'] ?>"
								style="" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'id', 'order' => $order_direction['id'] ) ) ); ?>">
									<span><?php _e( 'ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="name"
								class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
								style="" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
									<span><?php _e( 'Name', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="template_author" class="manage-column" style="width: 10%;"
								scope="col"><span><?php _e( 'Author', 'yop_poll' ); ?></span></th>
							<th id="question"
								class="manage-column <?php echo $order_sortable['last_modified'] ?> <?php echo $order_direction_reverse['last_modified'] ?>"
								style="" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'last_modified', 'order' => $order_direction['last_modified'] ) ) ); ?>">
									<span><?php _e( 'Last Modified', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
		<?php
		}

		public function view_yop_poll_logs() {
			global $wpdb, $page, $action, $orderby, $order, $current_user;
			$per_page                   = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
			$page_no                    = isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1;
			$orderby                    = ( empty ( $orderby ) ) ? 'name' : $orderby;
			$order_direction            = array( 'vote_id' => 'asc', 'name' => 'asc', 'answer' => 'asc', 'user_nicename' => 'asc', 'user_email' => 'asc', 'user_from' => 'asc', 'tr_id' => 'asc', 'ip' => 'asc', 'vote_date' => 'asc' );
			$order_direction [$orderby] = ( 'desc' == $order ) ? 'asc' : 'desc';

			$order_direction_reverse            = array( 'vote_id' => 'desc', 'name' => 'desc', 'answer' => 'desc', 'user_nicename' => 'desc', 'user_email' => 'desc', 'user_from' => 'desc', 'tr_id' => 'desc', 'ip' => 'desc', 'vote_date' => 'desc' );
			$order_direction_reverse [$orderby] = ( 'desc' == $order ) ? 'desc' : 'asc';

			$order_sortable            = array( 'vote_id' => 'sortable', 'name' => 'sortable', 'answer' => 'sortable', 'user_nicename' => 'sortable', 'user_email' => 'sortable', 'user_from' => 'sortable', 'tr_id' => 'sortable', 'ip' => 'sortable', 'vote_date' => 'sortable' );
			$order_sortable [$orderby] = 'sorted';
			$poll_id                   = isset ( $_REQUEST ['poll_id'] ) ? ( int )$_REQUEST ['poll_id'] : NULL;

			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );

			$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
			if ( $this->current_user_can( 'view_own_polls_logs' ) && $poll_id ){
				if ( $poll_author != $current_user->ID && !$this->current_user_can( 'view_polls_logs' ) ){
					wp_die( __( 'You are not allowed to access this section.', 'yop_poll' ) );
				}
			}
			elseif ( !$this->current_user_can( 'view_polls_logs' ) ) {
				wp_die( __( 'You are not allowed to access this section.', 'yop_poll' ) );
			}
			$log_sdate = ( isset ( $_GET ['log_sdate'] ) ? $_GET ['log_sdate'] : '' );
			$log_edate = ( isset ( $_GET ['log_edate'] ) ? $_GET ['log_edate'] : '' );
			$group_by  = ( isset ( $_GET ['group_by'] ) ? $_GET ['group_by'] : 'vote' );

			$yop_polls = Yop_Poll_Model::get_yop_polls_filter_search( 'id', 'asc' );
			if ( $group_by == 'vote' ){
				$search = array( 'fields' => array( 'name', 'ip', 'user_nicename', 'user_email', 'user_from', 'tr_id' ), 'value' => isset ( $_REQUEST ['s'] ) ? trim( $_REQUEST ['s'] ) : '' );
			}
			else {
				$search = array( 'fields' => array( 'name', 'answer', 'ip', 'other_answer_value', 'user_nicename', 'user_from', 'tr_id', 'user_email' ), 'value' => isset ( $_REQUEST ['s'] ) ? trim( $_REQUEST ['s'] ) : '' );
			}
			$filter = array( 'field' => NULL, 'value' => NULL, 'operator' => '=' );
			if ( $group_by == 'vote' ){
				$total_logs = Yop_Poll_Model::get_total_group_logs_filter_search( $search, $poll_id, $log_sdate, $log_edate );
			}
			else {
				$total_logs = Yop_Poll_Model::get_total_logs_filter_search( $search, $poll_id, $log_sdate, $log_edate );
			}
			$total_logs_pages = ceil( $total_logs / $per_page );
			if ( intval( $page_no ) > intval( $total_logs_pages ) ){
				$page_no = 1;
			}
			if ( $group_by == 'vote' ){
				$logs = Yop_Poll_Model::get_group_logs_filter_search( $orderby, $order, $search, $poll_id, ( $page_no - 1 ) * $per_page, $per_page, $log_sdate, $log_edate );
			}
			else {
				$logs = Yop_Poll_Model::get_logs_filter_search( $orderby, $order, $search, $poll_id, ( $page_no - 1 ) * $per_page, $per_page, $log_sdate, $log_edate );
			}

			$args                    = array( 'base' => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&page_no=%#%', 'total' => $total_logs_pages, 'current' => max( 1, $page_no ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
			$pagination              = paginate_links( $args );
			$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Yop Poll Logs', 'yop_poll' ); ?></h2>
			<?php
				if ( isset ( $_REQUEST ['deleted'] ) ){
					echo '<div id="message" class="updated"><p>';
					$deleted = ( int )$_REQUEST ['deleted'];
					printf( _n( '%s Poll Log deleted.', '%s Poll Logs deleted.', $deleted ), $deleted );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<form method="get">
				<?php wp_nonce_field( 'yop-poll-logs' ); ?>
				<input type="hidden" name="order" value="<?php echo $order ?>"/>
				<input type="hidden" name="orderby" value="<?php echo $orderby ?>"/>
				<input type="hidden" name="page" value="yop-polls-logs"/>
				<p class="search-box">
					<label class="screen-reader-text" for="yop-poll-search-input"><?php _e( 'Search Poll Logs', 'yop_poll' ) ?></label>
					<input id="yop-poll-search-input" type="search"
						value="<?php if ( isset( $_REQUEST['s'] ) ): echo esc_html( stripslashes( $_REQUEST['s'] ) ); endif; ?>"
						name="s"/> <input id="search-submit" class="button" type="submit"
						value="<?php _e( 'Search Poll Logs', 'yop_poll' ); ?>" name=""/>
				</p>
				<div class="tablenav top">
					<div class="alignleft actions">
						<select name="group_by">
							<option <?php echo selected( $group_by, 'answer' ); ?>
								value="answer"><?php _e( 'Group Logs By Answer', 'yop_poll' ); ?></option>
							<option <?php echo selected( $group_by, 'vote' ); ?> value="vote"><?php _e( 'Group Logs By Vote', 'yop_poll' ); ?></option>
						</select> <input type="submit"
							value="<?php _e( 'Group', 'yop_poll' ); ?>"
							class="button-secondary action" id="doaction" name=""/>
					</div>
				</div>
				<div class="tablenav top">
					<div class="alignleft actions">
						<select name="action">
							<option selected="selected" value="-1"><?php _e( 'Bulk Actions', 'yop_poll' ); ?></option>
							<?php if ( $group_by == 'vote' ){ ?>
								<option value="delete_group"><?php _e( 'Delete', 'yop_poll' ); ?></option>
								<?php

								}
								else {
								?>
								<option value="delete"><?php _e( 'Delete', 'yop_poll' ); ?></option>
								<?php } ?>
						</select> <input type="submit"
							value="<?php _e( 'Apply', 'yop_poll' ); ?>"
							class="button-secondary action" id="doaction" name="">&nbsp;|&nbsp;
					</div>
					<div class="alignleft actions">
						<select name="poll_id">
							<option value=""><?php _e( 'All Logs', 'yop_poll' ); ?></option>
							<?php
								if ( count( $yop_polls ) > 0 ){
									foreach ( $yop_polls as $yop_poll ) {
									?>
									<option <?php echo selected( $poll_id, $yop_poll['id'] ); ?>
										value="<?php echo $yop_poll['id'] ?>"><?php echo $yop_poll['name'] ?></option>
									<?php
									}
								}
							?>
						</select> <label for="yop-poll-logs-start-date-input"><?php _e( 'Start Date', 'yop_poll' ); ?>
							:</label>
						<input id="yop-poll-logs-start-date-input" type="text"
							name="log_sdate" value="<?php echo $log_sdate; ?>"/>&nbsp;&nbsp; <label
							for="yop-poll-logs-end-date-input"><?php _e( 'End Date', 'yop_poll' ); ?>:</label>
						<input id="yop-poll-logs-end-date-input" type="text"
							name="log_edate" value="<?php echo $log_edate; ?>"/>&nbsp;&nbsp; <input
							type="submit" value="<?php _e( 'Filter', 'yop_poll' ); ?>"
							class="button-secondary" id="post-query-submit" name="">&nbsp;|&nbsp;
					</div>
					<div class="alignleft actions">
						<select name="export">
							<option value="page"><?php _e( 'This Page', 'yop_poll' ); ?></option>
							<option value="all"><?php _e( 'All Pages', 'yop_poll' ); ?></option>
						</select> <input type="submit"
							value="<?php _e( 'Export', 'yop_poll' ); ?>"
							class="button-secondary action" id="doaction" name="a">
						&nbsp;&nbsp;&nbsp;

					</div>
					<div class="tablenav-pages one-page">
						<label for="yop-poll-items-per-page" class="displaying-num"><?php _e( 'Items Per Page', 'yop_poll' ); ?>
							:</label>
						<input id="yop-poll-items-per-page" type="text" name="per_page"
							value="<?php echo $per_page; ?>"/> <input name="a"
							value="<?php _e( 'Set', 'yop_poll' ); ?>" type="submit"/>&nbsp;&nbsp;
						<span class="displaying-num"><?php echo count( $logs ); ?> / <?php echo $total_logs; ?>
							logs</span>
						<?php print $pagination; ?>
					</div>
					<br class="clear">
				</div>
				<table class="wp-list-table widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column"
								style="width: 2%;" scope="col"><input type="checkbox"></th>
							<th id="id"
								class="manage-column <?php echo $order_sortable['vote_id'] ?> <?php echo $order_direction_reverse['vote_id'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'vote_id', 'order' => $order_direction['vote_id'] ) ) ); ?>">
									<span><?php _e( 'Vote ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="name"
								class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
								style="width: 20%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
									<span><?php _e( 'Poll Name', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="total_votes"
								class="manage-column <?php echo $order_sortable['answer'] ?> <?php echo $order_direction_reverse['answer'] ?>"
								style="width: 18%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'answer', 'order' => $order_direction['answer'] ) ) ); ?>">
									<span><?php _e( 'Answer', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="user_from"
								class="manage-column <?php echo $order_sortable['user_from'] ?> <?php echo $order_direction_reverse['user_from'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'user_from', 'order' => $order_direction['user_from'] ) ) ); ?>">
									<span><?php _e( 'User Type', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="user_nicename"
								class="manage-column <?php echo $order_sortable['user_nicename'] ?> <?php echo $order_direction_reverse['user_nicename'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'user_nicename', 'order' => $order_direction['user_nicename'] ) ) ); ?>">
									<span><?php _e( 'User', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="user_email"
								class="manage-column <?php echo $order_sortable['user_email'] ?> <?php echo $order_direction_reverse['user_email'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'user_email', 'order' => $order_direction['user_email'] ) ) ); ?>">
									<span><?php _e( 'User Email', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="tr_id"
								class="manage-column <?php echo $order_sortable['tr_id'] ?> <?php echo $order_direction_reverse['tr_id'] ?>"
								style="width: 5%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'tr_id', 'order' => $order_direction['tr_id'] ) ) ); ?>">
									<span><?php _e( 'Tracking ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="ip"
								class="manage-column <?php echo $order_sortable['ip'] ?> <?php echo $order_direction_reverse['ip'] ?>"
								style="width: 5%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'ip', 'order' => $order_direction['ip'] ) ) ); ?>">
									<span><?php _e( 'Ip', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="vote_date"
								class="manage-column <?php echo $order_sortable['vote_date'] ?> <?php echo $order_direction_reverse['vote_date'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'vote_date', 'order' => $order_direction['vote_date'] ) ) ); ?>">
									<span><?php _e( 'Vote Date', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th id="footer_cb" class="manage-column column-cb check-column"
								style="width: 2%;" scope="col"><input type="checkbox"></th>
							<th id="id"
								class="manage-column <?php echo $order_sortable['vote_id'] ?> <?php echo $order_direction_reverse['vote_id'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'vote_id', 'order' => $order_direction['vote_id'] ) ) ); ?>">
									<span><?php _e( 'Vote ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_name"
								class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
								style="width: 20%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
									<span><?php _e( 'Poll Name', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_answer"
								class="manage-column <?php echo $order_sortable['answer'] ?> <?php echo $order_direction_reverse['answer'] ?>"
								style="width: 18%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'answer', 'order' => $order_direction['answer'] ) ) ); ?>">
									<span><?php _e( 'Answer', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_user_from"
								class="manage-column <?php echo $order_sortable['user_from'] ?> <?php echo $order_direction_reverse['user_from'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'user_from', 'order' => $order_direction['user_from'] ) ) ); ?>">
									<span><?php _e( 'User Type', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_user_nicename"
								class="manage-column <?php echo $order_sortable['user_nicename'] ?> <?php echo $order_direction_reverse['user_nicename'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'user_nicename', 'order' => $order_direction['user_nicename'] ) ) ); ?>">
									<span><?php _e( 'User', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_user_email"
								class="manage-column <?php echo $order_sortable['user_email'] ?> <?php echo $order_direction_reverse['user_email'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'user_email', 'order' => $order_direction['user_email'] ) ) ); ?>">
									<span><?php _e( 'User Email', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_tr_id"
								class="manage-column <?php echo $order_sortable['tr_id'] ?> <?php echo $order_direction_reverse['tr_id'] ?>"
								style="width: 5%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'tr_id', 'order' => $order_direction['tr_id'] ) ) ); ?>">
									<span><?php _e( 'Tracking ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_ip"
								class="manage-column <?php echo $order_sortable['ip'] ?> <?php echo $order_direction_reverse['ip'] ?>"
								style="width: 5%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'ip', 'order' => $order_direction['ip'] ) ) ); ?>">
									<span><?php _e( 'Ip', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="footer_vote_date"
								class="manage-column <?php echo $order_sortable['vote_date'] ?> <?php echo $order_direction_reverse['vote_date'] ?>"
								style="width: 10%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'vote_date', 'order' => $order_direction['vote_date'] ) ) ); ?>">
									<span><?php _e( 'Vote Date', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
						</tr>
					</tfoot>
					<?php
						if ( count( $logs ) > 0 ){
							foreach ( $logs as $log ) {
							?>
							<tbody id="the-list">
								<tr valign="middle" class="alternate"
									id="yop-poll-log<?php echo $log['id']; ?>">
									<th class="check-column" scope="row">
										<?php if ( $group_by == 'vote' ){ ?>
											<?php
												$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_vote_id( 'poll_id', $log ['vote_id'] );
												$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
												if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
												?>
												<input type="checkbox"
													value="<?php echo $log['vote_id']; ?>" name="yoppolllogscheck[]">
												<?php } ?>
											<?php

											}
											else {
											?>
											<?php
												$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_id( 'poll_id', $log ['id'] );
												$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
												if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
												?>
												<input type="checkbox" value="<?php echo $log['id']; ?>"
													name="yoppolllogscheck[]">
												<?php } ?>
											<?php } ?>
									</th>
									<td><strong><?php echo $log['vote_id']; ?></strong><br></td>
									<td><strong><?php if ( $log['name'] != '' ){
													echo esc_html( stripslashes( $log['name'] ) );
												}
												else {
													echo esc_html( stripslashes( Yop_Poll_Model::get_poll_field_from_database_by_id( 'name', $poll_id ) ) );
										} ?></strong><br>
										<div class="row-actions">
											<?php if ( $group_by == 'vote' ){ ?>
												<?php
													$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_vote_id( 'poll_id', $log ['vote_id'] );
													$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
													if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
													?>
													<span class="delete"><a
															onclick="if ( confirm( '<?php echo __( "You are about to delete this vote log", 'yop_poll' ) . " \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to delete', 'yop_poll' ); ?>'  ) ) { return true;}return false;"
															href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'delete_group', 'id' => $log['vote_id'] ) ), 'yop-poll-logs-delete' ); ?>"
															class="submitdelete"><?php _e( 'Delete', 'yop_poll' ) ?></a></span>
													<?php } ?>
												<?php

												}
												else {
												?>
												<?php
													$poll_id     = Yop_Poll_Model::get_poll_log_field_from_database_by_id( 'poll_id', $log ['id'] );
													$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
													if ( ( $this->current_user_can( 'delete_own_polls_logs' ) && $poll_author == $current_user->ID ) || ( $this->current_user_can( 'delete_polls_logs' ) ) ){
													?>
													<span class="delete"><a
															onclick="if ( confirm( '<?php echo __( "You are about to delete this poll log", 'yop_poll' ) . ": \'" . esc_html( $log['id'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to delete', 'yop_poll' ); ?>'  ) ) { return true;}return false;"
															href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'id' => $log['id'] ) ), 'yop-poll-logs-delete' ); ?>"
															class="submitdelete"><?php _e( 'Delete', 'yop_poll' ) ?></a></span>
													<?php } ?>
												<?php } ?>
										</div></td>
									<td>
										<?php echo ( 'Other' == $log['answer'] ) ? 'Other - ' . esc_html( stripslashes( $log['other_answer_value'] ) ) : esc_html( stripslashes( $log['answer'] ) ); ?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $log['user_from'] ) ); ?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $log['user_nicename'] ) ); ?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $log['user_email'] ) ); ?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $log['tr_id'] ) ); ?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $log['ip'] ) ); ?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $log['vote_date'] ) ); ?>
									</td>
								</tr>
							</tbody>
							<?php
							}
						}
					?>
				</table>
				<div class="tablenav bottom">
					<div class="tablenav-pages one-page">
						<span class="displaying-num"><?php echo count( $logs ); ?> / <?php echo $total_logs; ?>
							logs</span>
						<?php print $pagination; ?>
					</div>
				</div>
			</form>
		</div>
		<?php
		}

		public function view_yop_poll_bans() {
			global $wpdb, $page, $action, $orderby, $order;
			$per_page                   = ( isset ( $_GET ['per_page'] ) ? intval( $_GET ['per_page'] ) : 100 );
			$page_no                    = isset ( $_REQUEST ['page_no'] ) ? ( int )$_REQUEST ['page_no'] : 1;
			$orderby                    = ( empty ( $orderby ) ) ? 'name' : $orderby;
			$order_direction            = array( 'id' => 'asc', 'name' => 'asc', 'type' => 'asc', 'value' => 'asc' );
			$order_direction [$orderby] = ( 'desc' == $order ) ? 'asc' : 'desc';

			$order_direction_reverse            = array( 'id' => 'desc', 'name' => 'desc', 'type' => 'desc', 'value' => 'desc' );
			$order_direction_reverse [$orderby] = ( 'desc' == $order ) ? 'desc' : 'asc';

			$order_sortable            = array( 'id' => 'sortable', 'name' => 'sortable', 'type' => 'sortable', 'value' => 'sortable' );
			$order_sortable [$orderby] = 'sorted';
			$poll_id                   = isset ( $_REQUEST ['poll_id'] ) ? ( int )$_REQUEST ['poll_id'] : NULL;
			$type                      = isset ( $_REQUEST ['type'] ) ? $_REQUEST ['type'] : NULL;
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$yop_polls        = Yop_Poll_Model::get_yop_polls_filter_search( 'id', 'asc' );
			$search           = array( 'fields' => array( $wpdb->yop_poll_bans . '.value' ), 'value' => isset ( $_REQUEST ['s'] ) ? trim( $_REQUEST ['s'] ) : '' );
			$total_bans       = count( Yop_Poll_Model::get_bans_filter_search( $orderby, $order, $search, $type, $poll_id ) );
			$total_bans_pages = ceil( $total_bans / $per_page );
			if ( intval( $page_no ) > intval( $total_bans_pages ) ){
				$page_no = 1;
			}
			$bans = Yop_Poll_Model::get_bans_filter_search( $orderby, $order, $search, $type, $poll_id, ( $page_no - 1 ) * $per_page, $per_page );

			$args                    = array( 'base' => remove_query_arg( 'page_no', $_SERVER ['REQUEST_URI'] ) . '%_%', 'format' => '&page_no=%#%', 'total' => $total_bans_pages, 'current' => max( 1, $page_no ), 'prev_next' => true, 'prev_text' => __( '&laquo; Previous' ), 'next_text' => __( 'Next &raquo;' ) );
			$pagination              = paginate_links( $args );
			$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'action' ), $_SERVER ['REQUEST_URI'] );
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Yop Poll Bans', 'yop_poll' ); ?> <a
					href="javascript:void(0);" class="add-new-h2"
					id="yop-poll-add-new-ban"><?php _e( 'Add New', 'yop_poll' ); ?></a>
			</h2>
			<?php
				if ( isset ( $_REQUEST ['deleted'] ) ){
					echo '<div id="message" class="updated"><p>';
					$deleted = ( int )$_REQUEST ['deleted'];
					printf( _n( '%s Poll Ban deleted!', '%s Poll Bans deleted!', $deleted ), $deleted );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<?php
				if ( isset ( $_REQUEST ['bans-added'] ) ){
					echo '<div id="message" class="updated"><p>';
					$added = ( int )$_REQUEST ['bans-added'];
					printf( _n( '%s Poll Ban added!', '%s Poll Bans added!', $added ), $added );
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'bans-added' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<?php
				if ( isset ( $_REQUEST ['bans-error'] ) ){
					echo '<div id="message" class="error"><p>';
					print $_REQUEST ['bans-error'];
					echo '</p></div>';
					$_SERVER ['REQUEST_URI'] = remove_query_arg( array( 'bans-error' ), $_SERVER ['REQUEST_URI'] );
				}
			?>
			<div id='yop-poll-add-ban-div' style="display: none;">
				<p><?php _e( 'Ban IP, Username or Email', 'yop_poll' ); ?></p>
				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
					<input type="hidden" name="page" value="yop-polls-bans"/>
					<input type="hidden" name="action" value="add-ban"/>
					<?php wp_nonce_field( 'yop-poll-add-ban' ); ?>
					<table class="form-table">
						<tbody>
							<tr class="form-field form-required">
								<th scope="row"><label for="ban-poll-id"><?php _e( 'Poll', 'yop_poll' ); ?> <span
											class="description">(required)</span></label></th>
								<td><select id="ban-poll-id" name="ban_poll_id">
										<option value="0"><?php _e( 'Bans For All Polls', 'yop_poll' ); ?></option>
										<?php
											if ( count( $yop_polls ) > 0 ){
												foreach ( $yop_polls as $yop_poll ) {
												?>
												<option value="<?php echo $yop_poll['id'] ?>"><?php echo $yop_poll['name'] ?></option>
												<?php
												}
											}
										?>
									</select></td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row"><label for="yop-poll-ban-type"><?php _e( 'Type', 'yop_poll' ); ?> <span
											class="description">(required)</span></label></th>
								<td><select id="yop-poll-ban-type" name="ban_type">
									<option value=""><?php _e( 'Choose Ban Type', 'yop_poll' ); ?></option>
									<option value="ip"><?php _e( 'IP', 'yop_poll' ); ?></option>
									<option value="username"><?php _e( 'Username', 'yop_poll' ); ?></option>
									<option value="email"><?php _e( 'Email', 'yop_poll' ); ?></option></td>
							</tr>
							<tr class="form-field form-required">
								<th scope="row"><label for="yop-poll-ban-value"><?php _e( 'Value', 'yop_poll' ); ?>
										<span
											class="description">(required)</span><br> <small><i><?php _e( 'One Value Per Line', 'yop_poll' ); ?></i></small></label></th>
								<td><textarea rows="5" cols="20" id="yop-poll-ban-value"
										name="ban_value"></textarea></td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" value="<?php _e( 'Add Ban', 'yop_poll' ); ?> "
							class="button-primary"> <input id="yop-poll-add-ban-close"
							type="button" value="<?php _e( 'Close', 'yop_poll' ); ?> "
							class="button-primary">

					</p>
				</form>
			</div>
			<br/>
			<form method="get">
				<?php wp_nonce_field( 'yop-poll-bans' ); ?>
				<input type="hidden" name="order" value="<?php echo $order ?>"/>
				<input type="hidden" name="orderby" value="<?php echo $orderby ?>"/>
				<input type="hidden" name="page" value="yop-polls-bans"/>
				<p class="search-box">
					<label class="screen-reader-text" for="yop-poll-search-input"><?php _e( 'Search Poll Bans', 'yop_poll' ) ?></label>
					<input id="yop-poll-search-input" type="search"
						value="<?php if ( isset( $_REQUEST['s'] ) ): echo esc_html( stripslashes( $_REQUEST['s'] ) ); endif; ?>"
						name="s"/> <input id="search-submit" class="button" type="submit"
						value="<?php _e( 'Search Poll Bans', 'yop_poll' ); ?>" name=""/>
				</p>
				<div class="tablenav top">
					<div class="alignleft actions">
						<select name="action">
							<option selected="selected" value="-1"><?php _e( 'Bulk Actions', 'yop_poll' ); ?></option>
							<option value="delete"><?php _e( 'Delete', 'yop_poll' ); ?></option>
						</select> <input type="submit"
							value="<?php _e( 'Apply', 'yop_poll' ); ?>"
							class="button-secondary action" id="doaction" name="">
					</div>
					<div class="alignleft actions">
						<select name="poll_id">
							<option value=""><?php _e( 'All Polls', 'yop_poll' ); ?></option>
							<?php
								if ( count( $yop_polls ) > 0 ){
									foreach ( $yop_polls as $yop_poll ) {
									?>
									<option <?php echo selected( $poll_id, $yop_poll['id'] ); ?>
										value="<?php echo $yop_poll['id'] ?>"><?php echo $yop_poll['name'] ?></option>
									<?php
									}
								}
							?>
						</select>
					</div>
					<div class="alignleft actions">
						<select name="type">
							<option value=""><?php _e( 'All Ban Types', 'yop_poll' ); ?></option>
							<option <?php echo selected( 'ip', $type ); ?> value="ip"><?php _e( 'IP', 'yop_poll' ); ?></option>
							<option <?php echo selected( 'username', $type ); ?> value="username"><?php _e( 'Username', 'yop_poll' ); ?></option>
							<option <?php echo selected( 'email', $type ); ?> value="email"><?php _e( 'Email', 'yop_poll' ); ?></option>
						</select> <input type="submit"
							value="<?php _e( 'Filter', 'yop_poll' ); ?>"
							class="button-secondary" id="post-query-submit" name="">
					</div>
					<div class="tablenav-pages one-page">
						<label for="yop-poll-items-per-page" class="displaying-num"><?php _e( 'Items Per Page', 'yop_poll' ); ?>
							:</label>
						<input id="yop-poll-items-per-page" type="text" name="per_page"
							value="<?php echo $per_page; ?>"/> <input name="a"
							value="<?php _e( 'Set', 'yop_poll' ); ?>" type="submit"/>&nbsp;&nbsp;
						<span class="displaying-num"><?php echo count( $bans ); ?> / <?php echo $total_bans;
							_e( 'Bans', 'yop_poll' ) ?> </span>
						<?php print $pagination; ?>
					</div>
					<br class="clear">
				</div>
				<table class="wp-list-table widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column"
								style="width: 2%;" scope="col"><input type="checkbox"></th>
							<th id="id"
								class="manage-column <?php echo $order_sortable['id'] ?> <?php echo $order_direction_reverse['id'] ?>"
								style="width: 5%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'id', 'order' => $order_direction['id'] ) ) ); ?>">
									<span><?php _e( 'ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="name"
								class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
								style="width: 25%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
									<span><?php _e( 'Poll Name', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="type"
								class="manage-column <?php echo $order_sortable['type'] ?> <?php echo $order_direction_reverse['type'] ?>"
								style="width: 25%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'type', 'order' => $order_direction['type'] ) ) ); ?>">
									<span><?php _e( 'Ban Type', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="value"
								class="manage-column <?php echo $order_sortable['value'] ?> <?php echo $order_direction_reverse['value'] ?>"
								style="width: 15%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'value', 'order' => $order_direction['value'] ) ) ); ?>">
									<span><?php _e( 'Ban Value', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th id="cb" class="manage-column column-cb check-column"
								style="width: 2%;" scope="col"><input type="checkbox"></th>
							<th id="id"
								class="manage-column <?php echo $order_sortable['id'] ?> <?php echo $order_direction_reverse['id'] ?>"
								style="width: 5%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'id', 'order' => $order_direction['id'] ) ) ); ?>">
									<span><?php _e( 'ID', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="name"
								class="manage-column <?php echo $order_sortable['name'] ?> <?php echo $order_direction_reverse['name'] ?>"
								style="width: 25%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'name', 'order' => $order_direction['name'] ) ) ); ?>">
									<span><?php _e( 'Poll Name', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="type"
								class="manage-column <?php echo $order_sortable['type'] ?> <?php echo $order_direction_reverse['type'] ?>"
								style="width: 25%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'type', 'order' => $order_direction['type'] ) ) ); ?>">
									<span><?php _e( 'Ban Type', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
							<th id="value"
								class="manage-column <?php echo $order_sortable['value'] ?> <?php echo $order_direction_reverse['value'] ?>"
								style="width: 15%" scope="col"><a
									href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'value', 'order' => $order_direction['value'] ) ) ); ?>">
									<span><?php _e( 'Ban Value', 'yop_poll' ); ?></span> <span
										class="sorting-indicator"></span>
								</a></th>
						</tr>
					</tfoot>
					<?php
						if ( count( $bans ) > 0 ){
							foreach ( $bans as $ban ) {
							?>
							<tbody id="the-list">
								<tr valign="middle" class="alternate"
									id="yop-poll-log<?php echo $ban['id']; ?>">
									<th class="check-column" scope="row"><input type="checkbox"
											value="<?php echo $ban['id']; ?>" name="yoppollbanscheck[]"></th>
									<td><strong><?php echo $ban['id']; ?></strong><br></td>
									<td><strong><?php echo esc_html( stripslashes( $ban['name'] ) ); ?></strong><br>
										<div class="row-actions">
											<span class="delete"><a
													onclick="if ( confirm( '<?php echo __( "You are about to remove this poll ban", 'yop_poll' ) . ": \'" . esc_html( $log['id'] ) . "\' \\n  \'" . __( "Cancel", 'yop_poll' ) . "\' " . __( 'to stop', 'yop_poll' ) . ", \'" . __( 'OK', 'yop_poll' ) . "\' " . __( 'to remove', 'yop_poll' ); ?>' ) ) { return true;}return false;"
													href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'id' => $ban['id'] ) ), 'yop-poll-bans-delete' ); ?>"
													class="submitdelete"><?php _e( 'Remove', 'yop_poll' ) ?></a></span>
										</div></td>
									<td>
										<?php echo esc_html( stripslashes( $ban['type'] ) ); ?>
									</td>
									<td>
										<?php echo esc_html( stripslashes( $ban['value'] ) ); ?>
									</td>
								</tr>
							</tbody>
							<?php
							}
						}
					?>
				</table>
				<div class="tablenav bottom">
					<div class="tablenav-pages one-page">
						<span class="displaying-num"><?php echo count( $bans ); ?> / <?php echo $total_bans;
							_e( 'Bans', 'yop_poll' ) ?> </span>
						<?php print $pagination; ?>
					</div>
				</div>
			</form>
		</div>
		<?php
		}

		public function view_yop_poll_become_pro() {
			global $wpdb, $page, $action;
			require_once( ABSPATH . '/wp-admin/options-head.php' );
			$pro_options = get_option( 'yop_poll_pro_options', array() );
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Become Pro', 'yop_poll' ); ?></h2>
			<div id="message"></div>
			<form action="options.php" method="post">
				<?php settings_fields( 'yop_poll_pro_options' ); ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label for="pro_key">Your Pro Key</label></th>
							<td><input id="pro_key" class="regular-text" type="text"
								value="<?php echo $pro_options['pro_key']; ?>"
								name="yop_poll_pro_options[pro_key]">
						</tr>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
		}

		public function yop_poll_options_admin_init() {
			register_setting( 'yop_poll_options', 'yop_poll_options', array( &$this, 'yop_poll_options_validate' ) );
			register_setting( 'yop_poll_pro_options', 'yop_poll_pro_options', array( &$this, 'yop_poll_pro_options_validate' ) );
		}

		public function yop_poll_pro_options_validate( $input ) {
			$pro_options       = get_option( 'yop_poll_pro_options', array() );
			$newinput          = $pro_options;
			$errors            = '';
			$updated           = '';
			$message_delimiter = '<br>';
			// set api key
			if ( isset ( $input ['pro_key'] ) ){
				if ( $input ['pro_key'] != '' ){
					require_once( $this->_config->plugin_inc_dir . '/pro_member_model.php' );
					$yop_poll_pro_member = Yop_Poll_Pro_Member_Model::getInstance();
					if ( $yop_poll_pro_member->register_pro_member( $input ['pro_key'] ) ){
						$newinput ['pro_key']     = trim( $input ['pro_key'] );
						$newinput ['pro_api_key'] = $yop_poll_pro_member->api_return_data['apy_key'];
						$newinput ['pro_user']    = 'yes';
						$updated .= __( 'Your Pro Key Saved!', 'yop_poll' ) . $message_delimiter;
					}
					else {
						$newinput ['pro_key']     = trim( $input ['pro_key'] );
						$newinput ['pro_api_key'] = '';
						$newinput ['pro_user']    = 'no';
						$errors .= __( 'Pro Key Error: ', 'yop_poll' ) . $yop_poll_pro_member->error . $message_delimiter;
					}
				}
				else {
					$newinput ['pro_key']     = $pro_options ['pro_key'];
					$newinput ['pro_api_key'] = $pro_options ['pro_api_key'];
					$newinput ['pro_user']    = $pro_options ['pro_user'];
					$errors .= __( 'Pro Key Is Empty!', 'yop_poll' ) . $message_delimiter;
				}
			}
			else {
				$newinput ['pro_key']     = $pro_options ['pro_key'];
				$newinput ['pro_api_key'] = $pro_options ['pro_api_key'];
				$newinput ['pro_user']    = $pro_options ['pro_user'];
				$errors .= __( 'An Error Has Occured!', 'yop_poll' ) . $message_delimiter;
			}

			if ( '' != $errors )
				add_settings_error( 'general', 'yop-poll-errors', $errors, 'error' );
			if ( '' != $updated )
				add_settings_error( 'general', 'yop-poll-updates', $updated, 'updated' );

			return $newinput;
		}

		public function yop_poll_options_validate( $input ) {
			$default_options   = get_option( 'yop_poll_options', array() );
			$newinput          = $default_options;
			$errors            = '';
			$updated           = '';
			$message_delimiter = '<br>';
			if ( $this->current_user_can( 'manage_polls_options' ) ){
				// allow_other_answers
				if ( isset ( $input ['allow_other_answers'] ) ){
					if ( in_array( $input ['allow_other_answers'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['allow_other_answers'] != trim( $input ['allow_other_answers'] ) ){
							$newinput ['allow_other_answers'] = trim( $input ['allow_other_answers'] );
							$updated .= __( 'Option "Allow Other Answer" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['allow_other_answers'] = $default_options ['allow_other_answers'];
						$errors .= __( 'Option "Allow Other Answer" Not Updated! Choose "yes" or "no"!', 'yop_poll' ) . $message_delimiter;
					}

					if ( 'yes' == $input ['allow_other_answers'] ){
						// other_answers_label
						if ( isset ( $input ['other_answers_label'] ) ){
							if ( $default_options ['other_answers_label'] != trim( $input ['other_answers_label'] ) ){
								$newinput ['other_answers_label'] = trim( $input ['other_answers_label'] );
								$updated .= __( 'Option "Other Answer Label" Updated!', 'yop_poll' ) . $message_delimiter;
							}
						}

						//add_other_answers_to_default_answers

						if ( isset ( $input ['add_other_answers_to_default_answers'] ) ){
							if ( in_array( $input ['add_other_answers_to_default_answers'], array( 'yes', 'no' ) ) ){
								if ( $default_options ['add_other_answers_to_default_answers'] != trim( $input ['add_other_answers_to_default_answers'] ) ){
									$newinput ['add_other_answers_to_default_answers'] = trim( $input ['add_other_answers_to_default_answers'] );
									$updated .= __( 'Option "Add the values submitted in \'Other\' as answers" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
							else {
								$newinput ['add_other_answers_to_default_answers'] = $default_options ['add_other_answers_to_default_answers'];
								$errors .= __( 'Option "Add the values submitted in \'Other\' as answers" Not Updated! Choose "yes" or "no"!', 'yop_poll' ) . $message_delimiter;
							}
						}

						if ( isset ( $input ['display_other_answers_values'] ) ){
							if ( in_array( $input ['display_other_answers_values'], array( 'yes', 'no' ) ) ){
								if ( $default_options ['display_other_answers_values'] != trim( $input ['display_other_answers_values'] ) ){
									$newinput ['display_other_answers_values'] = trim( $input ['display_other_answers_values'] );
									$updated .= __( 'Option "Display Other Answers Values" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
							else {
								$newinput ['display_other_answers_values'] = $default_options ['display_other_answers_values'];
								$errors .= __( 'Option "Display Other Answers Values" Not Updated! Choose "yes" or "no"!', 'yop_poll' ) . $message_delimiter;
							}
						}
					}
				}

				// allow_multiple_answers
				if ( isset ( $input ['allow_multiple_answers'] ) ){
					if ( in_array( $input ['allow_multiple_answers'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['allow_multiple_answers'] != trim( $input ['allow_multiple_answers'] ) ){
							$newinput ['allow_multiple_answers'] = trim( $input ['allow_multiple_answers'] );
							$updated .= __( 'Option "Allow Multiple Answers" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						// allow_multiple_answers_number
						if ( 'yes' == $input ['allow_multiple_answers'] ){
							if ( isset ( $input ['allow_multiple_answers_number'] ) ){
								if ( ctype_digit( $input ['allow_multiple_answers_number'] ) ){
									if ( $default_options ['allow_multiple_answers_number'] != trim( $input ['allow_multiple_answers_number'] ) ){
										$newinput ['allow_multiple_answers_number'] = trim( $input ['allow_multiple_answers_number'] );
										$updated .= __( 'Option "Max Number of allowed answers" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['allow_multiple_answers_number'] = $default_options ['allow_multiple_answers_number'];
									$errors .= __( 'Option "Max Number of allowed answers" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}
							if ( isset ( $input ['allow_multiple_answers_min_number'] ) ){
								if ( ctype_digit( $input ['allow_multiple_answers_min_number'] ) ){
									if ( $default_options ['allow_multiple_answers_min_number'] != trim( $input ['allow_multiple_answers_min_number'] ) ){
										$newinput ['allow_multiple_answers_min_number'] = trim( $input ['allow_multiple_answers_min_number'] );
										$updated .= __( 'Option "Min Number of allowed answers" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['allow_multiple_answers_min_number'] = $default_options ['allow_multiple_answers_min_number'];
									$errors .= __( 'Option "Min Number of allowed answers" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['allow_multiple_answers'] = $default_options ['allow_multiple_answers'];
						$errors .= __( 'Option "Allow Multiple Answers" Not Updated! Choose "yes" or "no"!', 'yop_poll' ) . $message_delimiter;
					}
				}

				if ( isset ( $input ['use_captcha'] ) ){
					if ( in_array( $input ['use_captcha'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['use_captcha'] != trim( $input ['use_captcha'] ) ){
							$newinput ['use_captcha'] = trim( $input ['use_captcha'] );
							$updated .= __( 'Option "Use CAPTCHA" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['use_captcha'] = $default_options ['use_captcha'];
						$errors .= __( 'Option "Use CAPTCHA" Not Updated! Choose "yes" or "no"!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// display_answers
				if ( isset ( $input ['display_answers'] ) ){
					if ( in_array( $input ['display_answers'], array( 'vertical', 'orizontal', 'tabulated' ) ) ){
						if ( $default_options ['display_answers'] != trim( $input ['display_answers'] ) ){
							$newinput ['display_answers'] = trim( $input ['display_answers'] );
							$updated .= __( 'Option "Display Answers" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'tabulated' == $input ['display_answers'] ){
							// display_answers_tabulated_cols
							if ( isset ( $input ['display_answers_tabulated_cols'] ) ){
								if ( ctype_digit( $input ['display_answers_tabulated_cols'] ) ){
									if ( $default_options ['display_answers_tabulated_cols'] != trim( $input ['display_answers_tabulated_cols'] ) ){
										$newinput ['display_answers_tabulated_cols'] = trim( $input ['display_answers_tabulated_cols'] );
										$updated .= __( 'Option "Columns for Tabulated Display Answers" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['display_answers_tabulated_cols'] = $default_options ['display_answers_tabulated_cols'];
									$errors .= __( 'Option "Columns for Tabulated Display Answers" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['display_answers'] = $default_options ['display_answers'];
						$errors .= __( 'Option "Display Answers" Not Updated! you must choose between \'vertical\', \'horizontal\' or \'tabulated\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// display_results
				if ( isset ( $input ['display_results'] ) ){
					if ( in_array( $input ['display_results'], array( 'vertical', 'orizontal', 'tabulated' ) ) ){
						if ( $default_options ['display_results'] != trim( $input ['display_results'] ) ){
							$newinput ['display_results'] = trim( $input ['display_results'] );
							$updated .= __( 'Option "Display Results" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'tabulated' == $input ['display_results'] ){
							// display_results_tabulated_cols
							if ( isset ( $input ['display_results_tabulated_cols'] ) ){
								if ( ctype_digit( $input ['display_results_tabulated_cols'] ) ){
									if ( $default_options ['display_results_tabulated_cols'] != trim( $input ['display_results_tabulated_cols'] ) ){
										$newinput ['display_results_tabulated_cols'] = trim( $input ['display_results_tabulated_cols'] );
										$updated .= __( 'Option "Columns for Tabulated Display Results" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['display_results_tabulated_cols'] = $default_options ['display_results_tabulated_cols'];
									$errors .= __( 'Option "Columns for Tabulated Display Results" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['display_results'] = $default_options ['display_results'];
						$errors .= __( 'Option "Display Results" Not Updated! Choose the display layout: \'vertical\', \'horizontal\' or \'tabulated\'', 'yop_poll' ) . $message_delimiter;
					}
				}
				//template_width
				if ( isset ( $input ['template_width'] ) ){
					if ( '' != trim( $input ['template_width'] ) ){
						if ( $default_options ['template_width'] != trim( $input ['template_width'] ) ){
							$newinput ['template_width'] = trim( $input ['template_width'] );
							$updated .= __( 'Option "Poll Template Width" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['template_width'] = $default_options ['template_width'];
						$errors .= __( 'Option "Poll Template Width" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
					}
				}

				//widget_template_width
				if ( isset ( $input ['widget_template_width'] ) ){
					if ( '' != trim( $input ['widget_template_width'] ) ){
						if ( $default_options ['widget_template_width'] != trim( $input ['widget_template_width'] ) ){
							$newinput ['widget_template_width'] = trim( $input ['widget_template_width'] );
							$updated .= __( 'Option "Widget Template Width" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['widget_template_width'] = $default_options ['widget_template_width'];
						$errors .= __( 'Option "Widget Template Width" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// use_template_bar
				if ( isset ( $input ['use_template_bar'] ) ){
					if ( in_array( $input ['use_template_bar'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['use_template_bar'] != trim( $input ['use_template_bar'] ) ){
							$newinput ['use_template_bar'] = trim( $input ['use_template_bar'] );
							$updated .= __( 'Option "Use Template Result Bar" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'no' == $input ['use_template_bar'] ){
							// bar_background
							if ( isset ( $input ['bar_background'] ) ){
								if ( ctype_alnum( $input ['bar_background'] ) ){
									if ( $default_options ['bar_background'] != trim( $input ['bar_background'] ) ){
										$newinput ['bar_background'] = trim( $input ['bar_background'] );
										$updated .= __( 'Option "Result Bar Background Color" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['bar_background'] = $default_options ['bar_background'];
									$errors .= __( 'Option "Result Bar Background Color" Not Updated! Fill in an alphanumeric value!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// bar_height
							if ( isset ( $input ['bar_height'] ) ){
								if ( ctype_digit( $input ['bar_height'] ) ){
									if ( $default_options ['bar_height'] != trim( $input ['bar_height'] ) ){
										$newinput ['bar_height'] = trim( $input ['bar_height'] );
										$updated .= __( 'Option "Result Bar Height" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['bar_height'] = $default_options ['bar_height'];
									$errors .= __( 'Option "Result Bar Height" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// bar_border_color
							if ( isset ( $input ['bar_border_color'] ) ){
								if ( ctype_alnum( $input ['bar_border_color'] ) ){
									if ( $default_options ['bar_border_color'] != trim( $input ['bar_border_color'] ) ){
										$newinput ['bar_border_color'] = trim( $input ['bar_border_color'] );
										$updated .= __( 'Option "Result Bar Border Color" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['bar_border_color'] = $default_options ['bar_border_color'];
									$errors .= __( 'Option "Result Bar Border Color" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// bar_border_width
							if ( isset ( $input ['bar_border_width'] ) ){
								if ( ctype_digit( $input ['bar_border_width'] ) ){
									if ( $default_options ['bar_border_width'] != trim( $input ['bar_border_width'] ) ){
										$newinput ['bar_border_width'] = trim( $input ['bar_border_width'] );
										$updated .= __( 'Option "Result Bar Border Width" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['bar_border_width'] = $default_options ['bar_border_width'];
									$errors .= __( 'Option "Result Bar Border Width" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// bar_border_style
							if ( isset ( $input ['bar_border_style'] ) ){
								if ( ctype_alpha( $input ['bar_border_style'] ) ){
									if ( $default_options ['bar_border_style'] != trim( $input ['bar_border_style'] ) ){
										$newinput ['bar_border_style'] = trim( $input ['bar_border_style'] );
										$updated .= __( 'Option "Result Bar Border Style" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['bar_border_style'] = $default_options ['bar_border_style'];
									$errors .= __( 'Option "Result Bar Border Style" Not Updated! Fill in an alphanumeric value!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['use_template_bar'] = $default_options ['use_template_bar'];
						$errors .= __( 'Option "Use Template Result Bar" Not Updated! Choose "yes" or "no"!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// sorting_answers
				if ( isset ( $input ['sorting_answers'] ) ){
					if ( in_array( $input ['sorting_answers'], array( 'exact', 'alphabetical', 'random', 'votes' ) ) ){
						if ( $default_options ['sorting_answers'] != trim( $input ['sorting_answers'] ) ){
							$newinput ['sorting_answers'] = trim( $input ['sorting_answers'] );
							$updated .= __( 'Option "Sort Answers" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						// sorting_answers_direction
						if ( isset ( $input ['sorting_answers_direction'] ) ){
							if ( in_array( $input ['sorting_answers_direction'], array( 'asc', 'desc' ) ) ){
								if ( $default_options ['sorting_answers_direction'] != trim( $input ['sorting_answers_direction'] ) ){
									$newinput ['sorting_answers_direction'] = trim( $input ['sorting_answers_direction'] );
									$updated .= __( 'Option "Sort Answers Direction" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
							else {
								$newinput ['sorting_answers_direction'] = $default_options ['sorting_answers_direction'];
								$errors .= __( 'Option "Sort Answers Direction" Not Updated! Please choose between \'Ascending\' or \'Descending\'', 'yop_poll' ) . $message_delimiter;
							}
						}
					}
					else {
						$newinput ['sorting_answers'] = $default_options ['sorting_answers'];
						$errors .= __( 'Option "Sort Answers" Not Updated! Please choose between: \'exact\', \'alphabetical\', \'random\' or \'votes\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// sorting_results
				if ( isset ( $input ['sorting_answers'] ) ){
					if ( in_array( $input ['sorting_results'], array( 'exact', 'alphabetical', 'random', 'votes' ) ) ){
						if ( $default_options ['sorting_results'] != trim( $input ['sorting_results'] ) ){
							$newinput ['sorting_results'] = trim( $input ['sorting_results'] );
							$updated .= __( 'Option "Sort Results" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						// sorting_results_direction
						if ( isset ( $input ['sorting_results_direction'] ) ){
							if ( in_array( $input ['sorting_results_direction'], array( 'asc', 'desc' ) ) ){
								if ( $default_options ['sorting_results_direction'] != trim( $input ['sorting_results_direction'] ) ){
									$newinput ['sorting_results_direction'] = trim( $input ['sorting_results_direction'] );
									$updated .= __( 'Option "Sort Results Direction" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
							else {
								$newinput ['sorting_results_direction'] = $default_options ['sorting_results_direction'];
								$errors .= __( 'Option "Sort Results Direction" Not Updated! Please choose between \'Ascending\' or \'Descending\'', 'yop_poll' ) . $message_delimiter;
							}
						}
					}
					else {
						$newinput ['sorting_results'] = $default_options ['sorting_results'];
						$errors .= __( 'Option "Sort Results" Not Updated! Please choose between: \'exact\', \'alphabetical\', \'random\' or \'votes\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// start_date
				if ( isset ( $input ['start_date'] ) ){
					if ( '' != trim( $input ['start_date'] ) ){
						if ( $default_options ['start_date'] != trim( $input ['start_date'] ) ){
							$newinput ['start_date'] = trim( $input ['start_date'] );
							$updated .= __( 'Option "Poll Start Date" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['start_date'] = $default_options ['start_date'];
						$errors .= __( 'Option "Poll Start Date" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// never_expire
				if ( !isset ( $input ['never_expire'] ) ){
					$input ['never_expire'] = 'no';
				}
				if ( 'yes' == $input ['never_expire'] ){
					if ( $default_options ['never_expire'] != trim( $input ['never_expire'] ) ){
						$newinput ['never_expire'] = trim( $input ['never_expire'] );
						$newinput ['end_date']     = '9999-12-31 23:59:59';
						$updated .= __( 'Option "Poll End Date" Updated!', 'yop_poll' ) . $message_delimiter;
					}
				}
				else {
					if ( isset ( $input ['end_date'] ) ){
						if ( '' != $input ['end_date'] ){
							if ( $default_options ['end_date'] != trim( $input ['end_date'] ) ){
								$newinput ['end_date']     = $input ['end_date'];
								$newinput ['never_expire'] = 'no';
								$updated .= __( 'Option "Poll End Date" Updated!', 'yop_poll' ) . $message_delimiter;
							}
						}
						else {
							$errors .= __( 'Option "Poll End Date" Not Updated! The field is empty! ', 'yop_poll' ) . $message_delimiter;
						}
					}
				}

				// view_results
				if ( isset ( $input ['view_results'] ) ){
					if ( in_array( $input ['view_results'], array( 'before', 'after', 'after-poll-end-date', 'never', 'custom-date' ) ) ){
						if ( $default_options ['view_results'] != trim( $input ['view_results'] ) ){
							$newinput ['view_results'] = trim( $input ['view_results'] );
							$updated .= __( 'Option "View Results" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'custom-date' == $newinput ['view_results'] ){
							// view_results_start_date
							if ( isset ( $input ['view_results_start_date'] ) ){
								if ( $default_options ['view_results_start_date'] != trim( $input ['view_results_start_date'] ) ){
									$newinput ['view_results_start_date'] = $input ['view_results_start_date'];
									$updated .= __( 'Option "View Results Custom Date" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['view_results'] = $default_options ['view_results'];
						$errors .= __( 'Option "View Results" Not Updated! Please choose between: \'Before\', \'After\', \'After Poll End Date\', \'Never\' or \'Custom Date\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// vote_permisions
				if ( isset ( $input ['view_results_permissions'] ) ){
					if ( in_array( $input ['view_results_permissions'], array( 'quest-only', 'registered-only', 'guest-registered' ) ) ){
						if ( $default_options ['view_results_permissions'] != trim( $input ['view_results_permissions'] ) ){
							$newinput ['view_results_permissions'] = trim( $input ['view_results_permissions'] );
							$updated .= __( 'Option "View Results Permissions" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['view_results_permissions'] = $default_options ['view_results_permissions'];
						$errors .= __( 'Option "View Results Permissions" Not Updated! Please choose between \'Quest Only\', \'Registered Only\', \'Guest & Registered Users\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// view_results_type
				if ( isset ( $input ['view_results_type'] ) ){
					if ( in_array( $input ['view_results_type'], array( 'votes-number', 'percentages', 'votes-number-and-percentages' ) ) ){
						if ( $default_options ['view_results_type'] != trim( $input ['view_results_type'] ) ){
							$newinput ['view_results_type'] = trim( $input ['view_results_type'] );
							$updated .= __( 'Option "View Results Type" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['view_results_type'] = $default_options ['view_results_type'];
						$errors .= __( 'Option "View Results Type" Not Updated! Please choose between: \'Votes number\', \'Percentages\' or \'Votes number and percentages\' ', 'yop_poll' ) . $message_delimiter;
					}
				}

				// answer_result_label
				if ( isset ( $input ['answer_result_label'] ) ){
					if ( 'votes-number' == $input ['view_results_type'] ){
						if ( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-VOTES%' ) === false ){
							$newinput ['answer_result_label'] = $default_options ['answer_result_label'];
							$errors .= __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-VOTES%!', 'yop_poll' ) . $message_delimiter;
						}
						else {
							if ( $default_options ['answer_result_label'] != trim( $input ['answer_result_label'] ) ){
								$newinput ['answer_result_label'] = trim( $input ['answer_result_label'] );
								$updated .= __( 'Option "Poll Answer Result Label" Updated!', 'yop_poll' ) . $message_delimiter;
							}
						}
					}

					if ( 'percentages' == $input ['view_results_type'] ){
						if ( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-PERCENTAGES%' ) === false ){
							$newinput ['answer_result_label'] = $default_options ['answer_result_label'];
							$errors .= __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-PERCENTAGES%!', 'yop_poll' ) . $message_delimiter;
						}
						else {
							if ( $default_options ['answer_result_label'] != trim( $input ['answer_result_label'] ) ){
								$newinput ['answer_result_label'] = trim( $input ['answer_result_label'] );
								$updated .= __( 'Option "Poll Answer Result Label" Updated!', 'yop_poll' ) . $message_delimiter;
							}
						}
					}

					if ( 'votes-number-and-percentages' == $input ['view_results_type'] ){
						if ( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-PERCENTAGES%' ) === false ){
							$newinput ['answer_result_label'] = $default_options ['answer_result_label'];
							$errors .= __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-VOTES% and %POLL-ANSWER-RESULT-PERCENTAGES%!', 'yop_poll' ) . $message_delimiter;
						}
						elseif ( stripos( $input ['answer_result_label'], '%POLL-ANSWER-RESULT-VOTES%' ) === false ) {
							$newinput ['answer_result_label'] = $default_options ['answer_result_label'];
							$errors .= __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-VOTES% and %POLL-ANSWER-RESULT-PERCENTAGES%!', 'yop_poll' ) . $message_delimiter;
						}
						else {
							if ( $default_options ['answer_result_label'] != trim( $input ['answer_result_label'] ) ){
								$newinput ['answer_result_label'] = trim( $input ['answer_result_label'] );
								$updated .= __( 'Option "Poll Answer Result Label" Updated!', 'yop_poll' ) . $message_delimiter;
							}
						}
					}
				}

				// singular_answer_result_votes_number_label
				if ( isset ( $input ['singular_answer_result_votes_number_label'] ) ){
					if ( '' != $input ['singular_answer_result_votes_number_label'] ){
						if ( $default_options ['singular_answer_result_votes_number_label'] != trim( $input ['singular_answer_result_votes_number_label'] ) ){
							$newinput ['singular_answer_result_votes_number_label'] = trim( $input ['singular_answer_result_votes_number_label'] );
							$updated .= __( 'Option "Poll Answer Result Votes Number Singular Label" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['singular_answer_result_votes_number_label'] = $default_options ['singular_answer_result_votes_number_label'];
						$errors .= __( 'Option "Poll Answer Result Votes Number Singular Label" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// plural_answer_result_votes_number_label
				if ( isset ( $input ['plural_answer_result_votes_number_label'] ) ){
					if ( '' != $input ['singular_answer_result_votes_number_label'] ){
						if ( $default_options ['plural_answer_result_votes_number_label'] != trim( $input ['plural_answer_result_votes_number_label'] ) ){
							$newinput ['plural_answer_result_votes_number_label'] = trim( $input ['plural_answer_result_votes_number_label'] );
							$updated .= __( 'Option "Poll Answer Result Votes Number Plural Label" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['plural_answer_result_votes_number_label'] = $default_options ['plural_answer_result_votes_number_label'];
						$errors .= __( 'Option "Poll Answer Result Votes Number Plural Label" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// vote_button_label
				if ( isset ( $input ['vote_button_label'] ) ){
					if ( '' != $input ['vote_button_label'] ){
						if ( $default_options ['vote_button_label'] != trim( $input ['vote_button_label'] ) ){
							$newinput ['vote_button_label'] = trim( $input ['vote_button_label'] );
							$updated .= __( 'Option "Vote Button Label" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['vote_button_label'] = $default_options ['vote_button_label'];
						$errors .= __( 'Option "Vote Button Label" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// view_results_link
				if ( isset ( $input ['view_results_link'] ) ){
					if ( in_array( $input ['view_results_link'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['view_results_link'] != trim( $input ['view_results_link'] ) ){
							$newinput ['view_results_link'] = trim( $input ['view_results_link'] );
							$updated .= __( 'Option "View Results Link" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['view_results_link'] ){
							// view_results_link_label
							if ( isset ( $input ['view_results_link_label'] ) ){
								if ( '' != $input ['view_results_link_label'] ){
									if ( $default_options ['view_results_link_label'] != trim( $input ['view_results_link_label'] ) ){
										$newinput ['view_results_link_label'] = trim( $input ['view_results_link_label'] );
										$updated .= __( 'Option "View Results Link Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['view_results_link_label'] = $default_options ['view_results_link_label'];
									$errors .= __( 'Option "View Results Link Label" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['view_results_link'] = $default_options ['view_results_link'];
						$errors .= __( 'Option "View Results Link" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// view_back_to_vote_link
				if ( isset ( $input ['view_back_to_vote_link'] ) ){
					if ( in_array( $input ['view_back_to_vote_link'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['view_back_to_vote_link'] != trim( $input ['view_back_to_vote_link'] ) ){
							$newinput ['view_back_to_vote_link'] = trim( $input ['view_back_to_vote_link'] );
							$updated .= __( 'Option "View Back To Vote Link" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['view_back_to_vote_link'] ){
							// view_results_link_label
							if ( isset ( $input ['view_back_to_vote_link_label'] ) ){
								if ( '' != $input ['view_back_to_vote_link_label'] ){
									if ( $default_options ['view_back_to_vote_link_label'] != trim( $input ['view_back_to_vote_link_label'] ) ){
										$newinput ['view_back_to_vote_link_label'] = trim( $input ['view_back_to_vote_link_label'] );
										$updated .= __( 'Option "View Back to Vote Link Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['view_back_to_vote_link_label'] = $default_options ['view_back_to_vote_link_label'];
									$errors .= __( 'Option "View Back to Vote Link Label" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['view_back_to_vote_link'] = $default_options ['view_back_to_vote_link'];
						$errors .= __( 'Option "View Back to Vote Link" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// view_total_votes
				if ( isset ( $input ['view_total_votes'] ) ){
					if ( in_array( $input ['view_total_votes'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['view_total_votes'] != trim( $input ['view_total_votes'] ) ){
							$newinput ['view_total_votes'] = trim( $input ['view_total_votes'] );
							$updated .= __( 'Option "View Total Votes" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						// view_total_votes
						if ( 'yes' == $input ['view_total_votes'] ){
							if ( isset ( $input ['view_total_votes_label'] ) ){
								if ( stripos( $input ['view_total_votes_label'], '%POLL-TOTAL-VOTES%' ) === false ){
									$newinput ['view_total_votes_label'] = $default_options ['view_total_votes_label'];
									$errors .= __( 'You must use %POLL-TOTAL-VOTES% to define your Total Votes label!', 'yop_poll' ) . $message_delimiter;
								}
								else {
									if ( $default_options ['view_total_votes_label'] != trim( $input ['view_total_votes_label'] ) ){
										$newinput ['view_total_votes_label'] = trim( $input ['view_total_votes_label'] );
										$updated .= __( 'Option "View Total Votes Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
							}
						}
					}
					else {
						$newinput ['view_total_votes'] = $default_options ['view_total_votes'];
						$errors .= __( 'Option "View Total Votes" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// view_total_answers
				if ( isset ( $input ['view_total_answers'] ) ){
					if ( in_array( $input ['view_total_answers'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['view_total_answers'] != trim( $input ['view_total_answers'] ) ){
							$newinput ['view_total_answers'] = trim( $input ['view_total_answers'] );
							$updated .= __( 'Option "View Total Answers" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						// view_total_answers
						if ( 'yes' == $input ['view_total_answers'] ){
							if ( isset ( $input ['view_total_answers_label'] ) ){
								if ( stripos( $input ['view_total_answers_label'], '%POLL-TOTAL-ANSWERS%' ) === false ){
									$newinput ['view_total_answers_label'] = $default_options ['view_total_answers_label'];
									$errors .= __( 'You must use %POLL-TOTAL-ANSWERS% to define your Total Answers label!', 'yop_poll' ) . $message_delimiter;
								}
								else {
									if ( $default_options ['view_total_answers_label'] != trim( $input ['view_total_answers_label'] ) ){
										$newinput ['view_total_answers_label'] = trim( $input ['view_total_answers_label'] );
										$updated .= __( 'Option "View Total Answers Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
							}
						}
					}
					else {
						$newinput ['view_total_answers'] = $default_options ['view_total_answers'];
						$errors .= __( 'Option "View Total Answers" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				if ( isset ( $input ['message_after_vote'] ) ){
					if ( $default_options ['message_after_vote'] != trim( $input ['message_after_vote'] ) ){
						$newinput ['message_after_vote'] = trim( $input ['message_after_vote'] );
						$updated .= __( 'Option "Message After Vote" Updated!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// use_default_loading_image
				if ( isset ( $input ['use_default_loading_image'] ) ){
					if ( in_array( $input ['use_default_loading_image'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['use_default_loading_image'] != trim( $input ['use_default_loading_image'] ) ){
							$newinput ['use_default_loading_image'] = trim( $input ['use_default_loading_image'] );
							$updated .= __( 'Option "Use Default Loading Image" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'no' == $input ['use_default_loading_image'] ){
							if ( isset ( $input ['loading_image_url'] ) ){
								if ( stripos( $input ['loading_image_url'], 'http' ) === false ){
									$newinput ['loading_image_url'] = $default_options ['loading_image_url'];
									$errors .= __( 'You must use a url like "http://.." to define your Loading Image Url!', 'yop_poll' ) . $message_delimiter;
								}
								else {
									if ( $default_options ['loading_image_url'] != trim( $input ['loading_image_url'] ) ){
										$newinput ['loading_image_url'] = trim( $input ['loading_image_url'] );
										$updated .= __( 'Option "Loading Image Url" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
							}
						}
					}
					else {
						$newinput ['use_default_loading_image'] = $default_options ['use_default_loading_image'];
						$errors .= __( 'Option "Use Default Loading Image" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// vote_permisions
				if ( isset ( $input ['vote_permisions'] ) ){
					if ( in_array( $input ['vote_permisions'], array( 'quest-only', 'registered-only', 'guest-registered' ) ) ){
						if ( $default_options ['vote_permisions'] != trim( $input ['vote_permisions'] ) ){
							$newinput ['vote_permisions'] = trim( $input ['vote_permisions'] );
							$updated .= __( 'Option "Vote Permissions" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( in_array( $input ['vote_permisions'], array( 'registered-only', 'guest-registered' ) ) ){

							if ( isset( $input['vote_permisions_facebook'] ) && in_array( $input['vote_permisions_facebook'], array( 'yes', 'no' ) ) ){
								if ( $default_options ['vote_permisions_facebook'] != trim( $input ['vote_permisions_facebook'] ) ){
									$newinput ['vote_permisions_facebook'] = trim( $input ['vote_permisions_facebook'] );
									$updated .= __( 'Option "Vote as Facebook User" Updated!', 'yop_poll' ) . $message_delimiter;
								}
								if ( 'yes' == $input['vote_permisions_facebook'] ){
									if ( $default_options ['vote_permisions_facebook_label'] != trim( $input ['vote_permisions_facebook_label'] ) ){
										$newinput ['vote_permisions_facebook_label'] = trim( $input ['vote_permisions_facebook_label'] );
										$updated .= __( 'Option "Vote as Facebook User Buton Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
							}
							if ( isset( $input['vote_permisions_wordpress'] ) && in_array( $input['vote_permisions_wordpress'], array( 'yes', 'no' ) ) ){
								if ( $default_options ['vote_permisions_wordpress'] != trim( $input ['vote_permisions_wordpress'] ) ){
									$newinput ['vote_permisions_wordpress'] = trim( $input ['vote_permisions_wordpress'] );
									$updated .= __( 'Option "Vote as Wordpress User" Updated!', 'yop_poll' ) . $message_delimiter;
								}

								if ( 'yes' == $input['vote_permisions_wordpress'] ){
									if ( $default_options ['vote_permisions_wordpress_label'] != trim( $input ['vote_permisions_wordpress_label'] ) ){
										$newinput ['vote_permisions_wordpress_label'] = trim( $input ['vote_permisions_wordpress_label'] );
										$updated .= __( 'Option "Vote as Wordpress User Buton Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
							}
							if ( isset( $input['vote_permisions_anonymous'] ) && in_array( $input['vote_permisions_anonymous'], array( 'yes', 'no' ) ) ){
								if ( $default_options ['vote_permisions_anonymous'] != trim( $input ['vote_permisions_anonymous'] ) ){
									$newinput ['vote_permisions_anonymous'] = trim( $input ['vote_permisions_anonymous'] );
									$updated .= __( 'Option "Vote as Anonymous User" Updated!', 'yop_poll' ) . $message_delimiter;
								}

								if ( 'yes' == $input['vote_permisions_anonymous'] ){
									if ( $default_options ['vote_permisions_anonymous_label'] != trim( $input ['vote_permisions_anonymous_label'] ) ){
										$newinput ['vote_permisions_anonymous_label'] = trim( $input ['vote_permisions_anonymous_label'] );
										$updated .= __( 'Option "Vote as Anonymous User Buton Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
							}
						}
					}
					else {
						$newinput ['vote_permisions'] = $default_options ['vote_permisions'];
						$errors .= __( 'Option "Vote Permissions" Not Updated! Please choose between \'Quest Only\', \'Registered Only\', \'Guest & Registered Users\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// blocking_voters
				if ( isset ( $input ['blocking_voters'] ) ){
					if ( in_array( $input ['blocking_voters'], array( 'dont-block', 'cookie', 'ip', 'username', 'cookie-ip' ) ) ){
						if ( $default_options ['blocking_voters'] != trim( $input ['blocking_voters'] ) ){
							$newinput ['blocking_voters'] = trim( $input ['blocking_voters'] );
							$updated .= __( 'Option "Blocking Voters" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'dont-block' != $newinput ['blocking_voters'] ){
							// blocking_voters_interval_value
							if ( isset ( $input ['blocking_voters_interval_value'] ) ){
								if ( ctype_digit( $input ['blocking_voters_interval_value'] ) ){
									if ( $default_options ['blocking_voters_interval_value'] != trim( $input ['blocking_voters_interval_value'] ) ){
										$newinput ['blocking_voters_interval_value'] = trim( $input ['blocking_voters_interval_value'] );
										$updated .= __( 'Option "Blocking Voters Interval Value" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['blocking_voters_interval_value'] = $default_options ['blocking_voters_interval_value'];
									$errors .= __( 'Option "Blocking Voters Interval Value" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// blocking_voters_interval_unit
							if ( isset ( $input ['blocking_voters_interval_unit'] ) ){
								if ( in_array( $input ['blocking_voters_interval_unit'], array( 'seconds', 'minutes', 'hours', 'days' ) ) ){
									if ( $default_options ['blocking_voters_interval_unit'] != trim( $input ['blocking_voters_interval_unit'] ) ){
										$newinput ['blocking_voters_interval_unit'] = trim( $input ['blocking_voters_interval_unit'] );
										$updated .= __( 'Option "Blocking Voters Interval Unit" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['blocking_voters_interval_unit'] = $default_options ['blocking_voters_interval_unit'];
									$errors .= __( 'Option "Blocking Voters Interval Unit" Not Updated! Please choose between \'Seconds\', \'Minutes\', \'Hours\' or \'Days\'', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['blocking_voters'] = $default_options ['blocking_voters'];
						$errors .= __( 'Option "Blocking Voters" Not Updated! Please choose between: \'Don`t Block\', \'Cookie\', \'Ip\', \'Username\', \'Cookie and Ip\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// limit_number_of_votes_per_user
				if ( isset ( $input ['limit_number_of_votes_per_user'] ) ){
					if ( in_array( $input ['limit_number_of_votes_per_user'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['limit_number_of_votes_per_user'] != trim( $input ['limit_number_of_votes_per_user'] ) ){
							$newinput ['limit_number_of_votes_per_user'] = trim( $input ['limit_number_of_votes_per_user'] );
							$updated .= __( 'Option "Limit Number of Votes per User" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['limit_number_of_votes_per_user'] ){
							if ( isset ( $input ['number_of_votes_per_user'] ) ){
								if ( intval( $input ['number_of_votes_per_user'] ) <= 0 ){
									$newinput ['number_of_votes_per_user'] = $default_options ['number_of_votes_per_user'];
									$errors .= __( '"Number of Votes per User" must be a number > 0 !', 'yop_poll' ) . $message_delimiter;
								}
								else {
									if ( $default_options ['number_of_votes_per_user'] != $input ['number_of_votes_per_user'] ){
										$newinput ['number_of_votes_per_user'] = trim( $input ['number_of_votes_per_user'] );
										$updated .= __( 'Option "Number of Votes per User" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
							}
						}
					}
					else {
						$newinput ['use_default_loading_image'] = $default_options ['use_default_loading_image'];
						$errors .= __( 'Option "Use Default Loading Image" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// percentages_decimals
				if ( isset ( $input ['percentages_decimals'] ) ){
					if ( ctype_digit( $input ['percentages_decimals'] ) ){
						if ( $default_options ['percentages_decimals'] != trim( $input ['percentages_decimals'] ) ){
							$newinput ['percentages_decimals'] = trim( $input ['percentages_decimals'] );
							$updated .= __( 'Option "Percentages Decimals" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['percentages_decimals'] = $default_options ['percentages_decimals'];
						$errors .= __( 'Option "Percentages Decimals" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// redirect_after_vote
				if ( isset ( $input ['redirect_after_vote'] ) ){
					if ( in_array( $input ['redirect_after_vote'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['redirect_after_vote'] != trim( $input ['redirect_after_vote'] ) ){
							$newinput ['redirect_after_vote'] = trim( $input ['redirect_after_vote'] );
							$updated .= __( 'Option "Redirect After Vote" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['redirect_after_vote'] ){
							// archive_order
							if ( isset ( $input ['redirect_after_vote_url'] ) ){
								if ( '' != $input ['redirect_after_vote_url'] ){
									if ( $default_options ['redirect_after_vote_url'] != trim( $input ['redirect_after_vote_url'] ) ){
										$newinput ['redirect_after_vote_url'] = trim( $input ['redirect_after_vote_url'] );
										$updated .= __( 'Option "Redirect After Vote Url" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['redirect_after_vote_url'] = $default_options ['redirect_after_vote_url'];
									$errors .= __( 'Option "Redirect After Vote Url" Not Updated! Please fill in an url!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['redirect_after_vote'] = $default_options ['redirect_after_vote'];
						$errors .= __( 'Option ""Redirect After Vote" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// date_format
				if ( isset ( $input ['date_format'] ) ){
					if ( $default_options ['date_format'] != trim( $input ['date_format'] ) ){
						$newinput ['date_format'] = trim( $input ['date_format'] );
						$updated .= __( 'Option "Poll Date Format" Updated!', 'yop_poll' ) . $message_delimiter;
					}
				}

				// view_poll_archive_link
				if ( isset ( $input ['view_poll_archive_link'] ) ){
					if ( in_array( $input ['view_poll_archive_link'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['view_poll_archive_link'] != trim( $input ['view_poll_archive_link'] ) ){
							$newinput ['view_poll_archive_link'] = trim( $input ['view_poll_archive_link'] );
							$updated .= __( 'Option "View Poll Archive Link" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['view_poll_archive_link'] ){
							// view_results_link_label
							if ( isset ( $input ['view_poll_archive_link_label'] ) ){
								if ( '' != $input ['view_poll_archive_link_label'] ){
									if ( $default_options ['view_poll_archive_link_label'] != trim( $input ['view_poll_archive_link_label'] ) ){
										$newinput ['view_poll_archive_link_label'] = trim( $input ['view_poll_archive_link_label'] );
										$updated .= __( 'Option "View Poll Archive Link Label" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['view_poll_archive_link_label'] = $default_options ['view_poll_archive_link_label'];
									$errors .= __( 'Option "View Poll Archive Link Label" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}

							if ( isset ( $input ['poll_archive_url'] ) ){
								if ( '' != $input ['poll_archive_url'] ){
									if ( $default_options ['poll_archive_url'] != trim( $input ['poll_archive_url'] ) ){
										$newinput ['poll_archive_url'] = trim( $input ['poll_archive_url'] );
										$updated .= __( 'Option "Poll Archive URL" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['poll_archive_url'] = $default_options ['poll_archive_url'];
									$errors .= __( 'Option "Poll Archive URL" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['view_poll_archive_link'] = $default_options ['view_poll_archive_link'];
						$errors .= __( 'Option "View Poll Archive Link" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// show_in_archive
				if ( isset ( $input ['show_in_archive'] ) ){
					if ( in_array( $input ['show_in_archive'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['show_in_archive'] != trim( $input ['show_in_archive'] ) ){
							$newinput ['show_in_archive'] = trim( $input ['show_in_archive'] );
							$updated .= __( 'Option "Show Poll in Arhive" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['show_in_archive'] ){
							// archive_order
							if ( isset ( $input ['archive_order'] ) ){
								if ( ctype_digit( $input ['archive_order'] ) ){
									if ( $default_options ['archive_order'] != trim( $input ['archive_order'] ) ){
										$newinput ['archive_order'] = trim( $input ['archive_order'] );
										$updated .= __( 'Option "Archive Order" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['archive_order'] = $default_options ['archive_order'];
									$errors .= __( 'Option "Archive Order" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['show_in_archive'] = $default_options ['show_in_archive'];
						$errors .= __( 'Option "Show Poll in Archive" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// send_email_notifications
				if ( isset ( $input ['send_email_notifications'] ) ){
					if ( in_array( $input ['send_email_notifications'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['send_email_notifications'] != trim( $input ['send_email_notifications'] ) ){
							$newinput ['send_email_notifications'] = trim( $input ['send_email_notifications'] );
							$updated .= __( 'Option "Send Email Notifications" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['send_email_notifications'] ){
							// email_notifications_from_name
							if ( isset ( $input ['email_notifications_from_name'] ) ){
								if ( '' != $input ['email_notifications_from_name'] ){
									if ( $default_options ['email_notifications_from_name'] != trim( $input ['email_notifications_from_name'] ) ){
										$newinput ['email_notifications_from_name'] = trim( $input ['email_notifications_from_name'] );
										$updated .= __( 'Option "Notifications From Name" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['email_notifications_from_name'] = $default_options ['email_notifications_from_name'];
									$errors .= __( 'Option "Notifications From Name" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// email_notifications_from_email
							if ( isset ( $input ['email_notifications_from_email'] ) ){
								if ( '' != $input ['email_notifications_from_email'] ){
									if ( $default_options ['email_notifications_from_email'] != trim( $input ['email_notifications_from_email'] ) ){
										$newinput ['email_notifications_from_email'] = trim( $input ['email_notifications_from_email'] );
										$updated .= __( 'Option "Notifications From Email" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['email_notifications_from_email'] = $default_options ['email_notifications_from_email'];
									$errors .= __( 'Option "Notifications From Email" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// email_notifications_recipients
							if ( isset ( $input ['email_notifications_recipients'] ) ){
								if ( '' != $input ['email_notifications_recipients'] ){
									if ( $default_options ['email_notifications_recipients'] != trim( $input ['email_notifications_recipients'] ) ){
										$newinput ['email_notifications_recipients'] = trim( $input ['email_notifications_recipients'] );
										$updated .= __( 'Option "Email Notifications Recipients" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['email_notifications_recipients'] = $default_options ['email_notifications_recipients'];
									$errors .= __( 'Option "Email Notifications Recipients" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// email_notifications_subject
							if ( isset ( $input ['email_notifications_subject'] ) ){
								if ( '' != $input ['email_notifications_subject'] ){
									if ( $default_options ['email_notifications_subject'] != trim( $input ['email_notifications_subject'] ) ){
										$newinput ['email_notifications_subject'] = trim( $input ['email_notifications_subject'] );
										$updated .= __( 'Option "Email Notifications Subject" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['email_notifications_subject'] = $default_options ['email_notifications_subject'];
									$errors .= __( 'Option "Email Notifications Subject" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}

							// email_notifications_subject
							if ( isset ( $input ['email_notifications_body'] ) ){
								if ( '' != $input ['email_notifications_body'] ){
									if ( $default_options ['email_notifications_body'] != trim( $input ['email_notifications_body'] ) ){
										$newinput ['email_notifications_body'] = trim( $input ['email_notifications_body'] );
										$updated .= __( 'Option "Email Notifications Body" Updated!', 'yop_poll' ) . $message_delimiter;
									}
								}
								else {
									$newinput ['email_notifications_body'] = $default_options ['email_notifications_body'];
									$errors .= __( 'Option "Email Notifications Body" Not Updated! The field is empty!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['send_email_notifications'] = $default_options ['send_email_notifications'];
						$errors .= __( 'Option "Send Email Notifications" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				// archive_polls_per_page
				if ( isset ( $input ['archive_polls_per_page'] ) ){
					if ( ctype_digit( $input ['archive_polls_per_page'] ) ){
						if ( $default_options ['archive_polls_per_page'] != trim( $input ['archive_polls_per_page'] ) ){
							$newinput ['archive_polls_per_page'] = trim( $input ['archive_polls_per_page'] );
							$updated .= __( 'Option "Archive Polls Per Page', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['archive_polls_per_page'] = $default_options ['archive_polls_per_page'];
						$errors .= __( 'Option "Archive Polls Per Page" Not Updated! Please fill in a number!', 'yop_poll' ) . $message_delimiter;
					}
				}

				//share after vote
				if ( isset ( $input ['share_after_vote'] ) ){
					if ( in_array( $input ['share_after_vote'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['share_after_vote'] != trim( $input ['share_after_vote'] ) ){
							$newinput ['share_after_vote'] = trim( $input ['share_after_vote'] );
							$updated .= __( 'Option "Share After Vote" Updated!', 'yop_poll' ) . $message_delimiter;
						}

						if ( 'yes' == $input ['share_after_vote'] ){
							// share_name
							if ( isset ( $input ['share_name'] ) ){
								if ( $default_options ['share_name'] != trim( $input ['share_name'] ) ){
									$newinput ['share_name'] = trim( $input ['share_name'] );
									$updated .= __( 'Option "Share Name" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
							// share_caption
							if ( isset ( $input ['share_caption'] ) ){
								if ( $default_options ['share_caption'] != trim( $input ['share_caption'] ) ){
									$newinput ['share_caption'] = trim( $input ['share_caption'] );
									$updated .= __( 'Option "Share Caption" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
							// share_description
							if ( isset ( $input ['share_description'] ) ){
								if ( $default_options ['share_description'] != trim( $input ['share_description'] ) ){
									$newinput ['share_description'] = trim( $input ['share_description'] );
									$updated .= __( 'Option "Share Description" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
							// share_picture
							if ( isset ( $input ['share_picture'] ) ){
								if ( $default_options ['share_picture'] != trim( $input ['share_picture'] ) ){
									$newinput ['share_picture'] = trim( $input ['share_picture'] );
									$updated .= __( 'Option "Share Picture" Updated!', 'yop_poll' ) . $message_delimiter;
								}
							}
						}
					}
					else {
						$newinput ['share_after_vote'] = $default_options ['share_after_vote'];
						$errors .= __( 'Option "Share After Vote" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}

				//start_scheduler
				if ( isset ( $input ['start_scheduler'] ) ){
					if ( in_array( $input ['start_scheduler'], array( 'yes', 'no' ) ) ){
						if ( $default_options ['start_scheduler'] != trim( $input ['start_scheduler'] ) ){
							$newinput ['start_scheduler'] = trim( $input ['start_scheduler'] );
							$updated .= __( 'Option "Start Scheduler" Updated!', 'yop_poll' ) . $message_delimiter;
						}
					}
					else {
						$newinput ['start_scheduler'] = $default_options ['start_scheduler'];
						$errors .= __( 'Option "Start Scheduler" Not Updated! Please choose between \'yes\' or \'no\'', 'yop_poll' ) . $message_delimiter;
					}
				}
			}
			else {
				$errors .= __( 'Bad Request!', 'yop_poll' ) . $message_delimiter;
			}

			if ( '' != $errors )
				add_settings_error( 'general', 'yop-poll-errors', $errors, 'error' );
			if ( '' != $updated )
				add_settings_error( 'general', 'yop-poll-updates', $updated, 'updated' );

			return $newinput;
		}

		public function view_yop_poll_options() {
			require_once( ABSPATH . '/wp-admin/options-head.php' );
			global $page;
			$default_options = get_option( 'yop_poll_options', array() );
			$pro_options     = get_option( 'yop_poll_pro_options' );
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php _e( 'Yop Poll Options', 'yop_poll' ); ?></h2>
			<div id="message"></div>
			<br/>

			<form action="options.php" method="post">
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<?php settings_fields( 'yop_poll_options' ); ?>
							<div class="meta-box-sortables ui-sortable" id="normal-sortables">
								<div class="postbox" id="yop-poll-advanced-options-div1">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Answers options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th>
														<?php _e( 'Allow other answers ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-allow-other-answers-no"><input
																id="yop-poll-allow-other-answers-no"
																<?php echo 'no' == $default_options['allow_other_answers'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[allow_other_answers]"
															value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
															for="yop-poll-allow-other-answers-yes"><input
																id="yop-poll-allow-other-answers-yes"
																<?php echo 'yes' == $default_options['allow_other_answers'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[allow_other_answers]"
															value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-other-answers-label-div" style="<?php echo 'no' == $default_options['allow_other_answers'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Other Answer Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-other-answers-label" type="text"
															name="yop_poll_options[other_answers_label]"
															value="<?php echo isset( $other_answer[0]['answer'] ) ? esc_html( stripslashes( $other_answer[0]['answer'] ) ) : $default_options['other_answers_label'] ?>"/>
														<input type="hidden"
															name="yop_poll_options[other_answers_id]"
															value="<?php echo isset( $other_answer[0]['id'] ) ? $other_answer[0]['id'] : '' ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-other-answers-to-results-div" style="<?php echo 'no' == $default_options['allow_other_answers'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Add the values submitted in "Other" as answers ', 'yop_poll' ); ?>
														:<br><font size="0"><?php _e( 'all the values submitted in this field by your users will be automatically added as an available "Answer"', 'yop_poll' ) ?></font>
													</th>
													<td>
														<label for="yop-poll-add-other-answers-to-default-answers-no"><input
																id="yop-poll-add-other-answers-to-default-answers-no"
																<?php echo 'no' == $default_options['add_other_answers_to_default_answers'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[add_other_answers_to_default_answers]"
															value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
															for="yop-poll-add-other-answers-to-default-answers-yes"><input
																id="yop-poll-add-other-answers-to-default-answers-yes"
																<?php echo 'yes' == $default_options['add_other_answers_to_default_answers'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[add_other_answers_to_default_answers]"
															value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-display-other-answers-values-div" style="<?php echo 'no' == $default_options['allow_other_answers'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Display Other Answers Values', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-display-other-answers-values-no"><input
																id="yop-poll-display-other-answers-values-no"
																<?php echo 'no' == $default_options['display_other_answers_values'] ? 'checked="checked"' : ''; ?>
																type="radio"
																name="yop_poll_options[display_other_answers_values]"
															value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
															for="yop-poll-display-other-answers-values-yes"><input
																id="yop-poll-display-other-answers-values-yes"
																<?php echo 'yes' == $default_options['display_other_answers_values'] ? 'checked="checked"' : ''; ?>
																type="radio"
																name="yop_poll_options[display_other_answers_values]"
															value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Allow Multiple Answers ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-allow-multiple-answers-no"><input
																id="yop-poll-allow-multiple-answers-no"
																<?php echo $default_options['allow_multiple_answers'] == 'no' ? 'checked="checked"' : ''; ?>
																type="radio"
															name="yop_poll_options[allow_multiple_answers]" value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label>
														<label for="yop-poll-allow-multiple-answers-yes"><input
																id="yop-poll-allow-multiple-answers-yes"
																<?php echo $default_options['allow_multiple_answers'] == 'yes' ? 'checked="checked"' : ''; ?>
																type="radio"
															name="yop_poll_options[allow_multiple_answers]" value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>

													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-allow-multiple-answers-div" style="<?php echo $default_options['allow_multiple_answers'] == 'no' ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Max Number of allowed answers', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-allow-multiple-answers-number"
															type="text"
															name="yop_poll_options[allow_multiple_answers_number]"
															value="<?php echo $default_options['allow_multiple_answers_number']; ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-allow-multiple-answers-div1" style="<?php echo $default_options['allow_multiple_answers'] == 'no' ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Min Number of allowed answers', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-allow-multiple-answers-min-number"
															type="text"
															name="yop_poll_options[allow_multiple_answers_min_number]"
															value="<?php echo $default_options['allow_multiple_answers_min_number']; ?>"/>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div2">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Display Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th>
														<?php _e( 'Use CAPTCHA ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-use-captcha-no"><input
																id="yop-poll-use-captcha-no"
																<?php echo 'no' == $default_options['use_captcha'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[use_captcha]"
															value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
															for="yop-poll-use-captcha-yes"><input
																id="yop-poll-use-captcha-yes"
																<?php echo 'yes' == $default_options['use_captcha'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[use_captcha]"
															value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Display Answers ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-display-answers-vertical"><input
																id="yop-poll-display-answers-vertical"
																<?php echo $default_options['display_answers'] == 'vertical' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[display_answers]"
															value="vertical"/> <?php _e( 'Vertical', 'yop_poll' ); ?></label>
														<label for="yop-poll-display-answers-orizontal"><input
																id="yop-poll-display-answers-orizontal"
																<?php echo $default_options['display_answers'] == 'orizontal' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[display_answers]"
															value="orizontal"/> <?php _e( 'Horizontal', 'yop_poll' ); ?></label>
														<label for="yop-poll-display-answers-tabulated"><input
																id="yop-poll-display-answers-tabulated"
																<?php echo $default_options['display_answers'] == 'tabulated' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[display_answers]"
															value="tabulated"/> <?php _e( 'Tabulated', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-display-answers-tabulated-div" style="<?php echo $default_options['display_answers'] != 'tabulated' ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Columns', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-display-answers-tabulated-cols"
															type="text"
															name="yop_poll_options[display_answers_tabulated_cols]"
															value="<?php echo $default_options['display_answers_tabulated_cols']; ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Display Results ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-display-results-vertical"><input
																id="yop-poll-display-results-vertical"
																<?php echo $default_options['display_results'] == 'vertical' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[display_results]"
															value="vertical"> <?php _e( 'Vertical', 'yop_poll' ); ?></label>
														<label for="yop-poll-display-results-orizontal"><input
																id="yop-poll-display-results-orizontal"
																<?php echo $default_options['display_results'] == 'orizontal' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[display_results]"
															value="orizontal"> <?php _e( 'Horizontal', 'yop_poll' ); ?></label>
														<label for="yop-poll-display-results-tabulated"><input
																id="yop-poll-display-results-tabulated"
																<?php echo $default_options['display_results'] == 'tabulated' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[display_results]"
															value="tabulated"> <?php _e( 'Tabulated', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-display-results-tabulated-div" style="<?php echo $default_options['display_results'] != 'tabulated' ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Columns', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-display-results-tabulated-cols"
															type="text"
															name="yop_poll_options[display_results_tabulated_cols]"
															value="<?php echo $default_options['display_results_tabulated_cols']; ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Poll Template Width', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-template-width"
															type="text"
															name="yop_poll_options[template_width]"
															value="<?php echo $default_options['template_width']; ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Widget Template Width', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-widget-template-width"
															type="text"
															name="yop_poll_options[widget_template_width]"
															value="<?php echo $default_options['widget_template_width']; ?>"/>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div3">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Poll Bar Style', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th>
														<?php _e( 'Use Template Result Bar', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-use-template-bar-no"><input
																id="yop-poll-use-template-bar-no"
																<?php echo 'no' == $default_options['use_template_bar'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[use_template_bar]"
															value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
															for="yop-poll-use-template-bar-yes"><input
																id="yop-poll-use-template-bar-yes"
																<?php echo 'yes' == $default_options['use_template_bar'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[use_template_bar]"
															value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
												</tr>
												<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
													<th><label for="yop-poll-bar-background"><?php _e( 'Background Color', 'yop_poll' ); ?></label>
													</th>
													<td>#<input class="yop-small-input"
															id="yop-poll-bar-background"
															value="<?php echo $default_options['bar_background']; ?>"
															onblur="yop_poll_update_bar_style('#yop-poll-bar-preview', 'background-color', '#' + this.value)"
															type="text" name="yop_poll_options[bar_background]"/>
													</td>
												</tr>
												<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
													<th><label for="yop-poll-bar-height"><?php _e( 'Height', 'yop_poll' ); ?></label>
													</th>
													<td><input class="yop-small-input" id="yop-poll-bar-height"
															value="<?php echo $default_options['bar_height']; ?>"
															onblur="yop_poll_update_bar_style('#yop-poll-bar-preview', 'height', this.value + 'px')"
															type="text" name="yop_poll_options[bar_height]"/> px</td>
												</tr>
												<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
													<th><label for="yop-poll-bar-border-color"><?php _e( 'Border Color', 'yop_poll' ) ?></label>
													</th>
													<td>#<input class="yop-small-input"
															id="yop-poll-bar-border-color"
															value="<?php echo $default_options['bar_border_color']; ?>"
															onblur="yop_poll_update_bar_style( '#yop-poll-bar-preview', 'border-color', '#' + this.value )"
															type="text" name="yop_poll_options[bar_border_color]"/>
													</td>
												</tr>
												<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
													<th><label for="yop-poll-bar-border-width"><?php _e( 'Border Width', 'yop_poll' ); ?></label>
													</th>
													<td><input class="yop-small-input"
															id="yop-poll-bar-border-width"
															value="<?php echo $default_options['bar_border_width']; ?>"
															onblur="yop_poll_update_bar_style('#yop-poll-bar-preview', 'border-width', this.value + 'px')"
															type="text" name="yop_poll_options[bar_border_width]"/> px
													</td>
												</tr>
												<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
													<th><label for="yop-poll-bar-border-style"><?php _e( 'Border Style', 'yop_poll' ); ?></label>
													</th>
													<td><select id="yop-poll-bar-border-style"
															onchange="yop_poll_update_bar_style('#yop-poll-bar-preview', 'border-style', this.value)"
															name="yop_poll_options[bar_border_style]">
															<option
																<?php print 'solid' == $default_options['bar_border_style'] ? 'selected="selected"' : ''; ?>
																value="solid">Solid</option>
															<option
																<?php print 'dashed' == $default_options['bar_border_style'] ? 'selected="selected"' : ''; ?>
																value="dashed">Dashed</option>
															<option
																<?php print 'dotted' == $default_options['bar_border_style'] ? 'selected="selected"' : ''; ?>
																value="dotted">Dotted</option>
														</select></td>
												</tr>
												<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
													<th><label><?php _e( 'Yop Poll Bar Preview', 'yop_poll' ); ?></label>
													</th>
													<td>
														<div id="yop-poll-bar-preview"; style="width: 100px; height: <?php echo $default_options['bar_height']; ?>
														px; background-color:#<?php

															echo $default_options ['bar_background'];
														?>
														; border-style: <?php echo $default_options['bar_border_style']; ?>
														; border-width: <?php echo $default_options['bar_border_width']; ?>
														px; border-color: #<?php

															echo $default_options ['bar_border_color'];
														?>;"></div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div4">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Sorting Answers &amp; Results', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th><?php _e( 'Sort Answers', 'yop_poll' ); ?>:</th>
													<td valign="top"><label for="yop_poll_sorting_answers_exact"><input
																id="yop_poll_sorting_answers_exact"
																<?php echo $default_options['sorting_answers'] == 'exact' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_answers]"
															value="exact"> <?php _e( 'Exact Order', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_answers_alphabetical"><input
																id="yop_poll_sorting_answers_alphabetical"
																<?php echo $default_options['sorting_answers'] == 'alphabetical' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_answers]"
															value="alphabetical"> <?php _e( 'Alphabetical Order', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_answers_random"><input
																id="yop_poll_sorting_answers_random"
																<?php echo $default_options['sorting_answers'] == 'random' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_answers]"
															value="random"> <?php _e( 'Random Order', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_answers_votes"><input
																id="yop_poll_sorting_answers_votes"
																<?php echo $default_options['sorting_answers'] == 'votes' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_answers]"
															value="votes"> <?php _e( 'Number of Votes', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Sort Answers Rule', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop_poll_sorting_answers_asc"><input
																id="yop_poll_sorting_answers_asc"
																<?php echo $default_options['sorting_answers_direction'] == 'asc' ? 'checked="checked"' : ''; ?>
																type="radio"
																name="yop_poll_options[sorting_answers_direction]"
															value="asc"> <?php _e( 'Ascending', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_answers_desc"><input
																id="yop_poll_sorting_answers_desc"
																<?php echo $default_options['sorting_answers_direction'] == 'desc' ? 'checked="checked"' : ''; ?>
																type="radio"
																name="yop_poll_options[sorting_answers_direction]"
															value="desc"> <?php _e( 'Descending', 'yop_poll' ); ?> </label>
													</td>
												</tr>
												<tr>
													<th><?php _e( 'Sorting Results', 'yop_poll' ); ?>:</th>
													<td valign="top"><label for="yop_poll_sorting_results_exact"><input
																id="yop_poll_sorting_results_exact"
																<?php echo $default_options['sorting_results'] == 'exact' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_results]"
															value="exact"> <?php _e( 'Exact Order', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_results_alphabetical"><input
																id="yop_poll_sorting_results_alphabetical"
																<?php echo $default_options['sorting_results'] == 'alphabetical' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_results]"
															value="alphabetical"> <?php _e( 'Alphabetical Order', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_results_random"><input
																id="yop_poll_sorting_results_random"
																<?php echo $default_options['sorting_results'] == 'random' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_results]"
															value="random"> <?php _e( 'Random Order', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_results_votes"><input
																id="yop_poll_sorting_results_votes"
																<?php echo $default_options['sorting_results'] == 'votes' ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[sorting_results]"
															value="votes"> <?php _e( 'Number of Votes', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Sorting Results Rule', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop_poll_sorting_results_asc"><input
																id="yop_poll_sorting_results_asc"
																<?php echo $default_options['sorting_results_direction'] == 'asc' ? 'checked="checked"' : ''; ?>
																type="radio"
																name="yop_poll_options[sorting_results_direction]"
															value="asc"> <?php _e( 'Ascending', 'yop_poll' ); ?></label>
														<label for="yop_poll_sorting_results_desc"><input
																id="yop_poll_sorting_results_desc"
																<?php echo $default_options['sorting_results_direction'] == 'desc' ? 'checked="checked"' : ''; ?>
																type="radio"
																name="yop_poll_options[sorting_results_direction]"
															value="desc"> <?php _e( 'Descending', 'yop_poll' ); ?></label>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div5">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Poll Start/End Date', 'yop_poll' ); ?>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th><label for="yop-poll-start-date-input"><?php _e( 'Start Date', 'yop_poll' ); ?>
															:</label><br><font size="0">(<?php _e( 'Current Server Time', 'yop_poll' );
																echo ': ' . current_time( 'mysql' ); ?>)</font>
													</th>
													<td><input id="yop-poll-start-date-input" type="text"
															name="yop_poll_options[start_date]"
															value="<?php echo '' == $default_options['start_date'] ? current_time( 'mysql' ) : $default_options['start_date']; ?>"/>
													</td>
												</tr>
												<tr>
													<th><label for="yop-poll-end-date-input"><?php _e( 'End Date ', 'yop_poll' ); ?>
															:</label><br><font size="0">(<?php _e( 'Current Server Time', 'yop_poll' );
																echo ': ' . current_time( 'mysql' ); ?>)</font>
													</th>
													<td><input style="<?php echo 'yes' == $default_options['never_expire'] ? 'display: none;' : ''; ?>" <?php echo 'yes' == $default_options['never_expire'] ? 'disabled="disabled"' : ''; ?> id="yop-poll-end-date-input" type="text" name="yop_poll_options[end_date]" value="<?php echo '' == $default_options['end_date'] ? '' : $default_options['end_date']; ?>"/>
														<label for="yop-poll-never-expire"><input type="checkbox"
																<?php echo $default_options['never_expire'] == 'yes' ? 'checked="checked"' : ''; ?>
																id="yop-poll-never-expire"
															name="yop_poll_options[never_expire]" value="yes"/> <?php _e( 'No End Date', 'yop_poll' ); ?></label>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div6">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'View Results Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th>
														<?php _e( 'View Results', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-before-vote"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'before' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-before-vote" type="radio"
															value="before" name="yop_poll_options[view_results]"/> <?php _e( 'Before Vote', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-after-vote"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'after' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-after-vote" type="radio"
															value="after" name="yop_poll_options[view_results]"/> <?php _e( 'After Vote', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-after-poll-end-date"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'after-poll-end-date' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-after-poll-end-date" type="radio"
																value="after-poll-end-date"
															name="yop_poll_options[view_results]"/> <?php _e( 'After Poll End Date', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-never"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'never' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-never" type="radio" value="never"
															name="yop_poll_options[view_results]"/> <?php _e( 'Never', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-custom"><input
																class="yop-poll-view-results-show-custom"
																<?php echo 'custom-date' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-custom" type="radio"
															value="custom-date" name="yop_poll_options[view_results]"/> <?php _e( 'Custom Date', 'yop_poll' ); ?></label>
														<div id="yop-poll-display-view-results-div" style="<?php echo 'custom-date' != $default_options['view_results'] ? 'display: none;' : ''; ?>">
															<label for="yop-poll-view-results-start-date"><?php _e( 'Results display date (the users will be able to see the results starting with this date)', 'yop_poll' ); ?>
																:</label>
															<input id="yop-poll-view-results-start-date" type="text"
																name="yop_poll_options[view_results_start_date]"
																value="<?php echo $default_options['view_results_start_date']; ?>">
														</div></td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Results Permissions', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-permissions-quest-only"><input
																id="yop-poll-view-results-permissions-quest-only"
																<?php echo 'quest-only' == $default_options['view_results_permissions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="quest-only"
															name="yop_poll_options[view_results_permissions]"/> <?php _e( 'Guest Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-permissions-registered-only"><input
																id="yop-poll-view-results-permissions-registered-only"
																<?php echo 'registered-only' == $default_options['view_results_permissions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="registered-only"
															name="yop_poll_options[view_results_permissions]"/> <?php _e( 'Registered Users Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-permissions-guest-registered"><input
																id="yop-poll-view-results-permissions-guest-registered"
																<?php echo 'guest-registered' == $default_options['view_results_permissions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="guest-registered"
															name="yop_poll_options[view_results_permissions]"/> <?php _e( 'Guest &amp; Registered Users', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Results Display', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-votes-number"><input
																id="yop-poll-view-results-votes-number"
																<?php echo 'votes-number' == $default_options['view_results_type'] ? 'checked="checked"' : ''; ?>
																type="radio" value="votes-number"
															name="yop_poll_options[view_results_type]"/> <?php _e( 'By Votes Number', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-percentages"><input
																id="yop-poll-view-results-percentages"
																<?php echo 'percentages' == $default_options['view_results_type'] ? 'checked="checked"' : ''; ?>
																type="radio" value="percentages"
															name="yop_poll_options[view_results_type]"/> <?php _e( 'Percentages', 'yop_poll' ); ?></label>
														<label
															for="yop-poll-view-results-votes-number-and-percentages"><input
																id="yop-poll-view-results-votes-number-and-percentages"
																<?php echo 'votes-number-and-percentages' == $default_options['view_results_type'] ? 'checked="checked"' : ''; ?>
																type="radio" value="votes-number-and-percentages"
															name="yop_poll_options[view_results_type]"/> <?php _e( 'by Votes Number and Percentages', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Poll Answer Result Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-answer-result-label" type="text"
															name="yop_poll_options[answer_result_label]"
															value="<?php echo esc_html( stripslashes( $default_options['answer_result_label'] ) ); ?>"/>
														<small><i><?php _e( 'Use %POLL-ANSWER-RESULT-PERCENTAGES% for showing answer percentages and  %POLL-ANSWER-RESULT-VOTES% for showing answer number of votes', 'yop_poll' ); ?></i></small>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Poll Answer Result Votes Number Label', 'yop_poll' ); ?>
														:
													</th>
													<td>
														<?php _e( 'Singular', 'yop_poll' ); ?>
														<input
															id="yop-poll-singular-answer-result-votes-number-label"
															type="text"
															name="yop_poll_options[singular_answer_result_votes_number_label]"
															value="<?php echo esc_html( stripslashes( $default_options['singular_answer_result_votes_number_label'] ) ); ?>"/>
														<?php _e( 'Plural', 'yop_poll' ); ?>
														<input
															id="yop-poll-plural-answer-result-votes-number-label"
															type="text"
															name="yop_poll_options[plural_answer_result_votes_number_label]"
															value="<?php echo esc_html( stripslashes( $default_options['plural_answer_result_votes_number_label'] ) ); ?>"/>

													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Vote Button Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-vote-button-label" type="text"
															name="yop_poll_options[vote_button_label]"
															value="<?php echo esc_html( stripslashes( $default_options['vote_button_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Results Link', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-link-yes"><input
																<?php echo 'yes' == $default_options['view_results_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-link-yes" type="radio"
															value="yes" name="yop_poll_options[view_results_link]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-link-no"><input
																<?php echo 'no' == $default_options['view_results_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-link-no" type="radio" value="no"
															name="yop_poll_options[view_results_link]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-results-link-div" style="<?php echo 'yes' != $default_options['view_results_link'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Results Link Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-results-link-label" type="text"
															name="yop_poll_options[view_results_link_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_results_link_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Back To Vote Link ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-back-to-vote-link-yes"><input
																<?php echo 'yes' == $default_options['view_back_to_vote_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-back-to-vote-link-yes" type="radio"
															value="yes" name="yop_poll_options[view_back_to_vote_link]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-back-to-vote-link-no"><input
																<?php echo 'no' == $default_options['view_back_to_vote_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-back-to-vote-link-no" type="radio"
															value="no" name="yop_poll_options[view_back_to_vote_link]"/><?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-back-to-vote-link-div" style="<?php echo 'yes' != $default_options['view_back_to_vote_link'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Back To Vote Link Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-back-to-vote-link-label"
															type="text"
															name="yop_poll_options[view_back_to_vote_link_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_back_to_vote_link_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Total Votes ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-total-votes-yes"><input
																<?php echo 'yes' == $default_options['view_total_votes'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-votes-yes" type="radio" value="yes"
															name="yop_poll_options[view_total_votes]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-total-votes-no"><input
																<?php echo 'no' == $default_options['view_total_votes'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-votes-no" type="radio" value="no"
															name="yop_poll_options[view_total_votes]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-total-votes-div" style="<?php echo 'yes' != $default_options['view_total_votes'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Total Votes Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-total-votes-label" type="text"
															name="yop_poll_options[view_total_votes_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_total_votes_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Total Answers ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-total-answers-yes"><input
																<?php echo 'yes' == $default_options['view_total_answers'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-answers-yes" type="radio"
															value="yes" name="yop_poll_options[view_total_answers]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-total-answers-no"><input
																<?php echo 'no' == $default_options['view_total_answers'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-answers-no" type="radio" value="no"
															name="yop_poll_options[view_total_answers]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-total-answers-div" style="<?php echo 'yes' != $default_options['view_total_answers'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Total Answers Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-total-answers-label" type="text"
															name="yop_poll_options[view_total_answers_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_total_answers_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Message After Vote', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-message-after-vote" type="text"
															name="yop_poll_options[message_after_vote]"
															value="<?php echo esc_html( stripslashes( $default_options['message_after_vote'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th><label for="yop-poll-page-url"><?php _e( 'Poll Page Url ', 'yop_poll' ); ?>
															:</label>
													</th>
													<td><input id="yop-poll-page-url" type="text"
															name="yop_poll_options[poll_page_url]"
															value="<?php echo esc_html( stripslashes( $default_options['poll_page_url'] ) ); ?>"/>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div7">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Other Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th>
														<?php _e( 'Vote Permissions ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-vote-permisions-quest-only"><input
																id="yop-poll-vote-permisions-quest-only"
																<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="quest-only"
															name="yop_poll_options[vote_permisions]"/> <?php _e( 'Guest Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-registered-only"><input
																id="yop-poll-vote-permisions-registered-only"
																<?php echo 'registered-only' == $default_options['vote_permisions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="registered-only"
															name="yop_poll_options[vote_permisions]"/> <?php _e( 'Registered Users Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-guest-registered"><input
																id="yop-poll-vote-permisions-guest-registered"
																<?php echo 'guest-registered' == $default_options['vote_permisions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="guest-registered"
															name="yop_poll_options[vote_permisions]"/> <?php _e( 'Guest &amp; Registered Users', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<?php if ( false ){ ?>
													<tr class="yop-poll-vote-as-div" style="<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
														<th>
															<?php _e( 'Vote as Facebook User', 'yop_poll' ); ?>:
															<?php if ($pro_options['pro_user'] == 'no') { ?>
																<br/><font size="-1">(<?php _e( 'Available only for pro version of Yop Poll', 'yop_poll' ); ?>
																	)</font>
																<?php } ?></label>
														</th>
														<td><label for="yop-poll-vote-permisions-facebook-yes"><input
																	<?php echo 'yes' == $default_options['vote_permisions_facebook'] ? 'checked="checked"' : ''; ?>
																	id="yop-poll-vote-permisions-facebook-yes" type="radio"
																value="yes" name="yop_poll_options[vote_permisions_facebook]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
															<label for="yop-poll-vote-permisions-facebook-no"><input
																	<?php echo 'no' == $default_options['vote_permisions_facebook'] ? 'checked="checked"' : ''; ?>
																	id="yop-poll-vote-permisions-facebook-no" type="radio" value="no"
																name="yop_poll_options[vote_permisions_facebook]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
														</td>
													</tr>

													<tr class="yop-poll-vote-as-div yop_poll_suboption" id="yop-poll-vote-permisions-facebook-div" style="<?php echo 'yes' != $default_options['vote_permisions_facebook'] ? 'display: none;' : 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
														<th>
															<?php _e( '"Vote as Facebook User" Button Label', 'yop_poll' ); ?>
															:
														</th>
														<td><input id="yop-poll-vote-permisions-facebook-label" type="text"
																name="yop_poll_options[vote_permisions_facebook_label]"
																value="<?php echo esc_html( stripslashes( $default_options['vote_permisions_facebook_label'] ) ); ?>"/>
														</td>
													</tr>
													<?php } ?>

												<tr class="yop-poll-vote-as-div" style="<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Vote as Wordpress User', 'yop_poll' ); ?>
														<br><font size="0">(<?php _e( 'Will force users to login into your blog', 'yop_poll' ); ?>
															)</font>:
													</th>
													<td><label for="yop-poll-vote-permisions-wordpress-yes"><input
																<?php echo 'yes' == $default_options['vote_permisions_wordpress'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-wordpress-yes" type="radio"
															value="yes" name="yop_poll_options[vote_permisions_wordpress]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-wordpress-no"><input
																<?php echo 'no' == $default_options['vote_permisions_wordpress'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-wordpress-no" type="radio" value="no"
															name="yop_poll_options[vote_permisions_wordpress]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop-poll-vote-as-div yop_poll_suboption" id="yop-poll-vote-permisions-wordpress-div" style="<?php echo 'yes' != $default_options['vote_permisions_wordpress'] ? 'display: none;' : 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( '"Vote as Wordpress User" Button Label', 'yop_poll' ); ?>
														:
													</th>
													<td><input id="yop-poll-vote-permisions-wordpress-label" type="text"
															name="yop_poll_options[vote_permisions_wordpress_label]"
															value="<?php echo esc_html( stripslashes( $default_options['vote_permisions_wordpress_label'] ) ); ?>"/>
													</td>
												</tr>

												<tr class="yop-poll-vote-as-div" style="<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Vote as Anonymous User', 'yop_poll' ); ?>
														<br><font size="0">(<?php _e( 'Logged users will be treated as anonymous', 'yop_poll' ); ?>
															)</font>:
													</th>
													<td><label for="yop-poll-vote-permisions-anonymous-yes"><input
																<?php echo 'yes' == $default_options['vote_permisions_anonymous'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-anonymous-yes" type="radio"
															value="yes" name="yop_poll_options[vote_permisions_anonymous]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-anonymous-no"><input
																<?php echo 'no' == $default_options['vote_permisions_anonymous'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-anonymous-no" type="radio" value="no"
															name="yop_poll_options[vote_permisions_anonymous]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop-poll-vote-as-div yop_poll_suboption" id="yop-poll-vote-permisions-anonymous-div" style="<?php echo 'yes' != $default_options['vote_permisions_anonymous'] ? 'display: none;' : 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( '"Vote as Anonymous User" Button Label', 'yop_poll' ); ?>
														:
													</th>
													<td><input id="yop-poll-vote-permisions-anonymous-label" type="text"
															name="yop_poll_options[vote_permisions_anonymous_label]"
															value="<?php echo esc_html( stripslashes( $default_options['vote_permisions_anonymous_label'] ) ); ?>"/>
													</td>
												</tr>

												<tr>
													<th>
														<?php _e( 'Blocking Voters', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-blocking-voters-dont-block"><input
																class="yop-poll-blocking-voters-hide-interval"
																<?php echo 'dont-block' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-dont-block" type="radio"
															value="dont-block" name="yop_poll_options[blocking_voters]"/> <?php _e( 'Dont`t Block', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-cookie"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'cookie' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-cookie" type="radio"
															value="cookie" name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Cookie', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-ip"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'ip' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-ip" type="radio" value="ip"
															name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Ip', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-username"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'username' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-username" type="radio"
															value="username" name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Username', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-cookie-ip"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'cookie-ip' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-cookie-ip" type="radio"
															value="cookie-ip" name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Cookie &amp; Ip', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-blocking-voters-interval-div" style="<?php echo 'dont-block' == $default_options['blocking_voters'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Blocking voters interval', 'yop_poll' ); ?>:
													</th>
													<td><input type="text"
															name="yop_poll_options[blocking_voters_interval_value]"
															id="yop-poll-blocking-voters-interval-value"
															value="<?php echo $default_options['blocking_voters_interval_value']; ?>"/>
														<select id="yop-poll-blocking-voters-interval-unit"
															name="yop_poll_options[blocking_voters_interval_unit]">
															<option
																<?php echo 'seconds' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="seconds"><?php _e( 'Seconds', 'yop_poll' ); ?></option>
															<option
																<?php echo 'minutes' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="minutes"><?php _e( 'Minutes', 'yop_poll' ); ?></option>
															<option
																<?php echo 'hours' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="hours"><?php _e( 'Hours', 'yop_poll' ); ?></option>
															<option
																<?php echo 'days' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="days"><?php _e( 'Days', 'yop_poll' ); ?></option>
														</select></td>
												</tr>
												<tr class="yop-poll-limit-number-of-votes-per-user-div">
													<th>
														<?php _e( 'Limit Number of Votes per User', 'yop_poll' ); ?>
														:<br><small>(<?php _e( 'Only for logged users', 'yop_poll' ); ?>
															)</small>
													</th>
													<td><label for="yop-poll-limit-number-of-votes-per-user-yes"><input
																<?php echo 'yes' == $default_options['limit_number_of_votes_per_user'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-limit-number-of-votes-per-user-yes" type="radio"
															value="yes" name="yop_poll_options[limit_number_of_votes_per_user]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-limit-number-of-votes-per-user-no"><input
																<?php echo 'no' == $default_options['limit_number_of_votes_per_user'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-limit-number-of-votes-per-user-no" type="radio" value="no"
															name="yop_poll_options[limit_number_of_votes_per_user]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop-poll-limit-number-of-votes-per-user-divs yop_poll_suboption" id="yop-poll-number-of-votes-per-user-div" style="<?php echo 'yes' != $default_options['limit_number_of_votes_per_user'] ? 'display: none;' : '' ?>">
													<th>
														<?php _e( 'Number of Votes per User', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-number-of-votes-per-user" type="text"
															name="yop_poll_options[number_of_votes_per_user]"
															value="<?php echo esc_html( stripslashes( $default_options['number_of_votes_per_user'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Percentages Decimals', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-percentages-decimals" type="text"
															name="yop_poll_options[percentages_decimals]"
															value="<?php echo esc_html( stripslashes( $default_options['percentages_decimals'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Use Default Loading Image', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-use-default-loading-image-yes"><input
																<?php echo 'yes' == $default_options['use_default_loading_image'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-use-default-loading-image-yes" type="radio"
																value="yes"
															name="yop_poll_options[use_default_loading_image]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-use-default-loading-image-no"><input
																<?php echo 'no' == $default_options['use_default_loading_image'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-use-default-loading-image-no" type="radio"
																value="no"
															name="yop_poll_options[use_default_loading_image]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-use-default-loading-image-div" style="<?php echo 'yes' == $default_options['use_default_loading_image'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Loading Image Url', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-loading-image-url" type="text"
															name="yop_poll_options[loading_image_url]"
															value="<?php echo esc_html( stripslashes( $default_options['loading_image_url'] ) ); ?>"/>
													</td>
												</tr>

												<tr>
													<th>
														<?php _e( 'Redirect After Vote', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-redirect-after-vote-yes"><input
																<?php echo 'yes' == $default_options['redirect_after_vote'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-redirect-after-vote-yes" type="radio"
																value="yes"
															name="yop_poll_options[redirect_after_vote]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-redirect-after-vote-no"><input
																<?php echo 'no' == $default_options['redirect_after_vote'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-redirect-after-vote-no" type="radio"
																value="no"
															name="yop_poll_options[redirect_after_vote]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-redirect-after-vote-url-div" style="<?php echo 'no' == $default_options['redirect_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Redirect After Vote Url', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-redirect-after-vote-url" type="text"
															name="yop_poll_options[redirect_after_vote_url]"
															value="<?php echo esc_html( stripslashes( $default_options['redirect_after_vote_url'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Poll Date Format', 'yop_poll' ); ?>
														: <br/><font size="0"><?php _e( 'Check', 'yop_popll' ) ?>
															<a target="_blank" href="http://codex.wordpress.org/Formatting_Date_and_Time"> <?php _e( 'documentation', 'yop_popll' ) ?></a></font>
													</th>
													<td><input id="yop-poll-date-format" type="text"
															name="yop_poll_options[date_format]"
															value="<?php echo esc_html( stripslashes( $default_options['date_format'] ) ); ?>"/>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div8">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Archive Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
											<tr>
												<th>
													<?php _e( 'View Poll Archive Link ', 'yop_poll' ); ?>:
												</th>
												<td><input
														<?php checked( 'yes', $default_options['view_poll_archive_link'] ); ?>
														id="yop-poll-view-poll-archive-link-yes" type="radio"
														value="yes" name="yop_poll_options[view_poll_archive_link]"/><label
														for="yop-poll-view-poll-archive-link-yes"><?php _e( 'Yes', 'yop_poll' ); ?></label>
													<input
														<?php checked( 'no', $default_options['view_poll_archive_link'] ); ?>
														id="yop-poll-view-poll-archive-link-no" type="radio"
														value="no" name="yop_poll_options[view_poll_archive_link]"/><label
														for="yop-poll-view-poll-archive-link-no"><?php _e( 'No', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-view-poll-archive-link-div" style="<?php echo 'yes' != $default_options['view_poll_archive_link'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'View Poll Archive Link Label', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-view-poll-archive-link-label"
														type="text"
														name="yop_poll_options[view_poll_archive_link_label]"
														value="<?php echo esc_html( stripslashes( $default_options['view_poll_archive_link_label'] ) ); ?>"/>
												</td>
											</tr>
											<tr id="yop-poll-view-poll-archive-link-div" style="<?php echo 'yes' != $default_options['view_poll_archive_link'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Poll Archive Url', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-poll-archive-url" type="text"
														name="yop_poll_options[poll_archive_url]"
														value="<?php echo esc_html( stripslashes( $default_options['poll_archive_url'] ) ); ?>"/>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Show Poll In Archive ', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-show-in-archive-yes"><input
															<?php checked( 'yes', $default_options['show_in_archive'] ); ?>
															id="yop-poll-show-in-archive-yes" type="radio" value="yes"
														name="yop_poll_options[show_in_archive]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
													<label for="yop-poll-show-in-archive-no"><input
															<?php checked( 'no', $default_options['show_in_archive'] ); ?>
															id="yop-poll-show-in-archive-no" type="radio" value="no"
														name="yop_poll_options[show_in_archive]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-show-in-archive-div" style="<?php echo 'yes' != $default_options['show_in_archive'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Archive Order', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-show-in-archive-order" type="text"
														name="yop_poll_options[archive_order]"
														value="<?php echo $default_options['archive_order']; ?>"/>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Archive Polls Per Page', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-archive-polls-per-page" type="text"
														name="yop_poll_options[archive_polls_per_page]"
														value="<?php echo $default_options['archive_polls_per_page']; ?>"/>
												</td>
											</tr>

										</table>
									</div>
								</div>

								<div class="postbox" id="yop-poll-advanced-options-div8">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Notifications Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
											<tr>
												<th>
													<?php _e( 'Send Email Notifications', 'yop_poll' ); ?>:
												</th>
												<td><input <?php checked( 'yes', $default_options['send_email_notifications'] ); ?>
														id="yop-poll-send-email-notifications-yes" type="radio"
														value="yes" name="yop_poll_options[send_email_notifications]"/><label
														for="yop-poll-send-email-notifications-yes"><?php _e( 'Yes', 'yop_poll' ); ?></label>
													<input <?php checked( 'no', $default_options['send_email_notifications'] ); ?>
														id="yop-poll-send-email-notifications-no" type="radio"
														value="no" name="yop_poll_options[send_email_notifications]"/><label
														for="yop-poll-send-email-notifications-no"><?php _e( 'No', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-from-name-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications From Name', 'yop_poll' ); ?>:
												</th>
												<td valign="top">
													<input id="yop-poll-email-notifications-from-name"
														type="text"
														name="yop_poll_options[email_notifications_from_name]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_from_name'] ) ); ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-from-email-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications From Email', 'yop_poll' ); ?>:
												</th>
												<td valign="top">
													<input id="yop-poll-email-notifications-from-email"
														type="text"
														name="yop_poll_options[email_notifications_from_email]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_from_email'] ) ); ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-recipients-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications Recipients', 'yop_poll' ); ?>
													:<br><font size="0"><?php _e( 'Use comma separated email addresses: email@xmail.com,email2@xmail.com', 'yop_poll' ) ?></font>
												</th>
												<td valign="top">
													<input id="yop-poll-email-notifications-recipients"
														type="text"
														name="yop_poll_options[email_notifications_recipients]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_recipients'] ) ); ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-subject-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications Subject', 'yop_poll' ); ?>:
												</th>
												<td>
													<input id="yop-poll-email-notifications-subject"
														type="text"
														name="yop_poll_options[email_notifications_subject]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_subject'] ) ); ?>"
														/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-body-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications Body', 'yop_poll' ); ?>:
												</th>
												<td>
													<textarea id="yop-poll-email-notifications-body" rows="10"
														name="yop_poll_options[email_notifications_body]"><?php echo esc_html( stripslashes( $default_options['email_notifications_body'] ) ); ?></textarea>
												</td>
											</tr>
										</table>
									</div>
								</div>

								<?php if ( false ){ ?>
									<div class="postbox" id="yop-poll-advanced-options-div9">
										<div title="Click to toggle" class="handlediv">
											<br/>
										</div>
										<h3 class="hndle">
											<span><?php _e( 'Facebook Share Options', 'yop_poll' ); ?>
												<?php if ( $pro_options['pro_user'] == 'no' ){ ?>
													<font size="-1">(<?php _e( 'Available only for pro version of Yop Poll', 'yop_poll' ); ?>
														)</font>
													<?php } ?>
											</span>
										</h3>
										<div class="inside">
											<table cellspacing="0" class="links-table">
												<tbody>
												<tr>
													<th>
														<?php _e( 'Share After Vote ', 'yop_poll' ); ?>:
													</th>
													<td><input
															<?php checked( 'yes', $default_options['share_after_vote'] ); ?>
															id="yop-poll-share-after-vote-yes" type="radio"
															value="yes" name="yop_poll_options[share_after_vote]"/><label
															for="yop-poll-share-after-vote-yes"><?php _e( 'Yes', 'yop_poll' ); ?></label>
														<input
															<?php checked( 'no', $default_options['share_after_vote'] ); ?>
															id="yop-poll-share-after-vote-no" type="radio"
															value="no" name="yop_poll_options[share_after_vote]"/><label
															for="yop-poll-share-after-vote-no"><?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-name-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Name', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-name"
															type="text"
															name="yop_poll_options[share_name]"
															value="<?php echo esc_html( stripslashes( $default_options['share_name'] ) ); ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-caption-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Caption', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-caption"
															type="text"
															name="yop_poll_options[share_caption]"
															value="<?php echo esc_html( stripslashes( $default_options['share_caption'] ) ); ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-description-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Description', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-description"
															type="text"
															name="yop_poll_options[share_description]"
															value="<?php echo esc_html( stripslashes( $default_options['share_description'] ) ); ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-picture-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Picture', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-picture"
															type="text"
															name="yop_poll_options[share_picture]"
															value="<?php echo esc_html( stripslashes( $default_options['share_picture'] ) ); ?>"/>
													</td>
												</tr>

											</table>
										</div>
									</div>
									<?php } ?>
							</div>
							<input name="Submit" class="button-primary" type="submit"
								value="<?php _e( 'Save Changes', 'yop_poll' ); ?>"/>
						</div>

						<div class="postbox-container" id="postbox-container-1">
							<div class="meta-box-sortables ui-sortable" id="side-sortables">
								<div class="postbox " id="linksubmitdiv">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Save Changes', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<div id="submitlink" class="submitbox">
											<div id="major-publishing-actions">


												<div id="publishing-action">
													<input name="Submit" class="button-primary" type="submit"
														value="<?php _e( 'Save Changes', 'yop_poll' ); ?>"/>
												</div>
												<div class="clear"></div>
											</div>
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>

		<?php
		}

		public function view_add_edit_new_poll() {
			global $yop_poll_add_new_config, $action, $current_user;
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$yop_poll_model  = new Yop_Poll_Model ();
			$page_name       = __( 'Add New Yop Poll', 'yop_poll' );
			$action_type     = 'add-new';
			$poll_id         = '';
			$default_options = get_option( 'yop_poll_options', array() );
			if ( 'edit' == $action ){
				$poll_id     = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
				$poll_author = Yop_Poll_Model::get_poll_field_from_database_by_id( 'poll_author', $poll_id );
				if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_author != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) )
					wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
				$yop_poll_model       = new Yop_Poll_Model ( $poll_id );
				$answers              = Yop_Poll_Model::get_poll_answers( $poll_id );
				$other_answer         = Yop_Poll_Model::get_poll_answers( $poll_id, array( 'other' ) );
				$custom_fields        = Yop_Poll_Model::get_poll_customfields( $poll_id );
				$page_name            = __( 'Edit Poll', 'yop_poll' );
				$action_type          = 'edit';
				$poll_default_options = get_yop_poll_meta( $poll_id, 'options', true );
				foreach ( $default_options as $option_name => $option_value ) {
					if ( isset ( $poll_default_options [$option_name] ) ){
						$default_options [$option_name] = $poll_default_options [$option_name];
					}
				}
			}
			$current_poll        = $yop_poll_model->get_current_poll();
			$answers_number      = $yop_poll_add_new_config ['default_number_of_answers'];
			$customfields_number = $yop_poll_add_new_config ['default_number_of_customfields'];
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php print $page_name; ?><?php if ('edit' == $action): ?><a
						class="add-new-h2"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls-add-new', 'action' => false, 'id' => false ) ) ); ?>"><?php _e( 'Add New', 'yop_poll' ); ?></a><?php endif; ?></h2>
			<div id="message"></div>
			<form method="post" name="yop_poll_edit_add_new_form"
				id="yop-poll-edit-add-new-form">
				<?php wp_nonce_field( 'yop-poll-edit-add-new' ); ?>
				<span <?php if ( 'edit' != $action ){ ?> style="display: none;"
						<?php } ?>> Shortcode: <input id="yop_poll_shortcode" type="text"
						value='[yop_poll id="<?php echo $current_poll['id']; ?>"]'
						readonly="readonly">
				</span>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="stuffbox" id="yop-poll-namediv">
								<h3>
									<label for="yop-poll-name"><?php _e( 'Poll Name', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<input type="text" id="yop-poll-name"
										value="<?php echo esc_html( stripslashes( $current_poll['name'] ) ); ?>"
										tabindex="1" name="yop_poll_name" size="30"/>
									<p><?php _e( 'Example: Test Poll', 'yop_poll' ); ?></p>
								</div>
							</div>
							<div class="stuffbox" id="yop-poll-questiondiv">
								<h3>
									<label for="yop-poll-question"><?php _e( 'Question', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<input type="text" id="yop-poll-question"
										value="<?php echo esc_html( stripslashes( $current_poll['question'] ) ); ?>"
										tabindex="1" name="yop_poll_question" size="30"/>
									<p><?php _e( 'Example: How is my plugin?', 'yop_poll' ); ?></p>
								</div>
							</div>
							<div class="stuffbox" id="yop-poll-answersdiv">
								<h3>
									<span><?php _e( 'Answers', 'yop_poll' ); ?></span>
								</h3>
								<div class="inside">
									<table cellspacing="0" class="links-table"
										id='yop-poll-answer-table'>
										<tbody>
											<?php
												for ($answer_id = 1;
													$answer_id < $answers_number;
													$answer_id++) {
													if ( isset ( $answers [$answer_id - 1] ) ){
														$answer_options = get_yop_poll_answer_meta( $answers [$answer_id - 1] ['id'], 'options' );
													}
												?>
												<tr class="yop_poll_tr_answer"
													id="yop_poll_tr_answer<?php echo $answer_id ?>">
													<th scope="row"><label class="yop_poll_answer_label"
															for="yop-poll-answer<?php echo $answer_id ?>"><?php echo $yop_poll_add_new_config['text_answer']; ?> <?php echo $answer_id ?></label></th>
													<td><input type="hidden"
															value="<?php echo isset( $answers[$answer_id - 1]['id'] ) ? $answers[$answer_id - 1]['id'] : ''; ?>"
															name="yop_poll_answer_ids[answer<?php echo $answer_id ?>]"/>
														<input type="text"
															value="<?php echo isset( $answers[$answer_id - 1]['answer'] ) ? esc_html( stripslashes( $answers[$answer_id - 1]['answer'] ) ) : ''; ?>"
															id="yop-poll-answer<?php echo $answer_id ?>"
															name="yop_poll_answer[answer<?php echo $answer_id ?>]"/></td>
													<td align="right">
														<?php if ( 'edit' == $action ){ ?>
															<input type="button"
																value="<?php echo $yop_poll_add_new_config['text_change_votes_number_answer']; ?> (<?php echo $answers[$answer_id - 1]['votes'] ?>)"
																onclick="yop_poll_show_change_votes_number_answer(<?php echo $answers [$answer_id - 1] ['id'] ?>); return false;"
																class="button yop-poll-change-no-votes-buttons" id="yop-poll-change-no-votes-button-<?php echo $answers [$answer_id - 1] ['id'] ?>"/>
															<?php } ?>
														<input type="button"
															value="<?php echo $yop_poll_add_new_config['text_customize_answer']; ?>"
															onclick="yop_poll_toogle_customize_answer('#yop-poll-answer-table', <?php echo $answer_id ?>); return false;"
															class="button"/>
														<input
															onclick="yop_poll_remove_answer('#yop-poll-answer-table', <?php echo $answer_id ?>); return false;"
															type="button"
															value="<?php echo $yop_poll_add_new_config['text_remove_answer']; ?>"
															class="button"/></td>
												</tr>
												<tr class="yop_poll_tr_customize_answer"
													id="yop_poll_tr_customize_answer<?php echo $answer_id ?>"
													style="display: none;">
													<td colspan="3">
														<table cellspacing="0" width="100%">
															<tbody>
																<tr>
																	<th>
																		<?php echo $yop_poll_add_new_config['text_is_default_answer']; ?>
																		:
																	</th>
																	<td valign="top"><input
																			id="yop-poll-is-default-answer-no-<?php echo $answer_id ?>"
																			<?php echo checked( 'no', isset ( $answer_options[0]['is_default_answer'] ) ? $answer_options[0]['is_default_answer'] : $default_options['is_default_answer'] ); ?>
																			type="radio"
																			name="yop_poll_answer_options[answer<?php echo $answer_id ?>][is_default_answer]"
																			value="no"/> <label
																			for="yop-poll-id-default-answer-no-<?php echo $answer_id ?>"><?php _e( 'No', 'yop_poll' ); ?></label>&nbsp;|&nbsp;
																		<input
																			id="yop-poll-is-default-answer-yes-<?php echo $answer_id ?>"
																			<?php echo checked( 'yes', isset ( $answer_options[0]['is_default_answer'] ) ? $answer_options[0]['is_default_answer'] : $default_options['is_default_answer'] ); ?>
																			type="radio"
																			name="yop_poll_answer_options[answer<?php echo $answer_id ?>][is_default_answer]"
																			value="yes"/> <label
																			for="yop-poll-id-default-answer-yes-<?php echo $answer_id ?>"><?php _e( 'Yes', 'yop_poll' ); ?></label>
																	</td>
																</tr>
															</tbody>
														</table>
														<table cellspacing="0" width="100%">
															<tbody>
																<tr>
																	<th>
																		<?php echo $yop_poll_add_new_config['text_poll_bar_style']['use_template_bar_label']; ?>
																		:
																	</th>
																	<td><input
																			onclick="jQuery('#yop-poll-answer-use-template-bar-table-<?php echo $answer_id ?>').show();"
																			id="yop-poll-answer-use-template-bar-no-<?php echo $answer_id ?>"
																			<?php echo checked( 'no', isset ( $answer_options[0]['use_template_bar'] ) ? $answer_options[0]['use_template_bar'] : $default_options['use_template_bar'] ); ?>
																			type="radio"
																			name="yop_poll_answer_options[answer<?php echo $answer_id ?>][use_template_bar]"
																			value="no"/> <label
																			for="yop-poll-answer-use-template-bar-no-<?php echo $answer_id ?>"><?php _e( 'No', 'yop_poll' ); ?></label>&nbsp;|&nbsp;
																		<input
																			onclick="jQuery('#yop-poll-answer-use-template-bar-table-<?php echo $answer_id ?>').hide();"
																			id="yop-poll-answer-use-template-bar-yes-<?php echo $answer_id ?>"
																			<?php echo checked( 'yes', isset ( $answer_options[0]['use_template_bar'] ) ? $answer_options[0]['use_template_bar'] : $default_options['use_template_bar'] ); ?>
																			type="radio"
																			name="yop_poll_answer_options[answer<?php echo $answer_id ?>][use_template_bar]"
																			value="yes"/> <label
																			for="yop-poll-answer-use-template-bar-yes-<?php echo $answer_id ?>"><?php _e( 'Yes', 'yop_poll' ); ?></label>
																	</td>
																</tr>
															</tbody>
														</table>
														<table cellspacing="0" width="100%" id="yop-poll-answer-use-template-bar-table-<?php echo $answer_id ?>" style="<?php echo( 'yes' == ( isset ( $answer_options[0]['use_template_bar'] ) ? $answer_options[0]['use_template_bar'] : $default_options['use_template_bar'] ) ? 'display: none;' : '' ); ?>">
															<tbody>
																<tr>
																	<th><label><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_label']; ?></label>
																	</th>
																	<td>
																		<table cellspacing="0" style="margin-left: 0px;"
																			style="width:100%">
																			<tbody>
																				<tr>
																					<th><label
																							for="yop-poll-answer-option-bar-background-answer<?php echo $answer_id ?>"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_background_label']; ?></label>
																					</th>
																					<td>#<input
																							id="yop-poll-answer-option-bar-background-answer<?php echo $answer_id ?>"
																							value="<?php echo isset ( $answer_options[0]['bar_background'] ) ? $answer_options[0]['bar_background'] : $default_options['bar_background']; ?>"
																							onblur="yop_poll_update_bar_style('#yop-poll-bar-preview<?php echo $answer_id ?>', 'background-color', '#' + this.value)"
																							type="text"
																							name="yop_poll_answer_options[answer<?php echo $answer_id ?>][bar_background]"/>
																					</td>
																				</tr>
																				<tr>
																					<th><label
																							for="yop-poll-answer-option-bar-height-answer<?php echo $answer_id ?>"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_height_label']; ?></label>
																					</th>
																					<td><input
																							id="yop-poll-answer-option-bar-height-answer<?php echo $answer_id ?>"
																							value="<?php echo isset ( $answer_options[0]['bar_height'] ) ? $answer_options[0]['bar_height'] : $default_options['bar_height']; ?>"
																							onblur="yop_poll_update_bar_style('#yop-poll-bar-preview<?php echo $answer_id ?>', 'height', this.value + 'px')"
																							type="text"
																							name="yop_poll_answer_options[answer<?php echo $answer_id ?>][bar_height]"/>
																						px</td>
																				</tr>
																				<tr>
																					<th><label
																							for="yop-poll-answer-option-bar-border-color-answer<?php echo $answer_id ?>"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_border_color_label']; ?></label>
																					</th>
																					<td>#<input
																							id="yop-poll-answer-option-bar-border-color-answer<?php echo $answer_id ?>"
																							value="<?php echo isset ( $answer_options[0]['bar_border_color'] ) ? $answer_options[0]['bar_border_color'] : $default_options['bar_border_color']; ?>"
																							onblur="yop_poll_update_bar_style( '#yop-poll-bar-preview<?php echo $answer_id ?>', 'border-color', '#' + this.value )"
																							type="text"
																							name="yop_poll_answer_options[answer<?php echo $answer_id ?>][bar_border_color]"/>
																					</td>
																				</tr>
																				<tr>
																					<th><label
																							for="yop-poll-answer-option-bar-border-width-answer<?php echo $answer_id ?>"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_border_width_label']; ?></label>
																					</th>
																					<td><input
																							id="yop-poll-answer-option-bar-border-width-answer<?php echo $answer_id ?>"
																							value="<?php echo isset ( $answer_options[0]['bar_border_width'] ) ? $answer_options[0]['bar_border_width'] : $default_options['bar_border_width']; ?>"
																							onblur="yop_poll_update_bar_style('#yop-poll-bar-preview<?php echo $answer_id ?>', 'border-width', this.value + 'px')"
																							type="text"
																							name="yop_poll_answer_options[answer<?php echo $answer_id ?>][bar_border_width]"/>
																						px</td>
																				</tr>
																				<tr>
																					<th><label
																							for="yop-poll-answer-option-bar_border-style-answer<?php echo $answer_id ?>"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_border_style_label']; ?></label>
																					</th>
																					<td><select
																							id="yop-poll-answer-option-bar_border-style-answer<?php echo $answer_id ?>"
																							onchange="yop_poll_update_bar_style('#yop-poll-bar-preview<?php echo $answer_id ?>', 'border-style', this.value)"
																							name="yop_poll_answer_options[answer<?php echo $answer_id ?>][bar_border_style]">
																							<option
																								<?php print ( 'solid' == ( isset ( $answer_options[0]['bar_border_style'] ) ? $answer_options[0]['bar_border_style'] : $default_options['bar_border_style'] ) ) ? 'selected="selected"' : ''; ?>
																								value="solid">Solid</option>
																							<option
																								<?php print ( 'dashed' == ( isset ( $answer_options[0]['bar_border_style'] ) ? $answer_options[0]['bar_border_style'] : $default_options['bar_border_style'] ) ) ? 'selected="selected"' : ''; ?>
																								value="dashed">Dashed</option>
																							<option
																								<?php print ( 'dotted' == ( isset ( $answer_options[0]['bar_border_style'] ) ? $answer_options[0]['bar_border_style'] : $default_options['bar_border_style'] ) ) ? 'selected="selected"' : ''; ?>
																								value="dotted">Dotted</option>
																						</select></td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
																<tr>
																	<th><label><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_preview_label']; ?></label>
																	</th>
																	<td>
																		<div id="yop-poll-bar-preview<?php echo $answer_id ?>"; style="width: 100px; height: <?php echo isset ( $answer_options[0]['bar_height'] ) ? $answer_options[0]['bar_height'] : $default_options['bar_height']; ?>
																		px; background-color:#<?php

																			echo isset ( $answer_options [0] ['bar_background'] ) ? $answer_options [0] ['bar_background'] : $default_options ['bar_background'];
																		?>
																		; border-style: <?php echo isset ( $answer_options[0]['bar_border_style'] ) ? $answer_options[0]['bar_border_style'] : $default_options['bar_border_style']; ?>
																		; border-width: <?php echo isset ( $answer_options[0]['bar_border_width'] ) ? $answer_options[0]['bar_border_width'] : $default_options['bar_border_width']; ?>
																		px; border-color: #<?php

																			echo isset ( $answer_options [0] ['bar_border_color'] ) ? $answer_options [0] ['bar_border_color'] : $default_options ['bar_border_color'];
																		?>;"></div>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
												<?php } ?>
										</tbody>
									</table>
									<p id="yop-poll-add-answer-holder" style="display: block;">
										<button id="yop-poll-add-answer-button" class="button"><?php _e( 'Add New Answer', 'yop_poll' ) ?></button>
										<button id="yop-poll-answers-advanced-options-button"
											class="button"><?php _e( 'Answers Advanced Options', 'yop_poll' ); ?></button>
									</p>

									<table cellspacing="0" id="yop-poll-answers-advanced-options-div"
										style="display: none;" class="links-table">
										<tbody>
											<tr>
												<th>
													<?php _e( 'Allow other answers ', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-allow-other-answers-no"><input
															id="yop-poll-allow-other-answers-no"
															<?php echo 'no' == $default_options['allow_other_answers'] ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[allow_other_answers]"
														value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
														for="yop-poll-allow-other-answers-yes"><input
															id="yop-poll-allow-other-answers-yes"
															<?php echo 'yes' == $default_options['allow_other_answers'] ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[allow_other_answers]"
														value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
													<?php if ( 'edit' == $action ){ ?>
														<input type="button"
															value="<?php echo $yop_poll_add_new_config['text_change_votes_number_answer']; ?> (<?php echo $other_answer[0]['votes'] ?>)"
															onclick="yop_poll_show_change_votes_number_answer(<?php echo $other_answer[0] ['id'] ?>); return false;"
															class="button yop-poll-change-no-votes-buttons" id="yop-poll-change-no-votes-button-<?php echo $other_answer[0] ['id'] ?>"/>
														<?php } ?>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-other-answers-label-div" style="<?php echo 'no' == $default_options['allow_other_answers'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Other Answer Label', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-other-answers-label" type="text"
														name="yop_poll_options[other_answers_label]"
														value="<?php echo isset( $other_answer[0]['answer'] ) ? esc_html( stripslashes( $other_answer[0]['answer'] ) ) : $default_options['other_answers_label'] ?>"/>
													<input type="hidden"
														name="yop_poll_options[other_answers_id]"
														value="<?php echo isset( $other_answer[0]['id'] ) ? $other_answer[0]['id'] : '' ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-other-answers-to-results-div" style="<?php echo 'no' == $default_options['allow_other_answers'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Add the values submitted in "Other" as answers ', 'yop_poll' ); ?>
													:<br><small><?php _e( 'all the values submitted in this field by your users will be automatically added as an available "Answer"', 'yop_poll' ) ?></small>
												</th>
												<td>
													<label for="yop-poll-add-other-answers-to-default-answers-no"><input
															id="yop-poll-add-other-answers-to-default-answers-no"
															<?php echo 'no' == $default_options['add_other_answers_to_default_answers'] ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[add_other_answers_to_default_answers]"
														value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
														for="yop-poll-add-other-answers-to-default-answers-yes"><input
															id="yop-poll-add-other-answers-to-default-answers-yes"
															<?php echo 'yes' == $default_options['add_other_answers_to_default_answers'] ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[add_other_answers_to_default_answers]"
														value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-display-other-answers-values-div" style="<?php echo 'no' == $default_options['allow_other_answers'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Display Other Answers Values', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-display-other-answers-values-no"><input
															id="yop-poll-display-other-answers-values-no"
															<?php echo 'no' == $default_options['display_other_answers_values'] ? 'checked="checked"' : ''; ?>
															type="radio"
															name="yop_poll_options[display_other_answers_values]"
														value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
														for="yop-poll-display-other-answers-values-yes"><input
															id="yop-poll-display-other-answers-values-yes"
															<?php echo 'yes' == $default_options['display_other_answers_values'] ? 'checked="checked"' : ''; ?>
															type="radio"
															name="yop_poll_options[display_other_answers_values]"
														value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-is-default-other-answers-values-div" style="<?php echo 'no' == $default_options['allow_other_answers'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Make "Other answer" default answer ', 'yop_poll' ); ?>
													:<br>
													<small><?php _e( '"Other Answer" will be autoselected', 'yop_poll' ); ?></small>
												</th>
												<td><label for="yop-poll-is-default-other-answers-no"><input
															id="yop-poll-is-default-other-answers-no"
															<?php echo $default_options['is_default_answer'] == 'no' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[is_default_answer]"
														value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
														for="yop-poll-is-default-other-answers-yes"><input
															id="yop-poll-is-default-other-answers-yes"
															<?php echo $default_options['is_default_answer'] == 'yes' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[is_default_answer]"
														value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Allow Multiple Answers ', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-allow-multiple-answers-no"><input
															id="yop-poll-allow-multiple-answers-no"
															<?php echo $default_options['allow_multiple_answers'] == 'no' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[allow_multiple_answers]"
														value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
														for="yop-poll-allow-multiple-answers-yes"><input
															id="yop-poll-allow-multiple-answers-yes"
															<?php echo $default_options['allow_multiple_answers'] == 'yes' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[allow_multiple_answers]"
														value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-allow-multiple-answers-div" style="<?php echo $default_options['allow_multiple_answers'] == 'no' ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Number of allowed answers', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-allow-multiple-answers-number"
														type="text"
														name="yop_poll_options[allow_multiple_answers_number]"
														value="<?php echo $default_options['allow_multiple_answers_number']; ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-allow-multiple-answers-div1" style="<?php echo $default_options['allow_multiple_answers'] == 'no' ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Min Number of allowed answers', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-allow-multiple-answers-min-number"
														type="text"
														name="yop_poll_options[allow_multiple_answers_min_number]"
														value="<?php echo $default_options['allow_multiple_answers_min_number']; ?>"/>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Display Answers ', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-display-answers-vertical"><input
															id="yop-poll-display-answers-vertical"
															<?php echo $default_options['display_answers'] == 'vertical' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[display_answers]"
														value="vertical"/> <?php _e( 'Vertical', 'yop_poll' ); ?></label>
													<label for="yop-poll-display-answers-orizontal"><input
															id="yop-poll-display-answers-orizontal"
															<?php echo $default_options['display_answers'] == 'orizontal' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[display_answers]"
														value="orizontal"/> <?php _e( 'Horizontal', 'yop_poll' ); ?></label>
													<label for="yop-poll-display-answers-tabulated"><input
															id="yop-poll-display-answers-tabulated"
															<?php echo $default_options['display_answers'] == 'tabulated' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[display_answers]"
														value="tabulated"/> <?php _e( 'Tabulated', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-display-answers-tabulated-div" style="<?php echo $default_options['display_answers'] != 'tabulated' ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Columns', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-display-answers-tabulated-cols"
														type="text"
														name="yop_poll_options[display_answers_tabulated_cols]"
														value="<?php echo $default_options['display_answers_tabulated_cols']; ?>"/>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Display Results ', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-display-results-vertical"><input
															id="yop-poll-display-results-vertical"
															<?php echo $default_options['display_results'] == 'vertical' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[display_results]"
														value="vertical"> <?php _e( 'Vertical', 'yop_poll' ); ?></label>
													<label for="yop-poll-display-results-orizontal"><input
															id="yop-poll-display-results-orizontal"
															<?php echo $default_options['display_results'] == 'orizontal' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[display_results]"
														value="orizontal"> <?php _e( 'Horizontal', 'yop_poll' ); ?></label>
													<label for="yop-poll-display-results-tabulated"><input
															id="yop-poll-display-results-tabulated"
															<?php echo $default_options['display_results'] == 'tabulated' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[display_results]"
														value="tabulated"> <?php _e( 'Tabulated', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-display-results-tabulated-div" style="<?php echo $default_options['display_results'] != 'tabulated' ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Columns', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-display-results-tabulated-cols"
														type="text"
														name="yop_poll_options[display_results_tabulated_cols]"
														value="<?php echo $default_options['display_results_tabulated_cols']; ?>"/>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Use Template Result Bar', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-use-template-bar-no"><input
															id="yop-poll-use-template-bar-no"
															<?php echo 'no' == $default_options['use_template_bar'] ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[use_template_bar]"
														value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
														for="yop-poll-use-template-bar-yes"><input
															id="yop-poll-use-template-bar-yes"
															<?php echo 'yes' == $default_options['use_template_bar'] ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[use_template_bar]"
														value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
											</tr>
											<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
												<th><label for="yop-poll-bar-background"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_background_label']; ?></label>
												</th>
												<td>#<input class="yop-small-input"
														id="yop-poll-bar-background"
														value="<?php echo $default_options['bar_background']; ?>"
														onblur="yop_poll_update_bar_style('#yop-poll-bar-preview', 'background-color', '#' + this.value)"
														type="text" name="yop_poll_options[bar_background]"/>
												</td>
											</tr>
											<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
												<th><label for="yop-poll-bar-height"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_height_label']; ?></label>
												</th>
												<td><input class="yop-small-input" id="yop-poll-bar-height"
														value="<?php echo $default_options['bar_height']; ?>"
														onblur="yop_poll_update_bar_style('#yop-poll-bar-preview', 'height', this.value + 'px')"
														type="text" name="yop_poll_options[bar_height]"/> px</td>
											</tr>
											<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
												<th><label for="yop-poll-bar-border-color"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_border_color_label']; ?></label>
												</th>
												<td>#<input class="yop-small-input"
														id="yop-poll-bar-border-color"
														value="<?php echo $default_options['bar_border_color']; ?>"
														onblur="yop_poll_update_bar_style( '#yop-poll-bar-preview', 'border-color', '#' + this.value )"
														type="text" name="yop_poll_options[bar_border_color]"/>
												</td>
											</tr>
											<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
												<th><label for="yop-poll-bar-border-width"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_border_width_label']; ?></label>
												</th>
												<td><input class="yop-small-input"
														id="yop-poll-bar-border-width"
														value="<?php echo $default_options['bar_border_width']; ?>"
														onblur="yop_poll_update_bar_style('#yop-poll-bar-preview', 'border-width', this.value + 'px')"
														type="text" name="yop_poll_options[bar_border_width]"/> px</td>
											</tr>
											<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">
												<th><label for="yop-poll-bar-border-style"><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_style_border_style_label']; ?></label>
												</th>
												<td><select id="yop-poll-bar-border-style"
														onchange="yop_poll_update_bar_style('#yop-poll-bar-preview', 'border-style', this.value)"
														name="yop_poll_options[bar_border_style]">
														<option
															<?php print 'solid' == $default_options['bar_border_style'] ? 'selected="selected"' : ''; ?>
															value="solid">Solid</option>
														<option
															<?php print 'dashed' == $default_options['bar_border_style'] ? 'selected="selected"' : ''; ?>
															value="dashed">Dashed</option>
														<option
															<?php print 'dotted' == $default_options['bar_border_style'] ? 'selected="selected"' : ''; ?>
															value="dotted">Dotted</option>
													</select></td>
											</tr>
											<tr class="yop-poll-custom-result-bar-table yop_poll_suboption" style="<?php echo $default_options['use_template_bar'] == 'yes' ? 'display: none;' : ''; ?>">

												<th><label><?php echo $yop_poll_add_new_config['text_poll_bar_style']['poll_bar_preview_label']; ?></label>
												</th>
												<td>
													<div id="yop-poll-bar-preview"; style="width: 100px; height: <?php echo $default_options['bar_height']; ?>
													px; background-color:#<?php

														echo $default_options ['bar_background'];
													?>
													; border-style: <?php echo $default_options['bar_border_style']; ?>
													; border-width: <?php echo $default_options['bar_border_width']; ?>
													px; border-color: #<?php

														echo $default_options ['bar_border_color'];
													?>;"></div>
												</td>
											</tr>
											<tr>
												<th><?php _e( 'Sort Answers', 'yop_poll' ); ?>:</th>
												<td valign="top"><label for="yop_poll_sorting_answers_exact"><input
															id="yop_poll_sorting_answers_exact"
															<?php echo $default_options['sorting_answers'] == 'exact' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_answers]"
														value="exact"> <?php _e( 'Exact Order', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_answers_alphabetical"><input
															id="yop_poll_sorting_answers_alphabetical"
															<?php echo $default_options['sorting_answers'] == 'alphabetical' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_answers]"
														value="alphabetical"> <?php _e( 'Alphabetical Order', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_answers_random"><input
															id="yop_poll_sorting_answers_random"
															<?php echo $default_options['sorting_answers'] == 'random' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_answers]"
														value="random"> <?php _e( 'Random Order', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_answers_votes"><input
															id="yop_poll_sorting_answers_votes"
															<?php echo $default_options['sorting_answers'] == 'votes' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_answers]"
														value="votes"> <?php _e( 'Number of Votes', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Sort Answers Rule', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop_poll_sorting_answers_asc"><input
															id="yop_poll_sorting_answers_asc"
															<?php echo $default_options['sorting_answers_direction'] == 'asc' ? 'checked="checked"' : ''; ?>
															type="radio"
															name="yop_poll_options[sorting_answers_direction]"
														value="asc"> <?php _e( 'Ascending', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_answers_desc"><input
															id="yop_poll_sorting_answers_desc"
															<?php echo $default_options['sorting_answers_direction'] == 'desc' ? 'checked="checked"' : ''; ?>
															type="radio"
															name="yop_poll_options[sorting_answers_direction]"
														value="desc"> <?php _e( 'Descending', 'yop_poll' ); ?> </label>
												</td>
											</tr>
											<tr>
												<th><?php _e( 'Sorting Results in', 'yop_poll' ); ?>:</th>
												<td valign="top"><label for="yop_poll_sorting_results_exact"><input
															id="yop_poll_sorting_results_exact"
															<?php echo $default_options['sorting_results'] == 'exact' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_results]"
														value="exact"> <?php _e( 'Exact Order', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_results_alphabetical"><input
															id="yop_poll_sorting_results_alphabetical"
															<?php echo $default_options['sorting_results'] == 'alphabetical' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_results]"
														value="alphabetical"> <?php _e( 'Alphabetical Order', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_results_random"><input
															id="yop_poll_sorting_results_random"
															<?php echo $default_options['sorting_results'] == 'random' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_results]"
														value="random"> <?php _e( 'Random Order', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_results_votes"><input
															id="yop_poll_sorting_results_votes"
															<?php echo $default_options['sorting_results'] == 'votes' ? 'checked="checked"' : ''; ?>
															type="radio" name="yop_poll_options[sorting_results]"
														value="votes"> <?php _e( 'Number of Votes', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Sorting Results Rule', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop_poll_sorting_results_asc"><input
															id="yop_poll_sorting_results_asc"
															<?php echo $default_options['sorting_results_direction'] == 'asc' ? 'checked="checked"' : ''; ?>
															type="radio"
															name="yop_poll_options[sorting_results_direction]"
														value="asc"> <?php _e( 'Ascending', 'yop_poll' ); ?></label>
													<label for="yop_poll_sorting_results_desc"><input
															id="yop_poll_sorting_results_desc"
															<?php echo $default_options['sorting_results_direction'] == 'desc' ? 'checked="checked"' : ''; ?>
															type="radio"
															name="yop_poll_options[sorting_results_direction]"
														value="desc"> <?php _e( 'Descending', 'yop_poll' ); ?></label>
												</td>
											</tr>
										</tbody>
									</table>

								</div>
							</div>
							<div class="stuffbox" id="yop-poll-customfieldsdiv">
								<h3>
									<span><?php _e( 'Custom Text Fields', 'yop_poll' ); ?></span>
								</h3>
								<div class="inside">
									<table cellspacing="0" class="links-table"
										id='yop-poll-customfields-table'>
										<tbody>
											<?php
												for ( $custom_field_id = 1; $custom_field_id < $customfields_number; $custom_field_id++ ) {
													if ( isset ( $custom_fields [$custom_field_id - 1] ['id'] ) ){
													?>
													<tr class="yop_poll_tr_customfields"
														id="yop_poll_tr_customfield<?php echo $custom_field_id; ?>">
														<th scope="row"><label class="yop_poll_customfield_label"
																for="yop_poll_customfield<?php echo $custom_field_id; ?>"><?php echo $yop_poll_add_new_config['text_customfield'] ?> <?php echo $custom_field_id ?></label>
														</th>
														<td><input type="hidden"
																value="<?php echo isset( $custom_fields[$custom_field_id - 1]['id'] ) ? $custom_fields[$custom_field_id - 1]['id'] : ''; ?>"
																name="yop_poll_customfield_ids[customfield<?php echo $custom_field_id ?>]"/>
															<input type="text"
																value="<?php echo isset( $custom_fields[$custom_field_id - 1]['custom_field'] ) ? $custom_fields[$custom_field_id - 1]['custom_field'] : ''; ?>"
																id="yop-poll-customfield<?php echo $custom_field_id ?>"
																name="yop_poll_customfield[customfield<?php echo $custom_field_id ?>]"/>
															<input value="yes"
																<?php if ( isset ( $custom_fields[$custom_field_id - 1]['required'] ) )
																		echo ( 'yes' == $custom_fields[$custom_field_id - 1]['required'] ) ? 'checked="checked"' : ''; ?>
																id="yop-poll-customfield-required-<?php echo $custom_field_id ?>"
																type="checkbox"
																name="yop_poll_customfield_required[customfield<?php echo $custom_field_id ?>]"/>
															<label
																for="yop-poll-customfield-required-<?php echo $custom_field_id ?>"><?php echo $yop_poll_add_new_config['text_requiered_customfield'] ?></label>
														</td>
														<td align="right"><input
																onclick="yop_poll_remove_customfield( '#yop-poll-customfields-table', <?php echo $custom_field_id ?> ); return false;"
																type="button"
																value="<?php echo $yop_poll_add_new_config['text_remove_customfield']; ?>"
																class="button"/></td>
													</tr>
													<?php
													}
												}
											?>
										</tbody>
									</table>
									<p id="yop-poll-add-customfield-holder" style="display: block;">
										<button id="yop-poll-add-customfield-button" class="button"><?php _e( 'Add New Custom Field', 'yop_poll' ) ?></button>
									</p>
								</div>
							</div>
							<div class="meta-box-sortables ui-sortable" id="normal-sortables">
								<div class="postbox" id="yop-poll-advanced-options-div">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Poll Start/End Date', 'yop_poll' ); ?>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th><label for="yop-poll-start-date-input"><?php _e( 'Start Date', 'yop_poll' ); ?>
															:</label><br><small>(<?php _e( 'Current Server Time', 'yop_poll' );
																echo ': ' . current_time( 'mysql' ); ?>)</small>
													</th>
													<td><input id="yop-poll-start-date-input" type="text"
															name="yop_poll_options[start_date]"
															value="<?php echo ( 'edit' != $action ) ? current_time( 'mysql' ) : ( '' == $default_options['start_date'] ) ? current_time( 'mysql' ) : $default_options['start_date']; ?>"/>
													</td>
												</tr>
												<tr>
													<th><label for="yop-poll-end-date-input"><?php _e( 'End Date ', 'yop_poll' ); ?>
															:</label><br><small>(<?php _e( 'Current Server Time', 'yop_poll' );
																echo ': ' . current_time( 'mysql' ); ?>)</small>
													</th>
													<td><input style="<?php echo 'yes' == $default_options['never_expire'] ? 'display: none;' : ''; ?>" <?php echo 'yes' == $default_options['never_expire'] ? 'disabled="disabled"' : ''; ?> id="yop-poll-end-date-input" type="text" name="yop_poll_options[end_date]" value="<?php echo '' == $default_options['end_date'] ? '' : $default_options['end_date']; ?>"/>
														<label for="yop-poll-never-expire"><input type="checkbox"
																<?php echo $default_options['never_expire'] == 'yes' ? 'checked="checked"' : ''; ?>
																id="yop-poll-never-expire"
															name="yop_poll_options[never_expire]" value="yes"/> <?php _e( 'No end date', 'yop_poll' ); ?></label>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'View Results Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th>
														<?php _e( 'View Results', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-before-vote"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'before' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-before-vote" type="radio"
															value="before" name="yop_poll_options[view_results]"/> <?php _e( 'Before Vote', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-after-vote"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'after' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-after-vote" type="radio"
															value="after" name="yop_poll_options[view_results]"/> <?php _e( 'After Vote', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-after-poll-end-date"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'after-poll-end-date' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-after-poll-end-date" type="radio"
																value="after-poll-end-date"
															name="yop_poll_options[view_results]"/> <?php _e( 'After Poll End Date', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-never"><input
																class="yop-poll-view-results-hide-custom"
																<?php echo 'never' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-never" type="radio" value="never"
															name="yop_poll_options[view_results]"/> <?php _e( 'Never', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-custom"><input
																class="yop-poll-view-results-show-custom"
																<?php echo 'custom-date' == $default_options['view_results'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-custom" type="radio"
															value="custom-date" name="yop_poll_options[view_results]"/> <?php _e( 'Custom Date', 'yop_poll' ); ?></label>
														<div id="yop-poll-display-view-results-div" style="<?php echo 'custom-date' != $default_options['view_results'] ? 'display: none;' : ''; ?>">
															<label for="yop-poll-view-results-start-date"><?php _e( 'Results display date (the users will be able to see the results starting with this date)', 'yop_poll' ); ?>
																:</label>
															<input id="yop-poll-view-results-start-date" type="text"
																name="yop_poll_options[view_results_start_date]"
																value="<?php echo $default_options['view_results_start_date']; ?>">
														</div></td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Results Permissions', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-permissions-quest-only"><input
																id="yop-poll-view-results-permissions-quest-only"
																<?php echo 'quest-only' == $default_options['view_results_permissions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="quest-only"
															name="yop_poll_options[view_results_permissions]"/> <?php _e( 'Guest Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-permissions-registered-only"><input
																id="yop-poll-view-results-permissions-registered-only"
																<?php echo 'registered-only' == $default_options['view_results_permissions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="registered-only"
															name="yop_poll_options[view_results_permissions]"/> <?php _e( 'Registered Users Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-permissions-guest-registered"><input
																id="yop-poll-view-results-permissions-guest-registered"
																<?php echo 'guest-registered' == $default_options['view_results_permissions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="guest-registered"
															name="yop_poll_options[view_results_permissions]"/> <?php _e( 'Guest &amp; Registered Users', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Results Display', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-votes-number"><input
																id="yop-poll-view-results-votes-number"
																<?php echo 'votes-number' == $default_options['view_results_type'] ? 'checked="checked"' : ''; ?>
																type="radio" value="votes-number"
															name="yop_poll_options[view_results_type]"/> <?php _e( 'By Votes Number', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-percentages"><input
																id="yop-poll-view-results-percentages"
																<?php echo 'percentages' == $default_options['view_results_type'] ? 'checked="checked"' : ''; ?>
																type="radio" value="percentages"
															name="yop_poll_options[view_results_type]"/> <?php _e( 'Percentages', 'yop_poll' ); ?></label>
														<label
															for="yop-poll-view-results-votes-number-and-percentages"><input
																id="yop-poll-view-results-votes-number-and-percentages"
																<?php echo 'votes-number-and-percentages' == $default_options['view_results_type'] ? 'checked="checked"' : ''; ?>
																type="radio" value="votes-number-and-percentages"
															name="yop_poll_options[view_results_type]"/> <?php _e( 'by Votes Number and Percentages', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Poll Answer Result Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-answer-result-label" type="text"
															name="yop_poll_options[answer_result_label]"
															value="<?php echo esc_html( stripslashes( $default_options['answer_result_label'] ) ); ?>"/>
														<small><i><?php _e( 'Use %POLL-ANSWER-RESULT-PERCENTAGES% for showing answer percentages and  %POLL-ANSWER-RESULT-VOTES% for showing answer number of votes', 'yop_poll' ); ?></i></small>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Poll Answer Result Votes Number Label', 'yop_poll' ); ?>
														:
													</th>
													<td>
														<?php _e( 'Singular', 'yop_poll' ); ?>
														<input
															id="yop-poll-singular-answer-result-votes-number-label"
															type="text"
															name="yop_poll_options[singular_answer_result_votes_number_label]"
															value="<?php echo esc_html( stripslashes( $default_options['singular_answer_result_votes_number_label'] ) ); ?>"/>
														<?php _e( 'Plural', 'yop_poll' ); ?>
														<input
															id="yop-poll-plural-answer-result-votes-number-label"
															type="text"
															name="yop_poll_options[plural_answer_result_votes_number_label]"
															value="<?php echo esc_html( stripslashes( $default_options['plural_answer_result_votes_number_label'] ) ); ?>"/>

													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Vote Button Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-vote-button-label" type="text"
															name="yop_poll_options[vote_button_label]"
															value="<?php echo esc_html( stripslashes( $default_options['vote_button_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Results Link', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-results-link-yes"><input
																<?php echo 'yes' == $default_options['view_results_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-link-yes" type="radio"
															value="yes" name="yop_poll_options[view_results_link]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-results-link-no"><input
																<?php echo 'no' == $default_options['view_results_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-results-link-no" type="radio" value="no"
															name="yop_poll_options[view_results_link]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-results-link-div" style="<?php echo 'yes' != $default_options['view_results_link'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Results Link Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-results-link-label" type="text"
															name="yop_poll_options[view_results_link_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_results_link_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Back To Vote Link ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-back-to-vote-link-yes"><input
																<?php echo 'yes' == $default_options['view_back_to_vote_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-back-to-vote-link-yes" type="radio"
															value="yes" name="yop_poll_options[view_back_to_vote_link]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-back-to-vote-link-no"><input
																<?php echo 'no' == $default_options['view_back_to_vote_link'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-back-to-vote-link-no" type="radio"
															value="no" name="yop_poll_options[view_back_to_vote_link]"/><?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-back-to-vote-link-div" style="<?php echo 'yes' != $default_options['view_back_to_vote_link'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Back To Vote Link Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-back-to-vote-link-label"
															type="text"
															name="yop_poll_options[view_back_to_vote_link_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_back_to_vote_link_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Total Votes ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-total-votes-yes"><input
																<?php echo 'yes' == $default_options['view_total_votes'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-votes-yes" type="radio" value="yes"
															name="yop_poll_options[view_total_votes]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-total-votes-no"><input
																<?php echo 'no' == $default_options['view_total_votes'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-votes-no" type="radio" value="no"
															name="yop_poll_options[view_total_votes]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-total-votes-div" style="<?php echo 'yes' != $default_options['view_total_votes'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Total Votes Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-total-votes-label" type="text"
															name="yop_poll_options[view_total_votes_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_total_votes_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'View Total Answers ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-view-total-answers-yes"><input
																<?php echo 'yes' == $default_options['view_total_answers'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-answers-yes" type="radio"
															value="yes" name="yop_poll_options[view_total_answers]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-view-total-answers-no"><input
																<?php echo 'no' == $default_options['view_total_answers'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-view-total-answers-no" type="radio" value="no"
															name="yop_poll_options[view_total_answers]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-view-total-answers-div" style="<?php echo 'yes' != $default_options['view_total_answers'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'View Total Answers Label', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-view-total-answers-label" type="text"
															name="yop_poll_options[view_total_answers_label]"
															value="<?php echo esc_html( stripslashes( $default_options['view_total_answers_label'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Message After Vote', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-message-after-vote" type="text"
															name="yop_poll_options[message_after_vote]"
															value="<?php echo esc_html( stripslashes( $default_options['message_after_vote'] ) ); ?>"/>
													</td>
												</tr>
												<?php if ( 'no' == $default_options['has_auto_generate_poll_page'] ){ ?>
													<tr>
														<th>
															<?php _e( 'Auto Generate Poll Page ', 'yop_poll' ); ?>:
														</th>
														<td><input
																<?php checked( 'yes', $default_options['auto_generate_poll_page'] ); ?>
																id="yop-poll-auto-generate-poll-page-yes" type="radio"
																value="yes" name="yop_poll_options[auto_generate_poll_page]"/><label
																for="yop-poll-auto-generate-poll-page-yes"><?php _e( 'Yes', 'yop_poll' ); ?></label>
															<input
																<?php checked( 'no', $default_options['auto_generate_poll_page'] ); ?>
																id="yop-poll-auto-generate-poll-page-no" type="radio"
																value="no" name="yop_poll_options[auto_generate_poll_page]"/><label
																for="yop-poll-auto-generate-poll-page-no"><?php _e( 'No', 'yop_poll' ); ?></label>
														</td>
													</tr>
													<?php } ?>
												<tr>
													<th><label for="yop-poll-page-url"><?php _e( 'Poll Page Url ', 'yop_poll' ); ?>
															:</label>
													</th>
													<td><input id="yop-poll-page-url" type="text"
															name="yop_poll_options[poll_page_url]"
															value="<?php echo esc_html( stripslashes( $default_options['poll_page_url'] ) ); ?>"/>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div">
									<div title="Click to toggle" class="handlediv"><br/></div>
									<h3 class="hndle">
										<span><?php _e( 'Other Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
												<tr>
													<th>
														<?php _e( 'Use CAPTCHA ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-use-captcha-no"><input
																id="yop-poll-use-captcha-no"
																<?php echo 'no' == $default_options['use_captcha'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[use_captcha]"
															value="no"/> <?php _e( 'No', 'yop_poll' ); ?></label> <label
															for="yop-poll-use-captcha-yes"><input
																id="yop-poll-use-captcha-yes"
																<?php echo 'yes' == $default_options['use_captcha'] ? 'checked="checked"' : ''; ?>
																type="radio" name="yop_poll_options[use_captcha]"
															value="yes"/> <?php _e( 'Yes', 'yop_poll' ); ?></label></td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Vote Permissions ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-vote-permisions-quest-only"><input
																id="yop-poll-vote-permisions-quest-only"
																<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="quest-only"
															name="yop_poll_options[vote_permisions]"/> <?php _e( 'Guest Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-registered-only"><input
																id="yop-poll-vote-permisions-registered-only"
																<?php echo 'registered-only' == $default_options['vote_permisions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="registered-only"
															name="yop_poll_options[vote_permisions]"/> <?php _e( 'Registered Users Only', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-guest-registered"><input
																id="yop-poll-vote-permisions-guest-registered"
																<?php echo 'guest-registered' == $default_options['vote_permisions'] ? 'checked="checked"' : ''; ?>
																type="radio" value="guest-registered"
															name="yop_poll_options[vote_permisions]"/> <?php _e( 'Guest &amp; Registered Users', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<?php if ( false ){ ?>
													<tr class="yop-poll-vote-as-div" style="<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
														<th>
															<?php _e( 'Vote as Facebook User', 'yop_poll' ); ?>:
															<?php if ($pro_options['pro_user'] == 'no') { ?>
																<br/><small>(<?php _e( 'Available only for pro version of Yop Poll', 'yop_poll' ); ?>
																	)</small>
																<?php } ?></label>
														</th>
														<td><label for="yop-poll-vote-permisions-facebook-yes"><input
																	<?php echo 'yes' == $default_options['vote_permisions_facebook'] ? 'checked="checked"' : ''; ?>
																	id="yop-poll-vote-permisions-facebook-yes" type="radio"
																value="yes" name="yop_poll_options[vote_permisions_facebook]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
															<label for="yop-poll-vote-permisions-facebook-no"><input
																	<?php echo 'no' == $default_options['vote_permisions_facebook'] ? 'checked="checked"' : ''; ?>
																	id="yop-poll-vote-permisions-facebook-no" type="radio" value="no"
																name="yop_poll_options[vote_permisions_facebook]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
														</td>
													</tr>
													<tr class="yop-poll-vote-as-div yop_poll_suboption" id="yop-poll-vote-permisions-facebook-div" style="<?php echo 'yes' != $default_options['vote_permisions_facebook'] ? 'display: none;' : 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
														<th>
															<?php _e( '"Vote as Facebook User" Button Label', 'yop_poll' ); ?>
															:
														</th>
														<td><input id="yop-poll-vote-permisions-facebook-label" type="text"
																name="yop_poll_options[vote_permisions_facebook_label]"
																value="<?php echo esc_html( stripslashes( $default_options['vote_permisions_facebook_label'] ) ); ?>"/>
														</td>
													</tr>
													<?php } ?>

												<tr class="yop-poll-vote-as-div" style="<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Vote as Wordpress User', 'yop_poll' ); ?>
														<br><small> (<?php _e( 'Will force users to login into your blog', 'yop_poll' ); ?>
															)</small>:
													</th>
													<td><label for="yop-poll-vote-permisions-wordpress-yes"><input
																<?php echo 'yes' == $default_options['vote_permisions_wordpress'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-wordpress-yes" type="radio"
															value="yes" name="yop_poll_options[vote_permisions_wordpress]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-wordpress-no"><input
																<?php echo 'no' == $default_options['vote_permisions_wordpress'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-wordpress-no" type="radio" value="no"
															name="yop_poll_options[vote_permisions_wordpress]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop-poll-vote-as-div yop_poll_suboption" id="yop-poll-vote-permisions-wordpress-div" style="<?php echo 'yes' != $default_options['vote_permisions_wordpress'] ? 'display: none;' : 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( '"Vote as Wordpress User" Button Label', 'yop_poll' ); ?>
														:
													</th>
													<td><input id="yop-poll-vote-permisions-wordpress-label" type="text"
															name="yop_poll_options[vote_permisions_wordpress_label]"
															value="<?php echo esc_html( stripslashes( $default_options['vote_permisions_wordpress_label'] ) ); ?>"/>
													</td>
												</tr>

												<tr class="yop-poll-vote-as-div" style="<?php echo 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Vote as Anonymous User', 'yop_poll' ); ?>
														<br><small>(<?php _e( 'Logged users will be treated as anonymous', 'yop_poll' ); ?>
															)</small>:
													</th>
													<td><label for="yop-poll-vote-permisions-anonymous-yes"><input
																<?php echo 'yes' == $default_options['vote_permisions_anonymous'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-anonymous-yes" type="radio"
															value="yes" name="yop_poll_options[vote_permisions_anonymous]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-vote-permisions-anonymous-no"><input
																<?php echo 'no' == $default_options['vote_permisions_anonymous'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-vote-permisions-anonymous-no" type="radio" value="no"
															name="yop_poll_options[vote_permisions_anonymous]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop-poll-vote-as-div yop_poll_suboption" id="yop-poll-vote-permisions-anonymous-div" style="<?php echo 'yes' != $default_options['vote_permisions_anonymous'] ? 'display: none;' : 'quest-only' == $default_options['vote_permisions'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( '"Vote as Anonymous User" Button Label', 'yop_poll' ); ?>
														:
													</th>
													<td><input id="yop-poll-vote-permisions-anonymous-label" type="text"
															name="yop_poll_options[vote_permisions_anonymous_label]"
															value="<?php echo esc_html( stripslashes( $default_options['vote_permisions_anonymous_label'] ) ); ?>"/>
													</td>
												</tr>

												<tr>
													<th>
														<?php _e( 'Blocking Voters ', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-blocking-voters-dont-block"><input
																class="yop-poll-blocking-voters-hide-interval"
																<?php echo 'dont-block' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-dont-block" type="radio"
															value="dont-block" name="yop_poll_options[blocking_voters]"/> <?php _e( 'Dont`t Block', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-cookie"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'cookie' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-cookie" type="radio"
															value="cookie" name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Cookie', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-ip"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'ip' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-ip" type="radio" value="ip"
															name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Ip', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-username"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'username' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-username" type="radio"
															value="username" name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Username', 'yop_poll' ); ?></label>
														<label for="yop-poll-blocking-voters-cookie-ip"><input
																class="yop-poll-blocking-voters-show-interval"
																<?php echo 'cookie-ip' == $default_options['blocking_voters'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-blocking-voters-cookie-ip" type="radio"
															value="cookie-ip" name="yop_poll_options[blocking_voters]"/> <?php _e( 'By Cookie &amp; Ip', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-blocking-voters-interval-div" style="<?php echo 'dont-block' == $default_options['blocking_voters'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Blocking voters interval', 'yop_poll' ); ?>:
													</th>
													<td><input type="text"
															name="yop_poll_options[blocking_voters_interval_value]"
															id="yop-poll-blocking-voters-interval-value"
															value="<?php echo $default_options['blocking_voters_interval_value']; ?>"/>
														<select id="yop-poll-blocking-voters-interval-unit"
															name="yop_poll_options[blocking_voters_interval_unit]">
															<option
																<?php echo 'seconds' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="seconds"><?php _e( 'Seconds', 'yop_poll' ); ?></option>
															<option
																<?php echo 'minutes' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="minutes"><?php _e( 'Minutes', 'yop_poll' ); ?></option>
															<option
																<?php echo 'hours' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="hours"><?php _e( 'Hours', 'yop_poll' ); ?></option>
															<option
																<?php echo 'days' == $default_options['blocking_voters_interval_unit'] ? 'selected="selected"' : ''; ?>
																value="days"><?php _e( 'Days', 'yop_poll' ); ?></option>
														</select></td>
												</tr>
												<tr class="yop-poll-limit-number-of-votes-per-user-div">
													<th>
														<?php _e( 'Limit Number of Votes per User', 'yop_poll' ); ?>:
														<br><small>(<?php _e( 'Only for logged users', 'yop_poll' ); ?>
															)</small>
													</th>
													<td><label for="yop-poll-limit-number-of-votes-per-user-yes"><input
																<?php echo 'yes' == $default_options['limit_number_of_votes_per_user'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-limit-number-of-votes-per-user-yes" type="radio"
															value="yes" name="yop_poll_options[limit_number_of_votes_per_user]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-limit-number-of-votes-per-user-no"><input
																<?php echo 'no' == $default_options['limit_number_of_votes_per_user'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-limit-number-of-votes-per-user-no" type="radio" value="no"
															name="yop_poll_options[limit_number_of_votes_per_user]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop-poll-limit-number-of-votes-per-user-divs yop_poll_suboption" id="yop-poll-number-of-votes-per-user-div" style="<?php echo 'yes' != $default_options['limit_number_of_votes_per_user'] ? 'display: none;' : '' ?>">
													<th>
														<?php _e( 'Number of Votes per User', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-number-of-votes-per-user" type="text"
															name="yop_poll_options[number_of_votes_per_user]"
															value="<?php echo esc_html( stripslashes( $default_options['number_of_votes_per_user'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th><?php _e( 'Poll Template ', 'yop_poll' ); ?>:</th>
													<td>
														<?php
															$templates = YOP_POLL_MODEL::get_yop_poll_templates_search( 'id', 'asc' );
														?>
														<select class="yop-poll-template" id="yop-poll-template"
															name="yop_poll_options[template]"
															onchange="yop_poll_return_template_preview(jQuery(this).val(), '#yop_poll_preview_page_template', 1);">
															<option value=""><?php _e( '--SELECT Template--', 'yop_poll' ); ?></option>
															<?php
																if ( count( $templates ) > 0 ){
																	foreach ( $templates as $template ) {
																	?>
																	<option
																		<?php if ( $default_options['template'] == $template['id'] )
																				echo 'selected="selected"' ?>
																		value="<?php echo $template['id']; ?>"><?php echo esc_html( stripslashes( $template['name'] ) ) ?></option>
																	<?php
																	}
																}
															?>
														</select>
														<div id="yop_poll_preview_page_template" style="position: relative; float: right;">
															<?php
																if ( ( $default_options['template'] ) )
																	print( Yop_Poll_Model::return_template_preview_html( $default_options['template'], 1 ) );
															?>
														</div>
													</td>
												</tr>
												<tr class="yop_poll_suboption">
													<th>
														<?php _e( 'Poll Template Width', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-template-width"
															type="text"
															name="yop_poll_options[template_width]"
															style="width: 50%;"
															value="<?php echo $default_options['template_width']; ?>"/>
													</td>
												</tr>
												<tr>
													<th><?php _e( 'Widget Template ', 'yop_poll' ); ?>:</th>
													<td>
														<?php
															$templates = YOP_POLL_MODEL::get_yop_poll_templates_search( 'id', 'asc' );
														?>
														<select class="yop-poll-widget-template" id="yop-poll-widget-template"
															name="yop_poll_options[widget_template]"
															onchange="yop_poll_return_template_preview(jQuery(this).val(), '#yop_poll_preview_widget_template', 2);">
															<option value=""><?php _e( '--SELECT Template--', 'yop_poll' ); ?></option>
															<?php
																if ( count( $templates ) > 0 ){
																	foreach ( $templates as $template ) {
																	?>
																	<option
																		<?php if ( $default_options['widget_template'] == $template['id'] )
																				echo 'selected="selected"' ?>
																		value="<?php echo $template['id']; ?>"><?php echo esc_html( stripslashes( $template['name'] ) ) ?></option>
																	<?php
																	}
																}
															?>
														</select>
														<div id="yop_poll_preview_widget_template" style="position: relative; float: right;">
															<?php
																if ( ( $default_options['widget_template'] ) )
																	print( Yop_Poll_Model::return_template_preview_html( $default_options['widget_template'], 2 ) );
															?>
														</div>
													</td>
												</tr>
												<tr class="yop_poll_suboption">
													<th>
														<?php _e( 'Widget Template Width', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-widget-template-width"
															type="text"
															name="yop_poll_options[widget_template_width]"
															style="width: 50%;"
															value="<?php echo $default_options['widget_template_width']; ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Percentages Decimals', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-percentages-decimals" type="text"
															name="yop_poll_options[percentages_decimals]"
															value="<?php echo esc_html( stripslashes( $default_options['percentages_decimals'] ) ); ?>"/>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Use Default Loading Image', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-use-default-loading-image-yes"><input
																<?php echo 'yes' == $default_options['use_default_loading_image'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-use-default-loading-image-yes" type="radio"
																value="yes"
															name="yop_poll_options[use_default_loading_image]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-use-default-loading-image-no"><input
																<?php echo 'no' == $default_options['use_default_loading_image'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-use-default-loading-image-no" type="radio"
																value="no"
															name="yop_poll_options[use_default_loading_image]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-use-default-loading-image-div" style="<?php echo 'yes' == $default_options['use_default_loading_image'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Loading Image Url', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-loading-image-url" type="text"
															name="yop_poll_options[loading_image_url]"
															value="<?php echo esc_html( stripslashes( $default_options['loading_image_url'] ) ); ?>"/>
													</td>
												</tr>

												<tr>
													<th>
														<?php _e( 'Redirect After Vote', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-redirect-after-vote-yes"><input
																<?php echo 'yes' == $default_options['redirect_after_vote'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-redirect-after-vote-yes" type="radio"
																value="yes"
															name="yop_poll_options[redirect_after_vote]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-redirect-after-vote-no"><input
																<?php echo 'no' == $default_options['redirect_after_vote'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-redirect-after-vote-no" type="radio"
																value="no"
															name="yop_poll_options[redirect_after_vote]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-redirect-after-vote-url-div" style="<?php echo 'no' == $default_options['redirect_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Redirect After Vote Url', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-redirect-after-vote-url" type="text"
															name="yop_poll_options[redirect_after_vote_url]"
															value="<?php echo esc_html( stripslashes( $default_options['redirect_after_vote_url'] ) ); ?>"/>
													</td>
												</tr>

												<tr>
													<th>
														<?php _e( 'Reset Poll Stats Automatically', 'yop_poll' ); ?>:
													</th>
													<td><label for="yop-poll-schedule-reset-poll-stats-yes"><input
																<?php echo 'yes' == $default_options['schedule_reset_poll_stats'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-schedule-reset-poll-stats-yes" type="radio"
																value="yes"
															name="yop_poll_options[schedule_reset_poll_stats]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
														<label for="yop-poll-schedule-reset-poll-stats-no"><input
																<?php echo 'no' == $default_options['schedule_reset_poll_stats'] ? 'checked="checked"' : ''; ?>
																id="yop-poll-schedule-reset-poll-stats-no" type="radio"
																value="no"
															name="yop_poll_options[schedule_reset_poll_stats]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop-poll-schedule-reset-poll-stats-options-div yop_poll_suboption" style="<?php echo 'no' == $default_options['schedule_reset_poll_stats'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Reset Stats Date', 'yop_poll' ); ?>
														:<br><small>(<?php _e( 'Current Server Time', 'yop_poll' );
																echo ': ' . current_time( 'mysql' ); ?>)</small>
													</th>
													<td><input id="yop-poll-schedule-reset-poll-stats-date" type="text"
															name="yop_poll_options[schedule_reset_poll_date]"
															value="<?php echo date( 'Y-m-d H:i:s', $default_options['schedule_reset_poll_date'] ); ?>"/>
													</td>
												</tr>
												<tr class="yop-poll-schedule-reset-poll-stats-options-div yop_poll_suboption" style="<?php echo 'no' == $default_options['schedule_reset_poll_stats'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Reset Stats Every', 'yop_poll' ); ?>:
													</th>
													<td><input style="width:20%" id="yop-poll-schedule-reset-poll-stats-recurring-value" type="text"
															name="yop_poll_options[schedule_reset_poll_recurring_value]"
															value="<?php echo esc_html( stripslashes( $default_options['schedule_reset_poll_recurring_value'] ) ); ?>"/>
														<select name="yop_poll_options[schedule_reset_poll_recurring_unit]">
															<option value="hour" <?php echo selected( 'hour', $default_options['schedule_reset_poll_recurring_unit'] ) ?>>HOURS</option>
															<option value="day" <?php echo selected( 'day', $default_options['schedule_reset_poll_recurring_unit'] ) ?>>DAYS</option>
														</select>
													</td>
												</tr>
												<tr>
													<th>
														<?php _e( 'Poll Date Format', 'yop_poll' ); ?>
														: <br/><small><?php _e( 'Check', 'yop_popll' ) ?>
															<a target="_blank" href="http://codex.wordpress.org/Formatting_Date_and_Time"> <?php _e( 'documentation', 'yop_popll' ) ?></a></small>
													</th>
													<td><input id="yop-poll-date-format" type="text"
															name="yop_poll_options[date_format]"
															value="<?php echo esc_html( stripslashes( $default_options['date_format'] ) ); ?>"/>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Archive Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
											<tr>
												<th>
													<?php _e( 'View Poll Archive Link ', 'yop_poll' ); ?>:
												</th>
												<td><input
														<?php checked( 'yes', $default_options['view_poll_archive_link'] ); ?>
														id="yop-poll-view-poll-archive-link-yes" type="radio"
														value="yes" name="yop_poll_options[view_poll_archive_link]"/><label
														for="yop-poll-view-poll-archive-link-yes"><?php _e( 'Yes', 'yop_poll' ); ?></label>
													<input
														<?php checked( 'no', $default_options['view_poll_archive_link'] ); ?>
														id="yop-poll-view-poll-archive-link-no" type="radio"
														value="no" name="yop_poll_options[view_poll_archive_link]"/><label
														for="yop-poll-view-poll-archive-link-no"><?php _e( 'No', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-view-poll-archive-link-div" style="<?php echo 'yes' != $default_options['view_poll_archive_link'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'View Poll Archive Link Label', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-view-poll-archive-link-label"
														type="text"
														name="yop_poll_options[view_poll_archive_link_label]"
														value="<?php echo esc_html( stripslashes( $default_options['view_poll_archive_link_label'] ) ); ?>"/>
												</td>
											</tr>
											<tr id="yop-poll-view-poll-archive-link-div" style="<?php echo 'yes' != $default_options['view_poll_archive_link'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Poll Archive Url', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-poll-archive-url" type="text"
														name="yop_poll_options[poll_archive_url]"
														value="<?php echo esc_html( stripslashes( $default_options['poll_archive_url'] ) ); ?>"/>
												</td>
											</tr>
											<tr>
												<th>
													<?php _e( 'Show Poll In Archive ', 'yop_poll' ); ?>:
												</th>
												<td><label for="yop-poll-show-in-archive-yes"><input
															<?php checked( 'yes', $default_options['show_in_archive'] ); ?>
															id="yop-poll-show-in-archive-yes" type="radio" value="yes"
														name="yop_poll_options[show_in_archive]"/> <?php _e( 'Yes', 'yop_poll' ); ?></label>
													<label for="yop-poll-show-in-archive-no"><input
															<?php checked( 'no', $default_options['show_in_archive'] ); ?>
															id="yop-poll-show-in-archive-no" type="radio" value="no"
														name="yop_poll_options[show_in_archive]"/> <?php _e( 'No', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption" id="yop-poll-show-in-archive-div" style="<?php echo 'yes' != $default_options['show_in_archive'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Archive Order', 'yop_poll' ); ?>:
												</th>
												<td><input id="yop-poll-show-in-archive-order" type="text"
														name="yop_poll_options[archive_order]"
														value="<?php echo esc_html( stripslashes( $default_options['archive_order'] ) ); ?>"/>
												</td>
											</tr>

										</table>
									</div>
								</div>
								<div class="postbox" id="yop-poll-advanced-options-div8">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Notifications Options', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<table cellspacing="0" class="links-table">
											<tbody>
											<tr>
												<th>
													<?php _e( 'Send Email Notifications', 'yop_poll' ); ?>:
												</th>
												<td><input <?php checked( 'yes', $default_options['send_email_notifications'] ); ?>
														id="yop-poll-send-email-notifications-yes" type="radio"
														value="yes" name="yop_poll_options[send_email_notifications]"/><label
														for="yop-poll-send-email-notifications-yes"><?php _e( 'Yes', 'yop_poll' ); ?></label>
													<input <?php checked( 'no', $default_options['send_email_notifications'] ); ?>
														id="yop-poll-send-email-notifications-no" type="radio"
														value="no" name="yop_poll_options[send_email_notifications]"/><label
														for="yop-poll-send-email-notifications-no"><?php _e( 'No', 'yop_poll' ); ?></label>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-from-name-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications From Name', 'yop_poll' ); ?>:
												</th>
												<td valign="top">
													<input id="yop-poll-email-notifications-from-name"
														type="text"
														name="yop_poll_options[email_notifications_from_name]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_from_name'] ) ); ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-from-email-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications From Email', 'yop_poll' ); ?>:
												</th>
												<td valign="top">
													<input id="yop-poll-email-notifications-from-email"
														type="text"
														name="yop_poll_options[email_notifications_from_email]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_from_email'] ) ); ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-recipients-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications Recipients', 'yop_poll' ); ?>
													:<br><small><?php _e( 'Use comma separated email addresses: email@xmail.com,email2@xmail.com', 'yop_poll' ) ?></small>
												</th>
												<td valign="top">
													<input id="yop-poll-email-notifications-recipients"
														type="text"
														name="yop_poll_options[email_notifications_recipients]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_recipients'] ) ); ?>"/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-subject-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications Subject', 'yop_poll' ); ?>:
												</th>
												<td>
													<input id="yop-poll-email-notifications-subject"
														type="text"
														name="yop_poll_options[email_notifications_subject]"
														value="<?php echo esc_html( stripslashes( $default_options['email_notifications_subject'] ) ); ?>"
														/>
												</td>
											</tr>
											<tr class="yop_poll_suboption yop-poll-email-notifications-div" id="yop-poll-email-notifications-body-div" style="<?php echo 'yes' != $default_options['send_email_notifications'] ? 'display: none;' : ''; ?>">
												<th>
													<?php _e( 'Notifications Body', 'yop_poll' ); ?>:
												</th>
												<td>
													<textarea id="yop-poll-email-notifications-body" rows="10"
														name="yop_poll_options[email_notifications_body]"><?php echo esc_html( stripslashes( $default_options['email_notifications_body'] ) ); ?></textarea>
												</td>
											</tr>
										</table>
									</div>
								</div>
								<?php if ( false ){ ?>
									<div class="postbox" id="yop-poll-advanced-options-div9">
										<div title="Click to toggle" class="handlediv">
											<br/>
										</div>
										<h3 class="hndle">
											<span><?php _e( 'Facebook Share Options', 'yop_poll' ); ?>
												<?php if ( $pro_options['pro_user'] == 'no' ){ ?>
													<small>(<?php _e( 'Available only for pro version of Yop Poll', 'yop_poll' ); ?> )</small>
													<?php } ?>
											</span>
										</h3>
										<div class="inside">
											<table cellspacing="0" class="links-table">
												<tbody>
												<tr>
													<th>
														<?php _e( 'Share After Vote ', 'yop_poll' ); ?>:
													</th>
													<td><input
															<?php checked( 'yes', $default_options['share_after_vote'] ); ?>
															id="yop-poll-share-after-vote-yes" type="radio"
															value="yes" name="yop_poll_options[share_after_vote]"/><label
															for="yop-poll-share-after-vote-yes"><?php _e( 'Yes', 'yop_poll' ); ?></label>
														<input
															<?php checked( 'no', $default_options['share_after_vote'] ); ?>
															id="yop-poll-share-after-vote-no" type="radio"
															value="no" name="yop_poll_options[share_after_vote]"/><label
															for="yop-poll-share-after-vote-no"><?php _e( 'No', 'yop_poll' ); ?></label>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-name-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Name', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-name"
															type="text"
															name="yop_poll_options[share_name]"
															value="<?php echo esc_html( stripslashes( $default_options['share_name'] ) ); ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-caption-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Caption', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-caption"
															type="text"
															name="yop_poll_options[share_caption]"
															value="<?php echo esc_html( stripslashes( $default_options['share_caption'] ) ); ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-description-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Description', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-description"
															type="text"
															name="yop_poll_options[share_description]"
															value="<?php echo esc_html( stripslashes( $default_options['share_description'] ) ); ?>"/>
													</td>
												</tr>
												<tr class="yop_poll_suboption" id="yop-poll-share-after-vote-picture-tr" style="<?php echo 'yes' != $default_options['share_after_vote'] ? 'display: none;' : ''; ?>">
													<th>
														<?php _e( 'Share Picture', 'yop_poll' ); ?>:
													</th>
													<td><input id="yop-poll-share-picture"
															type="text"
															name="yop_poll_options[share_picture]"
															value="<?php echo esc_html( stripslashes( $default_options['share_picture'] ) ); ?>"/>
													</td>
												</tr>

											</table>
										</div>
									</div>
									<?php } ?>
							</div>
							<input type="hidden" value="<?php echo $poll_id ?>"
								name="yop_poll_id" id="yop-poll-edit-add-new-form-poll-id"/> <input
								type="hidden" value="<?php echo $action_type ?>"
								name="action_type" id="yop-poll-edit-add-new-form-action-type"/>
							<input type="button" accesskey="p" class="button-primary"
								value="<?php _e( 'Save Poll', 'yop_poll' ); ?>"
								id="yop-poll-edit-add-new-form-submit"/>
						</div>
						<div class="postbox-container" id="postbox-container-1">
							<div class="meta-box-sortables ui-sortable" id="side-sortables">
								<div class="postbox " id="linksubmitdiv">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Save', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<div id="submitlink" class="submitbox">
											<div id="major-publishing-actions">
												<div id="publishing-action">
													<input type="button" accesskey="p" class="button-primary"
														value="<?php _e( 'Save Poll', 'yop_poll' ); ?>"
														id="yop-poll-edit-add-new-form-submit1"/>
												</div>
												<div class="clear"></div>
											</div>
											<div class="clear"></div>
										</div>
									</div>
								</div>
								<?php if ( 'edit' == $action ) : ?>
									<div id="submitdiv" class="postbox ">
										<div title="Click to toggle" class="handlediv">
											<br/>
										</div>
										<h3 class="hndle">
											<span><?php _e( 'Tools ', 'yop_poll' ); ?></span>
										</h3>
										<div class="inside">
											<div id="submitpost" class="submitbox">
												<div id="minor-publishing">
													<div id="misc-publishing-actions">
														<div class="misc-pub-section">
															<label for="post_status"><?php _e( 'Poll Author', 'yop_poll' ) ?>
																:</label>
															<span id="yop-poll-change-poll-author-container-<?php echo $current_poll['id'] ?>"><b><?php $poll_author = get_user_by( 'id', $current_poll ['poll_author'] );
																	echo $poll_author->user_nicename; ?></b></span>
															<a class="edit-post-status hide-if-no-js" href="javascript:void(0)" onclick="yop_poll_show_change_poll_author( '<?php echo $current_poll ['id'] ?>', 'answers'); return false;" style="display: inline;">Edit</a>
														</div>
													</div>
													<div id="misc-publishing-actions">
														<div class="misc-pub-section">
															<label for="post_status"><?php _e( 'Total Votes', 'yop_poll' ) ?>
																:</label>
															<span id="yop-poll-change-no-votes-poll-container-<?php echo $current_poll['id'] ?>"><b><?php echo $current_poll['total_votes'] ?></b></span>
															<a class="edit-post-status hide-if-no-js" href="javascript:void(0)" onclick="yop_poll_show_change_total_number_poll( '<?php echo $current_poll ['id'] ?>', 'votes'); return false;" style="display: inline;">Edit</a>
														</div>
													</div>
													<div id="misc-publishing-actions">
														<div class="misc-pub-section">
															<label for="post_status"><?php _e( 'Total Answers', 'yop_poll' ) ?>
																:</label>
															<span id="yop-poll-change-no-answers-poll-container-<?php echo $current_poll['id'] ?>"><b><?php echo $current_poll['total_answers'] ?></b></span>
															<a class="edit-post-status hide-if-no-js" href="javascript:void(0)" onclick="yop_poll_show_change_total_number_poll( '<?php echo $current_poll ['id'] ?>', 'answers'); return false;" style="display: inline;">Edit</a>
														</div>
													</div>
												</div>
												<div class="clear"></div>
											</div>
										</div>
									</div>
									<?php endif; ?>
							</div>
						</div>
					</div>
					<br class="clear">
				</div>
			</form>
		</div>
		<?php
		}

		public function view_add_edit_poll_template() {
			global $action, $current_user;
			$page_name   = __( 'Add New Poll Template', 'yop_poll' );
			$action_type = 'add-new';
			$template_id = '';
			if ( 'edit' == $action ){
				$template_id     = ( isset ( $_GET ['id'] ) ? intval( $_GET ['id'] ) : 0 );
				$template_author = Yop_Poll_Model::get_poll_template_field_from_database_by_id( 'template_author', $template_id );
				if ( ( !$this->current_user_can( 'edit_own_polls_templates' ) || $template_author != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls_templates' ) ) )
					wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
				$page_name   = __( 'Edit Poll Template', 'yop_poll' );
				$action_type = 'edit';
			}
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$current_template = YOP_POLL_MODEL::get_poll_template_from_database_by_id( $template_id );
		?>
		<div class="wrap">
			<div class="icon32 icon32-yop-poll">
				<br>

			</div>
			<h2><?php print $page_name; ?><?php if ('edit' == $action): ?><a
						class="add-new-h2"
					href="<?php echo esc_url( add_query_arg( array( 'page' => 'yop-polls-templates', 'action' => 'add-new', 'id' => false ) ) ); ?>"><?php _e( 'Add New', 'yop_poll' ); ?></a><?php endif; ?></h2>
			<div id="message"></div>
			<form method="post" name="yop_poll_edit_add_new_template_form"
				id="yop-poll-edit-add-new-template-form">
				<?php wp_nonce_field( 'yop-poll-edit-add-new-template' ); ?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="stuffbox" id="yop-poll-template-namediv">
								<h3>
									<label for="yop-poll-template-name"><?php _e( 'Template Name', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<input type="text" id="yop-poll-template-name"
										value="<?php echo esc_html( stripslashes( $current_template['name'] ) ); ?>"
										tabindex="1" name="yop_poll_template_name" size="30"/>
									<p><?php _e( 'Example: Test Poll Template', 'yop_poll' ); ?></p>
								</div>
							</div>
							<div class="stuffbox" id="yop-poll-before-vote-template-div">
								<h3>
									<label for="yop-poll-before_vote-template-input"><?php _e( 'Template Before Vote', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<?php wp_editor( stripslashes( $current_template['before_vote_template'] ), 'yop-poll-before-vote-template-input', array( 'textarea_name' => 'yop_poll_before_vote_template', 'teeny' => true, 'media_buttons' => false ) ); ?>
								</div>
							</div>
							<div class="stuffbox" id="yop-poll-after-vote-template-div">
								<h3>
									<label for="yop-poll-after-vote-template-input"><?php _e( 'Template After Vote', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<?php wp_editor( stripslashes( $current_template['after_vote_template'] ), 'yop-poll-after-vote-template-input', array( 'textarea_name' => 'yop_poll_after_vote_template', 'teeny' => true, 'media_buttons' => false ) ); ?>
								</div>
							</div>
							<div class="postbox" id="yop-poll-template-before-start-date-div">
								<div title="Click to toggle" class="handlediv"
									id="yop-poll-template-before-start-date-handler">
									<br>

								</div>
								<h3>
									<label for="yop-poll-template-before-start-date-input"><?php _e( 'Template Before Start Date', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<?php wp_editor( stripslashes( $current_template['before_start_date_template'] ), 'yop-poll-template-before-start-date-input', array( 'textarea_name' => 'yop_poll_template_before_start_date', 'teeny' => true, 'media_buttons' => false ) ); ?>
								</div>
							</div>
							<div class="postbox" id="yop-poll-template-after-end-date-div">
								<div title="Click to toggle" class="handlediv"
									id="yop-poll-template-after-end-date-handler">
									<br>

								</div>
								<h3>
									<label for="yop-poll-template-after-end-date-input"><?php _e( 'Template After End Date', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<?php wp_editor( stripslashes( $current_template['after_end_date_template'] ), 'yop-poll-template-after-end-date-input', array( 'textarea_name' => 'yop_poll_template_after_end_date', 'teeny' => true, 'media_buttons' => false ) ); ?>
								</div>
							</div>
							<div class="postbox" id="yop-poll-template-css-div">
								<div title="Click to toggle" class="handlediv"
									id="yop-poll-template-css-handler">
									<br>

								</div>
								<h3>
									<label for="yop-poll-template-css-input"><?php _e( 'Css', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<?php wp_editor( stripslashes( $current_template['css'] ), 'yop-poll-template-css-input', array( 'textarea_name' => 'yop_poll_template_css', 'teeny' => true, 'media_buttons' => false ) ); ?>
								</div>
							</div>
							<div class="postbox" id="yop-poll-template-js-div">
								<div title="Click to toggle" class="handlediv"
									id="yop-poll-template-js-handler">
									<br>

								</div>
								<h3>
									<label for="yop-poll-template-js-input"><?php _e( 'JavaScript', 'yop_poll' ); ?></label>
								</h3>
								<div class="inside">
									<?php wp_editor( stripslashes( $current_template['js'] ), 'yop-poll-template-js-input', array( 'textarea_name' => 'yop_poll_template_js', 'teeny' => true, 'media_buttons' => false ) ); ?>
								</div>
							</div>

							<input type="hidden" value="<?php echo $current_template['id']; ?>"
								name="template_id"
								id="yop-poll-edit-add-new-template-form-template-id"/> <input
								type="hidden" value="<?php echo $action_type ?>"
								name="action_type"
								id="yop-poll-edit-add-new-template-form-action-type"/> <input
								type="button" class="button-primary"
								value="<?php _e( 'Save Poll Template', 'yop_poll' ) ?>"
								id="yop-poll-edit-add-new-template-form-save"/>
						</div>
						<div class="postbox-container" id="postbox-container-1">
							<div class="meta-box-sortables ui-sortable" id="side-sortables">
								<div class="postbox " id="linksubmitdiv">
									<div title="Click to toggle" class="handlediv">
										<br/>
									</div>
									<h3 class="hndle">
										<span><?php _e( 'Save', 'yop_poll' ); ?></span>
									</h3>
									<div class="inside">
										<div id="submitlink" class="submitbox">
											<div id="major-publishing-actions">
												<div id="publishing-action">
													<input type="button" accesskey="p" class="button-primary"
														value="<?php _e( 'Save Poll Template', 'yop_poll' ); ?>"
														id="yop-poll-edit-add-new-template-form-save1"/>
												</div>
												<div class="clear"></div>
											</div>
											<div class="clear"></div>
										</div>
									</div>
								</div>
								<?php
									if ( 'edit' == $action ){
									?>
									<div id="submitdiv" class="postbox ">
										<div title="Click to toggle" class="handlediv">
											<br/>
										</div>
										<h3 class="hndle">
											<span><?php _e( 'Tools ', 'yop_poll' ); ?></span>
										</h3>
										<div class="inside">
											<div id="submitpost" class="submitbox">
												<div id="minor-publishing">
													<div id="misc-publishing-actions">
														<div class="misc-pub-section">
															<label for="post_status"><?php _e( 'Template Author', 'yop_poll' ) ?>
																:</label>
															<span id="yop-poll-change-template-author-container-<?php echo $current_template['id'] ?>"><b><?php $template_author = get_user_by( 'id', $current_template['template_author'] );
																	echo $template_author->user_nicename; ?></b></span>
															<a class="edit-post-status hide-if-no-js" href="javascript:void(0)" onclick="yop_poll_show_change_template_author( '<?php echo $current_template ['id'] ?>', 'answers'); return false;" style="display: inline;">Edit</a>
														</div>
													</div>
												</div>
												<div class="clear"></div>
											</div>
											<div id="submitpost" class="submitbox">
												<div id="minor-publishing">
													<div id="misc-publishing-actions">
														<div class="misc-pub-section">
															<p><?php _e( 'Select Template For Reset', 'yop_poll' ) ?>
																:</p>
															<select name="yop_poll_reset_template_id"
																id="yop-poll-reset-template-id">
																<option <?php selected( 'White', $current_template['name'] ); ?> value="1"><?php _e( 'White', 'yop_poll' ) ?></option>
																<option <?php selected( 'Grey', $current_template['name'] ); ?> value="2"><?php _e( 'Grey', 'yop_poll' ) ?></option>
																<option <?php selected( 'Dark', $current_template['name'] ); ?> value="3"><?php _e( 'Dark', 'yop_poll' ) ?></option>
																<option <?php selected( 'Blue v1', $current_template['name'] ); ?> value="4"><?php _e( 'Blue v1', 'yop_poll' ) ?></option>
																<option <?php selected( 'Blue v2', $current_template['name'] ); ?> value="5"><?php _e( 'Blue v2', 'yop_poll' ) ?></option>
																<option <?php selected( 'Blue v3', $current_template['name'] ); ?> value="6"><?php _e( 'Blue v3', 'yop_poll' ) ?></option>
																<option <?php selected( 'Red v1', $current_template['name'] ); ?> value="7"><?php _e( 'Red v1', 'yop_poll' ) ?></option>
																<option <?php selected( 'Red v2', $current_template['name'] ); ?> value="8"><?php _e( 'Red v2', 'yop_poll' ) ?></option>
																<option <?php selected( 'Red v3', $current_template['name'] ); ?> value="9"><?php _e( 'Red v3', 'yop_poll' ) ?></option>
																<option <?php selected( 'Green v1', $current_template['name'] ); ?> value="10"><?php _e( 'Green v1', 'yop_poll' ) ?></option>
																<option <?php selected( 'Green v2', $current_template['name'] ); ?> value="11"><?php _e( 'Green v2', 'yop_poll' ) ?></option>
																<option <?php selected( 'Green v3', $current_template['name'] ); ?> value="12"><?php _e( 'Green v3', 'yop_poll' ) ?></option>
																<option <?php selected( 'Orange v1', $current_template['name'] ); ?> value="13"><?php _e( 'Orange v1', 'yop_poll' ) ?></option>
																<option <?php selected( 'Orange v2', $current_template['name'] ); ?> value="14"><?php _e( 'Orange v2', 'yop_poll' ) ?></option>
																<option <?php selected( 'Orange v3', $current_template['name'] ); ?> value="15"><?php _e( 'Orange v3', 'yop_poll' ) ?></option>
															</select>
														</div>
													</div>
												</div>
												<div class="clear"></div>
											</div>
											<div id="submitlink" class="submitbox">
												<div id="major-publishing-actions">
													<div id="publishing-action">
														<input type="button" accesskey="r" class="button-primary"
															value="<?php _e( 'Reset Poll Template', 'yop_poll' ); ?>"
															id="yop-poll-edit-add-new-template-form-reset" onclick="if (confirm('<?php _e( 'Are You Sure You Want To Reset This Template?' ); ?>')) { yop_poll_reset_template() }"/>
													</div>
													<div class="clear"></div>
												</div>
												<div class="clear"></div>
											</div>
										</div>
									</div>
									<?php
									}
								?>
							</div>
						</div>
					</div>
					<br class="clear">
				</div>
			</form>
		</div>
		<?php
		}
		/**
		* End Views section
		*/

		/**
		* Start Ajax section
		*/
		function ajax_edit_add_new_poll() {
			if ( is_admin() ){
				global $wpdb, $current_user;
				check_ajax_referer( 'yop-poll-edit-add-new' );

				require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
				$yop_poll_model = new Yop_Poll_Model ();
				if ( 'add-new' == $_REQUEST ['action_type'] ){
					if ( ( !$this->current_user_can( 'edit_own_polls' ) ) && ( !$this->current_user_can( 'edit_polls' ) ) )
						wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
					else {
						$yop_poll_id = $yop_poll_model->add_poll_to_database( $_REQUEST, $this->_config );
						if ( $yop_poll_id ){
							_e( 'Poll successfully added!', 'yop_poll' );
						}
						else {
							echo $yop_poll_model->error;
						}
					}
				}
				if ( 'edit' == $_REQUEST ['action_type'] ){
					if ( ctype_digit( $_REQUEST ['yop_poll_id'] ) ){
						$poll_details = Yop_Poll_Model::get_poll_from_database_by_id( $_REQUEST ['yop_poll_id'] );
						if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_details['poll_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) )
							wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
						else {
							$yop_poll_id = $yop_poll_model->edit_poll_in_database( $_REQUEST, $this->_config );
							if ( $yop_poll_id )
								_e( 'Poll successfully Edited!', 'yop_poll' );
							else
								echo $yop_poll_model->error;
						}
					}
					else
						_e( 'We\'re unable to update your poll!', 'yop_poll' );
				}
				unset ( $yop_poll_model );
			}
			die ();
		}

		function ajax_edit_add_new_poll_template() {
			if ( is_admin() ){
				global $wpdb, $current_user;
				check_ajax_referer( 'yop-poll-edit-add-new-template' );
				require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
				$yop_poll_model = new Yop_Poll_Model ();
				if ( 'add-new' == $_REQUEST ['action_type'] ){
					if ( ( !$this->current_user_can( 'edit_own_polls_templates' ) ) && ( !$this->current_user_can( 'edit_polls_templates' ) ) )
						wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
					else {
						$yop_poll_template_id = $yop_poll_model->add_poll_template_to_database( $_REQUEST, $this->_config );
						if ( $yop_poll_template_id ){
							_e( 'Poll template successfully added!', 'yop_poll' );
						}
						else {
							echo $yop_poll_model->error;
						}
					}
				}
				if ( 'edit' == $_REQUEST ['action_type'] ){
					if ( ctype_digit( $_REQUEST ['template_id'] ) ){
						$template_details = Yop_Poll_Model::get_poll_template_from_database_by_id( $_REQUEST ['template_id'] );
						if ( ( !$this->current_user_can( 'edit_own_polls_templates' ) || $template_details['template_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls_templates' ) ) )
							wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
						else {
							$yop_poll_template_id = $yop_poll_model->edit_poll_template_in_database( $_REQUEST, $this->_config );
							if ( $yop_poll_template_id ){
								_e( 'Poll Template successfully Edited!', 'yop_poll' );
							}
							else {
								echo $yop_poll_model->error;
							}
						}
					}
					else
						_e( 'We\'re unable to update your poll template!', 'yop_poll' );
				}
				unset ( $yop_poll_model );
			}
			die ();
		}

		function ajax_reset_poll_template() {
			if ( is_admin() ){
				global $wpdb, $current_user;
				check_ajax_referer( 'yop-poll-edit-add-new-template' );
				require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
				$yop_poll_model = new Yop_Poll_Model ();
				if ( 'edit' == $_REQUEST ['action_type'] ){
					if ( ctype_digit( $_REQUEST ['template_id'] ) ){
						$template_details = Yop_Poll_Model::get_poll_template_from_database_by_id( $_REQUEST ['template_id'] );
						if ( ( !$this->current_user_can( 'edit_own_polls_templates' ) || $template_details['template_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls_templates' ) ) )
							wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
						else {
							$yop_poll_template_id = $yop_poll_model->reset_poll_template( $_REQUEST, $this->_config );
							if ( $yop_poll_template_id ){
								_e( 'Poll Template Successfully Reseted!', 'yop_poll' );
							}
							else {
								echo $yop_poll_model->error;
							}
						}
					}
					else
						_e( 'We\'re unable to reset your poll template!', 'yop_poll' );
				}
				unset ( $yop_poll_model );
			}
			die ();
		}

		public function yop_poll_do_vote() {
			$error   = '';
			$success = '';
			$message = '';
			if ( is_admin() ){
				$poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : NULL;
				$unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : NULL;
				$location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : NULL;
				if ( $poll_id ){
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
					$yop_poll_model->set_unique_id( $unique_id );
					$poll_html = $yop_poll_model->register_vote( $_REQUEST );
					if ( $poll_html ){
						$message = $poll_html;
						$success = $yop_poll_model->success;
					}
					else {
						$error = $yop_poll_model->error;
					}
					unset ( $yop_poll_model );
				}
				else {
					$error = __( 'Invalid Request! Try later!', 'yop_poll' );
				}
			}
			print '[ajax-response]' . json_encode( array( 'error' => $error, 'success' => $success, 'message' => $message ) ) . '[/ajax-response]';
			die ();
		}

		public function yop_poll_view_results() {
			$error   = '';
			$success = '';
			$message = '';

			if ( is_admin() ){
				$poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : 0;
				$unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : '';
				$location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : 'page';
				$tr_id     = isset ( $_REQUEST ['tr_id'] ) ? $_REQUEST ['tr_id'] : '';
                if ( wp_verify_nonce( $_REQUEST['yop-poll-nonce-' . $poll_id . $unique_id], 'yop_poll-' . $poll_id . $unique_id . '-user-actions' ) ){
				if ( $poll_id ){
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
					$yop_poll_model->set_unique_id( $unique_id );
					$yop_poll_model->vote = true;
					$poll_html            = do_shortcode( $yop_poll_model->return_poll_html( array( 'tr_id' => $tr_id, 'location' => $location ) ) );
					if ( $poll_html ){
						$message = $poll_html;
						$success = $yop_poll_model->success;
					}
					else {
						$error = $yop_poll_model->error;
					}
					unset ( $yop_poll_model );
				}
				else {
					$error = __( 'Invalid Request! Try later!', 'yop_poll' );
				}
			}

                            else {
                                    $error = __( 'Bad Request! Try later!', 'yop_poll' );
                                }

            }

			print '[ajax-response]' . json_encode( array( 'error' => $error, 'success' => $success, 'message' => $message ) ) . '[/ajax-response]';
			die ();
		}

		public function yop_poll_back_to_vote() {
			$error   = '';
			$success = '';
			$message = '';
			if ( is_admin() ){
				$poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : 0;
				$unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : '';
				$location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : 'page';
				$tr_id     = isset ( $_REQUEST ['tr_id'] ) ? $_REQUEST ['tr_id'] : '';
				if ( $poll_id ){
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
					$yop_poll_model->set_unique_id( $unique_id );
					$poll_html = do_shortcode( $yop_poll_model->return_poll_html( array( 'tr_id' => $tr_id, 'location' => $location ) ) );
					if ( $poll_html ){
						$message = $poll_html;
						$success = $yop_poll_model->success;
					}
					else {
						$error = $yop_poll_model->error;
					}
					unset ( $yop_poll_model );
				}
				else {
					$error = __( 'Invalid Request! Try later!', 'yop_poll' );
				}
			}
			print '[ajax-response]' . json_encode( array( 'error' => $error, 'success' => $success, 'message' => $message ) ) . '[/ajax-response]';
			die ();
		}

		public function yop_poll_load_css() {
			header( 'Content-Type: text/css' );
			// check_ajax_referer('yop-poll-public-css');
			if ( is_admin() ){
				$poll_id   = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : NULL;
				$location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : NULL;
				$unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : NULL;
				if ( $poll_id ){
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
					$yop_poll_model->set_unique_id( $unique_id );
					$poll_css = $yop_poll_model->return_poll_css( array( 'location' => $location ) );
					print $poll_css;
					unset ( $yop_poll_model );
				}
			}
			die ();
		}

		public function yop_poll_load_js() {
			header( 'Content-Type: text/javascript' );
			// check_ajax_referer('yop-poll-public-js');
			if ( is_admin() ){
				$poll_id   = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : NULL;
				$location  = isset ( $_REQUEST ['location'] ) ? $_REQUEST ['location'] : NULL;
				$unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : NULL;
				if ( $poll_id ){
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
					$yop_poll_model->set_unique_id( $unique_id );
					$poll_js = $yop_poll_model->return_poll_js( array( 'location' => $location ) );
					print $poll_js;
					unset ( $yop_poll_model );
				}
			}
			die ();
		}

		public function ajax_get_polls_for_editor() {
			check_ajax_referer( 'yop-poll-editor' );
			if ( is_admin() ){
				require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
				$yop_polls = Yop_Poll_Model::get_yop_polls_filter_search( 'id', 'asc' );
			?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<title><?php _e( 'Insert Poll', 'yop_poll' ); ?></title>
					<script type="text/javascript"
						src="<?php echo get_option( 'siteurl' ) ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
					<script type="text/javascript">
						function insertYopPollTinyMce( poll_id, tr_id ) {
							tr_id = typeof tr_id !== 'undefined' ? tr_id : '';
							if ( isNaN( poll_id ) ) {
								alert( '<?php _e( 'Error: Invalid Yop Poll!\n\nPlease choose the poll again:\n\n', 'yop_poll' ) ?>' );
							}
							else {
								if ( poll_id != null && poll_id != '' ) {
									if ( tr_id != '' ) {
										tinyMCEPopup.editor.execCommand( 'mceInsertContent', false, '[yop_poll id="' + poll_id + '" tr_id="' + tr_id + '"]' );
									}
									else {
										tinyMCEPopup.editor.execCommand( 'mceInsertContent', false, '[yop_poll id="' + poll_id + '"]' );
									}
								}
								else {
									tinyMCEPopup.editor.execCommand( 'mceInsertContent', false, '[yop_poll]' );
								}
								tinyMCEPopup.close();
							}
						}
					</script>
				</head>
				<body>
					<p>
						<label for="yop-poll-id-dialog"> <span><?php _e( 'Poll to Display', 'yop_poll' ); ?>:</span>
							<select class="widefat" name="yop_poll_id" id="yop-poll-id-dialog">
								<option value="-3"><?php _e( 'Display Random Poll', 'yop_poll' ); ?></option>
								<option value="-2"><?php _e( 'Display Latest Poll', 'yop_poll' ); ?></option>
								<option value="-1"><?php _e( 'Display Current Active Poll', 'yop_poll' ); ?></option>
								<?php
									if ( count( $yop_polls ) > 0 ){
										foreach ( $yop_polls as $yop_poll ) {
										?>
										<option value="<?php echo $yop_poll['id']; ?>"><?php echo esc_html( stripslashes( $yop_poll['name'] ) ); ?></option>
										<?php
										}
									}
								?>
							</select>
						</label>
						<br/>
						<label for="yop-poll-tr-id-dialog"> <span><?php _e( 'Tracking ID', 'yop_poll' ); ?>:</span><br>
							<input class="widefat" name="yop_poll_tr_id" id="yop-poll-tr-id-dialog"/>
						</label>


						<center> <input type="button" class="button-primary"
								value="<?php _e( 'Insert Poll', 'yop_poll' ); ?>"
								onclick=" insertYopPollTinyMce( document.getElementById('yop-poll-id-dialog').value, document.getElementById('yop-poll-tr-id-dialog').value );"/></center>
						<br/>
						<center> <input type="button" class="button-primary"
								value="<?php _e( 'Close', 'yop_poll' ); ?>"
								onclick="tinyMCEPopup.close();"/></center>
					</p>
				</body>
			</html>
			<?php
			}
			die ();
		}

		public function ajax_preview_template() {
			if ( true /*check_ajax_referer( 'yop-poll-edit-add-new' )*/ ){
				if ( is_admin() ){
					$template_id = trim( $_POST['template_id'] );
					$loc         = trim( $_POST['loc'] );
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new Yop_Poll_Model();
					$template       = $yop_poll_model->return_template_preview_html( $template_id, $loc );
					print $template;
					unset( $yop_poll_model );
				}
			}
			die();
		}

		public function ajax_get_polls_for_html_editor() {
			check_ajax_referer( 'yop-poll-html-editor' );
			if ( is_admin() ){
				require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
				$yop_polls = Yop_Poll_Model::get_yop_polls_filter_search( 'id', 'asc' );
			?>
			<p style="text-align: center;">
				<label for="yop-poll-id-html-dialog"> <span><?php _e( 'Poll to Display', 'yop_poll' ); ?>:</span>
					<select class="widefat" name="yop_poll_id"
						id="yop-poll-id-html-dialog">
						<option value="-3"><?php _e( 'Display Random Poll', 'yop_poll' ); ?></option>
						<option value="-2"><?php _e( 'Display Latest Poll', 'yop_poll' ); ?></option>
						<option value="-1"><?php _e( 'Display Current Active Poll', 'yop_poll' ); ?></option>
						<?php
							if ( count( $yop_polls ) > 0 ){
								foreach ( $yop_polls as $yop_poll ) {
								?>
								<option value="<?php echo $yop_poll['id']; ?>"><?php echo esc_html( stripslashes( $yop_poll['name'] ) ); ?></option>
								<?php
								}
							}
						?>
					</select>
				</label>
				<br/><br/>
				<label for="yop-poll-tr-id-html-dialog"> <span><?php _e( 'Tracking ID', 'yop_poll' ); ?>:</span>
					<input type="text" name="yop_poll_tr_id" id="yop-poll-tr-id-html-dialog" class="widefat" value=""/>
				</label>

				<br/> <br/> <input type="button" class=""
					value="<?php _e( 'Insert Poll', 'yop_poll' ); ?>"
					onclick=" insertYopPoll( edCanvas, document.getElementById('yop-poll-id-html-dialog').value, document.getElementById('yop-poll-tr-id-html-dialog').value );"/>
				<br/> <br/> <input type="button" class=""
					value="<?php _e( 'Close', 'yop_poll' ); ?>" onclick="tb_remove();"/>
			</p>

			<?php
			}
			die ();
		}

		public function ajax_show_captcha() {
			if ( is_admin() ){
				$poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : NULL;
				$unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : NULL;
				if ( $poll_id ){
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
					$yop_poll_model->set_unique_id( $unique_id );
					$poll_options = $yop_poll_model->poll_options;
					if ( 'yes' == $poll_options ['use_captcha'] ){
						require_once( $this->_config->plugin_inc_dir . '/securimage.php' );
						$img               = new Yop_Poll_Securimage ();
						$img->ttf_file     = $this->_config->plugin_path . 'captcha/AHGBold.ttf';
						$img->namespace    = 'yop_poll_' . $poll_id . $unique_id;
						$img->image_height = 60;
						$img->image_width  = intval( $img->image_height * M_E );
						$img->text_color   = new Yop_Poll_Securimage_Color ( rand( 0, 255 ), rand( 0, 255 ), rand( 0, 255 ) );
						$img->show();
					}
				}
				else
					wp_die( 'Invalid Poll' );
			}
			else
				wp_die( 'captcha error' );
			die ();
		}

		public function ajax_play_captcha() {
			if ( is_admin() ){
				$poll_id   = isset ( $_REQUEST ['poll_id'] ) ? $_REQUEST ['poll_id'] : NULL;
				$unique_id = isset ( $_REQUEST ['unique_id'] ) ? $_REQUEST ['unique_id'] : NULL;
				if ( $poll_id ){
					require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
					$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );
					$poll_options   = $yop_poll_model->poll_options;
					if ( 'yes' == $poll_options ['use_captcha'] ){
						require_once( $this->_config->plugin_inc_dir . '/securimage.php' );
						$img                   = new Yop_Poll_Securimage ();
						$img->audio_path       = $this->_config->plugin_path . 'captcha/audio/';
						$img->audio_noise_path = $this->_config->plugin_path . 'captcha/audio/noise/';
						$img->namespace        = 'yop_poll_' . $poll_id . $unique_id;

						$img->outputAudioFile();
					}
				}
				else
					wp_die( 'Invalid Poll' );
			}
			else
				wp_die( 'captcha error' );
			die ();
		}

		public function ajax_show_optin_box_modal() {
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$optin_box_modal_options                    = get_option( 'yop_poll_optin_box_modal_options' );
			$optin_box_modal_options ['show']           = 'no'; //restore to no
			$optin_box_modal_options ['last_show_date'] = Yop_Poll_Model::get_mysql_curent_date();
			update_option( 'yop_poll_optin_box_modal_options', $optin_box_modal_options );
		?>
		<?php
			$this->yop_poll_optin_form1();
		?>
		<?php
			die ();
		}

		public function ajax_modal_option_signup() {
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$optin_box_modal_options                      = get_option( 'yop_poll_optin_box_modal_options' );
			$optin_box_modal_options ['modal_had_submit'] = 'yes';
			update_option( 'yop_poll_optin_box_modal_options', $optin_box_modal_options );
			die ();
		}

		public function ajax_sidebar_option_signup() {
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$optin_box_modal_options                        = get_option( 'yop_poll_optin_box_modal_options' );
			$optin_box_modal_options ['sidebar_had_submit'] = 'yes';
			update_option( 'yop_poll_optin_box_modal_options', $optin_box_modal_options );
			die ();
		}

		public function ajax_is_wordpress_user() {
			global $current_user;
			if ( $current_user->ID > 0 )
				print '[response]true[/response]';
			else
				print '[response]false[/response]';
			die();
		}

		public static function base64_decode( $str ) {
			$str = str_replace( '-', '/', $str );
			$str = str_replace( '_', '+', $str );
			return base64_decode( $str );
		}

		public function ajax_set_wordpress_vote() {

			$poll_id   = self::base64_decode( $_GET['poll_id'] );
			$unique_id = self::base64_decode( $_GET['unique_id'] );
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$yop_poll_model = new YOP_POLL_MODEL ( $poll_id );

			$answers          = YOP_POLL_MODEL::get_poll_answers( $poll_id, array( 'default', 'other' ) );
			$yop_poll_answers = array();
			if ( count( $answers ) > 0 ){
				foreach ( $answers as $answer ) {
					$yop_poll_answers[] = array( 'id' => $answer['id'], 'value' => html_entity_decode( (string)$answer['answer'], ENT_QUOTES, 'UTF-8' ), 'type' => $answer['type'] );
				}
			}

			$public_config = array( 'poll_options' => array( 'share_after_vote' => $yop_poll_model->poll_options['share_after_vote'], 'share_name' => html_entity_decode( (string)$yop_poll_model->poll_options['share_name'], ENT_QUOTES, 'UTF-8' ), 'share_caption' => html_entity_decode( (string)$yop_poll_model->poll_options['share_caption'], ENT_QUOTES, 'UTF-8' ), 'share_description' => html_entity_decode( (string)$yop_poll_model->poll_options['share_description'], ENT_QUOTES, 'UTF-8' ), 'share_picture' => html_entity_decode( (string)$yop_poll_model->poll_options['share_picture'], ENT_QUOTES, 'UTF-8' ), 'share_question' => html_entity_decode( (string)$yop_poll_model->poll['question'], ENT_QUOTES, 'UTF-8' ), 'share_poll_name' => html_entity_decode( (string)$yop_poll_model->poll['name'], ENT_QUOTES, 'UTF-8' ), 'share_link' => $yop_poll_model->poll_options['poll_page_url'] == '' ? site_url() : $yop_poll_model->poll_options['poll_page_url'], 'answers' => $yop_poll_answers, 'redirect_after_vote' => html_entity_decode( (string)$yop_poll_model->poll['redirect_after_vote'], ENT_QUOTES, 'UTF-8' ), 'redirect_after_vote_url' => html_entity_decode( (string)$yop_poll_model->poll['redirect_after_vote_url'], ENT_QUOTES, 'UTF-8' ),

				) );

		?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<script type="text/javascript">

					function close_window() {
						var yop_poll_various_config = new Object();
						yop_poll_various_config.poll_id = '<?php echo self::base64_decode( $_GET['poll_id'] ) ?>';
						yop_poll_various_config.unique_id = '<?php echo self::base64_decode( $_GET['unique_id'] ) ?>';
						yop_poll_various_config.poll_location = '<?php echo self::base64_decode( $_GET['poll_location'] ) ?>';
						yop_poll_various_config.is_modal = <?php echo self::base64_decode( $_GET['is_modal'] ) == 'true' ? 'true' : 'false'; ?>;
						yop_poll_various_config.vote_loading_image_target = '<?php echo self::base64_decode( $_GET['vote_loading_image_target'] ) ?>';
						yop_poll_various_config.vote_loading_image_id = '<?php echo self::base64_decode( $_GET['vote_loading_image_id'] ) ?>';
						yop_poll_various_config.vote_type = '<?php echo self::base64_decode( $_GET['vote_type'] ) ?>';
						yop_poll_various_config.facebook_user_details = '<?php echo isset( $_GET['facebook_user_details'] ) ? $_GET['facebook_user_details'] : '' ?>';
						yop_poll_various_config.facebook_error = '<?php echo isset( $_GET['facebook_error'] ) ? $_GET['facebook_error'] : '' ?>';
						yop_poll_various_config.public_config =  <?php echo json_encode( $public_config ); ?>;
						window.opener.jQuery( '#yop-poll-nonce-' + yop_poll_various_config.poll_id + yop_poll_various_config.unique_id ).val( '<?php echo wp_create_nonce( 'yop_poll-'.$poll_id.$unique_id.'-user-actions' ) ?>' );
						result = window.opener.yop_poll_do_vote( yop_poll_various_config );
						if ( result ) {
							window.close();
						}
					}
				</script>
			</head>
			<body onload="close_window()">
				<div style="margin:auto; width: 100px; height: 100px; text-align: center;"><img src="<?php echo $this->_config->plugin_url ?>/images/loading100x100.gif" alt="<?php _e( 'Loading', 'yop_poll' ) ?>"/><br>
				</div>
			</body>
		</html>
		<?php
			die();
		}

		public function ajax_do_change_votes_number_answer() {
			global $current_user;
			$answer_id         = intval( $_POST['yop_poll_answer_id'] );
			$votes_number      = intval( $_POST['yop_poll_answer_votes'] );
			$change_to_all     = $_POST['yop_poll_change_to_all_poll_answers'];
			$according_to_logs = $_POST['yop_poll_update_answers_with_logs'];
			$response          = NULL;

			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$answer_details = YOP_POLL_MODEL::get_poll_answer_by_id( $answer_id );

			$yop_poll_model = new Yop_Poll_Model ( $answer_details['poll_id'] );
			$poll_details   = $yop_poll_model->get_current_poll();

			if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_details ['poll_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) ){
				$response = __( 'You are not allowed to edit this item.', 'yop_poll' );
			}
			else {
				if ( !wp_verify_nonce( $_POST['yop-poll-nonce-change-votes-number-answer-' . $answer_id], 'yop_poll-change-votes-number-answer-action-' . $answer_id ) ){
					$response = __( 'Bad Request!', 'yop_poll' );
				}
				else {
					if ( 'yes' == $according_to_logs ){
						if ( 'yes' == $change_to_all ){
							$poll_answers = YOP_POLL_MODEL::get_poll_answers( $answer_details['poll_id'], array( 'default', 'other' ) );
							if ( count( $poll_answers ) > 0 )
								foreach ( $poll_answers as $answer ) {
									Yop_Poll_Model::update_answer_field( $answer['id'], array( 'name' => 'votes', 'value' => YOP_POLL_MODEL::get_answer_votes_from_logs( $answer['id'] ), 'type' => '%d' ) );
							}
						}
						else
							Yop_Poll_Model::update_answer_field( $answer_id, array( 'name' => 'votes', 'value' => YOP_POLL_MODEL::get_answer_votes_from_logs( $answer_id ), 'type' => '%d' ) );
						$response = __( 'Success', 'yop_poll' );
					}
					else {
						if ( intval( $votes_number ) < 0 )
							$response = __( 'Invalid Number Of Votes', 'yop_poll' ) . '!';
						else {
							if ( 'yes' == $change_to_all )
								Yop_Poll_Model::update_all_poll_answers_field( $answer_details['poll_id'], array( 'name' => 'votes', 'value' => $votes_number, 'type' => '%d' ) );
							else
								Yop_Poll_Model::update_answer_field( $answer_id, array( 'name' => 'votes', 'value' => $votes_number, 'type' => '%d' ) );
							$response = __( 'Success', 'yop_poll' );
						}
					}
				}
			}
			print '[response]' . $response . '[/response]';
			die();
		}

		public function ajax_do_change_total_number_poll() {
			global $current_user;
			$poll_id              = intval( $_POST['yop_poll_id'] );
			$total_votes          = intval( $_POST['yop_poll_total_votes'] );
			$total_answers        = intval( $_POST['yop_poll_total_answers'] );
			$type                 = $_POST['yop_poll_type'];
			$change_to_all        = $_POST['yop_poll_change_to_all'];
			$according_to_logs    = $_POST['yop_poll_update_poll_with_logs'];
			$according_to_answers = $_POST['yop_poll_update_poll_with_answers'];
			$response             = NULL;

			$yop_poll_model = new Yop_Poll_Model ( $poll_id );
			$poll_details   = $yop_poll_model->get_current_poll();

			if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_details ['poll_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) ){
				$response = __( 'You are not allowed to edit this item.', 'yop_poll' );
			}
			else {
				if ( !wp_verify_nonce( $_POST['yop-poll-nonce-change-total-number-poll-' . $poll_id], 'yop_poll-change-total-number-poll-action-' . $poll_id ) ){
					$response = __( 'Bad Request!', 'yop_poll' );
				}
				else {
					if ( 'yes' == $according_to_logs ){
						if ( 'votes' == $type ){
							if ( 'yes' == $change_to_all ){
								$all_polls = YOP_POLL_MODEL::get_yop_polls_filter_search();
								if ( count( $all_polls ) > 0 ){
									foreach ( $all_polls as $poll )
										Yop_Poll_Model::update_poll_field( $poll['id'], array( 'name' => 'total_votes', 'value' => YOP_POLL_MODEL::get_poll_total_votes_from_logs( $poll['id'] ), 'type' => '%d' ) );
								}
							}
							else {
								Yop_Poll_Model::update_poll_field( $poll_id, array( 'name' => 'total_votes', 'value' => YOP_POLL_MODEL::get_poll_total_votes_from_logs( $poll_id ), 'type' => '%d' ) );
							}
						}
						if ( 'answers' == $type ){
							if ( 'yes' == $change_to_all ){
								$all_polls = YOP_POLL_MODEL::get_yop_polls_filter_search();
								if ( count( $all_polls ) > 0 ){
									foreach ( $all_polls as $poll )
										Yop_Poll_Model::update_poll_field( $poll['id'], array( 'name' => 'total_answers', 'value' => YOP_POLL_MODEL::get_poll_total_answers_from_logs( $poll['id'] ), 'type' => '%d' ) );
								}
							}
							else {
								Yop_Poll_Model::update_poll_field( $poll_id, array( 'name' => 'total_answers', 'value' => YOP_POLL_MODEL::get_poll_total_answers_from_logs( $poll_id ), 'type' => '%d' ) );
							}
						}
						$response = __( 'Success', 'yop_poll' );
					}
					elseif ( 'yes' == $according_to_answers ) {
						if ( 'votes' == $type ){
							if ( 'yes' == $change_to_all ){
								$all_polls = YOP_POLL_MODEL::get_yop_polls_filter_search();
								if ( count( $all_polls ) > 0 ){
									foreach ( $all_polls as $poll )
										Yop_Poll_Model::update_poll_field( $poll['id'], array( 'name' => 'total_votes', 'value' => YOP_POLL_MODEL::get_poll_total_votes_from_answers( $poll['id'] ), 'type' => '%d' ) );
								}
							}
							else {
								Yop_Poll_Model::update_poll_field( $poll_id, array( 'name' => 'total_votes', 'value' => YOP_POLL_MODEL::get_poll_total_votes_from_answers( $poll_id ), 'type' => '%d' ) );
							}
						}
						$response = __( 'Success', 'yop_poll' );
					}
					else {
						if ( intval( $total_votes ) < 0 && $type == 'votes' )
							$response = __( 'Invalid Number Of Total Votes', 'yop_poll' ) . '!';
						if ( intval( $total_answers ) < 0 && $type == 'answers' )
							$response = __( 'Invalid Number Of Total Answers', 'yop_poll' ) . '!';
						else {
							if ( 'votes' == $type ){
								if ( 'yes' == $change_to_all )
									Yop_Poll_Model::update_all_polls_field( array( 'name' => 'total_votes', 'value' => $total_votes, 'type' => '%d' ) );
								else
									Yop_Poll_Model::update_poll_field( $poll_id, array( 'name' => 'total_votes', 'value' => $total_votes, 'type' => '%d' ) );
							}
							if ( 'answers' == $type ){
								if ( 'yes' == $change_to_all )
									Yop_Poll_Model::update_all_polls_field( array( 'name' => 'total_answers', 'value' => $total_answers, 'type' => '%d' ) );
								else
									Yop_Poll_Model::update_poll_field( $poll_id, array( 'name' => 'total_answers', 'value' => $total_answers, 'type' => '%d' ) );
							}
							$response = __( 'Success', 'yop_poll' );
						}
					}
				}
			}
			print '[response]' . $response . '[/response]';
			die();
		}

		public function ajax_do_change_poll_author() {
			global $current_user;
			$poll_id     = intval( $_POST['yop_poll_id'] );
			$poll_author = intval( $_POST['yop_poll_author'] );
			$response    = NULL;

			$yop_poll_model = new Yop_Poll_Model ( $poll_id );
			$poll_details   = $yop_poll_model->get_current_poll();

			if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_details ['poll_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) ){
				$response = __( 'You are not allowed to edit this item.', 'yop_poll' );
			}
			else {
				if ( !wp_verify_nonce( $_POST['yop-poll-nonce-change-poll-author-' . $poll_id], 'yop_poll-change-poll-author-action-' . $poll_id ) ){
					$response = __( 'Bad Request!', 'yop_poll' );
				}
				else {
					if ( intval( $poll_author ) <= 0 )
						$response = __( 'Invalid Poll Author', 'yop_poll' ) . '!';
					else {
						Yop_Poll_Model::update_poll_field( $poll_id, array( 'name' => 'poll_author', 'value' => $poll_author, 'type' => '%d' ) );
						$response = __( 'Success', 'yop_poll' );
					}
				}
			}
			print '[response]' . $response . '[/response]';
			die();
		}

		public function ajax_do_change_template_author() {
			global $current_user;
			$template_id     = intval( $_POST['yop_poll_template_id'] );
			$template_author = intval( $_POST['yop_poll_template_author'] );
			$response        = NULL;

			$template_details = Yop_Poll_Model::get_poll_template_from_database_by_id( $template_id );

			if ( ( !$this->current_user_can( 'edit_own_polls_templates' ) || $template_details ['template_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls_templates' ) ) ){
				$response = __( 'You are not allowed to edit this item.', 'yop_poll' );
			}
			else {
				if ( !wp_verify_nonce( $_POST['yop-poll-nonce-change-poll-template-author-' . $template_id], 'yop_poll-change-poll-template-author-action-' . $template_id ) ){
					$response = __( 'Bad Request!', 'yop_poll' );
				}
				else {
					if ( intval( $template_author ) <= 0 )
						$response = __( 'Invalid Template Author', 'yop_poll' ) . '!';
					else {
						Yop_Poll_Model::update_template_field( $template_id, array( 'name' => 'template_author', 'value' => $template_author, 'type' => '%d' ) );
						$response = __( 'Success', 'yop_poll' );
					}
				}
			}
			print '[response]' . $response . '[/response]';
			die();
		}

		public function ajax_show_change_votes_number_answer() {
			global $current_user;
			$answer_id = intval( $_GET['answer_id'] );

			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$answer_details = YOP_POLL_MODEL::get_poll_answer_by_id( $answer_id );
			$yop_poll_model = new Yop_Poll_Model ( $answer_details['poll_id'] );
			$poll_details   = $yop_poll_model->get_current_poll();
			if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_details ['poll_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) )
				wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
		?>
		<div id="yop-poll-change-votes">
			<form id="yop-poll-change-answer-no-votes-form">
				<table class="links-table" cellspacing="0">
					<tbody>
						<tr><td colspan="2" align="center"><b><?php echo $answer_details['answer']; ?></b></td></tr>
						<tr><td colspan="2" align="center" id="yop-poll-change-no-votes-error" class="error-message"></td></tr>
						<tr id="yop-poll-manual-change-no-votes">
							<td>
								<label class="yop_poll_answer_no_votes_label" for="yop-poll-answer-no-votes"><?php _e( 'New Number Of Votes', 'yop_poll' ); ?>
									:</label></td>
							<td>
								<input id="yop-poll-answer-no-votes" type="text" value="<?php echo $answer_details['votes']; ?>" name="yop_poll_answer_votes"/>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="checkbox" name="yop_poll_update_answers_with_logs" value="yes" id="yop-poll-update-answers-with-logs" onclick="if ( jQuery(this).prop('checked') ) jQuery( '#yop-poll-manual-change-no-votes').hide(); else  jQuery( '#yop-poll-manual-change-no-votes').show();"/>
								<label for="yop-poll-update-answers-with-logs"><?php _e( 'Update According To Logs', 'yop_poll' ); ?></label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="checkbox" name="yop_poll_change_to_all_poll_answers" value="yes" id="yop-poll-change-to-all-poll-answers" onclick="if ( jQuery(this).prop('checked') )return confirm('<?php _e( 'Are You Sure You Want To Change To All Poll Answers?', 'yop_poll' ); ?>');"/>
								<label for="yop-poll-change-to-all-poll-answers"><?php _e( 'Change To All Poll Answers', 'yop_poll' ); ?></label>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="button" class="button-primary" value="<?php _e( 'Save', 'yop_poll' ) ?>" onclick="yop_poll_do_change_votes_number_answer( '<?php echo $answer_details['id']; ?>' )"/>
								<input type="hidden" name="yop_poll_answer_id" value="<?php echo $answer_details['id']; ?>"/>
							</td>
						</tr>
					</tbody>
				</table>
				<?php wp_nonce_field( 'yop_poll-change-votes-number-answer-action-' . $answer_id, 'yop-poll-nonce-change-votes-number-answer-' . $answer_id, false ); ?>
			</form>
		</div>
		<?php
			die();
		}

		public function ajax_show_change_total_number_poll() {
			global $current_user;
			$poll_id = intval( $_GET['poll_id'] );
			$type    = $_GET['type'];

			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$yop_poll_model = new Yop_Poll_Model ( $poll_id );
			$poll_details   = $yop_poll_model->get_current_poll();
			if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_details ['poll_author'] != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) )
				wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
		?>
		<div id="yop-poll-change-total">
			<form id="yop-poll-change-poll-total-no-form">
				<table class="links-table" cellspacing="0">
					<tbody>
						<tr><td colspan="2" align="center"><b><?php echo $poll_details['name']; ?></b></td></tr>
						<tr><td colspan="2" align="center" id="yop-poll-change-total-no-error" class="error-message"></td></tr>
						<?php if ( 'votes' == $type ){ ?>
							<tr id="yop-poll-manual-change-no-votes">
								<td>
									<label class="yop_poll_total_votes_label" for="yop-poll-total-votes"><?php _e( 'New Number Of Poll Total Votes', 'yop_poll' ); ?>
										:</label></td>
								<td>
									<input id="yop-poll-total-votes" type="text" value="<?php echo $poll_details['total_votes']; ?>" name="yop_poll_total_votes"/>
								</td>
							</tr>
							<?php } ?>
						<?php if ( 'answers' == $type ){ ?>
							<tr id="yop-poll-manual-change-no-votes">
								<td>
									<label class="yop_poll_total_answers_label" for="yop-poll-total-answers"><?php _e( 'New Number Of Poll Total Answers', 'yop_poll' ); ?>
										:</label></td>
								<td>
									<input id="yop-poll-total-answers" type="text" value="<?php echo $poll_details['total_answers']; ?>" name="yop_poll_total_answers"/>
								</td>
							</tr>
							<?php } ?>
						<tr>
							<td colspan="2">
								<input type="checkbox" name="yop_poll_update_poll_with_logs" value="yes" id="yop-poll-update-poll-with-logs" onclick="if ( jQuery(this).prop('checked') ) { jQuery( '#yop-poll-manual-change-no-votes').hide(); jQuery('#yop-poll-update-poll-with-answers').attr('checked', false)} else { if ( ! jQuery('#yop-poll-update-poll-with-answers').prop('checked') ) jQuery( '#yop-poll-manual-change-no-votes').show();} "/>
								<label for="yop-poll-update-poll-with-logs"><?php _e( 'Update According To Logs', 'yop_poll' ); ?></label>
							</td>
						</tr>
						<?php if ( 'votes' == $type ){ ?>
							<tr>
								<td colspan="2">
									<input type="checkbox" name="yop_poll_update_poll_with_answers" value="yes" id="yop-poll-update-poll-with-answers" onclick="if ( jQuery(this).prop('checked') ) {jQuery( '#yop-poll-manual-change-no-votes').hide(); jQuery('#yop-poll-update-poll-with-logs').attr('checked', false ); } else { if ( ! jQuery('#yop-poll-update-poll-with-logs').prop('checked') ) jQuery( '#yop-poll-manual-change-no-votes').show(); }"/>
									<label for="yop-poll-update-poll-with-answers"><?php _e( 'Update According To Answers', 'yop_poll' ); ?></label>
								</td>
							</tr>
							<?php } ?>
						<tr>
							<td colspan="2">
								<input type="checkbox" name="yop_poll_change_to_all" value="yes" id="yop-poll-change-to-all" onclick="if ( jQuery(this).prop('checked') )return confirm('<?php _e( 'Are You Sure You Want To Change To All Polls?', 'yop_poll' ); ?>');"/>
								<label for="yop-poll-change-to-all"><?php _e( 'Change To All Polls', 'yop_poll' ); ?></label>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="button" class="button-primary" value="<?php _e( 'Save', 'yop_poll' ) ?>" onclick="yop_poll_do_change_total_number_poll( '<?php echo $poll_details['id']; ?>', '<?php echo $type; ?>')"/>
								<input type="hidden" name="yop_poll_id" value="<?php echo $poll_details['id']; ?>"/>
								<input type="hidden" name="yop_poll_type" value="<?php echo $type; ?>"/>
							</td>
						</tr>
					</tbody>
				</table>
				<?php wp_nonce_field( 'yop_poll-change-total-number-poll-action-' . $poll_id, 'yop-poll-nonce-change-total-number-poll-' . $poll_id, false ); ?>
			</form>
		</div>
		<?php
			die();
		}

		public function ajax_show_change_poll_author() {
			global $current_user;
			$poll_id = intval( $_GET['poll_id'] );

			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$yop_poll_model = new Yop_Poll_Model ( $poll_id );
			$poll_details   = $yop_poll_model->get_current_poll();
			$poll_author    = get_user_by( 'id', $poll_details ['poll_author'] );
			if ( ( !$this->current_user_can( 'edit_own_polls' ) || $poll_author->ID != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls' ) ) )
				wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
		?>
		<div id="yop-poll-change-poll-author">
			<form id="yop-poll-change-poll-author-form">
				<table class="links-table" cellspacing="0">
					<tbody>
						<tr><td colspan="2" align="center"><b><?php echo $poll_details['name']; ?></b></td></tr>
						<tr><td colspan="2" align="center" id="yop-poll-change-poll-author-error" class="error-message"></td></tr>
						<tr id="yop-poll-manual-change-no-votes">
							<td>
								<label class="yop_poll_total_votes_label" for="yop-poll-author"><?php _e( 'New Poll Author', 'yop_poll' ); ?>
									:</label></td>
							<td>
								<?php
									$blogusers = get_users( 'blog_id=' . $GLOBALS['blog_id'] . '&orderby=nicename&order=ASC' );
								?>
								<select id="yop-poll-author-select" name="yop_poll_author">
									<?php
										foreach ( $blogusers as $user ) {
											echo '<option ' . selected( $poll_author->ID, $user->ID, false ) . ' value="' . $user->ID . '">' . $user->user_nicename . '</option>';
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="button" class="button-primary" value="<?php _e( 'Save', 'yop_poll' ) ?>" onclick="yop_poll_do_change_poll_author( '<?php echo $poll_details['id']; ?>')"/>
								<input type="hidden" name="yop_poll_id" value="<?php echo $poll_details['id']; ?>"/>
							</td>
						</tr>
					</tbody>
				</table>
				<?php wp_nonce_field( 'yop_poll-change-poll-author-action-' . $poll_id, 'yop-poll-nonce-change-poll-author-' . $poll_id, false ); ?>
			</form>
		</div>
		<?php
			die();
		}

		public function ajax_show_change_template_author() {
			global $current_user;
			$template_id = intval( $_GET['template_id'] );

			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$template_details = YOP_POLL_MODEL::get_poll_template_from_database_by_id( $template_id );
			$template_author  = get_user_by( 'id', $template_details ['template_author'] );
			if ( ( !$this->current_user_can( 'edit_own_polls_templates' ) || $template_author->ID != $current_user->ID ) && ( !$this->current_user_can( 'edit_polls_templates' ) ) )
				wp_die( __( 'You are not allowed to edit this item.', 'yop_poll' ) );
		?>
		<div id="yop-poll-change-template-author">
			<form id="yop-poll-change-template-author-form">
				<table class="links-table" cellspacing="0">
					<tbody>
						<tr><td colspan="2" align="center"><b><?php echo $template_details['name']; ?></b></td></tr>
						<tr><td colspan="2" align="center" id="yop-poll-change-template-author-error" class="error-message"></td></tr>
						<tr>
							<td>
								<label for="yop-template-author"><?php _e( 'New Template Author', 'yop_poll' ); ?>
									:</label></td>
							<td>
								<?php
									$blogusers = get_users( 'blog_id=' . $GLOBALS['blog_id'] . '&orderby=nicename&order=ASC' );
								?>
								<select id="yop-poll-template-author-select" name="yop_poll_template_author">
									<?php
										foreach ( $blogusers as $user ) {
											echo '<option ' . selected( $template_author->ID, $user->ID, false ) . ' value="' . $user->ID . '">' . $user->user_nicename . '</option>';
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="button" class="button-primary" value="<?php _e( 'Save', 'yop_poll' ) ?>" onclick="yop_poll_do_change_template_author( '<?php echo $template_details['id']; ?>')"/>
								<input type="hidden" name="yop_poll_template_id" value="<?php echo $template_details['id']; ?>"/>
							</td>
						</tr>
					</tbody>
				</table>
				<?php wp_nonce_field( 'yop_poll-change-poll-template-author-action-' . $template_id, 'yop-poll-nonce-change-poll-template-author-' . $template_id, false ); ?>
			</form>
		</div>
		<?php
			die();
		}
		/**
		* End Ajax section
		*/

		/* start tinymce */
		function load_editor_functions( $hook ) {
			global $post;

			if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'page-new.php' || $hook == 'page.php' ){
				$yop_poll_editor_config = array( 'dialog_url' => wp_nonce_url( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_editor', 'yop-poll-editor' ), 'dialog_html_url' => wp_nonce_url( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_html_editor', 'yop-poll-html-editor' ), 'name' => __( 'Yop Poll', 'yop_poll' ), 'title' => __( 'Insert Poll', 'yop_poll' ), 'prompt_insert_poll_id' => __( 'Please insert the poll ID:\n\n', 'yop_poll' ), 'prompt_insert_again_poll_id' => __( 'Error: Poll Id must be numeric!\n\nPlease insert the poll ID Again:\n\n', 'yop_poll' ) );
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'thickbox' );
				wp_enqueue_script( 'yop-poll-editor-functions', "{$this->_config->plugin_url}/tinymce/yop-poll-editor-functions.js", 'jquery', $this->_config->version, true );
				wp_localize_script( 'yop-poll-editor-functions', 'yop_poll_editor_config', $yop_poll_editor_config );
			}
		}

		function register_button( $buttons ) {
			array_push( $buttons, "separator", "yoppoll" );
			return $buttons;
		}

		function add_plugin( $plugin_array ) {
			$plugin_array ['yoppoll'] = "{$this->_config->plugin_url}/tinymce/yop-poll-editor.js";
			return $plugin_array;
		}

		function my_yop_poll_button( $hook ) {
			if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'page-new.php' || $hook == 'page.php' ){
				if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ){
					return;
				}

				if ( get_user_option( 'rich_editing' ) == 'true' ){
					add_filter( 'mce_external_plugins', array( &$this, 'add_plugin' ) );
					add_filter( 'mce_buttons', array( &$this, 'register_button' ) );
				}
			}
		}
		/**
		* end tinymce
		*/

		/*START donate*/
		private function yop_poll_donate() {
			$optin_box_modal_options = get_option( 'yop_poll_optin_box_modal_options' );
			wp_enqueue_style("yop-poll-donate-css", "{$this->_config->plugin_url}/css/yop-poll-donate.css");
		?>
		<div class="postbox-container side" id="postbox-container-1">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<div class="postbox " id="donate">
					<div title="Click to toggle" class="handlediv"><br/></div>
					<div id="toggle">
						<div id="toggle_title"><?php _e( 'Support This Plugin', 'yop_poll' ); ?></div>
					</div>
					<div class="inside">
						<p><b><?php _e( 'Have you found this plugin useful? Please help support it\'s continued development with a donation', 'yop_poll' ); ?>
								!</b></p>
						<a href="http://www.yop-poll.com/thankyou/don.php" target="_blank">
							<div id="donateButton"><span><?php _e( 'Donate', 'yop_poll' ); ?></span></div>
						</a>
						<br/>
						<b><?php _e( 'Short on funds?', 'yop_poll' ); ?></b><br/>
						<a target="_blank"
							href="http://wordpress.org/support/view/plugin-reviews/yop-poll?filter=5"><b><?php _e( 'Rate YOP POLL 5&#9733;\'s on WordPress.org', 'yop_poll' ); ?></b></a>
					</div>
				</div>

				<?php
					include_once( ABSPATH . WPINC . '/feed.php' );
					$feed = fetch_feed( 'http://yop-poll.com/feed/' );
					if ( !is_wp_error( $feed ) )
						$feeditems = $feed->get_items( 0, $feed->get_item_quantity( 5 ) );
				?>
				<?php
					if ( $feeditems ){
					?>
					<div class="postbox " id="linksubmitdiv2">
						<div title="Click to toggle" class="handlediv">
							<br/>
						</div>
						<h3 class="hndle">
							<span><?php _e( 'The Latest About Yop Poll', 'yop_poll' ); ?></span>
						</h3>
						<div class="inside">
							<ul id="news_ul">
								<?php
									foreach ( $feeditems as $item ) {
										$url = preg_replace( '/#.*/', '', esc_url( $item->get_permalink(), $protocolls = NULL, 'display' ) );
									?>
									<li><a href="<?php echo $url ?>" target="_blank"><b><?php echo esc_html( $item->get_title() ); ?></b></a></li>
									<?php
									}
								?>
							</ul>
						</div>
					</div>
					<?php
					}
				?>
				<?php
					if ( $optin_box_modal_options ['show'] == 'no' ){
						if ( $optin_box_modal_options ['sidebar_had_submit'] == 'no' ){
							if ( $optin_box_modal_options ['modal_had_submit'] == 'no' ){
							?>
							<?php $this->yop_poll_optin_form2(); ?>
							<?php
							}
						}
					}
				?>
			</div>
		</div>
		<?php
		}

		private function yop_poll_optin_form() {
		?>
		<style type="text/css">
			#WFItem394041 html {
				color: #000 !important;
				background: #FFF !important;
			}

			#WFItem394041 body, #WFItem394041 div, #WFItem394041 dl, #WFItem394041 dt, #WFItem394041 dd, #WFItem394041 ul, #WFItem394041 ol, #WFItem394041 li, #WFItem394041 h1, #WFItem394041 h2, #WFItem394041 h3, #WFItem394041 h4, #WFItem394041 h5, #WFItem394041 h6, #WFItem394041 pre, #WFItem394041 code, #WFItem394041 form, #WFItem394041 fieldset, #WFItem394041 legend, #WFItem394041 input, #WFItem394041 textarea, #WFItem394041 p, #WFItem394041 blockquote, #WFItem394041 th, #WFItem394041 td {
				margin-left: 0 !important;
				margin-right: 0 !important;
				margin-top: 0 !important;
				margin-bottom: 0 !important;
				padding-left: 0 !important;
				padding-bottom: 0 !important;
				padding-right: 0 !important;
				padding-top: 0 !important;
				width: auto !important;
				background-image: none !important;
			}

			#WFItem394041 div, #WFItem394041 dl, #WFItem394041 dt, #WFItem394041 dd, #WFItem394041 ul, #WFItem394041 ol, #WFItem394041 li, #WFItem394041 h1, #WFItem394041 h2 {
				border: none !important;
				background: none !important;
				float: none !important;
				width: auto !important;
				-moz-box-shadow: 0 !important;
				-webkit-box-shadow: 0 !important;
				box-shadow: 0 !important;
				text-indent: inherit !important;
			}

			#WFItem394041 table {
				border-collapse: collapse !important;
				border-spacing: 0 !important;
			}

			#WFItem394041 fieldset, #WFItem394041 img {
				border: 0 !important;
			}

			#WFItem394041 address, #WFItem394041 caption, #WFItem394041 cite, #WFItem394041 code, #WFItem394041 dfn, #WFItem394041 em, #WFItem394041 th, #WFItem394041 var {
				font-style: normal !important;
				font-weight: normal !important;
			}

			#WFItem394041 strong {
				font-style: normal !important;
				font-weight: bold !important;
			}

			#WFItem394041 em {
				font-style: italic !important;
				font-weight: normal !important;
				position: static !important;
			}

			#WFItem394041 li {
				list-style: none !important;
			}

			caption, #WFItem394041 th {
				text-align: left !important;
			}

			#WFItem394041 h1, #WFItem394041 h2, #WFItem394041 h3, #WFItem394041 h4, #WFItem394041 h5, #WFItem394041 h6 {
				font-size: 100% !important;
				font-weight: normal !important;
			}

			#WFItem394041 q:before, #WFItem394041 q:after {
				content: " " !important;
			}

			#WFItem394041 abbr, #WFItem394041 acronym {
				border: 0 !important;
				font-variant: normal !important;
			}

			#WFItem394041 sup {
				vertical-align: text-top !important;
			}

			#WFItem394041 sub {
				vertical-align: text-bottom !important;
			}

			#WFItem394041 input, #WFItem394041 textarea, #WFItem394041 select {
				font-family: inherit !important;
				font-size: inherit !important;
				font-weight: inherit !important;
				height: auto !important;
				line-height: auto !important;
			}

			#WFItem394041 input, #WFItem394041 textarea, #WFItem394041 select {
				*font-size: 100% !important;
			}

			#WFItem394041 legend {
				color: #000 !important;
			}

			#WFItem394041 .clearfix:after {
				visibility: hidden !important;
				display: block !important;
				font-size: 0 !important;
				content: " " !important;
				clear: both !important;
				height: 0 !important;
			}

			#WFItem394041 .clearer {
				display: block !important;
				clear: both !important;
				font-size: 0.1px !important;
				height: 0.1px !important;
				line-height: 0.1px !important;
				width: 100% !important;
			}

			#WFWrapper .close {
				background: url(https://app.getresponse.com/images/common/highslide/close.png) repeat scroll 0 0 transparent !important;
				cursor: pointer !important;
				height: 30px !important;
				position: absolute !important;
				right: -15px !important;
				top: -15px !important;
				width: 30px !important;
				z-index: 1 !important;
			}

			#wfCloseX {
				background: url(https://app.getresponse.com/images/common/highslide/close.png) repeat scroll 0 0 transparent !important;
				cursor: pointer !important;
				height: 30px !important;
				position: absolute !important;
				right: 0 !important;
				top: 0 !important;
				width: 30px !important;
				z-index: 1 !important;
				cursor: pointer !important;
			}

			#WFItem394041 .clrB {
				clear: both !important;
				text-align: left !important;
			}

			#WFItem394041 * html .clearfix {
				zoom: 1 !important;
			}

			#WFItem394041 *:first-child + html .clearfix {
				zoom: 1 !important;
			}

			#WFItem394041 .wf-contbox {
				width: 100% !important;
			}

			.wf-formTpl li:before {
				content: "" !important;
			}

			#WFItem394041 form {
				display: inline-block !important;
				width: 100% !important;
			}

			#WFItem394041 label {
				margin: 0 !important;
			}

			#WFItem394041 p {
				line-height: 18px !important;
				font-size: 11px !important;
			}

			#WFItem394041 input[type="submit"] {
				cursor: pointer !important;
				text-shadow: none !important;
				float: none !important;
				text-transform: none !important;
				letter-spacing: 0 !important;
				white-space: nowrap !important;
			}

			#WFItem394041 {
				color: #555555 !important;
				font-family: Arial, Helvetica, sans-serif !important;
				font-size: 12px !important;
			}

			#WFItem394041 .wf-body li {
				padding: 10px 15px !important;
				clear: both !important;
			}

			#WFItem394041 .actTinyMceElBodyContent ul li {
				padding: 3px !important;
				list-style-type: disc !important;
			}

			#WFItem394041 .actTinyMceElBodyContent ol li {
				padding: 3px !important;
				list-style-type: decimal !important;
			}

			#WFItem394041 .actTinyMceElBodyContent ul {
				margin-left: 20px !important;
			}

			#WFItem394041 .actTinyMceElBodyContent ol {
				margin-left: 25px !important;
			}

			#WFItem394041 .actTinyMceElBodyContent ol ul, #WFItem394041 .actTinyMceElBodyContent ul ol, #WFItem394041 .actTinyMceElBodyContent ul ul, #WFItem394041 .actTinyMceElBodyContent ol ol {
				margin-top: 0 !important;
				margin-bottom: 0 !important;
			}

			#WFItem394041 .wf-body {
				background-image: none !important;
				background-position: left top !important;
				background-repeat: no-repeat !important;
				border-color: #92757d !important;
				border-width: 0px !important;
				border-style: none !important;
				padding-left: 0 !important;
				padding-right: 0 !important;
				padding-top: 0 !important;
				padding-bottom: 0 !important;
				-webkit-border-top-left-radius: 0px !important;
				-moz-border-radius-topleft: 0px !important;
				border-top-left-radius: 0px !important;
				-webkit-border-top-right-radius: 0px !important;
				-moz-border-radius-topright: 0px !important;
				border-top-right-radius: 0px !important;
				-webkit-border-bottom-left-radius: 0px !important;
				-moz-border-radius-bottomleft: 0px !important;
				border-bottom-left-radius: 0px !important;
				-webkit-border-bottom-right-radius: 0px !important;
				-moz-border-radius-bottomright: 0px !important;
				border-bottom-right-radius: 0px !important;
			}

			#WFItem394041 .wf-header {
				color: #2B93D3 !important;
				background-color: #F5F5F5 !important;
				background-image: none !important;
				background-position: left top !important;
				background-repeat: no-repeat !important;
				border-color: #6b6262 !important;
				border-width: 0px !important;
				border-style: none !important;
				padding-left: 10px !important;
				padding-right: 10px !important;
				padding-top: 20px !important;
				padding-bottom: 0px !important;
				display: block !important;
				min-height: 32px !important;
				-webkit-border-top-left-radius: 0px !important;
				-moz-border-radius-topleft: 0px !important;
				border-top-left-radius: 0px !important;
				-webkit-border-top-right-radius: 0px !important;
				-moz-border-radius-topright: 0px !important;
				border-top-right-radius: 0px !important;
				-webkit-border-bottom-left-radius: 0px !important;
				-moz-border-radius-bottomleft: 0px !important;
				border-bottom-left-radius: 0px !important;
				-webkit-border-bottom-right-radius: 0px !important;
				-moz-border-radius-bottomright: 0px !important;
				border-bottom-right-radius: 0px !important;
			}

			#WFItem394041 .wf-footer {

			}

			#WFItem394041 .wf-input {
				padding: 2px 0 !important;
				font-family: Arial !important;
				font-size: 14px !important;
				color: #000000 !important;
				font-weight: normal !important;
				font-style: normal !important;
				text-decoration: none !important;
				background-color: #FFFFFF !important;
				border-width: 1px !important;
				border-color: #D9D9D9 !important;
				border-style: solid !important;
				width: 100% !important;
				-webkit-border-top-left-radius: 8px !important;
				-moz-border-radius-topleft: 8px !important;
				border-top-left-radius: 8px !important;
				-webkit-border-top-right-radius: 8px !important;
				-moz-border-radius-topright: 8px !important;
				border-top-right-radius: 8px !important;
				-webkit-border-bottom-left-radius: 8px !important;
				-moz-border-radius-bottomleft: 8px !important;
				border-bottom-left-radius: 8px !important;
				-webkit-border-bottom-right-radius: 8px !important;
				-moz-border-radius-bottomright: 8px !important;
				border-bottom-right-radius: 8px !important;
			}

			#WFItem394041 .wf-label {
				font-family: Arial !important;
				font-size: 12px !important;
				color: #555555 !important;
				font-weight: bold !important;
				font-style: normal !important;
				text-decoration: none !important;
				padding-top: 3px !important;
				padding-bottom: 3px !important;
				padding-left: 0 !important;
				padding-right: 3px !important;
				display: block !important;
			}

			#WFItem394041 .wf-input {
				font-family: Arial !important;
				font-size: 14px !important;
				color: #555555 !important;
				font-weight: normal !important;
				font-style: normal !important;
				text-decoration: none !important;
				background-color: #FFFFFF !important;
				border-width: 1px !important;
				border-color: #D9D9D9 !important;
				border-style: solid !important;
				width: 100% !important;
				-webkit-border-top-left-radius: 8px !important;
				-moz-border-radius-topleft: 8px !important;
				border-top-left-radius: 8px !important;
				-webkit-border-top-right-radius: 8px !important;
				-moz-border-radius-topright: 8px !important;
				border-top-right-radius: 8px !important;
				-webkit-border-bottom-left-radius: 8px !important;
				-moz-border-radius-bottomleft: 8px !important;
				border-bottom-left-radius: 8px !important;
				-webkit-border-bottom-right-radius: 8px !important;
				-moz-border-radius-bottomright: 8px !important;
				border-bottom-right-radius: 8px !important;
			}

			#WFItem394041 .wf-button {
				font-family: Arial !important;
				font-size: 22px !important;
				color: #FFFFFF !important;
				font-weight: bold !important;
				font-style: normal !important;
				text-decoration: none !important;
				background-color: #0DBAFF !important;
				background-image: url(https://app.getresponse.com/images/core/webforms/gradient_top.png) !important;
				border-width: 0px !important;
				border-color: #000000 !important;
				border-style: none !important;
				padding-left: 16px !important;
				padding-right: 16px !important;
				padding-top: 8px !important;
				padding-bottom: 8px !important;
				-webkit-border-top-left-radius: 8px !important;
				-moz-border-radius-topleft: 8px !important;
				border-top-left-radius: 8px !important;
				-webkit-border-top-right-radius: 8px !important;
				-moz-border-radius-topright: 8px !important;
				border-top-right-radius: 8px !important;
				-webkit-border-bottom-left-radius: 8px !important;
				-moz-border-radius-bottomleft: 8px !important;
				border-bottom-left-radius: 8px !important;
				-webkit-border-bottom-right-radius: 8px !important;
				-moz-border-radius-bottomright: 8px !important;
				border-bottom-right-radius: 8px !important;
			}

			#WFItem394041 .wf-button:hover {
				!important;

			}

			#WFItem394041 .wf-submit {
				text-align: center !important;
			}

			#WFItem394041 .wf-labelpos {
				float: none !important;
				width: auto !important;
				text-align: left !important;
			}

			#WFItem394041 .wf-inputpos {
				float: none !important;
				width: auto !important;
			}

			#WFItem394041 .wf-privacy {
				font-family: Arial !important;
				font-size: 12px !important;
				color: #B8B8B8 !important;
				font-weight: normal !important;
				font-style: italic !important;
				text-decoration: none !important;
				text-align: center !important;
			}

			#WFItem394041 .wf-privacyico {
				padding: 4px 0 0 20px !important;
				background: url(https://app.getresponse.com/images/core/webforms/lock.png) no-repeat left center !important;
			}

			#WFItem394041 .wf-counter {
				font-family: Arial !important;
				font-size: 12px !important;
				color: #44454f !important;
				font-weight: bold !important;
				font-style: normal !important;
				text-decoration: none !important;
				text-align: center !important;
			}

			#WFItem394041 .wf-poweredby {
				font-family: Arial !important;
				font-size: 10px !important;
				color: #B8B8B8 !important;
				font-weight: normal !important;
				font-style: italic !important;
				text-decoration: none !important;
				text-align: center !important;
			}

			#WFItem394041 .wf-link {
				font-family: Arial !important;
				font-size: 12px !important;
				color: #ffffff !important;
				font-weight: normal !important;
				font-style: normal !important;
				text-decoration: none !important;
			}

			#WFItem394041 .wf-link:hover {
				font-family: Arial !important;
				font-size: 12px !important;
				color: #ffffff !important;
				font-weight: normal !important;
				font-style: normal !important;
				text-decoration: none !important;
			}

			#WFItem394041 .wf-text {
				font-family: Arial !important;
				font-size: 12px !important;
				color: #B8B8B8 !important;
			}

			#WFItem394041 .wf-divider {
				border-top-width: 1px !important;
				border-color: #ffffff !important;
				border-style: solid !important;
			}

			#WFItem394041 .wf-image {
				text-align: center !important;
			}

			#WFItem394041 .wf-privacylink {
				color: #B8B8B8 !important;
				font-style: italic !important;
			}

			#WFItem394041 .wf-poweredbylink {
				color: #B8B8B8 !important;
				font-style: italic !important;
			}

			#WFItem394041 .wf-imgbox {
				overflow: hidden !important;
			}

			#WFItem394041 .wf-rc-reload {
				display: inline-block !important;
				width: 16px !important;
				height: 16px !important;
				overflow: hidden !important;
				text-indent: -99999px !important;
				position: relative !important;
				top: 3px !important;
				margin-left: 3px !important;
				background: url(https://app.getresponse.com/images/core/webforms/captchaico.png) no-repeat 0 -32px !important;
			}

			#WFItem394041 .wf-rc-audio {
				display: inline-block !important;
				width: 16px !important;
				height: 16px !important;
				overflow: hidden !important;
				text-indent: -99999px !important;
				position: relative !important;
				top: 3px !important;
				margin-left: 3px !important;
				background: url(https://app.getresponse.com/images/core/webforms/captchaico.png) no-repeat 0 0 !important;
			}

			#WFItem394041 .wf-rc-image {
				display: inline-block !important;
				width: 16px !important;
				height: 16px !important;
				overflow: hidden !important;
				text-indent: -99999px !important;
				position: relative !important;
				top: 3px !important;
				margin-left: 3px !important;
				background: url(https://app.getresponse.com/images/core/webforms/captchaico.png) no-repeat 0 -16px !important;
			}

			#WFItem394041 #recaptcha_image {
				width: 200px !important;
				height: 38px !important;
			}

			#WFItem394041 #recaptcha_image img {
				width: 200px !important;
				height: 38px !important;
			}

			#WFItem394041 .wf-rc-boxm {
				width: 200px !important;
				margin: 0 auto !important;
				overflow: hidden !important;
			}

			#WFItem394041 em {
				color: inherit !important;
				font-style: italic !important;
			}
		</style>
		<div id="WFItem394041" class="wf-formTpl">
			<form accept-charset="utf-8"
				action="https://app.getresponse.com/add_contact_webform.html"
				method="post" target="_top">
				<div class="box">
					<div id="WFIcenter" class="wf-body">
						<ul class="wf-sortable" id="wf-sort-id">
							<li class="wf-email" rel="undefined"
								style="display: block !important;">
								<div class="wf-contbox">
									<div class="wf-labelpos">
										<label class="wf-label">Email:</label>
									</div>
									<div class="wf-inputpos">
										<input type="text" class="wf-input wf-req wf-valid__email"
											name="email"></input>
									</div>
									<em class="clearfix clearer"></em>
								</div>
							</li>
							<li class="wf-submit" rel="undefined"
								style="display: block !important;">
								<div class="wf-contbox">
									<div class="wf-inputpos">
										<input type="submit"
											style="width: 126px ! important; display: inline !important;"
											value="Sign Up!" class="wf-button" name="submit"></input>
									</div>
									<em class="clearfix clearer"></em>
								</div>
							</li>
							<li class="wf-captcha" rel="undefined"
								style="display: none !important;">
								<div wf-captchaerror="Incorrect please try again"
									wf-captchasound="Enter the numbers you hear:"
									wf-captchaword="Enter the words above:"
									class="wf-contbox wf-captcha-1" id="wf-captcha-1"></div>
							</li>
						</ul>
					</div>
					<div id="WFIfooter" class="wf-footer el">
						<div class="actTinyMceElBodyContent"></div>
						<em class="clearfix clearer"></em>
					</div>
				</div>
				<input type="hidden" name="webform_id" value="394041"/>
			</form>
		</div>
		<?php
		}

		private function yop_poll_optin_form1() {
		?>
        <style type="text/css">
            @font-face {
                font-family: Lato-Reg;
                src: url(<?php echo $this->_config->plugin_url; ?>/fonts/Lato-Reg.ttf);
            }

            @font-face {
                font-family: Lato-Lig;
                src: url(<?php echo $this->_config->plugin_url; ?>/fonts/Lato-Lig.ttf);
            }

            @font-face {
                font-family: Lato-Bla;
                src: url(<?php echo $this->_config->plugin_url; ?>/fonts/Lato-Bla.ttf);
            }

            @font-face {
                font-family: 'FontomasCustomRegular';
                src: url('<?php echo $this->_config->plugin_url; ?>/fonts/fontomas-webfont.eot');
                src: url('<?php echo $this->_config->plugin_url; ?>/fonts/fontomas-webfont.eot?#iefix') format('embedded-opentype'), url('<?php echo $this->_config->plugin_url; ?>/fonts/fontomas-webfont.woff') format('woff'), url('<?php echo $this->_config->plugin_url; ?>/fonts/fontomas-webfont.ttf') format('truetype'), url('<?php echo $this->_config->plugin_url; ?>/fonts/fontomas-webfont.svg#FontomasCustomRegular') format('svg');
                font-weight: normal;
                font-style: normal;
            }

            /* Optin */
            #WFItem394041 {
                background: #f7f7f7; /* Old browsers */
                background: -moz-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left bottom, right top, color-stop(0%, #f7f7f7), color-stop(100%, #ffffff)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* IE10+ */
                background: linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f7f7f7', endColorstr='#ffffff', GradientType=1); /* IE6-9 fallback on horizontal gradient */
                border: 1px solid #fff;
                -moz-box-shadow: 0px 0px 9px #dadada;
                -webkit-box-shadow: 0px 0px 9px #dadada;
                box-shadow: 0px 0px 9px #dadada;
                color: #95abb7;
                text-align: center;
                width: 25em;
                height: auto;
            }
            #WFItem394041 h1 {
                font-size: 8em;
                margin: 0.2em;
                color: #fff;
                font-family: Lato-Bla, Arial, Helvetica, sans-serif;
                line-height: 1em;
            }

            #WFItem394041 label {
                position: relative;
            }

            #WFItem394041 h2 {
                font-size: 3em;
                margin-top: 0.5em;
                font-variant: small-caps;
				font-weight: bold;
                color: #95abb7;
            }

			#WFItem394041 h3 {
                font-size: 2em;
                margin-top: 0.2em;
				font-weight: bold;
                color: #95abb7;
				display: inline;
            }

            #WFItem394041 #circle {
                border-radius: 50%;
                background: #00a8ff;
                position: relative;
                margin: 0 auto;
                width: 7.75em;
                height: 7.75em;
            }

            #WFItem394041 #email {
                border-style: none;
                border: 1px solid #00a8ff;
                background: #fff;
                margin-top: 0.5em;
                padding-left: 2em;
                font-size: 1.125em;
                font-family: Calibri, Arial, Helvetica, sans-serif;
                color: #aeaaaa;
                -webkit-transition: all 0.3s linear;
                -moz-transition: all 0.3s linear;
                -o-transition: all 0.3s linear;
                transition: all 0.3s linear;
                width: 75%;
                height: 2.3em;
            }

            #WFItem394041 #email:focus {
                background: #f9f9f9;
            }

            #WFItem394041 .wf-button {
                margin-top: 10px;
                font-size: 1.4em;
                height: 1.7em;
                margin-bottom: 0.5em;
                border: none;
                background: #00a8ff;
                color: white;
                cursor: pointer;
                width: 75%;
            }

            #WFItem394041 .wf-button:active {
                background: #0098e6;
            }

            #yop-poll-close-modal-box {
                text-decoration: none;
                position: absolute;
                color: #00A8FF;
                cursor: pointer;
                float: right;
                font-size: 1.5em;
                height: 1em;
                width: 1em;
                top: 5px;
                right: 5px;
            }

            #WFItem394041 [data-icon]:after {
                left: 2px;
                content: attr(data-icon);
                font-family: 'FontomasCustomRegular';
                color: #00a8ff;
                position: absolute;
                left: 5px;
                top: 4px;
                width: 20px;
            }

		</style>
        <div id="WFItem394041" class="wf-formTpl">
            <a href="javascript:void(0)" id="yop-poll-close-modal-box"><span>x</span></a>
			<form accept-charset="utf-8"
                  action="https://app.getresponse.com/add_contact_webform.html"
                  method="post" target="_top">
				<div class="box">
					<div id="WFIcenter" class="wf-body">
						<ul class="wf-sortable" id="wf-sort-id">
							<li>
                                <div id="circle"><h1>?</h1></div>
								<p><h2><?php _e( 'Need Help?', 'yop_poll' ); ?></h2><br><h3><?php _e( 'Download<br /> YOP Poll User Guide', 'yop_poll' ); ?></h3></p>
							</li>
							<li class="wf-email" rel="undefined"
                                style="display: block !important;">
								<div class="wf-contbox">
									<div class="wf-inputpos">
                                        <label for="email" data-icon="e"/>
										<input id="email" type="text" class="wf-input wf-req wf-valid__email"
                                               name="email" placeholder="<?php _e( 'Email', 'yop_poll' ); ?>"></input>
									</div>
									<em class="clearfix clearer"></em>
								</div>
							</li>
							<li class="wf-submit" rel="undefined"
                                style="display: block !important;">
								<div class="wf-contbox">
									<div class="wf-inputpos">
										<input type="submit" value="<?php _e( 'Send me the FREE guide!', 'yop_poll' ); ?>" class="wf-button" name="submit"></input>
									</div>
									<em class="clearfix clearer"></em>
								</div>
							</li>
							<li class="wf-captcha" rel="undefined"
                                style="display: none !important;">
								<div wf-captchaerror="<?php _e( 'Incorrect please try again', 'yop_poll' ); ?>"
                                     wf-captchasound="<?php _e( 'Enter the numbers you hear:', 'yop_poll' ); ?>"
                                     wf-captchaword="<?php _e( 'Enter the words above:', 'yop_poll' ); ?>"
                                     class="wf-contbox wf-captcha-1" id="wf-captcha-1"></div>
							</li>
						</ul>
					</div>
					<div id="WFIfooter" class="wf-footer el">
						<div class="actTinyMceElBodyContent"></div>
						<em class="clearfix clearer"></em>
					</div>
				</div>
				<input type="hidden" name="webform_id" value="394041"/>
			</form>
		</div>
		<?php
		}

		private function yop_poll_optin_form2() {
			wp_enqueue_style("yop-poll-optin2-css", "{$this->_config->plugin_url}/css/yop-poll-optin2.css");
		?>

        <div id="WFItem394041" class="optin">
            <div id="circle">
				<h1>?</h1>
			</div>
			<form accept-charset="utf-8"
                  action="https://app.getresponse.com/add_contact_webform.html"
                  method="post" target="_top">
				<div class="box">
					<div id="WFIcenter" class="wf-body">
						<ul class="wf-sortable" id="wf-sort-id">
							<li>
								<p>
                                    <b>
                                        <?php _e( 'Need Help?', 'yop_poll' ); ?><br>
                                        <?php _e( 'Download YOP Poll User Guide!', 'yop_poll' ); ?>
                                    </b>
                                </p>
                            </li>
							<li class="wf-email" rel="undefined"
                                style="display: block !important;">
								<div class="wf-contbox">
									<div class="wf-inputpos">
                                        <label for="email" data-icon="e"/>
										<input id="email" type="text" class="wf-input wf-req wf-valid__email"
                                               name="email" placeholder="<?php _e( 'Email', 'yop_poll' ); ?>"></input>
									</div>
									<em class="clearfix clearer"></em>
								</div>
							</li>
							<li class="wf-submit" rel="undefined"
                                style="display: block !important;">
								<div class="wf-contbox">
									<div class="wf-inputpos">
										<input type="submit" value="<?php _e( 'Send me the guide!', 'yop_poll' ); ?>" class="wf-button" name="submit"></input>
									</div>
									<em class="clearfix clearer"></em>
								</div>
							</li>
							<li class="wf-captcha" rel="undefined"
                                style="display: none !important;">
								<div wf-captchaerror="<?php _e( 'Incorrect please try again', 'yop_poll' ); ?>"
                                     wf-captchasound="<?php _e( 'Enter the numbers you hear:', 'yop_poll' ); ?>"
                                     wf-captchaword="<?php _e( 'Enter the words above:', 'yop_poll' ); ?>"
                                     class="wf-contbox wf-captcha-1" id="wf-captcha-1"></div>
							</li>
						</ul>
					</div>
					<div id="WFIfooter" class="wf-footer el">
						<div class="actTinyMceElBodyContent"></div>
						<em class="clearfix clearer"></em>
					</div>
				</div>
				<input type="hidden" name="webform_id" value="394041"/>
			</form>
		</div>
		<?php
		}

		public function load_optin_box() {
			$optin_box_modal_options = get_option( 'yop_poll_optin_box_modal_options' );
			if ( $optin_box_modal_options ['show'] == 'yes' ){
				wp_enqueue_script( 'yop-poll-modal-box-js', "{$this->_config->plugin_url}/modal/js/jquery.modalbox-1.5.0-min.js", array( 'jquery' ), $this->_config->version, true );
				wp_enqueue_script( 'yop-poll-modal-functions', "{$this->_config->plugin_url}/js/yop-poll-modal-functions.js", array( 'jquery', 'yop-poll-modal-box-js' ), $this->_config->version, true );
				$yop_poll_modal_functions_config = array( 'ajax' => array( 'url' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), 'action' => 'yop_poll_modal_option_signup' ) );
				wp_localize_script( 'yop-poll-modal-functions', 'yop_poll_modal_functions_config', $yop_poll_modal_functions_config );
				wp_enqueue_style( 'yop-poll-modal-box-css', "{$this->_config->plugin_url}/modal/css/jquery.modalbox-basic.css", array(), $this->_config->version );
			}
			elseif ( $optin_box_modal_options ['sidebar_had_submit'] == 'no' ) {
				wp_enqueue_script( 'yop-poll-sidebar-option-functions', "{$this->_config->plugin_url}/js/yop-poll-sidebar-optin-functions.js", array( 'jquery' ), $this->_config->version, true );
				$yop_poll_sidebar_functions_config = array( 'ajax' => array( 'url' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ), 'action' => 'yop_poll_sidebar_option_signup' ) );
				wp_localize_script( 'yop-poll-sidebar-option-functions', 'yop_poll_sidebar_functions_config', $yop_poll_sidebar_functions_config );
			}
		}

		public function popup_bar() {
			require_once( $this->_config->plugin_inc_dir . '/yop_poll_model.php' );
			$date_30_days_before           = Yop_Poll_Model::get_mysql_custom_date( -30, 'DAY' );
			$yop_poll_first_install_date   = get_option( 'yop_poll_first_install_date' );
			$yop_poll_admin_notices_donate = get_option( 'yop_poll_admin_notices_donate' );
			if ( $date_30_days_before >= $yop_poll_first_install_date ){
				if ( 'yes' == $yop_poll_admin_notices_donate ){
				?>
                    <style type="text/css">
                        .yop-poll-updated {
                            background: #f7f7f7 !important; /* Old browsers */
                            background: -moz-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%) !important; /* FF3.6+ */
                            background: -webkit-gradient(linear, left bottom, right top, color-stop(0%, #f7f7f7), color-stop(100%, #ffffff)) !important; /* Chrome,Safari4+ */
                            background: -webkit-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%) !important; /* Chrome10+,Safari5.1+ */
                            background: -o-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%) !important; /* Opera 11.10+ */
                            background: -ms-linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%) !important; /* IE10+ */
                            background: linear-gradient(45deg, #f7f7f7 0%, #ffffff 100%) !important; /* W3C */
                            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f7f7f7', endColorstr='#ffffff', GradientType=1) !important; /* IE6-9 fallback on horizontal gradient */
                            border: 1px solid #fff !important;
                            -moz-box-shadow: 0px 0px 9px #dadada !important;
                            -webkit-box-shadow: 0px 0px 9px #dadada !important;
                            box-shadow: 0px 0px 9px #dadada !important;
                            margin: 5px 0px 15px !important;
                            padding: 1px 12px !important;
                            font-size: 16px !important;
                            line-height: 1.4em !important;
                            text-shadow: 1px 1px #fff !important;
                            width: 98% !important;
                            border: none !important;
                        }

                        .yop-poll-button {
                            border: none;
                            background: #fff;
                            border: 1px solid #00a8ff;
                            color: #95abb7;
                            padding: 5px;
                            font-family: Lato-Bla, Arial, Helvetica, sans-serif;
                            font-size: 16px;
                            -webkit-transition: all 0.3s linear;
                            -moz-transition: all 0.3s linear;
                            -o-transition: all 0.3s linear;
                            transition: all 0.3s linear;
                        }

                        .yop-poll-button:hover {
                            color: #fff;
                            background: #00a8ff;
                            border: 1px solid #fff;
                            cursor: pointer;
                        }
                    </style>
				<div class="updated yop-poll-updated" id="message">
					<p>
						<?php _e( 'It looks like you\'ve been enjoying YOP POLL Plugin for at least 30 days.', 'yop_poll' ); ?>
						<?php _e( 'Would you consider a small donation to help support continued development of the plugin?', 'yop_poll' ); ?>
					</p>
					<p>
						<input type="button" class="yop-poll-button"
							value="<?php _e( 'Support This Plugin', 'yop_poll' ) ?>"
							onclick="window.open('<?php echo esc_url( add_query_arg( array( 'yop_poll_support_plugin' => 'yes', '_wpnonce' => wp_create_nonce( 'yop_poll_donate_addmin_notices' ) ) ) ); ?>', '_blank');"/>
						<input type="button" class="yop-poll-button"
							value="<?php _e( 'Rate it 5&#9733;\'s', 'yop_poll' ) ?>"
							onclick="window.open('<?php echo esc_url( add_query_arg( array( 'yop_poll_rate_plugin' => 'yes', '_wpnonce' => wp_create_nonce( 'yop_poll_donate_addmin_notices' ) ) ) ); ?>', '_blank');"/>
						<input type="button" class="yop-poll-button"
							value="<?php _e( 'Tell Your Followers', 'yop_poll' ) ?>"
							onclick="window.open('<?php echo esc_url( add_query_arg( array( 'yop_poll_tell_followers' => 'yes', '_wpnonce' => wp_create_nonce( 'yop_poll_donate_addmin_notices' ) ) ) ); ?>', '_blank');"/>
						<input type="button" class="yop-poll-button"
							value="<?php _e( 'Don\'t Bug Me Again', 'yop_poll' ) ?>"
							onclick="document.location.href='<?php echo esc_url( add_query_arg( array( 'yop_poll_do_not_bug' => 'yes', '_wpnonce' => wp_create_nonce( 'yop_poll_donate_addmin_notices' ) ) ) ); ?>'"/>
					</p>
				</div>
				<?php
				}
			}
		}

		public function popup_bar_operations() {
			if ( ( isset ( $_GET ['yop_poll_support_plugin'] ) || isset ( $_GET ['yop_poll_rate_plugin'] ) || isset ( $_GET ['yop_poll_tell_followers'] ) || isset ( $_GET ['yop_poll_do_not_bug'] ) ) && wp_verify_nonce( $_REQUEST ['_wpnonce'], 'yop_poll_donate_addmin_notices' ) ){
				update_option( "yop_poll_admin_notices_donate", 'no' );
				$this->remove_action( 'admin_notices', 'popup_bar' );

				if ( isset ( $_GET ['yop_poll_support_plugin'] ) ){
					if ( 'yes' == $_GET ['yop_poll_support_plugin'] ){
						wp_redirect( 'http://www.yop-poll.com/thankyou/don.php', '302' );
					}
				}

				if ( isset ( $_GET ['yop_poll_rate_plugin'] ) ){
					if ( 'yes' == $_GET ['yop_poll_rate_plugin'] ){
						wp_redirect( 'http://wordpress.org/support/view/plugin-reviews/yop-poll?filter=5', '302' );
					}
				}

				if ( isset ( $_GET ['yop_poll_tell_followers'] ) ){
					if ( 'yes' == $_GET ['yop_poll_tell_followers'] ){
						wp_redirect( 'http://twitter.com/home?status=' . urlencode( 'I use Yop Poll Plugin for WordPress and you should too - http://wordpress.org/extend/plugins/yop-poll/' ), '302' );
					}
				}

				if ( isset ( $_GET ['yop_poll_do_not_bug'] ) ){
					if ( 'yes' == $_GET ['yop_poll_do_not_bug'] ){
						wp_redirect( remove_query_arg( array( 'yop_poll_do_not_bug', '_wpnonce', 'yop_poll_tell_followers', 'yop_poll_rate_plugin', 'yop_poll_support_plugin' ), stripslashes( $_SERVER ['REQUEST_URI'] ) ), '302' );
					}
				}
			}
		}

		/* END donate */
		private function current_user_can( $capability = '' ) {
			global $current_user;
			get_currentuserinfo();
			$user_roles = $current_user->roles;
			$user_role  = array_shift( $user_roles );

			$capabilities_roles = array(
				'manage_polls_options'                                                => array(
					'administrator' => true,
					'editor'        => true,
					'author'        => false,
					'contributor'   => false,
					'subscriber'    => false ),
				'manage_polls_bans'                                                   => array(
					'administrator' => true,
					'editor'        => false,
					'author'        => false,
					'contributor'   => false, 'subscriber' => false ), 'delete_polls' => array( 'administrator' => true, 'editor' => false, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'delete_own_polls' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'edit_polls' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'edit_own_polls' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'clone_polls' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'clone_own_polls' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'view_polls_logs' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'view_own_polls_logs' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'view_polls_results' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'view_own_polls_results' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'reset_polls_stats' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'reset_own_polls_stats' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'delete_polls_logs' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'delete_own_polls_logs' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'edit_polls_templates' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'edit_own_polls_templates' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'delete_polls_templates' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'delete_own_polls_templates' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'clone_polls_templates' => array( 'administrator' => true, 'editor' => true, 'author' => false, 'contributor' => false, 'subscriber' => false ), 'clone_own_polls_templates' => array( 'administrator' => true, 'editor' => true, 'author' => true, 'contributor' => false, 'subscriber' => false ), 'become_pro' => array( 'administrator' => false, 'editor' => false, 'author' => false, 'contributor' => false, 'subscriber' => false ) );
			if ( isset ( $capabilities_roles [$capability] [$user_role] ) )
				return $capabilities_roles [$capability] [$user_role];
			return false;
		}
}