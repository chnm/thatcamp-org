<?php
/**
 * Lim_View Class
 *
 * View class that contains features like:
 * - Get vars inside the view in different ways:
 *        1) $var1
 *        2) $this->var1
 *        3) $this->getVar('var1', 'defaultValue')
 * - Manage content filters with priority
 * - Manage global vars and global filters, which will be available for all views
 *
 * @package  View
 * @version  2.1.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/lim_view-php-view-class/
 * @license  MIT license
 */
class Lim_View {

    /**
     * Containing global vars (available for all view instances)
     * @var array
     */
    protected static $_globalVars = array();

    /**
     * Containing global filter callbacks
     * @var array
     */
    protected static $_globalFilters = array();

    /**
     * Always able to use the php short open tag (<?= $var ?>), even when
     * short_open_tag is disabled in the php configuration
     * @var boolean
     */
    protected static $_shortOpenTag = false;

    /**
     * Throw exceptions on failure
     * @var boolean
     */
    protected static $_throwExceptions = true;

    /**
     * Containing view paths
     * @var array
     */
    protected static $_paths = array();

    /**
     * View file path
     * @var string
     */
    protected $_file = null;

    /**
     * View vars
     * @var array
     */
    protected $_vars = array();

    /**
     * Containing filter callbacks
     * @var array
     */
    protected $_filters = array();


    /**
     * Constructor
     * @param string $file  Optional, can be set later
     */
    public function __construct($file = null) {
        $this->setFile($file);
    }

    /**
     * Set or get php short open tag support (<?= $var ?>)
     * @param boolean $shortOpenTag  Optional, else used as getter
     * @return boolean|void  Returns php short tag support or nothing
     */
    public static function shortOpenTag($shortOpenTag = null) {
        if ($shortOpenTag !== null) {
            self::$_shortOpenTag = (bool) $shortOpenTag;
        }

        return self::$_shortOpenTag;
    }

    /**
     * Set when exceptions should be thrown or not.
     * @param boolean $throwExceptions  Optional, else used as a getter
     * @return boolean
     */
    public static function throwExceptions($throwExceptions = null) {
        if ($throwExceptions !== null) {
            self::$_throwExceptions = (bool) $throwExceptions;
        }

        return self::$_throwExceptions;
    }

    /**
     * Add view path
     * @param string|array $path
     */
    public static function addPath($path) {
        if (is_array($path)) {
            foreach ($path as $val) {
                self::$_paths[$val] = $val;
            }
        } else {
            self::$_paths[$path] = $path;
        }
    }

    /**
     * Remove view path
     * @param string $path
     */
    public static function removePath($path) {
        unset(self::$_paths[$path]);
    }

    /**
     * Get array of all paths
     * @return array
     */
    public static function getPaths() {
        return self::$_paths;
    }

    /**
     * Remove all view paths
     */
    public static function clearPaths() {
        foreach (self::$_paths as $path) {
            self::removePath($path);
        }
    }

    /**
     * Set the view file
     * @param string $file
     * @return $this
     */
    public function setFile($file) {
        $this->_file = $file;

        return $this;
    }

    /**
     * Get the view file
     * @return string
     */
    public function getFile() {
        return $this->_file;
    }

    /**
     * Check if view file exists
     * @param string $file  Check if given view file exists
     * @return boolean
     */
    public static function exists($file) {
        // check if file exists
        if (self::_fileExists($file)) {
            return true;
        }

        // check if file can be found in the paths
        foreach (self::$_paths as $path) {
            if (self::_fileExists($path . $file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set (local) var
     * @param string|array $key  Can also give array of values, f.e. array('var1' => 'value1', 'var2' => 'value2')
     * @param mixed $value  Optional, default null
     * @return $this
     */
    public function setVar($key, $value = null) {
        $keys = (is_array($key)) ? $key : array($key => $value);

        foreach ($keys as $k => $v) {
            $this->_vars[$k] = $v;
        }

        return $this;
    }

    /**
     * Set var
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        $this->setVar($key, $value);
    }

    /**
     * Get a var value (if not exists it will look for a global var with the given key)
     * @param string $key
     * @param mixed $defaultValue  Optional, return default when key was not found
     * @param boolean $includeGlobalVars  Optional, default true
     * @return mixed
     */
    public function getVar($key, $defaultValue = null, $includeGlobalVars = true) {
        if (array_key_exists($key, $this->_vars)) {
            return $this->_vars[$key];
        }

        if ($includeGlobalVars && array_key_exists($key, self::$_globalVars)) {
            return self::$_globalVars[$key];
        }

        return $defaultValue;
    }

    /**
     * Get var value, also checks global vars
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        if (!$this->hasVar($key)) {
            $this->_throwException('Key "' . $key . '" was not set.');
        }

        return $this->getVar($key);
    }

    /**
     * Unset given (local) var
     * @param string|array $key  Can also give array of keys to unset
     * @return $this
     */
    public function unsetVar($key) {
        $keys = (is_array($key)) ? $key : array(0 => $key);

        foreach ($keys as $i => $k) {
            if (array_key_exists($k, $this->_vars)) {
                unset($this->_vars[$k]);
            }
        }

        return $this;
    }

    /**
     * Unset (local) var
     * @param string $key
     */
    public function __unset($key) {
        $this->unsetVar($key);
    }

    /**
     * Check if given var exists
     * @param string $key
     * @param boolean $includeGlobalVars  Optional, default true
     * @return boolean
     */
    public function hasVar($key, $includeGlobalVars = true) {
        return (array_key_exists($key, $this->_vars) || ($includeGlobalVars && array_key_exists($key, self::$_globalVars)));
    }

    /**
     * Get all view vars
     * @param boolean $includeGlobalVars  Optional, default true
     * @return array
     */
    public function getVars($includeGlobalVars = true) {
        return ($includeGlobalVars) ? array_merge(self::$_globalVars, $this->_vars) : $this->_vars;
    }

    /**
     * Unset all (local) vars
     * @param string $key
     * @return $this
     */
    public function clearVars() {
        foreach ($this->_vars as $key => $val) {
            $this->unsetVar($key);
        }

        return $this;
    }

    /**
     * Set a global var (available for all views)
     * @param string|array $key  Can also give array of values, f.e. array('var1' => 'value1', 'var2' => 'value2')
     * @param mixed $value  Optional, default null
     */
    public static function setGlobalVar($key, $value = null) {
        $keys = (is_array($key)) ? $key : array($key => $value);

        foreach ($keys as $k => $v) {
            self::$_globalVars[$k] = $v;
        }
    }

    /**
     * Set a global var (available for all views)
     * @param string $key
     * @param mixed $defaultValue  Optional, return default when key was not found
     */
    public static function getGlobalVar($key, $defaultValue = null) {
        if (array_key_exists($key, self::$_globalVars)) {
            return self::$_globalVars[$key];
        }

        return $defaultValue;
    }

    /**
     * Unset global var
     * @param string|array $key  Can also give array of keys to unset
     */
    public static function unsetGlobalVar($key) {
        $keys = (is_array($key)) ? $key : array(0 => $key);

        foreach ($keys as $i => $k) {
            if (array_key_exists($k, self::$_globalVars)) {
                unset(self::$_globalVars[$k]);
            }
        }
    }

    /**
     * Check if global var exists
     * @param string $key
     * @return boolean
     */
    public static function hasGlobalVar($key) {
        return array_key_exists($key, self::$_globalVars);
    }

    /**
     * Get array of all global vars
     * @return array
     */
    public static function getGlobalVars() {
        return self::$_globalVars;
    }

    /**
     * Unset all global vars
     */
    public static function clearGlobalVars() {
        foreach (self::$_globalVars as $key => $val) {
            self::unsetGlobalVar($key);
        }
    }

    /**
     * Set filter callback. 2 parameters will be passed on to the callback function:
     *  (1) $content: rendered content
     *  (2) $view: this view object
     * @param mixed $callback
     * @param string $key  Optional
     * @param integer $priority  Optional, default 10
     * @return $this
     */
    public function addFilter($callback, $key = null, $priority = 10) {
        $cb = array('callback' => $callback, 'priority' => (int) $priority);

        if ($key === null) {
            $this->_filters[] = $cb;
        } else {
            $this->_filters[$key] = $cb;
        }

        return $this;
    }

    /**
     * Remove filter
     * @param string $key
     * @return $this
     */
    public function removeFilter($key) {
        if (array_key_exists($key, $this->_filters)) {
            unset($this->_filters[$key]);
        }

        return $this;
    }

    /**
     * Check if filter exists
     * @param string $key
     * @param boolean $includeGlobalFilters  Optional, default true
     * @return boolean
     */
    public function hasFilter($key, $includeGlobalFilters = true) {
        return (array_key_exists($key, $this->_filters) || ($includeGlobalFilters && array_key_exists($key, self::$_globalFilters)));
    }

    /**
     * Get filter
     * @param boolean $includeGlobalFilters  Optional, default true
     * @param boolean $prioritizedOrder
     * @return array
     */
    public function getFilters($includeGlobalFilters = true, $prioritizedOrder = false) {
        $filters = ($includeGlobalFilters) ? array_merge(self::$_globalFilters, $this->_filters) : $this->_filters;

        return ($prioritizedOrder) ? self::_getPrioritizedFilters($filters) : $filters;
    }

    /**
     * Remove all filters
     * @return $this
     */
    public function clearFilters() {
        foreach ($this->_filters as $key => $val) {
            $this->removeFilter($key);
        }

        // reset indexes
        $this->_filters = array();

        return $this;
    }

    /**
     * Set global filter (used for all views). 2 parameters will be passed on to the callback function:
     *  (1) $content: rendered content
     *  (2) $view: this view object
     * @param mixed $callback
     * @param string $key  Optional
     * @param integer $priority  Optional, default 10
     */
    public static function addGlobalFilter($callback, $key = null, $priority = 10) {
        $cb = array('callback' => $callback, 'priority' => (int) $priority);

        // add global filter as a callback function
        if ($key === null) {
            self::$_globalFilters[] = $cb;
        } else {
            self::$_globalFilters[$key] = $cb;
        }
    }

    /**
     * Remove filter
     * @param string $key
     */
    public static function removeGlobalFilter($key) {
        if (array_key_exists($key, self::$_globalFilters)) {
            unset(self::$_globalFilters[$key]);
        }
    }

    /**
     * Check if filter exists
     * @param string $key
     * @return boolean
     */
    public static function hasGlobalFilter($key) {
        return array_key_exists($key, self::$_globalFilters);
    }

    /**
     * Get filter
     * @param boolean $prioritizedOrder
     * @return array
     */
    public static function getGlobalFilters($prioritizedOrder = false) {
        return ($prioritizedOrder) ? self::_getPrioritizedFilters(self::$_globalFilters) : self::$_globalFilters;
    }

    /**
     * Remove all global filters
     */
    public static function clearGlobalFilters() {
        foreach (self::$_globalFilters as $key => $val) {
            self::removeGlobalFilter($key);
        }

        // reset indexes
        self::$_globalFilters = array();
    }

    /**
     * Render the view content
     * @param boolean $echo  Optional, default false
     * @param boolean $applyGlobalFilters  Optional, default true
     * @return string
     * @throw Exception
     */
    public function render($echo = false, $applyGlobalFilters = true) {
        // check if view file exists
        $file = null;

        if (self::_fileExists($this->_file)) {
            $file = $this->_file;
        } else {
            foreach (self::$_paths as $path) {
                if (self::_fileExists($path . $this->_file)) {
                    $file = $path . $this->_file;
                }
            }

            if ($file === null) {
                $this->_throwException('The file "' . $this->_file . '" could not be fetched.');
                return $this;
            }
        }

        // get global and local vars
        $vars = $this->getVars(true);

        // extract vars to global namespace
        extract($vars, EXTR_SKIP);

        // start output buffer
        ob_start();

        // replace short php tags to normal (in case the server doesn't support it)
        if (self::$_shortOpenTag) {
            echo eval('?>' . str_replace('<?=', '<?php echo ', file_get_contents($file)));
        } else {
            include $file;
        }

        // get the view content
        $content = ob_get_contents();

        // clean output buffer
        ob_end_clean();

        // set filters
        $filters = $this->getFilters($applyGlobalFilters, true);

        // call filters
        foreach ($filters as $key => $value) {
            if (is_callable($value['callback'])) {
                $content = call_user_func($value['callback'], $content, $this);
            }
        }

        // print content
        if ($echo) {
            echo $content;
        }

        return $content;
    }

    /**
     * Show rendered view content
     * @param boolean $applyGlobalFilters  Optional, default true
     * @return $this
     */
    public function show($applyGlobalFilters = true) {
        $this->render(true, $applyGlobalFilters);

        return $this;
    }

    /**
     * Renders the view
     * @return string
     */
    public function __toString() {
        return $this->render(false);
    }

    /**
     * Throw exception
     * @param string $msg
     * @throw Exception
     */
    protected function _throwException($msg) {
        if (self::$_throwExceptions) {
            throw new Exception(get_class($this) . ' - ' . $msg);
        }
    }

    /**
     * File exists
     * @param string $file
     * @return boolean
     */
    protected static function _fileExists($file) {
        return (file_exists($file) && is_file($file));
    }

    /**
     * Get filters in priority order
     * @param array $filters
     * @return array
     */
    protected static function _getPrioritizedFilters($filters) {
        uasort($filters, array('Lim_View', '_sortPriority'));

        return $filters;
    }

    /**
     * Sort by priority (callback)
     * @param array $a
     * @param array $b
     * @return integer
     */
    protected static function _sortPriority($a, $b) {
        if ($a['priority'] === $b['priority']) {
            return 0;
        }

        return ($a['priority'] > $b['priority']) ? -1 : 1;
    }

} // Lim_View Class
