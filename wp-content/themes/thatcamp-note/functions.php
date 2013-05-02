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

function thatcampbase_custom_header_setup() {
	$args = array(
		// Text color and image (empty to use none).
		'default-text-color'     => '444',
		'default-image'          => '',

		// Set height and width, with a maximum value for the width.
		'height'                 => 300,
		'width'                  => 1240,
		'max-width'              => 2000,

		// Support flexible height and width.
		'flex-height'            => true,
		'flex-width'             => true,

		// Random image rotation off by default.
		'random-default'         => false,

		// Callbacks for styling the header and the admin preview.
		'wp-head-callback'       => 'thatcampbase_header_style',
		'admin-head-callback'    => 'thatcampbase_admin_header_style',
		'admin-preview-callback' => 'thatcampbase_admin_header_image',
	);

	add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'thatcampbase_custom_header_setup' );
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
?>