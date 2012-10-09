<?php
/**
 * Sidebar Home
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<?php do_action( 'bp_before_sidebar' ); ?>
<div id="sidebar" role="complementary">	
	<?php do_action( 'bp_inside_before_sidebar' ); ?>
	<!-- demo sidebar content -->

	<a class="email-thatcamplink">Contact Us</a>
	
	<a class="rss-thatcamplink">RSS</a>
	

	<?php if ( is_active_sidebar( 'sidebar-home' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-home' ); ?>
	<?php endif; ?>
	
	
	<?php do_action( 'bp_inside_after_sidebar' ); ?>
</div>
<?php do_action( 'bp_after_sidebar' ); ?>