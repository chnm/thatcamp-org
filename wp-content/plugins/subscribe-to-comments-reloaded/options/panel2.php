<?php
// Avoid direct access to this piece of code
if ( ! function_exists( 'is_admin' ) || ! is_admin() ) {
	header( 'Location: /' );
	exit;
}

// Update options
if ( isset( $_POST['options'] ) ) {
	$faulty_fields = '';
	if ( isset( $_POST['options']['show_subscription_box'] ) && ! subscribe_reloaded_update_option( 'show_subscription_box', $_POST['options']['show_subscription_box'], 'yesno' ) ) {
		$faulty_fields = __( 'Enable default checkbox', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['checked_by_default'] ) && ! subscribe_reloaded_update_option( 'checked_by_default', $_POST['options']['checked_by_default'], 'yesno' ) ) {
		$faulty_fields = __( 'Checked by default', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['checked_by_default_value'] ) && ! subscribe_reloaded_update_option( 'checked_by_default_value', $_POST['options']['checked_by_default_value'], 'integer' ) ) {
		$faulty_fields = __( 'Checked by default Value', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['enable_advanced_subscriptions'] ) && ! subscribe_reloaded_update_option( 'enable_advanced_subscriptions', $_POST['options']['enable_advanced_subscriptions'], 'yesno' ) ) {
		$faulty_fields = __( 'Advanced subscription', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['default_subscription_type'] ) && ! subscribe_reloaded_update_option( 'default_subscription_type', $_POST['options']['default_subscription_type'], 'integer' ) ) {
		$faulty_fields = __( 'Advanced subscription', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['checkbox_inline_style'] ) && ! subscribe_reloaded_update_option( 'checkbox_inline_style', $_POST['options']['checkbox_inline_style'], 'text-no-encode' ) ) {
		$faulty_fields = __( 'Custom inline style', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['checkbox_html'] ) && ! subscribe_reloaded_update_option( 'checkbox_html', $_POST['options']['checkbox_html'], 'text-no-encode' ) ) {
		$faulty_fields = __( 'Custom HTML', 'subscribe-reloaded' ) . ', ';
	}
	// default_subscription_type
	if ( isset( $_POST['options']['checkbox_label'] ) && ! subscribe_reloaded_update_option( 'checkbox_label', $_POST['options']['checkbox_label'], 'text-no-encode' ) ) {
		$faulty_fields = __( 'Checkbox label', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['subscribed_label'] ) && ! subscribe_reloaded_update_option( 'subscribed_label', $_POST['options']['subscribed_label'], 'text-no-encode' ) ) {
		$faulty_fields = __( 'Subscribed label', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['subscribed_waiting_label'] ) && ! subscribe_reloaded_update_option( 'subscribed_waiting_label', $_POST['options']['subscribed_waiting_label'], 'text-no-encode' ) ) {
		$faulty_fields = __( 'Awaiting label', 'subscribe-reloaded' ) . ', ';
	}
	if ( isset( $_POST['options']['author_label'] ) && ! subscribe_reloaded_update_option( 'author_label', $_POST['options']['author_label'], 'text-no-encode' ) ) {
		$faulty_fields = __( 'Author label', 'subscribe-reloaded' ) . ', ';
	}

	// Display an alert in the admin interface if something went wrong
	echo '<div class="updated fade"><p>';
	if ( empty( $faulty_fields ) ) {
		_e( 'Your settings have been successfully updated.', 'subscribe-reloaded' );
	} else {
		_e( 'There was an error updating the following fields:', 'subscribe-reloaded' );
		echo ' <strong>' . substr( $faulty_fields, 0, - 2 ) . '</strong>';
	}
	echo "</p></div>\n";
}
?>
<form action="" method="post">
	<h3><?php _e( 'Options', 'subscribe-reloaded' ) ?></h3>
	<table class="form-table <?php echo $wp_locale->text_direction ?>">
		<tbody>
		<tr>
			<th scope="row">
				<label for="show_subscription_box"><?php _e( 'Enable default checkbox', 'subscribe-reloaded' ) ?></label>
			</th>
			<td>
				<input type="radio" name="options[show_subscription_box]" id="show_subscription_box" value="yes"<?php echo ( subscribe_reloaded_get_option( 'show_subscription_box' ) == 'yes' ) ? ' checked="checked"' : ''; ?>> <?php _e( 'Yes', 'subscribe-reloaded' ) ?> &nbsp; &nbsp; &nbsp;
				<input type="radio" name="options[show_subscription_box]" value="no" <?php echo ( subscribe_reloaded_get_option( 'show_subscription_box' ) == 'no' ) ? '  checked="checked"' : ''; ?>> <?php _e( 'No', 'subscribe-reloaded' ) ?>
				<div class="description"><?php _e( 'Disable this option if you want to move the subscription checkbox to a different place on your page.', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="checked_by_default"><?php _e( 'Checked by default', 'subscribe-reloaded' ) ?></label>
			</th>
			<td>
				<input type="radio" name="options[checked_by_default]" id="checked_by_default" value="yes"<?php echo ( subscribe_reloaded_get_option( 'checked_by_default' ) == 'yes' ) ? ' checked="checked"' : ''; ?>> <?php _e( 'Yes', 'subscribe-reloaded' ) ?> &nbsp; &nbsp; &nbsp;
				<input type="radio" name="options[checked_by_default]" value="no" <?php echo ( subscribe_reloaded_get_option( 'checked_by_default' ) == 'no' ) ? '  checked="checked"' : ''; ?>> <?php _e( 'No', 'subscribe-reloaded' ) ?>
				<div class="description"><?php _e( 'Decide if the checkbox should be checked by default or not.', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
<?php
// This option will be visible only when the Checkbox option is enable
if ( subscribe_reloaded_get_option( 'checked_by_default' ) == 'yes') :
?>
			<tr>
				<th scope="row">
					<label for="checked_by_default_value"><?php _e( 'Default Checkbox Value', 'subscribe-reloaded' ) ?></label>
				</th>
				<td>
					<select name="options[checked_by_default_value]" id="checked_by_default_value">
						<option value="0" <?php echo ( subscribe_reloaded_get_option( 'checked_by_default_value' ) === '0' ) ? "selected='selected'" : ''; ?>><?php _e( 'All new comments', 'subscribe-reloaded' ); ?></option>
						<option value="1" <?php echo ( subscribe_reloaded_get_option( 'checked_by_default_value' ) === '1' ) ? "selected='selected'" : ''; ?>><?php _e( 'Replies to this comment', 'subscribe-reloaded' ); ?></option>
					</select>
					<div class="description"><?php _e( 'Select the default option for the Checkbox. Be careful! Some users might like to be subscribed to all the post.', 'subscribe-reloaded' ); ?></div>
				</td>
			</tr>
<?php else :
	echo "<input type='hidden' name='options[checked_by_default_value]' value = '0'>";
endif; ?>
		<tr>
			<th scope="row">
				<label for="enable_advanced_subscriptions"><?php _e( 'Advanced subscription', 'subscribe-reloaded' ) ?></label>
			</th>
			<td>
				<input type="radio" name="options[enable_advanced_subscriptions]" id="enable_advanced_subscriptions" value="yes"<?php echo ( subscribe_reloaded_get_option( 'enable_advanced_subscriptions' ) == 'yes' ) ? ' checked="checked"' : ''; ?>> <?php _e( 'Yes', 'subscribe-reloaded' ) ?> &nbsp; &nbsp; &nbsp;
				<input type="radio" name="options[enable_advanced_subscriptions]" value="no" <?php echo ( subscribe_reloaded_get_option( 'enable_advanced_subscriptions' ) == 'no' ) ? '  checked="checked"' : ''; ?>> <?php _e( 'No', 'subscribe-reloaded' ) ?>
				<div class="description"><?php _e( 'Allow users to choose from different subscription types (all, replies only).', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		<?php
// Make sure that the default subscription type is visible only when advance subscriptions are enable
if ( subscribe_reloaded_get_option( 'enable_advanced_subscriptions' ) == 'yes' ):    ?>
			<tr>
				<th scope="row">
					<label for="default_subscription_type"><?php _e( 'Advanced default', 'subscribe-reloaded' ) ?></label>
				</th>
				<td>
					<select name="options[default_subscription_type]" id="default_subscription_type">
						<option value="0" <?php echo ( subscribe_reloaded_get_option( 'default_subscription_type' ) === '0' ) ? "selected='selected'" : ''; ?>><?php _e( 'None', 'subscribe-reloaded' ); ?></option>
						<option value="1" <?php echo ( subscribe_reloaded_get_option( 'default_subscription_type' ) === '1' ) ? "selected='selected'" : ''; ?>><?php _e( 'All new comments', 'subscribe-reloaded' ); ?></option>
						<option value="2" <?php echo ( subscribe_reloaded_get_option( 'default_subscription_type' ) === '2' ) ? "selected='selected'" : ''; ?>><?php _e( 'Replies to this comment', 'subscribe-reloaded' ); ?></option>
					</select>

					<div class="description"><?php _e( 'The default subscription type that should be selected when Advanced subscriptions are enable.', 'subscribe-reloaded' ); ?></div>
				</td>
			</tr>
<?php endif; ?>
		<tr>
			<th scope="row">
				<label for="checkbox_inline_style"><?php _e( 'Custom inline style', 'subscribe-reloaded' ) ?></label>
			</th>
			<td>
				<input type="text" name="options[checkbox_inline_style]" id="checkbox_inline_style" value="<?php echo subscribe_reloaded_get_option( 'checkbox_inline_style' ); ?>" size="20">

				<div class="description"><?php _e( 'Custom inline CSS to add to the checkbox.', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="checkbox_html"><?php _e( 'Custom HTML', 'subscribe-reloaded' ) ?></label></th>
			<td>
				<?php
					$id_checkbox_html = "checkbox_html";
					$args_notificationContent = array(
						"media_buttons" => false,
						"textarea_rows" => 5,
						"teeny"         => true,
						"textarea_name" => "options[{$id_checkbox_html}]",
						"tinymce"		=> false
					);
					wp_editor( subscribe_reloaded_get_option( $id_checkbox_html ), $id_checkbox_html, $args_notificationContent );
				?>
				<div class="description"><?php _e( 'Custom HTML code to be used when displaying the checkbox. Allowed tags: [checkbox_field], [checkbox_label]', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		</tbody>
	</table>

	<h3><?php _e( 'Messages for your visitors', 'subscribe-reloaded' ) ?></h3>
	<table class="form-table <?php echo $wp_locale->text_direction ?>">
		<tbody>
		<tr>
			<th scope="row"><label for="checkbox_label"><?php _e( 'Default label', 'subscribe-reloaded' ) ?></label>
			</th>
			<td>
				<?php
					$id_checkbox_label = "checkbox_label";
					$args_notificationContent = array(
						"media_buttons" => false,
						"textarea_rows" => 3,
						"teeny"         => true,
						"textarea_name" => "options[{$id_checkbox_label}]"
					);
					wp_editor( subscribe_reloaded_get_option( $id_checkbox_label ), $id_checkbox_label, $args_notificationContent );
				?>
				<div class="description"><?php _e( 'Label associated to the checkbox. Allowed tag: [subscribe_link]', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="subscribed_label"><?php _e( 'Subscribed label', 'subscribe-reloaded' ) ?></label></th>
			<td>
				<?php
					$id_subscribed_label = "subscribed_label";
					$args_notificationContent = array(
						"media_buttons" => false,
						"textarea_rows" => 3,
						"teeny"         => true,
						"textarea_name" => "options[{$id_subscribed_label}]"
					);
					wp_editor( subscribe_reloaded_get_option( $id_subscribed_label ), $id_subscribed_label, $args_notificationContent );
				?>
				<div class="description"><?php _e( 'Label shown to those who are already subscribed to a post. Allowed tag: [manager_link]', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="subscribed_waiting_label"><?php _e( 'Pending label', 'subscribe-reloaded' ) ?></label></th>
			<td>
				<?php
					$id_subscribed_waiting_label = "subscribed_waiting_label";
					$args_notificationContent = array(
						"media_buttons" => false,
						"textarea_rows" => 3,
						"teeny"         => true,
						"textarea_name" => "options[{$id_subscribed_waiting_label}]"
					);
					wp_editor( subscribe_reloaded_get_option( $id_subscribed_waiting_label ), $id_subscribed_waiting_label, $args_notificationContent );
				?>
				<div class="description"><?php _e( "Label shown to those who are already subscribed, but haven't clicked on the confirmation link yet. Allowed tag: [manager_link]", 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="author_label"><?php _e( 'Author label', 'subscribe-reloaded' ) ?></label></th>
			<td>
				<?php
					$id_author_label = "author_label";
					$args_notificationContent = array(
						"media_buttons" => false,
						"textarea_rows" => 3,
						"teeny"         => true,
						"textarea_name" => "options[{$id_author_label}]"
					);
					wp_editor( subscribe_reloaded_get_option( $id_author_label ), $id_author_label, $args_notificationContent );
				?>
				<div class="description"><?php _e( 'Label shown to authors (and administrators). Allowed tag: [manager_link]', 'subscribe-reloaded' ); ?></div>
			</td>
		</tr>
		</tbody>
	</table>
	<p class="submit"><input type="submit" value="<?php _e( 'Save Changes' ) ?>" class="button-primary" name="Submit">
	</p>
</form>
