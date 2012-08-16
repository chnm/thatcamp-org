<?php 
/*
Plugin Name: Popular Widget
Plugin URI: http://xparkmedia.com/plugins/popular-widget/
Description: Display most viewed, most commented and tags in one widget (with tabs)
Author: Hafid R. Trujillo Huizar
Version: 1.5.4
Author URI: http://www.xparkmedia.com
Requires at least: 3.0.0
Tested up to: 3.4.0

Copyright 2011-2012 by Hafid Trujillo http://www.xparkmedia.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License,or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not,write to the Free Software
Foundation,Inc.,51 Franklin St,Fifth Floor,Boston,MA 02110-1301 USA
*/

// Stop direct access of the file
if( !defined( 'ABSPATH' ) ) 
	die( );

include( "include.php" );

class PopularWidget extends PopularWidgetFunctions {
	
	/**
	 * Constructor
	 *
	 * @return void
	 * @since 0.5.0
	 */
	function PopularWidget( ){
		
		$this->tabs = array();
		$this->version = "1.5.4";
		$this->domain  = "pop-wid";
		
		parent::PopularWidgetFunctions( ); 

		define( 'POPWIDGET_FOLDER', plugin_basename( dirname( __FILE__ ) ) );
		define( 'POPWIDGET_URL', WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . "/" );
		define( 'POPWIDGET_ABSPATH', str_replace("\\","/", dirname( __FILE__ ) ) );
		
		$this->load_text_domain( );
		
		$this->defaults = apply_filters( 'pop_defaults_settings', array(
			'nocomments' => false, 'nocommented' => false, 'noviewed' => false, 'norecent' => false,
			'imgsize' => 'thumbnail', 'counter' => false, 'excerptlength' => 15, 'tlength' => 20, 'userids' => false,
			'calculate' => 'visits', 'title' => '', 'limit'=> 5, 'cats'=>'', 'lastdays' => 90, 'taxonomy' => 'post_tag',
			'posttypes' => array( 'post' => 'on' ), 'thumb' => false, 'excerpt' => false, 'notags'=> false,
		) );
		
		$this->tabs = apply_filters( 'pop_defaults_tabs', array(
			 'recent' =>  __( '<span>Recent </span> Posts', $this->domain ) , 
			 'comments' => __( '<span>Recent </span>Comments', $this->domain ) , 
			 'commented' => __( '<span>Most </span>Commented', $this->domain ), 
			 'viewed' => __( '<span>Most </span>Viewed', $this->domain ), 
			 'tags' => __( 'Tags', $this->domain ) 
		 ) );
		 
		add_action( 'template_redirect',array( &$this,'set_post_view') );
		add_action( 'admin_print_styles',array(&$this,'load_admin_styles') );
		add_action( 'wp_enqueue_scripts',array(&$this,'load_scripts_styles') );
		
		$widget_ops = array( 'classname' => 'popular-widget', 'description' => __( "Display most popular posts and tags", $this->domain ));
		$this->WP_Widget( 'popular-widget', __( 'Popular Widget',$this->domain ), $widget_ops );
	}
	
	/**
	* Register localization/language file
	*
	* @return void
	* @since 0.5.0 
	*/
	function load_text_domain(){
		$locale 	= get_locale( );
		$filedir 	= POPWIDGET_ABSPATH . '/langs/'. $this->domain . '-' . $locale . '.mo';
		
		if( function_exists( 'load_plugin_textdomain' ) )
			load_plugin_textdomain( $this->domain, false, apply_filters( 'pop_load_textdomain', POPWIDGET_FOLDER . '/langs', $this->domain , $locale ) );
		elseif( function_exists( 'load_textdomain' ) )
			load_textdomain( $this->domain, apply_filters( 'pop_load_textdomain', $filedir, $this->domain , $locale ) );
	}
	
	/**
	 * Load backend js/css
	 *
	 * @return void
	 * @since 1.2.0
	 */
	function load_admin_styles(){
		global $pagenow;
		if( $pagenow != 'widgets.php' ) return;
		wp_enqueue_style( 'popular-admin', POPWIDGET_URL . '_css/admin.css', NULL, $this->version );
		wp_enqueue_script( 'popular-admin', POPWIDGET_URL . '_js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, true ); 
	}
	
	/**
	 * Load frontend js/css
	 *
	 * @return void
	 * @since 0.5.0 
	 */
	function load_scripts_styles(){
		if( is_admin() || !is_active_widget( false, false, $this->id_base, true ) ) return;
		wp_enqueue_style( 'popular-widget', POPWIDGET_URL.'_css/pop-widget.css', NULL, $this->version );
		wp_enqueue_script( 'popular-widget', POPWIDGET_URL . '_js/pop-widget.js', array('jquery'), $this->version, true ); 	
	}
	
	/**
	 * Display widget field id
	 *
	 * @return void
	 * @since 1.5.0
	 */
	function field_id( $field ){
		echo  $this->get_field_id( $field );
	}
	
	/**
	 * Display widget field name
	 *
	 * @return void
	 * @since 1.5.0
	 */
	function field_name( $field ){
		echo  $this->get_field_name( $field );
	}
	
	/**
	 * Add postview count.
	 *
	 * @return void
	 * @since 0.5.0
	 */
	function set_post_view( ) {
		
		if( !is_single() && !is_page() && !is_singular() ) 
			return;
		
		$widgets = get_option($this->option_name);
		if( empty( $widgets[$this->number]) ) return;	
		
		global $post;
		$instance = $widgets[$this->number];
		
		if( $instance['calculate'] == 'visits' ){
			
			if( !isset( $_COOKIE['popular_views_'.COOKIEHASH] ) ){
				setcookie( 'popular_views_' . COOKIEHASH, "$post->ID|", 0, COOKIEPATH );
				update_post_meta( $post->ID, '_popular_views', get_post_meta( $post->ID, '_popular_views', true)+1 );
			}else{
				$views = explode( "|", $_COOKIE['popular_views_' . COOKIEHASH] );
				foreach( $views as $post_id ){ 
					if( $post->ID == $post_id ) {
						$exist = true; break;
					}
				}
			}
			
			if( empty( $exist ) ){
				$views[] = $post->ID;
				setcookie( 'popular_views_' . COOKIEHASH, implode( "|", $views ),0 , COOKIEPATH );
			}
			
		} else update_post_meta( $post->ID, '_popular_views', get_post_meta( $post->ID, '_popular_views', true )+1 );
	}
	
	/**
	 * Configuration form.
	 *
	 * @param array $instance
	 * @return void
	 * @since 0.5.0
	 */
	function form( $instance ) {
	
		$this->tabs = ( empty( $instance['order'] ) ) 
		? $this->tabs : $instance['order'];
				
		$instance = wp_parse_args( $instance, $this->defaults );
		extract( $instance );
		
		$post_types = get_post_types(array('public'=>true),'names','and');
		?>
		
		<p>
	 		<label for="<?php $this->field_id( 'title') ?>"><?php _e( 'Title', $this->domain ) ?> 
				<input class="widefat" id="<?php $this->field_id( 'title') ?>" name="<?php $this->field_name( 'title' ) ?>" type="text" value="<?php echo esc_attr( $title ) ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php $this->field_id( 'lastdays') ?>"><?php _e('In the last',$this->domain)?> 
				<input id="<?php $this->field_id( 'lastdays' )?>" name="<?php $this->field_name( 'lastdays' )?>" size="4" type="text" value="<?php echo esc_attr( $lastdays ) ?>"/> 
				<?php _e( 'days',$this->domain )?>
			</label>
		</p>
		
		<p>
			<label for="<?php $this->field_id( 'limit' )?>"><?php _e( 'Show how many posts?', $this->domain )?> 
				<input id="<?php $this->field_id( 'limit' )?>" name="<?php $this->field_name('limit')?>" size="5" type="text" value="<?php echo esc_attr( $limit ) ?>"/>
			</label>
		</p>
		
		<p>
			<label for="<?php $this->field_id( 'userid' )?>"><?php _e( 'Filter by user id', $this->domain )?> 
				<input  class="widefat" id="<?php $this->field_id( 'userids' )?>" name="<?php $this->field_name('userids')?>" size="20" type="text" value="<?php echo esc_attr( $userids ) ?>"/>
			</label><br /><small><?php _e( 'comma-separated user IDs', $this->domain )?> </small>
		</p>
		
		<p>
			<label for="<?php $this->field_id( 'cats' )?>"><?php _e( 'In categories', $this->domain )?> 
				<input  class="widefat" id="<?php $this->field_id( 'cats' )?>" name="<?php $this->field_name( 'cats' )?>" size="20" type="text" value="<?php echo esc_attr( $cats ) ?>"/>
			</label><br /><small><?php _e( 'comma-separated category IDs', $this->domain )?> </small>
		</p>
		
		<p>
			<label for="<?php $this->field_id( 'imgsize' )?>"><?php _e('Image Size',$this->domain)?>
			<select id="<?php $this->field_id( 'imgsize' ) ?>" name="<?php $this->field_name( 'imgsize' ) ?>">
			<?php foreach( get_intermediate_image_sizes() as $size ):?>
				<option value="<?php echo $size?>" <?php selected( $size, $imgsize )?>><?php echo $size ?></option>
			<?php endforeach;?>
			</select>
			</label>
		</p>
		
		<p><label for="<?php  $this->field_id( 'taxonomy' )?>"><?php _e( 'Tags taxonomy' ,$this->domain)?>
		<select id="<?php $this->field_id( 'taxonomy' ); ?>" name="<?php $this->field_name(  'taxonomy' ); ?>">
			<?php foreach( get_taxonomies( array('public'=>true), 'names') as $tax => $taxname ):?>
				<option value="<?php echo $tax?>" <?php selected( $tax, $taxonomy )?>><?php echo $taxname ?></option>
			<?php endforeach;?>
		</select></label>
		</p>
		
		
		<h4 class="popw-collapse"><?php _e( 'Display:', $this->domain )?><span></span></h4>
		<div class="popw-inner">
			<p>
				<label for="<?php $this->field_id( 'counter' )?>">
					<input id="<?php $this->field_id( 'counter' )?>" name="<?php $this->field_name('counter')?>" type="checkbox" <?php checked( 'on', $counter ) ?> /> 
					<?php _e( 'Display count', $this->domain )?>
				</label><br />		
				
				<label for="<?php $this->field_id( 'thumb' )?>">
					<input id="<?php $this->field_id( 'thumb' )?>" name="<?php $this->field_name( 'thumb' )?>" type="checkbox" <?php checked( 'on', $thumb ) ?> /> 
					<?php _e( 'Display thumbnail', $this->domain )?>
				</label><br />
				
				<label for="<?php $this->field_id('excerpt')?>">
					<input id="<?php $this->field_id('excerpt')?>" name="<?php $this->field_name('excerpt')?>" type="checkbox" <?php checked( 'on', $excerpt ) ?> /> 
					<?php _e( 'Display post excerpt', $this->domain )?>
				</label>
			</p>
			
			<p>
				<label for="<?php $this->field_id( 'tlength' )?>"><?php _e( 'Title length', $this->domain )?> 
					<input id="<?php $this->field_id( 'tlength' )?>" name="<?php $this->field_name( 'tlength' )?>" size="4" type="text" value="<?php echo esc_attr( $tlength ) ?>"/> 
					<?php _e( 'characters', $this->domain )?>
				</label>
			</p>
			
			<p>
				<label for="<?php $this->field_id( 'excerptlength' )?>"><?php _e( 'Excerpt length', $this->domain )?> 
					<input id="<?php $this->field_id( 'excerptlength' )?>" name="<?php $this->field_name('excerptlength')?>" size="5" type="text" 
					value="<?php echo esc_attr( $excerptlength ) ?>"/> <?php _e( 'Words', $this->domain ) ?>
				</label>
			</p>
		
		</div>
		
		<h4 class="popw-collapse"><?php _e( 'Calculate:', $this->domain )?><span></span></h4>
		<div class="popw-inner">
			<p>
				<label for="<?php $this->field_id( 'calculate-views' )?>">
					<input id="<?php $this->field_id( 'calculate-views' )?>" name="<?php $this->field_name( 'calculate' )?>" value="views" type="radio" <?php checked( $calculate, 'views' ) ?> /> 
					<abbr title="Every time the user views the page"><?php _e( 'Views', $this->domain )?></abbr>
				</label> <br /><small><?php _e( 'Every time user views the post.', $this->domain ) ?></small><br />
				
				<label for="<?php $this->field_id( 'calculate-visits' )?>">
					<input id="<?php $this->field_id( 'calculate-visits' )?>" name="<?php $this->field_name('calculate')?>" value="visits" type="radio" <?php checked( $calculate, 'visits' ) ?> />
					<abbr title="Every time the user visits the site"><?php _e( 'Visits', $this->domain )?></abbr>
				</label><br /><small><?php _e( 'Calculate only once per visit.', $this->domain ) ?></small>
			</p>
		</div>
		
		<h4 class="popw-collapse"><?php _e( 'Post Types:',$this->domain )?><span></span></h4>
		<div class="popw-inner">
			<p>
				<?php foreach ( $post_types  as $post_type ) { ?>
				<label for="<?php $this->field_id( $post_type )?>">
					<input id="<?php $this->field_id( $post_type )?>" name="<?php $this->field_name( 'posttypes' ); echo "[$post_type]" ?>" type="checkbox" 
					<?php checked( false, empty( $posttypes[$post_type] )  ) ?> /> 
					<?php echo $post_type ?></label><br />
				<?php } ?>
			</p>
		</div>
		
		<h4 class="popw-collapse"><?php _e( 'Arrange / Disable:',$this->domain )?><span></span></h4>
		<div class="popw-inner popw-sortable">
			<p>
				<?php foreach( $this->tabs as $tab => $label ) { ?>
				<div class="sort-tabs">
					<label for="<?php $this->field_id( "no{$tab}" )?>"><a href="<?php echo "#$tab" ?>" class="rename" title="<?php _e( 'Rename tab', $this->domain ) ?>"><?php echo $label ?></a>
						<input id="<?php $this->field_id( "no{$tab}" )?>" name="<?php echo $this->field_name( "no{$tab}" )?>" type="checkbox"  <?php checked( ${"no{$tab}"}, 'on' ) ?> /> 
					</label>
					<span class="rename-<?php echo "$tab" ?>"><input name="<?php $this->field_name( 'order' ); echo "[$tab]" ?>" type="text" value="<?php echo esc_attr( $label ) ?>" class="widefat"/></span>
				</div>
				<?php } ?>
			</p>
		</div>
		
		<?php do_action( 'pop_admin_form' ) ?>

		<!--<a href="http://xparkmedia.com/popular-widget/"><?php _e('New! Popular Widget Pro',$this->domain)?></a>&nbsp; | &nbsp;-->
		<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8SJEQXK5NK4ES"><?php _e( 'Donate', $this->domain )?></a> 
		
		<?php
	}
	
	/**
	 * Display widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 * @since 0.5.0
	 */
	function widget( $args, $instance ) {
		
		global $wpdb;
		$this->tabs = ( empty( $instance['order'] ) ) 
		? $this->tabs :  $instance['order'];
		
		$this->args = $args;
		$this->instance = wp_parse_args( $instance, $this->defaults );
		$this->instance['excerptlength'] = (int)$this->instance['excerptlength'];

		extract( $this->args ); extract( $this->instance ); 
		
		foreach( $posttypes as $type => $val ) 
			$types_array[] = "'$type'";
		 
		$this->instance['number'] = $this->number;
		$this->instance['types'] = implode( ',', $types_array );
		$this->time = date( 'Y-m-d H:i:s', strtotime( "-{$lastdays} days", current_time('timestamp') ) );
		
		$disabled_tabs = 0;
		foreach( array( 'nocomments', 'nocommented', 'noviewed', 'norecent', 'notags') as $disabled )
			if( empty( $this->instance[$disabled] ) ) $disabled_tabs ++;
		
		//start widget
		$output  = $before_widget ."\n";
		if( $title ) $output  .= $before_title. $title . $after_title . "\n";
		
		$output .= '<div class="pop-layout-v">';
		
		$output .= '<ul id="pop-widget-tabs-'.$this->number.'" class="pop-widget-tabs pop-widget-tabs-'. $disabled_tabs .'" >';
		foreach( $this->tabs as $tab => $label ) { 
			if( ${"no{$tab}"} != 'on' ) 
				$output .= '<li><a href="#' . $tab . '" rel="nofollow">' . $label  . '</a></li>';
		}
		$output .= '</ul>';
		
		$output .= '<div class="pop-inside-'. $this->number.' pop-inside">';
		
		foreach( $this->tabs as $tab => $label ) { 
			if( ${"no{$tab}"} != 'on' ){
				if(  $tab != 'tags' ) $output .= '<ul id="pop-widget-'.$tab.'-'.$this->number.'">';
				
				switch( $tab ){
					case 'recent':
						$output .= $this->get_recent_posts( );
						break;
					case 'comments':
						$output .= $this->get_comments( );
						break;
					case 'commented':
						$output .= $this->get_most_commented( );
						break;
					case 'viewed':
						$output .= $this->get_most_viewed(  );
						break;
					case 'tags':
						$output .= wp_tag_cloud( 
							apply_filters( 'pop_tag_cloud', array( 
								'smallest'=>'8', 'largest'=>'22', 'format'=>"list", 'echo'=>false, 'taxonomy' => $taxonomy 
							), $this->instance )
						);
						break;
					default:
						$output .= apply_filters( 'pop_tab_content', '', $tab, $label );
				}
				
				do_action( 'pop_after_tab_content', $tab, $label  );
				
				if(  $tab != 'tags' )  $output .= '</ul>';
			}
		}
		
		$output .= '</div><!--.pop-inside-->';
		
		$output .= '</div><!--.pop-layout-v-->';
		echo $output .=  $after_widget . "\n";
		
	}
		
}
add_action( 'widgets_init' , create_function( '', 'return register_widget("PopularWidget");' ) );