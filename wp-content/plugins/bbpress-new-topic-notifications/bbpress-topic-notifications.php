<?php
/*
Plugin Name: bbPress New Topic Notifications
Plugin URI: http://wordpress.org/extend/bbpress-new-topic-notifications
Description: Send notification emails to specific users when a new bbPress topic is posted.
Version: 1.1
Author: jaredatch
Author URI: http://jaredatchison.com 

Much of this plugin was adapted fron Notifly!
http://wordpress.org/extend/plugins/notifly/
*/

/**
 * Copyright (c) 2011 Jared Atchison. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

class ja_bbPress_Topic_Notifications {

	static $instance;

	function __construct() {
		
		self::$instance =& $this;
		
		// Admin area
		add_action( 'admin_init',           array( $this, 'admin_settings' ),       15      );
		add_filter( 'plugin_action_links',  array( $this, 'add_settings_link' ),    10, 2   );
		
		// Triggers notification
		add_action( 'bbp_new_topic',        array( $this, 'send_notification' ),    10, 4   );
		
		// Triggers activation check
		register_activation_hook( __FILE__, array( $this, 'activation_hook' ) );
	}
	
	/**
	 * activation_hook()
	 *
	 * Check to see if bbPress is activated
	 */
	function activation_hook() {
		// Obviously, this wouldn't work to well without bbPress
        if( !class_exists( 'bbPress' ) ) {
	        deactivate_plugins( plugin_basename(__FILE__) ); // Deactivate ourself
            wp_die( __('Sorry, you need to activate bbPress first.', 'ja_bbp_notifications') );
		}
		
		//Default email template
		$email_template =   "Howdy!\n\n";
		$email_template .=  "A new topic has been posted.\n\n";
		$email_template .=  "Topic URL: [topic-url]\n";
		$email_template .=  "Title: [topic-title]\n";
		$email_template .=  "Author: [topic-author]\n\n";
		$email_template .=  "Excerpt: [topic-excerpt]";
		
		// Set default template on activation
		if ( !get_option( 'ja_bbp_notification_email_template' ) ) {
			update_option( 'ja_bbp_notification_email_template', $email_template );
		}					

	}
	
	/**
	 * add_settings_link( $links, $file )
	 *
	 * Add Settings link to plugins area
	 *
	 * @return string Links
	 */
	function add_settings_link( $links, $file ) {
		if ( plugin_basename( __FILE__ ) == $file ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=bbpress' ) . '#ja_bbp_notification_email_addresses">' . __( 'Settings', 'ja_bbp_notifications' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
	
	/**
	 * admin_settings()
	 *
	 * Setup the settings on the main bbPress forum settings page
	 */
	function admin_settings() {
		// Add the section to primary bbPress options
		add_settings_section( 'ja_bbp_notification_options', __( 'New Topic Notifications', 'ja_bbp_notifications' ), array( $this, 'section_heading' ), 'bbpress' );

		// Add the email address textarea
		add_settings_field( 'ja_bbp_notification_email_addresses', __( 'Email Addresses', 'ja_bbp_notifications' ), array( $this, 'email_addresses_textarea' ), 'bbpress', 'ja_bbp_notification_options' );
		
		// Add the email template textarea
		add_settings_field( 'ja_bbp_notification_email_template', __( 'Email Template', 'ja_bbp_notifications' ), array( $this, 'email_template_textarea' ), 'bbpress', 'ja_bbp_notification_options' );

		// Register our settings with the bbPress forum settings page
		register_setting( 'bbpress', 'ja_bbp_notification_email_addresses', array( $this, 'validate_email_addresses' ) );
		register_setting( 'bbpress', 'ja_bbp_notification_email_template', array( $this, 'validate_email_template' ) );
	}
	
	/**
	 * section_heading()
	 *
	 * Output the new topic notification section in the bbPress forum settings
	 *
	 */
	function section_heading() {
		_e( 'Email addresses of people to notifly when a new topic is posted. One per line.', 'ja_bbp_notifications' );
	}
	
	/**
	 * email_addresses_textarea()
	 *
	 * Output the textarea of email addresses
	 */
	function email_addresses_textarea() {
		$email_addresses = get_option( 'ja_bbp_notification_email_addresses' );
		$email_addresses = str_replace( ' ', "\n", $email_addresses );

		echo '<textarea id="ja_bbp_notification_email_addresses" cols="50" rows="4" name="ja_bbp_notification_email_addresses">' . $email_addresses . '</textarea>';
	}
	
	/**
	 * email_template_textarea()
	 *
	 * Output the textarea of email addresses
	 */
	function email_template_textarea() {
		$email_template = get_option( 'ja_bbp_notification_email_template' );
		$shortcodes = '[topic-title], [topic-content], [topic-excerpt], [topic-url], [topic-author]';
		
		echo '<textarea id="ja_bbp_notification_email_template" cols="50" rows="8" name="ja_bbp_notification_email_template">' . $email_template . '</textarea>';
		echo '<p>' . __('Availabe shortcodes:', 'ja_bbp_notifications') . '<br />' . $shortcodes . '</p>';
	}
	
	/**
	 * validate_email_addresses( $email_addresses )
	 *
	 * Returns validated results
	 *
	 * @param string $email_addresses
	 * @return string
	 *
	 */
	function validate_email_addresses( $email_addresses ) {

		// Make array out of textarea lines
		$valid_addresses = '';
		$recipients      = str_replace( ' ', "\n", $email_addresses );
		$recipients      = explode( "\n", $recipients );

		// Check validity of each address
		foreach ( $recipients as $recipient ) {
			if ( is_email( trim( $recipient ) ) )
				$valid_addresses .= $recipient . "\n";
		}

		// Trim off extra whitespace
		$valid_addresses = trim( $valid_addresses );
		
		return $valid_addresses;
	}
	
	/**
	 * validate_email_template ( $email_template )
	 *
	 * Returns sanitized results
	 *
	 * @param string $email_template
	 * @return string
	 *
	 */
	function validate_email_template( $email_template ) {
		$email_template = esc_html( $email_template );
		return $email_template;
	}
	
	/**
	 * get_recipients()
	 *
	 * Gets the recipients
	 *
	 * @return array
	 */
	function get_recipients() {
		// Get recipients and turn into an array
		$recipients = get_option( 'ja_bbp_notification_email_addresses' );
		$recipients = str_replace( ' ', "\n", $recipients );
		$recipients = explode( "\n", $recipients );
		$recipients = array_values( $recipients );

		// Return result
		return $recipients;
	}
	
	/**
	 * send_notification()
	 *
	 * Send the notification e-mails to the addresses defined in options
	 */
	function send_notification( $topic_id = 0, $forum_id = 0, $anonymous_data = false, $topic_author = 0 ) {
		
		// Grab stuff we will be needing for the email
		$recipients     = $this->get_recipients();
		$blogname       = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$topic_title    = html_entity_decode( strip_tags( bbp_get_topic_title( $topic_id ) ), ENT_NOQUOTES, 'UTF-8');
		$topic_content  = html_entity_decode( strip_tags( bbp_get_topic_content( $topic_id ) ), ENT_NOQUOTES, 'UTF-8');
		$topic_excerpt  = html_entity_decode( strip_tags( bbp_get_topic_excerpt( $topic_id, 100 ) ), ENT_NOQUOTES, 'UTF-8');
		$topic_author   = bbp_get_topic_author( $topic_id );
		$topic_url      = bbp_get_topic_permalink( $topic_id );

		// Get the template
		$email_template = get_option( 'ja_bbp_notification_email_template' );

		// Swap out the shortcodes with useful information :)
        $find           = array( '[topic-title]', '[topic-content]', '[topic-excerpt]', '[topic-author]', '[topic-url]' );
        $replace        = array( $topic_title, $topic_content, $topic_excerpt, $topic_author, $topic_url );
        $email_body     = str_replace( $find, $replace, $email_template );
		$email_subject  = $blogname . __( ' new topic', 'ja_bbp_notifications' ) . ' - ' . $topic_title;

		if ( !empty( $recipients ) ) {
			// Send email to each user
			foreach ( $recipients as $recipient ) {
				@wp_mail( $recipient, $email_subject, $email_body );
			}
		}
	}
	
}

new ja_bbPress_Topic_Notifications();