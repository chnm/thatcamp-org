<?php
/*
Plugin Name: Cookie Fix
Description: Fix for cookie error while logged in.
Version: 0.1
Author: see https://wordpress.org/support/topic/cookie-error-when-logging-in/page/2 for source
License: GPL2
*/

function set_wp_test_cookie() {
	@setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
	if ( SITECOOKIEPATH != COOKIEPATH )
		setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);
}
add_action( 'after_setup_theme', 'set_wp_test_cookie', 101 );
?>
