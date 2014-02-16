<?php

  // textdomain
  load_theme_textdomain('painter');

  // widgets
  if(function_exists('register_sidebar'))
  {
    register_sidebar(array(
      'before_widget' => '<div id="%2$s" class="widget %1$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    ));
  }

  // includes
  include_once(TEMPLATEPATH.'/inc/the-thumb.php');
  include_once(TEMPLATEPATH.'/inc/custom-theme.php');
  include_once(TEMPLATEPATH.'/inc/custom-header.php');
  include_once(TEMPLATEPATH.'/inc/custom-colors.php');

?>
