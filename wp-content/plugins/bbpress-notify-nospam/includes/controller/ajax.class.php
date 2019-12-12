<?php defined('ABSPATH') or die("No direct access allowed");
/**
 * Controls ajax requests.
 * 
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_Controller_Ajax extends bbPress_Notify_noSpam {

	private $ar;
	
	public function __construct()
	{
		if ( ! parent::is_admin() )
			return;
	
		$this->load_lib('model/ajax_request');
	
		add_action('wp_ajax_bbpnns_update_db', array( $this, 'update_db' ) );
		add_action('wp_ajax_bbpnns_dry_run_fetch_posts', array( $this, 'fetch_posts' ) );
		add_action('wp_ajax_bbpnns_dry_run_run_test', array( $this, 'run_dry_run' ) );
	}
	
	public function run_dry_run()
	{
	    $params = array(
	        'post_type'      => '',
	        'post_id'        => '',
	        'nonce'          => '',
	    );
	    
	    // _init creates the model and helps with testing
	    $this->_init( $params, 'POST', $callback );
	    
	    $settings = $this->load_lib('dal/settings_dao')->load();
	    
	    $this->ar->is_success = false;
	    try {
	        $nonce = $params['nonce'];
	        
	        if ( ! wp_verify_nonce( $nonce, 'dry-run-test-nonce' ) )
	        {
	            throw new Exception( __( 'Invalid nonce', 'bbPress_Notify_noSpam' ) );
	        }
	        
	        
	         // Stop bbpress from sending anything.
	        add_filter( 'bbp_forum_subscription_mail_message', '__return_false' );
	        add_filter( 'bbp_subscription_mail_message', '__return_false' );
	        
	        remove_action( 'bbp_new_reply', 'bbp_notify_topic_subscribers', 11 );
	        remove_action( 'bbp_new_topic', 'bbp_notify_forum_subscribers', 11 );
	        
	        // Turn on dry_run
	        add_filter( 'bbpnns_dry_run', '__return_true', PHP_INT_MAX );
	        
	        $anonymous_data = [];
	        // Trigger new post
	        if ( $this->get_topic_post_type() === $params['post_type'] )
	        {
	            $topic_id = $params['post_id'];
	            $forum_id = bbp_get_topic_forum_id( $topic_id );
	            $topic_author = bbp_get_topic_author_id( $topic_id );
	            
	            do_action( 'bbp_new_topic', $topic_id, $forum_id, $anonymous_data, $topic_author );
	            
	            if ( $settings->background_notifications )
	            {
	                do_action( 'bbpress_notify_bg_topic', $topic_id, $forum_id, $anonymous_data, $topic_author );
	            }
	        }
	        else
	        {
	            $reply_id = $params['post_id'];
	            $topic_id = bbp_get_reply_topic_id( $reply_id );
	            $forum_id = bbp_get_topic_forum_id( $topic_id );
	            $reply_author = bbp_get_reply_author_id( $reply_id );
	            
	            do_action( 'bbp_new_reply', $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author  );
	            
	            
	            if ( $settings->background_notifications )
	            {
	                do_action( 'bbpress_notify_bg_reply', $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author );
	            }
	        }
	        
	        // Read the trace
	        $trace = apply_filters( 'bbpnns_dry_run_trace_info', [] );
	        
	        $this->ar->is_success = true;
	        $this->ar->data = $trace;
	    
	    }
	    catch( Exception $e )
	    {
	        // Set the model values
	        $this->ar->is_success = false;
	        $this->ar->msg = $e->getMessage();
	    }
	    
	    return $this->_done();
	}
	
	
	/**
	 * Fetch topics for Dry-run
	 */
	public function fetch_posts()
	{
	    $params = array(
	        's'              => '',
	        'posts_per_page' => -1,
	        'paged'          => 1,
	        'nonce'          => '',
	        'post_type'      => '',
	    );
	    
	    // _init creates the model and helps with testing
	    $this->_init( $params, 'POST', $callback );
	    
	    
	    $this->ar->is_success = true;
	    try { 
	        $nonce = $params['nonce'];
	        
	        if ( ! wp_verify_nonce( $nonce, 'dry-run-post-nonce' ) )
	        {
	            throw new Exception( __( 'Invalid nonce', 'bbPress_Notify_noSpam' ) );
	        }
	        
	        $dao = $this->load_lib( 'dal/dry_run_dao' );
	       
	        $posts = [];
	        
	        if ( $this->get_topic_post_type() === $params['post_type'] )
	        {
    	        $posts = $dao->get_topics( [
    	            's'              => $params['s'],
    	            'posts_per_page' => $params['posts_per_page'],
    	            'paged'          => $params['paged'],
    	        ] );
	        }
	        else
	        {
	            $posts = $dao->get_replies( [
	                's'              => $params['s'],
	                'posts_per_page' => $params['posts_per_page'],
	                'paged'          => $params['paged'],
	            ] );
	        }
	        
	        $results = [ 'results' => [], 'pagination' => [ 'more' => false ] ];
	        
	        if ( ! empty( $posts ) )
	        {
	            foreach ( $posts as $id => $title )
	            {
	                $results['results'][] = [ 'id' => $id, 'text' => $title ];
	            }
	            
	            $results['total_count'] = count($posts);
	        }
	        
	        $this->ar->is_success = true;
	        $this->ar->data = $results;
	        
	    }
	    catch( Exception $e )
	    {
	        // Set the model values
	        $this->ar->is_success = false;
	        $this->ar->msg = $e->getMessage();
	    }
	    
	    // And print out the response
	    return $this->_done();
	}
	
	
	/**
	 * Update the settings to version 2
	 */
	public function update_db( $message='', $callback=null )
	{
		$params = array(
            'message'  => &$message,
		    'nonce'    => '',
		);
		
		// _init creates the model and helps with testing
		$this->_init($params, 'POST', $callback);
		
		try { 
			
			$nonce = $params['nonce'];
			
			if ( ! wp_verify_nonce( $nonce, 'bbpnns_v2_conversion_needed' ) )
			{
				throw new Exception( __( 'Invalid nonce', 'bbPress_Notify_noSpam' ) );
			}
			
			$conv = $this->load_lib( 'helper/converter', array( 'add_action' => false ) );
			if ( ! $conv->do_db_upgrade() )
			{
				throw new Exception( __( 'There was a problem updating the database.', 'bbPress_Notify_noSpam' ) );
			}
			
			
			// Set the model values
			$this->ar->is_success = true;
			$this->ar->msg = __( '<strong>Database update completed successfully!</strong>', 'bbPress_Notify_noSpam' );
// 			$data = (object) array( 'some data' => true );
// 			$this->ar->data = $data;
		}
		catch (Exception $e)
		{
			// If there was an error, set it accordingly 
			$this->ar->is_success = false;
			$this->ar->msg = $e->getMessage();
// 			$this->ar->data = null;
		}

		// And print out the response
		return $this->_done();
	}
	
	
	
	
	
	/**
	 * Wrapper to check if we're in an ajax call
	 * @return boolean
	 */
	private function _doing_ajax()
	{
		return (defined('DOING_AJAX') && DOING_AJAX);
	}
	
	/**
	 * Wrapper to fetch query params
	 * @param array $vars
	 * @param string $method
	 * @param string $callback
	 */
	private function _init( &$vars=array(), $method='POST', &$callback=null)
	{
		$this->ar = new bbPress_Notify_noSpam_Model_Ajax_Request();
		$params   = array();
		
		if ( 'GET' === $method && isset( $_GET ) ) 
		{
			$params = $_GET;
		}
		elseif( 'POST' === $method && isset($_POST) ) 
		{
			$params = $_POST;
		}

		if ( isset($params) ) 
		{
			$vars = shortcode_atts( $vars, $params );
	
			if ( isset( $params['callback'] ) ) 
			{
				$callback = trim($params['callback']);
			}
		}
		
		$this->ar->callback = $callback;
	}
	
	
	/**
	 * Output or return Ajax Request model
	 */
	private function _done()
	{
		if ( $this->_doing_ajax() )
		{
			$this->ar->output();
			wp_die();
		}
		
		ob_start();
		$this->ar->output();
		return ob_get_clean();
	}
	
}

/* End of file ajax.class.php */
/* Location: bbpress-notify-nospam/includes/controller/ajax.class.php */
