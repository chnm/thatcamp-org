<?php

/**
 * pingback logging
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

if ( !function_exists( __NAMESPACE__ . '\\xmlrpc_call' ) ) {
    /**
     * Log pingbacks
     *
     * @since 3.5.0 Refactored for unit testing
     * @since 2.2.0
     *
     * @param string    $call
     */
    function xmlrpc_call( $call )
    {
        
        if ( 'pingback.ping' == $call ) {
            openlog( 'WP_FAIL2BAN_PINGBACK_LOG' );
            syslog( LOG_INFO, 'Pingback requested' );
            closelog();
            // @codeCoverageIgnoreEnd
        }
    
    }
    
    add_action( 'xmlrpc_call', __NAMESPACE__ . '\\xmlrpc_call' );
}
