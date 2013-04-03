<?php
/**
 * Search page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<?php do_action( 'bp_before_blog_search' ); ?>
		<div id="search-page" class="feature-box" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="post-header">
					<h1 class="post-title"><?php printf( __( 'Search Results for: %s', 'thatcamp'), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header>
		<?php while ( have_posts() ) : the_post(); 
			get_template_part( 'parts/content', get_post_format() );
		endwhile;
			thatcamp_content_nav( 'nav-below' );
		else : 
			get_template_part( 'content', 'notfound' );
		endif; ?>
		</div>
		<?php do_action( 'bp_after_blog_search' ); ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>
