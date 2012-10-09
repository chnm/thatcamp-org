<?php
// thatcamp content navigation
function thatcamp_content_nav( $nav_id ) {
	global $wp_query;

	$nav_class = 'site-nav paging-nav';
	if ( is_single() )
		$nav_class = 'site-nav post-nav';

	?>
	<nav id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>" role="navigation">
		<h1 class="assistive-text"><?php _e( 'Post navigation', 'thatcamp' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'thatcamp' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'thatcamp' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'thatcamp' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'thatcamp' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav>

	<?php
}



// thatcamp comments
if ( ! function_exists( 'thatcamp_comment' ) ) :
function thatcamp_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
			  case 'pingback'  :
			  case 'trackback' :
		?>
	<li class="article-pingback">
		<p><?php _e( 'Pingback:', 'thatcamp'); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'thatcamp'), ' ' ); ?></p>
		<?php break; 
		default:
		// Proceed with normal comments.
		global $post;
	?>
	
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
					<?php
					// this needs deciding if want smaller / works even with smaller
						$avatar_size = 64;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 32;
					?>
					<?php if ( $comment->user_id ) : ?>
						<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => $avatar_size, 'height' => $avatar_size, 'email' => $comment->comment_author_email ) ); ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, $avatar_size ); ?>
					<?php endif; ?>
			</div>
			<footer class="comment-meta">
						<?php printf( __( '%s <span class="says">says:</span>', '_s' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
						<?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) : ?>
						<div class="comment-edit">
							<?php edit_comment_link( __( 'Edit', 'thatcamp' ), '<span class="edit-link">', '</span>' ); ?>
						</div>
						<?php endif; ?>
					<div class="comment-info">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						printf( __( '%1$s at %2$s', '_s' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					</div>	
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'thatcamp' ); ?></em>
				<?php endif; ?>
		</footer>	
		<div class="comment-body">	
			<?php comment_text(); ?>
		</div>
		<div class="comment-reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div>
	</article>
	<?php break;
	endswitch;
}
endif;

?>