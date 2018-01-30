<?php
/*
* Plugin Name:  bbPress Notify (No-Spam)
* Description:  Sends email notifications upon topic/reply creation, as long as it's not flagged as spam. If you like this plugin, <a href="https://wordpress.org/support/view/plugin-reviews/bbpress-notify-nospam#postform" target="_new">help share the trust and rate it!</a>
* Version:      1.18
* Author:       <a href="http://usestrict.net" target="_new">Vinny Alves (UseStrict Consulting)</a>
* License:      GNU General Public License, v2 ( or newer )
* License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* ( at your option ) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* Copyright (C) 2012-2016 www.usestrict.net, released under the GNU General Public License.
*/

/* Search for translations */
load_plugin_textdomain( 'bbpress_notify', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

class bbPress_Notify_noSpam {
	
	const VERSION = '1.18';
	
	protected $settings_section = 'bbpress_notify_options';
	
	protected $bbpress_topic_post_type;
	
	protected $bbpress_reply_post_type;
	
	public static $instance;
	
	public $domain = 'bbpress_notify'; 
	
	public $options = array();

	public $users_in_roles = array();
	
	private $bridge_warnings = array();
	
	function __construct()
	{
		/* Register hooks, filters and actions */
		
		// This cannot be in is_admin() because it needs to handle future publishing, which doesn't have is_admin() status
		add_action( 'save_post', array( $this, 'notify_on_save' ), 10, 2 );
		
		if ( is_admin() )
		{
			// Add settings to the Dashboard
			add_action( 'admin_init', array( $this, 'admin_settings' ) );
			// Add Settings link to the plugin page
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
			
			// On plugin activation, check whether bbPress is active
			register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
				
			// Deactivate original bbPress Notify if found
			add_action( 'admin_init', array( $this, 'deactivate_old' ) );
			
			// Notification meta boxes if needed
			add_action( 'add_meta_boxes', array( $this, 'add_notification_meta_box' ), 10 );
			
// 			add_action( 'admin_notices', array( $this, 'maybe_show_admin_message' ) );

// 			add_action( 'admin_notices', array( $this, 'maybe_show_surety_message' ) );
			
			add_action( 'wp_ajax_usc_dismiss_notice', array( $this, 'handle_notice_dismissal' ) );
			
			add_action( 'plugins_loaded', array( $this, 'get_bridge_warnings' ), PHP_INT_MAX );
			
		}
		else 
		{
			// Stop timeouts if doing cron.
			if ( defined('DOING_CRON') && DOING_CRON)
			{
				set_time_limit(0);
			}
		}
		
		// New topics and replies can be generated from admin and non-admin interfaces
		
		// Set the bbpress post_types
		add_action( 'init', array( $this, 'set_post_types' ) );
		
		$this->options['bg_topic'] = get_option( 'bbpress_notify_newtopic_background' );
		$this->options['bg_reply'] = get_option( 'bbpress_notify_newreply_background' );
		
		$this->options['subscrp_forum'] = get_option('bbpnns_hijack_bbp_subscriptions_forum');
		$this->options['subscrp_topic'] = get_option('bbpnns_hijack_bbp_subscriptions_topic');
		
		// Triggers the notifications on new topics
		if ( $this->options['bg_topic'] )
		{
			// Store topic vars for wp-cron
			add_action( 'bbp_new_topic', array( $this, 'bg_notify_new_topic' ), 100, 4 );
			
			// Called by wp-cron
			add_action( 'bbpress_notify_bg_topic', array( $this, 'notify_new_topic' ), 10, 4 );
		}
		else
		{
			add_action( 'bbp_new_topic', array( $this, 'notify_new_topic' ), 100, 4 );
		} 
		
		// Triggers the notifications on new replies
		if ( $this->options['bg_reply'] )
		{
			// Store reply vars for wp-cron
			add_action( 'bbp_new_reply', array( $this, 'bg_notify_new_reply' ), 100, 7 );
			
			// Called by wp-cron
			add_action( 'bbpress_notify_bg_reply', array( $this, 'notify_new_reply' ), 10, 7 );
		}
		else
		{
			add_action( 'bbp_new_reply', array( $this, 'notify_new_reply' ), 100, 7 );
		}
		
		
		if ( $this->options['subscrp_forum'] )
		{
			// Stop core subscriptions in its tracks
			add_filter( 'bbp_forum_subscription_mail_message', '__return_false' );
		}
		
		if ( $this->options['subscrp_topic'] )
		{
			// Stop core subscriptions in its tracks
			add_filter( 'bbp_subscription_mail_message', '__return_false' );
		}
		
		// Munge bbpress_notify_newpost_recipients if forum is hidden
		add_filter( 'bbpress_notify_recipients_hidden_forum', array( $this, 'munge_newpost_recipients' ), 10, 3 );
		
		// Allow other plugins to fetch available topic tags
		add_filter( 'bbpnns_available_tags', array( $this, 'get_available_tags' ), 10, 1 ); // deprecated, but still works
		add_filter( 'bbpnns_available_topic_tags', array( $this, 'get_available_topic_tags' ), 10, 1 );
		
		// Allow other plugins to fetch available reply tags
		add_filter( 'bbpnns_available_reply_tags', array( $this, 'get_available_reply_tags' ), 10, 1 );
		
		add_filter( 'bbpnns_is_in_effect', array( $this, 'bbpnns_is_in_effect' ), 10, 2 );
	}
	
	
	/**
	 * Check if bbpnns is in effect (whether because of selected roles or of bbpress core notification Overrides.
	 * @param bool $retval
	 * @param int $user_id
	 * @return boolean
	 */
	public function bbpnns_is_in_effect( $retval=false, $user_id=null )
	{
		// Check if any overrides are on
		if ( ! isset( $this->override_forum ) )
		{
			$this->override_forum = (bool) get_option( 'bbpnns_hijack_bbp_subscriptions_forum', false );
		}

		if ( true === $this->override_forum )
			return true;
		
		if ( ! isset( $this->override_topic ) )
		{
			$this->override_topic = (bool) get_option( 'bbpnns_hijack_bbp_subscriptions_topic', false );
		}
		
		if ( true === $this->override_topic )
			return true;
		
		// Check if the user_id passed is part of the OK'd roles.
		return $this->user_in_ok_role( $user_id );
	}
	
	
	/**
	 * Check if a user is in one of the the OK'd roles.
	 * @param int $user_id
	 */
	public function user_in_ok_role( $user_id=null )
	{
		if ( ! $user_id )
		{
			$user = wp_get_current_user();
			$user_id = $user->ID;
		}
		if ( isset( $this->users_in_roles[$user_id] ) )
			return $this->users_in_roles[$user_id];
	
		if ( empty( $this->topic_roles ) )
			$this->topic_roles = get_option( 'bbpress_notify_newtopic_recipients' );
	
		if ( empty( $this->reply_roles ) )
			$this->reply_roles = get_option( 'bbpress_notify_newreply_recipients' );

		// Start out false
		$this->users_in_roles[$user_id] = false;
	
		foreach ( (array) $this->topic_roles as $role )
		{
			if ( user_can( $user_id, $role ) )
			{
				$this->users_in_roles[$user_id] = true;
				break;
			}
		}
	
		foreach ( (array) $this->reply_roles as $role )
		{
			if ( user_can( $user_id, $role ) )
			{
				$this->users_in_roles[$user_id] = true;
				break;
			}
		}
	
		return $this->users_in_roles[$user_id];
	}
	
	
	/**
	 * @since 1.5
	 * @desc Make it a singleton
	 * @return bbPress_Notify_noSpam
	 */
	public static function bootstrap()
	{
		// Make sure bbPress is still installed and avoid race conditions
		if ( ! class_exists( 'bbPress' ) )
		{
			if ( 'plugins_loaded' !== current_filter() )
			{
				add_action( 'plugins_loaded', array( 'bbPress_Notify_NoSpam', 'bootstrap' ), 100000 );
			}
			else
			{
				add_action( 'admin_notices', array( 'bbPress_Notify_NoSpam', 'missing_bbpress_notice' ) );
			}
			
			return false;
		}
		
		
		// bbPress is here, so let's load ourselves 
		if ( ! isset( self::$instance ) )
			self::$instance = new self();
		
		return self::$instance;
	}

	function set_post_types()
	{
		$this->bbpress_topic_post_type = bbp_get_topic_post_type();
		$this->bbpress_reply_post_type = bbp_get_reply_post_type();
	}
 	
	function bg_notify_new_reply( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $reply_author = 0, $bool=false, $reply_to=null )
	{
		wp_schedule_single_event( time() + 10, 'bbpress_notify_bg_reply', array( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, $bool, $reply_to ) );
	}
	
	
	function bg_notify_new_topic( $topic_id = 0, $forum_id = 0, $anonymous_data = false, $topic_author = 0 )
	{
		wp_schedule_single_event( time() + 10, 'bbpress_notify_bg_topic', array( $topic_id, $forum_id, $anonymous_data, $topic_author ) );
	}
	
	
	function deactivate_old()
	{
		$old_plugin = 'bbpress-notify/bbpress-notify.php';
		if ( is_plugin_active( $old_plugin ) )
		{
			deactivate_plugins( $old_plugin );
		}
	
	}
	
	/**
	 * Deprecated, the project did not get backing.
	 */
	function maybe_show_admin_message()
	{
		$old_key     = 'bbpnns-dismissed-1_7_1';
		$dismiss_key = 'bbpnns-opt-out-msg';
		
		if ( isset( $_GET[$dismiss_key] )  )
		{
			delete_option( 'bbpress-notify-pro-dismissed' );
			update_option( $dismiss_key, true );
		}
		elseif ( ! get_option( $old_key ) || ! get_option( $dismiss_key ) )
		{
			$add_on_url  = 'http://usestrict.net/2015/03/bbpress-notify-no-spam-opt-out-add-on/'; 
			$dismiss_url = esc_url( add_query_arg( array( $dismiss_key => 1 ), $_SERVER['REQUEST_URI'] ) );
            ?>
				<div class="updated">
                <p><?php _e( sprintf( '<div style="display:inline">Users asked and we delivered! Allow your subscribers to opt-out 
									   from receiving your notifications with <a href="%s" target="_new"><strong>bbPress Notify ( No Spam ) Opt Out Add On</strong></a>.</div> 
									   <div style="float:right"><a href="%s">Dismiss</a></div>', $add_on_url, $dismiss_url ), $this->domain ); ?></p>
				</div>
			<?php
		}
	}
	
	
	/**
	 * Maybe Show the Surety Mail notice
	 */
	function maybe_show_surety_message()
	{
		$notice    = sanitize_text_field( 'surety' );
		$dismissed = (bool) get_site_option( "{$notice}_dismissed", $default=false );  
		
		if ( true === $dismissed )
			return;
		
		?>
		<div id="usc_surety_notice" class="updated notice is-dismissible">
                <p><?php _e( sprintf( 'Is the email you send getting hung up by spam filters?
Losing sales and traffic to the junk folder? Because you are using <strong>bbPress Notify (No-Spam)</strong>, you may qualify for <a href="%s" target="_new">SuretyMail for Wordpress inbox delivery optimization!
Learn more here!</a>', 'https://usestrict.net/go/suretymail4wp'), $this->domain ); ?></p>
		</div>
		<script>
jQuery(document).ready(function($){
	$(document).on('click', '#usc_surety_notice .notice-dismiss', function(){
		$.ajax({
			method: 'POST',
			url: ajaxurl,
			data: {
				action: 'usc_dismiss_notice',
				usc_nonce: '<?php echo wp_create_nonce( "usc_dismiss_{$notice}_nonce" ); ?>',
				notice: '<?php echo $notice; ?>'
			}
		});
	});
});
		</script>
		<?php 
	}
	
	/**
	 * Handles Ajax notice dismissal calls  
	 */
	function handle_notice_dismissal()
	{
		$notice = null;
		if ( isset( $_POST['notice'] ) )
		{
			$notice = sanitize_text_field( $_POST['notice'] );
		}
		
		if ( $notice && check_ajax_referer( "usc_dismiss_{$notice}_nonce", 'usc_nonce' ) )
		{
			update_site_option( "{$notice}_dismissed", true );
		}
	}
	
	
	/* Checks whether bbPress is active because we need it. If bbPress isn't active, we are going to disable ourself */
	function on_activation()
	{
		if( !class_exists( 'bbPress' ) )
		{
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Sorry, you need to activate bbPress first.', 'bbpress_notify' ) );
		}
	
		// Default settings
		if ( !get_option( 'bbpress_notify_newtopic_background' ) )
		{
			update_option( 'bbpress_notify_newtopic_background', 0 );
		}
		if ( !get_option( 'bbpress_notify_newreply_background' ) )
		{
			update_option( 'bbpress_notify_newreply_background', 0 );
		}
		if ( !get_option( 'bbpress_notify_newtopic_recipients' ) )
		{
			update_option( 'bbpress_notify_newtopic_recipients', array( 'administrator' ) );
		}
		if ( !get_option( 'bbpress_notify_newreply_recipients' ) )
		{
			update_option( 'bbpress_notify_newreply_recipients', array( 'administrator' ) );
		}
		if ( !get_option( 'bbpress_notify_newtopic_email_subject' ) )
		{
			update_option( 'bbpress_notify_newtopic_email_subject', __( '[[blogname]] New topic: [topic-title]' ) );
		}
		if ( !get_option( 'bbpress_notify_newtopic_email_body' ) )
		{
			update_option( 'bbpress_notify_newtopic_email_body', __( "Hello!\nA new topic has been posted by [topic-author].\nTopic title: [topic-title]\nTopic url: [topic-url]\n\nExcerpt:\n[topic-excerpt]" ) );
		}
		if ( !get_option( 'bbpress_notify_newreply_email_subject' ) )
		{
			update_option( 'bbpress_notify_newreply_email_subject', __( '[[blogname]] [reply-title]' ) );
		}
		if ( !get_option( 'bbpress_notify_newreply_email_body' ) )
		{
			update_option( 'bbpress_notify_newreply_email_body', __( "Hello!\nA new reply has been posted by [reply-author].\nTopic title: [reply-title]\nTopic url: [reply-url]\n\nExcerpt:\n[reply-excerpt]" ) );
		}
		if ( !get_option( "bbpress_notify_default_{$this->bbpress_topic_post_type}_notification" ) )
		{
			update_option( "bbpress_notify_default_{$this->bbpress_topic_post_type}_notification", 0 );
		}
		if ( !get_option( "bbpress_notify_default_{$this->bbpress_reply_post_type}_notification" ) )
		{
			update_option( "bbpress_notify_default_{$this->bbpress_reply_post_type}_notification", 0 );
		}
		if ( ! get_option( 'bbpress_notify_encode_subject' ) )
		{
			update_option( 'bbpress_notify_encode_subject', 1 );
		}
		if ( ! get_option( 'bbpnns_notify_authors_topic' ) )
		{
			update_option( 'bbpnns_notify_authors_topic', 0 );
		}
		if ( ! get_option( 'bbpnns_notify_authors_reply' ) )
		{
			update_option( 'bbpnns_notify_authors_reply', 0 );
		}
	}
	
	
	/**
	 * @since 1.0
	 */
	function notify_new_topic( $topic_id = 0, $forum_id = 0 )
	{
		global $wpdb;

		$status = get_post_status( $topic_id ); 

		if (   in_array( $status, (array) apply_filters( 'bbpnns_post_status_blacklist', array( 'spam' ), $status, $forum_id, $topic_id, $reply_id=false ) ) || 
			 ! in_array( $status, (array) apply_filters( 'bbpnns_post_status_whitelist', array( 'publish' ), $status, $forum_id, $topic_id, $reply_id=false ) ) )
			return -1;

		if ( 0 === $forum_id )
			$forum_id = bbp_get_topic_forum_id( $topic_id );

		if ( true === apply_filters( 'bbpnns_skip_topic_notification', false, $forum_id, $topic_id ) )
			return -3;

		$recipients = $this->get_recipients( $forum_id, 'topic' );

		if ( false === (bool) apply_filters( 'bbpnns_notify_authors_topic', get_option( 'bbpnns_notify_authors_topic' ) ) )
		{
			/**
			 * Remove topic author from the recipient list
			 * @since 1.9.4
			 */
			$author_id = bbp_get_topic_author_id( $topic_id );
			
			unset( $recipients[ $author_id ] );
		}
		
		/**
		 * Allow topic recipients munging
		 * @since 1.6.5
		 */
		$recipients = apply_filters( 'bbpress_topic_notify_recipients', $recipients, $topic_id, $forum_id );

		if ( empty( $recipients ) )
			return -2;

		list( $email_subject, $email_body ) = $this->_build_email( 'topic', $topic_id );

		return $this->send_notification( $recipients, $email_subject, $email_body, $type='topic', $topic_id, $forum_id );
	}
	

	/**
	 * Extracted get_recipients code to its own method
	 * @since 1.9
	 * @param int $forum_id
	 * @param string $type
	 */
	public function get_recipients( $forum_id, $type, $topic_id=null )
	{
		$opt = ( 'topic' === $type ) ? 'bbpress_notify_newtopic_recipients' : 'bbpress_notify_newreply_recipients';
		
		$roles = get_option( $opt );
		$roles = apply_filters( 'bbpress_notify_recipients_hidden_forum', $roles, $type, $forum_id );
		
		$recipients = array();
		foreach ( ( array ) $roles as $role )
		{
			if ( ! $role ) continue;
		
			$users = get_users( array( 'role' => $role ) );
			foreach ( ( array ) $users as $user )
			{
				$recipients[$user->ID] = $user; // make sure unique recipients
			}
		}

		// Core subscribers logic
		$subscrp_active = bbp_is_subscriptions_active();
		$subscribers = array();
		if ( $this->options['subscrp_forum'] && $subscrp_active && 'topic' === $type )
		{
			$subscribers = bbp_get_forum_subscribers( $forum_id );
		}
		elseif( $this->options['subscrp_topic'] && $subscrp_active && 'reply' === $type )
		{
			$subscribers = bbp_get_topic_subscribers( $topic_id );
		}

		/**
		 * Allow subscribers to be accessed/changed by other plugins. Introduced for the opt-out add-on.
		 * @since 1.15.4
		 */
		$subscribers = apply_filters( 'bbpnns_core_subscribers', $subscribers );
			
		foreach ( (array) $subscribers as $sub_id )
		{
			if ( isset( $recipients[$sub_id] ) )
				continue;
								
			$recipients[$sub_id] = new WP_User( $sub_id );
		}
		
		return $recipients;
	}
	
	/**
	 * @since 1.5
	 * @desc Forces admin-only recipients if forum is hidden
	 * @param array $type
	 * @param number $topic_id
	 * @return array
	 */
	public function munge_newpost_recipients( $roles=array(), $type, $forum_id = 0 )
	{
		if ( true === ( bool ) bbp_is_forum_hidden( $forum_id ) &&
		     true === ( bool ) get_option( "bbpress_notify_hidden_forum_{$type}_override", true ) )
		{
			$roles = array('administrator');
		}
	
		return $roles;
	}
	
	
	/**
	 * @since 1.0
	 */
	function notify_new_reply( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $reply_author = 0, $bool = false, $reply_to = null )
	{
		global $wpdb;

		$status = get_post_status( $reply_id ); 

		if ( 0 === $forum_id )
			$forum_id = bbp_get_reply_forum_id( $reply_id );
		
		if ( 0 === $topic_id )
			$topic_id = bbp_get_reply_topic_id( $reply_id );
		
		if (   in_array( $status, (array) apply_filters( 'bbpnns_post_status_blacklist', array( 'spam' ), $status, $forum_id, $topic_id, $reply_id ) ) || 
			 ! in_array( $status, (array) apply_filters( 'bbpnns_post_status_whitelist', array( 'publish' ), $status, $forum_id, $topic_id, $reply_id ) ) )
			return -1;

		if ( true === apply_filters( 'bbpnns_skip_reply_notification', false, $forum_id, $topic_id, $reply_id ) )
			return -3;


		$recipients = $this->get_recipients( $forum_id, 'reply', $topic_id );
		
		if ( false === (bool) apply_filters( 'bbpnns_notify_authors_reply', get_option( 'bbpnns_notify_authors_reply' ) ) )
		{
			/**
			 * Remove reply author from recipients
			 * @since 1.9.4
			 */
			$author_id = bbp_get_reply_author_id( $reply_id );
			
			unset( $recipients[ $author_id ] );
		}

		/**
		 * Allow reply recipients munging
		 * @since 1.6.5
		 */
		$recipients = apply_filters( 'bbpress_reply_notify_recipients', $recipients, $reply_id, $topic_id, $forum_id );

		if ( empty( $recipients ) )
			return -2;

		list( $email_subject, $email_body ) = $this->_build_email( 'reply', $reply_id );

		return $this->send_notification( $recipients, $email_subject, $email_body, $type='reply', $reply_id, $forum_id );
	}
	
	
	/**
	 * @since 1.4
	 */
	private function _build_email( $type, $post_id )
	{
		$email_subject = wp_specialchars_decode( get_option( "bbpress_notify_new{$type}_email_subject" ), ENT_QUOTES );
		$email_body    = wp_specialchars_decode( get_option( "bbpress_notify_new{$type}_email_body" ), ENT_QUOTES );
		
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$excerpt_size = apply_filters( 'bpnns_excerpt_size', 100 );
		
		// Replace shortcodes
		if ( 'topic' === $type )
		{
			$content = bbp_get_topic_content( $post_id );
			$title   = wp_specialchars_decode( strip_tags( bbp_get_topic_title( $post_id ) ), ENT_QUOTES );
			$excerpt = wp_specialchars_decode( strip_tags( bbp_get_topic_excerpt( $post_id, $excerpt_size ) ), ENT_QUOTES );
			$author  = bbp_get_topic_author( $post_id );
			$url     = apply_filters( 'bbpnns_topic_url', bbp_get_topic_permalink( $post_id ), $post_id, $title );
			$forum 	 = wp_specialchars_decode( strip_tags( get_the_title( bbp_get_topic_forum_id( $post_id ) ) ), ENT_QUOTES );
		}
		elseif ( 'reply' === $type )
		{
			$content = bbp_get_reply_content( $post_id );
			$title   = wp_specialchars_decode( strip_tags( bbp_get_reply_title( $post_id ) ), ENT_QUOTES );
			$excerpt = wp_specialchars_decode( strip_tags( bbp_get_reply_excerpt( $post_id, $excerpt_size ) ), ENT_QUOTES );
			$author  = bbp_get_reply_author( $post_id );
			$url     = apply_filters( 'bbpnns_reply_url', bbp_get_reply_permalink( $post_id ), $post_id, $title );
			$forum 	 = wp_specialchars_decode( strip_tags( get_the_title( bbp_get_reply_forum_id( $post_id ) ) ), ENT_QUOTES );
			
			// Topic-specific stuff in replies
			$topic_id     = bbp_get_reply_topic_id( $post_id );
			$topic_title  = wp_specialchars_decode( strip_tags( bbp_get_topic_title( $topic_id ) ), ENT_QUOTES );
			$topic_author = bbp_get_topic_author( $topic_id );
			$topic_author_email = bbp_get_topic_author_email( $topic_id );
			
			$topic_content = '';
			if ( false !== strpos( $email_body, '[topic-content]' ) )
			{
				$topic_content = bbp_get_topic_content( $topic_id );
				$topic_content = preg_replace( '/<br\s*\/?>/is', PHP_EOL, $topic_content );
				$topic_content = preg_replace( '/(?:<\/p>\s*<p>)/ism', PHP_EOL . PHP_EOL, $topic_content );
				$topic_content = wp_specialchars_decode( $topic_content, ENT_QUOTES );
			}
			
			$topic_excerpt = '';
			if ( false !== strpos( $email_body, '[topic-excerpt]' ) )
			{
				$topic_excerpt = wp_specialchars_decode( strip_tags( bbp_get_topic_excerpt( $topic_id, $excerpt_size ) ), ENT_QUOTES );
			}
			
		}
		else 
		{
			wp_die( 'Invalid type!' );
		}
		
		$content = preg_replace( '/<br\s*\/?>/is', PHP_EOL, $content );
		$content = preg_replace( '/(?:<\/p>\s*<p>)/ism', PHP_EOL . PHP_EOL, $content );
		$content = wp_specialchars_decode( $content, ENT_QUOTES );
		
		$topic_reply = apply_filters( 'bbpnns_topic_reply', bbp_get_reply_url( $post_id ), $post_id, $title );
		
		$author_email = 'topic' === $type ? bbp_get_topic_author_email( $post_id ) : bbp_get_reply_author_email( $post_id ); 
		
		$email_subject = str_replace( '[blogname]', $blogname, $email_subject );
		$email_subject = str_replace( "[$type-title]", $title, $email_subject );
		$email_subject = str_replace( "[$type-content]", $content, $email_subject );
		$email_subject = str_replace( "[$type-excerpt]", $excerpt, $email_subject );
		$email_subject = str_replace( "[$type-author]", $author, $email_subject );
		$email_subject = str_replace( "[$type-url]", $url, $email_subject );
		$email_subject = str_replace( "[$type-replyurl]", $topic_reply, $email_subject );
		$email_subject = str_replace( "[$type-forum]", $forum, $email_subject );
		$email_subject = str_replace( "[$type-author-email]", $author_email, $email_subject );
		
		$email_body = str_replace( '[blogname]', $blogname, $email_body );
		$email_body = str_replace( "[$type-title]", $title, $email_body );
		$email_body = str_replace( "[$type-content]", $content, $email_body );
		$email_body = str_replace( "[$type-excerpt]", $excerpt, $email_body );
		$email_body = str_replace( "[$type-author]", $author, $email_body );
		$email_body = str_replace( "[$type-url]", $url, $email_body );
		$email_body = str_replace( "[$type-replyurl]", $topic_reply, $email_body );
		$email_body = str_replace( "[$type-forum]", $forum, $email_body );
		$email_body = str_replace( "[$type-author-email]", $author_email, $email_body );
		
		/**
		 * Also do some topic tag replacement in replies. See https://wordpress.org/support/topic/tags-for-reply-e-mail-body/
		 * @since 1.15.3
		 */
		if ( 'reply' === $type )
		{
			$email_subject = str_replace( "[topic-title]", $topic_title, $email_subject );
			$email_subject = str_replace( "[topic-author]", $topic_author, $email_subject );
			$email_subject = str_replace( "[topic-author-email]", $topic_author_email, $email_subject );
			
			$email_body = str_replace( "[topic-title]", $topic_title, $email_body );
			$email_body = str_replace( "[topic-author]", $topic_author, $email_body );
			$email_body = str_replace( "[topic-author-email]", $topic_author_email, $email_body );
			$email_body = str_replace( "[topic-content]", $topic_content, $email_body );
			$email_body = str_replace( "[topic-excerpt]", $topic_excerpt, $email_body );
			
			if ( strpos( $email_body, '[topic-url]' ) || strpos( $email_subject, '[topic-url]' ) )
			{
				$topic_id  = bbp_get_reply_topic_id( $post_id );
				$topic_url = apply_filters( 'bbpnns_topic_url', bbp_get_topic_permalink( $topic_id ), $topic_id, $title );
					
				$email_subject = str_replace( '[topic-url]', $topic_url, $email_subject );
				$email_body    = str_replace( '[topic-url]', $topic_url, $email_body );
			}
		}
		
		/**
		 * Allow subject and body modifications
		 * @since 1.6.6
		 */
		$email_subject = apply_filters( 'bbpnns_filter_email_subject_in_build', $email_subject, $type, $post_id );
		$email_body    = apply_filters( 'bbpnns_filter_email_body_in_build', $email_body, $type, $post_id );
		
		return array( $email_subject, $email_body );
	}
	
	
	/**
	 * @since 1.14
	 * @param WP_Error $wp_error
	 */
	public function capture_wp_mail_failure( WP_Error $wp_error )
	{
		$this->wp_mail_error = $wp_error;
	}

	/**
	 * Used by send_notification to set the correct content type.
	 * @since 1.14
	 * @param unknown $content_type
	 * @return string
	 */
	public function set_content_type( $content_type )
	{
		if ( ! isset( $this->message_type ) )
			$this->message_type = get_option( 'bbpress_notify_message_type', 'html' );
		
		switch( $this->message_type )
		{
			case 'html':
			case 'multipart':
				$content_type = 'text/html';
				break;
			default:
				$content_type = 'text/plain';	
		}
		
		return $content_type;		
	}
	
	
	/**
	 * If the admin selected Multipart messages, this is where we set the AltBody for $phpmailer, that automagically transforms
	 * HTML messages into Multipart ones.
	 * @param unknown $phpmailer
	 */
	public function set_alt_body( $phpmailer )
	{
		$phpmailer->AltBody = wp_strip_all_tags( $this->convert_images_and_links( $this->AltBody ) );
	}
	
	public function add_signature_header( $phpmailer )
	{
		$sig = sprintf( 'bbPress Notify (No-Spam) v.%s (%s)', self::VERSION, 'http://wordpress.org/plugins/bbpress-notify-nospam/' );
		$phpmailer->addCustomHeader( 'X-Built-By', $sig );	
	}
	
	
	/**
	 * @since 1.0
	 */
	public function send_notification( $recipients, $subject, $body, $type='', $post_id='', $forum_id='' )
	{
		$this->message_type = get_option( 'bbpress_notify_message_type', 'html' );
		
		// Set the content type
		add_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ), 1000, 1);
		
		// Capture wp_mail failure
		add_action( 'wp_mail_failed', array( $this, 'capture_wp_mail_failure' ), 10, 1 );
		
		$headers = array( sprintf( "From: %s <%s>", get_option( 'blogname' ), get_bloginfo( 'admin_email' ) ) );
		
		$headers = apply_filters( 'bbpnns_extra_headers', $headers, $recipients, $subject, $body );
		
		add_action( 'phpmailer_init', array( $this, 'add_signature_header') );

		// Allow Management of recipients list
		$recipients = apply_filters( 'bbpnns_filter_recipients_before_send', $recipients );

		/**
		 * This is a workaround for cases where UTF-8 characters were blocking the message.
		 * Run these functions outside the loop for better performance.
		 */ 
		$do_enc = ( bool ) get_option( 'bbpress_notify_encode_subject', false );
		$preferences = array();
		
		/**
		 * We use this in prep_image_cid() and convert_links().
		 * Load it just once.
		 */
		$this->charset = get_bloginfo( 'charset' );
		
		if ( true === $do_enc && function_exists( 'iconv_get_encoding' ) )
		{
			$enc         = iconv_get_encoding( 'internal_encoding' );
			$preferences = apply_filters( 'bbpnns_subject_enc_preferences',
					array( 'input-charset' => $enc, 'output-charset' => "UTF-8", 'scheme' => 'Q' )
			);
		}
		
		// Evaluate this only once, check many
		$is_dry_run = apply_filters( 'bbpnns_dry_run', false );
		
		foreach ( ( array ) $recipients as $recipient_id => $user_info )
		{
			/**
			 * Allow skipping user during notification
			 * @since 1.6.4 
			 */ 
			$email = ( $recipient_id == -1 ) ? get_bloginfo( 'admin_email' ) : ( string ) $user_info->user_email ;
			$email = apply_filters( 'bbpnns_skip_notification', $email ); // Allow user to be skipped for some reason

			if ( ! empty( $email ) && false === $is_dry_run )
			{
				/**
				 * Allow per user subject and body modifications
				 * @since 1.6.4
				 */
				$filtered_body    = apply_filters( 'bbpnns_filter_email_body_for_user', $body, $user_info, $type, $post_id, $forum_id );
				$filtered_subject = apply_filters( 'bbpnns_filter_email_subject_for_user', $subject, $user_info, $type, $post_id, $forum_id );
				
				/**
				 * Replace user name tags
				 * @since 1.14
				 */
				foreach ( array( 'first_name', 'last_name', 'display_name', 'user_nicename' ) as $prop )
				{
					$filtered_body    = str_replace( "[recipient-{$prop}]", $user_info->{$prop}, $filtered_body );
					$filtered_subject = str_replace( "[recipient-{$prop}]", $user_info->{$prop}, $filtered_subject );
				}
			
				/**
				 * Multipart messages
				 * @since 1.14
				 */
				switch( $this->message_type )
				{
					case 'multipart':
						$this->AltBody = $filtered_body;
						if ( ! has_action( 'phpmailer_init', array( $this, 'set_alt_body' ) ) )
						{
							add_action( 'phpmailer_init', array( $this, 'set_alt_body' ), 1001, 1);
						}
					case 'html':
						$filtered_body = $this->prep_image_cid( $filtered_body );
						$filtered_body = wpautop( $filtered_body ); // Handle missing p tags.
						if ( ! has_action( 'phpmailer_init', array( $this, 'embed_images' ) ) )
						{
							add_action( 'phpmailer_init', array( $this, 'embed_images' ), 1000, 1);
						} 
						break;
					case 'plain':
						$filtered_body = wp_strip_all_tags( $this->convert_images_and_links( $filtered_body ) );
						break;
					default:
				}
				
				/**
				 * Make this optional
				 * @since 1.9.3
				 */
				if ( true === $do_enc && function_exists( 'iconv_mime_encode' ) )
				{
					/**
					 * Enable UTF-8 characters in subject line
					 * @since 1.9
					 */
					$filtered_subject = iconv_mime_encode( 'Subject', $filtered_subject, $preferences );
					$filtered_subject = substr( $filtered_subject, strlen( 'Subject:' ) );
				}
				
				/**
				 * User headers, if any
				 */
				$recipient_headers = apply_filters( 'bbpnns_extra_headers_recipient', $headers, $user_info, $filtered_subject, $filtered_body );
				
				do_action( 'bbpnns_before_wp_mail', $user_info, $filtered_subject, $filtered_body, $recipient_headers );
				
				// For debugging
// 				add_action( 'phpmailer_init', function($pm){  $pm->preSend(); error_log(__LINE__ . ' message: ' . print_r($pm->getSentMIMEMessage(),1), 3, dirname(__FILE__) . '/out.log' ); });
				
				// Turn on nl2br for wpMandrill
				add_filter( 'mandrill_nl2br', array( $this, 'handle_mandrill_nl2br' ), 10, 2 );
				
				if ( ! wp_mail( $email, $filtered_subject, $filtered_body, $recipient_headers ) )
				{
					do_action( 'bbpnns_email_failed_single_user', $user_info, $filtered_subject, $filtered_body, $recipient_headers, $this->wp_mail_error );
					do_action( 'bbpnns_after_wp_mail', $user_info, $filtered_subject, $filtered_body, $recipient_headers );
					
					// Turn off nl2br for wpMandrill
					remove_filter( 'mandrill_nl2br', array( $this, 'handle_mandrill_nl2br' ), 10 );
					continue;
				}
				
				do_action( 'bbpnns_after_wp_mail', $user_info, $filtered_subject, $filtered_body, $recipient_headers );
				
				// Turn off nl2br for wpMandrill
				remove_filter( 'mandrill_nl2br', array( $this, 'handle_mandrill_nl2br' ), 10 );
				
				do_action( 'bbpnns_after_email_sent_single_user', $user_info, $filtered_subject, $filtered_body );
			}
		}
		
		do_action( 'bbpnns_after_email_sent_all_users', $recipients, $filtered_subject, $filtered_body );
		
		if ( true === apply_filters( 'bbpnns_dry_run', false ) )
			return array( $recipients, $body );
		
		return true;
	}
	
	
	/**
	 * Embeds images into message
	 * @param PHPMailer $phpmailer
	 */
	public function embed_images( $phpmailer )
	{
		if ( empty( $this->cid_ary ) )
			return;
		
		foreach ( $this->cid_ary as $el )
		{
			if ( false === $el )
				continue;
			
			$filepath = $el['filepath'];
			$name     = $el['filename'];
			$cid      = $el['cid'];
			
			$phpmailer->AddEmbeddedImage( $filepath, $cid, $name );
		}
	}
	
	
	/**
	 * Process images to embed in the email.
	 * @param string $text
	 * @return string
	 */
	public function prep_image_cid( $text )
	{
		if ( ! isset( $this->cid_ary ) )
		{
			$this->cid_ary = array();
		}
		
		$dom = new DOMDocument();

		$previous_value = libxml_use_internal_errors(TRUE);
		
		$dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', $this->charset));
				
		libxml_use_internal_errors($previous_value);
		
		$local       = parse_url( get_option( 'siteurl' ) );
		$this_domain = strtolower( $local['host'] );
		
		$images = $dom->getElementsByTagName('img');
		
		foreach ( $images as $img )
		{
			$src = $img->getAttribute('src');

			if ( $src && ! isset( $this->cid_ary[$src] ) )
			{
				$parsed = parse_url( $src );
				
				if ( $this_domain === strtolower( $parsed['host'] ) )
				{
					$path     = ABSPATH . $parsed['path'];
					$parts    = pathinfo( $path );
					$ext      = strtolower( $parts['extension'] );
					$filename = $parts['filename'];
					
					// We only allow images
					if ( in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif' ) ) )
					{
						$cid = uniqid();
						$this->cid_ary[$src] = array( 'cid' => $cid, 'filepath' => $path, 'filename' => $filename );
					}
					else
					{
						$this->cid_ary[$src] = false;
					}
				}
				else 
				{
					// NOOP: Only embed local images for safety
				}
			}
			
			if ( false !== $this->cid_ary[$src] )
			{
				$img->setAttribute( 'src', 'cid:' . $this->cid_ary[$src]['cid'] );
			}
		}
		
		if ( $images )
		{
			$text = $dom->saveHTML($dom->documentElement->lastChild);
			$text = preg_replace('@^<body>|</body>$@', '', $text );
		}
		
		
		return $text;
	}
	
	/**
	 * Make sure we keep our links instead of stripping them out along with the rest of the HTML. 
	 * @param string $text
	 * @return string|unknown
	 */
	public function convert_images_and_links( $text )
	{
		$dom = new DOMDocument();
	
		$previous_value = libxml_use_internal_errors(TRUE);
	
		$dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', $this->charset));
	
		libxml_use_internal_errors($previous_value);
	
		$elements = $dom->getElementsByTagName( 'a' );
	
		foreach ( $elements as $el )
		{
			$href  = $el->getAttribute('href');
				
			// Capture links that have only images in them.
			foreach ( $el->getElementsByTagName('img') as $img )
			{
				$alt = $img->getAttribute('alt');
				$src = $img->getAttribute('src');
	
				$img_text = '*image*';
				if ( $alt )
				{
					$img_text = $alt;
				}
				elseif ( 'cid:' === substr( $src, 0, 4) )
				{
					$cid = substr( $src, 3 );
					foreach ( $this->cid_ary as $osrc => $el )
					{
						if ( $osrc === $src )
						{
							$img_text = $el['filename'];
							break;
						}
					}
				}
				else
				{
					$img_text = basename( $src );
				}
				
				$img->nodeValue = sprintf( '[img]%s[/img]', $img_text );
			}
				
			$el->nodeValue = sprintf( '(%s) [%s]', $el->nodeValue, $href );
		}
	
		// Unlinked images now
		foreach ( $dom->getElementsByTagName('img') as $img )
		{
			$alt = $img->getAttribute('alt');
			$src = $img->getAttribute('src');

			$img_text = '*image*';
			if ( $alt )
			{
				$img_text = $alt;
			}
			elseif ( 'cid:' === substr( $src, 0, 4) )
			{
				$cid = substr( $src, 3 );
				foreach ( $this->cid_ary as $osrc => $el )
				{
					if ( $osrc === $src )
					{
						$img_text = $el['filename'];
						break;
					}
				}
			}
			else
			{
				$img_text = basename( $src );
			}
			
			$img->nodeValue = sprintf( '[img]%s[/img]', $img_text );
		}
	
	
		if ( $elements )
		{
			$text = $dom->documentElement->lastChild->nodeValue;
		}
	
		return $text;
	}
	
	
	
	/**
	 * On-the-fly handling of nl2br by Mandrill
	 * @param bool $nl2br
	 * @param array $message
	 * @return bool
	 */
	public function handle_mandrill_nl2br( $nl2br, $message )
	{
		$bbpnns_nl2b2_option = apply_filters( 'bbpnns_handle_mandrill_nl2br', true, $nl2br, $message ); 
		
		return $bbpnns_nl2b2_option;
	}
	
	/**
	 * @since 1.0
	 */
	/* Add the settings to the bbPress page in the Dashboard */
	function admin_settings() {
		// Add section to bbPress options
		add_settings_section( $this->settings_section, __( 'E-mail Notifications', 'bbpress_notify' ), array( $this, '_settings_intro_text' ), 'bbpress' );

		add_settings_field( 'bbpress_notify_encode_subject', __( 'Encode Topic and Reply Subject line', 'bbpress_notify' ), array( $this, '_encode_subject' ), 'bbpress', 'bbpress_notify_options' );

		// Decide whether to send multipart messages
		add_settings_field( 'bbpress_notify_message_type', __( 'E-mail Type', 'bbpress_notify' ), array( $this, '_message_type' ), 'bbpress', 'bbpress_notify_options' );
		
		
		// Hook for additional topic settings
		do_action( 'bbpnns_before_topic_settings' );
		
		// Add background option
		add_settings_field( 'bbpress_notify_newtopic_background', __( 'Background Topic Notifications', 'bbpress_notify' ), array( $this, '_topic_background_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		
		// Add default notification option
		add_settings_field( "bbpress_notify_default_{$this->bbpress_topic_post_type}_notification", __( 'Admin UI Topic Notifications', 'bbpress_notify' ), array( $this, '_admin_ui_topic_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		
		// Add form fields for all topic settings
		// Hijack bbPress Topic Subscriptions
		add_settings_field( 'bbpnns_hijack_bbp_subscriptions_forum', __( 'bbPress Forums Subscriptions Override', 'bbpress_notify' ), array( $this, '_hijack_bbp_subscriptions_topic' ), 'bbpress', 'bbpress_notify_options' );
		add_settings_field( 'bbpress_notify_newtopic_recipients', __( 'Topic Recipent Roles', 'bbpress_notify' ), array( $this, '_topic_recipients_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		add_settings_field( 'bbpress_notify_notify_authors_topic', __( 'Notify authors of their own Topics', 'bbpress_notify' ), array( $this, '_notify_authors_topic' ), 'bbpress', 'bbpress_notify_options' );
		
		add_settings_field( 'bbpress_notify_hidden_forum_topic_override', __( 'Force Admin-only emails if Forum is hidden ( topics )', 'bbpress_notify' ), array( $this, '_hidden_forum_topic_override' ), 'bbpress', 'bbpress_notify_options' );
		
		add_settings_field( 'bbpress_notify_newtopic_email_subject', __( 'Topic E-mail subject', 'bbpress_notify' ), array( $this, '_email_newtopic_subject_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		add_settings_field( 'bbpress_notify_newtopic_email_body', __( 'Topic E-mail body', 'bbpress_notify' ), array( $this, '_email_newtopic_body_inputfield' ), 'bbpress', 'bbpress_notify_options' );

		// Hook for additional topic settings
		do_action( 'bbpnns_after_topic_settings' );
		
		add_settings_field( 'bbpress_notify_newreply_background', __( 'Background Reply Notifications', 'bbpress_notify' ), array( $this, '_reply_background_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		
		// Add default notification option
		add_settings_field( "bbpress_notify_default_{$this->bbpress_reply_post_type}_notification", __( 'Admin UI Reply Notifications', 'bbpress_notify' ), array( $this, '_admin_ui_reply_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		
		add_settings_field( 'bbpnns_hijack_bbp_subscriptions_topic', __( 'bbPress Topics Subscriptions Override', 'bbpress_notify' ), array( $this, '_hijack_bbp_subscriptions_reply' ), 'bbpress', 'bbpress_notify_options' );
		
		add_settings_field( 'bbpress_notify_newreply_recipients', __( 'Reply Recipent Roles', 'bbpress_notify' ), array( $this, '_reply_recipients_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		add_settings_field( 'bbpress_notify_notify_authors_reply', __( 'Notify authors of their own Replies', 'bbpress_notify' ), array( $this, '_notify_authors_reply' ), 'bbpress', 'bbpress_notify_options' );
		add_settings_field( 'bbpress_notify_hidden_forum_reply_override', __( 'Force Admin-only emails if Forum is hidden ( replies )', 'bbpress_notify' ), array( $this, '_hidden_forum_reply_override' ), 'bbpress', 'bbpress_notify_options' );
		
		
		add_settings_field( 'bbpress_notify_newreply_email_subject', __( 'Reply E-mail subject', 'bbpress_notify' ), array( $this, '_email_newreply_subject_inputfield' ), 'bbpress', 'bbpress_notify_options' );
		add_settings_field( 'bbpress_notify_newreply_email_body', __( 'Reply E-mail body', 'bbpress_notify' ), array( $this, '_email_newreply_body_inputfield' ), 'bbpress', 'bbpress_notify_options' );
	
		// Hook for additional reply settings
		do_action( 'bbpnns_after_reply_settings' );
		
		// Register the settings as part of the bbPress settings
		register_setting( 'bbpress', 'bbpress_notify_newtopic_recipients' );
		register_setting( 'bbpress', 'bbpress_notify_hidden_forum_topic_override' );
		register_setting( 'bbpress', 'bbpress_notify_hidden_forum_reply_override' );
		register_setting( 'bbpress', 'bbpress_notify_newtopic_email_subject' );
		register_setting( 'bbpress', 'bbpress_notify_newtopic_email_body' );
		register_setting( 'bbpress', 'bbpress_notify_newtopic_background' );
		register_setting( 'bbpress', 'bbpnns_hijack_bbp_subscriptions_forum' );
		register_setting( 'bbpress', 'bbpnns_notify_authors_topic' );
	
		register_setting( 'bbpress', 'bbpress_notify_newreply_recipients' );
		register_setting( 'bbpress', 'bbpress_notify_newreply_email_subject' );
		register_setting( 'bbpress', 'bbpress_notify_newreply_email_body' );
		register_setting( 'bbpress', 'bbpress_notify_newreply_background' );
		register_setting( 'bbpress', 'bbpnns_hijack_bbp_subscriptions_topic' );
		register_setting( 'bbpress', 'bbpnns_notify_authors_reply' );
		
		register_setting( 'bbpress', "bbpress_notify_default_{$this->bbpress_topic_post_type}_notification" );
		register_setting( 'bbpress', "bbpress_notify_default_{$this->bbpress_reply_post_type}_notification" );
		register_setting( 'bbpress', "bbpress_notify_message_type" );
		register_setting( 'bbpress', 'bbpress_notify_encode_subject' );
		
		// Hook to register additional topic/reply settings
		do_action( 'bbpnns_register_settings' );
	}
	
	/**
	 * @since 1.0
	 */
	function _settings_intro_text( $args )
	{
		printf( '<span id="%s">%s</span>', $args['id'], __( 'Configure e-mail notifications when new topics and/or replies are posted.', 'bbpress_notify' ) );
	}
	
	/**
	 * @since 1.9.3
	 */
	public function _encode_subject()
	{
		$saved_option = get_option( 'bbpress_notify_encode_subject' );
		$html_checked = ( $saved_option ) ? 'checked="checked"' : '';
		$description = __( 'Encode Subject line using UTF-8.<br><span class="description">Turn this OFF if you\'re using a plugin that already encodes it like wpMandrill and are seeing extra question marks in the email subject.</span>', 'bbpress_notify' );
		printf( '<label><input type="checkbox" %s name="bbpress_notify_encode_subject" value="1"/> %s </label><br>', $html_checked, $description );
	}
	
	
	/**
	 * @since 1.3
	 */
	function _topic_background_inputfield()
	{
		$saved_option = get_option( 'bbpress_notify_newtopic_background' );
		$html_checked = ( $saved_option ) ? 'checked="checked"' : '';
		$description = __( 'Send emails in the background the next time the site is visited.', 'bbpress_notify' );
		printf( '<label><input type="checkbox" %s name="bbpress_notify_newtopic_background" value="1"/> %s </label><br>', $html_checked, $description );
	}
	
	/**
	 * @since 1.3
	 */
	function _reply_background_inputfield()
	{
		$saved_option = get_option( 'bbpress_notify_newreply_background' );
		$html_checked = ( $saved_option ) ? 'checked="checked"' : '';
		$description = __( 'Send emails in the background the next time the site is visited.', 'bbpress_notify' );
		printf( '<label><input type="checkbox" %s name="bbpress_notify_newreply_background" value="1"/> %s</label><br>', $html_checked, $description );
	}
	
	/**
	 * @since 1.0
	 */
	/* Show a <select> combobox with recipient options for new topic notifications */
	function _topic_recipients_inputfield()
	{
		global $wp_roles;
		
		printf( __( 'Select one or more roles below to determine which users will be notified of new Topics.<br><br>', 'bbpress-notify') );
		
		$options = $wp_roles->get_names();
		$saved_option = get_option( 'bbpress_notify_newtopic_recipients' );
		foreach ( $options as $value => $description )
		{
			$html_checked = '';
			if ( in_array( $value, ( array )$saved_option ) ) { $html_checked = 'checked="checked"'; }
			printf( '<label><input type="checkbox" %s name="bbpress_notify_newtopic_recipients[]" value="%s"/> %s</label><br>', $html_checked, $value, $description );
		}

		if ( ! empty( $this->bridge_warnings ) )
		{
			foreach( $this->bridge_warnings as $w )
			{
				?>
				<div class="notice notice-warning inline">
					<p><?php echo $w; ?></p>
				</div>
				<?php
			}
		}
	}
	
	/**
	 * @since 1.12
	 */
	function _notify_authors_topic()
	{
		$checked     = (bool) get_option( 'bbpnns_notify_authors_topic' );
		$description = __('Authors must receive a notification when they create a topic.', 'bbpress-notify' );
		printf( '<label><input type="checkbox" %s name="bbpnns_notify_authors_topic"> %s</label>', checked( $checked, true, $echo=false ), $description );
		
	}
	
	/**
	 * @since 1.12
	 */
	function _notify_authors_reply()
	{
		$checked     = (bool) get_option( 'bbpnns_notify_authors_reply' );
		$description = __('Authors must receive a notification when they create a reply.', 'bbpress-notify' );
		printf( '<label><input type="checkbox" %s name="bbpnns_notify_authors_reply"> %s</label>', checked( $checked, true, $echo=false ), $description );
	}
	
	
	/**
	 * @since 1.12
	 */
	function _hijack_bbp_subscriptions_topic()
	{
		$checked     = (bool) get_option( 'bbpnns_hijack_bbp_subscriptions_forum' );
		$description = sprintf( __( 'Override Subscriptions to Forums.<br><span class="description">Enable this option if you want bbPress Notify (No-Spam) to handle bbPress subscriptions to Forums (new topics).
						<br>The bbPress Feature "Allow users to subscribe to forums and topics" must also be enabled for this to work.<br>   
						<a href="%s" target="_blank">Click here to learn more</a></span>', 'bbpress-notify' ), 'https://usestrict.net/2013/02/bbpress-notify-nospam/#subscriptions' );
		printf( '<label><input type="checkbox" %s name="bbpnns_hijack_bbp_subscriptions_forum"> %s</label>', checked( $checked, true, $echo=false ), $description );
	}
	
	/**
	 * @since 1.12
	 */
	function _hijack_bbp_subscriptions_reply()
	{
		$checked     = (bool) get_option( 'bbpnns_hijack_bbp_subscriptions_topic' );
		$description = sprintf( __( 'Override Subscriptions to Topics. <br><span class="description">Enable this option if you want bbPress Notify (No-Spam) to handle bbPress subscriptions to Topics (new replies).
									<br>The bbPress Feature "Allow users to subscribe to forums and topics" must also be enabled for this to work.<br>   
						   			<a href="%s" target="_blank">Click here to learn more</a></span>', 'bbpress-notify' ), 'https://usestrict.net/2013/02/bbpress-notify-nospam/#subscriptions' );
		printf( '<label><input type="checkbox" %s name="bbpnns_hijack_bbp_subscriptions_topic"> %s</label>', checked( $checked, true, $echo=false ), $description );
	}
	
	
	/**
	 * @since 1.14.1
	 */
	function _message_type()
	{
		$selected    = get_option( 'bbpress_notify_message_type', 'html' );
		$description = __( '<span class="description">Choose which type of E-mails you want your users to receive.', 'bbpress-notify' );
?>
		<select name="bbpress_notify_message_type" id="bbpress_notify_message_type">
		<?php foreach( array( 'html'      => __( 'HTML', 'bbpress-notify' ), 
							  'plain'     => __( 'Plain Text', 'bbpress-notify'),
							  'multipart' => __( 'Both', 'bbpress-notify') ) as $val => $text ): ?>
		<option value="<?php echo $val?>" <?php selected( $selected, $val ); ?>><?php echo esc_html( $text ); ?></option>
		<?php endforeach;?>
		</select>
		<br>
<?php
		echo $description;
	}
	
	/**
	 * @since 1.5
	 */
	function _hidden_forum_topic_override()
	{
		$saved_option = get_option( 'bbpress_notify_hidden_forum_topic_override' );
		
		$checked = true === ( bool ) $saved_option ? 'checked="checked"' : '';
		printf( '<label><input type="checkbox" %s name="bbpress_notify_hidden_forum_topic_override" value="1"/> %s</label><br>', $checked,
		__( 'Force Admin-only emails if Forum is hidden ( topics ).', 'bbpress-notify' ) );
	}
	
	/**
	 * @since 1.0
	 */
	/* Show a <select> combobox with recipient options for new reply notifications */
	function _reply_recipients_inputfield()
	{
		global $wp_roles;

		printf( __( 'Select one or more roles below to determine which users will be notified of new Replies to Topics.<br><br>', 'bbpress-notify') );
		
		$options = $wp_roles->get_names();
		$saved_option = get_option( 'bbpress_notify_newreply_recipients' );
		foreach ( $options as $value => $description )
		{
			$html_checked = '';
			if ( in_array( $value, ( array )$saved_option ) ) { $html_checked = 'checked="checked"'; }
			printf( '<label><input type="checkbox" %s name="bbpress_notify_newreply_recipients[]" value="%s"/> %s</label><br>', $html_checked, $value, $description );
		}
		
		if ( ! empty( $this->bridge_warnings ) )
		{
			foreach( $this->bridge_warnings as $w )
			{
				?>
				<div class="notice notice-warning inline">
					<p><?php echo $w; ?></p>
				</div>
				<?php
			}
		}
	}
	
	
	/**
	 * @since 1.5
	 */
	function _hidden_forum_reply_override()
	{
		$saved_option = get_option( 'bbpress_notify_hidden_forum_reply_override' );
	
		$checked = true === ( bool ) $saved_option ? 'checked="checked"' : '';
		printf( '<label><input type="checkbox" %s name="bbpress_notify_hidden_forum_reply_override" value="1"/> %s</label><br>', $checked,
		__( 'Force Admin-only emails if Forum is hidden ( replies ).', 'bbpress-notify' ) );
	}
	
	
	/**
	 * @since 1.0
	 */
	/* Show a <input> field for new topic e-mail subject */
	function _email_newtopic_subject_inputfield()
	{
		printf( '<input type="text" id="bbpress_notify_newtopic_email_subject" name="bbpress_notify_newtopic_email_subject" value="%s" style="width:100%%" />', get_option( 'bbpress_notify_newtopic_email_subject' ) );
	}
	
	
	/**
	 * Deprecated
	 * @param string $tags
	 * @return string
	 */
	public function get_available_tags( $tags='' )
	{
		return $this->get_available_topic_tags( $tags );	
	}
	
	
	/**
	 * A method for the topic tags. 
	 * @since 1.9
	 */
	public function get_available_topic_tags( $tags='' )
	{
		$tags 		= '[blogname], [recipient-first_name], [recipient-last_name], [recipient-display_name], ' .
					  '[recipient-user_nicename], [topic-title], [topic-content], [topic-excerpt], [topic-url], ' . 
					  '[topic-replyurl], [topic-author], [topic-author-email], [topic-forum]';
		$extra_tags = apply_filters( 'bbpnns_extra_topic_tags',  null );
		
		if ( $extra_tags )
			$tags .= ', '. $extra_tags;
		
		return $tags;		
	}
	
	
	/**
	 * A method just for the reply tags
	 * @since 1.10
	 */
	public function get_available_reply_tags( $tags='' )
	{
		$tags 		= '[blogname], [recipient-first_name], [recipient-last_name], [recipient-display_name], [recipient-user_nicename], ' .
		              '[reply-title], [reply-content], [reply-excerpt], [reply-url], [reply-replyurl], [reply-author], [reply-author-email], ' .
		              '[reply-forum], [topic-url], [topic-title], [topic-author], [topic-author-email], [topic-content], [topic-excerpt]';
		
		$extra_tags = apply_filters( 'bbpnns_extra_reply_tags',  null );
		
		if ( $extra_tags )
			$tags .= ', '. $extra_tags;
	
		return $tags;
	}
	
	
	/**
	 * @since 1.0
	 */
	/* Show a <textarea> input for new topic e-mail body */
	function _email_newtopic_body_inputfield()
	{
		wp_editor( $content=get_option( 'bbpress_notify_newtopic_email_body' ), $id='bbpress_notify_newtopic_email_body', array( 'textarea_rows' => 15, 'media_buttons' => false ) );
		
		$tags = $this->get_available_tags();
		
		printf( '<p>%s: %s</p>', __( '<strong>Available Tags</strong>', 'bbpress_notify' ), $tags );
	}
	
	/**
	 * @since 1.0
	 */
	/* Show a <input> field for new reply e-mail subject */
	function _email_newreply_subject_inputfield()
	{
		printf( '<input type="text" id="bbpress_notify_newreply_email_subject" name="bbpress_notify_newreply_email_subject" value="%s" style="width:100%%"/>', get_option( 'bbpress_notify_newreply_email_subject' ) );
	}
	
	/**
	 * @since 1.0
	 */
	/* Show a <textarea> input for new reply e-mail body */
	function _email_newreply_body_inputfield()
	{
		wp_editor( $content=get_option( 'bbpress_notify_newreply_email_body' ), $id='bbpress_notify_newreply_email_body', array( 'textarea_rows' => 15, 'media_buttons' => false ) );
		
		$tags = $this->get_available_reply_tags();
		
		printf( '<p>%s: %s</p>', __( '<strong>Available Tags</strong>', 'bbpress_notify' ), $tags );
	}
	
	/**
	 * @since 1.4
	 */
	function plugin_action_links( $links, $file ) 
	{
		if ( $file === plugin_basename( dirname( __FILE__ ).'/bbpress-notify-nospam.php' ) )
			$links[] = '<a href="' . admin_url( 'admin.php?page=bbpress#' . $this->settings_section ) . '">'.__( 'Settings' ).'</a>';
		
	
		return $links;
	}
	
	/**
	 * @since 1.4
	 */
	function add_notification_meta_box()
	{
		add_meta_box( 'send_notification', __( 'Notifications', 'bbpress_notify' ),
			array( $this, 'notification_meta_box_content' ),  bbp_get_topic_post_type(), 'side', 'high' );
		
		add_meta_box( 'send_notification', __( 'Notifications', 'bbpress_notify' ),
			array( $this, 'notification_meta_box_content' ),  bbp_get_reply_post_type(), 'side', 'high' );
	} 
	
	/**
	 * @since 1.4
	 */
	function notification_meta_box_content( $post )
	{
		$type = ( $post->post_type === $this->bbpress_topic_post_type ) ? 'topic' : 'reply';
		
		$default = get_option( "bbpress_notify_default_{$type}_notification" );
		$checked = checked( $default, true, false );
		
		wp_create_nonce( "bbpress_send_{$type}_notification_nonce" );
		
		wp_nonce_field( "bbpress_send_{$type}_notification_nonce", "bbpress_send_{$type}_notification_nonce" );
		printf( '<label><input type="checkbox" name="bbpress_notify_send_notification" %s> %s</label>', $checked, __( 'Send notification.', 'bbpress_notify' ) );
	} 
	
	/**
	 * @since 1.4
	 */
	function _admin_ui_topic_inputfield()
	{
		$default = get_option( "bbpress_notify_default_{$this->bbpress_topic_post_type}_notification" );
		$checked = checked( $default, true, false );

		printf( '<label><input type="checkbox" value="1" name="%s" %s> %s</label>', "bbpress_notify_default_{$this->bbpress_topic_post_type}_notification", $checked,
		__( 'Send notifications when future publishing or creating Topics in the Admin UI ( <span class="description">Can be overridden in the New/Update Topic screen</span> ).' ) );
	}
	
	/**
	 * @since 1.4
	 */
	function _admin_ui_reply_inputfield()
	{
		$default = get_option( "bbpress_notify_default_{$this->bbpress_reply_post_type}_notification" );
		$checked = checked( $default, true, false );

		printf( '<label><input type="checkbox" value="1" name="%s" %s> %s</label>', "bbpress_notify_default_{$this->bbpress_reply_post_type}_notification", $checked,
		__( 'Send notifications when future publishing or creating Replies in the Admin UI ( <span class="description">Can be overridden in the New/Update Reply screen</span> ).' ) );
	}
	
	
	/**
	 * Sends notifications when user saves/publishes a post. Note that the send notification checkbox must be ticked.
	 * @param int $post_id
	 * @param object $post
	 * @return array
	 */
	function notify_on_save( $post_id, $post )
	{
		$is_future_publish = doing_action( 'publish_future_post' );
		
		if ( empty( $_POST ) && ! $is_future_publish ) return;

		if ( $this->bbpress_topic_post_type !== $post->post_type && $this->bbpress_reply_post_type !== $post->post_type ) return;
		
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_post', $post_id ) ) return;
		
		if ( wp_is_post_revision( $post_id ) || 'publish' !== $post->post_status ) return;
		
		if ( ! $is_future_publish && ( ! isset( $_POST['bbpress_notify_send_notification'] ) || ! $_POST['bbpress_notify_send_notification'] ) ) return;

		$type = ( $post->post_type === $this->bbpress_topic_post_type ) ? 'topic' : 'reply';
		
		if (  ! $is_future_publish && 
			( ! isset( $_POST["bbpress_send_{$type}_notification_nonce"] ) ||
			  ! wp_verify_nonce( $_POST["bbpress_send_{$type}_notification_nonce"], "bbpress_send_{$type}_notification_nonce" ) ) )
		{
			return;
		}
		
		// Check the default notification options
		if ( ! isset( $_POST ) && $is_future_publish )
		{
			$do_notify = get_option( "bbpress_notify_default_{$type}_notification" );
			
			if ( ! $do_notify ) return;
		}

		// Still here, so we can notify
		if ( $post->post_type === $this->bbpress_topic_post_type )
		{
			return $this->notify_new_topic( $post_id );
		}
		else 
		{
			return $this->notify_new_reply( $post_id );
		}
	}
	
	/**
	 * @since 1.5.4
	 */
	public static function missing_bbpress_notice()
	{
		?>
		<div class="error">
			<p>
				<?php _e( '<strong>bbPress Notify (No-Spam)</strong> could not find an active bbPress plugin. It will not load until bbPress is installed and active.' ); ?>
			</p>
		</div>
		<?php 
	}
	
	
	/**
	 * Checks if there are any plugins that need a bridge
	 */
	public function get_bridge_warnings()
	{
		$bridges = array(
				array( 'bridge_name'  => 'bbPress Notify (No-Spam)/Private Groups Bridge',
					   'bridge_class' => 'bbpnns_private_groups_bridge', 
					   'bridge_url'   => 'https://usestrict.net/product/bbpress-notify-no-spam-private-groups-bridge/',
					   'plays_with'   => 'Private Groups',
					   'has_player'   => ( function_exists( 'rpg_user_profile_field' ) ),
				),
				array( 'bridge_name'  => 'bbPress Notify (No-Spam)/BuddyPress Bridge',
					   'bridge_class' => 'BbpnnsBuddypressBridge',
					   'bridge_url'   => 'https://usestrict.net/product/bbpress-notify-no-spam-buddypress-bridge/',
					   'plays_with'   => 'BuddyPress',
					   'has_player'   => ( function_exists( 'buddypress' ) ),
				),
				array( 'bridge_name'  => 'bbPress Notify (No-Spam)/MemberPress Bridge',
					   'bridge_class' => 'bbpnns_memberpress_bridge',
					   'bridge_url'   => 'https://usestrict.net/product/bbpress-notify-no-spam-memberpress-bridge/',
					   'plays_with'   => 'MemberPress',
					   'has_player'   => ( class_exists( 'bbpnns_memberpress_bridge' ) ),
				),
		);
		
		$message = __( '<strong>WARNING</strong>, you are using <strong>%s</strong> but do not have <strong>%s</strong> installed or active. ' .
				       'Setting role-based notifications will send messages to *all* '. 
				       'members of the selected roles. <a href="%s" target="_new">Click here</a> to get the add-on so that bbpnns and %s can play nicely.', $this->domain);

		foreach( $bridges as $bridge )
		{
			if ( true === $bridge['has_player'] && ! class_exists( $bridge['bridge_class'] ) )
			{
				$this->bridge_warnings[] = sprintf( $message, $bridge['plays_with'], $bridge['bridge_name'], $bridge['bridge_url'], $bridge['plays_with'] );
			}
		}
	}
}


/* Kick off the class */
bbPress_Notify_NoSpam::bootstrap();


/* End of file bbpress-notify-nospam.php */
/* Location: bbpress-notify-nospam/bbpress-notify-nospam.php */
