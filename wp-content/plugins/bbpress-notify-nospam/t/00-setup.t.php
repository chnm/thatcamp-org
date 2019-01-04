<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 * @group bbPress_Notify_noSpam_setup
 */
require_once( 'bbPress_Notify_noSpam_Child.class.php' );

class Tests_bbPress_Notify_noSpam_Setup extends WP_UnitTestCase {

	public $child;
	
	function setUp()
	{
		parent::setUp();
		
		$this->child = new bbPress_Notify_noSpam_Child();
	}
	
	function test_plugin_loads()
	{
		$this->assertTrue( class_exists( 'bbPress_Notify_noSpam' ), 'The plugin was loaded' );
	}
	
	
	function test_environment()
	{
		$Boilerplate = bbPress_Notify_noSpam::bootstrap();
		
		$this->assertTrue( method_exists( $Boilerplate, 'get_env' ), 'get_env() method exists' );
		$this->assertFalse( is_callable( array( $Boilerplate, 'get_env' ) ), 'and it\'s not public' );
		
		$env = $this->child->get_env();
		
		$this->assertTrue( is_object( $env ), 'We get an env object' );
		
		foreach ( array( 'root_dir', 'inc_dir', 'tmpl_dir', 'js_url', 'css_url', 'img_url' ) as $prop )
		{
			$this->assertTrue( property_exists( $env, $prop ), sprintf( 'Required property %s exists', $prop ) );
			$this->assertTrue( isset( $env->{$prop} ), sprintf( 'Required property %s is set', $prop ) );
			
			$path = ( 'url' === substr( $prop, -3 ) ) ? 
					str_replace( get_site_url() . '/', ABSPATH, $env->{$prop} ) : 
					$env->{$prop};

			$this->assertTrue( is_dir( $path ), sprintf( 'Directory for %s exists', $prop ) );
		}
	}
	
	
	function test_is_admin_method()
	{
		$Boilerplate = bbPress_Notify_noSpam::bootstrap();
	
		$this->assertTrue( method_exists( $Boilerplate, 'is_admin' ), 'We have a custom is_admin' );
	
		$this->assertTrue( is_callable( array( $Boilerplate, 'is_admin' ) ), 'And we can call it' );
	
		add_filter( get_parent_class( $this->child ) . '_is_admin', '__return_true' );

		$this->assertTrue( $Boilerplate::is_admin(), 'Filter works and returns true' );
	
		remove_all_filters( get_parent_class( $this->child ) . '_is_admin' );
	
		add_filter( get_parent_class( $this->child ) . '_is_admin', '__return_false' );
	
		$this->assertFalse( $Boilerplate::is_admin(), 'Filter works and returns false' );
	}
	
	
	function test_load_lib()
	{
		$Boilerplate = bbPress_Notify_noSpam::bootstrap();
		
		$this->assertTrue( method_exists( $Boilerplate, 'load_lib' ), 'load_lib() method exists' );
		$this->assertFalse( is_callable( array( $Boilerplate, 'load_lib' ) ), 'load_lib() is not public' );
		
		$lib = $this->child->load_lib( 'controller/settings' );
		$this->assertTrue( is_object( $lib ), 'Test lib is loaded' );
		$this->assertTrue( $lib instanceof bbPress_Notify_noSpam_Controller_Settings, 'lib is instance of the correct class' );
		
		$lib2 = $this->child->load_lib( 'controller/settings' );
		$this->assertTrue( $lib === $lib2, 'Got cached lib version' );
	}
	
	
	function test_render_template()
	{
		$Boilerplate = bbPress_Notify_noSpam::bootstrap();
		
		// Change tmpl_dir for testing
		$env = $this->child->get_env();
		$env->tmpl_dir = $env->root_dir . 't/data/';

		$this->assertTrue( is_dir( $env->tmpl_dir ), 'Testing tmpl_dir exists' );
		$this->assertTrue( file_exists( $env->tmpl_dir . '/template_for_test.tmpl.php' ), 'Template file exists' );
		
		$this->assertTrue( method_exists( $Boilerplate, 'render_template' ), 'render_template Method exists' );
		$this->assertFalse( is_callable( $Boilerplate, 'render_template' ), 'render_template Is not public' );
		
		$stash = array( 'foo' => 'foo', 'bar' => 'bar' );
		$this->expectOutputString( 'This is a test template having foo = foo and bar = bar' );
		$this->child->render_template( 'template_for_test', $stash );
	}
	
}

/* End of 00-setup.t.php */
/* Location: t/00-setup.t.php */
