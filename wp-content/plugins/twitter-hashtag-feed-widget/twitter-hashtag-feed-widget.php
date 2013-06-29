<?php
/*
Plugin Name: Twitter Hashtag Feed Widget
Plugin URI: http://wordpress.org/extend/plugins/twitter-hashtag-feed-widget
Description: A sidebar widget that creates a simple, clean Twitter feed of a specified hashtag.
Version: 1.0.2
Author: Nick McLarty
Author URI: http://www.inick.net
License: GPL2
*/

/**
 * Add Twitter Hashtag Feed Widget to WordPress
 */

add_action( 'widgets_init', 'register_twitter_hashtag_feed_widget' );

function register_twitter_hashtag_feed_widget() {
	register_widget( 'Twitter_Hashtag_Feed_Widget' );
} // function register_twitter_hashtag_feed_widget

class Twitter_Hashtag_Feed_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */

	public function __construct() {
		parent::__construct(
			'Twitter_Hashtag_Feed_Widget',	// Base ID
			'Twitter Hashtag Feed Widget',	// Widget Name
			array( 'description' => __( 'Adds a widget with a Twitter feed using a hashtag search.', 'text_domain' ) )
		);
	} // public function __construct


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from the database.
	 * @return array Updated safe values to be saved.
	 */

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$args_list = array( 'title', 'hashtag', 'consumer_key', 'consumer_secret', 'oauth_token', 'oauth_token_secret', 'results' );

		foreach( $args_list as $arg ) {
			$instance[$arg] = strip_tags( $new_instance[$arg] );
		}

		return $instance;
	} // public function update


	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 * @param array $instance Previously saved values from the database.
	 */

	public function form( $instance ) {
		$args_list = array(	'title' => 'Title',
							'hashtag' => 'Twitter Hashtag to Search',
							'consumer_key' => 'Twitter Consumer Key',
							'consumer_secret' => 'Twitter Consumer Secret',
							'oauth_token' => 'Twitter OAuth Access Token',
							'oauth_token_secret' => 'Twitter OAuth Access Token Secret',
							'results' => 'Number of Results' );

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : __( 'New title', 'text_domain' );
		$hashtag = isset( $instance['hashtag'] ) ? esc_attr( $instance['hashtag'] ) : __( '#hashtag', 'text_domain' );
		$consumer_key = esc_attr( $instance['consumer_key'] );
		$consumer_secret = esc_attr( $instance['consumer_secret'] );
		$oauth_token = esc_attr( $instance['oauth_token'] );
		$oauth_token_secret = esc_attr( $instance['oauth_token_secret'] );
		$results = isset( $instance['results'] ) ? esc_attr( $instance['results'] ) : 5;

		foreach( $args_list as $arg_key => $arg_label ) {
	?>
	<p><label for="<?php echo $this->get_field_id( $arg_key ); ?>">
		<?php _e( $arg_label ); ?>: <input class="widefat" id="<?php echo $this->get_field_id( $arg_key ); ?>" 
			name="<?php echo $this->get_field_name( $arg_key ); ?>" type="text" value="<?php echo ${$arg_key}; ?>" /></label>
	</p>
	<?php 
		}
	} // public function form


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @params array $args Widget arguments.
	 * @params array $instance Saved values from database.
	 */

	public function widget( $args, $instance ) {
		extract( $args );

		$hash = md5( $instance['hashtag'] );	// Uniquely identify the hashtag being queried from Twitter
		if ( !$json = get_transient( 'twithashfeed-' . $hash ) ) {	// Prepare once, serve many
			require_once 'twitteroauth/twitteroauth.php';	// TwitterOAuth Library (courtesy of https://github.com/abraham/twitteroauth)

			$url = 'https://api.twitter.com/1.1/search/tweets.json';	// Twitter REST API v1.1 (https://dev.twitter.com/docs/api/1.1)

			$query = array(	'q' => $instance['hashtag'],
							'result_type' => 'recent',
							'count' => $instance['results'],
						);

			$conn = new TwitterOAuth( $instance['consumer_key'], $instance['consumer_secret'], 
				$instance['oauth_token'], $instance['oauth_token_secret'] );

			$content = $conn->get( $url, $query );

			set_transient( 'twithashfeed-' . $hash, json_encode( $content ), 60 );
		} else {
			$content = json_decode( $json );
		}

		echo $before_widget;

		if ( $instance['title'] ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
		}

		echo '<ul>';

		if ( $content->search_metadata->count > 0 ) {
			foreach( $content->statuses as $status ) {
				$status->text = preg_replace( '/(http[s]?:\/\/[^\s]*)/i', '<a href="$1">$1</a>', $status->text );	// Hyperlink any URLs
				$status->text = preg_replace( '/(^|\s)@([a-z0-9_]+)/i', '$1<a href="https://twitter.com/$2">@$2</a>', $status->text );	// Hyperlink @mentions
				$status->text = preg_replace( '/(^|\s)#([a-z0-9_]+)/i', '$1<a href="https://twitter.com/search?q=%23$2&src=hash">#$2</a>', $status->text );	// Hyperlink #hashes

				echo '<li>';
				echo '<a href="https://twitter.com/' . $status->user->screen_name . '" target="_blank">';
				echo "@{$status->user->screen_name}</a>: " . $status->text;
				echo '</li>';
			}
		} elseif ( $content->errors ) {
			$errors = new WP_Error();

			foreach( $content->errors as $error ) {
				$errors->add( $error->code, $error->message );
			}

			echo '<div class="error">' . $errors->get_error_message() . '</div>';
		} else {
			echo 'No results found.';
		}

		echo "</ul>" . $after_widget;

	} // public function widget

} // class Twitter_Hashtag_Feed_Widget
