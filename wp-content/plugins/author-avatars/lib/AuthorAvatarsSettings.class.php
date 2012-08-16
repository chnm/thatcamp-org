<?php

/**
 * Shorthand for AuthorAvatarsSettings::get_instance()
 */
function &AA_Settings() {
	return AuthorAvatarsSettings::get_instance();
}

/**
 * A settings class holding all settings for the Author Avatars plugin.
 *
 * Note: this is supposed to be a single instance class (singleton). Please
 * use the get_instance() method or the AA_Settings() function to get a class instance...
 */
class AuthorAvatarsSettings {

	/**
	 * Default values for option 'author_avatars_wpmu_settings' (sitewide)
	 */
	var $defaults_sitewide = array(
		'blog_filters_enabled' => null, // blog filters only enabled for the root blog (see init in _setDefaults())
	);

	/**
	 * Default values for option 'author_avatars_settings' (per blog)
	 */
	var $defaults = array(
	);

	/**
	 * Constructor
	 *
	 * @access protected
	 */
	function AuthorAvatarsSettings() {
		$this->_setDefaults();
	}
	
	/**
	 * Initialise computed default values which cannot be set on the class field above.
	 */
	function _setDefaults() {
		// blog filters only enabled for the root blog
		$this->defaults_sitewide['blog_filters_enabled'] = defined('BLOGID_CURRENT_SITE') ? array(BLOGID_CURRENT_SITE) : array(1);
		
	}
	
	/**
	 * Implements the singleton design pattern.
	 *
	 * @access public
	 * @return AuthorAvatarsSettings singleton class instance
	 */
	function &get_instance() {
		static $instance = null;
		
		if ($instance === null) {
			$c = __CLASS__;
			$instance = new $c;
		}
		return $instance;
	}
	
	/**
	 * Loads and returns a settings value for a given key.
	 * If not value was found then the default value is returned.
	 *
	 * @access public
	 * @return mixed
	 */
	function get($key, $check_sitewide=true) {
		if ($key) {
			$settings = get_option('author_avatars_settings');
			if (isset($settings[$key])) {
				return $settings[$key];
			}
			elseif (AA_is_wpmu() && $check_sitewide &&  ($sitewide_value = $this->get_sitewide($key, true))) {
				return $sitewide_value;
			}
			else {
				return $this->get_default($key);
			}
		}
	}
	
	/**
	 * Loads and returns a sitewide settings value for a given key.
	 * If not value was found then the default value is returned.
	 *
	 * @access public
	 * @return mixed
	 */
	function get_sitewide($key, $no_default=false) {
		if ($key) {
			$settings = get_site_option('author_avatars_wpmu_settings');
			return isset($settings[$key]) ? $settings[$key] : ( $no_default ? false : $this->get_default_sitewide($key));
		}
	}
	
	/**
	 * Returns default value for an author avatar setting.
	 *
	 * @access protected
	 * @param $key the key of an author avatar setting.
	 * @return the default value or null if none found.
	 */
	function get_default($key) {
		return ($key && isset($this->defaults[$key])) ? $this->defaults[$key] : null;
	}
	
	/**
	 * Returns default value for a sitewide author avatar setting.
	 *
	 * @access protected
	 * @param $key the key of an author avatar setting.
	 * @return the default value or null if none found.
	 */
	function get_default_sitewide($key) {
		return ($key && isset($this->defaults_sitewide[$key])) ? $this->defaults_sitewide[$key] : null;
	}
	
	/**
	 * Check if a given settings value is the default value.
	 */
	function is_default($key, $value) {
		return $this->get_default($key) == $value;
	}
	
	/**
	 * Check if a given sitewide settings value is the default value.
	 */
	function is_default_sitewide($key, $value) {
		return $this->get_default_sitewide($key) == $value;
	}
	
	/**
	 * Save the given settings
	 */
	function save($settings) {
		if (!is_array($settings)) return false;
		
		// validation??
		
		// merge new settings with old ones
		$old_settings = get_option('author_avatars_settings');
		$settings = array_merge($old_settings, $settings);

		// remove default values
		foreach ($settings as $key => $value) {
			if ($this->is_default($key, $value)) {
				unset($settings[$key]);
			}
		}
		
		// save to database
		update_option('author_avatars_settings', $settings);
	}
	
	/**
	 * Save the given sitewide settings
	 */
	function save_sitewide($settings) {
		if (!is_array($settings)) return false;
			
		// validation??
		
		// merge new settings with old ones
		$old_settings = (array) get_site_option('author_avatars_wpmu_settings');
		$settings = array_merge($old_settings, $settings);
		
		// remove default values
		foreach ($settings as $key => $value) {
			if ($this->is_default_sitewide($key, $value)) {
				unset($settings[$key]);
			}
		}
		
		// save to database
		update_site_option('author_avatars_wpmu_settings', $settings);
		return true;
	}
	
	/**
	 * This function checks if the blog filters are enabled for a given blog id.
	 *
	 * @access public
	 * @param $blog The id of the blog to check.
	 * @return boolean True if the blog filters are enabled on the given blog, false otherwise.
	 */
	function blog_filters_is_active($blog) {
		$blog_filters_enabled = (array) $this->get_sitewide('blog_filters_enabled');
		return in_array(-1, $blog_filters_enabled) || in_array($blog, $blog_filters_enabled);
	}
	
	/** 
	 * Returns true if we are on a wpmu site and the current blog is allowed to show users from multiple blogs.
	 *
	 * @access public
	 * @param $blog The id of the blog to check. If empty then the current blog id is used.
	 * @return boolean True if we are allowed to use blog selection on the blog...
	 */
	function blog_selection_allowed($blog = false) {
		// check that we are on a wpmu enabled blog:
		if (!AA_is_wpmu()) return false;
		
		// if $blog is empty use current blog id
		if (!$blog) {
			global $blog_id;
			$blog = $blog_id;
		}
		return $this->blog_filters_is_active($blog);
	}
}
?>