<?php
/**
 * Class MyDevLib_FormHelper_0x5x0
 *
 * @package  Demo WPRun
 * @category WordPress Plugin
 * @version  0.5.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class MyDevLib_FormHelper_0x5x0
{

    /**
     * @var string
     */
    private $groupName = null;

    /**
     * @var array
     */
    private $values = array();


    /**
     * @param string $name      Optional, should be the option name (containing multiple values)
     * @param array  $values    Optional
     */
    public function __construct($name = null, array $values = array())
    {
        $this->groupName = $name;
        $this->values = $values;
    }

    /**
     * Get form field name
     * @param string $key
     * @return string
     */
    public function getFieldName($key)
    {
        if ($this->groupName === null) {
            return $key;
        }

        return $this->groupName . '[' . $key . ']';
    }

    /**
     * Get form field id
     * @param string $key
     * @return string
     */
    public function getFieldId($key)
    {
        if ($this->groupName === null) {
            return $key;
        }

        return $this->groupName . '-' . $key;
    }

    /**
     * Get value
     * @param string $key
     * @param mixed  $defaultValue  Optional
     * @return mixed|null
     */
    public function getValue($key, $defaultValue = null)
    {
        if (!isset($this->values[$key])) {
            return $defaultValue;
        }

        return $this->values[$key];
    }

    /**
     * Show html label
     * @param string $key
     * @param string $labelText
     */
    public function label($key, $labelText)
    {
        echo '<label for="' . $this->getFieldId($key) . '">
                     ' . $labelText . '
               </label>';
    }

    /**
     * Show text input field
     * @param string $key
     * @param string $class
     */
    public function textField($key, $class = 'regular-text')
    {
        echo '<input type="text"
                    class="' . $class . '"
                    id="' . $this->getFieldId($key) . '"
                    name="' . $this->getFieldName($key) . '"
                    value="' . esc_attr($this->getValue($key)) . '"
                >';
    }

    /**
     * Show a check field
     * @param string $key
     * @param mixed  $checkedValue
     * @param mixed  $uncheckedValue
     * @param string $class
     */
    public function checkField($key, $checkedValue, $uncheckedValue = null, $class = '')
    {
        // workaround for also posting a value when checkbox is unchecked
        if ($uncheckedValue !== null) {
            echo '<input type="hidden"
                        name="' . $this->getFieldName($key) . '"
                        value="' . esc_attr($uncheckedValue) . '"
                    >';
        }

        echo '<input type="checkbox"
                    class="' . $class . '"
                    id="' . $this->getFieldId($key) . '"
                    name="' . $this->getFieldName($key) . '"
                    value="' . esc_attr($checkedValue) . '"
                    ' . $this->getCheckedAttr($key, $checkedValue) . '
                >';
    }

    /**
     * Show a radio field
     * @param string $key
     * @param mixed  $checkedValue
     * @param string $class
     */
    public function radioField($key, $checkedValue, $class = '')
    {
        $id = $this->getFieldId($key) . '-' . sanitize_key($checkedValue);

        echo '<input type="radio"
                    class="' . $class . '"
                    id="' . $id . '"
                    name="' . $this->getFieldName($key) . '"
                    value="' . esc_attr($checkedValue) . '"
                    ' . $this->getCheckedAttr($key, $checkedValue) . '
                >';
    }

    /**
     * Show select field with or without options
     * @param string $key
     * @param mixed  $checkedValue
     * @param array  $options
     * @param string $class
     */
    public function selectField($key, $checkedValue, array $options = array(), $class = '')
    {
        echo '<select class="' . $class . '"
                    class="' . $class . '"
                    id="' . $this->getFieldId($key) . '"
                    name="' . $this->getFieldName($key) . '"
                >';

        foreach ($options as $value => $text) {
            $this->selectOption($text, $value, ($checkedValue == $value));
        }

        echo '</select>';
    }

    /**
     * Show a select option
     * @param string  $text
     * @param string  $value
     * @param boolean $selected
     */
    public function selectOption($text, $value, $selected = false)
    {
        echo '<option value="' . esc_attr($value) . '"' . ($selected ? ' selected' : '') . '>
                    ' . $text  . '
               </option>';
    }

    /**
     * Show submit button
     */
    public function submitButton()
    {
        echo '<input type="submit"
                    class="button button-primary button-large"
                    value="' . __('Save Changes') . '"
                >';
    }

    /**
     * Get the checked attribute
     * @param string $key
     * @param mixed  $checkedValue
     * @return string
     */
    private function getCheckedAttr($key, $checkedValue)
    {
        return ($this->getValue($key) == $checkedValue) ? ' checked' : '';
    }

}

/*?>*/
