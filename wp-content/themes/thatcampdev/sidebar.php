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
	<?php if ( is_active_sidebar( 'sidebar' ) ) { ?>
		<?php dynamic_sidebar( 'sidebar' ); ?>
	<?php }else{ ?>
		<aside class="widget">
			<h3 class="widgettitle"><?php _e('Meta', 'thatcamp'); ?></h3>
			<ul>
				<?php wp_register(); ?>
				<?php wp_loginout(); ?>
				<?php wp_meta(); ?>
			</ul>
		</aside>
	<?php } ?>
	<?php do_action( 'bp_inside_after_sidebar' ); ?>
</div>
<?php do_action( 'bp_after_sidebar' ); ?>