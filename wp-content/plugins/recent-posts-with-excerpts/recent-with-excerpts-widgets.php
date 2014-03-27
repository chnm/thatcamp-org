<?php
/*
Plugin Name: Recent Posts with Excerpts
Plugin URI: http://stephanieleary.com/code/wordpress/recent-posts-with-excerpts/
Donate link: http://stephanieleary.com/code/wordpress/recent-posts-with-excerpts/
Description: A widget that lists your most recent posts with excerpts. The number of posts and excerpts is configurable; for example, you could show five posts but include the excerpt for only the most recent. Supports <a href="http://robsnotebook.com/the-excerpt-reloaded/">The Excerpt Reloaded</a> and <a href="http://sparepencil.com/code/advanced-excerpt/">Advanced Excerpt</a>.
Version: 2.5.4
Author: Stephanie Leary
Author URI: http://stephanieleary.com

Copyright 2009  Stephanie Leary  (email : steph@sillybean.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// i18n
load_plugin_textdomain( 'recent_posts_with_excerpts', '', plugin_dir_path(__FILE__) . '/languages' );

class RecentPostsWithExcerpts extends WP_Widget {

	function RecentPostsWithExcerpts() {
			$widget_ops = array('classname' => 'recent_with_excerpt', 'description' => __( 'Your most recent posts, with optional excerpts', 'recent_posts_with_excerpts') );
			$this->WP_Widget('RecentPostsWithExcerpts', __('Recent Posts with Excerpts', 'recent_posts_with_excerpts'), $widget_ops);
	}
	
	
	function widget( $args, $instance ) {
			extract( $args );
			
			$title = apply_filters('widget_title', $instance['title']);
			
			echo $before_widget;
			if ( !empty($title) ) {
				if (!empty($instance['postlink']))  {
					if (get_option('show_on_front') == 'page')
						$link = get_permalink(get_option('page_for_posts'));
					else $link = get_permalink(get_option('home'));
					$before_title .= '<a href="'.$link.'">';
					$after_title .= '</a>';
				}
				echo $before_title.$title.$after_title;
			}
			
			$ul_classes = 'recent_posts_with_excerpts';
			$ul_classes = apply_filters('recent_posts_with_excerpts_list_classes', $ul_classes);
			if ( !empty( $ul_classes ) )
				$ul_classes = ' class="'.$ul_classes.'"';
			$li_classes = '';
			$li_classes = apply_filters('recent_posts_with_excerpts_item_classes', $li_classes);
			if ( !empty( $li_classes ) )
				$li_classes = ' class="'.$li_classes.'"';
			$h2_classes = 'recent_posts_with_excerpts';
			$h2_classes = apply_filters('recent_posts_with_excerpts_heading_classes', $h2_classes);
			if ( !empty( $h2_classes ) )
				$h2_classes = ' class="'.$h2_classes.'"';
			
			do_action('recent_posts_with_excerpts_begin');
			echo '<ul'.$ul_classes.'>';
			
			// retrieve last n blog posts
			$q = array('posts_per_page' => $instance['numposts']);
			if (!empty($instance['cat'])) 
				$q['cat'] = $instance['cat'];
			if (!empty($instance['tag'])) 
				$q['tag'] = $instance['tag'];
			$q = apply_filters('recent_posts_with_excerpts_query', $q);
			$rpwe = new wp_query($q);
			$excerpts = $instance['numexcerpts'];
			$date = apply_filters('recent_posts_with_excerpts_date_format', $instance['date']);
				  
			// the Loop
			if ($rpwe->have_posts()) :
			while ($rpwe->have_posts()) : $rpwe->the_post(); 
				echo '<li'.$li_classes.'>'; 
				if ($excerpts > 0 && $instance['thumb'] && $instance['thumbposition'] == 'above')
					echo '<a href="'.get_permalink().'">'. get_the_post_thumbnail( get_the_id(), $instance['thumbsize']) .'</a>';
				
                echo '<h2'.$h2_classes.'><a href="'.get_permalink().'">'.get_the_title().'</a></h2>';
				
				if (!empty($date)) 
					echo '<h3 class="date">'.get_the_time($date).'</h3>';
                
                if ($excerpts > 0) { // show the excerpt 
					if ($instance['thumb'] && $instance['thumbposition'] == 'between')
						echo '<a href="'.get_permalink().'">'. get_the_post_thumbnail( get_the_id(), $instance['thumbsize']) .'</a>';
					?>
                    <blockquote> <?php 
                    // the excerpt of the post
                    if (function_exists('the_excerpt_reloaded')) 
                        the_excerpt_reloaded($instance['words'], $instance['tags'], 'content', FALSE, '', '', '1', '');
                    else the_excerpt();  // this covers Advanced Excerpt as well as the built-in one
                    if (!empty($instance['more_text'])) { ?><p class="alignright"><small><a href="<?php the_permalink(); ?>"><?php echo $instance['more_text']; } ?></a></small></p>
                    </blockquote> <?php

					if ($excerpts > 0 && $instance['thumb'] && $instance['thumbposition'] == 'below')
						echo '<a href="'.get_permalink().'">'. get_the_post_thumbnail( get_the_id(), $instance['thumbsize']) .'</a>';
						
                    $excerpts--;
		        }?></li>
			<?php endwhile; endif; ?>
			</ul>
			<?php
			do_action('recent_posts_with_excerpts_end');
			echo $after_widget;
			wp_reset_query();
	}
	
	
	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['numposts'] = intval($new_instance['numposts']);
			$instance['numexcerpts'] = intval($new_instance['numexcerpts']);
			$instance['more_text'] = strip_tags($new_instance['more_text']);
			$instance['date'] = strip_tags($new_instance['date']);
			$instance['words'] = intval($new_instance['words']);
			$instance['tags'] = $new_instance['tags'];
			$instance['cat'] = intval($new_instance['cat']);
			$instance['tag'] = strip_tags($new_instance['tag']);
			$instance['postlink'] = $new_instance['postlink'];
			$instance['thumb'] = intval($new_instance['thumb']);
			$instance['thumbposition'] = esc_html($new_instance['thumbposition']);
			$instance['thumbsize'] = esc_attr($new_instance['thumbsize']);
			return $instance;
	}

	function form( $instance ) {
		if (get_option('show_on_front') == 'page')
				$link = get_permalink(get_option('page_for_posts'));
			else $link = get_permalink(get_option('home'));
			//Defaults
				$instance = wp_parse_args( (array) $instance, array( 
						'title' => __('Recent Posts', 'recent_posts_with_excerpts'),
						'numposts' => 5,
						'numexcerpts' => 5,
						'date' => get_option('date_format'),
						'more_text' => __('more...', 'recent_posts_with_excerpts'),
						'words' => '55',
						'tags' => '<p><div><span><br><img><a><ul><ol><li><blockquote><cite><em><i><strong><b><h2><h3><h4><h5><h6>',
						'cat' => 0,
						'tag' => '',
						'postlink' => $link,
						'thumb' => 0,
						'thumbposition' => 'above',
						'thumbsize' => ''));
			
	?>  
       
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'recent_posts_with_excerpts'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" /></p>
        
        
        <p>
        <label for="<?php echo $this->get_field_id('postlink'); ?>"><?php _e('Link widget title to blog home page?', 'recent_posts_with_excerpts'); ?></label>
        <input id="<?php echo $this->get_field_id('postlink'); ?>" name="<?php echo $this->get_field_name('postlink'); ?>" type="checkbox" <?php if ($instance['postlink']) { ?> checked="checked" <?php } ?> />
        </p>
        <p><label for="<?php echo $this->get_field_id('numposts'); ?>"><?php _e('Number of posts to show:', 'recent_posts_with_excerpts'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('numposts'); ?>" name="<?php echo $this->get_field_name('numposts'); ?>" type="text" value="<?php echo $instance['numposts']; ?>" /></p>
        
        <p>
        <p><label for="<?php echo $this->get_field_id('numexcerpts'); ?>"><?php _e('Number of excerpts to show:', 'recent_posts_with_excerpts'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('numexcerpts'); ?>" name="<?php echo $this->get_field_name('numexcerpts'); ?>" type="text" value="<?php echo $instance['numexcerpts']; ?>" /></p>
        
        <p>
        <label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('"More" link text:', 'recent_posts_with_excerpts'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" type="text" value="<?php echo $instance['more_text']; ?>" />
        <br /><small><?php _e('Leave blank to omit "more" link', 'recent_posts_with_excerpts'); ?></small>
        </p>

        <p>
        <label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date format:', 'recent_posts_with_excerpts'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="text" value="<?php echo $instance['date']; ?>" />
        <br /><small><?php _e('Leave blank to omit the date', 'recent_posts_with_excerpts'); ?></small>
        </p>

        <p><label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Limit to category:', 'recent_posts_with_excerpts'); ?>
        <?php wp_dropdown_categories(array('name' => $this->get_field_name('cat'), 'show_option_all' => __('None (all categories)'), 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>$instance['cat'])); ?></label></p>
        <p>
        <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Limit to tags:', 'recent_posts_with_excerpts'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo $instance['tag']; ?>" />
        <br /><small><?php _e('Enter post tags separated by commas ("cat,dog")', 'recent_posts_with_excerpts'); ?></small>
        </p>
        <?php
        if (function_exists('the_excerpt_reloaded')) { ?>
        	<p>
	        <label for="<?php echo $this->get_field_id('words'); ?>"><?php _e('Limit excerpt to how many words?', 'recent_posts_with_excerpts'); ?></label>
	        <input class="widefat" id="<?php echo $this->get_field_id('words'); ?>" name="<?php echo $this->get_field_name('words'); ?>" type="text" value="<?php echo $instance['words']; ?>" />
	        </p>
	        <p>
	        <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Allowed HTML tags:', 'recent_posts_with_excerpts'); ?></label>
	        <input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="text" value="<?php echo htmlspecialchars($instance['tags'], ENT_QUOTES); ?>" />
	        <br /><small><?php 
			printf( __('E.g.: %s', 'recent_posts_with_excerpts'), 
			'&lt;p&gt;&lt;div&gt;&lt;span&gt;&lt;br&gt;&lt;img&gt;&lt;a&gt;&lt;ul&gt;&lt;ol&gt;&lt;li&gt;&lt;blockquote&gt;&lt;cite&gt;&lt;em&gt;&lt;i&gt;&lt;strong&gt;&lt;b&gt;&lt;h2&gt;&lt;h3&gt;&lt;h4&gt;&lt;h5&gt;&lt;h6&gt;');
			?>
	        </small></p>
		<?php } ?>
		<p>
        <label for="<?php echo $this->get_field_id('thumb'); ?>"><?php _e('Show featured images in excerpts?', 'recent_posts_with_excerpts'); ?></label>
        <input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" type="checkbox" value="1" <?php checked($instance['thumb'], '1'); ?> />
        </p>

		<p><label for="<?php echo $this->get_field_id('thumbposition'); ?>"><?php _e('Featured image position:', 'recent_posts_with_excerpts'); ?></label> 
			<select id="<?php echo $this->get_field_id('thumbposition'); ?>" name="<?php echo $this->get_field_name('thumbposition'); ?>">
				<option value="above" <?php selected('above', $instance['thumbposition']) ?>><?php _e('Above title', 'recent_posts_with_excerpts'); ?></option>
				<option value="between" <?php selected('between', $instance['thumbposition']) ?>><?php _e('Between title and excerpt', 'recent_posts_with_excerpts'); ?></option>
				<option value="below" <?php selected('below', $instance['thumbposition']) ?>><?php _e('Below excerpt', 'recent_posts_with_excerpts'); ?></option>
			</select>
		</p>
		
		<p><label for="<?php echo $this->get_field_id('thumbsize'); ?>"><?php _e('Featured image size:', 'recent_posts_with_excerpts'); ?></label> <br />
			<select id="<?php echo $this->get_field_id('thumbsize'); ?>" name="<?php echo $this->get_field_name('thumbsize'); ?>">
				<option value=""<?php selected( $instance['thumbsize'], '' ); ?>>&nbsp;</option>
				<?php
				global $_wp_additional_image_sizes;
		     	$sizes = array();
		 		foreach( get_intermediate_image_sizes() as $s ){
		 			//$sizes[ $s ] = array( 0, 0 );
		 			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
		 				$sizes[ $s ][0] = get_option( $s . '_size_w' );
		 				$sizes[ $s ][1] = get_option( $s . '_size_h' );
		 			}else{
		 				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
		 					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
		 			}
		 		}

		 		foreach( $sizes as $size => $atts ){
		 			echo '<option value="'.$size.'" '. selected( $size, $instance['thumbsize'], false ).'>' . $size . ' (' . implode( 'x', $atts ) . ')</option>';
		 		}
				?>
			</select>
		</p>
		<?php	
	}
}

function recent_posts_with_excerpts_init() {
	register_widget('RecentPostsWithExcerpts');
}

add_action('widgets_init', 'recent_posts_with_excerpts_init');