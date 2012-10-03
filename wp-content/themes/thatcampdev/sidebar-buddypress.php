<?php
/**
 * Sidebar BuddyPress
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<?php do_action( 'bp_before_sidebar' ); ?>
<div id="sidebar" role="complementary">	
	<?php do_action( 'bp_inside_before_sidebar' ); ?>
	<?php dynamic_sidebar( 'sidebar' ); ?>
	<?php do_action( 'bp_inside_after_sidebar' ); ?>
</div>
<?php do_action( 'bp_after_sidebar' ); ?>