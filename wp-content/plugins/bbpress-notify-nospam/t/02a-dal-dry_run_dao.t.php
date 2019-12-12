<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 * @group bbPress_Notify_noSpam_dal
 * @group bbPress_Notify_noSpam_dal_dry_run_dao
 */
require_once( 'bbPress_Notify_noSpam_Child.class.php' );

class Tests_bbPress_Notify_noSpam_DAL_Dry_Run_DAO extends WP_UnitTestCase {

	public $child;
	
	function setUp()
	{
		parent::setUp();
		
		$this->child = new bbPress_Notify_noSpam_Child();
	}
	
	function test_get_topics()
	{
	    /// SET UP DATA
	    $author = $this->factory->user->create( ['role' => 'author'] );
	    
	    $forum_id = bbp_insert_forum([
	            'post_title'  => 'test-forum',
	            'post_status' => 'publish'
	        ]);
	    
	    $this->assertTrue( is_numeric( $forum_id ), 'Forum inserted successfully' );
	    
	    foreach ( range( 1,3 ) as $i )
	    {
	        $title = $i . ' Test topic';
	        
	        $topic_id = bbp_insert_topic(
	            [
	                'post_parent'  => $forum_id,
	                'post_title'   => $title,
	                'post_content' => '',
	                'post_author'  => $author,
	            ],
	            [
	                'forum_id' => $forum_id
	            ]
	        );
	        
	        $this->assertTrue( is_numeric( $topic_id ), 'Topic inserted successfully' );
	    }
	    
	    // Create a post to be sure it's not returned with the topics.
	    $post_id = $this->factory->post->create( ['post_type' => 'post'] );
	    
	    /////////////////

	    
	    $dao = $this->child->load_lib( 'dal/dry_run_dao' );
	    
	    // No filtering
	    $topics = $dao->get_topics();
	    
	    $this->assertEquals( count($topics), 3, 'Got correct number of posts back' );
	    $this->assertTrue( ! isset( $topics[$post_id] ), 'Regular post is not included' );
	    
	    // WP Search filtering
	    $topics = $dao->get_topics([ 's' => '1 Test'] );
	    $this->assertEquals( count($topics), 1, 'Got one topic back');
	    
	    // Overriding post_type doesn't work.
	    $topics = $dao->get_topics([ 'post_type' => 'post', 'paged' => 1, 'posts_per_page' => 1 ] );
	    
	    $this->assertTrue( ! isset( $topics[$post_id] ), 'Could not override post_type' );
	}
	
	
	function test_get_replies()
	{
	    /// SET UP DATA
	    $author = $this->factory->user->create( ['role' => 'author'] );
	    
	    $forum_id = bbp_insert_forum([
	        'post_title'  => 'test-forum',
	        'post_status' => 'publish'
	    ]);
	    
	    $this->assertTrue( is_numeric( $forum_id ), 'Forum inserted successfully' );
	    
	    $topic_id = bbp_insert_topic(
	        [
	            'post_parent'  => $forum_id,
	            'post_title'   => 'Test topic',
	            'post_content' => '',
	            'post_author'  => $author,
	        ],
	        [
	            'forum_id' => $forum_id
	        ]
	    );
	    
	    $this->assertTrue( is_numeric( $topic_id ), 'Topic inserted successfully' );
	    
	    foreach ( range( 1,3 ) as $i )
	    {
	        $title = $i . ' Test reply';
	        
	        $reply_id = bbp_insert_reply(
	            [
	                'post_parent'  => $topic_id,
	                'post_title'   => $title,
	                'post_content' => '',
	                'post_author'  => $author,
	            ],
	            [
	                'forum_id' => $forum_id,
	                'topic_id' => $topic_id
	            ]
	            );
	        
	        $this->assertTrue( is_numeric( $reply_id ), 'Reply inserted successfully' );
	    }
	    
	    // Create a post to be sure it's not returned with the topics.
	    $post_id = $this->factory->post->create( ['post_type' => 'post'] );
	    
	    /////////////////
	    
	    $dao = $this->child->load_lib( 'dal/dry_run_dao' );
	    
	    // No filtering
	    $replies = $dao->get_replies();
	    
	    $this->assertEquals( count($replies), 3, 'Got correct number of posts back' );
	    $this->assertTrue( ! isset( $replies[$post_id] ), 'Regular post is not included' );
	    
	    // WP Search filtering
	    $replies = $dao->get_replies([ 's' => '1 Test'] );
	    $this->assertEquals( count($replies), 1, 'Got one topic back');
	    
	    // Overriding post_type doesn't work.
	    $replies = $dao->get_replies([ 'post_type' => 'post', 'paged' => 1, 'posts_per_page' => 1 ] );
	    
	    $this->assertTrue( ! isset( $replies[$post_id] ), 'Could not override post_type' );
	}
}

/* End of 02a-dal-dry_run_dao.t.php */
/* Location: t/02a-dal-dry_run_dao.t.php */
