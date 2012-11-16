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
	</div>
	<div id="sidebar-innerright">
		<nav id="side-nav" role="navigation">
			<nav id="side-nav" role="navigation">
				<?php
				  if($post->post_parent) {
				  $children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
				  $titlenamer = get_the_title($post->post_parent);
				  }

				  else {
				  $children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
				  $titlenamer = get_the_title($post->ID);
				  }
				  if ($children) { ?>

				  <h2> <?php echo $titlenamer; ?> </h2>
				  <ul class="side_menu">
				  <?php echo $children; ?>
				  </ul>

				<?php } ?>
			
			</nav>
		
		</nav>
	</div>
</div>