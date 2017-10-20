<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start session for captcha validation
if (!isset ($_SESSION)) session_start(); 
$_SESSION['vscf-widget-rand'] = isset($_SESSION['vscf-widget-rand']) ? $_SESSION['vscf-widget-rand'] : rand(100, 999);

// The shortcode
function vscf_widget_shortcode($vscf_atts) {
	$vscf_atts = shortcode_atts( array( 
		"email_to" => get_bloginfo('admin_email'),
		"label_name" => __('Name', 'very-simple-contact-form'),
		"label_email" => __('Email', 'very-simple-contact-form'),
		"label_subject" => __('Subject', 'very-simple-contact-form'),
		"label_message" => __('Message', 'very-simple-contact-form'),
		"label_captcha" => __('Enter number %s', 'very-simple-contact-form'),
		"label_submit" => __('Submit', 'very-simple-contact-form'),
		"error_name" => __('Please enter at least 2 characters', 'very-simple-contact-form'),
		"error_subject" => __('Please enter at least 2 characters', 'very-simple-contact-form'),
		"error_message" => __('Please enter at least 10 characters', 'very-simple-contact-form'),
		"error_captcha" => __('Please enter the correct number', 'very-simple-contact-form'),
		"error_email" => __('Please enter a valid email', 'very-simple-contact-form'),
		"message_success" => __('Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form'),
		"message_error" => __('Error! Could not send form. This might be a server issue.', 'very-simple-contact-form'),
		"from_header" => '',
		"subject" => '',
		"prefix_subject" => '',
		"hide_subject" => '',
		"auto_reply" => '',
		"auto_reply_message" => __('Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form'),
		"scroll_to_form" => ''
	), $vscf_atts);

	// Set variables 
	$form_data = array(
		'form_name' => '',
		'form_email' => '',
		'form_subject' => '',
		'form_captcha' => '',
		'form_firstname' => '',
		'form_lastname' => '',
		'form_message' => ''
	);
	$error = false;
	$sent = false;
	$fail = false;

	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['vscf_widget_send']) ) {
	
		// Sanitize content
		$post_data = array(
			'form_name' => sanitize_text_field($_POST['vscf_name']),
			'form_email' => sanitize_email($_POST['vscf_email']),
			'form_subject' => sanitize_text_field($_POST['vscf_subject']),
			'form_message' => wp_kses_post($_POST['vscf_message']),
			'form_captcha' => sanitize_text_field($_POST['vscf_captcha']),
			'form_firstname' => sanitize_text_field($_POST['vscf_firstname']),
			'form_lastname' => sanitize_text_field($_POST['vscf_lastname'])
		);

		// Validate name
		$value = $post_data['form_name'];
		if ( strlen($value)<2 ) {
			$error_class['form_name'] = true;
			$error = true;
		}
		$form_data['form_name'] = $value;

		// Validate email
		$value = $post_data['form_email'];
		if ( empty($value) ) {
			$error_class['form_email'] = true;
			$error = true;
		}
		$form_data['form_email'] = $value;

		// Validate subject
		if ($vscf_atts['hide_subject'] != "true") {		
			$value = $post_data['form_subject'];
			if ( strlen($value)<2 ) {
				$error_class['form_subject'] = true;
				$error = true;
			}
			$form_data['form_subject'] = $value;
		}

		// Validate message
		$value = $post_data['form_message'];
		if ( strlen($value)<10 ) {
			$error_class['form_message'] = true;
			$error = true;
		}
		$form_data['form_message'] = $value;

		// Validate captcha
		$value = $post_data['form_captcha'];
		if ( $value != $_SESSION['vscf-widget-rand'] ) { 
			$error_class['form_captcha'] = true;
			$error = true;
		}
		$form_data['form_captcha'] = $value;

		// Validate first honeypot field
		$value = $post_data['form_firstname'];
		if ( strlen($value)>0 ) {
			$error = true;
		}
		$form_data['form_firstname'] = $value;

		// Validate second honeypot field
		$value = $post_data['form_lastname'];
		if ( strlen($value)>0 ) {
			$error = true;
		}
		$form_data['form_lastname'] = $value;

		// Sending form submission
		if ($error == false) {
			// Hook to support plugin Contact Form DB
			do_action( 'vscf_before_send_mail', $form_data );
			// Hook ends
			$to = $vscf_atts['email_to'];
			$auto_reply_to = $form_data['form_email'];
			// Subject
			if ($vscf_atts['hide_subject'] != "true") {
				if (!empty($vscf_atts['subject'])) {		
					$subject = $vscf_atts['subject'];
				} else {
					if (!empty($vscf_atts['prefix_subject'])) {	
						$subject = "(".$vscf_atts['prefix_subject'].") " . $form_data['form_subject'];
					} else {
						$subject = "(".get_bloginfo('name').") " . $form_data['form_subject'];
					}
				}
			} else {
				if (!empty($vscf_atts['subject'])) {		
					$subject = $vscf_atts['subject'];
				} else {
					if (!empty($vscf_atts['prefix_subject'])) {
						$subject = $vscf_atts['prefix_subject'];
					} else {
						$subject = get_bloginfo('name');
					}
				}
			}
			// From email header
			if (empty($vscf_atts['from_header'])) {
				$from = vscf_from_header();
			} else {
				$from = $vscf_atts['from_header'];
			}
			// Mail
			$message = $form_data['form_name'] . "\r\n\r\n" . $form_data['form_email'] . "\r\n\r\n" . $form_data['form_message'] . "\r\n\r\n" . sprintf( esc_attr__( 'IP: %s', 'very-simple-contact-form' ), vscf_get_the_ip() ); 
			$headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n";
			$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
			$headers .= "From: ".$form_data['form_name']." <".$from.">" . "\r\n";
			$headers .= "Reply-To: <".$form_data['form_email'].">" . "\r\n";
			$auto_reply_message = $vscf_atts['auto_reply_message'] . "\r\n\r\n" . $form_data['form_name'] . "\r\n\r\n" . $form_data['form_email'] . "\r\n\r\n" . $form_data['form_message'] . "\r\n\r\n" . sprintf( esc_attr__( 'IP: %s', 'very-simple-contact-form' ), vscf_get_the_ip() ); 
			$auto_reply_headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n";
			$auto_reply_headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
			$auto_reply_headers .= "From: ".get_bloginfo('name')." <".$from.">" . "\r\n";
			$auto_reply_headers .= "Reply-To: <".$vscf_atts['email_to'].">" . "\r\n";

			if( wp_mail($to, $subject, $message, $headers) ) { 
				if ($vscf_atts['auto_reply'] == "true") {
					wp_mail($auto_reply_to, $subject, $auto_reply_message, $auto_reply_headers);
				}
				$result = $vscf_atts['message_success'];
				$sent = true;
			} else {
				$result = $vscf_atts['message_error'];
				$fail = true;
			}		
		}
	}

	// Hide or display subject field 
	if ($vscf_atts['hide_subject'] == "true") {
		$hide = true;
	}

	// After submit scroll to form 
	if ($vscf_atts['scroll_to_form'] == "true") {
		$action = 'action="#vscf-anchor"';
		$anchor_begin = '<div id="vscf-anchor">';
		$anchor_end = '</div>';
	} else {
		$action = '';
		$anchor_begin = '';
		$anchor_end = '';
	}

	// Contact form
	$email_form = '<form class="vscf" id="vscf" method="post" '.$action.'>
		<div class="form-group">
			<label for="vscf_name">'.esc_attr($vscf_atts['label_name']).': <span class="'.(isset($error_class['form_name']) ? "error" : "hide").'" >'.esc_attr($vscf_atts['error_name']).'</span></label>
			<input type="text" name="vscf_name" id="vscf_name" '.(isset($error_class['form_name']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_name']).'" />
		</div>
		<div class="form-group">
			<label for="vscf_email">'.esc_attr($vscf_atts['label_email']).': <span class="'.(isset($error_class['form_email']) ? "error" : "hide").'" >'.esc_attr($vscf_atts['error_email']).'</span></label>
			<input type="email" name="vscf_email" id="vscf_email" '.(isset($error_class['form_email']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_email']).'" />
		</div>
		<div'.(isset($hide) ? ' class="hide"' : ' class="form-group"').'>
			<label for="vscf_subject">'.esc_attr($vscf_atts['label_subject']).': <span class="'.(isset($error_class['form_subject']) ? "error" : "hide").'" >'.esc_attr($vscf_atts['error_subject']).'</span></label>
			<input type="text" name="vscf_subject" id="vscf_subject" '. (isset($error_class['form_subject']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_subject']).'" />
		</div>
		<div class="form-group">
			<label for="vscf_captcha">'.sprintf(esc_attr($vscf_atts['label_captcha']), $_SESSION['vscf-widget-rand']).': <span class="'.(isset($error_class['form_captcha']) ? "error" : "hide").'" >'.esc_attr($vscf_atts['error_captcha']).'</span></label>
			<input type="text" name="vscf_captcha" id="vscf_captcha" '.(isset($error_class['form_captcha']) ? ' class="form-control error"' : ' class="form-control"').' maxlength="50" value="'.esc_attr($form_data['form_captcha']).'" />
		</div>
		<div class="form-group hide">
			<input type="text" name="vscf_firstname" id="vscf_firstname" class="form-control" maxlength="50" value="'.esc_attr($form_data['form_firstname']).'" />
		</div>
		<div class="form-group hide">
			<input type="text" name="vscf_lastname" id="vscf_lastname" class="form-control" maxlength="50" value="'.esc_attr($form_data['form_lastname']).'" />
		</div>
		<div class="form-group">
			<label for="vscf_message">'.esc_attr($vscf_atts['label_message']).': <span class="'.(isset($error_class['form_message']) ? "error" : "hide").'" >'.esc_attr($vscf_atts['error_message']).'</span></label>
			<textarea name="vscf_message" id="vscf_message" rows="10" '.(isset($error_class['form_message']) ? ' class="form-control error"' : ' class="form-control"').'>'.wp_kses_post($form_data['form_message']).'</textarea>
		</div>
		<div class="form-group">
			<button type="submit" name="vscf_widget_send" id="vscf_widget_send" class="btn btn-primary">'.esc_attr($vscf_atts['label_submit']).'</button>
		</div>
	</form>';
	
	// After form validation
	if ($sent == true) {
		unset($_SESSION['vscf-rand']);
		return $anchor_begin . '<p class="vscf-info">'.esc_attr($result).'</p>' . $anchor_end;
	} elseif ($fail == true) {
		return $anchor_begin . '<p class="vscf-info">'.esc_attr($result).'</p>' . $anchor_end;
	} else {
		return $anchor_begin .$email_form. $anchor_end;
	}
} 
add_shortcode('contact-widget', 'vscf_widget_shortcode');
