<?php

  /**
   * Popular Widget - functions
   *
   * @file functions.php
   * @package Popular Widget
   * @author Hafid Trujillo
   * @copyright 20010-2013
   * @filesource  wp-content/plugins/popular-widget/functions.php
   * @since 1.6.0
   */

	class PopularWidgetFunctions extends WP_Widget { 
		
		/**
		 * Constructor
		 *
		 * @return void
		 * @since 0.5.0
		 */
		function PopularWidgetFunctions( ){ 
			//add_action( 'template_redirect',array( &$this,'set_post_view') );
			add_action( 'admin_print_styles',array( &$this,'load_admin_styles' ) );
			add_action( 'wp_enqueue_scripts',array( &$this,'load_scripts_styles' ) );
			add_action( 'wp_ajax_popwid_page_view_count', array( &$this,'set_post_view'));
			add_action( 'wp_ajax_nopriv_popwid_page_view_count', array( &$this,'set_post_view'));
		 }
		 
		 /**
		 * Load backend js/css
		 *
		 * @return void
		 * @since 1.2.0
		 */
		function load_admin_styles(){
			global $pagenow;

			if( ! in_array( $pagenow ,array( 'widgets.php', 'customize.php')) )
				return;
			
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
			
			if( ! is_admin() || is_active_widget( false, false, $this->id_base, true ) ) {
				wp_enqueue_style( 'popular-widget', POPWIDGET_URL . '_css/pop-widget.css', NULL, $this->version );
				wp_enqueue_script( 'popular-widget', POPWIDGET_URL . '_js/pop-widget.js', array('jquery'), $this->version, true ); 
			}
			
			if( ! is_singular() && ! apply_filters( 'pop_allow_page_view', false ) )
				return;
				
			global $post;
			wp_localize_script ( 'popular-widget', 'popwid', apply_filters ( 'pop_localize_script_variables', array(
				'postid' => $post->ID ,
				'ajaxurl' => admin_url( 'admin-ajax.php' ), 
			), $post ));			
		}
		
		/**
		 * Display widget field id
		 *
		 * @return void
		 * @since 1.5.0
		 */
		function field_id( $field ){
			echo $this->get_field_id( $field );
		}
		
		/**
		 * Display widget field name
		 *
		 * @return void
		 * @since 1.5.0
		 */
		function field_name( $field ){
			echo $this->get_field_name( $field );
		}
		
		/**
		 * Download language file
		 *
		 * @return void
		 * @since 1.5.6
		 */
		function download_language_file( $filedir = false ) {
			 _deprecated_function( __FUNCTION__, '1.6.0' );
			return;
		}
			 
		/**
		* Register localization/language file
		*
		* @return void
		* @since 0.5.0 
		*/
		function load_text_domain(){
			
			$locale = get_locale();
			if ( $locale  == 'en_US' || is_textdomain_loaded( 'popular-widget' ) )
				return;
	
			$filedir = WP_CONTENT_DIR . '/languages/' . 'popular-widget' . '-' . $locale  . '.mo';
		
			if ( function_exists( 'load_plugin_textdomain' ) )
				load_plugin_textdomain( 'popular-widget', false, apply_filters('pop_load_textdomain', '../languages/', 'popular-widget', $locale ) );
				
			elseif ( function_exists( 'load_textdomain' ) )
				load_textdomain( 'popular-widget', apply_filters('pop_load_textdomain', $filedir, 'popular-widget', $locale ) );
		}
		
		/**
		 * Limit the words in a string
		 *
		 * @param string $string
		 * @param unit $word_limit
		 * @return string
		 * @since 0.5.0
		 */
		function limit_words( $string, $word_limit ){ 
		
			$words = explode( " ", wp_strip_all_tags( strip_shortcodes( $string ) ));
			
			if( $word_limit && ( str_word_count( $string ) > $word_limit ) ) 
				return $output =  implode( " ",array_splice( $words, 0, $word_limit ) ) ."...";
				
			else if( $word_limit ) 
				return $output = implode( " ", array_splice( $words, 0, $word_limit ) );
				
			else return $string;
		 } 
		 
		 /**
		 * Get the first 
		 * image attach to the post
		 *
		 * @param unit $post_id
		 * @return string|void
		 * @since 1.0.0
		 */
		function get_post_image( $post_id, $size ){ 
			
			if ( has_post_thumbnail( $post_id ) 
			&& function_exists( 'has_post_thumbnail' ) )
				return get_the_post_thumbnail( $post_id, $size ); 
						
			$images = get_children (
			 apply_filters( 'pop_get_children_args', array( 
				'order' => 'ASC',
				'numberposts' => 1,
				'orderby' => 'menu_order',
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
			 ), $post_id, $size ) );
			
			if( empty( $images ) ) 
				return false;
				
			foreach( $images as $image )
				return wp_get_attachment_image( $image->ID, $size );
		 }
		
		/**
		 * Add postview count.
		 *
		 * @return void
		 * @since 0.5.0
		 */
		function set_post_view( ) {

			if( empty( $_POST['postid'] ) ) 
				return;
			
			//short circuit views count
			if( ! apply_filters( 'pop_set_post_view', true ) )
				return;
			
			global $wp_registered_widgets;
			
			$meta_key_old = false;
			$postid 	= ( int ) $_POST['postid'];
			$widgets = get_option( $this->option_name );
	
			foreach( (array) $widgets as $number => $widget ){
				if( ! isset( $wp_registered_widgets["popular-widget-{$number}"] ) )
					continue;
									
				$instance 		= $wp_registered_widgets["popular-widget-{$number}"];
				$meta_key	= isset( $instance['meta_key'] ) ? $instance['meta_key'] : '_popular_views';
				
				// avoid duplicate enties
				if( $meta_key_old == $meta_key )
					continue;
				
				do_action( 'pop_before_set_pos_view', $instance, $number );
				
				if( isset($instance['calculate'] ) && $instance['calculate']  == 'visits' ){
					
					if( ! isset( $_COOKIE['popular_views_'.COOKIEHASH] ) ){
						setcookie( 'popular_views_' . COOKIEHASH, "$postid|", 0, COOKIEPATH );
						update_post_meta( $postid, $meta_key, get_post_meta( $postid, $meta_key, true ) +1 );
					
					}else{
						
						$views = explode( "|", $_COOKIE['popular_views_' . COOKIEHASH] );
						foreach( $views as $post_id ){ 
							if( $postid == $post_id ) {
								$exist = true;  break;
							}
						}
					}
					
					if( empty( $exist ) ){
						$views[] = $postid;
						setcookie( 'popular_views_' . COOKIEHASH, implode( "|", $views ), 0 , COOKIEPATH );
						update_post_meta( $postid, $meta_key, get_post_meta( $postid, $meta_key, true ) +1 );
					}
					
				}else update_post_meta( $postid, $meta_key, get_post_meta( $postid, $meta_key, true ) +1 );
				
				$meta_key_old = $meta_key;
				
				do_action( 'pop_after_set_pos_view', $instance, $number );
				do_action( 'pop_after_set_post_view', $instance, $number );
			}
			die();
		}
		
		/**
		 *get recent results
		 *
		 *@return void
		 *@since 1.0.1
		*/
		function get_recent_posts( ){ 
		
			extract( $this->instance );
			$posts = wp_cache_get( "pop_recent_{$number}", 'pop_cache' );
			
			if( $posts == false ) { 
				foreach( $posttypes as $post => $v )
					if( $v == 'on' ) $post_types[] = $post;
				
				$args = array( 'suppress_fun' => true, 'post_type' => $post_types, 'posts_per_page' => $limit );
				
				if( $cats && $exclude_cats == 'on' ) $args['category__not_in'] = explode( ',', $cats );
				else if ( $cats ) $args['category__in'] = explode( ',', $cats );
				
				if( $userids && $exclude_users == 'on' ) $args['author'] = trim( "-". $userids, ',' );
				else if( $userids ) $args['author'] = trim( $userids, ',' );
							 
				$posts = get_posts( apply_filters( 'pop_get_recent_posts_args', $args) );
				wp_cache_set( "pop_recent_{$number}", $posts, 'pop_cache' );
			 }
			 
			return apply_filters( 'pop_recent_posts_content', 
				$this->display_post_tab_content( $posts ), $this->instance, $posts 
			);
		}
		
		/**
		 *get the latest comments
		 *
		 *@return void
		 *@since 1.0.0
		*/
		function get_comments( ){ 
		
			extract( $this->instance ); 
			$comments = wp_cache_get( "pop_comments_{$number}", 'pop_cache' );
			
			if( $comments == false ) { 
				global $wpdb;  $join = $where = '';
				
				//taxonomy filter
				if( !empty( $cats ) )
				$where = " AND ( c.comment_post_ID " . ( ( $exclude_cats == 'on' ) ? ' NOT IN ' : ' IN ' ) . 
				"( SELECT object_id FROM $wpdb->term_relationships tr " .
				"JOIN $wpdb->comments c ON c.comment_post_ID = tr.object_id " .
				"WHERE term_taxonomy_id IN ( " . esc_sql( trim( $cats, ',' ) ) . " ) ) ) ";
				
				//user filter
				if( !empty( $userids ) )
				$where .=  " AND c.user_id ". ( ( $exclude_cats == 'on' ) ? ' NOT IN ' : ' IN ' ) . " ( ". esc_sql( trim( $userids, ',' ) ) ." )"; 
				
				$join = apply_filters( 'pop_comments_join', $join, $this->instance );
				$where = apply_filters( 'pop_comments_where', $where, $this->instance );
		
				$comments = $wpdb->get_results( 
					"SELECT SQL_CALC_FOUND_ROWS c.* " .
					"FROM $wpdb->comments c $join 
					WHERE comment_date >= '{$this->time}' AND comment_approved = 1 AND comment_type = '' " . 
					"$where GROUP BY comment_ID ORDER BY comment_date DESC LIMIT $limit"
				 );
				wp_cache_set( "pop_comments_{$number}", $comments, 'pop_cache' );
			}
			return apply_filters( 'pop_most_comments_content', 
				$this->display_comment_tab_content( $comments ), $this->instance, $comments 
			);
		}
		
		/**
		 *Get commented results
		 *
		 *@return string
		 *@since 1.0.0
		*/
		function get_most_commented( ){ 
			
			extract( $this->instance );
			$commented = wp_cache_get( "pop_commented_{$number}", 'pop_cache' );
			
			if( $commented == false ){ 
				global $wpdb;  $join = $where = '';
				
				//taxonomy filter
				if( !empty( $cats ) )
					$where = " AND ( p.ID " . ( ( $exclude_cats == 'on' ) ? ' NOT IN ' : ' IN ' ) . 
					"( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ( " . esc_sql( trim( $cats, ',' ) ) . " ) ) ) ";
				
				//user filter
				if( !empty( $userids ) )
					$where .=  " AND c.user_id ". ( ( $exclude_cats == 'on' ) ? ' NOT IN ' : ' IN ' ) . " ( ". esc_sql( trim( $userids, ',' ) ) ." )"; 
				
				$join = apply_filters( 'pop_commented_join', $join, $this->instance );
				$where = apply_filters( 'pop_commented_where', $where, $this->instance );
				
				$commented = $wpdb->get_results( 
					"SELECT SQL_CALC_FOUND_ROWS p.*, p.comment_count views " . 
					"FROM $wpdb->posts p $join WHERE post_date >= '{$this->time}' AND post_status = 'publish' AND comment_count != 0 " . 
					"AND post_type IN ( $types ) $where GROUP BY ID ORDER BY comment_count DESC LIMIT $limit"
				);
			}
			return apply_filters( 'pop_most_commented_content', 
				$this->display_post_tab_content( $commented ), $this->instance, $commented 
			);
		}
		
		/**
		 *Get viewed results
		 *
		 *@return string
		 *@since 1.0.0
		*/
		function get_most_viewed( ){ 
			
			extract( $this->instance );
			$viewed = wp_cache_get( "pop_viewed_{$number}", 'pop_cache' );
			
			if( $viewed == false ) { 
				global $wpdb;  $join = $where = '';
				
				//taxonomy filter
				if( !empty( $cats ) )
				$where = " AND ( p.ID " . ( ( $exclude_cats == 'on' ) ? ' NOT IN ' : ' IN ' ) . 
				"( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ( " . esc_sql( trim( $cats, ',' ) ) . " ) ) ) ";
				
				//user filter
				if( !empty( $userids ) )
				$where .=  " AND post_author ". ( ( $exclude_cats == 'on' ) ? ' NOT IN ' : ' IN ' ) . " ( ". esc_sql( trim( $userids, ',' ) ) ." )"; 
				
				$join = apply_filters( 'pop_viewed_join', $join, $this->instance );
				$where = apply_filters( 'pop_viewed_where', $where, $this->instance );
				
				$viewed = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS p.*, meta_value as views FROM $wpdb->posts p " . 
				"JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND meta_key = %s AND meta_value != '' " .
				"WHERE 1=1 AND p.post_status = 'publish' AND post_date >= '{$this->time}' AND p.post_type IN ( $types ) $where " . 
				"GROUP BY p.ID ORDER BY ( meta_value+0 ) DESC LIMIT $limit", $meta_key) );
				
				wp_cache_set( "pop_viewed_{$number}", $viewed, 'pop_cache' );
			}
			return apply_filters( 'pop_most_viewed_content', $this->display_post_tab_content( $viewed ), $this->instance, $viewed );
		}
		
		
		/**
		 *Display tags content
		 *
		 *@return string
		 *@since 1.6.0
		*/
		function get_tags( ){
			extract( $this->instance );
			return wp_tag_cloud( apply_filters( 'pop_tag_cloud', array( 
				'smallest'=>'8', 'largest'=>'22', 'format'=>"list", 'echo'=>false, 'taxonomy' => $taxonomy 
			), $this->instance ) );
		}
		
		
		/**
		 *Display tab post content
		 *
		 *@return string
		 *@since 1.6.0
		*/
		function display_post_tab_content( $posts ){
			if( empty ( $posts ) && !is_array( $posts ) )
				return;
				
			$output = '';
			extract( $this->instance );
			
			foreach( $posts as $key => $post ){ 
				$output .= '<li><a href="'. esc_url( get_permalink( $post->ID ) ) . '" title="' . esc_attr( $post->post_title ) . '" rel="' . esc_attr( $rel ) . '">';
				
				$output .= apply_filters( "pop_{$this->current_tab}_before_post",  '', $post );

				//image
				if( !empty( $thumb ) )  $image = $this->get_post_image( $post->ID, $imgsize );
				$output .= isset( $image ) ? $image . '<span class="pop-overlay">' : '<span class="pop-text">';
				
				// title
				$output .= apply_filters( "pop_{$this->current_tab}_title", 
					'<span class="pop-title">'. $this->limit_words( $post->post_title, $tlength ) . '</span> ', $post 
				);
				
				// counter
				if( !empty( $counter ) && isset( $post->views ) )
					$output .= '<span class="pop-count">( ' . preg_replace( "/(?<=\d)(?=(\d{3})+(?!\d))/", ",", $post->views ) . ' )</span>';
				
				// excerpt
				if( !empty( $excerpt ) ){ 
					if( $post->post_excerpt )  
						$output .= '<span class="pop-summary">' . $this->limit_words( ( $post->post_excerpt ), $excerptlength ) . '</span>';
					else $output .= '<span class="pop-summary">' . $this->limit_words( ( $post->post_content ), $excerptlength ) . '</span>';
				 }
			 
				$output .= '</span>';
				
				$output .= apply_filters( "pop_{$this->current_tab}_after_post", '', $post );
				$output .= '</a><br class="pop-cl" /></li>';
			}
			return $output;
		}
		
		
		/**
		 *Display comment tab content
		 *
		 *@return string
		 *@since 1.6.0
		*/
		function display_comment_tab_content( $comments ){
			if( empty ( $comments ) && !is_array( $comments ) )
				return;
				
			$output = '';
			extract( $this->instance );
			
			foreach( $comments as $key => $comment ){ 
			
				$comment_author = ( $comment->comment_author ) ? $comment->comment_author : "Anonymous";
				
				$output .= '<li><a href="'. esc_url( get_comment_link( $comment->comment_ID ) ) . '" title="' . 
				esc_attr( $comment_author ) . '" rel="' . esc_attr( $rel ) . '">';
				
				//image
				if( !empty( $thumb ) )  $image = get_avatar( $comment->comment_author_email, 100 ); 
				$output .= isset( $image ) ? $image . '<span class="pop-overlay">' : '<span class="pop-text">';
				
				// title
				$output .= apply_filters( "pop_{$this->current_tab}_title", 
					'<span class="pop-title">'. $this->limit_words( $comment_author, $tlength ) . '</span> ', $comment 
				);
				
				// excerpt
				if( !empty( $excerpt ) )
				$output .= '<span class="pop-summary">' . $this->limit_words( ( $comment->comment_content ), $excerptlength ) . '</span>';
			 
				$output .= '</span></a><br class="pop-cl" /></li>';
			}
			return $output;
		}
	}