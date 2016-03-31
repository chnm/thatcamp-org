<?php
/**
 * Class WPDev_Option
 *
 * Managing WP option database entry containing multiple values
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 */
class WPDev_Option
{

    /**
     * @var string
     */
    protected $optionName = null;

    /**
     * @var string
     */
    protected $optionGroup = null;

    /**
     * @var array
     */
    protected $values = array();

    /**
     * @param string  $optionName
     * @param array   $defaultValues    Optional
     * @param string  $optionGroup      Optional, default will be same as optionName
     * @param string  $registerHook     Optional, default 'admin_init'
     */
    public function __construct($optionName, array $defaultValues = array(), $optionGroup = null, $registerHook = 'admin_init')
    {
        $this->optionName = $optionName;

        if (is_string($optionGroup)) {
            $this->optionGroup = $optionGroup;
        } else {
            $this->optionGroup = $optionName;
        }

        // first set all defaults
        $this->values = $defaultValues;

        $this->setValuesFromDatabase();

        // add register to action hook
        if (is_string($registerHook)) {
            add_action($registerHook, array($this, 'register'), 1);
        }
    }

    /**
     * Set saved option values
     */
    protected function setValuesFromDatabase()
    {
        // get saved option values
        $savedValues = get_option($this->optionName);

        // overwrite defaults with saved values (if exists)
        if ($savedValues) {
            $this->values = array_merge($this->values, $savedValues);
        }
    }

    /**
     * Get value
     * @param string $key
     * @return mixed|null
     * @throw Exception
     */
    public function getValue($key)
    {
        if (!key_exists($key, $this->values)) {
            throw new Exception('Key "' . $key . '" does not exist.');
        }

        return $this->values[$key];
    }

    /**
     * Set value
     * @param string  $key
     * @param mixed   $value
     * @param boolean $addKeyWhenNonExists  Optional, default true
     * @throw Exception
     */
    public function setValue($key, $value, $addKeyWhenNonExists = true)
    {
        if ($addKeyWhenNonExists === true || key_exists($key, $this->values)) {
            $this->values[$key] = $value;
        }
    }

    /**
     * Add new value, does nothing when key already exists
     * @param string  $key
     * @param mixed   $value
     */
    public function addValue($key, $value)
    {
        if (!key_exists($key, $this->values)) {
            $this->values[$key] = $value;
        }
    }

    /**
     * Get all values
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Update values
     */
    public function update()
    {
        return update_option($this->optionName, $this->values);
    }

    /**
     * Register setting
     * Could be called on the "admin_init" action hook
     */
    public function register()
    {
        register_setting($this->optionGroup, $this->optionName);
    }

    /**
     * Unregister setting
     * Could be used on the "register_deactivation_hook"
     */
    public function unregister()
    {
        unregister_setting($this->optionGroup, $this->optionName);
    }

    /**
     * Delete from database
     * Could be used on the "register_uninstall_hook"
     * or for testing use "register_deactivation_hook"
     */
    public function delete()
    {
        delete_option($this->optionName);
    }

    /**
     * Set hidden form fields, like nonce and action
     */
    public function settingsFields()
    {
        settings_fields($this->optionGroup);
    }

    /**
     * Get form field name
     * @param string $key
     * @return string
     */
    public function getFieldName($key)
    {
        return $this->optionName . '[' . $key . ']';
    }

}

/*?>*/
