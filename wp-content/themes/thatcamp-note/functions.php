<?php
/**
 * Functions and definitions for notecamp
 *
 * @package notecamp
 * @since notecamp 1.0
 */
/* notecamp setup functions */
if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

add_filter( 'show_admin_bar', '__return_false' );

if ( ! function_exists( 'thatcampbase_header_setup' ) ) :
	function thatcamp_header_setup() {

           register_default_headers( array(
		'thatcamp-default' => array(
			'url' => style_dir . '/assets/images/deafult-header.png',
			'thumbnail_url' => thatcamp_style_dir . '/assets/images/default-header-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'THATCamp Header', 'thatcamp-base' )
		)		
	) );
}
endif;
?>