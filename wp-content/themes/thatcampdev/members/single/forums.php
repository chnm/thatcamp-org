<?php

/**
 * BuddyPress - Users Forums
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php bp_get_options_nav(); ?>

		<li id="forums-order-select" class="last filter">

			<label for="forums-order-by"><?php _e( 'Order By:', 'thatcamp' ); ?></label>
			<select id="forums-order-by">
				<option value="active"><?php _e( 'Last Active', 'thatcamp' ); ?></option>
				<option value="popular"><?php _e( 'Most Posts', 'thatcamp' ); ?></option>
				<option value="unreplied"><?php _e( 'Unreplied', 'thatcamp' ); ?></option>

				<?php do_action( 'bp_forums_directory_order_options' ); ?>

			</select>
		</li>
	</ul>
</div>
<?php

if ( bp_is_current_action( 'favorites' ) ) :
	get_template_part( 'members/single/forums/topics'); 

else :
	do_action( 'bp_before_member_forums_content' ); ?>

	<div class="forums myforums">

		<?php get_template_part( 'forums/forums', 'loop'); 

	</div>

	<?php do_action( 'bp_after_member_forums_content' ); ?>

<?php endif; ?>
