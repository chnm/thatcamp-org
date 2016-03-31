<?php
/**
 * More example snippets to test with namespaced functions and classes
 *
 * @since 1.4
 */

namespace Foobar;

function Bar( $content ) {
	return $content;
}

class Foo {
	public function __construct() {
		// Type 5: static class method (string)
		add_filter( 'the_content', __CLASS__ . '::static_method_added_from_constructor_as_string' );

		// Type 6: static class method (array)
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

/* Testing with namespace */

// Type 4: String function callback
add_filter( 'the_content', __NAMESPACE__ . '\Bar' );

// Type 5: Static object method - string callback
add_filter( 'the_content', __NAMESPACE__ . '\Foo::static_method_added_outside_class_as_string' );

// Type 6: Static object method - array callback
add_filter( 'the_content', array( __NAMESPACE__ . '\Foo', 'static_method_added_outside_class_as_array' ) );

// Type 7: Object method with instantiated object
$ns_obj = new namespace\Foo();
add_filter( 'the_content', array( $ns_obj, 'object_method_added_outside_class' ) );

?>