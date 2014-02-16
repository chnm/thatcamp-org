<?php
/*
Function Name: Custom Header
Description: Allow the user to chose wich image will be in the header of the blog
Version: 0.1
Author: Marcelo Mesquita
Author URI: http://www.marcelomesquita.com/
*/

// constants
define('HEADER_IMAGE', '');
define('HEADER_IMAGE_WIDTH', 980);
define('HEADER_IMAGE_HEIGHT', 120);

// activate custom header
add_custom_image_header('header_style', 'admin_header_style');

// load header style
function header_style()
{
  if(get_header_image() == '')
    return;
  
  ?>
    <style type="text/css" media="screen">
      #header
      {
        background: url(<?php header_image(); ?>) no-repeat;
      }
      
      <?php if('blank' == get_header_textcolor()) : ?>
        #header .blog-title
        {
          margin:0px;
          padding:0px;
        }
        
        #header .blog-title a
        {
          width:<?php print (HEADER_IMAGE_WIDTH / 2); ?>px;
          height:<?php print HEADER_IMAGE_HEIGHT; ?>px;
          display:block;
          text-indent:-5000px;
        }
        
        #header .blog-description
        {
          display:none;
        }
      <?php endif; ?>
    </style>
  <?php
}

// load preview style
function admin_header_style()
{
  ?>
    <style type="text/css">
      #headimg
      {
        background: url(<?php header_image(); ?>) no-repeat;
        height: <?php print HEADER_IMAGE_HEIGHT; ?>px;
        width:<?php print HEADER_IMAGE_WIDTH; ?>px;
      }
      
      #headimg h1
      {
        margin:0px;
        padding:30px 20px 0px 20px;
        font-size:30px;
      }

      #headimg h1 a
      {
        text-decoration:none;
      }

      #headimg #desc
      {
        padding:0px 20px 0px 20px;
      }
    </style>
  <?php
}
?>
