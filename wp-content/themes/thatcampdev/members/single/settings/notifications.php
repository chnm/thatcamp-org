<?php

/**
 * BuddyPress Notification Settings
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

get_header( 'thatcamp' ); ?>

	
<?php get_sidebar( 'profile' ); ?>
	<div id="primary" class="main-content">
	<div id="content" role="main">
			<?php do_action( 'bp_before_member_settings_template' ); ?>

			<div id="item-body" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div>

				<h3><?php _e( 'Email Notification', 'thatcamp' ); ?></h3>

				<?php do_action( 'bp_template_content' ); ?>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
					<p><?php _e( 'Send a notification by email when:', 'thatcamp' ); ?></p>

					<?php do_action( 'bp_notification_settings' ); ?>

					<?php do_action( 'bp_members_notification_settings_before_submit' ); ?>

					<div class="submit">
						<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'thatcamp' ); ?>" id="submit" class="auto" />
					</div>

					<?php do_action( 'bp_members_notification_settings_after_submit' ); ?>

					<?php wp_nonce_field('bp_settings_notifications'); ?>

				</form>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div>

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div>
	</div>
<?php get_footer( 'thatcamp' ); ?>