<?php get_header(); ?>

	<div id="primary" class="main-content">
	<div id="content" class="clearfix feature-box thatcamp-signup" role="main">
	 
		<?php do_action( 'bp_before_register_page' ); ?>

		<div id="page" class="register-page">

			<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

			<?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>
				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'bp_before_registration_disabled' ); ?>

					<p><?php _e( 'User registration is currently not allowed.', 'thatcamp' ); ?></p>

				<?php do_action( 'bp_after_registration_disabled' ); ?>
			<?php endif; ?>

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

				<h1 class="post-title red-text"><?php _e( 'Sign up for an account', 'thatcamp' ); ?></h1>

				<?php do_action( 'template_notices' ); ?>

				<p><?php _e( 'Signing up for a THATCamp account here will give you a profile on the main site at thatcamp.org only. To gain access to a particular THATCamp website, you will need to register to attend that THATCamp through its own website -- see the <a href="http://thatcamp.org">home page</a> or the <a href="http://thatcamp.org/camps">THATCamp Directory</a> for links to individual THATCamp websites. All information below is <strong>required</strong>.', 'thatcamp' ); ?></p>

				<?php do_action( 'bp_before_account_details_fields' ); ?>

				<div class="register-section" id="basic-details-section">

					<label for="signup_username"><?php _e( 'Username', 'thatcamp' ); ?></label>
					<small><?php _e('htubman, fdouglass, alincoln', 'thatcamp' ); ?></small>
					<?php do_action( 'bp_signup_username_errors' ); ?>
					<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>" />

					<label for="signup_email"><?php _e( 'Email address', 'thatcamp' ); ?></label>
					<small><?php _e('harriet-tubman@gmail.com', 'thatcamp' ); ?></small><br />
					<?php do_action( 'bp_signup_email_errors' ); ?>
					<input type="text" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" />
					<small>Due to spam user registrations, we have disabled user account creation for hotmail.com, outlook.com, and live.com email addresses.</small>

					<label for="signup_password"><?php _e( 'Choose a password', 'thatcamp' ); ?></label>
					<?php do_action( 'bp_signup_password_errors' ); ?>
					<input type="password" name="signup_password" id="signup_password" value="" />

					<label for="signup_password_confirm"><?php _e( 'Confirm password', 'thatcamp' ); ?></label>
					<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
					<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />
					
					<?php /* honeypot */ ?>
					<div id="thatcamp-zip-code">
					<label for="zip-code">Don't fill this field in - it's a trick for robots.
						<input type="text" name="zip-code" />
					</label>
					</div>

				</div>

				<?php do_action( 'bp_after_account_details_fields' ); ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<?php do_action( 'bp_before_signup_profile_fields' ); ?>

					<div class="register-section" id="profile-details-section">

						<?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( 'profile_group_id=1' ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

						<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

							<div class="editfield">

								<?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>

									<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php endif; ?></label>
									<small><?php _e('Harriet Tubman, Frederick Douglass, Abe Lincoln', 'thatcamp' ); ?></small>

									<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); ?>
									<input type="text" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" value="<?php bp_the_profile_field_edit_value(); ?>" />

								<?php endif; ?>

								<?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

									<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'thatcamp' ); ?><?php endif; ?></label>
									<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); ?>
									<textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_edit_value(); ?></textarea>

								<?php endif; ?>

								<?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>

									<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'thatcamp' ); ?><?php endif; ?></label>
									<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); ?>
									<select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>">
										<?php bp_the_profile_field_options(); ?>
									</select>

								<?php endif; ?>

								<?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>

									<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'thatcamp' ); ?><?php endif; ?></label>
									<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); ?>
									<select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" multiple="multiple">
										<?php bp_the_profile_field_options(); ?>
									</select>

								<?php endif; ?>

								<?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>

									<div class="radio">
										<span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'thatcamp' ); ?><?php endif; ?></span>

										<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); ?>
										<?php bp_the_profile_field_options(); ?>

										<?php if ( !bp_get_the_profile_field_is_required() ) : ?>
											<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'thatcamp' ); ?></a>
										<?php endif; ?>
									</div>

								<?php endif; ?>

								<?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>

									<div class="checkbox">
										<span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'thatcamp' ); ?><?php endif; ?></span>

										<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); ?>
										<?php bp_the_profile_field_options(); ?>
									</div>

								<?php endif; ?>

								<?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>

									<div class="datebox">
										<label for="<?php bp_the_profile_field_input_name(); ?>_day"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'thatcamp' ); ?><?php endif; ?></label>
										<?php do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' ); ?>

										<select name="<?php bp_the_profile_field_input_name(); ?>_day" id="<?php bp_the_profile_field_input_name(); ?>_day">
											<?php bp_the_profile_field_options( 'type=day' ); ?>
										</select>

										<select name="<?php bp_the_profile_field_input_name(); ?>_month" id="<?php bp_the_profile_field_input_name(); ?>_month">
											<?php bp_the_profile_field_options( 'type=month' ); ?>
										</select>

										<select name="<?php bp_the_profile_field_input_name(); ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year">
											<?php bp_the_profile_field_options( 'type=year' ); ?>
										</select>
									</div>

								<?php endif; ?>
								
								<?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
									<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
										<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'thatcamp' ), bp_get_the_profile_field_visibility_level_label() ) ?> <a href="#" class="visibility-toggle-link">Change</a>
									</p>
									
									<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
										<fieldset>
											<legend><?php _e( 'Who can see this field?', 'thatcamp' ) ?></legend>
										
											<?php bp_profile_visibility_radio_buttons() ?>
										
										</fieldset>
										<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'thatcamp' ) ?></a>
										
									</div>
								<?php else : ?>
									<p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
										<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'thatcamp' ), bp_get_the_profile_field_visibility_level_label() ) ?>
									</p>			
								<?php endif ?>


								<?php do_action( 'bp_custom_profile_edit_fields' ); ?>

								<p class="description"><?php bp_the_profile_field_description(); ?></p>

							</div>

						<?php endwhile; ?>

						<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_group_field_ids(); ?>" />

						<?php endwhile; endif; endif; ?>

					</div>

					<?php do_action( 'bp_after_signup_profile_fields' ); ?>

				<?php endif; ?>

				<?php if ( bp_get_blog_signup_allowed() ) : ?>

					<?php do_action( 'bp_before_blog_details_fields' ); ?>

					<div class="register-section" id="blog-details-section">

						<h4><?php _e( 'Blog Details', 'thatcamp' ); ?></h4>

						<p><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'thatcamp' ); ?></p>

						<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

							<label for="signup_blog_url"><?php _e( 'Blog URL', 'thatcamp' ); ?> <?php _e( '(required)', 'thatcamp' ); ?></label>
							<?php do_action( 'bp_signup_blog_url_errors' ); ?>

							<?php if ( is_subdomain_install() ) : ?>
								http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" /> .<?php bp_blogs_subdomain_base(); ?>
							<?php else : ?>
								<?php echo site_url(); ?>/ <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
							<?php endif; ?>

							<label for="signup_blog_title"><?php _e( 'Site Title', 'thatcamp' ); ?> <?php _e( '(required)', 'thatcamp' ); ?></label>
							<?php do_action( 'bp_signup_blog_title_errors' ); ?>
							<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />

							<span class="label"><?php _e( 'I would like my site to appear in search engines, and in public listings around this network.', 'thatcamp' ); ?>:</span>
							<?php do_action( 'bp_signup_blog_privacy_errors' ); ?>

							<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'thatcamp' ); ?></label>
							<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'thatcamp' ); ?></label>

						</div>

					</div>

					<?php do_action( 'bp_after_blog_details_fields' ); ?>

				<?php endif; ?>
				
				<?php do_action( 'bp_before_registration_submit_buttons' ); ?>
				
<!--				<div class="g-recaptcha" data-sitekey="6LdNAlYUAAAAAM9YT5umDOnnQO9g48HieHB7AaxZ"></div> -->

				<div class="submit">
					<input type="submit" name="signup_submit" id="signup_submit" value="<?php _e( 'Sign up', 'thatcamp' ); ?>" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ); ?>

				<?php wp_nonce_field( 'bp_new_signup' ); ?>

			<?php endif; ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<h2><?php _e( 'Sign Up Complete!', 'thatcamp' ); ?></h2>

				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'bp_before_registration_confirmed' ); ?>

				<?php if ( bp_registration_needs_activation() ) : ?>
					<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'thatcamp' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'thatcamp' ); ?></p>
				<?php endif; ?>

				<?php do_action( 'bp_after_registration_confirmed' ); ?>

			<?php endif; ?>

			<?php do_action( 'bp_custom_signup_steps' ); ?>

			</form>

		</div>

		<?php do_action( 'bp_after_register_page' ); ?>

		</div>
	</div>

	<?php get_sidebar( 'thatcamp' ); ?>

	<script type="text/javascript">
		jQuery(document).ready( function() {
			if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
				jQuery('div#blog-details').toggle();

			jQuery( 'input#signup_with_blog' ).click( function() {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>

<?php get_footer( 'thatcamp' ); ?>
