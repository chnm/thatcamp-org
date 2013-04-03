<?php
/**
 * Sidebar
 *
 * @package notecamp
 * @since notecamp 1.0
 */
?>	
<div id="sidebar" role="complementary">	
	<nav id="top-nav" role="navigation">
					<h3 class="assistive-text"><?php _e( 'Menu', 'notecamp' ); ?></h3>
					<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'notecamp' ); ?>"><?php _e( 'Skip to content', 'notecamp' ); ?></a></div>
					<?php wp_nav_menu( array(
						'theme_location' => 'top', 
						'menu_class' => 'top_menu',
						'container' => ''
					)); ?>
					<div class="clear"></div>
				</nav>
	<?php if ( is_active_sidebar( 'sidebar' ) ) { ?>
		<?php dynamic_sidebar( 'sidebar' ); ?>
	<?php }else{ ?>
		<aside class="widget">
			<h3 class="widgettitle"><?php _e('Meta', 'notecamp'); ?></h3>
			<ul>
				<?php wp_register(); ?>
				<?php wp_loginout(); ?>
				<?php wp_meta(); ?>
			</ul>
		</aside>
	<?php } ?>
</div>