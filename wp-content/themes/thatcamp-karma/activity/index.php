<?php

/**
 * Template Name: BuddyPress - Activity Directory
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

get_header( 'thatcamp' ); ?>

	<?php do_action( 'bp_before_directory_activity_page' ); ?>
	<div id="primary" class="main-content">
	<div id="content">
	<div id="page" class="feature-box" role="main">

	<h1 class="post-title red-text">Activity</h1>
			<?php do_action( 'bp_before_directory_activity' ); ?>

			<?php do_action( 'bp_before_directory_activity_content' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<?php do_action( 'bp_before_directory_activity_list' ); ?>

			<div class="activity" role="main">
				<?php get_template_part( 'activity/activity', 'loop'); ?>
			</div>

			<?php do_action( 'bp_after_directory_activity_list' ); ?>

			<?php do_action( 'bp_directory_activity_content' ); ?>

			<?php do_action( 'bp_after_directory_activity_content' ); ?>

			<?php do_action( 'bp_after_directory_activity' ); ?>

		</div>
		</div>
	</div>

	<?php do_action( 'bp_after_directory_activity_page' ); ?>

<?php get_sidebar( 'activity' ); ?>
<?php get_footer( 'thatcamp' ); ?>
