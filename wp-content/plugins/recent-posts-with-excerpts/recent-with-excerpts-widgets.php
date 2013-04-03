<?php
/*
Plugin Name: Recent Posts with Excerpts
Plugin URI: http://stephanieleary.com/code/wordpress/recent-posts-with-excerpts/
Description: A widget that lists your most recent posts with excerpts. The number of posts and excerpts is configurable; for example, you could show five posts but include the excerpt for only the most recent. Supports <a href="http://robsnotebook.com/the-excerpt-reloaded/">The Excerpt Reloaded</a> and <a href="http://sparepencil.com/code/advanced-excerpt/">Advanced Excerpt</a>.
Version: 2.3.1
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

class RecentPostsWithExcerpts extends WP_Widget {

	function RecentPostsWithExcerpts() {
			$widget_ops = array('classname' => 'recent_with_excerpt', 'description' => __( 'Your most recent posts, with optional excerpts') );
			$this->WP_Widget('RecentPostsWithExcerpts', __('Recent Posts with Excerpts'), $widget_ops);
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
			
			$ul_classes = apply_filters('recent_posts_with_excerpts_list_classes', '');
			$li_classes = apply_filters('recent_posts_with_excerpts_item_classes', '');
			
			echo '<ul class="'.$ul_classes.'">';
			
			// retrieve last n blog posts
			$q = array('posts_per_page' => $instance['numposts']);
			if (!empty($instance['cat'])) $q .= '&cat='.$instance['cat'];
			if (!empty($instance['tag'])) $q .= '&tag='.$instance['tag'];
			$rpwe = new wp_query($q);
			$excerpts = $instance['numexcerpts'];
			$date = apply_filters('recent_posts_with_excerpts_date_format', $instance['date']);
				  
			// the Loop
			if ($rpwe->have_posts()) :
			while ($rpwe->have_posts()) : $rpwe->the_post(); 
				echo '<li class="'.$li_classes.'">'; ?>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php if (!empty($date)) { ?> <h3 class="date"><?php echo the_time($date); ?></h3> <?php } ?>
                <?php
                if ($excerpts > 0) { // show the excerpt ?>
                    <blockquote> <?php 
                    // the excerpt of the post
                    if (function_exists('the_excerpt_reloaded')) 
                        the_excerpt_reloaded($instance['words'], $instance['tags'], 'content', FALSE, '', '', '1', '');
                    else the_excerpt();  // this covers Advanced Excerpt as well as the built-in one
                    if (!empty($instance['more_text'])) { ?><p class="alignright"><small><a href="<?php the_permalink(); ?>"><?php echo $instance['more_text']; } ?></a></small></p>
                    </blockquote> <?php
                    $excerpts--;
		        }?></li>
			<?php endwhile; endif; ?>
			</ul>
			<?php
			echo $after_widget;
			wp_reset_query();
	}
	
	
	function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['numposts'] = $new_instance['numposts'];
			$instance['numexcerpts'] = $new_instance['numexcerpts'];
			$instance['more_text'] = strip_tags($new_instance['more_text']);
			$instance['date'] = strip_tags($new_instance['date']);
			$instance['words'] = strip_tags($new_instance['words']);
			$instance['tags'] = $new_instance['tags'];
			$instance['cat'] = $new_instance['cat'];
			$instance['tag'] = $new_instance['tag'];
			$instance['postlink'] = $new_instance['postlink'];
			return $instance;
	}

	function form( $instance ) {
		if (get_option('show_on_front') == 'page')
				$link = get_permalink(get_option('page_for_posts'));
			else $link = get_permalink(get_option('home'));
			//Defaults
				$instance = wp_parse_args( (array) $instance, array( 
						'title' => 'Recent Posts',
						'numposts' => 5,
						'numexcerpts' => 5,
						'date' => get_option('date_format'),
						'more_text' => 'more...',
						'words' => '55',
						'tags' => '<p><div><span><br><img><a><ul><ol><li><blockquote><cite><em><i><strong><b><h2><h3><h4><h5><h6>',
						'cat' => 0,
						'tag' => '',
						'postlink' => $link));	
	?>  
       
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" /></p>
        
        <p>
        <p>
        <label for="<?php echo $this->get_field_id('postlink'); ?>"><?php _e('Link widget title to blog home page?'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('postlink'); ?>" name="<?php echo $this->get_field_name('postlink'); ?>" type="checkbox" <?php if ($instance['postlink']) { ?> checked="checked" <?php } ?> />
        </p>
        <p><label for="<?php echo $this->get_field_id('numposts'); ?>"><?php _e('Number of posts to show:'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('numposts'); ?>" name="<?php echo $this->get_field_name('numposts'); ?>" type="text" value="<?php echo $instance['numposts']; ?>" /></p>
        
        <p>
        <p><label for="<?php echo $this->get_field_id('numexcerpts'); ?>"><?php _e('Number of excerpts to show:'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('numexcerpts'); ?>" name="<?php echo $this->get_field_name('numexcerpts'); ?>" type="text" value="<?php echo $instance['numexcerpts']; ?>" /></p>
        
        <p>
        <label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('\'More\' link text:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" type="text" value="<?php echo $instance['more_text']; ?>" />
        <br /><small><?php _e('Leave blank to omit \'more\' link'); ?></small>
        </p>

        <p>
        <label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date format:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="text" value="<?php echo $instance['date']; ?>" />
        <br /><small><?php _e('Leave blank to omit the date'); ?></small>
        </p>
        <p><label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Limit to category: '); ?>
        <?php wp_dropdown_categories(array('name' => $this->get_field_name('cat'), 'show_option_all' => __('None (all categories)'), 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>$instance['cat'])); ?></label></p>
        <p>
        <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Limit to tags:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo $instance['tag']; ?>" />
        <br /><small><?php _e('Enter post tags separated by commas (\'cat,dog\')'); ?></small>
        </p>
        <?php
        if (function_exists('the_excerpt_reloaded')) { ?>
        <p>
        <label for="<?php echo $this->get_field_id('words'); ?>"><?php _e('Limit excerpt to how many words?'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('words'); ?>" name="<?php echo $this->get_field_name('words'); ?>" type="text" value="<?php echo $instance['words']; ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Allowed HTML tags:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="text" value="<?php echo htmlspecialchars($instance['tags'], ENT_QUOTES); ?>" />
        <br /><small><?php _e('E.g.: &lt;p&gt;&lt;div&gt;&lt;span&gt;&lt;br&gt;&lt;img&gt;&lt;a&gt;&lt;ul&gt;&lt;ol&gt;&lt;li&gt;&lt;blockquote&gt;&lt;cite&gt;&lt;em&gt;&lt;i&gt;&lt;strong&gt;&lt;b&gt;&lt;h2&gt;&lt;h3&gt;&lt;h4&gt;&lt;h5&gt;&lt;h6&gt;'); ?>
        </small></p>
			<?php } 
	}
}

function recent_posts_with_excerpts_init() {
	register_widget('RecentPostsWithExcerpts');
}

add_action('widgets_init', 'recent_posts_with_excerpts_init');
?>