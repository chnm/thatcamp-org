<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// add admin options page
function vscf_menu_page() {
    add_options_page( __( 'VSCF', 'very-simple-contact-form' ), __( 'VSCF', 'very-simple-contact-form' ), 'manage_options', 'vscf', 'vscf_options_page' );
}
add_action( 'admin_menu', 'vscf_menu_page' );

// add admin settings and such 
function vscf_admin_init() {
	add_settings_section( 'vscf-section', __( 'General', 'very-simple-contact-form' ), '', 'vscf' );

	add_settings_field( 'vscf-field', __( 'Uninstall', 'very-simple-contact-form' ), 'vscf_field_callback', 'vscf', 'vscf-section' );
	register_setting( 'vscf-options', 'vscf-setting', 'esc_attr' );

	add_settings_field( 'vscf-field-2', __( 'Submissions', 'very-simple-contact-form' ), 'vscf_field_callback_2', 'vscf', 'vscf-section' );
	register_setting( 'vscf-options', 'vscf-setting-2', 'esc_attr' );

	add_settings_field( 'vscf-field-3', __( 'Reply', 'very-simple-contact-form' ), 'vscf_field_callback_3', 'vscf', 'vscf-section' );
	register_setting( 'vscf-options', 'vscf-setting-3', 'esc_attr' );

	add_settings_field( 'vscf-field-4', __( 'Privacy', 'very-simple-contact-form' ), 'vscf_field_callback_4', 'vscf', 'vscf-section' );
	register_setting( 'vscf-options', 'vscf-setting-4', 'esc_attr' );

	add_settings_field( 'vscf-field-19', __( 'Privacy', 'very-simple-contact-form' ), 'vscf_field_callback_19', 'vscf', 'vscf-section' );
	register_setting( 'vscf-options', 'vscf-setting-19', 'esc_attr' );

	add_settings_section( 'vscf-section-2', __( 'Labels', 'very-simple-contact-form' ), '', 'vscf' );

	add_settings_field( 'vscf-field-5', __( 'Name', 'very-simple-contact-form' ), 'vscf_field_callback_5', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-5', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-6', __( 'Email', 'very-simple-contact-form' ), 'vscf_field_callback_6', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-6', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-7', __( 'Subject', 'very-simple-contact-form' ), 'vscf_field_callback_7', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-7', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-8', __( 'Captcha', 'very-simple-contact-form' ), 'vscf_field_callback_8', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-8', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-9', __( 'Message', 'very-simple-contact-form' ), 'vscf_field_callback_9', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-9', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-18', __( 'Privacy', 'very-simple-contact-form' ), 'vscf_field_callback_18', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-18', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-10', __( 'Submit', 'very-simple-contact-form' ), 'vscf_field_callback_10', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-10', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-11', __( 'Error input field', 'very-simple-contact-form' ), 'vscf_field_callback_11', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-11', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-12', __( 'Error textarea', 'very-simple-contact-form' ), 'vscf_field_callback_12', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-12', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-13', __( 'Error email', 'very-simple-contact-form' ), 'vscf_field_callback_13', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-13', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-14', __( 'Error captcha', 'very-simple-contact-form' ), 'vscf_field_callback_14', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-14', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-15', __( 'Server error message', 'very-simple-contact-form' ), 'vscf_field_callback_15', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-15', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-16', __( 'Thank you message', 'very-simple-contact-form' ), 'vscf_field_callback_16', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-16', 'sanitize_text_field' );

	add_settings_field( 'vscf-field-17', __( 'Reply message', 'very-simple-contact-form' ), 'vscf_field_callback_17', 'vscf', 'vscf-section-2' );
	register_setting( 'vscf-options', 'vscf-setting-17', 'sanitize_text_field' );
}
add_action( 'admin_init', 'vscf_admin_init' );

function vscf_field_callback() {
	$value = esc_attr( get_option( 'vscf-setting' ) );
	?>
	<input type='hidden' name='vscf-setting' value='no'>
	<label><input type='checkbox' name='vscf-setting' <?php checked( $value, 'yes' ); ?> value='yes'> <?php _e( 'Do not delete form submissions and settings.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_2() {
	$value = esc_attr( get_option( 'vscf-setting-2' ) );
	?>
	<input type='hidden' name='vscf-setting-2' value='no'>
	<label><input type='checkbox' name='vscf-setting-2' <?php checked( $value, 'yes' ); ?> value='yes'> <?php _e( 'List form submissions in dashboard.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_3() {
	$value = esc_attr( get_option( 'vscf-setting-3' ) );
	?>
	<input type='hidden' name='vscf-setting-3' value='no'>
	<label><input type='checkbox' name='vscf-setting-3' <?php checked( $value, 'yes' ); ?> value='yes'> <?php _e( 'Activate confirmation email to sender.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_4() {
	$value = esc_attr( get_option( 'vscf-setting-4' ) );
	?>
	<input type='hidden' name='vscf-setting-4' value='no'>
	<label><input type='checkbox' name='vscf-setting-4' <?php checked( $value, 'yes' ); ?> value='yes'> <?php _e( 'Activate privacy checkbox on form.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_19() {
	$value = esc_attr( get_option( 'vscf-setting-19' ) );
	?>
	<input type='hidden' name='vscf-setting-19' value='no'>
	<label><input type='checkbox' name='vscf-setting-19' <?php checked( $value, 'yes' ); ?> value='yes'> <?php _e( 'Disable collection of IP address.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_5() {
	$vscf_placeholder = esc_attr__( 'Name', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-5' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-5' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_6() {
	$vscf_placeholder = esc_attr__( 'Email', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-6' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-6' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_7() {
	$vscf_placeholder = esc_attr__( 'Subject', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-7' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-7' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_8() {
	$vscf_placeholder = esc_attr__( 'Enter number %s', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-8' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-8' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_9() {
	$vscf_placeholder = esc_attr__( 'Message', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-9' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-9' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_18() {
	$vscf_placeholder = esc_attr__( 'I consent to having this website collect my personal data via this form.', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-18' ) );
	echo "<input type='text' size='40' maxlength='200' name='vscf-setting-18' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_10() {
	$vscf_placeholder = esc_attr__( 'Submit', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-10' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-10' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_11() {
	$vscf_placeholder = esc_attr__( 'Please enter at least 2 characters', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-11' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-11' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_12() {
	$vscf_placeholder = esc_attr__( 'Please enter at least 10 characters', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-12' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-12' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_13() {
	$vscf_placeholder = esc_attr__( 'Please enter a valid email', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-13' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-13' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_14() {
	$vscf_placeholder = esc_attr__( 'Please enter the correct number', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-14' ) );
	echo "<input type='text' size='40' maxlength='50' name='vscf-setting-14' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_15() {
	$vscf_placeholder = esc_attr__( 'Error! Could not send form. This might be a server issue.', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-15' ) );
	echo "<input type='text' size='40' maxlength='200' name='vscf-setting-15' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_16() {
	$vscf_placeholder = esc_attr__( 'Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-16' ) );
	echo "<input type='text' size='40' maxlength='200' name='vscf-setting-16' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

function vscf_field_callback_17() {
	$vscf_placeholder = esc_attr__( 'Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form' ); 
	$vscf_setting = esc_attr( get_option( 'vscf-setting-17' ) );
	echo "<input type='text' size='40' maxlength='200' name='vscf-setting-17' placeholder='$vscf_placeholder' value='$vscf_setting' />";
}

// display admin options page
function vscf_options_page() {
?>
<div class="wrap"> 
	<div id="icon-plugins" class="icon32"></div> 
	<h1><?php _e( 'Very Simple Contact Form', 'very-simple-contact-form' ); ?></h1> 
	<form action="options.php" method="POST">
	<?php settings_fields( 'vscf-options' ); ?>
	<?php do_settings_sections( 'vscf' ); ?>
	<?php submit_button(); ?>
	</form>
	<p><?php _e( 'More customizations can be made using (shortcode) attributes.', 'very-simple-contact-form' ); ?></p>
	<p><?php _e( 'Info about attributes', 'very-simple-contact-form' ); ?>: <a href="https://wordpress.org/plugins/very-simple-contact-form" target="_blank"><?php _e( 'click here', 'very-simple-contact-form' ); ?></a></p>
</div>
<?php
}
