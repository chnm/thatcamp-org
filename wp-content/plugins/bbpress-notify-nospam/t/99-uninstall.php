<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 * @group bbPress_Notify_noSpam_uninstall
 */

require_once( 'bbPress_Notify_noSpam_Child.class.php' );

class Tests_bbPress_Notify_noSpam_Uninstall extends WP_UnitTestCase {

	public $child;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->child = new bbPress_Notify_noSpam_Child();
	}
	
	public function test_plugin_settings_are_deleted()
	{
		// Add some tests to match your Uninstall file.
		
		$this->assertTrue( true );
		
		
	}
	
	
}

/* End of 01-uninstall.t.php */
/* Location: t/01-uninstall.t.php */
