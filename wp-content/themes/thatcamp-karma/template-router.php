<?php
/**
 * Router template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 *
 * Template Name: Help Documents Router
 */
?>
<?php get_header(); ?>
<div id="primary-documents" class="main-content">
	<div id="content" class="clearfix feature-box" role="main">
		<div id="page" role="main">
		<?php while ( have_posts() ) : the_post(); ?>

<?php do_action( 'bp_before_blog_page' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header">
		<h1 class="post-title red-text"><?php the_title(); ?></h1>
	</header>
	<div class="post-body">
		<?php the_content(); ?>
			<!-- Adds list of child pages -->
			<ul class="router">
			<?php wp_list_pages( 'title_li=&child_of='.$post->ID.'&echo=1' ); ?>
			</ul>
			<!-- end child pages -->
		<?php endwhile; ?>			
	</div>
	<footer class="post-meta">
		<div class="post-edit">
				<?php edit_post_link( __( 'Edit &rarr;', 'thatcamp'), ' <span class="edit-link">', '</span> | ' ); ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php _e( 'Permalink', 'thatcamp'); ?></a>
		</div>
	</footer>
</article>
<?php do_action( 'bp_after_blog_page' ); ?>
		</div>
	</div>
</div>
<?php get_sidebar('documents'); ?>
<?php get_footer() ?>