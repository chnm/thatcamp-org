<?php

/**
 * BuddyPress - Users Friends
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php if ( bp_is_my_profile() ) bp_get_options_nav(); ?>

		<?php if ( !bp_is_current_action( 'requests' ) ) : ?>

			<li id="members-order-select" class="last filter">

				<label for="members-friends"><?php _e( 'Order By:', 'thatcamp' ); ?></label>
				<select id="members-friends">
					<option value="active"><?php _e( 'Last Active', 'thatcamp' ); ?></option>
					<option value="newest"><?php _e( 'Newest Registered', 'thatcamp' ); ?></option>
					<option value="alphabetical"><?php _e( 'Alphabetical', 'thatcamp' ); ?></option>

					<?php do_action( 'bp_member_blog_order_options' ); ?>

				</select>
			</li>

		<?php endif; ?>

	</ul>
</div>

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
