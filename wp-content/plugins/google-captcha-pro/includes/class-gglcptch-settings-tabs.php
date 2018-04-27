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

			if ( is_network_admin() ) {
				$tabs = array(
					'settings'      => array( 'label' => __( 'Settings', 'google-captcha-pro' ) ),
					'misc'          => array( 'label' => __( 'Misc', 'google-captcha-pro' ) ),
					'license'       => array( 'label' => __( 'License Key', 'google-captcha-pro' ) )
				);
			} else {
				$tabs = array(
					'settings'      => array( 'label' => __( 'Settings', 'google-captcha-pro' ) ),
					'misc'          => array( 'label' => __( 'Misc', 'google-captcha-pro' ) ),
					'custom_code'   => array( 'label' => __( 'Custom Code', 'google-captcha-pro' ) ),
					'license'       => array( 'label' => __( 'License Key', 'google-captcha-pro' ) )
				);
			}

			parent::__construct( array(
				'plugin_basename'    => $plugin_basename,
				'plugins_info'       => $gglcptch_plugin_info,
				'prefix'             => 'gglcptch',
				'default_options'    => gglcptch_get_default_pro_options(),
				'options'            => $gglcptch_options,
				'is_network_options' => is_network_admin(),
				'tabs'               => $tabs,
				'wp_slug'            => 'google-captcha'
			) );

			$this->all_plugins = get_plugins();

			/* Private and public keys */
			$this->keys = array(
				'public' => array(
					'display_name'	=>	__( 'Site Key', 'google-captcha-pro' ),
					'form_name'		=>	'gglcptch_public_key',
					'error_msg'		=>	'',
				),
				'private' => array(
					'display_name'	=>	__( 'Secret Key', 'google-captcha-pro' ),
					'form_name'		=>	'gglcptch_private_key',
					'error_msg'		=>	'',
				),
			);

			$this->versions = array(
				'v1'			=> sprintf( '%s 1', __( 'Version', 'google-captcha-pro' ) ),
				'v2'			=> sprintf( '%s 2', __( 'Version', 'google-captcha-pro' ) ),
				'invisible'		=> __( 'Invisible', 'google-captcha-pro' )
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

			if ( $this->is_multisite && ! $this->is_network_options ) {
				if ( $network_options = get_site_option( 'gglcptch_options' ) ) {
					if ( 'all' == $network_options['network_apply'] && 0 == $network_options['network_change'] )
						$this->change_permission_attr = ' readonly="readonly" disabled="disabled"';
					if ( 'all' == $network_options['network_apply'] && 0 == $network_options['network_view'] )
						$this->forbid_view = true;
				}
			}

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
			global $wpdb;

			if ( ! $this->forbid_view && empty( $this->change_permission_attr ) ) {
				/* Save data for settings page */
				if ( empty( $_POST['gglcptch_public_key'] ) ) {
					$this->keys['public']['error_msg'] = __( 'Enter site key', 'google-captcha-pro' );
					$error = __( "WARNING: The captcha will not be displayed until you fill key fields.", 'google-captcha-pro' );
				} else {
					$this->keys['public']['error_msg'] = '';
				}

				if ( empty( $_POST['gglcptch_private_key'] ) ) {
					$this->keys['private']['error_msg'] = __( 'Enter secret key', 'google-captcha-pro' );
					$error = __( "WARNING: The captcha will not be displayed until you fill key fields.", 'google-captcha-pro' );
				} else {
					$this->keys['private']['error_msg'] = '';
				}

				if ( $_POST['gglcptch_public_key'] != $this->options['public_key'] || $_POST['gglcptch_private_key'] != $this->options['private_key'] )
					$this->options['keys_verified'] = false;

				if ( $_POST['gglcptch_recaptcha_version'] != $this->options['recaptcha_version'] ) {
					$this->options['keys_verified'] = false;
					$this->options['need_keys_verified_check'] = true;
				}

				$this->options['whitelist_message']			= stripslashes( esc_html( $_POST['gglcptch_whitelist_message'] ) );
				$this->options['public_key']				= trim( stripslashes( esc_html( $_POST['gglcptch_public_key'] ) ) );
				$this->options['private_key']				= trim( stripslashes( esc_html( $_POST['gglcptch_private_key'] ) ) );

				$this->options['recaptcha_version']	=	in_array( $_POST['gglcptch_recaptcha_version'], array( 'v1', 'v2', 'invisible' ) ) ? $_POST['gglcptch_recaptcha_version']: 'v2';
				$this->options['theme']				=	stripslashes( esc_html( $_POST['gglcptch_theme'] ) );
				$this->options['theme_v2']			=	stripslashes( esc_html( $_POST['gglcptch_theme_v2'] ) );

				$this->options['size_v2']					= 'compact' == $_POST['gglcptch_size_v2'] ? 'compact' : 'normal';
				$this->options['language']					= isset( $_POST['gglcptch_language'] ) ? stripslashes( esc_html( $_POST['gglcptch_language'] ) ) : 'en';
				$this->options['use_multilanguage_locale']	= isset( $_POST['gglcptch_use_multilanguage_locale'] ) ? stripslashes( esc_html( $_POST['gglcptch_use_multilanguage_locale'] ) ) : 0;

				$this->options['disable_submit']			= isset( $_POST['gglcptch_disable_submit'] ) ? 1 : 0;

				foreach ( $this->forms as $form_slug => $form_data ) {
					$this->options[ $form_slug ] = isset( $_POST["gglcptch_{$form_slug}"] ) ? 1 : 0;
				}

				if ( function_exists( 'get_editable_roles' ) ) {
					foreach ( get_editable_roles() as $role => $fields ) {
						$this->options[ $role ] = isset( $_POST[ 'gglcptch_' . $role ] ) ? 1 : 0;
					}
				}

				/*
				 * Update plugin option in the database
				 */
				if ( $this->is_network_options ) {
					if ( 'all' == $_REQUEST['gglcptch_network_apply'] ) {
						$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
						$old_blog = $wpdb->blogid;
						foreach ( $blogids as $blog_id ) {
							switch_to_blog( $blog_id );
							if ( $old_options = get_option( 'gglcptch_options' ) ) {
								$blog_options = array_merge( $old_options, $this->options );
								update_option( 'gglcptch_options', $blog_options );
							} else {
								add_option( 'gglcptch_options', array_merge( $this->default_options, $this->options ) );
							}
						}
						switch_to_blog( $old_blog );
					}
					$this->options['network_apply']  = esc_html( $_REQUEST['gglcptch_network_apply'] );
					$this->options['network_view']   = isset( $_REQUEST['gglcptch_network_view'] ) ? 1 : 0;
					$this->options['network_change'] = isset( $_REQUEST['gglcptch_network_change'] ) ? 1 : 0;
					update_site_option( 'gglcptch_options', $this->options );
				} else {
					update_option( 'gglcptch_options', $this->options );
				}

				$message = __( "Settings saved.", 'google-captcha-pro' );
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Displays 'settings' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_settings() {
			global $gglcptch_languages, $wp_version; ?>
			<h3 class="bws_tab_label"><?php _e( 'Google Captcha Settings', 'google-captcha-pro' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php if ( $this->forbid_view ) { ?>
				<div class="error inline bws_visible"><p><strong><?php _e( "Notice:", 'google-captcha-pro' ); ?></strong> <strong><?php _e( "It is prohibited to view Google Captcha Pro settings on this site in the Google Captcha Pro network settings.", 'google-captcha-pro' ); ?></strong></p></div>
			<?php } else {
				if ( ! empty( $this->change_permission_attr ) ) { ?>
					<div class="error inline bws_visible"><p><strong><?php _e( "Notice:", 'google-captcha-pro' ); ?></strong> <strong><?php _e( "It is prohibited to change Google Captcha Pro settings on this site in the Google Captcha Pro network settings.", 'google-captcha-pro' ); ?></strong></p></div>
				<?php }
				if ( $this->is_network_options ) { ?>
					<table class="form-table gglcptch_network_settings">
						<tr valign="top">
							<th scope="row"><?php _e( 'Apply network settings', 'google-captcha-pro' ); ?></th>
							<td>
								<fieldset>
									<label><input<?php echo $this->change_permission_attr; ?>  type="radio" name="gglcptch_network_apply" value="all" <?php if ( "all" == $this->options['network_apply'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Apply to all sites and use by default', 'google-captcha-pro' ); ?> <span class="bws_info">(<?php _e( 'All current settings on separate sites will be replaced', 'google-captcha-pro' ); ?>)</span></label><br />
									<div class="bws_network_apply_all">
										<label><input<?php echo $this->change_permission_attr; ?>  type="checkbox" name="gglcptch_network_change" value="1" <?php if ( 1 == $this->options['network_change'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Allow changing the settings on separate websites', 'google-captcha-pro' ); ?></label><br />
										<label><input<?php echo $this->change_permission_attr; ?>  type="checkbox" name="gglcptch_network_view" value="1" <?php if ( 1 == $this->options['network_view'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Allow viewing the settings on separate websites', 'google-captcha-pro' ); ?></label><br />
									</div>
									<label><input<?php echo $this->change_permission_attr; ?>  type="radio" name="gglcptch_network_apply" value="default" <?php if ( "default" == $this->options['network_apply'] ) echo 'checked="checked"'; ?> /> <?php _e( 'By default', 'google-captcha-pro' ); ?> <span class="bws_info">(<?php _e( 'Settings will be applied to newly added websites by default', 'google-captcha-pro' ); ?>)</span></label><br />
									<label><input<?php echo $this->change_permission_attr; ?>  type="radio" name="gglcptch_network_apply" value="off" <?php if ( "off" == $this->options['network_apply'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Do not apply', 'google-captcha-pro' ); ?> <span class="bws_info">(<?php _e( 'Change the settings on separate sites of the multisite only', 'google-captcha-pro' ); ?>)</span></label>
								</fieldset>
							</td>
						</tr>
					</table>
				<?php } ?>
				<div class="bws_tab_sub_label gglcptch_settings_form"><?php _e( 'Authentication', 'google-captcha-pro' ); ?></div>
				<div class="bws_info gglcptch_settings_form"><?php _e( 'Register your website with Google to get required API keys and enter them below.', 'google-captcha-pro' ); ?> <a target="_blank" href="https://www.google.com/recaptcha/admin#list"><?php _e( 'Get the API Keys', 'google-captcha-pro' ); ?></a></div>
				<table class="form-table gglcptch_settings_form">
					<?php foreach ( $this->keys as $key => $fields ) { ?>
						<tr>
							<th><?php echo $fields['display_name']; ?></th>
							<td>
								<input<?php echo $this->change_permission_attr; ?> class="regular-text" type="text" name="<?php echo $fields['form_name']; ?>" value="<?php echo $this->options[ $key . '_key' ] ?>" maxlength="200" />
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
									<a<?php echo $this->change_permission_attr; ?> class="button button-secondary" href="<?php echo add_query_arg( array( '_wpnonce' => wp_create_nonce( 'gglcptch-test-keys' ), 'action' => 'gglcptch-test-keys', 'is_network' => $this->is_network_options ? '1' : '0' ), admin_url( 'admin-ajax.php' ) ); ?>"><?php _e( 'Test reCAPTCHA' , 'google-captcha-pro' ); ?></a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</table>
				<div class="bws_tab_sub_label gglcptch_settings_form"><?php _e( 'General', 'google-captcha-pro' ); ?></div>
				<table class="form-table gglcptch_settings_form">
					<tr valign="top">
						<th scope="row"><?php _e( 'Enable reCAPTCHA for', 'google-captcha-pro' ); ?></th>
						<td>
							<!--[if !IE]> -->
							<div class="gglcptch-settings-accordion">
							<!-- <![endif]-->
								<?php foreach ( $this->sections as $section_slug => $section ) {

									if ( empty( $section['name'] ) || empty( $section['forms'] ) || ! is_array( $section['forms'] ) )
										continue;

									$section_notice = ! empty( $section['section_notice'] ) ? $section['section_notice'] : ''; ?>
									<p class="gglcptch_section_header">
										<i><?php echo $section['name']; ?></i>
										<?php if ( ! empty( $section_notice ) ) { ?>
											&nbsp;<span class="bws_info"><?php echo $section_notice; ?></span>
										<?php } ?>
									</p>
									<fieldset class="gglcptch_section_forms">
										<?php foreach ( $section['forms'] as $form_slug ) {
											$form_notice = $this->forms[ $form_slug ]['form_notice'];
											$form_atts = '';
											if ( '' == $this->change_permission_attr && ( '' != $form_notice || '' != $section_notice ) ) {
												$form_atts .= disabled( 1, 1, false );
											}
											$form_atts .= checked( ! empty( $this->options[ $form_slug ] ), true, false ); ?>
											<label>
												<input type="checkbox"<?php echo $this->change_permission_attr; echo $form_atts; ?> name="gglcptch_<?php echo $form_slug; ?>" value="1" /> <?php echo $this->forms[ $form_slug ]['form_name']; ?>
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
					<tr valign="top">
						<th scope="row"><?php _e( 'Hide reCAPTCHA for', 'google-captcha-pro' ); ?></th>
						<td>
							<fieldset>
								<?php if ( function_exists( 'get_editable_roles' ) ) {
									foreach ( get_editable_roles() as $role => $fields ) {
										printf(
											'<label><input %1$s type="checkbox" name="%2$s" value="%3$s" %4$s> %5$s</label><br/>',
											$this->change_permission_attr,
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
						<th scope="row"><?php _e( 'reCAPTCHA Version', 'google-captcha-pro' ); ?></th>
						<td>
							<fieldset>
								<?php foreach ( $this->versions as $version => $version_name ) { ?>
									<label>
										<input<?php echo $this->change_permission_attr; ?> type="radio" name="gglcptch_recaptcha_version" value="<?php echo $version; ?>" <?php checked( $version, $this->options['recaptcha_version'] ); ?>> <?php echo $version_name; ?>
									</label>
									<br/>
								<?php } ?>
							</fieldset>
						</td>
					</tr>
					<tr class="gglcptch_theme_v1" valign="top">
						<th scope="row">
							<?php _e( 'Theme', 'google-captcha-pro' ); ?>
						</th>
						<td>
							<select<?php echo $this->change_permission_attr; ?> name="gglcptch_theme">
								<?php foreach ( $this->themes as $theme ) { ?>
									<option value="<?php echo $theme[0]; ?>" <?php selected( $theme[0], $this->options['theme'] ); ?>><?php echo $theme[1]; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class="gglcptch_theme_v2" valign="top">
						<th scope="row">
							<?php _e( 'Theme', 'google-captcha-pro' ); ?>
						</th>
						<td>
							<select<?php echo $this->change_permission_attr; ?> name="gglcptch_theme_v2">
								<option value="light" <?php selected( 'light', $this->options['theme_v2'] ); ?>>Light</option>
								<option value="dark" <?php selected( 'dark', $this->options['theme_v2'] ); ?>>Dark</option>
							</select>
						</td>
					</tr>
					<tr class="gglcptch_theme_v2" valign="top">
						<th scope="row">
							<?php _e( 'Size', 'google-captcha-pro' ); ?>
						</th>
						<td>
							<fieldset>
								<label><input<?php echo $this->change_permission_attr; ?> name="gglcptch_size_v2" type="radio" value="normal" <?php checked( 'normal', $this->options['size_v2'] ); ?>><?php _e( 'Normal', 'google-captcha-pro' ); ?></label>
								<br />
								<label><input<?php echo $this->change_permission_attr; ?> name="gglcptch_size_v2" type="radio" value="compact" <?php checked( 'compact', $this->options['size_v2'] ); ?>><?php _e( 'Compact', 'google-captcha-pro' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Language', 'google-captcha-pro' ); ?></th>
						<td>
							<select<?php echo $this->change_permission_attr; ?> id="gglcptch_language" name="gglcptch_language">
								<?php foreach ( $gglcptch_languages as $code => $name ) { ?>
									<option value="<?php echo $code; ?>" <?php selected( $this->options["language"], $code ); ?>><?php echo $name; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Multilanguage</th>
						<td>
							<?php $plugin_info = gglcptch_plugin_status( array( 'multilanguage/multilanguage.php', 'multilanguage-pro/multilanguage-pro.php' ), $this->all_plugins, $this->is_network_options );
							$attrs = $plugin_notice = '';
							if ( 'deactivated' == $plugin_info['status'] ) {
								$attrs = 'disabled="disabled"';
								$plugin_notice = ' <a href="' . self_admin_url( 'plugins.php' ) . '">' . __( 'Activate', 'google-captcha-pro' ) . '</a>';
							} elseif ( 'not_installed' == $plugin_info['status'] ) {
								$attrs = 'disabled="disabled"';
								$plugin_notice = ' <a href="https://bestwebsoft.com/products/wordpress/plugins/multilanguage/?k=390f8e0d92066f2b73a14429d02dcee7&pn=109&v=' . $this->plugins_info["Version"] . '&wp_v=' . $wp_version . '" target="_blank">' . __( 'Install Now', 'google-captcha-pro' ) . '</a>';
							} ?>
							<label>
								<input<?php echo $this->change_permission_attr; ?> id="gglcptch_use_multilanguage_locale" type="checkbox" <?php echo $attrs; ?> <?php checked( $this->options["use_multilanguage_locale"], 1 ); ?> name="gglcptch_use_multilanguage_locale" value="1" /> <span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'google-captcha-pro' ); echo $plugin_notice; ?></span>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Whitelist Notification', 'google-captcha-pro' ); ?></th>
						<td>
							<textarea<?php echo $this->change_permission_attr; ?> name="gglcptch_whitelist_message"><?php echo $this->options['whitelist_message']; ?></textarea>
							<div class="bws_info"><?php _e( 'This message will be displayed instead of the reCAPTCHA.', 'google-captcha-pro' ); ?></div>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Disabled Submit Button', 'google-captcha-pro' ); ?></th>
						<td>
							<input<?php echo $this->change_permission_attr; ?> id="gglcptch_disable_submit" type="checkbox" <?php checked( ! empty( $this->options["disable_submit"] ) ); ?> name="gglcptch_disable_submit" value="1" />&nbsp;
							<span class="bws_info">
								<?php _e( 'Enable to keep submit button disabled until reCAPTCHA is loaded (do not use this option if you see "Failed to load Google reCAPTCHA" message).', 'google-captcha-pro' ); ?>
							</span>
						</td>
					</tr>
				</table>
			<?php }
		}

		/**
		 * Display custom error\message\notice
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function display_custom_messages( $save_results ) {
			if ( 'v1' == $this->options['recaptcha_version'] ) { ?>
				<div class="updated inline bws-notice"><p><strong><?php _e( "Only one reCAPTCHA can be displayed on the page, it's related to reCAPTCHA version 1 features.", 'google-captcha-pro' ); ?></strong></p></div>
			<?php }
			if ( ! empty( $this->options['need_keys_verified_check'] ) ) { ?>
				<div class="updated inline bws-notice"><p><strong><?php _e( 'reCAPTCHA version was changed. Please submit "Test reCAPTCHA" and regenerate Site and Secret keys if necessary.', 'google-captcha-pro' ); ?></strong></p></div>
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
					<?php _e( 'Google Captcha Shortcode', 'google-captcha-pro' ); ?>
				</h3>
				<div class="inside">
					<?php _e( "Add Google Captcha to your posts or pages using the following shortcode:", 'google-captcha-pro' ); ?>
					<?php bws_shortcode_output( '[bws_google_captcha]' ); ?>
				</div>
			</div>
		<?php }
	}
}