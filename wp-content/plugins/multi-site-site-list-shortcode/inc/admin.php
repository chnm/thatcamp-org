<?php

if ( !class_exists( 'mssls_admin' ) ) {

	class mssls_admin extends bit51_mssls {

		/**
		 * Initialize admin function
		 */
		function __construct() {
			global $blog_id;

			if ( $blog_id == 1 ) {

				//add scripts and css
				add_action( 'admin_print_scripts', array( $this, 'config_page_scripts' ) );
				add_action( 'admin_print_styles', array( $this, 'config_page_styles' ) );

				//add menu items
				add_action( 'admin_menu', array( $this, 'register_settings_page' ) );

				//add settings
				add_action( 'admin_init', array( $this, 'register_settings' ) );

				//add action link
				add_filter( 'plugin_action_links', array( $this, 'add_action_link' ), 10, 2 );

				//add donation reminder
				add_action( 'admin_init', array( $this, 'ask' ) );

			}
		}

		/**
		 * Register page settings
		 */
		function register_settings_page() {
			add_options_page( __( $this->pluginname, 'multi_site_site_list_shortcode' ), __( $this->pluginname, 'multi_site_site_list_shortcode' ), $this->accesslvl, 'multi_site_site_list_shortcode', array( $this, 'mssls_admin_init' ) );
		}

		/**
		 * Register admin page main content
		 * To add more boxes to the admin page add a 2nd inner array item with title and callback function or content
		 */
		function mssls_admin_init() {
			$this->admin_page( $this->pluginname . ' ' . __( 'Options', 'multi_site_site_list_shortcode' ),
				array(
					array( __( 'Instructions', 'multi_site_site_list_shortcode' ), 'install_instructions' ), //primary admin page content
					array( __( 'General Options', 'multi_site_site_list_shortcode' ), 'general_options' ), //primary admin page content
					array( __( 'Exclude Sites', 'multi_site_site_list_shortcode' ), 'exclude_options' ) //choose sites to be excluded from view
				)
			);
		}

		/**
		 * Create instructions block
		 */
		function install_instructions() {
			?>
			<p><?php echo __( 'Set your options below and then enter the shortcode', 'multi_site_site_list_shortcode' ) . ' <strong><em>[site-list]</em></strong> ' . __( 'where you would like your site list to appear (you can even enter it in a text widget).', 'multi_site_site_list_shortcode' ); ?></p>
			<p><?php echo __( 'You can overwrite the settings below to make it easier to use the shortcode in multiple locations. Here are the options:', 'multi_site_site_list_shortcode' ); ?></p>
			<ul>
				<li><?php echo __( 'sort: alpha for alphabetically (anything else will sort by created date).', 'multi_site_site_list_shortcode' ); ?></li>
				<li><?php echo __( 'limit: set a new limit (or 0 for no limit).', 'multi_site_site_list_shortcode' ); ?></li>
				<li><?php echo __( 'newwin: 1 to open links in a new window, 0 to open them in a current window.', 'multi_site_site_list_shortcode' ); ?></li>
				<li><?php echo __( 'showtag: 1 to show site description (tagline), 0 to hide it.', 'multi_site_site_list_shortcode' ); ?></li>
			</ul>
			<p><?php echo __( 'example: to sort alphabetically and show site descriptions you can overwrite the default options below using the following tag', 'multi_site_site_list_shortcode' ); ?>
				<br/><em><strong>[site-list sort=alpha showtag=1]</strong></em></p>
		<?php
		}

		/**
		 * Create admin page main content
		 */
		function general_options() {
			?>
			<form method="post" action="options.php">
				<?php settings_fields( 'bit51_mssls_options' ); //use main settings group ?>
				<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "sortby"><?php _e( 'Sort By', 'multi_site_site_list_shortcode' ); ?></label>
						</th>
						<td>
							<input name="bit51_mssls[sortby]" id="sortby" value="0"
							       type="radio" <?php checked( '0', $options['sortby'] ); ?> /> <?php _e( 'Alphabetically', 'multi_site_site_list_shortcode' ); ?>
							<br/>
							<input name="bit51_mssls[sortby]" id="sortby" value="1"
							       type="radio" <?php checked( '1', $options['sortby'] ); ?> /> <?php _e( 'Site Creation Date', 'multi_site_site_list_shortcode' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "showtag"><?php _e( 'Show Taglines', 'multi_site_site_list_shortcode' ); ?></label>
						</th>
						<td>
							<input name="bit51_mssls[showtag]" id="sortby" value="0"
							       type="radio" <?php checked( '0', $options['showtag'] ); ?> /> <?php _e( 'No', 'multi_site_site_list_shortcode' ); ?>
							<br/>
							<input name="bit51_mssls[showtag]" id="sortby" value="1"
							       type="radio" <?php checked( '1', $options['showtag'] ); ?> /> <?php _e( 'Yes', 'multi_site_site_list_shortcode' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "openin"><?php _e( 'Link Target', 'multi_site_site_list_shortcode' ); ?></label>
						</th>
						<td>
							<input name="bit51_mssls[openin]" id="openin" value="0"
							       type="radio" <?php checked( '0', $options['openin'] ); ?> /> <?php _e( 'Same Window', 'multi_site_site_list_shortcode' ); ?>
							<br/>
							<input name="bit51_mssls[openin]" id="openin" value="1"
							       type="radio" <?php checked( '1', $options['openin'] ); ?> /> <?php _e( 'New Window', 'multi_site_site_list_shortcode' ); ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "limit"><?php _e( 'Limit Output', 'multi_site_site_list_shortcode' ); ?></label>
						</th>
						<td>
							<input name="bit51_mssls[limit]" id="limit" value="<?php echo $options['limit']; ?>"
							       type="text"/> <br/>

							<p><?php _e( 'Limit the number of sites displayed. This is useful if using the shortcode in a text widget. Set 0 for no limit.', 'multi_site_site_list_shortcode' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "openin"><?php _e( 'Exclude Sites', 'multi_site_site_list_shortcode' ); ?></label>
						</th>
						<td>
							<?php
							global $wpdb;
							global $table_prefix;
							$options = get_option( $this->primarysettings );

							//get list of all blogs not marked mature, archived, or spam
							$blogs = $wpdb->get_col( "SELECT blog_id FROM `" . $wpdb->blogs . "` WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ORDER BY blog_id DESC" );

							//make sure there are still blogs left
							if ( $blogs ) {

								foreach ( $blogs as $blog ) {
									if ( $blog == '1' ) { //set correct table for primary blog
										$table = $wpdb->base_prefix . 'options';
									} else {
										$table = $wpdb->base_prefix . $blog . '_options';
									}

									if ( is_array( $options['excluded'] ) ) { //get array of excluded blog id's
										$excluded = $options['excluded'];
									} else {
										$excluded = unserialize( $options['excluded'] );
									}

									if ( is_array( $excluded ) && in_array( $blog, $excluded ) ) { //to check or not to check
										$checked = "checked";
									} else {
										$checked = "";
									}

									$sitedetails = $wpdb->get_results( 'SELECT option_value FROM `' . $table . '` WHERE option_name IN (\'siteurl\',\'blogname\') ORDER BY option_name DESC' ); //get site details

									if ( $sitedetails ) { //as long as blog exists display it with a checkbox
										echo '<input type="checkbox" ' . $checked . ' name="bit51_mssls[excluded' . $blog . ']" value="' . $blog . '" id="' . $blog . '" /> <a href="' . $sitedetails[0]->option_value . '">' . $sitedetails[1]->option_value . '</a><br />';
									}
								}
							}
							?>
							<p><?php _e( 'Put a checkmark below next to the sites you would like to remove from your site list.', 'multi_site_site_list_shortcode' ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>"/>
				</p>
			</form>
		<?php
		}

		/**
		 * Validate input
		 */
		function mssls_val_options( $input ) {
			$exclude = array(); //create exclude array

			//if we're dealing with one of the exclude fields add the value to the exclude array
			foreach ( $input as $field => $value ) {
				if ( $field != 'sortby' && $field != 'openin' && $field != 'showtag' && $field != 'limit' ) {
					unset( $input[$field] );
					$exclude[] = $value;
				}
			}

			$input['excluded'] = serialize( $exclude ); //convert array to string and add to $input

			//process non-exclude fields
			$input['sortby'] = ( $input['sortby'] == 1 ? 1 : 0 );
			$input['openin'] = ( $input['openin'] == 1 ? 1 : 0 );
			$input['showtag'] = ( $input['showtag'] == 1 ? 1 : 0 );
			$input['limit'] = intval( $input['limit'] );

			return $input;
		}
	}
}
