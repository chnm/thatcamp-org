<?php
/**
 * Single post view
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

	<div class="post-meta">
		<div class="post-author">By <?php the_author_posts_link() ?> <?php thatcamp_add_friend_button( get_the_author_ID() ) ?></div>
	</div>

	<div class="post-body">
		<?php the_content(); ?>
	</div>
	<footer class="post-meta">
		<div class="post-date">
			<?php echo get_the_date(); ?></div>
		<div class="post-categories">
			<?php _e( 'Categories: ', 'thatcampbase'); ?><?php the_category( ' ' ); ?>
		</div>
		<div class="post-tags">
			<?php $tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ): ?>
			<?php printf( __( 'Tags: %2$s', 'thatcampbase'), 'tag-links', $tags_list ); ?>
			<?php endif; ?>
		</div>			
	</footer>
</article>
