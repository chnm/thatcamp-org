<?php
/**
 * @group bbpnns
 * @group bbPress_Notify_noSpam
 * @group bbPress_Notify_noSpam_controller
 * @group bbPress_Notify_noSpam_controller_login
 */
require_once( 'bbPress_Notify_noSpam_Child.class.php' );

class Tests_bbPress_Notify_noSpam_Controller_Login extends WP_UnitTestCase {

    public $child;
    public $ctl;
    public $topics;
    public $forums;
    
    function setUp()
    {
        parent::setUp();

        $this->child = new bbPress_Notify_noSpam_Child();
        
        $this->ctl = $this->child->load_lib( 'controller/login' );
        
        $user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
        
        // Create new forums
        $this->forums[] = bbp_insert_forum(
            array(
                'post_title'  => 'test-public-forum',
                'post_status' => 'publish'
            )
        );
        
        $this->forums[] = bbp_insert_forum(
            array(
                'post_title'  => 'test-private-forum',
                'post_status' => 'private'
            )
        );
        
        foreach ( $this->forums as $f_id )
        {
            // Create new topic
            $this->topics[$f_id][] = bbp_insert_topic(
                array(
                    'post_parent'  => $f_id,
                    'post_title'   => 'test-topic',
                    'post_content' => 'Test topic content',
                    'post_author'  => $user_id,
                ),
                array(
                    'forum_id' => $f_id
                )
            );
        }
    }
    
    
    public function test_filters()
    {
        $this->assertTrue( (bool) has_filter( 'bbpnns_topic_url', array( $this->ctl, 'maybe_add_redirect' ) ), 'topic maybe_add_redirect filter exists' );
        $this->assertTrue( (bool) has_filter( 'bbpnns_reply_url', array( $this->ctl, 'maybe_add_redirect' ) ), 'reply maybe_add_redirect filter exists' );
        $this->assertTrue( (bool) has_filter( 'bbpnns_topic_reply', array( $this->ctl, 'maybe_add_redirect' ) ), 'reply maybe_add_redirect filter exists' );
        $this->assertEquals( 100001, has_filter( 'template_redirect', array( $this->ctl, 'maybe_handle_login' ) ), 'reply maybe_add_redirect filter exists and has correct priority' );
    }
    
    
    public function test_links()
    {
        foreach ($this->forums as $forum_id)
        {
            $visibility = bbp_get_forum_visibility( $forum_id );
            
            $post_id = reset($this->topics[$forum_id]);
            
            $url = $this->ctl->maybe_add_redirect( 'unchanged', $post_id, 'test-title', $forum_id );
            
            if ( 'publish' === $visibility )
            {
                $this->assertEquals( 'unchanged', $url, 'Got unchanged URL' );
            }
            else
            {
                $this->assertRegexp('@\?bbpnns-login=1&redirect_to=.*?@', $url, 'Got correct login URL' );
            }
        }
        
        // Test reply-url
        $post_id   = reset($this->topics[$forum_id]);
        $reply_url = bbp_get_reply_url( $post_id );
        $reply_url = $this->ctl->maybe_add_redirect( $reply_url, $post_id, 'test-title' );
        
        $this->assertRegexp('@\?bbpnns-login=1&redirect_to=.*?@', $url, 'Got correct login URL' );
    }
    
    
    public function test_redirect_url()
    {
        $hidden = '';
        
        foreach( $this->forums as $forum_id )
        {
            if ( 'publish' === bbp_get_forum_visibility( $forum_id ) )
            {
                $hidden = $forum_id;
                break;
            }
        }
        
        $post_id = reset( $this->topics[$hidden] );
        
        $_GET['bbpnns-login'] = 1;
        $_GET['redirect_to'] = esc_url( get_permalink( $post_id ) );
        
        add_filter( 'BBPNNS_TESTING', '__return_true' );
        
        wp_set_current_user(0);
        
        $would_redirect_to = $this->ctl->maybe_handle_login();
        
        $this->assertRegexp( '@/wp-login.php\?redirect_to=http%3A%2F%2Fwp_plugins%2F.*?@', $would_redirect_to );

        $user_id = $this->factory->user->create();
        
        wp_set_current_user($user_id);

        $would_redirect_to = $this->ctl->maybe_handle_login();
        
        $this->assertRegexp( '@http://wp_plugins/.*@', $would_redirect_to );

        remove_all_filters( 'BBPNNS_TESTING' );
    }
}

/* End of 04-controller-login.t.php */
/* Location: t/04-controller-login.t.php */
