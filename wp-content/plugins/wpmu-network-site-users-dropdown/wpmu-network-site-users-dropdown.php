<?php
/**
 * Plugin Name: WPMU Network Site Users Dropdown
 * Plugin URI:  http://www.obenlands.de/en/portfolio/wpmu-network-site-users-dropdown/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wpmu-network-site-users-dropdown
 * Description: Replaces the input field for adding existing users to a site with a more comfortable dropdown menu.
 * Version:     2
 * Author:      Konstantin Obenland
 * Author URI:  http://www.obenlands.de/en/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wpmu-network-site-users-dropdown
 * Text Domain: wpmu-network-site-users-dropdown
 * Domain Path: /lang
 * License:     GPLv2
 *
 * @package WPMU Network Site Users Dropdown
 */

if ( ! class_exists( 'Obenland_Wp_Plugins_V4' ) ) {
	require_once 'obenland-wp-plugins.php';
}

register_activation_hook( __FILE__, array(
	'Obenland_WPMU_Network_Site_Users_Dropdown',
	'activation',
) );

/**
 * Class Obenland_WPMU_Network_Site_Users_Dropdown
 */
class Obenland_WPMU_Network_Site_Users_Dropdown extends Obenland_Wp_Plugins_V4 {

	/**
	 * Constructor.
	 *
	 * Adds all necessary filters.
	 *
	 * @author Konstantin Obenland
	 * @since  1.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct( array(
			'textdomain'     => 'wpmu-network-site-users-dropdown',
			'plugin_path'    => __FILE__,
			'donate_link_id' => 'HEXL3UM8D7R6N',
		) );

		$this->hook( 'network_site_users_after_list_table' );
		add_action( 'show_network_site_users_add_existing_form', '__return_false' );
	}


	/**
	 * Checks whether we are on a multisite install and bails if not. The
	 * plugin will stay deactivated.
	 *
	 * @author Konstantin Obenland
	 * @since  1.1 - 03.04.2011
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function activation() {
		load_plugin_textdomain( 'wpmu-network-site-users-dropdown', false, 'wpmu-network-site-users-dropdown/lang' );

		if ( ! is_multisite() ) {
			wp_die( esc_html__( 'This plugin requires multisite to be enabled!', 'wpmu-network-site-users-dropdown' ), '', array(
				'back_link' => true,
			) );
		}
	}


	/**
	 * Displays the dropdown form
	 *
	 * @author Konstantin Obenland
	 * @since  1.0
	 * @access public
	 *
	 * @global $editblog_roles
	 * @global $id
	 * @global $default_role
	 *
	 * @return void
	 */
	public function network_site_users_after_list_table() {
		global $editblog_roles, $id, $default_role, $wpdb;

		if ( empty( $editblog_roles ) ) {
			$editblog_roles = get_blog_option( $id, $wpdb->get_blog_prefix( $id ) . 'user_roles' );
		}

		// Exclude users, who are already associated with the current site.
		$exclude = get_users( array(
			'blog_id' => $id,
			'fields'  => '',
		) );

		// Get all other users.
		$users = get_users( array(
			'blog_id' => '',
			'orderby' => 'user_nicename',
			'exclude' => $exclude,
			'fields'  => array(
				'user_login',
				'display_name',
			),
		) );

		if ( current_user_can( 'promote_users' ) && ! empty( $users ) ) :
		?>
		<h3 id="add-existing-user"><?php esc_html_e( 'Add Existing User' ); ?></h3>
		<form action="site-users.php?action=adduser" id="adduser" method="post">
			<?php wp_nonce_field( 'edit-site' ); ?>
			<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Username' ); ?></th>
					<td>
						<select name="newuser" id="newuser">
							<?php
							foreach ( $users as $user ) :
								echo '<option value="' . esc_attr( $user->user_login ) . '">' . esc_html( $user->display_name ) . '</option>';
							endforeach;
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Role' ); ?></th>
					<td>
						<select name="new_role" id="new_role_0">
							<?php
							reset( $editblog_roles );
							foreach ( $editblog_roles as $role => $role_assoc ) :
								echo '<option' . selected( $role, $default_role ) . ' value="' . esc_attr( $role ) . '">' . esc_html( translate_user_role( $role_assoc['name'] ) ) . '</option>';
							endforeach;
							?>
						</select>
					</td>
				</tr>
			</table>
			<?php wp_nonce_field( 'add-user', '_wpnonce_add-user' ); ?>
			<?php submit_button( esc_attr__( 'Add User' ), 'primary', 'add-user' ); ?>
		</form>
		<?php
		endif;
	}

} // End Class Obenland_WPMU_Network_Site_Users_Dropdown.


/**
 * Instantiates the class based on certain conditions
 *
 * @author Konstantin Obenland
 * @since  1.3 - 03.05.2011
 * @global $pagenow
 *
 * @return void
 */
function wpmunsud_instantiate() {
	global $pagenow;

	if ( get_user_count() > 100 ) {
		return;
	}

	$plugins = get_site_option( 'active_sitewide_plugins' );

	if ( is_network_admin() || ( ! isset( $plugins[ plugin_basename( __FILE__ ) ] ) && 'plugins.php' === $pagenow ) ) {
		new Obenland_WPMU_Network_Site_Users_Dropdown();
	}
}
add_action( 'plugins_loaded', 'wpmunsud_instantiate' );
