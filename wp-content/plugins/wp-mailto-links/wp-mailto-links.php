<?php defined('ABSPATH') OR die('No direct access.');
/**
 * WP Mailto Links - Manage Email Links
 *
 * @package  WPML
 * @category WordPress Plugin
 * @version  2.1.2
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/wp-mailto-links-plugin
 * @license  GPLv2+ license
 *
 * @wordpress-plugin
 * Plugin Name:    WP Mailto Links - Manage Email Links
 * Version:        2.1.2
 * Plugin URI:     http://www.freelancephp.net/wp-mailto-links-plugin
 * Description:    Manage mailto links on your site and protect email addresses from spambots, set mail icon and more.
 * Author:         Victor Villaverde Laan
 * Author URI:     http://www.freelancephp.net
 * License:        GPLv2+ license
 * Text Domain:    wp-mailto-links
 * Domain Path:    /languages
 */
call_user_func(function () {

    // set constant
    if (!defined('WP_MAILTO_LINKS_FILE')) {
        define('WP_MAILTO_LINKS_FILE', __FILE__);
    }
    if (!defined('WP_MAILTO_LINKS_DIR')) {
        define('WP_MAILTO_LINKS_DIR', __DIR__);
    }

    // set class auto-loader
    if (!class_exists('WPRun_AutoLoader_0x5x0')) {
        require_once WP_MAILTO_LINKS_DIR . '/classes/WPRun/AutoLoader.php';
    }
    WPRun_AutoLoader_0x5x0::register();
    WPRun_AutoLoader_0x5x0::addPath(WP_MAILTO_LINKS_DIR . '/classes');

    // load text domain
    add_action('plugins_loaded', function () {
        load_plugin_textdomain('wp-mailto-links', false, plugin_basename(WP_MAILTO_LINKS_DIR) . '/languages/');
    });

    /**
     * Create plugin components
     */

    // create option
    $option = WPML_Option_Settings::create();

    // create register hooks
    WPML_RegisterHook_Activate::create(WP_MAILTO_LINKS_FILE, $option);
    WPML_RegisterHook_Uninstall::create(WP_MAILTO_LINKS_FILE, $option);

    if (is_admin()) {
        WPML_AdminPage_Settings::create($option);
    } else {
        // create custom filters final_output and widget_output
        WPRun_Filter_FinalOutput_0x5x0::create();
        WPRun_Filter_WidgetOutput_0x5x0::create();

        $emailEncoder = WPML_Front_Email::create($option);
        WPML_Front_Site::create($option, $emailEncoder);

        // create shortcode
        WPML_Shortcode_Mailto::create($option, $emailEncoder);

        // create template tags
        WPML_TemplateTag_Filter::create($option, $emailEncoder);
        WPML_TemplateTag_Mailto::create($emailEncoder);
    }

});


/*?>*/
