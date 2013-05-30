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
  background: #A1A82E;
  border: 1px solid #A1A82E;
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