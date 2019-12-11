<?php
/**
 * XML-RPC Request logging
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Log XML-RPC requests
 *
 * It seems attackers are doing weird things with XML-RPC. This makes it easy to
 * log them for analysis and future blocking.
 *
 * @since 4.0.0 Fix: Removed HTTP_RAW_POST_DATA
 *              https://wordpress.org/support/?p=10971843
 * @since 3.6.0 
 * 
 * @codeCoverageIgnore
 */
if (false === ($fp = fopen(WP_FAIL2BAN_XMLRPC_LOG, 'a+'))) {
    // TODO: decided whether to log this
} else {
    $raw_data = (version_compare(PHP_VERSION, '7.0.0') >= 0)
        ? file_get_contents('php://input')
        : $HTTP_RAW_POST_DATA;

    fprintf($fp, "# ---\n# Date: %s\n# IP: %s\n\n%s\n", date(DATE_ATOM), remote_addr(), $raw_data);
    fclose($fp);
}
