<?php
/**
 * Author page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header('signup'); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="author-archives" class="feature-box" role="main">
		<?php	if ( have_posts() ) the_post();?>
		<header class="post-header">
			<h1 class="post-title author"><?php printf( __( 'Author Archives: %s', 'thatcamp'), "<span class='vcard'><a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></h1>
		</header>
			<div id="entry-author-content">
				<div id="author-image">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'market_author_bio_avatar_size', 128 ) ); ?>
				</div>
			</div>
			
				<?php if ( get_the_author_meta( 'description' ) ) : ?>
				<div id="author-details">
					<h2><?php printf( __( 'About %s', 'thatcamp'), get_the_author() ); ?></h2>
					<?php the_author_meta( 'description' ); ?>
				</div>
		<?php endif; ?>

		<?php rewind_posts();?>
		
		<?php while ( have_posts() ) : the_post();
			get_template_part( 'parts/content', get_post_format() );
		endwhile; 
			thatcamp_content_nav( 'nav-below' ); ?>
		</div>
	</div>	
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>
