<?php

/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

?>

<?php do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

			<form action="" method="post" id="members-directory-form" class="dir-form">

		<h1 class="post-title red-text"><?php _e( 'People', 'thatcamp' ); ?></h1>

			<div id="members-dir-search" class="dir-search" role="search">

				<?php
				$default_search_value = bp_get_search_default_text( 'members' );
				$search_value         = !empty( $_REQUEST['msearch'] ) ? stripslashes( $_REQUEST['msearch'] ) : $default_search_value;
				?>

				<form action="" method="get" id="search-members-form">
					<label><input type="text" name="msearch" id="members_search" placeholder="Find people" /></label>
					<input type="submit" id="members_search_submit" name="members_search_submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
				</form>
			</div>
		</form>
	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="member-dir-count-top">

           		<?php bp_members_pagination_count(); ?>

       		 </div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>
	</div>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<ul id="members-list" class="item-list" role="main">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li>
			<div class="item-avatar">
				<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar('type=full&width=140&height=140'); ?></a>
				<h4><a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></h4>
			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>


	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">

           		<?php bp_members_pagination_count(); ?>

       		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'thatcamp' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
