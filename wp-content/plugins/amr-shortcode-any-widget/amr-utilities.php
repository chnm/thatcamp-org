<?php
/*-----------------------------------*/
function get_sidebar_id ($name) { 
/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.  
take the first one that matches */
global $wp_registered_sidebars;	

	foreach ($wp_registered_sidebars as $i => $a) {
		if ((isset ($a['name'])) and ( $a['name'] === $name)) 
		return ($i);
	}
	return (false);
}
/*-----------------------------------*/
function amr_get_sidebar_name ($id) { /* dont need anymore ? or at least temporarily */
/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.  take the first one */
global $wp_registered_sidebars;	
	foreach ($wp_registered_sidebars as $i => $a) {
		if ((isset ($a['id'])) and ( $a['id'] === $id)) {
			if (isset($a['name'])) return ($a['name']);
			else return ($id);
		}
	}
	return (false);
}
/*-----------------------------------*/
function amr_check_if_widget_debug() {
	// only do these debug if we are logged in and are the administrator

	if ((!is_user_logged_in()) or (!current_user_can('administrator'))) 
		return false;
		
	if (isset($_REQUEST['do_widget_debug'])) {
		$url_without_debug_query = remove_query_arg( 'do_widget_debug');
		$eek = '<a href="'.$url_without_debug_query.'">Remove debug</a>';
		echo '<br/>Note: Debugs only shown to a Logged in Administrator.'
		.'<br />'
		.$eek
		.'<br />';
		return true;
		}
	else 
		return false;
}
/*-----------------------------------*/
function amr_show_widget_debug($type='', $atts=array()) {
global $wp_registered_sidebars, $wp_registered_widgets, $_wp_sidebars_widgets;
// only do these debug if we are logged in and are the administrator

	$debug = amr_check_if_widget_debug();
		
	if ($type=='empty') {
		if (current_user_can('administrator')) 
			echo '<br /> You are admin: <a href="'.add_query_arg('do_widget_debug','1').'">Try debug </a></b>'
			.'See a exclamation point ! above ?.  Hover over to see error message.'
			.'</p>'; 
	
		if ($debug) {
			
			echo '<p>As a last resort, we may dump the wp variables to do with sidebars and widgets. Maybe that will help you:</p>';
			$sidebars_widgets = wp_get_sidebars_widgets(); 
			echo '<h3> result of wp_get_sidebars_widgets():</h3>';
			foreach ($sidebars_widgets as $i=>$w) {
				echo '<br/>'.$i; var_dump($w);
				};

			echo '<h3>$_wp_sidebars_widgets:</h3>';
			var_dump($_wp_sidebars_widgets);
			//echo '<br /><h3>$wp_registered_widgets:</h3>';
			//var_dump($wp_registered_widgets);
			echo '<br /><h3>$wp_registered_sidebars:</h3>';
			var_dump($wp_registered_sidebars);
		}
	}
	
	if (($type=='which one') and ($debug)) { 
			echo '<h3>DEBUG on: Is your widget in the widgets_for_shortcodes sidebar?</h3>';
			//echo '<br />The shortcode attributes you entered are:<br />';
			//var_dump($atts);
			echo '<br /><h2>widgets_for_shortcodes sidebar and widgets</h2>';
			$found = false;
			foreach ($_wp_sidebars_widgets as $i=> $w) {
				if (($i == "widgets_for_shortcodes")) {
				echo 'Sidebar:&nbsp;<b>'.$i.': '.amr_get_sidebar_name($i).'</b> has widgets: <br />';
				$found = true;
				if (is_array($w)) {
					sort ($w);
					foreach ($w as $i2=> $w2) {
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$w2.' <br />';
					};
				}
				echo '<br />';
				}
				//else {echo ' '.$i;}
			};
			if (!$found) echo '<h2>widgets_for_shortcodes sidebar empty or not defined.</h2>';
		}	
}
/*-----------------------------------*/
function amr_save_shortcodes_sidebar() {  // when switching a theme, save the widgets we use for the shortcodes as they are getting overwritten
	$sidebars_widgets = wp_get_sidebars_widgets(); 
	if (!empty($sidebars_widgets['widgets_for_shortcodes']))
		update_option('sidebars_widgets_for_shortcodes_saved',$sidebars_widgets['widgets_for_shortcodes']);
	else {  // our shortcodes sidebar is empty  but when to fix ?

	}	
}
/*-----------------------------------*/
function amr_restore_shortcodes_sidebar() {  // when switching a theme, restore the widgets we use for the shortcodes as they are getting overwritten
global $_wp_sidebars_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets(); 
	if (empty($sidebars_widgets['widgets_for_shortcodes'])) {
		$sidebars_widgets['widgets_for_shortcodes'] = get_option('sidebars_widgets_for_shortcodes_saved');
		update_option('sidebars_widgets',$sidebars_widgets);
		
	}	
	
}
/*-----------------------------------*/
function amr_upgrade_sidebar() { // added in 2014 February for compatibility.. keep for how long. till no sites running older versions.?
	$sidebars_widgets = wp_get_sidebars_widgets(); 
	if (!empty($sidebars_widgets['Shortcodes']) and empty($sidebars_widgets['widgets_for_shortcodes'])) {  // we need to upgrade
		$sidebars_widgets['widgets_for_shortcodes'] = $sidebars_widgets['Shortcodes'];
		unset ($sidebars_widgets['Shortcodes']);
		update_option('sidebars_widgets',$sidebars_widgets );
		add_action( 'admin_notices', 'widgets_shortcode_admin_notice' );
	}
}	
	
/*-----------------------------------*/
function widgets_shortcode_admin_notice() {
    ?>
    <div class="updated">
        <p>Please go to widgets page and check your "widgets for shortcodes" sidebar.  It will hopefully have been corrected upgraded with your widgets and all should be fine.</p>
    </div>
    <?php
}
/*-----------------------------------*/

?>