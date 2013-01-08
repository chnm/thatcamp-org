<?php
/*
Template Name: Parent Page
*/
?>

<?php get_header(); ?>
<!-- page.php -->
<div id="content">
    <div id="primary">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<h1><?php the_title(); ?></h1>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
				
				<ul class="childlist"><?php  wp_list_pages('title_li=&child_of='.$post->ID); ?>
  				</ul>			
  				
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				
				<?php previous_link(); ?><?php next_link(); ?>	<br /><br />
			</div>
		</div>
		<?php endwhile; endif; ?>
	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
