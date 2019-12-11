<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// shortcode for page
function vscf_shortcode($vscf_atts) {
	// attributes
	$vscf_atts = shortcode_atts(array(
		'class' => 'vscf-container',
		'email_to' => '',
		'from_header' => vscf_from_header(),
		'prefix_subject' => '',
		'subject' => '',
		'label_name' => '',
		'label_email' => '',
		'label_subject' => '',
		'label_captcha' => '',
		'label_message' => '',
		'label_privacy' => '',
		'label_submit' => '',
		'error_name' => '',
		'error_email' => '',
		'error_subject' => '',
		'error_captcha' => '',
		'error_message' => '',
		'message_success' => '',
		'message_error' => '',
		'auto_reply_message' => ''
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
	$list_submissions_setting = get_option('vscf-setting-2');
	$subject_setting = get_option('vscf-setting-23');
	$auto_reply_setting = get_option('vscf-setting-3');
	$privacy_setting = get_option('vscf-setting-4');
	$ip_address_setting = get_option('vscf-setting-19');
	$anchor_setting = get_option('vscf-setting-21');

	// include labels
	include 'vscf-labels.php';

	// captcha
	$vscf_rand = vscf_random_number();

	// set nonce field
	$vscf_nonce_field = wp_nonce_field( 'vscf_nonce_action', 'vscf_nonce', true, false );

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
		// sanitize input
		if ($subject_setting != "yes") {
			$subject_value = sanitize_text_field($_POST['vscf_subject']);
		} else {
			$subject_value = '';
		}
		if($privacy_setting == "yes") {
			$privacy_value = sanitize_key($_POST['vscf_privacy']);
		} else {
			$privacy_value = '';
		}
		$post_data = array(
			'form_name' => sanitize_text_field($_POST['vscf_name']),
			'form_email' => sanitize_email($_POST['vscf_email']),
			'form_subject' => $subject_value,
			'form_captcha' => sanitize_text_field($_POST['vscf_captcha']),
			'form_captcha_hidden' => sanitize_text_field($_POST['vscf_captcha_hidden']),
			'form_message' => sanitize_textarea_field($_POST['vscf_message']),
			'form_privacy' => $privacy_value,
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
		return '<script type="text/javascript">window.location="'.vscf_redirect_success().'"</script>';
	} elseif ($fail == true) {
		return '<script type="text/javascript">window.location="'.vscf_redirect_error().'"</script>';
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

// shortcode for widget
function vscf_widget_shortcode($vscf_atts) {
	// attributes
	$vscf_atts = shortcode_atts(array(
		'class' => 'vscf-container',
		'email_to' => '',
		'from_header' => vscf_from_header(),
		'prefix_subject' => '',
		'subject' => '',
		'label_name' => '',
		'label_email' => '',
		'label_subject' => '',
		'label_captcha' => '',
		'label_message' => '',
		'label_privacy' => '',
		'label_submit' => '',
		'error_name' => '',
		'error_email' => '',
		'error_subject' => '',
		'error_captcha' => '',
		'error_message' => '',
		'message_success' => '',
		'message_error' => '',
		'auto_reply_message' => ''
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
	$list_submissions_setting = get_option('vscf-setting-2');
	$subject_setting = get_option('vscf-setting-23');
	$auto_reply_setting = get_option('vscf-setting-3');
	$privacy_setting = get_option('vscf-setting-4');
	$ip_address_setting = get_option('vscf-setting-19');
	$anchor_setting = get_option('vscf-setting-21');

	// include labels
	include 'vscf-labels.php';

	// captcha
	$vscf_rand = vscf_widget_random_number();

	// set nonce field
	$vscf_nonce_field = wp_nonce_field( 'vscf_widget_nonce_action', 'vscf_widget_nonce', true, false );

	// name and id of submit button
	$submit_name_id = 'vscf_widget_send';

	// form anchor
	if ($anchor_setting == "yes") {
		$anchor_begin = '<div id="vscf-anchor">';
		$anchor_end = '</div>';
	} else {
		$anchor_begin = '';
		$anchor_end = '';
	}

	// processing form
	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['vscf_widget_send']) && isset( $_POST['vscf_widget_nonce'] ) && wp_verify_nonce( $_POST['vscf_widget_nonce'], 'vscf_widget_nonce_action' ) ) {
		// sanitize input
		if ($subject_setting != "yes") {
			$subject_value = sanitize_text_field($_POST['vscf_subject']);
		} else {
			$subject_value = '';
		}
		if($privacy_setting == "yes") {
			$privacy_value = sanitize_key($_POST['vscf_privacy']);
		} else {
			$privacy_value = '';
		}
		$post_data = array(
			'form_name' => sanitize_text_field($_POST['vscf_name']),
			'form_email' => sanitize_email($_POST['vscf_email']),
			'form_subject' => $subject_value,
			'form_captcha' => sanitize_text_field($_POST['vscf_captcha']),
			'form_captcha_hidden' => sanitize_text_field($_POST['vscf_captcha_hidden']),
			'form_message' => sanitize_textarea_field($_POST['vscf_message']),
			'form_privacy' => $privacy_value,
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
		return '<script type="text/javascript">window.location="'.vscf_widget_redirect_success().'"</script>';
	} elseif ($fail == true) {
		return '<script type="text/javascript">window.location="'.vscf_widget_redirect_error().'"</script>';
	}

	// display form or the result of submission
	if ( isset( $_GET['vscfsw'] ) ) {
		if ( $_GET['vscfsw'] == 'success' ) {
			return $anchor_begin . '<p class="vscf-info">'.esc_attr($thank_you_message).'</p>' . $anchor_end;
		} elseif ( $_GET['vscfsw'] == 'fail' ) {
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
add_shortcode('contact-widget', 'vscf_widget_shortcode');
