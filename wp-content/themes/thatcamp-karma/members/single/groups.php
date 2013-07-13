<?php

/**
 * BuddyPress - Users Groups
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

?>

<?php

if ( bp_is_current_action( 'invites' ) ) :
	get_template_part( 'members/single/groups/invites');

else :
	do_action( 'bp_before_member_groups_content' ); ?>

	<div class="groups mygroups">

	<?php get_template_part( 'members/single/content-header' ) ?>

		<?php get_template_part( 'groups/groups', 'loop');  ?>

	</div>

	<?php do_action( 'bp_after_member_groups_content' ); ?>

<?php endif; ?>
