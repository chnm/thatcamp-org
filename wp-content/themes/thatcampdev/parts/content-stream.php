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
	<div class="post-avatar">
			<span class="img-wrapper floatright"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?></span>
	</div>
	<div class="post-meta">
		<header class="post-header">
			<?php
				// Sitewide Tags keeps the original post permalink in postmeta
				$permalink = get_post_meta( get_the_ID(), 'permalink' );
				if ( ! $permalink ) {
					$permalink = get_permalink();
				}

				// We need some data about the source blog
				$source_blog_url = $source_blog_name = $source_blog_link = '';
				$source_blog_id = get_post_meta( get_the_ID(), 'blogid' );
				if ( $source_blog_id ) {
					$source_blog_url  = get_blog_option( $source_blog_id, 'home' );
					$source_blog_name = get_blog_option( $source_blog_id, 'blogname' );
					$source_blog_link = '<a href="' . $source_blog_url . '">' . $source_blog_name . '</a>';
				}
			?>
			<h3 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark" class="postlink"><?php the_title(); ?></a></h3>
		</header>
		<span class="meta-date"><?php echo get_the_date(); ?></span>
		<span class="meta-author"><?php printf( _x( 'by %s', 'Post written by...', 'thatcamp' ), bp_core_get_userlink( $post->post_author ) ); ?></span>
		<?php if ( $source_blog_link ) : ?>
			<span class="meta-source"><?php printf( _x( 'on %s', 'From the blog...', 'thatcamp' ), $source_blog_link ) ?></span>
		<?php endif ?>
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcamp'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark" class="readmore postlink">Read more...</a>
	</div>
</article>
