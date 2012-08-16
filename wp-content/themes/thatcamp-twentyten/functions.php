<?php
/**
 * THATCamp Twenty Ten functions. Loads before the Twenty Ten functions.php file. 
 * @since Twenty Ten 1.0
 */

function jorbin_remove_twenty_ten_headers(){
	unregister_default_headers( array(
		'berries',
		'cherryblossom',
		'concave',
		'fern',
		'forestfloor',
		'inkwell',
		'path' ,
		'sunset')
	);
}

add_action( 'after_setup_theme', 'jorbin_remove_twenty_ten_headers', 11 );

define('thatcamp_twentyten_dir', get_bloginfo('stylesheet_directory'));
define( 'HEADER_IMAGE', thatcamp_twentyten_dir . '/images/headers/thatcamp-white.png' );

function thatcamp_twentyten_setup() {

           register_default_headers( array(
		'thatcamp-default' => array(
			'url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-white.png',
			'thumbnail_url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-white-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'THATCamp White', 'twentyten' )
//		),
//		'thatcamp-purple' => array(
//			'url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-purple.png',
//			'thumbnail_url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-purple-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Purple', 'twentyten' )
//		),
//		'thatcamp-green' => array(
//			'url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-green.png',
//			'thumbnail_url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-green-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Green', 'twentyten' )
//		),
//		'thatcamp-blue' => array(
//			'url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-blue.png',
//			'thumbnail_url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-blue-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Blue', 'twentyten' )
//		),
//		'thatcamp-red' => array(
//			'url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-red.png',
//			'thumbnail_url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-red-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Red', 'twentyten' )
//		),
//		'thatcamp-brown' => array(
//			'url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-brown.png',
//			'thumbnail_url' => thatcamp_twentyten_dir . '/images/headers/thatcamp-brown-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Brown', 'twentyten' )
		)		
	) );
}

add_action( 'after_setup_theme', 'thatcamp_twentyten_setup' );


?>
