<?php
/*
Plugin Name: Twitter for Wordpress extended
Version: 1.0.3
Plugin URI: http://www.das-motorrad-blog.de/meine-wordpress-plugins/
Author: Marc Schieferdecker
Author URI: http://www.das-motorrad-blog.de
Description: Displays your tweets/friends timeline/search results/twitter trends in your WordPress blog. Based on <a href="http://rick.jinlabs.com/code/twitter">Twitter for Wordpress</a> by <a href="http://rick.jinlabs.com/">Ricardo González</a>.
License: GPL
*/

define( 'MAGPIE_CACHE_ON', 1 ); // 2.7 Cache Bug
define( 'MAGPIE_CACHE_AGE', 180 );
define( 'MAGPIE_INPUT_ENCODING', 'UTF-8' );
define( 'MAGPIE_OUTPUT_ENCODING', 'UTF-8' );

$twitter_options[ 'widget_fields' ][ 'display_what' ] = array( 'label' => 'What to display:', 'type' => 'selection', 'default' => 'usertimeline' );
$twitter_options[ 'widget_fields' ][ 'title' ] = array( 'label' => 'Title:', 'type' => 'text', 'default' => '' );
$twitter_options[ 'widget_fields' ][ 'username' ] = array( 'label' => 'Username:', 'type' => 'text', 'default' => '' );
$twitter_options[ 'widget_fields' ][ 'password' ] = array( 'label' => 'Password:', 'type' => 'password', 'default' => '' );
$twitter_options[ 'widget_fields' ][ 'tag' ] = array( 'label' => 'Tag or search string:', 'type' => 'text', 'default' => '' );
$twitter_options[ 'widget_fields' ][ 'num' ] = array( 'label' => 'Number of links/trends:', 'type' => 'text', 'default' => '5' );
$twitter_options[ 'widget_fields' ][ 'update' ] = array( 'label' => 'Show timestamps:', 'type' => 'checkbox', 'default' => true );
$twitter_options[ 'widget_fields' ][ 'linked' ] = array( 'label' => 'Linked:', 'type' => 'text', 'default' => '#' );
$twitter_options[ 'widget_fields' ][ 'hyperlinks' ] = array( 'label' => 'Discover Hyperlinks:', 'type' => 'checkbox', 'default' => true );
$twitter_options[ 'widget_fields' ][ 'twitter_users' ] = array( 'label' => 'Discover @replies:', 'type' => 'checkbox', 'default' => true );
$twitter_options[ 'widget_fields' ][ 'encode_utf8' ] = array( 'label' => 'UTF8 Encode:', 'type' => 'checkbox', 'default' => false );
$twitter_options[ 'widget_fields' ][ 'imgwidth' ] = array( 'label' => 'Avatars images width (used on search results, 0 to deactivate):', 'type' => 'text', 'default' => '32' );
$twitter_options[ 'prefix' ] = 'twitter';

/**
 * This function is a copy of "fetch_rss" from wp-includes/rss.php.
 * I changed the function so I can set the http headers for the http request.
 */
function fetch_rss_with_request_headers( $url, $request_headers = array() )
{
	// initialize constants
	init();

	if ( !isset($url) ) {
		// error("fetch_rss called without a url");
		return false;
	}

	// if cache is disabled
	if ( !MAGPIE_CACHE_ON ) {
		// fetch file, and parse it
		$resp = _fetch_remote_file( $url );
		if ( is_success( $resp->status ) ) {
			return _response_to_rss( $resp );
		}
		else {
			// error("Failed to fetch $url and cache is off");
			return false;
		}
	}
	// else cache is ON
	else {
		// Flow
		// 1. check cache
		// 2. if there is a hit, make sure its fresh
		// 3. if cached obj fails freshness check, fetch remote
		// 4. if remote fails, return stale object, or error

		$cache = new RSSCache( MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE );

		if (MAGPIE_DEBUG and $cache->ERROR) {
			debug($cache->ERROR, E_USER_WARNING);
		}


		$cache_status 	 = 0;		// response of check_cache
		$rss 			 = 0;		// parsed RSS object
		$errormsg		 = 0;		// errors, if any

		if (!$cache->ERROR) {
			// return cache HIT, MISS, or STALE
			$cache_status = $cache->check_cache( $url );
		}

		// if object cached, and cache is fresh, return cached obj
		if ( $cache_status == 'HIT' ) {
			$rss = $cache->get( $url );
			if ( isset($rss) and $rss ) {
				$rss->from_cache = 1;
				if ( MAGPIE_DEBUG > 1) {
				debug("MagpieRSS: Cache HIT", E_USER_NOTICE);
			}
				return $rss;
			}
		}

		// else attempt a conditional get

		// setup headers
		if ( $cache_status == 'STALE' ) {
			$rss = $cache->get( $url );
			if ( isset($rss->etag) and $rss->last_modified ) {
				$request_headers['If-None-Match'] = $rss->etag;
				$request_headers['If-Last-Modified'] = $rss->last_modified;
			}
		}

		$resp = _fetch_remote_file( $url, $request_headers );

		if (isset($resp) and $resp) {
			if ($resp->status == '304' ) {
				// we have the most current copy
				if ( MAGPIE_DEBUG > 1) {
					debug("Got 304 for $url");
				}
				// reset cache on 304 (at minutillo insistent prodding)
				$cache->set($url, $rss);
				return $rss;
			}
			elseif ( is_success( $resp->status ) ) {
				$rss = _response_to_rss( $resp );
				if ( $rss ) {
					if (MAGPIE_DEBUG > 1) {
						debug("Fetch successful");
					}
					// add object to cache
					$cache->set( $url, $rss );
					return $rss;
				}
			}
			else {
				$errormsg = "Failed to fetch $url. ";
				if ( $resp->error ) {
					# compensate for Snoopy's annoying habbit to tacking
					# on '\n'
					$http_error = substr($resp->error, 0, -2);
					$errormsg .= "(HTTP Error: $http_error)";
				}
				else {
					$errormsg .=  "(HTTP Response: " . $resp->response_code .')';
				}
			}
		}
		else {
			$errormsg = "Unable to retrieve RSS file for unknown reasons.";
		}

		// else fetch failed

		// attempt to return cached object
		if ($rss) {
			if ( MAGPIE_DEBUG ) {
				debug("Returning STALE object for $url");
			}
			return $rss;
		}

		// else we totally failed
		// error( $errormsg );

		return false;

	} // end if ( !MAGPIE_CACHE_ON ) {
}

// Display current trends
function twitter_trends( $num = 20, $list = false, $encode_utf8 = false )
{
	global $twitter_options;

	$json = @file_get_contents( 'http://search.twitter.com/trends/current.json' );

	if( $list )
		echo '<ul class="twitter">';

	if( $json )
	{
		// Regex trends, it's faster and json_decode is only available since php 5.2.0
		preg_match_all( '#\{"query":"(.*)","name":"(.*)"\}#iU', $json, $matches );
		if( is_array( $matches ) && count( $matches[ 0 ] ) )
		{
			$i = 0;
			foreach( $matches[ 2 ] AS $mkey => $trend )
			{
				$query = $matches[ 1 ][ $mkey ];
				$query = str_replace( "\\\"", "\"", $query );

				if( $list )
					echo '<li class="twitter-item">';
				else
				if( $num != 1 )
					echo '<p class="twitter-tag-message">';

				echo "<a href=\"http://twitter.com/timeline/search?q=" . urlencode( $query ) . "\">" . $trend . "</a>";

				if( $list )
					echo '</li>';
				else
				if( $num != 1 )
					echo '</p>';

				$i++;
				if( $i >= $num )
					break;
			}
		}
		else
		{
			if( $list )
				echo '<li>';
			echo 'No trends found.';
			if( $list )
				echo '</li>';
		}
	}
	else
	{
		if( $list )
			echo '<li>';
		echo 'No trends found.';
		if( $list )
			echo '</li>';
	}

	if( $list )
		echo '</ul>';
}

// Display Twitter messages
function twitter_friends( $username = '', $password = '', $num = 10, $list = false, $update = true, $hyperlinks = true, $twitter_users = true, $encode_utf8 = false )
{
	global $twitter_options;
	include_once( ABSPATH . WPINC . '/rss.php' );

	$messages = fetch_rss_with_request_headers( 'http://twitter.com/statuses/friends_timeline.rss', array( 'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ), 'Expect' => '' ) );

	if( $list )
		echo '<ul class="twitter">';

	if( $username == '' || $password == '' )
	{
		if( $list )
			echo '<li>';
		echo 'RSS not configured';
		if( $list )
			echo '</li>';
	}
	else
	{
		if( empty( $messages -> items ) )
		{
			if( $list )
				echo '<li>';
			echo 'No Twitter messages found.';
			if( $list )
				echo '</li>';
		}
		else
		{
			$i = 0;
			foreach( $messages -> items AS $message )
			{
				$usr = substr( $message[ 'description' ], 0, strpos( $message[ 'description' ], ':' ) );
				$message[ 'description' ] = substr( $message[ 'description' ], strpos( $message[ 'description' ], ':' ) + 2 );
				$msg = "<a href=\"http://twitter.com/$usr\" title=\"$usr\" class=\"twitter-imagelink\">$usr</a>: " . $message[ 'description' ] . " ";
				if( $encode_utf8 )
					$msg = utf8_encode( $msg );
				$link = $message[ 'link' ];

				if( $list )
					echo '<li class="twitter-item">';
				else
				if( $num != 1 )
					echo '<p class="twitter-tag-message">';

				if( $hyperlinks )
					$msg = hyperlinks( $msg );
				if( $twitter_users )
					$msg = twitter_users( $msg );
				echo $msg;

				if( $update )
				{
					$time = strtotime( $message[ 'pubdate' ] );

					if( (abs( time() - $time) ) < 86400 )
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					else
						$h_time = date(__( 'Y/m/d' ), $time );

					echo
						sprintf( __( '%s', 'twitter-for-wordpress'),' <span class="twitter-timestamp"><abbr title="' . date(__( 'Y/m/d H:i:s'), $time) . '">' . $h_time . '</abbr></span>' );
				}

				if( $list )
					echo '</li>';
				else
				if( $num != 1 )
					echo '</p>';

				$i++;
				if( $i >= $num )
					break;
			}
		}
	}

	if( $list )
		echo '</ul>';
}

function twitter_searchstring( $searchstring = '', $num = 10, $list = false, $imgwidth = 32, $update = true, $hyperlinks = true, $twitter_users = true, $encode_utf8 = false )
{
	global $twitter_options;
	include_once( ABSPATH . WPINC . '/rss.php' );

	$messages = fetch_rss( 'http://search.twitter.com/search.rss?q=' . urlencode( $searchstring ) );

	if( $list )
		echo '<ul class="twitter">';

	if( $searchstring == '' )
	{
		if( $list )
			echo '<li>';
		echo 'RSS not configured';
		if( $list )
			echo '</li>';
	}
	else
	{
		if( empty( $messages -> items ) )
		{
			if( $list )
				echo '<li>';
			echo 'No Twitter messages found.';
			if( $list )
				echo '</li>';
		}
		else
		{
			$i = 0;
			foreach( $messages -> items AS $message )
			{
				$img = $message[ 'google' ][ 'image_link' ];
				$usr = substr( $message[ 'author' ], 0, strpos( $message[ 'author' ], '@' ) );
				$msg = "<a href=\"http://twitter.com/$usr\" title=\"$usr\" class=\"twitter-imagelink\">" . (empty( $imgwidth ) ? "$usr: " : "<img src=\"$img\" title=\"$usr\" alt=\"$usr\" width=\"$imgwidth\"/>") . "</a> " . $message[ 'description' ] . " ";
				if( $encode_utf8 )
					$msg = utf8_encode( $msg );
				$link = $message[ 'link' ];

				if( $list )
					echo '<li class="twitter-item">';
				else
				if( $num != 1 )
					echo '<p class="twitter-tag-message">';

				if( $hyperlinks )
					$msg = hyperlinks( $msg );
				if( $twitter_users )
					$msg = twitter_users( $msg );
				echo $msg;

				if( $update )
				{
					$time = strtotime( $message[ 'pubdate' ] );

					if( (abs( time() - $time) ) < 86400 )
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					else
						$h_time = date(__( 'Y/m/d' ), $time );

					echo
						sprintf( __( '%s', 'twitter-for-wordpress'),' <span class="twitter-timestamp"><abbr title="' . date(__( 'Y/m/d H:i:s'), $time) . '">' . $h_time . '</abbr></span>' );
				}

				if( $list )
					echo '</li>';
				else
				if( $num != 1 )
					echo '</p>';

				$i++;
				if( $i >= $num )
					break;
			}
		}
	}

	if( $list )
		echo '</ul>';
}

// Display Twitter messages
function twitter_messages( $username = '', $num = 10, $list = false, $update = true, $linked = '#', $hyperlinks = true, $twitter_users = true, $encode_utf8 = false )
{
	global $twitter_options;
	include_once( ABSPATH . WPINC . '/rss.php' );

	$messages = fetch_rss( 'http://twitter.com/statuses/user_timeline/' . $username . '.rss' );

	if( $list )
		echo '<ul class="twitter">';

	if( $username == '' )
	{
		if( $list )
			echo '<li>';
		echo 'RSS not configured';
		if( $list )
			echo '</li>';
	}
	else
	{
		if( empty( $messages -> items ) )
		{
			if( $list )
				echo '<li>';
			echo 'No public Twitter messages.';
			if( $list )
				echo '</li>';
		}
		else
		{
			$i = 0;
			foreach( $messages -> items AS $message )
			{
				$msg = " " . substr(strstr( $message[ 'description' ], ': '), 2, strlen( $message[ 'description' ])) . " ";
				if( $encode_utf8 )
					$msg = utf8_encode( $msg );
				$link = $message[ 'link' ];

				if( $list )
					echo '<li class="twitter-item">';
				else
				if( $num != 1 )
					echo '<p class="twitter-message">';

				if( $linked != '' || $linked != false )
				{
					if( $linked == 'all' )
						$msg = '<a href="' . $link . '" class="twitter-link">' . $msg . '</a>';
					else
						$msg = $msg . '<a href="' . $link . '" class="twitter-link">' . $linked . '</a>';
				}
				if( $hyperlinks )
					$msg = hyperlinks( $msg );
				if( $twitter_users )
					$msg = twitter_users( $msg );
				echo $msg;

				if( $update )
				{
					$time = strtotime( $message[ 'pubdate' ] );

					if( (abs( time() - $time) ) < 86400 )
						$h_time = sprintf( __( '%s ago'), human_time_diff( $time ) );
					else
						$h_time = date(__( 'Y/m/d'), $time );

					echo sprintf( __( '%s', 'twitter-for-wordpress'), ' <span class="twitter-timestamp"><abbr title="' . date(__( 'Y/m/d H:i:s'), $time ) . '">' . $h_time . '</abbr></span>' );
				}

				if( $list )
					echo '</li>';
				else
				if( $num != 1 )
					echo '</p>';

				$i++;
				if( $i >= $num )
					break;
			}
		}
	}

	if( $list )
		echo '</ul>';
}

// Link discover stuff
function hyperlinks( $text )
{
	// match protocol://address/path/file.extension?some=variable&another=asf%
	$text = preg_replace("/\s([a-zA-Z]+:\/\/[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i"," <a href=\"$1\" class=\"twitter-link\">$1</a>$2", $text );
	// match www.something.domain/path/file.extension?some=variable&another=asf%
	$text = preg_replace("/\s(www\.[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i"," <a href=\"http://$1\" class=\"twitter-link\">$1</a>$2", $text );
	// match name@address
	$text = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto://$1\" class=\"twitter-link\">$1</a>$2", $text );
	return $text;
}

function twitter_users( $text )
{
	$text = preg_replace( '/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text );
	return $text;
}

// Twitter widget stuff
function widget_twitter_init()
{
	if( !function_exists( 'register_sidebar_widget' ) )
		return;

	$check_options = get_option( 'widget_twitter' );
	if( $check_options[ 'number' ] == '' )
	{
		$check_options[ 'number' ] = 1;
		update_option( 'widget_twitter', $check_options );
	}

	function widget_twitter( $args, $number = 1 )
	{
		global $twitter_options;

		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract( $args );

		// Each widget can store its own options. We keep strings here.
		include_once( ABSPATH . WPINC . '/rss.php' );
		$options = get_option( 'widget_twitter' );

		// fill options with default values if value is not set
		$item = $options[ $number ];
		foreach( $twitter_options[ 'widget_fields' ] AS $key => $field )
		{
			if( !isset( $item[ $key ] ) )
				$item[ $key ] = $field[ 'default' ];
		}
		// Why fetching messages in the next line? They're fetched by the function. Commented it out -- Marc
		//$messages = fetch_rss( 'http://twitter.com/statuses/user_timeline/' . $item[ 'username' ] . '.rss' );

		// These lines generate our output.
		echo $before_widget . $before_title . $item[ 'title' ] . $after_title;

		if( $item[ 'display_what' ] == 'currenttrends' )
			twitter_trends( $item[ 'num' ], true, $item[ 'encode_utf8' ] );
		else
		if( $item[ 'display_what' ] == 'friendstimeline' && !empty( $item[ 'password' ] ) )
			twitter_friends( $item[ 'username' ], $item[ 'password' ], $item[ 'num' ], true, $item[ 'update' ], $item[ 'hyperlinks' ], $item[ 'twitter_users' ], $item[ 'encode_utf8' ] );
		else
		if( $item[ 'display_what' ] == 'searchresult' && !empty( $item[ 'tag' ] ) )
			twitter_searchstring( $item[ 'tag' ], $item[ 'num' ], true, $item[ 'imgwidth' ], $item[ 'update' ], $item[ 'hyperlinks' ], $item[ 'twitter_users' ], $item[ 'encode_utf8' ] );
		else
		if( $item[ 'display_what' ] == 'usertimeline' && !empty( $item[ 'username' ] ) )
			twitter_messages( $item[ 'username' ], $item[ 'num' ], true, $item[ 'update' ], $item[ 'linked' ], $item[ 'hyperlinks' ], $item[ 'twitter_users' ], $item[ 'encode_utf8' ] );
		echo $after_widget;
	}

	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_twitter_control( $number )
	{
		global $twitter_options;

		// Get our options and see if we're handling a form submission.
		$options = get_option( 'widget_twitter' );
		if( isset( $_POST[ 'twitter-submit' ] ) )
		{
			foreach( $twitter_options[ 'widget_fields' ] AS $key => $field )
			{
				$options[ $number ][ $key ] = $field[ 'default' ];
				$field_name = sprintf( '%s_%s_%s', $twitter_options[ 'prefix' ], $key, $number );
				if( $field[ 'type' ] == 'text')
				{
					$options[$number][$key] = strip_tags(stripslashes( $_POST[$field_name]) );
				}
				else
				if( $field[ 'type' ] == 'checkbox' )
				{
					$options[$number][$key] = isset( $_POST[$field_name] );
				}
				else
				{
					$options[$number][$key] = $_POST[$field_name];
				}
			}
			update_option( 'widget_twitter', $options );
		}

		foreach( $twitter_options[ 'widget_fields' ] AS $key => $field )
		{
			$field_name = sprintf( '%s_%s_%s', $twitter_options[ 'prefix' ], $key, $number );
			$field_checked = '';
			if( $field[ 'type' ] == 'text' )
			{
				$field_value = htmlspecialchars( $options[$number][$key], ENT_QUOTES );
			}
			else
			if( $field[ 'type' ] == 'checkbox' )
			{
				$field_value = 1;
				if( !empty( $options[ $number ][ $key ] ) )
					$field_checked = 'checked="checked"';
			}
			else
			{
				$field_value = $options[$number][$key];
			}

			if( $field[ 'type' ] == 'selection' )
			{
				$s1 = ''; $s2 = ''; $s3 = ''; $s4 = '';

				if( $field_value == 'usertimeline' )
					$s1 = ' selected="selected"';
				if( $field_value == 'friendstimeline' )
					$s2 = ' selected="selected"';
				if( $field_value == 'searchresult' )
					$s3 = ' selected="selected"';
				if( $field_value == 'currenttrends' )
					$s4 = ' selected="selected"';
				printf( '<p style="text-align:right;" class="twitter_field"><label for="%s">%s <select id="%s" name="%s" class="%s"><option value="usertimeline"%s>User timeline</option><option value="friendstimeline"%s>Friends timeline (requires password)</option><option value="searchresult"%s>Search result (requires tag or search string)</option><option value="currenttrends"%s>Current trends on twitter</option></select></label></p>',
					$field_name, __( $field[ 'label' ]), $field_name, $field_name, $field[ 'type' ], $s1, $s2, $s3, $s4 );
			}
			else
			{
				printf(	'<p style="text-align:right;" class="twitter_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',
					$field_name, __( $field[ 'label' ]), $field_name, $field_name, $field[ 'type' ], $field_value, $field[ 'type' ], $field_checked );
			}
		}
		echo '<input type="hidden" id="twitter-submit" name="twitter-submit" value="1" />';
	}

	function widget_twitter_setup()
	{
		$options = $newoptions = get_option( 'widget_twitter' );

		if( isset( $_POST[ 'twitter-number-submit' ] ) )
		{
			$number = intval( $_POST[ 'twitter-number' ] );
			$newoptions[ 'number' ] = $number;
		}

		if( $options != $newoptions )
		{
			update_option( 'widget_twitter', $newoptions );
			widget_twitter_register( );
		}
	}


	function widget_twitter_page()
	{
		$options = $newoptions = get_option( 'widget_twitter' );
	?>
		<div class="wrap">
			<form method="POST">
				<h2><?php _e( 'Twitter Widgets' ); ?></h2>
				<p style="line-height: 30px;"><?php _e( 'How many Twitter widgets would you like?' ); ?>
				<select id="twitter-number" name="twitter-number" value="<?php echo $options[ 'number' ]; ?>">
	<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".( $options[ 'number' ]==$i ? "selected='selected'" : '').">$i</option>"; ?>
				</select>
				<span class="submit"><input type="submit" name="twitter-number-submit" id="twitter-number-submit" value="<?php echo attribute_escape(__( 'Save') ); ?>" /></span></p>
			</form>
		</div>
	<?php
	}

	function widget_twitter_register()
	{

		$options = get_option( 'widget_twitter' );
		$dims = array( 'width' => 300, 'height' => 300 );
		$class = array( 'classname' => 'widget_twitter' );

		for( $i = 1; $i <= 9; $i++ )
		{
			$name = sprintf(__( 'Twitter #%d'), $i );
			$id = "twitter-$i"; // Never never never translate an id
			wp_register_sidebar_widget( $id, $name, $i <= $options[ 'number' ] ? 'widget_twitter' : /* unregister */ '', $class, $i );
			wp_register_widget_control( $id, $name, $i <= $options[ 'number' ] ? 'widget_twitter_control' : /* unregister */ '', $dims, $i );
		}
		add_action( 'sidebar_admin_setup', 'widget_twitter_setup' );
		add_action( 'sidebar_admin_page', 'widget_twitter_page' );
	}

	widget_twitter_register();
}

// Run our code later in case this loads prior to any required plugins.
add_action( 'widgets_init', 'widget_twitter_init' );

?>
