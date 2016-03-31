<?php
/**
 * Class WPDev_Admin_Page_Abstract
 *
 * Creating a normal admin options page
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 */
class WPDev_Admin_Page_Abstract implements WPDev_Admin_Page_Interface
{

    /**
     * Settings
     * @var array
     */
    protected $settings = array(
        'id'            => '',  // = menuSlug
        'title'         => '',  // = page title
        'menuTitle'     => '',
        'function'      => null,
        'iconUrl'       => '',
        'position'      => null,
        'parentSlug'    => 'options-general.php',
        'pageTemplate'  => '',
        'templateVars'  => array(),
    );

    /**
     * Page hook
     * @var string
     */
    protected $hook = null;

    /**
     * Constructor
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);

        // default menu title
        if (empty($this->settings['menuTitle'])) {
            $this->settings['menuTitle'] = $this->settings['title'];
        }

        // default callback function
        if (empty($this->settings['function'])) {
            $this->settings['function'] = array($this, 'showPage');
        }

        add_action('admin_menu', array($this, 'createPage'));
    }

    /**
     * Get the page hook
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * Create page with menu item
     */
    public function createPage()
    {
        if (empty($this->settings['parentSlug'])) {
        // create main menu
            $this->hook = add_menu_page(
                $this->settings['title']
                , $this->settings['menuTitle']
                , 'manage_options'
                , $this->settings['id']
                , $this->settings['function']
                , $this->settings['iconUrl']
            );
        } else {
        // create submenu
            $this->hook = add_submenu_page(
                $this->settings['parentSlug']
                , $this->settings['title']
                , $this->settings['menuTitle']
                , 'manage_options'
                , $this->settings['id']
                , $this->settings['function']
            );
        }
    }

    /**
     * Show page
     */
    public function showPage()
    {
        echo $this->renderTemplate(array(
            'id' => $this->settings['id'],
        ));
    }

    /**
     * Show page template
     * @param array $vars  Optional
     */
    protected function renderTemplate($templateVars = array())
    {
        $vars = array_merge($this->settings['templateVars'], $templateVars);
        $view = WPDev_View::create($this->settings['pageTemplate'], $vars);

        if (!$view->exists()) {
            return false;
        }

        return $view->render();
    }

}

/*?>*/
