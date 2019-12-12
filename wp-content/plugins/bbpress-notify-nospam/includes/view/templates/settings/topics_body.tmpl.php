<?php do_action( 'bbpnns_settings_topics_box_top' ); ?>

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
	.full-width {
		width: 100%;	
	}
</style>

<script>
jQuery(document).ready(function($){
	
	$("#bbpnns-topic-recipients").select2({
		  placeholder: "<?php esc_attr_e( 'Select one or more Roles', 'bbPress_Notify_noSpam' ) ;?>",
		  allowClear: true
		});
});

</script>

<table class="form-table">
	<tbody>
	
	<?php do_action( 'bbpnns_settings_topics_box_before_first_row' ); ?>
	
		<tr>
			<th scope="row"><?php _e( 'Recipients' , 'bbPress_Notify_noSpam' ) ; ?></th>
			<td><label for="bbpnns-topic-recipients"><?php _e( 'Select one or more roles below to determine which users will be notified of new Topics.' )?>
				
				<br><br>
				<?php 
					global $wp_roles;

					$options      = $wp_roles->get_names();
					$saved_option = array_flip( $stash->settings->newtopic_recipients );
				?>
				<select id="bbpnns-topic-recipients" class="full-width" multiple="multiple" name="<?php echo $this->settings_name; ?>[newtopic_recipients][]">
			<?php foreach( $options as $value => $description ) :?>
			
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( isset( $saved_option[$value] ) );?>><?php echo esc_html( $description ); ?></option>
			
			<?php endforeach; ?>
			</select>
			<br><br>
				<span class="description"><?php _e( 'By selecting roles, all users of the selected roles will receive a notification of each new topic.<br>
				You can also leave none selected and check only the "Override Subscriptions to Forums" option. That way only users who have explicitly subscribed to a 
				given forum will receive notifications.', 'bbPress_Notify_noSpam' ) ?></span>
			</td>
		</tr>
	
		<tr>
			<th scope="row"><?php _e( 'Admin UI Topic Notifications', 'bbPress_Notify_noSpam' ) ; ?></th>
			<td>
				<label>
					<input type="checkbox" name="<?php echo $this->settings_name; ?>[default_topic_notification_checkbox]" value="1"
					<?php checked( $stash->settings->default_topic_notification_checkbox ); ?> >
					       <?php _e( 'Make "Send Notifications" option checked by default.', 'bbPress_Notify_noSpam' ) ; ?>
					       <br><br>
					       <span class="description"><?php _e( 'This option controls the status of the "Send Notifications" 
					       										checkbox in the New/Edit Topic Admin Screen', 'bbPress_Notify_noSpam' ) ; ?>.</span>
				</label>
			</td>
		</tr>
		
		
		<tr>
			<th scope="row"><?php _e( 'bbPress Forums Subscriptions Override', 'bbPress_Notify_noSpam' ) ;?></th>
			<td>
				<label>
					<input type="checkbox" name="<?php echo $this->settings_name; ?>[override_bbp_forum_subscriptions]" value="1"
					<?php checked( $stash->settings->override_bbp_forum_subscriptions ); ?> >
					       <?php _e( 'Override Subscriptions to Forums.', 'bbPress_Notify_noSpam' ) ; ?>
					       <br><br>
					       <span class="description"><?php _e( 'Enable this option if you want bbPress Notify (No-Spam) to handle bbPress subscriptions to Forums (new topics).
The bbPress Setting "Allow users to subscribe to forums and topics" must also be enabled for this to work.<br><a target="_blank" href="https://usestrict.net/2013/02/bbpress-notify-nospam/#subscriptions">Click here to learn more.</a>', 'bbPress_Notify_noSpam' ) ;?></span>
				</label>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><?php _e( 'Notify authors of their own Topics', 'bbPress_Notify_noSpam' ) ; ?></th>
			<td>
				<label>
					<input type="checkbox" name="<?php echo $this->settings_name; ?>[notify_authors_topic]" value="1"
					<?php checked( $stash->settings->notify_authors_topic ); ?> >
					       <?php _e( 'Authors must also receive a notification when they create a topic.', 'bbPress_Notify_noSpam' ) ; ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php _e( 'Force Admin-only emails if Forum is hidden', 'bbPress_Notify_noSpam' ) ; ?></th>
			<td>
				<label>
					<input type="checkbox" name="<?php echo $this->settings_name; ?>[hidden_forum_topic_override]" value="1"
					<?php checked( $stash->settings->hidden_forum_topic_override ); ?> >
					       <?php _e( 'Only admins should be notified of new topics in hidden forums.', 'bbPress_Notify_noSpam' ) ; ?>
				</label>
				<br></br>
				<span class="description"><?php _e( 'Looking to have group forums? Try <a href="https://wordpress.org/plugins/bbp-private-groups/" target="_blank">Private Groups</a>
				and our premium <a href="https://usestrict.net/product/bbpress-notify-no-spam-private-groups-bridge/" target="_new">Private Groups Bridge</a> add-on.', 'bbPress_Notify_noSpam' ) ;?>
				</span>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><?php _e( 'Topic E-mail Subject Line', 'bbPress_Notify_noSpam' ) ; ?></th>
			<td>
				<input type="text" class="full-width bbpnns-message-subject" name="<?php echo $this->settings_name; ?>[newtopic_email_subject]" value="<?php echo esc_attr( $stash->settings->newtopic_email_subject ); ?>">
				<br><br>
				<span class="description bbpnns-subject-line">
				<?php printf( __( '<strong>Available Tags</strong>: %s.', 'bbPress_Notify_noSpam' ) , 
					  join( ', ', apply_filters( 'bbpnns_settings_available_topics_tags', array() ) ) ); ?>
				</span>
			</td>
		</tr>
		
		<tr>
			<td colspan="2"><strong><?php _e( 'Topic E-mail Body', 'bbPress_Notify_noSpam' ) ; ?></strong><br>
				<?php 
					wp_editor( $content=$stash->settings->newtopic_email_body, $id='bbpnns_newtopic_email_body', 
					array( 'textarea_rows' => 15, 'media_buttons' => false, 'textarea_name' => $this->settings_name . '[newtopic_email_body]' ) );
				?>
				<br>
				<span class="description bbpnns-message-body">
				<?php printf( __( '<strong>Available Tags</strong>: %s.', 'bbPress_Notify_noSpam' ) , 
					  join( ', ', apply_filters( 'bbpnns_settings_available_topics_tags', array() ) ) ); ?>
				</span>
			</td>
		</tr>
		
		<input type="hidden" name="bbpnns_nullable_fields" 
value="default_topic_notification_checkbox,override_bbp_forum_subscriptions,notify_authors_topic,hidden_forum_topic_override,newtopic_recipients" />
		
		<?php do_action( 'bbpnns_settings_topics_box_after_last_row' ); ?>
		
	</tbody>
</table>


<?php do_action( 'bbpnns_settings_topics_box_bottom' ); ?>

<?php 

/* End if file topics_body.tmpl.php */
/* Location: includes/view/templates/settings/topics_body.tmpl.php */
