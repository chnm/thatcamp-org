<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// start session for captcha validation
if (!isset ($_SESSION)) session_start(); 
$_SESSION['vscf-widget-rand'] = isset($_SESSION['vscf-widget-rand']) ? $_SESSION['vscf-widget-rand'] : rand(100, 999);

// the shortcode
function vscf_widget_shortcode($vscf_atts) {
	$vscf_atts = shortcode_atts( array( 
		"email_to" => get_bloginfo('admin_email'),
		"from_header" => vscf_from_header(),
		"subject" => '',
		"hide_subject" => '',
		"auto_reply" => '',
		"auto_reply_message" => '',
		"message_success" => '',
		"scroll_to_form" => ''
	), $vscf_atts);

	// get custom settings from settingspage
	$list_submissions_setting = esc_attr(get_option('vscf-setting-2'));
	$auto_reply_setting = esc_attr(get_option('vscf-setting-3'));
	$privacy_setting = esc_attr(get_option('vscf-setting-4'));
	$ip_address_setting = esc_attr(get_option('vscf-setting-19'));

	// get custom messages from settingspage
	$server_error_message = esc_attr(get_option('vscf-setting-15'));
	$thank_you_message = esc_attr(get_option('vscf-setting-16'));
	$auto_reply_message = esc_attr(get_option('vscf-setting-17'));

	// get custom labels from settingspage
	$name_label = esc_attr(get_option('vscf-setting-5'));
	$email_label = esc_attr(get_option('vscf-setting-6'));
	$subject_label = esc_attr(get_option('vscf-setting-7'));
	$captcha_label = esc_attr(get_option('vscf-setting-8'));
	$message_label = esc_attr(get_option('vscf-setting-9'));
	$privacy_label = esc_attr(get_option('vscf-setting-18'));
	$submit_label = esc_attr(get_option('vscf-setting-10'));
	$error_input_label = esc_attr(get_option('vscf-setting-11'));
	$error_textarea_label = esc_attr(get_option('vscf-setting-12'));
	$error_email_label = esc_attr(get_option('vscf-setting-13'));
	$error_captcha_label = esc_attr(get_option('vscf-setting-14'));

	// show default label if no custom label is set
	if (empty($name_label)) {
		$name_label = esc_attr__( 'Name', 'very-simple-contact-form' );
	}
	if (empty($email_label)) {
		$email_label = esc_attr__( 'Email', 'very-simple-contact-form' );
	}
	if (empty($subject_label)) {
		$subject_label = esc_attr__( 'Subject', 'very-simple-contact-form' );
	}
	if (empty($captcha_label)) {
		$captcha_label = esc_attr__( 'Enter number %s', 'very-simple-contact-form' );
	}
	if (empty($message_label)) {
		$message_label = esc_attr__( 'Message', 'very-simple-contact-form' );
	}
	if (empty($privacy_label)) {
		$privacy_label = esc_attr__( 'I consent to having this website collect my personal data via this form.', 'very-simple-contact-form' );
	}
	if (empty($submit_label)) {
		$submit_label = esc_attr__( 'Submit', 'very-simple-contact-form' );
	}
	if (empty($error_input_label)) {
		$error_input_label = esc_attr__( 'Please enter at least 2 characters', 'very-simple-contact-form' );
	}
	if (empty($error_textarea_label)) {
		$error_textarea_label = esc_attr__( 'Please enter at least 10 characters', 'very-simple-contact-form' );
	}
	if (empty($error_email_label)) {
		$error_email_label = esc_attr__( 'Please enter a valid email', 'very-simple-contact-form' );
	}
	if (empty($error_captcha_label)) {
		$error_captcha_label = esc_attr__( 'Please enter the correct number', 'very-simple-contact-form' );
	}

	// show default message if no custom message is set
	if (empty($server_error_message)) {
		$server_error_message = esc_attr__( 'Error! Could not send form. This might be a server issue.', 'very-simple-contact-form' );
	}

	// thank you message
	$value = $thank_you_message;
	if (empty($vscf_atts['message_success'])) {
		if (empty($value)) {
			$thank_you_message = esc_attr__( 'Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form' );
		} else {
			$thank_you_message = $value;
		}
	} else {
		$thank_you_message = $vscf_atts['message_success'];
	}

	// auto reply message
	$value = $auto_reply_message;
	if (empty($vscf_atts['auto_reply_message'])) {
		if (empty($value)) {
			$auto_reply_message = esc_attr__( 'Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form' );
		} else {
			$auto_reply_message = $value;
		}
	} else {
		$auto_reply_message = $vscf_atts['auto_reply_message'];
	}

	// set variables 
	$form_data = array(
		'form_name' => '',
		'form_email' => '',
		'form_subject' => '',
		'form_captcha' => '',
		'form_privacy' => '',
		'form_firstname' => '',
		'form_lastname' => '',
		'form_message' => ''
	);
	$error = false;
	$sent = false;
	$fail = false;

	// processing form
	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['vscf_widget_send']) && isset( $_POST['vscf_widget_nonce'] ) && wp_verify_nonce( $_POST['vscf_widget_nonce'], 'vscf_widget_nonce_action' ) ) {
		// sanitize content
		$post_data = array(
			'form_name' => sanitize_text_field($_POST['vscf_name']),
			'form_email' => sanitize_email($_POST['vscf_email']),
			'form_subject' => sanitize_text_field($_POST['vscf_subject']),
			'form_message' => wp_kses_post($_POST['vscf_message']),
			'form_captcha' => sanitize_text_field($_POST['vscf_captcha']),
			'form_privacy' => sanitize_key($_POST['vscf_privacy']),
			'form_firstname' => sanitize_text_field($_POST['vscf_firstname']),
			'form_lastname' => sanitize_text_field($_POST['vscf_lastname'])
		);

		// validate name
		$value = $post_data['form_name'];
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
			$value = $post_data['form_subject'];
			if ( strlen($value)<2 ) {
				$error_class['form_subject'] = true;
				$error = true;
			}
			$form_data['form_subject'] = $value;
		}

		// validate message
		$value = $post_data['form_message'];
		if ( strlen($value)<10 ) {
			$error_class['form_message'] = true;
			$error = true;
		}
		$form_data['form_message'] = $value;

		// validate captcha
		$value = $post_data['form_captcha'];
		if ( $value != $_SESSION['vscf-widget-rand'] ) { 
			$error_class['form_captcha'] = true;
			$error = true;
		}
		$form_data['form_captcha'] = $value;

		// validate first honeypot field
		$value = $post_data['form_firstname'];
		if ( strlen($value)>0 ) {
			$error = true;
		}
		$form_data['form_firstname'] = $value;

		// validate second honeypot field
		$value = $post_data['form_lastname'];
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

		// sending and saving form submission
		if ($error == false) {
			// hook to support plugin Contact Form DB
			do_action( 'vscf_before_send_mail', $form_data );
			// email admin
			$to = $vscf_atts['email_to'];
			// from email header
			$from = $vscf_atts['from_header'];
			// subject
			if (!empty($vscf_atts['subject'])) {	
				$subject = $vscf_atts['subject'];
			} elseif ($vscf_atts['hide_subject'] != "true") {
				$subject = "(".get_bloginfo('name').") " . $form_data['form_subject'];
			} else {
				$subject = get_bloginfo('name');
			}
			// auto reply to sender
			if ($vscf_atts['auto_reply'] == "true") {
				$auto_reply = true;
			} elseif ($vscf_atts['auto_reply'] == "false") {
				$auto_reply = false;
			} elseif ($auto_reply_setting == "yes") {
				$auto_reply = true;
			} else {
				$auto_reply = false;
			}
			// set consent
			$value = $post_data['form_privacy'];
			if ( $value ==  "yes" ) {
				$consent = esc_attr__( 'Yes', 'very-simple-contact-form' );
			} else {
				$consent = esc_attr__( 'No', 'very-simple-contact-form' );
			}
			// show or hide ip address
			if ($ip_address_setting == "yes") {
				$ip_address = '';
			} else {
				$ip_address = sprintf( esc_attr__( 'IP: %s', 'very-simple-contact-form' ), vscf_get_the_ip() );
			}
			// save form submission in database
			if ($list_submissions_setting == "yes") {
				$vscf_post_information = array(
					'post_title' => esc_attr($subject),
					'post_content' => $form_data['form_name'] . "\r\n\r\n" . $form_data['form_email'] . "\r\n\r\n" . $form_data['form_message'] . "\r\n\r\n" . sprintf( esc_attr__( 'Privacy consent: %s', 'very-simple-contact-form' ), $consent ) . "\r\n\r\n" . $ip_address,
					'post_type' => 'submission',
					'post_status' => 'pending',
					'meta_input' => array( "name_sub" => $form_data['form_name'], "email_sub" => $form_data['form_email'] )
				);
				$post_id = wp_insert_post($vscf_post_information);
			}
			// mail
			$content = $form_data['form_name'] . "\r\n\r\n" . $form_data['form_email'] . "\r\n\r\n" . $form_data['form_message'] . "\r\n\r\n" . sprintf( esc_attr__( 'Privacy consent: %s', 'very-simple-contact-form' ), $consent ) . "\r\n\r\n" . $ip_address; 
			$headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n";
			$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
			$headers .= "From: ".$form_data['form_name']." <".$from.">" . "\r\n";
			$headers .= "Reply-To: <".$form_data['form_email'].">" . "\r\n";
			$auto_reply_content = $auto_reply_message . "\r\n\r\n" . $form_data['form_name'] . "\r\n\r\n" . $form_data['form_email'] . "\r\n\r\n" . $form_data['form_message'] . "\r\n\r\n" . $ip_address; 
			$auto_reply_headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n";
			$auto_reply_headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
			$auto_reply_headers .= "From: ".get_bloginfo('name')." <".$from.">" . "\r\n";
			$auto_reply_headers .= "Reply-To: <".$vscf_atts['email_to'].">" . "\r\n";

			if( wp_mail($to, $subject, $content, $headers) ) { 
				if ($auto_reply == true) {
					wp_mail($form_data['form_email'], $subject, $auto_reply_content, $auto_reply_headers);
				}
				$result = $thank_you_message;
				$sent = true;
			} else {
				$result = $server_error_message;
				$fail = true;
			}		
		}
	}

	// hide or display subject field 
	if ($vscf_atts['hide_subject'] == "true") {
		$hide_subject = true;
	}

	// hide or display privacy field 
	if ($privacy_setting != "yes") {
		$hide_privacy = true;
	}

	// set nonce field
	$nonce = wp_nonce_field( 'vscf_widget_nonce_action', 'vscf_widget_nonce' ); 

	// scroll back to form location after submit
	if ($vscf_atts['scroll_to_form'] == "true") {
		$action = 'action="#vscf-anchor"';
		$anchor_begin = '<div id="vscf-anchor">';
		$anchor_end = '</div>';
	} else {
		$action = '';
		$anchor_begin = '';
		$anchor_end = '';
	}

	// contact form
	$email_form = '<form class="vscf" id="vscf" method="post" '.$action.'>
		<div class="form-group">
			<label for="vscf_name">'.esc_attr($name_label).': <span class="'.(isset($error_class['form_name']) ? "error" : "hide").'" >'.esc_attr($error_input_label).'</span></label>
			<input type="text" name="vscf_name" id="vscf_name" '.(isset($error_class['form_name']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_name']).'" />
		</div>
		<div class="form-group">
			<label for="vscf_email">'.esc_attr($email_label).': <span class="'.(isset($error_class['form_email']) ? "error" : "hide").'" >'.esc_attr($error_email_label).'</span></label>
			<input type="email" name="vscf_email" id="vscf_email" '.(isset($error_class['form_email']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_email']).'" />
		</div>
		<div'.(isset($hide_subject) ? ' class="hide"' : ' class="form-group"').'>
			<label for="vscf_subject">'.esc_attr($subject_label).': <span class="'.(isset($error_class['form_subject']) ? "error" : "hide").'" >'.esc_attr($error_input_label).'</span></label>
			<input type="text" name="vscf_subject" id="vscf_subject" '. (isset($error_class['form_subject']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_subject']).'" />
		</div>
		<div class="form-group">
			<label for="vscf_captcha">'.sprintf(esc_attr($captcha_label), $_SESSION['vscf-widget-rand']).': <span class="'.(isset($error_class['form_captcha']) ? "error" : "hide").'" >'.esc_attr($error_captcha_label).'</span></label>
			<input type="text" name="vscf_captcha" id="vscf_captcha" '.(isset($error_class['form_captcha']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_captcha']).'" />
		</div>
		<div class="form-group hide">
			<input type="text" name="vscf_firstname" id="vscf_firstname" class="form-control" maxlength="50" value="'.esc_attr($form_data['form_firstname']).'" />
		</div>
		<div class="form-group hide">
			<input type="text" name="vscf_lastname" id="vscf_lastname" class="form-control" maxlength="50" value="'.esc_attr($form_data['form_lastname']).'" />
		</div>
		<div class="form-group">
			<label for="vscf_message">'.esc_attr($message_label).': <span class="'.(isset($error_class['form_message']) ? "error" : "hide").'" >'.esc_attr($error_textarea_label).'</span></label>
			<textarea name="vscf_message" id="vscf_message" rows="10" '.(isset($error_class['form_message']) ? ' class="form-control error"' : ' class="form-control"').'>'.wp_kses_post($form_data['form_message']).'</textarea>
		</div>
		<div'.(isset($hide_privacy) ? ' class="hide"' : ' class="form-group"').'>
			<input type="hidden" name="vscf_privacy" id="vscf_privacy" value="no">
			<label><input type="checkbox" name="vscf_privacy" id="vscf_privacy" class="custom-control-input" value="yes" '.checked( esc_attr($form_data['form_privacy']), "yes", false ).' /> <span class="'.(isset($error_class['form_privacy']) ? "error" : "").'" >'.esc_attr($privacy_label).'</span></label>
		</div>
		<div class="form-group hide">
			'. $nonce .'
		</div>
		<div class="form-group">
			<button type="submit" name="vscf_widget_send" id="vscf_widget_send" class="btn btn-primary">'.esc_attr($submit_label).'</button>
		</div>
	</form>';
	
	// after form validation
	if ($sent == true) {
		unset($_SESSION['vscf-widget-rand']);
		return $anchor_begin . '<p class="vscf-info">'.esc_attr($result).'</p>' . $anchor_end;
	} elseif ($fail == true) {
		unset($_SESSION['vscf-widget-rand']);
		return $anchor_begin . '<p class="vscf-info">'.esc_attr($result).'</p>' . $anchor_end;
	} else {
		return $anchor_begin .$email_form. $anchor_end;
	}
} 
add_shortcode('contact-widget', 'vscf_widget_shortcode');
