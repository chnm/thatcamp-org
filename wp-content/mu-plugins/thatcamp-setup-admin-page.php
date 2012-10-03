<?php

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
