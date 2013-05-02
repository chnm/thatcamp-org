<?php
/**
 * Header
 *
 * @package notecamp
 * @since notecamp 1.0
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
	<title><?php wp_title( '|', true, 'right'); ?><?php bloginfo('name'); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class() ?> id="notecamp">
	<div id="site-wrapper">
	<div id="branding-wrapper">
		<header id="branding" role="banner" class="wrapper clearfix">
				<nav id="top-nav" role="navigation">
					<h3 class="assistive-text"><?php _e( 'Menu', 'notecamp' ); ?></h3>
					<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'notecamp' ); ?>"><?php _e( 'Skip to content', 'notecamp' ); ?></a></div>
					<?php wp_nav_menu( array(
						'theme_location' => 'top',
						'menu_class' => 'top_menu',
						'container' => ''
					)); ?>
					<div class="clear"></div>
				</nav>
		</header>
	</div>
	<nav id="show-menu"><i class="icon-reorder"></i></nav>
				<div id="responsive-menu">
					<nav id="responsive-nav" role="navigation">
						<h3 class="assistive-text"><?php _e( 'Menu', 'blogilates' ); ?></h3>
						<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'blogilates' ); ?>"><?php _e( 'Skip to content', 'blogilates' ); ?></a></div>
						<?php wp_nav_menu( array(
							'theme_location' => 'middle',
							'menu_class' => 'responsive_menu',
							'container' => ''
						)); ?>
					</nav>
				</div>
		<div id="header-image">
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>
		<?php endif; ?>
			</div>
		<div id="main-wrapper" class="inner-wrapper">