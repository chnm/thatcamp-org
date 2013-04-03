<?php /* widget_cbnet_twitter */
/**
 * Plugin Name: cbnet Twitter Widget
 * Plugin URI: http://www.chipbennett.net/wordpress/plugins/cbnet-twitter-widget/
 * Description: A widget that displays tweets from any Twitter profile, list, favorites, or search.
 * Version: 1.3
 * Author: chipbennett
 * Author URI: http://www.chipbennett.net/
 *
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * Thanks to Justin Tadlock, as well as Otto and the other fine folks at
 * the WPTavern forum (www.wptavern.org/forum) for help with this plugin
 */
 
 /**
 * Load Plugin textdomain
 */
function cbnet_twitter_widget_load_textdomain() {
	load_plugin_textdomain( 'cbnet-twitter-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
// Load Plugin textdomain
add_action( 'plugins_loaded', 'cbnet_twitter_widget_load_textdomain' );

/**
 * Add function to widgets_init that'll load our widget.
 * @since 1.0
 */
add_action( 'widgets_init', 'cbnet_twitter_widget_load_widget' );

/**
 * Register cbnet Twitter Widget
 *
 * @since 1.0
 */
function cbnet_twitter_widget_load_widget() {
	register_widget( 'widget_cbnet_twitter_widget' );
}

/**
 * widget_cbnet_twitter_widget class.
 *
 * @since 1.0
 */
class widget_cbnet_twitter_widget extends WP_Widget {

    function widget_cbnet_twitter_widget() {
        $widget_ops = array('classname' => 'widget-cbnet-twitter-widget', 'description' => __( 'Widget to display Twitter widgets', 'cbnet-twitter-widget' ) );
        $this->WP_Widget('plugin_cbnet_twitter_widget', __( 'cbnet Twitter Widget', 'cbnet-twitter-widget' ), $widget_ops);
    }

    function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
		$widgettype =  $instance['widgettype'];
		$twitteruserid =  $instance['twitteruserid'];
		$twitteruserlist = $instance['twitteruserlist'];
		$twittersearch =  $instance['twittersearch'];
		$typetitle = $instance['typetitle'];
		$typedesc = $instance['typedesc'];
		$widgetsetuser = $instance['widgetsetuser'];
		$shellbg = $instance['shellbg'];
		$shellcolor = $instance['shellcolor'];
		$tweetbg = $instance['tweetbg'];
		$tweetcolor = $instance['tweetcolor'];
		$tweetlink = $instance['tweetlink'];
		$live = ($instance['live'] ? 'true' : 'false');
		$scrollbar = ($instance['scrollbar'] ? 'true' : 'false');
		$behavior = $instance['behavior'];
		$interval = $instance['interval'];
		$loop = ($instance['loop'] ? 'true' : 'false');
		$rpp = $instance['rpp'];
		$avatars = ($instance['avatars'] ? 'true' : 'false');
		$timestamp = ($instance['timestamp'] ? 'true' : 'false');
		$hashtags = ($instance['hashtags'] ? 'true' : 'false');
		$widthauto = ($instance['widthauto'] ? 'true' : 'false');
		$width =  ( $widthauto == 'true' ? "'auto'" : $instance['width']); 
		$height =  $instance['height']; 

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;
?>
<!-- Begin Twitter Profile -->
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: '<?php echo $widgettype; ?>',
  rpp: <?php echo $rpp; ?>,
  interval: <?php echo $interval; ?>,
  <?php if ( $instance['widgettype'] != 'profile' ) { echo "title: '" . $typetitle . "',\n"; } ?>
  <?php if ( $instance['widgettype'] != 'profile' ) { echo "subject: '" . $typedesc . "',\n"; } ?>
  <?php if ( $instance['widgettype'] == 'search' ) { echo "search: '" . $twittersearch . "',\n"; } ?>
  width: <?php echo $width; ?>,
  height: <?php echo $height; ?>,
  theme: {
    shell: {
      background: '<?php echo $shellbg; ?>',
      color: '<?php echo $shellcolor; ?>'
    },
    tweets: {
      background: '<?php echo $tweetbg; ?>',
      color: '<?php echo $tweetcolor; ?>',
      links: '<?php echo $tweetlink; ?>'
    }
  },
  features: {
    scrollbar: <?php echo $scrollbar; ?>,
    loop: <?php echo $loop; ?>,
    live: <?php echo $live; ?>,
    hashtags: <?php echo $hashtags; ?>,
    timestamp: <?php echo $timestamp; ?>,
    avatars: <?php echo $avatars; ?>,
    behavior: '<?php echo $behavior; ?>'
  }
}).render()<?php echo $widgetsetuser; ?>.start();
</script>
<br />
<!-- End Twitter List -->

<?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance = array( 'live' => 0, 'scrollbar' => 0, 'loop' => 0, 'avatars' => 0, 'timestamp' => 0, 'hashtags' => 0, 'widthauto' => 0);
		foreach ( $instance as $field => $val ) {
			if ( isset($new_instance[$field]) )
				$instance[$field] = '1';
		}
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['widgettype'] = $new_instance['widgettype'];
        $instance['twitteruserid'] = strip_tags($new_instance['twitteruserid']);
        $instance['twitteruserlist'] = strip_tags($new_instance['twitteruserlist']);
        $instance['twittersearch'] = strip_tags($new_instance['twittersearch']);
        $instance['typetitle'] = strip_tags($new_instance['typetitle']);
        $instance['typedesc'] = strip_tags($new_instance['typedesc']);
        $instance['widgetsetuser'] = '';
        if ( $instance['widgettype'] == 'profile' || $instance['widgettype'] == 'faves' ) {
			$instance['widgetsetuser'] = ".setUser('" . $instance['twitteruserid'] . "')";
        } elseif ( $instance['widgettype'] == 'list' ) {
			$instance['widgetsetuser'] = ".setList('" . $instance['twitteruserid'] . "', '" . $instance['twitteruserlist'] . "')";
        }
        $instance['shellbg'] = strip_tags($new_instance['shellbg']);
        $instance['shellcolor'] = strip_tags($new_instance['shellcolor']);
        $instance['tweetbg'] = strip_tags($new_instance['tweetbg']);
        $instance['tweetcolor'] = strip_tags($new_instance['tweetcolor']);
        $instance['tweetlink'] = strip_tags($new_instance['tweetlink']);
        $instance['behavior'] = $new_instance['behavior'];
        $instance['interval'] = $new_instance['interval'];
        $instance['rpp'] = $new_instance['rpp'];
        $instance['width'] = $new_instance['width'];
        $instance['height'] = $new_instance['height'];

        return $instance;
    }

    function form( $instance ) {
		$defaults = array( 'title' => '', 'widgettype' => 'profile', 'twitteruserid' => '', 'twitteruserlist' => '', 'twittersearch' => '',  'typetitle' => '',  'typedesc' => '', 'shellbg' => '#cccccc', 'shellcolor' => '#ffffff', 'tweetbg' => '#ffffff', 'tweetcolor' => '#444444', 'tweetlink' => '#5588aa', 'scrollbar' => 'true', 'loop' => 'false', 'live' => 'true', 'hashtags' => 'true', 'timestamp' => 'true', 'avatars' => 'true', 'behavior' => 'all', 'interval' => '6000', 'rpp' => '4', 'widthauto' => 'false', 'width' => '150', 'height' => '300' );
        $instance = wp_parse_args( (array) $instance, $defaults );
		?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title (Heading)', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
</p>
<p>
<strong><?php _e( 'Widget Type', 'cbnet-twitter-widget' ); ?>:</strong>
</p>
<p>
<label for="<?php echo $this->get_field_id('widgettype'); ?>"><?php _e( 'Widget Type', 'cbnet-twitter-widget' ); ?>:</label> 
<select name="<?php echo $this->get_field_name('widgettype'); ?>" style="width:100%;">
  <option <?php selected( 'profile' == $instance['widgettype'] ); ?> value="profile"><?php _e( 'Profile', 'cbnet-twitter-widget' ); ?></option>
  <option <?php selected( 'list' == $instance['widgettype'] ); ?> value="list"><?php _e( 'List', 'cbnet-twitter-widget' ); ?></option>
  <option <?php selected( 'faves' == $instance['widgettype'] ); ?> value="faves"><?php _e( 'Favorites', 'cbnet-twitter-widget' ); ?></option>
  <option <?php selected( 'search' == $instance['widgettype'] ); ?> value="search"><?php _e( 'Search', 'cbnet-twitter-widget' ); ?></option>
</select>
</p>
<p>
<strong><?php _e( 'General Settings', 'cbnet-twitter-widget' ); ?>:</strong>
</p>
<p>
<label for="<?php echo $this->get_field_id('twitteruserid'); ?>"><?php _e( 'Twitter User ID', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('twitteruserid'); ?>" name="<?php echo $this->get_field_name('twitteruserid'); ?>" type="text" value="<?php echo $instance['twitteruserid']; ?>" />
<small>(<?php _e( 'Note: This setting applies to Widget types "Profile", "List", and "Favorites"', 'cbnet-twitter-widget' ); ?>)</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('twitteruserlist'); ?>"><?php _e( 'Twitter List', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('twitteruserlist'); ?>" name="<?php echo $this->get_field_name('twitteruserlist'); ?>" type="text" value="<?php echo $instance['twitteruserlist']; ?>" />
<small>(<?php _e( 'Note: This setting applies to Widget type "List"', 'cbnet-twitter-widget' ); ?>)</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('twittersearch'); ?>"><?php _e( 'Search Query', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('twittersearch'); ?>" name="<?php echo $this->get_field_name('twittersearch'); ?>" type="text" value="<?php echo $instance['twittersearch']; ?>" />
<small>(<?php _e( 'Note: This setting applies to Widget type "Search"', 'cbnet-twitter-widget' ); ?>)</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('typetitle'); ?>"><?php _e( 'Title', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('typetitle'); ?>" name="<?php echo $this->get_field_name('typetitle'); ?>" type="text" value="<?php echo $instance['typetitle']; ?>" />
<small>(<?php _e( 'Note: This setting applies to Widget types "List", "Favorites", and "Profile"', 'cbnet-twitter-widget' ); ?>)</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('typedesc'); ?>"><?php _e( 'Caption', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('typedesc'); ?>" name="<?php echo $this->get_field_name('typedesc'); ?>" type="text" value="<?php echo $instance['typedesc']; ?>" />
<small>(<?php _e( 'Note: This setting applies to Widget types "List", "Favorites", and "Profile"', 'cbnet-twitter-widget' ); ?>)</small>
</p>
<p>
<strong><?php _e( 'Advanced Settings - Preferences', 'cbnet-twitter-widget' ); ?>:</strong>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['live'], true ); ?> id="<?php echo $this->get_field_id( 'live' ); ?>" name="<?php echo $this->get_field_name( 'live' ); ?>" />
<label for="<?php echo $this->get_field_id( 'live' ); ?>"><?php _e( 'Poll for New Results?', 'cbnet-twitter-widget' ); ?></label>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['scrollbar'], true ); ?> id="<?php echo $this->get_field_id( 'scrollbar' ); ?>" name="<?php echo $this->get_field_name( 'scrollbar' ); ?>" />
<label for="<?php echo $this->get_field_id( 'scrollbar' ); ?>"><?php _e( 'Include Scrollbar?', 'cbnet-twitter-widget' ); ?></label>
</p>
<p>
<label for="<?php echo $this->get_field_id( 'behavior' ); ?>"><?php _e( 'Behavior (load all/loop):' ); ?></label> 
<select name="<?php echo $this->get_field_name( 'behavior' ); ?>" style="width:100%;">
	<option value="all" <?php selected( 'all' == $instance['behavior'] ); ?>><?php _e( 'Load All Tweets', 'cbnet-twitter-widget' ); ?></option>
	<option value="default" <?php selected( 'default' == $instance['behavior'] ); ?>><?php _e( 'Timed Interval', 'cbnet-twitter-widget' ); ?></option>
</select>
</p>
<p style="margin-left:15px;">
(<?php _e( 'Note: these settings only apply if "Timed Interval" is selected.', 'cbnet-twitter-widget' ); ?>)
</p>
<p style="margin-left:15px;">
<input class="checkbox" type="checkbox" <?php checked( $instance['loop'], true ); ?> id="<?php echo $this->get_field_id( 'loop' ); ?>" name="<?php echo $this->get_field_name( 'loop' ); ?>" />
<label for="<?php echo $this->get_field_id( 'loop' ); ?>"><?php _e( 'Loop Results?', 'cbnet-twitter-widget' ); ?></label>
</p>
<p style="margin-left:15px;">
<label for="<?php echo $this->get_field_id('interval'); ?>"><?php _e( 'Interval (ms)', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('interval'); ?>" name="<?php echo $this->get_field_name('interval'); ?>" type="text" value="<?php echo $instance['interval']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('rpp'); ?>"><?php _e( 'Number of Tweets', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('rpp'); ?>" name="<?php echo $this->get_field_name('rpp'); ?>" type="text" value="<?php echo $instance['rpp']; ?>" />
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['avatars'], true ); ?> id="<?php echo $this->get_field_id( 'avatars' ); ?>" name="<?php echo $this->get_field_name( 'avatars' ); ?>" />
<label for="<?php echo $this->get_field_id( 'avatars' ); ?>"><?php _e( 'Show Avatars?', 'cbnet-twitter-widget' ); ?></label>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['timestamp'], true ); ?> id="<?php echo $this->get_field_id( 'timestamp' ); ?>" name="<?php echo $this->get_field_name( 'timestamp' ); ?>" />
<label for="<?php echo $this->get_field_id( 'timestamp' ); ?>"><?php _e( 'Show Timestamps?', 'cbnet-twitter-widget' ); ?></label>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['hashtags'], true ); ?> id="<?php echo $this->get_field_id( 'hashtags' ); ?>" name="<?php echo $this->get_field_name( 'hashtags' ); ?>" />
<label for="<?php echo $this->get_field_id( 'hashtags' ); ?>"><?php _e( 'Show Hashtags?', 'cbnet-twitter-widget' ); ?></label>
</p>
<p>
<strong><?php _e( 'Advanced Settings - Appearance', 'cbnet-twitter-widget' ); ?>:</strong>
<br />
<?php _e( 'Note: enter all colors as HEX values (e.g. #ffffff for white)', 'cbnet-twitter-widget' ); ?>
</p>
<p>
<label for="<?php echo $this->get_field_id('shellbg'); ?>"><?php _e( 'Shell Background', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('shellbg'); ?>" name="<?php echo $this->get_field_name('shellbg'); ?>" type="text" value="<?php echo $instance['shellbg']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('shellcolor'); ?>"><?php _e( 'Shell Text', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('shellcolor'); ?>" name="<?php echo $this->get_field_name('shellcolor'); ?>" type="text" value="<?php echo $instance['shellcolor']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('tweetbg'); ?>"><?php _e( 'Tweet Background', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('tweetbg'); ?>" name="<?php echo $this->get_field_name('tweetbg'); ?>" type="text" value="<?php echo $instance['tweetbg']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('tweetcolor'); ?>"><?php _e( 'Tweet Text', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('tweetcolor'); ?>" name="<?php echo $this->get_field_name('tweetcolor'); ?>" type="text" value="<?php echo $instance['tweetcolor']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('tweetlink'); ?>"><?php _e( 'Tweet Links', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('tweetlink'); ?>" name="<?php echo $this->get_field_name('tweetlink'); ?>" type="text" value="<?php echo $instance['tweetlink']; ?>" />
</p>
<p>
<strong><?php _e( 'Advanced Settings - Dimensions', 'cbnet-twitter-widget' ); ?>:</strong>
</p>
<p>
<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Width (pixels)', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $instance['width']; ?>" />
</p>
<p style="margin-left:15px;">OR:
<input class="checkbox" type="checkbox" <?php checked( $instance['widthauto'], true ); ?> id="<?php echo $this->get_field_id( 'widthauto' ); ?>" name="<?php echo $this->get_field_name( 'widthauto' ); ?>" />
<label for="<?php echo $this->get_field_id( 'widthauto' ); ?>"><?php _e( 'Auto Width?', 'cbnet-twitter-widget' ); ?></label>
</p>
<p>
<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e( 'Height (pixels)', 'cbnet-twitter-widget' ); ?>:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $instance['height']; ?>" />
</p>
		<?php
    }
}
?>