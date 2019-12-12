<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 * @group bbPress_Notify_noSpam_model
 * @group bbPress_Notify_noSpam_model_settings
 */
require_once( 'bbPress_Notify_noSpam_Child.class.php' );

class Tests_bbPress_Notify_noSpam_Model_Settings extends WP_UnitTestCase {

	public $child;
	
	function setUp()
	{
		parent::setUp();
		
		$this->child = new bbPress_Notify_noSpam_Child();
	}
	
	function test_construct()
	{
		$m = $this->child->load_lib( 'model/settings' );
		
		$this->assertEquals( 'bbPress_Notify_noSpam_Model_Settings', get_class($m), 'Got the right class name' );
	}
	
	function test_properties()
	{
		$m = $this->child->load_lib( 'model/settings' );
		
		$keys = array_keys( $m->option_keys );
		
		$this->assertNotEmpty( $keys, 'Option Keys is populated' );
		
		$reflect = new ReflectionClass( $m );
		
		$props = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);
		
		foreach( $props as $prop )
		{
			$this->assertTrue( in_array( $prop->name, $keys ), "{$prop->name} is in \$keys" );
		}
		
		foreach ( $keys as $key )
		{
			$found_prop = false;
			
			foreach ( $props as $prop )
			{
				if ( $key === $prop->name )
				{
					$found_prop = true;
					break;
				}
			}
			
			$this->assertTrue( $found_prop, 'Found private property ' . $key );
		}
	}
	
	function test_defaults()
	{
		$m = $this->child->load_lib( 'model/settings' );
		
		$false_vals = array( 'encode_subject',                      'newtopic_background',                 'newreply_background', 
							 'default_topic_notification_checkbox', 'default_reply_notification_checkbox',
						     'override_bbp_forum_subscriptions',    'override_bbp_topic_subscriptions',
							 'notify_authors_topic',                'notify_authors_reply',                'hidden_forum_topic_override' );
		foreach ( $false_vals as $prop )
		{
			$this->assertFalse( $m->{$prop}, 'Got false for prop ' . $prop );
		}
		

		$this->assertEquals( $m->newtopic_email_subject, __( '[[blogname]] New topic: [topic-title]', $this->child->domain ), 'Good default for topic email subject' );
		$this->assertEquals( $m->newreply_email_subject, __( '[[blogname]] New reply for [topic-title]', $this->child->domain ), 'Good default for reply email subject' );
		$this->assertEquals( $m->newtopic_email_body, __( "Hello!\nA new topic has been posted by [topic-author].\nTopic title: [topic-title]\nTopic url: [topic-url]\n\nExcerpt:\n[topic-excerpt]", $this->child->domain ), 'Good default for topic email body' );
		$this->assertEquals( $m->newreply_email_body, __( "Hello!\nA new reply has been posted by [reply-author].\nTopic title: [reply-title]\nTopic url: [reply-url]\n\nExcerpt:\n[reply-excerpt]", $this->child->domain ), 'Good default for reply email body' );
		
		$this->assertEquals( $m->email_type, 'html', 'Good default for message type' );
		$this->assertEquals( $m->newtopic_recipients, array(), 'Good empty/default for new topic recipients' );
		$this->assertEquals( $m->newreply_recipients, array(), 'Good empty/default for new reply recipients' );
	}
	
	function test_getter()
	{
		$m = $this->child->load_lib( 'model/settings' );
		
		// Check normalization after bad conversion.
		$m->newreply_recipients = array( 'administrator' => 'Administrator' );
		$this->assertEquals( $m->newreply_recipients, array( 'administrator' ), 'Normalization works for topic recipients' );
		
		$m->newtopic_recipients = array( 'administrator' => 'Administrator' );
		$this->assertEquals( $m->newtopic_recipients, array( 'administrator' ), 'Normalization works for reply recipients' );
		
		$m->newtopic_email_subject = 'Test topic subject with an entity: &#8211;'; 
		$m->newreply_email_subject = 'Test reply subject with an entity: &#8211;'; 
		
		$m->encode_subject = false;
		$this->assertEquals( $m->newtopic_email_subject, 'Test topic subject with an entity: &#8211;', 'Unchanged subject'); 
		$this->assertEquals( $m->newreply_email_subject, 'Test reply subject with an entity: &#8211;', 'Unchanged subject');
		
		$m->encode_subject = true;
		$this->assertEquals( $m->newtopic_email_subject, 'Test topic subject with an entity: –', 'De-entitized subject');
		$this->assertEquals( $m->newreply_email_subject, 'Test reply subject with an entity: –', 'De-entitized subject');
		
	}
	
	function test_setter_on_new()
	{
		$m = new bbPress_Notify_noSpam_Model_Settings( array( 'email_type' => 'plain') );

		$this->assertEquals( $m->email_type, 'plain' );
	}
	
	function test_validate()
	{
		$m = new bbPress_Notify_noSpam_Model_Settings();
		
		$bool_only = array( 'encode_subject', 'newtopic_background', 'newreply_background',
							'default_topic_notification_checkbox', 'default_reply_notification_checkbox',
							'override_bbp_forum_subscriptions', 'override_bbp_topic_subscriptions',
							'notify_authors_topic', 'notify_authors_reply', 'hidden_forum_topic_override' );
		
		foreach( $bool_only as $prop )
		{
			try{
				$m->{$prop} = 'foo';
			}
			catch( WPDieException $e )
			{
				$msg = $e->getMessage();
				$this->assertEquals( $msg, sprintf( 'Invalid value for %s', $m->option_keys[$prop] ), 'Bad value threw exception for ' . $prop );
			}
		}
		
		try {
			$m->email_type = 'foo';
		}
		catch( WPDieException $e )
		{
			$this->assertEquals( $e->getMessage(), sprintf( 'Invalid value for %s', $m->option_keys['email_type'] ), 'Bad value threw exception for message_type' );
		}
		
		
		foreach ( array( 'newtopic_recipients', 'newreply_recipients' ) as $prop )
		{
			try {
				$m->{$prop} = true;
			}
			catch( WPDieException $e )
			{
				$this->assertEquals( $e->getMessage(), sprintf( 'Invalid data type for %s', $m->option_keys[$prop] ), 'Bad value threw exception for ' . $prop );
			}
		}
		
		foreach ( array( 'newtopic_email_subject', 'newreply_email_subject', 'newtopic_email_body', 'newreply_email_body') as $prop )
		{
			try {
				$m->{$prop} = '       ';
			}
			catch( WPDieException $e )
			{
				$this->assertEquals( $e->getMessage(), sprintf( '%s cannot be empty!', $m->option_keys[$prop] ), 'Bad value threw exception for ' . $prop );
			}
		}
	}
	
	
	function test_as_array()
	{
		$m = new bbPress_Notify_noSpam_Model_Settings();
		
		$ary = $m->as_array();
		
		$this->assertTrue( is_array( $ary ), 'Got an array back' );
		
		foreach ( $ary as $key => $value )
		{
			if ( false !== strpos($key, ' ' )  )
			{
				$this->fail( 'Got a space in a key. Should not happen!' );
			}
		}
	}
}

/* End of 01-model-settings.t.php */
/* Location: t/01-model-settings.t.php */
