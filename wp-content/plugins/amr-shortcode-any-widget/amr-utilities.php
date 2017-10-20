<?php

function amr_show_shortcode_widget_possibilities () {
global $_wp_sidebars_widgets;	

	$sidebars_widgets = $_wp_sidebars_widgets;
	ksort ($sidebars_widgets);  // push inactive down the bottom of the list
	$text= '<ul>';
	foreach ($sidebars_widgets as $sidebarid => $sidebar) {	
	
		if (is_array($sidebar)) {
			$text .= '<li><em>[do_widget_area '.$sidebarid.']</em><ul>';
			foreach ($sidebar as $i=> $w) {		
									
				$text .=  '<li>';
				$text .=  '[do_widget id="'.$w.'"]';
				$text .= '</li>';	
										
			};	
			$text .=   '</ul></li>';
		}	
	}
	$text .=  '</ul>';
	return ($text);
}

function amr_get_widgets_sidebar($wid) { 
/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.  
take the first one that matches */
global $_wp_sidebars_widgets;	

	foreach ($_wp_sidebars_widgets as $sidebarid => $sidebar) {	
		
		if (is_array($sidebar) ) { // ignore the 'array version' sidebarid that isnt actually a sidebar
			foreach ($sidebar as $i=> $w) {		
				if ($w == $wid) { 
					return 	$sidebarid;
				}	
			};	
		}	
	}
	return (false); // widget id not in any sidebar
}

function amr_get_sidebar_id ($name) { 
/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.  
take the first one that matches */
global $wp_registered_sidebars;	

	foreach ($wp_registered_sidebars as $i => $a) {
		if ((isset ($a['name'])) and ( $a['name'] === $name)) 
		return ($i);
	}
	return (false);
}

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

function amr_check_if_widget_debug() {
global $said;
	// only do these debug if we are logged in and are the administrator

	if (is_admin()) return false;   // if running in backend, then do not do debug.  20151217
	
	if ((!is_user_logged_in()) or (!current_user_can('administrator'))) 
		return false;
		
	if (isset($_REQUEST['do_widget_debug'])) {
		if (empty($said)) {
			$said = true;
		}	
		else return true;
		
		$url_without_debug_query = esc_url(remove_query_arg( 'do_widget_debug'));
		$eek = '<a href="'.$url_without_debug_query.'">Remove debug</a>';
		echo '<br/>Note: Debug help is only shown to a logged-in Administrator.'
		.$eek
		.'<br />';
		$text =	amr_show_shortcode_widget_possibilities () ;	
		echo $text;
		return true;
		}
	else 
		return false;
}

function amr_show_widget_debug($type='', $name, $id, $sidebar) {
global $wp_registered_sidebars, $wp_registered_widgets, $_wp_sidebars_widgets, $debugcount;
// only do these debug if we are logged in and are the administrator	
	
	$debug = amr_check_if_widget_debug();
	$text =	amr_show_shortcode_widget_possibilities () ;	
		 	
	if ($type=='empty') { 
		if (current_user_can('administrator')) 		
			$text = '<p>Problem with do_widget shortcode?  Try one of the following:</p>'.$text; 		
	}
	elseif (($type=='which one') and ($debug)) { 
			$text = '<p>Debug help is on: Is your widget in the widgets_for_shortcodes sidebar?</p>'
			.$text;
		}	

	return ($text);
}

function amr_save_shortcodes_sidebar() {  // when switching a theme, save the widgets we use for the shortcodes as they are getting overwritten
	$sidebars_widgets = wp_get_sidebars_widgets(); 
	if (!empty($sidebars_widgets['widgets_for_shortcodes']))
		update_option('sidebars_widgets_for_shortcodes_saved',$sidebars_widgets['widgets_for_shortcodes']);
	else {  // our shortcodes sidebar is empty  but when to fix ?

	}	
}

function amr_restore_shortcodes_sidebar() {  // when switching a theme, restore the widgets we use for the shortcodes as they are getting overwritten
global $_wp_sidebars_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets(); 
	if (empty($sidebars_widgets['widgets_for_shortcodes'])) {
		$sidebars_widgets['widgets_for_shortcodes'] = get_option('sidebars_widgets_for_shortcodes_saved');
		update_option('sidebars_widgets',$sidebars_widgets);
		
	}	
	
}

function amr_upgrade_sidebar() { // added in 2014 February for compatibility.. keep for how long. till no sites running older versions.?
	$sidebars_widgets = wp_get_sidebars_widgets(); 
	if (!empty($sidebars_widgets['Shortcodes']) and empty($sidebars_widgets['widgets_for_shortcodes'])) {  // we need to upgrade
		$sidebars_widgets['widgets_for_shortcodes'] = $sidebars_widgets['Shortcodes'];
		unset ($sidebars_widgets['Shortcodes']);
		update_option('sidebars_widgets',$sidebars_widgets );
		add_action( 'admin_notices', 'widgets_shortcode_admin_notice' );
	}
}	
	
function widgets_shortcode_admin_notice() {
    ?>
    <div class="updated">
        <p>Please go to widgets page and check your "widgets for shortcodes" sidebar.  It will hopefully have been corrected upgraded with your widgets and all should be fine.</p>
    </div>
    <?php
}


?>