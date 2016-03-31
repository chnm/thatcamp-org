<?php
/**
 * Class WPDev_Admin_MetaBoxes
 *
 * Creating an admin options page with a menu item
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 */
class WPDev_Admin_MetaBoxes
{

    /**
     * Settings
     * @var array
     */
    protected $settings = array(
        // adminPage should be a pageHook name or an instanceof WPDev_Admin_Page_Interface
        // when true metaboxes will be shown on all admin pages
        // else will never be shown
        'adminPage'        => null,
        'templatesPath'    => '',
        'templateFileExt'  => '.php',
        'templateVars'     => array(),
    );

    /**
     * All metaBoxes
     * @example
     *      array(
     *          'general' => array(
     *             'title'    => __('General Settings'),
     *             'context'  => 'advanced',  // Optional 'normal', 'advanced', or 'side'
     *             'screen'   => null,        // Optional 'post', 'page', 'dashboard', 'link', 'attachment', 'custom_post_type', 'comment'
     *             'priority' => 'default',   // Optional 'high', 'core', 'default' or 'low'
     *          ),
     *      );
     * @var array
     */
    protected $metaBoxes = array();

    /**
     * Constructor
     * @param array $settings
     * @param array $metaBoxes Optional
     * @param array $helptabs  Optional
     */
    public function __construct(array $metaBoxes, array $settings = array())
    {
        $this->settings = array_merge($this->settings, $settings);
        $this->metaBoxes = $metaBoxes;

        add_action('admin_menu', array($this, 'init'));
    }

    /**
     * Set correct action hook
     */
    public function init()
    {
        $adminPage = $this->settings['adminPage'];

        if ($adminPage === true) {
            $actionName = 'admin_head';
        } elseif ($adminPage instanceof WPDev_Admin_Page_Interface) {
            $actionName = 'load-' . $adminPage->getHook();
        } elseif (is_string($adminPage)) {
            $actionName = 'load-' . $adminPage;
        }

        if (isset($actionName)) {
            add_action($actionName, array($this, 'addMetaBoxes'));
        }
    }

    /**
     * Add metaBoxes
     */
    public function addMetaBoxes()
    {
        $defaultMetaBoxSettings = array(
            'title'    => '',
            'callback' => array($this, 'showMetaBox'),
            'context'  => 'advanced',
            'screen'   => null,
            'priority' => 'default',
        );

        foreach ($this->metaBoxes as $id => $params) {
            // combine with defaults
            $settings = array_merge($defaultMetaBoxSettings, $params);

            add_meta_box(
                $id
                , $settings['title']
                , $settings['callback']
                , $settings['screen']
                , $settings['context']
                , $settings['priority']
                , array($id)
            );
        }
    }

    /**
     * Show the content of a metabox
     * @param WP_Post $post
     * @param array $box
     */
    public function showMetaBox($post, $box)
    {
        $id = $box['args'][0];

        $templateFile = $this->settings['templatesPath'] . '/' . $id . $this->settings['templateFileExt'];
        $view = WPDev_View::create($templateFile, $this->settings['templateVars']);

        if ($view->exists()) {
            echo $view->render();
        }
    }

}

/*?>*/
