<?php
/*
Plugin Name: add widgets to page
Plugin URI: http://www.ie.u-ryukyu.ac.jp/‾e065708/wp_plugins/addw2p.html
Version: 1.3.2
Description: Adds Wiget space to Posts and Pages using shortcode.
Author: Christina Uechi
Author URI: http://twitter.com/ucc_tina
*/

/*  Copyright 2009  Christina Uechi  ( tweet me : @ucc_tina )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function addw2p_menu() {
	if ( function_exists('add_management_page') ) {
		add_management_page("addw2p", "Widgets to Pages", 'read', __FILE__, 'addw2p_menu_options');
	}
}

function addw2p_menu_options() {
	echo '<div class="wrap">'."\n";
	echo '<h2>Add Widgets to Page</h2>'."\n";
	$names = get_option('addw2pdn');
	$names = explode("|",$names);
	//remove spaces
	if ( isset($_POST['RemoveSpace'])) {
		foreach ( $names as $name ) {
			if ( preg_match("/\s/",$name) ) {
				$new_names .= str_replace(" ","_",$name);
			} else {
				$new_names .= $name;
			}
			$new_names .= "|";
		}
		update_option('addw2pdn',$new_names);
		$names = explode("|",$new_names);
	}
	//delete widgets.
	if ( isset($_POST['Delete']) ) {
		foreach( $names as $name) {
			if ( $name != NULL ) $counts[] = $name;
		}
		echo "<p>Deleted";
		foreach ( $counts as $delete ) {
			if ( !isset($_POST[$delete]) ) {
				$count[] = $delete;
			} else {
				echo " ".$delete;
			}
		}
		foreach ( $count as $subname ) {
			$new_names .= "|";
			$new_names .= $subname;
		}
		update_option('addw2pdn',$new_names);
		echo ".</p>\n";
	//end deliting
	} else { //count widget spaces
		foreach( $names as $name) {
			if ( $name != NULL ) $count[] = $name;
		}
	}
	$num = count($count);
	if ( $num <= 0 ) {//if no widget space
		echo '<p>To use this, add [addw2p name="name"] to eny entries. (please change the name) (by using a same name, you are able to show same widget space)</p>'."\n";
		echo "<p>There is no widget settings.</p></div>\n";
		return;
	}
	//if there is a widgetspace
?>
<p>To use this, place a shortcode [addw2p name="name"] to eny entries. (replace 'name' with anyword of your choice)<br />
<strong>WARNING: Please only use numbers and alphabet, do not use spaces. You will not be able to delete it with this plugin.<br />
Bellow button'Replace spaces to under score' will change spaces to under score so you can delete it.</strong><br />
(By using a same name, you are able to show same widget space.)</p>
<p><strong>This will create a new area on the widget page called addw2p - name where you can drag your widgets.</strong><br />
If nothing shows, please refresh the widgets page. Note: You may have to refresh several times, maybe even close the page and open it again.<br />
Once the new area appears below sidebar, the plugin will be working.</p>
<p>To change the widgets that are showing, go to Appearance -> Widgets and edit the sidebar.</p>
<p>To delete it, check the names which you want to delete and press Delete button below.</p>
<p>There is <?php echo $num; ?> widget settings.</p>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<table style="border:0;border-spacing:0;">
<?php
	wp_nonce_field('update-options');
	foreach ($count as $name ) {
		echo '<tr><td style="padding:5px 15px 5px 5px;"><input type="checkbox" name="'.$name.'" value ="'.$name.'" ></td>'."\n";
		echo '<td style="padding:5px 15px 5px 5px;">'.$name.'</td></tr>'."\n";
	}
?>
<tr><td colspan="2">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="addw2pdn" />
<p class="submit"><input type="submit" name="Delete" class="delete-opts" value="Delete" /></p>
</td></tr>
</table>
</form>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="addw2pdn" />
<p><input type="submit" name="RemoveSpace" class="remove-space" value="Replace spaces to under score"></p>
</form>
</div>
<?php

}

function addw2p_css() {
?>
<style type="text/css">
	.addw2p {
		list-style-type: none;
		list-style-image: none;
	}
</style>
<?php
}


function addw2p_register($atts, $content) { //put widgets in enteries.
	 extract(shortcode_atts(array(
		'name' => '1'
	), $atts));
	if ( $name == 1) return $content;
	$title = "addw2p-".$name;
	$names = get_option('addw2pdn');
	$names = explode("|",$names);
	if( !in_array($name,$names) && $name != NULL ) {
	    	array_push($names,$name);
		foreach ($names as $subname ) {
			$new_names .= "|";
			$new_names .= $subname;
		}
		update_option('addw2pdn',$new_names);
	}
	//show widgets
	ob_start();
	echo '<ul class="ul-addw2p ul-'.$title.'">'."\n";
	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( $title ) ) :
	   echo '<li>please set some widgets to show from Appearance -> Widgets.</li>'."\n";
	endif;
	echo "</ul>\n";
	$myStr = ob_get_contents();
	ob_end_clean();
	return $myStr;
}

function addw2p_register_sidebar() {
	$names = get_option('addw2pdn');
	$names = explode("|",$names);
	$num = count($names);
	if ( $num <= 0 ) {
		return;
	}
	foreach ( $names as $name ) {
		if ( $name != NULL ) {
			$title = "addw2p-".$name;
			register_sidebar(array(
				'before_widget' => '<li id="%1$s" class="addw2p %2$s">',
				'after_widget' => '</li>',
				'before_title' => '',
				'after_title' => '',
 				'name' => $title
 			));
 		}
	}
	return;
}

add_action('wp_head','addw2p_css');
add_shortcode('addw2p','addw2p_register');
add_action('init','addw2p_register_sidebar');
if ( is_admin() ) add_action('admin_menu','addw2p_menu');

?>