<?php  

add_filter( 'twentyeleven_color_schemes', 'twentyeleven_color_schemes_green' );
add_action( 'twentyeleven_enqueue_color_scheme', 'twentyeleven_enqueue_color_scheme_green' );

function twentyeleven_color_schemes_green( $color_schemes ) {
	$color_schemes['green'] = array(
		'value' => 'green',
		'label' => __( 'green', 'twentyeleven' ),
		'thumbnail' => get_stylesheet_directory_uri() . 'images/green.png',
		'default_link_color' => '#a1a82e',
	);
	return $color_schemes;
}

function twentyeleven_enqueue_color_scheme_green( $color_scheme ) {
	if ( 'green' == $color_scheme )
		wp_enqueue_style( 'green', get_stylesheet_directory_uri() . '/green.css', array(), null );
}



?>