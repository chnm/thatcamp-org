<?php
/**
 * Port format display for posts - page
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( comments_open() && ! post_password_required() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( __( 'Comment', 'thatcampbase'), __( '1 Comment', 'thatcampbase'), __( '% Comments', 'thatcampbase') ); ?>
			</div>
		<?php endif; ?>
	<header class="post-header">
		<h1 class="post-title"><?php the_title(); ?></h1>
	</header>
	<div class="post-body">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'thatcampbase' ), 'after' => '</div>' ) ); ?>
	</div>
	<footer class="post-meta">
		<div class="post-edit">
				<?php edit_post_link( __( 'Edit &rarr;', 'thatcampbase'), ' <span class="edit-link">', '</span> | ' ); ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcampbase'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php _e( 'Permalink', 'thatcampbase'); ?></a>
		</div>
	</footer>
</article>