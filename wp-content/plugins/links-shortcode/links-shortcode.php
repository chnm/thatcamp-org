<?php
/*
Plugin Name: Links Shortcode
Plugin URI: http://blog.bigcircle.nl/about/wordpress-plugins
Description: Displays all links of a certain category in a post using a shortcode, according to a definable template. Includes optional Facebook Like button.
Version: 1.4.1
Author: Maarten Swemmer
Author URI: http://blog.bigcircle.nl
*/

$linkssc_default_template = "<div class=\"links_sc_fb\">\n[optional [date]: ||]<a href=\"[link_url]\" target=\"_blank\">[link_name]</a>\n[optional <br />[link_description]||]\n[optional <br />[fb_button]||]</div>\n";

require_once(ABSPATH . WPINC . '/formatting.php');

add_action( 'wp_enqueue_scripts', 'linkssc_css' );
function linkssc_css() 
{
	// added for SSL friendlyness:
	wp_register_style( 'linkssc-style', plugins_url('links-shortcode.css', __FILE__) );
	wp_enqueue_style( 'linkssc-style' );
	//previously: echo '<link rel="stylesheet" type="text/css" media="screen" href="'. WP_PLUGIN_URL . '/links-shortcode/links-shortcode.css"/>';	
}

function linkssc_update_info() {
	if ( $info = wp_remote_fopen("http://blog.bigcircle.nl/links-shortcode-latest.txt") )
		echo '<br />' . strip_tags( $info, "<br><a><b><i><span>" );
}
add_action('in_plugin_update_message-'.plugin_basename(__FILE__), 'linkssc_update_info');


function linkssc_getdate($text)
{
	$result = new StdClass; 
	
	if(preg_match("/\d\d\d\d-\d\d-\d\d:/",$text))
	{
		$result->date = substr($text,0,10);
		$result->title = substr($text,11);
	} 
	else
	{
		$result->date = '';
		$result->title = $text;	
	}
	return $result;
}

add_shortcode('links', 'linkssc_shortcode');
function linkssc_shortcode($atts, $content = null) 
{
	global $linkssc_default_template;
	$fblike = '';
	$fbrecommend = '';
	$facebook = get_option('linkssc_facebook', 'like');
	$fbcolors = get_option('linkssc_fbcolors', 'light');
	$template = get_option('linkssc_template', $linkssc_default_template);
		if ($template=='') { $template = $linkssc_default_template; update_option('linkssc_template', $linkssc_default_template); }
	$template_before = get_option('linkssc_template_b', '');
	$template_after = get_option('linkssc_template_a', '');
	if ($facebook == 'like') { $fblike = '1'; }
	elseif ($facebook == 'recommend') {$fbrecommend = '1'; } 

	extract(shortcode_atts(array(
			'fblike'		 => $fblike,
			'fbrecommend'	 => $fbrecommend,
			'fbcolors'		 => $fbcolors,
			'orderby'        => get_option('linkssc_orderby', 'name'), 
			'order'          => get_option('linkssc_order', 'DESC'),
			'limit'          => get_option('linkssc_howmany', '-1'), 
			'category'       => null,
			'category_name'  => null, 
			'hide_invisible' => 1,
			'show_updated'   => 0, 
			'include'        => null,
			'exclude'        => null,
			'search'         => ''
			), $atts)
	);
	
	$args = array(
            'orderby'        => $orderby, 
            'order'          => $order,
            'limit'          => $limit, 
            'category'       => $category,
            'category_name'  => $category_name, 
            'hide_invisible' => $hide_invisible,
            'show_updated'   => $show_updated, 
            'include'        => $include,
            'exclude'        => $exclude,
            'search'         => $search);
			
	if ($orderby == 'order' && function_exists('mylinkorder_get_bookmarks'))
	{
		// for compatibility with 'My link Order' plugin
		$bms = mylinkorder_get_bookmarks( $args );
	}
	else 
	{
		$bms = get_bookmarks( $args );
    }
	
	if ($fblike == '1'|| $fbrecommend == '1')
	{
		if ($fblike == '1') { $fbaction = 'like'; }
		else { $fbaction = 'recommend'; } 
	}
	else
	{
		// replace DIV style from class="links_sc_fb" to class="links_sc"
		$template = str_replace('"links_sc_fb"', '"links_sc"',$template);
	}
	
	$text = $template_before;
	foreach ($bms as $bm)
	{ 
        $newlinktext = $template.'';
		$title = linkssc_getdate($bm->link_name);
		$linkinfo = array();
		$linkinfo['link_name'] = $title->title;
		$linkinfo['link_url'] = $bm->link_url;
		$linkinfo['link_rel'] = $bm->link_rel;
		$linkinfo['link_image'] = $bm->link_image;
		$linkinfo['link_target'] = $bm->link_target;
		if (isset($bm->link_category)) {$linkinfo['link_category'] = $bm->link_category;} // because $bm->link_category is in most cases not set. TODO: find better solution
		$linkinfo['link_description'] = $bm->link_description;
		$linkinfo['link_visible'] = $bm->link_visible;
		$linkinfo['link_owner'] = get_the_author_meta('display_name', $bm->link_owner); // display the display name of a user instead of the user id.
		$linkinfo['link_rating'] = $bm->link_rating;
		$linkinfo['link_rating_stars'] = '<div class="links_sc_rating "><img class="links_sc_rating_full" src="'. WP_PLUGIN_URL . '/links-shortcode/fullstars.png" style="width:'.round(78*$linkinfo['link_rating']/10).'px;"/><img class="links_sc_rating_empty" src="'. WP_PLUGIN_URL . '/links-shortcode/emptystars.png" /></div>';
		if (preg_match('#^[\-0 :]*$#', $bm->link_updated)) { $linkinfo['link_updated'] = ''; $linkinfo['date'] = ''; } 
		else {
			$linkinfo['link_updated'] = $bm->link_updated;
			$a = split(' ', $bm->link_updated); $linkinfo['date'] = $a[0];
		}
		if ($title->date != '') { $linkinfo['date'] = $title->date; }
		list($linkinfo['date_year'],$linkinfo['date_month'],$linkinfo['date_day']) = split('-', $linkinfo['date']);
		$linkinfo['link_rel'] = $bm->link_rel;
		$linkinfo['link_notes'] = $bm->link_notes;
		$linkinfo['link_rss'] = $bm->link_rss;
		if ($fblike == '1'|| $fbrecommend == '1')
		{
			$linkinfo['fb_button'] = '<iframe src="//www.facebook.com/plugins/like.php?href='.urlencode($bm->link_url).'&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action='.$fbaction.'&amp;font&amp;colorscheme='.$fbcolors.'" scrolling="no" frameborder="0" ></iframe>';
		}
		else { $linkinfo['fb_button'] = ''; }
		$reallinkinfo = array_diff($linkinfo, array('')); // remove all elements with empty value;
		// insert al known values
		foreach ($reallinkinfo as $k=>$v)
		{
			$newlinktext = str_replace('['.$k.']',$v,$newlinktext);
		}
		// resolve optional elements
		$c = preg_match_all ('/\[optional (.*)\|\|(.*)\]/U',$newlinktext,$optionals, PREG_PATTERN_ORDER);
		for (;$c > 0;$c--)
		{
			if ((preg_match('/\[(.*)\]/U',$optionals[1][$c-1],$tag)) && (isset($linkinfo[$tag[1]]))) 
			{
				$newlinktext = str_replace ($optionals[0][$c-1],$optionals[2][$c-1],$newlinktext); 
			}
			else
			{
				$newlinktext = str_replace ($optionals[0][$c-1],$optionals[1][$c-1],$newlinktext); 
			}
		}
		foreach ($linkinfo as $k=>$v)
		{
			$newlinktext = str_replace('['.$k.']','',$newlinktext);
		}
		
		$text .= $newlinktext; 
		// for testing only:
		//$text .= print_r($bm,true).'<br>';
    }
	$text .= $template_after;
	return '<!-- Links -->'.$text.'<!-- /Links -->'; // add html comment for easier debugging
}

// taking care of translations
$plugin_dir = plugin_basename( dirname( __FILE__ ) .'/languages' );
load_plugin_textdomain( 'links-shortcode', null, $plugin_dir );

// Hook for adding admin menus
if ( is_admin() ){ // admin actions
	add_action('admin_menu', 'linkssc_add_options_page'); // add option page for plugin
	add_action('admin_init', 'linkssc_register_mysettings');
	add_action('admin_head', 'linkssc_add_LastMod_box'); // add last updated meta box on link editing page
	add_action('edit_link', 'linkssc_update_link_editied'); // update link edited field on editing a link
	add_action('add_link', 'linkssc_update_link_editied'); // update link edited field on adding a link
} 
else {
  // non-admin enqueues, actions, and filters
}

// Activation action
function linkssc_activation(){
	global $linkssc_default_template;
	add_option('linkssc_facebook', 'like' );
	add_option('linkssc_fbcolors', 'light' );
	add_option('linkssc_orderby', 'name');
	add_option('linkssc_order', 'DESC'); 
	add_option('linkssc_howmany', '-1');
	add_option('linkssc_template', $linkssc_default_template); 
	add_option('linkssc_template_b', ''); 
	add_option('linkssc_template_a', ''); 
}
register_activation_hook( __FILE__, 'linkssc_activation' );

//Uninstalling Action
function linkssc_uninstall(){
	delete_option('linkssc_facebook');	
	delete_option('linkssc_fbcolors');
	delete_option('linkssc_orderby');
	delete_option('linkssc_order');
	delete_option('linkssc_howmany');
	delete_option('linkssc_template');
	delete_option('linkssc_template_b'); 
	delete_option('linkssc_template_a'); 	
}
register_uninstall_hook( __FILE__, 'linkssc_uninstall' );

function linkssc_register_mysettings() { // whitelist options
	register_setting( 'links-shortcode-settings', 'linkssc_facebook' );
	register_setting( 'links-shortcode-settings', 'linkssc_fbcolors' );
	register_setting( 'links-shortcode-settings', 'linkssc_orderby' );
	register_setting( 'links-shortcode-settings', 'linkssc_order' );
	register_setting( 'links-shortcode-settings', 'linkssc_howmany' );
	register_setting( 'links-shortcode-settings', 'linkssc_template' );
	register_setting( 'links-shortcode-settings', 'linkssc_template_b' );
	register_setting( 'links-shortcode-settings', 'linkssc_template_a' );	
}

// action function for above hook
function linkssc_add_options_page() 
{
    // Add a new submenu under Settings:
    add_options_page(__('Links Shortcode','links-shortcode'), __('Links Shortcode','links-shortcode'), 'manage_options', 'links-shortcode-settings', 'linkssc_options_page');
	add_submenu_page( 'link-manager.php', __('Links Shortcode','links-shortcode'), __('Links Shortcode','links-shortcode'), 'manage_options', 'links-shortcode-settings', 'linkssc_options_page');
}

function linkssc_options_page() 
{
	global $linkssc_default_template;
	if (!current_user_can( 'manage_options' ) ) {
		wp_die ( __( 'You do not have sufficient permissions to access this page' ) );
	}
	$facebook = get_option('linkssc_facebook', 'like');
	$fbcolors = get_option('linkssc_fbcolors', 'light');
	$template = get_option('linkssc_template', $linkssc_default_template);
		if ($template=='') { $template = $linkssc_default_template; update_option('linkssc_template', $linkssc_default_template); }
	$template_b = get_option('linkssc_template_b', '');
	$template_a = get_option('linkssc_template_a', '');
	
	$orderby = get_option('linkssc_orderby', 'name');
	$order = get_option('linkssc_order', 'DESC');
	$howmany = get_option('linkssc_howmany', '-1');
	?>
	<div class="wrap">
	<div class="postbox" style="float:right;width:100px;margin:20px"><div class="inside" style="margin:10px"><?php _e('Like this plugin? Saves you work? Or using it in a professional context? A small contribution is highly appreciated.', 'all-related-posts'); ?><p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
		">
		<center><input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1"></center>
	</form>
	</div></div>
		
	<h2> <?php _e('Links Shortcode plugin settings','links-shortcode'); ?> </h2>

	<form method="post" action="options.php">
	<?php settings_fields( 'links-shortcode-settings' ); ?>	
	
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="linkssc_list" />	

	<h3> <?php _e('Default settings for the Links shortcode','links-shortcode'); ?></h3>
	<?php _e('Here you can specify the default options used when you used the [links] shortcode. You can overrule this on the shortcode itself, if you want.','links-shortcode'); ?><br />
	<?php _e('For help on using the shortcode (and for voting), please visit the plugin page on <a href="https://wordpress.org/extend/plugins/links-shortcode/" target="_blank">wordpress.org</a> or on <a href="http://blog.bigcircle.nl/about/wordpress-plugins" target="_blank">my blog</a>.','links-shortcode'); ?>
	<table class="form-table">
		<tr valign="top">
		<th scope="row"><?php _e('Show a facebook Like or Recommend button?','links-shortcode'); ?></th>
		<td><input type="radio" name="linkssc_facebook" value="like" <?php if ($facebook == 'like') echo 'CHECKED'; ?> /><?php _e('Yes, a Like button','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_facebook" value="recommend" <?php if ($facebook == 'recommend') echo 'CHECKED'; ?> /><?php _e('Yes, a Recommend button','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_facebook" value="none" <?php if ($facebook == 'none') echo 'CHECKED'; ?> /><?php _e('No','links-shortcode'); ?><br />
		</td>
		</tr>
		<!-- Change fb color sceme -->
		<tr valign="top">
		<th scope="row"><?php _e('What facebook color scheme?','links-shortcode'); ?></th>
		<td><input type="radio" name="linkssc_fbcolors" value="light" <?php if ($fbcolors == 'light') echo 'CHECKED'; ?> /><?php _e('Light','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_fbcolors" value="dark" <?php if ($fbcolors == 'dark') echo 'CHECKED'; ?> /><?php _e('Dark','links-shortcode'); ?><br />
		</td>
		</tr>
		<!-- End change fb color sceme -->
        <tr valign="top">
        <th scope="row"><?php _e('What to order your links by?','links-shortcode'); ?></th>
        <td><input type="radio" name="linkssc_orderby" value="name" <?php if ($orderby == 'name') echo 'CHECKED'; ?> /><?php _e('Link name','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_orderby" value="description" <?php if ($orderby == 'description') echo 'CHECKED'; ?> /><?php _e('Link description','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_orderby" value="url" <?php if ($orderby == 'url') echo 'CHECKED'; ?> /><?php _e('Link url','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_orderby" value="owner" <?php if ($orderby == 'owner') echo 'CHECKED'; ?> /><?php _e('Link owner, the user who added the link in the Links Manager','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_orderby" value="rating" <?php if ($orderby == 'rating') echo 'CHECKED'; ?> /><?php _e('Link rating','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_orderby" value="rand" <?php if ($orderby == 'rand') echo 'CHECKED'; ?> /><?php _e('Random','links-shortcode'); ?><br />
	<?php if (is_plugin_active('my-link-order/mylinkorder.php')) { ?>
			<input type="radio" name="linkssc_orderby" value="order" <?php if ($orderby == 'order') echo 'CHECKED'; ?> /><?php _e('As indicated using the My Link Order plugin','links-shortcode'); ?><br/>
	<?php } ?></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('How to order?','links-shortcode'); ?></th>
        <td><input type="radio" name="linkssc_order" value="ASC" <?php if ($order == 'ASC') echo 'CHECKED'; ?> /><?php _e('Ascending','links-shortcode'); ?><br />
			<input type="radio" name="linkssc_order" value="DESC" <?php if ($order == 'DESC') echo 'CHECKED'; ?> /><?php _e('Descending','links-shortcode'); ?>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row"><?php _e('How many links to show? (-1 for all)','links-shortcode'); ?></th>
        <td><input type="text" name="linkssc_howmany" value="<?php echo $howmany; ?>"  /><br />
		</td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><?php _e('How to display the links?','links-shortcode'); ?></th>
        <td><textarea name="linkssc_template" class="large-text code" rows="10"><?php echo $template; ?></textarea><br>
		<?php _e('The following codes can be used in the template: [link_url], [link_name], [link_image], [link_target], [link_description], [link_visible], [link_owner], [link_rating] (display as a number), [link_rating_stars] (display 0-5 stars), [link_updated] (only if not zero, otherwise empty), [link_rel], [link_notes], [link_rss], [fb_button]. You can provide alternative html to display in case a description, image or other property is not available for a link. See examples below.<br />
		The syntax is <b>[optional a||b]</b>, where b can be left empty, resulting in <b>[optional a||]</b>, (as in the examples below).','links-shortcode'); ?>
		</td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><?php _e('Provide an optional text or html to display before the links:','links-shortcode'); ?></th>
        <td><textarea name="linkssc_template_b" class="large-text code" rows="2"><?php echo $template_b; ?></textarea><br />
		<?php _e('You can use this for example to display links in a table. Example:','links-shortcode'); ?><pre>&lt;table></pre></td>
		</tr>
		
		<tr valign="top">
        <th scope="row"><?php _e('Provide an optional text or html to display after the links:','links-shortcode'); ?></th>
        <td><textarea name="linkssc_template_a" class="large-text code" rows="2"><?php echo $template_a; ?></textarea><br />
		<?php _e('Example:','links-shortcode'); ?><pre>&lt;/table></pre></td>
		</tr>
		
		<tr valign="top">
        <th scope="row"></th>
        <td><p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes','links-shortcode'); ?>" /></p></td>
		</tr>	
		
		<tr valign="top">
			<th scope="row"><?php _e('Examples','links-shortcode'); ?></th>
			<td>
				<b><?php _e('A list of links','links-shortcode'); ?></b><br /><?php _e('To show all links in a list with a Facebook like button, use the following main template:','links-shortcode'); ?><br />
				<pre style="margin-left:50px;"><?php echo htmlspecialchars($linkssc_default_template); ?></pre>
				<?php _e('<b>NB</b>: For compatibility reasons, in the example above in case you choose not to include a Facebook button, the plugin will automatically correct the DIV class from <b>\'links_sc_fb\'</b> to <b>\'links_sc\'</b> for optimal spacing.','links-shortcode'); ?>
				<?php _e('Leave the \'before\' and \'after\' template fields empty.','links-shortcode'); ?><br /><br />
				<b><?php _e('Links in a table','links-shortcode'); ?></b><br /><?php _e('To show all links in a table with images in a separate column at the left if available, use the following main template:','links-shortcode'); ?><br />
				<pre style="margin-left:50px;"><?php echo htmlspecialchars('<tr style="width:100%;">
<td style="width:100px;vertical-align:top">
[optional <a href="[link_url]" target=_blank><img src="[link_image]" border=0 style="width:100px"></a>||]
</td><td>
<div class="links_sc_fb" style="text-align:left">
[optional [date]: ||]<a href="[link_url]" target="_blank">[link_name]</a>
[optional <br />[link_description]||]
[optional <br />[fb_button]||]
</div>
</td></tr>'); ?>
				</pre>
				<?php _e('Enter the following in the \'before\' field:','links-shortcode'); ?><br />
				<pre style="margin-left:50px;"><?php echo htmlspecialchars('<table style="margin:0;padding:0;">'); ?></pre>
				<?php _e('And the following in the \'after field\':','links-shortcode'); ?>
				<pre style="margin-left:50px;"><?php echo htmlspecialchars('</table>'); ?></pre><br /><br />
			</td>
		</tr>
	</table>
	</form>
	</div>
	<?php
}

function linkssc_add_donate_link($links, $file) 
{
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin)
	{
		$donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=79AKXNVRT8YSQ&lc=NL&item_name=Links%20Shortcode%20plugin%20by%20Maarten&item_number=Links%20Shortcode%20plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank">'.__('Donate', 'links-shortcode').'</a>';
		$links[] = $donate_link;
	}
	return $links;
}
add_filter('plugin_row_meta', 'linkssc_add_donate_link', 10, 2 );

/*
The following enables filling the link_edited field for a link with a date when the link has been created or edited. 
It is based on "Andys Link Last Edited Meta Box" as described on http://fleacircusdir.livejournal.com/5498.html
(original author: AGC based on the work of Ozh and miekd)
*/

//See http://www.code-styling.de/english/how-to-use-wordpress-metaboxes-at-own-plugins
//    http://planetozh.com/blog/2008/02/wordpress-snippet-add_meta_box/
//    http://wordpress.org/extend/plugins/link-updated/
//    http://codex.wordpress.org/Function_Reference/add_meta_box

// function to update the link_edited field
function linkssc_update_link_editied($link_ID) {
    global $wpdb;
    $sql = "update wp_links set link_updated = NOW() where link_id = " . $link_ID . ";";
    $wpdb->query($sql);
}

// add meta box to show this date in the link editing screen
function linkssc_add_LastMod_box() {
    
    add_meta_box(
        'linkssclinkmodifieddiv', // id of the <div> we'll add
        'Last Modified', //title
        'linkssc_meta_box_add_last_modfied', // callback function that will echo the box content
        'link', // where to add the box: on "post", "page", or "link" page
        'side'  // location, 'normal', 'advanced', or 'side'
    );

}
// This function echoes the content of our meta box
function linkssc_meta_box_add_last_modfied($link) {
     if (! empty($link->link_id))
     {
    echo "Last Modified Date: ";
    echo $link->link_updated;
    }
    else
    { echo "New Link";}
}


?>