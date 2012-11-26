<?php
/**
 * Sidebar BuddyPress
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<div id="sidebar" role="complementary">	
	<div id="profile-right" role="complementary">
		<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>
		<div id="item-nav">
			<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
				<ul>

					<?php bp_get_displayed_user_nav(); ?>

					<?php do_action( 'bp_member_options_nav' ); ?>

				</ul>
			</div>
		</div><!-- #item-nav -->
		
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

		
	<div id="subnav" class="item-list-tabs no-ajax" role="navigation">
			<ul>
		<?php if ( bp_is_user_activity()): ?>
		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'thatcamp' ); ?></label>
			<select id="activity-filter-by">
				<option value="-1"><?php _e( 'Everything', 'thatcamp' ); ?></option>

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

				do_action( 'bp_member_activity_filter_options' ); ?>

			</select>
		</li>
	<?php endif; ?>

	<?php if ( bp_is_my_profile() ) : ?>
		<?php bp_get_options_nav(); ?>
	<?php endif; ?>
	</ul>
	</div>
	</div>
</div>
