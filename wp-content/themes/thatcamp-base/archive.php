<?php
/**
 * Archive page
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="blog-archives" role="main">
		<?php if ( have_posts() ) the_post(); ?>
			<header class="post-header">
				<h1 class="post-title">
					<?php if ( is_day() ) : ?>
						<?php printf( __( 'Daily Archives: <span>%s</span>', 'thatcampbase'), get_the_date() ); ?>
					<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Monthly Archives: <span>%s</span>', 'thatcampbase'), get_the_date( 'F Y' ) ); ?>
					<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Yearly Archives: <span>%s</span>', 'thatcampbase'), get_the_date( 'Y' ) ); ?>
					<?php else : ?>
						<?php _e( 'Blog Archives', 'thatcampbase'); ?>
					<?php endif; ?>
				</h1>
			</header>
		<?php
		rewind_posts();
		while ( have_posts() ) : the_post();
			get_template_part( 'parts/content', get_post_format() );
		endwhile;
			thatcampbase_content_nav( 'nav-below' );?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>

