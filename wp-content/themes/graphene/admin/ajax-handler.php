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


/**
 * Process the AJAX call to save/delete colour preset
 */
function graphene_ajax_update_preset_handler() {
	global $wpdb;
	check_ajax_referer( 'graphene_options-options', '_wpnonce' );
	
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		echo '<div class="error fade"><p>' . __( "Sorry, but you don't have the necessary permission to modify theme options.", 'graphene' ) . '</p></div>';
		die();
	}
	
	$action = $_POST['colour_preset_action'];
	$preset = ( $action == 'delete' ) ? trim( $_POST['colour_preset_name'] ) : sanitize_title( $_POST['colour_preset_name'] );
	
	$data = $_POST['graphene_settings'];
	if ( $action == 'delete' ) unset( $data['colour_presets'][$preset] );
	
	if ( $action == 'save' ) {
		
		$count = $dash = '';
		while ( array_key_exists( $preset . $dash . $count, $data['colour_presets'] ) ) {
			if ( ! $count ) { $count = 0; $dash = '-'; } 
			$count++;
		}
		if ( $count ) $preset .= '-' . $count;
		
		$colour_settings = $data;
		unset( $colour_settings['colour_preset'] ); unset( $colour_settings['colour_presets'] );
		$colour_settings = json_encode( $colour_settings );
		$data['colour_presets'][$preset]['name'] = trim( $_POST['colour_preset_name'] );
		$data['colour_presets'][$preset]['code'] = $colour_settings;
		$data['colour_preset'] = $preset;
	}
	
	$data = graphene_settings_validator( $data );
	
	if ( get_settings_errors( 'graphene_options' ) ){
		settings_errors( 'graphene_options' );
	} else {
		if ( $data ) update_option( 'graphene_settings', stripslashes_deep( $data ) );
		$message = ( $action == 'delete' ) ? __( 'Colour preset deleted.', 'graphene' ) : __( 'Colour preset saved.', 'graphene' );
		echo '<div class="updated fade"><p>' . $message . '</p></div>';
	}
		
	die();
}
add_action('wp_ajax_graphene_ajax_update_preset', 'graphene_ajax_update_preset_handler');