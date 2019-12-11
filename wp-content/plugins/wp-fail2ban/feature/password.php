<?php

/**
 * Password-related functionality
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * @since 4.0.5
 */

if ( !function_exists( __NAMESPACE__ . '\\retrieve_password' ) ) {
    /**
     * Log password reset requests
     *
     * @since 3.5.0
     *
     * @param string    $user_login
     *
     * @wp-f2b-extra Password reset requested for .*
     */
    function retrieve_password( $user_login )
    {
        openlog( 'WP_FAIL2BAN_PASSWORD_REQUEST_LOG' );
        syslog( LOG_NOTICE, "Password reset requested for {$user_login}" );
        closelog();
        // @codeCoverageIgnoreEnd
    }
    
    add_action( 'retrieve_password', __NAMESPACE__ . '\\retrieve_password' );
}
