<?php
/**
 * Plugin Name: Photonic Gallery for Flickr, Picasa, SmugMug, 500px, Zenfolio and Instagram
 * Plugin URI: http://aquoid.com/news/plugins/photonic/
 * Description: Extends the native gallery shortcode to support Flickr, Picasa, SmugMug, 500px, Zenfolio and Instagram. JS libraries like Fancybox, Colorbox and PrettyPhoto are supported. The plugin also helps convert a regular WP gallery into a slideshow.
 * Version: 1.44
 * Author: Sayontan Sinha
 * Author URI: http://mynethome.net/blog
 * License: GNU General Public License (GPL), v3 (or newer)
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: photonic
 *
 * Copyright (c) 2009 - 2013 Sayontan Sinha. All rights reserved.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

class Photonic {
	var $version, $registered_extensions, $defaults, $plugin_name, $options_page_name, $settings_page, $helper_page;
	function Photonic() {
		global $photonic_options, $photonic_setup_options, $photonic_is_ie6;
		if (!defined('PHOTONIC_VERSION')) {
			define('PHOTONIC_VERSION', '1.43');
		}

		if (!defined('PHOTONIC_PATH')) {
			define('PHOTONIC_PATH', plugin_dir_path(__FILE__));
		}

		require_once(plugin_dir_path(__FILE__)."/options/photonic-options.php");

		$this->plugin_name = plugin_basename(__FILE__);
		$this->set_version();

		add_action('admin_menu', array(&$this, 'add_admin_menu'));
		add_action('admin_enqueue_scripts', array(&$this, 'add_admin_scripts'));
		add_action('admin_init', array(&$this, 'admin_init'));

		$photonic_options = get_option('photonic_options');
		if (isset($photonic_options) && is_array($photonic_options)) {
			foreach ($photonic_setup_options as $default_option) {
				if (isset($default_option['id'])) {
					$mod_key = 'photonic_'.$default_option['id'];
					global $$mod_key;
					if (isset($photonic_options[$default_option['id']])) {
						$$mod_key = $photonic_options[$default_option['id']];
					}
					else {
						$$mod_key = $default_option['std'];
					}
				}
			}
		}
		if (isset($photonic_options) && is_array($photonic_options)) {
			foreach ($photonic_options as $key => $value) {
				$mod_key = 'photonic_'.$key;
				global $$mod_key;
				$$mod_key = $value;
			}
		}

		// Gallery
		if (!empty($photonic_alternative_shortcode)) {
			add_shortcode($photonic_alternative_shortcode, array(&$this, 'modify_gallery'));
		}
		else {
			add_filter('post_gallery', array(&$this, 'modify_gallery'), 20, 2);
		}
		add_action('wp_enqueue_scripts', array(&$this, 'add_scripts'), 20);
		add_action('wp_head', array(&$this, 'print_scripts'), 20);
		add_action('wp_loaded', array(&$this, 'check_authentication'), 20);

		add_action('wp_ajax_photonic_verify_password', array(&$this, 'verify_password'));
		add_action('wp_ajax_nopriv_photonic_verify_password', array(&$this, 'verify_password'));

		add_action('wp_ajax_photonic_flickr_display_photos', array(&$this, 'flickr_display_photos'));
		add_action('wp_ajax_nopriv_photonic_flickr_display_photos', array(&$this, 'flickr_display_photos'));

		add_action('wp_ajax_photonic_picasa_display_album', array(&$this, 'picasa_display_album'));
		add_action('wp_ajax_nopriv_photonic_picasa_display_album', array(&$this, 'picasa_display_album'));

		add_action('wp_ajax_photonic_smug_display_album', array(&$this, 'smug_display_album'));
		add_action('wp_ajax_nopriv_photonic_smug_display_album', array(&$this, 'smug_display_album'));

		add_action('wp_ajax_photonic_zenfolio_display_set', array(&$this, 'zenfolio_display_set'));
		add_action('wp_ajax_nopriv_photonic_zenfolio_display_set', array(&$this, 'zenfolio_display_set'));

		add_filter('media_upload_tabs', array(&$this, 'media_upload_tabs'));
		add_action('media_upload_photonic', array(&$this, 'media_upload_photonic'));

		add_action('wp_ajax_photonic_invoke_helper', array(&$this, 'invoke_helper'));

		$this->registered_extensions = array();
		$this->add_extensions();

		add_action('template_redirect', array(&$this, 'get_oauth2_access_token'));

		add_action('wp_ajax_photonic_authenticate', array(&$this, 'authenticate'));
		add_action('wp_ajax_nopriv_photonic_authenticate', array(&$this, 'authenticate'));

		//WP provides a global $is_IE, but we specifically need to find IE6x (or, heaven forbid, IE5x). Note that older versions of Opera used to identify themselves as IE6, so we exclude Opera.
		$photonic_is_ie6 = preg_match('/\bmsie [56]/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/\bopera/i', $_SERVER['HTTP_USER_AGENT']);

		$locale = get_locale();
		load_textdomain('photonic', locate_template(array("languages/{$locale}.mo", "{$locale}.mo")));
	}

	/**
	 * Adds a menu item to the "Settings" section of the admin page.
	 *
	 * @return void
	 */
	function add_admin_menu() {
		global $photonic_options_manager;
//		$this->options_page_name = add_options_page('Photonic', 'Photonic', 'edit_theme_options', 'photonic-options-manager', array(&$photonic_options_manager, 'render_options_page'));
		$this->options_page_name = add_menu_page('Photonic', 'Photonic', 'edit_theme_options', 'photonic-options-manager', array(&$photonic_options_manager, 'render_options_page'), plugins_url('include/images/crosshairs-16.png', __FILE__));
		$this->settings_page = add_submenu_page('photonic-options-manager', 'Settings', 'Settings', 'edit_theme_options', 'photonic-options-manager', array(&$photonic_options_manager, 'render_options_page'));
		$this->helper_page = add_submenu_page('photonic-options-manager', 'Helpers', 'Helpers', 'edit_theme_options', 'photonic-helpers', array(&$photonic_options_manager, 'render_helpers'));
//		add_action('load-'.$this->helper_page, array(&$this, 'add_meta_boxes'));

		$this->set_version();
	}

	/**
	 * Adds all scripts and their dependencies to the <head> of the Photonic administration page. This takes care to not add scripts on other admin pages.
	 *
	 * @param $hook
	 * @return void
	 */
	function add_admin_scripts($hook) {
		if ($this->options_page_name == $hook) {
			global $wp_version;
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('photonic-jscolor', plugins_url('include/scripts/jscolor/jscolor.js', __FILE__));

			if ($wp_version < 3.3) {
				wp_enqueue_script('photonic-jquery-ui-custom', plugins_url('include/scripts/jquery-ui/jquery-ui-1.8.12.custom.js', __FILE__), array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-position'), $this->version);
			}
			else {
				wp_enqueue_script('jquery-ui-slider');
			}

			wp_enqueue_script('photonic-admin-js', plugins_url('include/scripts/admin.js', __FILE__), array('jquery'), $this->version);

			wp_enqueue_style('photonic-admin-jq', plugins_url('include/scripts/jquery-ui/css/jquery-ui-1.7.3.custom.css', __FILE__), array(), $this->version);
			wp_enqueue_style('photonic-admin-css', plugins_url('include/css/admin.css', __FILE__), array('photonic-admin-jq'), $this->version);

			global $photonic_options;
			$js_array = array(
				'category' => isset($photonic_options) && isset($photonic_options['last-set-section']) ? $photonic_options['last-set-section'] : 'generic-settings',
			);
			wp_localize_script('photonic-admin-js', 'Photonic_Admin_JS', $js_array);
		}
		else if ($this->helper_page == $hook) {
			wp_enqueue_script('photonic-admin-js', plugins_url('include/scripts/admin-helpers.js', __FILE__), array('jquery'), $this->version);
			wp_enqueue_style('photonic-admin-css', plugins_url('include/css/admin.css', __FILE__), array(), $this->version);
		}
		else if ('media-upload-popup' == $hook) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('photonic-upload', plugins_url('include/css/admin-form.css', __FILE__), array(), $this->version);
		}
	}

	/**
	 * Adds all scripts and their dependencies to the <head> section of the page.
	 *
	 * @return void
	 */
	function add_scripts() {
		global $photonic_slideshow_library, $photonic_slideshow_mode, $photonic_slideshow_interval, $photonic_pphoto_theme, $photonic_carousel_mode;
		global $photonic_fbox_title_position, $photonic_gallery_panel_width, $photonic_gallery_panel_items;
		global $photonic_flickr_collection_set_title_display, $photonic_flickr_gallery_title_display;
		global $photonic_flickr_photo_title_display;
		global $photonic_picasa_photo_title_display, $photonic_picasa_photo_pop_title_display, $photonic_wp_thumbnail_title_display;
		global $photonic_500px_photo_title_display;
		global $photonic_smug_photo_title_display, $photonic_smug_photo_pop_title_display, $photonic_smug_albums_album_title_display;
		global $photonic_instagram_photo_title_display, $photonic_instagram_user_title_display;
		global $photonic_zenfolio_photo_title_display, $photonic_zenfolio_set_title_display;
		global $photonic_custom_lightbox_js, $photonic_custom_lightbox_css, $photonic_custom_lightbox;

		//wp_enqueue_script('photonic', plugins_url('include/scripts/photonic.js', __FILE__), array('jquery', 'jquery-ui-dialog', 'jquery-form'), $this->version);
		wp_enqueue_script('photonic', plugins_url('include/scripts/photonic.js', __FILE__), array('jquery'), $this->version);
		wp_deregister_script('jquery-cycle');
		wp_enqueue_script('jquery-cycle', plugins_url('include/scripts/jquery.cycle.all.min.js', __FILE__), array('jquery'), $this->version);

		if ($photonic_slideshow_library == 'fancybox') {
			wp_enqueue_script('photonic-slideshow', plugins_url('include/scripts/jquery.fancybox-1.3.4.pack.js', __FILE__), array('jquery'), $this->version);
		}
		else if ($photonic_slideshow_library == 'colorbox') {
			wp_enqueue_script('photonic-slideshow', plugins_url('include/scripts/jquery.colorbox-min.js', __FILE__), array('jquery'), $this->version);
		}
		else if ($photonic_slideshow_library == 'prettyphoto') {
			wp_enqueue_script('photonic-slideshow', plugins_url('include/scripts/jquery.prettyPhoto-min.js', __FILE__), array('jquery'), $this->version);
		}
		else if ($photonic_slideshow_library == 'thickbox') {
			wp_enqueue_script('thickbox');
		}
		else if ($photonic_slideshow_library == 'custom') {
			$counter = 1;
			$dependencies = array('jquery');
			if ($photonic_custom_lightbox == 'pirobox') {
				$dependencies[] = 'jquery-ui-draggable';
				$dependencies[] = 'jquery-ui-resizable';
			}
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $photonic_custom_lightbox_js) as $line){
				wp_enqueue_script('photonic-slideshow-'.$counter, trim($line), $dependencies, $this->version);
				$counter++;
			}
		}

		if (isset($photonic_carousel_mode) && $photonic_carousel_mode == 'on') {
			wp_enqueue_script('photonic-carousel', plugins_url('include/scripts/jquery.jcarousel.min.js', __FILE__), array('jquery'), $this->version);
		}
		
		$js_array = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'fbox_show_title' => $photonic_fbox_title_position == 'none' ? false : true,
			'fbox_title_position' => $photonic_fbox_title_position == 'none' ? 'outside' : $photonic_fbox_title_position,

			'flickr_collection_set_title_display' => $photonic_flickr_collection_set_title_display,
			'flickr_gallery_title_display' => $photonic_flickr_gallery_title_display,
			'flickr_photo_title_display' => $photonic_flickr_photo_title_display,

			'picasa_photo_title_display' => $photonic_picasa_photo_title_display,
			'picasa_photo_pop_title_display' => $photonic_picasa_photo_pop_title_display,

			'wp_thumbnail_title_display' => $photonic_wp_thumbnail_title_display,

			'Dpx_photo_title_display' => $photonic_500px_photo_title_display,

			'smug_photo_title_display' => $photonic_smug_photo_title_display,
			'smug_photo_pop_title_display' => $photonic_smug_photo_pop_title_display,
			'smug_albums_album_title_display' => $photonic_smug_albums_album_title_display,

			'instagram_photo_title_display' => $photonic_instagram_photo_title_display,
			'instagram_user_title_display' => $photonic_instagram_user_title_display,

			'zenfolio_photo_title_display' => $photonic_zenfolio_photo_title_display,
			'zenfolio_set_title_display' => $photonic_zenfolio_set_title_display,

			'slideshow_library' => $photonic_slideshow_library == 'custom' ? $photonic_custom_lightbox : $photonic_slideshow_library,
			'slideshow_mode' => (isset($photonic_slideshow_mode) && $photonic_slideshow_mode == 'on') ? true : false,
			'slideshow_interval' => (isset($photonic_slideshow_interval) && Photonic::check_integer($photonic_slideshow_interval)) ? $photonic_slideshow_interval : 5000,
			'pphoto_theme' => isset($photonic_pphoto_theme) ? $photonic_pphoto_theme : 'pp_default',
			'gallery_panel_width' => $photonic_gallery_panel_width,
			'gallery_panel_items' => $photonic_gallery_panel_items,
		);
		wp_localize_script('photonic', 'Photonic_JS', $js_array);

		$template_directory = get_template_directory();
		$stylesheet_directory = get_stylesheet_directory();

		if ($photonic_slideshow_library == 'fancybox') {
			if (@file_exists($stylesheet_directory.'/scripts/fancybox/jquery.fancybox-1.3.4.css')) {
				wp_enqueue_style("photonic-slideshow", get_stylesheet_directory_uri().'/scripts/fancybox/jquery.fancybox-1.3.4.css', array(), $this->version);
			}
			else if (@file_exists($template_directory.'/scripts/fancybox/jquery.fancybox-1.3.4.css')) {
				wp_enqueue_style("photonic-slideshow", get_template_directory_uri().'/scripts/fancybox/jquery.fancybox-1.3.4.css', array(), $this->version);
			}
			else {
				wp_enqueue_style("photonic-slideshow", plugins_url('include/scripts/fancybox/jquery.fancybox-1.3.4.css', __FILE__), array(), $this->version);
			}
		}
		else if ($photonic_slideshow_library == 'colorbox') {
			global $photonic_cbox_theme;
			if ($photonic_cbox_theme == 'theme' && @file_exists($stylesheet_directory.'/scripts/colorbox/colorbox.css')) {
				wp_enqueue_style("photonic-slideshow", get_stylesheet_directory_uri().'/scripts/colorbox/colorbox.css', array(), $this->version);
			}
			else if ($photonic_cbox_theme == 'theme' && @file_exists($template_directory.'/scripts/colorbox/colorbox.css')) {
				wp_enqueue_style("photonic-slideshow", get_template_directory_uri().'/scripts/colorbox/colorbox.css', array(), $this->version);
			}
			else if ($photonic_cbox_theme == 'theme') {
				wp_enqueue_style("photonic-slideshow", plugins_url('include/scripts/colorbox/style-1/colorbox.css', __FILE__), array(), $this->version);
			}
			else {
				wp_enqueue_style("photonic-slideshow", plugins_url('include/scripts/colorbox/style-'.$photonic_cbox_theme.'/colorbox.css', __FILE__), array(), $this->version);
			}
		}
		else if ($photonic_slideshow_library == 'prettyphoto') {
			if (@file_exists($stylesheet_directory.'/scripts/prettyphoto/css/prettyPhoto.css')) {
				wp_enqueue_style("photonic-slideshow", get_stylesheet_directory_uri().'/scripts/prettyphoto/css/prettyPhoto.css', array(), $this->version);
			}
			else if (@file_exists($template_directory.'/scripts/prettyphoto/css/prettyPhoto.css')) {
				wp_enqueue_style("photonic-slideshow", get_template_directory_uri().'/scripts/prettyphoto/css/prettyPhoto.css', array(), $this->version);
			}
			else {
				wp_enqueue_style("photonic-slideshow", plugins_url('include/scripts/prettyphoto/css/prettyPhoto.css', __FILE__), array(), $this->version);
			}
		}
		else if ($photonic_slideshow_library == 'thickbox') {
			wp_enqueue_style('thickbox');
		}
		else if ($photonic_slideshow_library == 'custom') {
			$counter = 1;
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $photonic_custom_lightbox_css) as $line){
				wp_enqueue_style('photonic-slideshow-'.$counter, trim($line), array(), $this->version);
				$counter++;
			}
		}

		if (isset($photonic_carousel_mode) && $photonic_carousel_mode == 'on') {
			wp_enqueue_style('photonic-carousel', plugins_url('include/scripts/jcarousel/tango/skin.css', __FILE__), array(), $this->version);
		}

		wp_enqueue_style('photonic', plugins_url('include/css/photonic.css', __FILE__), array(), $this->version);
	}

	/**
	 * Prints the dynamically generated CSS based on option selections.
	 *
	 * @return void
	 */
	function print_scripts() {
		global $photonic_flickr_collection_set_constrain_by_padding, $photonic_flickr_photos_constrain_by_padding, $photonic_flickr_photos_pop_constrain_by_padding, $photonic_flickr_galleries_constrain_by_padding;
		global $photonic_picasa_photos_pop_constrain_by_padding, $photonic_picasa_photos_constrain_by_padding, $photonic_wp_slide_align, $photonic_500px_photos_constrain_by_padding;
		global $photonic_smug_photos_constrain_by_padding, $photonic_smug_photos_pop_constrain_by_padding, $photonic_smug_albums_album_constrain_by_padding, $photonic_instagram_photos_constrain_by_padding, $photonic_instagram_users_constrain_by_padding;
		global $photonic_zenfolio_photos_constrain_by_padding, $photonic_zenfolio_sets_constrain_by_padding;

		$css = '<style type="text/css">'."\n";
		$css .= ".photonic-pad-photosets { margin: {$photonic_flickr_collection_set_constrain_by_padding}px; }\n";
		$css .= ".photonic-pad-galleries { margin: {$photonic_flickr_galleries_constrain_by_padding}px; }\n";
		$css .= ".photonic-flickr-stream .photonic-pad-photos { margin: 0 {$photonic_flickr_photos_constrain_by_padding}px; }\n";

		$css .= ".photonic-picasa-stream .photonic-pad-photos { margin: 0 {$photonic_picasa_photos_constrain_by_padding}px; }\n";
		$css .= ".photonic-picasa-stream img { ".$this->get_border_css('photonic_picasa_photo_thumb_border').$this->get_padding_css('photonic_picasa_photo_thumb_padding')." }\n";
		$css .= ".photonic-panel .photonic-picasa-image img { ".$this->get_border_css('photonic_picasa_photo_pop_thumb_border').$this->get_padding_css('photonic_picasa_photo_pop_thumb_padding')." }\n";

		$css .= ".photonic-500px-stream .photonic-pad-photos { margin: 0 {$photonic_500px_photos_constrain_by_padding}px; }\n";
		$css .= ".photonic-500px-stream img { ".$this->get_border_css('photonic_500px_photo_thumb_border').$this->get_padding_css('photonic_500px_photo_thumb_padding')." }\n";

		$css .= ".photonic-zenfolio-stream .photonic-pad-photos { margin: 0 {$photonic_zenfolio_photos_constrain_by_padding}px; }\n";
		$css .= ".photonic-zenfolio-stream .photonic-pad-photosets { margin: 0 {$photonic_zenfolio_sets_constrain_by_padding}px; }\n";
		$css .= ".photonic-zenfolio-photo img { ".$this->get_border_css('photonic_zenfolio_photo_thumb_border').$this->get_padding_css('photonic_zenfolio_photo_thumb_padding')." }\n";
		$css .= ".photonic-zenfolio-set-thumb img { ".$this->get_border_css('photonic_zenfolio_sets_set_thumb_border').$this->get_padding_css('photonic_zenfolio_sets_set_thumb_padding')." }\n";

		$css .= ".photonic-instagram-stream .photonic-pad-photos { margin: 0 {$photonic_instagram_photos_constrain_by_padding}px; }\n";
		$css .= ".photonic-instagram-photo img { ".$this->get_border_css('photonic_instagram_photo_thumb_border').$this->get_padding_css('photonic_instagram_photo_thumb_padding')." }\n";

		$css .= ".photonic-instagram-stream .photonic-pad-users { margin: 0 {$photonic_instagram_users_constrain_by_padding}px; }\n";
		$css .= ".photonic-instagram-user img { ".$this->get_border_css('photonic_instagram_user_thumb_border').$this->get_padding_css('photonic_instagram_user_thumb_padding')." }\n";

		$css .= ".photonic-pad-albums { margin: {$photonic_smug_albums_album_constrain_by_padding}px; }\n";
		$css .= ".photonic-smug-stream .photonic-pad-photos { margin: 0 {$photonic_smug_photos_constrain_by_padding}px; }\n";
		$css .= ".photonic-smug-stream img { ".$this->get_border_css('photonic_smug_photo_thumb_border').$this->get_padding_css('photonic_smug_photo_thumb_padding')." }\n";
		$css .= ".photonic-panel .photonic-smug-image img { ".$this->get_border_css('photonic_smug_photo_pop_thumb_border').$this->get_padding_css('photonic_smug_photo_pop_thumb_padding')." }\n";

		$css .= ".photonic-panel { ".$this->get_bg_css('photonic_flickr_gallery_panel_background').$this->get_border_css('photonic_flickr_set_popup_thumb_border')." }\n";

		$css .= ".photonic-panel .photonic-flickr-image img { ".$this->get_border_css('photonic_flickr_pop_photo_thumb_border').$this->get_padding_css('photonic_flickr_pop_photo_thumb_padding')." }\n";
		$css .= ".photonic-flickr-panel .photonic-pad-photos { margin: 0 {$photonic_flickr_photos_pop_constrain_by_padding}px; }\n";
		$css .= ".photonic-picasa-panel .photonic-pad-photos { margin: 0 {$photonic_picasa_photos_pop_constrain_by_padding}px; }\n";
		$css .= ".photonic-smug-panel .photonic-pad-photos { margin: 0 {$photonic_smug_photos_pop_constrain_by_padding}px; }\n";
		$css .= ".photonic-flickr-coll-thumb img { ".$this->get_border_css('photonic_flickr_coll_thumb_border').$this->get_padding_css('photonic_flickr_coll_thumb_padding')." }\n";
		$css .= ".photonic-flickr-set .photonic-flickr-set-solo-thumb img { ".$this->get_border_css('photonic_flickr_set_alone_thumb_border').$this->get_padding_css('photonic_flickr_set_alone_thumb_padding')." }\n";
		$css .= ".photonic-flickr-set-thumb img { ".$this->get_border_css('photonic_flickr_sets_set_thumb_border').$this->get_padding_css('photonic_flickr_sets_set_thumb_padding')." }\n";
		$css .= ".photonic-flickr-set-pop-thumb img { ".$this->get_border_css('photonic_flickr_set_pop_thumb_border').$this->get_padding_css('photonic_flickr_set_pop_thumb_padding')." }\n";
		$css .= ".photonic-flickr-gallery-pop-thumb img { ".$this->get_border_css('photonic_flickr_gallery_pop_thumb_border').$this->get_padding_css('photonic_flickr_gallery_pop_thumb_padding')." }\n";
		$css .= ".photonic-smug-album-thumb img { ".$this->get_border_css('photonic_smug_albums_album_thumb_border').$this->get_padding_css('photonic_smug_albums_album_thumb_padding')." }\n";

		if (checked($photonic_wp_slide_align, 'on', false)) {
			$css .= ".photonic-post-gallery-img img {margin: auto; display: block}\n";
		}
		$css .= "\n</style>\n";
		echo $css;
	}

	function set_version() {
		// Cannot use get_plugin_data in a non-admin view :-(
/*		$plugin_data = get_plugin_data(__FILE__);
		$this->version = $plugin_data['Version'];*/
		$this->version = PHOTONIC_VERSION;
	}

	function admin_init() {
		if (isset($_REQUEST['page']) && ('photonic-options-manager' == $_REQUEST['page'] || 'photonic-helpers' == $_REQUEST['page'])) {
			global $photonic_options_manager;
			require_once(plugin_dir_path(__FILE__)."/photonic-options-manager.php");
			$photonic_options_manager = new Photonic_Options_Manager(__FILE__);
			$photonic_options_manager->init();
		}
	}

	function add_extensions() {
		require_once(plugin_dir_path(__FILE__)."/extensions/Photonic_Processor.php");
		require_once(plugin_dir_path(__FILE__)."/extensions/Photonic_OAuth1_Processor.php");
		require_once(plugin_dir_path(__FILE__)."/extensions/Photonic_OAuth2_Processor.php");
		$this->register_extension('Photonic_Flickr_Processor', plugin_dir_path(__FILE__)."/extensions/Photonic_Flickr_Processor.php");
		$this->register_extension('Photonic_Picasa_Processor', plugin_dir_path(__FILE__)."/extensions/Photonic_Picasa_Processor.php");
		$this->register_extension('Photonic_Native_Processor', plugin_dir_path(__FILE__)."/extensions/Photonic_Native_Processor.php");
		$this->register_extension('Photonic_500px_Processor', plugin_dir_path(__FILE__)."/extensions/Photonic_500px_Processor.php");
		$this->register_extension('Photonic_SmugMug_Processor', plugin_dir_path(__FILE__)."/extensions/Photonic_SmugMug_Processor.php");
		$this->register_extension('Photonic_Instagram_Processor', plugin_dir_path(__FILE__)."/extensions/Photonic_Instagram_Processor.php");
		$this->register_extension('Photonic_Zenfolio_Processor', plugin_dir_path(__FILE__)."/extensions/Photonic_Zenfolio_Processor.php");
		do_action('photonic_register_extensions');
	}

	public function register_extension($extension, $path) {
		if (@!file_exists($path)) {
			return;
		}
		require_once($path);
		if (!class_exists($extension) || is_subclass_of($extension, 'Photonic_Processor')) {
			return;
		}
		$this->registered_extensions[] = $extension;
	}

	/**
	 * Overrides the native gallery short code, and does a lot more.
	 *
	 * @param $content
	 * @param array $attr
	 * @return string
	 */
	function modify_gallery($content, $attr = array()) {
		global $post, $photonic_flickr_gallery, $photonic_picasa_gallery, $photonic_native_gallery, $photonic_500px_gallery, $photonic_smugmug_gallery, $photonic_default_gallery_type, $photonic_nested_shortcodes, $photonic_instagram_gallery, $photonic_zenfolio_gallery, $photonic_alternative_shortcode;

		// If an alternative shortcode is used, then $content has the shortcode attributes
		if (!empty($photonic_alternative_shortcode)) {
			$attr = $content;
		}
		if ($attr == null) {
			$attr = array();
		}

		$attr = array_merge(array(
			// Specially for Photonic
			'type' => $photonic_default_gallery_type,  //default, flickr, picasa
			'style' => 'default',   //default, strip-below, strip-above, strip-right, strip-left, no-strip, launch
			'id'         => $post->ID,
		), $attr);

		if ($photonic_nested_shortcodes) {
			$attr = array_map('do_shortcode', $attr);
		}

		extract($attr);

		$type = strtolower($type);
		switch ($type) {
			case 'flickr':
				if (!isset($photonic_flickr_gallery)) {
					$photonic_flickr_gallery = new Photonic_Flickr_Processor();
				}
				$images = $photonic_flickr_gallery->get_gallery_images($attr);
				break;

			case 'picasa':
				if (!isset($photonic_picasa_gallery)) {
					$photonic_picasa_gallery = new Photonic_Picasa_Processor();
				}
				$images = $photonic_picasa_gallery->get_gallery_images($attr);
				break;

			case '500px':
				if (!isset($photonic_500px_gallery)) {
					$photonic_500px_gallery = new Photonic_500px_Processor();
				}
				$images = $photonic_500px_gallery->get_gallery_images($attr);
				break;

			case 'smugmug':
				if (!isset($photonic_smugmug_gallery)) {
					$photonic_smugmug_gallery = new Photonic_SmugMug_Processor();
				}
				$images = $photonic_smugmug_gallery->get_gallery_images($attr);
				break;

			case 'instagram':
				if (!isset($photonic_instagram_gallery)) {
					$photonic_instagram_gallery = new Photonic_Instagram_Processor();
				}
				$images = $photonic_instagram_gallery->get_gallery_images($attr);
				break;

			case 'zenfolio':
				if (!isset($photonic_zenfolio_gallery)) {
					$photonic_zenfolio_gallery = new Photonic_Zenfolio_Processor();
				}
				$images = $photonic_zenfolio_gallery->get_gallery_images($attr);
				break;

			case 'default':
			default:
				if (!isset($photonic_native_gallery)) {
					$photonic_native_gallery = new Photonic_Native_Processor();
				}
				$images = $photonic_native_gallery->get_gallery_images($attr);
				break;
		}

		if (isset($images) && is_array($images)) {
			if (isset($style)) {
				$gallery_html = $this->build_gallery($images, $style, $attr);
				return $gallery_html;
			}
		}
		else if (isset($images)) {
			return $images;
		}

		return $content;
	}

	/**
	 * Builds the markup for a gallery when you choose to use a specific gallery style. The following styles are allowed:
	 * 	1. strip-below: Shows thumbnails for the gallery below a larger image
	 * 	2. strip-above: Shows thumbnails for the gallery above a larger image
	 *  3. no-strip: Doesn't show thumbnails. Useful if you are making it behave like an automatic slideshow.
	 * 	4. launch: Shows a thumbnail for the gallery, which you can click to launch a slideshow.
	 * 	5. default: Shows the native WP styling
	 *
	 * @param $images
	 * @param string $style
	 * @param $attr
	 * @return string
	 */
	function build_gallery($images, $style = 'strip-below', $attr) {
		global $photonic_gallery_number, $photonic_slideshow_library, $photonic_wp_thumbnail_title_display;
		if (!is_array($images)) {
			return $images;
		}

		if (!isset($photonic_gallery_number)) {
			$photonic_gallery_number = 0;
		}

		$attr = array_merge(array(
			'columns' => 3,
			'thumb_width' => 75,
			'thumb_height' => 75,
			'thumb_size' => 'thumbnail',
			'slide_size' => 'large',
			'slideshow_height' => 500,
			'fx' => 'fade', 	// JQuery Cycle effects: fade, scrollUp, scrollDown, scrollLeft, scrollRight, scrollHorz, scrollVert, slideX, slideY, turnUp, turnDown, turnLeft,
								// turnRight, zoom, fadeZoom, blindX, blindY, blindZ, growX, growY, curtainX, curtainY, cover, uncover, wipe
			'timeout' => 4000, 	// Time between slides in ms
			'speed' => 1000,	// Time for each transition
			'pause' => true,	// Pause on hover
		), $attr);

		extract($attr);

		if (!isset($thumb_width) || (isset($thumb_width) && !Photonic::check_integer($thumb_width))) {
			$thumb_width = 75;
		}
		if (!isset($thumb_height) || (isset($thumb_height) && !Photonic::check_integer($thumb_height))) {
			$thumb_height = 75;
		}
		if (!isset($columns) || (isset($columns) && !Photonic::check_integer($columns))) {
			$columns = 3;
		}

		$sources = array();
		$thumbs = array();
		switch ($style) {
			case 'strip-below':
			case 'strip-above':
			case 'no-strip':
				$photonic_gallery_number++;
				$size = $slide_size;
				$ret = "<div class='photonic-post-gallery $style fix'><ul id='gallery-fancy-$photonic_gallery_number' class='photonic-post-gallery-content fix' style='height: {$slideshow_height}px;'>";
				foreach ( $images as $id => $attachment ) {
	//				$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);
					$sources[$id] = wp_get_attachment_image_src($id, $size, false);
					$thumbs[$id] = wp_get_attachment_image_src($id, $thumb_size);

					$ret .= "<li class='photonic-post-gallery-img'>";
					if (isset($attachment->post_title)) {
						$title = wptexturize($attachment->post_title);
					}
					else {
						$title = '';
					}
					$ret .= "<img src='".$sources[$id][0]."' alt='$title' id='gallery-fancy-$photonic_gallery_number-$id' />";
					$ret .= "</li>";
				}
				$ret .= '</ul></div>';
				ob_start();
	?>
		<script type="text/javascript">
			/* <![CDATA[ */
			$j = jQuery.noConflict();
			$j(document).ready(function() {
				// Builds a JQuery Cycle gallery based on input parameters
				$j('ul.photonic-post-gallery-content').each(function() {
					var parent = $j(this).parent();
				<?php
					$script = '';
					if ($style == 'strip-below') {
				?>
					 $j("<ul id='" + this.id + "-nav' class='photonic-post-gallery-nav fix'><?php echo $script; ?></ul>").insertAfter($j(this));
				<?php
					}
					else if ($style == 'strip-above') {
				?>
					$j("<ul id='" + this.id + "-nav' class='photonic-post-gallery-nav fix'><?php echo $script; ?></ul>").insertBefore($j(this));
				<?php
					}
				?>

					var thisId = this.id;
					$j(this).cycle({
						pause: <?php if (empty($pause)) echo 0; else echo 1; ?>,
						fit: 1,
						width: '100%',
						<?php if (isset($fx)) { ?>
						fx: '<?php echo $fx; ?>',
						<?php } ?>
						<?php if (isset($speed)) { ?>
						speed: '<?php echo $speed; ?>',
						<?php } ?>
						<?php if (isset($timeout)) { ?>
						timeout: '<?php echo $timeout; ?>',
						<?php } ?>
						<?php if ($style == 'strip-above' || $style == 'strip-below') { ?>
						pager: '#' + thisId + '-nav',

						pagerAnchorBuilder: function(idx, slide) {
							var thumbIds = <?php echo json_encode($thumbs); ?>;
							var image = slide.children[0];
							var lastDash = image.id.lastIndexOf('-');
							var imageId = image.id.substr(lastDash + 1);
							var thumbDetails = thumbIds[imageId];
							return '<li><a href="#" title="' + image.alt + '"><img src="' + thumbDetails[0] + '" width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>" alt="' + image.alt + '" /></a></li>';
						}
						<?php } ?>
					});
				});
			});
			/* ]]> */
		</script>
	<?php
				$ret .= ob_get_contents();
				ob_end_clean();
				break;

			case 'launch':
				$photonic_gallery_number++;
				$slideshow_library_class = ($photonic_slideshow_library == 'none') ? "" : ($photonic_slideshow_library == 'thickbox' ? " class='thickbox' " : " class='launch-gallery-$photonic_slideshow_library' ");
				$ret = "<div class='photonic-post-gallery $style fix'><ul id='gallery-fancy-$photonic_gallery_number-nav' class='photonic-post-gallery-nav fix'>";
				foreach ( $images as $id => $attachment ) {
					$src = wp_get_attachment_image_src($id, 'full');
					$thumb = wp_get_attachment_image_src($id, $thumb_size);
					$ret .= "<li class='photonic-gallery-{$columns}c'>";
					if (isset($attachment->post_title)) {
						$title = wptexturize($attachment->post_title);
					}
					else {
						$title = '';
					}
					if ($photonic_slideshow_library == 'prettyphoto') {
						$rel = "photonic-prettyPhoto[gallery-fancy-$photonic_gallery_number-group]";
					}
					else {
						$rel = "gallery-fancy-$photonic_gallery_number-group";
					}
					$ret .= "<a href=\"".$src[0]."\" rel='$rel' title='$title' $slideshow_library_class><img src='".$thumb[0]."' alt='$title' width='$thumb_width' height='$thumb_height' /></a>";
					$ret .= "</li>";
				}
				$ret .= '</ul></div>';
				break;

			case 'default':
			default:
				return "";
		}
		return $ret;
	}

	function flickr_display_photos() {
		global $photonic_flickr_gallery;
		if (!isset($photonic_flickr_gallery)) {
			$photonic_flickr_gallery = new Photonic_Flickr_Processor();
		}
		$photonic_flickr_gallery->display_photos();
	}
	/**
	 * If a Picasa album thumbnail is being displayed on a page, clicking on the thumbnail should launch a popup displaying all
	 * album photos. This function handles the click event and the subsequent invocation of the popup.
	 *
	 * @return void
	 */
	function picasa_display_album() {
		global $photonic_picasa_gallery;
		if (!isset($photonic_picasa_gallery)) {
			$photonic_picasa_gallery = new Photonic_Picasa_Processor();
		}
		$photonic_picasa_gallery->display_album();
	}

	/**
	 * If a SmugMug album thumbnail is being displayed on a page, clicking on the thumbnail should launch a popup displaying all
	 * album photos. This function handles the click event and the subsequent invocation of the popup.
	 *
	 * @return void
	 */
	function smug_display_album() {
		global $photonic_smugmug_gallery;
		if (!isset($photonic_smugmug_gallery)) {
			$photonic_smugmug_gallery = new Photonic_SmugMug_Processor();
		}
		$photonic_smugmug_gallery->display_album();
	}

	/**
	 * If a Zenfolio set thumbnail is being displayed on a page, clicking on the thumbnail should launch a popup displaying all
	 * set photos. This function handles the click event and the subsequent invocation of the popup.
	 *
	 * @return void
	 */
	function zenfolio_display_set() {
		global $photonic_zenfolio_gallery;
		if (!isset($photonic_zenfolio_gallery)) {
			$photonic_zenfolio_gallery = new Photonic_Zenfolio_Processor();
		}
		$photonic_zenfolio_gallery->display_set();
	}

	/**
	 * Checks if a text being passed to it is an integer or not.
	 *
	 * @param $val
	 * @return bool
	 */
	static function check_integer($val) {
		if (substr($val, 0, 1) == '-') {
			$val = substr($val, 1);
		}
		return (preg_match('/^\d*$/', $val) == 1);
	}

	/**
	 * Converts a string to a boolean variable, if possible.
	 *
	 * @param $value
	 * @return bool
	 */
	static function string_to_bool($value) {
		if ($value == true || $value == 'true' || $value == 'TRUE' || $value == '1') {
			return true;
		}
		else if ($value == false || $value == 'false' || $value == 'FALSE' || $value == '0') {
			return false;
		}
		else {
			return $value;
		}
	}

	/**
	 * Constructs the CSS for a "background" option
	 *
	 * @param $option
	 * @return string
	 */
	function get_bg_css($option) {
		global $$option;
		$option_val = $$option;
		if (!is_array($option_val)) {
			$val_array = array();
			$vals = explode(';', $option_val);
			foreach ($vals as $val) {
				if (trim($val) == '') { continue; }
				$pair = explode('=', $val);
				$val_array[$pair[0]] = $pair[1];
			}
			$option_val = $val_array;
		}
		$bg_string = "";
		$bg_rgba_string = "";
		if (!isset($option_val['colortype']) || $option_val['colortype'] == 'transparent') {
			$bg_string .= " transparent ";
		}
		else {
			if (isset($option_val['color'])) {
				if (substr($option_val['color'], 0, 1) == '#') {
					$color_string = substr($option_val['color'],1);
				}
				else {
					$color_string = $option_val['color'];
				}
				$rgb_str_array = array();
				if (strlen($color_string)==3) {
					$rgb_str_array[] = substr($color_string, 0, 1).substr($color_string, 0, 1);
					$rgb_str_array[] = substr($color_string, 1, 1).substr($color_string, 1, 1);
					$rgb_str_array[] = substr($color_string, 2, 1).substr($color_string, 2, 1);
				}
				else {
					$rgb_str_array[] = substr($color_string, 0, 2);
					$rgb_str_array[] = substr($color_string, 2, 2);
					$rgb_str_array[] = substr($color_string, 4, 2);
				}
				$rgb_array = array();
				$rgb_array[] = hexdec($rgb_str_array[0]);
				$rgb_array[] = hexdec($rgb_str_array[1]);
				$rgb_array[] = hexdec($rgb_str_array[2]);
				$rgb_string = implode(',',$rgb_array);
				$rgb_string = ' rgb('.$rgb_string.') ';

				if (isset($option_val['trans'])) {
					$bg_rgba_string = $bg_string;
					$transparency = (int)$option_val['trans'];
					if ($transparency != 0) {
						$trans_dec = $transparency/100;
						$rgba_string = implode(',', $rgb_array);
						$rgba_string = ' rgba('.$rgba_string.','.$trans_dec.') ';
						$bg_rgba_string .= $rgba_string;
					}
				}

				$bg_string .= $rgb_string;
			}
		}
		if (isset($option_val['image']) && trim($option_val['image']) != '') {
			$bg_string .= " url(".$option_val['image'].") ";
			$bg_string .= $option_val['position']." ".$option_val['repeat'];

			if (trim($bg_rgba_string) != '') {
				$bg_rgba_string .= " url(".$option_val['image'].") ";
				$bg_rgba_string .= $option_val['position']." ".$option_val['repeat'];
			}
		}

		if (trim($bg_string) != '') {
			$bg_string = "background: ".$bg_string.";\n";
			if (trim($bg_rgba_string) != '') {
				$bg_string .= "\tbackground: ".$bg_rgba_string.";\n";
			}
		}
		return $bg_string;
	}

	/**
	 * Generates the CSS for borders. Each border, top, right, bottom and left is generated as a separate line.
	 *
	 * @param $option
	 * @return string
	 */
	function get_border_css($option) {
		global $$option;
		$option_val = $$option;
		if (!is_array($option_val)) {
			$option_val = stripslashes($option_val);
			$edge_array = $this->build_edge_array($option_val);
			$option_val = $edge_array;
		}
		$border_string = '';
		foreach ($option_val as $edge => $selections) {
			$border_string .= "\tborder-$edge: ";
			if (!isset($selections['style'])) {
				$selections['style'] = 'none';
			}
			if ($selections['style'] == 'none') {
				$border_string .= "none";
			}
			else {
				if (isset($selections['border-width'])) {
					$border_string .= $selections['border-width'];
				}
				if (isset($selections['border-width-type'])) {
					$border_string .= $selections['border-width-type'];
				}
				else {
					$border_string .= "px";
				}
				$border_string .= " ".$selections['style']." ";
				if ($selections['colortype'] == 'transparent') {
					$border_string .= "transparent";
				}
				else {
					if (substr($selections['color'], 0, 1) == '#') {
						$border_string .= $selections['color'];
					}
					else {
						$border_string .= '#'.$selections['color'];
					}
				}
			}
			$border_string .= ";\n";
		}
		return $border_string;
	}

	/**
	 * Generates the CSS for use in padding. This generates individual padding strings for each side, top, right, bottom and left.
	 *
	 * @param $option
	 * @return string
	 */
	function get_padding_css($option) {
		global $$option;
		$option_val = $$option;
		if (!is_array($option_val)) {
			$option_val = stripslashes($option_val);
			$edge_array = $this->build_edge_array($option_val);
			$option_val = $edge_array;
		}
		$padding_string = '';
		foreach ($option_val as $edge => $selections) {
			$padding_string .= "\tpadding-$edge: ";
			if (isset($selections['padding'])) {
				$padding_string .= $selections['padding'];
			}
			else {
				$padding_string .= 0;
			}
			if (isset($selections['padding-type'])) {
				$padding_string .= $selections['padding-type'];
			}
			else {
				$padding_string .= "px";
			}
			$padding_string .= ";\n";
		}
		return $padding_string;
	}

	public function build_edge_array($option_val) {
		$edge_array = array();
		$edges = explode('||', $option_val);
		foreach ($edges as $edge_val) {
			if (trim($edge_val) != '') {
				$edge_options = explode('::', trim($edge_val));
				if (is_array($edge_options) && count($edge_options) > 1) {
					$val_array = array();
					$vals = explode(';', $edge_options[1]);
					foreach ($vals as $val) {
						$pair = explode('=', $val);
						if (is_array($pair) && count($pair) > 1) {
							$val_array[$pair[0]] = $pair[1];
						}
					}
					$edge_array[$edge_options[0]] = $val_array;
				}
			}
		}
		return $edge_array;
	}

	/**
	 * Adds a "Photonic" tab to the "Add Media" panel.
	 *
	 * @param $tabs
	 * @return array
	 */
	function media_upload_tabs($tabs) {
		$tabs['photonic'] = 'Photonic';
		return $tabs;
	}

	/**
	 * Invokes the form to display the photonic insertion screen in the "Add Media" panel. The call to wp_iframe ensures that the right CSS and JS are called.
	 *
	 * @return void
	 */
	function media_upload_photonic() {
		wp_iframe(array(&$this, 'media_upload_photonic_form'));
	}

	/**
	 * First prints the standard buttons for media upload, then shows the UI for Photonic.
	 *
	 * @return void
	 */
	function media_upload_photonic_form() {
		media_upload_header();
		require_once(plugin_dir_path(__FILE__)."/photonic-form.php");
	}

	static function get_image_sizes_selection($element_name, $show_full = false) {
		global $_wp_additional_image_sizes;
		$image_sizes = array();
		$standard_sizes = array('thumbnail', 'medium', 'large');
		if ($show_full) {
			$standard_sizes[] = 'full';
		}
		foreach ($standard_sizes as $standard_size) {
			if ($standard_size != 'full') {
				$image_sizes[$standard_size] = array('width' => get_option($standard_size.'_size_w'), 'height' => get_option($standard_size.'_size_h'));
			}
			else {
				$image_sizes[$standard_size] = array('width' => __('Original width', 'photonic'), 'height' => __('Original height', 'photonic'));
			}
		}
		if (is_array($_wp_additional_image_sizes)) {
			$image_sizes = array_merge($image_sizes, $_wp_additional_image_sizes);
		}
		$ret = "<select name='$element_name'>";
		foreach ($image_sizes as $size_name => $size_attrs) {
			$ret .= "<option value='$size_name'>$size_name ({$size_attrs['width']} &times; {$size_attrs['height']})</option>";
		}
		$ret .= '</select>';
		return $ret;
	}

	/**
	 * Make an HTTP request
	 *
	 * @static
	 * @param $url
	 * @param string $method GET | POST | DELETE
	 * @param null $post_fields
	 * @param string $user_agent
	 * @param int $timeout
	 * @param bool $ssl_verify_peer
	 * @return array|WP_Error
	 */
	static function http($url, $method = 'POST', $post_fields = NULL, $user_agent = null, $timeout = 30, $ssl_verify_peer = false) {
		$curl_args = array(
			'user-agent' => $user_agent,
			'timeout' => $timeout,
			'sslverify' => $ssl_verify_peer,
			'headers' => array('Expect:'),
			'method' => $method,
			'body' => $post_fields,
		);

		switch ($method) {
			case 'DELETE':
				if (!empty($post_fields)) {
					$url = "{$url}?{$post_fields}";
				}
				break;
		}

		$response = wp_remote_request($url, $curl_args);
		return $response;
	}

	/**
	 * Returns the page where the OAuth API is being invoked. The invocation happens through admin-ajax.php, but we don't want
	 * the validated user to land up there. Instead we want the users to reach the page where they clicked the "Login" button.
	 *
	 * @static
	 * @return string
	 */
	static function get_callback_url() {
		global $photonic_callback_url;
		if (isset($photonic_callback_url)) {
			return $photonic_callback_url;
		}

		$page_URL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$page_URL .= "s";
		}
		$page_URL .= "://";
		if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
			$page_URL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else {
			$page_URL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $page_URL;
	}

	/**
	 * Checks if a user has authenticated a particular provider's services. When this is invoked we don't know if the page has
	 * a Flickr / 500px / SmugMug gallery, so we just invoke it and set some global variables.
	 *
	 * @return mixed
	 */
	function check_authentication() {
		if (is_admin()) {
			return;
		}
		global $photonic_flickr_allow_oauth, $photonic_500px_allow_oauth, $photonic_smug_allow_oauth, $photonic_picasa_allow_oauth;
		if (!$photonic_flickr_allow_oauth && !$photonic_500px_allow_oauth && !$photonic_smug_allow_oauth && !$photonic_picasa_allow_oauth) {
			return;
		}

		global $photonic_flickr_oauth_done, $photonic_500px_oauth_done, $photonic_smug_oauth_done;
		$photonic_flickr_oauth_done = $photonic_500px_oauth_done = $photonic_smug_oauth_done = false;

		$cookie = Photonic::parse_cookie();
		if ($photonic_flickr_allow_oauth && isset($cookie['flickr']) && isset($cookie['flickr']['oauth_token']) && isset($cookie['flickr']['oauth_token_secret'])) {
			global $photonic_flickr_gallery;
			if (!isset($photonic_flickr_gallery)) {
				$photonic_flickr_gallery = new Photonic_Flickr_Processor();
			}
			$current_token = array(
				'oauth_token' => $cookie['flickr']['oauth_token'],
				'oauth_token_secret' => $cookie['flickr']['oauth_token_secret'],
			);
			if (isset($_REQUEST['oauth_verifier']) && isset($_REQUEST['oauth_token'])) {
				$current_token['oauth_token'] = $_REQUEST['oauth_token'];
				$current_token['oauth_verifier'] = $_REQUEST['oauth_verifier'];
				$new_token = $photonic_flickr_gallery->get_access_token($current_token);
				if (isset($new_token['oauth_token']) && isset($new_token['oauth_token_secret'])) {
					$photonic_flickr_oauth_done = true;
					$redirect = remove_query_arg(array('oauth_token', 'oauth_verifier'));
					wp_redirect($redirect);
					exit;
				}
			}
			else if (isset($cookie['flickr']['oauth_token_type']) && $cookie['flickr']['oauth_token_type'] == 'access') {
				$access_token_response = $photonic_flickr_gallery->check_access_token($current_token);
				if (is_wp_error($access_token_response)) {
					$photonic_flickr_gallery->is_server_down = true;
				}
				$photonic_flickr_oauth_done = $photonic_flickr_gallery->is_access_token_valid($access_token_response);
			}
		}

		if ($photonic_500px_allow_oauth && isset($cookie['500px']) && isset($cookie['500px']['oauth_token']) && isset($cookie['500px']['oauth_token_secret'])) {
			global $photonic_500px_gallery;
			if (!isset($photonic_500px_gallery)) {
				$photonic_500px_gallery = new Photonic_500px_Processor();
			}
			$current_token = array(
				'oauth_token' => $cookie['500px']['oauth_token'],
				'oauth_token_secret' => $cookie['500px']['oauth_token_secret'],
			);
			if (isset($_REQUEST['oauth_verifier']) && isset($_REQUEST['oauth_token'])) {
				$current_token['oauth_token'] = $_REQUEST['oauth_token'];
				$current_token['oauth_verifier'] = $_REQUEST['oauth_verifier'];
				$new_token = $photonic_500px_gallery->get_access_token($current_token);
				if (isset($new_token['oauth_token']) && isset($new_token['oauth_token_secret'])) {
					// Strip out the token and the verifier from the callback URL and send the user to the callback URL.
					$photonic_500px_oauth_done = true;
					$redirect = remove_query_arg(array('oauth_token', 'oauth_verifier'));
					wp_redirect($redirect);
					exit;
				}
			}
			else if (isset($cookie['500px']['oauth_token_type']) && $cookie['500px']['oauth_token_type'] == 'access') {
				$access_token_response = $photonic_500px_gallery->check_access_token($current_token);
				if (is_wp_error($access_token_response)) {
					$photonic_500px_gallery->is_server_down = true;
				}
				$photonic_500px_oauth_done = $photonic_500px_gallery->is_access_token_valid($access_token_response);
			}
		}

		if ($photonic_smug_allow_oauth && isset($cookie['smug']) && isset($cookie['smug']['oauth_token']) && isset($cookie['smug']['oauth_token_secret'])) {
			global $photonic_smugmug_gallery;
			if (!isset($photonic_smugmug_gallery)) {
				$photonic_smugmug_gallery = new Photonic_SmugMug_Processor();
			}
			$current_token = array(
				'oauth_token' => $cookie['smug']['oauth_token'],
				'oauth_token_secret' => $cookie['smug']['oauth_token_secret']
			);
			if (!$photonic_smug_oauth_done &&
					((isset($cookie['smug']['oauth_token_type']) && $cookie['smug']['oauth_token_type'] == 'request') || !isset($cookie['smug']['oauth_token_type']))) {
				$new_token = $photonic_smugmug_gallery->get_access_token($current_token);
				if (isset($new_token['oauth_token']) && isset($new_token['oauth_token_secret'])) {
					$access_token_response = $photonic_smugmug_gallery->check_access_token($new_token);
					if (is_wp_error($access_token_response)) {
						$photonic_smugmug_gallery->is_server_down = true;
					}
					$photonic_smug_oauth_done = $photonic_smugmug_gallery->is_access_token_valid($access_token_response);
				}
			}
			else if (isset($cookie['smug']['oauth_token_type']) && $cookie['smug']['oauth_token_type'] == 'access') {
				$access_token_response = $photonic_smugmug_gallery->check_access_token($current_token);
				if (is_wp_error($access_token_response)) {
					$photonic_smugmug_gallery->is_server_down = true;
				}
				$photonic_smug_oauth_done = $photonic_smugmug_gallery->is_access_token_valid($access_token_response);
			}
		}

		if (isset($photonic_picasa_allow_oauth)) {
			global $photonic_picasa_gallery;
			if (!isset($photonic_picasa_gallery)) {
				$photonic_picasa_gallery = new Photonic_Picasa_Processor();
			}
		}

		if (isset($photonic_instagram_allow_oauth)) {
			global $photonic_instagram_gallery;
			if (!isset($photonic_instagram_gallery)) {
				$photonic_instagram_gallery = new Photonic_Instagram_Processor();
			}
		}
	}

	/**
	 * Searches for specific cookies in the user's browser. It then builds an array with the available cookies. The keys of the array
	 * are the individual providers ('flickr', 'smug' etc) and the values are arrays of key-value mappings.
	 *
	 * @static
	 * @return array
	 */
	public static function parse_cookie() {
		$cookie = array(
			'flickr' => array(),
			'500px' => array(),
			'smug' => array(),
			'picasa' => array(),
			'instagram' => array(),
		);
		$auth_types = array(
			'flickr' => 'oauth1',
			'500px' => 'oauth1',
			'smug' => 'oauth1',
			'picasa' => 'oauth2',
			'instagram' => 'oauth2',
		);
		$cookie_keys = array('oauth_token', 'oauth_token_secret', 'oauth_token_type', 'access_token', 'access_token_type', 'oauth_token_created', 'oauth_token_expires', 'oauth_refresh_token');
		foreach ($cookie as $provider => $cookies) {
			$orig_secret = $auth_types[$provider] == 'oauth1' ? 'photonic_'.$provider.'_api_secret' : 'photonic_'.$provider.'_client_secret';
			global $$orig_secret; //echo "$provider - ".$$orig_secret."<br/>";
			if (isset($$orig_secret)) {
				$secret = md5($$orig_secret, false);
				foreach ($cookie_keys as $cookie_key) {
					$key = '-'.str_replace('_', '-', $cookie_key);
					if (isset($_COOKIE['photonic-'.$secret.$key])) {
						$cookie[$provider][$cookie_key] = $_COOKIE['photonic-'.$secret.$key];
					}
				}
			}
		}
		return $cookie;
	}

	/**
	 * The initiation process for the authentication. When a user clicks on this button, a request token is obtained and the authorization
	 * is performed. Then the user is redirected to an authorization site, where the user can authorize this site.
	 */
	function authenticate() {
		if (isset($_POST['provider'])) {
			$provider = $_POST['provider'];
			$callback_id = $_POST['callback_id'];
			$post_id = substr($callback_id, 19);
			global $photonic_callback_url;
			$photonic_callback_url = get_permalink($post_id);

			switch ($provider) {
				case 'flickr':
					global $photonic_flickr_gallery;
					if (!isset($photonic_flickr_gallery)) {
						$photonic_flickr_gallery = new Photonic_Flickr_Processor();
					}
					$request_token = $photonic_flickr_gallery->get_request_token();
					$authorize_url = $photonic_flickr_gallery->get_authorize_URL($request_token);
					echo $authorize_url.'&perms=read';
					die;

				case '500px':
					global $photonic_500px_gallery;
					if (!isset($photonic_500px_gallery)) {
						$photonic_500px_gallery = new Photonic_500px_Processor();
					}
					$request_token = $photonic_500px_gallery->get_request_token();
					$authorize_url = $photonic_500px_gallery->get_authorize_URL($request_token);
					echo $authorize_url;
					die;

				case 'smug':
					global $photonic_smugmug_gallery;
					if (!isset($photonic_smugmug_gallery)) {
						$photonic_smugmug_gallery = new Photonic_SmugMug_Processor();
					}
					$request_token = $photonic_smugmug_gallery->get_request_token('http://api.smugmug.com/services/oauth/');
					$authorize_url = $photonic_smugmug_gallery->get_authorize_URL($request_token);
					echo $authorize_url.'&Access=Full&Permissions=Read';
					die;
			}
		}
	}

	public function get_oauth2_access_token() {
		$parameters = Photonic_Processor::parse_parameters($_SERVER['QUERY_STRING']);
		global $photonic_picasa_client_secret, $photonic_instagram_client_secret;
		if ((isset($parameters['code']) || isset($parameters['token']) && isset($parameters['state']))) {
			$state_args = explode('::', $parameters['state']);
			if ($state_args[0] == md5($photonic_picasa_client_secret.'picasa')) { // Picasa response
				global $photonic_picasa_gallery;
				if (!isset($photonic_picasa_gallery)) {
					$photonic_picasa_gallery = new Photonic_Picasa_Processor();
				}
				$photonic_picasa_gallery->get_access_token($parameters);
			}
			else if ($state_args[0] == md5($photonic_instagram_client_secret.'instagram')) { // Instagram response
				global $photonic_instagram_gallery;
				if (!isset($photonic_instagram_gallery)) {
					$photonic_instagram_gallery = new Photonic_Instagram_Processor();
				}
				$photonic_instagram_gallery->get_access_token($parameters);
			}
		}
	}

	function invoke_helper() {
		global $photonic_options_manager;
		require_once(plugin_dir_path(__FILE__)."/photonic-options-manager.php");
		$photonic_options_manager = new Photonic_Options_Manager(__FILE__);
		$photonic_options_manager->init();
		$photonic_options_manager->invoke_helper();
	}

	function verify_password() {
		if (isset($_REQUEST['photonic-zenfolio-password'])) {
			global $photonic_zenfolio_gallery;
			if (!isset($photonic_zenfolio_gallery)) {
				$photonic_zenfolio_gallery = new Photonic_Zenfolio_Processor();
			}
			$response = $photonic_zenfolio_gallery->verify_password();
			if ($response == 'Success') {
				//
			}
			echo $response;
		}
		die();
	}

	function after_setup_theme() {
		$current_filters = $GLOBALS['wp_filter'];
		$filter_what = array('the_content', 'the_excerpt', 'widget_text');
		$remove_hooks = apply_filters('photonic_remove_filter_hooks', array('themify_addlightboxrel_replace'));
		if (is_array($current_filters)) {
			foreach ($filter_what as $filter_name) {
				if (isset($current_filters[$filter_name]) && is_array($current_filters[$filter_name])) {
					foreach ($current_filters[$filter_name] as $priority => $hooks) {
						if (is_array($hooks)) {
							foreach ($remove_hooks as $hook) {
								if (isset($hooks[$hook])) {
									remove_filter($filter_name, $hook, $priority);
								}
							}
						}
					}
				}
			}
		}
	}
}

add_action('init', 'photonic_init');
function photonic_init() {
	global $photonic;
	$photonic = new Photonic();
//	$photonic->after_setup_theme();
}
