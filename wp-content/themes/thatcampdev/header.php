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
	<div id="sitebar-wrapper" class="clearfix">
		<div id="sitebar">
		</div>
	</div>
	<div id="header-wrapper" class="clearfix">	
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
	<?php do_action( 'bp_before_container' ); ?>
	<div id="main-wrapper" class="clearfix">
		<div class="wrapper">