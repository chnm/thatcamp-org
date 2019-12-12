<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Datalayer for the Dry Run tool
 * @author vinnyalves
 */
class bbPress_Notify_noSpam_DAL_Dry_Run_Dao extends bbPress_Notify_noSpam {
	

	public function __construct() 
	{
		// NOOP - we don't want PHP to call the parent automatically
	}
	
	/**
	 * Query the database for topics given user provided search string
	 * @param array $args
	 * @return array
	 */
	public function get_topics( $args=[] )
	{
	    global $wpdb;

	    $post_type = $this->get_topic_post_type();
	    
	    $defaults = [
	        's'                   => '',
	        'posts_per_page'      => -1,
	        'paged'               => 1,
	        'ignore_sticky_posts' => false,
	    ];
	    
	    // Let people change the args...
	    $args = apply_filters( 'bbpnns/dal/dry_run_dao/get_topics', $args );
	    
	    // ...but weed out any non-supported values they may have added,
	    $args = shortcode_atts( $defaults, $args );

	    // and force post_type to be topics
	    $args['post_type'] = $post_type;
	    
	    return $this->_get_posts( $args, $want_parents = true );
	}
	
	/**
	 * Query the database for replies to a topic given a user provided search string
	 * @param array $args
	 * @return array
	 */
	public function get_replies( $args=[] )
	{
	    global $wpdb;
	    
	    $post_type = $this->get_reply_post_type();
	    
	    $defaults = [
	        's'                   => '',
	        'posts_per_page'      => -1,
	        'paged'               => 1,
	        'ignore_sticky_posts' => false,
	    ];
	    
	    // Let people change the args...
	    $args = apply_filters( 'bbpnns/dal/dry_run_dao/get_replies', $args );
	    
	    // ...but weed out any non-supported values they may have added,
	    $args = shortcode_atts( $defaults, $args );
	    
	    // and force post_type to be topics
	    $args['post_type'] = $post_type;
	    
	    return $this->_get_posts( $args, $want_parents = true );
	}
	
	/**
	 * Does the actual querying and formats the result.
	 * @see https://developer.wordpress.org/reference/functions/get_posts/
	 * @param array $args
	 * @return array
	 */
	private function _get_posts( $args, $want_parents = false )
	{
	    // Run the query
	    $posts = get_posts( $args );
	    
	    $full_posts = $results = [];
	    foreach ( (array) $posts as $post )
	    {
	        $title = $post->post_title ? $post->post_title : __('No title', 'bbPress_Notify_noSpam' );
	        $results[$post->ID]    = sprintf( __( '%s, %s ID %d', 'bbPress_Notify_noSpam' ), $title, ucfirst($post->post_type), $post->ID );
	        $full_posts[$post->ID] = $post;
	    }
	    
	    if( true === $want_parents )
	    {
    	    $forums = [];
	        foreach( $results as $id => $title )
	        {
	            $post      = $full_posts[$id];
	            $parent_id = $post->post_parent;
	            
	            if ( ! isset( $parents[$parent_id] ) )
	            {
	                $parents[$parent_id] = $this->get_topic_post_type() === $post->post_type ? bbp_get_forum_title( $parent_id ) : bbp_get_topic_title( $parent_id );
	            }
	            
	            $results[$id] = sprintf( '%s > %s', $parents[$parent_id], $results[$id] );
	        }
	    }
	    
	    return $results;
	}
	
}


/* End of file dry_run_dao.class.php */
/* Location: bbpress-notify-nospam/includes/dal/dry_run_dao.class.php */
