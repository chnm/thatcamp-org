<?php
/**
 * Class WPML_AdminPage_Settings_HelpTabs
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
final class WPML_AdminPage_Settings_HelpTabs extends WPRun_BaseAbstract_0x5x0
{

    /**
     * Add help tabs
     */
    public function addHelpTabs()
    {
        $screen = get_current_screen();

        $defaults = array(
            'content'   => '',
            'callback'  => $this->getCallback('showHelpTab'),
        );

        $screen->add_help_tab(wp_parse_args(array(
            'id'        => 'general',
            'title'     => __('General', 'wp-mailto-links'),
        ), $defaults));

        $screen->add_help_tab(wp_parse_args(array(
            'id'        => 'shortcodes',
            'title'     => __('Shortcode', 'wp-mailto-links'),
        ), $defaults));

        $screen->add_help_tab(wp_parse_args(array(
            'id'        => 'template-tags',
            'title'     => __('Template Tags', 'wp-mailto-links'),
        ), $defaults));

        $screen->add_help_tab(wp_parse_args(array(
            'id'        => 'filter-hook',
            'title'     => __('Filter Hook', 'wp-mailto-links'),
        ), $defaults));
    }

    /**
     * @param WP_Screen $screen
     * @param array     $args
     */
    protected function showHelpTab($screen, array $args)
    {
        $key = $args['id'];
        $templateFile = WP_MAILTO_LINKS_DIR . '/templates/admin-pages/settings/help-tabs/' . $key . '.php';

        $this->showTemplate($templateFile);
    }

}

/*?>*/
