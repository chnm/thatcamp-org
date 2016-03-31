<?php
/**
 * Class WPDev_Admin_HelpTabs
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
class WPDev_Admin_HelpTabs
{

    /**
     * Settings
     * @var array
     */
    protected $settings = array(
        // adminPage should be a pageHook name or an instanceof WPDev_Admin_Page_Interface
        // when true helptabs will be shown on all admin pages
        // else will never be shown
        'adminPage'       => null,
        'templatesPath'   => '',
        'templateVars'    => array(),
        'templateFileExt' => '.php',
        'sidebarKey'      => 'sidebar',
    );

    /**
     * All helpTabs
     * @example
     *      array(
     *          'main' => array(
     *              'title' => 'Uitleg',
     *           ),
     *      );
     * @var array
     */
    protected $helpTabs = array();

    /**
     * Constructor
     * @param array $helpTabs
     * @param array $settings
     */
    public function __construct(array $helpTabs, array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
        $this->helpTabs = $helpTabs;

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
            add_action($actionName, array($this, 'addHelptabs'));
        }
    }

    /**
     * Add helpTabs
     * @return void
     */
    public function addHelptabs()
    {
        $screen = get_current_screen();

        // help sidebar
        $sidebarContent = $this->renderTemplate($this->settings['sidebarKey']);

        if ($sidebarContent) {
            $screen->set_help_sidebar($sidebarContent);
        }

        // helpTabs
        foreach ($this->helpTabs as $key => $params) {
            // help tab
            $helpContent = $this->renderTemplate($key);

            $screen->add_help_tab(array(
                'id' => $key,
                'title' => $params['title'],
                'content' => $helpContent,
            ));
        }
    }

    /**
     * Render a template
     * @param string $file
     * @return string
     */
    protected function renderTemplate($key) {
        $templateFile = $this->settings['templatesPath'] . '/' . $key . $this->settings['templateFileExt'];

        $view = WPDev_View::create($templateFile, $this->settings['templateVars']);

        if (!$view->exists()) {
            return false;
        }

        return $view->render();
    }

}

/*?>*/
