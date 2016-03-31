<?php
/**
 * Class WPDev_Plugin
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 */
abstract class WPDev_Plugin
{

    /**
     * This property should also be included in child classes to prevent conflicts
     * @var \WPDev_Plugin
     */
    protected static $instance = null;

    /**
     * @var array
     */
    protected $globals = array();

    /**
     * Factory method
     * @param array $globals  Optional, only on first call
     */
    public static function create(array $globals = array())
    {
        static::$instance = new static($globals);
        static::$instance->init();
        return static::$instance;
    }

    /**
     * @return \WPDev_Plugin
     */
    public static function plugin()
    {
        return static::$instance;
    }

    /**
     * Short call for getting a global
     * @param string $key
     * @return mixed
     */
    public static function glob($key)
    {
        return static::$instance->getGlobal($key);
    }

    /**
     * Constructor
     * @param array $globals  Optional
     */
    protected function __construct(array $globals = array())
    {
        $this->globals = array_merge($this->globals, $globals);
    }

    /**
     * Init plugin
     * Should be implemented
     */
    abstract protected function init();

    /**
     * Get global
     * @param string $key
     * @return mixed
     */
    public function getGlobal($key)
    {
        if (key_exists($key, $this->globals)) {
            return $this->globals[$key];
        }

        return null;
    }

    /**
     * Get all globals
     * @return array
     */
    public function getAllGlobals()
    {
        return $this->globals;
    }

    /**
     * Set global
     * @param string $key
     * @param mixed  $value
     */
    public function setGlobal($key, $value)
    {
        $this->globals[$key] = $value;
    }

}

/*?>*/
