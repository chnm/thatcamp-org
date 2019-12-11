<?php

/**
 * Library functions
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Wrapper for \openlog
 *
 * @since 3.5.0 Refactored for unit testing
 *
 * @param string $log
 */
function openlog( $log = 'WP_FAIL2BAN_AUTH_LOG' )
{
    $tag = ( defined( 'WP_FAIL2BAN_SYSLOG_SHORT_TAG' ) && true === WP_FAIL2BAN_SYSLOG_SHORT_TAG ? 'wp' : 'wordpress' );
    $host = ( array_key_exists( 'WP_FAIL2BAN_HTTP_HOST', $_ENV ) ? $_ENV['WP_FAIL2BAN_HTTP_HOST'] : $_SERVER['HTTP_HOST'] );
    /**
     * Some varieties of syslogd have difficulty if $host is too long
     * @since 3.5.0
     */
    if ( defined( 'WP_FAIL2BAN_TRUNCATE_HOST' ) && 1 < intval( WP_FAIL2BAN_TRUNCATE_HOST ) ) {
        $host = substr( $host, 0, intval( WP_FAIL2BAN_TRUNCATE_HOST ) );
    }
    
    if ( false === \openlog( "{$tag}({$host})", WP_FAIL2BAN_OPENLOG_OPTIONS, constant( $log ) ) ) {
        error_log( 'WPf2b: Cannot open syslog', 0 );
        // @codeCoverageIgnore
    } elseif ( defined( 'WP_FAIL2BAN_TRACE' ) ) {
        error_log( 'WPf2b: Opened syslog', 0 );
        // @codeCoverageIgnore
    }

}

/**
 * Wrapper for \syslog
 *
 * @since 3.5.0
 *
 * @param int           $level
 * @param string        $msg
 * @param string|null   $remote_addr
 */
function syslog( $level, $msg, $remote_addr = null )
{
    $msg .= ' from ';
    $msg .= ( is_null( $remote_addr ) ? remote_addr() : $remote_addr );
    
    if ( false === \syslog( $level, $msg ) ) {
        error_log( "WPf2b: Cannot write to syslog: '{$msg}'", 0 );
        // @codeCoverageIgnore
    } elseif ( defined( 'WP_FAIL2BAN_TRACE' ) ) {
        error_log( "WPf2b: Wrote to syslog: '{$msg}'", 0 );
        // @codeCoverageIgnore
    }
    
    \closelog();
    if ( defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
        echo  "{$level}|{$msg}" ;
    }
}

/**
 * Graceful immediate exit
 *
 * @since 4.2.7 Remove JSON support
 * @since 4.0.5 Add JSON support
 * @since 3.5.0 Refactored for unit testing
 */
function bail()
{
    wp_die( 'Forbidden', 'Forbidden', array(
        'response' => 403,
    ) );
}

/**
 * Compute remote IP address
 *
 * @return string
 *
 * @todo Test me!
 * @codeCoverageIgnore
 */
function remote_addr()
{
    static  $remote_addr = null ;
    /**
     * @since 4.0.0
     */
    
    if ( is_null( $remote_addr ) ) {
        if ( defined( 'WP_FAIL2BAN_PROXIES' ) ) {
            
            if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
                $ip = ip2long( $_SERVER['REMOTE_ADDR'] );
                /**
                 * PHP 7 lets you define an array
                 * @since 3.5.4
                 */
                $proxies = ( is_array( WP_FAIL2BAN_PROXIES ) ? WP_FAIL2BAN_PROXIES : explode( ',', WP_FAIL2BAN_PROXIES ) );
                foreach ( $proxies as $proxy ) {
                    
                    if ( '#' == $proxy[0] ) {
                        continue;
                    } elseif ( 2 == count( $cidr = explode( '/', $proxy ) ) ) {
                        $net = ip2long( $cidr[0] );
                        $mask = ~(pow( 2, 32 - $cidr[1] ) - 1);
                    } else {
                        $net = ip2long( $proxy );
                        $mask = -1;
                    }
                    
                    if ( $net == ($ip & $mask) ) {
                        return ( false === ($len = strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' )) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : substr( $_SERVER['HTTP_X_FORWARDED_FOR'], 0, $len ) );
                    }
                }
            }
        
        }
        /**
         * For plugins and themes that anonymise requests
         * @since 3.6.0
         */
        $remote_addr = ( defined( 'WP_FAIL2BAN_REMOTE_ADDR' ) ? WP_FAIL2BAN_REMOTE_ADDR : $_SERVER['REMOTE_ADDR'] );
    }
    
    return $remote_addr;
}
