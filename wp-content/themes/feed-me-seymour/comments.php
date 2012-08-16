			<div id="comments">
<?php if ( post_password_required() ) : ?>
				<div class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', "feed-me-seymour" ); ?></div>
			</div><!-- .comments -->
<?php
		return;
	endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<?php if ( have_comments() ) : ?>
			<h3 id="comments-title">
<?php
    printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), "feed-me-seymour" ),
        number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
?>
            </h3>

<?php if ( get_comment_pages_count() > 1 ) : // are there comments to navigate through ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', "feed-me-seymour" ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', "feed-me-seymour" ) ); ?></div>
			</div>
<?php endif; // check for comment navigation ?>

			<ol class="commentlist">
				<?php wp_list_comments( array( 'callback' => 'mytheme_comment', 'reply_text' => __('Reply', "feed-me-seymour") ) ); ?>
			</ol>

<?php if ( get_comment_pages_count() > 1 ) : // are there comments to navigate through ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', "feed-me-seymour" ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', "feed-me-seymour" ) ); ?></div>
			</div>
<?php endif; // check for comment navigation ?>

<?php else : // this is displayed if there are no comments so far ?>

<?php if ( comments_open() ) : // If comments are open, but there are no comments ?>

<?php else : // if comments are closed ?>

		<p class="nocomments"><?php _e( 'Comments are closed.', "feed-me-seymour" ); ?></p>

<?php endif; ?>
<?php endif; ?>

<?php 
$args = array(
	'comment_notes_after' => '',
	'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="" rows="8" style="width:90%" aria-required="true"></textarea></p>'
);
comment_form($args); ?>

</div><!-- #comments -->
