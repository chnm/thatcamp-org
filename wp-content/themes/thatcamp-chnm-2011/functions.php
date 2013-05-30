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


/**
 * Always add our styles when using the proper theme
 *
 * Done inline to reduce overhead
 */
function thatcamp_add_styles_note() {
	//if ( bp_is_root_blog() ) {
	//	return;
	//}

	?>
<style type="text/css">
div.generic-button {
  margin: 1rem 0;
}
div.generic-button a {
  background: linear-gradient(#3C9CD2, #1F6EB6) repeat scroll 0 0 transparent;
  border: 1px solid #fff;
  opacity: 1;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  color: #fff;
  cursor: pointer;
  font-size: 0.9rem;
  outline: none;
  padding: 4px 10px;
  text-align: center;
  text-decoration: none;
  line-height: 14px;
  text-decoration: -1px -1px 0px #668800;
}
div.generic-button a:hover {
  opacity: 0.9;
}
div.generic-button.disabled-button {
  position: relative;
}
div.generic-button.disabled-button a {
  opacity: 0.7;
}
div.generic-button.disabled-button span {
  margin-left: -999em;
  position: absolute;
}
div.generic-button.disabled-button:hover span {
  border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
  box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
  position: absolute; left: 1em; top: 2em; z-index: 99;
  margin-left: 0;
  background: #2f2f2f; border: 1px solid #ccc;
  padding: 4px 8px;
  color: #fff;
  white-space: nowrap;
}
</style>
	<?php
}

remove_action( 'wp_head', 'thatcamp_add_styles' );
add_action( 'wp_head', 'thatcamp_add_styles_note' );


?>
