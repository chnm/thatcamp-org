<?php
/**
 * Sidebar BuddyPress
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<div id="sidebar" role="complementary">	
	<div id="object-nav" class="item-list-tabs no-ajax" role="navigation">
		<ul>
			<?php bp_get_displayed_user_nav(); ?>
			<?php do_action( 'bp_member_options_nav' ); ?>
		</ul>
	</div>
	<div id="profile-right" role="complementary">
		<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>
	<div id="subnav" class="item-list-tabs no-ajax" role="navigation">
	<ul>
		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'thatcamp' ); ?></label>
			<select id="activity-filter-by">
				<option value="-1"><?php _e( 'Everything', 'thatcamp' ); ?></option>
				<option value="activity_update"><?php _e( 'Updates', 'thatcamp' ); ?></option>

				<?php
				if ( !bp_is_current_action( 'groups' ) ) :
					if ( bp_is_active( 'blogs' ) ) : ?>

						<option value="new_blog_post"><?php _e( 'Posts', 'thatcamp' ); ?></option>
						<option value="new_blog_comment"><?php _e( 'Comments', 'thatcamp' ); ?></option>

					<?php
					endif;

					if ( bp_is_active( 'friends' ) ) : ?>

						<option value="friendship_accepted,friendship_created"><?php _e( 'Friendships', 'thatcamp' ); ?></option>

					<?php endif;

				endif;

				if ( bp_is_active( 'forums' ) ) : ?>

					<option value="new_forum_topic"><?php _e( 'Forum Topics', 'thatcamp' ); ?></option>
					<option value="new_forum_post"><?php _e( 'Forum Replies', 'thatcamp' ); ?></option>

				<?php endif;

				if ( bp_is_active( 'groups' ) ) : ?>

					<option value="created_group"><?php _e( 'New Groups', 'thatcamp' ); ?></option>
					<option value="joined_group"><?php _e( 'Group Memberships', 'thatcamp' ); ?></option>

				<?php endif;

				do_action( 'bp_member_activity_filter_options' ); ?>

			</select>
		</li>
		<?php bp_get_options_nav(); ?>
	</ul>
	</div>
	</div>
</div>
