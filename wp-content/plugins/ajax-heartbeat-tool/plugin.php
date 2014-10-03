<?php
/*
Plugin Name: AJAX Heartbeat Tool
Version: 1.0
Description: Provides a method of turning the WordPress heartbeat off as well as change some settings. 
Author: Mikel King
Text Domain: ajax-heartbeat-tool
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause
*/

/*
    Copyright (C) 2014, Mikel King, rd.com, (mikel.king AT olivent DOT com)
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:
    
        * Redistributions of source code must retain the above copyright notice, this
          list of conditions and the following disclaimer.
        
        * Redistributions in binary form must reproduce the above copyright notice,
          this list of conditions and the following disclaimer in the documentation
          and/or other materials provided with the distribution.
        
        * Neither the name of the {organization} nor the names of its
          contributors may be used to endorse or promote products derived from
          this software without specific prior written permission.
    
    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
    AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
    IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
    FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
    DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
    SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
    CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
    OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
    OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/*  Turned off ALL WP heartbeats 
    @TODO implement a proper slowdown using the heartbeat API 
    @URL http://pippinsplugins.com/using-the-wordpress-heartbeat-api/
    @URL http://code.tutsplus.com/tutorials/the-heartbeat-api-changing-the-pulse--wp-32462
*/

class Ajax_Heartbeat_Tool {
    const VERSION   = '1.0';
    const INTERVAL  = 60;
    const DISABLED  = false;
    const ENABLED   = true;
    
    private static $instance = array();
    
    protected static $initialized = false;

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'unregister_wp_heartbeat' ));
        add_filter( 'heartbeat_settings', array( $this, 'disable_wp_heartbeat_autostart' ));
        add_filter( 'heartbeat_settings', array( $this, 'set_wp_heartbeat_interval' ));
    }

    /*
        Will always return the self initiated copy of itself.
    */
    public static function init() {
        if (function_exists("is_admin") && is_admin() &&
            function_exists('add_filter') && ! self::$initialized) {
            self::$initialized = true;
            return( self::$initialized );
        }
    }

    public static function get_instance() {
        $caller = get_called_class();
        if ( !isset( self::$instance[$caller] ) ) {
            self::$instance[$caller] = new $caller();
            self::$instance[$caller]->init();
        }

        return( self::$instance[$caller] );
    }

    public function disable_wp_heartbeat_autostart( $settings ) {
        $settings['autostart'] = self::INTERVAL;
        return( $settings );
    }
    
    public function set_wp_heartbeat_interval( $settings ) {
        $settings['interval'] = self::DISABLED;
        return( $settings );
    }
    
    public function unregister_wp_heartbeat() {
    	global $pagenow;
    	
    	if ( 'post.php' != $pagenow && 'post-new.php' != $pagenow ) {
    		wp_deregister_script('heartbeat');
    		//wp_register_script('heartbeat', false);
    	}
    }
}

$aht = Ajax_Heartbeat_Tool::get_instance();
