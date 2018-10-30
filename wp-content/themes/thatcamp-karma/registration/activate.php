<?php get_header( 'thatcamp' ); ?>

	
	<div id="primary" class="main-content">
	<div id="content" role="main">

		<?php do_action( 'bp_before_activation_page' ); ?>

		<div class="page" id="activate-page">

			<?php if ( bp_account_was_activated() ) : ?>

				<h2 class="widgettitle"><?php _e( 'Account activated', 'thatcamp' ); ?></h2>

				<?php do_action( 'bp_before_activate_content' ); ?>

				<?php if ( isset( $_GET['e'] ) ) : ?>
					<p><?php _e( 'Your THATCamp account was activated successfully! Your account details have been sent to you in a separate email.', 'thatcamp' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'Your THATCamp account was activated successfully! You can now <a href="/wp-login.php">log in</a> with the username and password you provided when you signed up. Once you are logged in, you can edit your profile or register a new THATCamp.', 'thatcamp' ); ?></p>
				<?php endif; ?>

			<?php else : ?>

				<h3><?php _e( 'Activate your account', 'thatcamp' ); ?></h3>

				<?php do_action( 'bp_before_activate_content' ); ?>

				<p><?php _e( 'Please provide a valid activation key.', 'thatcamp' ); ?></p>

				<form action="" method="post" class="standard-form" id="activation-form">

					<label for="key"><?php _e( 'Activation key:', 'thatcamp' ); ?></label>
					<input type="text" name="key" id="key" value="<?php echo esc_attr( bp_get_current_activation_key() ); ?>" />

					<p class="submit">
						<input type="submit" name="submit" value="<?php _e( 'Activate', 'thatcamp' ); ?>" />
					</p>

				</form>

			<?php endif; ?>

			<?php do_action( 'bp_after_activate_content' ); ?>

		</div>

		<?php do_action( 'bp_after_activation_page' ); ?>

		</div>
	</div>
<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'thatcamp' ); ?>
