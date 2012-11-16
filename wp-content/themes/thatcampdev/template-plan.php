<?php
/**
 * Documents template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 *
 * Template Name: Plan Template
 */
?>
<?php get_header(); ?>
<div id="primary-documents" class="main-content">
	<div id="content" class="clearfix feature-box" role="main">
		<div id="page" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'parts/content', 'page' );?>
		<?php endwhile; ?>
		</div>
	</div>
</div>
<?php get_sidebar('plan'); ?>
<?php get_footer() ?>