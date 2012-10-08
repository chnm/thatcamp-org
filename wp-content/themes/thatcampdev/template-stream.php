<?php
/**
 * Stream template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 *
 * Template Name: Stream Template
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content" class="clearfix feature-box thatcamp-stream" role="main">
		<?php rewind_posts();
		while ( have_posts() ) : the_post();
			get_template_part( 'content');
		endwhile;?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>