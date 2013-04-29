<?php
/**
 * Template Name : Links
 *
 * @package bookcamp
 * @since bookcamp 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="page" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'parts/content', 'page' );?>
		<?php endwhile; ?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
