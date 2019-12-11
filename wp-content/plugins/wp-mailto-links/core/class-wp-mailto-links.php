<?php
if ( ! class_exists( 'WP_Mailto_Links' ) ) :

	/**
	 * Main WP_Mailto_Links Class.
	 *
	 * @since 3.0.0
	 * @package WPMT
	 * @author Ironikus <info@ironikus.com>
	 */
	final class WP_Mailto_Links {

		/**
		 * The real instance
		 *
		 * @var WP_Mailto_Links
		 * @since 3.0.0
		 */
		private static $instance;

		/**
		 * WPMT settings Object.
		 *
		 * @var object|WP_Mailto_Links_Settings
		 * @since 3.0.0
		 */
		public $settings;

		/**
		 * WPMT helpers Object.
		 *
		 * @var object|WP_Mailto_Links_Helpers
		 * @since 3.0.0
		 */
		public $helpers;

		/**
		 * WPMT validate Object.
		 *
		 * @var object|WP_Mailto_Links_Validate
		 * @since 3.0.0
		 */
		public $validate;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @since 3.0.0
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ironikus' ), '3.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 3.0.0
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ironikus' ), '3.0.0' );
		}

		/**
		 * Main WP_Mailto_Links Instance.
		 *
		 * Insures that only one instance of WP_Mailto_Links exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 3.0.0
		 * @static
		 * @staticvar array $instance
		 * @return object|WP_Mailto_Links The one true WP_Mailto_Links
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Mailto_Links ) ) {
				self::$instance                 = new WP_Mailto_Links;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers        = new WP_Mailto_Links_Helpers();
				self::$instance->settings       = new WP_Mailto_Links_Settings();
				self::$instance->validate       = new WP_Mailto_Links_Validate();

				new WPMT_Integrations_Loader();

				new WP_Mailto_Links_Run();

				/**
				 * Fire a custom action to allow extensions to register
				 * after WP Mailto Links was successfully registered
				 */
				do_action( 'wpmt_plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 3.0.0
		 * @return void
		 */
		private function includes() {
			require_once WPMT_PLUGIN_DIR . 'core/includes/classes/class-wp-mailto-links-helpers.php';
			require_once WPMT_PLUGIN_DIR . 'core/includes/classes/class-wp-mailto-links-settings.php';
			require_once WPMT_PLUGIN_DIR . 'core/includes/classes/class-wp-mailto-links-validate.php';

			require_once WPMT_PLUGIN_DIR . 'core/includes/integrations/loader.php';
			require_once WPMT_PLUGIN_DIR . 'core/includes/functions/template-tags.php'; 

			if( is_admin() ){
				require_once WPMT_PLUGIN_DIR . 'core/includes/classes/class-wp-mailto-links-run-admin.php';
			} else {
				require_once WPMT_PLUGIN_DIR . 'core/includes/classes/class-wp-mailto-links-run.php';
			}
			
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access private
		 * @since 3.0.0
		 * @return void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 3.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( WPMT_TEXTDOMAIN, FALSE, dirname( plugin_basename( WPMT_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.