<?php
if ( ! function_exists( 'is_admin' ) || ! is_admin() ) {
	header( 'Location: /' );
	exit;
}
?>
<div class="postbox">
	<h3><?php _e( 'Add New Subscription', 'subscribe-reloaded' ) ?></h3>

	<form action="" method="post" id="update_address_form"
		  onsubmit="if (this.srp.value == '' || this.sre.value == '') return false;">
		<fieldset style="border:0">
			<p><?php _e( 'Post:', 'subscribe-reloaded' );
echo ' <strong>' . get_the_title( intval( $_GET['srp'] ) ) . " (" . intval( $_GET['srp'] ) . ")"; ?></strong></p>

			<p class="liquid"><label for='sre'><?php _e( 'Email', 'subscribe-reloaded' ) ?></label>
				<input readonly='readonly' type='text' size='30' name='sre' id='sre' value='<?php echo esc_attr($_GET['sre']) ?>' />
			</p>

			<p class="liquid"><label for='srs'><?php _e( 'Status', 'subscribe-reloaded' ) ?></label>
				<select name="srs" id="srs">
					<option value='Y'><?php _e( 'Active', 'subscribe-reloaded' ) ?></option>
					<option value='R'><?php _e( 'Replies only', 'subscribe-reloaded' ) ?></option>
					<option value='YC'><?php _e( 'Ask user to confirm', 'subscribe-reloaded' ) ?></option>
				</select>
				<input type='submit' class='subscribe-form-button' value='<?php _e( 'Update', 'subscribe-reloaded' ) ?>' />
			</p>
			<input type='hidden' name='sra' value='add' />
			<input type='hidden' name='srp' value='<?php echo intval( $_GET['srp'] ) ?>' />
		</fieldset>
	</form>
</div>
