<?php
/**
 * Content display for posts - default template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php do_action( 'bp_before_blog_post' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-body">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'thatcamp') ); ?>
	</div>
	<footer class="post-meta">
		<h1><?php the_title(); ?></h1>
		<h2><time class="post-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" pubdate><?php echo get_the_date(); ?></time></h2>
		<div class="post-edit">
				<?php edit_post_link( __( 'Edit &rarr;', 'thatcamp'), ' <span class="edit-link">', '</span> | ' ); ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php _e( 'Permlink', 'thatcamp'); ?></a>
		</div>
	</footer>
</article>
<?php do_action( 'bp_after_blog_post' ); ?>