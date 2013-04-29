<?php 
/**
*PopularWidgetFunctions
*
*@Popular Widget
*@author Hafid Trujillo
*@copyright 20010-2011
*@since 1.0.1
*/

class PopularWidgetFunctions extends WP_Widget { 
	
	/**
	 * Constructor
	 *
	 * @return void
	 * @since 0.5.0
	 */
	function PopularWidgetFunctions( ){ 
	
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
		$words = explode( " ", strip_shortcodes( $string ) );
		
		if( ( str_word_count( $string ) ) > $word_limit ) 
			return implode( " ",array_splice( $words, 0, $word_limit ) )."...";
		
		else return implode( " ",array_splice( $words, 0, $word_limit ) );  
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
		
		if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post_id ) )
			return get_the_post_thumbnail( $post_id, $size ) ; 
					
		$images = get_children ( array( 
			'order' => 'ASC',
			'numberposts' => 1,
			'orderby' => 'menu_order',
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
		 ) );
		
		if( empty( $images ) ) 
			return false;
			
		foreach( $images as $image )
			return wp_get_attachment_image( $image->ID, $size );
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
			
			global $wpdb; 
			$join = $output = $where = '';
	
			if( !empty( $cats ) ){ 
				$join = 
				"INNER JOIN $wpdb->term_relationships tr ON c.comment_post_ID = tr.object_id
				INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id 
				INNER JOIN $wpdb->terms t ON tt.term_id = t.term_id ";
				$where = " AND t.term_id IN ( ". trim( $cats, ',' ) ." )"; 
			 }
			
			if( !empty( $userids ) )
				$where .= " AND user_id IN ( ". trim( $userids, ',' ) ." )"; 
				
			$join = apply_filters( 'pop_comments_join', $join, $this->instance );
			$where = apply_filters( 'pop_comments_where', $where, $this->instance );
		
			$comments = $wpdb->get_results( 
				"SELECT DISTINCT comment_content,comment_ID,comment_author,user_id,comment_author_email,comment_date
				FROM $wpdb->comments c $join WHERE comment_date >= '{$this->time}' AND comment_approved = 1 AND comment_type = '' 
				$where GROUP BY comment_ID ORDER BY comment_date DESC LIMIT $limit"
			 );
			 
			wp_cache_set( "pop_comments_{$number}", $comments, 'pop_cache' );
		 }
		
		$count = 1;
		
		foreach( $comments as $comment ){ 
		
			$comment_author = ( $comment->comment_author ) ? $comment->comment_author : "Anonymous";
			$title = ( $tlength && ( strlen( $comment_author ) > $tlength ) ) 
			? mb_substr( $comment_author,0,$tlength ) . " ..." : $comment_author;
			
			$output .= '<li><a href="'.get_comment_link( $comment->comment_ID ).'" rel="bookmark">';
			
			if( !empty( $thumb ) )
				$image = get_avatar( $comment->comment_author_email, 100 ); 
			
			$output .= isset( $image ) ? $image . '<div class="pop-overlay">' : '<div class="pop-text">';
			$output .= '<span class="pop-title">'.$title.'</span> ';
			
			if( !empty( $excerpt ) ){ 
				if( $comment->comment_content && $excerptlength ) 
					$output .= '<p>'.self::limit_words( wp_strip_all_tags( $comment->comment_content ),$excerptlength ).'</p>';
				else $output .= '<p>'.self::limit_words( wp_strip_all_tags( $comment->comment_content ),$excerptlength ).'</p>';
			 }
			
			$output .= '</div></a><div class="pop-cl"></div></li>'; $count++;
		 }
		
		return $output .= ( $count >1 ) ? '' : '<li></li>' ;
	 }
	
	/**
	 *get commented results
	 *
	 *@return void
	 *@since 1.0.0
	*/
	function get_most_commented( ){ 
		
		extract( $this->instance );
		$commented = wp_cache_get( "pop_commented_{$number}", 'pop_cache' );
		
		if( $commented == false ){ 
			
			global $wpdb; 
			$join = $output = $where = '';
	
			if( !empty( $cats ) ){ 
				$join = 
				"INNER JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
				INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id 
				INNER JOIN $wpdb->terms t ON tt.term_id = t.term_id ";
				$where = " AND t.term_id IN ( " . trim( $cats, ',' ) . " )"; 
			 }
			
			if( !empty( $userids ) )
				$where .= " AND post_author IN ( ". trim( $userids, ',' ) ." )"; 
			
			$join = apply_filters( 'pop_commented_join', $join, $this->instance );
			$where = apply_filters( 'pop_commented_where', $where, $this->instance );
			
			$commented = $wpdb->get_results( 
				"SELECT DISTINCT comment_count,ID,post_title,post_content,post_excerpt,post_date 
				FROM $wpdb->posts p $join WHERE post_date >= '{$this->time}' AND post_status = 'publish' AND comment_count != 0 
				AND post_type IN ( $types ) $where GROUP BY ID ORDER BY comment_count DESC LIMIT $limit"
			 );
			wp_cache_set( "pop_commented_{$number}", $commented, 'pop_cache' );
		 }
		
		$count = 1;
		
		foreach( $commented as $post ){ 
			
			$title = ( $tlength && ( strlen( $post->post_title ) > $tlength ) ) 
			? mb_substr( $post->post_title, 0, $tlength ) . " ..." : $post->post_title;
			
			$output .= '<li><a href="'.get_permalink( $post->ID ).'" rel="bookmark">';
			
			if( !empty( $thumb ) ) 
				$image = self::get_post_image( $post->ID, $imgsize );
				
			$output .= isset( $image ) ? $image.'<div class="pop-overlay">':'<div class="pop-text">';
			$output .= '<span class="pop-title">'.$title.'</span> ';
			
			if( !empty( $counter ) )
				$output .= '<span class="pop-count">( '.preg_replace( "/(?<=\d)(?=(\d{3})+(?!\d))/"," ", $post->comment_count ).' )</span>';
			
			if( !empty( $excerpt ) ){ 
				if( $post->post_excerpt && $excerptlength ) 
					$output .= '<p>'.self::limit_words( wp_strip_all_tags( $post->post_content ), $excerptlength ) . '</p>';
				else $output .= '<p>'. self::limit_words( wp_strip_all_tags( $post->post_content ), $excerptlength ) . '</p>';
			 }
			
			$output .= '</div></a><div class="pop-cl"></div></li>'; $count++;
		 }
		
		return $output .= ( $count >1 ) ? '' : '<li></li>' ;
		
	 }
	
	/**
	 *get viewed results
	 *
	 *@return void
	 *@since 1.0.0
	*/
	function get_most_viewed( ){ 
		
		extract( $this->instance );
		$viewed = wp_cache_get( "pop_viewed_{$number}", 'pop_cache' );
		
		if( $viewed == false ) { 
			
			global $wpdb; 
			$join = $output = $where = '';
			
			if( !empty( $cats ) ){ 
				$join = 
				"INNER JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
				INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id 
				INNER JOIN $wpdb->terms t ON tt.term_id = t.term_id ";
				$where = " AND t.term_id IN ( " . trim( $cats, ',' ) . " )"; 
			 }
			
			if( !empty( $userids ) )
				$where .= " AND post_author IN ( ". trim( $userids, ',' ) ." )"; 
				
			$join = apply_filters( 'pop_viewed_join', $join, $this->instance );
			$where = apply_filters( 'pop_viewed_where', $where, $this->instance );
			
			$viewed = $wpdb->get_results( 
				"SELECT ID,post_title,post_date,post_content,post_excerpt,meta_value as views
				FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON p.ID = pm.post_id $join
				WHERE meta_key = '_popular_views' AND meta_value != '' AND post_date >= '{$this->time}'
				AND post_status = 'publish' AND post_type IN ( $types ) $where 
				GROUP BY p.ID ORDER BY ( meta_value+0 ) DESC LIMIT $limit"
			 );
			 
			wp_cache_set( "pop_viewed_{$number}", $viewed, 'pop_cache' );
		 }
		
		$count=1;
		foreach( $viewed as $post ){ 
		
			$title = ( $tlength && ( strlen( $post->post_title ) > $tlength ) ) 
			? mb_substr( $post->post_title, 0, $tlength ) . " ..." : $post->post_title;
			
			$output .= '<li><a href="'.get_permalink( $post->ID ).'" rel="bookmark">';
					
			if( !empty( $thumb ) ) 
				$image = self::get_post_image( $post->ID, $imgsize );
			
			$output .= isset( $image ) ? $image.'<div class="pop-overlay">':'<div class="pop-text">';
			$output .= '<span class="pop-title">'.$title.'</span> ';
			
			if( !empty( $counter ) )
				$output .= '<span class="pop-count">( ' . preg_replace( "/(?<=\d)(?=(\d{3})+(?!\d))/",",",$post->views ) . ' )</span>';
			if( !empty( $excerpt ) ){ 
				if( $post->post_excerpt && $excerptlength ) 
					$output .= '<p>' . self::limit_words( wp_strip_all_tags( $post->post_content ),$excerptlength ).'</p>';
				else $output .= '<p>' . self::limit_words( wp_strip_all_tags( $post->post_content ),$excerptlength ).'</p>';
			 }
			$output .= '</div></a><div class="pop-cl"></div></li>'; $count++;
		 }
		 
		return $output .= ( $count >1 ) ? '' : '<li></li>' ;
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

			$posts = get_posts( array( 
				'suppress_fun' => true,
				'post_type' => $post_types,
				'posts_per_page' => $limit,
				'cat' => trim( $cats, ',' ),
				'author' => trim( $userids, ',' )
			 ) );
			 
			wp_cache_set( "pop_recent_{$number}", $posts, 'pop_cache' );
		 }

		$output = '';
		foreach( $posts as $key => $post ){ 
			$title = ( $tlength && ( strlen( $post->post_title ) > $tlength ) ) 
			? mb_substr( $post->post_title, 0, $tlength ) . " ..." : $post->post_title;
			$output .= '<li><a href="'. get_permalink( $post->ID ). '" rel="bookmark">';
			
			if( !empty( $thumb ) ) 
				$image = self::get_post_image( $post->ID, $imgsize );
			
			$output .= isset( $image ) ? $image . '<div class="pop-overlay">':'<div class="pop-text">';
			$output .= '<span class="pop-title">'.$title.'</span> ';
			
			if( !empty( $excerpt ) ){ 
				if( $post->post_excerpt && $excerptlength ) 
					$output .= '<p>' . self::limit_words( wp_strip_all_tags( $post->post_content ), $excerptlength ).'</p>';
				else $output .= '<p>' . self::limit_words( wp_strip_all_tags( $post->post_content ), $excerptlength ).'</p>';
			 }
			$output .= '</div></a><div class="pop-cl"></div></li>';
		 }
		return $output ;
	 }
 }