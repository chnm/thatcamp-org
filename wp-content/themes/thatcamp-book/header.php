<?php
/**
 * Header
 *
 * @package bookcamp
 * @since bookcamp 1.0
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
	<?php wp_head(); ?>
</head>
<body <?php body_class() ?> id="bookcamp">
	<div id="site-gap">
	<div id="site-wrapper">
		<header id="branding">
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		</header>
		
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
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>
		<?php endif; ?>
		<div id="main-wrapper" class="inner-wrapper">