<?php
/**
 * Processor for native WP galleries. This extends the Photonic_Processor class and defines methods local to WP.
 *
 * @package Photonic
 * @subpackage Extensions
 */

class Photonic_Native_Processor extends Photonic_Processor {
	/**
	 * Gets all images associated with the gallery. This method is lifted almost verbatim from the gallery short-code function provided by WP.
	 * We will take the gallery images and do some fun stuff with styling them in other methods. We cannot use the WP function because
	 * this code is nested within the gallery_shortcode function and we want to tweak that (there is no hook that executes after
	 * the gallery has been retrieved.
	 *
	 * @param array $attr
	 * @return array|bool
	 */
	function get_gallery_images($attr = array()) {
		global $post;
		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if (isset($attr['orderby'])) {
			$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
			if (!$attr['orderby'])
				unset($attr['orderby']);
		}

		extract(shortcode_atts(array(
			'order' => 'ASC',
			'orderby' => 'menu_order ID',
			'id' => $post->ID,
			'itemtag' => 'dl',
			'icontag' => 'dt',
			'captiontag' => 'dd',
			'columns' => 3,
			'size' => 'thumbnail',
			'include' => '',
			'exclude' => ''
		), $attr));

		$id = intval($id);
		if ('RAND' == $order)
			$orderby = 'none';

		if (!empty($include)) {
			$include = preg_replace('/[^0-9,]+/', '', $include);
			$_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

			$attachments = array();
			foreach ($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		}
		elseif (!empty($exclude)) {
			$exclude = preg_replace('/[^0-9,]+/', '', $exclude);
			$attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
		}
		else {
			$attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
		}
		return $attachments;
	}

	/**
	 * Access Token URL
	 *
	 * @return string
	 */
	public function access_token_URL() {
		// TODO: Implement access_token_URL() method.
	}

	/**
	 * Authenticate URL
	 *
	 * @return string
	 */
	public function authenticate_URL() {
		// TODO: Implement authenticate_URL() method.
	}

	/**
	 * Authorize URL
	 *
	 * @return string
	 */
	public function authorize_URL() {
		// TODO: Implement authorize_URL() method.
	}

	/**
	 * Request Token URL
	 *
	 * @return string
	 */
	public function request_token_URL() {
		// TODO: Implement request_token_method() method.
	}

	public function end_point() {
		// TODO: Implement end_point() method.
	}

	function parse_token($response) {
		// TODO: Implement parse_token() method.
	}

	public function check_access_token_method() {
		// TODO: Implement check_access_token_method() method.
	}
}
?>