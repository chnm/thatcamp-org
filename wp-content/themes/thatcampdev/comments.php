<?php
/**
 * Comments
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<div id="comments">
<?php if ( post_password_required() ) : ?>
	<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'thatcamp'); ?></p>
</div>
<?php
return;
endif;
?>
<?php if ( have_comments() ) : ?>
	<h3 id="comment-title"><?php
	printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'thatcamp'),
	number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
	?></h3>
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

<?php else : ?>

	<?php if ( pings_open() && !comments_open() && ( is_single() || is_page() ) && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="nocomments comments-closed pings-open">
			<?php printf( __( 'Comments are closed, but <a href="%1$s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', 'thatcamp' ), trackback_url( '0' ) ); ?>
		</p>
	<?php elseif ( !comments_open() && ( is_single() || is_page() ) ) : ?>
		<p class="nocomments">
			<?php _e( 'Comments are closed.', 'thatcamp' ); ?>
		</p>
	<?php endif; ?>
<?php if ( comments_open() ) : ?>
	<?php comment_form(); ?>
<?php endif; ?>
</div>