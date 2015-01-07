<div class="wrap">
	<h2><?php _e( 'Multisite Maintenance Mode', 'multisite-maintenance-mode' ); ?></h2>

	<form method="post" action="edit.php?action=mmm_save">
		<?php wp_nonce_field( 'mmm-settings' ); ?>
		<h3><?php _e( 'Toggle Maintenance Mode', 'multisite-maintenance-mode' ); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="mmm-status"><?php _e( 'Set maintenance mode status', 'multisite-maintenance-mode' ); ?></label></th>
				<td>
					<label><input name="mmm-status" type="checkbox" id="mmm-status" value="1" <?php checked( $status ); ?> /> <?php _e( 'Turn on maintenance mode', 'multisite-maintenance-mode' ); ?></label><br />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mmm-message"><?php _e( 'Admin bar message', 'multisite-maintenance-mode' ); ?></label></th>
				<td>
					<label><input name="mmm-message" type="text" id="mmm-message" value="<?php echo $message; ?>" /></label>
			</tr>
			<tr>
				<th scope="row"><label for="mmm-link"><?php _e( 'URL to your announcement page', 'multisite-maintenance-mode' ); ?></label></th>
				<td>
					<label><input name="mmm-link" type="text" id="mmm-link" value="<?php echo $link; ?>" /></label>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
	
</div><!-- .wrap -->
