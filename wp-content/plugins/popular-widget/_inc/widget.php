<?php

	/**
	 * Popular Widget - Widget
	 *
	 * @file widget.php
	 * @package Popular Widget
	 * @author Hafid Trujillo
	 * @copyright 20010-2013
	 * @filesource  wp-content/plugins/popular-widget/_inc/widget.php
	 * @since 1.6.0
	 */
	
	global $wpdb;
		
	$this->tabs = ( empty( $instance['order'] ) ) 
	? $this->tabs : $instance['order'];
	
	$this->args = $args;
	$this->instance = wp_parse_args( $instance, $this->defaults );
	
	extract( $this->args ); extract( $this->instance ); 
	
	foreach( $posttypes as $type => $val ) 
		$types_array[] = "'$type'";
	
	$this->instance['number'] 	= $this->number;
	$this->instance['types'] 	= implode( ',', $types_array );
	
	if( empty( $this->instance['meta_key'] ) )
		$this->instance['meta_key'] = '_popular_views';
	
	$disabled_tabs = 0;
	$this->time = date( 'Y-m-d H:i:s', strtotime( "-{$lastdays} days", current_time( 'timestamp' ) ) );
	
	foreach( array( 'nocomments', 'nocommented', 'noviewed', 'norecent', 'notags' ) as $disabled )
		if( empty( $this->instance[$disabled] ) ) $disabled_tabs ++;
	
	//start widget
	$output  = $before_widget ."\n";
	if( $title ) $output  .= $before_title. $title . $after_title . "\n";
	
	$output .= '<div class="pop-layout-v">';
	
	//tabs
	$output .= '<ul id="pop-widget-tabs-' . esc_attr( $this->number ) . '" class="pop-widget-tabs pop-widget-tabs-'. $disabled_tabs .'" >';
	foreach( $this->tabs as $tab => $label ) 
		if( ${"no{$tab}"} != 'on' ) $output .= '<li><a href="' . esc_attr( "#{$tab}" ) . '" rel="nofollow">' . $label . '</a></li>';
	$output .= '</ul>';
	
	//tab content
	$output .= '<div class="pop-inside-' .  $this->number . ' pop-inside">';
	foreach( $this->tabs as $tab => $label ) { 
	
		$this->current_tab = $tab = sanitize_title( $tab );
		
		if( ${"no{$tab}"} != 'on' ){
			if(  $tab != 'tags' ) $output .= '<ul id="pop-widget-' . $tab . '-' . $this->number . '">';
			
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
					$output .= $this->get_tags(  );
					break;
				default:
					$output .= apply_filters( "pop_{$tab}_tab_content" );
			}
			
			if(  $tab != 'tags' )  $output .= '</ul>';
		}
	}
	$output .= '</div><!--.pop-inside-->';
	
	$output .= '</div><!--.pop-layout-v-->';
	echo $output .=  $after_widget . "\n";