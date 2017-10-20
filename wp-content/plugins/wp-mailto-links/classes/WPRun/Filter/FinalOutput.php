<?php
/**
 * Class MyLib_Filter_FinalOutput_0x5x0
 *
 * @package  MyLib
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
     * Action for "init" hook
     */
    protected function actionInit()
    {
        ob_start($this->getCallback('apply'));
    }

    /**
     * Apply filters
     * @param string $content
     * @return string
     */
    protected function apply($content)
    {
        $filteredContent = apply_filters(self::FILTER_NAME, $content);

        // remove filters after applying to prevent multiple applies
        remove_all_filters(self::FILTER_NAME);

        return $filteredContent;
    }

}

/*?>*/
