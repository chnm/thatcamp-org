<?php

/**
 * BuddyPress - Users Friends
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<?php

if ( bp_is_current_action( 'requests' ) ) :
	get_template_part( 'members/single/friends/requests'); 

else :
	do_action( 'bp_before_member_friends_content' ); ?>

	<div class="members friends">

		<?php get_template_part( 'members/members', 'loop'); ?>

	</div>

	<?php do_action( 'bp_after_member_friends_content' ); ?>

<?php endif; ?>
