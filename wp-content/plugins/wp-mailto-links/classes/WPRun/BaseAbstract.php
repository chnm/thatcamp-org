<?php
/**
 * Class WPRun_BaseAbstract_0x5x0
 *
 * Base class for concrete subclasses
 * All subclasses are singletons and can be instantiated with the static
 * "create()" factory method.
 *
 * @package  WPRun
 * @category WordPress Plugin
 * @version  0.5.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class WPRun_BaseAbstract_0x5x0
{

    /**
     * @var string
     */
    protected $actionPrefix = 'action';

    /**
     * @var string
     */
    protected $filterPrefix = 'filter';

    /**
     * Only for internal use
     * @var string
     */
    private $internalCallbackPrefix = '_cb_';

    /**
     * Only for internal use
     * @var string
     */
    private $callbackTemplateTag = '_execTemplateTag';

    /**
     * List of (singleton) instances
     * Only for internal use
     * @var array
     */
    private static $instances = array();

    /**
     * @var array
     */
    private $arguments = array();

    /**
     * Factory method
     * @param mixed $param1   Optional, will be passed on to the constructor and init() method
     * @param mixed $paramN   Optional, will be passed on to the constructor and init() method
     * @return WPRun_BaseAbstract_0x5x0|false
     */
    final public static function create()
    {
        $className = get_called_class();
        $arguments = func_get_args();

        // check if instance of this class already exists
        if (key_exists($className, self::$instances)) {
            return false;
        }

        // pass all arguments to constructor
        $instance = new $className($arguments);

        return $instance;
    }

    /**
     * Constructor
     */
    private function __construct(array $arguments)
    {
        $className = get_called_class();
        self::$instances[$className] = $this;

        $this->arguments = $arguments;

        $this->initMethods();

        // call init
        if (method_exists($this, 'init')) {
            call_user_func_array(array($this, 'init'), $arguments);
        }
    }

    /**
     * Get argument passed on to the constructor
     * @param integer $index  Optional, when null return all arguments
     * @return mixed|null
     */
    final protected function getArgument($index = null)
    {
        // return all arguments when no index given
        if ($index === null) {
            return $this->arguments;
        }
        
        if (!isset($this->arguments[$index])) {
            return null;
        }
        
        return $this->arguments[$index];
    }

    /**
     * @param string $templateFilePath
     * @param array  $vars              Optional
     * @triggers E_USER_NOTICE          Template file not readable
     */
    final protected function showTemplate($templateFilePath, array $vars = array())
    {
        if (is_readable($templateFilePath)) {
            // create template variables
            extract($vars);

            // show file
            include $templateFilePath;
        } else {
            trigger_error('Template file "' . $templateFilePath . '" is not readable or may not exists.');
        }
    }

    /**
     * @param string $templateFilePath
     * @param array  $vars              Optional
     * @triggers E_USER_NOTICE          Template file not readable
     */
    final protected function renderTemplate($templateFilePath, array $vars = array())
    {
        // start output buffer
        ob_start();

        // output template
        $this->showTemplate($templateFilePath, $vars);

        // get the view content
        $content = ob_get_contents();

        // clean output buffer
        ob_end_clean();

        return $content;
    }

    /**
     * Get a callable to a method in current instance, when called will be
     * caught by __callStatic(), were the magic happens
     * @param string  $methodName
     * @return callable
     */
    final protected function getCallback($methodName)
    {
        return array(get_called_class(), $this->internalCallbackPrefix . $methodName);
    }

    /**
     * Create a WP template tag (global function)
     * @param string   $templateTag
     * @param callable $callback
     * @return boolean
     * @throws Exception
     */
    final protected function createTemplateTag($templateTag, $callback)
    {
        if (function_exists($templateTag)) {
            return false;
        }

        if (!is_callable($callback)) {
            throw new Exception('Given callback is not a callable.');
        }

        // create global function
        $func = 'function ' . $templateTag . '() {'
                . 'return ' . get_called_class() . '::' . $this->callbackTemplateTag . '("' . $callback[1] . '", func_get_args());'
                . '}';

        eval($func);

        return true;
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     * @return mixed|void
     */
    public function __call($methodName, $arguments)
    {
        return self::_magicCall($methodName, $arguments);
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     * @return mixed|void
     */
    public static function __callStatic($methodName, $arguments)
    {
        return self::_magicCall($methodName, $arguments);
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     * @return mixed|void
     */
    private static function _magicCall($methodName, $arguments)
    {
        $className = get_called_class();
        $instance = self::$instances[$className];

        // template tag callback
        if ($methodName === $instance->callbackTemplateTag) {
            $templateTagMethodName = $arguments[0];
            $templateTagArguments = $arguments[1];

            return call_user_func_array(array($instance, $templateTagMethodName), $templateTagArguments);
        }

        // catch callbacks set by getCallback() method
        // this way callbacks can also be implemented as protected
        $givenCallbackName = self::fetchNameContainingPrefix($instance->internalCallbackPrefix, $methodName);

        // normal callback
        if ($givenCallbackName !== null) {
            $realArguments = $arguments;

            $givenMethodName = $givenCallbackName;

            $callable = array($instance, $givenMethodName);

            if (is_callable($callable)) {
                return call_user_func_array($callable, $realArguments);
            }
        }
    }

    /**
     * Check and auto-initialize methods for hooks, shortcodes and template tags
     */
    private function initMethods()
    {
        $methods = get_class_methods($this);

        foreach ($methods as $methodName) {
            $actionName = self::fetchNameContainingPrefix($this->actionPrefix, $methodName);
            if ($actionName !== null) {
                self::addToHook($this, 'action', $actionName, $methodName);
                continue;
            }

            $filterName = self::fetchNameContainingPrefix($this->filterPrefix, $methodName);
            if ($filterName !== null) {
                self::addToHook($this, 'filter', $filterName, $methodName);
                continue;
            }
        }
    }

    /**
     * @param WPRun_BaseAbstract_0x5x0   $self
     * @param string                     $hookType  "action" or "filter"
     * @param string                     $hookName
     * @param string                     $methodName
     * @triggers E_USER_NOTICE
     */
    private static function addToHook($self, $hookType, $hookName, $methodName)
    {
        // fetch priority outof method name
        $splitMethodeName = explode('_', $methodName);
        $last = end($splitMethodeName);

        if (is_numeric($last)) {
            $priority = (int) $last;
            $realHookName = str_replace('_' . $last, '', $hookName);
        } else {
            $priority = 10;
            $realHookName = $hookName;
        }

        $wpHookName = self::toWpName($realHookName);

        // get the method's number of params
        $methodReflection = new ReflectionMethod(get_called_class(), $methodName);
        $acceptedArguments = $methodReflection->getNumberOfParameters();

        if ('action' === $hookType) {
            add_action($wpHookName, $self->getCallback($methodName), $priority, $acceptedArguments);
        } elseif ('filter' === $hookType) {
            add_filter($wpHookName, $self->getCallback($methodName), $priority, $acceptedArguments);
        } else {
            trigger_error('"' . $hookType . '" is not a valid hookType.');
        }
    }

    /**
     * Convert to WP naming convention (lowercase and underscores)
     * @param string $name
     * @return string
     */
    private static function toWpName($name)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $name)), '_');
    }

    /**
     * @param string $prefix
     * @param string $name
     * @return string|null
     */
    private static function fetchNameContainingPrefix($prefix, $name)
    {
        $prefixLength = strlen($prefix);

        if ($prefix !== substr($name, 0, $prefixLength)) {
            return null;
        }

        $fetchedName = substr($name, $prefixLength);
        return $fetchedName;
    }

}

/*?>*/
