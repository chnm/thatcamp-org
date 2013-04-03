<?php
/**
 * Single
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="single-view" role="main">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
			get_template_part( 'parts/content', 'single' );
			thatcampbase_content_nav( 'nav-below' );
			comments_template( '', true ); 
		endwhile; ?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>