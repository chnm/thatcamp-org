<?php

/**
 * BuddyPress - Create Group
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

get_header( 'thatcamp' ); ?>

	
	<div id="primary" class="main-content">
	<div id="content" role="main">
		
		<?php do_action( 'bp_before_create_group_content_template' ); ?>

		<form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">
			<h3><?php _e( 'Create a Group', 'thatcamp' ); ?> &nbsp;<a class="button" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php _e( 'Groups Directory', 'thatcamp' ); ?></a></h3>

			<?php do_action( 'bp_before_create_group' ); ?>

			<div class="item-list-tabs no-ajax" id="group-create-tabs" role="navigation">
				<ul>

					<?php bp_group_creation_tabs(); ?>

				</ul>
			</div>

			<?php do_action( 'template_notices' ); ?>

			<div class="item-body" id="group-create-body">

				<?php if ( bp_is_group_creation_step( 'group-details' ) ) : ?>

					<?php do_action( 'bp_before_group_details_creation_step' ); ?>

					<label for="group-name"><?php _e( 'Group Name (required)', 'thatcamp' ); ?></label>
					<input type="text" name="group-name" id="group-name" aria-required="true" value="<?php bp_new_group_name(); ?>" />

					<label for="group-desc"><?php _e( 'Group Description (required)', 'thatcamp' ); ?></label>
					<textarea name="group-desc" id="group-desc" aria-required="true"><?php bp_new_group_description(); ?></textarea>

					<?php
					do_action( 'bp_after_group_details_creation_step' );
					do_action( 'groups_custom_group_fields_editable' );

					wp_nonce_field( 'groups_create_save_group-details' ); ?>

				<?php endif; ?>

				<?php if ( bp_is_group_creation_step( 'group-settings' ) ) : ?>

					<?php do_action( 'bp_before_group_settings_creation_step' ); ?>

					<h4><?php _e( 'Privacy Options', 'thatcamp' ); ?></h4>

					<div class="radio">
						<label><input type="radio" name="group-status" value="public"<?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'This is a public group', 'thatcamp' ); ?></strong>
							<ul>
								<li><?php _e( 'Any site member can join this group.', 'thatcamp' ); ?></li>
								<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'thatcamp' ); ?></li>
								<li><?php _e( 'Group content and activity will be visible to any site member.', 'thatcamp' ); ?></li>
							</ul>
						</label>

						<label><input type="radio" name="group-status" value="private"<?php if ( 'private' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'This is a private group', 'thatcamp' ); ?></strong>
							<ul>
								<li><?php _e( 'Only users who request membership and are accepted can join the group.', 'thatcamp' ); ?></li>
								<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'thatcamp' ); ?></li>
								<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'thatcamp' ); ?></li>
							</ul>
						</label>

						<label><input type="radio" name="group-status" value="hidden"<?php if ( 'hidden' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e('This is a hidden group', 'thatcamp'); ?></strong>
							<ul>
								<li><?php _e( 'Only users who are invited can join the group.', 'thatcamp' ); ?></li>
								<li><?php _e( 'This group will not be listed in the groups directory or search results.', 'thatcamp' ); ?></li>
								<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'thatcamp' ); ?></li>
							</ul>
						</label>
					</div>

					<h4><?php _e( 'Group Invitations', 'thatcamp' ); ?></h4>

					<p><?php _e( 'Which members of this group are allowed to invite others?', 'thatcamp' ); ?></p>

					<div class="radio">
						<label>
							<input type="radio" name="group-invite-status" value="members"<?php bp_group_show_invite_status_setting( 'members' ); ?> />
							<strong><?php _e( 'All group members', 'thatcamp' ); ?></strong>
						</label>

						<label>
							<input type="radio" name="group-invite-status" value="mods"<?php bp_group_show_invite_status_setting( 'mods' ); ?> />
							<strong><?php _e( 'Group admins and mods only', 'thatcamp' ); ?></strong>
						</label>

						<label>
							<input type="radio" name="group-invite-status" value="admins"<?php bp_group_show_invite_status_setting( 'admins' ); ?> />
							<strong><?php _e( 'Group admins only', 'thatcamp' ); ?></strong>
						</label>
					</div>

					<?php if ( bp_is_active( 'forums' ) ) : ?>

						<h4><?php _e( 'Group Forums', 'thatcamp' ); ?></h4>

						<?php if ( bp_forums_is_installed_correctly() ) : ?>

							<p><?php _e( 'Should this group have a forum?', 'thatcamp' ); ?></p>

							<div class="checkbox">
								<label><input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php checked( bp_get_new_group_enable_forum(), true, true ); ?> /> <?php _e( 'Enable discussion forum', 'thatcamp' ); ?></label>
							</div>
						<?php elseif ( is_super_admin() ) : ?>

							<p><?php printf( __( '<strong>Attention Site Admin:</strong> Group forums require the <a href="%s">correct setup and configuration</a> of a bbPress installation.', 'thatcamp' ), bp_core_do_network_admin() ? network_admin_url( 'settings.php?page=bb-forums-setup' ) :  admin_url( 'admin.php?page=bb-forums-setup' ) ); ?></p>

						<?php endif; ?>

					<?php endif; ?>

					<?php do_action( 'bp_after_group_settings_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-settings' ); ?>

				<?php endif; ?>

				<?php if ( bp_is_group_creation_step( 'group-avatar' ) ) : ?>

					<?php do_action( 'bp_before_group_avatar_creation_step' ); ?>

					<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>

						<div class="left-menu">

							<?php bp_new_group_avatar(); ?>

						</div>

						<div class="main-column">
							<p><?php _e( "Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.", 'thatcamp' ); ?></p>

							<p>
								<input type="file" name="file" id="file" />
								<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'thatcamp' ); ?>" />
								<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
							</p>

							<p><?php _e( 'To skip the avatar upload process, hit the "Next Step" button.', 'thatcamp' ); ?></p>
						</div>

					<?php endif; ?>

					<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

						<h3><?php _e( 'Crop Group Avatar', 'thatcamp' ); ?></h3>

						<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'thatcamp' ); ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'thatcamp' ); ?>" />
						</div>

						<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'thatcamp' ); ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
						<input type="hidden" name="upload" id="upload" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

					<?php endif; ?>

					<?php do_action( 'bp_after_group_avatar_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-avatar' ); ?>

				<?php endif; ?>

				<?php if ( bp_is_group_creation_step( 'group-invites' ) ) : ?>

					<?php do_action( 'bp_before_group_invites_creation_step' ); ?>

					<?php if ( bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

						<div class="left-menu">

							<div id="invite-list">
								<ul>
									<?php bp_new_group_invite_friend_list(); ?>
								</ul>

								<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ); ?>
							</div>

						</div>

						<div class="main-column">

							<div id="message" class="info">
								<p><?php _e('Select people to invite from your friends list.', 'thatcamp'); ?></p>
							</div>

							<ul id="friend-list" class="item-list" role="main">

							<?php if ( bp_group_has_invites() ) : ?>

								<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

									<li id="<?php bp_group_invite_item_id(); ?>">

										<?php bp_group_invite_user_avatar(); ?>

										<h4><?php bp_group_invite_user_link(); ?></h4>
										<span class="activity"><?php bp_group_invite_user_last_active(); ?></span>

										<div class="action">
											<a class="remove" href="<?php bp_group_invite_user_remove_invite_url(); ?>" id="<?php bp_group_invite_item_id(); ?>"><?php _e( 'Remove Invite', 'thatcamp' ); ?></a>
										</div>
									</li>

								<?php endwhile; ?>

								<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ); ?>

							<?php endif; ?>

							</ul>

						</div>

					<?php else : ?>

						<div id="message" class="info">
							<p><?php _e( 'Once you have built up friend connections you will be able to invite others to your group. You can send invites any time in the future by selecting the "Send Invites" option when viewing your new group.', 'thatcamp' ); ?></p>
						</div>

					<?php endif; ?>

					<?php wp_nonce_field( 'groups_create_save_group-invites' ); ?>

					<?php do_action( 'bp_after_group_invites_creation_step' ); ?>

				<?php endif; ?>

				<?php do_action( 'groups_custom_create_steps' ); ?>

				<?php do_action( 'bp_before_group_creation_step_buttons' ); ?>

				<?php if ( 'crop-image' != bp_get_avatar_admin_step() ) : ?>

					<div class="submit" id="previous-next">

						<?php if ( !bp_is_first_group_creation_step() ) : ?>

							<input type="button" value="<?php _e( 'Back to Previous Step', 'thatcamp' ); ?>" id="group-creation-previous" name="previous" onclick="location.href='<?php bp_group_creation_previous_link(); ?>'" />

						<?php endif; ?>
						<?php if ( !bp_is_last_group_creation_step() && !bp_is_first_group_creation_step() ) : ?>

							<input type="submit" value="<?php _e( 'Next Step', 'thatcamp' ); ?>" id="group-creation-next" name="save" />

						<?php endif;?>

						<?php if ( bp_is_first_group_creation_step() ) : ?>

							<input type="submit" value="<?php _e( 'Create Group and Continue', 'thatcamp' ); ?>" id="group-creation-create" name="save" />

						<?php endif; ?>

						<?php if ( bp_is_last_group_creation_step() ) : ?>

							<input type="submit" value="<?php _e( 'Finish', 'thatcamp' ); ?>" id="group-creation-finish" name="save" />

						<?php endif; ?>
					</div>

				<?php endif;?>

				<?php do_action( 'bp_after_group_creation_step_buttons' ); ?>

				<input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id(); ?>" />

				<?php do_action( 'bp_directory_groups_content' ); ?>

			</div>

			<?php do_action( 'bp_after_create_group' ); ?>

		</form>
		
		<?php do_action( 'bp_after_create_group_content_template' ); ?>

		</div>
	</div>

<?php get_sidebar( 'thatcamp' ); ?>
<?php get_footer( 'thatcamp' ); ?>
