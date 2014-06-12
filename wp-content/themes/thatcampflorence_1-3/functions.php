<?php

require_once(ABSPATH . WPINC . '/rss.php');
	
if(function_exists('register_sidebar'))
register_sidebar();

function thatcamp_twitter_stream($keyword = 'thatcamp') {
    $rss = fetch_rss('http://search.twitter.com/search.atom?q='.$keyword);
    $maxitems = 5;
    $items = array_slice($rss->items, 0, $maxitems);
    ?>
    <ul id="twitter" class="tweet_area">
    <?php if (empty($items)) echo '<li>No items</li>';
    else
    foreach ( $items as $item ) { ?>
    <li class="tweet_list"><a href="<?php echo $item['author_uri']; ?>"><img class="tweet_avatar" src="<?php echo $item['link_image'] ?>" alt="<?php echo $item['author_name'] ?>" /></a> <a href="<?php echo $item['author_uri'] ?>"><?php echo $item['author_name']; ?></a> <?php echo $item['title']; ?></li>
	<?php } ?>
	</ul>
<?php }

function thatcamp_display_user_name($user, $withLink = true, $displayGravatar = true) {
    if($user) {
        $firstname = $user->first_name;
    	$lastname = $user->last_name;
    	
    	// Check to see if we have a first and last name. If not, use the display name.
    	if ($firstname && $lastname) {
    	    $html = $firstname .' '. $lastname;
    	} else {
    	    $html = $user->display_name;
    	}
    	
    	if($displayGravatar) {
            // $html = get_avatar($user->user_email).' '.$html;
        }
        
    	// If $withLink is true and if make_profile_private is not set to yes, we'll wrap the name in a link.
        // if ($withLink == true && $user->make_profile_private != 'Yes') {
        //     $html = '<a href="'.get_author_posts_url($user->ID).'">'.$html.'</a>';
        // }
        
        $html = '<h2 class="fn">'.$html.'</h2>';
        return $html;
    }
}