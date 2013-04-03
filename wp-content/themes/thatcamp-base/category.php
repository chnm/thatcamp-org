<?php
/**
 * Category page
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="category-archives" role="main">
		<header class="post-header">
			<h1 class="post-title">
				<?php printf( __( 'Category Archives: %s', 'thatcampbase'), '<span>' . single_cat_title( '', false ) . '</span>' );
?></h1>
		</header>
		<?php $categorydesc = category_description(); if ( ! empty( $categorydesc ) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . $categorydesc . '</div>' );
		while ( have_posts() ) : the_post(); 
			get_template_part( 'parts/content', get_post_format() );
		endwhile;
			thatcampbase_content_nav( 'nav-below' ); ?>		
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>
