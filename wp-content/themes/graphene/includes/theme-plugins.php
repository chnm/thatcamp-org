<?php 
/**
 * Add breadcrumbs to the top of the content area. Uses the Breadcrumb NavXT plugin
*/
if ( function_exists( 'bcn_display' ) ) :
	function graphene_breadcrumb_navxt(){
		echo '<div class="breadcrumb breadcrumb-navxt">';
		bcn_display();
		echo '</div>';
	}
	add_action( 'graphene_top_content', 'graphene_breadcrumb_navxt' );
endif;


/**
 * Add 'nodate' class for bbPress user home
*/
if ( class_exists( 'bbPress' ) ) :
	function graphene_bbpress_post_class( $classes ){
		if ( bbp_is_user_home() )
			$classes[] = 'nodate';
			
		return $classes;
	}
	add_filter( 'post_class', 'graphene_bbpress_post_class' );
endif;


/* WP e-Commerce compat stuffs */
if ( function_exists( 'is_products_page' ) ) :

/**
 * Disable child page listing for Products page
 */
function graphene_wpsc_disable_child(){
	if ( ! is_products_page() ) return;
	global $graphene_settings;
	$graphene_settings['child_page_listing'] = 'hide';
}
add_action( 'wp_head', 'graphene_wpsc_disable_child' );
 
endif;