<?php
/**
 * Comments
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
if ( post_password_required() )
	return;
?>
<div id="comments">
<?php if ( have_comments() ) : ?>
	<h2 id="comment-title"><?php
	printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'thatcamp'),
	number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
	?></h2>
	<?php do_action( 'bp_before_blog_comment_list' ); ?>
	<ol class="commentlist">
		<?php wp_list_comments( array( 'callback' => 'thatcamp_comment', 'type' => 'comment') );?>
	</ol>
	<?php do_action( 'bp_after_blog_comment_list' ); ?>
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :  ?>
		<nav id="comment-nav-below">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'thatcamp' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'thatcamp' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'thatcamp' ) ); ?></div>
		</nav>
	<?php endif;  ?>

<?php // If comments are closed and there are comments, let's leave a little note.
		elseif ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'twentytwelve' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>