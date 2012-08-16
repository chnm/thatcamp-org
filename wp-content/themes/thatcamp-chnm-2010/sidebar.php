<div id="secondary">
    <div class="sticky">
        <h2>Search</h2>
        <?php get_search_form(); ?>
		
    </div>
    
    <div class="sticky">
        <h2>Navigation</h2>
    <ul id="primary-nav">

    <?php wp_list_pages('title_li='); ?>
    </ul>
    </div>
 
<!--    
<div id="countdown-timer" class="sticky">
       <h2>Countdown</h2>
          <ul>
          <?php function_exists('fergcorp_countdownTimer')?fergcorp_countdownTimer():NULL; ?>
          </ul>
    </div>
-->
    
    <div id="recent-comments" class="sticky">
    <?php if (function_exists('get_recent_comments')) { ?>
   <li><h2><?php _e('Recent Comments'); ?></h2>
   <p>THATCampers can use the blog and comments to talk about session ideas. Follow along by subscribing to the <strong><a href="http://chnm2010.thatcampdev.info/comments/feed/">comments feed</a></strong> and to the <strong><a href="http://chnm2010.thatcampdev.info/feed/">blog feed</a></strong>!</p>

        <ul>
        <?php get_recent_comments(); ?>
        </ul>
   </li>
   <?php } ?>   
    </div>
    
    <?php if ( function_exists('thatcamp_twitter_stream')): ?>
    <div id="twitter-feed" class="sticky">
    <h2>Twitter</h2>
    <p><strong>Here's what others are saying about THATCamp on <a href="http://search.twitter.com/search?q=thatcamp">Twitter</a></strong></p>    
    <?php echo thatcamp_twitter_stream(); ?>
    </div>
    <?php endif; ?>
    
    <div id="post-list" class="sticky">
    <h2>All Posts</h2>
    <?php wp_get_archives('type=postbypost&limit=100'); ?>
    </div>
  
  <div class="sticky">
  <?php wp_tag_cloud(); ?>
  </div>
  
</div>
