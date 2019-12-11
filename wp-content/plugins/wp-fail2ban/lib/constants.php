<?php
/**
 * Constants
 *
 * @package wp-fail2ban
 * @since 4.2.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing
/**
 * Defaults
 *
 * @since 4.0.0
 */
define('DEFAULT_WP_FAIL2BAN_OPENLOG_OPTIONS',       LOG_PID|LOG_NDELAY);
define('DEFAULT_WP_FAIL2BAN_AUTH_LOG',              LOG_AUTH);
define('DEFAULT_WP_FAIL2BAN_COMMENT_LOG',           LOG_USER);
define('DEFAULT_WP_FAIL2BAN_PINGBACK_LOG',          LOG_USER);
define('DEFAULT_WP_FAIL2BAN_PASSWORD_REQUEST_LOG',  LOG_USER);
define('DEFAULT_WP_FAIL2BAN_SPAM_LOG',              LOG_AUTH);
/**
 * @since 4.0.5
 */
define('DEFAULT_WP_FAIL2BAN_COMMENT_EXTRA_LOG',     LOG_AUTH);
define('DEFAULT_WP_FAIL2BAN_PINGBACK_ERROR_LOG',    LOG_AUTH);
/**
 * @since 4.2.0
 */
define('DEFAULT_WP_FAIL2BAN_PLUGIN_AUTH_LOG',       LOG_AUTH);
define('DEFAULT_WP_FAIL2BAN_PLUGIN_COMMENT_LOG',    LOG_USER);
define('DEFAULT_WP_FAIL2BAN_PLUGIN_OTHER_LOG',      LOG_USER);
define('DEFAULT_WP_FAIL2BAN_PLUGIN_PASSWORD_LOG',   LOG_USER);
define('DEFAULT_WP_FAIL2BAN_PLUGIN_REST_LOG',       LOG_USER);
define('DEFAULT_WP_FAIL2BAN_PLUGIN_SPAM_LOG',       LOG_AUTH);
define('DEFAULT_WP_FAIL2BAN_PLUGIN_XMLRPC_LOG',     LOG_USER);

/*
31 | Test
30 | Plugin
29 |
28 |
27 |
26 |
25 |
24 |
---
23 | Event Class
22 | ..
21 | ..
20 | ..
19 | ..
18 | ..
17 | ..
16 | ..
---
15 | ID
14 | ..
13 | ..
12 | ..
11 | ..
10 | ..
09 | ..
08 | ..
---
07 | ..
06 | ..
05 | ..
04 | ..
03 | ..
02 | ..
01 | ..
00 | ..
*/



define('WPF2B_EVENT_CLASS_AUTH',                0x00010000);
define('WPF2B_EVENT_CLASS_COMMENT',             0x00020000);
define('WPF2B_EVENT_CLASS_XMLRPC',              0x00040000);
define('WPF2B_EVENT_CLASS_PASSWORD',            0x00080000);
define('WPF2B_EVENT_CLASS_REST',                0x00100000);    /** @since 4.1.0 */
define('WPF2B_EVENT_CLASS_SPAM',                0x00200000);    /** @since 4.2.0 */
define('WPF2B_EVENT_TYPE_PLUGIN',               0x40000000);    /** @since 4.2.0 */
define('WPF2B_EVENT_TYPE_TEST',                 0x80000000);    /** @since 4.2.0 */


/**
 *
 */
define('WPF2B_EVENT_ACTIVATED',                 0xffffffff);


/**
 * Auth
 */
define('WPF2B_EVENT_AUTH_OK',                   WPF2B_EVENT_CLASS_AUTH | 0x0001);
define('WPF2B_EVENT_AUTH_FAIL',                 WPF2B_EVENT_CLASS_AUTH | 0x0002);
define('WPF2B_EVENT_AUTH_BLOCK_USER',           WPF2B_EVENT_CLASS_AUTH | 0x0004);
define('WPF2B_EVENT_AUTH_BLOCK_USER_ENUM',      WPF2B_EVENT_CLASS_AUTH | 0x0008);

/**
 * Comment
 */
define('WPF2B_EVENT_COMMENT',                   WPF2B_EVENT_CLASS_COMMENT | 0x0001); // 0x00020001
define('WPF2B_EVENT_COMMENT_SPAM',              WPF2B_EVENT_CLASS_COMMENT | WPF2B_EVENT_CLASS_SPAM | 0x0001); // 0x00220001
//               comment extra
define('WPF2B_EVENT_COMMENT_NOT_FOUND',         WPF2B_EVENT_CLASS_COMMENT | 0x0002); // 0x00020002
define('WPF2B_EVENT_COMMENT_CLOSED',            WPF2B_EVENT_CLASS_COMMENT | 0x0004); // 0x00020004
define('WPF2B_EVENT_COMMENT_TRASH',             WPF2B_EVENT_CLASS_COMMENT | 0x0008); // 0x00020008
define('WPF2B_EVENT_COMMENT_DRAFT',             WPF2B_EVENT_CLASS_COMMENT | 0x0010); // 0x00020010
define('WPF2B_EVENT_COMMENT_PASSWORD',          WPF2B_EVENT_CLASS_COMMENT | WPF2B_EVENT_CLASS_PASSWORD | 0x0020); // 0x00020020

/**
 * XML-RPC
 */
define('WPF2B_EVENT_XMLRPC_PINGBACK',           WPF2B_EVENT_CLASS_XMLRPC | 0x0001);
define('WPF2B_EVENT_XMLRPC_PINGBACK_ERROR',     WPF2B_EVENT_CLASS_XMLRPC | 0x0002);
define('WPF2B_EVENT_XMLRPC_MULTI_AUTH_FAIL',    WPF2B_EVENT_CLASS_XMLRPC | WPF2B_EVENT_CLASS_AUTH | 0x0004);
define('WPF2B_EVENT_XMLRPC_AUTH_OK',            WPF2B_EVENT_CLASS_XMLRPC | WPF2B_EVENT_CLASS_AUTH | 0x0008);
define('WPF2B_EVENT_XMLRPC_AUTH_FAIL',          WPF2B_EVENT_CLASS_XMLRPC | WPF2B_EVENT_CLASS_AUTH | 0x0010);

/**
 * Password
 */
define('WPF2B_ACTION_PASSWORD_REQUEST',         WPF2B_EVENT_CLASS_PASSWORD | 0x0001);

/**
 * REST
 * @since 4.1.0
 */
define('WPF2B_EVENT_REST_AUTH_OK',              WPF2B_EVENT_CLASS_REST | WPF2B_EVENT_CLASS_AUTH | 0x0001);
define('WPF2B_EVENT_REST_AUTH_FAIL',            WPF2B_EVENT_CLASS_REST | WPF2B_EVENT_CLASS_AUTH | 0x0002);

/**
 *
 */
define('WPF2B_EVENT_DEACTIVATED',               0x00000000);
// phpcs:enable

