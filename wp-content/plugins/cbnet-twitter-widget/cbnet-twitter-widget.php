<?php /* widget_cbnet_twitter */
/**
 * Plugin Name: cbnet Twitter Widget
 * Plugin URI: http://www.chipbennett.net/wordpress/plugins/cbnet-twitter-widget/
 * Description: A widget that displays tweets from any Twitter profile, list, favorites, or search.
 * Version: 1.2
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
 * Define widget paths for admin notices
 * @since 1.2
 */
function cbnet_twitter_widget_admin_init() {
	define( 'CBNET_PROFILE_WIDGET_PLUGIN', 'cbnet-twitter-profile-display/cbnet-twitter-profile-display.php' );
	define( 'CBNET_PROFILE_WIDGET_PATH', WP_PLUGIN_DIR . '/' . CBNET_PROFILE_WIDGET_PLUGIN );
	define( 'CBNET_LIST_WIDGET_PLUGIN', '/cbnet-twitter-list-display/cbnet-twitter-list-display.php' );
	define( 'CBNET_LIST_WIDGET_PATH', WP_PLUGIN_DIR . '/' . CBNET_LIST_WIDGET_PLUGIN );
	define( 'CBNET_FAVES_WIDGET_PLUGIN', '/cbnet-twitter-faves-display/cbnet-twitter-faves-display.php' );
	define( 'CBNET_FAVES_WIDGET_PATH', WP_PLUGIN_DIR . '/' . CBNET_FAVES_WIDGET_PLUGIN );
	define( 'CBNET_SEARCH_WIDGET_PLUGIN', '/cbnet-twitter-search-display/cbnet-twitter-search-display.php' );
	define( 'CBNET_SEARCH_WIDGET_PATH', WP_PLUGIN_DIR . '/' . CBNET_SEARCH_WIDGET_PLUGIN );
}
add_action( 'admin_init', 'cbnet_twitter_widget_admin_init' );

/**
 * Add admin notices for obsolete widgets
 * @since 1.1
 */
function cbnet_twitter_widget_admin_notices() {
	$cbnet_old_widgets = array( 
		array( 'plugin' => CBNET_PROFILE_WIDGET_PLUGIN, 'path' => CBNET_PROFILE_WIDGET_PATH, 'name' => 'cbnet Twitter Profile Display' ),
		array( 'plugin' => CBNET_LIST_WIDGET_PLUGIN, 'path' => CBNET_LIST_WIDGET_PATH, 'name' => 'cbnet Twitter List Display' ),
		array( 'plugin' => CBNET_FAVES_WIDGET_PLUGIN, 'path' => CBNET_FAVES_WIDGET_PATH, 'name' => 'cbnet Twitter Faves Display' ),
		array( 'plugin' => CBNET_SEARCH_WIDGET_PLUGIN, 'path' => CBNET_SEARCH_WIDGET_PATH, 'name' => 'cbnet Twitter Search Display' )
	);
	foreach ( $cbnet_old_widgets as $cbnet_old_widget ) {
		if ( is_plugin_active( $cbnet_old_widget['plugin'] ) ) {
			echo "<div class='updated' style='background-color:#f66;'><p>Please deactivate the <strong>" . $cbnet_old_widget['name'] . "</strong>. It has been superceded by the cbnet Twitter Widget Plugin.</p></div>";
		} else if ( file_exists( $cbnet_old_widget['path'] ) ) {
			echo "<div class='updated'><p>Please delete the <strong>" . $cbnet_old_widget['name'] . "</strong>.</p></div>";
		}
	}
}
add_action( 'admin_notices', 'cbnet_twitter_widget_admin_notices' );

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
        $widget_ops = array('classname' => 'widget-cbnet-twitter-widget', 'description' => 'Widget to display Twitter widgets' );
        $this->WP_Widget('plugin_cbnet_twitter_widget', 'cbnet Twitter Widget', $widget_ops);
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
<label for="<?php echo $this->get_field_id('title'); ?>">Title (Heading):</label> 
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
</p>
<p>
<strong>Widget Type:</strong>
</p>
<p>
<label for="<?php echo $this->get_field_id('widgettype'); ?>">Widget Type:</label> 
<select name="<?php echo $this->get_field_name('widgettype'); ?>" style="width:100%;">
  <option <?php selected( 'profile' == $instance['widgettype'] ); ?> value="profile">Profile</option>
  <option <?php selected( 'list' == $instance['widgettype'] ); ?> value="list">List</option>
  <option <?php selected( 'faves' == $instance['widgettype'] ); ?> value="faves">Favorites</option>
  <option <?php selected( 'search' == $instance['widgettype'] ); ?> value="search">Search</option>
</select>
</p>
<p>
<strong>General Settings:</strong>
</p>
<p>
<label for="<?php echo $this->get_field_id('twitteruserid'); ?>">Twitter User ID:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('twitteruserid'); ?>" name="<?php echo $this->get_field_name('twitteruserid'); ?>" type="text" value="<?php echo $instance['twitteruserid']; ?>" />
<small>(Note: This setting applies to Widget types "Profile", "List", and "Favorites")</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('twitteruserlist'); ?>">Twitter List:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('twitteruserlist'); ?>" name="<?php echo $this->get_field_name('twitteruserlist'); ?>" type="text" value="<?php echo $instance['twitteruserlist']; ?>" />
<small>(Note: This setting applies to Widget type "List")</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('twittersearch'); ?>">Search Query:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('twittersearch'); ?>" name="<?php echo $this->get_field_name('twittersearch'); ?>" type="text" value="<?php echo $instance['twittersearch']; ?>" />
<small>(Note: This setting applies to Widget type "Search")</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('typetitle'); ?>">Title:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('typetitle'); ?>" name="<?php echo $this->get_field_name('typetitle'); ?>" type="text" value="<?php echo $instance['typetitle']; ?>" />
<small>(Note: This setting applies to Widget types "List", "Favorites", and "Profile")</small>
</p>
<p>
<label for="<?php echo $this->get_field_id('typedesc'); ?>">Caption:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('typedesc'); ?>" name="<?php echo $this->get_field_name('typedesc'); ?>" type="text" value="<?php echo $instance['typedesc']; ?>" />
<small>(Note: This setting applies to Widget types "List", "Favorites", and "Profile")</small>
</p>
<p>
<strong>Advanced Settings - Preferences:</strong>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['live'], true ); ?> id="<?php echo $this->get_field_id( 'live' ); ?>" name="<?php echo $this->get_field_name( 'live' ); ?>" />
<label for="<?php echo $this->get_field_id( 'live' ); ?>">Poll for New Results?</label>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['scrollbar'], true ); ?> id="<?php echo $this->get_field_id( 'scrollbar' ); ?>" name="<?php echo $this->get_field_name( 'scrollbar' ); ?>" />
<label for="<?php echo $this->get_field_id( 'scrollbar' ); ?>">Include Scrollbar?</label>
</p>
<p>
<label for="<?php echo $this->get_field_id( 'behavior' ); ?>"><?php _e( 'Behavior (load all/loop):' ); ?></label> 
<select name="<?php echo $this->get_field_name( 'behavior' ); ?>" style="width:100%;">
	<option value="all" <?php selected( 'all' == $instance['behavior'] ); ?>>Load All Tweets</option>
	<option value="default" <?php selected( 'default' == $instance['behavior'] ); ?>>Timed Interval</option>
</select>
</p>
<p style="margin-left:15px;">
(Note: these settings only apply if "Timed Interval" is selected.)
</p>
<p style="margin-left:15px;">
<input class="checkbox" type="checkbox" <?php checked( $instance['loop'], true ); ?> id="<?php echo $this->get_field_id( 'loop' ); ?>" name="<?php echo $this->get_field_name( 'loop' ); ?>" />
<label for="<?php echo $this->get_field_id( 'loop' ); ?>">Loop Results?</label>
</p>
<p style="margin-left:15px;">
<label for="<?php echo $this->get_field_id('interval'); ?>">Interval (ms):</label> 
<input class="widefat" id="<?php echo $this->get_field_id('interval'); ?>" name="<?php echo $this->get_field_name('interval'); ?>" type="text" value="<?php echo $instance['interval']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('rpp'); ?>">Number of Tweets:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('rpp'); ?>" name="<?php echo $this->get_field_name('rpp'); ?>" type="text" value="<?php echo $instance['rpp']; ?>" />
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['avatars'], true ); ?> id="<?php echo $this->get_field_id( 'avatars' ); ?>" name="<?php echo $this->get_field_name( 'avatars' ); ?>" />
<label for="<?php echo $this->get_field_id( 'avatars' ); ?>">Show Avatars?</label>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['timestamp'], true ); ?> id="<?php echo $this->get_field_id( 'timestamp' ); ?>" name="<?php echo $this->get_field_name( 'timestamp' ); ?>" />
<label for="<?php echo $this->get_field_id( 'timestamp' ); ?>">Show Timestamps?</label>
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked( $instance['hashtags'], true ); ?> id="<?php echo $this->get_field_id( 'hashtags' ); ?>" name="<?php echo $this->get_field_name( 'hashtags' ); ?>" />
<label for="<?php echo $this->get_field_id( 'hashtags' ); ?>">Show Hashtags?</label>
</p>
<p>
<strong>Advanced Settings - Appearance:</strong>
<br />
Note: enter all colors as HEX values (e.g. #ffffff for white)
</p>
<p>
<label for="<?php echo $this->get_field_id('shellbg'); ?>">Shell Background:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('shellbg'); ?>" name="<?php echo $this->get_field_name('shellbg'); ?>" type="text" value="<?php echo $instance['shellbg']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('shellcolor'); ?>">Shell Text:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('shellcolor'); ?>" name="<?php echo $this->get_field_name('shellcolor'); ?>" type="text" value="<?php echo $instance['shellcolor']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('tweetbg'); ?>">Tweet Background:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('tweetbg'); ?>" name="<?php echo $this->get_field_name('tweetbg'); ?>" type="text" value="<?php echo $instance['tweetbg']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('tweetcolor'); ?>">Tweet Text:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('tweetcolor'); ?>" name="<?php echo $this->get_field_name('tweetcolor'); ?>" type="text" value="<?php echo $instance['tweetcolor']; ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id('tweetlink'); ?>">Tweet Links:</label> 
<input class="widefat" id="<?php echo $this->get_field_id('tweetlink'); ?>" name="<?php echo $this->get_field_name('tweetlink'); ?>" type="text" value="<?php echo $instance['tweetlink']; ?>" />
</p>
<p>
<strong>Advanced Settings - Dimensions:</strong>
</p>
<p>
<label for="<?php echo $this->get_field_id('width'); ?>">Width (pixels):</label> 
<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $instance['width']; ?>" />
</p>
<p style="margin-left:15px;">OR:
<input class="checkbox" type="checkbox" <?php checked( $instance['widthauto'], true ); ?> id="<?php echo $this->get_field_id( 'widthauto' ); ?>" name="<?php echo $this->get_field_name( 'widthauto' ); ?>" />
<label for="<?php echo $this->get_field_id( 'widthauto' ); ?>">Auto Width?</label>
</p>
<p>
<label for="<?php echo $this->get_field_id('height'); ?>">Height (pixels):</label> 
<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $instance['height']; ?>" />
</p>
		<?php
    }
}
?>