<?php
/**
 * Class WPML_Admin
 *
 * @package  WPML
 * @category WordPress Plugins
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  MIT license
 */
class WPML_Admin extends WP_Plugin_AdminPage
{

    /**
     * Constructor
     * Init settings, metaboxes, helptabs etc
     */
    public function __construct()
    {
        $settings = array(
            'file' => WPML::get('file'),
            'key' => WPML::get('key'),
            'pageKey' => WPML::get('adminPage'),
            'pageTitle' => WPML::__('WP Mailto Links'),
            'menuIcon' => 'images/icon-wp-mailto-links-16.png',
            'mainMenu' => (bool) WPML::get('optionValues')->get('own_admin_menu'),
        );

        $this->metaboxes = array(
            'general' => array(
                'title' => WPML::__('General Settings'),
                'position' => 'normal',
             ),
            'style' => array(
                'title' => WPML::__('Style Settings'),
                'position' => 'normal',
             ),
            'admin' => array(
                'title' => WPML::__('Admin Settings'),
                'position' => 'normal',
             ),
            'this-plugin' => array(
                'title' => WPML::__('Support'),
                'position' => 'side',
             ),
            'other-plugins' => array(
                'title' => WPML::__('Other Plugins'),
                'position' => 'side',
             ),
        );

        $this->helptabs = array(
            'general' => array(
                'title' => WPML::__('General'),
             ),
            'shortcodes' => array(
                'title' => WPML::__('Shortcodes'),
             ),
            'templatefunctions' => array(
                'title' => WPML::__('Template functions'),
             ),
            'actionhooks' => array(
                'title' => WPML::__('Action Hooks'),
             ),
            'filterhooks' => array(
                'title' => WPML::__('Filter Hooks'),
             ),
            'faq' => array(
                'title' => WPML::__('FAQ'),
             ),
        );

        parent::__construct($settings);
    }

    /**
     * WP action callback
     */
    public function actionAdminInit()
    {
        // prepare view
        WPML_View::addPath(WPML::get('dir') . '/views');
        WPML_View::setGlobalVar('values', WPML::get('optionValues')->get());

        // actions and filters
        add_action('admin_notices', array($this, 'actionAdminNotices'));
        add_filter('plugin_action_links', array($this, 'filterPluginActionLinks'), 10, 2);
    }

    /**
     * WP action callback
     */
    public function loadPage()
    {
        parent::loadPage();
        
        // add plugin script
        wp_enqueue_script(
            'WPML_admin',
            WPML::url('js/wp-mailto-links-admin.js'),
            array('jquery'),
            WPML::get('version')
        );
    }

    /**
     * Callback add links on plugin page
     * @param array $links
     * @param string $file
     * @return array
     */
    public function filterPluginActionLinks($links, $file)
    {
        $pluginFile = plugin_basename(WPML::get('file'));
        $compareFile = substr($pluginFile, - strlen($file));

        if ($file == $compareFile) {
            $page = ($this->settings['mainMenu']) ? 'admin.php' : 'options-general.php';

            $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/'
                            . $page . '?page=' . WPML::get('adminPage') . '">'
                            . WPML::__('Settings') . '</a>';

            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * WP action callback
     * @return void
     */
    public function actionAdminNotices()
    {
        if ( ! WPML::get('isCompatible')) {
            $plugin_title = get_admin_page_title();

            echo '<div class="error">';
            echo sprintf(WPML::__('<p>Error - The plugin <strong>%1$s</strong> requires PHP %2$s + and WP %3$s +.'
                    . '  Please upgrade your PHP and/or WordPress.'
                    . '<br/>Disable the plugin to remove this message.</p>'), $plugin_title, WPML::get('minPhpVersion'), WPML::get('minWpVersion'));
            echo '</div>';
        }

        if (isset($_GET['page']) && $_GET['page'] === WPML::get('adminPage') && is_plugin_active('email-encoder-bundle/email-encoder-bundle.php')) {
            WPML_View::factory('/admin/notices.php')->show();
        }
    }

    /**
     * Check if widget logic filter is active
     * @return boolean
     */
    public static function hasWidgetLogicFilter()
    {
        $wlOptions = get_option('widget_logic');

        if (!is_array($wlOptions) || !key_exists('widget_logic-options-filter', $wlOptions)) {
            return false;
        }

        return ($wlOptions['widget_logic-options-filter'] === 'checked');
    }

} // End Class WPML_Admin
