<?php
/**
 * Functions and definitions for bookcamp
 *
 * @package bookcamp
 * @since bookcamp 1.0
 */
/* bookcamp setup functions */
if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

add_filter( 'show_admin_bar', '__return_false' );

/**
 * Google fonts
 *
 * Uses Oswald Google Font
 *
 * @since bookcamp (1.0)
 */
function bookcamp_load_fonts() {
    wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Courgette');
    wp_enqueue_style( 'googleFonts');
}
add_action('wp_enqueue_scripts', 'bookcamp_load_fonts');

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

add_action( 'after_setup_theme', 'thatcamp_header_setup' );
endif;
?>