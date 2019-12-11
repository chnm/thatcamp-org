<?php

/**
 * Library functions
 *
 * @package wp-fail2ban
 * @since 4.2.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Hook: plugins_loaded
 *
 * @since 4.2.0
 */
function plugins_loaded()
{
    do_action( 'wp_fail2ban_register' );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\plugins_loaded' );
/**
 * Register plugin
 *
 * @since 4.2.0
 *
 * @param string    $slug   Plugin slug. This must be the actual plugin slug. Maximum length is 255 which should be more than enough.
 * @param string    $name   Plugin display name. This should be an unescaped string - HTML is allowed.
 *
 * @return int|false        ID
 */
function register_plugin( $slug, $name )
{
    global  $wp_fail2ban, $wpdb ;
    if ( 255 < strlen( $slug ) ) {
        throw new \LengthException( 'slug too long' );
    }
    if ( 255 < strlen( $name ) ) {
        throw new \LengthException( 'name too long' );
    }
    if ( !is_array( @$wp_fail2ban['plugins'] ) ) {
        $wp_fail2ban['plugins'] = [];
    }
    if ( array_key_exists( $slug, $wp_fail2ban['plugins'] ) ) {
        return $wp_fail2ban['plugins'][$slug];
    }
    static  $id = 0 ;
    return $wp_fail2ban['plugins'][$slug] = [
        'id'       => ++$id,
        'name'     => $name,
        'messages' => [],
    ];
}

add_action(
    'wp_fail2ban_register_plugin',
    __NAMESPACE__ . '\\register_plugin',
    1,
    2
);
/**
 * Check if plugin is registered.
 *
 * @since 4.2.0
 *
 * @param string    $plugin_slug
 *
 * @return bool
 */
function is_registered_plugin( $plugin_slug )
{
    global  $wp_fail2ban ;
    return array_key_exists( $plugin_slug, $wp_fail2ban['plugins'] );
}

/**
 * Register plugin message.
 *
 * @since 4.2.0
 *
 * @param string    $plugin_slug
 * @param array     $msg    [
 *                      string  slug:           Message slug
 *                      string  fail:           hard|soft|extra
 *                      int     facility:       syslog facility
 *                      int     priority:       syslog priority
 *                      string  event_class:    Event Class
 *                      int     event_id:       Event ID
 *                      string  message:        Message with placeholders
 *                          HOST:   Remote IP
 *                          USER:   Current user name
 *                      array   vars:       Array of [name => regex] pairs
 */
function register_message( $plugin_slug, array $msg )
{
    global  $wp_fail2ban ;
    $event_classes = [
        'auth'     => WPF2B_EVENT_CLASS_AUTH,
        'comment'  => WPF2B_EVENT_CLASS_COMMENT,
        'password' => WPF2B_EVENT_CLASS_PASSWORD,
        'rest'     => WPF2B_EVENT_CLASS_REST,
        'spam'     => WPF2B_EVENT_CLASS_SPAM,
        'xmlrpc'   => WPF2B_EVENT_CLASS_XMLRPC,
        'other'    => 0,
    ];
    $args = [];
    if ( !is_registered_plugin( $plugin_slug ) ) {
        throw new \InvalidArgumentException( 'plugin not registered' );
    }
    if ( !array_key_exists( 'slug', $msg ) ) {
        throw new \InvalidArgumentException( "Missing 'slug'" );
    }
    if ( !is_string( $msg['slug'] ) ) {
        throw new \InvalidArgumentException( "'slug' must be string" );
    }
    if ( !array_key_exists( 'fail', $msg ) ) {
        throw new \InvalidArgumentException( "Missing 'fail'" );
    }
    if ( !in_array( $msg['fail'], [ 'hard', 'soft', 'extra' ] ) ) {
        throw new \UnexpectedValueException( "'fail' must be one of 'hard', 'soft', 'extra'" );
    }
    $args['fail'] = $msg['fail'];
    if ( !array_key_exists( 'priority', $msg ) ) {
        throw new \InvalidArgumentException( "Missing 'priority'" );
    }
    if ( !in_array( $msg['priority'], [
        LOG_CRIT,
        LOG_ERR,
        LOG_WARNING,
        LOG_NOTICE,
        LOG_INFO,
        LOG_DEBUG
    ] ) ) {
        throw new \UnexpectedValueException( "Invalid 'priority'" );
    }
    $args['priority'] = $msg['priority'];
    if ( !array_key_exists( 'event_class', $msg ) ) {
        throw new \InvalidArgumentException( "Missing 'event_class'" );
    }
    if ( !array_key_exists( $event_class = strtolower( $msg['event_class'] ), $event_classes ) ) {
        throw new \UnexpectedValueException( "Invalid 'event_class'" );
    }
    $args['class'] = $event_class;
    $event_class = $event_classes[$event_class];
    $log = sprintf( "WP_FAIL2BAN_%s_LOG", strtoupper( $event_class ) );
    if ( !array_key_exists( 'event_id', $msg ) ) {
        throw new \InvalidArgumentException( "Missing 'event_id'" );
    }
    if ( ($msg['event_id'] & 0xffff) !== $msg['event_id'] ) {
        throw new \UnexpectedValueException( "Invalid 'event_id'" );
    }
    $args['event_id'] = WPF2B_EVENT_TYPE_PLUGIN | $event_class | $msg['event_id'];
    if ( !array_key_exists( 'message', $msg ) ) {
        throw new \InvalidArgumentException( "Missing 'message'" );
    }
    if ( !is_string( $msg['message'] ) ) {
        throw new \UnexpectedValueException( "Invalid 'message'" );
    }
    $args['message'] = $msg['message'];
    if ( !array_key_exists( 'vars', $msg ) ) {
        throw new \InvalidArgumentException( "Missing 'vars'" );
    }
    if ( !is_array( $msg['vars'] ) ) {
        throw new \UnexpectedValueException( "Invalid 'vars'" );
    }
    $args['vars'] = $msg['vars'];
    $wp_fail2ban['plugins'][$plugin_slug]['messages'][$msg['slug']] = $args;
}

add_action(
    'wp_fail2ban_register_message',
    __NAMESPACE__ . '\\register_message',
    1,
    2
);
/**
 * Check if message is registered.
 *
 * NB: Assumes plugin is registered.
 *
 * @since 4.2.0
 *
 * @param string    $plugin_slug
 * @param string    $message_slug
 *
 * @return bool
 */
function is_registered_plugin_message( $plugin_slug, $message_slug )
{
    global  $wp_fail2ban ;
    return array_key_exists( $message_slug, $wp_fail2ban['plugins'][$plugin_slug]['messages'] );
}

/**
 * Log plugin message.
 *
 * @since 4.2.0
 *
 * @param string    $plugin_slug    Plugin slug for registered message
 * @param string    $message_slug   Message slug for registered message
 * @param array     $vars           Substitution vars
 */
function log_message( $plugin_slug, $message_slug = null, array $vars = array() )
{
    global  $wp_fail2ban ;
    if ( !is_registered_plugin( $plugin_slug ) ) {
        throw new \InvalidArgumentException( 'plugin not registered' );
    }
    if ( !is_registered_plugin_message( $plugin_slug, $message_slug ) ) {
        throw new \InvalidArgumentException( 'message not registered' );
    }
    $args = $wp_fail2ban['plugins'][$plugin_slug]['messages'][$message_slug];
    $msg = $args['message'];
    foreach ( $args['vars'] as $name => $regex ) {
        if ( array_key_exists( $name, $vars ) ) {
            $msg = str_replace( "___{$name}___", $vars[$name], $msg );
        }
    }
    openlog( sprintf( 'WP_FAIL2BAN_PLUGIN_%s_LOG', strtoupper( $args['class'] ) ) );
    syslog( $args['priority'], "({$plugin_slug}) {$msg}" );
    closelog();
    // @codeCoverageIgnoreEnd
}

add_action(
    'wp_fail2ban_log_message',
    __NAMESPACE__ . '\\log_message',
    1,
    3
);