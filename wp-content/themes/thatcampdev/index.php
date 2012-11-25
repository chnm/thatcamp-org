<?php
/**
 * Index page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header('signup'); ?>
<div id="primary" class="main-content">
	<div id="content">
		<?php do_action( 'bp_before_archive' ); ?>
		<div id="blog-archives" class="feature-box" role="main">
		<?php if ( have_posts() ) the_post(); ?>
		<?php
		while ( have_posts() ) : the_post();
			get_template_part( 'parts/content', get_post_format() );
		endwhile;
			thatcamp_content_nav( 'nav-below' );?>
		</div>
		<?php do_action( 'bp_after_archive' ); ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>

