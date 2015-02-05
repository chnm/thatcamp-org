<?php
	class Yop_Poll_Widget extends WP_Widget {
		//constructor
		function Yop_Poll_Widget() {
			$widget_options	= array(
				'classname'	=> 'Yop Poll Widget',
				'description'	=> 'Yop Poll Polls'
			);
			parent::WP_Widget('yop_poll_widget', 'Yop Polls', $widget_options );
		}

		function widget( $args, $instance ) {
			extract ( $args, EXTR_SKIP );
			$title				= ( $instance['title'] ) ? esc_attr( $instance['title'] ) : __( 'Yop Poll Widget', 'yop_poll' );
			$poll_id			= ( $instance['poll_id'] ) ? intval( $instance['poll_id'] ) : -1;
			$tr_id				= ( $instance['tr_id'] ) ? $instance['tr_id'] : '';
			$poll_unique_id		= uniqid( '_yp' );
			if ( -99 == $poll_id )
				return '';
			$pro_options		= get_option( 'yop_poll_pro_options' );
			require_once( YOP_POLL_INC.'/yop_poll_model.php');
			$yop_poll_model		= new YOP_POLL_MODEL( $poll_id );
			$yop_poll_model->set_unique_id( $poll_unique_id );
			$poll_id			= $yop_poll_model->poll['id'];
			
			$answers			= YOP_POLL_MODEL::get_poll_answers( $poll_id, array( 'default', 'other') );
			$yop_poll_answers	= array();
			if ( count( $answers ) > 0 ) {
				foreach ( $answers as $answer ) {
					$yop_poll_answers[]	= array( 'id' => $answer['id'], 'value' => html_entity_decode( (string) $answer['answer'], ENT_QUOTES, 'UTF-8'), 'type' => $answer['type'] );
				}
			}
			
			$template			= $yop_poll_model->return_poll_html( array( 'tr_id' => $tr_id, 'location' => 'widget', 'load_css' => true, 'load_js' => true ) );
			if ( 'yes' == $yop_poll_model->poll_options['use_default_loading_image'] )
				$loading_image_src	= YOP_POLL_URL.'/images/loading36x36.gif';
			else
				$loading_image_src	= $yop_poll_model->poll_options['loading_image_url'];
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'yop-poll-jquery-popup-windows', YOP_POLL_URL . "/js/jquery.popupWindow.js",array( 'jquery' ), YOP_POLL_VERSION, true);
			wp_enqueue_style( 'yop-poll-public', YOP_POLL_URL."/css/yop-poll-public.css", array(), YOP_POLL_VERSION );
			wp_enqueue_script( 'yop-poll-widget-user-defined_'.$poll_id.$poll_unique_id, add_query_arg( array( 'id' => $poll_id, 'location' => 'widget', 'unique_id' => $poll_unique_id ), admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')).'?action=yop_poll_load_js' ), array( 'jquery' ), YOP_POLL_VERSION, true);
			wp_enqueue_script( 'yop-poll-public', YOP_POLL_URL.'/js/yop-poll-public.js', array(), YOP_POLL_VERSION, true );
			wp_enqueue_script( 'yop-poll-json2', YOP_POLL_URL."/js/yop-poll-json2.js", array(), YOP_POLL_VERSION, true );
			wp_enqueue_script( 'yop-poll-jquery-base64', YOP_POLL_URL."/js/yop-poll-jquery.base64.min.js", array(), YOP_POLL_VERSION, true );
			
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
				'yop_poll_version'              => YOP_POLL_VERSION,
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
			wp_localize_script( 'yop-poll-public', 'yop_poll_public_config_'.$poll_id.$poll_unique_id, $yop_poll_public_config );
			
			echo $before_widget;
			echo $before_title . $title . $after_title;
			echo do_shortcode($template);
			echo $after_widget;
		}

		function update( $new_instance, $old_instance ) {
			var_dump( $new_instance );
			if ( ! isset( $new_instance['doSave'] ) )
				return false;
			if ( 'yes' != $new_instance['doSave'] )
				return false;
			$instance				= $old_instance;
			$instance['title']		= strip_tags($new_instance['title']);
			$instance['poll_id']	= intval($new_instance['poll_id']);
			$instance['tr_id']		= $new_instance['tr_id'];
			return $instance;
		}

		function form( $instance ) {
			$instance 	= wp_parse_args( (array) $instance, array('title' => __('Yop Polls', 'yop_poll'), 'poll_id' => -99) );
			$title		= esc_attr( $instance['title'] );
			$poll_id	= intval( $instance['poll_id'] );
			$tr_id		= $instance['tr_id'];
			global $wpdb;
			require_once( YOP_POLL_INC.'/yop_poll_model.php');
			$yop_polls	= Yop_Poll_Model::get_yop_polls_filter_search( 'id', 'asc' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<span>Title:</span>
				<input id="<?php echo $this->get_field_id('title'); ?>"
					name="<?php echo $this->get_field_name('title'); ?>"
					value="<?php echo $title ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('poll_id'); ?>">
				<span>Poll to Display:</span>
				<select id="<?php echo $this->get_field_id('poll_id'); ?>" name="<?php echo $this->get_field_name('poll_id'); ?>" class="widefat">
					<option value="-99"<?php selected(-99, $poll_id); ?>><?php _e('Do NOT Display Poll (Disable)', 'yop-poll'); ?></option>
					<option value="-3"<?php selected(-3, $poll_id); ?>><?php _e('Display Random Poll', 'yop-poll'); ?></option>
					<option value="-2"<?php selected(-2, $poll_id); ?>><?php _e('Display Latest Poll', 'yop-poll'); ?></option>
					<option value="-1"<?php selected(-1, $poll_id); ?>><?php _e('Display Current Active Poll', 'yop-poll'); ?></option>
					<?php
						if( count( $yop_polls ) > 0 ) {
							foreach( $yop_polls as $poll ) {
							?>
							<option value="<?php echo $poll['id']; ?>"<?php selected($poll['id'], $poll_id); ?>><?php echo esc_attr( $poll['name'] ); ?></option>
							<?php
							}
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('tr_id'); ?>">
				<span>Traking ID:</span>
				<input id="<?php echo $this->get_field_id('tr_id'); ?>"
					name="<?php echo $this->get_field_name('tr_id'); ?>"
					value="<?php echo $tr_id ?>" />
			</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('doSave'); ?>" name="<?php echo $this->get_field_name('doSave'); ?>" value="yes" />
		<?php
		}
}