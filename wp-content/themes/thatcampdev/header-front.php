<?php
/**
 * Header
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js" lang="en"><!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link href='http://fonts.googleapis.com/css?family=Francois+One' rel='stylesheet' type='text/css'>
	<?php do_action( 'bp_head' ); ?>	
	<?php wp_head(); ?>
</head>
<body <?php body_class() ?> id="logicalbones_hug">
	<?php do_action( 'bp_before_header' ); ?>
	<div id="sitebar-wrapper">
		<div id="sitebar">
		</div>
	</div>
	<div id="header-wrapper">	
		<header id="branding" class="wrapper" role="banner">
				<div id="site-logo">
					<a href="<?php echo site_url(); ?>">ThatCamp</a>
				</div>
				<nav id="top-nav" role="navigation">
					<h3 class="assistive-text"><?php _e( 'Menu', 'thatcamp' ); ?></h3>
					<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'thatcamp' ); ?>"><?php _e( 'Skip to content', 'thatcamp' ); ?></a></div>
					<?php wp_nav_menu( array(
						'theme_location' => 'top', 
						'menu_class' => 'top_menu',
						'container' => ''
					)); ?>
				</nav>
			<!--<div id="search-bar">
					<form action="<?php echo bp_search_form_action(); ?>" method="post" id="search-form">
							<label for="search-terms" class="accessibly-hidden"><?php _e( 'Search for:', 'thatcamp' ); ?></label>
							<input type="text" id="search-terms" name="search-terms" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />

							<?php echo bp_search_form_type_select(); ?>

							<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'thatcamp' ); ?>" />

							<?php wp_nonce_field( 'bp_search_form' ); ?>
					</form>
				<?php do_action( 'bp_search_login_bar' ); ?>
			</div>-->
			<?php do_action( 'bp_header' ); ?>
		</header>
		<?php do_action( 'bp_after_header'     ); ?>
	</div>
	<div id="avatarwall">
		<!--	don't load avatars for moment as broken<div id="ri-grid" class="ri-grid ri-grid-size-2">
					<ul>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/1.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/2.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/3.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/4.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/5.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/6.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/7.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/8.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/9.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/10.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/11.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/12.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/13.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/14.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/15.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/16.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/17.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/18.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/19.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/20.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/21.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/22.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/23.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/24.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/25.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/26.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/27.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/28.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/29.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/30.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/31.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/32.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/33.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/34.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/35.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/36.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/37.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/38.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/39.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/40.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/41.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/42.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/43.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/44.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/45.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/46.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/47.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/48.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/49.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/50.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/51.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/52.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/53.jpg"/></a></li>
						<li><a href="#"><img src="<?php echo site_url(); ?>/wp-content/themes/thatcampdev/assets/images/medium/54.jpg"/></a></li>
					</ul>
				</div>
	</div>-->
	<div id="about-wrapper">
		<div id="about" class="wrapper">
			<h2><?php _e( 'What is THATCamp?', 'thatcamp' ); ?></h3>
			<p>
				<?php _e('Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Duis mollis, 
				est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Nullam 
				id dolor id nibh ultricies vehicula ut id elit.', 'thatcamp'); ?>
			</p>
		</div>
	</div>
	<?php do_action( 'bp_before_container' ); ?>
	<div id="main-wrapper">
		<div class="wrapper">