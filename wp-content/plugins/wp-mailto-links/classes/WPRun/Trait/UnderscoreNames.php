<?php
/**
 * Class WPRun_Trait_UnderscoreNames_0x5x0
 *
 * Extends support for using underscore alias for methods and properties.
 * F.e. "$this->someCamelCaseFunc()" can be called with "$this->some_camel_case_func()"
 *
 * @package  WPRun
 * @category WordPress Plugin
 * @version  0.5.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
trait WPRun_Trait_UnderscoreNames_0x5x0
{

    /**
     * @var string
     */
    private static $returnVoidValue = '__VOID__';

    /**
     * @param string $methodName
     * @param array  $arguments
     * @return mixed|void
     */
    public function __call($methodName, $arguments)
    {
        $return = self::call($this, get_called_class(), $methodName, $arguments);

        // no underscore name or no camelCase equivalent found
        if ($return === self::$returnVoidValue) {
            // call parent method (if exists)
            if (is_callable('parent::__call')) {
                return parent::__call($methodName, $arguments);
            }

            // nothing found
            trigger_error('Method "' . $methodName . '" does not exist.');
        }

        return $return;
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     * @return mixed|void
     */
    public static function __callStatic($methodName, $arguments)
    {
        $return = self::call(get_called_class(), get_called_class(), $methodName, $arguments);

        // no underscore name or no camelCase equivalent found
        if ($return === self::$returnVoidValue) {
            // call parent method (if exists)
            if (is_callable('parent::__callStatic')) {
                return parent::__callStatic($methodName, $arguments);
            }

            // nothing found
            trigger_error('Method "' . $methodName . '" does not exist.');
        }

        return $return;
    }

    /**
     * @param string $propertyName
     * @return mixed
     */
    public function __get($propertyName)
    {
        $return = $this->property($propertyName);

        // no underscore name or no camelCase equivalent found
        if ($return === self::$returnVoidValue) {
            // call parent method (if exists)
            if (is_callable('parent::__get')) {
                return parent::__get($propertyName);
            }

            // nothing found
            trigger_error('Property "' . $propertyName . '" does not exist.');
        }

        return $return;
    }

    /**
     * @param string $propertyName
     * @param mixed  $value
     */
    public function __set($propertyName, $value)
    {
        $return = $this->property($propertyName, $value);

        // no underscore name or no camelCase equivalent found
        if ($return === self::$returnVoidValue) {
            // call parent method (if exists)
            if (is_callable('parent::__set')) {
                parent::__set($propertyName, $value);
                return;
            }

            // nothing found
            trigger_error('Property "' . $propertyName . '" does not exist.');
        }
    }

    /**
     * @param object $object
     * @param string $aliasName
     * @return mixed|void
     */
    private function property($aliasName /* [, $value] */)
    {
        // check if underscored property name was used
        $realName = self::toCamelCase($aliasName);
        $reflection = new ReflectionClass(get_called_class());

        if ($reflection->hasProperty($realName)) {
            if (func_num_args() > 1) {
                // set
                $this->{$realName} = func_get_arg(1);
                return true;
            } else {
                //get
                return $this->{$realName};
            }
        }

        return self::$returnVoidValue;
    }

    /**
     * @param string|object  $callbackTarget    Object or class name
     * @param string         $reflectionTarget  Class name
     * @param string         $aliasMethodName
     * @param array          $arguments
     * @return mixed|void
     * @throws Exception
     */
    private static function call($callbackTarget, $reflectionTarget, $aliasMethodName, $arguments)
    {
        // check called method name is an alias and can be converted
        $realMethodName = self::toCamelCase($aliasMethodName);
        $callback = array($callbackTarget, $realMethodName);

        if (method_exists($callbackTarget, $realMethodName)) {
            $reflection = new ReflectionMethod($reflectionTarget, $realMethodName);
            return call_user_func_array($callback, $arguments);
        }

        return self::$returnVoidValue;
    }

    /**
     * Convert to camelCase
     * @param string  $name
     * @return string
     */
    private static function toCamelCase($name)
    {
        $camelCaseName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
        return $camelCaseName;
    }

}

/*?>*/
