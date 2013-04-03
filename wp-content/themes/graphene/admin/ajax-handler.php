<?php
/**
 * Process the AJAX call to save the theme's settings
 */
function graphene_ajax_update_handler() {
	global $wpdb;
	check_ajax_referer( 'graphene_options-options', '_wpnonce' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		echo '<div class="error fade"><p>' . __( "Sorry, but you don't have the necessary permission to modify theme options.", 'graphene' ) . '</p></div>';
		die();
	}
	
	$data = $_POST['graphene_settings'];
	$data = graphene_settings_validator( $data );
	
	if ( get_settings_errors( 'graphene_options' ) ){
		settings_errors( 'graphene_options' );
	} else {
		if ( $data ) update_option( 'graphene_settings', stripslashes_deep( $data ) );
		echo '<div class="updated fade"><p>' . __( 'Options saved.', 'graphene' ) . '</p></div>';
	}
		
	die();
}
add_action('wp_ajax_graphene_ajax_update', 'graphene_ajax_update_handler');