<?php
/**
 * Class WPDev_Filter_FinalOutput
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 *
 * @example
 *      WPDev_Filter_FinalOutput::create();
 *
 *      add_filter('final_output', 'wp_replace_b_tags', 10, 1);
 *
 *      function wp_replace_b_tags($content) {
 *          $content = str_replace('<b>', '<strong>', $content);
 *          $content = str_replace('</b>', '</strong>', $content);
 *          return $content;
 *      }
 */
class WPDev_Filter_FinalOutput
{

    /**
     * Filter name
     * @var string
     */
    protected $filterName = 'final_output';

    /**
     * @var \WPDev_Filter_FinalOutput
     */
    protected static $instance = null;

    /**
     * Factory method
     * @return \WPDev_Filter_FinalOutput
     */
    public static function create()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        add_action('wp', array($this, 'bufferStart'), 1);
    }

    /**
     * Start buffer
     */
    public function bufferStart() {
        ob_start(array($this, 'filterOutput'));
    }

    /**
     * @param string $content
     * @return string
     */
    public function filterOutput($content) {
        return apply_filters($this->filterName, $content);
    }

}

/*?>*/
