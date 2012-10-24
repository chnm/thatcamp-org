<?php

/**
 * BuddyPress - Users Profile
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<?php if ( bp_is_my_profile() ) : ?>

	<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
		<ul>

			<?php bp_get_options_nav(); ?>

		</ul>
	</div>

<?php endif; ?>

<?php do_action( 'bp_before_profile_content' ); ?>

<div class="profile" role="main">

	<?php
		if ( bp_is_current_action( 'edit' ) )
			get_template_part( 'members/single/profile/edit');

		elseif ( bp_is_current_action( 'change-avatar' ) )
			get_template_part( 'members/single/profile/change', 'avatar');

		elseif ( bp_is_active( 'xprofile' ) )
			get_template_part( 'members/single/profile/profile', 'loop');

		else
			get_template_part( 'members/single/profile/profile', 'wp');
	?>

</div>

<?php do_action( 'bp_after_profile_content' ); ?>