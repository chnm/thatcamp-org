<?php
/**
 * Set the settings for options presets
*/
$authorised = true;
if (isset($_POST['graphene-preset']) ){ 
	if ( ! wp_verify_nonce( $_POST['graphene-preset'], 'graphene-preset' ) ) {$authorised = false;}
	if ( ! current_user_can( 'edit_theme_options' ) ){$authorised = false;}
} else {
	$authorised = false;
}

if ( $authorised ) {
	global $graphene_settings, $graphene_defaults;
			
	if ( $_POST['graphene_options_preset'] == 'reset' ) {
		delete_option( 'graphene_settings' );
		add_settings_error( 'graphene_options', 2, __( 'Settings have been reset.', 'graphene' ), 'updated' );
	}
	
	// Update the global settings variable
	$graphene_settings = array_merge( $graphene_defaults, get_option( 'graphene_settings', array() ) );

} else {
	wp_die( __( 'ERROR: You are not authorised to perform that operation', 'graphene' ) );
}