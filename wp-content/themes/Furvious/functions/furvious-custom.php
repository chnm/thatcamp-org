<?php 

$kreative_metaboxes = array(
	"image" => array (
		"name"		=> "thumbnail",
		"default" 	=> "",
		"label" 	=> "Thumbnail Image",
		"type" 		=> "text",
		"desc"      => "Enter the URL for image to be used as post thumbnail."
	),
);
	
function kreative_meta_box_content() {
	global $post, $kreative_metaboxes;
	
	echo '<table>'."\n";
	
	foreach ($kreative_metaboxes as $kreative_metabox) 
	{
		$kreative_metaboxvalue = get_post_meta($post->ID, $kreative_metabox["name"], true);
		
		if ($kreative_metaboxvalue == "" || ! isset($kreative_metaboxvalue)) 
		{
			$kreative_metaboxvalue = $kreative_metabox['default'];
		}
		
		echo "\t".'<tr>';
		echo "\t\t".'<th style="text-align: right;"><label for="kreative_'.$kreative_metabox["name"].'">'.$kreative_metabox['label'].':</label></th>'."\n";
		echo "\t\t".'<td><input size="70" type="'.$kreative_metabox['type'].'" value="'.$kreative_metaboxvalue.'" name="kreative_'.$kreative_metabox["name"].'" id="kreative_'.$kreative_metabox['name'].'"/></td>'."\n";
		echo "\t".'</tr>'."\n";
		echo "\t\t".'<tr><td></td><td><span style="font-size:11px">'.$kreative_metabox['desc'].'</span></td></tr>'."\n";				
	}
	echo '</table>'."\n\n";
}

function kreative_metabox_insert($post_id) {
	global $kreative_metaboxes;
	
	foreach ($kreative_metaboxes as $kreative_metabox) 
	{
		$var = "kreative_" . $kreative_metabox["name"];
		
		if (isset($_POST[$var])) 
		{			
			if (get_post_meta($post_id, $kreative_metabox["name"]) == "")
			{
				add_post_meta($post_id, $kreative_metabox["name"], $_POST[$var], true);
			}
			elseif ($_POST[$var] != get_post_meta($post_id, $kreative_metabox["name"], true))
			{
				update_post_meta($post_id, $kreative_metabox["name"], $_POST[$var]);
			}	
			elseif ($_POST[$var] == "")
			{
				delete_post_meta($post_id, $kreative_metabox["name"], get_post_meta($post_id, $kreative_metabox["name"], true));
			}
		}
	}
}

function kreative_meta_box() {
	$kt =& get_instance();
	$themename = $kt->config->item('themename');
	
	if ( function_exists('add_meta_box') ) {
		add_meta_box('kreative-settings', $themename . ' Custom Settings', 'kreative_meta_box_content', 'post', 'normal');
		add_meta_box('kreative-settings', $themename . ' Custom Settings', 'kreative_meta_box_content', 'page', 'normal');
	}
}

add_action('admin_menu', 'kreative_meta_box');
add_action('wp_insert_post', 'kreative_metabox_insert');