<?php
/**
 * Class WPML_AdminPage_Settings
 *
 * @package  WPML
 * @category WordPress Plugins
 * @version  2.1.6
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WP-Mailto-Links
 * @link     https://wordpress.org/plugins/wp-mailto-links/
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPML_AdminPage_Settings extends WPRun_BaseAbstract_0x5x0
{

    /**
     * @var string
     */
    private $pageId = 'wp-mailto-links-option-page';

    /**
     * @var string
     */
    private $pageHook = null;

    /**
     * @var integer
     */
    private $maxColumns = 2;

    /**
     * @var integer
     */
    private $defaultColumns = 2;

    /**
     * @var MyDevLib_OptionAbstract_0x5x0
     */
    private $option = null;

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
     * @param MyDevLib_OptionAbstract_0x5x0     $option
     * @param WPML_AdminPage_Settings_MetaBoxes $metaBoxes
     * @param WPML_AdminPage_Settings_HelpTabs  $helpTabs
     */
    protected function init(MyDevLib_OptionAbstract_0x5x0 $option
                                , WPML_AdminPage_Settings_MetaBoxes $metaBoxes
                                , WPML_AdminPage_Settings_HelpTabs $helpTabs)
    {
        $this->option = $option;
        $this->metaBoxes = $metaBoxes;
        $this->helpTabs = $helpTabs;
    }

    /**
     * Action for "admin_menu"
     */
    protected function actionAdminMenu()
    {
        if ($this->option->getValue('own_admin_menu')) {
            // add main menu
            $this->pageHook = add_menu_page(
                __('WP Mailto Links', 'wp-mailto-links')    // page title
                , __('Mailto Links', 'wp-mailto-links')     // menu title
                , 'manage_options'                          // capability
                , $this->pageId                             // id
                , $this->getCallback('showAdminPage')       // callback
                , 'dashicons-email'                         // icon
            );
        } else {
            // add submenu under settings-menu
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

        // get page body
        $bodyContent = $this->renderTemplate(WP_MAILTO_LINKS_DIR . '/templates/admin-pages/settings/body-content.php', array(
            'fieldsView'      => new MyDevLib_FormHelper_0x5x0($this->option->getOptionName(), $this->option->getValues()),
        ));

        // show admin page
        $this->showTemplate(WP_MAILTO_LINKS_DIR . '/templates/admin-pages/settings/page.php', array(
            'showUpdatedMessage' => $showUpdatedMessage,
            'id'                => $this->pageId,
            'columnCount'       => $columnCount,
            'option'            => $this->option,
            'bodyContent'       => $bodyContent,
        ));
    }

    /**
     * Action for "admin_enqueue_scripts"
     * @return void
     */
    protected function actionAdminEnqueueScripts()
    {
        if (get_current_screen()->id !== $this->pageHook) {
            return;
        }

        // set dashboard postbox
        wp_enqueue_script('dashboard');

        // set mailto script
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

        // set style
        wp_enqueue_style(
            'font-awesome'
            , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
            , array()
            , null
        );
    }

}

/*?>*/
