<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class BWSrecaptcha
 */
require_once( ABSPATH . 'wp-content/plugins/ninja-forms/includes/Abstracts/Field.php' );
class BWSrecaptcha extends NF_Abstracts_Field
{
    protected $_name = 'bwsrecaptcha';

    protected $_type = 'bwsrecaptcha';

    protected $_section = 'common';

    protected $_icon = 'filter';

    protected $_templates = 'bwsrecaptcha';

    protected $_test_value = '';

    protected $_settings = array('label', 'classes');

    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('BWS reCaptcha', 'google-captcha-pro');
        $this->_settings = $this->load_settings(
            array( 'label', 'label_pos' )
        );
    }

    public function localize_settings( $settings, $form ) {
        global  $gglcptch_options;
        $settings['recaptcha_version'] = $gglcptch_options['recaptcha_version'];
        $settings['public_key'] = $gglcptch_options['public_key'];
        $settings['content'] = gglcptch_display();
        return $settings;
    }
    /* Check reCaptcha in Ninja Forms */
    public function validate( $field, $data ) {

        if ( empty( $field['value'] ) && ! gglcptch_whitelisted_ip() ) {
            return __( 'User response is missing.', 'google-captcha-pro' );
        }
        $_POST["g-recaptcha-response"] = $field['value'];
        $gglcptch_check = gglcptch_check( 'ninja_form' );
        if ( ! $gglcptch_check['response'] ) {
            return $gglcptch_check['errors']->get_error_messages() ;
            //add_filter( 'gglcptch_recaptcha_content', 'gglcptch_error_message', 10, 1 );
        }
    }

}
/* Include fields-bwsrecaptcha.html template */
add_filter( 'ninja_forms_field_template_file_paths', 'my_custom_file_path' );
function my_custom_file_path( $paths )
{
    $paths[] =  dirname( __FILE__ ) . '/' ;

    return $paths;
}
/* Add reCaptcha in Ninja Forms Builder */
add_filter('ninja_forms_register_fields', function($fields)
{
    $is_user_logged_in = is_user_logged_in();
    if ( gglcptch_is_recaptcha_required( 'ninja_form', $is_user_logged_in ) ){

        $fields['bwsrecaptcha'] = new BWSrecaptcha;
    }

    return $fields;
});