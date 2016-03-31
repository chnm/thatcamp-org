<?php

/**
 * Example snippets to test whether all actions and filters are found & displayed in the
 * Debug Bar Actions and Filters plugin
 *
 * @since 1.4
 *
 * @todo: tests need to be added with examples for type 1, 3 and 8 in the callback if routine
 */

function simple_string_function_example( $content ) {
	return $content;
}

class testClass {
	public function __construct() {
		// Type 5: static class method (string)
		add_filter( 'the_content', __CLASS__ . '::static_method_added_from_constructor_as_string' );

		// Type 6: static class method (array )
		add_filter( 'the_content', array( __CLASS__, 'static_method_added_from_constructor_as_array' ) );

		// Type 7: object method
		add_filter( 'the_content', array( $this, 'object_method_added_from_constructor' ) );
	}

	public function object_method_added_from_constructor( $content ) {
		return $content;
	}

	public function object_method_added_outside_class( $content ) {
		return $content;
	}

	public static function static_method_added_from_constructor_as_string( $content ) {
		return $content;
	}

	public static function static_method_added_from_constructor_as_array( $content ) {
		return $content;
	}

	public static function static_method_added_outside_class_as_string( $content ) {
		return $content;
	}

	public static function static_method_added_outside_class_as_array( $content ) {
		return $content;
	}
}

// Closure assigned to var
$test_closure = function ( $content ) {
	return $content;
};

/** The actual tests **/

// Type 2: Closure
add_filter( 'the_content', function ( $content ) {
	return $content;
} );

// Type 2: Closure assigned to var
add_filter( 'the_content', $test_closure );

// Type 4: String function callback
add_filter( 'the_content', 'simple_string_function_example' );

// Type 4: Lambda
add_filter( 'the_content', create_function( '$content', 'return $content;' ) );

// Type 5: Static object method - string callback
add_filter( 'the_content', 'testClass::static_method_added_outside_class_as_string' ); // PHP 5.2.3+

// Type 6: Static object method - array callback
add_filter( 'the_content', array( 'testClass', 'static_method_added_outside_class_as_array' ) );

// Type 7: Object method with instantiated object
$obj = new testClass();
add_filter( 'the_content', array( $obj, 'object_method_added_outside_class' ) );

// Include namespace tests PHP5.3+
include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'callback_type_with_namespace_tests.php' );

?>