<?php

/**
 * BuddyPress - Users Activity
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<?php do_action( 'bp_before_member_activity_post_form' ); ?>

<div id="item-header">
	<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
</div>
<div class="feature-box">
	<p>
		Vestibulum id ligula porta felis euismod semper. Nulla vitae elit libero, a pharetra augue. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cras mattis consectetur purus sit amet fermentum. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.
	</p>
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
