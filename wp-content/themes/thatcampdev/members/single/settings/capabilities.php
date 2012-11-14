<?php

/**
 * BuddyPress Delete Account
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


				<h3><?php _e( 'Capabilities', 'thatcamp' ); ?></h3>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/capabilities/'; ?>" name="account-capabilities-form" id="account-capabilities-form" class="standard-form" method="post">

					<?php do_action( 'bp_members_capabilities_account_before_submit' ); ?>

					<label>
						<input type="checkbox" name="user-spammer" id="user-spammer" value="1" <?php checked( bp_is_user_spammer( bp_displayed_user_id() ) ); ?> />
						 <?php _e( 'This user is a spammer.', 'thatcamp' ); ?>
					</label>

					<div class="submit">
						<input type="submit" value="<?php _e( 'Save', 'thatcamp' ); ?>" id="capabilities-submit" name="capabilities-submit" />
					</div>

					<?php do_action( 'bp_members_capabilities_account_after_submit' ); ?>

					<?php wp_nonce_field( 'capabilities' ); ?>

				</form>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div>

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div>
	</div>
<?php get_footer( 'thatcamp' ); ?>