<?php
/**
 * Comments
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
if ( post_password_required() )
	return;
?>
<div id="comments">
<?php if ( have_comments() ) : ?>
	<h3 id="comment-title"><?php
	printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'thatcampbase'),
	number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
	?></h3>
	<ol class="commentlist">
		<?php wp_list_comments( array( 'callback' => 'thatcampbase_comment', 'type' => 'comment') );?>
	</ol>
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :  ?>
		<nav id="comment-nav-below">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'thatcampbase' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'thatcampbase' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'thatcampbase' ) ); ?></div>
		</nav>
	<?php endif;  ?>

	<?php // If comments are closed and there are comments, let's leave a little note.
		elseif ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'thatcampbase' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>