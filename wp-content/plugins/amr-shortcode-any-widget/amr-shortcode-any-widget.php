<?php
/*
Plugin Name: amr shortcode any widget
Plugin URI: http://webdesign.anmari.com/shortcode-any-widget/
Description: Include any widget in a page for any theme.  [do_widget widgetname ] or  [do_widget "widget name" ] [do_widget id=widgetnamedashed-n ]or include a whole widget area [do_widget_area]. Please see <a href="https://wordpress.org/plugins/amr-shortcode-any-widget/faq/">FAQ</a>.
Author: anmari
Version: 3.7
Author URI: http://webdesign.anmari.com

*/


add_action('in_widget_form', 'amr_spice_get_widget_id');
function amr_spice_get_widget_id($widget_instance) {
	echo "<p><strong>To use as shortcode with id:</strong> ";
    if ($widget_instance->number=="__i__"){
		echo "Save the widget first!</p>"   ;
	}  else {
       echo "[do_widget id=".$widget_instance->id. "]</p>";
    }
}

function amr_remove_widget_class($params) {  // remove the widget classes
	if (!empty($params[0]['before_widget'])) {
		$params[0]['before_widget'] = 
			str_replace ('"widget ','"',$params[0]['before_widget']);
	}
	
	if (!empty($params[0]['before_title'])) {  

		$params[0]['before_title'] = 
			$params[0]['before_title'] = str_replace ('widget-title','',$params[0]['before_title']);
			
	}
	
	return ($params);
}

function amr_do_widget_area($atts) {

global $wp_registered_widgets, $_wp_sidebars_widgets, $wp_registered_sidebars;

	extract(shortcode_atts(array(
		'widget_area' => 'widgets_for_shortcodes',
		'class' => 'amr-widget-area', /* the widget class is picked up automatically.  If we want to add an additional class at the wrap level to try to match a theme, use this */
		'widget_area_class' =>  '',  /* option to disassociate from themes widget styling use =none*/
		'widget_classes' =>  ''  /* option to disassociate from themes widget styling */

	), $atts));

	if (!empty($atts)) {
		if (($widget_area == 'widgets_for_shortcodes' ) and !empty($atts[0]))  
			$widget_area = $atts[0];
	}
	
	if (empty ($wp_registered_sidebars[$widget_area])) {
		echo '<br/>Widget area "'.$widget_area.'" not found. Registered widget areas (sidebars) are: <br/>';
		foreach ($wp_registered_sidebars as $area=> $sidebar) echo $area.'<br />';
	}
	//if (isset($_REQUEST['do_widget_debug']) and current_user_can('administrator')) var_dump( $wp_registered_sidebars); /**/
	
	if ($widget_area_class=='none')
		$class = '';
	else {	
		
		if (!empty($widget_area_class))  //2014 08  
			$class .= 'class="'.$class.' '.$widget_area_class.'"';
		else
		$class = 'class="'.$class.'"';		
	}	

	if (!empty($widget_classes) and ($widget_classes=='none'))
		add_filter('dynamic_sidebar_params','amr_remove_widget_class');
	
	ob_start();  /* catch the echo output, so we can control where it appears in the text  */
	dynamic_sidebar($widget_area);
	$output = ob_get_clean();
	remove_filter('dynamic_sidebar_params','amr_remove_widget_class');

	$output = PHP_EOL.'<div id="'.$widget_area.'" '.$class. '>'
		.$output			
		.'</div>'.PHP_EOL;
			
return ($output);
}

function amr_do_widget($atts) {

global $wp_registered_widgets, $_wp_sidebars_widgets, $wp_registered_sidebars;

/* check if the widget is in  the shortcode x sidebar  if not , just use generic, 
if it is in, then get the instance  data and use that */

	if (is_admin()) {return '';}  // eg in case someone decides to apply content filters when apost is saved, and not all widget stuff is there.
	extract(shortcode_atts(array(
		'sidebar' => 'Widgets for Shortcodes', //default
		'id' => '',
		'name' => '', /* MKM added explicit 'name' attribute.  For existing users we still need to allow prev method, else too many support queries will happen */
		'title' => '',   /* do the default title unless they ask us not to - use string here not boolean */
		'class' => 'amr_widget', /* the widget class is picked up automatically.  If we want to add an additional class at the wrap level to try to match a theme, use this */
		'wrap' => '', /* wrap the whole thing - title plus widget in a div - maybe the themes use a div, maybe not, maybe we want that styling, maybe not */
		'widget_classes' =>  ''  /* option to disassociate from themes widget styling */
	), $atts));
	
	if (isset($_wp_sidebars_widgets) ) {
		amr_show_widget_debug('which one', $name, $id, $sidebar);  //check for debug prompt and show widgets in shortcode sidebar if requested and logged in etc
	}
	else { 
		$output = '<br />No widgets defined at all in any sidebar!'; 
		return ($output);
	}
	
	/* compatibility check - if the name is not entered, then the first parameter is the name */
	if (empty($name) and !empty($atts[0]))  
		$name = $atts[0];
	/* the widget need not be specified, [do_widget widgetname] is adequate */
	if (!empty($name)) {  // we have a name
		$widget = $name;
		
		foreach ($wp_registered_widgets as $i => $w) { /* get the official internal name or id that the widget was registered with  */
			if (strtolower($w['name']) === strtolower($widget)) 
				$widget_ids[] = $i;
			//if ($debug) {echo '<br /> Check: '.$w['name'];}
		}	
		
		if (!($sidebarid = amr_get_sidebar_id ($sidebar))) {
			$sidebarid=$sidebar;   /* get the official sidebar id for this widget area - will take the first one */		
		}	
		
		
	}	
	else { /* check for id if we do not have a name */
	
			if (!empty($id))  { 	/* if a specific id has been specified */			
				foreach ($wp_registered_widgets as $i => $w) { /* get the official internal name or id that the widget was registered with  */
					if ($w['id'] === $id) {
						$widget_ids[] = $id;
					}	
				}
				//echo '<h2>We have an id: '.$id.'</h2>'; 	if (!empty($widget_ids)) var_dump($widget_ids);	
			}
			else {
				$output = '<br />No valid widget name or id given in shortcode parameters';	
			
				return $output;		
			}
			// if we have id, get the sidebar for it
			$sidebarid = amr_get_widgets_sidebar($id);
			if (!$sidebarid) {
				$output =  '<br />Widget not in any sidebars<br />';
				return $output;
			}	
	}
	
	if (empty($widget)) 	$widget = '';
	if (empty($id)) 		$id = '';
	
	if (empty ($widget_ids)) { 
		$output =  '<br />Error: Your Requested widget "'.$widget.' '.$id.'" is not in the widget list.<br />';
		$output .= amr_show_widget_debug('empty', $name, $id, $sidebar);
		return ($output) ;
	}		

		
	if (empty($widget)) 
		$widget = '';

	$content = ''; 			
	/* if the widget is in our chosen sidebar, then use the options stored for that */

	if ((!isset ($_wp_sidebars_widgets[$sidebarid])) or (empty ($_wp_sidebars_widgets[$sidebarid]))) { // try upgrade
		amr_upgrade_sidebar();
	}
	
	//if we have a specific sidebar selected, use that
	if ((isset ($_wp_sidebars_widgets[$sidebarid])) and (!empty ($_wp_sidebars_widgets[$sidebarid]))) {
			/* get the intersect of the 2 widget setups so we just get the widget we want  */

		$wid = array_intersect ($_wp_sidebars_widgets[$sidebarid], $widget_ids );
	
	}
	else { /* the sidebar is not defined or selected - should not happen */
			if (isset($debug)) {  // only do this in debug mode
				if (!isset($_wp_sidebars_widgets[$sidebarid]))
					$output =  '<p>Error: Sidebar "'.$sidebar.'" with sidebarid "'.$sidebarid.'" is not defined.</p>'; 
				 // shouldnt happen - maybe someone running content filters on save
				else 
					$output =  '<p>Error: Sidebar "'.$sidebar.'" with sidebarid "'.$sidebarid.'" is empty (no widgets)</p>'; 
			}		
		}
	
	$output = '';
	if (empty ($wid) or (!is_array($wid)) or (count($wid) < 1)) { 

		$output = '<p>Error: Your requested Widget "'.$widget.'" is not in the "'.$sidebar.'" sidebar</p>';
		$output .= amr_show_widget_debug('empty', $name, $id, $sidebar);

		unset($sidebar); 
		unset($sidebarid);

		}
	else {	
		/*  There may only be one but if we have two in our chosen widget then it will do both */
		$output = '';
		foreach ($wid as $i=>$widget_instance) {
			ob_start();  /* catch the echo output, so we can control where it appears in the text  */
			amr_shortcode_sidebar($widget_instance, $sidebar, $title, $class, $wrap, $widget_classes);
			$output .= ob_get_clean();
			}
	}
			
	return ($output);
	}
	

	
function amr_shortcode_sidebar( $widget_id, 
	$name="widgets_for_shortcode", 
	$title=true, 
	$class='', 
	$wrap='', 
	$widget_classes='') { /* This is basically the wordpress code, slightly modified  */
	global $wp_registered_sidebars, $wp_registered_widgets;
	
	$debug = amr_check_if_widget_debug();

	$sidebarid = amr_get_sidebar_id ($name);

	$amr_sidebars_widgets = wp_get_sidebars_widgets(); //201711 do we need?

	$sidebar = $wp_registered_sidebars[$sidebarid];  // has the params etc
	
	$did_one = false;
	 
	/* lifted from wordpress code, keep as similar as possible for now */

		if ( !isset($wp_registered_widgets[$widget_id]) ) return; // wp had c o n t i n u e

		$params = array_merge(
			array( 
				array_merge( $sidebar, 
					array('widget_id' => $widget_id, 
						'widget_name' => $wp_registered_widgets[$widget_id]['name']) ) ),
						(array) $wp_registered_widgets[$widget_id]['params']
		);	
			
		$validtitletags = array ('h1','h2','h3','h4','h5','header','strong','em');
		$validwraptags = array ('div','p','main','aside','section');
		
		if (!empty($wrap)) { /* then folks want to 'wrap' with their own html tag, or wrap = yes  */		
			if ((!in_array( $wrap, $validwraptags))) 
				$wrap = ''; 
			  /* To match a variety of themes, allow for a variety of html tags. */
			  /* May not need if our sidebar match attempt has worked */
		}

		if (!empty ($wrap)) {
			$params[0]['before_widget'] = '<'.$wrap.' id="%1$s" class="%2$s">';
			$params[0]['after_widget'] = '</'.$wrap.'>';
		}
		
		// wp code to get classname
		$classname_ = '';
		//foreach ( (array) $wp_registered_widgets[$widget_id]['classname'] as $cn ) {
			$cn = $wp_registered_widgets[$widget_id]['classname'];
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		//}
		$classname_ = ltrim($classname_, '_');
		
		// add MKM and others requested class in to the wp classname string
		// if no class specfied, then class will = amrwidget.  These classes are so can reverse out unwanted widget styling.

		// $classname_ .= ' widget '; // wordpress seems to almost always adds the widget class
		

		$classname_ .= ' '.$class;

		// we are picking up the defaults from the  thems sidebar ad they have registered heir sidebar to issue widget classes?
		
		
		// Substitute HTML id and class attributes into before_widget		
		if (!empty($params[0]['before_widget'])) 
			$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $widget_id, $classname_);
		else 
			$params[0]['before_widget'] = '';
		
		if (empty($params[0]['before_widget'])) 
			$params[0]['after_widget'] = '';

		$params = apply_filters( 'dynamic_sidebar_params', $params );  
		// allow, any pne usingmust ensure they apply to the correct sidebars
		
		if (!empty($title)) {
			if ($title=='false') { /* amr switch off the title html, still need to get rid of title separately */
				$params[0]['before_title'] = '<span style="display: none">';
				$params[0]['after_title'] = '</span>';
				}
			else {
				if (in_array( $title, $validtitletags)) {
					$class = ' class="widget-title" ';					
						
					$params[0]['before_title'] = '<'.$title.' '.$class.' >';
					$params[0]['after_title'] = '</'.$title.'>';
				}
			}			
		}
		
		if (!empty($widget_classes) and ($widget_classes == 'none') ) {
			$params = amr_remove_widget_class($params);  // also called in widget area shortcode
		}
		

		$callback = $wp_registered_widgets[$widget_id]['callback'];
		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
//	}
	return $did_one;
}


function amr_saw_setup_sidebar ($sidebars_widgets) {
	global $sidebars_widgets; //need?
	// now theme mods has record of widgets and sidebar, but not for our new one.
	if ( is_array( $sidebars_widgets ) )  {
		if (empty($sidebars_widgets['widgets_for_shortcodes'])) {
			$sidebars_widgets['widgets_for_shortcodes'] = array();
		}
	}	
	return $sidebars_widgets;
}

function amr_reg_sidebar() {  // this is fired late, so hopefully any theme sidebars will have been registered already.

global $wp_registered_widgets, $_wp_sidebars_widgets, $sidebars_widgets, $wp_registered_sidebars;

if ( function_exists('register_sidebar') )  {  // maybe later, get the first main sidebar and copy it's before/after etc
	$args = array(
		'name'			=>'Widgets for Shortcodes',
		'id'            => 'widgets_for_shortcodes',  // hope to avoid losing widgets
		'description'   => __('Sidebar to hold widgets and their settings. These widgets will be used in a shortcode.  This sidebars widgets should be saved with your theme settings now.','amr-shortcode-any-widget'),
		'before_widget' => '<aside'.' id="%1$s" class="%2$s ">',  // 201402 to match twentyfourteen theme
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title" >', // 201402 maybe dont use widget class - we are in content here not in a widget area but others want the widget styling. ?
		'after_title'   => '</h1>' );
		
	
		
	if (!empty($wp_registered_sidebars)) {  // we got some sidebars already.
		$main_sidebar = reset($wp_registered_sidebars);  // Grab the first sidebar and use that as defaults for the widgets
		$args['before_widget'] 	= $main_sidebar['before_widget'];
		$args['after_widget'] 	= $main_sidebar['after_widget'];
		$args['before_title'] 	= $main_sidebar['before_title'];
		$args['after_title'] 	= $main_sidebar['after_title'];
	}
	
	register_sidebar($args);

}
	
//else {	echo '<h1>CANNOT REGISTER widgets_for_shortcodes SIDEBAR</h1>';}
}

include ('amr-admin-form-html.php');
include ('amr-utilities.php');

if (is_admin() )  $amr_saw_plugin_admin = new amr_saw_plugin_admin();  

add_action('widgets_init', 		'amr_reg_sidebar',98);   // register late so it appears last
add_filter( 'theme_mod_sidebars_widgets', 'amr_saw_setup_sidebar' ); //20171126

add_action('switch_theme', 			'amr_save_shortcodes_sidebar'); 
add_action('after_switch_theme',	'amr_restore_shortcodes_sidebar');

add_shortcode('do_widget', 		'amr_do_widget');
add_shortcode('do_widget_area', 'amr_do_widget_area');  // just dump the whole widget area - to get same styling

//require_once(ABSPATH . 'wp-includes/widgets.php');   // *** do we really need this here?
function amr_saw_load_text() { 
// wp (see l10n.php) will check wp-content/languages/plugins if nothing found in plugin dir
	$result = load_plugin_textdomain( 'amr-shortcode-any-widget', false, 
	dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


add_action('plugins_loaded'         , 'amr_saw_load_text' );

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'amr_add_action_links' );

function amr_add_action_links ( $links ) {
 $mylinks[] = 
 '<a title="This page will also tell you if you are using the shortcodes and where" href="' . admin_url( 'options-general.php?page=amr_saw' ) . '">Where using?</a>';
  $mylinks[] =
 '<a title="Click for a page of tips" href="' . admin_url( 'options-general.php?page=amr_saw' ) . '">HELP</a>';
return array_merge( $links, $mylinks );
}
