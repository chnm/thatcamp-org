<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

$current_view = isset( $_GET['tctype'] ) && in_array( $_GET['tctype'], array( 'new', 'past', 'upcoming' ) ) ? $_GET['tctype'] : 'new';
$base_url = bp_get_root_domain() . '/' .  bp_get_groups_root_slug();

get_header( 'thatcamp' ); ?>

	<?php do_action( 'bp_before_directory_groups_page' ); ?>

	<div id="primary" class="main-content">
	<div id="content" role="main">

		<?php do_action( 'bp_before_directory_groups' ); ?>

		<form action="" method="post" id="groups-directory-form" class="dir-form">

			<h3><?php _e( 'THATCamps', 'thatcamp' ); ?></h3>

			<ul class="tc-selector">
				<?php thatcamp_directory_selector( 'new' ) ?>
				<?php thatcamp_directory_selector( 'past' ) ?>
				<?php thatcamp_directory_selector( 'upcoming' ) ?>
			</ul>

			<?php do_action( 'bp_before_directory_groups_content' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<div id="groups-dir-list" class="groups dir-list">

				<?php get_template_part( 'groups/groups', 'loop'); ?>

			</div>

			<?php do_action( 'bp_directory_groups_content' ); ?>

			<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

			<?php do_action( 'bp_after_directory_groups_content' ); ?>

		</form>

		<?php do_action( 'bp_after_directory_groups' ); ?>

		</div>
	</div>

	<?php do_action( 'bp_after_directory_groups_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'thatcamp' ); ?>

