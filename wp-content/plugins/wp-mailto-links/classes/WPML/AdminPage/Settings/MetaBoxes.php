<?php
/**
 * Class WPML_AdminPage_Settings_MetaBoxes
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
final class WPML_AdminPage_Settings_MetaBoxes extends WPRun_BaseAbstract_0x5x0
{

    /**
     * @var MyDevLib_OptionAbstract
     */
    private $option = null;

    /**
     * @param MyDevLib_OptionAbstract $option
     */
    protected function init($option)
    {
        $this->option = $option;
    }

    /**
     * Add meta boxes
     */
    public function addMetaBoxes()
    {
        $defaults = array(
            'id'        => '',
            'title'     => '',
            'callback'  => $this->getCallback('showMetaBox'),
            'screen'    => null,
            'context'   => 'advanced',
            'priority'  => 'default',
        );

        $this->addMetaBox(wp_parse_args(array(
            'id'        => 'mail-icon',
            'title'     => __('Mail Icon', 'wp-mailto-links'),
            'context'   => 'normal',
        ), $defaults));

        $this->addMetaBox(wp_parse_args(array(
            'id'        => 'admin',
            'title'     => __('Admin', 'wp-mailto-links'),
            'context'   => 'normal',
        ), $defaults));

        $this->addMetaBox(wp_parse_args(array(
            'id'        => 'additional-classes',
            'title'     => __('Additional Classes', 'wp-mailto-links'),
            'context'   => 'side',
        ), $defaults));

        $this->addMetaBox(wp_parse_args(array(
            'id'        => 'support',
            'title'     => __('Support', 'wp-mailto-links'),
            'context'   => 'side',
        ), $defaults));
    }

    /**
     * @param array $args
     */
    protected function addMetaBox(array $args)
    {
        add_meta_box(
            $args['id']
            , $args['title']
            , $args['callback']
            , $args['screen']
            , $args['context']
            , $args['priority']
        );
    }

    /**
     * @param WP_Post $post
     * @param array   $args
     */
    protected function showMetaBox($post, array $args)
    {
        $key = $args['id'];
        $templateFile = WP_MAILTO_LINKS_DIR . '/templates/admin-pages/settings/meta-boxes/' . $key . '.php';

        $this->showTemplate($templateFile, array(
            'option'        => $this->option,
            'fieldsView'    => new MyDevLib_FormHelper_0x5x0($this->option->getOptionName(), $this->option->getValues()),
        ));
    }

}

/*?>*/
