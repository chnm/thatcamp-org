<?php
/*
Plugin Name: Social View
Plugin URI: http://www.kieranoshea.com/
Description: Neatly displays YouTube and Flickr items that have been imported onto your blog via RSS feeds
Author: Kieran O'Shea
Author URI: http://www.kieranoshea.com
Version: 1.0.0
*/

// Function for main content
function fixContent($content) {

    global $post;

    if (!is_single()) {
       // Flickr
       if (strrpos($content,'flickr.com'))
       {

         // Get the width and height of the image and decide portrait or landscape
         $hgt = explode('height="',$content);
         $hgt = explode('"',$hgt[1]);
         $hgt = $hgt[0];
         $wdt = explode('width="',$content);
         $wdt = explode('"',$wdt[1]);
         $wdt = $wdt[0];
         $the_p_size = '150';
         if ($hgt > $wdt) {
            $sizing = 'height="'.$the_p_size.'"';
         } else {
           $sizing = 'width="'.$the_p_size.'"';
           $the_p_size = ($the_p_size/($wdt/$hgt));
         }

         // Sort the visible image for the post
         $flickr_display_image = explode('src="',$content);
         $flickr_display_image = $flickr_display_image[1];
         $flickr_display_image = explode('"',$flickr_display_image);
         $flickr_display_image = str_replace('_m.jpg','_m.jpg',$flickr_display_image[0]);
         $flickr_image = str_replace('_m.jpg','_b.jpg',$flickr_display_image);

         // Format the display
         $photo_bit = '<a href="'.$flickr_image.'"><img align="left" src="'.$flickr_display_image.'" '.$sizing.' alt="'.$post->post_title.'" border="0" style="border-right:5px solid #FFFFFF;" /></a>';

         $pwned = explode('<p>',$content);
         $pwned = $pwned[3];
         $text_bit = '<p>'.$pwned;

         $content = '<div style="height:'.($the_p_size-35).'px;">'.$photo_bit.$text_bit.'</div>';
       }

       // YouTube
       if (strrpos($content,'i.ytimg.com')) {
       	  $vid = explode('<a',$content);
          $vid = explode('/a>',$vid[1]);
          $vid = '<a'.$vid[0].'/a>';
          $vid = str_replace('<img alt=""','<img width="120" alt="" style="border-right:5px solid #FFFFFF;" align="left"',$vid);
          $descrip = explode('><span>',$content);
          $descrip = explode('</span></div>',$descrip[1]);
          $descrip = $descrip[0];
          $content = '<div style="height:100px;">'.$vid.$descrip.'</div>';
       }

    } else {

       // Flickr
       if (strrpos($content,'flickr.com'))
       {
         // Get the width and height of the image and decide portrait or landscape
         $hgt = explode('height="',$content);
         $hgt = explode('"',$hgt[1]);
         $hgt = $hgt[0];
         $wdt = explode('width="',$content);
         $wdt = explode('"',$wdt[1]);
         $wdt = $wdt[0];
         $the_p_size = '450';
         if ($hgt > $wdt) {
            $sizing = 'height="'.$the_p_size.'"';
         } else {
           $sizing = 'width="'.$the_p_size.'"';
           $the_p_size = ($the_p_size/($wdt/$hgt));
         }
         // Sort the visible image for the post
         $flickr_display_image = explode('src="',$content);
         $flickr_display_image = $flickr_display_image[1];
         $flickr_display_image = explode('"',$flickr_display_image);
         $flickr_display_image = str_replace('_m.jpg','_b.jpg',$flickr_display_image[0]);

         // Format the display
         $photo_bit = '<a href="'.$flickr_display_image.'"><img src="'.$flickr_display_image.'" '.$sizing.' alt="'.$post->post_title.'" border="0" /></a>';

         $pwned = explode('<p>',$content);
         $pwned = $pwned[3];
         $text_bit = '<p>'.$pwned;

         $content = $text_bit.'<div style="text-align:center;">'.$photo_bit.'</div>';
       }

       // YouTube
       if (strrpos($content,'i.ytimg.com')) {

       	  // Extract the video ID
          $vid = explode('<a',$content);
          $vid = explode('/a>',$vid[1]);
	  $vidid = explode('v=',$vid[0]);
	  $vidid = explode('&',$vidid[1]);
	  $vidid = $vidid[0];

	  // Get the description
          $descrip = explode('><span>',$content);
          $descrip = explode('</span></div>',$descrip[1]);
          $descrip = $descrip[0];

	  // Getting the embed
	  $embed = '<div style="text-align:center;"><object width="480" height="385">
	  	 <param name="movie" value="http://www.youtube.com/v/'.$vidid.'&amp;hl=en_US&amp;fs=1"></param>
	  	 <param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
	  	 <embed src="http://www.youtube.com/v/'.$vidid.'&amp;hl=en_US&amp;fs=1" type="application/x-shockwave-flash" 
		 allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed>
	  </object></div>';

	  // The final content
	  $content = '<p>'.$descrip.'</p>'.$embed.'<br />';
       }

    }

    // We're done
    return $content;
}

// Apply the functions
add_filter('the_content', 'fixContent');
add_filter('the_excerpt', 'fixContent');
add_filter('the_content_rss', 'fixContent');
add_filter('the_excerpt_rss', 'fixContent');

?>
