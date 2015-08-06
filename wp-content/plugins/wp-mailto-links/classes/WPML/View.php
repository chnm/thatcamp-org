<?php
/**
 * WPML_View Class
 *
 * Extends Lim_View adding a factory method for direct chaining:
 *    - View::factory('test.php')
 *            ->set('var1', 'value1')
 *            ->show();
 *
 * @package  WPML
 * @category WordPress Plugins
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  MIT license
 */
class WPML_View extends Lim_View {

    /**
     * Factory method for creating new instance
     * @param string $file
     * @return View
     */
    public static function factory($file = null) {
        return new WPML_View($file);
    }

} // WPML_View Class
