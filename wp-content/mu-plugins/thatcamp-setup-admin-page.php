<?php

function thatcamp_edit_profile_url( $url ) {
	$path = 'profile.php';
	$scheme = 'admin';
	$active = get_active_blog_for_user( get_current_user_id() );
	if ( $active )
		$url = get_admin_url( $active->blog_id, $path, $scheme );
	else
		$url = user_admin_url( $path, $scheme );

	return $url;
}
add_filter( 'edit_profile_url', 'thatcamp_edit_profile_url' );

/**
 * Creates the 'THATCamp Setup' admin page
 */
function thatcamp_add_menu_page() {
	$hook = add_menu_page(
		'THATCamp Setup',
		'THATCamp Setup',
		'manage_options',
		'thatcamp_setup',
		'thatcamp_menu_page'
	);

	add_action( $hook, 'thatcamp_admin_scripts' );
}
add_action( 'admin_menu', 'thatcamp_add_menu_page', 999 );

/**
 * Renders the admin page
 */
function thatcamp_menu_page() {
	$group_id = thatcamp_get_blog_group( get_current_blog_id() );

	$date = thatcamp_get_camp_date( $group_id, 'mmddyy' );
	$has_workshops = thatcamp_get_camp_has_workshops( $group_id );

	?>
	<form method="post">

	<div class="wrap">
		<h2>THATCamp Setup</h2>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="thatcamp_date">When will your THATCamp be held?</label>
				</th>

				<td>
					<input id="thatcamp_date" name="thatcamp_date" type="text" value="<?php echo esc_attr( $date ) ?>" />
					<p class="description">Use the <code>MM/DD/YYYY</code> format</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="thatcamp_has_workshops">Will your THATCamp have workshops?</label>
				</th>

				<td>
					<select id="thatcamp_has_workshops" name="thatcamp_has_workshops" type="text">
						<option value="yes" <?php selected( $has_workshops, 'yes' ) ?>>Yes</option>
						<option value="maybe" <?php selected( $has_workshops, 'maybe' ) ?>>Maybe</option>
						<option value="no" <?php selected( $has_workshops, 'no' ) ?>>No</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="thatcamp_organizers">Organizers</label>
				</th>

				<td>
					<ul id="organizer-list">
					<?php $users = new WP_User_Query( array( 'blog_id' => get_current_blog_id() ) ) ?>
					<?php foreach ( $users->results as $user ) : ?>
						<?php $is_organizer = get_user_meta( $user->ID, 'wp_' . get_current_blog_id() . '_is_organizer', true ) ?>
						<li><input name="thatcamp_organizers[]" value="<?php echo esc_attr( $user->ID ) ?>" <?php checked( 'yes', $is_organizer ) ?> type="checkbox"> <?php echo bp_core_get_userlink( $user->ID ) ?></li>
					<?php endforeach ?>
					</ul>

					<p class="description">Select all users who should be labeled as 'organizers' of your THATCamp.</p>
				</td>
			</tr>
		</table>

		<br /><br />

		<?php wp_nonce_field( 'thatcamp_setup' ) ?>

		<input type="submit" class="button-primary" name="thatcamp_setup_submit" value="Save Changes" />
	</div>

	</form>
	<?php
}

/**
 * Catches form submits
 */
function thatcamp_admin_catch_submit() {
	global $plugin_page;

	if ( current_user_can( 'manage_options' ) && 'thatcamp_setup' == $plugin_page && ! empty( $_POST['thatcamp_setup_submit'] ) ) {
		check_admin_referer( 'thatcamp_setup' );

		// Fetch the group id, which we'll need throughout
		$group_id = thatcamp_get_blog_group( get_current_blog_id() );

		// Date
		$date = isset( $_POST['thatcamp_date'] ) ? strtotime( $_POST['thatcamp_date'] ) : '';
		groups_update_groupmeta( $group_id, 'thatcamp_date', $date );

		// Has workshops
		$has_workshops = isset( $_POST['thatcamp_has_workshops'] ) ? $_POST['thatcamp_has_workshops'] : '';
		if ( ! in_array( $has_workshops, array( 'yes', 'maybe', 'no' ) ) )
			$has_workshops = 'no';
		groups_update_groupmeta( $group_id, 'thatcamp_has_workshops', $has_workshops );

		// Organizers
		$organizers = isset( $_POST['thatcamp_organizers'] ) ? $_POST['thatcamp_organizers'] : '';
		$organizers = wp_parse_id_list( $organizers );

		$org_key = 'wp_' . get_current_blog_id() . '_is_organizer';

		$existing = new WP_User_Query( array( 'meta_key' => $org_key, 'meta_value' => 'yes' ) );
		$existing_ids = array();

		if ( ! empty( $existing->results ) ) {
			$existing_ids = wp_list_pluck( $existing->results, 'ID' );
		}

		// Add passed organizers
		foreach ( $organizers as $org ) {
			update_user_meta( $org, $org_key, 'yes' );
		}

		// Remove others
		foreach ( $existing_ids as $existing_id ) {
			if ( ! in_array( $existing_id, $organizers ) ) {
				delete_user_meta( $existing_id, $org_key );
			}
		}

		wp_redirect( add_query_arg( array(
			'page' => 'thatcamp_setup',
			'settings-updated' => 'true',
		), admin_url( 'admin.php' ) ) );
	}
}
add_action( 'admin_init', 'thatcamp_admin_catch_submit' );

/**
 * Print success notice
 */
function thatcamp_admin_notice() {
	global $plugin_page;

	if ( 'thatcamp_setup' == $plugin_page && ! empty( $_GET['settings-updated'] ) ) {
		echo '<div class="updated settings-error"><p><strong>Settings saved.</strong></p></div>';
	}
}
add_action( 'admin_notices', 'thatcamp_admin_notice' );

/**
 * Enqueues scripts
 */
function thatcamp_admin_scripts() {
	wp_enqueue_script( 'thatcamp_setup', WP_CONTENT_URL . '/mu-plugins/js/thatcamp-setup.js'  );
	wp_enqueue_script( 'jqueryui-datepicker' );
}
