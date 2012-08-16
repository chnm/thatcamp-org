<?php get_header(); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class('posts'); ?> id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="thedate"><?php the_time(get_option('date_format')); ?></div>
			<div class="entry">
				<?php the_content(); ?>
				<?php the_tags(__('<p class="tags"><small>Tags: ', "feed-me-seymour"), ', ', '</small></p>'); ?>
				<p class="postmetadata alt">
					<small>
						<?php _e('This entry was posted on ', "feed-me-seymour").the_time(get_option('date_format'))._e(' at ', "feed-me-seymour").the_time()._e(' and is filed under ', "feed-me-seymour").the_category(', '); echo '. '; _e('You can follow any responses to this entry through the ', "feed-me-seymour").post_comments_feed_link('RSS 2.0')._e(' feed.', "feed-me-seymour"); ?>
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