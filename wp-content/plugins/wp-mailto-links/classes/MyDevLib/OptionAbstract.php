<?php
/**
 * Class MyDevLib_OptionAbstract_0x5x0
 *
 * @package  Demo WPRun
 * @category WordPress Plugin
 * @version  0.5.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class MyDevLib_OptionAbstract_0x5x0 extends WPRun_BaseAbstract_0x5x0
{

    /**
     * @var string
     */
    protected $optionGroup = null;

    /**
     * Recommended optionGroup and optionName have same name
     * @var string
     */
    protected $optionName = null;

    /**
     * @var array
     */
    protected $defaultValues = array();

    /**
     * @var array
     */
    private $optionValues = array();

    /**
     * Initialize
     */
    protected function init()
    {
        $this->initOptionValues();
    }

    /**
     * @return string
     */
    final public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * @return string
     */
    final public function getOptionGroup()
    {
        return $this->optionGroup;
    }

    /**
     * Get all values
     */
    final public function getValues()
    {
        return $this->optionValues;
    }

    /**
     * Get value
     * @param string $key
     * @param mixed  $defaultValue  Optional
     * @return mixed|null
     */
    final public function getValue($key, $defaultValue = null)
    {
        if (!$this->keyExists($key)) {
            return $defaultValue;
        }

        return $this->optionValues[$key];
    }

    /**
     * Set value
     * @param string $key
     * @return mixed|null
     */
    final public function setValue($key, $value)
    {
        if ($this->keyExists($key)) {
            $this->optionValues[$key] = $value;
        }
    }

    /**
     * Check if key value exists
     * @param type $key
     * @return boolean
     */
    final public function keyExists($key)
    {
        return key_exists($key, $this->optionValues);
    }

    /**
     * Add or update database entry
     */
    final public function update()
    {
        return update_option($this->optionName, $this->optionValues);
    }

    /**
     * Delete database entry
     */
    final public function delete()
    {
        return delete_option($this->optionName);
    }

    /**
     * Initialize setting the current option values
     */
    final protected function initOptionValues()
    {
        // get values form db
        $savedValues = get_option($this->optionName);

        if (is_array($savedValues)) {
            $this->optionValues = array_merge($this->defaultValues, $savedValues);
        } else {
            $this->optionValues = $this->defaultValues;
        }
    }

    /**
     * Callback automatically attached to the WP action "admin_init"
     */
    final protected function actionAdminInit()
    {
        register_setting($this->optionGroup, $this->optionName);
    }

}

/*?>*/
