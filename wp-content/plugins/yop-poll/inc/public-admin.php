<?php
	class Yop_Poll_Public_Admin extends Yop_Poll_Plugin {
		protected function init() {
			$this->add_action( 'init', 'load_translation_file', 1 );
            $this->add_filter( 'the_content', 'yop_poll_do_shortcode_the_content_filter', 1 );
			$this->add_action( 'init', 'public_loader', 1 );
			$this->add_action( 'widgets_init', 'widget_init' );
			$this->add_filter( 'widget_text', 'do_shortcode');
			$this->add_action( 'init', 'yop_poll_setup_schedule');
			$this->add_action( 'yop_poll_hourly_event', 'yop_poll_do_scheduler' );
		}

		public function yop_poll_setup_schedule() {
			$schedule_timestamp	= wp_next_scheduled( 'yop_poll_hourly_event', array() );
			$yop_poll_options	= get_option( 'yop_poll_options', false );
			if ( 'yes' == $yop_poll_options['start_scheduler'] ) {
				if ( ! $schedule_timestamp ) {
					wp_schedule_event( strtotime( substr( current_time( 'mysql'), 0, 14 ).'00:01' ), 'hourly', 'yop_poll_hourly_event', array() );
				}
			}
			else {
				wp_unschedule_event( $schedule_timestamp, 'yop_poll_hourly_event', array() );
			}
		}

		public function yop_poll_do_scheduler() {
			require_once ($this->_config->plugin_inc_dir . '/yop_poll_model.php');
			$yop_polls = Yop_Poll_Model::get_yop_polls_fields ( array( 'id' ) );
			if ( count( $yop_polls ) > 0 ) {
				foreach( $yop_polls as $yop_poll_id ) {
					$yop_poll_options	= Yop_Poll_Model::get_poll_options_by_id( $yop_poll_id['id'] );
					
					if ( 'yes' == $yop_poll_options['schedule_reset_poll_stats'] ) {
						if ( $yop_poll_options['schedule_reset_poll_date'] <= current_time( 'timestamp' ) ) {
							$unit_multiplier	= 0;
							if ( 'hour' == strtolower( trim( $yop_poll_options['schedule_reset_poll_recurring_unit'] ) ) )
								$unit_multiplier	= 60 * 60;
							if ( 'day' == strtolower( trim( $yop_poll_options['schedule_reset_poll_recurring_unit'] ) ) )
								$unit_multiplier	= 60 * 60 * 24;		
							$next_reset_date							= $yop_poll_options['schedule_reset_poll_date'] + intval( $yop_poll_options['schedule_reset_poll_recurring_value'] ) * $unit_multiplier;
							if ( $next_reset_date <= current_time( 'timestamp' ) ) {
								$next_reset_date	= strtotime( substr( current_time( 'mysql'), 0, 11 ) . substr( date('Y-m-d H:i:s', $yop_poll_options['schedule_reset_poll_date'] ), 11, 2 ) . ':00:00' ) + intval( $yop_poll_options['schedule_reset_poll_recurring_value'] ) * $unit_multiplier;	
							} 
							$poll_options								= get_yop_poll_meta( $yop_poll_id['id'], 'options', true );
							$poll_options['schedule_reset_poll_date'] 	= $next_reset_date; 
							update_yop_poll_meta( $yop_poll_id['id'], 'options', $poll_options ); 
							Yop_Poll_Model::reset_votes_for_poll ( $yop_poll_id['id'] );  
						}
					}
				}
			}
		}

		/**
		* this file initialize the text domain for translation file
		*
		*/
		public function load_translation_file() {
			$plugin_path = $this->_config->plugin_dir . '/' . $this->_config->languages_dir;
			load_plugin_textdomain( 'yop_poll', false, $plugin_path );
		}

		public function do_shortcode( $content ) {
			return do_shortcode( $content );
		}

		public function public_loader() {
			add_shortcode( 'yop_poll', array( &$this, 'yop_poll_shortcode_function' ) );
			add_shortcode( 'yop_poll_archive', array( &$this, 'yop_poll_archive_shortcode_function' ) );
		}

		/**
		* Start shortcodes
		*/

		public function yop_poll_archive_shortcode_function() {
			$template		= '';
			$yop_poll_page	= 1;
			$big			= 99999;
			if ( isset( $_REQUEST['yop_poll_page'] ) )
				$yop_poll_page	= $_REQUEST['yop_poll_page'];
			$general_default_options	= get_option( 'yop_poll_options', false );
			require_once( $this->_config->plugin_inc_dir.'/yop_poll_model.php');
			$archive = YOP_POLL_MODEL::get_archive_polls( 'archive_order', 'asc', ( intval( $yop_poll_page ) - 1)  * intval( $general_default_options['archive_polls_per_page'] ), intval( $general_default_options['archive_polls_per_page'] ) );
			$total_archive = ceil( count( YOP_POLL_MODEL::get_archive_polls( 'archive_order', 'asc', 0, $big ) ) / intval( $general_default_options['archive_polls_per_page'] ) );
			if ( count( $archive ) > 0 ) {
				foreach( $archive as $poll ) {
					$template	.= $this->return_yop_poll( $poll['id'] );
				}
			}
			$args = array(
				'base'         => remove_query_arg( 'yop_poll_page', $_SERVER['REQUEST_URI'] ).'%_%',
				'format'       => '?yop_poll_page=%#%',
				'total'        => $total_archive,
				'current'      => max( 1, $yop_poll_page ),
				'prev_next'    => True,
				'prev_text'    => __('&laquo; Previous', 'yop_poll'),
				'next_text'    => __('Next &raquo;', 'yop_poll')
			);
			return $template.paginate_links( $args );
		}

		public function return_yop_poll( $id, $tr_id = '', $offset = 0 ) {
			$pro_options		= get_option( 'yop_poll_pro_options' );
			require_once( $this->_config->plugin_inc_dir.'/yop_poll_model.php');
			$poll_unique_id		= uniqid( '_yp' );
			$yop_poll_model		= new YOP_POLL_MODEL( $id, $offset );
			$yop_poll_model->set_unique_id( $poll_unique_id );

			$id					= $yop_poll_model->poll['id'];

			$answers			= YOP_POLL_MODEL::get_poll_answers( $id, array( 'default', 'other') );
			$yop_poll_answers	= array();
			if ( count( $answers ) > 0 ) {
				foreach ( $answers as $answer ) {
					$yop_poll_answers[]	= array( 'id' => $answer['id'], 'value' => html_entity_decode( (string) $answer['answer'], ENT_QUOTES, 'UTF-8'), 'type' => $answer['type'] );
				}
			}

			if ( ! $yop_poll_model->poll )
				return '';
			$template			= $yop_poll_model->return_poll_html( array( 'tr_id' => $tr_id, 'location' => 'page', 'load_css' => true, 'load_js' => true ) );
			if ( 'yes' == $yop_poll_model->poll_options['use_default_loading_image'] )
				$loading_image_src	= $this->_config->plugin_url.'/images/loading36x36.gif';
			else
				$loading_image_src	= $yop_poll_model->poll_options['loading_image_url'];
			wp_enqueue_style( 'yop-poll-public', "{$this->_config->plugin_url}/css/yop-poll-public.css", array(), $this->_config->version );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'yop-poll-jquery-popup-windows', "{$this->_config->plugin_url}/js/jquery.popupWindow.js", array(), $this->_config->version, true ); 
			wp_enqueue_script( 'yop-poll-user-defined_'.$id.$poll_unique_id, add_query_arg( array( 'id' => $id, 'location' => 'page', 'unique_id' => $poll_unique_id ), admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')).'?action=yop_poll_load_js' ), array( 'jquery' ), $this->_config->version, true);
			wp_enqueue_script( 'yop-poll-public', "{$this->_config->plugin_url}/js/yop-poll-public.js", array(), $this->_config->version, true );
			wp_enqueue_script( 'yop-poll-json2', "{$this->_config->plugin_url}/js/yop-poll-json2.js", array(), $this->_config->version, true );
			wp_enqueue_script( 'yop-poll-jquery-base64', "{$this->_config->plugin_url}/js/yop-poll-jquery.base64.min.js", array(), $this->_config->version, true );

			$yop_poll_public_config_general = array(
				'ajax'	=> array(
					'url'                           => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
					'vote_action'                   => 'yop_poll_do_vote',
					'yop_poll_show_vote_options'    => 'yop_poll_show_vote_options',
					'captcha_action'                => 'yop_poll_show_captcha',
					'view_results_action'           => 'yop_poll_view_results',
					'back_to_vote_action'           => 'yop_poll_back_to_vote',
					'is_wordpress_user_action'      => 'yop_poll_is_wordpress_user'
				),
				'pro'	=> array(
					'api_key'           => $pro_options['pro_api_key'],
					'pro_user'          => $pro_options['pro_user'],
					'api_server_url'    => $pro_options['pro_api_server_url'],
					'pro_token'         => md5( $_SERVER['HTTP_HOST'] . $pro_options['pro_key'] )
				),
				'yop_poll_version'              => $this->_config->version,
				'vote_with_wordpress_login_url' => wp_login_url( admin_url('admin-ajax.php?action=yop_poll_set_wordpress_vote', (is_ssl() ? 'https' : 'http')) ),
				'vote_with_facebook_ajax_url' 	=> admin_url('admin-ajax.php?action=yop_poll_set_wordpress_vote', (is_ssl() ? 'https' : 'http')),
			);

			$vote_permisions_types	= 0;
			if ( 'quest-only' != $yop_poll_model->poll_options['vote_permisions'] ) {
				if ( 'yes'	== $yop_poll_model->poll_options['vote_permisions_wordpress'] )
					$vote_permisions_types += 1;
				if ( 'yes'	== $yop_poll_model->poll_options['vote_permisions_anonymous'] )
					$vote_permisions_types += 2;
				if ( 'yes'	== $yop_poll_model->poll_options['vote_permisions_facebook'] && 'yes' == $pro_options['pro_user'] )
					$vote_permisions_types += 4;
			}

			$yop_poll_public_config = array(
				'poll_options'	=> array(
					'vote_permisions'					=> $yop_poll_model->poll_options['vote_permisions'],
					'vote_permisions_facebook_label'	=> $yop_poll_model->poll_options['vote_permisions_facebook_label'],
					'vote_permisions_wordpress_label'	=> $yop_poll_model->poll_options['vote_permisions_wordpress_label'],
					'vote_permisions_anonymous_label'	=> $yop_poll_model->poll_options['vote_permisions_anonymous_label'],
					'vote_permisions_types'				=> $vote_permisions_types,
					'share_after_vote'					=> $yop_poll_model->poll_options['share_after_vote'],
					'share_name'						=> $yop_poll_model->poll_options['share_name'],
					'share_caption'						=> $yop_poll_model->poll_options['share_caption'],
					'share_description'					=> $yop_poll_model->poll_options['share_description'],
					'share_picture'						=> $yop_poll_model->poll_options['share_picture'],
					'share_question'					=> $yop_poll_model->poll['question'],
					'share_poll_name'					=> $yop_poll_model->poll['name'],
					'share_link'						=> $yop_poll_model->poll_options['poll_page_url'] == '' ? site_url() : $yop_poll_model->poll_options['poll_page_url'],
					'answers'							=> $yop_poll_answers,
					'redirect_after_vote'				=> $yop_poll_model->poll_options['redirect_after_vote'],
					'redirect_after_vote_url'			=> $yop_poll_model->poll_options['redirect_after_vote_url'],
				),
				'loading_image_src'				=> 	$loading_image_src,
				'loading_image_alt'				=> __( 'Loading', 'yop_poll'),

			);
			wp_localize_script( 'yop-poll-public', 'yop_poll_public_config_general', $yop_poll_public_config_general );
			wp_localize_script( 'yop-poll-public', 'yop_poll_public_config_'.$id.$poll_unique_id, $yop_poll_public_config );

			return $template;
		}

		public function yop_poll_shortcode_function ( $atts, $content = NULL ) {
			extract( shortcode_atts( array(
				'id' => -1,
				'tr_id' => '',
				'offset' => 0,
				), $atts ) );
			return $this->return_yop_poll( $id, $tr_id, $offset );

		}

		public function yop_poll_do_shortcode_the_content_filter( $content ) {
            global $shortcode_tags;
            // Backup current registered shortcodes and clear them all out
            $orig_shortcode_tags = $shortcode_tags;
            $shortcode_tags      = array();

            // Do the shortcode (only the one above is registered)
            $content = do_shortcode( $content );

            // Put the original shortcodes back
            $shortcode_tags = $orig_shortcode_tags;

            return $content;
		}

		public function widget_init(){
			register_widget('Yop_Poll_Widget');
		}

		/**
		* End shortcodes
		*/
}