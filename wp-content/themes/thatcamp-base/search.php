<?php
/**
 * Search page
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="search-page" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="post-header">
					<h1 class="post-title"><?php printf( __( 'Search Results for: %s', 'thatcampbase'), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header>
		<?php while ( have_posts() ) : the_post(); 
			get_template_part( 'content', get_post_format() );
		endwhile;
			thatcampbase_content_nav( 'nav-below' );
		else : 
			get_template_part( 'content', 'notfound' );
		endif; ?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>
