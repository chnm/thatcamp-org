<?php
/**
 * Class WPRun_AutoLoader_0x5x0
 *
 * @package  WPRun
 * @category WordPress Plugin
 * @version  0.5.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPRun_AutoLoader_0x5x0
{

    /**
     * @var array
     */
    private static $paths = array();

    /**
     * @var boolean
     */
    private static $registered = false;

    /**
     * @param array $globals  Optional, only on first call
     */
    final public static function register()
    {
        if (self::$registered === true) {
            return;
        }

        self::$registered = true;

        spl_autoload_register(array(get_called_class(), 'loadClass'));
    }

    /**
     * Add path for classes
     * @param string $path
     */
    final public static function addPath($path)
    {
        self::$paths[] = $path;
    }

    /**
     * Get all paths
     * @return array
     */
    final public static function getPaths()
    {
        return self::$paths;
    }

    /**
     * Loads a class file
     * @param string $className
     * @return void
     */
    public static function loadClass($className)
    {
        if (class_exists($className)) {
            return;
        }

        // remove version postfix
        $pureClassName = preg_replace('/_\d+x\d+x\d+/', '', $className);

        $internalPath = str_replace('_', DIRECTORY_SEPARATOR, $pureClassName);

        foreach (self::$paths as $path) {
            $file = $path . DIRECTORY_SEPARATOR . $internalPath . '.php';

            if (file_exists($file)) {
                include $file;
                return;
            }
        }
    }

}

/*?>*/
