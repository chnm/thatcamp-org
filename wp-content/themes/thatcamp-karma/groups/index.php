<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

$current_view = isset( $_GET['tctype'] ) && in_array( $_GET['tctype'], array( 'new', 'past', 'upcoming' ) ) ? $_GET['tctype'] : 'new';
$base_url = bp_get_root_domain() . '/' .  bp_get_groups_root_slug();
thatcamp_admin_scripts();

get_header( 'thatcamp' ); ?>

	<?php do_action( 'bp_before_directory_groups_page' ); ?>

	<div id="primary" class="main-content">
	<div id="content" role="main">

		<?php do_action( 'bp_before_directory_groups' ); ?>

		<form action="" method="get" id="groups-directory-form" class="dir-form">

			<h3><?php _e( 'THATCamps', 'thatcamp' ); ?></h3>

			<div class="tc-filters">
				<div class="tc-filter-type">
					<div class="tc-filter-label">Type:</div>
					<?php thatcamp_directory_selector() ?>
				</div>

				<div class="tc-filter-date">
					<div class="tc-filter-label">Date:</div>
					<div class="tc-filter-year">Year <?php thatcamp_year_dropdown() ?></div>
					<div class="tc-filter-month">Month <?php thatcamp_month_dropdown() ?></div>
				</div>

				<div class="tc-filter-location">
					<div class="tc-filter-label">Location:</div>
					<div class="tc-location-selector">
						<?php
						$location_args = array( 'context' => 'search' );
						foreach ( array( 'Country', 'State', 'Province' ) as $ltype ) {
							if ( ! empty( $_GET[ $ltype ] ) ) {
								$location_args[ strtolower( $ltype ) ] = stripslashes( urldecode( $_GET[ $ltype ] ) );
							}
						}
						?>
						<?php thatcamp_country_picker( $location_args ) ?>
					</div>
				</div>

				<input type="submit" value="Filter" />
			</div>


			<?php do_action( 'bp_before_directory_groups_content' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<div id="groups-dir-list" class="groups dir-list">

				<?php get_template_part( 'groups/groups', 'loop'); ?>

			</div>

			<?php do_action( 'bp_directory_groups_content' ); ?>

			<?php do_action( 'bp_after_directory_groups_content' ); ?>

		</form>

		<?php do_action( 'bp_after_directory_groups' ); ?>

		</div>
	</div>

	<?php do_action( 'bp_after_directory_groups_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'thatcamp' ); ?>

