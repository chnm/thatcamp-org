<?php
/**
 * Single post view
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php do_action( 'bp_before_blog_post' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header">
		<div class="post-meta-author">
			<span class="img-wrapper">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), '80' ); ?>
			</span>
		</div>
		<div class="post-right">
			<h1 class="post-title"><?php the_title(); ?></h1>
			<span class="author-link"><?php printf( _x( 'by %s', 'Post written by...', 'thatcamp' ), bp_core_get_userlink( $post->post_author ) ); ?></span>
			<span class="cat-links"><?php _e( 'Categories: ', 'thatcamp'); ?><?php the_category( ', ' ); ?></span>
			<span class="post-date"><?php echo get_the_date(); ?></span>
		</div>
	</header>
	<div class="post-body">
		<?php the_content(); ?>
	</div>
	<footer class="post-meta">
		<div class="post-tags">
			<?php $tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ): ?>
			<?php printf( __( 'Tags: %2$s', 'thatcamp'), 'tag-links', $tags_list ); ?> |
			<?php endif; ?>
		</div>
		<div class="post-edit">
				<?php edit_post_link( __( 'Edit &rarr;', 'thatcamp'), ' <span class="edit-link">', '</span> | ' ); ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php _e( 'Permalink', 'thatcamp'); ?></a>
		</div>
	</footer>
	<div class="comment-link">
		<?php if ( comments_open() && ! post_password_required() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( __( '<span class="leave-reply">Comment</span>', 'thatcamp'), __( '1 Comment', 'thatcamp'), __( '% Comments', 'thatcamp') ); ?>
			</div>
		<?php endif; ?>
	</div>
</article>
<?php do_action( 'bp_after_blog_single_post' ); ?>