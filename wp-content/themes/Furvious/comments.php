<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
?>
<!-- You can start editing here. -->
<div id="comments">
	<?php if ( have_comments() ) : ?>
		<?php if ( ! empty($comments_by_type['comment']) ) : ?>
			<h2><?php comments_number('No Comment', '1 Comment', '% Comments' );?> </h2>
			<div class="navigation">
				<div class="fleft"><?php previous_comments_link() ?></div>
				<div class="fright"><?php next_comments_link() ?></div>
			</div><br />
			<ol class="commentlist clear">
				<?php wp_list_comments('type=comment&avatar_size=38'); ?>
			</ol>
			<div class="navigation">
				<div class="fleft"><?php previous_comments_link() ?></div>
				<div class="fright"><?php next_comments_link() ?></div>
				<div class="clear"></div>
			</div>
		<?php endif; ?>
			
		<?php if ( ! empty($comments_by_type['pings']) ) : ?>
			<h2>Trackbacks/Pingbacks</h2>
			<ol class="pinglist">
				<?php wp_list_comments('type=pings&callback=fpings'); ?>
			</ol>
		<?php endif; ?>

	<?php else : // this is displayed if there are no comments so far ?>
		<?php if ('open' == $post->comment_status) : ?><!-- If comments are open, but there are no comments. -->		
		<?php else : // comments are closed ?><!-- If comments are closed. -->
		<p>Comments are closed.</p>
		<?php endif; ?>
	<?php endif; ?>
 
	<?php if ('open' == $post->comment_status) : ?>
		<div id="respond">
			<h3>Leave a comment</h3>
			<div class="cancel-comment-reply"><small><?php cancel_comment_reply_link(); ?></small></div>			
			<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
				<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
			<?php else : ?>
				<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
					<?php if ( $user_ID ) : ?>
						<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a></p>
					<?php else : ?>
						<p><label for="author">Name <?php if ($req) echo "(required)"; ?></small></label><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> /></p>
						<p><label for="email">E-mail <?php if ($req) echo "(required)"; ?></small></label><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> /></p>
						<p><label for="url">URL</small></label><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" /></p>
					<?php endif; ?>
					<p><label for="comment">Comment</small></label><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>
					<input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
					<?php comment_id_fields(); ?>
					<?php do_action('comment_form', $post->ID); ?>
				</form>
			<?php endif; // If registration required and not logged in ?>
		</div>
	<?php endif; // if you delete this the sky will fall on your head ?>
</div>