<?php
/*
Plugin Name: amr shortcode any widget
Plugin URI: http://webdesign.anmari.com/shortcode-any-widget/
Description: Include any widget in a page for any theme.  [do_widget widgetname ] or  [do_widget "widget name" ] or include a whole widget area [do_widget_area]. If upgrading see changelog.  Can be very powerful eg: with queryposts widget it can become a templater.
Author: anmari
Version: 2.3
Author URI: http://webdesign.anmari.com

*/
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
/*-----------------------------------*/
function do_widget_area($atts) {

global $wp_registered_widgets, $_wp_sidebars_widgets, $wp_registered_sidebars;

	extract(shortcode_atts(array(
		'widget_area' => 'widgets_for_shortcodes',
		'class' => 'amr-widget-area', /* the widget class is picked up automatically.  If we want to add an additional class at the wrap level to try to match a theme, use this */
		'widget_area_class' =>  '',  /* option to disassociate from themes widget styling use =none*/
		'widget_classes' =>  ''  /* option to disassociate from themes widget styling */

	), $atts));


	if (empty ($wp_registered_sidebars[$widget_area])) {
		echo '<br/>Widget area "'.$widget_area.'" not found. Registered widget areas (sidebars) are: <br/>';
		foreach ($wp_registered_sidebars as $area=> $sidebar) echo $area.'<br />';
	}
	if (isset($_REQUEST['do_widget_debug']) and current_user_can('administrator')) var_dump( $wp_registered_sidebars); /**/
	
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
/*-----------------------------------*/
function do_widget($atts) {

global $wp_registered_widgets, $_wp_sidebars_widgets, $wp_registered_sidebars;

/* check if the widget is in  the shortcode x sidebar  if not , just use generic, 
if it is in, then get the instance  data and use that */

	if (isset($_wp_sidebars_widgets) ) {
		amr_show_widget_debug('which one');  //check for debug prompt and show widgets in shortcode sidebar if requested and logged in etc
	}
	else { 
		echo '<br />No widgets defined at all in any sidebar!'; 
		return (false);
	}

	extract(shortcode_atts(array(
		'sidebar' => 'Widgets for Shortcodes',
		'id' => '',
		'name' => '', /* MKM added explicit 'name' attribute.  For existing users we still need to allow prev method, else too many support queries will happen */
		'title' => '',   /* do the default title unless they ask us not to - use string here not boolean */
		'class' => 'amr_widget', /* the widget class is picked up automatically.  If we want to add an additional class at the wrap level to try to match a theme, use this */
		'wrap' => '', /* wrap the whole thing - title plus widget in a div - maybe the themes use a div, maybe not, maybe we want that styling, maybe not */
		'widget_classes' =>  ''  /* option to disassociate from themes widget styling */
	), $atts));
	

	
	/* compatibility check - if the name is not entered, then the first parameter is the name */
	if (empty($name) and !empty($atts[0]))  
		$name = $atts[0];

	/* the widget need not be specified, [do_widget widgetname] is adequate */
	if (!empty($name)) {  // we have a name
		$widget = $name;
		
		foreach ($wp_registered_widgets as $i => $w) { /* get the official internal name or id that the widget was registered with  */
			if (strtolower($w['name']) === strtolower($widget)) $widget_ids[] = $i;
			//if ($debug) {echo '<br /> Check: '.$w['name'];}
		}	
	}	
	else { /* check for id if we do not have a name */
			if (!empty($id))  { 	/* if a specific id has been specified */
				
				foreach ($wp_registered_widgets as $i => $w) { /* get the official internal name or id that the widget was registered with  */
					if ($w['id'] === $id) $widget_ids[] = $id;
				}
				//if ($debug) { echo '<h2>We have an id: '.$id.'</h2>'; 	if (!empty($widget_ids)) var_dump($widget_ids);	}
			}
			else {
				echo '<br />No valid widget name or id given in shortcode parameters';		
				return (false);		
			}
	}
	
	if (empty($widget)) $widget = '';
	if (empty($id)) $id = '';
	
	if (empty ($widget_ids)) { 
		echo '<br /><a href="" title="Error: Your Requested widget '.$widget.' '.$id.' is not in the widget list. Typo maybe?">!</a><br />';
		amr_show_widget_debug('empty', $atts);
		return (false) ;
	}		
	
	if (!($sidebarid = get_sidebar_id ($sidebar))) 
		$sidebarid=$sidebar;   /* get the official sidebar id for this widget area - will take the first one */
		
	if (empty($widget)) 
		$widget = '';

	$content = ''; 			
	/* if the widget is in our chosen sidebar, then use the options stored for that */

	if ((!isset ($_wp_sidebars_widgets[$sidebarid])) or (empty ($_wp_sidebars_widgets[$sidebarid]))) { // try upgrade
		amr_upgrade_sidebar();
	}
	
	if ((isset ($_wp_sidebars_widgets[$sidebarid])) and (!empty ($_wp_sidebars_widgets[$sidebarid]))) {
/*		if ($debug) { 
			echo '<br />Widget ids in sidebar: "'.$sidebar.'" with id: '.$sidebarid .'<br />';
			sort ($_wp_sidebars_widgets[$sidebarid]);
			foreach ($_wp_sidebars_widgets[$sidebarid] as $i=> $w) {
				echo $i.' '.$w.'<br />';
			};	
		}
		*/
			/* get the intersect of the 2 widget setups so we just get the widget we want  */

		$wid = array_intersect ($_wp_sidebars_widgets[$sidebarid], $widget_ids );
/*			if ($debug) { echo '<br />Will use widget ids'.'<br />';
				foreach ($widget_ids as $i=> $w) {
					echo '&nbsp;&nbsp;&nbsp;'.$w.'<br />';
				};
			}
*/			
	}
		else { /* the sidebar is not defined */
			//if ($debug) {
			echo '<br /><a href="" title="Error: Sidebar '.$sidebar.' with sidebarid '.$sidebarid.' is empty (no widgets) or is not defined.">!</a><br />'; 
			//}
		}
	
	$output = '';
	if (empty ($wid) or (!is_array($wid)) or (count($wid) < 1)) { 
		//if ($debug) {	
		echo '<br /><a href="" title="Error: Your requested Widget '.$widget.' is not in the '.$sidebar.' sidebar ">!</a><br />';
		amr_show_widget_debug('empty', $atts);
		//}
		unset($sidebar); 
		unset($sidebarid);

		}
	else {	
		/*  There may only be one but if we have two in our chosen widget then it will do both */
		$output = '';
		foreach ($wid as $i=>$widget_instance) {
			ob_start();  /* catch the echo output, so we can control where it appears in the text  */
			shortcode_sidebar($widget_instance, $sidebar, $title, $class, $wrap, $widget_classes);
			$output .= ob_get_clean();
			}
	}
			
	return ($output);
}
/* -------------------------------------------------------------------------*/
function shortcode_sidebar( $widget_id, $name="widgets_for_shortcode", $title=true, $class='', $wrap='', $widget_classes='') { /* This is basically the wordpress code, slightly modified  */
	global $wp_registered_sidebars, $wp_registered_widgets;
	
	$debug = amr_check_if_widget_debug();

	$sidebarid = get_sidebar_id ($name);

	$sidebars_widgets = wp_get_sidebars_widgets(); 

	$sidebar = $wp_registered_sidebars[$sidebarid];  // has the params etc
	
	$did_one = false;
	 
	/* lifted from wordpress code, keep as similar as possible for now */

		if ( !isset($wp_registered_widgets[$widget_id]) ) continue;

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
/* -------------------------------------------------------------------------------------------------------------*/
function amr_reg_sidebar() {  // this is fired late, so hopefully any theme sidebars will have been registered already.

global $wp_registered_widgets, $_wp_sidebars_widgets, $wp_registered_sidebars;

if ( function_exists('register_sidebar') )  {  // maybe later, get the first main sidebar and copy it's before/after etc
	$args = array(
		'name'			=>'Widgets for Shortcodes',
		'id'            => 'widgets_for_shortcodes',  // hope to avoid losing widgets
		'description'   => 'Sidebar to hold widgets and their settings. These widgets will be used in a shortcode.  This sidebars widgets should be saved with your theme settings now.',
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
/*-----------------------------------*/
include ('amr-admin-form-html.php');
include ('amr-utilities.php');

if (is_admin() )  $amr_saw_plugin_admin = new amr_saw_plugin_admin();  

add_action('widgets_init', 		'amr_reg_sidebar',98);   // register late so it appears last
//add_action('widgets_init', 		'amr_upgrade_sidebar',99);  // copy old shortcodes sidebar to new one if necessary
//add_action('switch_theme', 		'amr_save_shortcodes_sidebar'); 
//add_action('after_switch_theme','amr_restore_shortcodes_sidebar');

add_shortcode('do_widget', 		'do_widget');
add_shortcode('do_widget_area', 'do_widget_area');  // just dump the whole widget area - to get same styling

//require_once(ABSPATH . 'wp-includes/widgets.php');   // *** do we really need this here?

?>