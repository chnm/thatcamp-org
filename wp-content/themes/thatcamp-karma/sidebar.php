<?php
/**
 * Sidebar
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<?php do_action( 'bp_before_sidebar' ); ?>
<div id="sidebar" role="complementary">	
	<?php do_action( 'bp_inside_before_sidebar' ); ?>
	<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar' ); ?>
	<?php endif; ?>
	
	<div id="twitterwidget-wrapper">
		<?php if ( is_active_sidebar( 'sidebar-twitter' ) ) : ?>
			<?php dynamic_sidebar( 'sidebar-twitter' ); ?>
		<?php endif; ?>
		<a href="https://twitter.com/thatcamp" class="button socialbutton">
			<span class="button-inner"><?php _e( 'Follow Us', 'thatcamp' ); ?></span>
		</a>
	</div>
	<?php do_action( 'bp_inside_after_sidebar' ); ?>
</div>
<?php do_action( 'bp_after_sidebar' ); ?>