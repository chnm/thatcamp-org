<?php

/**
 * Spam comments
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

if ( !function_exists( __NAMESPACE__ . '\\log_spam_comment' ) ) {
    /**
     * Catch comments marked as spam
     *
     * @since 3.5.0
     *
     * @param int       $comment_id
     * @param string    $comment_status
     *
     * @wp-f2b-hard Spam comment \d+
     */
    function log_spam_comment( $comment_id, $comment_status )
    {
        if ( 'spam' === $comment_status ) {
            
            if ( is_null( $comment = get_comment( $comment_id, ARRAY_A ) ) ) {
                /**
                 * @todo: decide what to do about this
                 */
            } else {
                $remote_addr = ( empty($comment['comment_author_IP']) ? 'unknown' : $comment['comment_author_IP'] );
                openlog( 'WP_FAIL2BAN_SPAM_LOG' );
                syslog( LOG_NOTICE, "Spam comment {$comment_id}", $remote_addr );
                closelog();
                // @codeCoverageIgnoreEnd
            }
        
        }
    }
    
    add_action(
        'comment_post',
        __NAMESPACE__ . '\\log_spam_comment',
        10,
        2
    );
    add_action(
        'wp_set_comment_status',
        __NAMESPACE__ . '\\log_spam_comment',
        10,
        2
    );
}
