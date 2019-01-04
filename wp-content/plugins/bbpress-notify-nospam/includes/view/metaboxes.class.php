<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Controls settings display
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_View_Metaboxes extends bbPress_Notify_noSpam {

	private $settings;
	
	public function __construct()
	{
		$this->settings = $this->load_lib( 'dal/settings_dao' )->load();
	}
	
	/**
	 * @since 1.4
	 */
	function notification_meta_box_content( $post )
	{
		$type = ( $post->post_type === $this->get_topic_post_type() ) ? 'topic' : 'reply';

		$default = $this->settings->{"default_{$type}_notification_checkbox"};
		$checked = checked( $default, true, false );
	
		wp_create_nonce( "bbpress_send_{$type}_notification_nonce" );
	
		wp_nonce_field( "bbpress_send_{$type}_notification_nonce", "bbpress_send_{$type}_notification_nonce" );
		printf( '<label><input type="checkbox" name="bbpress_notify_send_notification" %s> %s</label>', $checked, __( 'Send notification.', 'bbPress_Notify_noSpam' ) );
	}
	
}

/* End of file metaboxes.class.php */
/* Location: bbpress-notify-nospam/includes/view/metaboxes.class.php */
