<?php
/**
 * PayPal Donations Unit Tests
 */
class PayPalDonationsTest extends WP_UnitTestCase {

    private $plugin = 'paypal-donations';

    public function setUp() {
        parent::setUp();
    }

	// -------------------------------------------------------------------------
	// Tests
	// -------------------------------------------------------------------------

    function testPluginInitialization() {  
        // $this->assertFalse( null == $this->plugin );
    }
}
