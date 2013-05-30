<?php get_header(); ?>
<!-- single.php -->
<div id="content">
    <div id="primary">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
				<p><strong><?php the_time('l, F jS, Y') ?></strong> | <?php the_author_posts_link(); ?></p>
				<?php thatcamp_add_friend_button( get_the_author_ID() ) ?>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
		</div>
	<?php comments_template(); ?>
	<ul id="post-navigation" class="navigation">
		<li class="alignleft"><?php previous_post_link('&laquo; %link') ?></li>
		<li class="alignright"><?php next_post_link('%link &raquo;') ?></li>
	</ul>
	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
