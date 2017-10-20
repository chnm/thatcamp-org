<?php
/**
 * Class WPML_Option_Settings
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
final class WPML_Option_Settings extends MyDevLib_OptionAbstract_0x5x0
{

    /**
     * @var string
     */
    protected $optionGroup = 'wp-mailto-links';

    /**
     * Recommended optionGroup and optionName have same name
     * @var string
     */
    protected $optionName = 'wp-mailto-links';

    /**
     * @var array
     */
    protected $defaultValues = array(
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
        'mail_icon'         => '',
        'image'              => 1,
        'dashicons'         => '',
        'fontawesome'       => '', 
        'show_icon_before'  => 0, 
        'image_no_icon'     => 0,
        'no_icon_class'     => 'no-mail-icon',
        'class_name'        => 'mail-link',
        'security_check'    => 0,
        'own_admin_menu'    => 1,
    );

}

/*?>*/
