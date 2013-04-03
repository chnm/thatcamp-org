<?php
/**
 * Content display for posts - default template
 *
 * @package notecamp
 * @since notecamp 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-body">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'notecamp') ); ?>
	</div>
	<footer class="post-meta">
		<div class="post-edit">
			<?php edit_post_link( __( 'Edit &rarr;', 'notecamp'), ' <span class="edit-link">', '</span> | ' ); ?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'notecamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php _e( 'Permalink', 'notecamp'); ?></a>
		</div>
	</footer>
</article>