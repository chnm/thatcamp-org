<?php

/**
 * BuddyPress Delete Account
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */
?>

<?php get_header( 'thatcamp' ); ?>

	
<?php get_sidebar( 'profile' ); ?>
	<div id="primary" class="main-content">
	<div id="content" class="clearfix" role="main">

			<?php do_action( 'bp_before_member_settings_template' ); ?>

			<div id="item-body" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div>

				<h3><?php _e( 'Delete Account', 'thatcamp' ); ?></h3>

				<div id="message" class="info">
					
					<?php if ( bp_is_my_profile() ) : ?>

						<p><?php _e( 'Deleting your account will delete all of the content you have created. It will be completely irrecoverable.', 'thatcamp' ); ?></p>
						
					<?php else : ?>

						<p><?php _e( 'Deleting this account will delete all of the content it has created. It will be completely irrecoverable.', 'thatcamp' ); ?></p>

					<?php endif; ?>

				</div>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account'; ?>" name="account-delete-form" id="account-delete-form" class="standard-form" method="post">

					<?php do_action( 'bp_members_delete_account_before_submit' ); ?>

					<label>
						<input type="checkbox" name="delete-account-understand" id="delete-account-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-account-button').disabled = ''; } else { document.getElementById('delete-account-button').disabled = 'disabled'; }" />
						 <?php _e( 'I understand the consequences.', 'thatcamp' ); ?>
					</label>

					<div class="submit">
						<input type="submit" disabled="disabled" value="<?php _e( 'Delete Account', 'thatcamp' ); ?>" id="delete-account-button" name="delete-account-button" />
					</div>

					<?php do_action( 'bp_members_delete_account_after_submit' ); ?>

					<?php wp_nonce_field( 'delete-account' ); ?>

				</form>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div>

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		</div>
	</div>
<?php get_footer( 'thatcamp' ); ?>