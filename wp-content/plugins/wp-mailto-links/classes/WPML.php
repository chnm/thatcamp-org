<?php
/**
 * Class WPML
 *
 * @sinlgeton
 *
 * @package  WPML
 * @category WordPress Plugins
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  MIT license
 */
class WPML extends WP_Plugin_Abstract
{

    /**
     * Init before using
     * @param array $extraGlobals
     */
    public static function init(array $globals = array())
    {
        $instance = self::getInstance();

        // init globals
        $instance->globals = $globals;

        // load text domain for translations
        load_plugin_textdomain(self::get('domain'), false, self::get('dir') . '/languages');

        // compare versions
        $validPhpVersion = version_compare(phpversion(), self::get('minPhpVersion'), '>=');
        $validWpVersion = version_compare(preg_replace('/-.*$/', '', self::get('wpVersion')), self::get('minWpVersion'), '>=');

        // check plugin compatibility
        if ($validPhpVersion && $validWpVersion) {
            // start...
            add_action('init', array($instance, 'actionInit'), 5);
        } else {
            add_action('admin_notices', array($instance, 'actionAdminNotices'));
        }
    }

    /**
     * WP action callback
     */
    public function actionInit()
    {
        WPML::set('optionValues', $this->initOptionValues());

        if (is_admin()) {
            // create admin
            WPML::set('admin', new WPML_Admin);
        } else {
            // create front
            WPML::set('front', new WPML_Front);

            // create template functions
            if (!function_exists('wpml_mailto')):
                function wpml_mailto($email, $display = null, $attrs = array())
                {
                    if (is_array($display)) {
                       // backwards compatibility (old params: $display, $attrs = array())
                       $attrs   = $display;
                       $display = $email;
                   } else {
                       $attrs['href'] = 'mailto:'.$email;
                   }

                   return WPML::get('front')->protectedMailto($display, $attrs);
               }
            endif;

            if (!function_exists('wpml_filter')):
                function wpml_filter($content)
                {
                    return WPML::get('front')->filterContent($content);
                }
            endif;
        }

        // init test
        if (class_exists('Test_WP_Mailto_Links')) {
            $Test = new Test_WP_Mailto_Links;
        }
    }

    /**
     * Init option values
     */
    protected function initOptionValues()
    {
        $settings = array(
            'file' => self::get('file'),
            'optionGroup' => self::get('key'),
            'optionName' => self::get('optionName'),
        );

        $defaultValues = array(
            'version' => null,
            'convert_emails' => 1,
            'protect' => 1,
            'filter_body' => 1,
            'filter_posts' => 1,
            'filter_comments' => 1,
            'filter_widgets' => 1,
            'filter_rss' => 1,
            'filter_head' => 1,
            'protection_text' => '*protected email*',
            'icon' => 0,
            'image_no_icon' => 0,
            'no_icon_class' => 'no-mail-icon',
            'class_name' => 'mail-link',
            'widget_logic_filter' => 0,
            'own_admin_menu' => 0,
        );

        // options instance
        $optionValues = new WP_Plugin_OptionValues($settings, $defaultValues);

        // check if this is an update
        if ($optionValues->get('version') !== self::get('version')) {
            // update version
            $optionValues->set('version', self::get('version'));
            $optionValues->save();

            // check for old values of 1.x version
            $oldValues = get_option('WP_Mailto_Links_options');

            if ($oldValues) {
                $defaultValues = $oldValues;

                // set new instance with old values as defaults
                $optionValues = new WP_Plugin_OptionValues($settings, $defaultValues);
            }
        }
        
        return $optionValues;
    }

    /**
     * WP action callback
     */
    public function actionAdminNotices()
    {
        $plugin_title = get_admin_page_title();

        echo '<div class="error">';
        echo sprintf(WPML::__('<p>Warning - The plugin <strong>%s</strong> requires PHP 5.2.4+ and WP 3.4+.'
                . '  Please upgrade your PHP and/or WordPress.'
                . '<br/>Disable the plugin to remove this message.</p>'), $plugin_title);
        echo '</div>';
    }

} // End Class WPML
