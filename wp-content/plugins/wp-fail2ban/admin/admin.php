<?php

/**
 * Admin
 *
 * @package wp-fail2ban
 * @since 4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require __DIR__ . '/config.php';
require __DIR__ . '/lib/about.php';
/**
 * Register admin menus
 *
 * @since 4.0.0
 */
function admin_menu()
{
    global  $submenu ;
    add_menu_page(
        'WP fail2ban',
        'WP fail2ban',
        'manage_options',
        'wp-fail2ban',
        __NAMESPACE__ . '\\about',
        'dashicons-analytics'
    );
    
    if ( function_exists( '\\add_security_page' ) ) {
        $slug = 'wp-fail2ban';
        add_security_page(
            'WP fail2ban',
            'WP fail2ban',
            $slug,
            __NAMESPACE__ . '\\security'
        );
    } else {
        add_submenu_page(
            'wp-fail2ban',
            'Settings',
            'Settings',
            'manage_options',
            'wpf2b-settings',
            __NAMESPACE__ . '\\settings'
        );
    }
    
    $hook = add_submenu_page(
        'wp-fail2ban',
        'WP fail2ban - Remote Tools',
        'Remote Tools',
        'manage_options',
        'wp-fail2ban-tools',
        __NAMESPACE__ . '\\remote_tools'
    );
    add_action( "load-{$hook}", function () {
        if ( function_exists( '\\org\\lecklider\\charles\\wordpress\\wp_fail2ban\\addons\\remote_tools\\help' ) ) {
            \org\lecklider\charles\wordpress\wp_fail2ban\addons\remote_tools\help();
        }
    } );
    $submenu['wp-fail2ban'][0][0] = __( 'Welcome' );
}

add_action( 'admin_menu', __NAMESPACE__ . '\\admin_menu' );
/**
 * Add Settings link on Plugins page
 *
 * @since 4.2.6 Add support for ClassicPress security page
 * @since 4.2.0
 *
 * @param array     $links
 * @param string    $file
 */
function plugin_action_links( $links, $file )
{
    if ( preg_match( "|{$file}\$|", WP_FAIL2BAN_FILE ) ) {
        array_unshift( $links, sprintf(
            '<a href="%s?page=%s" title="%s">%s</a>',
            admin_url( 'admin.php' ),
            ( wf_fs()->is_free_plan() ? 'wp-fail2ban' : 'wpf2b-settings' ),
            __( 'Settings' ),
            ( function_exists( '\\add_security_page' ) ? '<span class="dashicon dashicons-admin-generic"></span>' : __( 'Settings' ) )
        ) );
    }
    return $links;
}

add_filter(
    'plugin_action_links',
    __NAMESPACE__ . '\\plugin_action_links',
    10,
    2
);