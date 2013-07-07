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
define( 'HEADER_IMAGE', thatcamp_twentyten_dir . '/images/thatcamp-twentyten-header.png' );

function thatcamp_twentyten_setup() {

           register_default_headers( array(
		'thatcamp-default' => array(
			'url' => thatcamp_twentyten_dir . '/images/thatcamp-twentyten-header.png',
			'thumbnail_url' => thatcamp_twentyten_dir . '/images/thatcamp-twentyten-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'THATCamp TwentyTen Default Header', 'twentyten' )
		)		
	) );
}

add_action( 'after_setup_theme', 'thatcamp_twentyten_setup' );

?>
