<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

$current_view = isset( $_GET['tctype'] ) && in_array( $_GET['tctype'], array( 'alphabetical', 'past', 'upcoming' ) ) ? $_GET['tctype'] : 'alphabetical';
$base_url = bp_get_root_domain() . '/' .  bp_get_groups_root_slug();

get_header( 'thatcamp' ); ?>

	<?php do_action( 'bp_before_directory_groups_page' ); ?>


	<div id="primary" class="main-content">
	<div id="content" role="main">

		<?php do_action( 'bp_before_directory_groups' ); ?>

		<form action="" method="post" id="groups-directory-form" class="dir-form">

			<h3><?php _e( 'THATCamps', 'thatcamp' ); ?></h3>

			<ul class="tc-selector">
				<li<?php if ( 'alphabetical' == $current_view ) : ?> class="current"<?php endif ?>><a href="<?php echo add_query_arg( 'tctype', 'alphabetical', $base_url ) ?>">Alphabetical</a></li>
				<li<?php if ( 'past' == $current_view ) : ?> class="current"<?php endif ?>><a href="<?php echo add_query_arg( 'tctype', 'past', $base_url ) ?>">Past</a></li>
				<li<?php if ( 'upcoming' == $current_view ) : ?> class="current"<?php endif ?>><a href="<?php echo add_query_arg( 'tctype', 'upcoming', $base_url ) ?>">Upcoming</a></li>
				<li<?php if ( 'alphabetical' == $current_view ) : ?> class="current"<?php endif ?>><a href="<?php echo add_query_arg( 'tctype', 'alphabetical', $base_url ) ?>">Alphabetical</a></li>
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

