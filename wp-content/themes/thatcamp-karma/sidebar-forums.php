<?php
/**
 * Sidebar Forums
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>

<div id="sidebar" role="complementary">
	<h2>Help</h2>
		<?php // Calls the Forums menu managed in WP Appearance
		wp_nav_menu( array(
				'theme_location' => 'forums',
				'menu' => 'forums',
				'menu_class' => 'side_menu',
				'container' => 'nav',
				'container_id' => 'side-nav',
		)); ?>	

<!-- Creates a custom Help menu with expandable sub-menus 
		<nav id="side-nav" role="navigation">
		<ul class="side_menu">
		<?php wp_list_pages( 'title_li=&child_of=3970&depth=2'); ?>
		<li class="page-item"><a href="/forums" title="Forums">Forums</a></li>
		</ul>
		</nav>
-->


	<div id="transposh-wrapper">
		<?php if ( is_active_sidebar( 'sidebar-transposh' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-transposh' ); ?>
		<?php endif; ?>
	</div>
</div>
