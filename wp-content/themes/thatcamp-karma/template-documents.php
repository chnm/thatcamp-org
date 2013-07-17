<?php
/**
 * Documents template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 *
 * Template Name: Documents Template
 */
?>
<?php get_header(); ?>
<div id="primary-documents" class="main-content">
	<div id="content" class="clearfix feature-box" role="main">
		<div id="page" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'parts/content', 'page' );?>
		<?php endwhile; ?>
		
		<?php // Add Next and Previous page links
		$pagelist = get_pages('sort_column=menu_order&sort_order=asc');
		$pages = array();
		foreach ($pagelist as $page) {
		$pages[] += $page->ID;
		}

		$current = array_search(get_the_ID(), $pages);
		$prevID = $pages[$current-1];
		$nextID = $pages[$current+1];
		?>

		<div id="nav-below">
		<?php if (!empty($prevID)) { ?>
		<div class="nav-previous">
		<a href="<?php echo get_permalink($prevID); ?>"
		title="<?php echo get_the_title($prevID); ?>">&larr;&nbsp;<?php echo get_the_title($prevID); ?></a>
		</div>
		<?php }
		if (!empty($nextID)) { ?>
		<div class="nav-next">
		<a href="<?php echo get_permalink($nextID); ?>" 
		 title="<?php echo get_the_title($nextID); ?>"><?php echo get_the_title($nextID); ?>&nbsp;&rarr;</a>
		</div>
		<?php } ?>
		</div><!-- .nav-below -->
		</div>
	</div>
</div>
<?php get_sidebar('documents'); ?>
<?php get_footer() ?>