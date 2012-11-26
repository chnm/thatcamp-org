<?php
/**
 * Functions and definitions for thatcamp
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */


// Check to see if BuddyPress is active, otherwise load default WordPress theme
if ( ! function_exists( 'bp_is_active' ) ) {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	return;
}


// Content width set
if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

// Don't sohw the admin bar
add_filter('show_admin_bar', '__return_false');


/**
 * Sets up Theme
 *
 * Performs various theme set ups and links in files.
 *
 * Adds in:
 * - Ajax.php for BuddyPress
 * - Template Functions - a functions file for commenting and other theme related functions
 * - BuddyPress Functions - a functions file modified from BP Default theme
 *
 * Sets up:
 * - Langauges and textdomain for thatcamp
 * - Feed links
 * - Editor Style
 * - Post Thumbnails
 * - Post formats - aside, image, link, quote all supported
 * - BuddyPress buttons - add friend, messsage (private and public), join, topic, blog, visit
 *
 * Registers the following Menus:
 * - Top (In Header)
 * - Bottom (In Footer)
 * - Middle (Responsive menu)
 * - Documents (Document template pages only)
 *
 * @since thatcamp (1.0)
 */

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
	register_nav_menu('middle', __('Middle', 'thatcamp'));
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


/**
 * Loads all scripts/stylesheets used in theme
 *
 * Loads the following scripts and stylesheets:
 * - Normalise.css : resets all CSS
 * - Print.css : a minimal print stylesheet
 * - Style.css : this is generated from the files in assets/less - the theme uses LESS and auto generates the style.css
 * - Gridscript.css : this is for the avatar grid on the home page
 * - Font-awesome.css : This is an icon font that can be reused throughout the site
 * - Grid script files : transit.min.js, gridrotator.js - this is for the avatar grid on the home page
 * - Custom.js : Custom grid scripting and also custom scripting for the responsive menu
 * - Modernizr : Polyfill and shim along with CSS transitions
 *
 * It sets up:
 * - Threaded comments
 * - Favourites and other scripts for BuddyPress to work
 *
 * @since thatcamp (1.0)
 */

if ( ! function_exists( 'thatcamp_load_scripts' ) ) :
function thatcamp_load_scripts() {

	wp_enqueue_style( 'normalise',  get_template_directory_uri() . '/assets/css/normalise.css', array());

	/* bare bones print styles */
	wp_enqueue_style( 'print',  get_template_directory_uri() . '/assets/css/print.css', array());

	wp_enqueue_style( 'gridscript',  get_template_directory_uri() . '/assets/css/gridscript.css', array());

	wp_enqueue_style( 'style', get_stylesheet_uri() );

	/* font awesome is rolled into Logical Bones */
	wp_enqueue_style( 'font-awesome',  get_template_directory_uri() . '/assets/css/font-awesome.css', array(), '2.0');

	wp_enqueue_script('modernizr', get_template_directory_uri() . '/assets/scripts/modernizr-2.6.2-min.js', array("jquery"), '2.0');

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


/**
 * Sets up the widget areas
 *
 * The following widget areas are available:
 * - Sidebar (Default Sidebar)
 * - Home
 * - Stream
 * - Activity
 * - Twitter (Widget area just for Twitter Widget)
 *
 *
 * @since thatcamp (1.0)
 */
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
			'name'          => __( 'Sidebar Home', 'thatcamp'),
			'id'            => 'sidebar-home',
			'description'   => 'Sidebar Home',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);

	register_sidebar(
		array(
			'name'          => __( 'Sidebar Stream', 'thatcamp'),
			'id'            => 'sidebar-stream',
			'description'   => 'Sidebar Stream',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);

	register_sidebar(
		array(
			'name'          => __( 'Sidebar Activity', 'thatcamp'),
			'id'            => 'sidebar-activity',
			'description'   => 'Sidebar Activity',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);

	register_sidebar(
		array(
			'name'          => __( 'Sidebar Twitter', 'thatcamp'),
			'id'            => 'sidebar-twitter',
			'description'   => 'Sidebar twitter',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
       		'before_title' => '<h3 class="widgettitle">',
       		'after_title' => '</h3>'
			)
	);


	require( get_stylesheet_directory() . '/functions/widgets.php' );
}
endif;
add_action( 'widgets_init', 'thatcamp_widgets_init' );

/**
 * Site Search
 *
 * Drop down filterable search.
 *
 * @since thatcamp (1.0)
 */
remove_action( 'bp_init', 'bp_core_action_search_site', 7 );
function thatcamp_action_search_site() {
	if ( !bp_is_current_component( bp_get_search_slug() ) )
		return;

	if ( empty( $_POST['search-terms'] ) ) {
		$redirect = wp_get_referer() ? wp_get_referer() : bp_get_root_domain();
		bp_core_redirect( $redirect );
		return;
	}

	$search_terms = stripslashes( $_POST['search-terms'] );
	$search_which = !empty( $_POST['search-which'] ) ? $_POST['search-which'] : '';
	$query_string = '/?s=';

	switch ( $search_which ) {
		case 'thatcamporg':
			$slug = '';
			$var  = '/?s=';

			// If posts aren't displayed on the front page, find the post page's slug.
			if ( 'page' == get_option( 'show_on_front' ) ) {
				$page = get_post( get_option( 'page_for_posts' ) );

				if ( !is_wp_error( $page ) && !empty( $page->post_name ) ) {
					$slug = $page->post_name;
					$var  = '?s=';
				}
			}

			$redirect = home_url( $slug . $query_string . urlencode( $search_terms ) );
			break;

		case 'all_thatcamps':
		default:
			$redirect = get_blog_option( thatcamp_proceedings_blog_id(), 'home' ) . $query_string . urlencode( $search_terms );
			break;
	}

	bp_core_redirect( $redirect );
}
add_action( 'bp_init', 'thatcamp_action_search_site', 7 );

// Gets the blog ID
function thatcamp_proceedings_blog_id() {
	global $wpdb;
	return $wpdb->get_var( "SELECT blog_id FROM $wpdb->blogs WHERE domain LIKE 'proceedings.%'" );
}

/**
 * Fixes directory titles
 */
function thatcamp_filter_title( $full_title, $title, $sep, $sep_location ) {
	if ( bp_is_groups_component() && bp_is_directory() ) {
		return 'THATCamps Directory | THATCamp';
	} else if ( bp_is_members_component() && bp_is_directory() ) {
		return 'People Directory | THATCamp';
	} else if ( bp_is_activity_component() && bp_is_directory() ) {
		return 'THATCamp Activity | THATCamp';
	} else {
		$new_title = $title;
	}

	return $new_title . ' ' . $sep . ' ';
}
add_filter( 'bp_modify_page_title', 'thatcamp_filter_title', 10, 4 );

/**
 * Modify the user nav before it gets rendered, so we remove redundant items
 */
function thatcamp_mod_user_nav() {
	global $bp;

	if ( bp_is_active( 'xprofile' ) ) {
		bp_core_remove_nav_item( 'profile' );
	}

	if ( bp_is_active( 'blogs' ) ) {
		bp_core_remove_nav_item( 'blogs' );
	}

	if ( bp_is_active( 'settings' ) ) {
		bp_core_remove_nav_item( 'settings' );
	}

	if ( isset( $bp->bp_nav['forums'] ) ) {
		unset( $bp->bp_nav['forums'] );
	}

	// Cheating: Change 'Activity' to 'About'
	if ( isset( $bp->bp_nav[ bp_get_activity_slug() ] ) ) {
		$bp->bp_nav[ bp_get_activity_slug() ]['name'] = 'About';
	}

	// Cheating: Put Camps before Friends
	if ( isset( $bp->bp_nav[ bp_get_groups_slug() ] ) ) {
		$bp->bp_nav[ bp_get_groups_slug() ]['position'] = 55;
	}
}
add_action( 'bp_actions', 'thatcamp_mod_user_nav', 1 );
