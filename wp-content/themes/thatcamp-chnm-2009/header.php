<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

        <title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?></title>

     <!-- Meta -->
        <meta name="description" content="THATCamp is a digital humanities unconference, hosted by the Center for History and New Media, George Mason University" />
        <meta name="keywords" content="THATCamp, The Humanities and Technology Camp, CHNM, Center for History and New Media, New Media, humanities, history, technology, digital humanities, unconference, barcamp, George Mason, GMU" />
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
    </head>
    <body<?php if(is_home()) echo ' id="home"'; if(is_page('campers') || is_author()) echo ' id="campers"'; if(is_page('blog') || is_single()) echo ' id="blog"'; if(is_page('schedule')) echo ' id="schedule"';?>>
        <div id="wrap">
        	<div id="header">
                <h1 id="thatcamp"><a href="<?php bloginfo('home'); ?>"><img src="/ui/i/thatcamp09.gif" alt="THATCamp" /></a></h1>
				<ul id="primary-nav" class="navigation">
					<li id="nav-home"><a href="<?php bloginfo('home'); ?>">Home</a></li>
					<li id="nav-schedule"><a href="<?php bloginfo('home'); ?>/schedule">Schedule</a></li>
					
					<li id="nav-blog"><a href="<?php bloginfo('home'); ?>/blog/">Blog</a></li>
					<li id="nav-campers"><a href="<?php bloginfo('home'); ?>/campers/">Campers</a></li>
				</ul>
				<div id="user-login"><?php global $current_user;
				      get_currentuserinfo(); ?>
				<?php if(is_user_logged_in()): ?>Hi, <?php echo $current_user->display_name; ?> | <?php endif; wp_loginout(); ?></div>
					
            </div>