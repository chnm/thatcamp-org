<?php
/**
 * Template Name : Links
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header(); ?>
<div id="primary" class="main-content">
	<div id="content">
		<div id="links-template" class="feature-box" role="main">
			<?php do_action( 'bp_before_blog_links' ); ?>
			<h2 class="pagetitle"><?php _e( 'Links', 'thatcamp' ); ?></h2>
			<ul id="links-list">
				<?php wp_list_bookmarks(); ?>
			</ul>
			</h2>
			<?php do_action( 'bp_after_blog_links' ); ?>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
