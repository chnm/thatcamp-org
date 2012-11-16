<?php

/**
 * BuddyPress - Users Blogs
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */

?>

<div class="item-list-tabs" id="subnav" role="navigation">
	<ul>

		<?php bp_get_options_nav(); ?>

		<li id="blogs-order-select" class="last filter">

			<label for="blogs-all"><?php _e( 'Order By:', 'thatcamp' ); ?></label>
			<select id="blogs-all">
				<option value="active"><?php _e( 'Last Active', 'thatcamp' ); ?></option>
				<option value="newest"><?php _e( 'Newest', 'thatcamp' ); ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'thatcamp' ); ?></option>

				<?php do_action( 'bp_member_blog_order_options' ); ?>

			</select>
		</li>
	</ul>
</div>

<?php do_action( 'bp_before_member_blogs_content' ); ?>

<div class="blogs myblogs" role="main">

	<?php get_template_part( 'blogs/blogs', 'loop');   ?>

</div>

<?php do_action( 'bp_after_member_blogs_content' ); ?>
