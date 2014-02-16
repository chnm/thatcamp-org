<?php
/*
Function Name: The Thumb
Description: Returns the first image of a post
Version: 0.2
Author: Marcelo Mesquita
Author URI: http://www.marcelomesquita.com/
*/

function the_thumb($size = "medium", $add = "")
{
  global $wpdb, $post;
  
  $thumb = $wpdb->get_row("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_parent = {$post->ID} AND post_mime_type LIKE 'image%' ORDER BY menu_order");
  
  if(!empty($thumb))
  {
    $image = image_downsize($thumb->ID, $size);
    
    print "<img src='{$image[0]}' alt='{$thumb->post_title}' {$add} />";
  }
}
?>
