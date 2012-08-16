<?php
/*
Plugin Name: FeedBurner FeedSmith Extend
Plugin URI: http://wordpress.org/extend/plugins/feedburner-setting
Description: This is a plugin originally authored by <a href="http://www.orderedlist.com/">Steve Smith</a>. It detects all ways to access your original WordPress feeds and redirects them to your FeedBurner feed. I enhanced it base on the FeedBurner FeedSmith and now it can redirects feeds for category and tag also.
Author: Jiayu (James) Ji
Author URI: https://sites.google.com/site/jiayuji/
Version: 1.0.0
*/

/*  
    Copyright 2011  FeedBurner FeedSmith Extend  (email : Jiayu.ji@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$category_count=10;

$data = array(
	'feedburner_url'		=> '',
	'feedburner_comments_url'	=> ''
);

$ol_flash = '';

function ol_is_authorized() {
	global $user_level;
	if (function_exists("current_user_can")) {
		return current_user_can('activate_plugins');
	} else {
		return $user_level > 5;
	}
}
								
add_option('FeedBurner FeedSmith Extend',$data,'FeedBurner FeedSmith Extend Replacement Options');

$feedburner_settings = get_option('feedburner_settings');

function fb_is_hash_valid($form_hash) {
	$ret = false;
	$saved_hash = fb_retrieve_hash();
	if ($form_hash === $saved_hash) {
		$ret = true;
	}
	return $ret;
}

function fb_generate_hash() {
	return md5(uniqid(rand(), TRUE));
}

function fb_store_hash($generated_hash) {
	return update_option('feedsetting_token',$generated_hash,'FeedBurner Security Hash');
}

function fb_retrieve_hash() {
	$ret = get_option('feedsetting_token');
	return $ret;
}

function ol_add_feedburner_options_page() {
	if (function_exists('add_options_page')) {
		add_options_page('FeedBurner', 'FeedBurner FeedSmith Extend', 8, basename(__FILE__), 'ol_feedburner_options_subpanel');
	}
}

function ol_feedburner_options_subpanel() {
	global $ol_flash, $feedburner_settings, $_POST, $wp_rewrite, $category_count;
	$allcats=get_categories("orderby=id&hide_empty=0");
	$alltags = get_terms("post_tag","orderby=name&get=all");
	if (ol_is_authorized()) {
		
		$updates_field=array();
		
		foreach($allcats as $cat)
		{
			$field='feedburner_category_'.$cat->term_id;
			if (isset($_POST[$field]))
				$updates_field[$field]=$_POST[$field];
		}
		
		foreach($alltags as $tag)
		{
			$field = 'feedburner_tag_'.$tag->term_id;
			if (isset($_POST[$field]))
				$updates_field[$field]=$_POST[$field];
		}
		
		// Easiest test to see if we have been submitted to
		if(isset($_POST['feedburner_url']) || isset($_POST['feedburner_comments_url']) || (count($updates_field)>0)) {
			// Now we check the hash, to make sure we are not getting CSRF
			if(fb_is_hash_valid($_POST['token'])) {

				// update the category feeds
				if (count($updates_field)>0)
					foreach($updates_field as $key=>$val)
					{
						$feedburner_settings[$key] = $val;
						update_option('feedburner_settings',$feedburner_settings);
						$ol_flash = "Your settings have been saved.";
					}
				if (isset($_POST['feedburner_url'])) { 
					$feedburner_settings['feedburner_url'] = $_POST['feedburner_url'];
					update_option('feedburner_settings',$feedburner_settings);
					$ol_flash = "Your settings have been saved.";
				}
				if (isset($_POST['feedburner_comments_url'])) { 
					$feedburner_settings['feedburner_comments_url'] = $_POST['feedburner_comments_url'];
					update_option('feedburner_settings',$feedburner_settings);
					$ol_flash = "Your settings have been saved.";
				} 
			} else {
				// Invalid form hash, possible CSRF attempt
				$ol_flash = "Security hash missing.";
			} // endif fb_is_hash_valid
		} // endif isset(feedburner_url)
	} else {
		$ol_flash = "You don't have enough access rights.";
	}
	
	if ($ol_flash != '') echo '<div id="message" class="updated fade"><p>' . $ol_flash . '</p></div>';
	
	if (ol_is_authorized()) {
		wp_enqueue_script(‘jquery’);
		$temp_hash = fb_generate_hash();
		fb_store_hash($temp_hash);
		echo '<script type="text/javascript" src="'.WP_PLUGIN_URL.'/feedBurner-feedSmith-extend/jquery-ui.min.js"></script>
		      <link rel="stylesheet" href="'.WP_PLUGIN_URL.'/feedBurner-feedSmith-extend/jquery.custom.css" type="text/css"/>	';
		echo '<div class="wrap">';
		echo '<h2>FeedBurner FeedSmith Extend</h2>';
		echo '<p>This plugin makes it easy to redirect 100% of traffic for your feeds to a FeedBurner feed you have created. FeedBurner can then track all of your feed subscriber traffic and usage and apply a variety of features you choose to improve and enhance your original WordPress feed. Feeds for categories and tags could be set separately with the help of this plugin.</p>
		<form action="" method="post">
		<input type="hidden" name="redirect" value="true" />
		<input type="hidden" name="token" value="' . fb_retrieve_hash() . '" />
		<div id = "tabs">
		<ul><li><a href="#main">Main</a></li><li><a href="#categories">Categories</a></li><li><a href="#tags">Tags</a></li></ul>
		<div id="main">
		<strong>Main Feed: </strong> <input type="text" name="feedburner_url" value="' . htmlentities($feedburner_settings['feedburner_url']) . '" size="45" /><br/>
		<strong>Comments Feed: </strong><input type="text" name="feedburner_comments_url" value="' . htmlentities($feedburner_settings['feedburner_comments_url']) . '" size="45" />
		</div>';
		echo '<div id="categories">';
		echo 'You can set FeedBurner feeds for each categories here
		<div id="categoriesContent">
		';
		echo '<input type="button" value="Show All Sub-Category" name="showAll" onclick="showSubCat(this);"/><br/>';
		
		$cats=get_categories("orderby=id&hide_empty=0&parent=0");	
		foreach($cats as $cat)
			echo "<strong>$cat->name</strong> &nbsp;&nbsp;Feed:<input type=\"text\" name=\"feedburner_category_$cat->term_id\" value=\"" . htmlentities($feedburner_settings['feedburner_category_'.$cat->term_id]) . "\" size=\"45\" /><br/>";

		echo '</div>
		</div>
		<div id="tags">';
		$tags = get_terms("post_tag","orderby=name&get=all");
		foreach($tags as $tag)
			echo "<strong>$tag->name</strong> &nbsp;&nbsp;Feed:<input type=\"text\" name=\"feedburner_tag_$tag->term_id\" value=\"" . htmlentities($feedburner_settings['feedburner_tag_'.$tag->term_id]) . "\" size=\"45\" /><br/>";
		echo '</div>';
		echo '<p><input type="submit" value="Save" /></p></div>
		</form>';
		echo '</div>';
	} else {
		echo '<div class="wrap"><p>Sorry, you are not allowed to access this page.</p></div>';
	}
	?>
	<script type="text/javascript">
	var $myjQuery = jQuery.noConflict();
	
	window.onload = function(){
		$myjQuery("#tabs").tabs();
	}
	
	function showSubCat(obj){
		$myjQuery.post(ajaxurl,{action: 'my_action',show: obj.name},function(data){
				$myjQuery("#categoriesContent").html(data);
		});
	}	
	</script>
	<?php
}

add_action('wp_ajax_my_action', 'my_action_callback');

function my_action_callback() {
    global $feedburner_settings;
    $show = $_POST['show'];
    if($show == 'showAll'){
			echo '<input type="button" value="Show Top Level Only" name="showParent" onclick="showSubCat(this);"/><br/>';
			$cats=get_categories("orderby=id&hide_empty=0");	
		}else{
			echo '<input type="button" value="Show All Sub-Category" name="showAll" onclick="showSubCat(this);"/><br/>';
			$cats=get_categories("orderby=id&hide_empty=0&parent=0");	
		}
    foreach($cats as $cat)
		echo "<strong>$cat->name</strong> &nbsp;&nbsp;Feed:<input type=\"text\" name=\"feedburner_category_$cat->term_id\" value=\"" . htmlentities($feedburner_settings['feedburner_category_'.$cat->term_id]) . "\" size=\"45\" /><br/>";
	
    die(); // this is required to return a proper result

}

function ol_feed_redirect() {
	global $wp, $feedburner_settings, $feed, $withcomments;
	if (is_feed() && $feed != 'comments-rss2' && !is_single() && $wp->query_vars['category_name'] == '' && ($withcomments != 1) && trim($feedburner_settings['feedburner_url']) != '') {
		if (function_exists('status_header')) status_header( 302 );
		header("Location:" . trim($feedburner_settings['feedburner_url']));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
	} 
	elseif (is_feed() && $feed != 'comments-rss2' && !is_single() && $wp->query_vars['category_name'] != '' && ($withcomments != 1)) {
		$cat= get_term_by('slug', $wp->query_vars['category_name'], 'category');
		if(trim($feedburner_settings['feedburner_category_'.$cat->term_id]) != ''){		
		if (function_exists('status_header')) status_header( 302 );
		header("Location:" . trim($feedburner_settings['feedburner_category_'.$cat->term_id]));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
		}
	}
	elseif (is_feed() && ($feed == 'comments-rss2' || $withcomments == 1) && trim($feedburner_settings['feedburner_comments_url']) != '') {
		if (function_exists('status_header')) status_header( 302 );
		header("Location:" . trim($feedburner_settings['feedburner_comments_url']));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
	}
	elseif (is_feed() && $feed != 'comments-rss2' && $wp->query_vars['tag'] != '' && ($withcomments != 1)){
		$tag = get_term_by('slug',$wp->query_vars['tag'], 'post_tag');
		if(trim($feedburner_settings['feedburner_tag_'.$tag->term_id]) != ''){
		if (function_exists('status_header')) status_header( 302 );
		header("Location:".trim($feedburner_settings['feedburner_tag_'.$tag->term_id]));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
		}
	}
}

function ol_check_url() {
	global $feedburner_settings;
	switch (basename($_SERVER['PHP_SELF'])) {
		case 'wp-rss.php':
		case 'wp-rss2.php':
		case 'wp-atom.php':
		case 'wp-rdf.php':
			if (trim($feedburner_settings['feedburner_url']) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim($feedburner_settings['feedburner_url']));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			}
			break;
		case 'wp-commentsrss2.php':
			if (trim($feedburner_settings['feedburner_comments_url']) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim($feedburner_settings['feedburner_comments_url']));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			}
			break;
	}
}

if (!preg_match("/feedburner|feedvalidator/i", $_SERVER['HTTP_USER_AGENT'])) {
	add_action('template_redirect', 'ol_feed_redirect');
	add_action('init','ol_check_url');
}

add_action('admin_menu', 'ol_add_feedburner_options_page');

?>
