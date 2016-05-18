<?php
/**
 * Class WPRun_Filter_FinalOutput_0x5x0
 *
 * @package  WPRun
 * @category WordPress Plugin
 * @version  0.5.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPRun_Filter_FinalOutput_0x5x0 extends WPRun_BaseAbstract_0x5x0
{

    const FILTER_NAME = 'final_output';

    /**
     * Method automatically attached as callback to the action "init"
     */
    protected function actionInit()
    {
        $filterName = self::FILTER_NAME;

        ob_start(function ($content) use ($filterName) {
            return apply_filters($filterName, $content);
        });
    }

}

/*?>*/
