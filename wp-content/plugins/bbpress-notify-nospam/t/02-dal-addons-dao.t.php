<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 * @group bbPress_Notify_noSpam_dal
 * @group bbPress_Notify_noSpam_dal_addons_dao
 */
require_once( 'bbPress_Notify_noSpam_Child.class.php' );

class Tests_bbPress_Notify_noSpam_DAL_Addons_DAO extends WP_UnitTestCase {

	public $child;
	
	function setUp()
	{
		parent::setUp();
		
		$this->child = new bbPress_Notify_noSpam_Child();
	}
	
	function test_get_products()
	{
		$dao = $this->child->load_lib('dal/addons_dao');
		$products = $dao->get_products( $force_reload=true );

		$this->assertTrue( is_array( $products ), 'Got an array back' );
		$this->assertNotEmpty( $products, 'And it is not empty' );
		
		$props = array( 'name', 'permalink', 'short_description', 'signature', 'version', 'image', 
				        'tested_up_to', 'last_updated', 'slug', 'is_active', 'is_installed', 'local' );
		
		foreach ($props as $prop) 
		{
			$this->assertTrue( property_exists( $products[0], $prop ), 'Found property ' . $prop );	
		}
	}
}

/* End of 02-dal-addons-dao.t.php */
/* Location: t/02-dal-addons-dao.t.php */
