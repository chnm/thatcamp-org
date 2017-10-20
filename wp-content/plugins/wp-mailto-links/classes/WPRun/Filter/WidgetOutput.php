<?php
/**
 * Class WPRun_Filter_WidgetOutput_0x5x0
 *
 * @package  MyLib
 * @category WordPress Plugin
 * @version  0.5.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPRun_Filter_WidgetOutput_0x5x0 extends WPRun_BaseAbstract_0x5x0
{

    const FILTER_NAME = 'widget_output';

    /**
     * Filter for "dynamic_sidebar_params" hook
     * @global array $wp_registered_widgets
     * @param  array $sidebarParams
     * @return array
     */
    protected function filterDynamicSidebarParams($sidebarParams)
    {
         global $wp_registered_widgets;

        if (is_admin()) {
            return $sidebarParams;
        }

        $widgetId = $sidebarParams[0]['widget_id'];

        // prevent overwriting when already set by another version of the widget output class
        if (isset($wp_registered_widgets[$widgetId]['_wo_original_callback'])) {
            return $sidebarParams;
        }

        $wp_registered_widgets[$widgetId]['_wo_original_callback'] = $wp_registered_widgets[$widgetId]['callback'];
        $wp_registered_widgets[$widgetId]['callback'] = $this->getCallback('widgetCallback');

        return $sidebarParams;
    }

    /**
     * Widget Callback
     * @global array $wp_registered_widgets
     */
    protected function widgetCallback()
    {
        global $wp_registered_widgets;

        $originalCallbackParams = func_get_args();
        $widgetId = $originalCallbackParams[0]['widget_id'];

        $originalCallback = $wp_registered_widgets[$widgetId]['_wo_original_callback'];
        $wp_registered_widgets[$widgetId]['callback'] = $originalCallback;

        $widgetIdBase = $wp_registered_widgets[$widgetId]['callback'][0]->id_base;

        if (is_callable($originalCallback)) {
            ob_start();
            call_user_func_array($originalCallback, $originalCallbackParams);
            $widgetOutput = ob_get_clean();

            echo apply_filters(self::FILTER_NAME, $widgetOutput, $widgetIdBase, $widgetId);

            // remove filters after applying to prevent multiple applies
            remove_all_filters(self::FILTER_NAME);
        }
    }

}

/*?>*/
