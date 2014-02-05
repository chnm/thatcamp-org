<?php
/**
 * Processor for Instagram. This extends the Photonic_OAuth2_Processor class and defines methods local to Instagram.
 *
 * @package Photonic
 * @subpackage Extensions
 */

class Photonic_Instagram_Processor extends Photonic_OAuth2_Processor {
	function __construct() {
		parent::__construct();
		global $photonic_instagram_client_id, $photonic_instagram_client_secret, $photonic_instagram_disable_title_link;
		$this->client_id = $photonic_instagram_client_id;
		$this->client_secret = $photonic_instagram_client_secret;
		$this->provider = 'instagram';
		$this->oauth_version = '2.0';
		$this->response_type = 'code';
		$this->scope = 'comments relationships likes';
		$this->link_lightbox_title = empty($photonic_instagram_disable_title_link);

		$cookie = Photonic::parse_cookie();
		global $photonic_instagram_allow_oauth;
		$this->oauth_done = false;
		if ($photonic_instagram_allow_oauth && isset($cookie['instagram']) && isset($cookie['instagram']['oauth_token'])) { // OAuth2, so no Access token secret
			if ($this->is_token_expired($cookie['instagram'])) { // either access has been revoked or token has expired.
				$this->oauth_done = false;
			}
			else {
				$this->oauth_done = true;
			}
		}
		else if (!isset($cookie['instagram']) || !isset($cookie['instagram']['oauth_token'])) {
			$this->oauth_done = false;
		}
	}

	public function authentication_url() {
		return 'https://api.instagram.com/oauth/authorize';
	}

	public function access_token_url() {
		return 'https://api.instagram.com/oauth/access_token';
	}

	/**
	 * Main function that fetches the images associated with the shortcode.
	 *
	 * @param array $attr
	 * @return mixed|string|void
	 */
	public function get_gallery_images($attr = array()) {
		$attr = array_merge(array(
			'style' => 'default',
			'show_captions' => false,
			'crop' => true,
			'display' => 'page',
			'max_results' => 1000,
			'columns'    => 'auto',

			'distance' => 1000,
			'thumb_size' => 75
		), $attr);
		extract($attr);

		global $photonic_instagram_client_id;
		if (!isset($photonic_instagram_client_id) || trim($photonic_instagram_client_id) == '') {
			return __("Instagram Client ID not defined.", 'photonic');
		}

		if (!isset($thumb_size) || (isset($thumb_size) && !Photonic::check_integer(ltrim($thumb_size, 'px')))) {
			$thumb_size = 75;
		}

		$auth_required = false;
		$base_url = 'https://api.instagram.com/v1/';
		if (!isset($view) || (isset($view) && trim($view) == '')) {
			return __('The <code>view</code> parameter has to be defined.', 'photonic');
		}
		else if ($view == 'user') {
			$query_url = $base_url.'users/';
			if (!isset($kind) || (isset($kind) && $kind == 'media' && !isset($user_id))) {
				return __('The <code>user_id</code> parameter has to be defined.', 'photonic');
			}
			else if ($kind == 'recent' && isset($user_id)) {
				$query_url .= $user_id.'/media/recent';
				$display_what = 'media';
			}
			else if ($kind == 'follows' && isset($user_id)) {
				$query_url .= $user_id.'/follows';
				$display_what = 'users';
			}
			else if ($kind == 'followed-by' && isset($user_id)) {
				$query_url .= $user_id.'/followed-by';
				$display_what = 'users';
			}
			else {
				return __('Invalid <code>kind</code> parameter passed for <code>view=user</code>.', 'photonic');
			}
			$auth_required = true;
			// recent, relationships
		}
		else if ($view == 'media') {
			$query_url = $base_url.'media/';
			if (!isset($kind) && !isset($user_id) && !isset($media_id)) {
				return __('The <code>user_id</code> or <code>media_id</code> parameter has to be defined if the <code>kind</code> parameter is not set', 'photonic');
			}
			else if (isset($media_id)) { // if media id (shortcode) is specified, we use it irrespective of other parameters
				$query_url = 'http://api.instagram.com/oembed?url='.urlencode('http://instagr.am/p/'.$media_id.'/');
				$display_what = 'single-media';
			}
			else if (isset($user_id)) { // if media id is not present, but user id is present we show that user's recent media
				$query_url = $base_url.'users/'.$user_id.'/media/recent';
				$display_what = 'media';
				$auth_required = true;
			}
			else if ($kind == 'popular') { // popular media
				$query_url .= 'popular';
				$display_what = 'media';
			}
			else if ($kind == 'search') { // search
				$query_url .= 'search?';
				if (!isset($lat) || !isset($lng)) { // Requires latitude and longitude
					return __('The <code>lat</code> and <code>lng</code> parameters have to be defined for media searches', 'photonic');
				}
				else {
					$query_url .= 'lat='.$lat.'&';
					$query_url .= 'lng='.$lng.'&';
					$query_url .= 'distance='.$distance.'&';
					// timestamp fields?
					$display_what = 'media';
				}
			}
			else {
				return __('Invalid <code>kind</code> parameter passed for <code>view=media</code>.', 'photonic');
			}
		}
		else if ($view == 'tag') {
			if (!isset($tag_name)) {
				return __('The <code>tag_name</code> parameter has to be defined', 'photonic');
			}
			$query_url = $base_url.'tags/'.$tag_name.'/media/recent';
			if (isset($min_id) || isset($max_id)) {
				$query_url .= '?';
				if (isset($min_id)) {
					$query_url .= 'min_id='.$min_id.'&';
				}
				if (isset($max_id)) {
					$query_url .= 'max_id='.$max_id.'&';
				}
			}
			$display_what = 'media';
		}
		else if ($view == 'location') {
			if (!isset($location_id)) {
				return __('The <code>location_id</code> parameter has to be defined', 'photonic');
			}
			$query_url = $base_url.'locations/'.$location_id.'/media/recent';
			if (isset($min_id) || isset($max_id) || isset($min_timestamp) || isset($max_timestamp)) {
				$query_url .= '?';
				if (isset($min_id)) {
					$query_url .= 'min_id='.$min_id.'&';
				}
				if (isset($max_id)) {
					$query_url .= 'max_id='.$max_id.'&';
				}
				if (isset($min_timestamp)) {
					$query_url .= 'min_timestamp='.$min_timestamp.'&';
				}
				if (isset($max_timestamp)) {
					$query_url .= 'max_timestamp='.$max_timestamp.'&';
				}
			}
			$display_what = 'media';
		}
		else {
			return __('Invalid <code>view</code> parameter passed for Instagram.', 'photonic');
		}

		if (isset($count)) {
			if (!stripos($query_url, '?')) {
				$query_url .= '?count='.$count;
			}
			else if (substr($query_url, -1, 1) != '&' && substr($query_url, -1, 1) != '?') {
				$query_url .= '&count='.$count;
			}
			else {
				$query_url .= 'count='.$count;
			}
		}

		global $photonic_instagram_allow_oauth;
		$ret = '';
		$post_id = get_the_ID();
		if (!empty($photonic_instagram_allow_oauth) && !$this->oauth_done && $auth_required) {
			$ret .= $this->get_login_box($post_id);
		}

		return $ret.$this->make_call($query_url, $display_what, $columns, $thumb_size, $auth_required);
	}

	/**
	 * Takes a token response from a request token call, then puts it in an appropriate array.
	 *
	 * @param $response
	 */
	public function parse_token($response) {
		// TODO: Implement parse_token() method.
	}

	protected function make_call($query_url, $display_what, $columns, $thumb_size = 75, $auth_required = false) {
		global $photonic_instagram_client_id;
		$ret = '';
		$query = $query_url;
		if (substr($query, -1, 1) != '&' && !stripos($query, '?')) {
			$query .= '?';
		}
		else if (substr($query, -1, 1) != '&' && stripos($query, '?')) {
			$query .= '&';
		}
		if ($auth_required) {
			$cookie = Photonic::parse_cookie();
			if (isset($cookie['instagram']) && !$this->is_token_expired($cookie['instagram'])) {
				$query .= 'access_token='.$cookie['instagram']['oauth_token'];
			}
			else {
				return __("Please login to see this content.", 'photonic');
			}
		}
		else {
			$query .= 'client_id='.$photonic_instagram_client_id;
		}

		$response = wp_remote_request($query, array(
			'sslverify' => false,
		));

		$url = '';
		if ($display_what == 'single-media') {
			$base_url = $this->get_normalized_http_url($query);
			$parameters = $this->parse_parameters(substr($query, strlen($base_url) + 1));
			if (isset($parameters['url'])) {
				$url = $parameters['url'];
			}
		}

		if (!is_wp_error($response)) {
			if (isset($response['response']) && isset($response['response']['code'])) {
				if ($response['response']['code'] == 200) {
					$body = json_decode($response['body']);
					if (isset($body->data) && $display_what != 'single-media') {
						$data = $body->data;
						$this->gallery_index++;
						switch ($display_what) {
							case 'users':
								$ret .= $this->process_users($data, $columns, $thumb_size);
								break;

							case 'media':
							default:
								$ret .= $this->process_media($data, $columns, $thumb_size);
								break;
						}
					}
					else if ($display_what == 'single-media') {
						$ret .= $this->process_single_media($body, $url);
					}
					else {
						return __('No data returned. Unknown error', 'photonic');
					}
				}
				else if (isset($response['body'])) {
					$body = json_decode($response['body']);
					if (isset($body->meta) && isset($body->meta->error_message)) {
						return $body->meta->error_message;
					}
					else {
						return __('Unknown error', 'photonic');
					}
				}
				else if (isset($response['response']['message'])) {
					return $response['response']['message'];
				}
				else {
					return __('Unknown error', 'photonic');
				}
			}
		}
		else {
			return __('There was a problem connecting. Please try back after some time.', 'photonic');
		}
		return $ret;
	}

	function process_media($data, $columns, $thumb_size) {
		global $photonic_instagram_photos_per_row_constraint, $photonic_instagram_photos_constrain_by_padding, $photonic_instagram_photos_constrain_by_count, $photonic_instagram_photo_title_display;

		$photo_objects = $this->build_level_1_objects($data, $thumb_size);
		$row_constraints = array('constraint-type' => $photonic_instagram_photos_per_row_constraint, 'padding' => $photonic_instagram_photos_constrain_by_padding, 'count' => $photonic_instagram_photos_constrain_by_count);
		$ret = $this->generate_level_1_gallery($photo_objects, $photonic_instagram_photo_title_display, $row_constraints, $columns, 'in-page', array('thumb-width' => $thumb_size, 'thumb-height' => $thumb_size));
		$ret = "<div class='photonic-instagram-stream photonic-stream' id='photonic-instagram-stream-{$this->gallery_index}'>".$ret.'</div>';
		return $ret;
	}

	function build_level_1_objects($data, $thumb_size, $type = 'media') {
		global $photonic_instagram_main_size, $photonic_instagram_user_link;
		$level_1_objects = array();
		if ($type == 'media') {
			if ($thumb_size <= 150) {
				$url_function = 'thumbnail';
			}
			else if ($thumb_size > 150 && $thumb_size <= 306) {
				$url_function = 'low_resolution';
			}
			else {
				$url_function = 'standard_resolution';
			}
			foreach ($data as $photo) {
				if (isset($photo->type) && $photo->type == 'image' && isset($photo->images)) {
					$photo_object = array();
					$photo_object['thumbnail'] = $photo->images->{$url_function}->url;
					if (!isset($photo->images->{$photonic_instagram_main_size})) {
						$photo_object['main_image'] = $photo->images->{$url_function}->url;
					}
					else {
						$photo_object['main_image'] = $photo->images->{$photonic_instagram_main_size}->url;
					}

					if (isset($photo->caption) && isset($photo->caption->text)) {
						$photo_object['title'] = esc_attr($photo->caption->text);
					}
					else {
						$photo_object['title'] = '';
					}
					$photo_object['alt_title'] = $photo_object['title'];
					$photo_object['main_page'] = $photo->link;
					$level_1_objects[] = $photo_object;
				}
			}
		}
		else {
			foreach ($data as $user) {
				$user_object = array();
				$user_object['thumbnail'] = $user->profile_picture;
				$user_object['main_image'] = $user->profile_picture;
				if ($photonic_instagram_user_link == 'instagram' || ($photonic_instagram_user_link == 'home' && empty($user->website))) {
					$user_object['main_page'] = "http://instagram.com/{$user->username}/";
				}
				else if ($photonic_instagram_user_link == 'home' && !empty($user->website)) {
					$user_object['main_page'] = $user->website;
				}
				else {
					$user_object['main_page'] = '';
				}
				if (!empty($user->full_name)) {
					$user_object['title'] = esc_attr($user->full_name);
				}
				else {
					$user_object['title'] = esc_attr($user->username);
				}
				$user_object['alt_title'] = $user_object['title'];

				$level_1_objects[] = $user_object;
			}
		}
		return $level_1_objects;
	}

	function process_users($users, $columns, $thumb_size) {
		global $photonic_instagram_user_title_display, $photonic_instagram_users_per_row_constraint,$photonic_instagram_users_constrain_by_count, $photonic_instagram_users_constrain_by_padding;

		$user_objects = $this->build_level_1_objects($users, $thumb_size, 'user');
		$row_constraints = array('constraint-type' => $photonic_instagram_users_per_row_constraint, 'padding' => $photonic_instagram_users_constrain_by_padding, 'count' => $photonic_instagram_users_constrain_by_count);
		$thumb_size = ($thumb_size > 150) ? 150 : $thumb_size;
		$ret = $this->generate_level_1_gallery($user_objects, $photonic_instagram_user_title_display, $row_constraints, $columns, 'in-page', array('thumb-width' => $thumb_size, 'thumb-height' => $thumb_size), false, 'user');
		$ret = "<div class='photonic-instagram-stream photonic-stream' id='photonic-instagram-stream-{$this->gallery_index}'>".$ret.'</div>';
		return $ret;
	}

	function process_single_media($body, $url) {
		global $photonic_instagram_single_photo_title_display, $photonic_instagram_single_photo_link, $photonic_external_links_in_new_tab;
		$ret = '';
		$title = '';

		if (!empty($body->url)) {
			if (!empty($body->title)) {
				$title = esc_attr($body->title);
			}

			$img = '<img src="'.$body->url.'" alt="'.$title.'" />';
			if (!empty($photonic_instagram_single_photo_link)) {
				if (!empty($photonic_external_links_in_new_tab)) {
					$target = ' target="_blank" ';
				}
				else {
					$target = '';
				}
				$img = '<a href="'.$url.'" title="'.$title.'" '.$target.'>'.$img.'</a>';
			}

			if (!empty($body->title)) {
				if ($photonic_instagram_single_photo_title_display == 'header') {
					$ret = '<h3 class="photonic-single-photo-header photonic-single-instagram-photo-header">'.$title.'</h3>';
					$ret .= $img;
				}
				else if ($photonic_instagram_single_photo_title_display == 'caption') {
					$ret = "<div class='wp-caption'>".$img."<div class='wp-caption-text'>".$title."</div></div>";
				}
				else {
					$ret = $img;
				}
			}
		}
		return $ret;
	}

	function is_token_expired($token) {
		if (empty($token)) {
			return true;
		}
		if (!isset($token['oauth_token']) || !isset($token['oauth_token_created'])) {
			return true;
		}
		$url = 'https://api.instagram.com/v1/users/self/feed?access_token='.$token['oauth_token'];
		$response = wp_remote_request($url, array(
			'sslverify' => false,
		));

		if (isset($response['body'])) {
			$body = json_decode($response['body']);
			if (isset($body->meta) && isset($body->meta->code) && $body->meta->code == 200) {
				return false;
			}
		}
		return true;
	}
}