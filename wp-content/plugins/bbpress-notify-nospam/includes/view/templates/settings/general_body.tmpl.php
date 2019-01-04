<?php do_action( 'bbpnns_settings_global_box_top' ); ?>

<style>
	.form-table tr {
		border-bottom: 1px groove #ccc;
	}
	.form-table tr:last-child {
		border-bottom:0;
	}
	.handlediv {
		display: none !important;
	}
</style>
<table class="form-table">
	<tbody>
	
	<?php do_action( 'bbpnns_settings_global_box_before_first_row' ); ?>
	
		<tr>
			<th scope="row"><?php _e( 'Email Type', 'bbPress_Notify_noSpam' ) ; ?></th>
			<td>
			<select name="<?php echo $this->settings_name;?>[email_type]" id="bbpress_notify_message_type">
			<?php foreach( array( 'html'      => __( 'HTML', 'bbPress_Notify_noSpam' ) , 
							      'plain'     => __( 'Plain Text', 'bbPress_Notify_noSpam' ) ,
							      'multipart' => __( 'Both', 'bbPress_Notify_noSpam' )  ) as $val => $text ): ?>
			<option value="<?php echo $val?>" <?php selected( $stash->settings->email_type, $val ); ?>><?php echo esc_html( $text ); ?></option>
			<?php endforeach;?>
		</select><br><br>
		<span class="description"><?php _e('Choose the type of emails you want your subscribers to receive. 
		Choosing <code>Both</code> sends out a single message in both <code>HTML</code> and <code>Plain Text</code> format, and it\'s up to the user\'s email client to decide which one to display
		based on their preferences.', 'bbPress_Notify_noSpam' ) ;?></span>
			</td>
		</tr>
		
		<?php $bg_notifications = ( $stash->settings->newtopic_background || $stash->settings->newreply_background || $stash->settings->background_notifications  ); ?>
		
		<tr>
			<th scope="row"><?php _e( 'Background Notifications', 'bbPress_Notify_noSpam' ) ; ?></th>
			<td>
				<label>
					<input type="checkbox" name="<?php echo $this->settings_name; ?>[background_notifications]" value="1"
					<?php checked( $bg_notifications ); ?> >
					       <?php _e( 'Send emails in the background the next time the site is visited.', 'bbPress_Notify_noSpam' ) ; ?>
				</label>
			</td>
		</tr>
	
		<tr>
			<th scope="row"><?php _e( 'Subject Line Encoding', 'bbPress_Notify_noSpam' ) ; ?></th>
			<td>
			<?php $disable_encoding = ! function_exists( 'iconv_mime_encode' );  ?>
				<label><input type="checkbox" name="<?php echo $this->settings_name?>[encode_subject]" 
				       value="1" <?php checked( $stash->settings->encode_subject ); ?>  <?php echo $disable_encoding ? 'disabled="disabled"' : '' ?>>
				       <?php _e( 'Encode Subject line using UTF-8.', 'bbPress_Notify_noSpam' ) ; ?></label>
				       <br><br>
				 
				 <?php if ( $disable_encoding ) : ?>
					<i class="dashicons-before dashicons-warning"></i><span class="description"><?php _e( 'The option above is disabled because your host did not provide the PHP function <code>iconv_mime_encode</code>. 
					This is unusual. Perhaps you could consider <a href="https://usestrict.net/go/siteground" target="_new">changing to a better one</a>?</span>', 'bbPress_Notify_noSpam' ) ; ?>				 
				 
				 <?php else: ?>
				       
			 		<span class="description"><?php _e( 'Turn this option ON if you\'re seeing HTML entities in your email subject lines such as <code>&amp;dash;</code>
			       		Leave it OFF if you\'re using a third party email plugin that already does this for you, or if you see extra question marks in the email subject.', 'bbPress_Notify_noSpam' ) ; ?></span>
				 <?php endif;?>
			</td>
		</tr>
		
		<input type="hidden" name="bbpnns_checkbox_fields" value="background_notifications,encode_subject,show_credits" />
		
		<?php do_action( 'bbpnns_settings_global_box_after_last_row' ); ?>
		
	</tbody>
</table>


<?php do_action( 'bbpnns_settings_global_box_bottom' ); ?>


<?php 

/* End if file topics_body.tmpl.php */
/* Location: includes/view/templates/settings/topics_body.tmpl.php */
