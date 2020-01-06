<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// add admin options page
function vscf_menu_page() {
    add_options_page( esc_attr__( 'VSCF', 'very-simple-contact-form' ), esc_attr__( 'VSCF', 'very-simple-contact-form' ), 'manage_options', 'vscf', 'vscf_options_page' );
}
add_action( 'admin_menu', 'vscf_menu_page' );

// add admin settings and such
function vscf_admin_init() {
	add_settings_section( 'vscf-general-section', esc_attr__( 'General', 'very-simple-contact-form' ), '', 'vscf-general' );

	add_settings_field( 'vscf-field-22', esc_attr__( 'Email', 'very-simple-contact-form' ), 'vscf_field_callback_22', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting-22', array('sanitize_callback' => 'sanitize_email') );

	add_settings_field( 'vscf-field-1', esc_attr__( 'Uninstall', 'very-simple-contact-form' ), 'vscf_field_callback_1', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting', array('sanitize_callback' => 'sanitize_key') );

	add_settings_field( 'vscf-field-2', esc_attr__( 'Submissions', 'very-simple-contact-form' ), 'vscf_field_callback_2', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting-2', array('sanitize_callback' => 'sanitize_key') );
	
	add_settings_field( 'vscf-field-23', esc_attr__( 'Subject', 'very-simple-contact-form' ), 'vscf_field_callback_23', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting-23', array('sanitize_callback' => 'sanitize_key') );

	add_settings_field( 'vscf-field-3', esc_attr__( 'Reply', 'very-simple-contact-form' ), 'vscf_field_callback_3', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting-3', array('sanitize_callback' => 'sanitize_key') );

	add_settings_field( 'vscf-field-4', esc_attr__( 'Privacy', 'very-simple-contact-form' ), 'vscf_field_callback_4', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting-4', array('sanitize_callback' => 'sanitize_key') );

	add_settings_field( 'vscf-field-19', esc_attr__( 'Privacy', 'very-simple-contact-form' ), 'vscf_field_callback_19', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting-19', array('sanitize_callback' => 'sanitize_key') );

	add_settings_field( 'vscf-field-21', esc_attr__( 'Anchor', 'very-simple-contact-form' ), 'vscf_field_callback_21', 'vscf-general', 'vscf-general-section' );
	register_setting( 'vscf-general-options', 'vscf-setting-21', array('sanitize_callback' => 'sanitize_key') );

	add_settings_section( 'vscf-label-section', esc_attr__( 'Labels', 'very-simple-contact-form' ), '', 'vscf-label' );

	add_settings_field( 'vscf-field-5', esc_attr__( 'Name', 'very-simple-contact-form' ), 'vscf_field_callback_5', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-5', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-6', esc_attr__( 'Email', 'very-simple-contact-form' ), 'vscf_field_callback_6', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-6', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-7', esc_attr__( 'Subject', 'very-simple-contact-form' ), 'vscf_field_callback_7', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-7', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-8', esc_attr__( 'Captcha', 'very-simple-contact-form' ), 'vscf_field_callback_8', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-8', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-9', esc_attr__( 'Message', 'very-simple-contact-form' ), 'vscf_field_callback_9', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-9', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-18', esc_attr__( 'Privacy', 'very-simple-contact-form' ), 'vscf_field_callback_18', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-18', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-10', esc_attr__( 'Submit', 'very-simple-contact-form' ), 'vscf_field_callback_10', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-10', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-11', esc_attr__( 'Name error', 'very-simple-contact-form' ), 'vscf_field_callback_11', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-11', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-13', esc_attr__( 'Email error', 'very-simple-contact-form' ), 'vscf_field_callback_13', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-13', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-20', esc_attr__( 'Subject error', 'very-simple-contact-form' ), 'vscf_field_callback_20', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-20', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-14', esc_attr__( 'Captcha error', 'very-simple-contact-form' ), 'vscf_field_callback_14', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-14', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-12', esc_attr__( 'Message error', 'very-simple-contact-form' ), 'vscf_field_callback_12', 'vscf-label', 'vscf-label-section' );
	register_setting( 'vscf-label-options', 'vscf-setting-12', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_section( 'vscf-message-section', esc_attr__( 'Messages', 'very-simple-contact-form' ), '', 'vscf-message' );

	add_settings_field( 'vscf-field-15', esc_attr__( 'Server error message', 'very-simple-contact-form' ), 'vscf_field_callback_15', 'vscf-message', 'vscf-message-section' );
	register_setting( 'vscf-message-options', 'vscf-setting-15', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-16', esc_attr__( 'Thank you message', 'very-simple-contact-form' ), 'vscf_field_callback_16', 'vscf-message', 'vscf-message-section' );
	register_setting( 'vscf-message-options', 'vscf-setting-16', array('sanitize_callback' => 'sanitize_text_field') );

	add_settings_field( 'vscf-field-17', esc_attr__( 'Reply message', 'very-simple-contact-form' ), 'vscf_field_callback_17', 'vscf-message', 'vscf-message-section' );
	register_setting( 'vscf-message-options', 'vscf-setting-17', array('sanitize_callback' => 'sanitize_text_field') );
}
add_action( 'admin_init', 'vscf_admin_init' );

function vscf_field_callback_22() {
	$placeholder = esc_attr( get_option( 'admin_email' ) );
	$value = esc_attr( get_option( 'vscf-setting-22' ) );
	?>
	<input type='text' size='40' name='vscf-setting-22' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_1() {
	$value = esc_attr( get_option( 'vscf-setting' ) );
	?>
	<input type='hidden' name='vscf-setting' value='no'>
	<label><input type='checkbox' name='vscf-setting' <?php checked( $value, 'yes' ); ?> value='yes'> <?php esc_attr_e( 'Do not delete form submissions and settings.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_2() {
	$value = esc_attr( get_option( 'vscf-setting-2' ) );
	?>
	<input type='hidden' name='vscf-setting-2' value='no'>
	<label><input type='checkbox' name='vscf-setting-2' <?php checked( $value, 'yes' ); ?> value='yes'> <?php esc_attr_e( 'List form submissions in dashboard.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_23() {
	$value = esc_attr( get_option( 'vscf-setting-23' ) );
	?>
	<input type='hidden' name='vscf-setting-23' value='no'>
	<label><input type='checkbox' name='vscf-setting-23' <?php checked( $value, 'yes' ); ?> value='yes'> <?php esc_attr_e( 'Disable subject field.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_3() {
	$value = esc_attr( get_option( 'vscf-setting-3' ) );
	?>
	<input type='hidden' name='vscf-setting-3' value='no'>
	<label><input type='checkbox' name='vscf-setting-3' <?php checked( $value, 'yes' ); ?> value='yes'> <?php esc_attr_e( 'Activate confirmation email to sender.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_4() {
	$value = esc_attr( get_option( 'vscf-setting-4' ) );
	?>
	<input type='hidden' name='vscf-setting-4' value='no'>
	<label><input type='checkbox' name='vscf-setting-4' <?php checked( $value, 'yes' ); ?> value='yes'> <?php esc_attr_e( 'Activate privacy consent checkbox on form.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_19() {
	$value = esc_attr( get_option( 'vscf-setting-19' ) );
	?>
	<input type='hidden' name='vscf-setting-19' value='no'>
	<label><input type='checkbox' name='vscf-setting-19' <?php checked( $value, 'yes' ); ?> value='yes'> <?php esc_attr_e( 'Disable collection of IP address.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_21() {
	$value = esc_attr( get_option( 'vscf-setting-21' ) );
	?>
	<input type='hidden' name='vscf-setting-21' value='no'>
	<label><input type='checkbox' name='vscf-setting-21' <?php checked( $value, 'yes' ); ?> value='yes'> <?php esc_attr_e( 'Scroll back to form position after submit.', 'very-simple-contact-form' ); ?></label>
	<?php
}

function vscf_field_callback_5() {
	$placeholder = esc_attr__( 'Name', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-5' ) );
	?>
	<input type='text' size='40' name='vscf-setting-5' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_6() {
	$placeholder = esc_attr__( 'Email', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-6' ) );
	?>
	<input type='text' size='40' name='vscf-setting-6' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_7() {
	$placeholder = esc_attr__( 'Subject', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-7' ) );
	?>
	<input type='text' size='40' name='vscf-setting-7' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_8() {
	$placeholder = esc_attr__( 'Enter number %s', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-8' ) );
	?>
	<input type='text' size='40' name='vscf-setting-8' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_9() {
	$placeholder = esc_attr__( 'Message', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-9' ) );
	?>
	<input type='text' size='40' name='vscf-setting-9' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_18() {
	$placeholder = esc_attr__( 'I consent to having this website collect my personal data via this form.', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-18' ) );
	?>
	<input type='text' size='40' name='vscf-setting-18' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_10() {
	$placeholder = esc_attr__( 'Submit', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-10' ) );
	?>
	<input type='text' size='40' name='vscf-setting-10' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_11() {
	$placeholder = esc_attr__( 'Please enter at least 2 characters', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-11' ) );
	?>
	<input type='text' size='40' name='vscf-setting-11' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_13() {
	$placeholder = esc_attr__( 'Please enter a valid email', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-13' ) );
	?>
	<input type='text' size='40' name='vscf-setting-13' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_20() {
	$placeholder = esc_attr__( 'Please enter at least 2 characters', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-20' ) );
	?>
	<input type='text' size='40' name='vscf-setting-20' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_14() {
	$placeholder = esc_attr__( 'Please enter the correct number', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-14' ) );
	?>
	<input type='text' size='40' name='vscf-setting-14' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_12() {
	$placeholder = esc_attr__( 'Please enter at least 10 characters', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-12' ) );
	?>
	<input type='text' size='40' name='vscf-setting-12' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_15() {
	$placeholder = esc_attr__( 'Error! Could not send form. This might be a server issue.', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-15' ) );
	?>
	<input type='text' size='40' name='vscf-setting-15' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_16() {
	$placeholder = esc_attr__( 'Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-16' ) );
	?>
	<input type='text' size='40' name='vscf-setting-16' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<?php
}

function vscf_field_callback_17() {
	$placeholder = esc_attr__( 'Thank you! You will receive a response as soon as possible.', 'very-simple-contact-form' );
	$value = esc_attr( get_option( 'vscf-setting-17' ) );
	?>
	<input type='text' size='40' name='vscf-setting-17' placeholder='<?php echo $placeholder; ?>' value='<?php echo $value; ?>' />
	<p><i><?php esc_attr_e( 'Displayed in the confirmation email to sender.', 'very-simple-contact-form' ); ?></i></p>
	<?php
}

// display admin options page
function vscf_options_page() {
?>
<div class="wrap">
	<h1><?php esc_attr_e( 'Very Simple Contact Form', 'very-simple-contact-form' ); ?></h1>
	<?php
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general_options';
	?>
	<h2 class="nav-tab-wrapper">
		<a href="?page=vscf&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'General', 'very-simple-contact-form' ); ?></a>
		<a href="?page=vscf&tab=label_options" class="nav-tab <?php echo $active_tab == 'label_options' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Labels', 'very-simple-contact-form' ); ?></a>
		<a href="?page=vscf&tab=message_options" class="nav-tab <?php echo $active_tab == 'message_options' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Messages', 'very-simple-contact-form' ); ?></a>
	</h2>
	<form action="options.php" method="POST">
		<?php if( $active_tab == 'general_options' ) { ?>
			<?php settings_fields( 'vscf-general-options' ); ?>
			<?php do_settings_sections( 'vscf-general' ); ?>
		<?php } elseif( $active_tab == 'label_options' ) { ?>
			<?php settings_fields( 'vscf-label-options' ); ?>
			<?php do_settings_sections( 'vscf-label' ); ?>
		<?php } else { ?>
			<?php settings_fields( 'vscf-message-options' ); ?>
			<?php do_settings_sections( 'vscf-message' ); ?>
		<?php } ?>
		<?php submit_button(); ?>
	</form>
	<p><?php esc_attr_e( 'More customizations can be made by using (shortcode) attributes.', 'very-simple-contact-form' ); ?></p>
	<?php $link_label = __( 'click here', 'very-simple-contact-form' ); ?>
	<?php $link_wp = '<a href="https://wordpress.org/plugins/very-simple-contact-form" target="_blank">'.$link_label.'</a>'; ?>
	<p><?php printf( esc_attr__( 'For info, available attributes and support %s.', 'very-simple-contact-form' ), $link_wp ); ?></p>
</div>
<?php
}
