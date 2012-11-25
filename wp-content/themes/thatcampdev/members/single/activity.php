<?php

/**
 * BuddyPress - Users Activity
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

?>

<?php do_action( 'bp_before_member_activity_post_form' ); ?>

<div id="item-header">
	<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
		<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>
</div>
<div id="member-fields">
		<?php if ( $data = bp_get_profile_field_data( 'field=2' ) ) : ?>
	<div id="member-bio" class="feature-box">
		<p>
			<?php echo $data; ?>
		</p>
	</div>
	<?php endif ?>
	<?php if ( $data = bp_get_profile_field_data( 'field=4' ) ) : ?>
	<div id="member-twitter" class="profile-line">
		<span class="profile-titlemember">Twitter:</span><a href="http://twitter.com/<?php echo $data ?>" title="<?php echo $data ?>">
		@<?php echo $data; ?></a></span>
	</div>
	<?php endif ?>
		<?php if ( $data = bp_get_profile_field_data( 'field=3' ) ) : ?>
	<div id="member-website" class="profile-line">

		<span class="profile-titlemember">Website:</span><a href="http://<?php echo $data ?>" title="<?php echo $data ?>">
		<?php echo $data; ?></a></span>
	</div>
	<?php endif ?>
	<?php if ( $data = bp_get_profile_field_data( 'field=5' ) ) : ?>
	<div id="member-position" class="profile-line">

		<span class="profile-titlemember">Position/Job Title:</span><?php echo $data; ?>
	</div>
	<?php endif ?>
		<?php if ( $data = bp_get_profile_field_data( 'field=6' ) ) : ?>
	<div id="member-organisation" class="profile-line">
		<span class="profile-titlemember">Organisation:</span><?php echo $data; ?>
	</div>
	<?php endif ?>
		<?php if ( $data = bp_get_profile_field_data( 'field=7' ) ) : ?>
	<div id="member-camps" class="profile-line">
			<span class="profile-titlemember">Previous THATcamps:</span><?php echo $data; ?>
	</div>
	<?php endif ?>
</div>
<?php

if ( is_user_logged_in() && bp_is_my_profile() && ( !bp_current_action() || bp_is_current_action( 'just-me' ) ) )
get_template_part( 'activity/post', 'form');

do_action( 'bp_after_member_activity_post_form' );
do_action( 'bp_before_member_activity_content' ); ?>

<div class="activity" role="main">

	<?php get_template_part( 'activity/activity', 'loop');  ?>

</div>

<?php do_action( 'bp_after_member_activity_content' ); ?>
