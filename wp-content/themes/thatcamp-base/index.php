<?php
/**
 * Index main page
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="latest" role="main">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); 
				get_template_part( 'parts/content', get_post_format() );
			endwhile;
				thatcampbase_content_nav( 'nav-below' ); ?>
		<?php endif; ?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>