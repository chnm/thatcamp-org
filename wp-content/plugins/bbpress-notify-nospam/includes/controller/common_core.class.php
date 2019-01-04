<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * 
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_Controller_Common_Core extends bbPress_Notify_noSpam {

	public $bbpress_topic_post_type;
	public $bbpress_reply_post_type;
	public $settings;
	
	private $wp_mail_error = 'Unknown Error';
	
	public function __construct()
	{
		$this->bbpress_topic_post_type = $this->get_topic_post_type();
		$this->bbpress_reply_post_type = $this->get_reply_post_type();
		
// 		$this->settings = apply_filters( $this->domain . '_settings', array() );
		$this->settings = $this->load_lib('dal/settings_dao')->load(); 
		
		// This cannot be in is_admin() because it needs to handle future publishing, which doesn't have is_admin() status
		add_action( 'save_post', array( $this, 'notify_on_save' ), 10, 2 );
		
		
		// Triggers the notifications on new topics
		if ( $this->settings->newtopic_background )
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
		if ( $this->settings->newreply_background )
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
		
		
		if ( $this->settings->override_bbp_forum_subscriptions )
		{
			// Stop core subscriptions in its tracks
			add_filter( 'bbp_forum_subscription_mail_message', '__return_false' );
			add_action( 'plugins_loaded', array( $this, 'remove_core_forum_notification' ), 10 );
		}
		
		if ( $this->settings->override_bbp_topic_subscriptions )
		{
			// Stop core subscriptions in its tracks
			add_filter( 'bbp_subscription_mail_message', '__return_false' );
			add_action( 'plugins_loaded', array( $this, 'remove_core_topic_notification' ), 10 );
		}
		
		// Munge bbpress_notify_newpost_recipients if forum is hidden
		add_filter( 'bbpress_notify_recipients_hidden_forum', array( $this, 'munge_newpost_recipients' ), 10, 3 );
		
		// Allow other plugins to fetch available topic tags
		add_filter( 'bbpnns_available_tags', array( $this, 'get_available_tags' ), 10, 1 ); // deprecated, but still works
		add_filter( 'bbpnns_available_topic_tags', array( $this, 'get_available_topic_tags' ), 10, 1 );
		
		// Allow other plugins to fetch available reply tags
		add_filter( 'bbpnns_available_reply_tags', array( $this, 'get_available_reply_tags' ), 10, 1 );
		
		add_filter( 'bbpnns_is_in_effect', array( $this, 'bbpnns_is_in_effect' ), 10, 2 );
		
		/**
		 * Whether DOMDocument supports node as parameter.
		 */
		$this->supports_node = version_compare( PHP_VERSION, '5.3.6', '>=' );
		
		// Get it just once
		$this->site_url = get_option( 'siteurl' );
	}
	
	
	function bg_notify_new_reply( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $reply_author = 0, $bool=false, $reply_to=null )
	{
		wp_schedule_single_event( time() + 10, 'bbpress_notify_bg_reply', array( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, $bool, $reply_to ) );
	}
	
	
	function bg_notify_new_topic( $topic_id = 0, $forum_id = 0, $anonymous_data = false, $topic_author = 0 )
	{
		wp_schedule_single_event( time() + 10, 'bbpress_notify_bg_topic', array( $topic_id, $forum_id, $anonymous_data, $topic_author ) );
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
	
		// Start out false
		$this->users_in_roles[$user_id] = false;
	
		foreach ( (array) $this->settings->newtopic_recipients as $role )
		{
			if ( user_can( $user_id, $role ) )
			{
				$this->users_in_roles[$user_id] = true;
				break;
			}
		}
	
		foreach ( (array) $this->settings->newreply_recipients as $role )
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
	 * Used by send_notification to set the correct content type.
	 * @since 1.14
	 * @param unknown $content_type
	 * @return string
	 */
	public function set_content_type( $content_type )
	{
		if ( ! isset( $this->message_type ) )
		{
			$this->message_type = $this->settings->email_type;
		}
	
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
	
			$phpmailer->addEmbeddedImage( $filepath, $cid, $name );
		}
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
	
		$dom            = new DOMDocument();
		$previous_value = libxml_use_internal_errors(TRUE);
		if ( function_exists( 'mb_convert_encoding' ) )
		{
			$dom->loadHTML( mb_convert_encoding($text, 'HTML-ENTITIES', $this->charset ) );
		}
		else
		{
			$dom->loadHTML( htmlspecialchars_decode(utf8_decode(htmlentities($text, ENT_COMPAT, $this->charset, false))) );
		}
		libxml_use_internal_errors( $previous_value );
	
		$local       = parse_url( $this->site_url );
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
	
		if ( $images->length )
		{
			if ( $this->supports_node )
			{
				$text = $dom->saveHTML($dom->documentElement->lastChild);
				$text = preg_replace('@^<body>|</body>$@i', '', $text );
			}
			else
			{
				$text = $dom->saveHTML();
				preg_match('@<body>(.*)?</body>@ims', $text, $matches );
				if ( isset( $matches[1] ) )
				{
					$text = $matches[1];
				}
			}
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
	
		if ( function_exists( 'mb_convert_encoding' ) )
		{
			$dom->loadHTML( mb_convert_encoding($text, 'HTML-ENTITIES', $this->charset ) );
		}
		else
		{
			$dom->loadHTML( htmlspecialchars_decode(utf8_decode(htmlentities($text, ENT_COMPAT, $this->charset, false))) );
		}
	
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
	 * @since 1.4
	 */
	private function _build_email( $type, $post_id )
	{
		$email_subject = wp_specialchars_decode( $this->settings->{"new{$type}_email_subject"}, ENT_QUOTES );
		$email_body    = wp_specialchars_decode( $this->settings->{"new{$type}_email_body"}, ENT_QUOTES );
	
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
	 * Extracted get_recipients code to its own method
	 * @since 1.9
	 * @param int $forum_id
	 * @param string $type
	 */
	public function get_recipients( $forum_id, $type, $topic_id=null )
	{
		$roles = $this->settings->{"new{$type}_recipients"};
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
	 * Check if bbpnns is in effect (whether because of selected roles or of bbpress core notification Overrides.
	 * @param bool $retval
	 * @param int $user_id
	 * @return boolean
	 */
	public function bbpnns_is_in_effect( $retval=false, $user_id=null )
	{
		                                         // Check if any overrides are on
		return ( $this->settings->override_bbp_forum_subscriptions || $this->settings->override_bbp_topic_subscriptions 
				// Check if the user_id passed is part of the OK'd roles.
				|| $this->user_in_ok_role( $user_id ) ); 
	}
	
	
	/**
	 * A method just for the reply tags
	 * @since 1.10
	 */
	public function get_available_reply_tags( $tags='' )
	{
		$tags = '[blogname], [recipient-first_name], [recipient-last_name], [recipient-display_name], [recipient-user_nicename], ' .
				'[reply-title], [reply-content], [reply-excerpt], [reply-url], [reply-replyurl], [reply-author], [reply-author-email], ' .
				'[reply-forum], [topic-url], [topic-title], [topic-author], [topic-author-email], [topic-content], [topic-excerpt]';
	
		$extra_tags = apply_filters( 'bbpnns_extra_reply_tags',  null );
	
		if ( $extra_tags )
			$tags .= ', '. $extra_tags;
	
		return $tags;
	}
	
	
	/**
	 * A method for the topic tags.
	 * @since 1.9
	 */
	public function get_available_topic_tags( $tags='' )
	{
		$tags = '[blogname], [recipient-first_name], [recipient-last_name], [recipient-display_name], ' .
				'[recipient-user_nicename], [topic-title], [topic-content], [topic-excerpt], [topic-url], ' .
				'[topic-replyurl], [topic-author], [topic-author-email], [topic-forum]';
		$extra_tags = apply_filters( 'bbpnns_extra_topic_tags',  null );
	
		if ( $extra_tags )
			$tags .= ', '. $extra_tags;
	
		return $tags;
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
	 * @since 1.5
	 * @desc Forces admin-only recipients if forum is hidden
	 * @param array $type
	 * @param number $topic_id
	 * @return array
	 */
	public function munge_newpost_recipients( $roles=array(), $type, $forum_id = 0 )
	{
		if ( true === ( bool ) bbp_is_forum_hidden( $forum_id ) &&
			 true === ( bool ) $this->settings->{"hidden_forum_{$type}_override"} )
		{
			$roles = array('administrator');
		}
	
		return $roles;
	}
	
	/**
	 * Remove the core forum notifications if Override Subscriptions to Forums is on.
	 */
	public function remove_core_forum_notification()
	{
		remove_action( 'bbp_new_topic', 'bbp_notify_forum_subscribers', 11 );
	}
	
	
	/**
	 * Remove the core topic notification if Override Subscriptions to Topics is on.
	 */
	public function remove_core_topic_notification()
	{
		remove_action( 'bbp_new_reply', 'bbp_notify_topic_subscribers', 11 );
	}
	
	
	/**
	 * Sends notifications when user saves/publishes a post. Note that the send notification checkbox must be ticked.
	 * @param int $post_id
	 * @param object $post
	 * @return array
	 */
	public function notify_on_save( $post_id, $post )
	{
		$is_future_publish = doing_action( 'publish_future_post' );
	
		if ( empty( $_POST ) && ! $is_future_publish ) return;
	
		if ( $this->bbpress_topic_post_type !== $post->post_type && $this->bbpress_reply_post_type !== $post->post_type ) return;
	
		if ( ! $is_future_publish && ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_post', $post_id ) ) return;
	
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
			$do_notify = $this->settings->{"default_{$type}_notification_checkbox"};
				
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
	 * @since 1.0
	 */
	public function notify_new_topic( $topic_id = 0, $forum_id = 0 )
	{
		global $wpdb;
	
		$status = get_post_status( $topic_id );
	
		if ( in_array( $status, (array) apply_filters( 'bbpnns_post_status_blacklist', array( 'spam' ), $status, $forum_id, $topic_id, $reply_id=false ) ) ||
			! in_array( $status, (array) apply_filters( 'bbpnns_post_status_whitelist', array( 'publish' ), $status, $forum_id, $topic_id, $reply_id=false ) ) )
		{
			return -1;
		}
	
		if ( 0 === $forum_id )
		{
			$forum_id = bbp_get_topic_forum_id( $topic_id );
		}

		if ( true === apply_filters( 'bbpnns_skip_topic_notification', false, $forum_id, $topic_id ) )
		{
			return -3;
		}

		$recipients = $this->get_recipients( $forum_id, 'topic' );

		if ( false === (bool) apply_filters( 'bbpnns_notify_authors_topic', $this->settings->notify_authors_topic ) )
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
		{
			return -2;
		}

		list( $email_subject, $email_body ) = $this->_build_email( 'topic', $topic_id );

		return $this->send_notification( $recipients, $email_subject, $email_body, $type='topic', $topic_id, $forum_id );
	}
	
	
	/**
	 * @since 1.0
	 */
	public function notify_new_reply( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $reply_author = 0, $bool = false, $reply_to = null )
	{
		global $wpdb;
	
		$status = get_post_status( $reply_id );
	
		if ( 0 === $forum_id )
			$forum_id = bbp_get_reply_forum_id( $reply_id );
	
		if ( 0 === $topic_id )
			$topic_id = bbp_get_reply_topic_id( $reply_id );
	
		if ( in_array( $status, (array) apply_filters( 'bbpnns_post_status_blacklist', array( 'spam' ), $status, $forum_id, $topic_id, $reply_id ) ) ||
		   ! in_array( $status, (array) apply_filters( 'bbpnns_post_status_whitelist', array( 'publish' ), $status, $forum_id, $topic_id, $reply_id ) ) )
		{
			return -1;
		}
	
		if ( true === apply_filters( 'bbpnns_skip_reply_notification', false, $forum_id, $topic_id, $reply_id ) )
		{
			return -3;
		}

		$recipients = $this->get_recipients( $forum_id, 'reply', $topic_id );

		if ( false === (bool) apply_filters( 'bbpnns_notify_authors_reply', $this->settings->notify_authors_reply ) )
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
		{
			return -2;
		}

		list( $email_subject, $email_body ) = $this->_build_email( 'reply', $reply_id );

		return $this->send_notification( $recipients, $email_subject, $email_body, $type='reply', $reply_id, $forum_id );
	}
	
	/**
	 * @since 1.0
	 */
	public function send_notification( $recipients, $subject, $body, $type='', $post_id='', $forum_id='' )
	{
		$this->message_type = $this->settings->email_type;
	
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
		$do_enc = ( bool ) $this->settings->encode_subject;
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
				//				add_action( 'phpmailer_init', function($pm){  $pm->postSend(); error_log(__LINE__ . ' message: ' . print_r($pm->getSentMIMEMessage(),1) /* , 3, dirname(__FILE__) . '/out.log' */ ); });
	
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
		
		do_action( 'bbpnns_after_email_sent_all_users', $recipients, $subject, $body );
		
		if ( true === apply_filters( 'bbpnns_dry_run', false ) )
			return array( $recipients, $body );
		
		return true;
	}
	
}

/* End of file common_core.class.php */
/* Location: bbpress-notify-nospam/includes/controller/common_core.class.php */
