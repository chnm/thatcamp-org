<?php
/**
 * Class WPDev_Admin_Page_MetaBox
 *
 * Creating admin options page with metaboxes
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 */
class WPDev_Admin_Page_MetaBox extends WPDev_Admin_Page_Abstract
{

    /**
     * Additional Settings
     * @var array
     */
    protected $additionalSettings = array(
        'defaultColumns' => 2,
        'maxColumns'     => 2,
        'bodyTemplate'   => 'body-content.php',
    );

    /**
     * Constructor
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        // add additional settings
        $completeSettings = array_merge($this->additionalSettings, $settings);

        parent::__construct($completeSettings);

        add_action('admin_menu', array($this, 'createPage'));
    }

    /**
     * Create page with menu item
     */
    public function createPage()
    {
        parent::createPage();

        // load plugin page
        add_action('load-' . $this->hook, array($this, 'loadPage'));
    }

    /**
     * Load page
     */
    public function loadPage()
    {
        // set dashboard postbox
        wp_enqueue_script('dashboard');

        // screen settings
        if (function_exists('add_screen_option')) {
            add_screen_option('layout_columns', array(
                'max'       => $this->settings['maxColumns'],
                'default'   => $this->settings['defaultColumns']
            ));
        }
    }

    /**
     * Show complete admin page
     */
    public function showPage()
    {
        echo $this->renderTemplate(array(
            'id'            => $this->settings['id'],
            'bodyTemplate'  => $this->settings['bodyTemplate'],
            'columnCount'   => (1 == get_current_screen()->get_columns()) ? 1 : 2,
        ));
    }

}

/*?>*/
