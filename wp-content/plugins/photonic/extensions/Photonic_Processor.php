<?php
/**
 * Gallery processor class to be extended by individual processors. This class has an abstract method called <code>get_gallery_images</code>
 * that has to be defined by each inheriting processor.
 *
 * This is also where the OAuth support is implemented. The URLs are defined using abstract functions, while a handful of utility functions are defined.
 * Most utility functions have been adapted from the OAuth PHP package distributed here: http://code.google.com/p/oauth-php/.
 *
 * @package Photonic
 * @subpackage Extensions
 */

abstract class Photonic_Processor {
	public $library, $thumb_size, $full_size, $api_key, $api_secret, $provider, $nonce, $oauth_timestamp, $signature_parameters, $link_lightbox_title,
		$oauth_version, $oauth_done, $show_more_link, $is_server_down, $is_more_required, $login_shown, $login_box_counter, $gallery_index;

	function __construct() {
		global $photonic_slideshow_library, $photonic_custom_lightbox;
		if ($photonic_slideshow_library != 'custom') {
			$this->library = $photonic_slideshow_library;
		}
		else {
			$this->library = $photonic_custom_lightbox;
		}
		$this->nonce = Photonic_Processor::nonce();
		$this->oauth_timestamp = time();
		$this->oauth_version = '1.0';
		$this->show_more_link = false;
		$this->is_server_down = false;
		$this->is_more_required = true;
		$this->login_shown = false;
		$this->login_box_counter = 0;
		$this->gallery_index = 0;
	}

	/**
	 * Main function that fetches the images associated with the shortcode.
	 *
	 * @abstract
	 * @param array $attr
	 */
	abstract protected function get_gallery_images($attr = array());

	public function oauth_signature_method() {
		return 'HMAC-SHA1';
	}

	/**
	 * Takes a token response from a request token call, then puts it in an appropriate array.
	 *
	 * @param $response
	 */
	public abstract function parse_token($response);

	/**
	 * Generates a nonce for use in signing calls.
	 *
	 * @static
	 * @return string
	 */
	public static function nonce() {
		$mt = microtime();
		$rand = mt_rand();
		return md5($mt . $rand);
	}

	/**
	 * Encodes the URL as per RFC3986 specs. This replaces some strings in addition to the ones done by a rawurlencode.
	 * This has been adapted from the OAuth for PHP project.
	 *
	 * @static
	 * @param $input
	 * @return array|mixed|string
	 */
	public static function urlencode_rfc3986($input) {
		if (is_array($input)) {
			return array_map(array('Photonic_Processor', 'urlencode_rfc3986'), $input);
		}
		else if (is_scalar($input)) {
			return str_replace(
				'+',
				' ',
				str_replace('%7E', '~', rawurlencode($input))
			);
		}
		else {
			return '';
		}
	}

	/**
	 * Takes an array of parameters, then parses it and generates a query string. Prior to generating the query string the parameters are sorted in their natural order.
	 * Without sorting the signatures between this application and the provider might differ.
	 *
	 * @static
	 * @param $params
	 * @return string
	 */
	public static function build_query($params) {
		if (!$params) {
			return '';
		}
		$keys = array_map(array('Photonic_Processor', 'urlencode_rfc3986'), array_keys($params));
		$values = array_map(array('Photonic_Processor', 'urlencode_rfc3986'), array_values($params));
		$params = array_combine($keys, $values);

		// Sort by keys (natsort)
		uksort($params, 'strnatcmp');
		$pairs = array();
		foreach ($params as $key => $value) {
			if (is_array($value)) {
				natsort($value);
				foreach ($value as $v2) {
					$pairs[] = ($v2 == '') ? "$key=0" : "$key=$v2";
				}
			}
			else {
				$pairs[] = ($value == '') ? "$key=0" : "$key=$value";
			}
		}

		$string = implode('&', $pairs);
		return $string;
	}

	/**
	 * Takes a string of parameters in an HTML encoded string, then returns an array of name-value pairs, with the parameter
	 * name and the associated value.
	 *
	 * @static
	 * @param $input
	 * @return array
	 */
	public static function parse_parameters($input) {
		if (!isset($input) || !$input) return array();

		$pairs = explode('&', $input);

		$parsed_parameters = array();
		foreach ($pairs as $pair) {
			$split = explode('=', $pair, 2);
			$parameter = urldecode($split[0]);
			$value = isset($split[1]) ? urldecode($split[1]) : '';

			if (isset($parsed_parameters[$parameter])) {
				// We have already recieved parameter(s) with this name, so add to the list
				// of parameters with this name
				if (is_scalar($parsed_parameters[$parameter])) {
					// This is the first duplicate, so transform scalar (string) into an array
					// so we can add the duplicates
					$parsed_parameters[$parameter] = array($parsed_parameters[$parameter]);
				}

				$parsed_parameters[$parameter][] = $value;
			}
			else {
				$parsed_parameters[$parameter] = $value;
			}
		}
		return $parsed_parameters;
	}

	/**
	 * Returns the URL of a request sans the query parameters.
	 * This has been adapted from the OAuth PHP package.
	 *
	 * @static
	 * @param $url
	 * @return string
	 */
	public static function get_normalized_http_url($url) {
		$parts = parse_url($url);

		$port = @$parts['port'];
		$scheme = $parts['scheme'];
		$host = $parts['host'];
		$path = @$parts['path'];

		$port or $port = ($scheme == 'https') ? '443' : '80';

		if (($scheme == 'https' && $port != '443')
				|| ($scheme == 'http' && $port != '80')) {
			$host = "$host:$port";
		}
		return "$scheme://$host$path";
	}

	/**
	 * If authentication is enabled for this processor and the user has not authenticated this site to access his profile,
	 * this shows a login box.
	 *
	 * @param $post_id
	 * @return string
	 */
	public function get_login_box($post_id = '') {
		$login_box_option = 'photonic_'.$this->provider.'_login_box';
		$login_button_option = 'photonic_'.$this->provider.'_login_button';
		global $$login_box_option, $$login_button_option;
		$login_box = $$login_box_option;
		$login_button = $$login_button_option;
		$this->login_box_counter++;
		$ret = '<div id="photonic-login-box-'.$this->provider.'-'.$this->login_box_counter.'" class="photonic-login-box photonic-login-box-'.$this->provider.'">';
		if ($this->is_server_down) {
			$ret .= __("The authentication server is down. Please try after some time.", 'photonic');
		}
		else {
			$ret .= wp_specialchars_decode($login_box, ENT_QUOTES);
			if (trim($login_button) == '') {
				$login_button = 'Login';
			}
			else {
				$login_button = wp_specialchars_decode($login_button, ENT_QUOTES);
			}
			$url = '#';
			$target = '';
			if ($this->provider == 'picasa' || $this->provider == 'instagram') {
				$url = $this->get_authorization_url();
				$target = 'target="_blank"';
			}

			if (!empty($post_id)) {
				$rel = "rel='auth-button-single-$post_id'";
			}
			else {
				$rel = '';
			}
			$ret .= "<p class='photonic-auth-button'><a href='$url' $target class='auth-button auth-button-{$this->provider}' $rel>".$login_button."</a></p>";
		}
		$ret .= '</div>';
		return $ret;
	}

	function more_link_button($link_to = '') {
		global $photonic_archive_link_more;
		if (empty($photonic_archive_link_more) && $this->is_more_required) {
			return "<div class='photonic-more-link-container'><a href='$link_to' class='photonic-more-button more-button-{$this->provider}'>See the rest</a></div>";
		}
		$this->is_more_required = true;
		return '';
	}

	/**
	 * Prints the header for a section. Typically used for albums / photosets / groups, where some generic information about the album / photoset / group is available.
	 *
	 * @param array $header The header object, which contains the title, thumbnail source URL and the link where clicking on the thumb will take you
	 * @param string $type Indicates what type of object is being displayed like gallery / photoset / album etc. This is added to the CSS class.
	 * @param array $hidden Contains the elements that should be hidden from the header display.
	 * @param array $counters Contains counts of the object that the header represents. In most cases this has just one value. Zenfolio objects have multiple values.
	 * @param string $link Should clicking on the thumbnail / title take you anywhere?
	 * @param string $display Indicates if this is on the page or in a popup
	 * @return string
	 */
	function process_object_header($header, $type = 'group', $hidden = array(), $counters = array(), $link, $display = 'in-page') {
		$ret = '';
		if (!empty($header['title'])) {
			global $photonic_external_links_in_new_tab;
			$title = esc_attr($header['title']);
			if (!empty($photonic_external_links_in_new_tab)) {
				$target = ' target="_blank" ';
			}
			else {
				$target = '';
			}

			$anchor = '';
			if (!empty($header['thumb_url'])) {
				$image = '<img src="'.$header['thumb_url'].'" alt="'.$title.'" />';

				if ($link) {
					$anchor = "<a href='".$header['link_url']."' class='photonic-header-thumb photonic-{$this->provider}-$type-solo-thumb' title='".$title."' $target>".$image."</a>";
				}
				else {
					$anchor = "<div class='photonic-header-thumb photonic-{$this->provider}-$type-solo-thumb'>$image</div>";
				}
			}

			if (empty($hidden['thumbnail']) || empty($hidden['title']) || empty($hidden['counter'])) {
				$popup_header_class = '';
				if ($display == 'popup') {
					$popup_header_class = 'photonic-panel-header';
				}
				$ret .= "<div class='photonic-{$this->provider}-$type $popup_header_class'>";

				if (empty($hidden['thumbnail'])) {
					$ret .= $anchor;
				}
				if (empty($hidden['title']) || empty($hidden['counter'])) {
					$ret .= "<div class='photonic-header-details photonic-$type-details'>";
					if (empty($hidden['title'])) {
						if ($link) {
							$ret .= "<div class='photonic-header-title photonic-$type-title'><a href='".$header['link_url']."' $target>".$title.'</a></div>';
						}
						else {
							$ret .= "<div class='photonic-header-title photonic-$type-title'>".$title.'</div>';
						}
					}
					if (empty($hidden['counter'])) {
						$counter_texts = array();
						if (!empty($counters['groups'])) {
							$counter_texts[] = sprintf(_n('%s group', '%s groups', $counters['groups'], 'photonic'), $counters['groups']);
						}
						if (!empty($counters['sets'])) {
							$counter_texts[] = sprintf(_n('%s set', '%s sets', $counters['sets'], 'photonic'), $counters['sets']);
						}
						if (!empty($counters['photos'])) {
							$counter_texts[] = sprintf(_n('%s photo', '%s photos', $counters['photos'], 'photonic'), $counters['photos']);
						}

						apply_filters('photonic_modify_counter_texts', $counter_texts, $counters);

						if (!empty($counter_texts)) {
							$ret .= "<span class='photonic-header-info photonic-$type-photos'>".implode(', ', $counter_texts).'</span>';
						}
					}
					$ret .= "</div><!-- .photonic-$type-details -->";
				}
				$ret .= "</div>";
			}
		}

		return $ret;
	}

	function get_popup_tooltip($option) {
		if ('tooltip' == $option) {
			return "<script type='text/javascript'>\$j('.photonic-{$this->provider}-panel a').each(function() { \$j(this).data('title', \$j(this).attr('title')); }); \$j('.photonic-{$this->provider}-panel a').each(function() { if (!(\$j(this).parent().hasClass('photonic-header-title'))) { var iTitle = \$j(this).find('img').attr('alt'); \$j(this).tooltip({ bodyHandler: function() { return iTitle; }, showURL: false });}})</script>";
		}
		return '';
	}

	function get_popup_lightbox() {
		$ret = '';
		if ($this->library == 'fancybox') {
			$ret .= "<script type='text/javascript'>\$j('a.launch-gallery-fancybox').each(function() { \$j(this).fancybox({ transitionIn:'elastic', transitionOut:'elastic',speedIn:600,speedOut:200,overlayShow:true,overlayOpacity:0.8,overlayColor:\"#000\",titleShow:Photonic_JS.fbox_show_title,titlePosition:Photonic_JS.fbox_title_position,titleFormat:photonicFormatFancyBoxTitle});});</script>";
		}
		else if ($this->library == 'colorbox') {
			$ret .= "<script type='text/javascript'>\$j('a.launch-gallery-colorbox').each(function() { \$j(this).colorbox({ opacity: 0.8, maxWidth: '95%', maxHeight: '95%', title: photonicLightBoxTitle(this), slideshow: Photonic_JS.slideshow_mode, slideshowSpeed: Photonic_JS.slideshow_interval });});</script>";
		}
		else if ($this->library == 'prettyphoto') {
			$ret .= "<script type='text/javascript'>\$j(\"a[rel^='photonic-prettyPhoto']\").prettyPhoto({ theme: Photonic_JS.pphoto_theme, autoplay_slideshow: Photonic_JS.slideshow_mode, slideshow: parseInt(Photonic_JS.slideshow_interval), show_title: false, social_tools: '', deeplinking: false });</script>";
		}
		else if ($this->library == 'fancybox2') {
			$ret .= "<script type='text/javascript'>\$j('a.launch-gallery-fancybox').fancybox({ autoPlay:Photonic_JS.slideshow_mode,playSpeed: parseInt(Photonic_JS.slideshow_interval, 10),beforeLoad: function(){ if (Photonic_JS.fbox_show_title) {this.title = \$j(this.element).data('title'); }},helpers: { title: { type: Photonic_JS.fbox_title_position } }});</script>";
		}
		return $ret;
	}

	function generate_level_1_gallery($photos, $title_position, $row_constraints = array(), $columns = 'auto',
		$display = 'in-page', $sizes = array(), $show_lightbox = true, $type = 'photo', $pagination = array()) {
		$col_class = '';
		if (Photonic::check_integer($columns)) {
			$col_class = 'photonic-gallery-'.$columns.'c';
		}

		if ($col_class == '' && $row_constraints['constraint-type'] == 'padding') {
			$col_class = 'photonic-pad-photos';
		}
		else if ($col_class == '') {
			$col_class = 'photonic-gallery-'.$row_constraints['count'].'c';
		}

		$link_attributes = $this->get_lightbox_attributes($display, $col_class, $show_lightbox);

		$ul_class = "class='title-display-$title_position'";
		if ($display == 'popup') {
			$ul_class = "class='slideshow-grid-panel lib-{$this->library} title-display-$title_position'";
		}

		$ret = "<ul $ul_class>";

		global $photonic_external_links_in_new_tab;
		if (!empty($photonic_external_links_in_new_tab)) {
			$target = " target='_blank' ";
		}
		else {
			$target = '';
		}

		$counter = 0;
		global $photonic_gallery_panel_items, $photonic_slideshow_library;
		foreach ($photos as $photo) {
			$counter++;
			$thumb = $photo['thumbnail'];
			$orig = $photo['main_image'];
			$url = $photo['main_page'];
			$title = esc_attr($photo['title']);
			$alt = esc_attr($photo['alt_title']);
			$orig = ($this->library == 'none' || !$show_lightbox) ? $url : $orig;

			$shown_title = '';
			if ($title_position == 'below') {
				$shown_title = '<span class="photonic-photo-title">'.wp_specialchars_decode($alt, ENT_QUOTES).'</span>';
			}
			if ($display == 'in-page') {
				$ret .= "\n\t".'<li class="photonic-'.$this->provider.'-image photonic-'.$this->provider.'-'.$type.' '.$col_class.'">';
			}
			else if ($counter % $photonic_gallery_panel_items == 1 && $display != 'in-page') {
				$ret .= "\n\t".'<li class="photonic-'.$this->provider.'-image photonic-'.$this->provider.'-'.$type.'">';
			}

			$style = array();
			if (!empty($sizes['thumb-width'])) $style[] = 'width:'.$sizes['thumb-width'].'px';
			if (!empty($sizes['thumb-height'])) $style[] = 'height:'.$sizes['thumb-height'].'px';
			if (!empty($style)) $style = 'style="'.implode(';', $style).'"'; else $style = '';
			$title_link_start = ($this->link_lightbox_title && $photonic_slideshow_library != 'thickbox') ? esc_attr("<a href='$url' $target>") : '';
			$title_link_end = ($this->link_lightbox_title && $photonic_slideshow_library != 'thickbox') ? esc_attr("</a>") : '';
			if ($display == 'in-page') {
				$ret .= '<a '.$link_attributes.' href="'.$orig.'" title="'.$title_link_start.$title.$title_link_end.'" '.$target.'><img alt="'.$alt.'" src="'.$thumb.'" '.$style.'/></a>'.$shown_title;
			}
			else {
				$ret .= '<a '.$link_attributes.' href="'.$orig.'" title="'.$title_link_start.$title.$title_link_end.'" '.$target.'><img alt="'.$alt.'" src="'.$thumb.'" '.$style.'/>'.$shown_title.'</a>';
			}
/*			if (!empty($object['passworded'])) {
				$prompt_title = esc_attr(__('Enter Password', 'photonic'));
				$prompt_submit = esc_attr(__('Access', 'photonic'));
				$form_url = admin_url('admin-ajax.php');
				$password_prompt = "
				<div class='photonic-password-prompter' id='photonic-zenfolio-prompter-{$object['id_1']}-$this->gallery_index' title='$prompt_title'>
					<form class='photonic-password-form photonic-zenfolio-form' action='$form_url'>
						<input type='password' name='photonic-zenfolio-password' />
						<input type='hidden' name='photonic-zenfolio-realm' value='{$photoset->AccessDescriptor->RealmId}' />
						<input type='hidden' name='action' value='photonic_verify_password' />
						<input type='submit' name='photonic-zenfolio-submit' value='$prompt_submit' />
					</form>
				</div>";
				$ret .= $password_prompt;
			}*/
			if ($display == 'in-page' || ($counter % $photonic_gallery_panel_items == 0 && $display != 'in-page')) {
				$ret .= "</li>";
			}
		}
		if ($ret != "<ul $ul_class>") {
			if (substr($ret, -5) != "</li>") {
				$ret .= "</li>";
			}
			$ret .= "\n</ul>\n";

			if (!empty($pagination) && !empty($pagination['paginate'])) {
				// Show "Load more" button
				if (!empty($pagination['current_page']) && !empty($pagination['per_page'])) {
					if (!empty($pagination['left'])) {
						//
					}
				}
			}
		}
		else {
			$ret = '';
		}

		if (is_archive()) {
			global $photonic_archive_thumbs;
			if (!empty($photonic_archive_thumbs) && $counter < $photonic_archive_thumbs) {
				$this->is_more_required = false;
			}
		}

		return $ret;
	}

	function generate_level_2_gallery($objects, $row_constraints, $columns, $type, $singular_type, $title_position, $level_1_count_display) {
		$ul_class = "class='title-display-$title_position'";

		$ret = "<ul $ul_class>";

		if ($columns != 'auto') {
			$col_class = 'photonic-gallery-'.$columns.'c';
		}
		else if ($row_constraints['constraint-type'] == 'padding') {
			$col_class = 'photonic-pad-'.$type;
		}
		else {
			$col_class = 'photonic-gallery-'.$row_constraints['count'].'c';
		}

		$counter = 0;
		foreach ($objects as $object) {
			$id = empty($object['id_1']) ? '' : $object['id_1'].'-';
			$id = $id.$this->gallery_index;
			$id = empty($object['id_2']) ? $id : ($id.'-'.$object['id_2']);
			$title = esc_attr($object['title']);
			$image = "<img src='".$object['thumbnail']."' alt='".$title."' />";
			$additional_classes = !empty($object['classes']) ? implode(' ', $object['classes']) : '';
			$anchor = "<a href='{$object['main_page']}' class='photonic-{$this->provider}-$singular_type-thumb $additional_classes' id='photonic-{$this->provider}-$singular_type-thumb-$id' title='".$title."'>".$image."</a>";
			$text = '';
			if ($title_position == 'below') {
				$text = "<span class='photonic-$singular_type-title'>".$title."</span>";
				if (!$level_1_count_display && !empty($object['counter'])) {
					$text .= '<span class="photonic-'.$singular_type.'-photo-count">'.sprintf(__('%s photos', 'photonic'), $object['counter']).'</span>';
				}
			}
			$ret .= "\n\t<li class='photonic-{$this->provider}-image photonic-{$this->provider}-$singular_type-thumb $col_class' id='photonic-{$this->provider}-$singular_type-$id'>{$anchor}{$text}</li>";
			$counter++;
		}

		if ($ret != "<ul $ul_class>") {
			$ret .= "\n</ul>\n";
		}
		else {
			$ret = '';
		}
		if (is_archive()) {
			global $photonic_archive_thumbs;
			if (!empty($photonic_archive_thumbs) && $counter < $photonic_archive_thumbs) {
				$this->is_more_required = false;
			}
		}
		return $ret;
	}

	/**
	 * Depending on the lightbox library, this function provides the CSS class and the rel tag for the thumbnail. This method borrows heavily from
	 * Justin Tadlock's Cleaner Gallery Plugin.
	 *
	 * @param $display
	 * @param $col_class
	 * @param $show_lightbox
	 * @return string
	 */
	function get_lightbox_attributes($display, $col_class, $show_lightbox) {
		$class = '';
		$rel = '';
		if ($this->library != 'none' && $show_lightbox) {
			$class = 'launch-gallery-'.$this->library." ".$this->library;
			$rel = 'lightbox-photonic-'.$this->provider.'-stream-'.$this->gallery_index;
			switch ($this->library) {
				case 'lightbox':
				case 'slimbox':
				case 'jquery_lightbox_plugin':
				case 'jquery_lightbox_balupton':
					$class = 'launch-gallery-lightbox lightbox';
					$rel = "lightbox[{$rel}]";
					break;

				case 'fancybox2':
					$class = 'launch-gallery-fancybox fancybox';
					break;

				case 'pirobox':
					$class = 'launch-gallery-pirobox pirobox_gall';
					break;

				case 'prettyphoto':
					$rel = 'photonic-prettyPhoto['.$rel.']';
					break;

				default:
					$class = 'launch-gallery-'.$this->library." ".$this->library;
					$rel = 'lightbox-photonic-'.$this->provider.'-stream-'.$this->gallery_index;
					break;
			}

			if ($display == 'popup') {
				$class .= ' '.$col_class;
			}
			$class = " class='$class' ";
			$rel = " rel='$rel' ";
		}
		return $class.$rel;
	}
}
