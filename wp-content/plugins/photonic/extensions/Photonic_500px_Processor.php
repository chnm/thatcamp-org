<?php
/**
 * Processor for 500px. This extends the Photonic_Processor class and defines methods local to 500px.
 *
 * @package Photonic
 * @subpackage Extensions
 */

class Photonic_500px_Processor extends Photonic_OAuth1_Processor {
	function __construct() {
		parent::__construct();
		global $photonic_500px_api_key, $photonic_500px_api_secret, $photonic_500px_disable_title_link;
		$this->api_key = $photonic_500px_api_key;
		$this->api_secret = $photonic_500px_api_secret;
		$this->provider = '500px';
		$this->link_lightbox_title = empty($photonic_500px_disable_title_link);
	}

	/**
	 * A very flexible function to display photos from 500px. This makes use of the 500px API, hence it requires the user's Consumer API key.
	 * The API key is defined in the options. The function makes use of one API call:
	 *  <a href='http://developer.500px.com/docs/photos-index'>GET Photos</a> - for retrieving photos based on search critiera
	 *
	 * The following short-code parameters are supported:
	 * - feature: popular | upcoming | editors | fresh_today | fresh_yesterday | fresh_week | user | user_friends | user_favorites
	 * - user_id, username: Any one of them is required if feature = user | user_friends | user_favorites
	 * - only: 	Abstract | Animals | Black and White | Celebrities | City and Architecture | Commercial | Concert | Family | Fashion | Street | Travel |
	 * 			Film | Fine Art | Food | Journalism | Landscapes | Macro | Nature | Nude | People | Performing Arts | Sport | Still Life | Underwater
	 * - rpp: Number of photos
	 * - thumb_size: Size of the thumbnail. Can be 1 | 2 | 3, which correspond to 75 &times; 75 px, 140 &times; 140 px and 280 &times; 280 px respectively.
	 * - main_size: Size of the opened main photo. Can be 3 | 4, which correspond to 280 &times; 280 px and the full size respectively.
	 * - sort: created_at | rating | times_viewed | taken_at
	 *
	 * @param array $attr
	 * @return string|void
	 */
	function get_gallery_images($attr = array()) {
		global $photonic_500px_api_key;

		$attr = array_merge(array(
			'style' => 'default',
			'date_to'   => strftime("%F", time() + 86400), // date format yyyy-mm-dd
			'date_from'   => '',
			//		'feature' => ''  // popular | upcoming | editors | fresh_today | fresh_yesterday | fresh_week
			// Defaults from WP ...
			'columns'    => 'auto',
			'thumb_size'       => '1',
			'main_size'       => '4',
			'view' => 'photos',
			'rpp' => 20
		), $attr);
		$attr = array_map('trim', $attr);

		extract($attr);

		if (!isset($photonic_500px_api_key) || trim($photonic_500px_api_key) == '') {
			return __("500px Consumer Key not defined", 'photonic');
		}

		$user_feature = false;
		$base_query = 'https://api.500px.com/v1/photos';
		if ((isset($view) && ($view == 'collections' || $view == 'sets'))) {
			if (!isset($view_id)) {
				return __("The id for the collection is missing", 'photonic');
			}
			$base_query = 'https://api.500px.com/v1/collections';
		}
		else if ((isset($view) && $view == 'users')) {
			$base_query = 'https://api.500px.com/v1/users';
		}

		if (isset($view_id)) {
			$base_query .= '/'.$view_id;
		}
		else if (isset($tag) || isset($term)) {
			$base_query .= '/search';
		}
		else if ((isset($view) && $view == 'users') && (isset($id) || isset($username) || isset($email))) {
			$base_query .= '/show';
		}

		$query_url = $base_query.'?consumer_key='.$photonic_500px_api_key;
//		$query_url = $base_query.'?';
		if (isset($feature) && $feature != '') {
			$feature = esc_html($feature);
			$query_url .= '&feature='.$feature;
			if (in_array($feature, array('user', 'user_friends', 'user_favorites'))) {
				$user_feature = true;
			}
		}

		$user_set = false;
		if (isset($user_id) && $user_id != '') {
			$query_url .= '&user_id='.$user_id;
			$query_url .= '&id='.$user_id;
			$user_set = true;
		}
		else if (isset($username) && $username != '') {
			$query_url .= '&username='.$username;
			$user_set = true;
		}

		if (isset($id) && $id != '') {
			$query_url .= '&id='.$id;
		}

		if (isset($email) && $email != '') {
			$query_url .= '&email='.$email;
		}

		if ($user_feature && !$user_set) {
			return __("A user-specific feature has been requested, but the username or user_id is missing", 'photonic');
		}

		if (isset($only) && $only != '') {
			$only = urlencode($only);
			$query_url .= '&only='.$only;
		}

		if (isset($exclude) && $exclude != '') {
			$exclude = urlencode($exclude);
			$query_url .= '&exclude='.$exclude;
		}

		global $photonic_archive_thumbs;
		if (is_archive()) {
			if (isset($photonic_archive_thumbs) && !empty($photonic_archive_thumbs)) {
				if (isset($rpp) && $photonic_archive_thumbs < $rpp) {
					$query_url .= '&rpp='.$photonic_archive_thumbs;
					$rpp = $photonic_archive_thumbs;
					$this->show_more_link = true;
				}
				else if (isset($rpp)) {
					$query_url .= '&rpp='.$rpp;
				}
			}
			else if (isset($rpp)) {
				$query_url .= '&rpp='.$rpp;
			}
		}
		else if (isset($rpp)) {
			$query_url .= '&rpp='.$rpp;
		}

		if (isset($sort) && $sort != '') {
			$query_url .= '&sort='.$sort;
		}

		if (isset($tag) && $tag != '') {
			$query_url .= '&tag='.$tag;
		}

		if (isset($term) && $term != '') {
			$query_url .= '&term='.$term;
		}

		// Allow users to define additional query parameters
		$query_url = apply_filters('photonic_500px_query', $query_url, $attr);

		$ret = '';

		global $photonic_500px_allow_oauth, $photonic_500px_oauth_done;
		if ($photonic_500px_allow_oauth && !$photonic_500px_oauth_done) {
			$post_id = get_the_ID();
			$ret .= $this->get_login_box($post_id);
		}

		$this->gallery_index++;
		$ret .= "<div class='photonic-500px-stream photonic-stream' id='photonic-500px-stream-{$this->gallery_index}'>";
		$ret .= $this->process_response($query_url, $thumb_size, $main_size, $columns, $date_from, $date_to, $rpp, $rpp, 1);
		$ret .= "</div>";
		return $ret;
	}

	/**
	 * Queries the server, then parses through the response.
	 * The date filtering capability was provided by Bart Kuipers (http://www.bartkuipers.com/).
	 *
	 * @param $url
	 * @param string $thumb_size
	 * @param string $main_size
	 * @param string $columns
	 * @param string $date_from_string
	 * @param string $date_to_string
	 * @param int $number_of_photos_to_go
	 * @param int $number_per_page
	 * @param int $page_number
	 * @return string
	 */
	function process_response($url, $thumb_size = '1', $main_size = '4', $columns = 'auto', $date_from_string = '',
		$date_to_string = '', $number_of_photos_to_go = 20, $number_per_page = 20, $page_number = 1) {
		$query_url = $this->get_query_url($url, $page_number);
		$response = wp_remote_request($query_url, array('sslverify' => false));

		if (is_wp_error($response)) {
			return "";
		}
		else if ($response['response']['code'] == 401) { // Unauthorized
			return "Sorry, you need to be authorized to see this.";
		}
		else if ($response['response']['code'] != 200 && $response['response']['code'] != '200') { // Something went wrong
			return "<!-- Currently there is an error with the server. Code: ".$response['response']['code'].", Message: ".$response['response']['message']."-->";
		}
		else {
			$content = $response['body'];
			$content = json_decode($content);
			if (isset($content->photos)) {
				return $this->process_photos($content, $url, $thumb_size, $main_size, $columns, $date_from_string,
					$date_to_string, $number_of_photos_to_go, $number_per_page, $page_number);
			}
			else if (isset($content->photo)) {
				return $this->process_single_photo($content);
			}
			else if (isset($content->collections)) {
				if (count($content->collections) == 0) {
					return 'No collections found!';
				}
				else {
					return '';
				}
			}
			else if (isset($content->users)) {
				return $this->process_users($content);
			}
			else {
				return '';
			}
		}
	}

	private function get_query_url($url, $page_number) {
		global $photonic_500px_oauth_done;
		if (stripos($url, '&page=') === false) {
			$query_url = $url . '&page=' . $page_number;
		}
		else {
			$query_url = $url;
		}

		if ($photonic_500px_oauth_done) {
			$end_point = $this->end_point();
			$normalized_url = Photonic_Processor::get_normalized_http_url($query_url);
			if (strstr($normalized_url, $end_point) > -1) {
				$params = substr($query_url, strlen($normalized_url) + 1);
				if (strlen($params) > 1) {
					$params = substr($params, 1);
				}
				$params = Photonic_Processor::parse_parameters($params);
				$signed_args = $this->sign_call($normalized_url, 'GET', $params);
				$query_url = $normalized_url . '?' . Photonic_Processor::build_query($signed_args);
				return $query_url;
			}
			return $query_url;
		}
		return $query_url;
	}

	function process_photos($content, $url, $thumb_size = '1', $main_size = '4', $columns = 'auto', $date_from_string = '',
		$date_to_string = '', $number_of_photos_to_go = 20, $number_per_page = 20, $page_number = 1) {
		global $photonic_500px_photos_per_row_constraint, $photonic_500px_photos_constrain_by_count, $photonic_500px_photos_constrain_by_padding, $photonic_500px_photo_title_display;
		$ret = '';
		if (isset($content->title)) { // A collection
			$ret .= '<h3>'.$content->title.'</h3>';
		}
		$all_photos = $this->get_all_photos($content, $url, $date_from_string, $date_to_string, $number_of_photos_to_go, $number_per_page, $page_number);
		$objects = $this->build_level_1_objects($all_photos, 'photos', $thumb_size, $main_size);
		$row_constraints = array('constraint-type' => $photonic_500px_photos_per_row_constraint, 'padding' => $photonic_500px_photos_constrain_by_padding, 'count' => $photonic_500px_photos_constrain_by_count);
		$ret .= $this->generate_level_1_gallery($objects, $photonic_500px_photo_title_display, $row_constraints, $columns);

		if ($ret != '' && $page_number === 1) {
			if ($this->show_more_link) {
				$ret .= $this->more_link_button(get_permalink().'#photonic-500px-stream-'.$this->gallery_index);
			}
		}
		return $ret;
	}

	function build_level_1_objects($dpx_objects, $type, $thumb_size = '1', $main_size = '4') {
		$objects = array();
		foreach ($dpx_objects as $dpx_object) {
			$object = array();
			if ($type == 'photos') {
				$image = $dpx_object->image_url;
				$first = substr($image, 0, strrpos($image, '/'));
				$last = substr($image, strrpos($image, '/'));
				$extension = substr($last, stripos($last, '.'));

				$object['thumbnail'] = "$first/$thumb_size$extension";
				$object['main_image'] = "$first/$main_size$extension";
				$object['main_page'] = "http://500px.com/photo/".$dpx_object->id;
				$object['title'] = esc_attr($dpx_object->name);
				$object['alt_title'] = $object['title'];
			}
			else {
				if (isset($dpx_object->domain)) {
					$url = parse_url($dpx_object->domain);
					if (!isset($url['scheme'])) {
						$url = 'http://'.$url['path'];
					}
					else {
						$url = $url['scheme'].'://'.$url['path'];
					}
					$object['main_page'] = $url;
				}
				if (isset($dpx_object->userpic_url)) {
					$pic_url = parse_url($dpx_object->userpic_url);
					if (!isset($pic_url['scheme'])) {
						$pic_url = 'http://500px.com'.$pic_url['path'];
					}
					else {
						$pic_url = $dpx_object->userpic_url;
					}
					$object['thumbnail'] = $pic_url;
					$object['main_image'] = $pic_url;

					$alt = '';
					if (isset($dpx_object->fullname)) {
						$alt .= $dpx_object->fullname;
					}
					else {
						$alt .= $dpx_object->username;
					}
					if (isset($dpx_object->photos_count)) {
						$alt .= '<br/>'.sprintf(__('%1$s photos', 'photonic'), $dpx_object->photos_count);
					}
					if (isset($dpx_object->friends_count)) {
						$alt .= '<br/>'.sprintf(__('%1$s friends', 'photonic'), $dpx_object->friends_count);
					}
					if (isset($dpx_object->followers_count)) {
						$alt .= '<br/>'.sprintf(__('%1$s followers', 'photonic'), $dpx_object->followers_count);
					}
					$object['title'] = esc_attr($alt);
					$object['alt_title'] = esc_attr($alt);
				}
			}
			$objects[] = $object;
		}
		return $objects;
	}

	function get_all_photos($content, $url, $date_from_string = '', $date_to_string = '', $number_of_photos_to_go = 20, $number_per_page = 20, $page_number = 1) {
		$photos = $content->photos;
		$all_photos_found = false;

		$selected_photos = array();
		foreach ($photos as $photo) {
			$timestamp = strtotime($photo->created_at);
			if ($timestamp !== false) {
				$date_from = strtotime($date_from_string);
				if ($date_from === false) {
					$date_from = 1;
				}
				if ($timestamp < $date_from) {
					$all_photos_found = true;
					continue;
				}

				$date_to = strtotime($date_to_string);
				if ($date_to === false) {
					$date_to = gettimeofday();
					$date_to = $date_to['sec'] + 86400; // tomorrow's date
				}
				if ($timestamp > $date_to) {
					$all_photos_found = true;
					continue;
				}
			}
			if ($number_of_photos_to_go <= 0) {
				continue;
			}
			$selected_photos[] = $photo;
			$number_of_photos_to_go--;
		}
		if ($number_of_photos_to_go > 0 && $all_photos_found != true && count($photos) >= $number_per_page ) {
			$new_query = $this->get_query_url($url, $page_number + 1);
			$more_photos = $this->get_all_photos($content, $new_query, $date_from_string, $date_to_string, $number_of_photos_to_go, $number_per_page, $page_number + 1);
			$selected_photos = array_merge($selected_photos, $more_photos);
		}
		return $selected_photos;
	}

	function process_single_photo($content) {
		if (isset($content->photo)) {
			$photo = $content->photo;
			$ret = '';
			if (isset($photo->name) && !empty($photo->name)) {
				$ret .= '<h3 class="photonic-single-photo-header photonic-single-500px-photo-header">'.$photo->name.'</h3>';
			}
			$img = '<img src="'.$photo->image_url.'" alt="'.esc_attr($photo->name).'">';
			if (isset($photo->description) && !empty($photo->description)) {
				$ret .= '<div class="wp-caption">';
				$ret .= $img;
				$ret .= '<div class="wp-caption-text">'.$photo->description.'</div>';
				$ret .='</div>';
			}
			else {
				$ret .= $img;
			}
			return $ret;
		}
		else {
			return '';
		}
	}

	function process_users($content, $columns = 'auto') {
		if (isset($content->users)) {
			if (count($content->users) == 0) {
				return 'No users found!';
			}
			else {
				global $photonic_500px_photos_per_row_constraint, $photonic_500px_photos_constrain_by_padding, $photonic_500px_photos_constrain_by_count;
				$users = $content->users;
				$objects = $this->build_level_1_objects($users, 'users');
				$row_constraints = array('constraint-type' => $photonic_500px_photos_per_row_constraint, 'padding' => $photonic_500px_photos_constrain_by_padding, 'count' => $photonic_500px_photos_constrain_by_count);
				return $this->generate_level_1_gallery($objects, 'tooltip', $row_constraints, $columns, 'in-page', array(), false, 'user');
			}
		}
		else {
			return '';
		}
	}

	/**
	 * Access Token URL
	 *
	 * @return string
	 */
	public function access_token_URL() {
		return 'https://api.500px.com/v1/oauth/access_token';
	}

	/**
	 * Authenticate URL
	 *
	 * @return string
	 */
	public function authenticate_URL() {
		return 'https://api.500px.com/v1/oauth/authorize';
	}

	/**
	 * Authorize URL
	 *
	 * @return string
	 */
	public function authorize_URL() {
		return 'https://api.500px.com/v1/oauth/authorize';
	}

	/**
	 * Request Token URL
	 *
	 * @return string
	 */
	public function request_token_URL() {
		return 'https://api.500px.com/v1/oauth/request_token';
	}

	public function end_point() {
		return 'https://api.500px.com/v1/';
	}

	function parse_token($response) {
		$body = $response['body'];
		$token = Photonic_Processor::parse_parameters($body);
		return $token;
	}

	public function check_access_token_method() {
		// TODO: Implement check_access_token_method() method.
	}

	/**
	 * Method to validate that the stored token is indeed authenticated.
	 *
	 * @param $request_token
	 * @return array|WP_Error
	 */
	function check_access_token($request_token) {
		$signed_parameters = $this->sign_call('https://api.500px.com/v1/users', 'GET', array());
//		$end_point = $this->end_point();
		$end_point = 'https://api.500px.com/v1/users?'.Photonic_Processor::build_query($signed_parameters);
		$response = Photonic::http($end_point, 'GET', null);

		return $response;
	}

	public function is_access_token_valid($response) {
		if (is_wp_error($response)) {
			return false;
		}
		$response = $response['response'];
		if ($response['code'] == 200) {
			return true;
		}
		return false;
	}
}