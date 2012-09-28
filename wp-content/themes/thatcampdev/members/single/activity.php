<?php

/**
 * BuddyPress - Users Activity
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<?php do_action( 'bp_before_member_activity_post_form' ); ?>

<?php
//if ( is_user_logged_in() && bp_is_my_profile() && ( !bp_current_action() || bp_is_current_action( 'just-me' ) ) )
//	locate_template( array( 'activity/post-form.php'), true );

//do_action( 'bp_after_member_activity_post_form' );
do_action( 'bp_before_member_activity_content' ); ?>

<div class="activity" role="main">

	<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>

</div>

<?php do_action( 'bp_after_member_activity_content' ); ?>
