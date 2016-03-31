<?php
/**
 * Class WPDev_Filter_WidgetOutput
 *
 * Original idea and code taken from the Widget Logic plugin
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @credit   https://wordpress.org/plugins/widget-logic/
 * @license  MIT license
 *
 * @example
 *      WPDev_Filter_WidgetOutput::create($wp_registered_widgets);
 *
 *      add_filter('widget_output', 'wp_replace_b_tags', 10, 1);
 *
 *      function wp_replace_b_tags($content) {
 *          $content = str_replace('<b>', '<strong>', $content);
 *          $content = str_replace('</b>', '</strong>', $content);
 *          return $content;
 *      }
 */
class WPDev_Filter_WidgetOutput
{

    /**
     * Filter name
     * @var string
     */
    protected $filterName = 'widget_output';

    /**
     * @var array
     */
    protected $wpRegisteredWidgets = null;

    /**
     * @var \WPDev_Filter_WidgetOutput
     */
    protected static $instance = null;

    /**
     * Factory method
     * @param array &$wpRegisteredWidgets Contains WP global $wp_registered_widgets
     * @return \WPDev_Filter_WidgetOutput
     * @throw Exception
     */
    public static function create(array & $wpRegisteredWidgets)
    {
        if (self::$instance === null) {
            self::$instance = new self($wpRegisteredWidgets);
        }

        return self::$instance;
    }

    /**
     * @param array &$wpRegisteredWidgets
     */
    protected function __construct(array & $wpRegisteredWidgets)
    {
        $this->wpRegisteredWidgets = & $wpRegisteredWidgets;

        add_filter('dynamic_sidebar_params', array($this, 'setCallbacks'), 5);
    }

    /**
     * Set callbacks for all widgets
     */
    public function setCallbacks($sidebarParams) {
        if (is_admin()) {
            return $sidebarParams;
        }

        $widgetId = $sidebarParams[0]['widget_id'];

        $this->wpRegisteredWidgets[$widgetId]['original_callback'] = $this->wpRegisteredWidgets[$widgetId]['callback'];
        $this->wpRegisteredWidgets[$widgetId]['callback'] = array($this, 'widgetCallback');

        return $sidebarParams;
    }

    /**
     * @echo string
     */
    public function widgetCallback() {
        $originalCallbackParams = func_get_args();
        $widgetId = $originalCallbackParams[0]['widget_id'];

        $originalCallback = $this->wpRegisteredWidgets[$widgetId]['original_callback'];
        $this->wpRegisteredWidgets[$widgetId]['callback'] = $originalCallback;

        $widgetIdBase = $this->wpRegisteredWidgets[$widgetId]['callback'][0]->id_base;

        if (is_callable($originalCallback)) {
            ob_start();
            call_user_func_array($originalCallback, $originalCallbackParams);
            $widgetOutput = ob_get_clean();

            echo apply_filters($this->filterName, $widgetOutput, $widgetIdBase, $widgetId);
        }
    }

}

/*?>*/
