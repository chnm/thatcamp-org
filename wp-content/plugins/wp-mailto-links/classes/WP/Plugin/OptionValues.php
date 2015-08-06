<?php
/**
 * Class WP_Plugin_OptionValues
 *
 * Managing options
 *
 * @package  WP_Plugin
 * @category WordPress Plugins
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  MIT license
 */
class WP_Plugin_OptionValues
{

    /**
     * @var array
     */
    protected $values = array();

    /**
     * @var array
     */
    protected $settings = array(
        'file' => null,
        'optionGroup' => null,
        'optionName' => null,
        'uninstall' => true,
    );

    /**
     * @param array $settings  Optional
     * @param array $defaultValues  Optional
     */
    public function __construct(array $settings = null, array $defaultValues = null)
    {
        // set given setting values
        if ($settings !== null) {
            foreach ($settings as $key => $value) {
                if (key_exists($key, $this->settings)) {
                    $this->settings[$key] = $value;
                }
            }
        }

        // get saved option values
        $savedValues = get_option($this->settings['optionName']);

        // set values
        $this->values = $defaultValues;

        if ($savedValues) {
            foreach ($savedValues as $key => $value) {
                $this->values[$key] = $value;
            }
        }

        // set uninstall hook
        if ($this->settings['uninstall']) {
            register_uninstall_hook($this->settings['file'], array('WP_Plugin_OptionValues', 'uninstall'));
        }

        // add actions
        add_action('admin_init', array($this, 'actionAdminInit'), 1);
    }

    /**
     * Get value
     * @param string|null $key
     * @return mixed|null
     */
    public function get($key = null)
    {
        if ($key === null) {
            return $this->values;
        }

        if (key_exists($key, $this->values)) {
            return $this->values[$key];
        }

        return null;
    }

    /**
     * Change value
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        if (key_exists($key, $this->values)) {
            $this->values[$key] = $value;
        }
    }

    /**
     * Save values
     */
    public function save()
    {
        update_option($this->settings['optionName'], $this->values);
    }

    /**
     * WP action callback
     */
    public function actionAdminInit()
    {
        register_setting($this->settings['optionGroup'], $this->settings['optionName']);
    }

    /**
     * WP hook callback
     */
    public static function uninstall()
    {
        delete_option($this->settings['optionName']);
        unregister_setting($this->settings['optionGroup'], $this->settings['optionName']);
    }

} // End Class WP_Plugin_OptionValues
