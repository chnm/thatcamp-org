<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
  <head>
    <!-- Meta -->
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="Generator" content="WordPress" />
    <meta name="Description" content="<?php bloginfo('description'); ?>" />
    <meta name="Keywords" content="<?php wp_title('&raquo;', true, 'right'); ?> <?php bloginfo('name'); ?>" />
    <meta name="Robots" content="ALL" />
    <meta name="Distribution" content="Global" />
    <meta name="Author" content="Marcelo Mesquita - http://www.marcelomesquita.com" />
    <meta name="Resource-Type" content="Document" />
    
    <!-- Title -->
    <title><?php wp_title('&raquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    
    <!-- Pingback -->
    <link href="<?php bloginfo('pingback_url'); ?>" rel="pingback" />
    
    <!-- Icon -->
    <link type="image/x-icon" href="<?php bloginfo('stylesheet_directory'); ?>/img/icon/favicon.ico" rel="shortcut icon" />
    
    <!-- RSS -->
    <link type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="feeds de <?php bloginfo('name'); ?>" rel="alternate" />
    <?php if(is_category()) : ?><link type="application/rss+xml" href="<?php print get_category_feed_link($cat, 'rss2'); ?>" title="feeds de <?php single_cat_title(); ?>" rel="alternate" /><?php endif; ?>
    
    <!-- CSS -->
    <link type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/style.css" rel="stylesheet" media="screen" />
    <link type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/print.css" rel="stylesheet" media="print" />
    
    <!-- JavaScript -->
    <?php wp_enqueue_script('jquery'); ?>
    <?php wp_enqueue_script('cycle', get_bloginfo('stylesheet_directory') . '/js/jquery.cycle-2.3.pack.js', array('jquery'), '2.3'); ?>
    <?php wp_enqueue_script('backtotop', get_bloginfo('stylesheet_directory') . '/js/backtotop.js'); ?>
    <?php wp_enqueue_script('painter', get_bloginfo('stylesheet_directory') . '/js/script.js', array('cycle', 'backtotop')); ?>
    
    <?php wp_head(); ?>
  </head>
  <body>
    
    <!-- Container -->
    <div id="container">
      
      <!-- Dater -->
      <div id="dater">
        
        <!-- RSS -->
        <div class="rss"><a href="<?php bloginfo('rss2_url'); ?>" title="RSS"><?php bloginfo('rss2_url'); ?></a></div>
        
        <!-- Date -->
        <p><?php printf("%s, %d %s %s %s %d", __(date('l')), date('j'), __('of', 'painter'), __(date('F')), __('of', 'painter'), date('Y')); ?></p>
      </div>
      
      <!-- Header -->
      <div id="header">
        <h1 class="blog-title"><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
        <p class="blog-description"><?php bloginfo('description'); ?></p>
      </div>
      
      <!-- Menu -->
      <div id="menu">
        <ul>
          <li><a href="<?php bloginfo('url'); ?>" title="<?php _e('Home', 'painter'); ?>"><?php _e('Home', 'painter'); ?></a></li>
          <?php wp_list_pages('depth=2&title_li='); ?>
        </ul>
      </div>
      
      <!-- BreadCrumb -->
      <?php if(class_exists('breadcrumb_navigation_xt')) : ?>
        <div id="breadcrumb">
          <p>
          <?php
            // New breadcrumb object
            $mybreadcrumb = new breadcrumb_navigation_xt;
            // Options for breadcrumb_navigation_xt
            $mybreadcrumb->opt["separator"] = " &raquo; ";
            // Display the breadcrumb
            $mybreadcrumb->display();
          ?>
          </p>
        </div>
      <?php endif; ?>
