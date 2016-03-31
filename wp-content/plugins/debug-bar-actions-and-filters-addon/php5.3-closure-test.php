<?php
/**
 * PHP 5.3+ functionality in a separate file
 *
 * @since 1.4.1
 * @author  subharanjan
 * @package debug-bar-actions-and-filters-addon
 * @version 1.4.1
 */
if ( ! function_exists( 'debug_bar_action_and_filters_addon_is_closure' ) ) {
	/**
	 * Function to check for closures
	 *
	 * @param   mixed $arg function name
	 *
	 * @return  boolean $closurecheck return whether or not a closure
	 */
	function debug_bar_action_and_filters_addon_is_closure( $arg ) {
		$test         = function () {
		};
		$closurecheck = ( $arg instanceof $test );

		return $closurecheck;
	}
}