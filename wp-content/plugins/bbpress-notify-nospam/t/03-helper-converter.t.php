<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 * @group bbPress_Notify_noSpam_helper
 * @group bbPress_Notify_noSpam_helper_converter
 */
require_once( 'bbPress_Notify_noSpam_Child.class.php' );

class Tests_bbPress_Notify_noSpam_Helper_Converter extends WP_UnitTestCase {

	public $child;
	
	function setUp()
	{
		parent::setUp();
		
		$this->child = new bbPress_Notify_noSpam_Child();
		
		$this->bbpress_topic_post_type = $this->child->get_topic_post_type();
		$this->bbpress_reply_post_type = $this->child->get_reply_post_type();
		
		$this->options = array(
				'bbpnns-dismissed-1_7_1'        => 'bbpnns-dismissed-1_7_1',
				'bbpnns-opt-out-msg'            => 'bbpnns-opt-out-msg',
				'bbpress-notify-pro-dismissed'  => 'bbpress-notify-pro-dismissed' ,
				'bbpress_notify_newtopic_background' => 'newtopic_background',
				'bbpress_notify_newreply_background' => 'newreply_background',
				'bbpress_notify_newtopic_recipients' => 'newtopic_recipients',
				'bbpress_notify_newreply_recipients' => 'newreply_recipients',
				'bbpress_notify_newtopic_email_subject' => 'newtopic_email_subject',
				'bbpress_notify_newtopic_email_body'    => 'newtopic_email_body',
				'bbpress_notify_newreply_email_subject' => 'newreply_email_subject',
				'bbpress_notify_newreply_email_body'    => 'newreply_email_body',
				"bbpress_notify_default_{$this->bbpress_topic_post_type}_notification" => 'default_topic_notification_checkbox',
				"bbpress_notify_default_{$this->bbpress_reply_post_type}_notification" => 'default_reply_notification_checkbox',
				'bbpress_notify_encode_subject'         => 'encode_subject',
				'bbpnns_notify_authors_topic'           => 'notify_authors_topic',
				'bbpnns_notify_authors_reply'           => 'notify_authors_reply',
				'bbpnns_hijack_bbp_subscriptions_forum' => 'override_bbp_forum_subscriptions',
				'bbpnns_hijack_bbp_subscriptions_topic' => 'override_bbp_topic_subscriptions',
				'bbpress_notify_message_type'           => 'html',
				'bbpnns_dismissed_admin_notices'        => 'bbpnns_dismissed_admin_notices',
				'bbpress_notify_hidden_forum_topic_override' => false,
				'bbpress_notify_hidden_forum_reply_override' => false,
		);
	}

	function test_construct()
	{
		$conv = $this->child->load_lib( 'helper/converter', array( 'add_action' => false ) );
		$this->assertFalse( (bool) has_action( 'admin_enqueue_scripts', array($conv, 'enqueue_scripts')), 'Action not added as expected');
		
		$conv = new bbPress_Notify_noSpam_Helper_Converter();
		$this->assertTrue( (bool) has_action( 'admin_enqueue_scripts', array($conv, 'enqueue_scripts')), 'Default adds action');
	}
	
	
	function test_do_db_upgrade()
	{
		$this->_set_up_legacy_data();
		
		$conv = $this->child->load_lib( 'helper/converter', $enqueue_action=false );
		
		$conv->do_db_upgrade();
		
		$dao = $this->child->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
		
		$this->assertTrue( get_option( 'bbpnns_v2_conversion_complete', false ), 'The completed option was set' );
		
		foreach ($this->options as $key => $value )
		{
			$this->assertFalse( get_option( $key, false ), "The legacy option '$key' has been removed" );
		}
		
		$this->assertTrue( $settings->background_notifications, 'Got true for background_notifications' );
		
		foreach ( $this->options as $key => $prop )
		{
			if ( in_array( $key, array( 'bbpnns_dismissed_admin_notices', 'bbpnns-dismissed-1_7_1', 'bbpnns-opt-out-msg', 'bbpress-notify-pro-dismissed' )))
			{
				continue;
			}

			$value = $prop;
			
			switch ( $key )
			{
				case 'bbpress_notify_message_type':
					$prop  = 'email_type';
					$value = 'html';
					break;
				case 'bbpress_notify_newtopic_recipients':
				case 'bbpress_notify_newreply_recipients':
					$value = array( 'administrator' => 'Administrator');
					break;
				case 'bbpress_notify_encode_subject':
				case 'bbpress_notify_newtopic_background':
				case 'bbpress_notify_newreply_background':
				case "bbpress_notify_default_{$this->bbpress_topic_post_type}_notification":
				case "bbpress_notify_default_{$this->bbpress_reply_post_type}_notification":
				case 'bbpnns_hijack_bbp_subscriptions_forum':
				case 'bbpnns_hijack_bbp_subscriptions_topic':
				case 'bbpnns_notify_authors_topic':
				case 'bbpnns_notify_authors_reply':
					$value = true;
					break;
				case 'bbpress_notify_hidden_forum_topic_override':
					$value = false;
					$prop  = 'hidden_forum_topic_override';
				case 'bbpress_notify_hidden_forum_reply_override':
					$value = false;
					$prop  = 'hidden_forum_reply_override';
					break;
				default:				
			}

			$this->assertEquals( $settings->{$prop}, $value, "Got expected value for $key" );
		}
		
	}
	
	
	function _set_up_legacy_data()
	{
		foreach ( $this->options as $key => $value )
		{
			if ( 'bbpress_notify_newtopic_recipients' === $key || 'bbpress_notify_newreply_recipients' === $key )
			{
				$value = array( 'administrator' );
			}
			
			update_option( $key, $value );
		}
	}
}

/* End of 03-helper-converter.t.php */
/* Location: t/03-helper-converter.t.php */
