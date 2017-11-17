<?php
/**
 * Displays the content on the plugin settings page
 */

require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

if ( ! class_exists( 'Gglcptch_Settings_Tabs' ) ) {
	class Gglcptch_Settings_Tabs extends Bws_Settings_Tabs {
		private $keys, $versions, $forms, $sections, $themes;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $gglcptch_options, $gglcptch_plugin_info;

			$tabs = array(
				'settings'      => array( 'label' => __( 'Settings', 'google-captcha' ) ),
				'misc'          => array( 'label' => __( 'Misc', 'google-captcha' ) ),
				'custom_code'   => array( 'label' => __( 'Custom Code', 'google-captcha' ) ),
				'license'       => array( 'label' => __( 'License Key', 'google-captcha' ) )
			);

			parent::__construct( array(
				'plugin_basename'    => $plugin_basename,
				'plugins_info'       => $gglcptch_plugin_info,
				'prefix'             => 'gglcptch',
				'default_options'    => gglcptch_get_default_options(),
				'options'            => $gglcptch_options,
				'is_network_options' => is_network_admin(),
				'tabs'               => $tabs,
				'wp_slug'            => 'google-captcha',
				'pro_page'           => 'admin.php?page=google-captcha-pro.php',
				'bws_license_plugin' => 'google-captcha-pro/google-captcha-pro.php',
				'link_key'           => 'b850d949ccc1239cab0da315c3c822ab',
				'link_pn'            => '109'
			) );

			$this->all_plugins = get_plugins();

			/* Private and public keys */
			$this->keys = array(
				'public' => array(
					'display_name'	=>	__( 'Site Key', 'google-captcha' ),
					'form_name'		=>	'gglcptch_public_key',
					'error_msg'		=>	'',
				),
				'private' => array(
					'display_name'	=>	__( 'Secret Key', 'google-captcha' ),
					'form_name'		=>	'gglcptch_private_key',
					'error_msg'		=>	'',
				),
			);

			$this->versions = array(
				'v1'			=> sprintf( '%s 1', __( 'Version', 'google-captcha' ) ),
				'v2'			=> sprintf( '%s 2', __( 'Version', 'google-captcha' ) ),
				'invisible'		=> __( 'Invisible', 'google-captcha' )
			);

			/* Supported forms */
			$this->forms = gglcptch_get_forms();
			$this->sections = gglcptch_get_sections();

			/* Google captcha themes */
			$this->themes = array(
				array( 'red', 'Red' ),
				array( 'white', 'White' ),
				array( 'blackglass', 'Blackglass' ),
				array( 'clean', 'Clean' ),
			);

			add_action( get_parent_class( $this ) . '_display_custom_messages', array( $this, 'display_custom_messages' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {

			/* Save data for settings page */
			if ( empty( $_POST['gglcptch_public_key'] ) ) {
				$this->keys['public']['error_msg'] = __( 'Enter site key', 'google-captcha' );
				$error = __( "WARNING: The captcha will not be displayed until you fill key fields.", 'google-captcha' );
			} else {
				$this->keys['public']['error_msg'] = '';
			}

			if ( empty( $_POST['gglcptch_private_key'] ) ) {
				$this->keys['private']['error_msg'] = __( 'Enter secret key', 'google-captcha' );
				$error = __( "WARNING: The captcha will not be displayed until you fill key fields.", 'google-captcha' );
			} else {
				$this->keys['private']['error_msg'] = '';
			}

			if ( $_POST['gglcptch_public_key'] != $this->options['public_key'] || $_POST['gglcptch_private_key'] != $this->options['private_key'] ) {
				$this->options['keys_verified'] = false;
			}

			if ( $_POST['gglcptch_recaptcha_version'] != $this->options['recaptcha_version'] ) {
				$this->options['keys_verified'] = false;
				$this->options['need_keys_verified_check'] = true;
			}

			$this->options['whitelist_message']	=	stripslashes( esc_html( $_POST['gglcptch_whitelist_message'] ) );
			$this->options['public_key']			=	trim( stripslashes( esc_html( $_POST['gglcptch_public_key'] ) ) );
			$this->options['private_key']		=	trim( stripslashes( esc_html( $_POST['gglcptch_private_key'] ) ) );
			$this->options['recaptcha_version']	=	in_array( $_POST['gglcptch_recaptcha_version'], array( 'v1', 'v2', 'invisible' ) ) ? $_POST['gglcptch_recaptcha_version']: 'v2';
			$this->options['theme']				=	stripslashes( esc_html( $_POST['gglcptch_theme'] ) );
			$this->options['theme_v2']			=	stripslashes( esc_html( $_POST['gglcptch_theme_v2'] ) );

			$this->options['disable_submit']	= isset( $_POST['gglcptch_disable_submit'] ) ? 1 : 0;

			foreach ( $this->forms as $form_slug => $form_data ) {
				$this->options[ $form_slug ] = isset( $_POST["gglcptch_{$form_slug}"] ) ? 1 : 0;
			}

			if ( function_exists( 'get_editable_roles' ) ) {
				foreach ( get_editable_roles() as $role => $fields ) {
					$this->options[ $role ] = isset( $_POST[ 'gglcptch_' . $role ] ) ? 1 : 0;
				}
			}

			update_option( 'gglcptch_options', $this->options );
			$message = __( "Settings saved.", 'google-captcha' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Displays 'settings' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_settings() {
			global $wp_version;
			$is_main_site = is_main_site( get_current_blog_id() ); ?>
			<h3 class="bws_tab_label"><?php _e( 'Google Captcha Settings', 'google-captcha' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label"><?php _e( 'Authentication', 'google-captcha' ); ?></div>
			<div class="bws_info"><?php _e( 'Register your website with Google to get required API keys and enter them below.', 'google-captcha' ); ?> <a target="_blank" href="https://www.google.com/recaptcha/admin#list"><?php _e( 'Get the API Keys', 'google-captcha' ); ?></a></div>
			<table class="form-table">
				<?php foreach ( $this->keys as $key => $fields ) { ?>
					<tr>
						<th><?php echo $fields['display_name']; ?></th>
						<td>
							<input class="regular-text" type="text" name="<?php echo $fields['form_name']; ?>" value="<?php echo $this->options[ $key . '_key' ] ?>" maxlength="200" />
							<label class="gglcptch_error_msg error"><?php echo $fields['error_msg']; ?></label>
							<span class="dashicons dashicons-yes gglcptch_verified <?php if ( ! isset( $this->options['keys_verified'] ) || true !== $this->options['keys_verified'] ) echo 'hidden'; ?>"></span>
						</td>
					</tr>
				<?php }
				if ( ! empty( $this->options['public_key'] ) && ! empty( $this->options['private_key'] ) ) { ?>
					<tr class="hide-if-no-js">
						<th></th>
						<td>
							<div id="gglcptch-test-keys">
								<a class="button button-secondary" href="<?php echo add_query_arg( array( '_wpnonce' => wp_create_nonce( 'gglcptch-test-keys' ), 'action' => 'gglcptch-test-keys', 'is_network' => $this->is_network_options ? '1' : '0' ), admin_url( 'admin-ajax.php' ) ); ?>"><?php _e( 'Test reCAPTCHA' , 'google-captcha' ); ?></a>
							</div>
						</td>
					</tr>
				<?php } ?>
			</table>
			<div class="bws_tab_sub_label"><?php _e( 'General', 'google-captcha' ); ?></div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Enable reCAPTCHA for', 'google-captcha' ); ?></th>
					<td>
						<!--[if !IE]> -->
							<div class="gglcptch-settings-accordion">
						<!-- <![endif]-->
							<?php foreach ( $this->sections as $section_slug => $section ) {

								if ( empty( $section['name'] ) || empty( $section['forms'] ) || ! is_array( $section['forms'] ) ) {
									continue;
								}

								$section_notice = ! empty( $section['section_notice'] ) ? $section['section_notice'] : ''; ?>
								<p class="gglcptch_section_header">
									<i><?php echo $section['name']; ?></i>
									<?php if ( ! empty( $section_notice ) ) { ?>
										&nbsp;<span class="bws_info"><?php echo $section_notice; ?></span>
									<?php } ?><br />
								</p>
								<fieldset class="gglcptch_section_forms">
									<?php foreach ( $section['forms'] as $form_slug ) {
										$form_notice = $this->forms[ $form_slug ]['form_notice'];
										$form_atts = '';
										if ( '' != $form_notice || '' != $section_notice ) {
											$form_atts .= disabled( 1, 1, false );
										}
										$form_atts .= checked( ! empty( $this->options[ $form_slug ] ), true, false ); ?>
										<label>
											<input type="checkbox"<?php echo $form_atts; ?> name="gglcptch_<?php echo $form_slug; ?>" value="1" /> <?php echo $this->forms[ $form_slug ]['form_name']; ?>
										</label>
										<?php if ( '' != $form_notice ) { ?>
											&nbsp;<span class="bws_info"><?php echo $form_notice; ?></span>
										<?php } ?>
										<br />
									<?php } ?>
									<hr />
								</fieldset>
							<?php } ?>
						<!--[if !IE]> -->
							</div> <!-- .gglcptch-settings-accordion -->
						<!-- <![endif]-->
					</td>
				</tr>
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-captcha' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<?php gglcptch_supported_plugins_banner(); ?>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Hide reCAPTCHA for', 'google-captcha' ); ?></th>
					<td>
						<fieldset>
							<?php if ( function_exists( 'get_editable_roles' ) ) {
								foreach ( get_editable_roles() as $role => $fields ) {
									printf(
										'<label><input type="checkbox" name="%1$s" value="%2$s" %3$s> %4$s</label><br/>',
										'gglcptch_' . $role,
										$role,
										checked( ! empty( $this->options[ $role ] ), true, false ),
										translate_user_role( $fields['name'] )
									);
								}
							} ?>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'reCAPTCHA Version', 'google-captcha' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $this->versions as $version => $version_name ) { ?>
								<label>
									<input type="radio" name="gglcptch_recaptcha_version" value="<?php echo $version; ?>" <?php checked( $version, $this->options['recaptcha_version'] ); ?>> <?php echo $version_name; ?>
								</label>
								<br/>
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr class="gglcptch_theme_v1" valign="top">
					<th scope="row">
						<?php _e( 'Theme', 'google-captcha' ); ?>
					</th>
					<td>
						<select name="gglcptch_theme">
							<?php foreach ( $this->themes as $theme ) { ?>
								<option value="<?php echo $theme[0]; ?>" <?php selected( $theme[0], $this->options['theme'] ); ?>><?php echo $theme[1]; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr class="gglcptch_theme_v2" valign="top">
					<th scope="row">
						<?php _e( 'Theme', 'google-captcha' ); ?>
					</th>
					<td>
						<select name="gglcptch_theme_v2">
							<option value="light" <?php selected( 'light', $this->options['theme_v2'] ); ?>>Light</option>
							<option value="dark" <?php selected( 'dark', $this->options['theme_v2'] ); ?>>Dark</option>
						</select>
					</td>
				</tr>
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-captcha' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<?php gglcptch_additional_settings_banner(); ?>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Whitelist Notification', 'google-captcha' ); ?></th>
					<td>
						<textarea name="gglcptch_whitelist_message"><?php echo $this->options['whitelist_message']; ?></textarea>
						<div class="bws_info"><?php _e( 'This message will be displayed instead of the reCAPTCHA.', 'google-captcha' ); ?></div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Disabled Submit Button', 'google-captcha' ); ?></th>
					<td>
						<input<?php echo $this->change_permission_attr; ?> id="gglcptch_disable_submit" type="checkbox" <?php checked( ! empty( $this->options["disable_submit"] ) ); ?> name="gglcptch_disable_submit" value="1" />&nbsp;
						<span class="bws_info">
							<?php _e( 'Enable to keep submit button disabled until reCAPTCHA is loaded (do not use this option if you see "Failed to load Google reCAPTCHA" message).', 'google-captcha' ); ?>
						</span>
					</td>
				</tr>
			</table>
		<?php }

		/**
		 * Display custom error\message\notice
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function display_custom_messages( $save_results ) {
			if ( $this->options['recaptcha_version'] == 'v1' ) { ?>
				<div class="updated inline bws-notice"><p><strong><?php _e( "Only one reCAPTCHA can be displayed on the page, it's related to reCAPTCHA version 1 features.", 'google-captcha' ); ?></strong></p></div>
			<?php }
			if ( ! empty( $this->options['need_keys_verified_check'] ) ) { ?>
				<div class="updated inline bws-notice"><p><strong><?php _e( 'reCAPTCHA version was changed. Please submit "Test reCAPTCHA" and regenerate Site and Secret keys if necessary.', 'google-captcha' ); ?></strong></p></div>
			<?php }
		}

		/**
		 * Display custom metabox
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Google Captcha Shortcode', 'google-captcha' ); ?>
				</h3>
				<div class="inside">
					<?php _e( "Add Google Captcha to your posts or pages using the following shortcode:", 'google-captcha' ); ?>
					<?php bws_shortcode_output( '[bws_google_captcha]' ); ?>
				</div>
			</div>
		<?php }
	}
}