<?php

/*
 * Plugin Name: WP fail2ban
 * Plugin URI: https://wp-fail2ban.com/
 * Description: Write a myriad of WordPress events to syslog for integration with fail2ban.
 * Text Domain: wp-fail2ban
 * Version: 4.2.7.1
 * Author: Charles Lecklider
 * Author URI: https://charles.lecklider.org/
 * License: GPLv2
 * SPDX-License-Identifier: GPL-2.0
 * Requires PHP: 5.3
 *
 */
/*
 *  Copyright 2012-19  Charles Lecklider  (email : wordpress@charles.lecklider.org)
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
/**
 * WP fail2ban
 *
 * @package wp-fail2ban 
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

/**
 * @since 4.0.5
 */
define( 'WP_FAIL2BAN_VER', '4.2.7.1' );
define( 'WP_FAIL2BAN_FILE', __FILE__ );

if ( defined( 'ABSPATH' ) ) {
    /**
     * Freemius integration
     *
     * @since 4.0.0
     */
    
    if ( function_exists( __NAMESPACE__ . '\\wf_fs' ) ) {
        // @codeCoverageIgnoreStart
        wf_fs()->set_basename( false, __FILE__ );
        return;
    } else {
        /**
         * Create a helper function for easy SDK access.
         */
        function wf_fs()
        {
            global  $wf_fs ;
            
            if ( !isset( $wf_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                $wf_fs = fs_dynamic_init( array(
                    'id'             => '3072',
                    'slug'           => 'wp-fail2ban',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_146d2c2a5bee3b157e43501ef8682',
                    'is_premium'     => false,
                    'has_addons'     => true,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 14,
                    'is_require_payment' => false,
                ),
                    'menu'           => array(
                    'slug'       => 'wp-fail2ban',
                    'first-path' => 'admin.php?page=wp-fail2ban',
                    'support'    => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $wf_fs;
        }
        
        // Init Freemius.
        wf_fs();
        // Set currency to GBP
        wf_fs()->add_filter( 'default_currency', function () {
            return 'gbp';
        } );
        // Signal that SDK was initiated.
        do_action( 'wf_fs_loaded' );
    }
    
    // @codeCoverageIgnoreEnd
    /**
     * Freemius insists on mangling the formatting of the main plugin file
     *
     * @since 4.0.0     Refactored
     */
    require_once 'wp-fail2ban-main.php';
}
