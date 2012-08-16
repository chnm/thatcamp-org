<?php
/**
 * THATCamp Graphene functions. Loads before the Graphene functions.php file. 
 */

function thatcamp_remove_graphene_headers(){
	unregister_default_headers( array(
		'Schematic', 
		'Flow', 
		'Fluid', 
		'Techno', 
		'Fireworks', 
		'Nebula', 
		'Sparkle' )
	);
}

add_action( 'after_setup_theme', 'thatcamp_remove_graphene_headers', 11);

define('thatcamp_graphene_dir', get_bloginfo('stylesheet_directory'));
define( 'HEADER_IMAGE', thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-blue.png' );

function thatcamp_graphene_setup() {

           register_default_headers( array(
		'thatcamp-default' => array(
			'url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-blue.png',
			'thumbnail_url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-blue-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'THATCamp Blue', 'graphene' )
//		) ,
//		'thatcamp-graphene-purple' => array(
//			'url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-purple.png',
//			'thumbnail_url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-purple-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Purple', 'graphene' )
//		),
//		'thatcamp-graphene-green' => array(
//			'url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-green.png',
//			'thumbnail_url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-green-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Green', 'graphene' )
//		),
//		'thatcamp-graphene-white' => array(
//			'url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-white.png',
//			'thumbnail_url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-white-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp White', 'graphene' )
//		),
//		'thatcamp-graphene-red' => array(
//			'url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-red.png',
//			'thumbnail_url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-red-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Red', 'graphene' )
//		),
//		'thatcamp-graphene-brown' => array(
//			'url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-brown.png',
//			'thumbnail_url' => thatcamp_graphene_dir . '/images/headers/thatcamp-graphene-brown-thumbnail.png',
//			/* translators: header image description */
//			'description' => __( 'THATCamp Brown', 'graphene' )
		)		
	) );
}

add_action( 'after_setup_theme', 'thatcamp_graphene_setup' );

?>
