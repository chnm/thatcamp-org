<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <main>
 * and the left sidebar conditional
 *
 * @since 1.0.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div id="page" class="clearfix">
		<header id="header" class="navbar-default">
			<div class="container">
				<nav id="site-navigation" class="navbar navbar-default" role="navigation">
					<h3 class="sr-only"><?php _e( 'Main menu', 'farben-basic' ); ?></h3>
					<a class="sr-only" href="#primary" title="<?php esc_attr_e( 'Skip to content', 'farben-basic' ); ?>"><?php _e( 'Skip to content', 'farben-basic' ); ?></a>

					<?php $header_class = ( is_rtl() ) ? ' navbar-right' : ''; ?>

					<div class="navbar-header<?php echo esc_attr( $header_class ); ?>">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					    </button>

						<a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
							<small><?php bloginfo( 'description' ); ?></small>
						</a>
					</div>

					<div class="collapse navbar-collapse">
						<?php $menu_class = ( is_rtl() ) ? '' : ' navbar-right'; ?>
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => '', 'menu_class' => 'nav navbar-nav' . esc_attr( $menu_class ), 'fallback_cb' => 'bavotasan_default_menu' ) ); ?>
					</div>
				</nav><!-- #site-navigation -->
			</div>
		</header>

		<main>
