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
		<?php if ( function_exists( 'get_sitewide_tags_option' ) ) switch_to_blog( get_sitewide_tags_option( 'tags_blog_id' ) ); ?>

		<?php
			$stream_args = array();
			if ( isset( $_GET['category'] ) )
				$stream_args['category_name'] = urldecode( $_GET['category'] );

			if ( isset( $_GET['per_page'] ) )
				$stream_args['posts_per_page'] = intval( $_GET['per_page'] );

			if ( isset( $_GET['paged'] ) )
				$stream_args['paged'] = intval( $_GET['paged'] );
		?>

		<?php $stream_query = new WP_Query( $stream_args ) ?>
		<?php if ( $stream_query->have_posts() ) :
			while ( $stream_query->have_posts() ) : $stream_query->the_post();
				get_template_part( 'parts/content-stream');
			endwhile;
		      endif; ?>

		<?php restore_current_blog() ?>

		<?php if ( get_next_posts_link( null, $stream_query->max_num_pages ) ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'thatcamp' ), $stream_query->max_num_pages ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'thatcamp' ) ); ?></div>
		<?php endif; ?>


	</div>
</div>
<?php get_sidebar( 'stream' ); ?>
<?php get_footer() ?>
