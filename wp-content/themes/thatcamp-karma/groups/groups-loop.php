<?php

/**
 * BuddyPress - Groups Loop
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php buddypress()->groups->main_group_loop = true; ?>
<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pagination-links" id="group-dir-pag-top">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<?php while ( bp_groups() ) : bp_the_group() ?>
		<article class="camp-listitem">
			<div class="camp-listdate"><?php echo thatcamp_get_camp_date_pretty() ?></div>

			<div class="camp-listinfo">
				<h3><a href="<?php thatcamp_camp_permalink() ?>" class="camplink"><?php bp_group_name() ?></a></h3>
				<div class="item-desc">
                                        <?php if ( $location = thatcamp_get_location( bp_get_group_id(), 'pretty' ) ) : ?>
                                                <?php echo $location . '<br />'; ?>
                                        <?php endif ?>

					<?php thatcamp_get_camp_date( bp_get_group_id, 'unix' ) ?>
					<?php thatcamp_camp_description() ?>
				</div>
			</div>
		</article>
	<?php endwhile ?>

	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-dir-count-bottom">

			<?php bp_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-bottom">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'thatcamp' ); ?></p>
	</div>

<?php endif; ?>

<?php buddypress()->groups->main_group_loop = false; ?>
<?php do_action( 'bp_after_groups_loop' ); ?>
