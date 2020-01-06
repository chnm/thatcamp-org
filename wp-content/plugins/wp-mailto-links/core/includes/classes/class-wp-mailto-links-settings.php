<?php

/**
 * Class WP_Mailto_Links_Settings
 *
 * This class contains all of our important settings
 * Here you can configure the whole plugin behavior.
 *
 * @since 3.0.0
 * @package WPMT
 * @author Ironikus <info@ironikus.com>
 */
class WP_Mailto_Links_Settings{

	/**
	 * Our globally used capability
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $admin_cap;

	/**
	 * The main page name
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_name;

	/**
	 * WP_Mailto_Links_Settings constructor.
	 *
	 * We define all of our necessary settings in here.
	 * If you need to do plugin related changes, everything will
	 * be available in this file.
	 */
	function __construct(){
		$this->admin_cap            	= 'manage_options';
		$this->page_name            	= 'wp-mailto-links-option-page';
		$this->page_title           	= WPMT_NAME;
		$this->final_outout_buffer_hook = 'final_output';
		$this->widget_callback_hook 	= 'widget_output';
		$this->template_tags 			= array( 'wpml_filter' => 'template_tag_wpmt_filter', 'wpml_mailto' => 'template_tag_wpml_mailto' );
		$this->settings_key        		= 'wp-mailto-links';
		$this->version_key        		= 'wp-mailto-links-version';
		$this->image_secret_key     	= 'wp-mailto-links-img-key';
		$this->at_identifier     		= '##wpmtAddIdent##';
		$this->previous_version        	= null;
		$this->hook_priorities        	= array(
			'buffer_final_output' => 1000,
			'setup_single_filter_hooks' => 100,
			'add_custom_template_tags' => 10,
			'load_frontend_header_styling' => 10,
			'wpmt_dynamic_sidebar_params' => 100,
			'filter_rss' => 100,
			'filter_page' => 100,
			'filter_content' => 100,
			'first_version_init' => 100,
			'version_update' => 100,
			'display_email_image' => 10,
			'callback_rss_remove_shortcodes' => 10,
		);

		//Regex
		$this->email_regex 			= '([_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,}))';
		$this->soft_attribute_regex = array(
			'woocommerce_variation_attribute_tag' 				=> '/data-product_variations="([^"]*)"/i',
			'jetpack_carousel_image_attribute_tag' 				=> '/data-image-meta="([^"]*)"/i',
			'jetpack_carousel_image_attribute_description_tag' 	=> '/data-image-description="([^"]*)"/i',
			'html_placeholder_tag' 								=> '/placeholder="([^"]*)"/i',
		);

		//Load data
		$this->settings        			= $this->load_settings();
		$this->version        			= $this->load_version();
		$this->email_image_secret       = $this->load_email_image_secret();
	}

	/**
	 * ######################
	 * ###
	 * #### MAIN SETTINGS
	 * ###
	 * ######################
	 */

	 /**
	  * Load the settings for our admin settings page
	  *
	  * @return array - An array with all available settings and filled values
	  */
	private function load_settings(){
		$fields = array(

			'protect' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'protect',
				'type'        => 'multi-input',
				'input-type'  => 'radio',
				'title'       => WPMT()->helpers->translate( 'Protect emails', 'wpmt-settings-protect' ),
				'inputs' 	  => array( 
					1 => array(
						'label' => WPMT()->helpers->translate( 'Full-page scan', 'wpmt-settings-protect-label' ),
						'description' => WPMT()->helpers->translate('This will check the whole page against any mails and secures them.', 'wpmt-settings-protect-tip')
					),
					2 => array(
						'label' => WPMT()->helpers->translate( 'Wordpress filters', 'wpmt-settings-protect-label' ),
						'description' => WPMT()->helpers->translate('Secure only mails that occur within WordPress filters. (Not recommended)', 'wpmt-settings-protect-tip'),
						'advanced' 	  => true,
					),
					3 => array(
						'label' => WPMT()->helpers->translate( 'Don\'t do anything.', 'wpmt-settings-protect-label' ),
						'description' => WPMT()->helpers->translate('This turns off the protection for emails. (Not recommended)', 'wpmt-settings-protect-tip')
					),
				 ),
				'required'    => false
			),

			'protect_using' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'protect_using',
				'type'        => 'multi-input',
				'input-type'  => 'radio',
				'title'       => WPMT()->helpers->translate( 'Protect emails using', 'wpmt-settings-protect_using' ),
				'inputs' 	  => array( 
					'with_javascript' => array(
						'label' => WPMT()->helpers->translate( 'automatically the best method (including javascript)', 'wpmt-settings-protect_using-label' )
					),
					'without_javascript' => array(
						'label' => WPMT()->helpers->translate( 'automatically the best method (excluding javascript)', 'wpmt-settings-protect_using-label' ),
					),
					'strong_method' => array(
						'label' => WPMT()->helpers->translate( 'a strong method that replaces all emails with a "*protection text*".', 'wpmt-settings-protect_using-label' ),
						'description' => WPMT()->helpers->translate('You can configure the protection text within the advanced settings.', 'wpmt-settings-protect_using-tip')
					),
					'char_encode' => array(
						'label' => WPMT()->helpers->translate( 'simple HTML character encoding.', 'wpmt-settings-protect_using-label' ),
						'description' => WPMT()->helpers->translate('Offers goot (but not the best) protection, which saves you in most scenarios.', 'wpmt-settings-protect_using-tip')
					),
				 ),
				'required'    => false
			),

			'filter_body' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'filter_body',
				'type'        => 'multi-input',
				'input-type'  => 'checkbox',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate( 'Protect...', 'wpmt-settings-filter_body' ),
				'label'       => WPMT()->helpers->translate( 'Customize what this plugin protects.', 'wpmt-settings-filter_body-label' ),
				'inputs' 	  => array(
					'filter_rss' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'RSS feed', 'wpmt-settings-filter_rss-label' ),
						'description' => WPMT()->helpers->translate( 'Activating this option results in protecting the rss feed based on the given protection method.', 'wpmt-settings-filter_rss-tip' )
					),
					'remove_shortcodes_rss' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'Remove all shortcodes from the RSS feeds', 'wpmt-settings-remove_shortcodes_rss-label' ),
						'description' => WPMT()->helpers->translate( 'Activating this option results in protecting the rss feed based on the given protection method.', 'wpmt-settings-remove_shortcodes_rss-tip' )
					),
					'input_strong_protection' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'input form email fields using strong protection.', 'wpmt-settings-input_strong_protection-label' ),
						'description' => WPMT()->helpers->translate( 'Warning: this option could conflict with certain form plugins. Test it first. (Requires javascript)', 'wpmt-settings-input_strong_protection-tip' )
					),
					'convert_plain_to_mailto' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'plain emails by converting them to mailto links', 'wpmt-settings-convert_plain_to_mailto-label' ),
						'description' => WPMT()->helpers->translate( 'Plain emails will be automatically converted to mailto links where possible.', 'wpmt-settings-convert_plain_to_mailto-tip' )
					),
					'convert_plain_to_image' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'plain emails by converting them to png images', 'wpmt-settings-convert_plain_to_mailto-label' ),
						'description' => WPMT()->helpers->translate( 'Plain emails will be automatically converted to png images where possible.', 'wpmt-settings-convert_plain_to_mailto-tip' )
					),
					'protect_shortcode_tags' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'shortcode content', 'wpmt-settings-protect_shortcode_tags-label' ),
						'description' => WPMT()->helpers->translate( 'Protect every shortcode content separately. (This may slows down your site)', 'wpmt-settings-protect_shortcode_tags-tip' )
					),
					'filter_hook' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'emails from "init" hook', 'wpmt-settings-filter_hook-label' ),
						'description' => WPMT()->helpers->translate( 'Check this option if you want to register the email filters on the "init" hook instead of the "wp" hook.', 'wpmt-settings-filter_hook-tip' )
					),
					'deactivate_rtl' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'mailto links without CSS direction', 'wpmt-settings-filter_hook-label' ),
						'description' => WPMT()->helpers->translate( 'Check this option if your site does not support CSS directions.', 'wpmt-settings-filter_hook-tip' )
					),
					'no_script_tags' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'no script tags', 'wpmt-settings-filter_hook-label' ),
						'description' => WPMT()->helpers->translate( 'Check this option if you face issues with encoded script tags. This will deactivate protection for script tags.', 'wpmt-settings-filter_hook-tip' )
					),
					'no_attribute_validation' => array(
						'advanced' 	  => true,
						'label' => __( 'html attributes without soft encoding.', 'wp-mailto-links' ),
						'description' => __( 'Do not soft-filter all html attributes. This might optimizes the performance, but can break the site if other plugins use your email in attribute tags.', 'wp-mailto-links' )
					),
				 ),
				'required'    => false,
			),

			'image_settings' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'image_settings',
				'type'        => 'multi-input',
				'input-type'  => 'text',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate( 'Image settings', 'wpmt-settings-filter_body' ),
				'label'       => WPMT()->helpers->translate( 'Customize the settings for dynamically created images.', 'wpmt-settings-filter_body-label' ),
				'inputs' 	  => array(
					'image_color' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'Image Colors', 'wpmt-settings-image_color-label' ),
						'description' => WPMT()->helpers->translate( 'Please include RGB colors, comme saparated. E.g.: 0,0,255', 'wpmt-settings-image_color-tip' )
					),
					'image_background_color' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'Image Background Colors', 'wpmt-settings-image_background_color-label' ),
						'description' => WPMT()->helpers->translate( 'Please include RGB colors, comme saparated. E.g.: 0,0,255', 'wpmt-settings-image_background_color-tip' )
					),
					'image_text_opacity' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'Text Opacity', 'wpmt-settings-image_text_opacity-label' ),
						'description' => WPMT()->helpers->translate( 'Change the text opacity for the created images. 0 = not transparent - 127 = completely transprent', 'wpmt-settings-image_text_opacity-tip' )
					),
					'image_background_opacity' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'Background Opacity', 'wpmt-settings-image_background_opacity-label' ),
						'description' => WPMT()->helpers->translate( 'Change the background opacity for the created images. 0 = not transparent - 127 = completely transprent', 'wpmt-settings-image_background_opacity-tip' )
					),
					'image_font_size' => array(
						'advanced' 	  => true,
						'label' => WPMT()->helpers->translate( 'Font Size', 'wpmt-settings-image_font_size-label' ),
						'description' => WPMT()->helpers->translate( 'Change the font size of the image text. Default: 4 - You can choose from 1 - 5', 'wpmt-settings-image_font_size-tip' )
					),
				 ),
				'required'    => false,
			),

			'exclude_posts' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'exclude_posts',
				'type'        => 'text',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate('Exclude post id\'s from protection', 'wpmt-settings-exclude_posts'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('By comma separating post id\'s ( e.g. 123,4535,643), you are able to exclude these posts from the logic protection.', 'wpmt-settings-exclude_posts-tip')
			),

			'protection_text' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'protection_text',
				'type'        => 'text',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate('Set protection text *', 'wpmt-settings-class_name'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('This text will be shown for protected email addresses and within noscript tags.', 'wpmt-settings-class_name-tip')
			),

			'class_name' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'class_name',
				'type'        => 'text',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate('Additional classes', 'wpmt-settings-class_name'),
				'label'       => WPMT()->helpers->translate('Add extra classes to mailto links.', 'wpmt-settings-class_name-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('Leave blank for none', 'wpmt-settings-class_name-tip')
			),

			'footer_scripts' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'footer_scripts',
				'type'        => 'checkbox',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate('Load scripts in footer', 'wpmt-settings-footer_scripts'),
				'label'       => WPMT()->helpers->translate('Check this button if you want to load all frontend scripts within the footer.', 'wpmt-settings-footer_scripts-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('This forces every script to be enqueued within the footer.', 'wpmt-settings-footer_scripts-tip')
			),

			'disable_marketing' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'disable_marketing',
				'type'        => 'checkbox',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate('Disable Marketing', 'wpmt-settings-disable_marketing'),
				'label'       => WPMT()->helpers->translate('Disable all marketing notifications', 'wpmt-settings-disable_marketing-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('If you are not satisfied with our marketing recommendations, check this box.', 'wpmt-settings-disable_marketing-tip')
			),

			'security_check' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'security_check',
				'type'        => 'checkbox',
				'title'       => WPMT()->helpers->translate('Security Check', 'wpmt-settings-security_check'),
				'label'       => WPMT()->helpers->translate('Mark emails on the site as successfully encoded', 'wpmt-settings-security_check-label') . '<i class="dashicons-before dashicons-lock" style="color:green;"></i>',
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('Only visible for admin users. If your emails look broken, simply deactivate this feature.', 'wpmt-settings-security_check-tip')
			),

			'own_admin_menu' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'own_admin_menu',
				'type'        => 'checkbox',
				'advanced' 	  => true,
				'title'       => WPMT()->helpers->translate('Admin Menu', 'wpmt-settings-own_admin_menu'),
				'label'       => WPMT()->helpers->translate('Show this page in the main menu item', 'wpmt-settings-own_admin_menu-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('Otherwise it will be shown in "Settings"-menu.', 'wpmt-settings-security_check-tip')
			),

			'advanced_settings' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'advanced_settings',
				'type'        => 'checkbox',
				'title'       => WPMT()->helpers->translate('Advanced Settings', 'wpmt-settings-advanced_settings'),
				'label'       => WPMT()->helpers->translate('Show advanced settings for more configuration possibilities.', 'wpmt-settings-advanced_settings-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => WPMT()->helpers->translate('Activate the advanced settings in case you want to customize the default logic or you want to troubleshoot the plugin.', 'wpmt-settings-advanced_settings-tip')
			),

		);

		//End Migrate Old Plugin

		$default_values = array(
			'protect' 				=> 1,
			'filter_rss' 			=> 1,
			'protect_using' 		=> 'with_javascript',
			'class_name' 			=> 'mail-link',
			'protection_text' 		=> '*protected email*',
			'image_color' 			=> '0,0,0',
			'image_background_color'=> '0,0,0',
			'image_text_opacity'	=> '0',
			'image_background_opacity'	=> '127',
			'image_font_size'	=> '4',
		);
		$values = get_option( $this->settings_key );

		if( empty( $values ) && ! is_array( $values ) ){
			update_option( $this->settings_key, $default_values );
			$values = $default_values;
		}

		//Bakwards compatibility
		if( ! isset( $values['protect_using'] ) ){
			$values['protect_using'] = 'with_javascript';
		}

		//In case the mailto functiinality was deactivated, we will set it do "Do nothing" as well.
		if( ! isset( $values['protect'] ) || (string) $values['protect'] === '0' ){
			$values['protect'] = 3;
		}
		///Backwards compatibility

		//Value corrections
		if( ! isset( $values['image_color'] ) ){
			$values['image_color'] = $default_values['image_color'];
		}
		$image_color = explode( ',', $values['image_color'] );
		if( count( $image_color ) != 3 ){
			$values['image_color'] = $default_values['image_color'];
		}
		foreach( explode( ',', $values['image_color'] ) as $image_color_key => $image_color_single ){
			if( ! is_numeric( trim( $image_color_single ) ) ){
				$values['image_color'] = $default_values['image_color'];
			}
		}

		if( ! isset( $values['image_background_color'] ) ){
			$values['image_background_color'] = $default_values['image_background_color'];
		}
		$image_background_color = explode( ',', $values['image_background_color'] );
		if( count( $image_background_color ) != 3 ){
			$values['image_background_color'] = $default_values['image_background_color'];
		}
		foreach( explode( ',', $values['image_background_color'] ) as $image_background_color_key => $image_background_color_single ){
			if( ! is_numeric( trim( $image_background_color_single ) ) ){
				$values['image_background_color'] = $default_values['image_background_color'];
			}
		}

		if( ! isset( $values['image_text_opacity'] ) || ! is_numeric( $values['image_text_opacity'] ) ){
			$values['image_text_opacity'] = $default_values['image_text_opacity'];
		}
		if( ! isset( $values['image_background_opacity'] ) || ! is_numeric( $values['image_background_opacity'] ) ){
			$values['image_background_opacity'] = $default_values['image_background_opacity'];
		}
		if( ! isset( $values['image_font_size'] ) || ! is_numeric( $values['image_font_size'] ) ){
			$values['image_font_size'] = $default_values['image_font_size'];
		}
		///Value corrections

		foreach( $fields as $key => $field ){
			if( $field['type'] === 'multi-input' ){
				foreach( $field['inputs'] as $smi_key => $smi_data ){

					if( $field['input-type'] === 'radio' ){
						if( isset( $values[ $key ] ) && (string) $values[ $key ] === (string) $smi_key ){
							$fields[ $key ]['value'] = $values[ $key ];
						}
					} else {
						if( isset( $values[ $smi_key ] ) ){
							$fields[ $key ]['inputs'][ $smi_key ]['value'] = $values[ $smi_key ];
						}
					}
					
				}
			} else {
				if( isset( $values[ $key ] ) ){
					$fields[ $key ]['value'] = $values[ $key ];
				}
			}
		}

		return apply_filters( 'wpmt/settings/fields', $fields );
	}

	/**
	 * ######################
	 * ###
	 * #### VERSIONING
	 * ###
	 * ######################
	 */

	 public function load_version(){

		$current_version = get_option( $this->get_version_key() );

		if( empty( $current_version ) ){
			$current_version = WPMT_VERSION;
			update_option( $this->get_version_key(), $current_version );

			add_action( 'init', array( $this, 'first_version_init' ), $this->get_hook_priorities( 'first_version_init' ) );
		} else {
			if( $current_version !== WPMT_VERSION ){
				$this->previous_version = $current_version;
				$current_version = WPMT_VERSION;
				update_option( $this->get_version_key(), $current_version );

				add_action( 'init', array( $this, 'version_update' ), $this->get_hook_priorities( 'version_update' ) );
			}
		}

		return $current_version;
	 }

	 public function load_email_image_secret(){

		if( ! (bool) $this->get_setting( 'convert_plain_to_image', true, 'filter_body' ) ){
			return false;
		}

		$image_descret = get_option( $this->get_image_secret_key() );

		if( ! empty( $image_descret ) ){
			return $image_descret;
		}

		$key = '';

		for ($i = 0; $i < 265; $i++) {
			$key .= chr(mt_rand(33, 126));
		}

		update_option( $this->get_image_secret_key(), $key );

		return $key;
	 }

	 /**
	  * Fires an action after our settings key was initially set
	  * the very first time.
	  *
	  * @return void
	  */
	 public function first_version_init(){
		 do_action( 'wpmt/settings/first_version_init', WPMT_VERSION );
	 }

	 /**
	  * Fires after the version of the plugin is initially updated
	  *
	  * @return void
	  */
	 public function version_update(){
		 do_action( 'wpmt/settings/version_update', WPMT_VERSION, $this->previous_version );
	 }

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Our admin cap handler function
	 *
	 * This function handles the admin capability throughout
	 * the whole plugin.
	 *
	 * $target - With the target function you can make a more precised filtering
	 * by changing it for specific actions.
	 *
	 * @param string $target - A identifier where the call comes from
	 * @return mixed
	 */
	public function get_admin_cap( $target = 'main' ){
		/**
		 * Customize the globally used capability for this plugin
		 *
		 * This filter is called every time the capability is needed.
		 */
		return apply_filters( 'wpmt/settings/capability', $this->admin_cap, $target );
	}

	/**
	 * Return the page name for our admin page
	 *
	 * @return string - the page name
	 */
	public function get_page_name(){
		/*
		 * Filter the page name based on your needs
		 */
		return apply_filters( 'wpmt/settings/page_name', $this->page_name );
	}

	/**
	 * Return the page title for our admin page
	 *
	 * @return string - the page title
	 */
	public function get_page_title(){
		/*
		 * Filter the page title based on your needs.
		 */
		return apply_filters( 'wpmt/settings/page_title', $this->page_title );
	}

	/**
	 * Return the settings_key
	 *
	 * @return string - the settings key
	 */
	public function get_settings_key(){
		return $this->settings_key;
	}

	/**
	 * Return the version_key
	 *
	 * @return string - the version_key
	 */
	public function get_version_key(){
		return $this->version_key;
	}

	/**
	 * Return the image_secret_key
	 *
	 * @return string - the image_secret_key
	 */
	public function get_image_secret_key(){
		return $this->image_secret_key;
	}

	/**
	 * Return the email_image_secret
	 *
	 * @return string - the email_image_secret
	 */
	public function get_email_image_secret(){
		return $this->email_image_secret;
	}

	/**
	 * Return the version
	 *
	 * @return string - the version
	 */
	public function get_version(){
		return apply_filters( 'wpmt/settings/get_version', $this->version );
	}

	/**
	 * Return the default template tags
	 *
	 * @return array - the template tags
	 */
	public function get_template_tags(){
		return apply_filters( 'wpmt/settings/get_template_tags', $this->template_tags );
	}

	/**
	 * Return the widget callback hook name
	 *
	 * @return string - the final widget callback hook name
	 */
	public function get_widget_callback_hook(){
		return apply_filters( 'wpmt/settings/widget_callback_hook', $this->widget_callback_hook );
	}

	/**
	 * Return the final output buffer hook name
	 *
	 * @return string - the final output buffer hook name
	 */
	public function get_final_outout_buffer_hook(){
		return apply_filters( 'wpmt/settings/final_outout_buffer_hook', $this->final_outout_buffer_hook );
	}

	/**
	 * Return the @ symbol identifier
	 *
	 * @return string - the @ symbol identifier
	 */
	public function get_at_identifier(){
		return apply_filters( 'wpmt/settings/at_identifier', $this->at_identifier );
	}

	/**
     * @link http://www.mkyong.com/regular-expressions/how-to-validate-email-address-with-regular-expression/
     * @param boolean $include
     * @return string
     */
    public function get_email_regex( $include = false ){

        if ($include === true) {
            $return = $this->email_regex;
        } else {
			$return = '/' . $this->email_regex . '/i';
		}

		return apply_filters( 'wpmt/settings/get_email_regex', $return, $include );
	}
	
	/**
	 * Get Woocommerce variation attribute regex
	 * 
     * @param boolean $include
     * @return string
     */
    public function get_soft_attribute_regex( $single = null ){

		$return = $this->soft_attribute_regex;

		if( $single !== null ){
			if( isset( $this->soft_attribute_regex[ $single ] ) ){
				$return = $this->soft_attribute_regex[ $single ];
			} else {
				$return = false;
			}
		}

		return apply_filters( 'wpmt/settings/get_soft_attribute_regex', $return, $single );
    }

	/**
     * Get hook priorities
	 * 
     * @param boolean $single - wether you want to return only a single hook priority or not
     * @return mixed - An array or string of hook priority(-ies)
     */
    public function get_hook_priorities( $single = false ){

		$return = $this->hook_priorities;
		$default = false;
		
		if( $single ){
			if( isset( $this->hook_priorities[ $single ] ) ){
				$return = $this->hook_priorities[ $single ];
			} else {
				$return = 10;
				$default = true;
			}
		}

		return apply_filters( 'wpmt/settings/get_hook_priorities', $return, $default );
    }

	/**
	 * ######################
	 * ###
	 * #### Settings helper
	 * ###
	 * ######################
	 */

	 /**
	  * Get the admin page url
	  *
	  * @return string - The admin page url
	  */
	 public function get_admin_page_url(){

		$url = admin_url( "options-general.php?page=" . $this->get_page_name() );

		 return apply_filters( 'wpmt/settings/get_admin_page_url', $url );
	 }

	 /**
	  * Helper function to reload the settings
	  *
	  * @return array - An array of all available settings
	  */
	 public function reload_settings(){

		$this->settings = $this->load_settings();

		 return $this->settings;
	 }

	/**
	 * Return the default strings that are available
	 * for this plugin.
	 *
	 * @param $slug - the identifier for your specified setting
	 * @param $single - wether you only want to return the value or the whole settings element
	 * @param $group - in case you call a multi-input that contains multiple values (e.g. checkbox), you can set a sub-slug to grab the sub value
	 * @return string - the default string
	 */
	public function get_setting( $slug = '', $single = false, $group = '' ){
		$return = $this->settings;

		if( empty( $slug ) ){
			return $return;
		}

		if( isset( $this->settings[ $slug ] ) || ( ! empty( $group ) && isset( $this->settings[ $group ] ) ) ){
			if( $single ){
				$return = false; // Default false

				//Set default to the main valie if available given with radio buttons)
				if( isset( $this->settings[ $slug ]['value'] ) ){
					$return = $this->settings[ $slug ]['value'];
				}

				if( 
					! empty( $group )
					&& isset( $this->settings[ $group ]['type'] )
					&& $this->settings[ $group ]['type'] === 'multi-input'
					)
				{
					if( isset( $this->settings[ $group ]['inputs'][ $slug ] ) && isset( $this->settings[ $group ]['inputs'][ $slug ]['value'] ) ){
						$return = $this->settings[ $group ]['inputs'][ $slug ]['value'];
					}
				}
				
			} else {

				if( ! empty( $group ) && isset( $this->settings[ $group ] ) ){
					$return = $this->settings[ $group ];
				} else {
					$return = $this->settings[ $slug ];
				}
				
			}
			
		}

		return $return;
	}

}