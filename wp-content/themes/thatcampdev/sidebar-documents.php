<?php
/**
 * Sidebar Documents
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<div id="sidebar-documents" role="complementary">	
	<div id="sidebar-innerleft">
		<p>
			Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla vitae elit libero, a pharetra augue. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
		</p>
	</div>
	<div id="sidebar-innerright">
		<nav id="side-nav" role="navigation">
			<div class="skip-link assistive-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'thatcamp' ); ?>"><?php _e( 'Skip to content', 'thatcamp' ); ?></a></div>
			<?php wp_nav_menu( array(
				'theme_location' => 'documents', 
				'menu_class' => 'side_menu',
				'container' => ''
			)); ?>
		</nav>
	</div>
</div>