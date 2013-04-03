<?php
/*
Plugin Name: Easy Twitter Links
Version: 1.0
Plugin URI: http://wordpress.org/extend/plugins/easy-twitter-links
Description: Creates links automatically from #tagname or @username anywhere within your blog posts and comments
Author: Josh Jones
Author URI: http://eight7teen.com
*/

function user_links($auto_user) {
	$user_id	= '/([^a-zA-Z0-9])\@([a-zA-Z0-9_]+)/';
	$user_link	= '\1@<a href="http://twitter.com/\2" rel="nofollow" target="_blank" title="View \2\'s Twitter Profile">\2</a>\3';
return preg_replace($user_id,$user_link,$auto_user);
}

function tag_links($auto_tags) {
	$twitter_tags	= '/(^|\s)#(\w+)/';
	$tag_links		= '\1#<a href="http://search.twitter.com/search?q=%23\2" rel="nofollow" target="_blank" title="Search Twitter for &quot;\2&quot;">\2</a>';
return preg_replace($twitter_tags,$tag_links,$auto_tags);
}

//	if(is_single()) {
add_filter('the_content','user_links');
add_filter('the_content','tag_links');
add_filter('comment_text','user_links');
add_filter('comment_text','tag_links');
//	}

?>