<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Controls login functionality for private forums.
 * 
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_Controller_Login extends bbPress_Notify_noSpam {

    private $forums = array();
    
    #############################
    
    public function __construct()
    {
        add_action( 'template_redirect', array( $this, 'maybe_handle_login' ), 100001 );
        
        add_filter( 'bbpnns_topic_url', array( $this, 'maybe_add_redirect' ), 10, 4 );
        add_filter( 'bbpnns_reply_url', array( $this, 'maybe_add_redirect' ), 10, 4 );
        add_filter( 'bbpnns_topic_reply', array( $this, 'maybe_add_redirect' ), 10, 3 );
    }
    
    
    /**
     * If necessary, sends users to the login URL with a redirect to wherever they wanted to go.
     */
    public function maybe_handle_login()
    {
        if ( isset( $_GET['bbpnns-login'] ) )
        {
            if ( ! is_user_logged_in() )
            {
                $redirect_to = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
                
                $login_url = apply_filters( 'bbpnns-login-url', wp_login_url( $_GET['redirect_to'] ), $_GET );
                
                if ( true === apply_filters( 'BBPNNS_TESTING', false ) )
                {
                    return $login_url;
                }
                    
                wp_safe_redirect( $login_url );
                exit();
            }
            else
            {
                if ( true === apply_filters( 'BBPNNS_TESTING', false ) )
                {
                    return $_GET['redirect_to'];
                }
                
                wp_safe_redirect( $_GET['redirect_to'] );
                exit();
            }
        }
    }
    
    
    /**
     * Check if a login URL is required
     * @param string $esc_url
     * @param int $forum_id
     * @param WP_Post $post
     * @return string
     */
    public function maybe_add_redirect( $url, $post_id, $title, $forum_id='' )
    {
        if ( ! $forum_id )
        {
            $forum_id = get_post_meta( $post_id, '_bbp_forum_id', true );
        }
        
        if ( ! isset( $this->forums[$forum_id] ) )
        {
            $this->forums[$forum_id] = bbp_get_forum_visibility( $forum_id );
        }
        
        if ( 'publish' !== $this->forums[$forum_id] )
        {
            $url = esc_url_raw( add_query_arg( array('bbpnns-login' => 1, 'redirect_to' => esc_url( $url ) ), home_url( '/' ) ) );
        }
        
        return $url;
    }
    
}

/* End of file login.class.php */
/* Location: bbpress-notify-nospam/includes/controller/login.class.php */
