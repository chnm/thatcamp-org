<?php
/**
 * Class WPML_AdminPage_Settings_MetaBoxes
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
        $this->addMetaBox('mail-icon', __('Mail Icon', 'wp-mailto-links'), 'normal');
        $this->addMetaBox('admin', __('Admin', 'wp-mailto-links'), 'normal');

        // side position
        $this->addMetaBox('additional-classes', __('Additional Classes', 'wp-mailto-links'), 'side');
        $this->addMetaBox('support', __('Support', 'wp-mailto-links'), 'side');
    }

    /**
     * @param string $id
     * @param string $title
     * @param string $context
     */
    protected function addMetaBox($id, $title, $context = 'advanced')
    {
        add_meta_box(
            $id                                 // id
            , $title                            // title
            , $this->getCallback('showMetaBox') // callback
            , null                              // screen
            , $context                          // context: 'advanced', 'normal', 'side'
            , 'default'                         // priority
        );
    }

    /**
     * @param WP_Post $post
     * @param array   $args
     */
    public function showMetaBox($post, array $args)
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
