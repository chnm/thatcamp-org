<?php /* Arclite/digitalnature */ ?>
<?php if ( !empty($post->post_password) && $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) : ?>
<p class="error"><?php _e('Enter your password to view comments','arclite'); ?></p>
<?php return; endif; ?>
<?php if ($comments || comments_open()) : ?>
<?php
  /* Count the totals */
  $numPingbacks = 0;
  $numComments  = 0;

  /* Loop throught comments to count these totals */
  foreach ($comments as $comment) if (get_comment_type() != "comment") $numPingbacks++; else $numComments++; ?>

  <?php if (($numComments > 0) || ($numPingbacks > 0)) {  ?>

  <h3 class="comments">
    <?php
      if ($numComments == 1) printf(__('1 Comment', 'arclite'), $numComments); else printf(__('%s Comments', 'arclite'), $numComments);
      if ($numPingbacks == 1) printf(' ('.__('and one trackback', 'arclite').') ', $numPingbacks);
      else if ($numPingbacks > 1) printf(' ('.__('and %s trackbacks', 'arclite').') ', $numPingbacks);
    ?>
  </h3>

  <!-- comments -->
  <ul id="comments" class="clearfix">
    <?php
      // for WordPress 2.7 or higher
  	 if (function_exists('wp_list_comments')) { wp_list_comments('callback=list_comments');	}
      else { // for WordPress 2.6.3 or lower
  	    foreach ($comments as $comment)
    		  //if($comment->comment_type != 'pingback' && $comment->comment_type != 'trackback')
              list_comments($comment, null, null);
        }
    ?>
   </ul>
   <?php } else { ?><h3 class="comments"><?php _e('No comments yet.','arclite'); ?></h3><?php }	?>

   <?php
      if (get_option('page_comments')) {
       $comment_pages = paginate_comments_links('echo=0');
       if ($comment_pages) { ?>
        <div class="commentnavi">
  	      <div class="commentpager">
  	    	<?php echo $comment_pages; ?>
  	      </div>
        </div>
       <?php
  	   }
  	  }
   ?>

   <?php
    if (comments_open()) :
     if (get_option('comment_registration') && !$user_ID ) { // If registration required and not logged in. ?>
  	<div id="comment_login" class="messagebox">
  	  <?php if (function_exists('wp_login_url')) $login_link = wp_login_url(); else $login_link = get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode(get_permalink()); ?>
    	  <p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'arclite'), $login_link); ?></p>
  	</div>

     <?php } else { ?>

      <div id="respond">
      <script type="text/javascript">
       function validatecomment(form){
         if(form.author.value == ('' || '<?php _e("Your name (required)","arclite"); ?>')){
           alert('<?php _e("Please enter your name","arclite"); ?>');
           return false;
         }
         if(form.email.value == ('' || '<?php _e("Your e-mail (required, will not be published)","arclite"); ?>')){
           alert('<?php _e("Please enter your email address","arclite"); ?>');
           return false;
         }
         if(form.comment.value == ('' || '<?php _e("Type your comment here","arclite"); ?>')){
           alert('<?php _e("Please type a comment","arclite"); ?>');
           return false;
         }
         if(form.url.value == ('' || '<?php _e("Your website","arclite"); ?>')){
           form.url.value = '';
           return true;
         }
       }
      </script>
      <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform" onsubmit="return validatecomment(this);">
        <?php if (function_exists('cancel_comment_reply_link')) { ?><div class="cancel-comment-reply"><?php cancel_comment_reply_link(__('Cancel Reply','arclite')); ?></div><?php } ?>
        <?php if ($user_ID) : ?>
          <?php if (function_exists('wp_logout_url')) $logout_link = wp_logout_url(); else $logout_link = get_option('siteurl') . '/wp-login.php?action=logout';	?>
      	  <p>
           <?php
            $login_link = get_option('siteurl')."/wp-admin/profile.php";
            printf(__('Logged in as %s.', 'arclite'), '<a href="'.$login_link.'"><strong>'.$user_identity.'</strong></a>');
           ?>
           <a href="<?php echo $logout_link; ?>" title="<?php _e('Log out of this account', 'arclite'); ?>"><?php _e('Logout &raquo;', 'arclite'); ?></a>
          </p>
       	  <?php else : ?>
  	      <?php if ($comment_author != "") : ?>
  		  <p><?php printf(__('Welcome back <strong>%s</strong>.', 'arclite'), $comment_author) ?> <span id="show_author_info"><a href="javascript:void(0);" onclick="MGJS.setStyleDisplay('author_info','');MGJS.setStyleDisplay('show_author_info','none');MGJS.setStyleDisplay('hide_author_info','');"> <?php _e('Change &raquo;','arclite'); ?></a></span> <span id="hide_author_info"><a href="javascript:void(0);" onclick="MGJS.setStyleDisplay('author_info','none');MGJS.setStyleDisplay('show_author_info','');MGJS.setStyleDisplay('hide_author_info','none');"><?php _e('Close &raquo;','arclite'); ?></a></span></p>
          <?php endif; ?>
          <div id="author_info">
            <div class="row">
              <input type="text" name="author" id="author" class="textfield required" size="34" tabindex="1" value="<?php if($comment_author<>'') echo $comment_author; else _e("Your name (required)","arclite"); ?>" onfocus="if( this.value == '<?php _e("Your name (required)","arclite"); ?>') {this.value = '';}"  onblur="if (this.value == '') { this.value = '<?php _e("Your name (required)","arclite"); ?>';}" />
            </div>
            <div class="row">
              <input type="text" name="email" id="email" class="textfield required" value="<?php if($comment_author_email<>'') echo $comment_author_email; else _e("Your e-mail (required, will not be published)","arclite"); ?>" onfocus="if(this.value == '<?php _e("Your e-mail (required, will not be published)","arclite"); ?>') { this.value = '';}"  onblur="if (this.value == '') { this.value = '<?php _e("Your e-mail (required, will not be published)","arclite"); ?>';}" size="60" tabindex="2" />
            </div>
            <div class="row">
              <input type="text" name="url" id="url" class="textfield" value="<?php if($comment_author_url<>'') echo $comment_author_url; else _e("Your website","arclite"); ?>" onfocus="if(this.value == '<?php _e("Your website","arclite"); ?>') { this.value = 'http://';}" onblur="if ((this.value == 'http://') || (this.value == '')) { this.value = '<?php _e("Your website","arclite"); ?>';}" size="60" tabindex="3" />
            </div>

  		  </div>
          <?php if ( $comment_author != "" ) : ?>
  	   	  <script type="text/javascript">MGJS.setStyleDisplay('hide_author_info','none');MGJS.setStyleDisplay('author_info','none');</script>
  	  	  <?php endif; ?>
        <?php endif; ?>

        <!-- comment input -->
        <div class="row">
        	<textarea name="comment" id="comment" class="required" tabindex="4" rows="8" cols="60" onfocus="if(this.value == '<?php _e("Type your comment here","arclite"); ?>') {this.value = '';}" onblur="if(this.value == '') {this.value = '<?php _e("Type your comment here","arclite"); ?>';}"><?php _e("Type your comment here","arclite"); ?></textarea>
        	<?php if (function_exists('highslide_emoticons')) : ?><div id="emoticon"><?php highslide_emoticons(); ?></div><?php endif; ?>
            <?php
             // Math Comment Spam Protection Plugin
             if ( function_exists('math_comment_spam_protection') ) {
       	      $mcsp_info = math_comment_spam_protection();  ?>
              <p>
  	           <label for="mcspvalue"><?php  printf(__('Spam protection: Sum of %s+%s = ?', 'arclite'), $mcsp_info['operand1'],$mcsp_info['operand2']); ?></label>
               <br />
               <input type="text" name="mcspvalue" id="mcspvalue" size="12" tabindex="4" value="" />
	           <input type="hidden" name="mcspinfo" value="<?php echo $mcsp_info['result']; ?>" />
              </p>
            <?php } ?>

        	<?php if (function_exists('comment_id_fields')) : comment_id_fields(); endif; ?>
        </div>
        <!-- /comment input -->

        <div id="submitbox" class="left">
		<input name="submit" type="submit" id="submit" class="button" tabindex="5" value="<?php _e('Submit Comment', 'arclite'); ?>" />
         <input type="hidden" name="formInput" />
        <?php do_action('comment_form', $post->ID); ?>
        </div>
      </form>

    </div>
    <?php } ?>
    <?php endif;  ?>

  <!-- /comments -->

<?php endif; ?>

<?php if (!comments_open()): // If comments are closed. ?>
 <?php if (is_page() && (!$comments)):
  else: ?>
 <h3 class="comments"><?php _e("Comments are closed.","arclite"); ?></h3>
 <?php endif; ?>
<?php endif; ?>