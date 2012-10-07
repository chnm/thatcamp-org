<?php
/**
 * Template Name : Links
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="page" class="feature-box" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'page' );?>
		<?php endwhile; ?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
