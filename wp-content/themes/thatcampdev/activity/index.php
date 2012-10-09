<?php

/**
 * Template Name: BuddyPress - Activity Directory
 *
 * @package logicalbones
 * @since logicalbones 1.0
 */

get_header( 'thatcamp' ); ?>

	<?php do_action( 'bp_before_directory_activity_page' ); ?>
	<div id="primary" class="main-content">
	<div id="content" role="main">

			<?php do_action( 'bp_before_directory_activity' ); ?>

			<?php do_action( 'bp_before_directory_activity_content' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<?php do_action( 'bp_before_directory_activity_list' ); ?>

			<div class="activity" role="main">

				<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>

			</div>

			<?php do_action( 'bp_after_directory_activity_list' ); ?>

			<?php do_action( 'bp_directory_activity_content' ); ?>

			<?php do_action( 'bp_after_directory_activity_content' ); ?>

			<?php do_action( 'bp_after_directory_activity' ); ?>

		</div>
	</div>

	<?php do_action( 'bp_after_directory_activity_page' ); ?>

<?php get_sidebar( 'dummycontent' ); ?>
<?php get_footer( 'thatcamp' ); ?>
