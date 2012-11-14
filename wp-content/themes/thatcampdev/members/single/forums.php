<?php

/**
 * BuddyPress - Users Forums
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>


<?php

if ( bp_is_current_action( 'favorites' ) ) :
	get_template_part( 'members/single/forums/topics'); 

else :
	do_action( 'bp_before_member_forums_content' ); ?>

	<div class="forums myforums">

		<?php get_template_part( 'forums/forums', 'loop'); ?>

	</div>

	<?php do_action( 'bp_after_member_forums_content' ); ?>

<?php endif; ?>
