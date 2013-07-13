<?php
/**
 * Home page template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 * Template Name: Home Template
 */
?>
<?php get_header('front'); ?>
<div id="primary" class="main-content">
	<div id="content" role="main">
		<div id="upcoming-camps" class="feature-box">
			<h2><?php _e( 'Upcoming THATCamps', 'thatcamp' ); ?></h2>

			<?php /* Set up the groups loop */ ?>
			<?php /* We do two separate loops: One for upcoming camps with dates,
			         and one for those without any dates */ ?>
			<?php

			$meta_filter_args = array(
				'filters' => array(
					'thatcamp_date' => false, // doesn't matter what it is
				),
				'orderby' => 'thatcamp_date',
				'order'   => 'ASC',
			);

			$meta_filter = new BP_Groups_Meta_Filter( $meta_filter_args );

			$group_args = array(
				'per_page' => null
			);
			?>

			<?php if ( bp_has_groups( $group_args ) ) : while ( bp_groups() ) : bp_the_group() ?>

			<?php /* Hack alert: Instead of querying intelligently, just skip old camps */ ?>
			<?php if ( thatcamp_is_in_the_future()) : ?>
				<article class="camp-listitem">
					<div class="camp-listdate"><?php echo thatcamp_get_camp_date_pretty() ?></div>

					<div class="camp-listinfo">
						<h3><a href="<?php thatcamp_camp_permalink() ?>" class="camplink"><?php bp_group_name() ?></a></h3>
						<div class="item-desc">
							<?php echo thatcamp_get_location( bp_get_group_id(), 'pretty' ) ?>
						</div>
					</div>
				</article>
			<?php endif ?>

			<?php endwhile; endif ?>

			<?php $meta_filter->remove_filters() ?>

			<?php /* Round two */ ?>

			<?php

			$all_group_args = array(
				'per_page' => null,
				'include'  => thatcamp_groups_without_dates(),
			);

			?>

			<?php if ( bp_has_groups( $all_group_args ) ) : while ( bp_groups() ) : bp_the_group() ?>
				<article class="camp-listitem">
					<div class="camp-listdate">TBA</div>

					<div class="camp-listinfo">
						<h3><a href="<?php thatcamp_camp_permalink() ?>" class="camplink"><?php bp_group_name() ?></a></h3>
						<div class="item-desc">
							<?php echo thatcamp_get_location( bp_get_group_id(), 'pretty' ) ?>
						</div>
					</div>
				</article>
			<?php endwhile; endif ?>


			<a href="<?php echo site_url(); ?>/camps" class="button campbutton offset">
				<span class="button-inner"><?php _e( 'View all THATCamps', 'thatcamp' ); ?></span>
			</a>


		</div>
	</div>
</div>
<?php get_sidebar('home'); ?>
<?php get_footer() ?>

