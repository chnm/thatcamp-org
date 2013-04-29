<?php
/**
 * Register custom Twitter widgets.
*/
global $twitter_username;
global $twitter_tweetcount;
$twitter_username = '';
$twitter_tweetcount = 1;

class Graphene_Widget_Twitter extends WP_Widget{
	
	function Graphene_Widget_Twitter(){
		// Widget settings
		$widget_ops = array( 'classname' => 'graphene-twitter', 'description' => __( 'Display the latest Twitter status updates.', 'graphene' ) );
		
		// Widget control settings
		$control_ops = array( 'id_base' => 'graphene-twitter' );
		
		// Create the widget
		$this->WP_Widget( 'graphene-twitter', 'Graphene Twitter', $widget_ops, $control_ops);
		
		/* Enqueue the twitter script if widget is active */
		if ( is_active_widget( false, false, $this->id_base, true ) && ! is_admin() )
			wp_enqueue_script( 'graphene-twitter', get_template_directory_uri() . '/js/twitter.js', array(), '', false );
	}
	
	function widget( $args, $instance ){		// This function displays the widget
		extract( $args );

		// User selected settings
		global $twitter_username;
		global $twitter_tweetcount;
		global $twitter_followercount;
		global $twitter_hide_replies;
		global $graphene_twitter_newwindow;
		$twitter_title = graphene_icl_t( $args['widget_id'], $instance['twitter_title'] );
		$twitter_username = $instance['twitter_username'];
		$twitter_tweetcount = $instance['twitter_tweetcount'];
		$twitter_followercount = $instance['twitter_followercount'];
		$twitter_hide_replies = ( array_key_exists( 'twitter_hide_replies', $instance ) ) ? $instance['twitter_hide_replies'] : false ;
		$new_window = $instance['new_window'];
		$graphene_twitter_newwindow = $new_window;
		$wrapper_id = 'tweet-wrap-' . $args['widget_id'];
		
		$follower_count_attr = ( $twitter_followercount ) ? 'data-show-count="true"' : 'data-show-count="false"';
		$hide_replies_attr = ( $twitter_hide_replies ) ? 'exclude_replies=true' : 'exclude_replies=false';
		
		echo $args['before_widget'].$args['before_title'].$twitter_title.$args['after_title'];
		?>
        	<ul id="<?php echo $wrapper_id; ?>">
            	<li><img src="<?php echo get_template_directory_uri(); ?>/images/ajax-loader.gif" width="16" height="16" alt="" /> <?php _e( 'Loading tweets...', 'graphene' ); ?></li>
            </ul>
            <p id="tweetfollow">
            	<a href="https://twitter.com/<?php echo $twitter_username; ?>" class="twitter-follow-button" <?php echo $follower_count_attr; ?> data-width="100%" data-align="right"><?php printf( __( 'Follow %s', 'graphene' ), '@' . $twitter_username ); ?></a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </p>
            
            <script src="http://api.twitter.com/1/statuses/user_timeline.json?screen_name=<?php echo $twitter_username; ?>&count=<?php echo $twitter_tweetcount; ?>&page=1&include_rts=true&<?php echo $hide_replies_attr; ?>&include_entities=true&callback=grapheneGetTweet" type="text/javascript"></script>
            <script type="text/javascript">				
				grapheneTwitter( '<?php echo $wrapper_id; ?>', 
									{
										<?php if ( $new_window ) echo 'newwindow: true,' ?>
										id: '<?php echo $twitter_username; ?>',
										count: <?php echo $twitter_tweetcount; ?>
									});
			</script>
            
            <?php do_action( 'graphene_twitter_widget' ); ?>
        <?php echo $args['after_widget']; ?>
        
        <?php
		// add_action( 'wp_footer', 'graphene_add_twitter_script' );
	}
	
	function update( $new_instance, $old_instance ){	// This function processes and updates the settings
		$instance = $old_instance;
		
		// Strip tags (if needed) and update the widget settings
		$instance['twitter_username'] = strip_tags( $new_instance['twitter_username']);
		$instance['twitter_tweetcount'] = strip_tags( $new_instance['twitter_tweetcount']);
		$instance['twitter_title'] = strip_tags( $new_instance['twitter_title'] );
		$instance['twitter_followercount'] = ( isset( $new_instance['twitter_followercount'] ) ) ? true : false ;
		$instance['twitter_hide_replies'] = ( isset( $new_instance['twitter_hide_replies'] ) ) ? true : false ;
		$instance['new_window'] = ( isset( $new_instance['new_window'] ) ) ? true : false ;
		
		graphene_wpml_register_strings( array( 'value' => $instance['twitter_title'], 'name' => $this->id ) );
		
		return $instance;
	}
	
	function form( $instance ){		// This function sets up the settings form
		
		// Set up default widget settings
		$defaults = array( 'twitter_username' => 'username',
						'twitter_tweetcount' => 5,
						'twitter_title' => __( 'Latest tweets', 'graphene' ),
						'twitter_followercount' => false,
						'twitter_hide_replies' => false,
						'new_window' => false,
						);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
        <p>
        	<label for="<?php echo $this->get_field_id( 'twitter_title' ); ?>"><?php _e( 'Title:', 'graphene' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'twitter_title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'twitter_title' ); ?>" value="<?php echo $instance['twitter_title']; ?>" class="widefat" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id( 'twitter_username' ); ?>"><?php _e( 'Twitter Username:', 'graphene' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'twitter_username' ); ?>" type="text" name="<?php echo $this->get_field_name( 'twitter_username' ); ?>" value="<?php echo $instance['twitter_username']; ?>" class="widefat" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id( 'twitter_tweetcount' ); ?>"><?php _e( 'Number of tweets to display:', 'graphene' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'twitter_tweetcount' ); ?>" type="text" name="<?php echo $this->get_field_name( 'twitter_tweetcount' ); ?>" value="<?php echo $instance['twitter_tweetcount']; ?>" size="1" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id( 'twitter_followercount' ); ?>"><?php _e( 'Show followers count', 'graphene' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'twitter_followercount' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'twitter_followercount' ); ?>" value="true" <?php checked( $instance['twitter_followercount'] ); ?> />
        </p>
         <p>
        	<label for="<?php echo $this->get_field_id( 'twitter_hide_replies' ); ?>"><?php _e( 'Hide @replies', 'graphene' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'twitter_hide_replies' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'twitter_hide_replies' ); ?>" value="true" <?php checked( $instance['twitter_hide_replies'] ); ?> /><br />
			<span class="description"><?php $showtweetcount = $instance['twitter_tweetcount']; printf( __('Note: Selecting this sometimes result in showing less than %s tweets', 'graphene' ), $showtweetcount ); ?></span>
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id( 'new_window' ); ?>"><?php _e( 'Open links in new window', 'graphene' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'new_window' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'new_window' ); ?>" value="true" <?php checked( $instance['new_window'] ); ?> />
        </p>
        <?php
	}
}


/**
 * Register the custom widget by passing the graphene_load_widgets() function to widgets_init
 * action hook.
*/ 
function graphene_load_widgets(){
	register_widget( 'Graphene_Widget_Twitter' );
}
add_action( 'widgets_init', 'graphene_load_widgets' );
?>
