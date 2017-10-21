<?php
/**
 * Display banners on settings page
 * @package Google Captcha(reCAPTCHA) by BestWebSoft
 * @since 1.27
 */

/**
 * Show ads for PRO
 * @param		string     $func        function to call
 * @return		void
 */
if ( ! function_exists( 'gglcptch_pro_block' ) ) {
	function gglcptch_pro_block( $func, $show_cross = true, $display_always = false ) {
		global $gglcptch_plugin_info, $wp_version, $gglcptch_options;
		if ( $display_always || ! bws_hide_premium_options_check( $gglcptch_options ) ) { ?>
			<div class="bws_pro_version_bloc gglcptch_pro_block <?php echo $func;?>" title="<?php _e( 'This options is available in Pro version of plugin', 'google-captcha' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-captcha' ); ?>"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<div class="bws_pro_version">
						<?php call_user_func( $func ); ?>
					</div>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=b850d949ccc1239cab0da315c3c822ab&pn=109&v=<?php echo $gglcptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google Captcha Pro (reCAPTCHA)">
						<?php _e( 'Upgrade to Pro', 'google-captcha' ); ?>
					</a>
					<div class="clear"></div>
				</div>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'gglcptch_whitelist_banner' ) ) {
	function gglcptch_whitelist_banner() { ?>
		<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats", 'google-captcha' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></div>
		<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for IPs: a comma", 'google-captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return.', 'google-captcha' ); ?></div>
		<?php _e( 'Reason', 'google-captcha' ); ?><br>
		<textarea disabled></textarea>
		<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for reasons: a comma", 'google-captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return.', 'google-captcha' ); ?></div>
	<?php }
}

if ( ! function_exists( 'gglcptch_supported_plugins_banner' ) ) {
	function gglcptch_supported_plugins_banner() {
		$pro_forms = array(
			'cf7'						=> array( 'form_name' => 'Contact Form 7' ),
			'si_contact_form'			=> array( 'form_name' => 'Fast Secure Contact Form' ),
			'jetpack_contact_form'		=> array( 'form_name' => __( 'Jetpack Contact Form', 'google-captcha' ) ),
			'sbscrbr'					=> array( 'form_name' => 'Subscriber' ),
			'bbpress_new_topic_form'	=> array( 'form_name' => __( 'bbPress New Topic form', 'google-captcha' ) ),
			'bbpress_reply_form'		=> array( 'form_name' => __( 'bbPress Reply form', 'google-captcha' ) ),
			'buddypress_register'		=> array( 'form_name' => __( 'BuddyPress Registration form', 'google-captcha' ) ),
			'buddypress_comments'		=> array( 'form_name' => __( 'BuddyPress Comments form', 'google-captcha' ) ),
			'buddypress_group'			=> array( 'form_name' => __( 'BuddyPress Add New Group form', 'google-captcha' ) ),
			'woocommerce_login'			=> array( 'form_name' => __( 'WooCommerce Login form', 'google-captcha' ) ),
			'woocommerce_register'		=> array( 'form_name' => __( 'WooCommerce Registration form', 'google-captcha' ) ),
			'woocommerce_lost_password'	=> array( 'form_name' => __( 'WooCommerce Reset password form', 'google-captcha' ) ),
			'woocommerce_checkout'		=> array( 'form_name' => __( 'WooCommerce Checkout form', 'google-captcha' ) ),
			'wpforo_login_form'			=> array( 'form_name' => __( 'wpForo Login form', 'google-captcha' ) ),
			'wpforo_register_form'		=> array( 'form_name' => __( 'wpForo Registration form', 'google-captcha' ) ),
			'wpforo_new_topic_form'		=> array( 'form_name' => __( 'wpForo New Topic form', 'google-captcha' ) ),
			'wpforo_reply_form'			=> array( 'form_name' => __( 'wpForo Reply form', 'google-captcha') )
		);
		$pro_sections = array(
			'external' => array(
				'name' => __( 'External Plugins', 'google-captcha' ),
				'forms' => array(
					'cf7',
					'si_contact_form',
					'jetpack_contact_form',
					'sbscrbr'
				)
			),
			'bbpress' => array(
				'name' => 'bbPress',
				'forms' => array(
					'bbpress_new_topic_form',
					'bbpress_reply_form'
				)
			),
			'buddypress' => array(
				'name' => 'BuddyPress',
				'forms' => array(
					'buddypress_register',
					'buddypress_comments',
					'buddypress_group'
				)
			),
			'woocommerce' => array(
				'name' => 'WooCommerce',
				'forms' => array(
					'woocommerce_login',
					'woocommerce_register',
					'woocommerce_lost_password',
					'woocommerce_checkout'
				)
			),
			'wpforo' => array(
				'name' => 'Forums - wpForo',
				'forms' => array(
					'wpforo_login_form',
					'wpforo_register_form',
					'wpforo_new_topic_form',
					'wpforo_reply_form'
				)
			)
		); ?>
		<table class="form-table bws_pro_version" style="margin-right: 10px; width: calc( 100% - 10px );">
			<tbody style="display: table-row-group;">
				<tr valign="top">
					<th scope="row"></th>
					<td style="padding-top: 30px;">
						<?php foreach ( $pro_sections as $section_slug => $section ) {

							if ( empty( $section['name'] ) || empty( $section['forms'] ) || ! is_array( $section['forms'] ) ) {
								continue;
							} ?>
							<!--[if !IE]> -->
							<div class="gglcptch-settings-accordion">
							<!-- <![endif]-->
								<p class="gglcptch_section_header">
									<i><?php echo $section['name']; ?></i><br />
								</p>
								<fieldset class="gglcptch_section_forms">
									<?php foreach ( $section['forms'] as $form_slug ) { ?>
										<label>
											<input type="checkbox"<?php disabled( true ); ?> /> <?php echo $pro_forms[ $form_slug ]['form_name']; ?>
										</label>
										<br />
									<?php } ?>
									<hr />
								</fieldset>
							<!--[if !IE]> -->
							</div> <!-- .gglcptch-settings-accordion -->
							<!-- <![endif]-->
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php }
}

if ( ! function_exists( 'gglcptch_additional_settings_banner' ) ) {
	function gglcptch_additional_settings_banner() { ?>
		<table class="form-table bws_pro_version">
			<tr class="gglcptch_theme_v2" valign="top">
				<th scope="row">
					<?php _e( 'Size', 'google-captcha' ); ?>
				</th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" checked><?php _e( 'Normal', 'google-captcha' ); ?></label><br />
						<label><input disabled="disabled" type="radio"><?php _e( 'Compact', 'google-captcha' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Language', 'google-captcha' ); ?></th>
				<td>
					<select disabled="disabled">
						<option selected="selected">English</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Multilanguage', 'google-captcha' ); ?></th>
				<td>
					<input disabled="disabled" type="checkbox" />
					<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'google-captcha' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}
