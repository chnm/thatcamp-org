<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

        <title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?></title>

     <!-- Meta -->
        <meta name="description" content="The Humanities and Technology Camp is an open, inexpensive meeting where humanists and technologists of all skill levels learn and build together in sessions proposed on the spot." />
        <meta name="keywords" content="THATCamp, The Humanities and Technology Camp, humanities, technology, digital humanities, unconference, BARCamp, host a thatcamp, host an unconference, how to host a THATCamp, how to host an unconference, graduate students, faculty, librarians, libraries, archivists, archives, museums, museum studies, universities, colleges, cultural heritage, programming, programmers, software development, open source, open access, new media, text mining, visualization, mapping, History, English, Philosophy, Computer Science, information science, Mellon Foundation, what is THATCamp, what is an unconference, what is digital humanities, definition of digital humanities, humanities computing, BootCamp, BootCamp fellowships, apply for a fellowship, anti-conference, workshops, training, fun times, beer" />
        <meta name="author" content="Center for History and New Media" />
        <meta name="robots" content="index,follow" />

        <!-- Stylesheets -->
        <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
    	<script type="text/javascript">
    	google.load("jquery", "1");
    	</script>

        <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

        <?php wp_head(); ?>
        <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-28931418-1']);
  _gaq.push(['_setDomainName', 'thatcampdev.info']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
    </head>
    <body<?php if(is_home()) echo ' id="home"'; if(is_page('campers') || is_author()) echo ' id="campers"'; if(is_page('blog') || is_single()) echo ' id="blog"'; if(is_page('schedule')) echo ' id="schedule"';?>>
        <div id="wrap" class="group">
        	<div id="header">
                <h1 id="thatcamp"><a href="<?php bloginfo('url'); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>
/images/logo.gif" alt="THATCamp" title="THATCamp" /></a></h1>
                <?php /* ?>
				<div id="user-login"><?php global $current_user;
				      get_currentuserinfo(); ?>
				<?php if(is_user_logged_in()): ?>Hi, <?php echo $current_user->display_name; ?> | <?php endif; wp_loginout(); ?></div>
				<?php	*/ ?>
            </div>
