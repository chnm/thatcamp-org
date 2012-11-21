<?php
/**
 * Index main page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header('front'); ?>
<div id="primary" class="main-content">
	<div id="content" role="main">
		<div id="latest" class="feature-box">
				<?php 
				if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); 
						get_template_part( 'content', get_post_format() );
					endwhile;
						blogilates_content_nav( 'nav-below' ); ?>
				<?php endif; ?>
	</div>
</div>
<?php get_sidebar('home'); ?>
<?php get_footer() ?>
