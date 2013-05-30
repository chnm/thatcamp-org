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
	function thatcampbase_header_setup() {

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



/**
 * Always add our styles when using the proper theme
 *
 * Done inline to reduce overhead
 */
function thatcamp_add_styles_book() {
	//if ( bp_is_root_blog() ) {
	//	return;
	//}

	?>
<style type="text/css">
div.generic-button {
  margin-bottom: 1rem;
}
div.generic-button a {
  background: #118899;
  border: 1px solid #118899;
  font-family: 'Courgette',cursive;
  opacity: 1;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  color: #ffffff;
  cursor: pointer;
  font-size: 1.1rem;
  outline: none;
  padding: 4px 10px;
  text-align: center;
  text-decoration: none;
  line-height: 14px;
  text-decoration: -1px -1px 0px #668800;
  margin-left: 5px;
}
div.generic-button a:hover {
  opacity: 0.9;
}
div.generic-button.disabled-button {
  position: relative;
}
div.generic-button.disabled-button a {
  opacity: 0.9;
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
  background: #FFFFAA; border: 1px solid #FFAD33;
  padding: 4px 8px;
  white-space: nowrap;
}
</style>
	<?php
}
remove_action( 'wp_head', 'thatcamp_add_styles' );
add_action( 'wp_head', 'thatcamp_add_styles_book' );