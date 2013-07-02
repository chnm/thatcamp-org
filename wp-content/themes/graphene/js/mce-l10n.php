<?php
/**
 * Generates the javascript for translatable TinyMCE editor buttons
 *
 * @package Graphene
 * @since 1.9
 */
function graphene_mce_translation(){
	
	$buttons = array(
		'warning_title'		=> __( 'Add a warning message block', 'graphene' ),
		'error_title'		=> __( 'Add an error message block', 'graphene' ),
		'notice_title'		=> __( 'Add a notice message block', 'graphene' ),
		'important_title'	=> __( 'Add an important message block', 'graphene' ),
		'pullquote'			=> __( 'Add a pullquote', 'graphene' ),
	);	
	
	$locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.graphenemcebuttons",' . json_encode( $buttons ) . ");\n";
	
	return $translated;
}
$strings = graphene_mce_translation();