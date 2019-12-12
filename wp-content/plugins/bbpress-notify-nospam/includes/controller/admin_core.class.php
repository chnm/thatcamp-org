<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * 
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_Controller_Admin_Core extends bbPress_Notify_noSpam {

	public function __construct()
	{
		// Add Settings link to the plugin page
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		
		// Deactivate original bbPress Notify if found
		add_action( 'admin_init', array( $this, 'deactivate_old' ) );
		
		// Notification meta boxes if needed
		add_action( 'add_meta_boxes', array( $this, 'add_notification_meta_box' ), 10 );
		
		// Required by BuddyPress bridge Add-on and others.
		do_action( 'bbpnns_register_settings' );
	}
	
	/**
	 * @since 1.4
	 */
	function add_notification_meta_box()
	{
		$view = $this->load_lib( 'view/metaboxes' );
		
		add_meta_box( 'send_notification', __( 'Notifications', 'bbPress_Notify_noSpam' ),
				array( $view, 'notification_meta_box_content' ),  $this->get_topic_post_type(), 'side', 'high' );
	
		add_meta_box( 'send_notification', __( 'Notifications', 'bbPress_Notify_noSpam' ),
				array( $view, 'notification_meta_box_content' ),  $this->get_reply_post_type(), 'side', 'high' );
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
	 * Deactivate the non-nospam version of bbPress Notify
	 * @since 1.0
	 */
	function deactivate_old()
	{
		$old_plugin = 'bbpress-notify/bbpress-notify.php';
		if ( is_plugin_active( $old_plugin ) )
		{
			deactivate_plugins( $old_plugin );
		}
	}
}

/* End of file admin_core.class.php */
/* Location: bbpress-notify-nospam/includes/controller/admin_core.class.php */
