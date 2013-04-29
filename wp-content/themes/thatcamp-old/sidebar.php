<div id="secondary">
<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
    <div class="widget">
        <div class="title">Search</div>
        <?php get_search_form(); ?>
		
    </div>
    
    <div class="widget">
        <div class="title">Navigation</div>
    <ul>
    <?php wp_list_pages('title_li=&depth=3'); ?>
    </ul>
    </div>
    
    <?php if ( function_exists('thatcamp_twitter_stream')): ?>
    <div id="twitter-feed" class="widget">
    <div class="title">Twitter</div>
    <p><strong>Here's what others are saying about THATCamp on <a href="http://search.twitter.com/search?q=thatcamp">Twitter</a></strong></p>    
    <?php echo thatcamp_twitter_stream(); ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
 
</div>
