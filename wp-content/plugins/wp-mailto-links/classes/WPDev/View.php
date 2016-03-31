<?php
/**
 * Class WPDev_View
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 */
class WPDev_View
{

    /**
     * Path of the file
     * @var string
     */
    protected $file = null;

    /**
     * View vars
     * @var array
     */
    protected $vars = array();


    /**
     * Create view instance (factory method)
     * @param string $file
     * @param array $vars  Optional
     * @return \WPDev_View
     */
    public static function create($file, array $vars = array())
    {
        return new self($file, $vars);
    }

    /**
     * Protected constructor to force using create factory
     * @param string $file
     * @param array $vars  Optional
     */
    protected function __construct($file, array $vars = array())
    {
        $this->file = $file;
        $this->vars = $vars;
    }

    /**
     * View file exists
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->file);
    }

    /**
     * Render a view
     * @param string $file
     * @param boolean $show  Optional, default false
     * @return string  Rendered content
     */
    public function render($show = false) {
        if (!$this->exists()) {
            throw new Exception('The file "' . $this->file . '" could not be rendered as view (file does not exist or is not readable).');
        }

        // extract vars to global namespace
        extract($this->vars, EXTR_SKIP);

        // start output buffer
        ob_start();

        include $this->file;

        // get the view content
        $content = ob_get_contents();

        // clean output buffer
        ob_end_clean();

        // print content
        if ($show) {
            echo $content;
        }

        return $content;
    }

}

/*?>*/
