<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */
?><!DOCTYPE html>
<!--[if lt IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie6 lte7 lte8 lte9"><![endif]-->
<!--[if IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie7 lte7 lte8 lte9"><![endif]-->
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="no-js ie ie8 lte8 lte9"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="no-js ie ie9 lte9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->


<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<title><?php
			/*
			 * Print the <title> tag based on what is being viewed.
			 * We filter the output of wp_title() a bit -- see
			 * boilerplate_filter_wp_title() in functions.php.
			 */
			wp_title( '|', true, 'right' );
		?></title>
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		
			  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
			  <link rel="icon" href="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/images/favicon.png" sizes="16x16" type="image/png" />
			  <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
			  <link rel="apple-touch-icon-precomposed" href="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/images/apple-touch-icon-precomposed.png">
			  <!-- For first-generation iPad: -->
			  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/images/apple-touch-icon-72x72-precomposed.png">
			  <!-- For iPhone 4 with high-resolution Retina display: -->
			  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/images/apple-touch-icon-114x114-precomposed.png">  
			  <!-- For iPad "3" with high-resolution Retina display: -->
			  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/images/apple-touch-icon-144x144-precomposed.png">   
			  		
<?php
		/* We add some JavaScript to pages with the comment form
		 * to support sites with threaded comments (when in use).
		 */
		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );

		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head();
		
?>
  <!-- Stuff that Boilerplate admin provides as options but which I prefer to place here -->
  <meta name="viewport" content="width=device-width">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <script src="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/js/modernizr.js"></script>
  <script src="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/js/respond.js"></script>
  <script src="<?php echo home_url().'/wp-content/themes/'.get_option('template');?>/js/2x.js"></script>  

  
	</head>
	<body <?php body_class(); ?>>
	<div id="outer">
		<header role="banner">
		
		<a href="<?php echo home_url( '/' ); ?>"><img alt="<?php bloginfo( 'name' ); ?>" src="<?php echo '/wp-content/themes/'.get_option('template');?>/images/thatcampoha.png"/></a>
		<!--
		<img id="header-date" src="<?php// echo home_url().'/wp-content/themes/'.get_option('template');?>/images/date.png"/>
		-->
		
			<h1 class="visuallyhidden"><a class="visuallyhidden" href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			
			<p class="tagline visuallyhidden"><?php bloginfo( 'description' ); ?></p>
			
		<div class="clearfix"></div>	
		</header>
		<nav id="access" role="navigation">
	    	  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
	    		<a id="skip" class="visuallyhidden" href="#content" title="<?php esc_attr_e( 'Skip to content', 'boilerplate' ); ?>"><?php _e( 'Skip to content', 'boilerplate' ); ?></a>
	    		<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
	    		<?php //wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
	    		
	    			
	    <div class="menu-header">
	    	
	    	<!- mobile and tablet nav -->
	    	<ul class="menu" id="menu-primary">
	    			
	    			<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home" id="mobile-home"><a href="<?php echo home_url( '/' ); ?>">Home</a></li>
	    			
	    			<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home" id="mobile-campers"><a href="<?php echo home_url( '/campers' ); ?>">Campers</a></li>

	    			<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home" id="mobile-posts"><a href="<?php echo home_url( '/session-posts' ); ?>">Posts</a></li>	    			

	    			
	    			<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home" id="menu-top"><a href="#menu">More</a></li>
	    	
	    			
	    	</ul>
	    	
	    	<!-- wider screen nav -->	
	    	<div id="wide-access">
	    	
	    	<a href="<?php echo home_url( '/' ); ?>">Home</a>
	    	<a href="<?php echo home_url( '/session-posts' ); ?>">Session Posts</a>
	    	<a href="<?php echo home_url( '/campers' ); ?>">Campers</a>
	    	
	    	<?php 
	    	//if Registration is still open, include in nav, if not, use the Log-in link
	    	$register = get_page_by_title('Register');
	    	$id = $register->ID;
	    	if(get_page($id)->post_status == 'publish'):?>
	    	<a href="<?php echo home_url( '/register' ); ?>">Register</a>
	    	<?php else: ?>
	    	<a href="<?php echo home_url( '/wp-admin' ); ?>">Log-in</a>
	    	<?php endif;?>
	    	
	    	<a href="<?php echo home_url( '/about' ); ?>">About</a>
	    	
	    	</div>
	    	
	    </div> <!-- #menu-header -->
	    		
	    </nav><!-- #access -->
		
		<section id="content" role="main">
