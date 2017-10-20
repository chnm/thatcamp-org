<?php
/**
 * bbPress Notify (No Spam) Uninstall
 *
 * Uninstall methods
 *
 */
if( ! defined('Bbpnns_TEST_UNINSTALL') && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

require_once( 'bbpress-notify-nospam.php' );

class bbPress_Notify_noSpam_Uninstall extends bbPress_Notify_noSpam
{
	public function __construct()
	{
		// Defer removal as we need bbPress to be loaded first
		add_action( 'plugins_loaded', array( $this, 'do_stuff' ) );
	}
	
	public function do_stuff()
	{
		$this->bbpress_topic_post_type = bbp_get_topic_post_type();
		$this->bbpress_reply_post_type = bbp_get_reply_post_type();
		
		$this->delete_options();
	}
	
	
	public function delete_options()
	{
		$options = array(
				'bbpnns-dismissed-1_7_1',
				'bbpnns-opt-out-msg',
				'bbpress-notify-pro-dismissed',
				'bbpress_notify_newtopic_background',
				'bbpress_notify_newreply_background',
				'bbpress_notify_newtopic_recipients',
				'bbpress_notify_newreply_recipients',
				'bbpress_notify_newtopic_email_subject',
				'bbpress_notify_newtopic_email_body',
				'bbpress_notify_newreply_email_subject',
				'bbpress_notify_newreply_email_body',
				"bbpress_notify_default_{$this->bbpress_topic_post_type}_notification",
				"bbpress_notify_default_{$this->bbpress_reply_post_type}_notification",
				'bbpress_notify_encode_subject',
				'bbpnns_notify_authors_topic',
				'bbpnns_notify_authors_reply',
				'bbpnns_hijack_bbp_subscriptions_forum',
				'bbpnns_hijack_bbp_subscriptions_topic',
				'bbpress_notify_message_type',
		);
		
		
		foreach ( $options as $option )
		{
			delete_option( $option );
		}
		
	}
	
	
}


new bbPress_Notify_noSpam_Uninstall();

/* End of uninstall.php */
/* Location: bbpress-notify-nospam/uninstall.php */
