<?php get_header(); ?>
	<?php
    $mySearch =& new WP_Query("s=$s&showposts=-1");
    $num = $mySearch->post_count;
    echo '<h1 class="catheader">'; printf(__('%1$s search results for "%2$s"', "feed-me-seymour"), $num, get_search_query()); echo '</h1>';
    ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<h2><a href="<?php the_permalink() ?>" title="<?php printf(__("Permanent Link to %s", "feed-me-seymour"), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
		<div class="thedate"><?php the_time(get_option('date_format')); ?></div>
        <div class="entry">
			 <?php 
			if(function_exists('has_post_thumbnail') && has_post_thumbnail()) { 
				echo '<a href="'.get_permalink().'">';
				the_post_thumbnail('thumbnail', array('class'=>'alignleft'));
				echo '</a>';
			} else { 
				echo resize(get_option('thumbnail_size_w'),get_option('thumbnail_size_h')); 
			}
			?>
			<?php the_excerpt() ?>
        </div>

        <p class="meta"><?php the_tags(__('Tags', "feed-me-seymour").": ", ', ', '<br />'); echo __('Posted in', "feed-me-seymour")." "; the_category(', ') ?> | <?php edit_post_link(__('Edit', "feed-me-seymour"), '', ' | '); ?>  <?php comments_popup_link(__('No Comments &#187;', "feed-me-seymour"), __('1 Comment &#187;', "feed-me-seymour"), __('% Comments &#187;', "feed-me-seymour")); ?></p>
    </div>
    
    <?php endwhile; ?>
    	<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', "feed-me-seymour")) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', "feed-me-seymour")) ?></div>
		</div>
            
    <?php else : ?>
	   	<p><?php _e("Sorry, but you are looking for something that isn't here.", "feed-me-seymour"); ?></p>
    <?php endif; ?>

<?php get_footer(); ?>
