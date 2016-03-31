<?php
/**
 * Class WPDev_Test_UnitBase
 *
 * Parent for all Unit Test
 *
 * @package  WPDev
 * @category WordPress Library
 * @version  0.3.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPDev
 * @license  MIT license
 */
class WPDev_Test_UnitBase extends PHPUnit_Framework_TestCase
{

    /**
     * Helper to get a clear mocked function
     * @param string $funcName
     * @return \WPDev_Test_MockFunction
     */
    public function mockFunction($funcName)
    {
        if (!class_exists('WPDev_Test_MockFunction')) {
            require_once 'MockFunction.php';
        }

        return WPDev_Test_MockFunction::getMock($funcName);
    }

}
