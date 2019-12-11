<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	// contact form
	$email_form = '<form id="vscf" class="'.$vscf_atts['class'].'" method="post">
		<div class="form-group vscf-name-group">
			<label for="vscf_name">'.esc_attr($name_label).': <span class="'.(isset($error_class['form_name']) ? "vscf-error" : "vscf-hide").'" >'.esc_attr($error_name_label).'</span></label>
			<input type="text" name="vscf_name" id="vscf_name" '.(isset($error_class['form_name']) ? ' class="form-control vscf-error"' : ' class="form-control"').' value="'.esc_attr($form_data['form_name']).'" />
		</div>
		<div class="form-group vscf-email-group">
			<label for="vscf_email">'.esc_attr($email_label).': <span class="'.(isset($error_class['form_email']) ? "vscf-error" : "vscf-hide").'" >'.esc_attr($error_email_label).'</span></label>
			<input type="email" name="vscf_email" id="vscf_email" '.(isset($error_class['form_email']) ? ' class="form-control vscf-error"' : ' class="form-control"').' value="'.esc_attr($form_data['form_email']).'" />
		</div>
		'. (($subject_setting != "yes") ? '
			<div class="form-group vscf-subject-group">
				<label for="vscf_subject">'.esc_attr($subject_label).': <span class="'.(isset($error_class['form_subject']) ? "vscf-error" : "vscf-hide").'" >'.esc_attr($error_subject_label).'</span></label>
				<input type="text" name="vscf_subject" id="vscf_subject" '. (isset($error_class['form_subject']) ? ' class="form-control vscf-error"' : ' class="form-control"').' value="'.esc_attr($form_data['form_subject']).'" />
			</div>
		' : '') .'
		<div class="form-group vscf-captcha-group">
			<label for="vscf_captcha">'.sprintf(esc_attr($captcha_label), $vscf_rand).': <span class="'.(isset($error_class['form_captcha']) ? "vscf-error" : "vscf-hide").'" >'.esc_attr($error_captcha_label).'</span></label>
			<input type="hidden" name="vscf_captcha_hidden" id="vscf_captcha_hidden" value="'.$vscf_rand.'" />
			<input type="text" name="vscf_captcha" id="vscf_captcha" '.(isset($error_class['form_captcha']) ? ' class="form-control vscf-error"' : ' class="form-control"').' value="'.esc_attr($form_data['form_captcha']).'" />
		</div>
		<div class="form-group vscf-hide">
			<input type="text" name="vscf_firstname" id="vscf_firstname" class="form-control" value="'.esc_attr($form_data['form_firstname']).'" />
		</div>
		<div class="form-group vscf-hide">
			<input type="text" name="vscf_lastname" id="vscf_lastname" class="form-control" value="'.esc_attr($form_data['form_lastname']).'" />
		</div>
		<div class="form-group vscf-message-group">
			<label for="vscf_message">'.esc_attr($message_label).': <span class="'.(isset($error_class['form_message']) ? "vscf-error" : "vscf-hide").'" >'.esc_attr($error_message_label).'</span></label>
			<textarea name="vscf_message" id="vscf_message" rows="10" '.(isset($error_class['form_message']) ? ' class="form-control vscf-error"' : ' class="form-control"').'>'.esc_textarea($form_data['form_message']).'</textarea>
		</div>
		'. (($privacy_setting == "yes") ? '
			<div class="form-group vscf-privacy-group">
				<input type="hidden" name="vscf_privacy" id="vscf_privacy_hidden" value="no" />
				<label><input type="checkbox" name="vscf_privacy" id="vscf_privacy" class="custom-control-input" value="yes" '.checked( esc_attr($form_data['form_privacy']), "yes", false ).' /> <span class="'.(isset($error_class['form_privacy']) ? "vscf-error" : "").'" >'.esc_attr($privacy_label).'</span></label>
			</div>
		' : '') .'
		<div class="form-group vscf-hide">
			'.$vscf_nonce_field.'
		</div>
		<div class="form-group vscf-submit-group">
			<button type="submit" name="'.$submit_name_id.'" id="'.$submit_name_id.'" class="btn btn-primary">'.esc_attr($submit_label).'</button>
		</div>
	</form>';
