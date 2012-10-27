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
		<?php if ( is_active_sidebar( 'sidebar-stream' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-stream' ); ?>
		<?php endif; ?>
	<?php do_action( 'bp_inside_after_sidebar' ); ?>
</div>
<?php do_action( 'bp_after_sidebar' ); ?>
