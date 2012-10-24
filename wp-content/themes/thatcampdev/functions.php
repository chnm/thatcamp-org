<?php
/**
 * Functions and definitions for thatcamp
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
/* thatcamp setup functions */
if ( ! function_exists( 'bp_is_active' ) ) {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	return;
}

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

add_filter('show_admin_bar', '__return_false'); 

// thatcamp set up function
add_action( 'after_setup_theme', 'thatcamp_build' );
if ( ! function_exists( 'thatcamp_build' ) ) :
function thatcamp_build() {
	
	// incs incoming
	require( get_template_directory() . '/assets/scripts/ajax.php' );

	// template contents and structure functions
	require( get_template_directory() . '/functions/template-functions.php' );

	// Language set up
	load_theme_textdomain('thatcamp', get_template_directory() . '/languages/');
	
	add_theme_support( 'thatcamp' );

	// Add RSS feed links
	add_theme_support('automatic-feed-links');

	// If having editor style add this
	add_editor_style();

	// Enable support for post thumbnails
	add_theme_support('post-thumbnails'); 
	
	// Register navigation menus
	register_nav_menu('top', __('Top', 'thatcamp'));
	register_nav_menu('bottom', __('Bottom', 'thatcamp'));
	register_nav_menu('documents', __('Documents', 'thatcamp'));
	
	// Add post format support
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote' ) );

	if ( !is_admin() ) {
		// Register buttons for the relevant component templates
		// Friends button
		if ( bp_is_active( 'friends' ) )
			add_action( 'bp_member_header_actions',    'bp_add_friend_button',           5 );

		// Activity button
		if ( bp_is_active( 'activity' ) )
			add_action( 'bp_member_header_actions',    'bp_send_public_message_button',  20 );

		// Messages button
		if ( bp_is_active( 'messages' ) )
			add_action( 'bp_member_header_actions',    'bp_send_private_message_button', 20 );

		// Group buttons
		if ( bp_is_active( 'groups' ) ) {
			add_action( 'bp_group_header_actions',     'bp_group_join_button',           5 );
			add_action( 'bp_group_header_actions',     'bp_group_new_topic_button',      20 );
			add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
		}

		// Blog button
		if ( bp_is_active( 'blogs' ) )
			add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
	}
	
	require( get_template_directory() . '/functions/buddypress-functions.php' );
}
endif;

// thatcamp load scripts
if ( ! function_exists( 'thatcamp_load_scripts' ) ) :
function thatcamp_load_scripts() {

	wp_enqueue_style( 'normalise',  get_template_directory_uri() . '/assets/css/normalise.css', array());
	
	/* bare bones print styles */
	wp_enqueue_style( 'print',  get_template_directory_uri() . '/assets/css/print.css', array());

	wp_enqueue_style( 'gridscript',  get_template_directory_uri() . '/assets/css/gridscript.css', array());
	
	wp_enqueue_style( 'style', get_stylesheet_uri() );

	/* font awesome is rolled into Logical Bones */
	wp_enqueue_style( 'font-awesome',  get_template_directory_uri() . '/assets/css/font-awesome.css', array());
	
	wp_enqueue_script('modernizr', get_template_directory_uri() . '/assets/scripts/modernizr-2.5.3-min.js', array("jquery"), '2.0');
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Enqueue the global JS - Ajax will not work without it
	wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/assets/scripts/global.js', array( 'jquery' ), bp_get_version() );

	// Add words that we need to use in JS to the end of the page so they can be translated and still used.
	$params = array(
		'my_favs'           => __( 'My Favorites', 'thatcamp' ),
		'accepted'          => __( 'Accepted', 'thatcamp' ),
		'rejected'          => __( 'Rejected', 'thatcamp' ),
		'show_all_comments' => __( 'Show all comments for this thread', 'thatcamp' ),
		'show_all'          => __( 'Show all', 'thatcamp' ),
		'comments'          => __( 'comments', 'thatcamp' ),
		'close'             => __( 'Close', 'thatcamp' ),
		'view'              => __( 'View', 'thatcamp' ),
		'mark_as_fav'	    => __( 'Favorite', 'thatcamp' ),
		'remove_fav'	    => __( 'Remove Favorite', 'thatcamp' )
	);
	wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );
	wp_enqueue_script('transit', get_template_directory_uri() . '/assets/scripts/jquery.transit.min.js', array("jquery"), '2.0');
	wp_enqueue_script('gridrotator', get_template_directory_uri() . '/assets/scripts/jquery.gridrotator.js', array("jquery"), '2.0');
	
	wp_enqueue_script('custom', get_template_directory_uri() . '/assets/scripts/custom.js', array("jquery"), '2.0');
		
}
endif;
add_action( 'wp_enqueue_scripts', 'thatcamp_load_scripts' );

// thatcamp widgets
if ( ! function_exists( 'thatcamp_widgets_init' ) ) :
function thatcamp_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'thatcamp'),
			'id'            => 'sidebar',
			'description'   => 'Sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">', 	  
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);
	
	
	register_sidebar(
		array(
			'name'          => __( 'Sidebar Documents', 'thatcamp'),
			'id'            => 'sidebar-documents',
			'description'   => 'Sidebar Documents',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">', 	  
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);
	
	register_sidebar(
		array(
			'name'          => __( 'Sidebar Home', 'thatcamp'),
			'id'            => 'sidebar-home',
			'description'   => 'Sidebar Home',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">', 	  
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);
	
	
}
endif;
add_action( 'widgets_init', 'thatcamp_widgets_init' );
?>