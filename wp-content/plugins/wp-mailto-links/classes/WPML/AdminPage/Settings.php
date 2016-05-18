<?php
/**
 * Class WPML_AdminPage_Settings
 *
 * @package  WPML
 * @category WordPress Plugins
 * @version  2.1.2
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WP-Mailto-Links
 * @link     https://wordpress.org/plugins/wp-mailto-links/
 * @license  GPLv2+ license
 */
final class WPML_AdminPage_Settings extends WPRun_BaseAbstract_0x5x0
{

    /**
     * @var MyDevLib_OptionAbstract
     */
    private $option = null;

    /**
     * @var string
     */
    private $pageId = 'wp-mailto-links-option-page';

    /**
     * @var integer
     */
    private $maxColumns = 2;

    /**
     * @var integer
     */
    private $defaultColumns = 2;

    /**
     * @var WPML_AdminPage_Settings_HelpTabs
     */
    private $helpTabs = null;

    /**
     * @var WPML_AdminPage_Settings_MetaBoxes
     */
    private $metaBoxes = null;
    
    /**
     * Initialize
     * @param MyDevLib_OptionAbstract $option
     */
    protected function init($option)
    {
        $this->option = $option;

        // create meta boxes
        $this->metaBoxes = WPML_AdminPage_Settings_MetaBoxes::create($this->option);

        // create help tabs
        $this->helpTabs = WPML_AdminPage_Settings_HelpTabs::create();
    }

    /**
     * Action for "admin_menu"
     */
    protected function actionAdminMenu()
    {
        if ($this->option->getValue('own_admin_menu')) {
            $this->pageHook = add_menu_page(
                __('WP Mailto Links', 'wp-mailto-links')    // page title
                , __('Mailto Links', 'wp-mailto-links')     // menu title
                , 'manage_options'                          // capability
                , $this->pageId                             // id
                , $this->getCallback('showAdminPage')       // callback
                , 'dashicons-email'                         // icon
            );
        } else {
            $this->pageHook = add_submenu_page(
                'options-general.php'                       // parent slug
                , __('WP Mailto Links', 'wp-mailto-links')  // page title
                , __('Mailto Links', 'wp-mailto-links')     // menu title
                , 'manage_options'                          // capability
                , $this->pageId                             // id
                , $this->getCallback('showAdminPage')       // callback
            );
        }

        // load page
        add_action('load-' . $this->pageHook, $this->getCallback('loadPage'));
    }

    /**
     * Action of "load-{page}"
     */
    protected function loadPage()
    {
        // set dashboard postbox
        wp_enqueue_script('dashboard');

        // screen settings
        add_screen_option('layout_columns', array(
            'max'       => $this->maxColumns,
            'default'   => $this->defaultColumns,
        ));

        // add meta boxes
        $this->metaBoxes->addMetaBoxes();

        // add help tabs
        $this->helpTabs->addHelpTabs();
    }

    /**
     * Callback show Admin Page
     */
    protected function showAdminPage()
    {
        $currentScreen = get_current_screen();

        // also show updated message for pages outsite the "Settings" menu
        if (isset($_GET['settings-updated']) && 'options-general' !== $currentScreen->parent_base) {
            $showUpdatedMessage = true;
        } else {
            $showUpdatedMessage = false;
        }

        $columnCount = (1 == $currentScreen->get_columns()) ? 1 : 2;

        $bodyContent = $this->renderTemplate(WP_MAILTO_LINKS_DIR . '/templates/admin-pages/settings/body-content.php', array(
            'fieldsView'      => new MyDevLib_FormHelper_0x5x0($this->option->getOptionName(), $this->option->getValues()),
        ));

        $this->showTemplate(WP_MAILTO_LINKS_DIR . '/templates/admin-pages/settings/page.php', array(
            'showUpdatedMessage' => $showUpdatedMessage,
            'id'                => $this->pageId,
            'columnCount'       => $columnCount,
            'option'            => $this->option,
            'bodyContent'       => $bodyContent,
        ));
    }

    protected function actionAdminEnqueueScripts()
    {
        wp_enqueue_script(
            'wp-mailto-links-admin'
            , plugins_url('/public/js/wp-mailto-links-admin.js', WP_MAILTO_LINKS_FILE)
            , array('jquery')
            , false
            , true
        );
        wp_localize_script('wp-mailto-links-admin', 'wpmlSettings', array(
            'pluginUrl' => plugins_url('', WP_MAILTO_LINKS_FILE),
            'dashiconsValue' => $this->option->getValue('dashicons'),
            'fontawesomeValue' => $this->option->getValue('fontawesome'),
        ));

        wp_enqueue_style(
            'font-awesome'
            , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
            , array()
            , null
        );
    }

}

/*?>*/
