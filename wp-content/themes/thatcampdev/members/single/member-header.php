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
		<a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a>
	</h4>
	
</div>

<div id="item-header-content">
	<?php do_action( 'bp_before_member_header_meta' ); ?>
	<div id="item-meta">
		<div id="item-buttons">
			<?php do_action( 'bp_member_header_actions' ); ?>
		</div>
		<?php
		 do_action( 'bp_profile_header_meta' );
		 ?>
	</div>
</div>

<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>