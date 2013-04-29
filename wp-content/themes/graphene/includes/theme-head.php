<?php 
/**
 * Build the custom CSS styles per colour option
 *
 * @global array $graphene_settings
 * @global array $graphene_defaults
 * @return string
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_build_style( $styles, $extra_args = array() ){
	global $graphene_defaults, $graphene_settings;
	$out = '';

	foreach ( $styles as $opts => $style ) {
		if ( stripos( $opts, '|' ) ){
			$opts = explode( '|', $opts );
		} else {
			$opts = (array) $opts;
		}
		
		if ( graphene_is_settings_custom( $opts ) ) {
			foreach ( $opts as $key => $opt ) {
				$opts[$key] = $graphene_settings[$opt];
			}
			$args = array_merge( array( $style ), $opts, $extra_args );
			if ( $args ) $out .= call_user_func_array( 'sprintf', $args );
		}
	}
	
	return $out;
}


/**
 * Check if the user settings are different than the default settings
 *
 * @param array $settings
 * @global array $graphene_settings
 * @global array $graphene_defaults
 * @return bool
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_is_settings_custom( $settings ){
	global $graphene_defaults, $graphene_settings;
	$settings = (array) $settings;
	
	$diff = false;
	foreach ( $settings as $key ) {
		if ( $graphene_defaults[$key] !== $graphene_settings[$key] ) {
			$diff = true;
			break;
		}
	}
	
	return $diff;
}


/**
 * Basic CSS minifier, based on the codes by Kit McAllister (http://kitmacallister.com/2011/minify-css-with-php/)
 *
 * @param string Regular CSS string to be minified
 * @return string Minified CSS string
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_minify_css( $css ){

	/* Strip comments */
	$css = preg_replace('!/\*.*?\*/!s','', $css);
	$css = preg_replace('/\n\s*\n/',"\n", $css);

	/* Minify */
	$css = preg_replace('/[\n\r \t]/',' ', $css);
	$css = preg_replace('/ +/',' ', $css);
	$css = preg_replace('/ ?([,:;{}] ) ?/','$1',$css);

	/* Kill trailing semicolon */
	$css = preg_replace('/;}/','}',$css);

	return $css;
}


/**
 * Get the custom style attributes, these are defined by theme options.
 * 
 * @global type $graphene_settings
 * @global type $graphene_defaults
 * @global type $content_width
 * @return string 
 */
function graphene_get_custom_style(){ 
	global $graphene_settings, $graphene_defaults, $content_width;
	
	$background = get_theme_mod( 'background_image', false);
	$bgcolor = get_theme_mod( 'background_color', false);
	$widgetcolumn = $graphene_settings['footerwidget_column'];
	$widgetcolumn_alt = $graphene_settings['alt_footerwidget_column'];
	$container_width = apply_filters( 'graphene_container_width', $graphene_settings['container_width'] );
	$gutter = $graphene_settings['gutter_width'];
	$grid_width = $graphene_settings['grid_width'];
        
	$style = '';
	
	/* Disable default background if a custom background colour is defined */
	if ( ! $background && $bgcolor ) {
		$style .= 'body{background-image:none;}';
	}
	
	/* Header text */
	$header_textcolour = get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR );
	if ( $header_textcolour != apply_filters( 'graphene_header_textcolor', '000000' ) )
		$style .= '.header_title, .header_title a, .header_title a:visited, .header_title a:hover, .header_desc {color:#' . $header_textcolour . ';}';
		
	/* Set the width of the bottom widget items if number of columns is specified */
	if ( ( $widgetcolumn != $graphene_defaults['footerwidget_column'] || $container_width != $graphene_defaults['container_width'] ) && $widgetcolumn ) {
		$widget_width = floor( ( ( ( $container_width - $gutter * 2 ) - 20 * ( $widgetcolumn - 1 ) ) / $widgetcolumn ) - 20 );
		$style .= '#sidebar_bottom .sidebar-wrap{width:' . $widget_width . 'px}';
	}
	if ( $graphene_settings['alt_home_footerwidget'] && ( $widgetcolumn_alt != $graphene_defaults['alt_footerwidget_column'] || $container_width != $graphene_defaults['container_width'] ) && $widgetcolumn_alt ) {
		$widget_width = floor( ( ( ( $container_width - $gutter * 2 ) - 20 * ( $widgetcolumn_alt - 1 ) ) / $widgetcolumn_alt ) - 20 );
		$style .= '.home #sidebar_bottom .sidebar-wrap{width:' . $widget_width . 'px}';
	}
        
	/* Set the width of the nav menu dropdown menu item width if specified */
	if ( $graphene_settings['navmenu_child_width'] ) {
		$nav_width = $graphene_settings['navmenu_child_width'];
		$style .= '#nav li ul{width:'.$nav_width.'px;}';
		
		if ( ! is_rtl() ){
			$background_left = -652-(200-$nav_width);
			$tmp_width = $nav_width-35;
            
			$style .= '	#nav li ul ul{margin-left:'.$nav_width.'px}
                       	#header-menu ul li.menu-item-ancestor > a {
						background-position:'.$background_left.'px -194px;
						width:'.$tmp_width.'px;
                        }
                        #header-menu ul li.menu-item-ancestor:hover > a,
                        #header-menu ul li.current-menu-item > a,
                        #header-menu ul li.current-menu-ancestor > a {
						background-position:'.$background_left.'px -238px;
                        }
						#secondary-menu ul li.menu-item-ancestor > a {
						background-position:'.$background_left.'px -286px;
						width:'.$tmp_width.'px;
						}
						#secondary-menu ul li.menu-item-ancestor:hover > a,
						#secondary-menu ul li.current-menu-item > a,
						#secondary-menu ul li.current-menu-ancestor > a {
						background-position:'.$background_left.'px -319px;
						}';
		} else {
            $style .= '	#nav li ul ul{margin-right:'.$nav_width.'px; margin-left: 0;}
						#header-menu ul li.menu-item-ancestor > a,
						#secondary-menu ul li.menu-item-ancestor > a {
						width:'.($nav_width-35).'px;
						}';
        }
		
		$style .= '#header-menu ul li a{width:'.($nav_width-20).'px;}';
		$style .= '#secondary-menu ul li a{width:'.($nav_width-30).'px;}';
	}
	
	/* Header title text style */ 
	$font_style = '';
	$font_style .= ( $graphene_settings['header_title_font_type'] ) ? 'font-family:'.$graphene_settings['header_title_font_type'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_lineheight'] ) ? 'line-height:'.$graphene_settings['header_title_font_lineheight'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_size'] ) ? 'font-size:'.$graphene_settings['header_title_font_size'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_weight'] ) ? 'font-weight:'.$graphene_settings['header_title_font_weight'].';' : '';
	$font_style .= ( $graphene_settings['header_title_font_style'] ) ? 'font-style:'.$graphene_settings['header_title_font_style'].';' : '';
	if ( $font_style ) { $style .= '#header .header_title { '.$font_style.' }'; }

	/* Header description text style */ 
	$font_style = '';
	$font_style .= ( $graphene_settings['header_desc_font_type'] ) ? 'font-family:'.$graphene_settings['header_desc_font_type'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_size'] ) ? 'font-size:'.$graphene_settings['header_desc_font_size'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_lineheight'] ) ? 'line-height:'.$graphene_settings['header_desc_font_lineheight'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_weight'] ) ? 'font-weight:'.$graphene_settings['header_desc_font_weight'].';' : '';
	$font_style .= ( $graphene_settings['header_desc_font_style'] ) ? 'font-style:'.$graphene_settings['header_desc_font_style'].';' : '';
	if ( $font_style ) { $style .= '#header .header_desc { '.$font_style.' }'; }
	
	/* Content text style */ 
	$font_style = '';
	$font_style .= ( $graphene_settings['content_font_type'] ) ? 'font-family:'.$graphene_settings['content_font_type'].';' : '';
	$font_style .= ( $graphene_settings['content_font_size'] ) ? 'font-size:'.$graphene_settings['content_font_size'].';' : '';
	$font_style .= ( $graphene_settings['content_font_lineheight'] ) ? 'line-height:'.$graphene_settings['content_font_lineheight'].';' : '';
	$font_style .= ( $graphene_settings['content_font_colour'] != $graphene_defaults['content_font_colour'] ) ? 'color:'.$graphene_settings['content_font_colour'].';' : '';
	if ( $font_style ) { $style .= '.entry-content, .sidebar, .comment-entry { '.$font_style.' }'; }
	
    /* Adjust post title if author's avatar is shown */
	if ( $graphene_settings['show_post_avatar'] ) {
		$tmp_margin = !is_rtl() ? 'margin-right' : 'margin-left';
		$style .= '.post-title a, .post-title a:visited{display:block;'.$tmp_margin.':45px;padding-bottom:0;}';
	}
	
	/* Slider */
	if ( $graphene_settings['slider_height'] ) {
		$style .= '.featured_slider #slider_root{height:'.$graphene_settings['slider_height'].'px;}';
	}
	if ( $graphene_settings['slider_full_width'] ) {
		$style .= '#content .featured_slider, .container_16 #content .slider_post {width:' . graphene_grid_width( '', 16 ) . 'px;}';
	}
	
	/* Header image height */
	if ( $graphene_settings['header_img_height'] != $graphene_defaults['header_img_height'] ){
		$style .= '#header{height:'. HEADER_IMAGE_HEIGHT .'px;}';
	}
	
	/* Link header image */
	if ( $graphene_settings['link_header_img'] && ( HEADER_IMAGE_WIDTH != 960 || HEADER_IMAGE_HEIGHT != $graphene_defaults['header_img_height'] ) ) {
		$style .= '#header_img_link{width:'. HEADER_IMAGE_WIDTH .'px; height:'. HEADER_IMAGE_HEIGHT .'px;}';
	}
		
	// Link style
	$link_styles = array(
					'link_colour_normal' 	=> 'a,.post-title,.post-title a,#comments > h4.current a{color: %s}',
					'link_colour_visited'	=> 'a:visited,.post-title a:visited{color: %s}',
					'link_colour_hover'		=> 'a:hover,.post-title a:hover{color: %s}',
					'link_decoration_normal'=> 'a,.post-title a{text-decoration: %s}',
					'link_decoration_hover'	=> 'a:hover,.post-title a:hover{text-decoration: %s}',
				);
	$style .= graphene_build_style( $link_styles );

	
	/* Title text colour */
	$font_style = array(
						'title_font_colour' => '.post-title, .post-title a, .post-title a:hover, .post-title a:visited { color: %s }',
					);
	$style .= graphene_build_style( $font_style );
	
	// Custom column width
	$style .= graphene_get_custom_column_width();
	
	return $style;
}


/**
 * Get the custom colour style attributes defined by the theme colour settings
 * 
 * @global array $graphene_settings
 * @global array $graphene_defaults
 * @return string 
 */
function graphene_get_custom_colours( $hook_suffix = '', $force_all = false ){
	global $graphene_settings, $graphene_defaults;
	
	if ( ! $hook_suffix && is_admin() ) {
		$current_screen = get_current_screen();
		$hook_suffix = $current_screen->base;
	}
	$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : '';
    
	$style = '';
    
	if ( ! is_admin() || ( $graphene_settings['hook_suffix'] == $hook_suffix && $tab == 'colours' ) || $force_all ) {
		
		// Top bar
		$colours = array(
					'top_bar_top_bg|top_bar_bottom_bg'
									 		=> '#top-bar,.top-bar-preview .top-bar{
													background: %2$s;
													background: -moz-linear-gradient(%1$s, %2$s);
													background: -webkit-linear-gradient(%1$s, %2$s);
													background: -o-linear-gradient(%1$s, %2$s);
													-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
													background: linear-gradient(%1$s, %2$s);
												}',
					'top_bar_border' 		=> '#top-bar,.top-bar-preview .top-bar{border-color:%s}',
					'top_bar_header_border' => '#header,.top-bar-preview .header{border-color:%s}',
					);
		$style .= graphene_build_style( $colours );
		
		// Primary Menu (top level)
		$colours = array(
					'menu_primary_top_bg|menu_primary_bottom_bg' 
												=> '#header-menu-wrap{
														background: %2$s;
														background: -moz-linear-gradient(%1$s, %2$s);
														background: -webkit-linear-gradient(%1$s, %2$s);
														background: -o-linear-gradient(%1$s, %2$s);
														-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
														background: linear-gradient(%1$s, %2$s);
													}',
					'menu_primary_border'		=> '#header-menu-wrap {border-bottom: 1px solid %s}',
					'menu_primary_item' 		=> '#header-menu > li > a{color: %s}',
					'menu_primary_description'	=> '#header-menu > li > a > span{color: %s}',
					'menu_primary_active_top_bg|menu_primary_active_bottom_bg' 
												=> '#header-menu > li:hover,#header-menu > li.current-menu-item,#header-menu > li.current-menu-ancestor{
														background: %2$s;
														background: -moz-linear-gradient(%1$s, %2$s);
														background: -webkit-linear-gradient(%1$s, %2$s);
														background: -o-linear-gradient(%1$s, %2$s);
														-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
														background: linear-gradient(%1$s, %2$s);
													}',
					'menu_primary_active_item' 	=> '#header-menu > li:hover > a, #header-menu > li.current-menu-item > a, #header-menu > li.current-menu-ancestor > a {color: %s}',
					'menu_primary_active_description'	=> '#header-menu > li:hover > a > span, #header-menu > li.current-menu-item > a > span, #header-menu > li.current-menu-ancestor > a > span{color: %s}',
				);
		$style .= graphene_build_style( $colours );
		
		// Primary Menu (sub-level)
		$colours = array(
					'menu_primary_dd_top_bg|menu_primary_dd_bottom_bg' 
												=> '#header-menu ul li, .primary-menu-preview.dropdown ul li{
														background: %2$s;
														background: -moz-linear-gradient(%1$s, %2$s);
														background: -webkit-linear-gradient(%1$s, %2$s);
														background: -o-linear-gradient(%1$s, %2$s);
														-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
														background: linear-gradient(%1$s, %2$s);
													}',
					'menu_primary_dd_item' 		=> '#header-menu ul li a,.primary-menu-preview.dropdown ul > li > a{color: %s}',
					'menu_primary_dd_active_top_bg|menu_primary_dd_active_bottom_bg' 
												=> '#header-menu ul li:hover,#header-menu ul li.current-menu-item,#header-menu ul li.current-menu-ancestor,.primary-menu-preview.dropdown ul li.current-menu-item,.primary-menu-preview.dropdown ul li.current-menu-ancestor{
														background: %2$s;
														background: -moz-linear-gradient(%1$s, %2$s);
														background: -webkit-linear-gradient(%1$s, %2$s);
														background: -o-linear-gradient(%1$s, %2$s);
														-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
														background: linear-gradient(%1$s, %2$s);
													}',
					'menu_primary_dd_active_item' 	=> '#header-menu ul li:hover > a,#header-menu ul li.current-menu-item > a,#header-menu ul li.current-menu-ancestor > a,.primary-menu-preview.dropdown ul li.current-menu-item > a,.primary-menu-preview.dropdown ul li.current-menu-ancestor > a{color: %s}',
					'menu_primary_dd_shadow'	=> '#nav #header-menu ul,.primary-menu-preview .sub-menu{box-shadow: 0 1px 2px %s}',
				);
		$style .= graphene_build_style( $colours );
		
		// Secondary Menu (top level)
		$colours = array(
						'menu_sec_bg'		=> '#secondary-menu-wrap{background:%s}',
						'menu_sec_border' 	=> '#secondary-menu-wrap{border-color:%s}',
						'menu_sec_item'		=> '#secondary-menu > li > a{color:%s}',
						'menu_sec_active_bg'	=> '#secondary-menu > li:hover,#secondary-menu > li.current-menu-item,#secondary-menu > li.current-menu-ancestor{background:%s}',
						'menu_sec_active_item' 	=> '#secondary-menu > li:hover > a,#secondary-menu > li.current-menu-item > a,#secondary-menu > li.current-menu-ancestor > a{color:%s}',
					);
		$style .= graphene_build_style( $colours );
		
		// Secondary Menu (sub-level)
		$colours = array(
					'menu_sec_dd_top_bg|menu_sec_dd_bottom_bg' 
											=> '#secondary-menu ul li, .secondary-menu-preview .sub-menu li{
														background: %2$s;
														background: -moz-linear-gradient(%1$s, %2$s);
														background: -webkit-linear-gradient(%1$s, %2$s);
														background: -o-linear-gradient(%1$s, %2$s);
														-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
														background: linear-gradient(%1$s, %2$s);
													}',
					'menu_sec_dd_item' 		=> '#secondary-menu ul li a,.secondary-menu-preview .sub-menu > li > a{color: %s}',
					'menu_sec_dd_active_top_bg|menu_sec_dd_active_bottom_bg' 
											=> '#secondary-menu ul li:hover,#secondary-menu .sub-menu li.current-menu-item,#secondary-menu ul li.current-menu-ancestor,.secondary-menu-preview .sub-menu li.current-menu-item,.secondary-menu-preview .sub-menu li.current-menu-ancestor{
														background: %2$s;
														background: -moz-linear-gradient(%1$s, %2$s);
														background: -webkit-linear-gradient(%1$s, %2$s);
														background: -o-linear-gradient(%1$s, %2$s);
														-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
														background: linear-gradient(%1$s, %2$s);
													}',
					'menu_sec_dd_active_item' 	=> '#secondary-menu ul li:hover > a,#secondary-menu ul li.current-menu-item > a,#secondary-menu ul li.current-menu-ancestor > a,.secondary-menu-preview .sub-menu li.current-menu-item > a,.secondary-menu-preview .sub-menu li.current-menu-ancestor > a{color: %s}',
					'menu_sec_dd_shadow'	=> '#nav #secondary-menu ul,.secondary-menu-preview .sub-menu{box-shadow: 0 1px 2px %s}',
				);
		$style .= graphene_build_style( $colours );
					
		
		// Content area
		$colours = array(
						'bg_content_wrapper' 	=> '#content, .menu-bottom-shadow, #sidebar_bottom{background-color: %s}',
						'bg_content' 			=> '.post{background-color: %s;}',
						'bg_meta_border'		=> '.post-title, .post-title a, .post-title a:visited, .entry-footer{border-color: %s;}',
						'bg_post_top_border'	=> '.post{border-top-color: %s}',
						'bg_post_bottom_border'	=> '.post{border-bottom-color: %s}',
						'bg_sticky_content' 	=> '.sticky{background-color: %s;}',
						'bg_child_page_content' => '.child-page{background-color: %s;}',
					);
		$style .= graphene_build_style( $colours );
		
		// Widgets
		$colours = array(
						'bg_widget_item'			=> '.sidebar div.sidebar-wrap{background-color: %s}',
						'bg_widget_box_shadow'		=> '.sidebar div.sidebar-wrap{
															-moz-box-shadow: 0 0 5px %1$s;
															-webkit-box-shadow: 0 0 5px %1$s;
															box-shadow: 0 0 5px %1$s;
														}',
						'bg_widget_list'			=> '.sidebar ul li{border-color: %s}',
						'bg_widget_header_border' 	=> '.sidebar h3{border-color: %s}',
						'bg_widget_title'			=> '.sidebar h3, .sidebar h3 a, .sidebar h3 a:visited{color: %s}',
						'bg_widget_title_textshadow'=> '.sidebar h3{text-shadow: 0 -1px %s}',
						'bg_widget_header_top|bg_widget_header_bottom' 
													=> '.sidebar h3{
															background: %2$s;
															background: -moz-linear-gradient(%1$s, %2$s);
															background: -webkit-linear-gradient(%1$s, %2$s);
															background: -o-linear-gradient(%1$s, %2$s);
															-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
															background: linear-gradient(%1$s, %2$s);
														}',
					);
		$style .= graphene_build_style( $colours );
		
		// Slider
		if ( $graphene_settings['slider_display_style'] != 'bgimage-excerpt' ) {
			$colours = array(
							'bg_slider_top|bg_slider_bottom' 
														=> '.featured_slider{
																-pie-background: linear-gradient(left top,%1$s,%2$s);
																background: %2$s;
																background: -moz-linear-gradient(left top,%1$s, %2$s);
																background: -webkit-linear-gradient(left top,%1$s, %2$s);
																background: -o-linear-gradient(%1$s, %2$s);
																-ms-filter: "progid:DXImageTransform.Microsoft.gradient(gradientType=1,startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
																background: linear-gradient(left top,%1$s, %2$s);
															}',
						);
			$style .= graphene_build_style( $colours );
		}
		
		// Block button
		$colours = array(
						'bg_button|bg_button_label|bg_button_label_textshadow|bg_button_box_shadow' 
							=> '.block-button, .block-button:visited, .Button, .button, #commentform #submit, .wpsc_buy_button{
									background: %5$s;
									background: -moz-linear-gradient(%1$s,%5$s);
									background: -webkit-linear-gradient(%1$s,%5$s);
									background: -o-linear-gradient(%1$s, %5$s);
									-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%5$s\')";
									background: linear-gradient(%1$s,%5$s);
									border-color: %5$s;
									text-shadow: 0 -1px 1px %3$s;
									color: %2$s;
									-moz-box-shadow: 0 0 5px %4$s;
									-webkit-box-shadow: 0 0 5px %4$s;
									box-shadow: 0 0 5px %4$s
								}
								.block-button:hover, .button:hover, #commentform #submit:hover,.wpsc_buy_button:hover {
									background: %5$s;
									background: -moz-linear-gradient(%1$s,%6$s);
									background: -webkit-linear-gradient(%1$s,%6$s);
									background: -o-linear-gradient(%1$s, %6$s);
									-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'%1$s\', EndColorStr=\'%6$s\')";
									background: linear-gradient(%1$s,%6$s);
									color: %2$s;
								}'
					);
		$extra_args = array( graphene_hex_addition( $graphene_settings['bg_button'], -26), graphene_hex_addition( $graphene_settings['bg_button'], -52) );
		$style .= graphene_build_style( $colours, $extra_args );
		if ( is_admin() ) $style = str_replace( '.button', '.colour-preview .button', $style );
                
        // Archive
		$colours = array(
						'bg_archive_left|bg_archive_right' 
											=> '.page-title{
												  -pie-background: linear-gradient(left top,%1$s,%2$s);
												  background: %2$s;
												  background: -moz-linear-gradient(left top,%1$s, %2$s);
												  background: -webkit-linear-gradient(left top,%1$s, %2$s);
												  background: -o-linear-gradient(left top,%1$s, %2$s);
												  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(gradientType=1,startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
												  background: linear-gradient(left top,%1$s, %2$s);
											  	}',
						'bg_archive_label' 	=> '.page-title{color: %s}',
						'bg_archive_text'	=> '.page-title span{color: %s}',
						'bg_archive_textshadow' => '.page-title{text-shadow: 0 -1px 0 %s}',
					);
		$style .= graphene_build_style( $colours );
		
		// Comments area
		$colours = array(
						'bg_comments'				=> '#comments ol li.comment, #comments ol li.pingback, #comments ol li.trackback{background-color: %s}',
						'comments_text_colour' 		=> '#comments{color: %s}',
						'threaded_comments_border|bg_author_comments_border'
												 	=> '#comments ol.children li.comment{border-color: %s}
														#comments ol.children li.bypostauthor, #comments li.bypostauthor.comment{border-color: %s}',
						'bg_author_comments'		=> '#comments ol.children li.bypostauthor, #comments li.bypostauthor.comment{background-color: %s}',
						'author_comments_text_colour'=> '#comments ol.children li.bypostauthor, #comments li.bypostauthor.comment{font-color: %s}',
						'bg_comment_form'			=> '#commentform{background-color: %s}',
						'comment_form_text'			=> '#commentform{color: %s}',
					);
		$style .= graphene_build_style( $colours );
		
		// Footer
		$colours = array(
						'footer_bg|footer_text' => '#footer,.graphene-footer{background-color:%1$s;color:%2$s}',
						'footer_heading' 		=> '#copyright h3,.graphene-copyright .heading{color:%s}',
						'footer_link|footer_submenu_text' 
												=> '#footer a, #footer a:visited, #footer a:hover,.graphene-footer a, .graphene-footer a:visited, .graphene-footer a:hover{color:%1$s;border-color:%1$s}
													#footer-menu .sub-menu li a, #footer-menu .sub-menu li a:visited, #footer-menu .sub-menu li a:hover,.footer-menu .sub-menu li a, .footer-menu .sub-menu li a:visited, .footer-menu .sub-menu li a:hover{color:%2$s;border-color:%2$s}',
						'footer_submenu_border'	=> '.footer-menu-wrap ul.sub-menu,.footer-menu-wrap ul.sub-menu{border-color:%s}',
					);
		$style .= graphene_build_style( $colours );

	}
	
	// Admin only
	if ( is_admin() && ( $graphene_settings['hook_suffix'] == $hook_suffix && $tab == 'colours' ) ) {
		
		// Widgets
		$colours = array(
						'content_font_colour'	=> '.graphene, .graphene li, .graphene p{color: %s}',
						'link_colour_normal'	=> '.graphene a{color: %s}',
						'link_colour_visited'	=> '.graphene a:visited{color: %s}',
						'link_colour_hover'		=> '.graphene a:hover{color: %s}',
					);
		$style .= graphene_build_style( $colours );
		
		// Slider
		$colours = array(
						'bg_slider_top|bg_slider_bottom'
							=> '#grad-box{
								  -pie-background: linear-gradient(left top,%1$s,%2$s);
								  background: %2$s;
								  background: -moz-linear-gradient(left top,%1$s, %2$s);
								  background: -webkit-linear-gradient(left top,%1$s, %2$s);
								  background: -o-linear-gradient(left top,%1$s, %2$s);
								  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(gradientType=1,startColorStr=\'%1$s\', EndColorStr=\'%2$s\')";
								  background: linear-gradient(left top,%1$s, %2$s);
							  }',
					);
		$style .= graphene_build_style( $colours );

	}
        
    return $style;
}


/**
 * Build and return the CSS styles custom column width
 *
 * @package Graphene
 * @since 1.6
 * @return string $style CSS styles
*/
function graphene_get_custom_column_width(){
	global $graphene_settings, $graphene_defaults;
	$column_mode = graphene_column_mode();
	$container = $graphene_settings['container_width'];
	$grid = $graphene_settings['grid_width'];
	$gutter = $graphene_settings['gutter_width'];
	$style = '';
	
	/* Custom container width */
	if ( $container != $graphene_defaults['container_width'] ){
		$style .= ".container_16 {width:{$container}px}";
		for ( $i = 1; $i <= 16; $i++ ){
			
			/* Grid */
			$style .= '.container_16 .grid_' . $i . '{width:';
			$style .= ( $grid * $i ) + ( $gutter * ( ( $i * 2 ) - 2) );
			$style .= 'px}';
			
			/* Prefix */
			$style .= '.container_16 .prefix_' . $i . '{padding-left:';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
			
			/* Suffix */
			$style .= '.container_16 .suffix_' . $i . '{padding-right:';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
			
			/* Push */
			$style .= '.container_16 .push_' . $i . '{left:';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
			
			/* Pull */
			$style .= '.container_16 .pull_' . $i . '{left:-';
			$style .= ( $grid * $i ) + ( $gutter * ( $i * 2 ) );
			$style .= 'px}';
		}
		
		/* Header image positioning */
		$style .= '.header-img {margin-left: -' . $container / 2 . 'px;}';
	}
	
	/* Custom column width - one-column mode */
	if ( strpos( $column_mode, 'one_col' ) === 0 && ( $container != $graphene_defaults['container_width'] ) ){
		$content = $container - $gutter * 2;
		
		$style .= '.one-column .comment-form-author, .one-column .comment-form-email, .one-column .comment-form-url {width:' . ( ( $content - $gutter * 6 ) / 3 ). 'px}';
		$style .= '.one-column .graphene-form-field {width:' . ( ( ( $content - $gutter * 6 ) / 3 ) - 8 ) . 'px}';
		$style .= '.one-column #commentform textarea {width:' . ( ( $content - $gutter * 2 ) - 8 ) . 'px}';
	}
	
	/* Custom column width - two-column mode */
	$content = $graphene_settings['column_width']['two_col']['content'];
	$content_default = $graphene_defaults['column_width']['two_col']['content'];
	
	if ( strpos( $column_mode, 'two_col' ) === 0 && ( $content != $content_default ) ){
		$sidebar = $graphene_settings['column_width']['two_col']['sidebar'];

		$style .= '#content-main, #content-main .grid_11, .container_16 .slider_post, #comments #respond {width:' . $content . 'px}';
		$style .= '#sidebar1, #sidebar2 {width:' . $sidebar . 'px}';
		$style .= '.comment-form-author, .comment-form-email, .comment-form-url {width:' . ( ( $content - $gutter * 6 ) / 3 ). 'px}';
		$style .= '.graphene-form-field {width:' . ( ( ( $content - $gutter * 6 ) / 3 ) - 8 ) . 'px}';
		$style .= '#commentform textarea {width:' . ( ( $content - $gutter * 2 ) - 8 ) . 'px}';
	}
	
	/* Custom column width - three-column mode */
	$content = $graphene_settings['column_width']['three_col']['content'];
	$sidebar_left = $graphene_settings['column_width']['three_col']['sidebar_left'];
	$sidebar_right = $graphene_settings['column_width']['three_col']['sidebar_right'];
	$content_default = $graphene_defaults['column_width']['three_col']['content'];
	$sidebar_left_default = $graphene_defaults['column_width']['three_col']['sidebar_left'];
	$sidebar_right_default = $graphene_defaults['column_width']['three_col']['sidebar_right'];
	
	if ( strpos( $column_mode, 'three_col' ) === 0 && ( $content != $content_default || $sidebar_left != $sidebar_left_default || $sidebar_right != $sidebar_right_default ) ){

		$style .= '#content-main, #content-main .grid_8, .container_16 .slider_post, #comments #respond {width:' . $content . 'px}';
		$style .= '#sidebar1 {width:' . $sidebar_right . 'px}';
		$style .= '#sidebar2 {width:' . $sidebar_left . 'px}';
		$style .= '.three-columns .comment-form-author, .three-columns .comment-form-email, .three-columns .comment-form-url {width:' . ( ( $content - $gutter * 6 ) / 3 ). 'px}';
		$style .= '.three-columns .graphene-form-field {width:' . ( ( ( $content - $gutter * 6 ) / 3 ) - 8 ) . 'px}';
		$style .= '.three-columns #commentform textarea {width:' . ( ( $content - $gutter * 2 ) - 8 ) . 'px}';
	}
	
	return apply_filters( 'graphene_custom_column_width_style', $style );
}
 

/**
 * Sets the various customised styling according to the options set for the theme.
 *
 * @param bool $out Whether to echo the styles or not
 * @param bool $minify Whether to minify the styles or not
 * @param bool $force_all If set to true, it returns the full generated CSS as it will be in the front end
 *
 * @package Graphene
 * @since Graphene 1.0.8
*/
function graphene_custom_style( $echo = true, $minify = true, $force_all = false ){
	if ( ! is_bool( $echo ) ) $echo = true;
	global $graphene_settings;
	
	if ( $graphene_settings['disable_css_generate'] && ! is_admin() ) return;
	
	$style = '';
		
	// the custom colours are needed in both the display and admin mode
	$style .= graphene_get_custom_colours( '', $force_all );
	
	// only get the custom css styles when were not in the admin mode
	if ( ! is_admin() || $force_all ) {
		$style .= graphene_get_custom_style();
	
		// always the custom css at the end, this is the most important
		if ( $graphene_settings['custom_css'] ) { $style .= $graphene_settings['custom_css']; }
	}
	
	if ( $minify ) $style = graphene_minify_css( $style );
	
	$style = apply_filters( 'graphene_custom_style', $style, $echo, $minify, $force_all );
	
    if ( $style && $echo ) echo '<style type="text/css">' . "\n" . $style . "\n" . '</style>' . "\n";
	else return $style;
}
add_action( 'wp_head', 'graphene_custom_style' );


/**
 * Check to see if there's a favicon.ico in wordpress root directory and add
 * appropriate head element for the favicon
*/
function graphene_favicon(){
	global $graphene_settings;
	if ( $graphene_settings['favicon_url'] ) { ?>
		<link rel="icon" href="<?php echo $graphene_settings['favicon_url']; ?>" type="image/x-icon" />
	<?php
    } elseif ( is_file( ABSPATH . 'favicon.ico' ) ){ ?>
		<link rel="icon" href="<?php echo home_url(); ?>/favicon.ico" type="image/x-icon" />
	<?php }
}
add_action( 'wp_head', 'graphene_favicon' );


/**
 * Add the .htc file for partial CSS3 support in Internet Explorer
*/
function graphene_ie_css3(){ ?>
	<!--[if lte IE 7]>
      <style type="text/css" media="screen">
      	#footer, div.sidebar-wrap, .block-button, .featured_slider, #slider_root, #nav li ul, .pie{behavior: url(<?php echo get_template_directory_uri(); ?>/js/PIE.php);}
        .featured_slider{margin-top:0 !important;}
        #header-menu-wrap {z-index:5}
      </style>
    <![endif]-->
    <?php
}
add_action( 'wp_head', 'graphene_ie_css3' );


/**
 * Fix IE8 image scaling issues when using max-width property on images
*/
function graphene_ie8_img(){ ?>
	<!--[if IE 8]>
    <script type="text/javascript">
        (function( $) {
            var imgs, i, w;
            var imgs = document.getElementsByTagName( 'img' );
            maxwidth = 0.98 * $( '.entry-content' ).width();
            for( i = 0; i < imgs.length; i++ ) {
                w = imgs[i].getAttribute( 'width' );
                if ( w > maxwidth ) {
                    imgs[i].removeAttribute( 'width' );
                    imgs[i].removeAttribute( 'height' );
                }
            }
        })(jQuery);
    </script>
    <![endif]-->
<?php
}
add_action( 'wp_footer', 'graphene_ie8_img' );


/**
 * Add Google Analytics code if tracking is enabled 
 */ 
function graphene_google_analytics(){
	global $graphene_settings;
    if ( $graphene_settings['show_ga'] ) : ?>
    <!-- BEGIN Google Analytics script -->
    	<?php echo stripslashes( $graphene_settings['ga_code'] ); ?>
    <!-- END Google Analytics script -->
    <?php endif; 
}
add_action( 'wp_head', 'graphene_google_analytics', 1000);


/**
 * This function prints out the title for the website.
 * If present, the theme will display customised site title structure.
*/
function graphene_title( $title, $sep = '&raquo;', $seplocation = '' ){
	global $graphene_settings;
	$default_title = $title;
	
	if ( is_feed() ){
		
		$title = $default_title;
		
	} elseif ( is_front_page() ) { 
	
		if ( $graphene_settings['custom_site_title_frontpage'] ) {
			$title = $graphene_settings['custom_site_title_frontpage'];
			$title = str_replace( '#site-name', get_bloginfo( 'name' ), $title );
			$title = str_replace( '#site-desc', get_bloginfo( 'description' ), $title );
		} else {
			$title = get_bloginfo( 'name' );
			$title .= ( $desc = get_bloginfo( 'description' ) ) ? " &raquo; " . $desc : '';
		}
		
	} else {
		
		if ( $graphene_settings['custom_site_title_content'] ) {
			$title = $graphene_settings['custom_site_title_content'];
			$title = str_replace( '#site-name', get_bloginfo( 'name' ), $title );
			$title = str_replace( '#site-desc', get_bloginfo( 'description' ), $title );
			$title = str_replace( '#post-title', $default_title, $title );
		} else {
			$title = $default_title . " &raquo; " . get_bloginfo( 'name' );
		}
	}
	
	return ent2ncr( apply_filters( 'graphene_title', trim( $title ) ) );
}
add_filter( 'wp_title', 'graphene_title', 10, 3 );


/**
 * Prints out custom <head> tags
 *
 * @package Graphene
 * @since Graphene 1.8
 */
function graphene_custom_head_tags(){
	global $graphene_settings;
	echo $graphene_settings['head_tags'];
}
add_action( 'wp_head', 'graphene_custom_head_tags', 100 );
