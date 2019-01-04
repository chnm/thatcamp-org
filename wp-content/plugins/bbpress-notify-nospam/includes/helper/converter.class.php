<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Controls converter functions.
 * 
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_Helper_Converter extends bbPress_Notify_noSpam {

	public function __construct( $params=array() )
	{
		if ( ! isset( $params['add_action'] ) || false !== $params['add_action'] )
		{
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts'), 10 );
		}
	}
	
	public function enqueue_scripts()
	{
		wp_enqueue_style( $this->domain . '-converter', $this->get_env()->css_url . 'converter.css', array(), self::VERSION );
		
		wp_enqueue_script( $this->domain . '-converter', $this->get_env()->js_url . 'converter.js', array( 'jquery' ), self::VERSION );
		wp_localize_script( $this->domain . '-converter', 'bbpnns_converter', array(
				'success_message' => __( 'Conversion successful!', 'bbPress_Notify_noSpam' ),
				'error_message'   => __( 'There was an error converting your database. Please contact support.', 'bbPress_Notify_noSpam' )
		) );
	}
	
	/**
	 * Perform the actual conversion
	 * @return boolean
	 */
	public function do_db_upgrade()
	{
		global $wpdb;
	
		$this->bbpress_topic_post_type = $this->get_topic_post_type();
		$this->bbpress_reply_post_type = $this->get_reply_post_type();
	
		$dao = $this->load_lib( 'dal/settings_dao' );
		$settings = $dao->load();
	
		$options = array(
				'bbpnns-dismissed-1_7_1'        => null,
				'bbpnns-opt-out-msg'            => null,
				'bbpress-notify-pro-dismissed'  => null ,
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
				'bbpress_notify_message_type'           => 'email_type',
				'bbpress_notify_hidden_forum_topic_override' => 'hidden_forum_topic_override',
				'bbpress_notify_hidden_forum_reply_override' => 'hidden_forum_reply_override',
				'bbpnns_dismissed_admin_notices'        => null,
		);
	
		$sql = sprintf( "select option_name, option_value from %s where option_name in ( %s )", $wpdb->options, join(', ', array_fill( 0, count($options), '%s' )) );
		$sth = $wpdb->prepare( $sql, array_keys( $options ) );
		$results = $wpdb->get_results( $sth );

		global $wp_roles;
		$all_roles = $wp_roles->roles;
		
		foreach ( $results as $row )
		{
			// Only work on not null maps
			if ( ! $options[$row->option_name] ) {
				continue;
			}
			
			$property = $options[$row->option_name];
			$value = maybe_unserialize( $row->option_value );
			
			if ( in_array( $row->option_name, array( 'bbpress_notify_newreply_recipients', 'bbpress_notify_newtopic_recipients' ) ) )
			{
				$roles = array();
				foreach ( $value as $role )
				{
					if ( ! isset( $all_roles[$role] ) ) 
					{
						continue;
					}
					$roles[$role] = $all_roles[$role]['name'];
				}
				
				$value = $roles;
			}
			
			$settings->{$property} = $value;
		}
	
		$settings->background_notifications = ( $settings->newtopic_background || $settings->newreply_background );
		$dao->save( $settings );
	
		// Finally delete the old options
		foreach ( $options as $option_name => $option_value )
		{
			delete_option( $option_name );
		}
	
		update_option( 'bbpnns_v2_conversion_complete', true );
	
		return true;
	}
	
}

/* End of file converter.class.php */
/* Location: bbpress-notify-nospam/includes/helper/converter.class.php */
