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
final class WPML_Admin extends WPDev_Admin_Page_MetaBox
{

    /**
     * @var WPDev_Admin_Page_Interface
     */
    protected $adminPage = null;

    /**
     * Initialize, add action and filter hooks
     */
    public function __construct()
    {
        add_action('init', array($this, 'createAdminPage'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    /**
     * Add scripts
     */
    public function enqueueScripts($hook)
    {
        if ($hook !== $this->adminPage->getHook()) {
            return;
        }

        wp_enqueue_script(
            'wp-mailto-links-admin'
            , WPML::glob('URL') . '/js/wp-mailto-links-admin.js'
            , array('jquery')
            , false
            , true
        );
        wp_localize_script('wp-mailto-links-admin', 'wpmlSettings', array(
            'pluginUrl' => WPML::glob('URL'),
            'dashiconsValue' => WPML::glob('option')->getValue('dashicons'),
            'fontawesomeValue' => WPML::glob('option')->getValue('fontawesome'),
        ));

        wp_enqueue_style(
            'font-awesome'
            , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
            , array()
            , null
        );
    }

    /**
     * Create admin pages
     */
    public function createAdminPage()
    {
        $templatesBasePath = WPML::glob('DIR') . '/templates/admin/page';
        $globals = WPML::plugin()->getAllGlobals();
        $mainMenu = (bool) WPML::glob('option')->getValue('own_admin_menu');

        // create page
        $this->adminPage = new WPDev_Admin_Page_MetaBox(array(
            'id'              => $globals['key'] . '-option-page',
            'title'           => __('WP Mailto Links', 'wp-mailto-links'),
            'menuTitle'       => __('Mailto Links', 'wp-mailto-links'),
            'parentSlug'      => $mainMenu ? null : 'options-general.php',
            'iconUrl'         => 'dashicons-email',
            'defaultColumns'  => 2,
            'maxColumns'      => 2,
            'pageTemplate'    => $templatesBasePath . '/page.php',
            'templateVars'    => $globals,
        ));

        // create meta boxes
        new WPDev_Admin_MetaBoxes(
            array(
                'mail-icon' => array(
                    'title' => __('Mail Icon', 'wp-mailto-links'),
                    'context' => 'normal',
                 ),
                'additional-classes' => array(
                    'title' => __('Additional Classes', 'wp-mailto-links'),
                    'context' => 'side',
                 ),
                'admin' => array(
                    'title' => __('Admin', 'wp-mailto-links'),
                    'context' => 'normal',
                 ),
                'support' => array(
                    'title' => __('Support', 'wp-mailto-links'),
                    'context' => 'side',
                 ),
            )
            , array(
                'adminPage'     => $this->adminPage,
                'templatesPath' => $templatesBasePath . '/meta-boxes',
                'templateVars'  => $globals,
            )
        );

        // create help tabs
        new WPDev_Admin_HelpTabs(
            array(
                'general' => array(
                    'title' => __('General', 'wp-mailto-links'),
                 ),
                'shortcodes' => array(
                    'title' => __('Shortcode', 'wp-mailto-links'),
                 ),
                'template-tags' => array(
                    'title' => __('Template Tags', 'wp-mailto-links'),
                 ),
                'filter-hook' => array(
                    'title' => __('Filter Hook', 'wp-mailto-links'),
                 ),
                'action-hook' => array(
                    'title' => __('Action Hook', 'wp-mailto-links'),
                 ),
            )
            , array(
                'adminPage'     => $this->adminPage,
                'templatesPath' => $templatesBasePath . '/help-tabs',
                'templateVars'  => $globals,
            )
        );
    }

}

/*?>*/
