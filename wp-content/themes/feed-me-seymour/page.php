<?php get_header(); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<h1 class="catheader"><?php the_title(); ?></h1>

		<div class="posts">
			<?php the_content(); ?>
   			<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages', "feed-me-seymour").':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		</div>
       	<?php comments_template(); ?>

	<?php endwhile; endif; ?>
<?php get_footer(); ?>