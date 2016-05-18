<?php
/**
 * Class WPML_AdminPage_Settings_HelpTabs
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
final class WPML_AdminPage_Settings_HelpTabs extends WPRun_BaseAbstract_0x5x0
{

    /**
     * Add help tabs
     */
    public function addHelpTabs()
    {
        $this->addHelpTab('general', __('General', 'wp-mailto-links'));
        $this->addHelpTab('shortcodes', __('Shortcode', 'wp-mailto-links'));
        $this->addHelpTab('template-tags', __('Template Tags', 'wp-mailto-links'));
        $this->addHelpTab('filter-hook', __('Filter Hook', 'wp-mailto-links'));
    }

    /**
     * @param string $id
     * @param string $title
     * @param string $content
     */
    protected function addHelpTab($id, $title, $content = '')
    {
        $screen = get_current_screen();

        $screen->add_help_tab(array(
            'id'        => $id,
            'title'     => $title,
            'content'   => $content,
            'callback'  => $this->getCallback('showHelpTab'),
        ));
    }

    /**
     * @param WP_Screen $screen
     * @param array     $args
     */
    protected function showHelpTab($screen, array $args)
    {
        $key = $args['id'];
        $templateFile = WP_MAILTO_LINKS_DIR . '/templates/admin-pages/settings/help-tabs/' . $key . '.php';

        $this->showTemplate($templateFile, array(
            'file' => WP_MAILTO_LINKS_FILE,
        ));
    }

}

/*?>*/
