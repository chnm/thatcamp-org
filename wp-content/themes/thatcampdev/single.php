<?php
/**
 * Single
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content" class="clearfix">
		<div id="single-view" class="feature-box" role="main">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
			get_template_part( 'content', 'single' );
			thatcamp_content_nav( 'nav-below' );
			comments_template( '', true ); 
		endwhile; ?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>