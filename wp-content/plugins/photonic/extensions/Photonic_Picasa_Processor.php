<?php
/**
 * Processor for Picasa. This extends the Photonic_Processor class and defines methods local to Picasa.
 *
 * @package Photonic
 * @subpackage Extensions
 */

class Photonic_Picasa_Processor extends Photonic_OAuth2_Processor {
	function __construct() {
		parent::__construct();
		global $photonic_picasa_client_id, $photonic_picasa_client_secret, $photonic_picasa_disable_title_link;
		$this->client_id = $photonic_picasa_client_id;
		$this->client_secret = $photonic_picasa_client_secret;
		$this->provider = 'picasa';
		$this->oauth_version = '2.0';
		$this->response_type = 'code';
		$this->scope = 'https://picasaweb.google.com/data/';
		$this->link_lightbox_title = empty($photonic_picasa_disable_title_link);

		$cookie = Photonic::parse_cookie();
		global $photonic_picasa_allow_oauth;
		$this->oauth_done = false;
		if ($photonic_picasa_allow_oauth && isset($cookie['picasa']) && isset($cookie['picasa']['oauth_token']) && isset($cookie['picasa']['oauth_refresh_token'])) { // OAuth2, so no Access token secret
			if ($this->is_token_expired($cookie['picasa'])) {
				$this->refresh_token($cookie['picasa']['oauth_refresh_token']);
				$cookie = Photonic::parse_cookie(); // Refresh the cookie object based on the results of the refresh token
				if ($this->is_token_expired($cookie['picasa'])) { // Tried refreshing, but didn't work
					$this->oauth_done = false;
				}
				else {
					$this->oauth_done = true;
				}
			}
			else {
				$this->oauth_done = true;
			}
		}
		else if (!isset($cookie['picasa']) || !isset($cookie['picasa']['oauth_token']) || !isset($cookie['picasa']['oauth_refresh_token'])) {
			$this->oauth_done = false;
		}
	}

	/**
	 *
	 * user_id
	 * kind
	 * album
	 * max_results
	 *
	 * thumb_size
	 * columns
	 * shorten caption
	 * show caption
	 *
	 * @param array $attr
	 * @return string
	 */
	function get_gallery_images($attr = array()) {
		$attr = array_merge(array(
			'style' => 'default',
			'show_captions' => false,
			'crop' => true,
			'display' => 'in-page',
			'max_results' => 1000,
			'thumbsize' => 75,
		), $attr);
		extract($attr);

		if (!isset($user_id) || (isset($user_id) && trim($user_id) == '')) {
			return '';
		}

		if (!isset($view)) {
			$view = null;
		}

		$query_url = 'http://picasaweb.google.com/data/feed/api/user/'.$user_id;
		global $photonic_picasa_allow_oauth;
		if (isset($photonic_picasa_allow_oauth) && $photonic_picasa_allow_oauth && $this->oauth_done) {
			if (isset($_COOKIE['photonic-' . md5($this->client_secret) . '-oauth-token'])) {
				$query_url = str_replace('http://', 'https://', $query_url);
			}
		}

		if (isset($album) && trim($album) != '') {
			$query_url .= '/album/'.urlencode($album);
		}

		if (isset($albumid) && trim($albumid) != '') {
			$query_url .= '/albumid/'.urlencode($albumid);
		}

		if (isset($kind) && trim($kind) != '' && in_array(trim($kind), array('album', 'photo', 'tag'))) {
			$kind = trim($kind);
			$query_url .= "?kind=".$kind."&";
		}
		else {
			$kind = '';
			$query_url .= "?".$kind;
		}

		if (!isset($view) || $view == null) {
			if ($kind == 'album') {
				$view = 'album';
			}
			else if ($kind == '') {
				if (!isset($album) && !isset($albumid)) {
					$view = 'album';
				}
			}
		}

		global $photonic_archive_thumbs;
		if (is_archive()) {
			if (isset($photonic_archive_thumbs) && !empty($photonic_archive_thumbs)) {
				if (isset($max_results) && $photonic_archive_thumbs < $max_results) {
					$query_url .= 'max-results='.$photonic_archive_thumbs.'&';
					$this->show_more_link = true;
				}
				else if (isset($max_results)) {
					$query_url .= '&max-results='.$max_results.'&';
				}
			}
			else if (isset($max_results)) {
				$query_url .= '&max-results='.$max_results.'&';
			}
		}
		else if (isset($max_results)) {
			$query_url .= '&max-results='.$max_results.'&';
		}

		if (isset($thumbsize) && trim($thumbsize) != '') {
			$query_url .= 'thumbsize='.trim($thumbsize).'&';
		}
		else {
			$query_url .= 'thumbsize=75&';
		}

		$query_url .= 'imgmax=1600u';

		global $photonic_picasa_allow_oauth;
		$ret = '';
		if ($photonic_picasa_allow_oauth && !$this->oauth_done) {
			$post_id = get_the_ID();
			$ret .= $this->get_login_box($post_id);
		}

		return $ret.$this->make_call($query_url, $display, $view, $attr);
	}

	function make_call($query_url, $display, $view, $attr) {
		global $photonic_picasa_allow_oauth;
		extract($attr);
		if (isset($photonic_picasa_allow_oauth) && $photonic_picasa_allow_oauth && $this->oauth_done) {
			if (isset($_COOKIE['photonic-' . md5($this->client_secret) . '-oauth-token'])) {
				$query_url = add_query_arg('access_token', $_COOKIE['photonic-' . md5($this->client_secret) . '-oauth-token'], $query_url);
				$response = $this->get_secure_curl_response($query_url);
				if (strlen($response) == 0 || substr($response, 0, 1) != '<') {
					$rss = '';
					if (stripos($response, 'No album found') !== false) {
//						$new_url = $this->get_google_plus_url($query_url);
//						$response = $this->get_secure_curl_response($new_url);
					}
				}
				else {
					$rss = $response;
				}
			}
		}
		else {
			$response = wp_remote_request($query_url);
			if (is_wp_error($response)) {
				$rss = '';
			}
			else if (200 != $response['response']['code']) {
				$rss = '';
			}
			else {
				$rss = $response['body'];
			}
		}

		$this->gallery_index++;
		if ($display != 'popup') {
			$out = "<div class='photonic-picasa-stream photonic-stream' id='photonic-picasa-stream-{$this->gallery_index}'>";
		}
		else {
			if (empty($panel)) {
				$panel = '';
			}
			$out = "<div class='photonic-picasa-panel photonic-panel' id='photonic-picasa-panel-$panel'>";
		}
		if (!isset($columns)) {
			$columns = 'auto';
		}

		if (!isset($panel)) {
			$panel = null;
		}

		$out .= $this->process_response($rss, $view, $display, $columns, $panel, $attr['thumbsize']);
		$out .= "</div>";
		return $out;
	}

	/**
	 * Reads the output from Picasa and parses it to generate the front-end output.
	 * In a later release this will be streamlined to use DOM-based parsing instead of event-based parsing.
	 *
	 * @param $rss
	 * @param null $view
	 * @param string $display
	 * @param null|string $columns
	 * @param null $panel
	 * @param int $thumb_size
	 * @return string
	 */
	function process_response($rss, $view = null, $display = 'in-page', $columns = 'auto', $panel = null, $thumb_size = 75) {
		global $photonic_picasa_photo_title_display, $photonic_picasa_photo_pop_title_display, $photonic_picasa_photos_per_row_constraint,
			$photonic_picasa_photos_constrain_by_count, $photonic_picasa_photos_constrain_by_padding, $photonic_picasa_photos_pop_per_row_constraint,
			$photonic_picasa_photos_pop_constrain_by_count, $photonic_picasa_photos_pop_constrain_by_padding;
		$picasa_result = simplexml_load_string($rss);
		$out = '';
		if ($display == 'popup') {
			$out .= "<div class='photonic-picasa-panel-content photonic-panel-content fix'>";
		}
		if (is_a($picasa_result, 'SimpleXMLElement')) {
			if (isset($picasa_result->entry) && count($picasa_result->entry) > 0) {
				$row_constraints = array('constraint-type' => $photonic_picasa_photos_per_row_constraint, 'padding' => $photonic_picasa_photos_constrain_by_padding, 'count' => $photonic_picasa_photos_constrain_by_count);
				if ($view == 'album' && $display == 'in-page') {
					$objects = $this->build_level_2_objects($picasa_result->entry, $thumb_size);
					$out .= $this->generate_level_2_gallery($objects, $row_constraints, $columns, 'albums', 'album', $photonic_picasa_photo_title_display, false);
				}
				else {
					if ($display == 'in-page') {
						$title_position = $photonic_picasa_photo_title_display;
					}
					else {
						$row_constraints = array('constraint-type' => $photonic_picasa_photos_pop_per_row_constraint, 'padding' => $photonic_picasa_photos_pop_constrain_by_padding, 'count' => $photonic_picasa_photos_pop_constrain_by_count);
						$title_position = $photonic_picasa_photo_pop_title_display;
					}
					$objects = $this->build_level_1_objects($picasa_result->entry);
					$out .= $this->generate_level_1_gallery($objects, $title_position, $row_constraints, $columns, $display);
				}
			}
		}

		if ($out != '') {
			if ($this->show_more_link) {
				$out .= $this->more_link_button(get_permalink().'#photonic-picasa-stream-'.$this->gallery_index);
			}
			global $photonic_picasa_photo_pop_title_display;
			if ($display == 'popup') {
				$out .= $this->get_popup_tooltip($photonic_picasa_photo_pop_title_display);
				$out .= $this->get_popup_lightbox();
				$out .= "</div>";
			}
		}
		return $out;
	}

	function build_level_1_objects($photos) {
		global $photonic_picasa_use_desc;
		$objects = array();
		foreach ($photos as $entry) {
			$media_photo = $entry->children('media', 1);
			$media_photo = $media_photo->group;
			if (stripos($media_photo->content->attributes()->type, 'video') !== false) {
				continue;
			}
			$object = array();
			$object['thumbnail'] = $media_photo->thumbnail->attributes()->url;
			$object['main_image'] = $media_photo->content->attributes()->url;
			if (isset($entry->link)) {
				foreach ($entry->link as $link) {
					$attributes = $link->attributes();
					if (isset($attributes['type']) && $attributes['type'] == 'text/html' && isset($attributes['href']) && isset($attributes['rel'])) {
						if ((stripos($attributes['rel'], 'http://schemas.google.com/photos') === 0 || stripos($attributes['rel'], 'http://schemas.google.com/photos') === 0) && stripos($attributes['rel'], '#canonical')) {
							$object['main_page'] = $attributes['href'];
							break;
						}
					}
				}
			}
			if (!isset($object['main_page'])) {
				$object['main_page'] = $object['main_image'];
			}

			if ($photonic_picasa_use_desc == 'desc' || ($photonic_picasa_use_desc == 'desc-title' && !empty($entry->summary))) {
				$object['title'] = esc_attr($entry->summary);
			}
			else {
				$object['title'] = esc_attr($entry->title);
			}
			$object['alt_title'] = $object['title'];

			$objects[] = $object;
		}
		return $objects;
	}

	function build_level_2_objects($albums, $thumb_size) {
		global $photonic_picasa_use_desc;
		$objects = array();
		foreach ($albums as $entry) {
			$media_photo = $entry->children('media', 1);
			$media_photo = $media_photo->group;
			$gphoto_photo = $entry->children('gphoto', 1);

			$object = array();
			$object['id_1'] = "{$gphoto_photo->user}";
			$object['id_2'] = "{$gphoto_photo->id}";
			$object['thumbnail'] = $media_photo->thumbnail->attributes()->url;
			if (isset($entry->link)) {
				foreach ($entry->link as $link) {
					$attributes = $link->attributes();
					if (isset($attributes['type']) && $attributes['type'] == 'text/html' && isset($attributes['href']) && isset($attributes['rel'])) {
						if (((stripos($attributes['rel'], 'http://schemas.google.com/photos') === 0 || stripos($attributes['rel'], 'http://schemas.google.com/photos') === 0) && stripos($attributes['rel'], '#canonical')) || $attributes['rel'] == 'alternate') {
							$object['main_page'] = $attributes['href'];
							break;
						}
					}
				}
			}
			if (!isset($object['main_page'])) {
				$object['main_page'] = $media_photo->content->attributes()->url;
			}
			if ($photonic_picasa_use_desc == 'desc' || ($photonic_picasa_use_desc == 'desc-title' && !empty($entry->summary))) {
				$object['title'] = esc_attr($entry->summary);
			}
			else {
				$object['title'] = esc_attr($entry->title);
			}
			$object['counter'] = $gphoto_photo->numphotos;
			$object['classes'] = array('photonic-picasa-album-thumb-'.$thumb_size);

			$objects[] = $object;
		}
		return $objects;
	}

	/**
	 * If a Picasa album thumbnail is being displayed on a page, clicking on the thumbnail should launch a popup displaying all
	 * album photos. This function handles the click event and the subsequent invocation of the popup.
	 *
	 * @return void
	 */
	function display_album() {
		$panel = $_POST['panel_id'];
		$panel = substr($panel, 28);
		$user = substr($panel, 0, strpos($panel, '-'));
		$album = substr($panel, strpos($panel, '-') + 1);
		$album = substr($album, strpos($album, '-') + 1);
		$thumb_size = 75;
		if (isset($_POST['thumb_size'])) {
			$thumb_size = $_POST['thumb_size'];
		}
		echo $this->get_gallery_images(array('user_id' => $user, 'albumid' => $album, 'view' => 'album', 'display' => 'popup', 'panel' => $panel, 'thumbsize' => $thumb_size));
		die();
	}

	/**
	 * Access Token URL
	 *
	 * @return string
	 */
	public function access_token_URL() {
		return 'https://accounts.google.com/o/oauth2/token';
	}

	public function authentication_url() {
		return 'https://accounts.google.com/o/oauth2/auth';
	}

	function parse_token($response) {
		$body = $response['body'];
		$body = json_decode($body);
		$token = array();
		$token['oauth_token'] =  $body->access_token;
		$token['oauth_token_type'] =  $body->token_type;
		$token['oauth_token_created'] =  time();
		$token['oauth_token_expires'] =  $body->expires_in;
		return $token;
	}

	function get_google_plus_url($query_url) {
		// Try Google+
		$url = Photonic_Processor::get_normalized_http_url($query_url);
		$user_and_album = substr($url, strlen('https://picasaweb.google.com/data/feed/api/'));
		$user_and_album = explode('/', $user_and_album);
		foreach ($user_and_album as $key => $value) {
			if ($value == 'user' && isset($user_and_album[$key + 1])) {
				$user = $user_and_album[$key + 1];
			}
			else if ($value == 'album' && isset($user_and_album[$key + 1])) {
				$album = $user_and_album[$key + 1];
			}
		}
		$query_args = substr($query_url, strlen($url));
		$new_url = '';
		if (isset($user) && isset($album)) {
			$new_url = 'https://plus.google.com/photos/'.$user.'/albums/'.$album.$query_args;
		}
		// https://plus.google.com/photos/104926144534698413096/albums/5818977512257357377
		return $new_url;
	}

	function get_secure_curl_response($query_url) {
		$cert = trailingslashit(PHOTONIC_PATH).'include/misc/cacert.crt';
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_HEADER, 0); // Don’t return the header, just the html
		curl_setopt($ch, CURLOPT_CAINFO, $cert); // Set the location of the CA-bundle
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return contents as a string

		$response = curl_exec ($ch);
		curl_close($ch);
		return $response;
	}
}
