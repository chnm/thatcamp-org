<?php

// Include Magpie RSS awesomeness from WordPress. We'll use this later.
require_once(ABSPATH . WPINC . '/rss.php');

/**
 * cw_get_authors
 * 
 * Function that display's the name of a camper, wrapped in a link if 
 * make_profile_private is not Yes. Should move this to the thatcamp 
 * theme's functions.php file, or a plugin. 
 * 
 **/

if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="title">',
        'after_title' => '</div>',
    ));

function cw_get_authors() {
	global $wpdb;
	
	$author_query = "SELECT ID 
	FROM $wpdb->users u 
	JOIN $wpdb->usermeta um 
	ON u.ID = um.user_id 
	WHERE u.user_nicename != 'chnmadmin'
	AND um.meta_key = 'last_name'
	ORDER BY um.meta_value ASC";
	
	return $wpdb->get_results($author_query);
		
}
// 
// function get_users_ordered_by_lastname() {
//     global $wpdb;
//     $sql = "SELECT user_id FROM $wpdb->usermeta WHERE $wpdb->usermeta.meta_key = 'last_name' ORDER BY $wpdb->usermeta.meta_value ASC";
// }

/** 
 * Parses the RSS feed from the Twitter Search for a particular keyword. Includes
 *
 * @param $keyword The keyword to search for. Default is 'thatcamp' 
 * 
 **/

function thatcamp_twitter_stream($keyword = 'thatcamp') {
    $rss = fetch_rss('http://search.twitter.com/search.atom?q='.$keyword);
    $maxitems = 5;
    $items = array_slice($rss->items, 0, $maxitems);
    ?>
    <ul id="twitter">
    <?php if (empty($items)) echo '<li>No items</li>';
    else
    foreach ( $items as $item ) { ?>
    <li><a href="<?php echo $item['author_uri']; ?>"><img src="<?php echo $item['link_image'] ?>" alt="<?php echo $item['author_name'] ?>" /></a> 
        <p><a href="<?php echo $item['author_uri'] ?>"><?php echo $item['author_name']; ?></a> <?php echo $item['title']; ?></p></li>
<?php }
}

/**
 * thatcamp_display_user_name
 * 
 * Function that display's the name of a camper, wrapped in a link if 
 * make_profile_private is not Yes. Should move this to the thatcamp 
 * theme's functions.php file, or a plugin. 
 *
 * @param $user The user object
 * @param $withLink (bool) Whether to wrap the name with a link to the user profile
 * @param $displayAvatar (bool) Whether to include a gravatar beside the name. 
 * 
 **/
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

add_filter('excerpt_length', 'my_excerpt_length');
function my_excerpt_length($length) {
return 20; 
} 
