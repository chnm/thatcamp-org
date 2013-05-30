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




/**
 * Always add our styles when using the proper theme
 *
 * Done inline to reduce overhead
 */
function thatcamp_add_styles_note() {
	//if ( bp_is_root_blog() ) {
	//	return;
	//}

	?>
<style type="text/css">
div.generic-button {
  margin-bottom: 1rem;
}
div.generic-button a {
  background: #ffa200;
  border: 1px solid #555;
  opacity: 1;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  color: #444;
  cursor: pointer;
  font-size: 0.9rem;
  outline: none;
  padding: 4px 10px;
  text-align: center;
  text-decoration: none;
  line-height: 14px;
  text-decoration: -1px -1px 0px #668800;
}
div.generic-button a:hover {
  opacity: 0.9;
}
div.generic-button.disabled-button {
  position: relative;
}
div.generic-button.disabled-button a {
  opacity: 0.5;
}
div.generic-button.disabled-button span {
  margin-left: -999em;
  position: absolute;
}
div.generic-button.disabled-button:hover span {
  border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;
  box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
  position: absolute; left: 1em; top: 2em; z-index: 99;
  margin-left: 0;
  background: #2f2f2f; border: 1px solid #ccc;
  padding: 4px 8px;
  color: #fff;
  white-space: nowrap;
}
</style>
	<?php
}

remove_action( 'wp_head', 'thatcamp_add_styles' );
add_action( 'wp_head', 'thatcamp_add_styles_note' );
