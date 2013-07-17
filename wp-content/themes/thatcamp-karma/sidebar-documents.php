<?php
/**
 * Sidebar Documents
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<div id="sidebar" role="complementary">
	<h2>Help</h2>
		<?php wp_nav_menu( array(
				'theme_location' => 'documents',
				'menu' => 'documents',
				'menu_class' => 'side_menu',
				'container' => 'nav',
				'container_id' => 'side-nav',
		)); ?>	
	<div id="transposh-wrapper">
		<?php if ( is_active_sidebar( 'sidebar-transposh' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-transposh' ); ?>
		<?php endif; ?>
	</div>
</div>
