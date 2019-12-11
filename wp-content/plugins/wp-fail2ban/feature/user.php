<?php

/**
 * Blocked user functionality
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * @since 4.0.5 Guard
 */

if ( !function_exists( __NAMESPACE__ . '\\authenticate' ) ) {
    /**
     * Catched blocked users
     *
     * @since 3.5.0 Refactored for unit testing
     * @since 2.0.0
     *
     * @param mixed|null    $user
     * @param string        $username
     * @param string        $password
     *
     * @return mixed|null
     *
     * @wp-f2b-hard Blocked authentication attempt for .*
     */
    function authenticate( $user, $username, $password )
    {
        
        if ( !empty($username) ) {
            /**
             * @since 3.5.0 Arrays allowed in PHP 7
             */
            $matched = ( is_array( WP_FAIL2BAN_BLOCKED_USERS ) ? in_array( $username, WP_FAIL2BAN_BLOCKED_USERS ) : preg_match( '/' . WP_FAIL2BAN_BLOCKED_USERS . '/i', $username ) );
            
            if ( $matched ) {
                openlog();
                syslog( LOG_NOTICE, "Blocked authentication attempt for {$username}" );
                closelog();
                // @codeCoverageIgnoreEnd
                bail();
            }
        
        }
        
        return $user;
    }
    
    add_filter(
        'authenticate',
        __NAMESPACE__ . '\\authenticate',
        1,
        3
    );
}
