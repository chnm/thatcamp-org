<?php

/**
 * BuddyPress - Groups Loop
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pagination-links" id="group-dir-pag-top">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list" role="main">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li>
			
			<div class="item">
				<div class="item-visit"><a href="<?php thatcamp_camp_permalink(); ?>" class="button campbutton">Visit Camp</a>
					<?php do_action( 'bp_directory_groups_actions' ); ?>
				</div>
				<div class="item-title"><a href="<?php thatcamp_camp_permalink(); ?>"><?php bp_group_name(); ?></a></div>
			
				<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>

				<?php do_action( 'bp_directory_groups_item' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

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

<?php do_action( 'bp_after_groups_loop' ); ?>
