<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// the shortcode
function vscf_shortcode($vscf_atts) {
	// attributes
	$vscf_atts = shortcode_atts(array(
		"email_to" => '',
		"from_header" => vscf_from_header(),
		"subject" => '',
		"hide_subject" => '',
		"auto_reply" => '',
		"auto_reply_message" => '',
		"label_name" => '',
		"label_email" => '',
		"label_subject" => '',
		"label_captcha" => '',
		"label_message" => '',
		"label_privacy" => '',
		"label_submit" => '',
		"error_name" => '',
		"error_email" => '',
		"error_subject" => '',
		"error_captcha" => '',
		"error_message" => '',
		"message_success" => '',
		"message_error" => ''
	), $vscf_atts);

	// initialize variables
	$form_data = array(
		'form_name' => '',
		'form_email' => '',
		'form_subject' => '',
		'form_captcha' => '',
		'form_message' => '',
		'form_privacy' => '',
		'form_firstname' => '',
		'form_lastname' => ''
	);
	$error = false;
	$sent = false;
	$fail = false;

	// get custom settings from settingspage
	$list_submissions_setting = esc_attr(get_option('vscf-setting-2'));
	$auto_reply_setting = esc_attr(get_option('vscf-setting-3'));
	$privacy_setting = esc_attr(get_option('vscf-setting-4'));
	$ip_address_setting = esc_attr(get_option('vscf-setting-19'));
	$anchor_setting = esc_attr(get_option('vscf-setting-21'));

	// include labels
	include 'vscf-labels.php';

	// captcha
	$captcha = vscf_random_number();

	// hide or display subject field
	if ($vscf_atts['hide_subject'] == "true") {
		$hide_subject = true;
	}

	// hide or display privacy field
	if ($privacy_setting != "yes") {
		$hide_privacy = true;
	}

	// set nonce field
	$nonce = wp_nonce_field( 'vscf_nonce_action', 'vscf_nonce', true, false );

	// name and id of submit button
	$submit_name_id = 'vscf_send';

	// form anchor
	if ($anchor_setting == "yes") {
		$anchor_begin = '<div id="vscf-anchor">';
		$anchor_end = '</div>';
	} else {
		$anchor_begin = '';
		$anchor_end = '';
	}

	// processing form
	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['vscf_send']) && isset( $_POST['vscf_nonce'] ) && wp_verify_nonce( $_POST['vscf_nonce'], 'vscf_nonce_action' ) ) {
		// sanitize content
		$post_data = array(
			'form_name' => sanitize_text_field($_POST['vscf_name']),
			'form_email' => sanitize_email($_POST['vscf_email']),
			'form_subject' => sanitize_text_field($_POST['vscf_subject']),
			'form_captcha' => sanitize_text_field($_POST['vscf_captcha']),
			'form_message' => sanitize_textarea_field($_POST['vscf_message']),
			'form_privacy' => sanitize_key($_POST['vscf_privacy']),
			'form_firstname' => sanitize_text_field($_POST['vscf_firstname']),
			'form_lastname' => sanitize_text_field($_POST['vscf_lastname'])
		);

		// include validation
		include 'vscf-validate.php';

		// include sending and saving form submission
		include 'vscf-submission.php';
	}

	// include form
	include 'vscf-form.php';

	// after form validation
	if ($sent == true) {
		vscf_redirect_success();
	} elseif ($fail == true) {
		vscf_redirect_error();
	}

	// display form or the result of submission
	if ( isset( $_GET['vscfsp'] ) ) {
		if ( $_GET['vscfsp'] == 'success' ) {
			return $anchor_begin . '<p class="vscf-info">'.esc_attr($thank_you_message).'</p>' . $anchor_end;
		} elseif ( $_GET['vscfsp'] == 'fail' ) {
			return $anchor_begin . '<p class="vscf-info">'.esc_attr($server_error_message).'</p>' . $anchor_end;
		}	
	} else {
		if ($error == true) {
			return $anchor_begin .$email_form. $anchor_end;
		} else {
			return $email_form;
		}
	}	   		
} 
add_shortcode('contact', 'vscf_shortcode');
