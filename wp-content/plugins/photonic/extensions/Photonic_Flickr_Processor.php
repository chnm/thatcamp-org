<?php
/**
 * Processor for Flickr Galleries
 *
 * @package Photonic
 * @subpackage Extensions
 */

class Photonic_Flickr_Processor extends Photonic_OAuth1_Processor {
	function __construct() {
		parent::__construct();
		global $photonic_flickr_api_key, $photonic_flickr_api_secret, $photonic_flickr_disable_title_link;
		$this->api_key = $photonic_flickr_api_key;
		$this->api_secret = $photonic_flickr_api_secret;
		$this->provider = 'flickr';
		$this->link_lightbox_title = empty($photonic_flickr_disable_title_link);
	}

	/**
	 * A very flexible function to display a user's photos from Flickr. This makes use of the Flickr API, hence it requires the user's API key.
	 * The API key is defined in the options. The function makes use of three different APIs:
	 *  1. <a href='http://www.flickr.com/services/api/flickr.photos.search.html'>flickr.photos.search</a> - for retrieving photos based on search critiera
	 *  2. <a href='http://www.flickr.com/services/api/flickr.photosets.getPhotos.html'>flickr.photosets.getPhotos</a> - for retrieving photo sets
	 *  3. <a href='http://www.flickr.com/services/api/flickr.galleries.getPhotos.html'>flickr.galleries.getPhotos</a> - for retrieving galleries
	 *
	 * The following short-code parameters are supported:
	 * All
	 * - per_page: number of photos to display
	 * - view: photos | collections | galleries | photosets, displays hierarchically if user_id is passed
	 * Photosets
	 * - photoset_id
	 * Galleries
	 * - gallery_id
	 * Photos
	 * - user_id: can be obtained from the Helpers page
	 * - tags: comma-separated list of tags
	 * - tag_mode: any | all, tells whether any tag should be used or all
	 * - text: string for text search
	 * - sort: date-posted-desc | date-posted-asc | date-taken-asc | date-taken-desc | interestingness-desc | interestingness-asc | relevance
	 * - group_id: group id for which photos will be displayed
	 *
	 * @param array $attr
	 * @return string|void
	 * @since 1.02
	 */
	function get_gallery_images($attr = array()) {
		global $photonic_flickr_api_key, $photonic_carousel_mode, $photonic_flickr_allow_oauth, $photonic_flickr_oauth_done;

		$attr = array_merge(array(
			'style' => 'default',
	//		'view' => 'photos'  // photos | collections | galleries | photosets: if only a user id is passed, what should be displayed?
			// Defaults from WP ...
			'columns'    => 'auto',
			'size'       => 's',
			'privacy_filter' => '',
			'per_page' => 100,
			'display' => 'in-page',
			'panel_id' => '',
			'page' => 1,
			'paginate' => false,
		), $attr);
		extract($attr);

		if (!isset($photonic_flickr_api_key) || trim($photonic_flickr_api_key) == '') {
			return __("Flickr API Key not defined", 'photonic');
		}

		$format = 'format=json&';

		$query_urls = array();
		$query = '&api_key='.$photonic_flickr_api_key;

		$ret = "";
		if (isset($view) && isset($user_id)) {
			switch ($view) {
				case 'collections':
					if (!isset($collection_id)) {
						$collections = $this->get_collection_list($user_id);
						foreach ($collections as $collection) {
							$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.collections.getTree&collection_id='.$collection['id'];
						}
					}
					break;

				case 'galleries':
					if (!isset($gallery_id)) {
						$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.galleries.getList';
					}
					break;

				case 'photosets':
					if (!isset($photoset_id)) {
						$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photosets.getList';
					}
					break;

				case 'photo':
					if (isset($photo_id)) {
						$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photos.getInfo';
					}
					break;

				case 'photos':
				default:
					$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photos.search';
					break;
			}
		}
		else if (isset($view) && $view == 'photos' && isset($group_id)) {
			$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photos.search';
		}
		else if (isset($view) && $view == 'photo' && isset($photo_id)) {
			$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photos.getInfo';
//			$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photos.getExif';
		}

		// Collection > galleries > photosets
		if (isset($collection_id)) {
			$collections = $this->get_collection_list($user_id, $collection_id);
			foreach ($collections as $collection) {
				$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.collections.getTree&collection_id='.$collection['id'];
			}
		}
		else if (isset($gallery_id)) {
			if (empty($gallery_id_computed)) {
				if (!isset($user_id)) {
					return __('User id is required for displaying a single gallery', 'photonic');
				}
				$temp_query = 'http://api.flickr.com/services/rest/?method=flickr.galleries.getList&user_id='.$user_id.'&api_key='.$photonic_flickr_api_key;

				if ($photonic_flickr_oauth_done) {
					$end_point = Photonic_Processor::get_normalized_http_url($temp_query);
					if (strstr($temp_query, $end_point) > -1) {
						$params = substr($temp_query, strlen($end_point));
						if (strlen($params) > 1) {
							$params = substr($params, 1);
						}
						$params = Photonic_Processor::parse_parameters($params);
						$signed_args = $this->sign_call($end_point, 'GET', $params);
						$temp_query = $end_point.'?'.Photonic_Processor::build_query($signed_args);
					}
				}

				$feed = Photonic::http($temp_query);
				if (!is_wp_error($feed) && 200 == $feed['response']['code']) {
					$feed = $feed['body'];
					$feed = simplexml_load_string($feed);
					if (is_a($feed, 'SimpleXMLElement')) {
						$main_attributes = $feed->attributes();
						if ($main_attributes['stat'] == 'ok') {
							$children = $feed->children();
							if (count($children) != 0) {
								if (isset($feed->galleries)) {
									$galleries = $feed->galleries;
									$galleries = $galleries->gallery;
									if (count($galleries) > 0) {
										$gallery = $galleries[0];
										$gallery = $gallery->attributes();
										$global_dbid = $gallery['id'];
										$global_dbid = substr($global_dbid, 0, stripos($global_dbid, '-'));
									}
								}
							}
						}
					}
				}
				if (isset($global_dbid)) {
					$gallery_id = $global_dbid.'-'.$gallery_id;
				}
			}
			$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.galleries.getInfo';
			$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.galleries.getPhotos';
		}
		else if (isset($photoset_id)) {
			$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photosets.getInfo';
			$query_urls[] = 'http://api.flickr.com/services/rest/?'.$format.'method=flickr.photosets.getPhotos';
		}

		if (isset($user_id)) {
			$query .= '&user_id='.$user_id;
		}

		if (isset($collection_id)) {
			$query .= '&collection_id='.$collection_id;
		}
		else if (isset($gallery_id)) {
			$query .= '&gallery_id='.$gallery_id;
		}
		else if (isset($photoset_id)) {
			$query .= '&photoset_id='.$photoset_id;
		}
		else if (isset($photo_id)) {
			$query .= '&photo_id='.$photo_id;
		}

		if (isset($tags)) {
			$query .= '&tags='.$tags;
		}

		if (isset($tag_mode)) {
			$query .= '&tag_mode='.$tag_mode;
		}

		if (isset($text)) {
			$query .= '&text='.$text;
		}

		if (isset($sort)) {
			$query .= '&sort='.$sort;
		}

		if (isset($group_id)) {
			$query .= '&group_id='.$group_id;
		}

		global $photonic_archive_thumbs;
		if (is_archive()) {
			if (isset($photonic_archive_thumbs) && !empty($photonic_archive_thumbs)) {
				if (isset($per_page) && $photonic_archive_thumbs < $per_page) {
					$query .= '&per_page='.$photonic_archive_thumbs;
					$this->show_more_link = true;
				}
				else if (isset($per_page)) {
					$query .= '&per_page='.$per_page;
				}
			}
			else if (isset($per_page)) {
				$query .= '&per_page='.$per_page;
			}
		}
		else if (isset($per_page)) {
			$query .= '&per_page='.$per_page;
		}

		if (!empty($page)) {
			$query .= '&page='.$page;
		}

		$login_required = false;
		if (isset($privacy_filter) && trim($privacy_filter) != '') {
			$query .= '&privacy_filter='.$privacy_filter;
			$login_required = $privacy_filter == 1 ? false : true;
		}

		// Allow users to define additional query parameters
		$query_urls = apply_filters('photonic_flickr_query_urls', $query_urls, $attr);
		$query = apply_filters('photonic_flickr_query', $query, $attr);

		if (isset($photonic_carousel_mode) && $photonic_carousel_mode == 'on') {
			$carousel = 'photonic-carousel jcarousel-skin-tango';
		}
		else {
			$carousel = '';
		}

		if ($photonic_flickr_allow_oauth && is_singular() && !$photonic_flickr_oauth_done && $login_required) {
			$post_id = get_the_ID();
			$ret .= $this->get_login_box($post_id);
		}

		if (empty($display)) {
			$display = 'in-page';
		}
		if (!isset($panel_id)) {
			$panel_id = '';
		}

		if ($display == 'in-page') {
			$ret .= "<div class='photonic-flickr-stream photonic-stream $carousel'>";
		}
		else {
			$ret .= "<div class='photonic-flickr-panel photonic-panel' id='photonic-flickr-panel-$panel_id'>";
		}
		foreach ($query_urls as $query_url) {
			$method = 'flickr.photos.getInfo';
			$iterator = array();
			$content_opened = false;
			if (is_array($query_url)) {
				$iterator = $query_url;
			}
			else {
				$iterator[] = $query_url;
				if ($display == 'popup' && (stripos($query_url, 'method=flickr.galleries.getPhotos') !== false || stripos($query_url, 'method=flickr.photosets.getPhotos') !== false)) {
					$ret .= "<div class='photonic-flickr-panel-content photonic-panel-content fix' id='photonic-flickr-panel-content-$panel_id'>";
					$content_opened = true;
				}
			}

			foreach ($iterator as $nested_query_url) {
				$this->gallery_index++;
				$merged_query = $nested_query_url.$query;

				$end_point = Photonic_Processor::get_normalized_http_url($merged_query);
				if (strstr($merged_query, $end_point) > -1) {
					$params = substr($merged_query, strlen($end_point));
					if (strlen($params) > 1) {
						$params = substr($params, 1);
					}
					$params = Photonic_Processor::parse_parameters($params);
					if (isset($params['jsoncallback'])) {
						unset($params['jsoncallback']);
					}
					$params['nojsoncallback'] = 1;
					$method = $params['method'];

					// We only worry about signing the call if the authentication is done. Otherwise we just show what is available.
					if ($photonic_flickr_oauth_done) {
						$signed_args = $this->sign_call($end_point, 'GET', $params);
						$merged_query = $end_point.'?'.Photonic_Processor::build_query($signed_args);
					}
					else {
						$merged_query = $end_point.'?'.Photonic_Processor::build_query($params);
					}
				}

				$ret .= $this->process_query($merged_query, $method, $columns, isset($user_id) ? $user_id : '', $display);
			}

			if ($content_opened) {
				global $photonic_flickr_photo_pop_title_display;
				$ret .= $this->get_popup_tooltip($photonic_flickr_photo_pop_title_display);
				$ret .= $this->get_popup_lightbox();
				$ret .= '</div>';
			}

			if ($this->show_more_link && $method != 'flickr.photosets.getInfo' && $method != 'flickr.photos.getInfo' && $method != 'flickr.galleries.getInfo') {
				$ret .= $this->more_link_button(get_permalink().'#photonic-flickr-stream-'.$this->gallery_index);
			}
		}
		$ret .= "</div>";
		return $ret;
	}

	/**
	 * Retrieves a list of collection objects for a given user. This first invokes the web-service, then iterates through the collections returned.
	 * For each collection returned it recursively looks for nested collections and sets.
	 *
	 * @param $user_id
	 * @param string $collection_id
	 * @return array
	 */
	function get_collection_list($user_id, $collection_id = '') {
		global $photonic_flickr_api_key, $photonic_flickr_oauth_done;
		$query = 'http://api.flickr.com/services/rest/?method=flickr.collections.getTree&user_id='.$user_id.'&api_key='.$photonic_flickr_api_key;
		if ($collection_id != '') {
			$query .= '&collection_id='.$collection_id;
		}

		if ($photonic_flickr_oauth_done) {
			$end_point = Photonic_Processor::get_normalized_http_url($query);
			if (strstr($query, $end_point) > -1) {
				$params = substr($query, strlen($end_point));
				if (strlen($params) > 1) {
					$params = substr($params, 1);
				}
				$params = Photonic_Processor::parse_parameters($params);
				$signed_args = $this->sign_call($end_point, 'GET', $params);
				$query = $end_point.'?'.Photonic_Processor::build_query($signed_args);
			}
		}

		$feed = Photonic::http($query);
		if (!is_wp_error($feed) && 200 == $feed['response']['code']) {
			$feed = $feed['body'];
			$feed = simplexml_load_string($feed);
			if (is_a($feed, 'SimpleXMLElement')) {
				$main_attributes = $feed->attributes();
				if ($main_attributes['stat'] == 'ok') {
					$children = $feed->children();
					if (count($children) != 0) {
						if (isset($feed->collections)) {
							$collections = $feed->collections;
							$collections = $collections->collection;
							$ret = array();
							$processed = array();
							foreach ($collections as $collection) {
								$collection_attrs = $collection->attributes();
								if (isset($collection_attrs['id'])) {
									if (!in_array($collection_attrs['id'], $processed)) {
										$iterative = $this->get_nested_collections($collection, $processed);
										$ret = array_merge($ret, $iterative);
									}
								}
							}
							return $ret;
						}
					}
				}
			}
		}
		return array();
	}

	/**
	 * Goes through a Flickr collection and recursively fetches all sets and other collections within it. This is returned as
	 * a flattened array.
	 *
	 * @param $collection
	 * @param $processed
	 * @return array
	 */
	function get_nested_collections($collection, &$processed) {
		$attributes = $collection->attributes();
		$id = isset($attributes['id']) ? (string)$attributes['id'] : '';
		if (in_array($id, $processed)) {
			return array();
		}
		$processed[] = $id;
		$id = substr($id, strpos($id, '-') + 1);
		$title = isset($attributes['title']) ? (string)$attributes['title'] : '';
		$description = isset($attributes['description']) ? (string)$attributes['description'] : '';
		$thumb = isset($attributes['iconsmall']) ? (string)$attributes['iconsmall'] : (isset($attributes['iconlarge']) ? (string)$attributes['iconlarge'] : '');

		$ret = array();

		$inner_sets = $collection->set;
		$sets = array();
		if (count($inner_sets) > 0) {
			foreach ($inner_sets as $inner_set) {
				$set_attributes = $inner_set->attributes();
				$sets[] = array(
					'id' => (string)$set_attributes['id'],
					'title' => (string)$set_attributes['title'],
					'description' => (string)$set_attributes['description'],
				);
			}
		}
		$ret[] = array(
			'id' => $id,
			'title' => $title,
			'description' => $description,
			'thumb' => $thumb,
			'sets' => $sets,
		);

		$inner_collections = $collection->collection;
		if (count($inner_collections) > 0) {
			foreach ($inner_collections as $inner_collection) {
				$inner_attribubtes = $inner_collection->attributes();
				$processed[] = $inner_attribubtes['id'];
			}
		}
		return $ret;
	}

	function process_query($query, $method, $columns, $user, $display) {
		$ret = '';
		$response = wp_remote_request($query);

		if (!is_wp_error($response)) {
			if ($response['response']['code'] == 200) {
				$body = $response['body'];
				$body = json_decode($body);
				switch ($method) {
					case 'flickr.photos.getInfo':
						if (isset($body->photo)) {
							$photo = $body->photo;
							$ret .= $this->process_photo($photo);
						}
						break;

					case 'flickr.photos.search':
						if (isset($body->photos) && isset($body->photos->photo)) {
							$photos = $body->photos->photo;
							$ret .= $this->process_photos($photos, '', $columns, $display);
						}
						break;

					case 'flickr.photosets.getInfo':
						if (isset($body->photoset)) {
							$photoset = $body->photoset;
							$ret .= $this->process_photoset_header($photoset, $display);
						}
						break;

					case 'flickr.photosets.getPhotos':
						if (isset($body->photoset)) {
							$photoset = $body->photoset;
							if (isset($photoset->photo) && isset($photoset->owner)) {
								$owner = $photoset->owner;
								$ret .= $this->process_photos($photoset->photo, $owner, $columns, $display);
							}
						}
						break;

					case 'flickr.photosets.getList':
						if (isset($body->photosets)) {
							$photosets = $body->photosets;
							$ret .= $this->process_photosets($photosets, $columns, $user);
						}
						break;

					case 'flickr.galleries.getInfo':
						if (isset($body->gallery)) {
							$gallery = $body->gallery;
							$ret .= $this->process_gallery_header($gallery, $display);
						}
						break;

					case 'flickr.galleries.getPhotos':
						if (isset($body->photos)) {
							$photos = $body->photos;
							if (isset($photos->photo)) {
								$ret .= $this->process_photos($photos->photo, '', $columns, $display);
							}
						}
						break;

					case 'flickr.galleries.getList':
						if (isset($body->galleries)) {
							$galleries = $body->galleries;
							$ret .= $this->process_galleries($galleries, $columns, $user);
						}
						break;

					case 'flickr.collections.getTree':
						if (isset($body->collections)) {
							$collections = $body->collections;
							$ret .= $this->process_collections($collections, $columns, $user);
						}
						break;
				}
			}
		}
		return $ret;
	}

	/**
	 * Prints a single photo with the title as an <h3> and the caption as the image caption.
	 *
	 * @param $photo
	 * @return string
	 */
	function process_photo($photo) {
		global $photonic_flickr_main_size, $photonic_external_links_in_new_tab;
		$ret = '';
		$main_size = $photonic_flickr_main_size == 'none' ? '' : '_'.$photonic_flickr_main_size;
		$orig = "http://farm".$photo->farm.".static.flickr.com/".$photo->server."/".$photo->id."_".$photo->secret.$main_size.".jpg";
		$ret .= "<img src='".$orig."'>";
		if (!empty($photonic_external_links_in_new_tab)) {
			$target = ' target="_blank" ';
		}
		else {
			$target = '';
		}

		if (isset($photo->urls) && isset($photo->urls->url) && count($photo->urls->url) > 0) {
			$ret = "<a href='".$photo->urls->url[0]->_content."' $target>".$ret."</a>";
		}
		if (isset($photo->description) && $photo->description->_content != '') {
			$ret = "<div class='wp-caption'>".$ret."<div class='wp-caption-text'>".$photo->description->_content."</div></div>";
		}
		if (isset($photo->title)) {
			$ret = "<h3 class='photonic-single-photo-header photonic-single-flickr-photo-header'>".$photo->title->_content."</h3>".$ret;
		}
		return $ret;
	}

	/**
	 * Prints thumbnails for all photos returned in a query. This is used for printing the results of a search, tag, photoset or gallery.
	 * The photos are printed in-page.
	 *
	 * @param $photos
	 * @param string $owner
	 * @param string $columns
	 * @param string $display
	 * @return string
	 */
	function process_photos($photos, $owner = '', $columns = 'auto', $display = 'in-page') {
		global $photonic_flickr_photo_title_display, $photonic_flickr_photo_pop_title_display;
		global $photonic_flickr_photos_per_row_constraint, $photonic_flickr_photos_constrain_by_padding, $photonic_flickr_photos_constrain_by_count;
		global $photonic_flickr_photos_pop_per_row_constraint, $photonic_flickr_photos_pop_constrain_by_padding, $photonic_flickr_photos_pop_constrain_by_count;

		if ($display == 'in-page') {
			$title_position = $photonic_flickr_photo_title_display;
			$row_constraints = array('constraint-type' => $photonic_flickr_photos_per_row_constraint, 'padding' => $photonic_flickr_photos_constrain_by_padding, 'count' => $photonic_flickr_photos_constrain_by_count);
		}
		else {
			$title_position = $photonic_flickr_photo_pop_title_display;
			$row_constraints = array('constraint-type' => $photonic_flickr_photos_pop_per_row_constraint, 'padding' => $photonic_flickr_photos_pop_constrain_by_padding, 'count' => $photonic_flickr_photos_pop_constrain_by_count);
		}
		$photo_objects = $this->build_level_1_objects($photos, $owner, $title_position);
		$ret = $this->generate_level_1_gallery($photo_objects, $title_position, $row_constraints, $columns, $display);
		return $ret;
	}

	function build_level_1_objects($photos, $owner = '', $title_position = 'thumbnail') {
		global $photonic_flickr_thumb_size, $photonic_flickr_main_size;
		$photo_objects = array();

		$main_size = $photonic_flickr_main_size == 'none' ? '' : '_'.$photonic_flickr_main_size;

		global $photonic_external_links_in_new_tab;
		if (!empty($photonic_external_links_in_new_tab)) {
			$target = " target='_blank' ";
		}
		else {
			$target = '';
		}

		foreach ($photos as $photo) {
			$photo_object = array();
			$photo_object['thumbnail'] = 'http://farm'.$photo->farm.'.static.flickr.com/'.$photo->server.'/'.$photo->id.'_'.$photo->secret.'_'.$photonic_flickr_thumb_size.'.jpg';
			$photo_object['main_image'] = 'http://farm'.$photo->farm.'.static.flickr.com/'.$photo->server.'/'.$photo->id.'_'.$photo->secret.$main_size.'.jpg';
			$photo_object['alt_title'] = esc_attr($photo->title);
			if (isset($photo->owner)) {
				$owner = $photo->owner;
			}
			$url = "http://www.flickr.com/photos/".$owner."/".$photo->id;
			$photo_object['main_page'] = $url;

			$title = esc_attr($photo->title);
			$photo_object['title'] = $title;

			$photo_objects[] = $photo_object;
		}

		return $photo_objects;
	}

	function build_level_2_objects($flickr_objects, $user, $type) {
		global $photonic_flickr_thumb_size;
		$objects = array();

		foreach ($flickr_objects as $flickr_object) {
			$object = array();
			$object['id_1'] = $flickr_object->id;
			$object['id_2'] = $flickr_object->id;
			$object['title'] = esc_attr($flickr_object->title->_content);
			if ($type == 'gallery') {
				$object['thumbnail'] = "http://farm".$flickr_object->primary_photo_farm.".static.flickr.com/".$flickr_object->primary_photo_server."/".$flickr_object->primary_photo_id."_".$flickr_object->primary_photo_secret."_".$photonic_flickr_thumb_size.".jpg";
				$object['main_page'] = $flickr_object->url;
				$object['counter'] = $flickr_object->count_photos;
				$object['classes'] = array("photonic-flickr-gallery-thumb-user-$user");
			}
			else if ($type == 'photoset') {
				$object['thumbnail'] = "http://farm".$flickr_object->farm.".static.flickr.com/".$flickr_object->server."/".$flickr_object->primary."_".$flickr_object->secret."_".$photonic_flickr_thumb_size.".jpg";
				$owner = isset($flickr_object->owner) ? $flickr_object->owner : $user;
				$object['main_page'] = "http://www.flickr.com/photos/$owner/sets/{$flickr_object->id}";
				$object['counter'] = $flickr_object->photos;
			}
			$objects[] = $object;
		}
		return $objects;
	}

	/**
	 * Prints the header for an in-page photoset.
	 *
	 * @param $photoset
	 * @param string $display
	 * @return string
	 */
	function process_photoset_header($photoset, $display = 'in-page') {
		global $photonic_flickr_thumb_size, $photonic_flickr_hide_set_thumbnail, $photonic_flickr_hide_set_title, $photonic_flickr_hide_set_photo_count, $photonic_flickr_hide_set_pop_thumbnail, $photonic_flickr_hide_set_pop_title, $photonic_flickr_hide_set_pop_photo_count;
		$owner = $photoset->owner;
		$header = array(
			'title' => $photoset->title->_content,
			'thumb_url' => "http://farm".$photoset->farm.".static.flickr.com/".$photoset->server."/".$photoset->primary."_".$photoset->secret."_".$photonic_flickr_thumb_size.".jpg",
			'link_url' => 'http://www.flickr.com/photos/'.$owner.'/sets/'.$photoset->id,
		);

		if ($display != 'popup') {
			$hidden = array('thumbnail' => !empty($photonic_flickr_hide_set_thumbnail), 'title' => !empty($photonic_flickr_hide_set_title), 'counter' => !empty($photonic_flickr_hide_set_photo_count));
		}
		else {
			$hidden = array('thumbnail' => !empty($photonic_flickr_hide_set_pop_thumbnail), 'title' => !empty($photonic_flickr_hide_set_pop_title), 'counter' => !empty($photonic_flickr_hide_set_pop_photo_count));
		}
		$counters = array('photos' => $photoset->photos);

		$ret = $this->process_object_header($header, 'set', $hidden, $counters, true, $display);

		return $ret;
	}

	/**
	 * Prints thumbnails for each photoset returned in a query.
	 *
	 * @param $photosets
	 * @param $columns
	 * @param $user
	 * @return string
	 */
	function process_photosets($photosets, $columns, $user) {
		global $photonic_flickr_collection_set_per_row_constraint, $photonic_flickr_collection_set_constrain_by_count, $photonic_flickr_collection_set_constrain_by_padding,
			$photonic_flickr_collection_set_title_display, $photonic_flickr_hide_collection_set_photos_count_display;
		$objects = $this->build_level_2_objects($photosets->photoset, $user, 'photoset');
		$row_constraints = array('constraint-type' => $photonic_flickr_collection_set_per_row_constraint, 'padding' => $photonic_flickr_collection_set_constrain_by_padding, 'count' => $photonic_flickr_collection_set_constrain_by_count);
		$ret = $this->generate_level_2_gallery($objects, $row_constraints, $columns, 'photosets', 'set', $photonic_flickr_collection_set_title_display, $photonic_flickr_hide_collection_set_photos_count_display);
		return $ret;
	}

	/**
	 * Shows the header for a gallery invoked in-page.
	 *
	 * @param $gallery
	 * @param string $display
	 * @return string
	 */
	function process_gallery_header($gallery, $display = 'in-page') {
		global $photonic_flickr_thumb_size, $photonic_flickr_hide_gallery_thumbnail, $photonic_flickr_hide_gallery_title, $photonic_flickr_hide_gallery_photo_count, $photonic_flickr_hide_gallery_pop_thumbnail, $photonic_flickr_hide_gallery_pop_title, $photonic_flickr_hide_gallery_pop_photo_count;
		$header = array(
			'title' => $gallery->title->_content,
			'thumb_url' => "http://farm".$gallery->primary_photo_farm.".static.flickr.com/".$gallery->primary_photo_server."/".$gallery->primary_photo_id."_".$gallery->primary_photo_secret."_".$photonic_flickr_thumb_size.".jpg",
			'link_url' => $gallery->url,
		);

		if ($display != 'popup') {
			$hidden = array('thumbnail' => !empty($photonic_flickr_hide_gallery_thumbnail), 'title' => !empty($photonic_flickr_hide_gallery_title), 'counter' => !empty($photonic_flickr_hide_gallery_photo_count));
		}
		else {
			$hidden = array('thumbnail' => !empty($photonic_flickr_hide_gallery_pop_thumbnail), 'title' => !empty($photonic_flickr_hide_gallery_pop_title), 'counter' => !empty($photonic_flickr_hide_gallery_pop_photo_count));
		}
		$counters = array('photos' => $gallery->count_photos);

		$ret = $this->process_object_header($header, 'gallery', $hidden, $counters, true, $display);
		return $ret;
	}

	/**
	 * Prints out the thumbnails for all galleries belonging to a user.
	 *
	 * @param $galleries
	 * @param $columns
	 * @param $user
	 * @return string
	 */
	function process_galleries($galleries, $columns, $user) {
		global $photonic_flickr_galleries_per_row_constraint, $photonic_flickr_galleries_constrain_by_padding,
			$photonic_flickr_galleries_constrain_by_count, $photonic_flickr_gallery_title_display, $photonic_flickr_hide_gallery_photos_count_display;

		$objects = $this->build_level_2_objects($galleries->gallery, $user, 'gallery');
		$row_constraints = array('constraint-type' => $photonic_flickr_galleries_per_row_constraint, 'padding' => $photonic_flickr_galleries_constrain_by_padding, 'count' => $photonic_flickr_galleries_constrain_by_count);
		$ret = $this->generate_level_2_gallery($objects, $row_constraints, $columns, 'galleries', 'gallery',
			$photonic_flickr_gallery_title_display, $photonic_flickr_hide_gallery_photos_count_display);
		return $ret;
	}

	/**
	 * Prints a collection header, followed by thumbnails of all sets in that collection.
	 *
	 * @param $collections
	 * @param $columns
	 * @param $user
	 * @return string
	 */
	function process_collections($collections, $columns, $user) {
		global $photonic_flickr_hide_empty_collection_details, $photonic_flickr_collection_set_per_row_constraint, $photonic_flickr_collection_set_constrain_by_padding,
			   $photonic_flickr_collection_set_constrain_by_count, $photonic_flickr_hide_collection_thumbnail, $photonic_flickr_hide_collection_title, $photonic_flickr_hide_collection_set_count, $photonic_flickr_collection_set_title_display, $photonic_flickr_hide_collection_set_photos_count_display;
		$ret = '';

		$row_constraints = array('constraint-type' => $photonic_flickr_collection_set_per_row_constraint, 'padding' => $photonic_flickr_collection_set_constrain_by_padding, 'count' => $photonic_flickr_collection_set_constrain_by_count);
		foreach ($collections->collection as $collection) {
			$dont_show = false;
			if (empty($collection->set) && !empty($photonic_flickr_hide_empty_collection_details)) {
				$dont_show = true;
			}
			$id = $collection->id;
			if (!$dont_show) {
				$url_id = substr($id, stripos($id, '-') + 1);
				$header = array('title' => $collection->title, 'thumb_url' => $collection->iconsmall, 'link_url' => "http://www.flickr.com/photos/".$user."/collections/".$url_id);
				$hidden = array('thumbnail' => !empty($photonic_flickr_hide_collection_thumbnail), 'title' => !empty($photonic_flickr_hide_collection_title), 'counter' => !empty($photonic_flickr_hide_collection_set_count));
				$counters = array();
				if (isset($collection->set)) {
					$photosets = $collection->set;
					$counters['sets'] = count($photosets);
				}

				$ret .= $this->process_object_header($header, 'collection', $hidden, $counters, true);
			}

			if (isset($collection->set) && !empty($collection->set)) {
				$flickr_objects = array();
				$photosets = $collection->set;
				foreach ($photosets as $set) {
					$set_url = 'http://api.flickr.com/services/rest/?format=json&nojsoncallback=1&&api_key='.$this->api_key.'&method=flickr.photosets.getInfo&photoset_id='.$set->id;
					$set_response = wp_remote_request($set_url);
					if (!is_wp_error($set_response) && isset($set_response['response']) && isset($set_response['response']['code']) && $set_response['response']['code'] == 200) {
						$set_response = json_decode($set_response['body']);
						if ($set_response->stat != 'fail' && isset($set_response->photoset)) {
							$photoset = $set_response->photoset;
							$flickr_objects[] = $photoset;
						}
					}
				}
				$objects = $this->build_level_2_objects($flickr_objects, $user, 'photoset');
				$ret .= $this->generate_level_2_gallery($objects, $row_constraints, $columns, 'photosets', 'set',
					$photonic_flickr_collection_set_title_display, $photonic_flickr_hide_collection_set_photos_count_display);
			}
		}
		return $ret;
	}

	/**
	 * Access Token URL
	 *
	 * @return string
	 */
	public function access_token_URL() {
		return 'http://www.flickr.com/services/oauth/access_token';
	}

	/**
	 * Authenticate URL
	 *
	 * @return string
	 */
	public function authenticate_URL() {
		return 'http://www.flickr.com/services/oauth/authorize';
	}

	/**
	 * Authorize URL
	 *
	 * @return string
	 */
	public function authorize_URL() {
		return 'http://www.flickr.com/services/oauth/authorize';
	}

	/**
	 * Request Token URL
	 *
	 * @return string
	 */
	public function request_token_URL() {
		return 'http://www.flickr.com/services/oauth/request_token';
	}

	public function end_point() {
		return 'http://api.flickr.com/services/rest/';
	}

	function parse_token($response) {
		$body = $response['body'];
		$token = Photonic_Processor::parse_parameters($body);
		return $token;
	}

	public function check_access_token_method() {
		return 'flickr.test.login';
	}

	/**
	 * Method to validate that the stored token is indeed authenticated.
	 *
	 * @param $request_token
	 * @return array|WP_Error
	 */
	function check_access_token($request_token) {
		$parameters = array('method' => $this->check_access_token_method(), 'format' => 'json', 'nojsoncallback' => 1);
		$signed_parameters = $this->sign_call($this->end_point(), 'GET', $parameters);

		$end_point = $this->end_point();
		$end_point .= '?'.Photonic_Processor::build_query($signed_parameters);
		$parameters = null;

		$response = Photonic::http($end_point, 'GET', $parameters);
		return $response;
	}

	/**
	 * If a Flickr Photoset / Gallery thumbnail is being displayed on a page, clicking on the thumbnail should launch a popup displaying all
	 * set / gallery photos. This function handles the click event and the subsequent invocation of the popup.
	 *
	 * @return void
	 */
	function display_photos() {
		if (isset($_POST['method']) && isset($_POST['object_id'])) {
			$object = '';
			$object_id = $_POST['object_id'];
			$gallery_id_computed = false;
			if ($_POST['method'] == 'flickr.photosets.getPhotos') {
				$object = 'photoset_id';
			}
			else if ($_POST['method'] == 'flickr.galleries.getPhotos') {
				$object = 'gallery_id';
				$gallery_id_computed = true;
			}
			echo $this->get_gallery_images(array($object => $object_id, 'panel_id' => $_POST['panel_id'], 'display' => 'popup', 'gallery_id_computed' => $gallery_id_computed));
		}
		die();
	}
}
