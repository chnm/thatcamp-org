<?php

/**
 * BuddyPress - Users Activity
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

?>

<?php do_action( 'bp_before_member_activity_post_form' ); ?>

<?php get_template_part( 'members/single/content-header' ) ?>

<?php

if ( is_user_logged_in() && bp_is_my_profile() && ( !bp_current_action() || bp_is_current_action( 'just-me' ) ) && ! thatcamp_activity_type() )
	get_template_part( 'activity/post', 'form');

do_action( 'bp_after_member_activity_post_form' );
do_action( 'bp_before_member_activity_content' ); ?>

<div class="activity" role="main">

	<?php get_template_part( 'activity/activity', 'loop');  ?>

</div>

<?php do_action( 'bp_after_member_activity_content' ); ?>
