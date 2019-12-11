<?php

/**
 * WP fail2ban main file 
 *  
 * @since 4.0.0 
 * @package wp-fail2ban 
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/lib/constants.php';
require_once __DIR__ . '/lib/loader.php';
require_once __DIR__ . '/lib/defaults.php';
register_activation_hook( WP_FAIL2BAN_FILE, function () {
    foreach ( get_mu_plugins() as $plugin => $data ) {
        
        if ( 0 === strpos( $data['Name'], 'WP fail2ban' ) ) {
            $wp_f2b_ver = substr( WP_FAIL2BAN_VER, 0, strrpos( WP_FAIL2BAN_VER, '.' ) );
            $wpf2b = 'WP fail2ban';
            $error_msg = sprintf( __( '<h1>Cannot activate %s</h1>' ), $wpf2b );
            $mu_file = WPMU_PLUGIN_DIR . '/' . $plugin;
            
            if ( is_link( $mu_file ) ) {
                
                if ( false === ($link = readlink( $mu_file )) || false === ($path = realpath( $mu_file )) ) {
                    $h3 = __( 'A broken symbolic link was found in <tt>mu-plugins</tt>:' );
                    $error_msg .= <<<__ERROR__
<h3>{$h3}</h3>
<p><tt>{$mu_file}</tt></p>
__ERROR__;
                } elseif ( WP_FAIL2BAN_FILE == $path ) {
                    // OK, we're linking to ourself
                } else {
                    $mu_file = str_replace( '/', '/<wbr>', $mu_file );
                    $mu_file = substr( $mu_file, strlen( WPMU_PLUGIN_DIR ) - 1 );
                    $h3 = __( 'A conflicting symbolic link was found in <tt>mu-plugins</tt>:' );
                    $error_msg .= <<<__ERROR__
<h3>{$h3}</h3>
<style>
table { text-align: center; }
td { width: 50%; }
th { font-size: 200%; }
td, th { font-family: monospace; }
span.tt { font-weight: bold; }
</style>
<table>
  <tr>
    <td>{$mu_file}</td>
    <th>&DoubleRightArrow;</th>
    <td>{$link}</td>
  </tr>
  <tr>
    <td colspan="3"><span class="tt">&equiv;</span> <span>{$path}</span></td>
  </tr>
  <tr>
    <td colspan="3"></td>
  </tr>
</table>
__ERROR__;
                }
            
            } else {
                $mu_file = str_replace( '/', '/<wbr>', $mu_file );
                $mu_file = substr( $mu_file, strlen( WPMU_PLUGIN_DIR ) - 1 );
                $h3 = __( 'A conflicting file was found in <tt>mu-plugins</tt>:' );
                $error_msg .= <<<__ERROR__
<h3>{$h3}</h3>
<p><tt>{$mu_file}</tt></p>
__ERROR__;
            }
            
            $error_msg .= sprintf( __( '<p>Please see the <a href="%s" target="_blank">documentation</a> for how to configure %s for <tt>mu-plugins</tt>.</p>' ), "https://docs.wp-fail2ban.com/en/{$wp_f2b_ver}/configuration.html#mu-plugins-support", $wpf2b );
            $error_msg .= sprintf( __( '<p>Click <a href="%s">here</a> to return to the plugins page.</p>' ), admin_url( 'plugins.php' ) );
            deactivate_plugins( plugin_basename( WP_FAIL2BAN_FILE ) );
            wp_die( $error_msg );
        }
    
    }
} );
require __DIR__ . '/feature/lib.php';
/**
 * @since 4.2.5
 */

if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
    /**
     * @since 4.2.0
     */
    global  $wp_fail2ban ;
    $wp_fail2ban['plugins'] = array();
    require __DIR__ . '/feature/plugins.php';
    if ( is_admin() ) {
        require 'admin/admin.php';
    }
} elseif ( is_admin() ) {
    require __DIR__ . '/admin/lib/about.php';
    add_action( 'admin_menu', function () {
        add_menu_page(
            'WP fail2ban',
            'WP fail2ban',
            'manage_options',
            'wp-fail2ban',
            __NAMESPACE__ . '\\about',
            'dashicons-analytics'
        );
    } );
}

/**
 * @since 4.0.5
 */

if ( !function_exists( __NAMESPACE__ . '\\wp_login' ) ) {
    /**
     * Hook: wp_login
     *
     * @since 4.1.0     Add REST support
     * @since 3.5.0     Refactored for unit testing
     * @since 1.0.0
     *
     * @param string    $user_login
     * @param mixed     $user
     */
    function wp_login( $user_login, $user )
    {
        global  $wp_xmlrpc_server ;
        openlog();
        syslog( LOG_INFO, "Accepted password for {$user_login}" );
        closelog();
        // @codeCoverageIgnoreEnd
    }
    
    add_action(
        'wp_login',
        __NAMESPACE__ . '\\wp_login',
        10,
        2
    );
}

/**
 * @since 4.0.5
 */

if ( !function_exists( __NAMESPACE__ . '\\wp_login_failed' ) ) {
    /**
     * Hook: wp_login_failed
     *
     * @since 4.2.4     Add message filter
     * @since 4.2.0     Change username check
     * @since 4.1.0     Add REST support
     * @since 3.5.0     Refactored for unit testing
     * @since 1.0.0
     *
     * @param string    $username
     *
     * @wp-f2b-hard Authentication attempt for unknown user .*
     * @wp-f2b-hard REST authentication attempt for unknown user .*
     * @wp-f2b-hard XML-RPC authentication attempt for unknown user .*
     * @wp-f2b-soft Authentication failure for .*
     * @wp-f2b-soft REST authentication failure for .*
     * @wp-f2b-soft XML-RPC authentication failure for .*
     */
    function wp_login_failed( $username )
    {
        global  $wp_xmlrpc_server ;
        
        if ( defined( 'REST_REQUEST' ) ) {
            $msg = 'REST a';
            $filter = '::REST';
        } elseif ( $wp_xmlrpc_server ) {
            $msg = 'XML-RPC a';
            $filter = '::XML-RPC';
        } else {
            $msg = 'A';
            $filter = '';
        }
        
        $username = trim( $username );
        $msg .= ( wp_cache_get( $username, 'useremail' ) || wp_cache_get( sanitize_user( $username ), 'userlogins' ) ? "uthentication failure for {$username}" : "uthentication attempt for unknown user {$username}" );
        $msg = apply_filters( "wp_fail2ban::wp_login_failed{$filter}", $msg );
        openlog();
        syslog( LOG_NOTICE, $msg );
        closelog();
        // @codeCoverageIgnoreEnd
    }
    
    add_action( 'wp_login_failed', __NAMESPACE__ . '\\wp_login_failed' );
}

/**
 * @since 4.2.5
 */

if ( !is_admin() ) {
    /**
     * User enumeration
     *
     * @since 4.0.0     Refactored
     * @since 2.1.0
     */
    if ( defined( 'WP_FAIL2BAN_BLOCK_USER_ENUMERATION' ) && true === WP_FAIL2BAN_BLOCK_USER_ENUMERATION ) {
        require_once __DIR__ . '/feature/user-enum.php';
    }
    /**
     * XML-RPC
     *
     * @since 4.0.0     Refactored
     * @since 3.0.0
     */
    if ( defined( 'XMLRPC_REQUEST' ) && true === XMLRPC_REQUEST ) {
        require_once __DIR__ . '/feature/xmlrpc.php';
    }
}

/**
 * Comments
 *
 * @since 4.0.0     Refactored
 * @since 3.5.0
 */
if ( defined( 'WP_FAIL2BAN_LOG_COMMENTS' ) && true === WP_FAIL2BAN_LOG_COMMENTS ) {
    require_once __DIR__ . '/feature/comments.php';
}
/**
 * Password
 *
 * @since 4.0.0     Refactored
 * @since 3.5.0
 */
if ( defined( 'WP_FAIL2BAN_LOG_PASSWORD_REQUEST' ) && true === WP_FAIL2BAN_LOG_PASSWORD_REQUEST ) {
    require_once __DIR__ . '/feature/password.php';
}
/**
 * Spam
 *
 * @since 4.0.0     Refactored
 * @since 3.5.0
 */
if ( defined( 'WP_FAIL2BAN_LOG_SPAM' ) && true === WP_FAIL2BAN_LOG_SPAM ) {
    require_once __DIR__ . '/feature/spam.php';
}
/**
 * Users
 *
 * @since 4.0.0     Refactored
 * @since 2.0.0
 */
if ( defined( 'WP_FAIL2BAN_BLOCKED_USERS' ) && '' < WP_FAIL2BAN_BLOCKED_USERS ) {
    require_once __DIR__ . '/feature/user.php';
}