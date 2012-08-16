<?php
// Set up Feed Me, Seymour information
$bavotasan_theme_data = get_theme_data(TEMPLATEPATH.'/style.css');
define('THEME_NAME', $bavotasan_theme_data['Name']);
define('THEME_AUTHOR', $bavotasan_theme_data['Author']);
define('THEME_HOMEPAGE', $bavotasan_theme_data['URI']);
define('THEME_VERSION', trim($bavotasan_theme_data['Version']));
define('THEME_URL', get_template_directory_uri());
define('THEME_FILE', "feed-me-seymour");

// Make theme available for translation
// Translations can be filed in the /languages/ directory
load_theme_textdomain(THEME_FILE, TEMPLATEPATH . '/languages');

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );

$feed_me_seymour_options = array (	
	array(	"name" => __("Site Width", "feed-me-seymour"),
			"desc" => __("Select the width of your site.", "feed-me-seymour"),
			"id" => "site_width",
			"default" => "800",
			"type" => "site"),
	
	array(  "name" => __("First Sidebar Width", "feed-me-seymour"),
			"desc" => __("What would you like your first sidebar width to be?", "feed-me-seymour"),
            "id" => "sidebar_width1",
			"default" => "180",
            "type" => "first-sidebar"),
			
	array(  "name" => __("Second Sidebar Width", "feed-me-seymour"),
			"desc" => __("What would you like your second sidebar width to be?", "feed-me-seymour"),
            "id" => "sidebar_width2",
			"default" => "180",
            "type" => "second-sidebar"),

	array(  "name" => __("Sidebar Location", "feed-me-seymour"),
			"desc" => __("Where would you like your sidebars located?", "feed-me-seymour"),
            "id" => "sidebar_location",
			"default" => "5",
            "type" => "location"),

	array(  "name" => __("Header Logo", "feed-me-seymour"),
			"desc" => __("If you would like to display a logo in the header, please enter the file path above.", "feed-me-seymour"),
            "id" => "logo_header",
            "type" => "logo"),	
			
	array(  "name" => __("Logo or Blog Name Location", "feed-me-seymour"),
			"desc" => __("Where do you want your Logo or Blog Name located?", "feed-me-seymour"),
            "id" => "logo_location",
			"default" => "fl",
            "type" => "logo-location"),	
			
	array(  "name" => __("Tag Line", "feed-me-seymour"),
			"desc" => __("Would you like to display a tag line beneath your blog name/logo?", "feed-me-seymour"),
            "id" => "tag_line",
			"default" => "1",
            "type" => "tag-line"),	
		
	array(  "name" => __("User Login", "feed-me-seymour"),
			"desc" => __("Would you like to have a User Login section at the top of your site?", "feed-me-seymour"),
            "id" => "user_login",
			"default" => "1",
            "type" => "login"),
			
	array(  "name" => __("RSS Button", "feed-me-seymour"),
			"desc" => __("Would you like an RSS Button in your header?", "feed-me-seymour"),
            "id" => "rss_button",
			"default" => "1",
            "type" => "rss"),
			
	array(  "name" => __("Number of Posts", "feed-me-seymour"),
			"desc" => __("How many posts would you like to appear on the main page?", "feed-me-seymour"),
            "id" => "number_posts",
			"default" => "9",
            "type" => "posts"),
			
	array(  "name" => __("Site Description", "feed-me-seymour"),
			"desc" => __("Add meta tag description (Excerpt used on single posts and pages)", "feed-me-seymour"),
            "id" => "site_description",
            "type" => "site-description"),

	array(  "name" => __("Keywords", "feed-me-seymour"),
			"desc" => __("Add meta tag keywords, separate by comma (Tags are used on single posts)", "feed-me-seymour"),
            "id" => "keywords",
            "type" => "keywords"),
				
	array(  "name" => __("Google Analytics", "feed-me-seymour"),
			"desc" => __("Add your Google Analytics code", "feed-me-seymour"),
            "id" => "google_analytics",
            "type" => "google")			
					
);
// setting up the $feed_me_seymour_values variable
$feed_me_seymour_values = get_option(THEME_FILE);
//delete_option(THEME_FILE);

function get_index($array, $index) {
  return isset($array[$index]) ? $array[$index] : null;
}

// CALL THEME OPTIONIS
function theme_option($var) {
	global $feed_me_seymour_values;
	$option = get_index($feed_me_seymour_values, $var);
	return $option;
}

// Set all default options
if(!$feed_me_seymour_values) {
	foreach ($feed_me_seymour_options as $default) {
		if(isset($default['id']) && isset($default['default'])) {
			$setdefaultvalues1[ $default['id'] ] = $default['default'];
		}
	}
	update_option(THEME_FILE, $setdefaultvalues1);
}

// Setting up the theme options CSS
$feed_me_seymour_values = get_option(THEME_FILE);
if($feed_me_seymour_values['site_width']) {
	$feed_me_seymour_site = $feed_me_seymour_values['site_width'];
	$feed_me_seymour_sidebar = $feed_me_seymour_values['sidebar_width1'];	
	$feed_me_seymour_secondsidebar =  $feed_me_seymour_values['sidebar_width2'];
	$feed_me_seymour_sidewidget = $feed_me_seymour_sidebar - 6;
	$feed_me_seymour_sidewidget2 = $feed_me_seymour_secondsidebar - 6;
	if(empty($feed_me_seymour_secondsidebar)) {
		$feed_me_seymour_content =  $feed_me_seymour_site - $feed_me_seymour_sidebar - 44;
	} else {
		$feed_me_seymour_content =  $feed_me_seymour_site - $feed_me_seymour_sidebar - $feed_me_seymour_secondsidebar - 64;		
	}
} else {
	$feed_me_seymour_site = 800;
	$feed_me_seymour_sidebar = 180;
	$feed_me_seymour_sidewidget = 174;
	$feed_me_seymour_content = 560;
}

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) )
	$content_width = $feed_me_seymour_content;

function pbt_header_css() {
	global $feed_me_seymour_site, $feed_me_seymour_sidebar, $feed_me_seymour_secondsidebar, $feed_me_seymour_sidewidget, $feed_me_seymour_sidewidget2, $feed_me_seymour_content;
?>
<style type="text/css">
body { width: <?php echo $feed_me_seymour_site; ?>px; }
#header { width: <?php echo $feed_me_seymour_site-26; ?>px; }
#mainwrapper { width: <?php echo $feed_me_seymour_site-2; ?>px; }
#sidebar { width: <?php echo $feed_me_seymour_sidebar; ?>px; }
#sidebar .side-widget, #sidebar .sidebox { width: <?php echo $feed_me_seymour_sidewidget; ?>px; }
#sidebar #sform { width: <?php echo ($feed_me_seymour_sidewidget-35); ?>px; }
<?php if(!empty($feed_me_seymour_secondsidebar)) { ?>
#secondsidebar { width: <?php echo $feed_me_seymour_secondsidebar; ?>px; }
#secondsidebar .side-widget { width: <?php echo $feed_me_seymour_sidewidget2; ?>px; }
#secondsidebar #sform { width: <?php echo ($feed_me_seymour_sidewidget2-35); ?>px; }
<?php } ?>
#leftcontent, #threecol, #threecol2, .commentlist { width: <?php echo $feed_me_seymour_content; ?>px; }
#leftcontent img { max-width: <?php echo $feed_me_seymour_content; ?>px; height: auto; }
</style>
	<?php
}

// Ajax save function
function save_theme_callback() {
	global $wpdb; // this is how you get access to the database

	$savevalues = array();
	
	$feed_me_seymour_options = explode("&", $_POST['option']);

	foreach ($feed_me_seymour_options as $value) {
		$key_value = explode("=",$value);
		$key = urldecode($key_value[0]);
		$value = urldecode($key_value[1]);
		$savevalues[ $key ] = $value; 
	}
	update_option(THEME_FILE, $savevalues);
	die();
}
add_action('wp_ajax_save_theme_options', 'save_theme_callback');

function mytheme_add_admin() {
	wp_register_script('effects_js', THEME_URL.'/admin/js/effects.js', array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' ),'',true);
	
	add_menu_page(THEME_FILE, "FMS", 'manage_options', THEME_FILE, 'pbt_options', THEME_URL.'/admin/images/icon.png');
	$themelayout = add_submenu_page(THEME_FILE, THEME_NAME." - Layout", __("Layout Options", "feed-me-seymour"), 'manage_options', THEME_FILE, 'pbt_options');
	add_action( "admin_print_scripts-$themelayout", 'pbt_admin_css' );
}
// initialize the theme
add_action('admin_menu', 'mytheme_add_admin'); 

// load the js and css on theme options page
function pbt_admin_css() {
	echo '<link rel="stylesheet" href="'.THEME_URL.'/admin/css/admin-style.css" />'."\n";
	wp_enqueue_script('effects_js');
}

// Setting up the layout options page tabs
function pbt_options() { 
?>
<div id="arturowrap" class="wrap">
    <h2><?php echo THEME_NAME." ".__("Layout Options", "feed-me-seymour"); ?></h2>
    <?php echo '<div id="message" class="updated fade" style="display: none;"><p><strong>'.THEME_NAME.' '.__("Options Saved", "feed-me-seymour").'</strong></p></div>'."\n"; ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <div id="side-info-column" class="inner-sidebar thinner">
            <a href="http://themes.bavotasan.com" target="_blank"><img src="<?php echo THEME_URL; ?>/admin/images/brand.png" class="bavota" alt="Themes by bavotasan.com" width="225" height="84" /></a>
            <a href="javascript:{}" id="savetheme"></a><div class="ajaxsave"></div>
            <br class="clear" />
            <div class="postbox thinner" id="themeresources">
            	<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Resources", "feed-me-seymour"); ?></span></h3>
                <div class="inside">
                    <ul>
                        <li><a href="http://themes.bavotasan.com" title="Themes by bavotasan.com">Themes by bavotasan.com</a></li>
                        <li><a href="http://support.bavotasan.com" title="<?php _e('Support Forum', "feed-me-seymour"); ?>"><?php _e('Support Forum', "feed-me-seymour"); ?></a></li>
                        <li><a href="http://themes.bavotasan.com/affiliates" title="<?php _e('Affiliates Program', "feed-me-seymour"); ?>"><?php _e('Affiliates Program', "feed-me-seymour"); ?></a></li>
                        <li><a href="http://bavotasan.com" title="bavotasan.com">bavotasan.com</a></li>
                    </ul>
 
                </div>
            </div>   
            <br class="clear" />
            <div class="postbox thinner" id="themeresources">
            	<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Donate", "feed-me-seymour"); ?></span></h3>
                <div class="inside">
                    <p class="donate"><?php printf(__("A lot of hard work went into creating %s. If you would like to show your support, please use the donate link below.", "feed-me-seymour"), "<strong>".THEME_NAME."</strong>"); ?><p>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="5745952">
                <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>    
                </div>
            </div> 
        </div> <!-- end of #side-info-column -->
	<form method="post" action="" id="themeform" class="themesbybavotasan">
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content thinmain">
                <div id='normal-sortables' class='meta-box-sortables'>
                    <div id="wrapper" class="arturo">
                        <div id="tabbed">
                        <ul class="tabs">
						<?php
                        $menuPages = array(
							__('Info', "feed-me-seymour") =>'pbt_info', 
							__('Main', "feed-me-seymour") =>'pbt_layout_options', 
							__('Header &amp; Footer', "feed-me-seymour") =>'pbt_header_options', 
							__('Sidebars', "feed-me-seymour") =>'pbt_sidebars_options', 
							__('SEO', "feed-me-seymour") =>'pbt_seo_options',
							__('Premium Themes', "feed-me-seymour") =>'pbt_upgrade'
						);
                        $x = 1;
                        foreach($menuPages as $menuPage => $pagefunction) {
                            echo '<li><a href="#tabbed-'.$x.'">'.$menuPage.'</a></li>';
                            $x++;
                        }
                        ?>
                        </ul>
                        </div>
                        <?php
                        $x = 1;
                        foreach($menuPages as $menuPage => $pagefunction) {
                            echo '<div class="tab-content" id="tabbed-'.$x.'">';
                            if($x>1 && $x<6) echo '<p class="openclose"><a href="#" class="openall">'.__("Open All", "feed-me-seymour").' [+]</a><a href="#" class="closeall">'.__("Close All", "feed-me-seymour").' [-]</a></p>';
                            $pagefunction();
                            echo '</div>';
                            $x++;
                        }	
                        ?>
                    </div> <!-- end of #wrapper -->
        		</div> <!-- end of #normal-sortables -->
        	</div> <!-- end of #post-body-content -->
        </div> <!-- end of #post-body -->
    </div> <!-- end of #poststuff -->
    </form>
</div> <!-- end of #wrap -->
<?php
}

////////////////////////
//
// Default input boxes
//
///////////////////////


// TEXTAREA
function textAreaBox($rows = 4, $valueName, $valueDesc, $valueID) {
?>
<div class="postbox">
	<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php echo $valueName; ?></span><small> - <?php echo $valueDesc; ?></small></h3>
	<div class="inside">
		<textarea name="<?php echo $valueID; ?>" cols="60" rows="<?php echo $rows; ?>"><?php echo stripslashes(theme_option($valueID)); ?></textarea>
		<br class="clear" />
	</div>
</div>
<?php
}

// INPUT TEXT
function textBox($size = 50, $valueName, $valueDesc, $valueID, $label = null, $maxlength = null, $align = null, $color = false) {
?>
<div class="postbox">
    <div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php echo $valueName; ?></span><small><?php if($valueDesc) echo " - ".$valueDesc; ?></small></h3>
    <div class="inside">
        <input type="text" name="<?php echo $valueID; ?>" size="<?php echo $size; ?>"<?php if($maxlength) echo ' maxlength="'.$maxlength.'"'; ?><?php if($align) echo ' class="'.$align.'"'; ?> value="<?php echo theme_option($valueID); ?>" /><?php if($label) echo '<label style="margin: 9px 0 0 5px;">'.$label.'</label>'; ?>
    <br class="clear" />
    </div>
</div>
<?php
}

// RADIO BUTTON
function radioBox($numof = 2, $valueName, $valueDesc, $valueID, $labels = null, $defaults = null) {
	if(!$labels) $labels = array( __('Yes', "feed-me-seymour"), __('No', "feed-me-seymour") );
	if(!$defaults) $defaults = array(1,2);
?>
<div class="postbox">
    <div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php echo $valueName; ?></span><small> - <?php echo $valueDesc; ?></small></h3>
    <div class="inside">
	<?php 
	$i = 0;
    for($x=1;$x<=$numof;$x++) {
		echo '<input  name="'.$valueID.'" type="radio" value="'.$defaults[$i].'"';
		if(theme_option($valueID) == $defaults[$i]) { echo " checked=\"checked\""; }
        echo ' />&nbsp;<label>'.$labels[$i].'</label>&nbsp;&nbsp;';
    	$i++;
    }
    ?>
    <br class="clear" />
    </div>
</div>
<?php
}


#####################
##  the info page  ##
#####################

function pbt_info() { 
?>
    <img src="<?php echo THEME_URL; ?>/screenshot.png" alt="<?php echo THEME_NAME; ?>" class="theme" width="200" height="150" />
    <?php
    echo '<p><ul><li><strong>'.__('Version', "feed-me-seymour").':</strong> '.THEME_VERSION.'</li><li><strong>'.__('Author', "feed-me-seymour").':</strong> <a href="http://bavotasan.com/">'.THEME_AUTHOR.'</a></li><li><strong>'.__('Built by', "feed-me-seymour").':</strong> <a href="http://themes.bavotasan.com/">Themes by bavotasan.com</a></li><li><strong>'.__('Theme home page', "feed-me-seymour").':</strong> <a href="'.THEME_HOMEPAGE.'">'.THEME_NAME.'</a></li></ul></p>'; 
    echo '<p>';
	printf(__("Thank you for downloading %s. Hope you enjoy using it!</p>", "feed-me-seymour"), '<strong>'.THEME_NAME.'</strong>');
	echo '</p><p>';
	_e("There are tons of layout possibilities available with this theme, as well as a bunch of cool features that will surely help you get your site looking and working it's best.", "feed-me-seymour");
	echo '</p>';
    echo '<p><a href="http://support.bavotasan.com/topic/how-to-customize-magazine-basic">'; _e('How to Customize a Basic Theme', "feed-me-seymour");
	echo '</a></p><p>';
    echo '<p><a href="http://themes.bavotasan.com/manual/">'; _e('Instruction Manual', "feed-me-seymour");
	echo '</a></p><p>';	
	echo '</p><p>';
    _e('If you have any questions, comments, or if you encounter a bug, please visit our <a href="http://support.bavotasan.com/">Support Forum</a> and let us know.', "feed-me-seymour"); 
	echo '</p>';
}

########################
##  the upgrade page  ##
########################

function pbt_upgrade() {
?>
    <div class="upgrade">
	    <div class="imgbox">
        	<a href="http://themes.bavotasan.com/our-themes/premium-themes/moderno/" title="Moderno" class="img-wrap"><img width="250" height="160" src="http://themes.bavotasan.com/wp-content/uploads/2010/03/moderno-250x160.jpg" class="attachment-category wp-post-image" alt="" title="moderno" /></a> <a href="http://demos.bavotasan.com/?wptheme=Moderno" class="link" title="<?php _e('View Demo', "feed-me-seymour"); ?>"><?php _e('View Demo', "feed-me-seymour"); ?></a><a href="https://www.e-junkie.com/ecom/gb.php?i=wpt-mod&amp;c=single&amp;cl=93121" target="ejejcsingle" class="link buy"><?php _e('Buy Now', "feed-me-seymour"); ?></a> <a href="http://themes.bavotasan.com/our-themes/premium-themes/moderno/" class="link" title="<?php _e('More Info', "feed-me-seymour"); ?>"><?php _e('More Info', "feed-me-seymour"); ?></a>
        </div>
	    <div class="imgbox">
        	<a href="http://themes.bavotasan.com/our-themes/premium-themes/magazine-premium/" title="Magazine Premium" class="img-wrap"><img width="250" height="160" src="http://themes.bavotasan.com/wp-content/uploads/2010/01/magpremfinal-250x160.jpg" class="attachment-category wp-post-image" alt="" title="magpremfinal" /></a> <a href="http://demos.bavotasan.com/?wptheme=Magazine Premium" class="link" title="<?php _e('View Demo', "feed-me-seymour"); ?>"><?php _e('View Demo', "feed-me-seymour"); ?></a><a href="https://www.e-junkie.com/ecom/gb.php?i=wpt-map&amp;c=single&amp;cl=93121" target="ejejcsingle" class="link buy"><?php _e('Buy Now', "feed-me-seymour"); ?></a> <a href="http://themes.bavotasan.com/our-themes/premium-themes/magazine-premium/" class="link" title="<?php _e('More Info', "feed-me-seymour"); ?>"><?php _e('More Info', "feed-me-seymour"); ?></a>
        </div>    
	    <div class="imgbox">
        	<a href="http://themes.bavotasan.com/our-themes/premium-themes/stationery/" title="Stationery" class="img-wrap"><img width="250" height="160" src="http://themes.bavotasan.com/wp-content/uploads/2009/08/stationery-250x160.jpg" class="attachment-category wp-post-image" alt="" title="stationery" /></a> <a href="http://demos.bavotasan.com/?wptheme=Stationery" class="link" title="<?php _e('View Demo', "feed-me-seymour"); ?>"><?php _e('View Demo', "feed-me-seymour"); ?></a><a href="https://www.e-junkie.com/ecom/gb.php?i=wpt-sta&amp;c=single&amp;cl=93121" target="ejejcsingle" class="link buy"><?php _e('Buy Now', "feed-me-seymour"); ?></a> <a href="http://themes.bavotasan.com/our-themes/premium-themes/stationery/" class="link" title="<?php _e('More Info', "feed-me-seymour"); ?>"><?php _e('More Info', "feed-me-seymour"); ?></a>
        </div>
        <div class="imgbox">
        	<a href="http://themes.bavotasan.com/our-themes/premium-themes/illustrious/" title="Illustrious" class="img-wrap"><img width="250" height="160" src="http://themes.bavotasan.com/wp-content/uploads/2009/10/illustrious-250x160.jpg" class="attachment-category wp-post-image" alt="" title="illustrious" /></a> <a href="http://demos.bavotasan.com/?wptheme=Illustrious" class="link" title="<?php _e('View Demo', "feed-me-seymour"); ?>"><?php _e('View Demo', "feed-me-seymour"); ?></a><a href="https://www.e-junkie.com/ecom/gb.php?i=wpt-ill&amp;c=single&amp;cl=93121" target="ejejcsingle" class="link buy"><?php _e('Buy Now', "feed-me-seymour"); ?></a> <a href="http://themes.bavotasan.com/our-themes/premium-themes/illustrious/" class="link" title="<?php _e('More Info', "feed-me-seymour"); ?>"><?php _e('More Info', "feed-me-seymour"); ?></a>
        </div>
	</div>
    <br class="clear" />
<?php
}

###############################
##  the layout options page  ##
###############################

function pbt_layout_options() {
	global $feed_me_seymour_options;

	foreach ($feed_me_seymour_options as $value) { 
		switch ( get_index($value,'type') ) {
	
			case "site":
				radioBox(2, $value['name'], $value['desc'], $value['id'], array( __('800px', "feed-me-seymour"), __('1024px', "feed-me-seymour")), array(800,1024));			
			break;
				
			case "posts":
				textBox(2, $value['name'], $value['desc'], $value['id'], '', 2, 'center');			
			break;
		} 
	}
}


###############################
##  the header options page  ##
###############################

function pbt_header_options() {
    global $feed_me_seymour_options;

	foreach ($feed_me_seymour_options as $value) { 
		switch ( get_index($value,'type') ) {
	
			case "logo":
			?>
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php echo $value['name']; ?></span><small> - <?php echo $value['desc']; ?></small></h3>
				<div class="inside">
					<input type="text" size="50" name="<?php echo $value['id']; ?>" value="<?php echo theme_option($value['id']); ?>" />
					<?php 
					echo '<div class="headerlogo"></div>';
					?> 
				<br class="clear" />
				</div>
			</div>
			<?php
			break;
			
			case "logo-location":
			?>
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php echo $value['name']; ?></span><small> - <?php echo $value['desc']; ?></small></h3>
				<div class="inside">
					<table>
						<tr>
							<td style="padding-right: 15px;">
								<img src="<?php echo THEME_URL; ?>/admin/images/logoleft.png" alt="" />
							</td>
							<td style="padding-right: 15px;">
								<img src="<?php echo THEME_URL; ?>/admin/images/logoright.png" alt="" />
							</td>
							<td style="padding-right: 15px;">
								<img src="<?php echo THEME_URL; ?>/admin/images/logomiddle.png" alt="" />
							</td>
						</tr>
						<tr>
							<td align="center" style="padding-right: 15px;">
								<input  name="<?php echo $value['id']; ?>" type="radio" value="fl"<?php if(theme_option($value['id']) == "fl") { echo " checked=\"checked\""; } ?> />
							</td>
							<td align="center" style="padding-right: 15px;">
								<input  name="<?php echo $value['id']; ?>" type="radio" value="fr"<?php if(theme_option($value['id']) == "fr") { echo " checked=\"checked\""; } ?> />
							</td>
							<td align="center" style="padding-right: 15px;">
								<input  name="<?php echo $value['id']; ?>" type="radio" value="aligncenter"<?php if(theme_option($value['id']) == "aligncenter") { echo " checked=\"checked\""; } ?> />
							</td>
						</tr>
					</table>
					<br class="clear" />
					</div>
			 </div>
			<?php break;			

			case "tag-line":
				radioBox(2, $value['name'], $value['desc'], $value['id']);
			break;
			
			case "login":
				radioBox(2, $value['name'], $value['desc'], $value['id']);
			break;

			case "rss":
				radioBox(2, $value['name'], $value['desc'], $value['id']);
			break;	
					
		}
	}	
}

#################################
##  the sidebars options page  ##
#################################

function pbt_sidebars_options() {
	global $feed_me_seymour_options;

	foreach ($feed_me_seymour_options as $value) { 
		switch ( get_index($value,'type') ) {
				
			case "first-sidebar":
				radioBox(2, $value['name'], $value['desc'], $value['id'], array(__("180px", "feed-me-seymour"), __("300px", "feed-me-seymour")), array(180,300));			
			break;
			   
			case "second-sidebar":
				radioBox(3, $value['name'], $value['desc'], $value['id'], array(__("180px", "feed-me-seymour"), __("300px", "feed-me-seymour"), __("None", "feed-me-seymour")), array(180,300,0));			
			break;			   
			   
			case "location":
			?>
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php echo $value['name']; ?></span><small> - <?php echo $value['desc']; ?></small></h3>
				<div class="inside">
					<div id="oneSidebar">
                        <table>
                            <tr>
                                <td style="padding-right: 15px;">
                                    <img src="<?php echo THEME_URL; ?>/admin/images/oneleft.png" alt="One Left" />
                                </td>
                                <td style="padding-right: 15px;">
                                    <img src="<?php echo THEME_URL; ?>/admin/images/oneright.png" alt="One Right" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding-right: 15px;">
                                    <input  name="<?php echo $value['id']; ?>" type="radio" value="1"<?php if(theme_option($value['id']) == "1") { echo " checked=\"checked\""; } ?> />
                                </td>
                                <td align="center" style="padding-right: 15px;">
                                    <input  name="<?php echo $value['id']; ?>" type="radio" value="2"<?php if(theme_option($value['id']) == "2") { echo " checked=\"checked\""; } ?> />
                                </td>
                            </tr>
                        </table>
					</div>
					<div id="twoSidebar">
                        <table>
                            <tr>
                                <td style="padding-right: 15px;">
                                    <img src="<?php echo THEME_URL; ?>/admin/images/twoleft.png" alt="" />
                                </td>
                                <td style="padding-right: 15px;">
                                    <img src="<?php echo THEME_URL; ?>/admin/images/tworight.png" alt="" />
                                </td>
                                <td style="padding-right: 15px;">
                                    <img src="<?php echo THEME_URL; ?>/admin/images/twoseparate.png" alt="" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding-right: 15px;">
                                    <input  name="<?php echo $value['id']; ?>" type="radio" value="3"<?php if(theme_option($value['id']) == "3") { echo ' checked="checked"'; } ?> />
                                </td>
                                <td align="center" style="padding-right: 15px;">
                                    <input  name="<?php echo $value['id']; ?>" type="radio" value="4"<?php if(theme_option($value['id']) == "4") { echo ' checked="checked"'; } ?> />
                                </td>
                                <td align="center" style="padding-right: 15px;">
                                    <input  name="<?php echo $value['id']; ?>" type="radio" value="5"<?php if(theme_option($value['id']) == "5") { echo ' checked="checked"'; } ?> />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <p class="locerror"></p>
                    <br class="clear" />
				</div>
			</div>
			<?php break;					
		} 
	}
}

############################
##  the seo options page  ##
############################

function pbt_seo_options() {
    global $feed_me_seymour_options;

	foreach ($feed_me_seymour_options as $value) { 
		switch ( get_index($value,'type') ) {
	
			case "site-description":
				textAreaBox(4, $value['name'], $value['desc'], $value['id']);
			break;
	
			case "keywords":
				textAreaBox(4, $value['name'], $value['desc'], $value['id']);
			break;
	
			case "google":
				textAreaBox(6, $value['name'], $value['desc'], $value['id']);
			break;
		}
	}
}

function pbt_widgets_init() {
	// include the widgets
	include(TEMPLATEPATH.'/widgets/widget_login.php');
	include(TEMPLATEPATH.'/widgets/widget_feature.php'); 
	
	// Initiating the sidebars
	if (function_exists("register_sidebar")) {
		register_sidebar(array(
		'name' => 'Sidebar One',
			'before_widget' => '<div class="side-widget">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>'
		));
		
		if (theme_option('sidebar_width2')!=0) {
			register_sidebar(array(
				'name' => 'Sidebar Two',
				'before_widget' => '<div class="side-widget">',
				'after_widget' => '</div>',
				'before_title' => '<h2>',
				'after_title' => '</h2>'
			));
		}
	}
}
add_action( 'widgets_init', 'pbt_widgets_init' );

// Tags for keywords
function csv_tags() {
    $posttags = get_the_tags();
 	$csv_tags = '';
    if($posttags) {
		foreach((array)$posttags as $tag) {
			$csv_tags .= $tag->name . ',';
		}
	}
    echo '<meta name="keywords" content="'.$csv_tags.theme_option('keywords').'" />';
}

// Theme excerpts
function theme_excerpt($num, $readmore = false) {
	if($readmore) {
		$link = '<br /><a href="'.get_permalink().'" class="more-link">'.__("Read more &raquo;", "feed-me-seymour").'</a>';
	}
	
	$limit = $num;
	if(!$limit) $limit = 55;
	$excerpt = explode(' ', get_the_excerpt(), $limit);
	if (count($excerpt)>=$limit) {
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).'...'.isset($link);
	} else {
		$excerpt = implode(" ",$excerpt).isset($link);
	}	
	$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	echo '<p>'.$excerpt.'</p>';
}

// Meta description
function metaDesc() {
	$content = apply_filters('the_content_rss', strip_tags(get_the_content()));
	$content = preg_replace('/\[.+\]/','', $content);
	$chars = array("", "\n", "\r", "chr(13)",  "\t", "\0", "\x0B");
	$content = htmlentities(str_replace($chars, " ", $content));
	if(empty($content)) {
		$content = htmlentities(theme_option('site_description'));
	}
	if (strlen($content) < 155) {
		echo $content;
	} else {
		$desc = substr($content,0,155);
		echo $desc."...";
	}
}

// image grabber function
function resize($w,$h,$class='alignleft',$showlink=true) {
	global $more, $post;
	$title = the_title_attribute('echo=0');
	if($showlink) {
		$link = "<a href='".get_permalink()."' title='$title'>";
		$linkend = "</a>";
	} else {
		$link ="";
		$linkend="";
	}
	$more = 1;
	$content = get_the_content();
	$pattern = '/<img[^>]+src[\\s=\'"]';
	$pattern .= '+([^"\'>\\s]+)/is';
	$more = 0;
	if(preg_match($pattern,$content,$match)) {
		$theImage =  "$link<img src=\"$match[1]\" class=\"$class\" alt=\"$title\" width=\"$w\" height=\"$h\" />$linkend\n\n";
		return $theImage;
	}
}

// Comments
function mytheme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>">
        <div class="comment-avatar">
        	<?php echo get_avatar( $comment, 54 ); ?>
        </div>     
        <div class="comment-author">
        	<?php echo get_comment_author_link()." ";
        	printf(__('on %1$s at %2$s', "feed-me-seymour"), get_comment_date(),get_comment_time()); 
			edit_comment_link(__('(Edit)', "feed-me-seymour"),'  ','');
			?>
        </div>
        <div class="comment-text">
	        <?php if ($comment->comment_approved == '0') { _e('<em>Your comment is awaiting moderation.</em>', "feed-me-seymour"); } ?>
        	<?php comment_text() ?>
        </div>
        <?php if($args['max_depth']!=$depth && comments_open()) { ?>
        <div class="reply">
        	<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
        </div>
        <?php } ?>
	</div>
<?php
}
// This theme allows users to set a custom background
if(function_exists('add_custom_background')) add_custom_background('set_theme_background');

function set_theme_background() {
	$bgimage = get_background_image();
	$bgcolor = get_background_color();
	//echo get_background_repeat();
	
	echo "<style type='text/css'>\n";
	 
	if(!empty($bgimage)) {
		$background_styles = 'background-image: url(\'' . get_theme_mod('background_image', '') . '\');'
		. ' background-repeat: ' . get_theme_mod('background_repeat', 'repeat') . ';'
		. ' background-position: top ' . get_theme_mod('background_position_x', 'left') .  ';' . 'background-attachment: '. get_theme_mod('background_attachment', 'scroll');
		echo "body { ".$background_styles."); } \n";
	} elseif(!empty($bgcolor)) {
		echo "body { background-color: #".$bgcolor."; }; \n";
	} else {
		echo "body { background: url(".THEME_URL."/images/background.png) #fee9c9 repeat-x; }\n";
	}
	echo "</style>";
}

// This theme uses wp_nav_menu()
if(function_exists('register_nav_menu')) {
	register_nav_menu('main', 'Main Navigation Menu');
}

add_theme_support( 'automatic-feed-links' );

function display_none() { }

/* ADDING NEW THUMBNAIL STUFF */
if(!function_exists('pbt_AddThumbColumn') && function_exists('add_theme_support')) {
 	add_theme_support('post-thumbnails');
 
	function pbt_AddThumbColumn($cols) {
		$cols['thumbnail'] = __('Thumbnail', "feed-me-seymour");
		return $cols;
	}
 
	function pbt_AddThumbValue($column_name, $post_id) {
		$width = (int) 35;
		$height = (int) 35;

		if ( 'thumbnail' == $column_name ) {
			// thumbnail of WP 2.9
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			// image from gallery
			if ($thumbnail_id) {
				$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			}
			if(isset($thumb) && $thumb) {
				echo $thumb;
			} else {
				echo __('None', "feed-me-seymour");
			}
		}
	}
 
	// for posts
	add_filter( 'manage_posts_columns', 'pbt_AddThumbColumn' );
	add_action( 'manage_posts_custom_column', 'pbt_AddThumbValue', 10, 2 );
 
	// for pages
	add_filter( 'manage_pages_columns', 'pbt_AddThumbColumn' );
	add_action( 'manage_pages_custom_column', 'pbt_AddThumbValue', 10, 2 );
}
?>