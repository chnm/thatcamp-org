<?php get_header(); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class('posts'); ?> id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="thedate"><?php the_time(get_option('date_format')); ?> by <?php echo the_author_posts_link();?>
			</div>
			<div class="entry">
				<?php the_content(); ?>
				<?php the_tags(__('<p class="tags"><small>Tags: ', "feed-me-seymour"), ', ', '</small></p>'); ?>
				 
				<?php 
					$words='<em><strong>About '.get_the_author().':</strong> '.get_the_author_meta('description').'</em>';
					$author = get_the_author_meta('description');
					echo ($author ? $words : '');	
				?>
				
				<p class="postmetadata alt">
					<small>
						<?php _e('This entry was posted on ', "feed-me-seymour").the_time(get_option('date_format'))._e(' at ', "feed-me-seymour").the_time()._e(' by ', "feed-me-seymour").the_author_posts_link()._e(' and is filed under ', "feed-me-seymour").the_category(', ');
						?>
					</small>
				</p>
			</div>
       		<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages', "feed-me-seymour").'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		</div>
		<?php comments_template(); ?>
	<?php endwhile; else: ?>
	<p><?php _e("Sorry, no posts matched your criteria.", "feed-me-seymour"); ?></p>
<?php endif; ?>
<?php get_footer(); ?>