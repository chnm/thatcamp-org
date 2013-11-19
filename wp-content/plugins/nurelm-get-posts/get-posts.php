<?php
/*
Plugin Name: NuRelm Get Posts
Plugin URI: http://www.nurelm.com/themanual/2009/08/21/nurelm-get-posts/
Description: Adds a shortcode tag [get_posts] to display a list of posts.
Version: 0.5
Author: Sami Shaaban
Author URI: http://www.nurelm.com/
*/

/*  Copyright 2013  Sami Shaaban  (email : sam@nurelm.com)

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

// gets the list HTML
function get_posts_generate($args = '') {
		
  $r = shortcode_atts(
    array(
      'numberposts'      => '5',
      'offset'           => '',
      'category'         => '',
      'category_name'    => '',
      'tag'              => '',
      'orderby'          => 'date',
      'order'            => '',
      'include'          => '',
      'exclude'          => '',
      'meta_key'         => '',
      'meta_value'       => '',
      'post_type'        => '',
      'post_status'      => '',
      'post_parent'      => '',
      'nopaging'         => '',
      'ul_class'         => 'get_posts_class',
      'fields'           => 'post_title',
      'fields_classes'   => 'post_title_class',
      'fields_make_link' => 'true'),
    $args );
		
  $fields_list = explode(",", $r['fields']);
  $fields_classes_list = explode(",", $r['fields_classes']);
  $fields_make_link_list = explode(",", $r['fields_make_link']);

	$content = "\n\n<ul class=\"".$r['ul_class']."\">\n";
	
	$posts = get_posts($args);
	foreach( $posts as $post ) {
    $content = $content. "  <li>";

    $i = 0;
    foreach ( $fields_list as $field ) {

      if (isset($fields_classes_list[$i])) {
        $content = $content .
          "<span class=\"" .
          trim($fields_classes_list[$i]) .
          "\">";
      }

      if (isset($fields_make_link_list[$i]) && 
          ($fields_make_link_list[$i] == "true" ||
           $fields_make_link_list[$i] == 1)) {
			  $content = $content .
          "<a href=\"" .
			    get_permalink($post->ID) .
  			  "\">";
      }

      $field = trim($field);
			$content = $content . $post->$field;

      if (isset($fields_make_link_list[$i]) && 
          ($fields_make_link_list[$i] == "true" ||
          $fields_make_link_list[$i] == 1)) {
        $content = $content . "</a>";
      }

      if (isset($fields_classes_list[$i])) {
        $content = $content . "</span>";
      }

      $i++;
    }

    $content = $content .  "</li>\n";
	}	

	$content = $content.'</ul>';
	
	return $content;	
}

add_shortcode('get_posts', 'get_posts_generate');
?>
