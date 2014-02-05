<?php
class Photonic_SmugMug_Processor extends Photonic_OAuth1_Processor {
	function __construct() {
		parent::__construct();
		global $photonic_smug_api_key, $photonic_smug_api_secret, $photonic_smug_disable_title_link;
		$this->api_key = $photonic_smug_api_key;
		$this->api_secret = $photonic_smug_api_secret;
		$this->provider = 'smug';
		$this->link_lightbox_title = empty($photonic_smug_disable_title_link);
	}

	/**
	 * The main gallery builder for SmugMug. SmugMug takes the following parameters:
	 * 	- nick_name = The nickname of the user. This is mandatory for SmugMug.
	 * 	- view = tree | albums | album | images. If left blank, a value of 'tree' is assumed.
	 * 	- columns = The number of columns to show the output in
	 *	- album = The album slug, which is the AlbumID_AlbumKey. Either this parameter is needed or the individual album_id and album_key are needed if view='album' or 'images'.
	 * 	- album_id, album_key = The ID and key of the album. Either both of these are needed, or the combination "album" is needed if view='album' or 'images'.
	 *	- empty = true | false. If true, empty albums and categories are returned in the response, otherwise they are ignored.
	 *	- columns = The number of columns to return the output in. Optional.
	 *
	 * @param array $attr
	 * @return string|void
	 */
	function get_gallery_images($attr = array()) {
		global $photonic_smug_api_key, $photonic_smug_thumb_size, $photonic_smug_main_size;

		if (!isset($photonic_smug_api_key) || trim($photonic_smug_api_key) == '') {
			return __("SmugMug API Key not defined", 'photonic');
		}

		$attr = array_merge(array(
			'style' => 'default',
			'columns'    => 'auto',
			'empty' => 'false',
		), $attr);
		extract($attr);

		$args = array(
			'APIKey' => $photonic_smug_api_key,
			'Empty' => $empty,
		);

		$chained_calls = array();
		if (isset($view)) {
			$view = trim($view);
		}
		else {
			$view = 'tree';
		}

		switch ($view) {
			case 'albums':
				$chained_calls[] = 'smugmug.albums.get';
				$args['Extras'] = 'URL,ImageCount,Passworded,Password,NiceName';
				break;

			case 'album':
			case 'images':
				$chained_calls[] = 'smugmug.albums.getInfo';
				$chained_calls[] = 'smugmug.images.get';

				if (isset($album_id) && trim($album_id) != '' && isset($album_key) && trim($album_key) != '') {
					$args['AlbumID'] = $album_id;
					$args['AlbumKey'] = $album_key;
					$args['Extras'] = "{$photonic_smug_thumb_size}URL,{$photonic_smug_main_size}URL,Caption,URL,Title,Passworded,Password";
				}
				else if (isset($album) && trim($album) != '') {
					$args['AlbumID'] = substr($album, 0, stripos($album, '_'));
					$args['AlbumKey'] = substr($album, stripos($album, '_') + 1);
					$args['Extras'] = "{$photonic_smug_thumb_size}URL,{$photonic_smug_main_size}URL,Caption,URL,Title,Passworded,Password";
				}

				if (isset($password) && trim($password) != '') {
					$args['Password'] = $password;
				}
				break;

			case 'tree':
			default:
				$chained_calls[] = 'smugmug.users.getTree';
				$args['Extras'] = "URL,ImageCount,Passworded";
				break;
		}

		if ($view == 'tree' || $view == 'albums') {
			if (!isset($nick_name) || (isset($nick_name) && trim($nick_name) == '')) {
				return "";
			}
		}

		if (isset($nick_name) && trim($nick_name) != '') {
			$args['NickName'] = $nick_name;
		}

		$ret = '';
		global $photonic_smug_allow_oauth, $photonic_smug_oauth_done;
		if ($photonic_smug_allow_oauth && is_single() && !$photonic_smug_oauth_done) {
			$post_id = get_the_ID();
			$ret .= $this->get_login_box($post_id);
		}

		return $ret.$this->make_chained_calls($chained_calls, $args, $attr);
	}

	/**
	 * Runs a sequence of web-service calls to get information. Most often a single web-service call with the "Extras" parameter suffices for SmugMug.
	 * But there are some scenarios, e.g. clicking on an album to get a popup of all images in that album, where you need to chain the calls for the header.
	 *
	 * @param $chained_calls
	 * @param $smug_args
	 * @param $shortcode_attr
	 * @return string
	 */
	function make_chained_calls($chained_calls, $smug_args, $shortcode_attr) {
		if (is_array($chained_calls) && count($chained_calls) > 0) {
			$this->gallery_index++;
			extract($shortcode_attr);

			$ret = '';
			global $photonic_smug_oauth_done;
			$passworded = false;
			foreach ($chained_calls as $call) {
				$smug_args['method'] = $call;
				if ($photonic_smug_oauth_done) {
					$signed_args = $this->sign_call('https://secure.smugmug.com/services/api/json/1.3.0/', 'POST', $smug_args);
					$response = Photonic::http('https://secure.smugmug.com/services/api/json/1.3.0/', 'POST', $signed_args);
				}
				else {
					$response = Photonic::http('https://secure.smugmug.com/services/api/json/1.3.0/', 'POST', $smug_args);
				}

				if ($call == 'smugmug.albums.get') {
					$body = $response['body'];
					$body = json_decode($body);
					if ($body->stat == 'ok') {
						$albums = $body->Albums;
						if (is_array($albums) && count($albums) > 0) {
							$album_text = $this->process_albums($albums, $columns);
							if (!empty($album_text)) {
								$ret .= "<div class='photonic-smug-stream photonic-stream' id='photonic-smug-stream-{$this->gallery_index}'>";
								$ret .= $album_text;
								$ret .= "</div>";
							}
						}
					}
				}
				else if ($call == 'smugmug.albums.getInfo') {
					$body = $response['body'];
					$body = json_decode($body);
					if ($body->stat == 'ok') {
						$album = $body->Album;
						if (isset($album->Passworded) && $album->Passworded && !isset($album->Password) && !isset($signed_args['Password'])) {
							$passworded = true;
						}
						$header_object = array();
						$rand = rand(1000, 9999);
						$header_object['thumb_url'] = "https://secure.smugmug.com/photos/random.mg?AlbumID={$album->id}&AlbumKey={$album->Key}&Size=75x75&rand=$rand";
						$header_object['title'] = $album->Title;
						$header_object['link_url'] = $album->URL;

						global $photonic_smug_disable_title_link, $photonic_smug_hide_album_thumbnail, $photonic_smug_hide_album_title, $photonic_smug_hide_album_photo_count;
						$hidden = array(
							'thumbnail' => !empty($photonic_smug_hide_album_thumbnail),
							'title' => !empty($photonic_smug_hide_album_title),
							'counter' => !empty($photonic_smug_hide_album_photo_count),
						);
						$counters = array('photos' => $album->ImageCount);
						if (empty($display)) {
							$display = 'in-page';
						}
						$insert = $this->process_object_header($header_object, 'album', $hidden, $counters, empty($photonic_smug_disable_title_link), $display);

						if (isset($shortcode_attr['display']) && $shortcode_attr['display'] == 'popup') {
							// Do nothing. We will insert this into the popup.
						}
						else {
							$ret .= $insert;
						}
					}
					else if ($body->stat == 'fail' && $body->code == 31) {
						$passworded = false;
					}
				}
				else if ($call == 'smugmug.images.get') {
					if (!$passworded) {
						if (isset($insert)) {
							$ret .= $this->process_images($response, $columns, $shortcode_attr, $insert);
						}
						else {
							$ret .= $this->process_images($response, $columns, $shortcode_attr);
						}
					}
				}
				else if ($call == 'smugmug.users.getTree') {
					$body = $response['body'];
					$body = json_decode($body);
					if ($body->stat == 'ok') {
						$categories = $body->Categories;
						if (is_array($categories) && count($categories) > 0) {
							$ret .= "<ul class='photonic-tree'>";
							foreach ($categories as $category) {
								if (isset($category->Albums)) {
									$albums = $category->Albums;
									$album_text = $this->process_albums($albums, $columns);
									if (!empty($album_text)) {
										$ret .= "<li>";
										$ret .= "<div class='photonic-smug-category'><span class='photonic-header-title photonic-category-title'>{$category->Name}</span></div>";
										$ret .= $album_text;
										$ret .= "</li>";
									}
								}

								if (isset($category->SubCategories)) {
									$sub_categories = $category->SubCategories;
									$ret .= "<li>";
									if (is_array($sub_categories) && count($sub_categories) > 0) {
										$ret .= "<ul class='photonic-sub-tree'>";
										foreach ($sub_categories as $sub_category) {
											$albums = $sub_category->Albums;
											$ret .= "<li>";
											$ret .= "<div class='photonic-smug-sub-category'><span class='photonic-header-title photonic-sub-category-title'>{$sub_category->Name}</span></div>";
											$ret .= $this->process_albums($albums, $columns);
											$ret .= "</li>";
										}
										$ret .= "</ul>";
									}
									$ret .= "</li>";
								}
							}
							$ret .= "</ul>";
						}
					}
				}
			}
			return $ret;
		}
		return '';
	}

	/**
	 * Parse an array of album objects returned by the SmugMug API, then return an appropriate response. For every album a random thumbnail
	 * is generated using a call to https://secure.smugmug.com/photos/random.mg, because SmugMug doesn't return a thumbnail for an album.
	 *
	 * @param $albums
	 * @param $columns
	 * @return string
	 */
	function process_albums($albums, $columns) {
		global $photonic_smug_albums_album_per_row_constraint, $photonic_smug_albums_album_constrain_by_count, $photonic_smug_albums_album_constrain_by_padding, $photonic_smug_albums_album_title_display, $photonic_smug_hide_albums_album_photos_count_display;
		$objects = $this->build_level_2_objects($albums);
		$row_constraints = array('constraint-type' => $photonic_smug_albums_album_per_row_constraint, 'padding' => $photonic_smug_albums_album_constrain_by_padding, 'count' => $photonic_smug_albums_album_constrain_by_count);
		$ret = $this->generate_level_2_gallery($objects, $row_constraints, $columns, 'albums', 'album', $photonic_smug_albums_album_title_display, $photonic_smug_hide_albums_album_photos_count_display);
		return $ret;
	}

	/**
	 * Takes a response, then parses out the images from that response and returns a set of thumbnails for it. This method handles
	 * both, in-page images as well as images in a popup panel.
	 *
	 * @param $response
	 * @param string $columns
	 * @param array $attr
	 * @param null $insert
	 * @return string
	 */
	function process_images($response, $columns = 'auto', $attr = array(), $insert = null) {
		global $photonic_smug_photos_per_row_constraint, $photonic_smug_photos_constrain_by_count, $photonic_smug_photos_constrain_by_padding,
			$photonic_smug_photos_pop_per_row_constraint, $photonic_smug_photos_pop_constrain_by_count,
			$photonic_smug_photos_pop_constrain_by_padding, $photonic_smug_photo_title_display, $photonic_smug_photo_pop_title_display;
		$body = $response['body'];
		$body = json_decode($body);
		if ($body->stat == 'ok') {
			$album = $body->Album;
			$images = $album->Images;
			$photo_objects = $this->build_level_1_objects($images);

			$ret = "";
			if (!empty($photo_objects)) {
				if (isset($attr['display']) && $attr['display'] == 'popup') {
					$popup_id = '';
					if (isset($attr['panel'])) {
						$popup_id = "id='photonic-smug-panel-".$attr['panel']."'";
					}
					$ret .= "<div class='photonic-smug-panel photonic-panel' $popup_id>";
					$ret .= $insert;
					$ret .= "<div class='photonic-smug-panel-content photonic-panel-content'>";
					$row_constraints = array('constraint-type' => $photonic_smug_photos_pop_per_row_constraint, 'padding' => $photonic_smug_photos_pop_constrain_by_padding, 'count' => $photonic_smug_photos_pop_constrain_by_count);
					$ret .= $this->generate_level_1_gallery($photo_objects, $photonic_smug_photo_pop_title_display, $row_constraints, $columns, 'popup');

					$ret .= $this->get_popup_tooltip($photonic_smug_photo_pop_title_display);
					$ret .= $this->get_popup_lightbox();
					$ret .= "</div>";
				}
				else {
					$ret .= "<div class='photonic-smug-stream photonic-stream' id='photonic-smug-stream-{$this->gallery_index}'>";
					$row_constraints = array('constraint-type' => $photonic_smug_photos_per_row_constraint, 'padding' => $photonic_smug_photos_constrain_by_padding, 'count' => $photonic_smug_photos_constrain_by_count);
					$ret .= $this->generate_level_1_gallery($photo_objects, $photonic_smug_photo_title_display, $row_constraints, $columns, 'popup');
				}
				$ret .= "</div>";
				return $ret;
			}
		}
		return "";
	}

	function build_level_1_objects($images) {
		$photo_objects = array();
		if (is_array($images) && count($images) > 0) {
			global $photonic_smug_thumb_size, $photonic_smug_main_size;
			foreach ($images as $image) {
				$photo_object = array();
				$thumb = "{$photonic_smug_thumb_size}URL";
				$main = "{$photonic_smug_main_size}URL";
				$photo_object['thumbnail'] = $image->{$thumb};
				$photo_object['main_image'] = $image->{$main};
				$photo_object['title'] = esc_attr($image->Caption);
				$photo_object['alt_title'] = esc_attr($image->Caption);
				$photo_object['main_page'] = $image->URL;

				$photo_objects[] = $photo_object;
			}
		}
		return $photo_objects;
	}

	function build_level_2_objects($albums) {
		global $photonic_smug_thumb_size, $photonic_smug_hide_password_protected_thumbnail;
		$objects = array();
		if (is_array($albums) && count($albums) > 0) {
			$rand = rand(1000, 9999);
			foreach ($albums as $album) {
				if (!empty($photonic_smug_hide_password_protected_thumbnail) && isset($album->Passworded) && $album->Passworded && !isset($album->Password)) {
					continue;
				}
				if ($album->ImageCount != 0) {
					$object = array();
					$object['id_1'] = $album->id.'-'.$album->Key;
					$object['thumbnail'] = "https://secure.smugmug.com/photos/random.mg?AlbumID={$album->id}&AlbumKey={$album->Key}&Size=$photonic_smug_thumb_size&rand=$rand";
					$object['main_page'] = $album->URL;
					$object['title'] = esc_attr($album->Title);
					$object['counter'] = $album->ImageCount;

					if (isset($album->Passworded) && $album->Passworded && !isset($album->Password)) {
						$object['classes'] = array('photonic-smug-passworded');
					}
					$objects[] = $object;
				}
			}
		}
		return $objects;
	}

	/**
	 * If a SmugMug album thumbnail is being displayed on a page, clicking on the thumbnail should launch a popup displaying all
	 * album photos. This function handles the click event and the subsequent invocation of the popup.
	 *
	 * @return void
	 */
	function display_album() {
		$panel = $_POST['panel_id'];
		$panel = substr($panel, 26);
		$album_id = substr($panel, 0, strpos($panel, '-'));
		$album_key = substr($panel, strpos($panel, '-') + 1);
		$album_key = substr($album_key, 0, strpos($album_key, '-'));
		echo $this->get_gallery_images(array('album_id' => $album_id, 'album_key' => $album_key, 'view' => 'album', 'display' => 'popup', 'panel' => $panel));
		die();
	}

	/**
	 * Access Token URL
	 *
	 * @return string
	 */
	public function access_token_URL() {
		return 'smugmug.auth.getAccessToken';
	}

	/**
	 * Authenticate URL
	 *
	 * @return string
	 */
	public function authenticate_URL() {
		return 'http://api.smugmug.com/services/oauth/authorize.mg';
	}

	/**
	 * Authorize URL
	 *
	 * @return string
	 */
	public function authorize_URL() {
		return 'http://api.smugmug.com/services/oauth/authorize.mg';
	}

	/**
	 * Request Token URL
	 *
	 * @return string
	 */
	public function request_token_URL() {
		return 'smugmug.auth.getRequestToken';
	}

	public function end_point() {
		return 'https://secure.smugmug.com/services/api/json/1.3.0/';
	}

	function parse_token($response) {
		$body = $response['body'];
		$body = json_decode($body);

		if ($body->stat == 'ok') {
			$auth = $body->Auth;
			$token = $auth->Token;
			return array('oauth_token' => $token->id, 'oauth_token_secret' => $token->Secret);
		}
		return array();
	}

	public function check_access_token_method() {
		return 'smugmug.auth.checkAccessToken';
	}
}
