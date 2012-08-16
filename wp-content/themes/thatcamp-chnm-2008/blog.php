<?php
/*
Template Name: Blog
*/
?>

<?php get_header(); ?>
<div id="content" class="blog">
	<?php if(have_posts()): ?>

		<?php while (have_posts()): the_post(); ?>
				<h2><?php the_title(); ?></h2>
				
	<?php endwhile; endif; ?>
	<div id="posts">
	<?php
	// Query 1 posts in all categories. Go ahead. Do it.
	$paged = get_query_var('paged');
	query_posts('cat=1&posts_per_page=5&paged='.$paged);// Which page of the blog are we on?
	
	// make posts print only the first part with a link to rest of the post.
	global $more;
	$more = 0;
	?>

	<?php 
	// Lets show 'em.
	if (have_posts()): ?>
	
	<?php while (have_posts()):  the_post(); ?>
			
			<div class="post hentry" id="post-<?php the_ID(); ?>">
				<h3 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
				<p class="entry-meta">
			<p><strong><?php the_time('l, F jS, Y') ?> | <a href="<?php bloginfo('url')/campers/<?php the_author_login(); ?>"><?php the_author(); ?></a></strong></p>
				</p>
				<div class="entry-content">
					
					<?php the_content();?>
				</div>

			</div>
			<?php endwhile; ?> 
			
			<div id="bottom-navigation" class="navigation">
				<div class="previous"><?php next_posts_link('&laquo; Older Entries') ?></div>
				<div class="next"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
			</div>
			<?php endif; ?>
	</div>


</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
