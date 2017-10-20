<?php
/**
 * Plugin Name: WP fail2ban
 * Plugin URI: https://charles.lecklider.org/wordpress/wp-fail2ban/
 * Description: Write all login attempts to syslog for integration with fail2ban.
 * Text Domain: wp-fail2ban
 * Version: 3.5.3
 * Author: Charles Lecklider
 * Author URI: https://charles.lecklider.org/
 * License: GPL2
 * SPDX-License-Identifier: GPL-2.0
 */

/**
 *  Copyright 2012-16  Charles Lecklider  (email : wordpress@charles.lecklider.org)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace org\lecklider\charles\wordpress\wp_fail2ban;


/**
 * Guard for MU
 */
global $wp_fail2ban;
if (empty($wp_fail2ban) && defined('WP_FAIL2BAN')) return;
define('WP_FAIL2BAN',true);


/**
 * Allow custom openlog openssl_get_cert_locations.
 * e.g. you may not want the PID if logging remotely.
 * @since 3.5.0
 */
if (!defined('WP_FAIL2BAN_OPENLOG_OPTIONS')) {
    define('WP_FAIL2BAN_OPENLOG_OPTIONS', LOG_PID);
}
/**
 * Make sure all custom logs are defined.
 * @since 3.5.0
 */
if (!defined('WP_FAIL2BAN_AUTH_LOG')) {
    define('WP_FAIL2BAN_AUTH_LOG', LOG_AUTH);
}
if (!defined('WP_FAIL2BAN_COMMENT_LOG')) {
    define('WP_FAIL2BAN_COMMENT_LOG', LOG_USER);
}
if (!defined('WP_FAIL2BAN_PINGBACK_LOG')) {
    define('WP_FAIL2BAN_PINGBACK_LOG', LOG_USER);
}


/**
 * @since 3.5.0 Refactored for unit testing
 */
function openlog($log = 'WP_FAIL2BAN_AUTH_LOG')
{
	$tag	= (defined('WP_FAIL2BAN_SYSLOG_SHORT_TAG') && true === WP_FAIL2BAN_SYSLOG_SHORT_TAG)
				? 'wp'
				: 'wordpress';
	$host	= (array_key_exists('WP_FAIL2BAN_HTTP_HOST', $_ENV))
				? $_ENV['WP_FAIL2BAN_HTTP_HOST']
				: $_SERVER['HTTP_HOST'];
    /**
     * Some varieties of syslogd have difficulty if $host is too long
     * @since 3.5.0
     */
    if (defined('WP_FAIL2BAN_TRUNCATE_HOST') && 1 < intval(WP_FAIL2BAN_TRUNCATE_HOST)) {
        $host = substr($host, 0, intval(WP_FAIL2BAN_TRUNCATE_HOST));
    }
	if (false === \openlog("$tag($host)", WP_FAIL2BAN_OPENLOG_OPTIONS, constant($log))) {
        error_log('WPf2b: Cannot open syslog', 0);
    } elseif (defined('WP_DEBUG') && true === WP_DEBUG) {
        error_log('WPf2b: Opened syslog', 0);
    }
}

/**
 * @since 3.5.0
 */
function syslog($level, $msg, $remote_addr = null)
{
    $msg .= ' from ';
    $msg .= (is_null($remote_addr))
                ? remote_addr()
                : $remote_addr;

    if (false === \syslog($level, $msg)) {
        error_log("WPf2b: Cannot write to syslog: '{$msg}'", 0);
    } elseif (defined('WP_DEBUG') && true === WP_DEBUG) {
        error_log("WPf2b: Wrote to syslog: '{$msg}'", 0);
    }
    \closelog();

    /**
     * @todo Remove this once phpunit can handle stderr.
     */
    if (!defined('ABSPATH')) {
        echo "$level|$msg";
    }
}

/**
 * @since 3.5.0 Refactored for unit testing
 */
function bail()
{
	wp_die('Forbidden', 'Forbidden', array('response' => 403));
}

function remote_addr()
{
	if (defined('WP_FAIL2BAN_PROXIES')) {
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ip = ip2long($_SERVER['REMOTE_ADDR']);
			foreach(explode(',', WP_FAIL2BAN_PROXIES) as $proxy) {
				if (2 == count($cidr = explode('/', $proxy))) {
					$net = ip2long($cidr[0]);
					$mask = ~ ( pow(2, (32 - $cidr[1])) - 1 );
				} else {
					$net = ip2long($proxy);
					$mask = -1;
				}
				if ($net == ($ip & $mask)) {
					return (false === ($len = strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')))
						? $_SERVER['HTTP_X_FORWARDED_FOR']
						: substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, $len);
				}
			}
		}
	}

	return $_SERVER['REMOTE_ADDR'];
}


/**
 * @since 2.0.0
 * @since 3.5.0 Refactored for unit testing
 */
function authenticate($user, $username, $password)
{
    if (!empty($username)) {
        /**
         * @since 3.5.0 Arrays allowed in PHP 7
         */
        $matched = (is_array(WP_FAIL2BAN_BLOCKED_USERS))
            ? in_array($username, WP_FAIL2BAN_BLOCKED_USERS)
            : preg_match('/'.WP_FAIL2BAN_BLOCKED_USERS.'/i', $username);

        if ($matched) {
            openlog();
            syslog(LOG_NOTICE, "Blocked authentication attempt for {$username}");
            bail();
        }
    }

    return $user;
}
if (defined('WP_FAIL2BAN_BLOCKED_USERS')) {
	add_filter('authenticate', __NAMESPACE__.'\authenticate', 1, 3);
}


/**
 * @since 2.1.0
 * @since 3.5.0 Refactored for unit testing
 * @since 3.5.1 Check is_admin
 */
if (defined('WP_FAIL2BAN_BLOCK_USER_ENUMERATION') && true === WP_FAIL2BAN_BLOCK_USER_ENUMERATION) {
    function parse_request($query)
    {
        if (!is_admin() && intval(@$query->query_vars['author'])) {
            openlog();
            syslog(LOG_NOTICE, 'Blocked user enumeration attempt');
            bail();
        }

        return $query;
    }
	add_filter('parse_request', __NAMESPACE__.'\parse_request', 1, 2);
}


/**
 * @since 2.2.0
 * @since 3.5.0 Refactored for unit testing
 */
if (defined('WP_FAIL2BAN_LOG_PINGBACKS') && true === WP_FAIL2BAN_LOG_PINGBACKS) {
    function xmlrpc_call($call)
    {
        if ('pingback.ping' == $call) {
            openlog('WP_FAIL2BAN_PINGBACK_LOG');
            syslog(LOG_INFO, 'Pingback requested');
        }
    }
	add_action('xmlrpc_call', __NAMESPACE__.'\xmlrpc_call');
}


/**
 * @since 3.5.0
 */
if (defined('WP_FAIL2BAN_LOG_COMMENTS') && true === WP_FAIL2BAN_LOG_COMMENTS) {
    function notify_post_author($maybe_notify, $comment_ID)
    {
        openlog('WP_FAIL2BAN_COMMENT_LOG');
        syslog(LOG_INFO, "Comment {$comment_ID}");

        return $maybe_notify;
    }
    add_filter('notify_post_author', __NAMESPACE__.'\notify_post_author', 10, 2);
}


/**
 * @since 3.5.0
 */
if (defined('WP_FAIL2BAN_LOG_SPAM') && true === WP_FAIL2BAN_LOG_SPAM) {
	function log_spam_comment($comment_id, $comment_status)
    {
		if ('spam' === $comment_status) {
			if (is_null($comment = get_comment($comment_id, ARRAY_A))) {
				/**
                 * @todo: decide what to do about this
                 */
			} else {
				$remote_addr = (empty($comment['comment_author_IP']))
					? 'unknown'
					: $comment['comment_author_IP'];

				openlog();
				syslog(LOG_INFO, "Spam comment {$comment_id}", $remote_addr);
			}
		}
	};
	add_action('comment_post', __NAMESPACE__.'\log_spam_comment', 10, 2);
	add_action('wp_set_comment_status', __NAMESPACE__.'\log_spam_comment', 10, 2);
}


/**
 * @since 3.5.0
 */
if (defined('WP_FAIL2BAN_LOG_PASSWORD_REQUEST') && true === WP_FAIL2BAN_LOG_PASSWORD_REQUEST) {
    function retrieve_password($user_login)
    {
        openlog();
        syslog(LOG_NOTICE, "Password reset requested for {$user_login}");
    }
	add_action('retrieve_password', __NAMESPACE__.'\retrieve_password');
}


/**
 * @since 1.0.0
 * @since 3.5.0 Refactored for unit testing
 */
function wp_login($user_login, $user)
{
    openlog();
    syslog(LOG_INFO, "Accepted password for {$user_login}");
}
add_action('wp_login', __NAMESPACE__.'\wp_login', 10, 2);


/**
 * @since 1.0.0
 * @since 3.5.0 Refactored for unit testing
 */
function wp_login_failed($username)
{
    global $wp_xmlrpc_server;

    $msg  = ($wp_xmlrpc_server)
            ? 'XML-RPC a'
            : 'A';
    $msg .= (wp_cache_get($username, 'userlogins'))
            ? "uthentication failure for {$username}"
            : "uthentication attempt for unknown user {$username}";
    openlog();
    syslog(LOG_NOTICE, $msg);
}
add_action('wp_login_failed', __NAMESPACE__.'\wp_login_failed');


/**
 * @since 3.0.0
 * @since 3.5.0 Refactored for unit testing
 */
function xmlrpc_login_error($error, $user)
{
    static $attempts = 0;

    if (++$attempts > 1) {
        openlog();
        syslog(LOG_NOTICE, 'XML-RPC multicall authentication failure');
        bail();
    }
}
add_action('xmlrpc_login_error', __NAMESPACE__.'\xmlrpc_login_error', 10, 2);


/**
 * @since 3.0.0
 * @since 3.5.0 Refactored for unit testing
 */
function xmlrpc_pingback_error($ixr_error)
{
    if (48 === $ixr_error->code)
        return $ixr_error;
    openlog();
    syslog(LOG_NOTICE, 'Pingback error '.$ixr_error->code.' generated');
}
add_filter('xmlrpc_pingback_error', __NAMESPACE__.'\xmlrpc_pingback_error', 5);
