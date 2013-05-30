<?php
/**
 * Single post view
 *
 * @package bookcamp
 * @since bookcamp 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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
			<?php if ( comments_open() && ! post_password_required() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( __( 'Comment', 'bookcamp'), __( '1 Comment', 'bookcamp'), __( '% Comments', 'bookcamp') ); ?>
			</div>
		<?php endif; ?>
		<div class="post-date">
			<?php echo get_the_date(); ?></div>
	</footer>
</article>
