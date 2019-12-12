<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 */
require_once( ABSPATH . '/wp-content/plugins/bbpress/bbpress.php' );
require_once( ABSPATH . '/wp-content/plugins/bbpress-notify-nospam/bbpress-notify-nospam.php' );

class Tests_bbPress_notify_no_spam_notify_new extends WP_UnitTestCase 
{
	public $forum_id;
	public $topic_id;
	public $reply_id;
	public $author;
	
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
		
		$this->author = $user = $this->factory->user->create_and_get( array( 'role' => 'administrator' ) );
		
		// Set up the body templates
		$this->topic_body = "<p>This is <br> a <br /> test &#039; paragraph for topic forum [topic-forum], Date: [date format=\"Y-m-d H:i:s\"], URL: [topic-url], and Author: [topic-author]</p>\n\n<p>And a new <br/>paragraph</p>";
		$this->reply_body = "<p>This is <br> a <br /> test &#039; paragraph for reply forum [reply-forum], Date: [date format=\"Y-m-d H:i:s\"], Topic URL: [topic-url], URL: [reply-url], and Author: [reply-author]</p>\n\n<p>And a new <br/>paragraph</p>";
		
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
				'post_author'  => $user->ID,
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
				'post_author'  => $user->ID,
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
		$this->topic_body_regex = "<p>This is <br> a <br \/> test ' paragraph for topic forum test-forum, Date: \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}, URL: http:\/\/wp_plugins\/.*?, and Author: {$user->user_login}<\/p>\n\n<p>And a new <br\/>paragraph<\/p>";
		$this->reply_body_regex = "<p>This is <br> a <br \/> test ' paragraph for reply forum test-forum, Date: \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}, Topic URL: http:\/\/wp_plugins\/.*?, URL: http:\/\/wp_plugins\/.*, and Author: {$user->user_login}<\/p>\n\n<p>And a new <br\/>paragraph<\/p>";
		
		$this->child = new bbPress_Notify_noSpam_Child();
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		remove_all_filters( 'bbpnns_dry_run' );
		remove_all_filters( 'bbpnns_skip_reply_notification' );
		remove_all_filters( 'bbpnns_skip_topic_notification' );
	}
	
	public function test_construct()
	{
		$bbpnns = new bbPress_Notify_noSpam();
		
		$this->assertEquals( 0, has_action('init', array( $bbpnns, 'init') ) );
	}
	
	public function test_common_core_actions_filters()
	{
	    $ccc = $this->child->load_lib( 'controller/common_core', [], $force=true );
	    
	    $this->assertTrue( (bool) has_action( 'save_post', [ $ccc, 'notify_on_save' ] ) );
	    $this->assertTrue( (bool) has_action( 'bbpnns_dry_run_trace', [ $ccc, 'trace' ] ) );
	    $this->assertTrue( (bool) has_filter( 'bbpress_notify_recipients_hidden_forum', [ $ccc, 'munge_newpost_recipients' ] ) );
	    $this->assertTrue( (bool) has_filter( 'bbpnns_available_tags', [ $ccc, 'get_available_tags' ] ) );
	    $this->assertTrue( (bool) has_filter( 'bbpnns_available_topic_tags', [ $ccc, 'get_available_topic_tags' ] ) );
	    $this->assertTrue( (bool) has_filter( 'bbpnns_available_reply_tags', [ $ccc, 'get_available_reply_tags' ] ) );
	    $this->assertTrue( (bool) has_filter( 'bbpnns_is_in_effect', [ $ccc, 'bbpnns_is_in_effect' ] ) );
	    
	    
	    $dao = $this->child->load_lib( 'dal/settings_dao' );
	    $settings = $dao->load();
	    
	    foreach ( [true,false] as $bool )
	    {
            remove_all_filters( 'bbp_forum_subscription_mail_message' );
            remove_all_filters( 'bbp_subscription_mail_message' );
	        
            $settings->background_notifications         = $bool;
            $settings->override_bbp_forum_subscriptions = $bool;
            $settings->override_bbp_topic_subscriptions = $bool;
	       
            $dao->save( $settings );
	       
            $ccc = $this->child->load_lib( 'controller/common_core', [], $force=true );

            if ( $bool )
            {
                $this->assertTrue( (bool) has_action( 'bbp_new_topic', [$ccc, 'bg_notify_new_topic'] ) );
                $this->assertTrue( (bool) has_action( 'bbp_approved_topic', [$ccc, 'bg_notify_new_topic'] ) );
                $this->assertTrue( (bool) has_action( 'bbp_new_topic', [$ccc, 'bg_filter_topic_recipients'] ) );
                $this->assertTrue( (bool) has_action( 'bbpress_notify_bg_topic', [$ccc, 'notify_new_topic'] ) );
                
                $this->assertTrue( (bool) has_action( 'bbp_new_reply', [$ccc, 'bg_notify_new_reply'] ) );
                $this->assertTrue( (bool) has_action( 'bbp_approved_reply', [$ccc, 'bg_notify_new_reply'] ) );
                $this->assertTrue( (bool) has_action( 'bbp_new_reply', [$ccc, 'bg_filter_reply_recipients'] ) );
                $this->assertTrue( (bool) has_action( 'bbpress_notify_bg_reply', [$ccc, 'notify_new_reply'] ) );
                
                
                $this->assertTrue( (bool) has_filter( 'bbp_forum_subscription_mail_message', '__return_false' ) );
                $this->assertTrue( (bool) has_action( 'plugins_loaded', [$ccc, 'remove_core_forum_notification'] ) );
                
                $this->assertTrue( (bool) has_filter( 'bbp_subscription_mail_message', '__return_false' ) );
                $this->assertTrue( (bool) has_action( 'plugins_loaded', [$ccc, 'remove_core_topic_notification'] ) );
            }
            else
            {
                $this->assertTrue( (bool) has_action( 'bbp_new_topic', [$ccc, 'notify_new_topic'] ) );
                $this->assertTrue( (bool) has_action( 'bbp_approved_topic', [$ccc, 'notify_new_topic'] ) );
                
                $this->assertTrue( (bool) has_action( 'bbp_new_reply', [$ccc, 'notify_new_reply'] ) );
                $this->assertTrue( (bool) has_action( 'bbp_approved_reply', [$ccc, 'notify_new_reply'] ) );
                
                $this->assertFalse( (bool) has_filter( 'bbp_forum_subscription_mail_message', '__return_false' ) );
                $this->assertFalse( (bool) has_action( 'plugins_loaded', [$ccc, 'remove_core_forum_notification'] ) );
            }
	    }
	}
	
	public function test_available_tags()
	{
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
		$this->assertTrue( (bool) has_filter( 'bbpnns_available_tags', array( $bbpnns, 'get_available_tags' ) ), 'Filter found' );
		
		$tags = $bbpnns->get_available_topic_tags( null );
		
		$this->assertNotEmpty( $tags, 'Available tags are not empty' );
		
		$this->assertTrue( (bool) strpos( $tags, '[topic-forum]' ), 'Once missing [topic-forum] is available.' );
		$this->assertTrue( (bool) strpos( $tags, '[date]' ), '[date] tag is there for topics' );
		
		
		$tags = $bbpnns->get_available_reply_tags( null );
		
		$this->assertNotEmpty( $tags, 'Available tags are not empty' );

		$this->assertTrue( (bool) strpos( $tags, '[reply-forum]' ), 'Once missing [reply-forum] is available.' );
		
		$this->assertTrue( (bool) strpos( $tags, '[topic-url]' ), '[topic-url] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-content]' ), '[topic-content] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-excerpt]' ), '[topic-excerpt] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-author]' ), '[topic-author] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[topic-author-email]' ), '[topic-author-email] tag is there for replies' );
		$this->assertTrue( (bool) strpos( $tags, '[date]' ), '[date] tag is there for replies' );
	}
	
	
	public function test_topic_recipient_filter()
	{
		$dao = $this->child->load_lib('dal/settings_dao');
		$settings = $dao->load();
		
		$settings->hidden_forum_topic_override = true;
		$dao->save($settings);
		
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
		$this->assertTrue( (bool ) has_filter( 'bbpress_notify_recipients_hidden_forum', array( $bbpnns, 'munge_newpost_recipients' )), 
				'bbpress_notify_recipients_hidden_forum filter exists' );
		
		$expected = array( 'foo', 'bar' );
		$recipients = apply_filters( 'bbpress_notify_recipients_hidden_forum', $expected, 'topic', $this->forum_id );
		
		$this->assertEquals( $recipients, $expected, 'Filter returns input array for non-hidden forum' );

		//hide forum
		bbp_hide_forum( $this->forum_id );
		
		$recipients = apply_filters( 'bbpress_notify_recipients_hidden_forum', $expected, 'topic', $this->forum_id );
		
		$this->assertEquals( $recipients, array('administrator'), 'Filter returns \'administrator\' array element for non-hidden forum' );
	}
	
	
	public function test_get_recipients()
	{
	    $subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
	    $admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
	    
	    $dao = $this->child->load_lib( 'dal/settings_dao' );
	    $orig_settings = $settings = $dao->load();
	    
	    $settings->newreply_recipients = array( 'administrator' );
	    $settings->override_bbp_topic_subscriptions = true;
	    $settings->include_bbp_forum_subscriptions_in_replies = true;
	    $settings->notify_authors_topic = false;
	    $settings->notify_authors_reply = false;
	    $dao->save($settings);
	    
	    add_filter( 'bbp_get_forum_subscribers', function( $users ) use ( $subscriber_id ){
	        return [ $subscriber_id ];
	    }, 10, 1 );
	    
	    $bbpnns = $this->child->load_lib( 'controller/common_core');
	    
	    $recipients = $bbpnns->get_recipients( $this->forum_id, 'reply', $this->topic_id, $this->author->ID  );
	    
        $this->assertTrue( isset( $recipients[$admin_id] ), 'Got admin' );
        $this->assertTrue( isset( $recipients[$subscriber_id] ), 'Got extra subscriber' );
        $this->assertFalse( isset( $recipients[$this->author->ID] ), 'Author is not in recipient list' );
        
        $settings->notify_authors_topic = true;
        $settings->notify_authors_reply = true;
        $dao->save( $settings );
        
        // Reload to get fresh settings
        $bbpnns = $this->child->load_lib( 'controller/common_core', [], $force=1);
        $recipients = $bbpnns->get_recipients( $this->forum_id, 'reply', $this->topic_id, $this->author->ID  );
        $this->assertTrue( isset( $recipients[$this->author->ID]), 'Author ID was added to recipient list' );
        

        $dao->save( $orig_settings );
        
	    remove_all_filters( 'bbp_get_forum_subscribers' );
	}
	
	public function test_bg_filter_reply_recipients()
	{
		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		
		$settings->newreply_recipients = array( 'administrator' );
		$dao->save($settings);
		
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
		$bbpnns->bg_filter_reply_recipients( $this->reply_id, $this->topic_id, $this->forum_id );
		
		$this->assertNotEmpty( $bbpnns->queued_recipients, 'Queued Recipients was populated' );
		$this->assertTrue( ! isset( $bbpnns->queued_recipients[0] ), 'Keys are user_ids' );
		
		$this->assertTrue( (bool) has_filter( 'bbp_topic_subscription_user_ids', array( $bbpnns, 'filter_queued_recipients' ) ) );
	}
	
	
	public function test_bg_filter_topic_recipients()
	{
		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		
		$settings->newtopic_recipients = array( 'administrator' );
		$dao->save($settings);
		
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
	
		$bbpnns->bg_filter_topic_recipients( $this->topic_id, $this->forum_id );
	
		$this->assertNotEmpty( $bbpnns->queued_recipients, 'Queued Recipients was populated' );
		$this->assertTrue( ! isset( $bbpnns->queued_recipients[0] ), 'Keys are user_ids' );
	
		$this->assertTrue( (bool) has_filter( 'bbp_forum_subscription_user_ids', array( $bbpnns, 'filter_queued_recipients' ) ) );
	}
	
	
	public function test_notify_topic()
	{
		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
		// Spam, returns -1
		bbp_spam_topic( $this->topic_id );
		$status = $bbpnns->notify_new_topic( $this->topic_id, $this->forum_id );
		$this->assertEquals( -1, $status, 'Spam topic returns -1' );
		
		// Non-spam, empty recipients returns -2
		bbp_unspam_topic( $this->topic_id );
		
// 		delete_option( 'bbpress_notify_newtopic_recipients' );
		$settings->newtopic_recipients = array();
		$settings->notify_authors_topic = false;
		$settings->notify_authors_reply = false;
		$dao->save( $settings );
		$bbpnns = $this->child->load_lib( 'controller/common_core', null, $force=true );
		
		$status = $bbpnns->notify_new_topic( $this->topic_id, $this->forum_id );
		$this->assertEquals( -2, $status, 'Empty Recipients -2' );
		
// 		update_option( 'bbpress_notify_newtopic_email_body', $this->topic_body );
		$settings->newtopic_email_body = $this->topic_body;
		$dao->save($settings);
		$bbpnns = $this->child->load_lib( 'controller/common_core', null, $force=true );
		
		// Non-spam, non-empty recipents
		$recipients = array( 'administrator', 'subscriber' );
		
// 		update_option( 'bbpress_notify_newtopic_recipients', $recipients );
		$settings->newtopic_recipients = $recipients;
		$dao->save( $settings );
		$bbpnns = $this->child->load_lib( 'controller/common_core', null, $force=true );
		
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
		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
		// Spam, returns -1
		bbp_spam_reply( $this->reply_id );
		$status = $bbpnns->notify_new_reply( $this->reply_id, $this->topic_id, $this->forum_id );
		$this->assertEquals( -1, $status, 'Spam reply returns -1' );
		
		// Clear recipients
		$expected_recipients = array();
// 		update_option( 'bbpress_notify_newreply_recipients', $expected_recipients );
		$settings->newreply_recipients = $expected_recipients;
		$dao->save( $settings );
		$bbpnns = $this->child->load_lib( 'controller/common_core', null, $force=true );
		
		// Non-spam, empty recipients returns -2
		bbp_unspam_reply( $this->reply_id );
		$status = $bbpnns->notify_new_reply( $this->reply_id, $this->topic_id, $this->forum_id );
		$this->assertEquals( -2, $status, 'Empty Recipients -2' );
		
// 		update_option( 'bbpress_notify_newreply_email_body', $this->reply_body );
		$settings->newreply_email_body = $this->reply_body;
		$dao->save( $settings );
		$bbpnns = $this->child->load_lib( 'controller/common_core', null, $force=true );
		
		// Non-spam, non-empty recipents
// 		update_option( 'bbpress_notify_newreply_recipients', array( 'administrator', 'subscriber' ));
		$settings->newreply_recipients = array( 'administrator', 'subscriber' );
		$dao->save( $settings );
		$bbpnns = $this->child->load_lib( 'controller/common_core', null, $force=true );
		
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
	
	
	public function test_filter_queued_recipients()
	{
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
		// This populates queued_recipients needed by filter_queued_recipients
		$bbpnns->bg_filter_reply_recipients( $this->reply_id, $this->topic_id, $this->forum_id );
		
		// Given a core recipient list, remove those who are in our queued_recipients.
		// Note that for our test, we're using the same list in the expected format, so the result will be an empty list.
		$core_recipient_list = array_keys( $bbpnns->queued_recipients );
		$clean = $bbpnns->filter_queued_recipients( $core_recipient_list );
		
		$this->assertEmpty( $clean, 'Affected recipients were filtered out');
	}
	

	public function test_send_notification()
	{
		$roles = array( 'administrator' );
		
		remove_all_filters('bbpnns_filter_recipients_before_send');
		
		// Non-hidden forum
		update_option( 'bbpress_notify_newtopic_recipients', $roles );
		
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
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
		
		$this->assertNotEmpty( $bbpnns->queued_recipients, 'Queued Recipients was populated' );
		
		$this->assertTrue( (bool) has_filter( 'bbp_forum_subscription_user_ids', array( $bbpnns, 'filter_queued_recipients' ) ) );
		$this->assertTrue( (bool) has_filter( 'bbp_topic_subscription_user_ids', array( $bbpnns, 'filter_queued_recipients' ) ) );
	}
	
	
	public function test_notify_on_publish_future_post()
	{
		global $wpdb;
		
		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		
		$type = 'topic';
// 		update_option( "bbpress_notify_default_{$type}_notification", true );
		$settings->{"default_{$type}_notification_checkbox"} = true;
		
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
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
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
// 		$bbpnns->set_post_types();
	
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
		
		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
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
		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		
		$settings->newtopic_recipients = $settings->newreply_recipients = array();
		$dao->save( $settings );

		$bbpnns = $this->child->load_lib( 'controller/common_core' );
		
		// Create new user in role
		$user = $this->factory->user->create_and_get( array( 'role' => 'administrator' ) );
		
		// Test no roles return false
		$this->assertFalse( $bbpnns->user_in_ok_role( $user->ID ) );

		$bbpnns->users_in_roles = array();
		
		// Save OK role
		$recipients = array( 'administrator' );
// 		update_option( 'bbpress_notify_newtopic_recipients', $recipients );
		$settings->newtopic_recipients = $recipients;
		$dao->save( $settings );
		
		$bbpnns = $this->child->load_lib( 'controller/common_core', null, array() );
		
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
		$bbpnns = $this->child->load_lib( 'controller/common_core' );

		$this->assertTrue( (bool) has_filter( 'bbpnns_is_in_effect', array( $bbpnns, 'bbpnns_is_in_effect' ) ), 'Filter found' );

		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		
		$settings->newtopic_recipients = [];
		$settings->newreply_recipients = [];
		$settings->override_bbp_forum_subscriptions = 
		$settings->override_bbp_topic_subscriptions = 
		$settings->notify_authors_topic = 
		$settings->notify_authors_reply = false;

		$dao->save($settings);
		$bbpnns = $this->child->load_lib( 'controller/common_core', [], $reload=true );
		
		$user = $this->factory->user->create_and_get( array( 'role' => 'administrator' ) );
		$this->assertFalse( $bbpnns->bbpnns_is_in_effect( null, $user->ID ) );
		
		$settings->override_bbp_forum_subscriptions = true;
		$dao->save($settings);
		$bbpnns = $this->child->load_lib( 'controller/common_core', [], $reload=true );
		$this->assertTrue( $bbpnns->bbpnns_is_in_effect( null, $user->ID ) );
		
		$settings->override_bbp_forum_subscriptions = false;
		$settings->override_bbp_topic_subscriptions = true;
		$bbpnns = $this->child->load_lib( 'controller/common_core', [], $reload=true );
		$this->assertTrue( $bbpnns->bbpnns_is_in_effect( null, $user->ID ) );
		
		$settings->override_bbp_topic_subscriptions = false;
		$settings->notify_authors_topic = true;
		$bbpnns = $this->child->load_lib( 'controller/common_core', [], $reload=true );
		$this->assertTrue( $bbpnns->bbpnns_is_in_effect( null, $user->ID ) );

		$settings->notify_authors_topic = false;
		$settings->notify_authors_reply = true;
		$bbpnns = $this->child->load_lib( 'controller/common_core', [], $reload=true );
		$this->assertTrue( $bbpnns->bbpnns_is_in_effect( null, $user->ID ) );
		
		$settings->notify_authors_reply = false;
		$settings->newtopic_recipients = ['administrator'];
		$bbpnns = $this->child->load_lib( 'controller/common_core', [], $reload=true );
		$this->assertTrue( $bbpnns->bbpnns_is_in_effect( null, $user->ID ) );
		
		
		$settings->newtopic_recipients = [];
		$settings->newreply_recipients = ['administrator'];
		$bbpnns = $this->child->load_lib( 'controller/common_core', [], $reload=true );
		$this->assertTrue( $bbpnns->bbpnns_is_in_effect( null, $user->ID ) );
	}
	
	public function test_dry_run_trace()
	{
	    remove_all_filters( 'bbpnns_dry_run_trace_info' );
	    remove_all_actions( 'bbpnns_dry_run_trace' );
	    
	    $bbpnns = $this->child->load_lib( 'controller/common_core', [], true );
	    
	    $this->assertTrue( (bool) has_action( 'bbpnns_dry_run_trace', array( $bbpnns, 'trace' ) ) );
	    
	    $message = 'Testing trace action';
	    
	    do_action( 'bbpnns_dry_run_trace', $message );
	    
	    $messages = apply_filters( 'bbpnns_dry_run_trace_info', [] );
	    
	    $this->assertNotEmpty( $messages, 'Messages not empty' );
	    $this->assertEquals( 1, count($messages), 'Got one message back' );
	    $this->assertRegexp( '/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \[\d+\] ' . $message . '/', $messages[0], 'Got expected message');
	    
	    remove_all_filters( 'bbpnns_dry_run_trace_info' );
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