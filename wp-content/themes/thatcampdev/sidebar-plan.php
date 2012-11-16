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
		<?php if ( is_active_sidebar( 'sidebar-documents' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-documents' ); ?>
		<?php endif; ?>
		<?php
		  $children = wp_list_pages('title_li=&child_of=823&echo=0');
		  if ($children) { ?>
		  <ul>
		  <?php echo $children; ?>
		  </ul>
		  <?php } ?>
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