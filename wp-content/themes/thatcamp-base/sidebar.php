<?php
/**
 * Sidebar
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>	
<div id="sidebar" role="complementary">	
	<?php if ( is_active_sidebar( 'sidebar' ) ) { ?>
		<?php dynamic_sidebar( 'sidebar' ); ?>
	<?php }else{ ?>
		<aside class="widget">
			<h3 class="widgettitle"><?php _e('Meta', 'thatcampbase'); ?></h3>
			<ul>
				<?php wp_register(); ?>
				<?php wp_loginout(); ?>
				<?php wp_meta(); ?>
			</ul>
		</aside>
	<?php } ?>
</div>
