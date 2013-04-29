<?php 
/**
 * Print the Google webfont loader into the <head> element.
 *
 * @package Graphene
 * @since Graphene 1.7.3
 */
function graphene_webfont_script(){
	global $graphene_settings;
	
	$families = array( 'Pontano+Sans::latin,latin-ext' );
	foreach ( explode( "\n", $graphene_settings['webfont_families'] ) as $family ){
		if ( $family ) $families[] = trim( $family );
	}
	$families = array_unique( $families );
	$families = join( "', '", $families );
	$families = apply_filters( 'graphene_webfont_families', $families );
	?>
	<script type="text/javascript">
	WebFontConfig = {
		google: { families: ['<?php echo $families; ?>'] }
	};
	(function() {
		var wf = document.createElement('script');
		wf.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		wf.type = 'text/javascript';
		wf.async = 'true';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(wf, s);
	})(); 
    </script>
    <?php
}
add_action( 'wp_head', 'graphene_webfont_script', 5 );
add_action( 'admin_head-appearance_page_custom-header', 'graphene_webfont_script' );
