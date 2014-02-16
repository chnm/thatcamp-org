<?php if('comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) return false; // dont access this file directly ?>

<?php if(!empty($post->post_password) and $_COOKIE['wp-postpass_'.COOKIEHASH] !== $post->post_password) return false; // if the post is protected and a pass was given ?>



<?php if($post->comment_status == 'open') : ?>
  <h2 class="content-title" id="respond"><?php _e("Leave a Comment", "painter") ?></h2>
  <div class="comment-form">
  
    <?php if(get_option('comment_registration') && !$user_ID) : ?>
      <p><?php _e('You need to be loged to make a comment', 'painter'); ?></p>
    
    <?php else : ?>
      <form action="<?php bloginfo('url'); ?>/wp-comments-post.php" method="post">
        
        <ul>
        <?php if($user_ID) : ?>
          <li><a href="<?php bloginfo('url'); ?>/wp-admin/profile.php"><?php print $user_identity; ?></a> (<?php wp_loginout(); ?>)</li>
        
        <?php else : ?>
          <li>
            <label for="author"><?php _e('Name', 'painter'); ?> *</label>
            <input type="text" name="author" id="author" value="<?php print $comment_author; ?>" tabindex="1" />
          </li>
          <li>
            <label for="email"><?php _e('E-mail', 'painter'); ?> *</label>
            <input type="text" name="email" id="email" value="<?php print $comment_author_email; ?>" tabindex="2" />
          </li>
          <li>
            <label for="url"><?php _e('Site', 'painter'); ?></label>
            <input type="text" name="url" id="url" value="<?php print $comment_author_url; ?>" tabindex="3" />
          </li>
        <?php endif; ?>
        
        <li>
          <label for="comment"><?php _e('Message', 'painter'); ?> *</label>
          <textarea name="comment" id="comment" tabindex="4"></textarea>
        </li>
        
        <?php if(function_exists('show_authimage')) : ?>
          <li>
            <label for="code"><?php _e('Validator', 'painter'); ?></label>
            <input type="text" name="code" id="code" value="" tabindex="5" /> <?php print show_authimage(); ?>
          </li>
        <?php endif; ?>
        
        <li>
          <?php do_action('comment_form', $post->ID); ?>
          <input type="hidden" name="comment_post_ID" value="<?php print $id; ?>" />
          <button type="submit" name="submit" tabindex="6"><?php _e("Comment", "painter"); ?></button>
        </li>
      </form>
    <?php endif; ?>
  </div>
  <br clear="all" />
<?php endif; ?>



<?php if($comments) : ?>
  <!-- Comments -->
  <h2 class="content-title">
    <a href="<?php print get_post_comments_feed_link(); ?>" title="<?php _e('Comments RSS', 'painter'); ?>" class="comment-rss" target="_blank"><?php _e('Comments RSS', 'painter'); ?></a>
    <a href="<?php trackback_url(); ?>" title="<?php _e('TrackBack', 'painter'); ?>" class="trackback-link"><?php _e('TrackBack', 'painter'); ?></a>
    <?php comments_number(__('Do your comment', 'painter'), __('1 comment', 'painter'), __('% comments', 'painter')); ?>
  </h2>
  
  <?php $trackbacks = array(); ?>
  <?php foreach($comments as $comment) : ?>
    <?php if($comment->comment_type == "pingback") : array_push($trackbacks, $comment); continue; endif; ?>
    <div id="comment-<?php comment_ID(); ?>" class="comment <?php if(get_the_author_email() == get_comment_author_email()) print "author-comment"; ?>">
      <?php // print get_avatar($comment); ?>
      <?php
        if(function_exists('get_avatar'))
        {
          print get_avatar($comment);
        }
        else
        {
          // alternate gravatar code for < 2.5
          $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=" . md5(get_comment_author_email());
          print "<img src='{$grav_url}' class='avatar' />";
        }
      ?>
      <div class="options">
        <?php edit_comment_link(__("Edit", "painter")); ?>
      </div>
      <h3 class="comment-author"><?php comment_author_link(); ?></h3>
      <div class="info">
        <span class="comment-date"><?php _e('in', 'painter'); ?> <?php comment_time(__('F jS, Y @ H:i', 'painter')); ?></span>
      </div>
      <?php if($comment->comment_approved == '0') : ?>
        <p class="comment-wait"><?php _e("Your comment is waiting moderation", "painter") ?></p>
      <?php endif; ?>
      <?php comment_text(); ?>
      <hr class="clear" />
    </div>
  <?php endforeach; ?>
  
  <?php if(function_exists('previous_comments_link') and function_exists('next_comments_link')) : ?>
    <div class="navigation">
      <div class="alignleft"><?php previous_comments_link() ?></div>
      <div class="alignright"><?php next_comments_link() ?></div>
    </div>
  <?php endif; ?>
  
<?php endif; ?>
  
<?php if(!empty($trackbacks)) : ?>
  <!-- Trackbacks -->
  <h2 class="content-title"><?php _e('TrackBack', 'painter') ?></h2>
  
  <?php foreach($trackbacks as $trackback) : ?>
    <div id="trackback-<?php $trackback->comment_ID; ?>" class="comment">
      <h3><?php print $trackback->comment_author; ?></h3>
      <p><?php print $trackback->comment_content; ?></p>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
