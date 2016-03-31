<?php
/**
 * Class WPML
 *
 * @package  WPML
 * @category WordPress Plugins
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  MIT license
 */
final class WPML extends WPDev_Plugin
{

    /**
     * This property should also be included in child classes to prevent conflicts
     * @var \WPML
     */
    protected static $instance = null;

    /**
     * Init
     */
    protected function init()
    {
        // create option and make it global
        $option = new WPDev_Option($this->getGlobal('key'), array(
            'protect'           => 1,
            'convert_emails'    => 1,
            'filter_body'       => 1,
            'filter_posts'      => 1,
            'filter_comments'   => 1,
            'filter_widgets'    => 1,
            'filter_rss'        => 1,
            'filter_head'       => 1,
            'input_strong_protection' => 0,
            'protection_text'   => '*protected email*',
            'mail_icon'         => '',  // type
            'image'              => 1,  // new
            'dashicons'         => '',  // new
            'fontawesome'       => '',  // new
            'show_icon_before'  => 0,   // new
            'image_no_icon'     => 0,
            'no_icon_class'     => 'no-mail-icon',
            'class_name'        => 'mail-link',
            'security_check'    => 0,
            'own_admin_menu'    => 1,
        ));

        $this->setGlobal('option', $option);

        // activation also after upgrade
        register_activation_hook($this->getGlobal('FILE'), array(__CLASS__, 'upgrade'));

        // delete option from DB on uninstall
        register_uninstall_hook($this->getGlobal('FILE'), array(__CLASS__, 'uninstall'));

        // load admin or front site
        if (is_admin()) {
            new WPML_Admin();
        } else {
            new WPML_FrontSite();
        }
    }

    /**
     * Upgrade procedure
     * Convert old to new option values
     */
    public static function upgrade()
    {
        $option = self::glob('option');

        $defaultOldValues = array(
            'version' => null,
            'convert_emails' => 1,
            'protect' => 1,
            'filter_body' => 1,
            'filter_posts' => 1,
            'filter_comments' => 1,
            'filter_widgets' => 1,
            'filter_rss' => 1,
            'filter_head' => 1,
            'input_strong_protection' => 0,
            'protection_text' => '*protected email*',
            'icon' => 0,
            'image_no_icon' => 0,
            'no_icon_class' => 'no-mail-icon',
            'class_name' => 'mail-link',
            'widget_logic_filter' => 0,
            'own_admin_menu' => 0,
        );

        // get old option name "WP_Mailto_Links_options"
        $oldOption = new WPDev_Option('WP_Mailto_Links_options', $defaultOldValues);
        $oldValues = $oldOption->getValues();

        if (!empty($oldValues)) {
            foreach ($oldValues as $key => $oldValue) {
                // take old value
                if ($key === 'icon') {
                    // old 'icon' contained the image number
                    // new 'mail_icon' contains type (image, dashicons, fontawesome)
                    $newValue = empty($oldValue) ? '' : 'image';
                    $option->setValue('mail_icon', $newValue, false);

                    // mail_icon === 'image' ---> 'image' contains number
                    if (!empty($oldValue)) {
                        $option->setValue('image', $oldValue, false);
                    }
                } else {
                    $option->setValue($key, $oldValue, false);
                }
            }

            $option->update();
            $oldOption->delete();
        }
    }

    /**
     * Uninstall plugin
     */
    public static function uninstall()
    {
        // remove option values
        $this->getGlobal('option')->delete();
    }

}

/*?>*/
