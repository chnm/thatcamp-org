<?php

/**
 * XML-RPC functionality
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

if ( !function_exists( __NAMESPACE__ . '\\xmlrpc_login_error' ) ) {
    /**
     * Catch multiple XML-RPC authentication failures
     *
     * @see \wp_xmlrpc_server::login()
     *
     * @since 4.0.0 Return $error
     * @since 3.5.0 Refactored for unit testing
     * @since 3.0.0
     *
     * @param \IXR_Error    $error
     * @param \WP_Error     $user
     *
     * @return \IXR_Error
     *
     * @wp-f2b-hard XML-RPC multicall authentication failure
     */
    function xmlrpc_login_error( $error, $user )
    {
        static  $attempts = 0 ;
        
        if ( ++$attempts > 1 ) {
            openlog();
            syslog( LOG_NOTICE, 'XML-RPC multicall authentication failure' );
            closelog();
            // @codeCoverageIgnoreEnd
            bail();
        } else {
            return $error;
        }
    
    }
    
    add_action(
        'xmlrpc_login_error',
        __NAMESPACE__ . '\\xmlrpc_login_error',
        10,
        2
    );
}

/**
 * @since 4.0.5 Guard
 */

if ( !function_exists( __NAMESPACE__ . '\\xmlrpc_pingback_error' ) ) {
    /**
     * Catch failed pingbacks
     *
     * @see \wp_xmlrpc_server::pingback_error()
     *
     * @since 4.0.0 Return $ixr_error
     * @since 3.5.0 Refactored for unit testing
     * @since 3.0.0
     *
     * @param \IXR_Error    $ixr_error
     *
     * @return \IXR_Error
     *
     * @wp-f2b-hard Pingback error .* generated
     */
    function xmlrpc_pingback_error( $ixr_error )
    {
        
        if ( 48 !== $ixr_error->code ) {
            openlog();
            syslog( LOG_NOTICE, 'Pingback error ' . $ixr_error->code . ' generated' );
            closelog();
            // @codeCoverageIgnoreEnd
        }
        
        return $ixr_error;
    }
    
    add_filter( 'xmlrpc_pingback_error', __NAMESPACE__ . '\\xmlrpc_pingback_error', 5 );
}

/**
 * @since 4.0.0 Refactored
 * @since 2.2.0
 */
if ( defined( 'WP_FAIL2BAN_LOG_PINGBACKS' ) && true === WP_FAIL2BAN_LOG_PINGBACKS ) {
    require_once 'xmlrpc/pingback.php';
}
/**
 * @since 4.0.0 Refactored
 * @since 3.6.0
 */
if ( defined( 'WP_FAIL2BAN_XMLRPC_LOG' ) && '' < WP_FAIL2BAN_XMLRPC_LOG ) {
    require_once 'xmlrpc/log.php';
}