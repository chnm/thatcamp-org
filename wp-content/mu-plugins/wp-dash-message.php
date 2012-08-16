<?php
/*
Plugin Name: WP Dash Message
Description: A basic dashboard welcome message
Version: 1.0.1
Author: Aleksandar Arsovski
License: GPL2
*/

/*  Copyright 2011  Aleksandar Arsovski  (email : alek_ars@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Hook for adding site level options menu in the settings menu bar
add_action( 'admin_menu', 'wpdwm_options_menu' );

//Hook for setting up a section with entry field in network-wide settings tab (for creating network-wide welcome messages)
add_action( 'wpmu_options', 'wpdwm_network_settings' );

//Hook for updating the network-wide welcome massage data
add_action( 'update_wpmu_options', 'wpdwm_save_network_settings') ;

//Hook calls function for registering/adding settings when admin area is accessed
add_action( 'admin_init', 'wpdwm_admin_init' );


//Internationalization setup
$wpdwm_plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'wp-dash-message', false, $wpdwm_plugin_dir );


/** Function for registering/addingß setings 
 * wpdwm_admin_init function.
 * 
 * @access public
 * @return void
 */
function wpdwm_admin_init() {
	
	// Hook used to register other dashboard message functions
	add_action( 'wp_dashboard_setup', 'wpdwm_add_dash_welcome' );
	
	register_setting( 'wp_dash_message', 'wp_dash_message', 'wp_dash_message_validate' );
	
	// Network-level settings section
	add_settings_section( 'wpdwm_dash_settings_page_main', '', 'wpdwm_main_section_text', 'wpdwm_dash_settings_page' );
	
	// Site-level settings field
	add_settings_field( 'wpdwm_welcome_text', __( 'Dashboard Message', 'wp-dash-message' ), 'wpdwm_site_level_entry_field', 'wpdwm_dash_settings_page', 'wpdwm_dash_settings_page_main' );

}

// Create the Dash Welcome "widget" and place it at the top of the dashboard
/**
 * wpdwm_add_dash_welcome function.
 * 
 * @access public
 * @return void
 */
function wpdwm_add_dash_welcome() {	
	
	// Get user's data in order to display username in header
	global $user_identity;
	
	// Change second parameter to change the header of the widget
	wp_add_dashboard_widget( 'dashboard_welcome_widget', __('Welcome', 'wp-dash-message' ) . ', ' . $user_identity, 'wpdwm_dashboard_welcome_widget_function' );	

	// Globalize the metaboxes array, this holds all the widgets for wp-admin
	global $wp_meta_boxes;
	
	// Get the regular dashboard widgets array 
	// (which has our new widget already but at the end)
	$wpdwm_normal_dashboard = $wp_meta_boxes[ 'dashboard' ][ 'normal' ][ 'core' ];

	// Backup and delete our new dashbaord widget from the end of the array
	$wpdwm_dashboard_widget_backup = array( 'dashboard_welcome_widget' => $wpdwm_normal_dashboard[ 'dashboard_welcome_widget' ] );
	unset ( $wpdwm_normal_dashboard[ 'dashboard_welcome_widget' ] );

	// Merge the two arrays together so our widget is at the beginning
	$wpdwm_sorted_dashboard = array_merge( $wpdwm_dashboard_widget_backup, $wpdwm_normal_dashboard );

	// Save the sorted array back into the original metaboxes
	$wp_meta_boxes[ 'dashboard' ][ 'normal' ][ 'core' ] = $wpdwm_sorted_dashboard;
}

// Create the function to output the contents of our Dashboard Widget
/**
 * wpdwm_dashboard_welcome_widget_function function.
 * 
 * @access public
 * @return void
 */
function wpdwm_dashboard_welcome_widget_function() {
	
	// Display the site level widget entry first...
	$options = get_option( 'wp_dash_message' );
	echo apply_filters( 'the_content', $options [ 'message' ] );
	
	// Display the network level widget entry second...
	$network_message = get_site_option ( 'wp_dash_message_network', '', true );
	if ( $network_message != '' ) {
		echo apply_filters( 'the_content', trim( $network_message ) );
	}?>
	
	<!--CSS for Widget-->
	<style>
		#dashboard_welcome_widget {
			background: lightYellow;
			color: #555;
			border: 2px solid #E6DB55;
		}
		#dashboard_welcome_widget .hndle {
			background-image: -webkit-linear-gradient( top, #FDFBAF, #FAF66C );
			background-image: -moz-linear-gradient( top,  #FDFBAF,  #FAF66C);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#FDFBAF', endColorstr='#FAF66C' );
		}
		#dashboard_welcome_widget h3 {
			text-shadow: white 0 1px 0;
			box-shadow: 0 1px 0 yellow;
		}
	</style>
	<?php
}




/**
 * wpdwm_options_menu function.
 * 
 * @access public
 * @return void
 */
function wpdwm_options_menu() {
	// Parameters for options: 1. site header name 2. setting menu bar name
	// 3. capability (decides whether user has access) 4. menu slug 5. options page function
	add_options_page( __( 'Dashboard Welcome Message Options', 'wp-dash-message' ), __( 'Dashboard Message', 'wp-dash-message' ), 'manage_options', 'wpdwm_options', 'wpdwm_dash_settings_page' );
}

/** Site level options page set-up
 * wpdwm_dash_settings_page function.
 * settings page
 * @access public
 * @return void
 */
function wpdwm_dash_settings_page() {
	
	// Determines if user has permission to access options and if they don't error message is displayed
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have the permission to modify the custom dashboard message box.', 'wp-dash-message' ) );
	}?>
	
	<div class="wrap">
		<h2><?php _e( 'Dashboard Welcome Message', 'wp-dash-message' ) ?></h2>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wp_dash_message' );
			do_settings_sections( 'wpdwm_dash_settings_page' );
			?>
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</form>
	</div>
	<?php
}


// empty function 
function wpdwm_main_section_text() { }

/** Sets up the site level entry field
 * wpdwm_site_level_entry_field function.
 * 
 * @access public
 * @return void
 */
function wpdwm_site_level_entry_field() {
	$options = get_option( 'wp_dash_message' );
	?>
	<!-- Creates the site level entry field and populates it with whatever is currently displayed on the widget -->
	<textarea id="wpdwm_welcome_text" name="wp_dash_message[message]" rows="15" cols="70"  ><?php echo $options[ 'message' ]; ?></textarea>
	<br />
	<span><?php _e( 'HTML allowed', 'wp-dash-message' ) ?></span>
	<?php
}

/** Validation/clean-up of message. "trim" removes all spaces before and after text body. Returns the validated entry
 * wp_dash_message_validate function.
 * 
 * @access public
 * @param mixed $input
 * @return void
 */
function wp_dash_message_validate($input) {
	$newinput[ 'message' ] =  trim( $input[ 'message' ] );
	return $newinput;
}

/** Sets up network level entry field in settings tab and populates field with current network-wide dashboard widget message
 * wpdwm_network_settings function.
 * 
 * @access public
 * @return void
 */
function wpdwm_network_settings() {
	$network_message = get_site_option( 'wp_dash_message_network', '', true );
	?>
	<h3><?php _e( 'Dashboard Message', 'wp-dash-message' ) ?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e( 'Network-Level Dashboard Message', 'wp-dash-message' ) ?></th>
				<td>
	                <textarea class="large-text" cols="45" rows="5" id="wp_dash_message_network" 
	                name="wp_dash_message_network"><?php echo $network_message; ?></textarea>
	                <span><?php _e( 'HTML allowed', 'wp-dash-message' ) ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<?php 
}

/** Updates the site options with the network-wide dash message entry
 * wpdwm_save_network_settings function.
 * 
 * @access public
 * @return void
 */
function wpdwm_save_network_settings() {
	update_site_option( 'wp_dash_message_network', $_POST[ 'wp_dash_message_network' ] );
}