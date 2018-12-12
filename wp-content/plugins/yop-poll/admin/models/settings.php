<?php
class YOP_Poll_Settings {
	public static function get_all_settings() {
		$settings = get_option( 'yop_poll_settings' );
		return $settings;
	}
	public static function get_email_settings() {
		$email_settings = array();
		$settings = self::get_all_settings();
		if ( '' !== $settings ) {
			$unserialized_settings = unserialize( $settings );
			$email_settings = array(
				'from_name' => $unserialized_settings['email']['from_name'],
				'from_email' => $unserialized_settings['email']['from_email'],
				'recipients' => $unserialized_settings['email']['recipients'],
				'subject' => $unserialized_settings['email']['subject'],
				'message' => $unserialized_settings['email']['message']
			);
		}
		return $email_settings;
	}
	public static function get_integrations() {
		$integrations_settings = array();
		$settings = self::get_all_settings();
		if ( '' !== $settings ) {
			$unserialized_settings = unserialize( $settings );
			$integrations_settings = array(
				'reCaptcha' => array(
					'enabled' => ( isset( $unserialized_settings['integrations'] ) && isset( $unserialized_settings['integrations']['reCaptcha'] ) && isset( $unserialized_settings['integrations']['reCaptcha']['integration'] ) ) ? $unserialized_settings['integrations']['reCaptcha']['integration'] : '',
	                'site_key' => ( isset( $unserialized_settings['integrations'] ) && isset( $unserialized_settings['integrations']['reCaptcha'] ) && isset( $unserialized_settings['integrations']['reCaptcha']['site_key'] ) ) ? $unserialized_settings['integrations']['reCaptcha']['site_key'] : '',
					'secret_key' => ( isset( $unserialized_settings['integrations'] ) && isset( $unserialized_settings['integrations']['reCaptcha'] ) && isset( $unserialized_settings['integrations']['reCaptcha']['secret_key'] ) ) ? $unserialized_settings['integrations']['reCaptcha']['secret_key'] : ''
				),
				'facebook' => array(
					'enabled' => ( isset( $unserialized_settings['integrations'] ) && isset( $unserialized_settings['integrations']['facebook'] ) && isset( $unserialized_settings['integrations']['facebook']['integration'] ) ) ? $unserialized_settings['integrations']['facebook']['integration'] : '',
	                'app_id' => ( isset( $unserialized_settings['integrations'] ) && isset( $unserialized_settings['integrations']['facebook'] ) && isset( $unserialized_settings['integrations']['facebook']['app_id'] ) ) ? $unserialized_settings['integrations']['facebook']['app_id'] : ''
				),
				'google' => array(
					'enabled' => ( isset( $unserialized['integrations'] ) && isset( $unserialized['integrations']['google'] ) && isset( $unserialized['integrations']['google']['integration'] ) ) ? $unserialized['integrations']['google']['integration'] : '',
	                'app_id' => ( isset( $unserialized['integrations'] ) && isset( $unserialized['integrations']['google'] ) && isset( $unserialized['integrations']['google']['app_id'] ) ) ? $unserialized['integrations']['google']['app_id'] : '',
	                'app_secret' => ( isset( $unserialized['integrations'] ) && isset( $unserialized['integrations']['google'] ) && isset( $unserialized['integrations']['google']['app_secret'] ) ) ? $unserialized['integrations']['google']['app_secret'] : ''
				)
			);
		}
		return $integrations_settings;
	}
}
