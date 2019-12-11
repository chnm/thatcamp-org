<?php

/**
 * Loader
 *
 * @package wp-fail2ban
 * @since 4.2.0
 */
namespace {
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }
    if ( defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
        return;
    }
    if ( !function_exists( 'boolval' ) ) {
        /**
         * PHP 5.3 helper
         *
         * @since 4.2.5
         *
         * @param mixed $val
         *
         * @return bool
         */
        function boolval( $val )
        {
            return (bool) $val;
        }
    
    }
}
namespace org\lecklider\charles\wordpress\wp_fail2ban {
    /**
     * Helper
     *
     * @since 4.0.0
     *
     * @param string    $define
     * @param callable  $cast
     * @param bool      $unset
     * @param array     $field
     */
    function _load(
        $define,
        $cast,
        $unset,
        array $field
    )
    {
        global  $wp_fail2ban ;
        $wp_fail2ban['config'][$define] = array(
            'validate' => $cast,
            'unset'    => $unset,
            'field'    => $field,
            'ndef'     => !defined( $define ),
        );
        if ( !defined( $define ) ) {
            
            if ( defined( "DEFAULT_{$define}" ) ) {
                // we've got a default
                define( $define, $cast( constant( "DEFAULT_{$define}" ) ) );
            } else {
                // bah
                define( $define, $cast( false ) );
            }
        
        }
    }
    
    /**
     * Validate IP list
     *
     * @since 4.0.0
     *
     * @param array|string  $value
     *
     * @return string
     */
    function validate_ips( $value )
    {
        return $value;
    }
    
    // phpcs:disable Generic.Functions.FunctionCallArgumentSpacing
    _load(
        'WP_FAIL2BAN_AUTH_LOG',
        'intval',
        true,
        array( 'logging', 'authentication', 'facility' )
    );
    _load(
        'WP_FAIL2BAN_LOG_COMMENTS',
        'boolval',
        true,
        array( 'logging', 'comments', 'enabled' )
    );
    _load(
        'WP_FAIL2BAN_LOG_COMMENTS_EXTRA',
        'intval',
        true,
        array( 'logging', 'comments', 'extra' )
    );
    _load(
        'WP_FAIL2BAN_COMMENT_LOG',
        'intval',
        false,
        array( 'logging', 'comments', 'facility' )
    );
    _load(
        'WP_FAIL2BAN_COMMENT_EXTRA_LOG',
        'intval',
        false,
        array( 'logging', 'comments-extra', 'facility' )
    );
    _load(
        'WP_FAIL2BAN_LOG_PASSWORD_REQUEST',
        'boolval',
        true,
        array( 'logging', 'password-request', 'enabled' )
    );
    _load(
        'WP_FAIL2BAN_PASSWORD_REQUEST_LOG',
        'intval',
        false,
        array( 'logging', 'password-request', 'facility' )
    );
    _load(
        'WP_FAIL2BAN_LOG_PINGBACKS',
        'boolval',
        true,
        array( 'logging', 'pingback', 'enabled' )
    );
    _load(
        'WP_FAIL2BAN_PINGBACK_LOG',
        'intval',
        false,
        array( 'logging', 'pingback', 'facility' )
    );
    _load(
        'WP_FAIL2BAN_LOG_SPAM',
        'boolval',
        true,
        array( 'logging', 'spam', 'enabled' )
    );
    _load(
        'WP_FAIL2BAN_SPAM_LOG',
        'intval',
        false,
        array( 'logging', 'spam', 'facility' )
    );
    _load(
        'WP_FAIL2BAN_OPENLOG_OPTIONS',
        'intval',
        true,
        array( 'syslog', 'connection' )
    );
    _load(
        'WP_FAIL2BAN_SYSLOG_SHORT_TAG',
        'boolval',
        true,
        array( 'syslog', 'workaround', 'short_tag' )
    );
    _load(
        'WP_FAIL2BAN_HTTP_HOST',
        'boolval',
        true,
        array( 'syslog', 'workaround', 'http_host' )
    );
    _load(
        'WP_FAIL2BAN_TRUNCATE_HOST',
        'boolval',
        true,
        array( 'syslog', 'workaround', 'truncate_host' )
    );
    _load(
        'WP_FAIL2BAN_BLOCK_USER_ENUMERATION',
        'boolval',
        true,
        array( 'block', 'user_enumeration' )
    );
    _load(
        'WP_FAIL2BAN_BLOCKED_USERS',
        'strval',
        true,
        array( 'block', 'users' )
    );
    _load(
        'WP_FAIL2BAN_PROXIES',
        __NAMESPACE__ . '\\validate_ips',
        true,
        array( 'remote-ip', 'proxies' )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_LOG_AUTH',
        'boolval',
        true,
        array(
        'logging',
        'plugins',
        'auth',
        'enabled'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_LOG_COMMENT',
        'boolval',
        true,
        array(
        'logging',
        'plugins',
        'comment',
        'enabled'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_LOG_PASSWORD',
        'boolval',
        true,
        array(
        'logging',
        'plugins',
        'password',
        'enabled'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_LOG_REST',
        'boolval',
        true,
        array(
        'logging',
        'plugins',
        'rest',
        'enabled'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_LOG_SPAM',
        'boolval',
        true,
        array(
        'logging',
        'plugins',
        'spam',
        'enabled'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_LOG_XMLRPC',
        'boolval',
        true,
        array(
        'logging',
        'plugins',
        'xmlrpc',
        'enabled'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_AUTH_LOG',
        'intval',
        false,
        array(
        'logging',
        'plugins',
        'auth',
        'facility'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_COMMENT_LOG',
        'intval',
        false,
        array(
        'logging',
        'plugins',
        'comment',
        'facility'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_PASSWORD_LOG',
        'intval',
        false,
        array(
        'logging',
        'plugins',
        'password',
        'facility'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_REST_LOG',
        'intval',
        false,
        array(
        'logging',
        'plugins',
        'rest',
        'facility'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_SPAM_LOG',
        'intval',
        false,
        array(
        'logging',
        'plugins',
        'spam',
        'facility'
    )
    );
    _load(
        'WP_FAIL2BAN_PLUGIN_XMLRPC_LOG',
        'intval',
        false,
        array(
        'logging',
        'plugins',
        'xmlrpc',
        'facility'
    )
    );
    // phpcs:enable
}