<?php

	class Yop_Poll_Model{
		var $error = NULL;
		var $success = NULL;
		var $poll = array(
			'id'            => NULL,
			'name'          => NULL,
			'question'      => NULL,
			'start_date'    => NULL,
			'end_date'      => NULL,
			'total_votes'   => 0,
			'total_answers' => 0,
			'status'        => 'open',
			'date_added'    => NULL,
			'last_modified' => NULL
		);

		var $poll_options = NULL;
		var $answers = NULL;
		var $custom_fields = NULL;

		var $template = array(
			'id'                         => NULL,
			'name'                       => NULL,
			'before_vote_template'       => NULL,
			'after_vote_template'        => NULL,
			'before_start_date_template' => NULL,
			'after_end_date_template'    => NULL,
			'css'                        => NULL,
			'js'                         => NULL,
			'status'                     => 'active',
			'date_added'                 => NULL,
			'last_modified'              => NULL,
			'show_in_archive'            => NULL,
			'archive_order'              => NULL,
			'reset_template_id'          => NULL
		);
		var $vote = false;
		var $vote_types = array( 'default', 'wordpress', 'facebook', 'anonymous' );
		var $unique_id = '';
		var $tr_id = '';

		public function __construct( $poll_id = -99, $offset = 0 ) {
			//do not load id= -99
			$poll = NULL;
			//Current Active Poll id = -1
			if ( -1 == $poll_id ){
				$poll = self::get_current_active_poll( $offset );
			}
			//Latest Poll id = -2
			elseif ( -2 == $poll_id ) {
				$polls = self::get_yop_polls_filter_search( 'date_added', 'desc' );
				$poll  = ( $polls[$offset] ) ? $polls[$offset] : NULL;
			}
			//Random Poll id = -3
			elseif ( -3 == $poll_id ) {
				$polls = self::get_yop_polls_filter_search( 'rand()', '' );
				$poll  = $polls[0];
			}
			//Latest Closed Poll id = -4
			elseif ( -4 == $poll_id ) {
				$poll = self::get_latest_closed_poll( $offset );
			}
			//normal poll
			elseif ( $poll_id > 0 ) {
				$poll = self::get_poll_from_database_by_id( $poll_id );
			}
			if ( $poll ){
				$this->poll         = $poll;
				$this->poll_options = get_yop_poll_meta( $this->poll['id'], 'options', true );
				$default_options    = get_option( 'yop_poll_options', false );
				if ( is_array( $default_options ) ){
					if ( count( $default_options ) > 0 ){
						foreach ( $default_options as $option_name => $option_value ) {
							if ( !isset( $this->poll_options [$option_name] ) ){
								$this->poll_options    [$option_name] = $option_value;
							}
						}
					}
				}
			}
		}

		public static function get_poll_options_by_id( $poll_id = 0 ) {
			$poll_options    = get_yop_poll_meta( $poll_id, 'options', true );
			$default_options = get_option( 'yop_poll_options', false );
			if ( is_array( $default_options ) ){
				if ( count( $default_options ) > 0 ){
					foreach ( $default_options as $option_name => $option_value ) {
						if ( !isset( $poll_options [$option_name] ) ){
							$poll_options    [$option_name] = $option_value;
						}
					}
				}
			}
			return $poll_options;
		}

		public function set_unique_id( $unique_id ) {
			$this->unique_id = $unique_id;
		}

		public function get_unique_id() {
			return $this->unique_id;
		}

		private function countAnswers( $request = array() ) {
			$answers = 0;
			if ( isset( $request['yop_poll_answer'] ) ){
				$answers = count( $request['yop_poll_answer'] );
			}
			if ( isset( $request['yop_poll_options']['allow_other_answers'] ) ){
				if ( 'yes' == $request['yop_poll_options']['allow_other_answers'] ){
					$answers = $answers + 1;
				}
			}
			return $answers;
		}

		public function verify_request_data( $request = array(), $config = NULL ) {
			if ( isset( $request['yop_poll_name'] ) && '' == trim( $request['yop_poll_name'] ) ){
				$this->error = __( 'Poll name needed!', 'yop_poll' );
				return false;
			}
			elseif ( isset( $request['yop_poll_question'] ) && '' == trim( $request['yop_poll_question'] ) ) {
				$this->error = __( 'Poll question needed!', 'yop_poll' );
				return false;
			}
			elseif ( $this->countAnswers( $request ) < $config->min_number_of_answers ) {
				$this->error = __( 'At least ' . $config->min_number_of_answers . ' answers needed!', 'yop_poll' );
				return false;
			}
			else {
				if ( isset( $request['yop_poll_answer'] ) ){
					$index = 1;
					foreach ( $request['yop_poll_answer'] as $answer_id => $answer_value ) {
						if ( '' == trim( $answer_value ) ){
							$this->error = __( 'Answer ' . $index . ' should not be empty!', 'yop_poll' );
							return false;
						}
						$index++;
					}
				}
				elseif ( isset( $request['yop_poll_options']['allow_other_answers'] ) ) {
					if ( 'no' == $request['yop_poll_options']['allow_other_answers'] ){
						$this->error = __( 'Answers not found!', 'yop_poll' );
						return false;
					}
				}

				if ( isset( $request['yop_poll_customfield'] ) ){
					$index = 1;
					if ( count( $request['yop_poll_customfield'] > 0 ) ){
						foreach ( $request['yop_poll_customfield'] as $customfield_id => $customfield_value ) {
							if ( '' == trim( $customfield_value ) ){
								$this->error = __( 'Custom Field ' . $index . ' should not be empty!', 'yop_poll' );
								return false;
							}
							$index++;
						}
					}
				}

				if ( isset( $request['yop_poll_options']['start_date'] ) ){
					if ( '' == $request['yop_poll_options']['start_date'] ){
						$this->error = __( 'Start Date needed!', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'Start Date not found!', 'yop_poll' );
					return false;
				}

				if ( !isset( $request['yop_poll_options']['never_expire'] ) ){
					if ( isset( $request['yop_poll_options']['end_date'] ) ){
						if ( '' == $request['yop_poll_options']['end_date'] ){
							$this->error = __( 'End Date needed!', 'yop_poll' );
							return false;
						}
					}
					if ( isset( $request['yop_poll_options']['start_date'] ) ){
						if ( isset( $request['yop_poll_options']['end_date'] ) ){
							if ( $request['yop_poll_options']['start_date'] > $request['yop_poll_options']['end_date'] ){
								$this->error = __( 'Invalid entry! Start Date is after the  End Date! ', 'yop_poll' );
								return false;
							}
						}
					}
				}

				//answer_result_label
				if ( isset( $request['yop_poll_options']['answer_result_label'] ) ){
					if ( 'votes-number' == $request['yop_poll_options']['view_results_type'] ){
						if ( stripos( $request['yop_poll_options']['answer_result_label'], '%POLL-ANSWER-RESULT-VOTES%' ) === false ){
							$this->error = __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-VOTES%!', 'yop_poll' );
							return false;
						}
					}

					if ( 'percentages' == $request['yop_poll_options']['view_results_type'] ){
						if ( stripos( $request['yop_poll_options']['answer_result_label'], '%POLL-ANSWER-RESULT-PERCENTAGES%' ) === false ){
							$this->error = __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-PERCENTAGES%!', 'yop_poll' );
							return false;
						}
					}

					if ( 'votes-number-and-percentages' == $request['yop_poll_options']['view_results_type'] ){
						if ( stripos( $request['yop_poll_options']['answer_result_label'], '%POLL-ANSWER-RESULT-PERCENTAGES%' ) === false ){
							$this->error = __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-VOTES% and %POLL-ANSWER-RESULT-PERCENTAGES%!', 'yop_poll' );
							return false;
						}
						elseif ( stripos( $request['yop_poll_options']['answer_result_label'], '%POLL-ANSWER-RESULT-VOTES%' ) === false ) {
							$this->error = __( 'Option "Poll Answer Result Label" Not Updated! You must use %POLL-ANSWER-RESULT-VOTES% and %POLL-ANSWER-RESULT-PERCENTAGES%!', 'yop_poll' );
							return false;
						}
					}
				}
				else {
					$this->error = __( 'Option "Poll Answer Result Label" Not Updated!', 'yop_poll' );
					return false;
				}

				//view_results_link
				if ( isset( $request['yop_poll_options']['view_results_link'] ) ){
					if ( in_array( $request['yop_poll_options']['view_results_link'], array( 'yes', 'no' ) ) ){
						if ( 'yes' == $request['yop_poll_options']['view_results_link'] ){
							//view_results_link_label
							if ( isset( $request['yop_poll_options']['view_results_link_label'] ) ){
								if ( '' == $request['yop_poll_options']['view_results_link_label'] ){
									$this->error = __( 'Option "View Results Link Label" is empty!', 'yop_poll' );
									return false;
								}
							}
							else {
								$this->error = __( 'Option "View Results Link Label" not found!', 'yop_poll' );
								return false;
							}
						}
					}
					else {
						$this->error = __( 'Option "View Results Link" is invalid! You must choose between \'yes\' or \'no\'', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'Option "View Results Link" not found!', 'yop_poll' );
					return false;
				}

				//view_back_to_vote_link
				if ( isset( $request['yop_poll_options']['view_back_to_vote_link'] ) ){
					if ( in_array( $request['yop_poll_options']['view_back_to_vote_link'], array( 'yes', 'no' ) ) ){
						if ( 'yes' == $request['yop_poll_options']['view_back_to_vote_link'] ){
							//view_back_to_vote_link_label
							if ( isset( $request['yop_poll_options']['view_back_to_vote_link_label'] ) ){
								if ( '' == $request['yop_poll_options']['view_back_to_vote_link_label'] ){
									$this->error = __( 'Option "View Back to Vote Link Label" is empty!', 'yop_poll' );
									return false;
								}
							}
							else {
								$this->error = __( 'Option "View Back to Vote Link Label" not found!', 'yop_poll' );
								return false;
							}
						}
					}
					else {
						$this->error = __( 'Option "View Back to Vote Link" is invalid! You must choose between \'yes\' or \'no\'', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'Option "View Back to Vote Link" not found!', 'yop_poll' );
					return false;
				}

				//view_total_votes
				if ( isset( $request['yop_poll_options']['view_total_votes'] ) ){
					if ( in_array( $request['yop_poll_options']['view_total_votes'], array( 'yes', 'no' ) ) ){
						//view_total_votes
						if ( 'yes' == $request['yop_poll_options']['view_total_votes'] ){
							if ( isset( $request['yop_poll_options']['view_total_votes_label'] ) ){
								if ( stripos( $request['yop_poll_options']['view_total_votes_label'], '%POLL-TOTAL-VOTES%' ) === false ){
									$this->error = __( 'You must use %POLL-TOTAL-VOTES% to define your Total Votes label!', 'yop_poll' );
									return false;
								}
							}
							else {
								$this->error = __( 'Option "Total Votes Label" not found!', 'yop_poll' );
								return false;
							}
						}
					}
					else {
						$this->error = __( 'Option "View Total Votes" is invalid! Please choose between \'yes\' or \'no\'', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'Option "View Total Votes" not found!', 'yop_poll' );
					return false;
				}

				//view_total_answers
				if ( isset( $request['yop_poll_options']['view_total_answers'] ) ){
					if ( in_array( $request['yop_poll_options']['view_total_answers'], array( 'yes', 'no' ) ) ){
						//view_total_votes
						if ( 'yes' == $request['yop_poll_options']['view_total_answers'] ){
							if ( isset( $request['yop_poll_options']['view_total_answers_label'] ) ){
								if ( stripos( $request['yop_poll_options']['view_total_answers_label'], '%POLL-TOTAL-ANSWERS%' ) === false ){
									$this->error = __( 'You must use %POLL-TOTAL-ANSWERS% to define your Total Answers label!', 'yop_poll' );
									return false;
								}
							}
							else {
								$this->error = __( 'Option "Total Answers Label" not found!', 'yop_poll' );
								return false;
							}
						}
					}
					else {
						$this->error = __( 'Option "View Total Answers" is invalid! You must choose between \'yes\' or \'no\'', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'Option "View Total Answers" not found!', 'yop_poll' );
					return false;
				}

				//view_poll_archive_link
				if ( isset( $request['yop_poll_options']['view_poll_archive_link'] ) ){
					if ( in_array( $request['yop_poll_options']['view_poll_archive_link'], array( 'yes', 'no' ) ) ){
						if ( 'yes' == $request['yop_poll_options']['view_poll_archive_link'] ){
							//view_poll_archive_link_label
							if ( isset( $request['yop_poll_options']['view_poll_archive_link_label'] ) ){
								if ( '' == $request['yop_poll_options']['view_poll_archive_link_label'] ){
									$this->error = __( 'Option "View Poll Archive Link Label" is empty!', 'yop_poll' );
									return false;
								}
							}
							else {
								$this->error = __( 'Option "View Poll Archive Link Label" not found!', 'yop_poll' );
								return false;
							}

							//poll_archive_url
							if ( isset( $request['yop_poll_options']['poll_archive_url'] ) ){
								if ( '' == $request['yop_poll_options']['poll_archive_url'] ){
									$this->error = __( 'Option "Poll Archive URL" is empty!', 'yop_poll' );
									return false;
								}
							}
							else {
								$this->error = __( 'Option "Poll Archive URL" not found!', 'yop_poll' );
								return false;
							}
						}
					}
					else {
						$this->error = __( 'Option "View Poll Archive Link" Is Invalid! You must choose between \'yes\' or \'no\'', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'Option "View Poll Archive Link" not found!', 'yop_poll' );
					return false;
				}

				//show_in_archive
				if ( isset( $request['yop_poll_options']['show_in_archive'] ) ){
					if ( in_array( $request['yop_poll_options']['show_in_archive'], array( 'yes', 'no' ) ) ){
						if ( 'yes' == $request['yop_poll_options']['show_in_archive'] ){
							//archive_order
							if ( isset( $request['yop_poll_options']['archive_order'] ) ){
								if ( '' == $request['yop_poll_options']['archive_order'] ){
									$this->error = __( 'Option "Archive Order" is empty!', 'yop_poll' );
									return false;
								}
							}
							else {
								$this->error = __( 'Option "Archive Order" not found!', 'yop_poll' );
								return false;
							}
						}
					}
					else {
						$this->error = __( 'Option "Show in Archive" is invalid! You must choose between \'yes\' or \'no\'', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'Option "Show in Archive" not found!', 'yop_poll' );
					return false;
				}

				if ( isset( $request['yop_poll_options']['template'] ) ){
					$template = self::get_poll_template_from_database_by_id( $request['yop_poll_options']['template'] );
					if ( !$template ){
						$this->error = __( 'Template not found!', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'You must choose a template for your poll!', 'yop_poll' );
					return false;
				}

				return true;
			}
			return false;
		}

		public function verify_template_request_data( $request = array(), $config = NULL ) {
			if ( isset( $request['yop_poll_template_name'] ) && '' == trim( $request['yop_poll_template_name'] ) ){
				$this->error = __( 'Poll Template name needed!', 'yop_poll' );
				return false;
			}
			elseif ( isset( $request['yop_pol_template'] ) && '' == trim( $request['yop_pol_template'] ) ) {
				$this->error = __( 'Poll template needed!', 'yop_poll' );
				return false;
			}
			else {
				return true;
			}
			return false;
		}

		public function make_poll_template_from_request_data( $request = array(), $config = NULL ) {
			$this->template['id']                         = isset( $request['template_id'] ) ? trim( $request['template_id'] ) : NULL;
			$this->template['name']                       = isset( $request['yop_poll_template_name'] ) ? trim( $request['yop_poll_template_name'] ) : NULL;
			$this->template['before_vote_template']       = isset( $request['yop_poll_before_vote_template'] ) ? trim( $request['yop_poll_before_vote_template'] ) : NULL;
			$this->template['after_vote_template']        = isset( $request['yop_poll_after_vote_template'] ) ? trim( $request['yop_poll_after_vote_template'] ) : NULL;
			$this->template['before_start_date_template'] = isset( $request['yop_poll_template_before_start_date'] ) ? trim( $request['yop_poll_template_before_start_date'] ) : NULL;
			$this->template['after_end_date_template']    = isset( $request['yop_poll_template_after_end_date'] ) ? trim( $request['yop_poll_template_after_end_date'] ) : NULL;
			$this->template['css']                        = isset( $request['yop_poll_template_css'] ) ? trim( $request['yop_poll_template_css'] ) : NULL;
			$this->template['js']                         = isset( $request['yop_poll_template_css'] ) ? trim( $request['yop_poll_template_js'] ) : NULL;
			$this->template['reset_template_id']          = isset( $request['yop_poll_reset_template_id'] ) ? trim( $request['yop_poll_reset_template_id'] ) : NULL;
		}

		public function add_poll_template_to_database( $request = array(), $config = NULL ) {
			if ( $this->verify_template_request_data( $request, $config ) ){
				$this->make_poll_template_from_request_data( $request, $config );
				$result = self::get_poll_template_from_database_by_name( $this->template['name'] );
				if ( !isset( $result['id'] ) ){
					$this->template['id'] = self::insert_poll_template_to_database( $this->template );
					return $this->template['id'];
				}
				else {
					$this->error = __( 'This template name already exist! Please choose another name!', 'yop_poll' );
					return false;
				}
			}
			else {
				return false;
			}
		}

		public static function add_bans( $request ) {
			global $wpdb;
			$success = NULL;
			$error   = NULL;

			if ( !isset( $request['ban_poll_id'] ) ){
				$error = __( 'You must choose a yop poll! ' );
			}
			elseif ( !ctype_digit( $request['ban_poll_id'] ) ) {
				$error = __( 'Invalid Yop Poll! Please try again! ' );
			}
			elseif ( !in_array( $request['ban_type'], array( 'ip', 'username', 'email' ) ) ) {
				$error = __( 'You must choose a ban type!', 'yop_poll' );
			}
			elseif ( '' == trim( $request['ban_value'] ) ) {
				$error = __( 'You must choose a ban value!', 'yop_poll' );
			}
			else {
				$ban_textarea = nl2br( $request['ban_value'] );
				$values       = explode( '<br />', $ban_textarea );
				if ( count( $values ) > 0 ){
					foreach ( $values as $value ) {
						if ( '' != trim( $value ) ){
							$ban   = array( 'poll_id' => trim( $request['ban_poll_id'] ), 'type' => trim( $request['ban_type'] ), 'value' => trim( $value ) );
							$exist = $wpdb->get_var( $wpdb->prepare( "
									SELECT id
									FROM " . $wpdb->yop_poll_bans . "
									WHERE poll_id in( 0, %d) AND
									(type = %s and value = %s )
									LIMIT 0,1
									", $ban['poll_id'], $ban['type'], $ban['value'] ) );

							if ( !$exist ){
								$ban = self::insert_ban_to_database( $ban );
								if ( $ban ){
									$success++;
								}
							}
						}
					}
				}
			}
			return array( 'success' => $success, 'error' => $error );
		}

		public function edit_poll_template_in_database( $request = array(), $config = NULL ) {
			if ( $this->verify_template_request_data( $request, $config ) ){
				$this->make_poll_template_from_request_data( $request, $config );
				$result = self::get_poll_template_from_database_by_id( $this->template['id'] );
				if ( isset( $result['id'] ) ){
					$result = self::get_poll_template_from_database_by_name( $this->template['name'], array( $this->template['id'] ) );
					if ( !isset( $result['id'] ) ){
						self::update_poll_template_in_database( $this->template );
						return $this->template['id'];
					}
					else {
						$this->error = __( 'This template name already exists! Please choose another name!', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'This poll template doesn`t exist!', 'yop_poll' );
					return false;
				}
			}
			else {
				return false;
			}
		}

		public function reset_poll_template( $request = array(), $config = NULL ) {
			$this->make_poll_template_from_request_data( $request, $config );
			$result = self::get_poll_template_from_database_by_id( $this->template['id'] );
			if ( isset( $result['id'] ) ){
				if ( '' != $this->template['reset_template_id'] ){
					self::reset_poll_template_in_database( $this->template['id'], $this->template['reset_template_id'] );
					return $this->template['id'];
				}
				else {
					$this->error = __( 'You need to choose a template to reset!', 'yop_poll' );
					return false;
				}
			}
			else {
				$this->error = __( 'This poll template doesn`t exist!', 'yop_poll' );
				return false;
			}
		}

		private static function reset_poll_template_in_database( $poll_id, $template_id ) {
			global $wpdb;

			$sql = "UPDATE `" . $wpdb->yop_poll_templates . "` SET ";
			switch ( $template_id ) {
				case '1': //White
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#555; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#555; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '2': //Grey
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#eee;\r\n    padding:10px;\r\n    color:#000;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#000; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#000; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '3': //Dark
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#555;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#333333; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '4': //Blue v1
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#327BD6;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '5': //Blue v2
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-weight:bold;\r\n    background:#327BD6;\r\n    color:#fff;\r\n    padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '6': //Blue v3
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #327BD6;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px;  }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#327BD6; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '7': //Red v1
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#B70004;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '8': //Red v2
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-weight:bold;\r\n    background:#B70004;\r\n    color:#fff;\r\n    padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '9': //Red v3
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #B70004;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#B70004; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '10': //Green v1
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#3F8B43;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:00FF00;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '11': //Green v2
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-weight:bold;\r\n    background:#3F8B43;\r\n    color:#fff;\r\n    padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '12': //Green v3
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #3F8B43;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#3F8B43; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '13': //Orange v1
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#FB6911;\r\n    padding:10px;\r\n    color:#fff;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#fff; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '14': //Orange v2
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:0px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-weight:bold;\r\n    background:#FB6911;\r\n    color:#fff;\r\n    padding:5px;\r\n        text-align:center;\r\n        font-size:12px;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
				case '15': //Orange v3
					$sql .= "`before_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-ANSWER-CHECK-INPUT% \r\n            %POLL-ANSWER-LABEL%\r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_CONTAINER]\r\n        [OTHER_ANSWER_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-answer-%POLL-ID%\\" . '"' . ">\r\n            %POLL-OTHER-ANSWER-CHECK-INPUT% \r\n            %POLL-OTHER-ANSWER-LABEL% \r\n            <span class=\\" . '"' . "yop-poll-results-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-RESULT-LABEL%</span>\r\n            %POLL-OTHER-ANSWER-TEXT-INPUT% \r\n            %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/OTHER_ANSWER_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-custom-%POLL-ID%\\" . '"' . ">\r\n    <ul>\r\n        [CUSTOM_FIELD_CONTAINER]\r\n        <li>%POLL-CUSTOM-FIELD-LABEL% %POLL-CUSTOM-FIELD-TEXT-INPUT%</li>\r\n        [/CUSTOM_FIELD_CONTAINER]\r\n    </ul>\r\n</div>    \r\n[CAPTCHA_CONTAINER]\r\n<div id=\"yop-poll-captcha-%POLL-ID%\">\r\n    <div class=\"yop-poll-captcha-image-div\" id=\"yop-poll-captcha-image-div-%POLL-ID%\">\r\n        %CAPTCHA-IMAGE%\r\n        <div class=\"yop-poll-captcha-helpers-div\" id=\"yop-poll-captcha-helpers-div-%POLL-ID%\">%RELOAD-CAPTCHA-IMAGE% </div>\r\n        <div class=\"yop_poll_clear\"></div>\r\n    </div>\r\n    %CAPTCHA-LABEL%\r\n    <div class=\"yop-poll-captcha-input-div\" id=\"yop-poll-captcha-input-div-%POLL-ID%\">%CAPTCHA-INPUT%</div>\r\n</div>\r\n[/CAPTCHA_CONTAINER]\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-VOTE-BUTTON%</div>\r\n    <div id=\\" . '"' . "yop-poll-results-%POLL-ID%\\" . '"' . ">%POLL-VIEW-RESULT-LINK%</div>\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n</div>',
					`after_vote_template`            = '<div id=\\" . '"' . "yop-poll-name-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-name\\" . '"' . ">%POLL-NAME%</div>\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label-%POLL-ID%\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text-%POLL-ID%\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result-%POLL-ID%\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per-%POLL-ID%\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <div>\r\n             %POLL-ANSWER-RESULT-BAR%\r\n           </div>\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n<div id=\\" . '"' . "yop-poll-vote-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-footer\\" . '"' . ">\r\n    <div>%POLL-TOTAL-ANSWERS%</div>\r\n    <div>%POLL-TOTAL-VOTES%</div>\r\n    <div id=\\" . '"' . "yop-poll-back-%POLL-ID%\\" . '"' . ">%POLL-BACK-TO-VOTE-LINK%</div>\r\n</div>',
					`before_start_date_template`    = 'This poll is about to <br>\r\nstart at %POLL-START-DATE%<br>\r\nand finish at %POLL-END-DATE%<br>',
					`after_end_date_template`        = 'This poll is closed!\r\nPoll activity: <br>\r\nstart_date %POLL-START-DATE%<br>\r\nend_date %POLL-END-DATE%<br>\r\n\r\nPoll Results:\r\n<div id=\\" . '"' . "yop-poll-question-%POLL-ID%\\" . '"' . " class=\\" . '"' . "yop-poll-question\\" . '"' . ">%POLL-QUESTION%</div>\r\n<div id=\\" . '"' . "yop-poll-answers-1\\" . '"' . " class=\\" . '"' . "yop-poll-answers\\" . '"' . ">\r\n    <ul>\r\n        [ANSWER_RESULT_CONTAINER]\r\n        <li class=\\" . '"' . "yop-poll-li-result-%POLL-ID%\\" . '"' . ">\r\n            <label class=\\" . '"' . "pds-feedback-label\\" . '"' . ">\r\n                <span class=\\" . '"' . "pds-answer-text\\" . '"' . ">%POLL-ANSWER-LABEL%</span>\r\n                <span class=\\" . '"' . "pds-feedback-result\\" . '"' . ">\r\n                    <span class=\\" . '"' . "pds-feedback-per\\" . '"' . "> %POLL-ANSWER-RESULT-LABEL%</span>\r\n                </span>\r\n            </label>\r\n            <span class=\\" . '"' . "pds-clear\\" . '"' . " style=\\" . '"' . "display: block;clear: both;height:1px;line-height:1px;\\" . '"' . "> </span>\r\n    %POLL-ANSWER-RESULT-BAR%\r\n        </li>\r\n        [/ANSWER_RESULT_CONTAINER]\r\n    </ul>\r\n</div>\r\n%POLL-VOTE-BUTTON%',
					`css`                            = '#yop-poll-container-%POLL-ID% {\r\n    width:%POLL-WIDTH%;\r\n    background:#fff;\r\n    padding:10px;\r\n    color:#555;\r\n    overflow:hidden;\r\n    font-size:12px;\r\n    border:5px solid #FB6911;\r\n}\r\n#yop-poll-name-%POLL-ID% {\r\n    font-size:14px;\r\n    font-weight:bold;\r\n}\r\n\r\n#yop-poll-question-%POLL-ID% {\r\n    font-size:14px;\r\n    margin:5px 0px;\r\n}\r\n#yop-poll-answers-%POLL-ID% {  }\r\n#yop-poll-answers-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li { \r\n    font-style:normal;\r\n    margin:0px 0px 10px 0px;\r\n    padding:0px;\r\n    font-size:12px;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li input { \r\n    margin:0px; \r\n    float:none;\r\n}\r\n#yop-poll-answers-%POLL-ID% ul li label { \r\n    margin:0px; \r\n    font-style:normal; \r\n    font-weight:normal; \r\n    font-size:12px; \r\n    float:none;\r\n}\r\n.yop-poll-results-%POLL-ID% {\r\n    font-size: 12px;\r\n    font-style: italic;\r\n    font-weight: normal;\r\n    margin-left: 15px;\r\n}\r\n\r\n#yop-poll-custom-%POLL-ID% {  }\r\n#yop-poll-custom-%POLL-ID% ul {\r\n    list-style: none outside none;\r\n    margin: 0;\r\n    padding: 0;\r\n}\r\n#yop-poll-custom-%POLL-ID% ul li { \r\n    padding:0px;\r\n    margin:0px;    \r\n    font-size:14px;\r\n}\r\n#yop-poll-container-%POLL-ID% input[type=\'text\'] { margin:0px 0px 5px 0px; padding:2%; width:96%; text-indent:2%; font-size:12px; }\r\n\r\n#yop-poll-captcha-input-div-%POLL-ID% {\r\nmargin-top:5px;\r\n}\r\n#yop-poll-captcha-helpers-div-%POLL-ID% {\r\nwidth:30px;\r\nfloat:left;\r\nmargin-left:5px;\r\nheight:0px;\r\n}\r\n\r\n#yop-poll-captcha-helpers-div-%POLL-ID% img {\r\nmargin-bottom:2px;\r\n}\r\n\r\n#yop-poll-captcha-image-div-%POLL-ID% {\r\nmargin-bottom:5px;\r\n}\r\n\r\n#yop_poll_captcha_image_%POLL-ID% {\r\nfloat:left;\r\n}\r\n\r\n.yop_poll_clear {\r\nclear:both;\r\n}\r\n\r\n#yop-poll-vote-%POLL-ID% {\r\n\r\n}\r\n.yop-poll-results-bar-%POLL-ID% { background:#f5f5f5; height:10px;  }\r\n.yop-poll-results-bar-%POLL-ID% div { background:#555; height:10px; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-vote-%POLL-ID% button { float:left; }\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% {\r\n    float: right;\r\n    margin-bottom: 20px;\r\n    margin-top: -20px;\r\n    width: auto;\r\n}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-results-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div#yop-poll-back-%POLL-ID% a { color:#FB6911; text-decoration:underline; font-size:12px;}\r\n#yop-poll-vote-%POLL-ID% div { float:left; width:100%; }\r\n\r\n#yop-poll-container-error-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:red;\r\n    text-transform:lowercase;\r\n}\r\n\r\n#yop-poll-container-success-%POLL-ID% {\r\n    font-size:12px;\r\n    font-style:italic;\r\n    color:green;\r\n}',
					`js`                            = 'function stripBorder_%POLL-ID%(object) {\r\n    object.each(function() {\r\n            if( parseInt(jQuery(this).width() ) > 0) {\r\n                jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ")) );\r\n            }\r\n            else {\r\n                jQuery(this).css(\\" . '"' . "border-left-width\\" . '"' . ", \'0px\');\r\n                jQuery(this).css(\\" . '"' . "border-right-width\\" . '"' . ", \'0px\');\r\n            }\r\n    });\r\n}\r\nfunction stripPadding_%POLL-ID%(object) {\r\n    object.each(function() { \r\n            jQuery(this).width( parseInt( jQuery(this).width() ) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) - parseInt(jQuery(this).css(\\" . '"' . "padding-left\\" . '"' . ")) );\r\n    });\r\n}\r\n\r\nfunction strip_results_%POLL-ID%() {\r\n        stripPadding_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop_poll_li_result-%POLL-ID%\\" . '"' . ") );   \r\n    stripBorder_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-result-bar-%POLL-ID%\\" . '"' . "));\r\n}\r\n\r\njQuery(document).ready(function(e) {\r\n                if(typeof window.strip_results_%POLL-ID% == \'function\') \r\n            strip_results_%POLL-ID%();\r\n    \r\n        if(typeof window.tabulate_answers_%POLL-ID% == \'function\') \r\n            tabulate_answers_%POLL-ID%();\r\n        \r\n        if(typeof window.tabulate_results_%POLL-ID% == \'function\') \r\n            tabulate_results_%POLL-ID%();\r\n        \r\n});\r\n\r\nfunction equalWidth_%POLL-ID%(obj, cols, findWidest ) {\r\n findWidest  = typeof findWidest  !== \'undefined\' ? findWidest  : false;\r\n    if ( findWidest ) {\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                width = parseInt(thisWidth / cols); \r\n                jQuery(this).width(width);    \r\n                jQuery(this).css(\'float\', \'left\');    \r\n        });\r\n    }\r\n    else {\r\n        var widest = 0;\r\n        obj.each(function() {\r\n                var thisWidth = jQuery(this).width();\r\n                if(thisWidth > widest) {\r\n                    widest = thisWidth; \r\n                }    \r\n        });\r\n        width = parseInt( widest / cols); \r\n        obj.width(width);    \r\n        obj.css(\'float\', \'left\');    \r\n    }    \r\n}\r\n\r\nfunction tabulate_answers_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID%\\" . '"' . "), %ANSWERS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-answer-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %ANSWERS-TABULATED-COLS%, true );\r\n}\r\n\r\nfunction tabulate_results_%POLL-ID%() {\r\n    equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID%\\" . '"' . "), %RESULTS-TABULATED-COLS% );\r\n        //equalWidth_%POLL-ID%( jQuery(\\" . '"' . "#yop-poll-container-%POLL-ID% .yop-poll-li-result-%POLL-ID% .yop-poll-results-bar-%POLL-ID% div \\" . '"' . "), %RESULTS-TABULATED-COLS%, true );\r\n}',
					`last_modified`                    = '" . current_time( 'mysql' ) . "' ";
					break;
			}
			$sql .= $wpdb->prepare( ' WHERE id = %d', $poll_id );
			$wpdb->query( $sql );
		}

		public function make_poll_from_request_data( $request = array(), $config = NULL ) {
			$this->poll['id']         = isset( $request['yop_poll_id'] ) ? trim( $request['yop_poll_id'] ) : NULL;
			$this->poll['name']       = isset( $request['yop_poll_name'] ) ? yop_poll_kses( trim( $request['yop_poll_name'] ) ) : NULL;
			$this->poll['question']   = isset( $request['yop_poll_question'] ) ? yop_poll_kses( trim( $request['yop_poll_question'] ) ) : NULL;
			$this->poll['start_date'] = isset( $request['yop_poll_options']['start_date'] ) ? trim( $request['yop_poll_options']['start_date'] ) : NULL;
			if ( !isset( $request['yop_poll_options']['never_expire'] ) ){
				$this->poll['end_date'] = isset( $request['yop_poll_options']['end_date'] ) ? trim( $request['yop_poll_options']['end_date'] ) : NULL;
			}
			else {
				$this->poll['end_date'] = '9999-12-31 23:59:59';
			}
			$this->poll['show_in_archive'] = isset( $request['yop_poll_options']['show_in_archive'] ) ? trim( $request['yop_poll_options']['show_in_archive'] ) : NULL;
			$this->poll['archive_order']   = isset( $request['yop_poll_options']['archive_order'] ) ? trim( $request['yop_poll_options']['archive_order'] ) : NULL;
		}

		public function make_answers_from_request_data( $request = array(), $config = NULL ) {
			$this->answers = NULL;
			$answer        = array( 'id' => NULL, 'poll_id' => $this->poll['id'], 'answer' => NULL, 'votes' => 0, 'status' => 'active', 'type' => 'default' );
			if ( isset ( $request['yop_poll_answer'] ) ){
				if ( count( $request['yop_poll_answer'] ) > 0 ){
					foreach ( $request['yop_poll_answer'] as $answer_id => $answer_value ) {
						$answer['answer'] = $answer_value;
						$answer['id']     = isset( $request['yop_poll_answer_ids'][$answer_id] ) ? $request['yop_poll_answer_ids'][$answer_id] : NULL;
						$answer['name']   = $answer_id;
						$this->answers[]  = $answer;
					}
				}
			}
			if ( isset ( $request['yop_poll_options']['allow_other_answers'] ) ){
				if ( 'yes' == $request['yop_poll_options']['allow_other_answers'] ){
					$answer['answer'] = isset( $request['yop_poll_options']['other_answers_label'] ) ? $request['yop_poll_options']['other_answers_label'] : 'Other';
					$other_answer     = self::get_poll_answers( $this->poll['id'], array( 'other' ) );
					$answer['id']     = isset( $other_answer[0]['id'] ) ? $other_answer[0]['id'] : NULL;
					$answer['type']   = 'other';
					$this->answers[]  = $answer;
				}
			}
		}

		public function make_custom_fields_from_request_data( $request = array(), $config = NULL ) {
			$this->custom_fields = NULL;
			if ( isset( $request['yop_poll_customfield'] ) ){
				if ( count( $request['yop_poll_customfield'] ) > 0 ){
					$custom_field = array( 'id' => NULL, 'poll_id' => $this->poll['id'], 'custom_field' => NULL, 'required' => NULL, 'status' => 'active' );
					foreach ( $request['yop_poll_customfield'] as $customfield_id => $customfield_value ) {
						$custom_field['custom_field'] = $customfield_value;
						$custom_field['id']           = isset( $request['yop_poll_customfield_ids'][$customfield_id] ) ? $request['yop_poll_customfield_ids'][$customfield_id] : NULL;
						$custom_field['required']     = isset( $request['yop_poll_customfield_required'][$customfield_id] ) ? 'yes' : 'no';
						$this->custom_fields[]        = $custom_field;
					}
				}
			}
		}

		public function add_poll_to_database( $request = array(), $config = NULL ) {
			if ( $this->verify_request_data( $request, $config ) ){
				$this->make_poll_from_request_data( $request, $config );
				$result = self::get_poll_from_database_by_name( $this->poll['name'] );
				if ( !isset( $result['id'] ) ){
					//inserting poll to db
					$this->poll['id'] = self::insert_poll_to_database( $this->poll );

					if ( isset( $request['yop_poll_options']['auto_generate_poll_page'] ) ){
						if ( 'yes' == $request['yop_poll_options']['auto_generate_poll_page'] ){
							$_p                   = array();
							$_p['post_title']     = $this->poll['name'];
							$_p['post_content']   = "[yop_poll id='" . $this->poll['id'] . "']";
							$_p['post_status']    = 'publish';
							$_p['post_type']      = 'page';
							$_p['comment_status'] = 'open';
							$_p['ping_status']    = 'open';
							$_p['post_category']  = array( 1 ); // the default 'Uncategorised'

							$poll_page_id = wp_insert_post( $_p );

							$request['yop_poll_options']['poll_page_url']               = get_permalink( $poll_page_id );
							$request['yop_poll_options']['has_auto_generate_poll_page'] = 'yes';
						}
					}

					//inserting poll options to db
					$poll_options    = array();
					$default_options = get_option( 'yop_poll_options', false );


					if ( isset( $request['yop_poll_options'] ) ){
						foreach ( $request['yop_poll_options'] as $option_name => $option_value ) {
							if ( $default_options ){
								if ( isset( $default_options[$option_name] ) ){
									if ( $default_options[$option_name] != $option_value ){
										$poll_options[$option_name] = $option_value;
									}
								}
							}
							else {
								$poll_options[$option_name] = $option_value;
							}
						}
					}
					//this is for checkbox options
					if ( !isset( $request['yop_poll_options']['never_expire'] ) ){
						$poll_options['never_expire'] = 'no';
					}

					if ( isset( $request['yop_poll_options']['schedule_reset_poll_date'] ) ){
						$poll_options['schedule_reset_poll_date'] = strtotime( $request['yop_poll_options']['schedule_reset_poll_date'] );
					}
					else {
						$poll_options['schedule_reset_poll_date'] = current_time( 'timestamp' );
					}

					if ( 'yes' == $request['yop_poll_options']['schedule_reset_poll_stats'] ){
						$default_options['start_scheduler'] = 'yes';
					}
					else {
						$change_start_scheduler_to_no = true;
						$yop_polls                    = self::get_yop_polls_fields( array( 'id' ) );
						if ( count( $yop_polls ) > 0 ){
							foreach ( $yop_polls as $yop_poll_id ) {
								if ( $yop_poll_id['id'] != $this->poll['id'] ){
									$yop_poll_options = get_yop_poll_meta( $yop_poll_id['id'], 'options', true );
									if ( isset( $yop_poll_options['schedule_reset_poll_stats'] ) && 'yes' == $yop_poll_options['schedule_reset_poll_stats'] ){
										$change_start_scheduler_to_no = false;
									}
								}
							}
						}
						if ( $change_start_scheduler_to_no ){
							$default_options['start_scheduler'] = 'no';
						}
						else {
							$default_options['start_scheduler'] = 'yes';
						}
					}
					update_option( 'yop_poll_options', $default_options );

					//if ( count( $poll_options ) > 0 )
					update_yop_poll_meta( $this->poll['id'], 'options', $poll_options );

					//inserting answers to db
					foreach ( $default_options as $option_name => $option_value ) {
						if ( isset( $poll_options[$option_name] ) ){
							if ( $option_name != 'use_template_bar' ){
								$default_options[$option_name] = $poll_options[$option_name];
							}
						}
					}
					$this->make_answers_from_request_data( $request, $config );
					if ( count( $this->answers ) > 0 ){
						foreach ( $this->answers as $answer ) {
							$answer_id = self::insert_answer_to_database( $answer );

							//insert poll answer options to db
							if ( 'other' != $answer['type'] ){
								if ( isset( $request['yop_poll_answer_options'][$answer['name']] ) ){
									$poll_answer_options = array();
									foreach ( $request['yop_poll_answer_options'][$answer['name']] as $option_name => $option_value ) {
										if ( isset( $poll_options[$option_name] ) && $poll_options[$option_name] != $option_value ){
											$poll_answer_options[$option_name] = $option_value;
										}
										elseif ( $default_options[$option_name] != $option_value ) {
											$poll_answer_options[$option_name] = $option_value;
										}
										if ( $option_name == 'is_default_answer' ){
											$poll_answer_options[$option_name] = $option_value;
										}
									}
									//if ( count( $poll_answer_options ) > 0 )
									update_yop_poll_answer_meta( $answer_id, 'options', $poll_answer_options, false );
								}
							}
						}
					}

					//inserting custom fields to db
					$this->make_custom_fields_from_request_data( $request, $config );
					if ( count( $this->custom_fields ) > 0 ){
						foreach ( $this->custom_fields as $custom_field ) {
							self::insert_custom_field_to_database( $custom_field );
						}
					}

					return $this->poll['id'];
				}
				else {
					$this->error = __( 'This poll already exists! Please choose another name!', 'yop_poll' );
					return false;
				}
			}
			else {
				return false;
			}
		}

		public function edit_poll_in_database( $request = array(), $config = NULL ) {
			if ( $this->verify_request_data( $request, $config ) ){
				$this->make_poll_from_request_data( $request, $config );

				$result = self::get_poll_from_database_by_id( $this->poll['id'] );
				if ( isset( $result['id'] ) ){
					//update poll in db

					self::update_poll_in_database( $this->poll );

					$poll_old_options = get_yop_poll_meta( $this->poll['id'], 'options', true );

					if ( !isset( $poll_old_options['has_auto_generate_poll_page'] ) ){
						$poll_old_options['has_auto_generate_poll_page'] = 'no';
					}
					if ( !isset( $request['yop_poll_options']['auto_generate_poll_page'] ) ){
						$request['yop_poll_options']['auto_generate_poll_page'] = 'no';
					}

					if ( 'yes' != $poll_old_options['has_auto_generate_poll_page'] ){
						if ( 'yes' == $request['yop_poll_options']['auto_generate_poll_page'] ){
							$_p                   = array();
							$_p['post_title']     = $this->poll['name'];
							$_p['post_content']   = "[yop_poll id='" . $this->poll['id'] . "']";
							$_p['post_status']    = 'publish';
							$_p['post_type']      = 'page';
							$_p['comment_status'] = 'open';
							$_p['ping_status']    = 'open';
							$_p['post_category']  = array( 1 ); // the default 'Uncategorised'

							$poll_page_id = wp_insert_post( $_p );

							$request['yop_poll_options']['poll_page_url']               = get_permalink( $poll_page_id );
							$request['yop_poll_options']['has_auto_generate_poll_page'] = 'yes';
						}
					}

					//update poll options in db
					$poll_options    = array();
					$default_options = get_option( 'yop_poll_options', false );


					if ( isset( $request['yop_poll_options'] ) ){
						foreach ( $request['yop_poll_options'] as $option_name => $option_value ) {
							if ( $default_options ){
								if ( isset ( $default_options[$option_name] ) ){
									if ( $default_options[$option_name] != $option_value ){
										$poll_options[$option_name] = $option_value;
									}
								}
							}
							else {
								$poll_options[$option_name] = $option_value;
							}
						}
					}
					//this is for checkbox options
					if ( !isset( $request['yop_poll_options']['never_expire'] ) ){
						$poll_options['never_expire'] = 'no';
					}

					if ( isset( $request['yop_poll_options']['schedule_reset_poll_date'] ) ){
						$poll_options['schedule_reset_poll_date'] = strtotime( $request['yop_poll_options']['schedule_reset_poll_date'] );
					}
					else {
						$poll_options['schedule_reset_poll_date'] = current_time( 'timestamp' );
					}

					if ( 'yes' == $request['yop_poll_options']['schedule_reset_poll_stats'] ){
						$default_options['start_scheduler'] = 'yes';
					}
					else {
						$change_start_scheduler_to_no = true;
						$yop_polls                    = self::get_yop_polls_fields( array( 'id' ) );
						if ( count( $yop_polls ) > 0 ){
							foreach ( $yop_polls as $yop_poll_id ) {
								if ( $yop_poll_id['id'] != $this->poll['id'] ){
									$yop_poll_options = get_yop_poll_meta( $yop_poll_id['id'], 'options', true );
									if ( isset( $yop_poll_options['schedule_reset_poll_stats'] ) && 'yes' == $yop_poll_options['schedule_reset_poll_stats'] ){
										$change_start_scheduler_to_no = false;
									}
								}
							}
						}
						if ( $change_start_scheduler_to_no ){
							$default_options['start_scheduler'] = 'no';
						}
						else {
							$default_options['start_scheduler'] = 'yes';
						}
					}
					update_option( 'yop_poll_options', $default_options );

					//if ( count( $poll_options ) > 0 )
					update_yop_poll_meta( $this->poll['id'], 'options', $poll_options );

					//add update answers in db
					foreach ( $default_options as $option_name => $option_value ) {
						if ( isset( $poll_options[$option_name] ) ){
							if ( $option_name != 'use_template_bar' ){
								$default_options[$option_name] = $poll_options[$option_name];
							}
						}
					}

					$this->make_answers_from_request_data( $request, $config );
					if ( count( $this->answers ) > 0 ){
						$answer_ids_for_not_remove = array();
						$all_poll_answers          = self::get_poll_answers( $this->poll['id'], array( 'default', 'other' ) );
						foreach ( $this->answers as $answer ) {
							if ( $answer['id'] ){
								self::update_answer_in_database( $answer );
								$answer_id = $answer['id'];
							}
							else {
								$answer_id = self::insert_answer_to_database( $answer );
							}
							//if( 'other' != $answer['type'] )
							$answer_ids_for_not_remove[] = $answer_id;

							//insert poll answer options to db
							if ( 'other' != $answer['type'] ){
								if ( isset( $request['yop_poll_answer_options'][$answer['name']] ) ){
									$poll_answer_options = array();
									foreach ( $request['yop_poll_answer_options'][$answer['name']] as $option_name => $option_value ) {
										if ( isset( $poll_options[$option_name] ) && $poll_options[$option_name] != $option_value ){
											$poll_answer_options[$option_name] = $option_value;
										}
										elseif ( $default_options[$option_name] != $option_value ) {
											$poll_answer_options[$option_name] = $option_value;
										}
										if ( $option_name == 'is_default_answer' ){
											$poll_answer_options[$option_name] = $option_value;
										}
									}
									//if ( count( $poll_answer_options ) > 0 ) {
									/*if ( isset( $request['yop_poll_options']['use_template_bar'] ) ) {
									if ( $request['yop_poll_options']['use_template_bar'] == 'yes' ) {
									if ( isset( $poll_answer_options[ 'use_template_bar' ] ) )
									$poll_answer_options[ 'use_template_bar' ]    = 'yes';
									}
									}*/
									update_yop_poll_answer_meta( $answer_id, 'options', $poll_answer_options, false );
								}
							}
						}
						//deleting removed answers
						if ( count( $all_poll_answers ) > 0 ){
							foreach ( $all_poll_answers as $answer ) {
								if ( !in_array( $answer['id'], $answer_ids_for_not_remove ) ){
									self::delete_poll_answers_from_db( $answer['id'], $this->poll['id'] );
									delete_yop_poll_answer_meta( $answer['id'], 'options' );
								}
							}
						}
					}

					//update insert custom fields in db
					$this->make_custom_fields_from_request_data( $request, $config );
					if ( count( $this->custom_fields ) > 0 ){
						$customfield_ids_for_not_remove = array();
						$all_poll_customfields          = self::get_poll_customfields( $this->poll['id'] );
						foreach ( $this->custom_fields as $custom_field ) {
							if ( $custom_field['id'] ){
								self::update_custom_field_in_database( $custom_field );
								$custom_field_id = $custom_field['id'];
							}
							else {
								$custom_field_id = self::insert_custom_field_to_database( $custom_field );
							}
							$customfield_ids_for_not_remove[] = $custom_field_id;
						}
						//deleting removed custom_fields
						if ( count( $all_poll_customfields ) > 0 ){
							foreach ( $all_poll_customfields as $customfield ) {
								if ( !in_array( $customfield['id'], $customfield_ids_for_not_remove ) ){
									self::delete_poll_customfields_from_db( $customfield['id'], $this->poll['id'] );
								}
							}
						}
					}
					else {
						self::delete_all_poll_customfields_from_db( $this->poll['id'] );
					}

					return $this->poll['id'];
				}
				else {
					$this->error = __( 'This poll doesn`t exist!', 'yop_poll' );
					return false;
				}
			}
			else {
				return false;
			}
		}

		public function get_current_poll() {
			return $this->poll;
		}

		public static function get_poll_answers( $poll_id, $types = array( 'default' ), $order = 'id', $order_dir = '', $include_others = false, $percentages_decimals = 0 ) {
			global $wpdb;

			if ( $include_others ){
				$types = array_diff( $types, array( 'other' ) );
			}

			$type_sql = '';
			if ( count( $types ) > 0 ){
				$type_sql .= ' AND type in (';
				foreach ( $types as $type ) {
					$type_sql .= "'" . $type . "',";
				}
				$type_sql = trim( $type_sql, ',' );
				$type_sql .= ' ) ';
			}
			$is_votes_sort = false;
			if ( 'votes' == $order ){
				$order         = 'id';
				$is_votes_sort = true;
			}
			$answers = $wpdb->get_results( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_answers . "
					WHERE poll_id = %d " . $type_sql . "
					ORDER BY " . $order . " " . $order_dir, $poll_id ), ARRAY_A );

			if ( $include_others ){
				$other_answer_details = $wpdb->get_row( $wpdb->prepare( "
						SELECT *
						FROM " . $wpdb->yop_poll_answers . "
						WHERE poll_id = %d AND type = 'other' ", $poll_id ), ARRAY_A );

				$other_answers_values = self::get_other_answers_votes( $other_answer_details['id'] );
				if ( count( $other_answers_values ) > 0 ){
					if ( 'id' == $order && 'desc' == $order_dir ){
						$interval = range( count( $other_answers_values ) - 1, 0, -1 );
					}
					else {
						$interval = range( 0, count( $other_answers_values ) - 1, 1 );
					}
					for ( $i = 0; $i < count( $other_answers_values ); $i++ ) {
						$answers[] = array( 'id' => $other_answer_details['id'], 'poll_id' => $poll_id, 'answer' => $other_answers_values[$interval[$i]]['other_answer_value'], 'votes' => $other_answers_values[$interval[$i]]['votes'], 'status' => 'active', 'type' => 'other' );
					}
				}
				else {
					$answers[] = $other_answer_details;
				}
			}
			$total_votes = self::get_sum_poll_votes( $poll_id );
			if ( count( $answers ) > 0 ){
				for ( $i = 0; $i < count( $answers ); $i++ ) {
					if ( 0 == intval( $total_votes ) ){
						$answers[$i]['procentes'] = 0;
					}
					else {
						$answers[$i]['procentes'] = round( ( intval( $answers[$i]['votes'] ) / intval( $total_votes ) * 100 ), $percentages_decimals );
						if ( 0 < $answers[$i]['procentes'] ){
							$answers[$i]['procentes'] = number_format( $answers[$i]['procentes'], $percentages_decimals );
						}
					}
				}
			}

			if ( $is_votes_sort ){
				$order_dir = ( '' == $order_dir ) ? 'asc' : $order_dir;
				usort( $answers, array( 'Yop_Poll_Model', "sort_answers_by_votes_" . $order_dir . "_callback" ) );
			}
			if ( $include_others ){
				if ( 'answer' == $order ){
					$order_dir = ( '' == $order_dir ) ? 'asc' : $order_dir;
					usort( $answers, array( 'Yop_Poll_Model', "sort_answers_alphabetical_" . $order_dir . "_callback" ) );
				}

				if ( 'rand()' == $order ){
					$interval = range( 0, count( $answers ) - 1, 1 );
					shuffle( $interval );
					$new_answers = array();
					foreach ( $interval as $number ) {
						$new_answers[] = $answers[$number];
					}
					$answers = $new_answers;
				}
			}
			return $answers;
		}

		public static function get_count_poll_answers( $poll_id, $types = array( 'default' ), $include_others = false ) {
			global $wpdb;

			$answers_no       = 0;
			$other_answers_no = 0;

			if ( $include_others ){
				$types = array_diff( $types, array( 'other' ) );
			}

			$type_sql = '';
			if ( count( $types ) > 0 ){
				$type_sql .= ' AND type in (';
				foreach ( $types as $type ) {
					$type_sql .= "'" . $type . "',";
				}
				$type_sql = trim( $type_sql, ',' );
				$type_sql .= ' ) ';
			}

			$answers_no = $wpdb->get_var( $wpdb->prepare( "
					SELECT count(*)
					FROM " . $wpdb->yop_poll_answers . "
					WHERE poll_id = %d " . $type_sql, $poll_id ) );

			if ( $include_others ){
				$other_answer_details = $wpdb->get_row( $wpdb->prepare( "
						SELECT *
						FROM " . $wpdb->yop_poll_answers . "
						WHERE poll_id = %d AND type = 'other' ", $poll_id ), ARRAY_A );

				$other_answers_no = count( self::get_other_answers_votes( $other_answer_details['id'] ) );
			}

			return $answers_no + $other_answers_no;
		}

		public static function get_poll_answer_by_id( $answer_id ) {
			global $wpdb;
			$answer = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_answers . "
					WHERE id = %d
					LIMIT 0,1", $answer_id ), ARRAY_A );
			return $answer;
		}

		private static function get_poll_answer_by_field( $poll_id, $field_name, $field_value, $field_type = '%s' ) {
			$answer = $GLOBALS['wpdb']->get_row( $GLOBALS['wpdb']->prepare( "
					SELECT *
					FROM {$GLOBALS['wpdb']->yop_poll_answers}
					WHERE {$field_name} = {$field_type} AND
					poll_id    = %d
					LIMIT 0,1", $field_value, $poll_id ), ARRAY_A );
			return $answer;
		}

		public static function get_poll_customfields( $poll_id ) {
			global $wpdb;
			$result = $wpdb->get_results( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_custom_fields . "
					WHERE poll_id = %d
					", $poll_id ), ARRAY_A );
			return $result;
		}

		public static function get_customfield_by_id( $customfield_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_custom_fields . "
					WHERE id = %d
					", $customfield_id ), ARRAY_A );
			return $result;
		}

		public static function get_poll_customfields_logs( $poll_id, $orderby = 'vote_date', $order = 'desc', $offset = 0, $per_page = 99999999, $sdate = '', $edate = '' ) {
			global $wpdb;
			$sdatesql = '';
			$edatesql = '';
			if ( $sdate != '' ){
				$sdatesql = $wpdb->prepare( ' AND vote_date >= %s ', $sdate . ' 00:00:00 ' );
			}
			if ( $edate != '' ){
				$edatesql = $wpdb->prepare( ' AND vote_date <= %s ', $edate . ' 23:59:59 ' );
			}
			$result = $wpdb->get_results( $wpdb->prepare( "
					SELECT group_concat( CONCAT( `custom_field_value`, '<#!->', `custom_field_id` ) SEPARATOR '<#!,>' ) as vote_log, vote_id, vote_date, user_id, id, tr_id
					FROM " . $wpdb->yop_poll_votes_custom_fields . "
					WHERE poll_id = %d " . $sdatesql . $edatesql . "GROUP BY vote_id
					ORDER BY " . esc_attr( $orderby ) . " " . esc_attr( $order ) . "
					LIMIT %d, %d
					", $poll_id, $offset, $per_page ), ARRAY_A );
			return $result;
		}

		public static function get_poll_total_customfields_logs( $poll_id, $sdate = '', $edate = '' ) {
			global $wpdb;
			$sdatesql = '';
			$edatesql = '';
			if ( $sdate != '' ){
				$sdatesql = $wpdb->prepare( ' AND vote_date >= %s ', $sdate . ' 00:00:00 ' );
			}
			if ( $edate != '' ){
				$edatesql = $wpdb->prepare( ' AND vote_date <= %s ', $edate . ' 23:59:59 ' );
			}
			$wpdb->query( $wpdb->prepare( "
					SELECT count(*)
					FROM " . $wpdb->yop_poll_votes_custom_fields . "
					WHERE poll_id = %d " . $sdatesql . $edatesql . "GROUP BY vote_id
					", $poll_id ) );
			return $wpdb->get_var( 'SELECT FOUND_ROWS()' );
		}

		public static function get_poll_from_database_by_name( $poll_name ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_polls . "
					WHERE name = %s
					LIMIT 0,1
					", $poll_name ), ARRAY_A );
			return $result;
		}

		public static function get_default_template() {
			global $wpdb;
			$result = $wpdb->get_row( "
				SELECT *
				FROM " . $wpdb->yop_poll_templates . "
				WHERE status = 'default'
				LIMIT 0,1
				", ARRAY_A );
			return $result;
		}

		public static function get_poll_template_from_database_by_name( $template_name, $exclude_ids = array() ) {
			global $wpdb;
			$exclude_sql = '';
			if ( count( $exclude_ids ) > 0 ){
				$exclude_sql .= 'AND id NOT IN(';
				foreach ( $exclude_ids as $id ) {
					$exclude_sql .= $id . ',';
				}
				$exclude_sql = trim( $exclude_sql, ',' );
				$exclude_sql .= ')';
			}
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_templates . "
					WHERE name = %s " . $exclude_sql, $template_name ), ARRAY_A );
			return $result;
		}

		public static function count_poll_from_database_by_name( $poll_name ) {
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare( "
					SELECT count(*)
					FROM " . $wpdb->yop_polls . "
					WHERE name = %s
					", $poll_name ) );
			return $result;
		}

		public static function count_poll_from_database_like_name( $poll_name ) {
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare( "
					SELECT count(*)
					FROM " . $wpdb->yop_polls . "
					WHERE name like %s
					", $poll_name . '%' ) );
			return $result;
		}

		public static function get_poll_from_database_by_id( $poll_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_polls . "
					WHERE id = %d
					LIMIT 0,1
					", $poll_id ), ARRAY_A );
			return $result;
		}

		public static function get_poll_field_from_database_by_id( $field = '', $poll_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_polls . "
					WHERE id = %d
					LIMIT 0,1
					", $poll_id ), ARRAY_A );
			if ( isset( $result[$field] ) ){
				return $result[$field];
			}
			return false;
		}

		public static function get_poll_template_field_from_database_by_id( $field = '', $template_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_templates . "
					WHERE id = %d
					LIMIT 0,1
					", $template_id ), ARRAY_A );
			if ( isset( $result[$field] ) ){
				return $result[$field];
			}
			return false;
		}

		public static function get_poll_log_field_from_database_by_id( $field = '', $log_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_logs . "
					WHERE id = %d
					LIMIT 0,1
					", $log_id ), ARRAY_A );
			if ( isset( $result[$field] ) ){
				return $result[$field];
			}
			return false;
		}

		public static function get_poll_log_field_from_database_by_vote_id( $field = '', $vote_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_logs . "
					WHERE vote_id = %s
					LIMIT 0,1
					", $vote_id ), ARRAY_A );
			if ( isset( $result[$field] ) ){
				return $result[$field];
			}
			return false;
		}

		private static function insert_poll_to_database( $poll ) {
			global $wpdb;
			global $current_user;
			wp_get_current_user();
			$wpdb->query( $wpdb->prepare( "
					INSERT INTO " . $wpdb->yop_polls . "
					SET
					poll_author = %d,
					name = %s,
					question = %s,
					start_date = %s,
					end_date = %s,
					total_votes = %s,
					total_answers = %s,
					status = %s,
					date_added = %s,
					last_modified = %s,
					show_in_archive = %s,
					archive_order  = %d
					", $current_user->ID, $poll['name'], $poll['question'], $poll['start_date'], $poll['end_date'], $poll['total_votes'], $poll['total_answers'], $poll['status'], current_time( 'mysql' ), current_time( 'mysql' ), $poll['show_in_archive'], $poll['archive_order'] ) );
			return $wpdb->insert_id;
		}

		private static function update_poll_in_database( $poll ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE " . $wpdb->yop_polls . "
					SET name = %s,
					question = %s,
					start_date = %s,
					end_date = %s,
					last_modified = %s,
					show_in_archive = %s,
					archive_order  = %d
					WHERE
					id = %d
					", $poll['name'], $poll['question'], $poll['start_date'], $poll['end_date'], current_time( 'mysql' ), $poll['show_in_archive'], $poll['archive_order'], $poll['id'] ) );
		}

		private static function insert_answer_to_database( $answer ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					INSERT INTO " . $wpdb->yop_poll_answers . "
					SET poll_id = %d,
					answer = %s,
					votes = %d,
					status = %s,
					type = %s
					",
					$answer['poll_id'], yop_poll_kses( $answer['answer'] ), $answer['votes'], $answer['status'], $answer['type'] ) );
			return $wpdb->insert_id;
		}

		private static function get_answer_from_database( $answer_id ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_answers . "
					WHERE id = %d
					LIMIT 0,1
					", $answer_id ), ARRAY_A );
			return $result;
		}

		private static function update_answer_in_database( $answer ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE " . $wpdb->yop_poll_answers . "
					SET answer = %s
					WHERE id = %d
					", yop_poll_kses( $answer['answer'] ), $answer['id'] ) );
		}

		public static function get_archive_polls( $orderby = 'archive_order', $order = 'asc', $offset = 0, $per_page = 99999 ) {
			global $wpdb;
			$archive = $wpdb->get_results( $wpdb->prepare( "
					SELECT id
					FROM " . $wpdb->yop_polls . "
					WHERE
					show_in_archive    = 'yes'
					ORDER BY " . esc_attr( $orderby ) . " " . esc_attr( $order ) . "
					LIMIT %d, %d
					", $offset, $per_page ), ARRAY_A );
			return $archive;
		}

		public static function get_current_active_poll( $offset = 0 ) {
			global $wpdb;
			$current_date = self::get_mysql_curent_date();
			return $wpdb->get_row( $wpdb->prepare( "
					SELECT * FROM " . $wpdb->yop_polls . "
					WHERE
					%s    >= start_date AND
					%s  <= end_date
					ORDER BY
					date_added ASC
					", $current_date, $current_date ), ARRAY_A, $offset );
		}

		public static function get_latest_closed_poll( $offset = 0 ) {
			global $wpdb;
			$current_date = self::get_mysql_curent_date();
			$result       = $wpdb->get_row( $wpdb->prepare( "
					SELECT * FROM " . $wpdb->yop_polls . "
					WHERE
					%s  >= end_date
					ORDER BY
					end_date DESC
					", $current_date ), ARRAY_A, $offset );
			return $result;
		}

		public static function get_yop_polls_filter_search( $orderby = 'id', $order = 'desc', $filter = array( 'field' => NULL, 'value' => NULL, 'operator' => '=' ), $search = array( 'fields' => array(), 'value' => NULL ) ) {
			global $wpdb;
			$sql        = "SELECT * FROM " . $wpdb->yop_polls;
			$sql_filter = '';
			$sql_search = '';
			if ( $filter['field'] && $filter['value'] ){
				$sql_filter .= $wpdb->prepare( ' `' . esc_attr( $filter['field'] ) . '` ' . esc_attr( $filter['operator'] ) . ' %s ', esc_attr( $filter['value'] ) );
			}
			if ( count( $search['fields'] ) > 0 ){
				if ( $filter['field'] && $filter['value'] ){
					$sql_search = ' AND ';
				}
				$sql_search .= ' ( ';
				foreach ( $search['fields'] as $field ) {
					$sql_search .= $wpdb->prepare( ' `' . esc_attr( $field ) . '` like \'%%%s%%\' OR', $search['value'] );
				}
				$sql_search = trim( $sql_search, 'OR' );
				$sql_search .= ' ) ';

			}
			if ( ( $filter['field'] && $filter['value'] ) || count( $search['fields'] ) > 0 ){
				$sql .= ' WHERE ' . $sql_filter . $sql_search;
			}
			$sql .= ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order );
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function get_yop_polls_fields( $fields = array( 'id' ) ) {
			global $wpdb;
			$fields_text = '';
			if ( count( $fields ) > 0 ){
				foreach ( $fields as $field ) {
					$fields_text .= $field . ', ';
				}
			}
			$fields_text = trim( $fields_text, ', ' );
			if ( $fields_text == '' ){
				$fields_text = 'id';
			}
			$sql = 'SELECT ' . $fields_text . ' FROM ' . $wpdb->yop_polls;
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function make_logs_filter_search_sql( $search = array( 'fields' => array(), 'value' => NULL ), $poll_id = NULL, $sdate = '', $edate = '' ) {
			global $wpdb;

			$sql_search = ' ';

			if ( $poll_id ){
				$sql_search .= $wpdb->prepare( 'WHERE poll_id = %d', $poll_id );
			}
			if ( '_Anonymous' == $search['value'] ){
				if ( $poll_id ){
					$sql_search .= ' AND  ';
				}
				else {
					$sql_search .= ' WHERE ';
				}
				$sql_search .= 'user_id=0 ';
			}
			else {
				if ( count( $search['fields'] ) > 0 ){
					if ( $poll_id ){
						$sql_search .= ' AND ( ';
					}
					else {
						$sql_search .= ' WHERE (';
					}
					foreach ( $search['fields'] as $field ) {
						$sql_search .= $wpdb->prepare( ' ' . esc_attr( $field ) . ' like \'%%%s%%\' OR', $search['value'] );
					}
					$sql_search = trim( $sql_search, 'OR' );
					$sql_search .= ' ) ';
				}
			}

			if ( $sdate != '' ){
				if ( '' == trim( $sql_search ) ){
					$sql_search .= $wpdb->prepare( ' WHERE vote_date >= %s ', $sdate . ' 00:00:00 ' );
				}
				else {
					$sql_search .= $wpdb->prepare( ' AND vote_date >= %s ', $sdate . ' 00:00:00 ' );
				}
			}
			if ( $edate != '' ){
				if ( '' == trim( $sql_search ) ){
					$sql_search .= $wpdb->prepare( ' WHERE vote_date <= %s ', $edate . ' 23:59:59 ' );
				}
				else {
					$sql_search .= $wpdb->prepare( ' AND vote_date <= %s ', $edate . ' 23:59:59 ' );
				}
			}
			return $sql_search;
		}

		public static function get_logs_filter_search( $orderby = 'id', $order = 'desc', $search = array( 'fields' => array(), 'value' => NULL ), $poll_id = NULL, $offset = 0, $per_page = 99999999, $sdate = '', $edate = '' ) {
			global $wpdb;

			if ( 'id' == $orderby ){
				$orderby = $wpdb->yop_poll_logs . ".id";
			}

			$sql_search = self::make_logs_filter_search_sql( $search, $poll_id, $sdate, $edate );

			$sql = "
			SELECT
			id,
			vote_id,
			poll_id,
			ip,
			http_referer,
			tr_id,
			vote_date,
			other_answer_value,
			name,
			user_nicename,
			user_email,
			user_id,
			user_from,
			IFNULL( answer , '_REMOVED' ) as answer
			FROM (
			SELECT
			" . $wpdb->yop_poll_logs . ".id,
			" . $wpdb->yop_poll_logs . ".vote_id,
			" . $wpdb->yop_poll_logs . ".poll_id,
			" . $wpdb->yop_poll_logs . ".ip,
			" . $wpdb->yop_poll_logs . ".http_referer,
			" . $wpdb->yop_poll_logs . ".tr_id,
			" . $wpdb->yop_poll_logs . ".vote_date,
			" . $wpdb->yop_poll_logs . ".other_answer_value,
			" . $wpdb->yop_polls . ".name,
			IF (" . $wpdb->yop_poll_answers . ".answer = 'Other' , concat( 'Other - ', " . $wpdb->yop_poll_logs . ".other_answer_value ), " . $wpdb->yop_poll_answers . ".answer ) as answer,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".name ,IFNULL( " . $wpdb->users . ".display_name, '" . __( '_Anonymous', 'yop_poll' ) . "' )  ) as user_nicename,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".email , " . $wpdb->users . ".user_email ) as user_email,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', 'facebook' , 'wordpress' ) as user_from,
			" . $wpdb->yop_poll_logs . ".user_id

			FROM " . $wpdb->yop_poll_logs . "
			LEFT JOIN (" . $wpdb->yop_polls . ',' . $wpdb->yop_poll_answers . ")
			ON (
			" . $wpdb->yop_poll_logs . ".poll_id = " . $wpdb->yop_polls . ".id AND
			" . $wpdb->yop_poll_logs . ".answer_id = " . $wpdb->yop_poll_answers . ".id
			)
			LEFT JOIN (" . $wpdb->users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->users . ".ID
			)
			LEFT JOIN (" . $wpdb->yop_poll_facebook_users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->yop_poll_facebook_users . ".id
			)
			";
			$sql .= ' ) as logs  ';
			$sql .= $sql_search;
			$sql .= ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order );
			$sql .= $wpdb->prepare( ' LIMIT %d, %d ', $offset, $per_page );
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function get_group_logs_filter_search( $orderby = 'id', $order = 'desc', $search = array( 'fields' => array(), 'value' => NULL ), $poll_id = NULL, $offset = 0, $per_page = 99999999, $sdate = '', $edate = '' ) {
			global $wpdb;

			$sql_search = self::make_logs_filter_search_sql( $search, $poll_id, $sdate, $edate );

			$sql = "
			SELECT
			id,
			vote_id,
			poll_id,
			ip,
			http_referer,
			tr_id,
			vote_date,
			other_answer_value,
			name,
			user_nicename,
			user_email,
			user_id,
			user_from,
			IFNULL( GROUP_CONCAT(DISTINCT answer SEPARATOR ', '), '_REMOVED' ) as answer
			FROM (
			SELECT
			" . $wpdb->yop_poll_logs . ".id,
			" . $wpdb->yop_poll_logs . ".vote_id,
			" . $wpdb->yop_poll_logs . ".poll_id,
			" . $wpdb->yop_poll_logs . ".ip,
			" . $wpdb->yop_poll_logs . ".http_referer,
			" . $wpdb->yop_poll_logs . ".tr_id,
			" . $wpdb->yop_poll_logs . ".vote_date,
			" . $wpdb->yop_poll_logs . ".other_answer_value,
			" . $wpdb->yop_polls . ".name,
			IF (" . $wpdb->yop_poll_answers . ".answer = 'Other' , concat( 'Other - ', " . $wpdb->yop_poll_logs . ".other_answer_value ), " . $wpdb->yop_poll_answers . ".answer ) as answer,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".name ,IFNULL( " . $wpdb->users . ".display_name, '" . __( '_Anonymous', 'yop_poll' ) . "' )  ) as user_nicename,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".email , " . $wpdb->users . ".user_email ) as user_email,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', 'facebook' , 'wordpress' ) as user_from,
			" . $wpdb->yop_poll_logs . ".user_id

			FROM " . $wpdb->yop_poll_logs . "
			LEFT JOIN (" . $wpdb->yop_polls . ',' . $wpdb->yop_poll_answers . ")
			ON (
			" . $wpdb->yop_poll_logs . ".poll_id = " . $wpdb->yop_polls . ".id AND
			" . $wpdb->yop_poll_logs . ".answer_id = " . $wpdb->yop_poll_answers . ".id
			)
			LEFT JOIN (" . $wpdb->users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->users . ".ID
			)
			LEFT JOIN (" . $wpdb->yop_poll_facebook_users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->yop_poll_facebook_users . ".id
			)
			";
			$sql .= ' ) as logs  ';
			$sql .= $sql_search;
			$sql .= ' GROUP BY vote_id';
			$sql .= ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order );
			$sql .= $wpdb->prepare( ' LIMIT %d, %d ', $offset, $per_page );

			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function get_total_logs_filter_search( $search = array( 'fields' => array(), 'value' => NULL ), $poll_id = NULL, $sdate = '', $edate = '' ) {
			global $wpdb;

			$sql_search = self::make_logs_filter_search_sql( $search, $poll_id, $sdate, $edate );

			$sql = "
			SELECT
			count(*)
			FROM
			(
			SELECT
			" . $wpdb->yop_poll_logs . ".id,
			" . $wpdb->yop_poll_logs . ".vote_id,
			" . $wpdb->yop_poll_logs . ".poll_id,
			" . $wpdb->yop_poll_logs . ".ip,
			" . $wpdb->yop_poll_logs . ".http_referer,
			" . $wpdb->yop_poll_logs . ".tr_id,
			" . $wpdb->yop_poll_logs . ".vote_date,
			" . $wpdb->yop_poll_logs . ".other_answer_value,
			" . $wpdb->yop_poll_logs . ".user_type,
			" . $wpdb->yop_polls . ".name,
			IF (" . $wpdb->yop_poll_answers . ".answer = 'Other' , concat( 'Other - ', " . $wpdb->yop_poll_logs . ".other_answer_value ), " . $wpdb->yop_poll_answers . ".answer ) as answer,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".name ,IFNULL( " . $wpdb->users . ".display_name, '" . __( 'Anonymous', 'yop_poll' ) . "' )  ) as user_nicename,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".email , " . $wpdb->users . ".user_email ) as user_email,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', 'facebook' , 'wordpress' ) as user_from,
			" . $wpdb->yop_poll_logs . ".user_id

			FROM " . $wpdb->yop_poll_logs . "
			LEFT JOIN (" . $wpdb->yop_polls . ',' . $wpdb->yop_poll_answers . ")
			ON (
			" . $wpdb->yop_poll_logs . ".poll_id = " . $wpdb->yop_polls . ".id AND
			" . $wpdb->yop_poll_logs . ".answer_id = " . $wpdb->yop_poll_answers . ".id
			)
			LEFT JOIN (" . $wpdb->users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->users . ".ID
			)
			LEFT JOIN (" . $wpdb->yop_poll_facebook_users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->yop_poll_facebook_users . ".id
			)
			";
			$sql .= ' ) as logs  ';
			$sql .= $sql_search;
			return $wpdb->get_var( $sql );
		}

		public static function get_total_group_logs_filter_search( $search = array( 'fields' => array(), 'value' => NULL ), $poll_id = NULL, $sdate = '', $edate = '' ) {
			global $wpdb;

			$sql_search = self::make_logs_filter_search_sql( $search, $poll_id, $sdate, $edate );

			$sql = "
			SELECT
			count(*)
			FROM
			(
			SELECT
			count(*)
			FROM
			(
			SELECT
			" . $wpdb->yop_poll_logs . ".id,
			" . $wpdb->yop_poll_logs . ".vote_id,
			" . $wpdb->yop_poll_logs . ".poll_id,
			" . $wpdb->yop_poll_logs . ".ip,
			" . $wpdb->yop_poll_logs . ".http_referer,
			" . $wpdb->yop_poll_logs . ".tr_id,
			" . $wpdb->yop_poll_logs . ".vote_date,
			" . $wpdb->yop_poll_logs . ".other_answer_value,
			" . $wpdb->yop_poll_logs . ".user_type,
			" . $wpdb->yop_polls . ".name,
			IF (" . $wpdb->yop_poll_answers . ".answer = 'Other' , concat( 'Other - ', " . $wpdb->yop_poll_logs . ".other_answer_value ), " . $wpdb->yop_poll_answers . ".answer ) as answer,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".name ,IFNULL( " . $wpdb->users . ".display_name, '" . __( '_Anonymous', 'yop_poll' ) . "' )  ) as user_nicename,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', " . $wpdb->yop_poll_facebook_users . ".email , " . $wpdb->users . ".user_email ) as user_email,
			IF( " . $wpdb->yop_poll_logs . ".user_type = 'facebook', 'facebook' , 'wordpress' ) as user_from,
			" . $wpdb->yop_poll_logs . ".user_id

			FROM " . $wpdb->yop_poll_logs . "
			LEFT JOIN (" . $wpdb->yop_polls . ',' . $wpdb->yop_poll_answers . ")
			ON (
			" . $wpdb->yop_poll_logs . ".poll_id = " . $wpdb->yop_polls . ".id AND
			" . $wpdb->yop_poll_logs . ".answer_id = " . $wpdb->yop_poll_answers . ".id
			)
			LEFT JOIN (" . $wpdb->users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->users . ".ID
			)
			LEFT JOIN (" . $wpdb->yop_poll_facebook_users . ")
			ON (
			" . $wpdb->yop_poll_logs . ".user_id = " . $wpdb->yop_poll_facebook_users . ".id
			)
			";
			$sql .= ' ) as logs  ';
			$sql .= $sql_search;
			$sql .= ' GROUP BY vote_id ) as count_logs';
			return $wpdb->get_var( $sql );
		}

		public static function get_bans_filter_search( $orderby = 'id', $order = 'desc', $search = array( 'fields' => array(), 'value' => NULL ), $type = NULL, $poll_id = NULL, $offset = 0, $per_page = 99999999 ) {
			global $wpdb;

			if ( 'id' == $orderby ){
				$orderby = $wpdb->yop_poll_bans . ".id";
			}

			$sql_search = ' ';

			if ( $poll_id ){
				$sql_search .= $wpdb->prepare( 'WHERE ' . $wpdb->yop_poll_bans . '.poll_id = %d', $poll_id );
			}
			if ( $type ){
				if ( $poll_id ){
					$sql_search .= ' AND  ';
				}
				else {
					$sql_search .= ' WHERE ';
				}
				$sql_search .= $wpdb->prepare( $wpdb->yop_poll_bans . '.type= %s', $type );
			}
			if ( count( $search['fields'] ) > 0 ){
				if ( $poll_id || $type ){
					$sql_search .= ' AND ( ';
				}
				else {
					$sql_search .= ' WHERE (';
				}
				foreach ( $search['fields'] as $field ) {
					$sql_search .= $wpdb->prepare( ' ' . esc_attr( $field ) . ' like \'%%%s%%\' OR', $search['value'] );
				}
				$sql_search = trim( $sql_search, 'OR' );
				$sql_search .= ' ) ';
			}

			$sql = "SELECT
			" . $wpdb->yop_poll_bans . ".id,
			" . $wpdb->yop_poll_bans . ".value,
			" . $wpdb->yop_poll_bans . ".type,
			IFNULL( " . $wpdb->yop_polls . ".name, '" . __( 'All Yop Polls', 'yop_poll' ) . "' ) as name

			FROM " . $wpdb->yop_poll_bans . "
			LEFT JOIN (" . $wpdb->yop_polls . ")
			ON (
			" . $wpdb->yop_poll_bans . ".poll_id = " . $wpdb->yop_polls . ".id
			)
			";
			$sql .= $sql_search;
			$sql .= ' ORDER BY ' . esc_attr( $orderby ) . ' ' . esc_attr( $order );
			$sql .= $wpdb->prepare( ' LIMIT %d, %d', $offset, $per_page );
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		private static function delete_poll_answers_from_db( $answer_id, $poll_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					DELETE FROM " . $wpdb->yop_poll_answers . "
					WHERE id = %d AND
					poll_id = %d
					", $answer_id, $poll_id ) );
		}

		private static function delete_all_poll_answer_from_db( $poll_id ) {
			global $wpdb;
			$answers = self::get_poll_answers( $poll_id, array( 'default', 'other' ) );
			if ( $answers ){
				foreach ( $answers as $answer ) {
					delete_yop_poll_answer_meta( $answer['id'], 'options' );
				}
			}

			$wpdb->query( $wpdb->prepare( "
					DELETE FROM " . $wpdb->yop_poll_answers . "
					WHERE poll_id = %d
					", $poll_id ) );
		}

		private static function insert_custom_field_to_database( $custom_field ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					INSERT INTO " . $wpdb->yop_poll_custom_fields . "
					SET poll_id = %d,
					custom_field = %s,
					required = %s,
					status = %s
					", $custom_field['poll_id'], $custom_field['custom_field'], $custom_field['required'], $custom_field['status'] ) );
			return $wpdb->insert_id;
		}

		private static function update_custom_field_in_database( $custom_field ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE " . $wpdb->yop_poll_custom_fields . "
					SET custom_field = %s,
					required = %s
					WHERE id = %d
					", $custom_field['custom_field'], $custom_field['required'], $custom_field['id'] ) );
		}

		private static function delete_poll_customfields_from_db( $customfield_id, $poll_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					DELETE FROM " . $wpdb->yop_poll_custom_fields . "
					WHERE id = %d AND
					poll_id = %d
					", $customfield_id, $poll_id ) );
		}

		private static function delete_all_poll_customfields_from_db( $poll_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					DELETE FROM " . $wpdb->yop_poll_custom_fields . "
					WHERE poll_id = %d
					", $poll_id ) );
		}

		public static function delete_poll_from_db( $poll_id ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( '
					DELETE FROM ' . $wpdb->yop_polls . '
					WHERE id = %d
					', $poll_id ) );

			delete_yop_poll_meta( $poll_id, 'options' );
			self::delete_all_poll_answer_from_db( $poll_id );
			self::delete_all_poll_customfields_from_db( $poll_id );
			self::delete_all_poll_logs( $poll_id );
		}

		public static function clone_poll( $poll_id ) {
			global $wpdb;
			$poll_details = self::get_poll_from_database_by_id( $poll_id );
			$clone_number = self::count_poll_from_database_like_name( $poll_details['name'] . ' - clone' );
			if ( $poll_details ){
				$poll        = array( 'name' => $poll_details['name'] . ' - clone' . ( 0 == $clone_number ? '' : $clone_number ), 'question' => $poll_details['question'], 'start_date' => $poll_details['start_date'], 'end_date' => $poll_details['end_date'], 'total_votes' => 0, 'total_answers' => 0, 'status' => $poll_details['status'], 'date_added' => NULL, 'show_in_archive' => $poll_details['show_in_archive'], 'archive_order' => $poll_details['archive_order'] + 1 );
				$new_poll_id = self::insert_poll_to_database( $poll );

				/*$poll_options = get_yop_poll_meta( $poll_id, 'options' );
				if ( ! isset( $poll_options[0]['has_auto_generate_poll_page'] ) )
				$poll_options[0]['has_auto_generate_poll_page'] = 'no';
				if ( 'yes' == $poll_options[0]['has_auto_generate_poll_page'] ) {
				$_p = array();
				$_p['post_title']       = $poll['name'];
				$_p['post_content']     = "[yop_poll id='".$new_poll_id."']";
				$_p['post_status']      = 'publish';
				$_p['post_type']        = 'page';
				$_p['comment_status']   = 'open';
				$_p['ping_status']      = 'open';
				$_p['post_category']    = array(1); // the default 'Uncategorised'

				$poll_page_id           = wp_insert_post( $_p );

				$poll_options[0]['poll_page_url']               = get_permalink( $poll_page_id );
				$poll_options[0]['has_auto_generate_poll_page'] = 'yes';
				}*/

				$poll_options[0]['poll_page_url']               = '';
				$poll_options[0]['has_auto_generate_poll_page'] = 'no';

				update_yop_poll_meta( $new_poll_id, 'options', $poll_options[0] );

				$answers = self::get_poll_answers( $poll_id, array( 'default', 'other' ) );
				if ( $answers ){
					foreach ( $answers as $answer ) {
						$answer_to_insert = array( 'poll_id' => $new_poll_id, 'answer' => $answer['answer'], 'votes' => 0, 'status' => $answer['status'], 'type' => $answer['type'] );
						$new_answer_id    = self::insert_answer_to_database( $answer_to_insert );

						if ( $answer['type'] != 'other' ){
							$answer_options = get_yop_poll_answer_meta( $answer['id'], 'options' );
							update_yop_poll_answer_meta( $new_answer_id, 'options', $answer_options[0] );
						}
					}
				}

				$custom_fields = self::get_poll_customfields( $poll_id );
				if ( $custom_fields ){
					foreach ( $custom_fields as $custom_field ) {
						$custom_field_to_insert = array( 'poll_id' => $new_poll_id, 'custom_field' => $custom_field['custom_field'], 'required' => $custom_field['required'], 'status' => $custom_field['status'] );
						$new_custom_field_id    = self::insert_custom_field_to_database( $custom_field_to_insert );
					}
				}
			}
		}

		public static function get_yop_poll_templates_search( $orderby = 'last_modified', $order = 'desc', $search = array( 'fields' => array(), 'value' => NULL ) ) {
			global $wpdb;
			$sql        = "SELECT * FROM " . $wpdb->yop_poll_templates;
			$sql_search = '';
			if ( count( $search['fields'] ) > 0 ){
				$sql_search .= ' ( ';
				foreach ( $search['fields'] as $field ) {
					$sql_search .= $wpdb->prepare( ' `' . $field . '` like \'%%%s%%\' OR', $search['value'] );
				}
				$sql_search = trim( $sql_search, 'OR' );
				$sql_search .= ' ) ';
			}
			if ( count( $search['fields'] ) > 0 ){
				$sql .= ' WHERE ' . $sql_search;
			}
			$sql .= ' ORDER BY ' . $orderby . ' ' . $order;
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function get_poll_template_from_database_by_id( $template_id = 0 ) {
			global $wpdb;
			$result = $wpdb->get_row( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_templates . "
					WHERE id = %d
					LIMIT 0,1
					", $template_id ), ARRAY_A );
			return $result;
		}

		private static function insert_poll_template_to_database( $template ) {
			global $wpdb;
			global $current_user;
			wp_get_current_user();
			$wpdb->query( $wpdb->prepare( "
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
					", $current_user->ID, $template['name'], $template['before_vote_template'], $template['after_vote_template'], $template['before_start_date_template'], $template['after_end_date_template'], $template['css'], $template['js'], current_time( 'mysql' ), current_time( 'mysql' ), $template['status'] ) );
			return $wpdb->insert_id;
		}

		private static function insert_ban_to_database( $ban ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					INSERT INTO " . $wpdb->yop_poll_bans . "
					SET poll_id = %d,
					type = %s,
					value = %s
					", $ban['poll_id'], $ban['type'], $ban['value'] ) );
			return $wpdb->insert_id;
		}

		private static function update_poll_template_in_database( $template ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
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
					", $template['name'], $template['before_vote_template'], $template['after_vote_template'], $template['before_start_date_template'], $template['after_end_date_template'], $template['css'], $template['js'], current_time( 'mysql' ), $template['id'] ) );
		}

		public static function delete_poll_template_from_db( $template_id ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( '
					DELETE FROM ' . $wpdb->yop_poll_templates . '
					WHERE id = %d
					', $template_id ) );
		}

		public static function delete_poll_log_from_db( $log_id ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( '
					DELETE FROM ' . $wpdb->yop_poll_logs . '
					WHERE id = %d
					', $log_id ) );
		}

		public static function delete_group_poll_log_from_db( $vote_id ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( '
					DELETE FROM ' . $wpdb->yop_poll_logs . '
					WHERE vote_id = %s
					', $vote_id ) );
		}

		public static function delete_poll_ban_from_db( $ban_id ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( '
					DELETE FROM ' . $wpdb->yop_poll_bans . '
					WHERE id = %d
					', $ban_id ) );
		}

		public static function clone_poll_template( $template_id ) {
			global $wpdb;
			$template_details = self::get_poll_template_from_database_by_id( $template_id );
			$clone_number     = self::count_poll_template_from_database_like_name( $template_details['name'] . ' - clone' );
			if ( $template_details ){
				$template        = array( 'name' => $template_details['name'] . ' - clone' . ( 0 == $clone_number ? '' : $clone_number ), 'before_vote_template' => $template_details['before_vote_template'], 'after_vote_template' => $template_details['after_vote_template'], 'before_start_date_template' => $template_details['before_start_date_template'], 'after_end_date_template' => $template_details['after_end_date_template'], 'css' => $template_details['css'], 'js' => $template_details['js'], 'status' => ( 'default' == $template_details['status'] ) ? 'other' : $template_details['status'], 'date_added' => NULL, 'last_modified' => NULL );
				$new_template_id = self::insert_poll_template_to_database( $template );
			}
		}

		public static function reset_votes_for_poll( $poll_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( 'UPDATE ' . $wpdb->yop_polls . ' SET total_votes = 0, total_answers = 0 WHERE id = %d', $poll_id ) );
			$wpdb->query( $wpdb->prepare( 'UPDATE ' . $wpdb->yop_poll_answers . ' SET votes = 0 WHERE poll_id = %d', $poll_id ) );
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->yop_poll_voters . ' WHERE poll_id = %d', $poll_id ) );
		}

		public static function count_poll_template_from_database_like_name( $template_name ) {
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare( "
					SELECT count(*)
					FROM " . $wpdb->yop_poll_templates . "
					WHERE name like %s
					", $template_name . '%' ) );
			return $result;
		}

		private static function insert_vote_in_database( $answer = array() ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( "
					INSERT INTO " . $wpdb->yop_poll_logs . "
					SET
					poll_id                = %d,
					vote_id                = %s,
					answer_id            = %d,
					ip                    = %s,
					user_id                = %s,
					user_type            = %s,
					http_referer        = %s,
					tr_id                = %s,
					host                = %s,
					other_answer_value    = %s,
					vote_date            = %s
					", $answer['poll_id'], $answer['vote_id'], $answer['answer_id'], $answer['ip'], $answer['user_id'], $answer['user_type'], $answer['http_referer'], $answer['tr_id'], $answer['host'], isset( $answer['other_answer_value'] ) ? $answer['other_answer_value'] : '', current_time( 'mysql' ) ) );
			return $wpdb->insert_id;
		}

		private static function insert_voter_in_database( $voter = array() ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( "
					INSERT INTO " . $wpdb->yop_poll_voters . "
					SET
					poll_id                = %d,
					user_id                = %s,
					user_type            = %s
					", $voter['poll_id'], $voter['user_id'], $voter['user_type'] ) );
			return $wpdb->insert_id;
		}

		public function add_user_other_answer_to_default_answers( &$answer ) { //add user other answer into answers table
			$poll_id      = $this->poll['id'];
			$poll_options = $this->poll_options;

			if ( isset( $poll_options['add_other_answers_to_default_answers'] ) ){
				if ( 'yes' == $poll_options['add_other_answers_to_default_answers'] ){
					if ( 'other' == $answer['type'] ){
						$answer_exist = self::get_poll_answer_by_field( $poll_id, 'answer', $answer['other_answer_value'], '%s' );
						if ( isset( $answer_exist['id'] ) ){
							$answer['answer_id']          = $answer_exist['id'];
							$answer['type']               = 'default';
							$answer['other_answer_value'] = '';
						}
						else {
							$answer_to_add = array( 'poll_id' => $answer['poll_id'], 'answer' => $answer['other_answer_value'], 'votes' => 0, 'status' => 'active', 'type' => 'default' );
							$new_answer_id = self::insert_answer_to_database( $answer_to_add );
							if ( $new_answer_id ){
								$answer['answer_id']          = $new_answer_id;
								$answer['type']               = 'default';
								$answer['other_answer_value'] = '';
							}
						}
					}
				}
			}
			$answer['logerror'] = $log;
			return $answer;
		}

		public function get_voter_number_of_votes( $voter ) {
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare( "
					SELECT count(*) as total_votes
					FROM " . $wpdb->yop_poll_voters . "
					WHERE
					poll_id = %d AND
					user_id = %d AND
					user_type = %s
					", $voter['poll_id'], $voter['user_id'], $voter['user_type'] ) );
			return $result;
		}

		public function user_have_votes_to_vote( $voter ) {
			$poll_options = $this->poll_options;
			if ( $voter['user_id'] > 0 ){
				if ( 'yes' == $poll_options['limit_number_of_votes_per_user'] ){
					if ( $this->get_voter_number_of_votes( $voter ) >= $poll_options['number_of_votes_per_user'] ){
						return false;
					}
				}
			}
			return true;
		}

		public static function delete_all_poll_logs( $poll_id ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->yop_poll_logs . " WHERE poll_id = %d ", $poll_id ) );
		}

		private static function insert_vote_custom_field_in_database( $custom_field = array() ) {
			global $wpdb;

			$custom_field['custom_field_value'] = strip_tags( $custom_field['custom_field_value'] );
			$wpdb->query( $wpdb->prepare( "
					INSERT INTO " . $wpdb->yop_poll_votes_custom_fields . "
					SET
					poll_id                = %d,
					vote_id                = %s,
					custom_field_id        = %d,
					user_id                = %s,
					user_type            = %s,
					custom_field_value    = %s,
					tr_id                = %s,
					vote_date            = %s
					", $custom_field['poll_id'], $custom_field['vote_id'], $custom_field['custom_field_id'], $custom_field['user_id'], $custom_field['user_type'], $custom_field['custom_field_value'], $custom_field['tr_id'], current_time( 'mysql' ) ) );
			return $wpdb->insert_id;
		}

		private static function insert_facebook_user_in_database( $user_details = array() ) {
			global $wpdb;

			$user_exist = $wpdb->get_row( $wpdb->prepare( "
					SELECT * FROM " . $wpdb->yop_poll_facebook_users . "
					WHERE
					fb_id    = %s
					LIMIT 0,1
					", $user_details['id'] ), ARRAY_A );

			if ( $user_exist ){

				$wpdb->query( $wpdb->prepare( "
						UPDATE " . $wpdb->yop_poll_facebook_users . "
						SET
						name                = %s,
						first_name            = %s,
						last_name            = %s,
						username            = %s,
						email                = %s,
						gender                = %s
						WHERE
						fb_id                = %s,
						", $user_details['name'], $user_details['first_name'], $user_details['last_name'], $user_details['username'], $user_details['email'], $user_details['gender'], $user_details['id'] ) );
				return $user_exist['id'];
			}
			else {
				$wpdb->query( $wpdb->prepare( "
						INSERT INTO " . $wpdb->yop_poll_facebook_users . "
						SET
						fb_id                = %s,
						name                = %s,
						first_name            = %s,
						last_name            = %s,
						username            = %s,
						email                = %s,
						gender                = %s,
						date_added            = %s
						", $user_details['id'], $user_details['name'], $user_details['first_name'], $user_details['last_name'], $user_details['username'], $user_details['email'], $user_details['gender'], current_time( 'mysql' ) ) );
				return $wpdb->insert_id;
			}
		}

		private function update_poll_votes_and_answers( $answers = 0, $votes = 0 ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->yop_polls}
					SET
					total_votes = total_votes + %d,
					total_answers = total_answers + %d
					WHERE
					id = %d
					", $votes, $answers, $this->poll['id'] ) );
		}

		private function update_answer_votes( $answer_id = 0, $votes = 0 ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->yop_poll_answers}
					SET
					votes = votes + %d
					WHERE
					id = %d
					", $votes, $answer_id ) );
		}

		public static function update_answer_field( $answer_id = 0, $field = array( 'name' => NULL, 'value' => NULL, 'type' => NULL ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->yop_poll_answers}
					SET
					" . $field['name'] . "= " . $field['type'] . "
					WHERE
					id = %d
					", $field['value'], $answer_id ) );
		}

		public static function update_all_poll_answers_field( $poll_id = 0, $field = array( 'name' => NULL, 'value' => NULL, 'type' => NULL ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->yop_poll_answers}
					SET
					" . $field['name'] . "= " . $field['type'] . "
					WHERE
					poll_id = %d
					", $field['value'], $poll_id ) );
		}

		public static function update_poll_field( $poll_id = 0, $field = array( 'name' => NULL, 'value' => NULL, 'type' => NULL ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->yop_polls}
					SET
					" . $field['name'] . "= " . $field['type'] . "
					WHERE
					id = %d
					", $field['value'], $poll_id ) );
		}

		public static function update_template_field( $template_id = 0, $field = array( 'name' => NULL, 'value' => NULL, 'type' => NULL ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->yop_poll_templates}
					SET
					" . $field['name'] . "= " . $field['type'] . "
					WHERE
					id = %d
					", $field['value'], $template_id ) );
		}

		public static function update_all_polls_field( $field = array( 'name' => NULL, 'value' => NULL, 'type' => NULL ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "
					UPDATE {$wpdb->yop_polls}
					SET
					" . $field['name'] . "= " . $field['type'] . "
					", $field['value'] ) );
		}

		public function register_vote( $request ) {
			global $current_user;
			$poll_id   = $this->poll['id'];
			$unique_id = $this->unique_id;
			$location  = $request['location'];
            yop_poll_dump($request['yop-poll-nonce-' . $poll_id . $unique_id]);
            if( wp_verify_nonce( $request['yop-poll-nonce-' . $poll_id . $unique_id], 'yop_poll-' . $poll_id     . $unique_id . '-user-actions' ) ) {
				$poll_details = $this->poll;
				$poll_options = $this->poll_options;
				$vote_id      = uniqid( 'vote_id_' );
				$vote_type    = $request['vote_type'];
				$tr_id        = $request['yop_poll_tr_id'];

				$facebook_error        = $request['facebook_error'];
				$facebook_user_details = json_decode( self::base64_decode( $request['facebook_user_details'] ), true );

				if ( 'facebook' == $vote_type ){
					if ( !is_array( $facebook_user_details ) ){
						$this->error = __( 'An Error Has Occured!', 'yop_poll' );
						return false;
					}

					if ( '' == $facebook_user_details['id'] ){
						$this->error = __( 'An Error Has Occured!', 'yop_poll' );
						return false;
					}

					if ( $facebook_error == 'access_denied' ){
						$this->error = __( 'You Don`t Have Permission to Vote! You must authorize YOP POLL application in your facebook account!', 'yop_poll' );
						return false;
					}
					if ( $facebook_error == 'access_error' ){
						$this->error = __( 'You Don`t Have Permission to Vote!', 'yop_poll' );
						return false;
					}

					$facebook_user_id = $this->insert_facebook_user_in_database( $facebook_user_details );
				}

				$current_date = YOP_POLL_MODEL::get_mysql_curent_date();
                if( wp_verify_nonce( $request['yop-poll-nonce-' . $poll_id . $unique_id], 'yop_poll-' . $poll_id . $unique_id . '-user-actions' ) ) {
				if ( $this->is_allowed_to_vote( $vote_type, $facebook_user_details ) ){
					if ( $current_date >= $poll_details['start_date'] ){
						if ( $current_date <= $poll_details['end_date'] ){
							if ( 'closed' == $poll_details['status'] ){
								$this->error = __( 'This poll is closed!', 'yop_poll' );
								return false;
							}
							else {
								if ( !$this->is_voted( $vote_type, $facebook_user_details, true ) ){
									$answers            = array();
									$voter              = array();
									$voter['poll_id']   = $poll_id;
									$voter['user_id']   = $current_user->ID;
									$voter['user_type'] = 'wordpress';
									if ( 'facebook' == $vote_type ){
										$voter['user_id']   = $facebook_user_id;
										$voter['user_type'] = $vote_type;
									}


									if ( $this->user_have_votes_to_vote( $voter ) ){
										if ( isset ( $request['yop_poll_answer'] ) ){
											if ( 'yes' == $poll_options['allow_multiple_answers'] ){
												if ( count( $request['yop_poll_answer'] ) <= intval( $poll_options['allow_multiple_answers_number'] ) ){
													if ( count( $request['yop_poll_answer'] ) >= intval( $poll_options['allow_multiple_answers_min_number'] ) ){
														foreach ( $request['yop_poll_answer'] as $answer_id ) {
															$answer              = array();
															$answer['poll_id']   = $poll_id;
															$answer['vote_id']   = $vote_id;
															$answer['answer_id'] = $answer_id;
															$answer['ip']        = self::get_ip();
															$answer['user_id']   = $current_user->ID;
															$answer['type']      = 'default';

															$answer['user_type'] = 'default';
															if ( in_array( $vote_type, $this->vote_types ) ){
																$answer['user_type'] = $vote_type;
															}

															$answer['http_referer']       = $_SERVER['HTTP_REFERER'];
															$answer['tr_id']              = $tr_id;
															$answer['host']               = esc_attr( @gethostbyaddr( self::get_ip() ) );
															$answer['other_answer_value'] = '';
															$answer_details               = self::get_poll_answer_by_id( $answer_id );
															if ( 'other' == $answer_details['type'] ){
																if ( isset( $request['yop_poll_other_answer'] ) ){
																	if ( '' != strip_tags( trim( $request['yop_poll_other_answer'] ) ) ){
																		$answer['other_answer_value'] = strip_tags( $request['yop_poll_other_answer'] );

																		$answer['type']               = 'other';
																	}
																	else {
																		$this->error = __( 'Your other answer is empty!', 'yop_poll' );
																		return false;
																	}
																}
																else {
																	$this->error = __( 'Your other answer is invalid!', 'yop_poll' );
																	return false;
																}
															}
															$answers[] = $answer;
														}
													}
													else {
														$this->error = __( 'Too few answers! Only more than ', 'yop_poll' ) . $poll_options['allow_multiple_answers_min_number'] . __( ' answers allowed!', 'yop_poll' );
														return false;
													}
												}
												else {
													$this->error = __( 'Too many answers! Only ', 'yop_poll' ) . $poll_options['allow_multiple_answers_number'] . __( ' answers allowed!', 'yop_poll' );
													return false;
												}
											}
											else {
												$answer              = array();
												$answer['poll_id']   = $poll_id;
												$answer['vote_id']   = $vote_id;
												$answer['answer_id'] = $request['yop_poll_answer'];
												$answer['ip']        = self::get_ip();
												$answer['user_id']   = $current_user->ID;
												$answer['type']      = 'default';

												$answer['user_type'] = 'default';
												if ( in_array( $vote_type, $this->vote_types ) ){
													$answer['user_type'] = $vote_type;
												}

												$answer['http_referer']       = $_SERVER['HTTP_REFERER'];
												$answer['tr_id']              = $tr_id;
												$answer['host']               = esc_attr( @gethostbyaddr( self::get_ip() ) );
												$answer['other_answer_value'] = '';
												$answer_details               = self::get_poll_answer_by_id( $request['yop_poll_answer'] );
												if ( 'other' == $answer_details['type'] ){
													if ( isset( $request['yop_poll_other_answer'] ) ){
														if ( '' != strip_tags( trim( $request['yop_poll_other_answer'] ) ) ){
                                                            $ans = self::yop_poll_get_answer_from_db($poll_id);
                                                           foreach($ans as $a){

                                                               if(strtoupper (strip_tags( $request['yop_poll_other_answer'] ))==strtoupper( $a['answer'])) {
                                                                   $this->error = __( 'mesaj de eroare!', 'yop_poll' );
                                                                   return false;
                                                               }
                                                           }
															$answer['other_answer_value'] = strip_tags( $request['yop_poll_other_answer'] );
															$answer['type']               = 'other';
														}
														else {
															$this->error = __( 'Your other answer is empty or contains invalid tags!', 'yop_poll' );
															return false;
														}
													}
													else {
														$this->error = __( 'Your other answer is invalid!', 'yop_poll' );
														return false;
													}
												}
												$answers[] = $answer;
											}
											if ( count( $answers ) > 0 ){
												$custom_fields      = array();
												$poll_custom_fields = self::get_poll_customfields( $poll_id );
												if ( count( $poll_custom_fields ) > 0 ){
													if ( isset( $request['yop_poll_customfield'] ) ){
														foreach ( $poll_custom_fields as $custom_field ) {
															if ( isset( $request['yop_poll_customfield'][$custom_field['id']] ) ){
																if ( '' == trim( $request['yop_poll_customfield'][$custom_field['id']] ) && 'yes' == $custom_field['required'] ){
																	$this->error = __( 'Custom field ', 'yop_poll' ) . $custom_field['custom_field'] . __( ' is required! ', 'yop_poll' );
																	return false;
																}
																else {
																	if ( $request['yop_poll_customfield'][$custom_field['id']] != '' ){
																		$new_custom_field                    = array();
																		$new_custom_field['poll_id']         = $poll_id;
																		$new_custom_field['vote_id']         = $vote_id;
																		$new_custom_field['custom_field_id'] = $custom_field['id'];
																		$new_custom_field['user_id']         = $current_user->ID;

																		$new_custom_field['user_type'] = 'default';
																		if ( in_array( $vote_type, $this->vote_types ) ){
																			$new_custom_field['user_type'] = $vote_type;
																		}

																		$new_custom_field['custom_field_value'] = strip_tags( trim( $request['yop_poll_customfield'][$custom_field['id']] ) );
																		$custom_fields[]                        = $new_custom_field;
																	}
																}
															}
															else {
																$this->error = __( 'Custom field ', 'yop_poll' ) . $custom_field['custom_field'] . __( ' is missing! ', 'yop_poll' );
																return false;
															}
														}
													}
													else {
														$this->error = __( 'Custom fields are missing!', 'yop_poll' );
														return false;
													}
												}

												if ( 'yes' == $poll_options['use_captcha'] ){
													require_once( YOP_POLL_INC . '/securimage.php' );
													$img            = new Yop_Poll_Securimage();
													$img->namespace = 'yop_poll_' . $poll_id . $unique_id;
													if ( $img->check( $_REQUEST['yop_poll_captcha_input'][$poll_id] ) ){
														$cookie_ids                 = '';
														$votes                      = 0;
														$mail_notifications_answers = '';
														foreach ( $answers as &$answer ) {
															if (    'facebook' == $vote_type ){
																$answer['user_id'] = $facebook_user_id;
															}
															if ( 'anonymous' == $vote_type ){
																$answer['user_id'] = 0;
															}
															$this->add_user_other_answer_to_default_answers( $answer );
															self::insert_vote_in_database( $answer );
															$cookie_ids .= $answer['answer_id'] . ',';
															$this->update_answer_votes( $answer['answer_id'], 1 );
															$votes++;
															$answer_base = self::get_answer_from_database( $answer['answer_id'] );
															if ( $answer_base['type'] != 'other' ){
																$mail_notifications_answers .= $answer_base['answer'] . '<br>';
															}
															else {
																$mail_notifications_answers .= $answer_base['answer'] . ': ' . $answer['other_answer_value'] . '<br>';
															}
														}
														$mail_notifications_answers = trim( $mail_notifications_answers, '<br>' );

														self::insert_voter_in_database( $voter );
														$this->update_poll_votes_and_answers( $votes, 1 );

														$mail_notifications_custom_fields = '';
														foreach ( $custom_fields as $custom_field ) {
															if ( 'facebook' == $vote_type ){
																$custom_field['user_id'] = $facebook_user_id;
															}
															if ( 'anonymous' == $vote_type ){
																$custom_field['user_id'] = 0;
															}
															$custom_field['tr_id'] = $tr_id;
															self::insert_vote_custom_field_in_database( $custom_field );
															$custom_field_base = self::get_customfield_by_id( $custom_field['custom_field_id'] );
															$mail_notifications_custom_fields .= $custom_field_base['custom_field'] . ': ' . $custom_field['custom_field_value'] . '<br>';
														}
														$mail_notifications_custom_fields = trim( $mail_notifications_custom_fields, '<br>' );

														$this->set_vote_cookie( trim( $cookie_ids, ',' ), $vote_type, $facebook_user_details );
														$this->vote = true;
														$this->poll = self::get_poll_from_database_by_id( $poll_id );
														if ( 'yes' == $poll_options['number_of_votes_per_user'] ){
															$this->success = str_replace( '%USER-VOTES-LEFT%', intval( $poll_options['number_of_votes_per_user'] ) - $this->get_voter_number_of_votes( $voter ), $poll_options['message_after_vote'] );
														}
														else {
															$this->success = str_replace( '%USER-VOTES-LEFT%', '', $poll_options['message_after_vote'] );
														}
														if ( 'yes' == $poll_options['send_email_notifications'] ){
															$headers = 'From: ' . $poll_options['email_notifications_from_name'] . ' <' . $poll_options['email_notifications_from_email'] . '>';
															$subject = str_replace( '[POLL_NAME]', $this->poll['name'], $poll_options['email_notifications_subject'] );
															$subject = str_replace( '[QUESTION]', $this->poll['question'], $subject );
															$subject = str_replace( '[ANSWERS]', $mail_notifications_answers, $subject );
															$subject = str_replace( '[CUSTOM_FIELDS]', $mail_notifications_custom_fields, $subject );
															$subject = str_replace( '[VOTE_ID]', $vote_id, $subject );
															$subject = str_replace( '[VOTE_DATE]', self::convert_date( current_time( 'mysql' ), $poll_options['date_format'] ), $subject );

															$body = str_replace( '[POLL_NAME]', $this->poll['name'], $poll_options['email_notifications_body'] );
															$body = str_replace( '[QUESTION]', $this->poll['question'], $body );
															$body = str_replace( '[ANSWERS]', $mail_notifications_answers, $body );
															$body = str_replace( '[CUSTOM_FIELDS]', $mail_notifications_custom_fields, $body );
															$body = str_replace( '[CUSTOM_FIELDS]', $mail_notifications_custom_fields, $body );
															$body = str_replace( '[VOTE_ID]', $vote_id, $body );
															$body = str_replace( '[VOTE_DATE]', self::convert_date( current_time( 'mysql' ), $poll_options['date_format'] ), $body );

															add_filter( 'wp_mail_content_type', 'yop_poll_set_html_content_type' );
															$is_sent = wp_mail( $poll_options['email_notifications_recipients'], $subject, $body, $headers );
															remove_filter( 'wp_mail_content_type', 'yop_poll_set_html_content_type' );
														}
														return do_shortcode( $this->return_poll_html( array( 'tr_id' => $tr_id, 'location' => $location ) ) );
													}
													else {
														$this->error = __( 'Incorrect security code entered!', 'yop_poll' );
														return false;
													}
												}
												else {
													$cookie_ids                 = '';
													$votes                      = 0;
													$mail_notifications_answers = '';
													foreach ( $answers as &$answer ) {
														if ( 'facebook' == $vote_type ){
															$answer['user_id'] = $facebook_user_id;
														}
														if ( 'anonymous' == $vote_type ){
															$answer['user_id'] = 0;
														}

														$this->add_user_other_answer_to_default_answers( $answer );
														self::insert_vote_in_database( $answer );
														$cookie_ids .= $answer['answer_id'] . ',';
														$this->update_answer_votes( $answer['answer_id'], 1 );
														$votes++;
														$answer_base = self::get_answer_from_database( $answer['answer_id'] );
														if ( $answer_base['type'] != 'other' ){
															$mail_notifications_answers .= $answer_base['answer'] . '<br>';
														}
														else {
															$mail_notifications_answers .= $answer_base['answer'] . ': ' . $answer['other_answer_value'] . '<br>';
														}
													}

													$mail_notifications_answers = trim( $mail_notifications_answers, '<br>' );

													self::insert_voter_in_database( $voter );

													$this->update_poll_votes_and_answers( $votes, 1 );

													$mail_notifications_custom_fields = '';
													foreach ( $custom_fields as $custom_field ) {
														if ( 'facebook' == $vote_type ){
															$custom_field['user_id'] = $facebook_user_id;
														}
														if ( 'anonymous' == $vote_type ){
															$custom_field['user_id'] = 0;
														}
														$custom_field['tr_id'] = $tr_id;
														self::insert_vote_custom_field_in_database( $custom_field );
														$custom_field_base = self::get_customfield_by_id( $custom_field['custom_field_id'] );
														$mail_notifications_custom_fields .= $custom_field_base['custom_field'] . ': ' . $custom_field['custom_field_value'] . '<br>';
													}
													$mail_notifications_custom_fields = trim( $mail_notifications_custom_fields, '<br>' );

													$this->set_vote_cookie( trim( $cookie_ids, ',' ), $vote_type, $facebook_user_details );
													$this->vote = true;
													$this->poll = self::get_poll_from_database_by_id( $poll_id );
													if ( 'yes' == $poll_options['limit_number_of_votes_per_user'] ){
														$this->success = str_replace( '%USER-VOTES-LEFT%', intval( $poll_options['number_of_votes_per_user'] ) - $this->get_voter_number_of_votes( $voter ), $poll_options['message_after_vote'] );
													}
													else {
														$this->success = str_replace( '%USER-VOTES-LEFT%', '', $poll_options['message_after_vote'] );
													}
													if ( 'yes' == $poll_options['send_email_notifications'] ){
														$headers = 'From: ' . $poll_options['email_notifications_from_name'] . ' <' . $poll_options['email_notifications_from_email'] . '>';
														$subject = str_replace( '[POLL_NAME]', $this->poll['name'], $poll_options['email_notifications_subject'] );
														$subject = str_replace( '[QUESTION]', $this->poll['question'], $subject );
														$subject = str_replace( '[ANSWERS]', $mail_notifications_answers, $subject );
														$subject = str_replace( '[CUSTOM_FIELDS]', $mail_notifications_custom_fields, $subject );
														$subject = str_replace( '[VOTE_ID]', $vote_id, $subject );
														$subject = str_replace( '[VOTE_DATE]', self::convert_date( current_time( 'mysql' ), $poll_options['date_format'] ), $subject );

														$body = str_replace( '[POLL_NAME]', $this->poll['name'], $poll_options['email_notifications_body'] );
														$body = str_replace( '[QUESTION]', $this->poll['question'], $body );
														$body = str_replace( '[ANSWERS]', $mail_notifications_answers, $body );
														$body = str_replace( '[CUSTOM_FIELDS]', $mail_notifications_custom_fields, $body );
														$body = str_replace( '[VOTE_ID]', $vote_id, $body );
														$body = str_replace( '[VOTE_DATE]', self::convert_date( current_time( 'mysql' ), $poll_options['date_format'] ), $body );

														add_filter( 'wp_mail_content_type', 'yop_poll_set_html_content_type' );
														$is_sent = wp_mail( $poll_options['email_notifications_recipients'], $subject, $body, $headers );
														remove_filter( 'wp_mail_content_type', 'yop_poll_set_html_content_type' );
													}

													return do_shortcode( $this->return_poll_html( array( 'tr_id' => $tr_id, 'location' => $location ) ) );
												}
											}
											else {
												$this->error = __( 'No vote was registered!', 'yop_poll' );
												return false;
											}
										}
										else {
											$this->error = __( 'No answer selected!', 'yop_poll' );
											return false;
										}
									}
									else {
										$this->error = __( 'You have run out of votes!', 'yop_poll' );
										return false;
									}
								}
								else {
									$this->error = __( 'You Already voted!', 'yop_poll' );
									return false;
								}
							}
						}
						else {
							$this->error = __( 'This poll is closed!', 'yop_poll' );
							return false;
						}
					}
					else {
						$this->error = __( 'You can vote once the poll starts!', 'yop_poll' );
						return false;
					}
				}
				else {
					$this->error = __( 'You are not allowed to vote!', 'yop_poll' );
					return false;
				}
			}
			else {
				$this->error = __( 'Bad Request!', 'yop_poll' );
				return false;
			}
            }
            else {
                $this->error = __( 'Bad Request!', 'yop_poll' );
                return false;
            }
		}

		public function return_poll_css( $attr = array( 'location' => 'page', 'preview' => false, 'template_id' => '', 'loc' => 1 ) ) {
			$preview = isset( $attr['preview'] ) ? $attr['preview'] : false;
			if ( $preview ){
				$template_id = isset( $attr['template_id'] ) ? $attr['template_id'] : '';
				if ( '' == $template_id ){
					$default_template = self::get_default_template();
					$template_id      = $default_template['id'] ? $default_template['id'] : 0;
				}
				$template_details = self::get_poll_template_from_database_by_id( $template_id );
				$template         = $template_details['css'];
				$template         = str_ireplace( "%POLL-ID%", 'preview-' . $attr['loc'] . '', $template );
				$template         = str_ireplace( "%POLL-WIDTH%", '200px', $template );
				return stripslashes( $template );
			}
			else {
				$poll_id   = $this->poll['id'];
				$location  = isset( $attr['location'] ) ? $attr['location'] : 'page';
				$unique_id = $this->unique_id;
				if ( !$poll_id ){
					return '';
				}
				$poll_details = $this->poll;
				$poll_options = $this->poll_options;
				if ( 'widget' == $location ){
					$template_id = $poll_options['widget_template'];
				}
				else {
					$template_id = $poll_options['template'];
				}

				if ( '' == $template_id ){
					$default_template = self::get_default_template();
					$template_id      = $default_template['id'] ? $default_template['id'] : 0;
				}
				$template_details = self::get_poll_template_from_database_by_id( $template_id );
				$template         = $template_details['css'];
				$template         = str_ireplace( '%POLL-ID%', $poll_id . $unique_id, $template );
				if ( 'widget' == $location ){
					$template = str_ireplace( '%POLL-WIDTH%', $poll_options['widget_template_width'], $template );
				}
				else {
					$template = str_ireplace( '%POLL-WIDTH%', $poll_options['template_width'], $template );
				}
				return stripslashes( $template );
			}
		}

		public function return_poll_js( $attr = array( 'location' => 'page' ) ) {
			$poll_id   = $this->poll['id'];
			$location  = isset( $attr['location'] ) ? $attr['location'] : 'page';
			$unique_id = $this->unique_id;

			if ( !$poll_id ){
				return '';
			}
			$poll_details = $this->poll;
			$poll_options = $this->poll_options;
			if ( 'widget' == $location ){
				$template_id = $poll_options['widget_template'];
			}
			else {
				$template_id = $poll_options['template'];
			}

			$display_other_answers_values = false;

			if ( isset( $poll_options['display_other_answers_values'] ) ){
				if ( 'yes' == $poll_options['display_other_answers_values'] ){
					$display_other_answers_values = true;
				}
				else {
					$display_other_answers_values = false;
				}
			}
			if ( '' == $template_id ){
				$default_template = self::get_default_template();
				$template_id      = $default_template['id'] ? $default_template['id'] : 0;
			}
			$answers_tabulated_cols = 1;
			$results_tabulated_cols = 1;
			if ( 'orizontal' == $poll_options['display_answers'] ){
				$ans_no = self::get_count_poll_answers( $poll_id, array( 'default', 'other' ) );
				if ( $ans_no > 0 ){
					$answers_tabulated_cols = $ans_no;
				}
			}
			if ( 'orizontal' == $poll_options['display_results'] ){
				$ans_no = self::get_count_poll_answers( $poll_id, array( 'default', 'other' ), $display_other_answers_values );
				if ( $ans_no > 0 ){
					$results_tabulated_cols = $ans_no;
				}
			}
			if ( 'tabulated' == $poll_options['display_answers'] ){
				$answers_tabulated_cols = $poll_options['display_answers_tabulated_cols'];
			}
			if ( 'tabulated' == $poll_options['display_results'] ){
				$results_tabulated_cols = $poll_options['display_results_tabulated_cols'];
			}

			$template_details = self::get_poll_template_from_database_by_id( $template_id );
			$template         = $template_details['js'];
			$template         = str_ireplace( '%POLL-ID%', $poll_id . $unique_id, $template );
			$template         = str_ireplace( '%ANSWERS-TABULATED-COLS%', $answers_tabulated_cols, $template );
			$template         = str_ireplace( '%RESULTS-TABULATED-COLS%', $results_tabulated_cols, $template );
			return stripslashes( $template );
		}

		public function return_template_preview_html( $template_id = '', $loc = 1 ) {
			if ( '' == $template_id ){
				return "";
			}
			else {
				$template_details = self::get_poll_template_from_database_by_id( $template_id );
				$template         = $template_details['before_vote_template'];
				$template         = stripslashes_deep( $template );

				$template = str_ireplace( '%POLL-NAME%', "Poll Name", $template );
				$template = str_ireplace( '%POLL-QUESTION%', "Poll Question", $template );
				$template = str_ireplace( '%POLL-VOTE-BUTTON%', '<button class="yop_poll_vote_button" >Vote</button>', $template );

				$t       = $template;
				$pattern = '/\[(\[?)(ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/';
				preg_match( $pattern, $template, $m );
				$m       = $m[0];
				$m       = str_ireplace( '/\[\/?ANSWER_CONTAINER\]/', "", $m );
				$answers = array( "Answer 1", "Answer 2", "Answer 3" );
				$ts      = "";
				foreach ( $answers as $key => $answer ) {
					$temps = str_ireplace( '%POLL-ANSWER-CHECK-INPUT%', '<input type="radio" value="' . $answer . '" name="yop_poll_answer-' . $loc . '" id="yop-poll-answer-' . $loc . '-' . $key . '" />', $m );
					$temps = str_ireplace( '%POLL-ANSWER-LABEL%', '<label for="yop-poll-answer-' . $loc . '-' . $key . '">' . $answer . '</label>', $temps );
					$ts .= $temps;
				}
				$template = preg_replace( $pattern, $ts, $template );
				$template = preg_replace( '/\[\/?ANSWER_CONTAINER\]/', "", $template );
				$pattern  = array( '/\[(\[?)(OTHER_ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/', '/\[(\[?)(CUSTOM_FIELD_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/', '/\[(\[?)(ANSWER_RESULT_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/', '/\[(\[?)(CAPTCHA_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/' );
				$template = preg_replace( $pattern, "", $template );
				$template = str_ireplace( "%POLL-ID%", "preview-" . $loc, $template );
				$template = self::strip_all_tags( $template );

				$t = '<style type="text/css">' . self::return_poll_css( array( "location" => 'page', 'preview' => true, 'template_id' => $template_id, 'loc' => $loc ) ) . '</style>';
				$t .= '<div id="yop-poll-container-preview-' . $loc . '" class="yop-poll-container">';
				$t .= '' . $template . '</div>';
				return $t;
			}
		}
        public  function back_4_9_1(){}




        public function yop_poll_get_answer_from_db($id) {
            global $wpdb;
            $answer = $wpdb->get_results( $wpdb->prepare( "
					SELECT *
					FROM " . $wpdb->yop_poll_answers . "
					WHERE poll_id = %d
					",$id ), ARRAY_A );
            return $answer;

        }

        public function yop_poll_get_polls_meta_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( $GLOBALS['wpdb']->prepare( "
                            SELECT *
                            FROM " . $wpdb->prefix . "yop2_pollmeta ORDER BY yop_poll_id ASC
                            " ), ARRAY_A );
            return $result;

        }

        public function yop_poll_get_answers_meta_from_db() {
            global $wpdb;

            $result = $wpdb->get_results( $GLOBALS['wpdb']->prepare( "
                            SELECT *
                            FROM " . $wpdb->prefix . "yop2_poll_answermeta
                            " ), ARRAY_A );
            return $result;

        }

        public function yop_poll_get_questions_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop2_poll_questions ORDER BY poll_id ASC
                            " ), ARRAY_A );
            return $result;
        }

        public function yop_poll_get_custom_fields_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( $GLOBALS['wpdb']->prepare( "
                            SELECT *
                            FROM " . $wpdb->prefix . "yop2_poll_custom_fields ORDER BY poll_id ASC
                            " ), ARRAY_A );
            return $result;
        }

        public function yop_poll_get_custom_fields_votes_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( $GLOBALS['wpdb']->prepare( "
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop2_poll_votes_custom_fields
                            " ), ARRAY_A );
            return $result;
        }

        public function yop_poll_get_bans_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "
                            SELECT *
                            FROM   " . $wpdb->prefix . "yop2_poll_bans ORDER BY poll_id ASC
                            " ), ARRAY_A );
            return $result;
        }

        public function yop_poll_get_answers_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "
                            SELECT *
                            FROM  " . $wpdb->prefix . "yop2_poll_answers ORDER BY poll_id ASC
                            " ), ARRAY_A );
            return $result;
        }

        public function yop_poll_get_logs_from_db() {
            global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "
                            SELECT *
                            FROM " . $wpdb->prefix . "yop2_poll_logs
                            " ), ARRAY_A );
            return $result;
        }

        private static function insert_ban_in_db( $ban ) {
            global $wpdb;
            $sql = $wpdb->query( $wpdb->prepare( "
	                INSERT INTO $wpdb->yop_poll_bans
                              ( poll_id,type,value,period ,unit)
		  	                    VALUES(%d,%s,%s,%d,%s)
	                        ", $ban['poll_id'], $ban['type'], $ban['value'], intval( $ban['period'] ), $ban['unit'] ) );
            return $wpdb->get_results( $sql );
        }
        private function save_poll_order( $poll, $poll_order ) {
            $poll_archive_order = get_option( 'yop_poll_archive_order', array() );
            if( $poll_archive_order == "" ) {
                $poll_archive_order = array();
            }if( trim( $poll_order ) <= 0 ) {
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
            update_option( 'yop_poll_archive_order', $poll_archive_order );
        }
		public function return_poll_html( $attr = array( 'tr_id' => '', 'location' => 'page', 'load_css' => false, 'load_js' => false ) ) {
			$tr_id     = isset( $attr['tr_id'] ) ? $attr['tr_id'] : '';
			$location  = isset( $attr['location'] ) ? $attr['location'] : 'page';
			$unique_id = $this->unique_id;
			$load_css  = isset( $attr['load_css'] ) ? $attr['load_css'] : false;
			//$load_js  = isset( $attr['load_js'] ) ? $attr['load_js'] : false;

			$poll_id = $this->poll['id'];
			if ( !$poll_id ){
				return '';
			}
			$poll_details = $this->poll;
			$poll_options = $this->poll_options;

			if ( function_exists( 'icl_translate' ) ){
				$poll_details['question'] = icl_translate( 'yop_poll', $poll_details['id'] . '_question', $poll_details['question'] );
				$poll_details['name']     = icl_translate( 'yop_poll', $poll_details['id'] . '_poll_title', $poll_details['name'] );

				//$poll_options['other_answers_label'] = icl_translate( 'yop_poll', $poll_details['id'] .'_other_answers_label', $poll_options['other_answers_label'] );
				$poll_options['singular_answer_result_votes_number_label'] = icl_translate( 'yop_poll', $poll_options['id'] . '_singular_answer_result_votes_number_label', $poll_options['singular_answer_result_votes_number_label'] );
				$poll_options['plural_answer_result_votes_number_label']   = icl_translate( 'yop_poll', $poll_options['id'] . '_plural_answer_result_votes_number_label', $poll_options['plural_answer_result_votes_number_label'] );
				$poll_options['vote_button_label']                         = icl_translate( 'yop_poll', $poll_details['id'] . '_vote_button_label', $poll_options['vote_button_label'] );
				$poll_options['view_results_link_label']                   = icl_translate( 'yop_poll', $poll_details['id'] . '_view_results_link_label', $poll_options['view_results_link_label'] );
				$poll_options['view_back_to_vote_link_label']              = icl_translate( 'yop_poll', $poll_details['id'] . '_view_back_to_vote_link_label', $poll_options['view_back_to_vote_link_label'] );
				$poll_options['view_total_votes_label']                    = icl_translate( 'yop_poll', $poll_details['id'] . '_view_total_votes_label', $poll_options['view_total_votes_label'] );
				$poll_options['view_total_answers_label']                  = icl_translate( 'yop_poll', $poll_details['id'] . '_view_total_answers_label', $poll_options['view_total_answers_label'] );
				$poll_options['view_total_voters_label']                   = icl_translate( 'yop_poll', $poll_details['id'] . '_view_total_voters_label', $poll_options['view_total_voters_label'] );
				$poll_options['view_poll_archive_link_label']              = icl_translate( 'yop_poll', $poll_details['id'] . '_view_poll_archive_link_label', $poll_options['view_poll_archive_link_label'] );
				$poll_options['answer_result_label']                       = icl_translate( 'yop_poll', $poll_details['id'] . '_answer_result_label', $poll_options['answer_result_label'] );
				$poll_options['vote_permisions_facebook_label']            = icl_translate( 'yop_poll', $poll_details['id'] . '_vote_permisions_facebook_label', $poll_options['vote_permisions_facebook_label'] );
				$poll_options['vote_permisions_wordpress_label']           = icl_translate( 'yop_poll', $poll_details['id'] . '_vote_permisions_wordpress_label', $poll_options['vote_permisions_wordpress_label'] );
				$poll_options['vote_permisions_anonymous_label']           = icl_translate( 'yop_poll', $poll_details['id'] . '_vote_permisions_anonymous_label', $poll_options['vote_permisions_anonymous_label'] );
			}

			if ( 'widget' == $location ){
				$template_id = $poll_options['widget_template'];
			}
			else {
				$template_id = $poll_options['template'];
			}

			if ( '' == $template_id ){
				$default_template = self::get_default_template();
				$template_id      = $default_template['id'] ? $default_template['id'] : 0;
			}

			$template_details = self::get_poll_template_from_database_by_id( $template_id );
			$is_voted         = $this->is_voted();
			$current_date     = self::get_mysql_curent_date();

			if ( $current_date >= $poll_details['start_date'] ){
				if ( $current_date <= $poll_details['end_date'] ){
					if ( !$is_voted ){
						$template = $template_details['before_vote_template'];
						if ( 'before' == $poll_options['view_results'] ){
							if ( $this->is_view_poll_results() ){
								$template = str_ireplace( '%POLL-ANSWER-RESULT-LABEL%', $poll_options['answer_result_label'], $template );
							}
						}
						$template = str_ireplace( '%POLL-VOTE-BUTTON%', '<button class="yop_poll_vote_button" id="yop_poll_vote-button-' . $poll_id . $unique_id . '" onclick="yop_poll_register_vote(\'' . $poll_id . '\', \'' . $location . '\', \'' . $unique_id . '\'); return false;">' . $poll_options['vote_button_label'] . '</button>', $template );
					}
					else {
						$template = $template_details['after_vote_template'];
						if ( 'after' == $poll_options['view_results'] || 'before' == $poll_options['view_results'] ){
							if ( $this->is_view_poll_results() ){
								$template = str_ireplace( '%POLL-ANSWER-RESULT-LABEL%', $poll_options['answer_result_label'], $template );
							}
						}

						if ( 'yes' == $poll_options['view_back_to_vote_link'] ){
							$vote       = $this->vote;
							$this->vote = false;
							if ( !$this->is_voted() ){
								$template = str_ireplace( '%POLL-BACK-TO-VOTE-LINK%', '<a href="javascript:void(0)" class="yop_poll_back_to_vote_link" id="yop_poll_back_to_vote_link' . $poll_id . $unique_id . '" onClick="yop_poll_back_to_vote(\'' . $poll_id . '\', \'' . $location . '\', \'' . $unique_id . '\')">' . $poll_options['view_back_to_vote_link_label'] . '</a>', $template );
							}
							$this->vote = $vote;
						}
					}
				}
				else {
					$template = $template_details['after_end_date_template'];
					if ( 'after-poll-end-date' == $poll_options['view_results'] || 'before' == $poll_options['view_results'] || 'after' == $poll_options['view_results'] ){
						if ( $this->is_view_poll_results() ){
							$template = str_ireplace( '%POLL-ANSWER-RESULT-LABEL%', $poll_options['answer_result_label'], $template );
						}
					}
				}
			}
			else {
				$template = $template_details['before_start_date_template'];
				if ( 'before' == $poll_options['view_results'] ){
					if ( $this->is_view_poll_results() ){
						$template = str_ireplace( '%POLL-ANSWER-RESULT-LABEL%', $poll_options['answer_result_label'], $template );
					}
				}
			}

			if ( 'custom-date' == $poll_options['view_results'] ){
				if ( $current_date >= $poll_options['view_results_start_date'] ){
					if ( $this->is_view_poll_results() ){
						$template = str_ireplace( '%POLL-ANSWER-RESULT-LABEL%', $poll_options['answer_result_label'], $template );
					}
				}
			}

			$template = stripslashes_deep( $template );
			$template = str_ireplace( '%POLL-ID%', $poll_id . $unique_id, $template );


			if ( 'yes' == $poll_options['poll_name_html_tags'] ){
				$template = str_ireplace( '%POLL-NAME%', stripslashes( $poll_details['name'] ), $template );
			}
			else {
				$template = str_ireplace( '%POLL-NAME%', esc_html( stripslashes( $poll_details['name'] ) ), $template );
			}

			$template = str_ireplace( '%POLL-START-DATE%', esc_html( stripslashes( self::convert_date( $poll_details['start_date'], $poll_options['date_format'] ) ) ), $template );
			$template = str_ireplace( '%POLL-PAGE-URL%', esc_html( stripslashes( $poll_options['poll_page_url'] ) ), $template );

			if ( '9999-12-31 23:59:59' == $poll_details['end_date'] ){
				$template = str_ireplace( '%POLL-END-DATE%', __( 'Never Expire', 'yop_poll' ), $template );
			}
			else {
				$template = str_ireplace( '%POLL-END-DATE%', esc_html( stripslashes( self::convert_date( $poll_details['end_date'], $poll_options['date_format'] ) ) ), $template );
			}

			if ( 'yes' == $poll_options['poll_question_html_tags'] ){
				$template = str_ireplace( '%POLL-QUESTION%', esc_html( stripslashes( $poll_details['question'] ) ), $template );
			}
			else {
				$template = str_ireplace( '%POLL-QUESTION%', esc_html( stripslashes( $poll_details['question'] ) ), $template );
			}

			if ( 'yes' == $poll_options['view_results_link'] ){
				$template = str_ireplace( '%POLL-VIEW-RESULT-LINK%', '<a href="javascript:void(0)" class="yop_poll_result_link" id="yop_poll_result_link' . $poll_id . $unique_id . '" onClick="yop_poll_view_results(\'' . $poll_id . '\', \'' . $location . '\', \'' . $unique_id . '\')">' . $poll_options['view_results_link_label'] . '</a>', $template );
			}

			if ( 'yes' == $poll_options['view_poll_archive_link'] ){
				$template = str_ireplace( '%POLL-VIEW-ARCHIVE-LINK%', '<a href="' . $poll_options['poll_archive_url'] . '" class="yop_poll_archive_link" id="yop_poll_archive_link_' . $poll_id . $unique_id . '" >' . $poll_options['view_poll_archive_link_label'] . '</a>', $template );
			}
			if ( 'yes' == $poll_options['view_total_answers'] ){
				$template = str_ireplace( '%POLL-TOTAL-ANSWERS%', $poll_options['view_total_answers_label'], $template );
				$template = str_ireplace( '%POLL-TOTAL-ANSWERS%', $poll_details['total_answers'], $template );
			}
			if ( 'yes' == $poll_options['view_total_votes'] ){
				$template = str_ireplace( '%POLL-TOTAL-VOTES%', $poll_options['view_total_votes_label'], $template );
				$template = str_ireplace( '%POLL-TOTAL-VOTES%', $poll_details['total_votes'], $template );
			}

			$msgDivS = false;
			$msgDivE = false;

			if ( strpos( $template, "%POLL-SUCCESS-MSG%" ) != false ){
				$msgDivS  = true;
				$template = str_ireplace( '%POLL-SUCCESS-MSG%', '<div id="yop-poll-container-success-' . $poll_id . $unique_id . '" class="yop-poll-container-success"></div>', $template );
			}
			if ( strpos( $template, "%POLL-ERROR-MSG%" ) != false ){
				$msgDivE  = true;
				$template = str_ireplace( '%POLL-ERROR-MSG%', '<div id="yop-poll-container-error-' . $poll_id . $unique_id . '" class="yop-poll-container-error"></div>', $template );
			}

			$pattern  = '\[(\[?)(ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			$template = preg_replace_callback( "/$pattern/s", array( &$this, 'answer_replace_callback' ), $template );

			$pattern  = '\[(\[?)(OTHER_ANSWER_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			$template = preg_replace_callback( "/$pattern/s", array( &$this, 'other_answer_replace_callback' ), $template );

			$pattern  = '\[(\[?)(CUSTOM_FIELD_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			$template = preg_replace_callback( "/$pattern/s", array( &$this, 'customfield_replace_callback' ), $template );

			$pattern  = '\[(\[?)(ANSWER_RESULT_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			$template = preg_replace_callback( "/$pattern/s", array( &$this, 'answer_result_replace_callback' ), $template );

			$pattern  = '\[(\[?)(CAPTCHA_CONTAINER)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			$template = preg_replace_callback( "/$pattern/s", array( &$this, 'captcha_replace_callback' ), $template );

			$temp     = self::strip_all_tags( $template );
			$template = "";

			if ( $load_css ){
				$template .= '<style type="text/css">' . self::return_poll_css( array( "location" => $location ) ) . '</style>';
			}

			$template .= '<div id="yop-poll-container-' . $poll_id . $unique_id . '" class="yop-poll-container">';
			if ( !$msgDivS ){
				$template .= '<div id="yop-poll-container-success-' . $poll_id . $unique_id . '" class="yop-poll-container-success"></div>';
			}
			if ( !$msgDivE ){
				$template .= '<div id="yop-poll-container-error-' . $poll_id . $unique_id . '" class="yop-poll-container-error"></div>';
			}

			$template .= '<form id="yop-poll-form-' . $poll_id . $unique_id . '" class="yop-poll-forms">' . $temp . '<input type="hidden" id="yop-poll-tr-id-' . $poll_id . $unique_id . '" name="yop_poll_tr_id" value="' . $tr_id . '"/>' . wp_nonce_field( 'yop_poll-' . $poll_id . $unique_id . '-user-actions', 'yop-poll-nonce-' . $poll_id . $unique_id, false, false ) . '</form></div>';
			return $template;
		}

		private function is_view_poll_results() {
			$poll_id      = $this->poll['id'];
			$unique_id    = $this->unique_id;
			$poll_options = $this->poll_options;
			$is_voted     = $this->is_voted();

			if ( ( ( 'before' == $poll_options['view_results'] ) || ( 'after' == $poll_options['view_results'] && $is_voted ) || ( 'custom-date' == $poll_options['view_results'] && self::get_mysql_curent_date() >= $poll_options['view_results_start_date'] ) || ( 'after-poll-end-date' == $poll_options['view_results'] && self::get_mysql_curent_date() >= $this->poll['end_date'] ) ) && 'never' != $poll_options['view_results'] && ( ( 'quest-only' == $poll_options['view_results_permissions'] && !is_user_logged_in() ) || ( 'registered-only' == $poll_options['view_results_permissions'] && is_user_logged_in() ) || ( 'guest-registered' == $poll_options['view_results_permissions'] ) )
			){
				return true;
			}
			return false;
		}

		public function answer_result_replace_callback( $m ) {
			$poll_id       = $this->poll['id'];
			$unique_id     = $this->unique_id;
			$poll_options  = $this->poll_options;
			$return_string = '';
			$is_voted      = $this->is_voted();

			if ( $this->is_view_poll_results() ){
				$display_other_answers_values = false;
				if( 'yes' == $poll_options['allow_other_answers'] ){
					if ( 'yes' == $poll_options['display_other_answers_values'] ){
						$display_other_answers_values = true;
					}
				}

				$answers_types = array( 'default' );
				if( 'yes' == $poll_options['allow_other_answers'] ){
					if ( 'yes' == $poll_options['add_other_answers_to_default_answers'] ){
						$answers_types = array( 'default', 'other' );
					}
				}

				$percentages_decimals = 0;
				if ( isset( $poll_options['percentages_decimals'] ) ){
					$percentages_decimals = $poll_options['percentages_decimals'];
				}
				if ( isset( $poll_options['sorting_results'] ) ){
					if ( 'exact' == $poll_options['sorting_results'] ){
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_results_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_results_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, $answers_types, 'id', $order_dir, $display_other_answers_values, $percentages_decimals );
					}
					elseif ( 'alphabetical' == $poll_options['sorting_results'] ) {
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_results_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_results_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, $answers_types, 'answer', $order_dir, $display_other_answers_values, $percentages_decimals );
					}
					elseif ( 'random' == $poll_options['sorting_results'] ) {
						$answers = self::get_poll_answers( $poll_id, $answers_types, 'rand()', '', $display_other_answers_values, $percentages_decimals );
					}
					elseif ( 'votes' == $poll_options['sorting_results'] ) {
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_results_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_results_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, $answers_types, 'votes', $order_dir, $display_other_answers_values, $percentages_decimals );
					}
					else {
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_results_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_results_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, $answers_types, 'id', $order_dir, $display_other_answers_values, $percentages_decimals );
					}
				}
				else {
					$order_dir = 'asc';
					if ( isset( $poll_options['sorting_results_direction'] ) ){
						$order_dir = ( 'asc' == $poll_options['sorting_results_direction'] ) ? 'asc' : 'desc';
					}
					$answers = self::get_poll_answers( $poll_id, $answers_types, 'id', $order_dir, $display_other_answers_values, $percentages_decimals );
				}
				if ( count( $answers ) > 0 ){
					foreach ( $answers as $answer ) {
						if ( function_exists( 'icl_translate' ) ){
							$answer['answer'] = icl_translate( 'yop_poll', $answer['id'] . '_answer', $answer['answer'] );
						}
						$poll_options   = $this->poll_options;
						$answer_options = get_yop_poll_answer_meta( $answer['id'], 'options', true );
						if ( $answer_options ){
							foreach ( $answer_options as $option_name => $option_value ) {
								if ( isset( $poll_options[$option_name] ) ){
									if ( $option_value != $poll_options[$option_name] ){
										$poll_options[$option_name] = $option_value;
									}
								}
								else {
									$poll_options[$option_name] = $option_value;
								}
							}
						}
						$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-VOTES%', self::display_poll_result_votes( $answer, $poll_options ), $m[5] );
						$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-PERCENTAGES%', self::display_poll_result_percentages( $answer, $poll_options ), $temp_string );
						if ( 'yes' == $poll_options['poll_answer_html_tags'] ){
							$temp_string = str_ireplace( '%POLL-ANSWER-LABEL%', yop_poll_kses( stripslashes( $answer['answer'] ) ), $temp_string );
						}
						else {
							$temp_string = str_ireplace( '%POLL-ANSWER-LABEL%', yop_poll_kses( esc_html( stripslashes( $answer['answer'] ) ) ), $temp_string );
						}
						$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-BAR%', self::display_poll_result_bar( $poll_id, $answer['id'], $answer['procentes'], $poll_options, $unique_id ), $temp_string );
						$return_string .= $temp_string;
					}
				}
			}

			return $return_string;
		}

		public function customfield_replace_callback( $m ) {
			$poll_id       = $this->poll['id'];
			$poll_options  = $this->poll_options;
			$return_string = '';
			$is_voted      = $this->is_voted();
			if ( !$is_voted ){
				$custom_fields = self::get_poll_customfields( $poll_id );
				if ( count( $custom_fields ) > 0 ){
					foreach ( $custom_fields as $custom_field ) {
						if ( function_exists( 'icl_translate' ) ){
							$custom_field['custom_field'] = icl_translate( 'yop_poll', $custom_field['id'] . '_custom_field', $custom_field['custom_field'] );
						}
						if ( 'yes' == $poll_options['poll_custom_field_html_tags'] ){
							$temp_string = str_ireplace( '%POLL-CUSTOM-FIELD-LABEL%', '<label for="yop-poll-customfield-' . $custom_field['id'] . '">' . strip_tags( stripslashes( $custom_field['custom_field'] ) ) . '</label>', $m[5] );
						}
						else {
							$temp_string = str_ireplace( '%POLL-CUSTOM-FIELD-LABEL%', '<label for="yop-poll-customfield-' . $custom_field['id'] . '">' . strip_tags( esc_html( stripslashes( $custom_field['custom_field'] ) ) ) . '</label>', $m[5] );
						}
						$temp_string = str_ireplace( '%POLL-CUSTOM-FIELD-TEXT-INPUT%', '<input type="text" value="" name="yop_poll_customfield[' . $custom_field['id'] . ']" id="yop-poll-customfield-' . $custom_field['id'] . '" />', $temp_string );
						$return_string .= $temp_string;
					}
				}
			}
			return $return_string;
		}

		public function other_answer_replace_callback( $m ) {
			$poll_id              = $this->poll['id'];
			$unique_id            = $this->unique_id;
			$poll_options         = $this->poll_options;
			$return_string        = '';
			$is_voted             = $this->is_voted();
			$percentages_decimals = 0;
			if ( isset( $poll_options['percentages_decimals'] ) ){
				$percentages_decimals = $poll_options['percentages_decimals'];
			}
			if ( !$is_voted ){
				$multiple_answers = false;
				if ( isset( $poll_options['allow_multiple_answers'] ) ){
					if ( 'yes' == $poll_options['allow_multiple_answers'] ){
						$multiple_answers = true;
					}
				}

				if ( isset( $poll_options['allow_other_answers'] ) ){
					if ( 'yes' == $poll_options['allow_other_answers'] ){
						$other_answer = self::get_poll_answers( $poll_id, array( 'other' ) );
						if ( !$other_answer ){
							$answer          = array( 'id' => NULL, 'poll_id' => $poll_id, 'answer' => isset( $poll_options['other_answers_label'] ) ? yop_poll_kses( $poll_options['other_answers_label'] ) : __( 'Other', 'yop_poll' ), 'votes' => 0, 'status' => 'active', 'type' => 'other' );
							$other_answer_id = self::insert_answer_to_database( $answer );
						}
						$other_answer = self::get_poll_answers( $poll_id, array( 'other' ), 'id', '', false, $percentages_decimals );

						if ( function_exists( 'icl_translate' ) ){
							$other_answer_label = icl_translate( 'yop_poll', $poll_id . '_other_answer_label', yop_poll_kses( stripslashes( $other_answer[0]['answer'] ) ) );
						}
						else {
							$other_answer_label = yop_poll_kses( stripslashes( $other_answer[0]['answer'] ) );
						}

						if ( $multiple_answers ){
							if ( isset( $poll_options['is_default_answer'] ) && 'yes' == $poll_options['is_default_answer'] ){
								$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-CHECK-INPUT%', '<input checked="checked" type="checkbox" value="' . $other_answer[0]['id'] . '" name="yop_poll_answer[' . $other_answer[0]['id'] . ']" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . $unique_id . '" />', $m[5] );
							}
							else {
								$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-CHECK-INPUT%', '<input type="checkbox" value="' . $other_answer[0]['id'] . '" name="yop_poll_answer[' . $other_answer[0]['id'] . ']" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . $unique_id . '" />', $m[5] );
							}
						}
						else {
							if ( isset( $poll_options['is_default_answer'] ) && 'yes' == $poll_options['is_default_answer'] ){
								$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-CHECK-INPUT%', '<input checked="checked" type="radio" value="' . $other_answer[0]['id'] . '" name="yop_poll_answer" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . $unique_id . '" />', $m[5] );
							}
							else {
								$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-CHECK-INPUT%', '<input type="radio" value="' . $other_answer[0]['id'] . '" name="yop_poll_answer" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . $unique_id . '" />', $m[5] );
							}
						}
						if ( 'yes' == $poll_options['poll_answer_html_tags'] ){
							$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-LABEL%', '<label for="yop-poll-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . $unique_id . '">' . stripslashes( $other_answer_label ) . '</label>', $temp_string );
						}
						else {
							$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-LABEL%', '<label for="yop-poll-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . $unique_id . '">' . esc_html( stripslashes( $other_answer_label ) ) . '</label>', $temp_string );
						}
						$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-TEXT-INPUT%', '<label><input onclick="document.getElementById(\'yop-poll-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . $unique_id . '\').checked=true;" type="text" value="" name="yop_poll_other_answer" id="yop-poll-other-answer-' . $poll_id . $unique_id . '-' . $other_answer[0]['id'] . '" /></label>', $temp_string );
						if ( $this->is_view_poll_results() ){
							$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-BAR%', self::display_poll_result_bar( $poll_id, $other_answer[0]['id'], $other_answer[0]['procentes'], $poll_options, $unique_id ), $temp_string );
							$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-RESULT-BAR%', self::display_poll_result_bar( $poll_id, $other_answer[0]['id'], $other_answer[0]['procentes'], $poll_options, $unique_id ), $temp_string );
							$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-VOTES%', self::display_poll_result_votes( $other_answer[0], $poll_options ), $temp_string );
							$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-RESULT-VOTES%', self::display_poll_result_votes( $other_answer[0], $poll_options ), $temp_string );
							$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-PERCENTAGES%', self::display_poll_result_percentages( $other_answer[0], $poll_options ), $temp_string );
							$temp_string = str_ireplace( '%POLL-OTHER-ANSWER-RESULT-PERCENTAGES%', self::display_poll_result_percentages( $other_answer[0], $poll_options ), $temp_string );
						}
						$return_string .= $temp_string;
					}
				}
			}
			return $return_string;
		}

		public function answer_replace_callback( $m ) {
			$poll_id              = $this->poll['id'];
			$unique_id            = $this->unique_id;
			$poll_options         = $this->poll_options;
			$return_string        = '';
			$is_voted             = $this->is_voted();
			$percentages_decimals = 0;
			if ( isset( $poll_options['percentages_decimals'] ) ){
				$percentages_decimals = $poll_options['percentages_decimals'];
			}
			if ( !$is_voted ){
				if ( isset( $poll_options['sorting_answers'] ) ){
					if ( 'exact' == $poll_options['sorting_answers'] ){
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_answers_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_answers_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, array( 'default' ), 'id', $order_dir, false, $percentages_decimals );
					}
					elseif ( 'alphabetical' == $poll_options['sorting_answers'] ) {
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_answers_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_answers_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, array( 'default' ), 'answer', $order_dir, false, $percentages_decimals );
					}
					elseif ( 'random' == $poll_options['sorting_answers'] ) {
						$answers = self::get_poll_answers( $poll_id, array( 'default' ), 'rand()', '', false, $percentages_decimals );
					}
					elseif ( 'votes' == $poll_options['sorting_answers'] ) {
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_answers_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_answers_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, array( 'default' ), 'votes', $order_dir, '', $percentages_decimals );
					}
					else {
						$order_dir = 'asc';
						if ( isset( $poll_options['sorting_answers_direction'] ) ){
							$order_dir = ( 'asc' == $poll_options['sorting_answers_direction'] ) ? 'asc' : 'desc';
						}
						$answers = self::get_poll_answers( $poll_id, array( 'default' ), 'id', $order_dir, false, $percentages_decimals );
					}
				}
				else {
					$order_dir = 'asc';
					if ( isset( $poll_options['sorting_answers_direction'] ) ){
						$order_dir = ( 'asc' == $poll_options['sorting_answers_direction'] ) ? 'asc' : 'desc';
					}
					$answers = self::get_poll_answers( $poll_id, array( 'default' ), 'id', $order_dir, false, $percentages_decimals );
				}
				$multiple_answers        = false;
				$answers_in_select_input = false;
				if ( isset( $poll_options['allow_multiple_answers'] ) ){
					if ( 'yes' == $poll_options['allow_multiple_answers'] ){
						$multiple_answers = true;
					}
				}
				if ( isset( $poll_options['answers_in_select_input'] ) ){
					if ( 'yes' == $poll_options['answers_in_select_input'] ){
						$answers_in_select_input = true;
					}
				}
				if ( count( $answers ) > 0 ){
					foreach ( $answers as $answer ) {
						$poll_options   = $this->poll_options;
						$answer_options = get_yop_poll_answer_meta( $answer['id'], 'options', true );
						if ( $answer_options ){
							foreach ( $answer_options as $option_name => $option_value ) {
								if ( isset( $poll_options[$option_name] ) ){
									if ( $option_value != $poll_options[$option_name] ){
										$poll_options[$option_name] = $option_value;
									}
								}
								else {
									$poll_options[$option_name] = $option_value;
								}
							}
						}
						else {
							$answer_options = array();
							foreach ( $poll_options as $option_name => $option_value ) {
								$answer_options[$option_name] = $option_value;
							}
						}

						if ( function_exists( 'icl_translate' ) ){
							$answer['answer'] = icl_translate( 'yop_poll', $answer['id'] . '_answer', $answer['answer'] );
						}

						if ( $multiple_answers ){
							if ( isset( $answer_options['is_default_answer'] ) && 'yes' == $answer_options['is_default_answer'] ){
								$temp_string = str_ireplace( '%POLL-ANSWER-CHECK-INPUT%', '<input type="checkbox" checked="checked" value="' . $answer['id'] . '" name="yop_poll_answer[' . $answer['id'] . ']" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $answer['id'] . '" />', $m[5] );
							}
							else {
								$temp_string = str_ireplace( '%POLL-ANSWER-CHECK-INPUT%', '<input type="checkbox" value="' . $answer['id'] . '" name="yop_poll_answer[' . $answer['id'] . ']" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $answer['id'] . '" />', $m[5] );
							}
						}
						else {
							if ( isset( $answer_options['is_default_answer'] ) && 'yes' == $answer_options['is_default_answer'] ){
								$temp_string = str_ireplace( '%POLL-ANSWER-CHECK-INPUT%', '<input type="radio" checked="checked" value="' . $answer['id'] . '" name="yop_poll_answer" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $answer['id'] . '" />', $m[5] );
							}
							else {
								$temp_string = str_ireplace( '%POLL-ANSWER-CHECK-INPUT%', '<input type="radio" value="' . $answer['id'] . '" name="yop_poll_answer" id="yop-poll-answer-' . $poll_id . $unique_id . '-' . $answer['id'] . '" />', $m[5] );
							}
						}
						if ( 'yes' == $poll_options['poll_answer_html_tags'] ){
							$temp_string = str_ireplace( '%POLL-ANSWER-LABEL%', '<label for="yop-poll-answer-' . $poll_id . $unique_id . '-' . $answer['id'] . '">' . yop_poll_kses( stripslashes( $answer['answer'] ) ) . '</label>', $temp_string );
						}
						else {
							$temp_string = str_ireplace( '%POLL-ANSWER-LABEL%', '<label for="yop-poll-answer-' . $poll_id . $unique_id . '-' . $answer['id'] . '">' . yop_poll_kses( stripslashes( $answer['answer'] ) ) . '</label>', $temp_string );
						}
						if ( $this->is_view_poll_results() ){
							$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-BAR%', self::display_poll_result_bar( $poll_id, $answer['id'], $answer['procentes'], $poll_options, $unique_id ), $temp_string );
							$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-VOTES%', self::display_poll_result_votes( $answer, $poll_options ), $temp_string );
							$temp_string = str_ireplace( '%POLL-ANSWER-RESULT-PERCENTAGES%', self::display_poll_result_percentages( $answer, $poll_options ), $temp_string );
						}
						$return_string .= $temp_string;
					}
				}
			}
			return $return_string;
		}

		public function captcha_replace_callback( $m ) {
			$poll_id       = $this->poll['id'];
			$unique_id     = $this->unique_id;
			$poll_options  = $this->poll_options;
			$return_string = '';
			$temp_string   = '';

			if ( 'yes' == $poll_options['use_captcha'] ){
				$sid         = md5( uniqid() );
				$temp_string = str_ireplace( '%CAPTCHA-IMAGE%', '<img class="yop_poll_captcha_image" id="yop_poll_captcha_image_' . $poll_id . $unique_id . '" src="' . admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_show_captcha&poll_id=' . $poll_id . '&sid=' . $sid . '&unique_id=' . $unique_id . '" />', $m[5] );
				$temp_string = str_ireplace( '%CAPTCHA-INPUT%', '<input type="text" value="" name="yop_poll_captcha_input[' . $poll_id . ']" id="yop-poll-captcha-input-' . $poll_id . $unique_id . '" />', $temp_string );
				$temp_string = str_ireplace( '%RELOAD-CAPTCHA-IMAGE%', '<a href="javascript:void(0)"><img src="' . YOP_POLL_URL . '/images/captcha_reload.png' . '" alt="' . __( 'Reload', 'yop_poll' ) . '" onClick="yop_poll_reloadCaptcha(' . "'" . $poll_id . "', '" . $unique_id . "'" . ')" /></a>', $temp_string );
				$temp_string = str_ireplace( '%CAPTCHA-LABEL%', __( 'Enter the code', 'yop_poll' ), $temp_string );
				$temp_string = str_ireplace( '%CAPTCHA-PLAY%', '<object type="application/x-shockwave-flash" data="' . YOP_POLL_URL . '/captcha/securimage_play.swf?bgcol=#ffffff&amp;icon_file=' . YOP_POLL_URL . '/images/captcha-audio.gif&amp;audio_file=' . urlencode( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_play_captcha&poll_id=' . $poll_id . '&unique_id=' . $unique_id ) . '" height="30" width="30">
					<param name="movie" value="' . YOP_POLL_URL . '/captcha/securimage_play.swf?bgcol=#ffffff&amp;icon_file=' . YOP_POLL_URL . '/images/captcha-audio.gif&amp;audio_file=' . urlencode( admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) . '?action=yop_poll_play_captcha&poll_id=' . $poll_id . '&unique_id=' . $unique_id ) . '" />
					</object>', $temp_string );
			}
			$return_string .= $temp_string;

			return $return_string;
		}

		public static function get_answer_votes_from_logs( $answer_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT count(*) as votes
				FROM " . $wpdb->yop_poll_logs . "
				WHERE answer_id = %d
				", $answer_id );
			return $wpdb->get_var( $sql );
		}

		public static function get_other_answers_votes( $answer_id, $offset = 0, $per_page = 99999999 ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT count(*) as votes, other_answer_value
				FROM " . $wpdb->yop_poll_logs . "
				WHERE answer_id = %d
				GROUP BY other_answer_value
				ORDER BY id
				LIMIT %d, %d
				", $answer_id, $offset, $per_page );
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function get_poll_votes_from_logs( $poll_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT count(*) as votes,
				answer_id
				FROM " . $wpdb->yop_poll_logs . "
				WHERE poll_id = %d
				GROUP BY answer_id
				", $poll_id );
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function get_poll_total_votes_from_logs( $poll_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT count( DISTINCT vote_id ) as total_votes
				FROM " . $wpdb->yop_poll_logs . "
				WHERE poll_id = %d
				", $poll_id );
			return $wpdb->get_var( $sql );
		}

		public static function get_poll_total_votes_from_answers( $poll_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT sum( votes ) as total_votes
				FROM " . $wpdb->yop_poll_answers . "
				WHERE poll_id = %d
				", $poll_id );
			return $wpdb->get_var( $sql );
		}

		public static function get_poll_total_answers_from_logs( $poll_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT count( DISTINCT id ) as total_answers
				FROM " . $wpdb->yop_poll_logs . "
				WHERE poll_id = %d
				", $poll_id );
			return $wpdb->get_var( $sql );
		}

		public static function get_poll_votes( $poll_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT votes,
				id as answer_id
				FROM " . $wpdb->yop_poll_answers . "
				WHERE poll_id = %d
				", $poll_id );
			return $wpdb->get_results( $sql, ARRAY_A );
		}

		public static function get_sum_poll_votes_from_logs( $poll_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT count(*) as votes
				FROM " . $wpdb->yop_poll_logs . "
				WHERE poll_id = %d
				", $poll_id );
			return $wpdb->get_var( $sql );
		}

		public static function get_sum_poll_votes( $poll_id ) {
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT sum( votes ) as votes
				FROM " . $wpdb->yop_poll_answers . "
				WHERE poll_id = %d
				", $poll_id );
			return $wpdb->get_var( $sql );
		}

		public static function sort_answers_alphabetical_asc_callback( $a, $b ) {
			$cmp = strcmp( $a['answer'], $b['answer'] );
			if ( $cmp == 0 ){
				return 0;
			}
			return ( $cmp < 0 ) ? -1 : 1;
		}

		public static function sort_answers_alphabetical_desc_callback( $a, $b ) {
			$cmp = strcmp( $a['answer'], $b['answer'] );
			if ( $cmp == 0 ){
				return 0;
			}
			return ( $cmp < 0 ) ? 1 : -1;
		}

		public static function sort_answers_by_votes_asc_callback( $a, $b ) {
			if ( intval( $a['votes'] ) == intval( $b['votes'] ) ){
				return 0;
			}
			return ( intval( $a['votes'] ) < intval( $b['votes'] ) ) ? -1 : 1;
		}

		public static function sort_answers_by_votes_desc_callback( $a, $b ) {
			if ( intval( $a['votes'] ) == intval( $b['votes'] ) ){
				return 0;
			}
			return ( intval( $a['votes'] ) < intval( $b['votes'] ) ) ? 1 : -1;
		}

		public static function display_poll_result_bar( $poll_id = 0, $answer_id = 0, $procent = 0, $poll_options = array(), $unique_id = '' ) {
			$result_bar = '';
			$result_bar = ' <div class="yop-poll-results-bar-' . $poll_id . $unique_id . '" ';
			if ( 'no' == $poll_options['use_template_bar'] ){
				$result_bar .= ' style="height:' . intval( $poll_options['bar_height'] + 2 * intval( $poll_options['bar_border_width'] ) ) . 'px;" ';
			}
			$result_bar .= '>';
			if ( floatval( $procent ) > 0 ){
				$result_bar .= '<div style="' . 'width:' . $procent . '%; ';
				if ( 'no' == $poll_options['use_template_bar'] ){
					$result_bar .= 'height:' . $poll_options['bar_height'] . 'px; ' . 'background-color:#' . $poll_options['bar_background'] . '; ' . 'border-style:' . $poll_options['bar_border_style'] . '; ' . 'border-width:' . $poll_options['bar_border_width'] . 'px; ' . 'border-color:#' . $poll_options['bar_border_color'] . '; ';
				}
				$result_bar .= '" ' . 'id="yop-poll-result-bar-div-' . $answer_id . '" ' . 'class="yop-poll-result-bar-div-' . $poll_id . $unique_id . '"' . '>' . '</div>';
			}
			$result_bar .= '</div>';
			return $result_bar;
		}

		private static function display_poll_result_votes( $answer = array(), $poll_options = array() ) {
			if ( 'votes-number' == $poll_options['view_results_type'] || 'votes-number-and-percentages' == $poll_options['view_results_type'] ){
				if ( '1' == $answer['votes'] ){
					return $answer['votes'] . ' ' . $poll_options['singular_answer_result_votes_number_label'];
				}
				else {
					return $answer['votes'] . ' ' . $poll_options['plural_answer_result_votes_number_label'];
				}
			}
		}

		private static function display_poll_result_percentages( $answer = array(), $poll_options = array() ) {
			if ( 'percentages' == $poll_options['view_results_type'] || 'votes-number-and-percentages' == $poll_options['view_results_type'] ){
				return $answer['procentes'] . '%';
			}
			else {
				return '';
			}
		}

		public static function strip_all_tags( $template ) {

			$tags = array( '%CAPTCHA-PLAY%', '%CAPTCHA-LABEL%', '%RELOAD-CAPTCHA-IMAGE%', '%CAPTCHA-IMAGE%', '%CAPTCHA-INPUT%', '%POLL-VIEW-ARCHIVE-LINK%', '%POLL-PAGE-URL%', '%POLL-VOTE-BUTTON%', '%POLL-START-DATE%', '%POLL-END-DATE%', '%POLL-ANSWER-RESULT-LABEL%', '%POLL-BACK-TO-VOTE-LINK%', '%POLL-VIEW-RESULT-LINK%', '%POLL-TOTAL-VOTERS%', '%POLL-TOTAL-ANSWERS%', '%POLL-TOTAL-VOTES%', '%POLL-ID%', '%POLL-NAME%', '%POLL-QUESTION%', '%POLL-ANSWER-RESULT-VOTES%', '%POLL-ANSWER-RESULT-PERCENTAGES%', '%POLL-ANSWER-RESULT-LABEL%', '%POLL-ANSWER-LABEL%', '%POLL-ANSWER-RESULT-BAR%', '%POLL-CUSTOM-FIELD-LABEL%', '%POLL-CUSTOM-FIELD-TEXT-INPUT%', '%POLL-OTHER-ANSWER-CHECK-INPUT%', '%POLL-OTHER-ANSWER-LABEL%', '%POLL-OTHER-ANSWER-TEXT-INPUT%', '%POLL-ANSWER-RESULT%', '%POLL-OTHER-ANSWER-RESULT%', '%POLL-ANSWER-RESULT-VOTES%', '%POLL-OTHER-ANSWER-RESULT-VOTES%', '%POLL-ANSWER-RESULT-PERCENTAGES%', '%POLL-OTHER-ANSWER-RESULT-PERCENTAGES%', '%POLL-ANSWER-CHECK-INPUT%', '%POLL-ANSWER-LABEL%', '%POLL-ANSWER-RESULT%' );

			foreach ( $tags as $tag ) {
				$template = str_ireplace( $tag, '', $template );
			}
			return $template;
		}

		private function is_voted( $vote_type = 'default', $facebook_user_details = NULL, $from_register = NULL ) {
			if ( $this->vote ){
				return true;
			}
			if ( isset( $this->poll_options['blocking_voters'] ) ){
				switch ( $this->poll_options['blocking_voters'] ) {
					case 'ip' :
						return $this->is_voted_ip();
						break;
					case 'cookie':
						return $this->is_voted_cookie();
						break;
					case 'username':
						return $this->is_voted_username( $vote_type = 'default', $facebook_user_details = NULL, $from_register );
						break;
					case 'cookie-ip':
						if ( $this->is_voted_cookie() ){
							return true;
						}
						else {
							return $this->is_voted_ip();
						}
						break;
					case 'dont-block':
						return false;
						break;
				}
			}
			return true;
		}

		private function is_ban( $vote_type = 'default', $facebook_user_details = NULL ) {
			global $wpdb, $current_user;

			$username = $current_user->data->user_login;
			$email    = $current_user->data->user_email;

			if ( 'facebook' == $vote_type ){
				$username = $facebook_user_details['username'];
				$email    = $facebook_user_details['email'];
			}
			if ( 'anonymous' == $vote_type ){
				$username = '';
				$email    = '';
			}
			$ip  = self::get_ip();
			$sql = $wpdb->prepare( "
				SELECT id
				FROM " . $wpdb->yop_poll_bans . "
				WHERE poll_id in( 0, %d) AND
				(
				(type = 'ip' and value = %s ) OR
				(type = 'username' and value = %s ) OR
				(type = 'email' and value = %s )
				)
				LIMIT 0,1
				", $this->poll['id'], $ip, $username, $email );
			return $wpdb->get_var( $sql );
		}

		private function is_voted_ip() {
			global $wpdb;
			$unit = 'DAY';
			if ( isset( $this->poll_options['blocking_voters_interval_unit'] ) ){
				switch ( $this->poll_options['blocking_voters_interval_unit'] ) {
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
			if ( isset( $this->poll_options['blocking_voters_interval_value'] ) ){
				$value = $this->poll_options['blocking_voters_interval_value'];
			}
			$ip     = self::get_ip();
			$log_id = $wpdb->get_var( $wpdb->prepare( "
					SELECT id
					FROM " . $wpdb->yop_poll_logs . "
					WHERE poll_id = %d AND
					ip = %s AND
					vote_date >= DATE_ADD( %s, INTERVAL -%d " . $unit . ")
					", $this->poll['id'], $ip, current_time( 'mysql' ), $value ) );

			return $log_id;
		}

		private function is_voted_cookie() {
			if ( isset( $_COOKIE['yop_poll_voted_' . $this->poll['id']] ) ){
				return true;
			}
			return false;
		}

		private function set_vote_cookie( $answer_ids = '0', $vote_type = 'default', $facebook_user_details = NULL ) {
			$expire_cookie = 0;
			$value         = 30;
			if ( isset( $this->poll_options['blocking_voters_interval_value'] ) ){
				$value = $this->poll_options['blocking_voters_interval_value'];
			}
			$unit = 'days';
			if ( isset( $this->poll_options['blocking_voters_interval_unit'] ) ){
				$unit = $this->poll_options['blocking_voters_interval_unit'];
			}

			switch ( $unit ) {
				case 'seconds' :
					$expire_cookie = time() + $value;
					break;
				case 'minutes' :
					$expire_cookie = time() + ( 60 * $value );
					break;
				case 'hours' :
					$expire_cookie = time() + ( 60 * 60 * $value );
					break;
				case 'days' :
					$expire_cookie = time() + ( 60 * 60 * 24 * $value );
					break;
			}
			setcookie( 'yop_poll_voted_' . $this->poll['id'], $answer_ids, $expire_cookie, COOKIEPATH, COOKIE_DOMAIN, false );
			setcookie( 'yop_poll_vote_type_' . $this->poll['id'], $vote_type, $expire_cookie, COOKIEPATH, COOKIE_DOMAIN, false );
			if ( 'facebook' == $vote_type ){
				setcookie( 'yop_poll_vote_facebook_user_' . $this->poll['id'], $facebook_user_details['id'], $expire_cookie, COOKIEPATH, COOKIE_DOMAIN, false );
			}
		}

		private function is_voted_username( $vote_type = 'default', $facebook_user_details = NULL, $from_register = NULL ) {
			global $current_user, $wpdb;

			if ( !$from_register ){
				$vote_type                   = in_array( $_COOKIE['yop_poll_vote_type_' . $this->poll['id']], $this->vote_types ) ? $_COOKIE['yop_poll_vote_type_' . $this->poll['id']] : 'default';
				$facebook_user_details['id'] = $_COOKIE['yop_poll_vote_facebook_user_' . $this->poll['id']];
			}

			$unit = 'DAY';
			if ( isset( $this->poll_options['blocking_voters_interval_unit'] ) ){
				switch ( $this->poll_options['blocking_voters_interval_unit'] ) {
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
			if ( 'default' == $vote_type || 'anonymous' == $vote_type ){
				if ( !is_user_logged_in() ){
					return $this->is_voted_ip();
				}
			}

			$value = 30;
			if ( isset( $this->poll_options['blocking_voters_interval_value'] ) ){
				$value = $this->poll_options['blocking_voters_interval_value'];
			}
			$ip      = self::get_ip();
			$user_id = $current_user->ID;

			if ( 'facebook' == $vote_type ){
				$user_id = $wpdb->get_var( $wpdb->prepare( "
						SELECT id
						FROM " . $wpdb->yop_poll_facebook_users . "
						WHERE
						fb_id = %d
						", $facebook_user_details['id'] ) );

				if ( !$user_id ){
					return false;
				}
			}

			$log_id = $wpdb->get_var( $wpdb->prepare( "
					SELECT id
					FROM " . $wpdb->yop_poll_logs . "
					WHERE poll_id = %d AND
					user_id = %d AND
					vote_date >= DATE_ADD( %s, INTERVAL -%d " . $unit . ")
					", $this->poll['id'], $user_id, current_time( 'mysql' ), $value ) );

			return $log_id;

		}

		public static function get_mysql_curent_date() {
			return current_time( 'mysql' );
		}

		public static function get_mysql_custom_date( $interval_value = 0, $interval_unit = 'DAY' ) {
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare( "SELECT %s + INTERVAL %d " . esc_sql( $interval_unit ), current_time( 'mysql' ), $interval_value ) );
		}

		private function is_allowed_to_vote( $vote_type = 'default', $facebook_user_details = NULL ) {
			global $current_user;
			if ( self::is_ban( $vote_type, $facebook_user_details ) ){
				return false;
			}
			if ( isset( $this->poll_options['vote_permisions'] ) ){
				switch ( $this->poll_options['vote_permisions'] ) {
					case 'quest-only':
						if ( $current_user->ID > 0 ){
							return false;
						}
						if ( 'facebook' == $vote_type ){
							return false;
						}
						return true;
						break;
					case 'registered-only':
						if ( $current_user->ID > 0 ){
							return true;
						}
						if ( 'facebook' == $vote_type ){
							if ( $facebook_user_details ){
								if ( $facebook_user_details['id'] != '' ){
									return true;
								}
							}
						}
						return false;
						break;
					default :
						if ( 'facebook' == $vote_type ){
							if ( !$facebook_user_details ){
								return false;
							}
							elseif ( $facebook_user_details['id'] == '' ) {
								return false;
							}
						}
						return true;
				}
			}
			return true;
		}

		public static function get_ip() {
			$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
			if ( !empty( $_SERVER['X_FORWARDED_FOR'] ) ){
				$X_FORWARDED_FOR = explode( ',', $_SERVER['X_FORWARDED_FOR'] );
				if ( !empty( $X_FORWARDED_FOR ) ){
					$REMOTE_ADDR = trim( $X_FORWARDED_FOR[0] );
				}
			}
			elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$HTTP_X_FORWARDED_FOR = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
				if ( !empty( $HTTP_X_FORWARDED_FOR ) ){
					$REMOTE_ADDR = trim( $HTTP_X_FORWARDED_FOR[0] );
				}
			}
			return preg_replace( '/[^0-9a-f:\., ]/si', '', $REMOTE_ADDR );
		}

		public static function get_oldest_poll_from_database() {
			global $wpdb;

			$yop_poll = $wpdb->get_row( "
				SELECT *
				FROM " . $wpdb->yop_polls . "
				ORDER BY
				date_added DESC
				LIMIT 0,1
				", ARRAY_A );
			return $yop_poll;
		}

		public static function convert_date( $original_date, $new_format = '' ) {
			return date_i18n( $new_format, strtotime( $original_date ) );
		}

		public static function base64_decode( $str ) {
			$str = str_replace( '-', '/', $str );
			$str = str_replace( '_', '+', $str );
			return base64_decode( $str );
		}
}