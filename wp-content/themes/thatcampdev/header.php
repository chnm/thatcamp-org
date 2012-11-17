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
<!--[if (IE 7)&!(IEMobile)]><html class="no-js ie7" lang="en"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html class="no-js ie8" lang="en"><![endif]-->
<!--[if (IE 9)&!(IEMobile)]><html class="no-js ie9" lang="en"><![endif]-->
<!--[if gt IE 9]><!--> <html <?php language_attributes(); ?> class="no-js" lang="en"><!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link href='http://fonts.googleapis.com/css?family=Francois+One' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
	<?php do_action( 'bp_head' ); ?>	
	<?php wp_head(); ?>
</head>
<body <?php body_class() ?> id="thatcamp">
	<?php do_action( 'bp_before_header' ); ?>
	<div id="sitebar-wrapper">
		<div id="sitebar" class="wrapper">
			<div id="user-section">
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo bp_loggedin_user_domain(); ?>">
						<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ); ?>
					</a>
					<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
					<a class="header-button logout" href="<?php echo wp_logout_url( wp_guess_url() ); ?>"><?php _e( 'Log Out', 'thatcamp' ); ?></a>
				<?php else : ?>

					<?php do_action( 'bp_before_sidebar_login_form' ); ?>

					<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ); ?>" method="post">
						<label><?php _e( 'Username', 'thatcamp' ); ?></label>
						<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php if ( isset( $user_login) ) echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" placeholder="Username" />

						<label><?php _e( 'Password', 'thatcamp' ); ?></label>
						<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" />

						<?php do_action( 'bp_sidebar_login_form' ); ?>
						<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e( 'Log In', 'thatcamp' ); ?>" tabindex="100" />
						<input type="hidden" name="testcookie" value="1" />
					</form>

					<?php do_action( 'bp_after_sidebar_login_form' ); ?>
				
				<?php endif; ?>
			</div>
			<div id="search-bar">
					<form action="<?php echo bp_search_form_action(); ?>" method="post" id="search-form">
						<input type="text" id="search-terms" name="search-terms" placeholder="Search" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />
						<select id="search-which" name="search-which">
							<option value="thatcamporg">thatcamp.org</option>
							<option value="all_thatcamps">All THATCamps</option>
						</select>
						<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'thatcamp' ); ?>" />
						<?php wp_nonce_field( 'bp_search_form' ); ?>
					</form>
				<?php do_action( 'bp_search_login_bar' ); ?>
			</div>
			
		</div>
	</div>
	<div id="header-wrapper">	
		<header id="branding" class="wrapper" role="banner">
				<div id="site-logo">
					<a href="<?php echo site_url(); ?>"><?php _e( 'THATCamp', 'thatcamp' ); ?></a>
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
					<nav id="show-menu"><i class="icon-reorder"></i><?php _e( 'Menu', 'thatcamp' ); ?></nav>
				<div id="responsive-menu">
					<nav id="responsive-nav" role="navigation">
						<h3 class="assistive-text"><?php _e( 'Menu', 'thatcamp' ); ?></h3>
						<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'thatcamp' ); ?>"><?php _e( 'Skip to content', 'thatcamp' ); ?></a></div>
						<?php wp_nav_menu( array(
							'theme_location' => 'middle', 
							'menu_class' => 'responsive_menu',
							'container' => ''
						)); ?>
					</nav>
				</div>
			<?php do_action( 'bp_header' ); ?>
		</header>
		<?php do_action( 'bp_after_header'     ); ?>
	</div>
	<?php do_action( 'bp_before_container' ); ?>
	<div id="main-wrapper">
		<div class="wrapper">