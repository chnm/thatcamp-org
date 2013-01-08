<?php
/**
 * Register translatable strings from the theme
 */
function graphene_wpml_register_strings( $string = NULL ){
	if ( ! function_exists( 'icl_register_string' ) ) return;
	
	if ( is_array( $string ) ) {
		if ( ! array_key_exists( 'context', $string ) ) $string['context'] = 'Graphene theme';
		$graphene_t_strings[] = $string;
	} else {
		global $graphene_t_strings;
	}
	if ( ! is_array( $graphene_t_strings ) ) return;
	
	foreach ( $graphene_t_strings as $string ) {
		icl_register_string( $string['context'], $string['name'], $string['value'] );
	}
}


/**
 * Add translatable strings to the $graphene_t_strings array
 *
 * @param 	array|string $strings can be an array of strings (when adding multiple strings at once), or a single string value.
 *			If it is an array, it is expected to be in the following structure: 
 *			array( 'context' => '', 'name' => '', 'value' => '' )
 * @param	string $name The name of the string to help identify the string, e.g. copyright text
 * @param	string $context The context of the string to help identify the string's origin, e.g. Graphene theme
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_add_t_string( $strings, $name = '', $context = 'Graphene theme' ){
	global $graphene_t_strings;
	
	if ( is_array( $strings ) ) {
		foreach ( $strings as $string ) {
			if ( ! ( $string['value'] && $string['name'] ) ) continue;
			if ( ! $string['context'] ) $string['context'] = 'Graphene theme';
			$graphene_t_strings[] =  $string;
		}
	} else {
		$string = array(
					'value' 	=> $strings,
					'name'		=> $name,
					'context'	=> $context
				);
		$graphene_t_strings[] =  $string;
	}
}


/**
 * Get the translated string
 *
 * @param string $value 	The default value that will be returned if string hasn't been translated
 * @param string $name		The name of the string
 * @param string $context	The context of the string
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_icl_t( $name, $value = '', $context = 'Graphene theme' ){
	if ( ! function_exists( 'icl_t' ) ) return $value;
	else return icl_t( $context, $name, $value );
}


/**
 * Registers the translatable options
 */
function graphene_register_t_options(){
	if ( ! function_exists( 'icl_t' ) ) return;
	global $graphene_settings;
	
	$options = array( 
					array( 'name' => 'Copyright text', 'value' => $graphene_settings['copy_text'], 'context' => '' ) ,
					array( 'name' => 'Home nav menu description', 'value' => $graphene_settings['navmenu_home_desc'], 'context' => '' ) 
				);
	foreach ( $graphene_settings['social_profiles'] as $social_profile ) {
		$options[] = array( 'name' => 'Social icon - ' . $social_profile['name'], 'value' => wp_kses_decode_entities( $social_profile['title'] ), 'context' => '' );
	}
	
	graphene_add_t_string( $options );
}


/**
 * Replace the strings in the theme's settings with the translated strings
 */
function graphene_translate_settings(){
	if ( ! function_exists( 'icl_t' ) ) return;
	if ( is_admin() ) return;
	
	global $graphene_settings;
	$graphene_settings['copy_text'] = graphene_icl_t( 'Copyright text', $graphene_settings['copy_text'] );
	$graphene_settings['navmenu_home_desc'] = graphene_icl_t( 'Home nav menu description', $graphene_settings['navmenu_home_desc'] );
	
	foreach ( $graphene_settings['social_profiles'] as $key => $social_profile ) {
		$graphene_settings['social_profiles'][$key]['title'] = graphene_icl_t( 'Social icon - ' . $social_profile['name'], wp_kses_decode_entities( $social_profile['title'] ) );
	}
}
add_action( 'template_redirect', 'graphene_translate_settings' );