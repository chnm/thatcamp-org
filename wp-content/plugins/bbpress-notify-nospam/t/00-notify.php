<?php
/**
 * @group bbpnns
 * @group bbpnns_notify
 */
require_once( ABSPATH . '/wp-content/plugins/bbpress/bbpress.php' );
require_once( ABSPATH . '/wp-content/plugins/bbpress-notify-nospam/bbpress-notify-nospam.php' );

class Tests_bbPress_notify_no_spam_notify_new extends WP_UnitTestCase 
{
	public $forum_id;
	public $topic_id;
	public $reply_id;
	
	public $topic_body;
	public $topic_body_regex;
	public $reply_body;
	public $reply_body_regex;
	
	public function __construct()
	{
		// NOOP
	}
	
	
	public function setUp()
	{
		parent::setUp();
		
		// Set up the body templates
		$this->topic_body = "<p>This is <br> a <br /> test &#039; paragraph for topic forum [topic-forum], URL: [topic-url], and Author: [topic-author]</p>\n\n<p>And a new <br/>paragraph</p>";
		$this->reply_body = "<p>This is <br> a <br /> test &#039; paragraph for reply forum [reply-forum], Topic URL: [topic-url], URL: [reply-url], and Author: [reply-author]</p>\n\n<p>And a new <br/>paragraph</p>";
		
		// Create new forum
		$this->forum_id = bbp_insert_forum( 
			array( 
				'post_title'  => 'test-forum',
				'post_status' => 'publish'
			 )
		 );
		
		// Create new topic
		$this->topic_id = bbp_insert_topic( 
			array( 
				'post_parent'  => $this->forum_id,
				'post_title'   => 'test-topic',
				'post_content' => $this->topic_body,
				'post_author'  => 1,
			 ),
			array( 
				'forum_id' => $this->forum_id		
			 )
		 );
		
		// Create new reply
		$this->reply_id = bbp_insert_reply( 
			array( 
				'post_parent'  => $this->topic_id,
				'post_title'   => 'test-reply',
				'post_content' => $this->reply_body,
				'post_author'  => 1,
			 ),
			array( 
				'forum_id' => $this->forum_id,
				'topic_id' => $this->topic_id		
			 )
		 );
		
		add_filter( 'bbpnns_dry_run', '__return_true' );
		
		// Non-spam, non-empty recipents
		$recipients = array( 'administrator', 'subscriber' );
		update_option( 'bbpress_notify_newtopic_recipients', $recipients );
		$subs_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		
		
		// Set up the expected body regexes
		$this->topic_body_regex = "<p>This is <br> a <br \/> test ' paragraph for topic forum test-forum, URL: http:\/\/wp_plugins\/\?p={$this->topic_id}, and Author: admin<\/p>\n\n<p>And a new <br\/>paragraph<\/p>";
		$this->reply_body_regex = "<p>This is <br> a <br \/> test ' paragraph for reply forum test-forum, Topic URL: http:\/\/wp_plugins\/\?p={$this->topic_id}, URL: http:\/\/wp_plugins\/\?p={$this->reply_id}, and Author: admin<\/p>\n\n<p>And a new <br\/>paragraph<\/p>";
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		remove_all_filters( 'bbpnns_dry_run' );
		remove_all_filters( 'bbpnns_skip_reply_notification' );
		remove_all_filters( 'bbpnns_skip_topic_notification' );
	}
	
	public function test_available_tags()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		$this->assertTrue( (bool) has_filter( 'bbpnns_available_tags', array( $bbpnns, 'get_available_tags' ) ), 'Filter found' );
		
		$tags = $bbpnns->get_available_topic_tags( null );
		
		$this->assertNotEmpty( $tags, 'Available tags are not empty' );
		
		$this->assertTrue( (bool) strpos( $tags, '[topic-forum]' ), 'Once missing [topic-forum] is available.' );
		
		
		$tags = $bbpnns->get_available_reply_tags( null );
		
		$this->assertNotEmpty( $tags, 'Available tags are not empty' );

		$this->assertTrue( (bool) strpos( $tags, '[reply-forum]' ), 'Once missing [reply-forum] is available.' );
		
		$this->assertTrue( (bool) strpos( $tags, '[topic-url]' ), '[topic-url] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-content]' ), '[topic-content] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-excerpt]' ), '[topic-excerpt] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-author]' ), '[topic-author] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-author-email]' ), '[topic-author-email] tag is there for replies' );
	}
	
	
	public function test_topic_recipient_filter()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		$this->assertTrue( (bool ) has_filter( 'bbpress_notify_recipients_hidden_forum', array( $bbpnns, 'munge_newpost_recipients' )), 
				'bbpress_notify_recipients_hidden_forum filter exists' );
		
		$expected = array( 'foo', 'bar' );
		$recipients = apply_filters( 'bbpress_notify_recipients_hidden_forum', $expected, 'topic', $this->forum_id );
		
		$this->assertEquals( $expected, $recipients, 'Filter returns input array for non-hidden forum' );

		//hide forum
		bbp_hide_forum( $this->forum_id );
		
		$recipients = apply_filters( 'bbpress_notify_recipients_hidden_forum', $expected, 'topic', $this->forum_id );
		
		$this->assertEquals( array('administrator'), $recipients, 'Filter returns \'administrator\' array element for non-hidden forum' );
		
	}
	
	
	public function test_notify_topic()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		// Spam, returns -1
		bbp_spam_topic( $this->topic_id );
		$status = $bbpnns->notify_new_topic( $this->topic_id, $this->forum_id );
		$this->assertEquals( -1, $status, 'Spam topic returns -1' );
		
		// Non-spam, empty recipients returns -2
		bbp_unspam_topic( $this->topic_id );
		delete_option( 'bbpress_notify_newtopic_recipients' );
		$status = $bbpnns->notify_new_topic( $this->topic_id, $this->forum_id );
		$this->assertEquals( -2, $status, 'Empty Recipients -2' );
		
		update_option( 'bbpress_notify_newtopic_email_body', $this->topic_body );
		
		// Non-spam, non-empty recipents
		$recipients = array( 'administrator', 'subscriber' );
		update_option( 'bbpress_notify_newtopic_recipients', $recipients );
		$arry = $bbpnns->notify_new_topic( $this->topic_id, $this->forum_id );
		$this->assertTrue( is_array( $arry ), 'Good notify returns array in test mode' );

		list( $recipients, $body ) = $arry;
		
		$author_id = bbp_get_topic_author_id( $this->topic_id );
		$this->assertFalse( isset( $recipients[$author_id] ), 'Author ID removed from recpients list' );
		$reg_body = $this->topic_body_regex;

		$this->assertRegexp( "/$reg_body/", $body, 'Topic body munged correctly' );
		
		// Force skip
		add_filter( 'bbpnns_skip_topic_notification', '__return_true' );
		$status = $bbpnns->notify_new_topic( $this->topic_id, $this->forum_id );
		$this->assertEquals( -3, $status, 'Force skip -3' );
		
	}
	
	
	public function test_notify_reply()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		// Spam, returns -1
		bbp_spam_reply( $this->reply_id );
		$status = $bbpnns->notify_new_reply( $this->reply_id, $this->topic_id, $this->forum_id );
		$this->assertEquals( -1, $status, 'Spam reply returns -1' );
		
		// Clear recipients
		$expected_recipients = array();
		update_option( 'bbpress_notify_newreply_recipients', $recipients );
		
		// Non-spam, empty recipients returns -2
		bbp_unspam_reply( $this->reply_id );
		$status = $bbpnns->notify_new_reply( $this->reply_id, $this->topic_id, $this->forum_id );
		$this->assertEquals( -2, $status, 'Empty Recipients -2' );
		
		update_option( 'bbpress_notify_newreply_email_body', $this->reply_body );
		
		// Non-spam, non-empty recipents
		update_option( 'bbpress_notify_newreply_recipients', array( 'administrator', 'subscriber' ));
		$arry = $bbpnns->notify_new_reply( $this->reply_id, $this->topic_id, $this->forum_id );
		
		$this->assertTrue( is_array( $arry ), 'Good notify returns array in test mode' );
		
		list( $recipients, $body ) = $arry;
		
		$author_id = bbp_get_reply_author_id( $this->reply_id );
		$this->assertFalse( isset( $recipients[$author_id] ), 'Author ID removed from recpients list' );
		
		$reg_body = $this->reply_body_regex;
		
		$this->assertRegexp( "/$reg_body/", $body, 'Reply body munged correctly' );
		
		// Force skip
		add_filter( 'bbpnns_skip_reply_notification', '__return_true' );
		$status = $bbpnns->notify_new_reply( $this->topic_id );
		$this->assertEquals( -3, $status, 'Force skip -3' );
	}
	
	
	public function test_send_notification()
	{
		$roles = array( 'administrator' );
		
		remove_all_filters('bbpnns_filter_recipients_before_send');
		
		// Non-hidden forum
		update_option( 'bbpress_notify_newtopic_recipients', $roles );
		
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		$users = get_users( array( 'role' => join(', ', $roles) ) );
		
		$recipients = array();
		foreach ( $users as $u )
		{
			$recipients[ $u->ID ] = $u;
		}

		list( $got_recipients, $body ) = $bbpnns->send_notification( $recipients, 'test subject', 'test_body' );

		$this->assertEquals( $recipients, $got_recipients, 'Test mode got expected recipients' );
		
		// Hidden forum returns admins only
		bbp_hide_forum( $this->forum_id );
		
		$roles = array( 'administrator', 'subscriber' );
		$roles = (array) apply_filters( 'bbpress_notify_recipients_hidden_forum', $roles, $this->forum_id );
	
		$users = get_users( array( 'role__in' => $roles ) );
		
		$recipients = array();
		foreach ( $users as $u )
		{
			$recipients[ $u->ID ] = $u;
		}
		
		list( $got_recipients, $body ) = $bbpnns->send_notification( $recipients, 'test subject', 'test_body' );
		
		$this->assertTrue( is_array( $got_recipients ), 'Got an array back');
		
		$result = array_intersect_key( $recipients, $got_recipients );
		
		$this->assertTrue( ! empty ( $result ), 'Filtered send_notification returns users' );
	}
	
	
// 	public function test_build_email()
// 	{
// 		$type    = 'reply';
// 		$subject = 'Vinny&#39;s test with quotes';
// 		$body    = 'This is a test';
		
// 		update_option( "bbpress_notify_new{$type}_email_subject", $subject );
// 		update_option( "bbpress_notify_new{$type}_email_body", $body );
		
// 		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
// 		list( $email_subject, $email_body ) = $bbpnns->_build_email( 'reply', $this->reply_id );
		
// 		var_dump($email_subject, $email_body);
// 	}
	
	
	public function test_notify_on_publish_future_post()
	{
		global $wpdb;
		
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		$bbpnns->set_post_types();
		
		$type = 'topic';
		update_option( "bbpress_notify_default_{$type}_notification", true );
		
		$author_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		
		wp_set_current_user( $author_id );
		
		$post = array(
				'post_content' => 'Test content',
				'post_name'    => 'Test name',
				'post_status'  => 'future',
				'post_author'  => $author_id,
				'post_type'    => 'topic',
				'post_date'  => date('Y-m-d H:i:s GMT', time() - 10 )
		);
		
		$topic_id = wp_insert_post( $post );
		
		$post = get_post( $topic_id );
		if ( 'publish' === $post->post_status )
		{
			$wpdb->query( "update {$wpdb->posts} set post_status = 'future' where ID = " . $post->ID );
			clean_post_cache( $post->ID );
		}
		
		$this->got_hit = false;
		add_filter( 'bbpress_topic_notify_recipients', function($recipients, $topic_id, $forum_id){
			$this->got_hit = true;
			return $recipients;
			
		}, 10, 3);
		
		$this->assertFalse( $this->got_hit, 'Initial value of got hit is false' );
		do_action( 'publish_future_post', $post->ID );
		$this->assertTrue( $this->got_hit, 'After action value of got hit is true' );
	}
	
	public function test_notify_on_save()
	{
			$bbpnns = bbPress_Notify_NoSpam::bootstrap();
			$bbpnns->set_post_types();
	
			$author_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
	
			wp_set_current_user( $author_id );
	
			$nonce_id = wp_create_nonce( 'bbpress_send_topic_notification_nonce' );
	
			$_POST = array( 'bbpress_notify_send_notification'       => true,
							'bbpress_send_topic_notification_nonce'  => $nonce_id
					 );
	
			$post = array( 
							'post_content' => 'Test content',
							'post_name'    => 'Test name',
							'post_status'  => 'publish',
							'post_author'  => $author_id,
							'post_type'    => 'topic',
					 );

			$topic_id = wp_insert_post( $post );
	
			$post = get_post( $topic_id );
	
			$result = $bbpnns->notify_on_save( $topic_id, $post );
	
			$this->assertFalse( empty( $result ) );
		}

	public function test_convert_images_and_links()
	{
		$text = 'This is <a href="http://thefirstlink.com">the first link</a> and 
				
				then <a href="http://thescondlink.com">the second link</a> and the final stuff.';
		
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		$conv = $bbpnns->convert_images_and_links( $text );
		
		$expected = 'This is (the first link) [http://thefirstlink.com] and 
				
				then (the second link) [http://thescondlink.com] and the final stuff.';
		$this->assertEquals( $expected, $conv, 'Conversion works as expected' );
		
		$text = 'This is <a href="http://thefirstlink.com"><img src="foo.gif" alt="foo"></a> and then <a href="http://thescondlink.com">the second link</a> and the final stuff.';
		
		$expected = 'This is ([img]foo[/img]) [http://thefirstlink.com] and then (the second link) [http://thescondlink.com] and the final stuff.';
		
		$conv = $bbpnns->convert_images_and_links( $text );
		
		$this->assertEquals( $conv, $expected, 'Got an image altered');
		
		$text = 'This is på en forumtråd <a href="http://thefirstlink.com"><img src="foo.gif" alt="foo"> and some text and <img src="foo.gif" alt="another image"></a> <img src="foo.gif" alt="yet another image"> and then <a href="http://thescondlink.com">the second link</a> and the final stuff.';
		
		$conv = $bbpnns->convert_images_and_links( $text );
		
		$expected = 'This is på en forumtråd ([img]foo[/img] and some text and [img]another image[/img]) [http://thefirstlink.com] [img]yet another image[/img] and then (the second link) [http://thescondlink.com] and the final stuff.';
		
		$this->assertEquals( $expected, $conv, 'Nested and not nested images OK');
	}
	
	
	public function test_user_in_role()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		delete_option( 'bbpress_notify_newtopic_recipients');
		delete_option( 'bbpress_notify_newreply_recipients');
		
		// Create new user in role
		$user = $this->factory->user->create_and_get( array( 'role' => 'administrator' ) );
		
		// Test no roles return false
		$this->assertFalse( $bbpnns->user_in_ok_role( $user->ID ) );

		$bbpnns->users_in_roles = array();
		
		// Save OK role
		$recipients = array( 'administrator' );
		update_option( 'bbpress_notify_newtopic_recipients', $recipients );
		
		// Test user IS in role
		$this->assertTrue( $bbpnns->user_in_ok_role( $user->ID ) );
		
		// Create user outside role
		$user2 = $this->factory->user->create_and_get( array( 'role' => 'subscriber' ) );
		// Test user not in role
		$this->assertFalse( $bbpnns->user_in_ok_role( $user2->ID ) );
		
		// Set current user
		wp_set_current_user( $user->ID );
		// Test current user returns accordingly
		$this->assertTrue( $bbpnns->user_in_ok_role() );
		
		// Set current user
		wp_set_current_user( $user2->ID );
		// Test current user returns accordingly
		$this->assertFalse( $bbpnns->user_in_ok_role() );
	}
	
	
	public function test_bbpnns_is_in_effect()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();

		$this->assertTrue( (bool) has_filter( 'bbpnns_is_in_effect', array( $bbpnns, 'bbpnns_is_in_effect' ) ), 'Filter found' );
		
		delete_option( 'bbpress_notify_newtopic_recipients' );
		delete_option( 'bbpress_notify_newreply_recipients' );
		delete_option( 'bbpnns_hijack_bbp_subscriptions_forum' );
		delete_option( 'bbpnns_hijack_bbp_subscriptions_topic' );
		
		$user = $this->factory->user->create_and_get( array( 'role' => 'administrator' ) );
		
		$this->assertFalse( apply_filters( 'bbpnns_is_in_effect', false ) );
		
		update_option( 'bbpnns_hijack_bbp_subscriptions_forum', true );
		unset( $bbpnns->override_forum );

		$this->assertTrue( apply_filters( 'bbpnns_is_in_effect', false, $user->ID ) );
		delete_option( 'bbpnns_hijack_bbp_subscriptions_forum' );
		delete_option( 'bbpnns_hijack_bbp_subscriptions_topic' );
		
		update_option( 'bbpnns_hijack_bbp_subscriptions_topic', true );
		unset( $bbpnns->override_forum );
		unset( $bbpnns->override_topic );
		
		$this->assertTrue( apply_filters( 'bbpnns_is_in_effect', false, $user->ID ) );
		delete_option( 'bbpnns_hijack_bbp_subscriptions_forum' );
		delete_option( 'bbpnns_hijack_bbp_subscriptions_topic' );
		
		unset( $bbpnns->override_forum );
		unset( $bbpnns->override_topic );
		
		$recipients = array( 'administrator' );
		update_option( 'bbpress_notify_newtopic_recipients', $recipients );
		
		$this->assertTrue( apply_filters( 'bbpnns_is_in_effect', false, $user->ID ) );
		
	}
	
	
	public function test_filters()
	{
		$url     = 'foo';
		$post_id = 1;
		$title   = 'test title'; 
		
		foreach ( array( 'bbpnns_topic_url', 'bbpnns_reply_url', 'bbpnns_topic_reply' ) as $filter )
		{
			add_filter( $filter, array( $this, '_url_filter' ), 10, 3 );
			
			$out = apply_filters( $filter, $url, $post_id, $title );
			$this->assertEquals( $out, 'Bar ' . $post_id . ' ' . $title, $filter . ' Filter works' );
		}
		
	}
	
	
	public function _url_filter( $url, $post_id, $title )
	{
		return 'Bar ' . $post_id . ' ' . $title;
	}
		
}

/* End of 00-notify.php */
/* Location: bbpress-notify-no-spam/t/00-notify.php */