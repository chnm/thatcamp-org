<?php

/**
 * BuddyPress - Members Directory
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

get_header( 'thatcamp' ); ?>

	<?php do_action( 'bp_before_directory_members_page' ); ?>

	
	<div id="primary" class="main-content">
	<div id="content" role="main">

		<?php do_action( 'bp_before_directory_members' ); ?>

			
		<div id="members-dir-list" class="members dir-list">

				<?php get_template_part( 'members/members', 'loop'); ?>

			</div>

		<?php do_action( 'bp_after_directory_members' ); ?>

		</div>
	</div>

	<?php do_action( 'bp_after_directory_members_page' ); ?>
<?php get_sidebar( 'members' ); ?>
<?php get_footer( 'thatcamp' ); ?>
