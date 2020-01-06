<?php
/**
 * Updating add reason for Whitelist via ajax
 * @uses   ajax
 * @return void
 */
if ( ! function_exists( 'gglcptch_update_reason' ) ) {
	function gglcptch_update_reason() {
		global $wpdb;
		if ( ! empty( $_POST['gglcptch_edit_ip_id'] ) ) {
			check_ajax_referer( 'gglcptch_ajax_nonce_value', 'gglcptch_nonce' );
			$ip_id		= intval( $_POST['gglcptch_edit_ip_id'] );

			$message_list = array(
				'reason_update_success'		=> __( 'The reason has been updated successfully.', 'google-captcha-pro' ),
				'reason_update_error'		=> __( 'Error while updating reason.', 'google-captcha-pro' ),
				'reason_untouched'			=> __( 'No changes was made.', 'google-captcha-pro' )
			);

			$add_reason	= ! empty( $_POST['gglcptch_reason'] ) ? stripslashes( sanitize_text_field( $_POST['gglcptch_reason'] ) ) : '';
			$n = $wpdb->update(
				$wpdb->prefix . 'gglcptch_whitelist',
				array( 'add_reason' => $add_reason ),
				array( 'id' => $ip_id )
			);
			if ( !! $n ) {
				/* if number of touched rows != 0/false */
				echo json_encode( array(
					'success'		=> $message_list['reason_update_success'],
					'reason'		=> $add_reason,
					'reason-html'	=> nl2br( $add_reason ),
				) );
				die();
			} else {
				if ( $n !== 0 ) {
					echo json_encode( array(
						'success'	=> '',
						'no_changes' => '',
						'error'		=> $message_list['reason_update_error']
					) );
					die();
				} else {
					echo json_encode( array(
						'success'	=> '',
						'no_changes' => $message_list['reason_untouched']
					) );
					die();
				}
			}
		} else {
			echo json_encode( array( 'error' => $message_list['reason_update_error'] ) );
			die();
		}
	}
}
add_action( 'wp_ajax_gglcptch_update_reason', 'gglcptch_update_reason' );