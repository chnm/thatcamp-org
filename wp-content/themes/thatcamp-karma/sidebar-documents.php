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
		<?php // Calls the Documents menu managed in WP Appearance
		wp_nav_menu( array(
				'theme_location' => 'documents',
				'menu' => 'documents',
				'menu_class' => 'side_menu',
				'container' => 'nav',
				'container_id' => 'side-nav',
		)); ?>	

<!-- Creates a custom Help menu with expandable sub-menus
		<nav id="side-nav" role="navigation">
		<ul class="side_menu">
		<?php wp_list_pages( 'title_li=&child_of=3970&depth=3'); ?>
		<li><a href="<?php bbp_get_forums_url(); ?>">Forums</a></li>
		</ul>
		</nav>
-->
	<div id="transposh-wrapper">
		<?php if ( is_active_sidebar( 'sidebar-transposh' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-transposh' ); ?>
		<?php endif; ?>
	</div>
</div>
