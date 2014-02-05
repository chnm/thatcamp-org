<?php
/**
 * Processor for Zenfolio photos. This extends the Photonic_Processor class and defines methods local to Zenfolio.
 *
 * @package Photonic
 * @subpackage Extensions
 */

class Photonic_Zenfolio_Processor extends Photonic_Processor {
	var $user_name, $user_agent, $token, $service_url, $secure_url, $unlocked_realms;
	function __construct() {
		parent::__construct();
		global $photonic_instagram_disable_title_link;
		$this->provider = 'zenfolio';
		$this->user_agent = "Photonic for ".get_home_url();
		$this->link_lightbox_title = empty($photonic_instagram_disable_title_link);
		$query_url = add_query_arg('dummy', 'dummy');
		$query_url = remove_query_arg('dummy');
		if (stripos($query_url, ':') === FALSE) {
			$protocol = 'http';
		}
		else {
			$protocol = substr($query_url, 0, stripos($query_url, ':'));
		}

		//$this->service_url = $protocol.'://api.zenfolio.com/api/1.6/zfapi.asmx';
		$this->service_url = 'http://api.zenfolio.com/api/1.6/zfapi.asmx';
		$this->secure_url = 'https://www.zenfolio.com/api/1.6/zfapi.asmx';
		$this->unlocked_realms = array();
	}

	/**
	 * Main function that fetches the images associated with the shortcode.
	 *
	 * @param array $attr
	 * @return mixed|string|void
	 */
	public function get_gallery_images($attr = array()) {
		global $photonic_zenfolio_thumb_size;

		$attr = array_merge(array(
			'style' => 'default',
			'columns' => 'auto',
			'thumb_size' => $photonic_zenfolio_thumb_size,
			'limit' => 20,
		), $attr);
		extract($attr);

		if (empty($limit)) {
			$limit = 20;
		}

		if (empty($columns)) {
			$columns = 'auto';
		}

		if (!isset($thumb_size)) {
			$thumb_size = $photonic_zenfolio_thumb_size;
		}

		$method = 'GetPopularPhotos';
		$params = array();
		if (!empty($view)) {
			switch ($view) {
				case 'photos':
					if (!empty($object_id)) {
						$method = 'LoadPhoto';
						if(($h = stripos($object_id, 'h')) !== false) {
							$object_id = substr($object_id, $h + 1);
							$object_id = hexdec($object_id);
						}
						else if (($p = stripos($object_id, 'p')) !== false) {
							$object_id = substr($object_id, $p + 1);
						}
						else if (strlen($object_id) == 7) {
							$object_id = hexdec($object_id);
						}

						$params['photoId'] = $object_id;
						$params['level'] = 'Full';
					}
					else if (!empty($text)) {
						$params['searchId'] = '';
						if (!empty($sort_order)) {
							$params['sortOrder'] = $sort_order; // Popularity | Date | Rank
						}
						else {
							$params['sortOrder'] = 'Date';
						}
						$params['query'] = $text;
						$params['offset'] = 0;
						$params['limit'] = $limit;
						$method = 'SearchPhotoByText';
					}
					else if (!empty($category_code)) {
						$params['searchId'] = '';
						if (!empty($sort_order)) {
							$params['sortOrder'] = $sort_order; // Popularity | Date
						}
						else {
							$params['sortOrder'] = 'Date';
						}
						$params['categoryCode'] = $category_code;
						$params['offset'] = 0;
						$params['limit'] = $limit;
						$method = 'SearchPhotoByCategory';
					}
					else if (!empty($kind)) {
						$params['offset'] = 0;
						$params['limit'] = $limit;
						switch ($kind) {
							case 'popular':
								$method = 'GetPopularPhotos';
								break;

							case 'recent':
								$method = 'GetRecentPhotos';
								break;

							default:
								return __('Invalid <code>kind</code> parameter.', 'photonic');
						}
					}
					else {
						return __('The <code>kind</code> parameter is required if <code>object_id</code> is not specified.', 'photonic');
					}
					break;

				case 'photosets':
					if (!empty($object_id)) {
						$method = 'LoadPhotoSet';
						if(($p = stripos($object_id, 'p')) !== false) {
							$object_id = substr($object_id, $p + 1);
						}

						$params['photosetId'] = $object_id;
						$params['level'] = 'Full';
						$params['includePhotos'] = true;
					}
					else if (!empty($text) && !empty($photoset_type)) {
						$params['searchId'] = '';
						if (strtolower($photoset_type) == 'gallery' || strtolower($photoset_type) == 'galleries') {
							$params['type'] = 'Gallery';
						}
						else if (strtolower($photoset_type) == 'collection' || strtolower($photoset_type) == 'collections') {
							$params['type'] = 'Collection';
						}
						else {
							return __('Invalid <code>photoset_type</code> parameter.', 'photonic');
						}

						if (!empty($sort_order)) {
							$params['sortOrder'] = $sort_order; // Popularity | Date | Rank
						}
						else {
							$params['sortOrder'] = 'Date';
						}
						$params['query'] = $text;
						$params['offset'] = 0;
						$params['limit'] = $limit;
						$method = 'SearchSetByText';
					}
					else if (!empty($category_code) && !empty($photoset_type)) {
						$params['searchId'] = '';
						if (strtolower($photoset_type) == 'gallery' || strtolower($photoset_type) == 'galleries') {
							$params['type'] = 'Gallery';
						}
						else if (strtolower($photoset_type) == 'collection' || strtolower($photoset_type) == 'collections') {
							$params['type'] = 'Collection';
						}
						else {
							return __('Invalid <code>photoset_type</code> parameter.', 'photonic');
						}

						if (!empty($sort_order)) {
							$params['sortOrder'] = $sort_order; // Popularity | Date
						}
						else {
							$params['sortOrder'] = 'Date';
						}
						$params['categoryCode'] = $category_code;
						$params['offset'] = 0;
						$params['limit'] = $limit;
						$method = 'SearchSetByCategory';
					}
					else if (!empty($kind) && !empty($photoset_type)) {
						switch ($kind) {
							case 'popular':
								$method = 'GetPopularSets';
								break;

							case 'recent':
								$method = 'GetRecentSets';
								break;

							default:
								return __('Invalid <code>kind</code> parameter.', 'photonic');
						}
						if (strtolower($photoset_type) == 'gallery' || strtolower($photoset_type) == 'galleries') {
							$params['type'] = 'Gallery';
						}
						else if (strtolower($photoset_type) == 'collection' || strtolower($photoset_type) == 'collections') {
							$params['type'] = 'Collection';
						}
						else {
							return __('Invalid <code>photoset_type</code> parameter.', 'photonic');
						}

						// These have to be after the $params['type'] assignment
						$params['offset'] = 0;
						$params['limit'] = $limit;
					}
					else if (empty($kind)) {
						return __('The <code>kind</code> parameter is required if <code>object_id</code> is not specified.', 'photonic');
					}
					else if (empty($photoset_type)) {
						return __('The <code>photoset_type</code> parameter is required if <code>object_id</code> is not specified.', 'photonic');
					}
					break;

				case 'hierarchy':
					if (empty($login_name)) {
						return __('The <code>login_name</code> parameter is required.', 'photonic');
					}
					$method = 'LoadGroupHierarchy';
					$params['loginName'] =  $login_name;
					break;

				case 'group':
					if (empty($object_id)) {
						return __('The <code>object_id</code> parameter is required.', 'photonic');
					}
					$method = 'LoadGroup';
					if(($f = stripos($object_id, 'f')) !== false) {
						$object_id = substr($object_id, $f + 1);
					}
					$params['groupId'] =  $object_id;
					$params['level'] = 'Full';
					$params['includeChildren'] = true;
					break;
			}
		}

		$this->gallery_index++;
		$response = $this->make_call($method, $params);

		if (isset($_COOKIE['photonic-zf-keyring'])) {
			$realms = $this->make_call('KeyringGetUnlockedRealms', array('keyring' => $_COOKIE['photonic-zf-keyring']));
			if (!empty($realms) && !empty($realms->result)) {
				$this->unlocked_realms = $realms->result;
			}
		}

		if (!empty($panel)) {
			$ret = "<div class='photonic-zenfolio-panel photonic-panel' id='photonic-zenfolio-panel-$panel'>";
			$display = 'popup';
		}
		else {
			$ret = "<div class='photonic-zenfolio-stream' id='photonic-zenfolio-stream-{$this->gallery_index}'>";
			$display = 'in-page';
		}
		$ret .= $this->process_response($method, $response, $columns, $display, $thumb_size);
		$ret .= "</div>\n";
		return $ret;
	}

	/**
	 * Takes a token response from a request token call, then puts it in an appropriate array.
	 *
	 * @param $response
	 */
	public function parse_token($response) {
		// TODO: Update content when authentication gets supported
	}

	/**
	 * Calls a Zenfolio method with the passed parameters. The method is called using JSON-RPC. WP's wp_remote_request
	 * method doesn't work here because of specific CURL parameter requirements.
	 *
	 * @param $method
	 * @param $params
	 * @param bool $force_secure
	 * @return array|mixed
	 */
	function make_call($method, $params, $force_secure = false) {
		$request['method'] = $method;
		$request['params'] = array_values($params);
		$request['id'] = 1;
		$bodyString = json_encode($request);
		$bodyLength = strlen($bodyString);

		$headers = array();
		$headers[] = 'Host: www.zenfolio.com';
		$headers[] = 'X-Zenfolio-User-Agent: '.$this->user_agent;
		if($this->token) {
			$headers[] = 'X-Zenfolio-Token: '.$this->token;
		}
		if (isset($_COOKIE['photonic-zf-keyring'])) {
			$headers[] = 'X-Zenfolio-Keyring: '.$_COOKIE['photonic-zf-keyring'];
		}
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Content-Length: '.$bodyLength."\r\n";
		$headers[] = $bodyString;

		$cert = trailingslashit(PHOTONIC_PATH).'include/misc/cacert.crt';

		if ($force_secure) {
			$ch = curl_init($this->service_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//			curl_setopt($ch, CURLOPT_CAINFO, $cert); // Set the location of the CA-bundle
		}
		else {
			$ch = curl_init($this->service_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		$response = curl_exec($ch);
//		print_r(curl_getinfo($ch, CURLINFO_HEADER_OUT));

		curl_close($ch);

		$response = json_decode($response);
		return $response;
	}

	/**
	 * Routing function that takes the response and redirects it to the appropriate processing function.
	 *
	 * @param $method
	 * @param $response
	 * @param $columns
	 * @param string $display
	 * @param int $thumb_size
	 * @return mixed|string|void
	 */
	function process_response($method, $response, $columns, $display = 'in-page', $thumb_size = 1) {
		if (!empty($response->result)) {
			$result = $response->result;
			$ret = '';
			switch ($method) {
				case 'GetPopularPhotos':
				case 'GetRecentPhotos':
				case 'SearchPhotoByText':
				case 'SearchPhotoByCategory':
					$ret = $this->process_photos($result, $columns, $display, $thumb_size);
					break;

				case 'LoadPhoto':
					$ret = $this->process_photo($result);
					break;

				case 'GetPopularSets':
				case 'GetRecentSets':
				case 'SearchSetByText':
				case 'SearchSetByCategory':
					$ret = $this->process_sets($result, $columns, $thumb_size);
					break;

				case 'LoadPhotoSet':
					$ret = $this->process_set($result, $columns, $display, $thumb_size);
					break;

				case 'LoadGroupHierarchy':
					$ret = $this->process_group_hierarchy($result, $columns, $display, $thumb_size);
					break;

				case 'LoadGroup':
					$ret = $this->process_group($result, $columns, $display, $thumb_size);
					break;
			}
			return $ret;
		}
		else if (!empty($response->error)) {
			if (!empty($response->error->message)) {
				return $response->error->message;
			}
			else {
				return __('Unknown error', 'photonic');
			}
		}
		else {
			return __('Unknown error', 'photonic');
		}
	}

	/**
	 * Takes an array of photos and displays each as a thumbnail. Each thumbnail, upon clicking launches a lightbox.
	 *
	 * @param $response
	 * @param $columns
	 * @param string $display
	 * @param int $thumb_size
	 * @return mixed|string|void
	 */
	function process_photos($response, $columns, $display = 'in-page', $thumb_size = 1) {
		if (!is_array($response)) {
			if (empty($response->Photos) || !is_array($response->Photos)) {
				return __('Response is not an array', 'photonic');
			}
			$response = $response->Photos;
		}

		global $photonic_zenfolio_photos_per_row_constraint, $photonic_zenfolio_photo_title_display, $photonic_zenfolio_photos_constrain_by_padding, $photonic_zenfolio_photos_constrain_by_count;
		$ret = '';
		if ($display == 'popup') {
			$ret .= "<div class='photonic-zenfolio-panel-content photonic-panel-content fix'>";
		}
		$row_constraints = array('constraint-type' => $photonic_zenfolio_photos_per_row_constraint, 'padding' => $photonic_zenfolio_photos_constrain_by_padding, 'count' => $photonic_zenfolio_photos_constrain_by_count);

		$photo_objects = $this->build_level_1_objects($response, $thumb_size);
		$ret .= $this->generate_level_1_gallery($photo_objects, $photonic_zenfolio_photo_title_display, $row_constraints, $columns, $display);

		if ($display == 'popup') {
			$ret .= $this->get_popup_tooltip($photonic_zenfolio_photo_title_display);
			$ret .= $this->get_popup_lightbox();
			$ret .= "</div>";
		}

		return $ret;
	}

	function build_level_1_objects($response, $thumb_size = 1) {
		if (!is_array($response)) {
			if (empty($response->Photos) || !is_array($response->Photos)) {
				return __('Response is not an array', 'photonic');
			}
			$response = $response->Photos;
		}

		global $photonic_zenfolio_main_size;

		$type = '$type';
		$photo_objects = array();
		foreach ($response as $photo) {
			if (empty($photo->$type) || $photo->$type != 'Photo') {
				continue;
			}
			$appendage = array();
			if (isset($photo->Sequence)) {
				$appendage[] = 'sn='.$photo->Sequence;
			}
			if (isset($photo->UrlToken)) {
				$appendage[] = 'tk='.$photo->UrlToken;
			}
//			$appendage = implode('&', $appendage);
//			if ($appendage) {
//				$appendage = '?'.$appendage;
//			}

			$photo_object = array();
			$photo_object['thumbnail'] = 'http://'.$photo->UrlHost.$photo->UrlCore.'-'.$thumb_size.'.jpg';
			$photo_object['main_image'] = 'http://'.$photo->UrlHost.$photo->UrlCore.'-'.$photonic_zenfolio_main_size.'.jpg';
			$photo_object['title'] = esc_attr($photo->Title);
			$photo_object['alt_title'] = esc_attr($photo->Title);
			$photo_object['main_page'] = $photo->PageUrl;

			$photo_objects[] = $photo_object;
		}

		return $photo_objects;
	}

	function build_level_2_objects($response, $thumb_size = 1) {
		global $photonic_zenfolio_hide_password_protected_thumbnail;
		$objects = array();
		foreach ($response as $photoset) {
			if (empty($photoset->TitlePhoto)) {
				continue;
			}
			if (!empty($photoset->AccessDescriptor) && !empty($photoset->AccessDescriptor->AccessType) && $photoset->AccessDescriptor->AccessType == 'Password' && !empty($photonic_zenfolio_hide_password_protected_thumbnail)) {
				continue;
			}

			$object = array();

			$photo = $photoset->TitlePhoto;
			$object['id_1'] = $photoset->Id;
			$object['thumbnail'] = 'http://'.$photo->UrlHost.$photo->UrlCore.'-'.$thumb_size.'.jpg';
			$object['main_page'] = $photoset->PageUrl;
			$object['title'] = esc_attr($photoset->Title);
			$object['counter'] = $photoset->PhotoCount;

			if (!empty($photoset->AccessDescriptor) && !empty($photoset->AccessDescriptor->AccessType) && $photoset->AccessDescriptor->AccessType == 'Password') {
				if (!in_array($photoset->AccessDescriptor->RealmId, $this->unlocked_realms)) {
					$object['classes'] = array('photonic-zenfolio-set-passworded');
				}
			}
			$objects[] = $object;
		}
		return $objects;
	}

	/**
	 * Prints a single photo with the title as an <h3> and the caption as the image caption.
	 *
	 * @param $photo
	 * @return string
	 */
	function process_photo($photo) {
		$type = '$type';
		if (empty($photo->$type) || $photo->$type != 'Photo') {
			return '';
		}
		$ret = '';
		if (!empty($photo->Title)) {
			$ret .= '<h3>'.$photo->Title.'</h3>';
		}
		global $photonic_zenfolio_main_size, $photonic_external_links_in_new_tab;
		if (!empty($photonic_external_links_in_new_tab)) {
			$target = " target='_blank' ";
		}
		else {
			$target = '';
		}

		$orig = 'http://'.$photo->UrlHost.$photo->UrlCore.'-'.$photonic_zenfolio_main_size.'.jpg';
		$img = '<img src="'.$orig.'" alt="'.esc_attr($photo->Caption).'" />';
		$img = '<a href="'.$photo->PageUrl.'" title="'.esc_attr($photo->Title).'" '.$target.' >'.$img.'</a>';
		if (!empty($photo->Caption)) {
			$ret .= "<div class='wp-caption'>".$img."<div class='wp-caption-text'>".$photo->Caption."</div></div>";
		}
		else {
			$ret .= $img;
		}
		return $ret;
	}

	/**
	 * Takes an array of photosets and displays a thumbnail for each of them. Password-protected thumbnails might be excluded via the options.
	 *
	 * @param $response
	 * @param $columns
	 * @param int $thumb_size
	 * @return mixed|string|void
	 */
	function process_sets($response, $columns, $thumb_size = 1) {
		if (!is_array($response)) {
			if (empty($response->PhotoSets) || !is_array($response->PhotoSets)) {
				return __('Response is not an array', 'photonic');
			}
			$response = $response->PhotoSets;
		}

		global $photonic_zenfolio_sets_per_row_constraint, $photonic_zenfolio_sets_constrain_by_count, $photonic_picasa_photos_pop_constrain_by_padding,
			$photonic_zenfolio_set_title_display, $photonic_zenfolio_hide_set_photos_count_display;
		$row_constraints = array('constraint-type' => $photonic_zenfolio_sets_per_row_constraint, 'padding' => $photonic_picasa_photos_pop_constrain_by_padding, 'count' => $photonic_zenfolio_sets_constrain_by_count);
		$objects = $this->build_level_2_objects($response, $thumb_size);
		$ret = $this->generate_level_2_gallery($objects, $row_constraints, $columns, 'photosets', 'set', $photonic_zenfolio_set_title_display, $photonic_zenfolio_hide_set_photos_count_display);
		return $ret;
	}

	/**
	 * Displays a header with a basic summary for a photoset, along with thumbnails for all associated photos.
	 *
	 * @param $response
	 * @param $columns
	 * @param string $display
	 * @param int $thumb_size
	 * @return string
	 */
	function process_set($response, $columns, $display = 'in-page', $thumb_size = 1) {
		$ret = '';
		if (is_array($response->Photos)) {
			global $photonic_zenfolio_link_set_page, $photonic_zenfolio_hide_set_thumbnail, $photonic_zenfolio_hide_set_title, $photonic_zenfolio_hide_set_photo_count;

			$header = $this->get_header_object($response, $thumb_size);
			$hidden = array('thumbnail' => !empty($photonic_zenfolio_hide_set_thumbnail), 'title' => !empty($photonic_zenfolio_hide_set_title), 'counter' => !empty($photonic_zenfolio_hide_set_photo_count));
			$counters = array('photos' => $response->ImageCount);

			$ret .= $this->process_object_header($header, 'set', $hidden, $counters, empty($photonic_zenfolio_link_set_page), $display);
			$ret .= $this->process_photos($response->Photos, $columns, $display, $thumb_size);
		}
		return $ret;
	}

	/**
	 * Takes a Zenfolio response object and converts it into an associative array with a title, a thumbnail URL and a link URL.
	 *
	 * @param $object
	 * @param $thumb_size
	 * @return array
	 */
	public function get_header_object($object, $thumb_size) {
		$header = array();

		if (!empty($object->Title)) {
			$header['title'] = $object->Title;
			if (!empty($object->TitlePhoto)) {
				$photo = $object->TitlePhoto;
				$header['thumb_url'] = 'http://' . $photo->UrlHost . $photo->UrlCore . '-' . $thumb_size . '.jpg';
				$header['link_url'] = $object->PageUrl;
			}
		}
		return $header;
	}

	/**
	 * For a given user this prints out the group hierarchy. This starts with the root level and first prints all immediate
	 * children photosets. It then goes into each child group and recursively displays the photosets for each of them in separate sections.
	 *
	 * @param $response
	 * @param $columns
	 * @param string $display
	 * @param int $thumb_size
	 * @return mixed|string|void
	 */
	function process_group_hierarchy($response, $columns, $display = 'in-page', $thumb_size = 1) {
		if (empty($response->Elements)) {
			return __('No galleries, collections or groups defined for this user', 'photonic');
		}

		$ret = $this->process_group($response, $columns, $display, $thumb_size);
		return $ret;
	}

	/**
	 * For a given group this displays the immediate children photosets and then recursively displays all children groups.
	 *
	 * @param $group
	 * @param $columns
	 * @param string $display
	 * @param int $thumb_size
	 * @return string
	 */
	function process_group($group, $columns, $display = 'in-page', $thumb_size = 1) {
		$ret = '';
		$type = '$type';
		if (!isset($group->Elements)) {
			$object_id = $group->Id;
			$method = 'LoadGroup';
			if(($f = stripos($object_id, 'f')) !== false) {
				$object_id = substr($object_id, $f + 1);
			}
			$params = array();
			$params['groupId'] =  $object_id;
			$params['level'] = 'Full';
			$params['includeChildren'] = true;
			$response = $this->make_call($method, $params);
			if (!empty($response->result)) {
				$group = $response->result;
			}
		}

		if (empty($group->Elements)) {
			return '';
		}

		$elements = $group->Elements;
		$photosets = array();
		$groups = array();
		global $photonic_zenfolio_hide_password_protected_thumbnail;
		$image_count = 0;
		foreach ($elements as $element) {
			if ($element->$type == 'PhotoSet') {
				if (!empty($element->AccessDescriptor) && !empty($element->AccessDescriptor->AccessType) && $element->AccessDescriptor->AccessType == 'Password' && !empty($photonic_zenfolio_hide_password_protected_thumbnail)) {
					continue;
				}
				$photosets[] = $element;
				$image_count += $element->ImageCount;
			}
			else if ($element->$type == 'Group') {
				$groups[] = $element;
			}
		}

		global $photonic_zenfolio_hide_empty_groups;
		if (!empty($group->Title) && ($image_count > 0 || empty($photonic_zenfolio_hide_empty_groups))) {
			global $photonic_zenfolio_link_group_page, $photonic_zenfolio_hide_group_title, $photonic_zenfolio_hide_group_photo_count, $photonic_zenfolio_hide_group_group_count, $photonic_zenfolio_hide_group_set_count;
			$header = $this->get_header_object($group, $thumb_size);
			$hidden = array(
				'thumbnail' => true,
				'title' => !empty($photonic_zenfolio_hide_group_title),
				'counter' => !(empty($photonic_zenfolio_hide_group_photo_count) || empty($photonic_zenfolio_hide_group_group_count) || empty($photonic_zenfolio_hide_group_set_count)),
			);
			$counters = array(
				'sets' => empty($photonic_zenfolio_hide_group_set_count) ? count($photosets) : 0,
				'groups' => empty($photonic_zenfolio_hide_group_group_count) ? count($groups) : 0,
				'photos' => empty($photonic_zenfolio_hide_group_photo_count)? $image_count : 0,
			);

			$ret .= $this->process_object_header('zenfolio', $header, 'set', $hidden, $counters, empty($photonic_zenfolio_link_group_page), $display);
		}

		$ret .= $this->process_sets($photosets, $columns, $thumb_size);

		foreach ($groups as $group) {
			$ret .= $this->process_group($group, $columns, $display, $thumb_size);
		}

		return $ret;
	}

	/**
	 * Displays a popup photoset. This is invoked upon clicking on a photoset thumbnail on the main page.
	 */
	function display_set() {
		$panel = $_POST['panel_id'];
		$panel = substr($panel, 28);
		$set = substr($panel, 0, strpos($panel, '-'));
		$thumb_size = $_POST['thumb_size'];
		echo $this->get_gallery_images(array('view' => 'photosets', 'object_id' => $set, 'panel' => $panel, 'thumb_size' => $thumb_size));
		die();
	}

	function verify_password() {
		if (empty($_REQUEST['photonic-zenfolio-password'])) {
			return __('Please enter a password.', 'photonic');
		}
		else if (empty($_REQUEST['photonic-zenfolio-realm'])) {
			return __('Unknown error.', 'photonic');
		}
		else {
			$method = 'KeyringAddKeyPlain';
			$params = array();
			$params['keyring'] = isset($_COOKIE['photonic-zf-keyring']) ? $_COOKIE['photonic-zf-keyring'] : '';
			$params['realmId'] = $_REQUEST['photonic-zenfolio-realm'];
			$params['password'] = $_REQUEST['photonic-zenfolio-password'];

			$response = $this->make_call($method, $params, true);

			if (!empty($response->result)) {
				$result = $response->result;
				setcookie('photonic-zf-keyring', $result, time() + 365 * 60 * 60 * 24, COOKIEPATH);
				return 'Success'; // NOT TO BE TRANSLATED!!!
			}
			else if (!empty($response->error)) {
				if (!empty($response->error->message)) {
					return $response->error->message;
				}
				else {
					return __('Unknown error', 'photonic');
				}
			}
			else {
				return __('Unknown error', 'photonic');
			}
		}
	}
}
