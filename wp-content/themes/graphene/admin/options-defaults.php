<?php
/**
 * Set the default values for all the settings. If no user-defined values
 * is available for any setting, these defaults will be used.
 */
global $graphene_defaults;
$graphene_defaults = array(
	/* Theme's DB version */
	'db_version' 		=> '1.2',
	
	/* Theme's options page hook suffix */
	'hook_suffix'		=> '',
	'hook_suffix_faq'	=> '',
	
	/* All options toggle */
	'show_all_options' 	=> false,
	
	/* Default excerpt length */
	'excerpt_length' 	=> apply_filters( 'graphene_excerpt_length', 55 ),
	
	/* Grid-based widths */
	'container_width' 	=> 960,
	'grid_width' 		=> 40,
	'gutter_width' 		=> 10,
	'column_width' 		=> array(
								'three_col'	=> array(
												'sidebar_left'	=> 220,
												'content' 		=> 460,
												'sidebar_right' => 220,
												),
								'two_col'	=> array(
												'content' 		=> 640,
												'sidebar' 		=> 280,
												),
							),

	/* Slider options */
	'slider_type' 				=> 'latest_posts', // latest_posts | random | posts_pages | categories
	'slider_specific_posts' 	=> '',
    'slider_specific_categories'=> '',
	'slider_exclude_categories'	=> 'disabled',
	'slider_random_category_posts' => false,
	'slider_postcount' 			=> '',
	'slider_img' 				=> 'featured_image', // disabled | featured_image | post_image | custom_url
	'slider_display_style' 		=> 'thumbnail-excerpt', // thumbnail-excerpt | bgimage-excerpt | full-post
	'slider_imgurl' 			=> '',
	'slider_height' 			=> '',
	'slider_speed' 				=> 7000,
	'slider_trans_speed' 		=> 400,
	'slider_position' 			=> false,
	'slider_disable'			=> false,
	'slider_full_width'			=> false,
    'slider_animation'			=> 'horizontal-slide', // horizontal-slide | vertical-slide | fade | none
	
	/* Infinite Scroll options */
	'inf_scroll_enable'			=> false,
	'inf_scroll_click'			=> false,
	'inf_scroll_comments'		=> false,
	
	/* Front page options */
	'frontpage_posts_cats' 		=> array(),
	
	/* Homepage panes */
	'show_post_type' 			=> 'latest-posts', // latest-posts | cat-latest-posts | posts
	'homepage_panes_count' 		=> '2',
	'homepage_panes_cat' 		=> array( '1' ),
	'homepage_panes_posts' 		=> '1',
	'disable_homepage_panes' 	=> false,
    
    /* Comment options */
    'comments_setting' 			=> 'wordpress', // wordpress | disabled_pages | disabled_completely
        
    /* Child page options */    
    'hide_parent_content_if_empty' => false,
    'child_page_listing' 		=> 'show_always', // hide | show_always | show_if_parent_empty
        
	/* RSS Feed options */
	'use_custom_rss_feed'       => false,
	'custom_rss_feed_url'       => '',
	
	/* Top bar options(Syndication) */
	'hide_top_bar' 				=> false,        		
	'social_media_new_window'   => false,	
	'social_profiles'           => array ( 
										array ( 
											'type'	=> 'rss',
											'name'	=> 'RSS',
											'title'	=> sprintf( __( 'Subscribe to %s\'s RSS feed', 'graphene' ), get_bloginfo( 'name' ) ),
											'url'	=> '',
										)
									),
	
	/* Adsense Options */
	'show_adsense' 				=> false,
	'adsense_code' 				=> '',
	'adsense_show_frontpage'    => false,
	
	/* Social Sharing options */
	'show_addthis' 				=> false,
	'show_addthis_page'         => false,
	'show_addthis_archive'		=> false,
	'addthis_location' 			=> 'post-bottom', // post-bottom | post-top | top-bottom
	'addthis_code' 				=> '',
	
	/* Google Analytics options */
	'show_ga' 					=> false,
	'ga_code' 					=> '',
	
	/* Widget Area options */
	'alt_home_sidebar' 			=> false,
	'alt_home_footerwidget' 	=> false,
	'enable_header_widget' 		=> false,
	
	/* Footer options */
	'show_cc' 					=> false,
	'copy_text' 				=> '',
	'hide_copyright' 			=> false,
	'hide_return_top' 			=> false,
    
    /* Print options */
    'print_css' 				=> false,
    'print_button' 				=> false,
    	
	/* Display Options Page */
	
	/* Header options */
	'light_header' 				=> false,
	'link_header_img' 			=> false,
	'featured_img_header' 		=> false,
	'use_random_header_img' 	=> false,
	'header_img_height'		 	=> 198,	
	'search_box_location' 		=> 'top_bar', // top_bar | nav_bar | disabled
	
	/* Column options */
    /* two column with the main-content on the left side */	
	'column_mode' 				=> 'two_col_left',  // one_column | two_col_left | two_col_right | three_col_left | three_col_right | three_col_center
	'bbp_column_mode' 			=> 'two_col_left',
	
	/* Posts Display options */
	'posts_show_excerpt' 		=> false,
	'archive_full_content' 		=> false,
	'hide_post_author' 			=> false,
	'post_date_display' 		=> 'icon_no_year',  // hidden | icon_no_year | icon_plus_year | text   
	'hide_post_commentcount' 	=> false,
	'hide_post_cat' 			=> false,
	'hide_post_tags' 			=> false,
	'show_post_avatar' 			=> false,
	'show_post_author' 			=> false,
	'show_excerpt_more' 		=> false,
	
	/* Excerpt options */
	'excerpt_html_tags' 		=> '',
	
	/* Footer widget options */
	'footerwidget_column' 		=> 3,
	'alt_footerwidget_column' 	=> 3,
	
	/* Navigation menu options */
	'navmenu_child_width' 		=> '',
	'navmenu_home_desc' 		=> '',
	'disable_menu_desc' 		=> false,
	
	/* Colour options */
	'colour_preset'		=> 'default',
	'colour_presets'	=> array(
								'default' 			=> array(
										'name'	=> __( 'Default', 'graphene' ),
										'code'	=> 								'{"bg_content_wrapper":"#e3e3e3","bg_content":"#ffffff","bg_meta_border":"#e3e3e3","bg_post_top_border":"#d8d8d8","bg_post_bottom_border":"#cccccc","bg_widget_item":"#ffffff","bg_widget_list":"#e3e3e3","bg_widget_header_border":"#195392","bg_widget_title":"#ffffff","bg_widget_title_textshadow":"#555555","bg_widget_header_bottom":"#1f6eb6","bg_widget_header_top":"#3c9cd2","bg_widget_box_shadow":"#BBBBBB","bg_slider_top":"#0F2D4D","bg_slider_bottom":"#2880C3","bg_button":"#2982C5","bg_button_label":"#ffffff","bg_button_label_textshadow":"#16497E","bg_button_box_shadow":"#aaaaaa","bg_archive_left":"#0F2D4D","bg_archive_right":"#2880C3","bg_archive_label":"#E3E3E3","bg_archive_text":"#ffffff","bg_archive_textshadow":"#333333","content_font_colour":"#2c2b2b","title_font_colour":"#1772af","link_colour_normal":"#1772af","link_colour_visited":"#1772af","link_colour_hover":"#074d7c","bg_comments":"#E9ECF5","comments_text_colour":"#2C2B2B","threaded_comments_border":"#DDDDDD","bg_author_comments":"#FFFFFF","bg_author_comments_border":"#CCCCCC","author_comments_text_colour":"#2C2B2B","bg_comment_form":"#EEEEEE","comment_form_text":"#2C2B2B"}'
										),
								'dream-magnet' 		=> array(
										'name'	=> __( 'Dream Magnet', 'graphene' ),
										'code'	=>  '{"bg_content_wrapper":"#e3e3e3","bg_content":"#ffffff","bg_meta_border":"#e3e3e3","bg_post_top_border":"#d8d8d8","bg_post_bottom_border":"#cccccc","bg_widget_item":"#ffffff","bg_widget_list":"#e3e3e3","bg_widget_header_border":"#022328","bg_widget_title":"#ffffff","bg_widget_title_textshadow":"#04343a","bg_widget_header_bottom":"#06454c","bg_widget_header_top":"#005F6B","bg_widget_box_shadow":"#BBBBBB","bg_slider_top":"#06454c","bg_slider_bottom":"#005F6B","bg_button":"#005F6B","bg_button_label":"#ffffff","bg_button_label_textshadow":"#053a41","bg_button_box_shadow":"#aaaaaa","bg_archive_left":"#06454c","bg_archive_right":"#005F6B","bg_archive_label":"#b6d2d5","bg_archive_text":"#eae9e9","bg_archive_textshadow":"#033c42","content_font_colour":"#2c2b2b","title_font_colour":"#008C9E","link_colour_normal":"#008C9E","link_colour_visited":"#008C9E","link_colour_hover":"#005F6B","bg_comments":"#E9ECF5","comments_text_colour":"#2C2B2B","threaded_comments_border":"#DDDDDD","bg_author_comments":"#FFFFFF","bg_author_comments_border":"#005F6B","author_comments_text_colour":"#2C2B2B","bg_comment_form":"#EEEEEE","comment_form_text":"#2C2B2B"}'
										),
								'curiosity-killed' 	=> array(
										'name'	=> __( 'Curiosity Killed', 'graphene' ),
										'code'	=>  '{"bg_content_wrapper":"#DCE9BE","bg_content":"#ffffff","bg_meta_border":"#ffffff","bg_post_top_border":"#ffffff","bg_post_bottom_border":"#ffffff","bg_widget_item":"#ffffff","bg_widget_list":"#e3e3e3","bg_widget_header_border":"#640822","bg_widget_title":"#ffffff","bg_widget_title_textshadow":"#402222","bg_widget_header_bottom":"#74122e","bg_widget_header_top":"#99173C","bg_widget_box_shadow":"#BBBBBB","bg_slider_top":"#74122e","bg_slider_bottom":"#99173C","bg_button":"#99173C","bg_button_label":"#ffffff","bg_button_label_textshadow":"#59071e","bg_button_box_shadow":"#aaaaaa","bg_archive_left":"#74122e","bg_archive_right":"#99173C","bg_archive_label":"#fdd2de","bg_archive_text":"#ffffff","bg_archive_textshadow":"#6d0d0d","content_font_colour":"#2c2b2b","title_font_colour":"#99173C","link_colour_normal":"#99173C","link_colour_visited":"#99173C","link_colour_hover":"#5b0820","bg_comments":"#f3fedd","comments_text_colour":"#2C2B2B","threaded_comments_border":"#DCE9BE","bg_author_comments":"#fee6ed","bg_author_comments_border":"#99173C","author_comments_text_colour":"#2C2B2B","bg_comment_form":"#ffffff","comment_form_text":"#696969"}'
										),
								'zesty-orange' 		=> array(
										'name'	=> __( 'Zesty Orange', 'graphene' ),
										'code'	=>  '{"bg_content_wrapper":"#FBE1BB","bg_content":"#ffffff","bg_meta_border":"#FBE1BB","bg_post_top_border":"#f8bd68","bg_post_bottom_border":"#f8bd68","bg_widget_item":"#ffffff","bg_widget_list":"#fec777","bg_widget_header_border":"#FF5000","bg_widget_title":"#ffffff","bg_widget_title_textshadow":"#FF5000","bg_widget_header_bottom":"#FF9600","bg_widget_header_top":"#FFB400","bg_widget_box_shadow":"#BBBBBB","bg_slider_top":"#FF9600","bg_slider_bottom":"#FFB400","bg_button":"#FF9600","bg_button_label":"#ffffff","bg_button_label_textshadow":"#a6660b","bg_button_box_shadow":"#888888","bg_archive_left":"#FF9600","bg_archive_right":"#FFB400","bg_archive_label":"#cc3a06","bg_archive_text":"#000000","bg_archive_textshadow":"#FF9600","content_font_colour":"#2c2b2b","title_font_colour":"#FF5000","link_colour_normal":"#FF5000","link_colour_visited":"#FF5000","link_colour_hover":"#ff7a00","bg_comments":"#fef0dd","comments_text_colour":"#2C2B2B","threaded_comments_border":"#FEC777","bg_author_comments":"#FFFFFF","bg_author_comments_border":"#FF9600","author_comments_text_colour":"#2C2B2B","bg_comment_form":"#fff3e1","comment_form_text":"#2C2B2B"}'
										)
							),
	
	'top_bar_top_bg' 			=> '#000000',
	'top_bar_bottom_bg' 		=> '#313130',
	'top_bar_border' 			=> '#222222',
	'top_bar_header_border' 	=> '#000000',
	
	'menu_primary_top_bg' 		=> '#212121',
	'menu_primary_bottom_bg' 	=> '#101010',
	'menu_primary_border'		=> '#000000',
	'menu_primary_item' 		=> '#ffffff',
	'menu_primary_description'	=> '#aaaaaa',
	'menu_primary_active_top_bg' 		=> '#eeeeee',
	'menu_primary_active_bottom_bg' 	=> '#dddddd',
	'menu_primary_active_item' 			=> '#000000',
	'menu_primary_active_description'	=> '#484848',
	'menu_primary_dd_top_bg' 			=> '#efefef',
	'menu_primary_dd_bottom_bg' 		=> '#dfdfdf',
	'menu_primary_dd_item' 				=> '#000000',
	'menu_primary_dd_shadow'			=> '#555555',
	'menu_primary_dd_active_top_bg' 	=> '#333333',
	'menu_primary_dd_active_bottom_bg' 	=> '#212121',
	'menu_primary_dd_active_item' 		=> '#ffffff',
	
	'menu_sec_bg' 				=> '#222222',
	'menu_sec_border' 			=> '#2c2c2c',
	'menu_sec_item' 			=> '#ffffff',
	'menu_sec_active_bg'		=> '#eeeeee',
	'menu_sec_active_item' 		=> '#000000',
	'menu_sec_dd_top_bg'		=> '#eeeeee',
	'menu_sec_dd_bottom_bg'		=> '#dedede',
	'menu_sec_dd_item' 			=> '#000000',
	'menu_sec_dd_shadow' 		=> '#555555',
	'menu_sec_dd_active_top_bg'	=> '#333333',
	'menu_sec_dd_active_bottom_bg'	=> '#212121',
	'menu_sec_dd_active_item' 	=> '#ffffff',
	
	'bg_content_wrapper' 		=> '#e3e3e3',
	'bg_content' 				=> '#ffffff',
	'bg_meta_border' 			=> '#e3e3e3',
	'bg_post_top_border' 		=> '#d8d8d8',
	'bg_post_bottom_border' 	=> '#cccccc',
	'bg_sticky_content' 		=> '#ddeeff',
	'bg_child_page_content' 	=> '#E9ECF5',
	'bg_widget_item' 			=> '#ffffff',
	'bg_widget_list' 			=> '#e3e3e3',
	'bg_widget_header_border'	=> '#195392',
	'bg_widget_title' 			=> '#ffffff',
	'bg_widget_title_textshadow'=> '#555555',
	'bg_widget_header_bottom' 	=> '#1f6eb6',
	'bg_widget_header_top' 		=> '#3c9cd2',
	'bg_widget_box_shadow'		=> '#BBBBBB',
	'bg_slider_top' 			=> '#0F2D4D',
	'bg_slider_bottom' 			=> '#2880C3',
	'bg_button' 				=> '#2982C5',
	'bg_button_label' 			=> '#ffffff',
	'bg_button_label_textshadow'=> '#16497E',
	'bg_button_box_shadow'		=> '#aaaaaa',
	'bg_archive_left' 			=> '#0F2D4D',
	'bg_archive_right' 			=> '#2880C3',
	'bg_archive_label' 			=> '#E3E3E3',
	'bg_archive_text' 			=> '#ffffff',
	'bg_archive_textshadow' 	=> '#333333',
	'content_font_colour' 		=> '#2c2b2b',
	'title_font_colour' 		=> '#1772af',
	'link_colour_normal' 		=> '#1772af',
	'link_colour_visited' 		=> '#1772af',
	'link_colour_hover' 		=> '#074d7c',
	
	'footer_bg' 				=> '#111111',
	'footer_heading' 			=> '#e3e3e3',
	'footer_text'				=> '#999999',
	'footer_link' 				=> '#ffffff',
	'footer_submenu_text' 		=> '#cccccc',
	'footer_submenu_border'		=> '#222222',
	
	/* Google Webfonts */
	'webfont_families'			=> '',
	
	/* Comments colour options */
	'bg_comments' 				=> '#E9ECF5',
	'comments_text_colour' 		=> '#2C2B2B',
	'threaded_comments_border'	=> '#DDDDDD',
	'bg_author_comments' 		=> '#FFFFFF',
	'bg_author_comments_border' => '#CCCCCC',
	'author_comments_text_colour'=> '#2C2B2B',
	'bg_comment_form' 			=> '#EEEEEE',
	'comment_form_text' 		=> '#2C2B2B',
    
	/* Header Text options */
	'header_title_font_type' 	=> '',
	'header_title_font_size'	=> '',
	'header_title_font_lineheight' => '',
	'header_title_font_weight' 	=> '',
	'header_title_font_style' 	=> '',
	
	'header_desc_font_type' 	=> '',
	'header_desc_font_size' 	=> '',
	'header_desc_font_lineheight'=> '',
	'header_desc_font_weight' 	=> '',
	'header_desc_font_style' 	=> '',
	
	/* Content Text options */
	'content_font_type' 		=> '',
	'content_font_size' 		=> '',
	'content_font_lineheight' 	=> '',
	
	'link_decoration_normal' 	=> '',
	'link_decoration_hover' 	=> '',
	
	/* Miscellaneous options */
	'hide_allowedtags' 			=> false,
	'custom_site_title_frontpage'=> '',
	'custom_site_title_content' => '',
	'favicon_url' 				=> '',
	'custom_css' 				=> '',
	'disable_editor_style' 		=> false,
    
	/* Advanced options */
	'enable_preview' 			=> false,
	'widget_hooks' 				=> array(),
	'head_tags'					=> '',
	'disable_css_generate'		=> false,
	
	/* Miscellaneous switches and vars */
	'disable_credit' 			=> false,
	'template_dir'				=> get_template_directory(),
);
