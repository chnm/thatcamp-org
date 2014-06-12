<?php

$cookiePath = '/';
$cookieTime = time()+2592000;

$theme = 'red';
if($_GET['theme'] == 'blue') { $theme = 'blue'; }

/*
if($_COOKIE['theme']) { $theme = $_COOKIE['theme']; }
if($_GET['theme'] == 'blue') { $theme = 'blue'; setcookie('theme',$theme,$cookieTime,$cookiePath); }
if($_GET['theme'] == 'red') { $theme = 'red'; setcookie('theme',$theme,$cookieTime,$cookiePath); }
*/

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<!-- Powered by orsifrancesco.com-->
<meta name="author" content="www.orsifrancesco.com" />

<meta name="description" content="The Humanities and Technology Camp" />
<meta name="keywords" content="that camp, thatcamp, florence, firenze" />
<link rel="shortcut icon" href="<?php echo get_bloginfo('template_directory'); ?>/favicon.ico" />
<link rel="image_src" href="<?php echo get_bloginfo('template_directory'); ?>/img/image_src.jpg" />
<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('template_directory'); ?>/style.css" />
<?php if($theme == 'blue') { ?><link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('template_directory'); ?>/blue.css" /><?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('template_directory'); ?>/css/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAogg2v6lfgz0vDieOOxWpXRSH68FfarF7A48CHpAfWNtfWRcOCxSDb-eTXQIzTUVfc2zPNEKM7qNoOA&amp;hl=en"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.gmap-1.1.0-min.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/superfish.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/js.js"></script>
<!--[if IE]><style type="text/css" media="all"> #content img { border: 1px solid #ccc; } </style><![endif]-->
<!--[if lte IE 7]><style type="text/css" media="all"> #extra { padding: 60px 10px 0; margin: 0 auto -30px; } </style><![endif]-->
<?php wp_head(); ?>
</head>
<body>

<noscript>
<div id="alert">
<div>
<h2>Enable Javascript</h2>
<p>
For a correct visualization of website you must enable Javascript in your Browser.<br/>
If you don't know how enable Javascript <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=12654">click here</a>.
</p>
</div>
</div>
</noscript>

<!-- background -->
<div id="background">

<!-- layout -->
<div id="layout">

<!-- c_top -->
<div id="c_top">

<!-- top -->
<div id="top">

<div id="navb">
<ul>
<li><a href="<?php bloginfo('url'); // collegamento a ./ ?>/feed/rss/"><img src="<?php echo get_bloginfo('template_directory'); ?>/img/logo_rss.png" alt="" /> Rss</a></li>
<li><a href="http://www.twitter.com/THATCampFirenze">Twitter</a></li>
<li><a href="http://www.facebook.com/event.php?eid=109082425837283">Facebook</a></li>
<!--
<?php if($theme == 'red') { ?>
<li>
<a href="<?php bloginfo('url'); // collegamento a ./ ?>?theme=blue">Blue Version Theme</a>
</li>
<?php } ?>
-->
<!--
<?php if($theme == 'blue') { ?>
<li>
<a href="<?php bloginfo('url'); // collegamento a ./ ?>?theme=red">Red Version Theme</a>
</li>
<?php } ?>
-->
</ul>
</div>
<div id="search"><?php include(TEMPLATEPATH . '/searchform.php'); ?></div>

</div>
<!-- end top -->

</div>
<!-- end c_top -->

<!-- header -->
<div id="header">
<div id="new"><a href="http://www.eui.eu/DepartmentsAndCentres/HistoryAndCivilization/Index.aspx"></a></div>

<!-- logo -->
<div id="logo">
<a href="<?php bloginfo('url'); // collegamento a ./ ?>"></a><h1><a href="<?php bloginfo('url'); // collegamento a ./ ?>">THATCamp</a></h1>
</div>
<!-- end logo -->

<!-- locality -->
<div id="locality"></div>
<!-- end locality -->

</div>
<!-- end header -->

<!-- navigation -->
<div id="navigation">
<ul class="sf-menu">
<li><a id="button_home" href="<?php bloginfo('url'); // collegamento a ./ ?>"></a></li>
<?php wp_list_pages('title_li&depth=3'); ?>
</ul>
</div>
<!-- end navigation -->

<!-- container -->
<div id="container">

<!-- tp_content -->
<div id="tp_content"></div>
<!-- end tp_content -->

<!-- content -->
<div id="content">