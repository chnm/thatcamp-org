<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

		// validate name
		$value = stripslashes($post_data['form_name']);
		if ( strlen($value)<2 ) {
			$error_class['form_name'] = true;
			$error = true;
		}
		$form_data['form_name'] = $value;

		// validate email
		$value = $post_data['form_email'];
		if ( empty($value) ) {
			$error_class['form_email'] = true;
			$error = true;
		}
		$form_data['form_email'] = $value;

		// validate subject
		if ($vscf_atts['hide_subject'] != "true") {
			$value = stripslashes($post_data['form_subject']);
			if ( strlen($value)<2 ) {
				$error_class['form_subject'] = true;
				$error = true;
			}
			$form_data['form_subject'] = $value;
		}

		// validate captcha
		$value = stripslashes($post_data['form_captcha']);
		if ( $value != $captcha ) {
			$error_class['form_captcha'] = true;
			$error = true;
		}
		$form_data['form_captcha'] = $value;

		// validate message
		$value = stripslashes($post_data['form_message']);
		if ( strlen($value)<10 ) {
			$error_class['form_message'] = true;
			$error = true;
		}
		$form_data['form_message'] = $value;

		// validate first honeypot field
		$value = stripslashes($post_data['form_firstname']);
		if ( strlen($value)>0 ) {
			$error = true;
		}
		$form_data['form_firstname'] = $value;

		// validate second honeypot field
		$value = stripslashes($post_data['form_lastname']);
		if ( strlen($value)>0 ) {
			$error = true;
		}
		$form_data['form_lastname'] = $value;

		// validate privacy
		if ($privacy_setting == "yes") {
			$value = $post_data['form_privacy'];
			if ( $value !=  "yes" ) {
				$error_class['form_privacy'] = true;
				$error = true;
			}
			$form_data['form_privacy'] = $value;
		}
