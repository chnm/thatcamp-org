<?php get_header(); ?>
<div id="content">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h1><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<ul class="entry-info two" style="float:left;">
				<li><strong><?php the_time('F jS, Y') ?></strong></li>
				<li><a href="http://thatcamp.org/camper/<?php the_author_login(); ?>"><?php the_author(); ?></a></li>
				<li>				<?php thatcamp_add_friend_button( get_the_author_ID() ) ?></li>
				<li><?php the_tags( 'Tags: ', ', ', ''); ?></li>
			</ul>
			<div class="entry six" style="float:right;">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				

				

			</div>
		</div>
		<div class="six" style="float:right;">
	<?php comments_template(); ?>
	</div>
	<ul id="post-navigation" class="navigation">
		<li class="alignleft"><?php previous_post_link('&laquo; %link') ?></li>
		<li class="alignright"><?php next_post_link('%link &raquo;') ?></li>
	</ul>
	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

</div>
<?php get_footer(); ?>
