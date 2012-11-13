<?php

/**
 * BuddyPress - Users Header
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar">
	<a href="<?php bp_displayed_user_link(); ?>">
		<?php bp_displayed_user_avatar( 'width=140&height=140' ); ?>
	</a>
	<h4>
		<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
		<span class="activity"><?php bp_member_last_active(); ?></span>
	</h4>
	
</div>

<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>