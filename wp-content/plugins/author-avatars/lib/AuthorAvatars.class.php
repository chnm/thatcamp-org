<?php
/**
 * Author Avatars class
 *
 * Performs updates and initialises widgets, shortcodes, admin areas.
 */

		// include global helper functions file.
		require_once('helper.functions.php');
		// include settings file
		require_once('AuthorAvatarsSettings.class.php');
		require_once('AuthorAvatarsWidget.class.php');

		function myplugin_register_widgets() {
			register_widget( 'AuthorAvatarsWidget' );
		}

		add_action( 'widgets_init', 'myplugin_register_widgets' );
		

class AuthorAvatars {

	/**
	 * Constructor
	 */
	function AuthorAvatars() {

		add_action( 'init', array( &$this, 'init' ), 10 );

	}


	/**
	 * Initializing Plugin
	 */
	function init() {

		if (!$this->system_check()) {
			_e('Author avatars: system check failed.', 'author-avatars');
		}
		elseif(!$this->install_check()) {
			_e('Author avatars: install check failed.', 'author-avatars');
		}
		elseif(!$this->update_check()) {
			_e('Author avatars: update check failed.', 'author-avatars');
		}
		else {
	
			$this->init_settings();
			add_action( 'init', array( $this, 'register_resources' ), 20 );
			$this->init_shortcodes();
			$this->init_controlpanels();


			$this->init_widgets();
		//	add_action( 'widgets_init', array( &$this, 'init_widgets') );


			// add tinymce editor
			add_action( 'init', array( &$this, 'init_tinymce_editor' ), 30 );
		}
	}

	/**
	 * Check we got everything we need to use the plugin
	 */
	function system_check() {
		if (!defined('AUTHOR_AVATARS_VERSION')) die('Author Avatars: constant AUTHOR_AVATARS_VERSION is not defined.');
		if (!defined('AUTHOR_AVATARS_VERSION_HISTORY')) die('Author Avatars: constant AUTHOR_AVATARS_VERSION_HISTORY is not defined.');
		return true;
	}

	/**
	 * Include settings class
	 */
	function init_settings() {
		// include global helper functions file.
		require_once('helper.functions.php');
		// include settings file
		require_once('AuthorAvatarsSettings.class.php');
		// load translation domain on init action
		add_action('init', array($this, 'load_translation_domain'), 20);


	}

	/**
	 * Loads the translation domain for ngettext calls
	 *
	 * @see http://codex.wordpress.org/User:Nbachiyski/I18n_for_WordPress_Developers#I18n_for_theme_and_plugin_developers
	 */
	function load_translation_domain() {
		// load translation file
		$plugin_dir = basename(dirname(dirname(__FILE__)));
		load_plugin_textdomain( 'author-avatars', false, $plugin_dir . '/translations' );
	}

	/**
	 * Registers all stylesheets and scripts
	 */
	function register_resources() {
		$aa_ver = AUTHOR_AVATARS_VERSION;
		// make sure styles are written on wp_head action
//		add_action( 'wp_head', 'wp_print_styles' );
		// styles 
		wp_register_style( 'MCE_BoxStyles', get_stylesheet_directory_uri().'/editorstyle.css' );
		wp_register_style( 'author-avatars-widget', WP_PLUGIN_URL . '/author-avatars/css/widget.css', array(), $aa_ver );
		wp_register_style( 'author-avatars-shortcode', WP_PLUGIN_URL . '/author-avatars/css/shortcode.css', array(), $aa_ver );
		wp_register_style( 'admin-form', WP_PLUGIN_URL . '/author-avatars/css/admin-form.css', array(), $aa_ver );
/**/	
		// scripts
		wp_register_script( 'jquery-ui-resizable', WP_PLUGIN_URL . '/author-avatars/js/jquery-ui.resizable.js', array( 'jquery-ui-core' ), '1.5.3' );
		wp_register_script( 'author-avatars-form', WP_PLUGIN_URL . '/author-avatars/js/form.js', array( 'jquery-ui-resizable'), $aa_ver );
		wp_register_script( 'author-avatars-widget-admin', WP_PLUGIN_URL . '/author-avatars/js/widget.admin.js', array( 'author-avatars-form'  ), $aa_ver );
		wp_register_script( 'tinymce-popup', '/wp-includes/js/tinymce/tiny_mce_popup.js', array(), function_exists( 'mce_version') ? mce_version() : false );
		wp_register_script( 'author-avatars-tinymce-popup', WP_PLUGIN_URL .'/author-avatars/js/tinymce.popup.js', array( 'author-avatars-form', 'jquery-ui-tabs' ), $aa_ver );
	}

	// /**
	//  * Init author avatar widget
	//  */
	function init_widgets() {
		// include necessary file(s).
		add_action( 'widgets_init', array( $this, 'author_avatars_widget_init' ) );
		
	    add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_resources'));
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_resources'));

	}

	/**
	 * Enqueues scripts and stylesheets
	 *
	 * @return void
	 */

	function admin_enqueue_resources() {	
		if (is_admin() && basename($_SERVER['PHP_SELF']) == 'widgets.php') { 
			wp_enqueue_script('author-avatars-widget-admin');
			wp_enqueue_style('admin-form');
		}
	}
		/**
	 * Enqueues scripts and stylesheets
	 *
	 * @return void
	 */

	function wp_enqueue_resources() {
		if (!is_admin())	
			wp_enqueue_style('author-avatars-widget');
	}

	public function author_avatars_widget_init()
	{
	     register_widget( 'AuthorAvatarsWidget' );

	}

	/**
	 * Init author avatars shortcodes
	 */
	function init_shortcodes() {
		// include necessary file(s).
		require_once('AuthorAvatarsShortcode.class.php');
		require_once('ShowAvatarShortcode.class.php');
		// Create objects of the shortcode classes. Registering is done in the objects' constructors
		$this->author_avatars_shortcode = new AuthorAvatarsShortcode();
		$this->show_avatar = new ShowAvatarShortcode();
	}

	/**
	 * Init author avatars editor button
	 */
	function init_tinymce_editor() {
		// load the Editor class for TinyMCE
		require_once('AuthorAvatarsEditorButton.class.php');
		$this->editor_button = new AuthorAvatarsEditorButton();
	}

	/**
	 * Init control panels
	 */
	function init_controlpanels() {
		// include necessary file(s).
		require_once('AuthorAvatarsSitewideAdminPanel.class.php');
		$this->wpmu_settings = new AuthorAvatarsSitewideAdminPanel();
	}

	/**
	 * Number of the currently installed version of the plugin.
	 * @access private
	 */

	var $__version_installed = null;

	/**
	 * returns the version number of the currently installed plugin.
	 */
	function get_installed_version($reset = false) {
		if ($this->__version_installed == null || $reset) {
			$this->__version_installed = get_option('author_avatars_version');
		}
		return $this->__version_installed;
	}

	/**
	 * updates the number of the currently installed version.
	 */
	function set_installed_version($value) {
		$oldversion = $this->get_installed_version();
		if (empty($oldversion)) {
			add_option('author_avatars_version', $value);
		}
		else {
			update_option('author_avatars_version', $value);
		}
		$this->__version_installed = $value;
	}

	/**
	 * Check if author avatars is installed and install it if necessary
	 * @return false if an error occured, true otherwise
	 */
	function install_check() {
		$version = $this->get_installed_version(true);
		// Version not empty -> plugin already installed
		if (!empty($version)) return true;
		// Version empty: this means we are either on version 0.1 (which didn't have this option) or on a fresh install.
		else {
			// check if the 0.1 version is installed
			if (get_option('widget_blogauthors')) {
				// set installed version to 0.1
				$this->set_installed_version('0.1');
				return true;
			}
			// else it's probably a new/fresh install
			else {
				if ($this->install()) {
					$this->set_installed_version(AUTHOR_AVATARS_VERSION);
					return true;
				}
				else {
					return false; // install failed.
				}
			}
		}
	}

	/**
	 * install the plugin
	 * @return true if install was successful, false otherwise
	 */
	function install() {
		// nothing to install
		return true;
	}

	/**
	 * Check if there's any need to do updates and start updates if necessary
	 * @return false if an error occured, true otherwise
	 */
	function update_check() {
		if ($this->get_installed_version() != AUTHOR_AVATARS_VERSION) $this->do_updates();
		return true;
	}

	/**
	 * tries to do all updates until we're up to date
	 */
	function do_updates() {
		$step_count = 0;
		$max_number_updates = 25;
		while ($this->get_installed_version() != AUTHOR_AVATARS_VERSION) {
			if ($step_count >= $max_number_updates) {
				break;
				die('Author Avatars: more than 25 update steps.. something might be wrong...'); // FIXME: change error handling!?
			}
			$this->do_update_step();
			$step_count++;
		}
	}

	/**
	 * Do one version update, for example from version 0.1 to version 0.2, and updates the version number in the end.
	 */
	function do_update_step() {
		$version_history = unserialize(AUTHOR_AVATARS_VERSION_HISTORY);

		foreach ($version_history as $i => $version) {
			// for the current version, if there is a next version
			if ($version == $this->get_installed_version() && ($i+1) < count($version_history)) {
				$new_version = $version_history[$i+1];

				$fn = 'update__'. ereg_replace("[^0-9]","",$version) .'_'. ereg_replace("[^0-9]","",$new_version);

				if (method_exists($this, $fn) && !$this->{$fn}()) {
					die('Author Avatars: error trying to update version '. $version .' to '. $new_version .'. '); // FIXME: change error handling!?
				}
				else {
					$this->set_installed_version($new_version);
				}
				break;
			}
		}
	}

	/**
	 * Do update step 0.1 to 0.2
	 */
	function update__01_02() {
		// update database: convert old widgets to new ones using the "MultiWidget" class.
		$old_widget = get_option('widget_blogauthors');
		$new_widget = $old_widget;
		foreach ($new_widget as $id => $widget) {
			$new_widget[$id]['__multiwidget'] = $id;
			$new_widget[$id]['title'] = __('Blog Authors', 'author-avatars');
		}

		delete_option('widget_blogauthors');
		add_option('multiwidget_author_avatars', $new_widget);

		// update sidebar option
		$sidebars = get_option('sidebars_widgets');
		foreach ($sidebars as $i => $sidebar) {
			if(is_array($sidebar)) {
				foreach ($sidebar as $k => $widget) {
					$sidebars[$i][$k] = str_replace('blogauthors-', 'author_avatars-', $widget);
				}
			}
		}

		update_option('sidebars_widgets', $sidebars);

		// return true if update successful
		return true;
	}

	/**
	 * Do update step 0.6.2 to 0.7
	 */
	function update__062_07() {
		$widgets = (array) get_option('multiwidget_author_avatars');
		foreach ($widgets as $id => $widget) {
			if (!empty($widget['display']) && is_array($widget['display'])) {
				$key = array_search('link_to_authorpage', $widget['display']);
				if ($key !== false) {
					unset($widgets[$id]['display'][$key]);
					$widgets[$id]['display']['user_link'] = 'authorpage';
				}
			}
		}
		update_option('multiwidget_author_avatars', $widgets);
		return true;
	}
}



?>