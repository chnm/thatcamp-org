<?php
/*
Plugin Name: Next Page
Plugin URI: http://sillybean.net/code/wordpress/next-page/
Description: Provides shortcodes and template tags for next/previous navigation in pages. 
Version: 1.5.1
License: GPLv2
Author: Stephanie Leary
Author URI: http://sillybean.net/
*/

add_action('admin_menu', 'next_page_add_pages');

register_activation_hook(__FILE__, 'next_page_activation');
function next_page_activation() {
	// remove old options
	$oldoptions = array();
	$oldoptions[] = get_option('next_page__before_prev_link');
	$oldoptions[] = get_option('next_page__prev_link_text');
	$oldoptions[] = get_option('next_page__after_prev_link');
	
	$oldoptions[] = get_option('next_page__before_parent_link');
	$oldoptions[] = get_option('next_page__parent_link_text');
	$oldoptions[] = get_option('next_page__after_parent_link');
	
	$oldoptions[] = get_option('next_page__before_next_link');
	$oldoptions[] = get_option('next_page__next_link_text');
	$oldoptions[] = get_option('next_page__after_next_link');
	
	$oldoptions[] = get_option('next_page__exclude');
	
	delete_option('next_page__before_prev_link');
	delete_option('next_page__prev_link_text');
	delete_option('next_page__after_prev_link');

	delete_option('next_page__before_parent_link');
	delete_option('next_page__parent_link_text');
	delete_option('next_page__after_parent_link');

	delete_option('next_page__before_next_link');
	delete_option('next_page__next_link_text');
	delete_option('next_page__after_next_link');

	delete_option('next_page__exclude');
	
	// set defaults
	$options = array();
	$options['before_prev_link'] = '<div class="alignleft">';
	$options['prev_link_text'] = __('Previous:', 'next-page').' %title%';
	$options['after_prev_link'] = '</div>';
	
	$options['before_parent_link'] = '<div class="aligncenter">';
	$options['parent_link_text'] = __('Up one level:', 'next-page').' %title%';
	$options['after_parent_link'] = '</div>';
	
	$options['before_next_link'] = '<div class="alignright">';
	$options['next_link_text'] = __('Next:', 'next-page').' %title%';
	$options['after_next_link'] = '</div>';
	
	$options['exclude'] = '';
	$options['loop'] = 0;
	
	// set new option
	add_option('next_page', array_merge($oldoptions, $options), '', 'yes');
}

// when uninstalled, remove option
register_uninstall_hook( __FILE__, 'next_page_delete_options' );

function next_page_delete_options() {
	delete_option('next_page');
}

// i18n
if (!defined('WP_PLUGIN_DIR'))
	define('WP_PLUGIN_DIR', dirname(dirname(__FILE__))); 
$lang_dir = basename(dirname(__FILE__)). '/languages';
load_plugin_textdomain( 'next_page', 'WP_PLUGIN_DIR'.$lang_dir, $lang_dir );

function next_page_plugin_actions($links, $file) {
 	if ($file == 'next-page/next-page.php' && function_exists("admin_url")) {
		$settings_link = '<a href="' . admin_url('options-general.php?page=next-page') . '">' . __('Settings', 'next-page') . '</a>';
		array_unshift($links, $settings_link); 
	}
	return $links;
}
add_filter('plugin_action_links', 'next_page_plugin_actions', 10, 2);

add_action('admin_init', 'register_next_page_options' );
function register_next_page_options(){
	register_setting( 'next_page', 'next_page' );
}

function next_page_add_pages() {
    // Add a new submenu under Options:
	$css = add_options_page('Next Page', 'Next Page', 'manage_options', 'next-page', 'next_page_options');
	add_action("admin_head-$css", 'next_page_css');
}

function next_page_css() { ?>
<style type="text/css">
#next-page, #parent-page, #previous-page { float: left; width: 30%; margin-right: 5%; }
#next-page { margin-right: 0; }
</style>
<?php 
}


// displays the options page content
function next_page_options() { ?>	
    <div class="wrap">
	<form method="post" id="next_page_form" action="options.php">
		<?php settings_fields('next_page');
		$options = get_option('next_page'); ?>

    <h2><?php _e( 'Next Page Options', 'next-page'); ?></h2>
    
	<p><?php _e("On the first and last pages in the sequence:", 'next-page'); ?><br />
    <label><input type="radio" name="next_page[loop]" id="loop" value="1" <?php checked('1', $options['loop']); ?> />
		<?php _e("Loop around, showing links back to the beginning or end", 'next-page'); ?></label><br />
	<label><input type="radio" name="next_page[loop]" id="loop" value="0" <?php checked('0', $options['loop']); ?> />
		<?php _e("Omit the empty link", 'next-page'); ?></label>	
	</p>

    <p><label><?php _e("Exclude pages: ", 'next-page'); ?><br />
    <input type="text" name="next_page[exclude]" id="exclude" 
		value="<?php echo $options['exclude']; ?>" /><br />
	<small><?php _e("Enter page IDs separated by commas.", 'next-page'); ?></small></label></p>
       
    <div id="previous-page">
    <h3><?php _e("Previous Page Display:", 'next-page'); ?></h3>
    <p><label><?php _e("Before previous page link: ", 'next-page'); ?><br />
    <input type="text" name="next_page[before_prev_link]" id="before_prev_link" 
		value="<?php echo esc_html($options['before_prev_link']); ?>" />  </label></p>
    
    <p><label><?php _e("Previous page link text: <small>Use %title% for the page title</small>", 'next-page'); ?><br />
    <input type="text" name="next_page[prev_link_text]" id="prev_link_text" 
		value="<?php echo esc_html($options['prev_link_text']); ?>" />  </label></p>
    
    <p><label><?php _e("After previous page link: ", 'next-page'); ?><br />
    <input type="text" name="next_page[after_prev_link]" id="after_prev_link" 
	value="<?php echo esc_html($options['after_prev_link']); ?>" />  </label></p>
    <p><?php _e('Shortcode:'); ?> <strong>[previous]</strong><br />
    <?php _e('Template tag:'); ?> <strong>&lt;?php previous_link(); ?&gt;</strong></p>
    </div>
    
    <div id="parent-page">
    <h3><?php _e("Parent Page Display:", 'next-page'); ?></h3>
    <p><label><?php _e("Before parent page link: ", 'next-page'); ?><br />
    <input type="text" name="next_page[before_parent_link]" id="before_parent_link" 
		value="<?php echo esc_html($options['before_parent_link']); ?>" />  </label></p>
    
    <p><label><?php _e("Parent page link text: <small>Use %title% for the page title</small>", 'next-page'); ?><br />
    <input type="text" name="next_page[parent_link_text]" id="parent_link_text" 
		value="<?php echo esc_html($options['parent_link_text']); ?>" />  </label></p>
    
    <p><label><?php _e("After parent page link: ", 'next-page'); ?><br />
    <input type="text" name="next_page[after_parent_link]" id="after_parent_link" 
		value="<?php echo esc_html($options['after_parent_link']); ?>" />  </label></p>
    <p><?php _e('Shortcode:'); ?> <strong>[parent]</strong><br />
    <?php _e('Template tag:'); ?> <strong>&lt;?php parent_link(); ?&gt;</strong></p>
    </div>
    
    <div id="next-page">
    <h3><?php _e("Next Page Display:", 'next-page'); ?></h3>
    <p><label><?php _e("Before next page link: ", 'next-page'); ?><br />
    <input type="text" name="next_page[before_next_link]" id="before_next_link" 
		value="<?php echo esc_html($options['before_next_link']); ?>" />  </label></p>
    
    <p><label><?php _e("Next page link text: <small>Use %title% for the page title</small>", 'next-page'); ?><br />
    <input type="text" name="next_page[next_link_text]" id="next_link_text" 
		value="<?php echo esc_html($options['next_link_text']); ?>" />  </label></p>
    
    <p><label><?php _e("After next page link: ", 'next-page'); ?><br />
    <input type="text" name="next_page[after_next_link]" id="after_next_link" 
		value="<?php echo esc_html($options['after_next_link']); ?>" />  </label></p>
    <p><?php _e('Shortcode:'); ?> <strong>[next]</strong><br />
    <?php _e('Template tag:'); ?> <strong>&lt;?php next_link(); ?&gt;</strong></p>
    </div>
    
	<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options', 'next-page'); ?>" />
	</p>
	</form>
	</div>
<?php 
} // end function next_page_options() 

// make the magic happen
function flatten_page_list($exclude = '') {
   $args = 'sort_column=menu_order&sort_order=asc';
   $pagelist = get_pages($args);
   $mypages = array();
   if (!empty($exclude)) {
       $excludes = split(',', $exclude);
       foreach ($pagelist as $thispage) {
           if (!in_array($thispage->ID, $excludes)) {
               $mypages[] += $thispage->ID;
           }
       }
   }
   else {
       foreach ($pagelist as $thispage) {
           $mypages[] += $thispage->ID;
       }
   }
   return $mypages;
}

function get_next_link() {
	global $post;
	$options = get_option('next_page');
	$exclude = $options['exclude'];
	$pagelist = flatten_page_list($exclude);
	$current = array_search($post->ID, $pagelist);
	$nextID = $pagelist[$current+1];
	
	if (!isset($nextID)) 
		if ($options['loop'])
			$nextID = $pagelist[0];
		else 
			return '';
	
	$before_link = stripslashes($options['before_next_link']);
	$linkurl = get_permalink($nextID);
	$title = get_the_title($nextID);
	$linktext = $options['next_link_text'];
	if (strpos($linktext, '%title%') !== false) 
		$linktext = str_replace('%title%', $title, $linktext);
	$after_link = stripslashes($options['after_next_link']);
	
	$link = $before_link . '<a href="' . $linkurl . '" title="' . $title . '">' . $linktext . '</a>' . $after_link;
	return $link;
} 

function get_previous_link() {
	global $post;
	$options = get_option('next_page');
	$exclude = $options['exclude'];
	$pagelist = flatten_page_list($exclude);
	$current = array_search($post->ID, $pagelist);
	$prevID = $pagelist[$current-1];
	
	if (!isset($prevID))
	 	if ($options['loop'])
			$prevID = $pagelist[count($pagelist) - 1];
		else 
			return '';
		
	$before_link = stripslashes($options['before_prev_link']);
	$linkurl = get_permalink($prevID);
	$title = get_the_title($prevID);
	$linktext = $options['prev_link_text'];
	if (strpos($linktext, '%title%') !== false) 
		$linktext = str_replace('%title%', $title, $linktext);
	$after_link = stripslashes($options['after_prev_link']);
	
	$link = $before_link . '<a href="' . $linkurl . '" title="' . $title . '">' . $linktext . '</a>' . $after_link;
	return $link;
} 

function get_parent_link() {
	global $post;
	$options = get_option('next_page');
	$parentID = $post->post_parent;
	
	$exclude = array($options['exclude']);
	if (in_array($parentID, $exclude)) return false;
	else {
		$before_link = stripslashes($options['before_parent_link']);
		$linkurl = get_permalink($parentID);
		$title = get_the_title($parentID);
		$linktext = $options['parent_link_text'];
		if (strpos($linktext, '%title%') !== false) 
			$linktext = str_replace('%title%', $title, $linktext);
		$after_link = stripslashes($options['after_parent_link']);
		
		$link = $before_link . '<a href="' . $linkurl . '" title="' . $title . '">' . $linktext . '</a>' . $after_link;
		return $link;
	}
}

function next_link() {
	echo get_next_link();
}
function previous_link() {
	echo get_previous_link();
}
function parent_link() {
	echo get_parent_link();
}

// shortcodes
add_shortcode('previous', 'get_previous_link');
add_shortcode('next', 'get_next_link');
add_shortcode('parent', 'get_parent_link');

// pre-3.1 compatibility, lifted from wp-includes/formatting.php
if (!function_exists('esc_html')) {
	function esc_html( $text ) {
		$safe_text = wp_check_invalid_utf8( $text );
		$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
		return apply_filters( 'esc_html', $safe_text, $text );
	}
}
?>