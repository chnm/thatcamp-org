<?php

/**
 * Comment logging
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

if ( !function_exists( __NAMESPACE__ . '\\notify_post_author' ) ) {
    /**
     * Log new comment
     *
     * @since 3.5.0
     *
     * @param bool $maybe_notify
     * @param int  $comment_ID
     *
     * @return bool
     *
     * @wp-f2b-extra Comment \d+
     */
    function notify_post_author( $maybe_notify, $comment_ID )
    {
        openlog( 'WP_FAIL2BAN_COMMENT_LOG' );
        syslog( LOG_INFO, "Comment {$comment_ID}" );
        closelog();
        // @codeCoverageIgnoreEnd
        return $maybe_notify;
    }
    
    add_filter(
        'notify_post_author',
        __NAMESPACE__ . '\\notify_post_author',
        10,
        2
    );
}


if ( defined( 'WP_FAIL2BAN_LOG_COMMENTS_EXTRA' ) ) {
    /** WPF2B_EVENT_COMMENT_NOT_FOUND */
    if ( WP_FAIL2BAN_LOG_COMMENTS_EXTRA & 0x20002 ) {
        /**
         * @since 4.0.5 Guard
         */
        
        if ( !function_exists( __NAMESPACE__ . '\\comment_id_not_found' ) ) {
            /**
             * Log attempted comment on non-existent post
             *
             * @since 4.0.0
             *
             * @param int $comment_post_ID
             *
             * @wp-f2b-extra Comment post not found \d+
             */
            function comment_id_not_found( $comment_post_ID )
            {
                openlog( 'WP_FAIL2BAN_COMMENT_EXTRA_LOG' );
                syslog( LOG_NOTICE, "Comment post not found {$comment_post_ID}" );
                closelog();
                // @codeCoverageIgnoreEnd
            }
            
            add_action( 'comment_id_not_found', __NAMESPACE__ . '\\comment_id_not_found' );
        }
    
    }
    /** LOG_ACTION_LOG_COMMENT_CLOSED */
    if ( WP_FAIL2BAN_LOG_COMMENTS_EXTRA & 0x20004 ) {
        /**
         * @since 4.0.5 Guard
         */
        
        if ( !function_exists( __NAMESPACE__ . '\\comment_closed' ) ) {
            /**
             * Log attempted comment on closed post
             *
             * @since 4.0.0
             *
             * @param int $comment_post_ID
             *
             * @wp-f2b-extra Comments closed on post \d+
             */
            function comment_closed( $comment_post_ID )
            {
                openlog( 'WP_FAIL2BAN_COMMENT_EXTRA_LOG' );
                syslog( LOG_NOTICE, "Comments closed on post {$comment_post_ID}" );
                closelog();
                // @codeCoverageIgnoreEnd
            }
            
            add_action( 'comment_closed', __NAMESPACE__ . '\\comment_closed' );
        }
    
    }
    /** LOG_ACTION_LOG_COMMENT_TRASH */
    if ( WP_FAIL2BAN_LOG_COMMENTS_EXTRA & 0x20008 ) {
        /**
         * @since 4.0.5 Guard
         */
        
        if ( !function_exists( __NAMESPACE__ . '\\comment_on_trash' ) ) {
            /**
             * Log attempted comment on trashed post
             *
             * @since 4.0.2 Fix message
             * @since 4.0.0
             *
             * @param int $comment_post_ID
             *
             * @wp-f2b-extra Comment attempt on trash post \d+
             */
            function comment_on_trash( $comment_post_ID )
            {
                openlog( 'WP_FAIL2BAN_COMMENT_EXTRA_LOG' );
                syslog( LOG_NOTICE, "Comment attempt on trash post {$comment_post_ID}" );
                closelog();
                // @codeCoverageIgnoreEnd
            }
            
            add_action( 'comment_on_trash', __NAMESPACE__ . '\\comment_on_trash' );
        }
    
    }
    /** LOG_ACTION_LOG_COMMENT_DRAFT */
    if ( WP_FAIL2BAN_LOG_COMMENTS_EXTRA & 0x20010 ) {
        /**
         * @since 4.0.5 Guard
         */
        
        if ( !function_exists( __NAMESPACE__ . '\\comment_on_draft' ) ) {
            /**
             * Log attempted comment on draft post
             *
             * @since 4.0.2 Fix message
             * @since 4.0.0
             *
             * @param int $comment_post_ID
             *
             * @wp-f2b-extra Comment attempt on draft post \d+
             */
            function comment_on_draft( $comment_post_ID )
            {
                openlog( 'WP_FAIL2BAN_COMMENT_EXTRA_LOG' );
                syslog( LOG_NOTICE, "Comment attempt on draft post {$comment_post_ID}" );
                closelog();
                // @codeCoverageIgnoreEnd
            }
            
            add_action( 'comment_on_draft', __NAMESPACE__ . '\\comment_on_draft' );
        }
    
    }
    /** LOG_ACTION_LOG_COMMENT_PASSWORD */
    if ( WP_FAIL2BAN_LOG_COMMENTS_EXTRA & 0x20020 ) {
        /**
         * @since 4.0.5 Guard
         */
        
        if ( !function_exists( __NAMESPACE__ . '\\comment_on_password_protected' ) ) {
            /**
             * Log attempted comment on password-protected post
             *
             * @since 4.0.2 Fix message
             * @since 4.0.0
             *
             * @param int $comment_post_ID
             *
             * @wp-f2b-extra Comment attempt on password-protected post \d+
             */
            function comment_on_password_protected( $comment_post_ID )
            {
                openlog( 'WP_FAIL2BAN_COMMENT_EXTRA_LOG' );
                syslog( LOG_NOTICE, "Comment attempt on password-protected post {$comment_post_ID}" );
                closelog();
                // @codeCoverageIgnoreEnd
            }
            
            add_action( 'comment_on_password_protected', __NAMESPACE__ . '\\comment_on_password_protected' );
        }
    
    }
}
