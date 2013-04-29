<?php
/**
 * Functions and definitions for thatcampbase
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
/* thatcampbase setup functions */
if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

add_filter( 'show_admin_bar', '__return_false' );

// thatcampbase set up function
add_action( 'after_setup_theme', 'thatcampbase_build' );
if ( ! function_exists( 'thatcampbase_build' ) ) :
function thatcampbase_build() {
	
	// adds files
	require( get_template_directory() . '/functions/template-functions.php' );

	// Language set up
	load_theme_textdomain('thatcampbase', get_template_directory() . '/languages/');

	// Add RSS feed links
	add_theme_support('automatic-feed-links');

	// Enable support for post thumbnails
	add_theme_support('post-thumbnails'); 
	set_post_thumbnail_size( 624, 9999 );

	// Register navigation menus
	register_nav_menu('top', __('Top', 'thatcampbase'));
	register_nav_menu('bottom', __('Bottom', 'thatcampbase'));
	
	// Add post format support
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote' ) );
}
endif;


require( get_template_directory() . '/functions/custom-header.php' );

// thatcampbase load scripts
if ( ! function_exists( 'thatcampbase_load_scripts' ) ) :
function thatcampbase_load_scripts() {

	wp_enqueue_style( 'normalise',  get_template_directory_uri() . '/assets/css/normalise.css', array());
	
	/* bare bones print styles */
	wp_enqueue_style( 'print',  get_template_directory_uri() . '/assets/css/print.css', array());
	
	wp_enqueue_style( 'style', get_stylesheet_uri() );

	/* font awesome is rolled into Logical Bones */
	wp_enqueue_style( 'font-awesome',  get_template_directory_uri() . '/assets/css/font-awesome.css', array(), '2.0');
	
	wp_enqueue_script('modernizr', get_template_directory_uri() . '/assets/scripts/modernizr-2.6.2-min.js', array("jquery"), '2.0');
	
	wp_enqueue_script('custom', get_template_directory_uri() . '/assets/scripts/custom-scripts.js', array("jquery"), '1.0');
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'thatcampbase_load_scripts' );

if ( ! function_exists( 'thatcampbase_load_background' ) ) :
function thatcampbase_load_background() {
    add_theme_support( 'custom-background', array(
	) );
}
add_action('after_setup_theme', 'thatcampbase_load_background');
endif;
/**
 * Google fonts
 *
 * Uses Oswald Google Font
 *
 * @since thatcampbase (1.0)
 */

if ( ! function_exists( 'thatcampbase_load_fonts' ) ) :
function thatcampbase_load_fonts() {
    wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Oswald:400,700,300');
    wp_enqueue_style( 'googleFonts');
}
add_action('wp_enqueue_scripts', 'thatcampbase_load_fonts');
endif;

// thatcampbase widgets
if ( ! function_exists( 'thatcampbase_widgets_init' ) ) :
function thatcampbase_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'thatcampbasebase'),
			'id'            => 'sidebar',
			'description'   => 'Sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">', 	  
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);
}
endif;
add_action( 'widgets_init', 'thatcampbase_widgets_init' );
?>