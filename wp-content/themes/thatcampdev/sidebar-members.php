<?php
/**
 * Sidebar BuddyPress
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<?php do_action( 'bp_before_sidebar' ); ?>
<div id="sidebar" role="complementary">	
	<form action="" method="post" id="members-directory-form" class="dir-form">

			<?php do_action( 'bp_before_directory_members_content' ); ?>

			<div id="members-dir-search" class="dir-search" role="search">

				<?php bp_directory_members_search_form(); ?>

			</div>

		
			<div class="item-list-tabs" id="subnav" role="navigation">
				<ul>

					<?php do_action( 'bp_members_directory_member_sub_types' ); ?>

					<li id="members-order-select" class="last filter">

						<label for="members-order-by" style="display:none;"><?php _e( 'Order By:', 'thatcamp' ); ?></label>
						<select id="members-order-by">
							<option value="active"><?php _e( 'Last Active', 'thatcamp' ); ?></option>
							<option value="newest"><?php _e( 'Newest Registered', 'thatcamp' ); ?></option>

							<?php if ( bp_is_active( 'xprofile' ) ) : ?>

								<option value="alphabetical"><?php _e( 'Alphabetical', 'thatcamp' ); ?></option>

							<?php endif; ?>

							<?php do_action( 'bp_members_directory_order_options' ); ?>

						</select>
					</li>
				</ul>
			</div>

			<?php do_action( 'bp_directory_members_content' ); ?>

			<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

			<?php do_action( 'bp_after_directory_members_content' ); ?>

		</form>
</div>
<?php do_action( 'bp_after_sidebar' ); ?>