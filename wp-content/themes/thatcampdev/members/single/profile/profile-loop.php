<div id="member-fields">
	<?php if ( $data = get_user_meta( bp_displayed_user_id(), 'description', true ) ) : ?>
	<div id="member-bio" class="feature-box">
		<p>
			<?php echo $data; ?>
		</p>
	</div>
	<?php endif ?>

	<?php if ( $data = thatcamp_get_user_data( bp_displayed_user_id(), 'user_twitter' ) ) : ?>
	<div id="member-twitter" class="profile-line">
		<span class="profile-titlemember">Twitter:</span><a href="http://twitter.com/<?php echo $data ?>" title="<?php echo $data ?>">
		@<?php echo $data; ?></a></span>
	</div>
	<?php endif ?>

	<?php if ( $data = thatcamp_get_user_data( bp_displayed_user_id(), 'user_url' ) ) : ?>
	<div id="member-website" class="profile-line">

		<span class="profile-titlemember">Website:</span><a href="<?php echo esc_url( $data ) ?>" title="<?php echo $data ?>">
		<?php echo $data; ?></a></span>
	</div>
	<?php endif ?>

	<?php if ( $data = get_user_meta( bp_displayed_user_id(), 'user_title', true ) ) : ?>
	<div id="member-position" class="profile-line">

		<span class="profile-titlemember">Position/Job Title:</span><?php echo $data; ?>
	</div>
	<?php endif ?>

	<?php if ( $data = get_user_meta( bp_displayed_user_id(), 'user_organization', true ) ) : ?>
	<div id="member-organisation" class="profile-line">
		<span class="profile-titlemember">Organization:</span><?php echo $data; ?>
	</div>
	<?php endif ?>

	<?php if ( $data = get_user_meta( bp_displayed_user_id(), 'previous_thatcamps', true ) ) : ?>
		<?php if ( 'Select an answer' != $data ) : ?>
			<div id="member-camps" class="profile-line">
				<span class="profile-titlemember">Previous THATCamps:</span><?php echo $data; ?>
			</div>
		<?php endif ?>
	<?php endif ?>
</div>
