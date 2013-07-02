<?php
/**
 * Register the stylesheets
*/
function graphene_register_styles(){
	global $graphene_settings;

	if ( ! is_admin() ){
		wp_register_style( 'graphene-stylesheet', get_stylesheet_uri(), array(), false, 'screen' );
		wp_register_style( 'graphene-stylesheet-rtl', get_template_directory_uri() . '/rtl.css', array(), false, 'screen' );
		wp_register_style( 'graphene-light-header', get_template_directory_uri() . '/style-light.css', array( 'graphene-stylesheet' ), false, 'screen' );
		wp_register_style( 'graphene-print', get_template_directory_uri() . '/style-print.css', array( 'graphene-stylesheet' ), false, 'print' );
		wp_register_style( 'graphene-bbpress', get_template_directory_uri() . '/style-bbpress.css', array( 'graphene-stylesheet' ), false, 'screen' );
	}
	
	wp_register_style( 'jquery-ui-slider', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.custom.css', array(), false, 'screen' );
	
}
add_action( 'init', 'graphene_register_styles' );


/**
 * Print the stylesheets
*/
function graphene_enqueue_styles(){
	global $graphene_settings;

	if ( ! is_admin() ){
		wp_enqueue_style( 'graphene-stylesheet' );
		if ( is_rtl() ) wp_enqueue_style( 'graphene-stylesheet-rtl' );
		if ( $graphene_settings['light_header'] ) wp_enqueue_style( 'graphene-light-header' );
		if ( is_singular() && $graphene_settings['print_css'] ) wp_enqueue_style( 'graphene-print' );
		if ( class_exists( 'bbPress' ) ) wp_enqueue_style( 'graphene-bbpress' );
	}
	
}
add_action( 'wp_enqueue_scripts', 'graphene_enqueue_styles' );


/**
 * Register custom scripts that the theme uses
*/
function graphene_register_scripts(){
	global $graphene_settings;
	
	wp_register_script( 'graphene-jquery-tools', get_template_directory_uri() . '/js/jquery.tools.min.js', array( 'jquery' ), '', true);
	wp_register_script( 'graphene-admin-js', get_template_directory_uri() . '/admin/js/admin.js', array( 'jquery' ), '', false );
	
	// Register scripts for older versions of WordPress
	if ( ! graphene_is_wp_version( '3.3' ) ){
		wp_register_script( 'jquery-ui-widget', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.widget.min.js', array( 'jquery-ui-core' ), '', true );
		wp_register_script( 'jquery-ui-mouse', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.mouse.min.js', array( 'jquery-ui-core' ), '', true );
		wp_register_script( 'jquery-ui-slider', get_template_directory_uri() . '/js/jquery-ui/jquery.ui.slider.min.js', array( 'jquery-ui-widget', 'jquery-ui-mouse' ), '', true );
	}
}
add_action( 'init', 'graphene_register_scripts' );


/**
 * Print custom scripts that the theme uses
*/
function graphene_enqueue_scripts(){
	global $graphene_settings;
	
	if ( ! is_admin() ) { // Front-end only
		wp_enqueue_script( 'jquery' );
		
		if ( ! $graphene_settings['slider_disable'] )
			wp_enqueue_script( 'graphene-jquery-tools' ); // jQuery Tools, required for slider
			
		if ( is_singular() && get_option( 'thread_comments' ) )
        	wp_enqueue_script( 'comment-reply' );
			
		if ( $graphene_settings['inf_scroll_enable'] || $graphene_settings['inf_scroll_comments'] )
			wp_enqueue_script( 'infinite-scroll', get_template_directory_uri() . '/js/jquery.infinitescroll.min.js', array( 'jquery' ), '', false );
			
		wp_enqueue_script( 'graphene-js', get_template_directory_uri() . '/js/graphene.js', array( 'jquery' ), '', false );
	}
}
add_action( 'wp_enqueue_scripts', 'graphene_enqueue_scripts' );


/**
 * Localize scripts and add JavaScript data
 *
 * @package Graphene
 * @since 1.9
 */
function graphene_localize_scripts(){
	global $graphene_settings, $wp_query;
	$posts_per_page = $wp_query->get( 'posts_per_page' );
	$comments_per_page = get_option( 'comments_per_page' );
	
	$js_object = array(
						/* General */
						'templateUrl'			=> get_template_directory_uri(),
						'isSingular'			=> is_singular(),
						
						/* Comments */
						'shouldShowComments'	=> graphene_should_show_comments(),
						
						/* Slider */
						'sliderDisable'			=> $graphene_settings['slider_disable'],
						'sliderAnimation'		=> $graphene_settings['slider_animation'],
						'sliderTransSpeed'		=> $graphene_settings['slider_trans_speed'],
						'sliderInterval'		=> $graphene_settings['slider_speed'],
						'sliderDisplay'			=> $graphene_settings['slider_display_style'],
						
						/* Infinite Scroll */
						'infScroll'				=> $graphene_settings['inf_scroll_enable'],
						'infScrollClick'		=> $graphene_settings['inf_scroll_click'],
						'infScrollComments'		=> $graphene_settings['inf_scroll_comments'],
						'totalPosts'			=> $wp_query->found_posts,
						'postsPerPage'			=> $posts_per_page,
						'isPageNavi'			=> function_exists( 'wp_pagenavi' ),
						'infScrollMsgText'		=> sprintf( 
														__( 'Fetching %1$s more item from %2$s left ...', 'graphene' ),
														'window.grapheneInfScrollItemsPerPage', 
														'window.grapheneInfScrollItemsLeft' ),
						'infScrollMsgTextPlural'=> sprintf( 
														_n( 'Fetching %1$s more item from %2$s left ...', 
															'Fetching %1$s more items from %2$s left ...', 
															$posts_per_page, 'graphene' ), 
														'window.grapheneInfScrollItemsPerPage', 
														'window.grapheneInfScrollItemsLeft' ),
						'infScrollFinishedText'	=> __( 'No more items to fetch', 'graphene' ),
						'commentsPerPage'		=> $comments_per_page,
						'totalComments'			=> graphene_get_comment_count( 'comments', true, true ),
						'infScrollCommentsMsg'	=> sprintf( 
														__( 'Fetching %1$s more top level comment from %2$s left ...', 'graphene' ), 
														'window.grapheneInfScrollCommentsPerPage', 
														'window.grapheneInfScrollCommentsLeft' ),
						'infScrollCommentsMsgPlural'=> sprintf( 
														_n( 'Fetching %1$s more top level comment from %2$s left ...', 
															'Fetching %1$s more top level comments from %2$s left ...', 
															$comments_per_page, 'graphene' ), 
														'window.grapheneInfScrollCommentsPerPage', 
														'window.grapheneInfScrollCommentsLeft' ),
						'infScrollCommentsFinishedMsg'	=> __( 'No more comments to fetch', 'graphene' ),
				);
	wp_localize_script( 'graphene-js', 'grapheneJS', apply_filters( 'graphene_js_object', $js_object ) );
}
add_action( 'wp_enqueue_scripts', 'graphene_localize_scripts' );