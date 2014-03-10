<?php
/*
Plugin Name: HidePost
Plugin URI: http://nguyenthanhcong.com/hidepost-plugin-for-wordpress/
Description: Protect a part of your post by using [hidepost] and [/hidepost]  between the protected content.
Version: 2.3.8
Author: Fu4ny
Author URI: http://nguyenthanhcong.com
*/
include("option_init.php");

function hidepost_replace_pattern_link($the_text){//Replace pattern with the link
global $post;
	$current_url = $_SERVER['REQUEST_URI']; //Current post's URL 
	// $current_url = get_permalink( $post->ID );  More compatible but Longer URL
    $the_text = stripslashes($the_text);
    $the_text = str_replace('%login%','<a href="'.get_bloginfo('url').'/wp-login.php?redirect_to='.$current_url.'">Login</a>',$the_text);
    $the_text = str_replace('%register%','<a href="'.get_bloginfo('url').'/wp-login.php?action=register'.'">Register</a>',$the_text);
    return $the_text;
}

function hidepost_replace_pattern(){//Replace pattern with the link
global $hidepost_content_text, $hidepost_link_text, $hidepost_role_text;
    // Replace the link
	$hidepost_content_text = hidepost_replace_pattern_link($hidepost_content_text);
    $hidepost_link_text = hidepost_replace_pattern_link($hidepost_link_text);
    $hidepost_role_text = hidepost_replace_pattern_link($hidepost_role_text);
	
	//Replace the Role 
	$hidepost_content_text = str_replace('%role%',"Registered Member",$hidepost_content_text);
	$hidepost_link_text = str_replace('%role%',"Registered Member",$hidepost_link_text);
}

function hidepost_get_level_req( $level_tag ) {
	$hidepost_max_level = -1;
	$hidepost_temp = strlen($level_tag);
	$hidepost_max_level = $level_tag[$hidepost_temp-1];
	return $hidepost_max_level;
}

function hidepost_text_content() {
	global $hidepost_content_text, $hidepost_content_text_hide;
	if ($hidepost_content_text_hide != 1) return $hidepost_content_text; 
		else return '';
}

function hidepost_role_text($hidepost_max_level) {
	global $hidepost_role_text, $hidepost_role_text_hide;
	switch ($hidepost_max_level):
		case 0: $show = 'Subscriber';
			break;
		case 1: $show = 'Contributor';
			break;
		case 2: $show = 'Author';
			break;
		case 3:
		case 4:
		case 5:
		case 6:
		case 7: $show = 'Editor';
			break;
		case 8:
		case 9: $show = 'Administrator';
			break;
	endswitch;
	$hidepost_role_text = str_replace('%role%',$show,$hidepost_role_text);
	if ($hidepost_role_text_hide != 1) return $hidepost_role_text; 
	else return '';
}

if(!function_exists('hidethis')) {

function hidethis( $content, $level=0,$display = true  ) {
	global $current_user, $user_ID, $user_level;
	get_currentuserinfo();
	if ($user_ID == '') {//If not logged in
		if ( $display ) { echo hidepost_text_content(); }
		else return hidepost_text_content();
	} else if ($user_level < $level){ //Not meet the require level
		if ( $display ) { echo hidepost_role_text($level); }
		else return hidepost_role_text($level);
	} else {
		if ( $display ) { echo $content; }
		else return $content;
	}
}

}

function hidepost_replace_hide($content) {
	global $current_user, $user_ID,$user_level, $m_id;
	$m_id++;
	preg_match_all('#\[hidepost(.*?)\](.*?)\[/hidepost\]#sie', $content, $matches);//Find the hidepost tag
	$level_tag = $matches[1][$m_id];
	$hidepost_max_level = 0;
	if ($level_tag[0] == '=') {
		$hidepost_max_level = hidepost_get_level_req($level_tag); //Get the level require
	}
   //Will allow bot if user want - Temporary disable
	/*  $hidepost_allow_bot = get_option('hidepost_allow_bot');
	if ($hidepost_allow_bot == 1) {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if (stristr('google Googlebot googlebot msnbot ia_archiver lycos jeeves scooter fast-webcrawler slurp@inktomi turnitinbot technorati yahoo findexa findlinks gaisbo zyborg surveybot bloglines blogsearch pubsub syndic8 userland  gigabot become.com', $useragent) !== false) {
				return $matches[2][$m_id];
			}
	}*/
	get_currentuserinfo();
	if ($user_ID == ''){//If not logged in
		return hidepost_text_content();
	}
	if ($user_level < $hidepost_max_level) {//Not meet the require level
		return hidepost_role_text($hidepost_max_level);
	}
	return $matches[2][$m_id]; //Return the content if user can see
}

function hidepost_replace_link($content) {
	global $current_user, $user_ID, $hidepost_link_text, $hidepost_link_text_hide, $m_id;
	$m_id++;
	preg_match_all('#\<a(.*?)\>(.*?)\</a\>#sie', $content, $matches);  //Find all the link
	get_currentuserinfo();
	if ( strpos($matches[0][$m_id],"class=\"more-link\"") != false ) {//Hacked
		return $matches[0][$m_id];
	}
	if ($user_ID == ''){//If not logged in
		if ($hidepost_link_text_hide != 1) return $hidepost_link_text; else return '';
	} else {return $matches[0][$m_id];} //Or return the content if user can see
}

function hidepost_filter_post($content) {
    global $m_id, $hidepost_hide_link, $hidepost_hide_content;
	hidepost_replace_pattern();
	//Protect the link
	if ($hidepost_hide_link == 1) {
	$m_id = -1;//Magic ^.^
	$content = preg_replace('#\<a(.*?)\>(.*?)\</a\>#sie','hidepost_replace_link($content)',$content);
	}
	//Protect content
	if ($hidepost_hide_content == 1) {
	$m_id = -1;
	$content = preg_replace('#\[hidepost(.*?)\](.*?)\[/hidepost\]#sie','hidepost_replace_hide($content)',$content);
	}
	return $content;
}



//Add the Option Page
function hidepost_options()	{
        if (function_exists('add_options_page')) {
            add_options_page('HidePost Options','HidePost', 6, 'hidepost/options.php');
        }
    }

function the_view($special_var){
    return htmlspecialchars(stripslashes($special_var));
}

//Replace your old tag with new [hidepost] tag
function hidepost_search_and_replace($old_tag,$old_close_tag,$new_tag) {
    global $wpdb;
    if ($new_tag <> "[hidepost]") { //Just double checking user want to replace
        return 'Your new tag must be [hidepost]';
    }
    if (($old_tag=="[hidepost]") && ($old_close_tag=="[/hidepost]")) {
        return 'Your tag already newest';
    }
	// Be careful editing those lines
    $new_close_tag = "[/hidepost]";
    echo 'Find '.$old_tag.' and replace with '.$new_tag.'... ' ;
    $query = "UPDATE $wpdb->posts ";
    $query .= "SET post_content = ";
    $query .= "REPLACE(post_content, \"$old_tag\", \"$new_tag\") ";
    $wpdb->get_results($query);
    echo 'Completed  <br/>';

    echo 'Find '.$old_close_tag.' and replace with '.$new_close_tag.'... ' ;
    $query = "UPDATE $wpdb->posts ";
    $query .= "SET post_content = ";
    $query .= "REPLACE(post_content, \"$old_close_tag\", \"$new_close_tag\") ";
    $wpdb->get_results($query);
    echo 'Completed  <br/>';

    return 'All done, Enjoy using HidePost<br />';
}

function is_checked($show_or_not) {
	if ($show_or_not == 1) return 'checked';
	else return '';
}

if ( !get_option('hidepost_hide_content') && !get_option('hidepost_disable_notice')) {
	function hidepost_warning() {
		echo "<div id='hidepost-warning' class='updated fade'><p><strong>"."You must enable HidePost at its <a href=\"options-general.php?page=hidepost/options.php\">Option Page</a> | <a href=\"options-general.php?page=hidepost/options.php&hidepost_disable_notice=1\">Disable this notice</a>"."</strong></p></div>";
	}
	add_action('admin_notices', 'hidepost_warning');
}

//Hook function
add_action('admin_menu', 'hidepost_options');
add_filter('the_content', 'hidepost_filter_post');
add_filter('the_excerpt', 'hidepost_filter_post');
add_filter('the_excerpt_rss', 'hidepost_filter_post');

//Function: Add Quick Tag For HidePost In TinyMCE >= WordPress 2.5
add_action('init', 'hidepost_tinymce_addbuttons');
function hidepost_tinymce_addbuttons() {
	if( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
		return;
	}
	if(get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "hidepost_tinymce_addplugin");
		add_filter('mce_buttons', 'hidepost_tinymce_registerbutton');
	}
}
function hidepost_tinymce_registerbutton($buttons) {
	array_push($buttons, 'separator', 'hidepost');
	return $buttons;
}
function hidepost_tinymce_addplugin($plugin_array) {
	$plugin_array['hidepost'] = WP_PLUGIN_URL.'/hidepost/tinymce/plugins/hidepost/editor_plugin.js';
	return $plugin_array;
}

//Add quick tag for normal editor
add_action('admin_footer', 'hidepost_quicktag_footer');
function hidepost_quicktag_footer(){
	echo '<script type="text/javascript">'."\n";
	echo "\t".'function insertLevel() {'."\n";
	echo "\t".'var hidepost_level = prompt(" Enter the lowest user level that can read the content ( default is 0 )","0");'."\n";
	echo "\t".'return hidepost_level;'."\n";
	echo "\t".'}'."\n";
	echo "\t".'function insertHidepost_qt( myField ) {'."\n";
	echo "\t\t".'var hidepost_startPos = myField.selectionStart;'."\n";
	echo "\t\t".'var hidepost_endPos = myField.selectionEnd;'."\n";
	echo "\t\t".'var hidepost_selection = myField.value.substring( hidepost_startPos, hidepost_endPos );'."\n";
	echo "\t\t".'edInsertContent(myField, "[hidepost=" + insertLevel() + "]" + hidepost_selection + "[/hidepost]");'."\n";
	echo "\t".'}'."\n";
	echo "\t".'if(document.getElementById("ed_toolbar")){'."\n";
	echo "\t\t".'qt_toolbar = document.getElementById("ed_toolbar");'."\n";
	echo "\t\t".'edButtons[edButtons.length] = new edButton("ed_hidepost","Hidepost","","","");'."\n";
	echo "\t\t".'var qt_button = qt_toolbar.lastChild;'."\n";
	echo "\t\t".'while (qt_button.nodeType != 1){'."\n";
	echo "\t\t"."\t".'qt_button = qt_button.previousSibling;'."\n";
	echo "\t".'}'."\n";
	echo "\t".'qt_button = qt_button.cloneNode(true);'."\n";
	echo "\t".'qt_button.value = "Hidepost";'."\n";
	echo "\t".'qt_button.title = "Insert Hidepost Tag";'."\n";
	echo "\t".'qt_button.onclick = function () { insertHidepost_qt( edCanvas); }'."\n";
	echo "\t".'qt_button.id = "ed_hidepost";'."\n";
	echo "\t".'qt_toolbar.appendChild(qt_button);'."\n";
	echo "\t".'}'."\n";
	echo '</script>'."\n";
}

?>
