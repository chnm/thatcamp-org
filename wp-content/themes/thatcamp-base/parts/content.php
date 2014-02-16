<?php
/**
 * Content display for posts - default template
 *
 * @package thatcampbase
 * @since thatcampbase 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( comments_open() && ! post_password_required() ) : ?>
		<div class="comments-link">
			<?php comments_popup_link( __( 'Comment', 'thatcampbase'), __( '1 Comment', 'thatcampbase'), __( '% Comments', 'thatcampbase') ); ?>
		</div>
	<?php endif; ?>

	<header class="post-header">
		<hgroup>
			<h1 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcampbase'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		</hgroup>
	</header>

	<?php if(function_exists('the_post_thumbnail')) { ?>
		<?php if(get_the_post_thumbnail() != "") { ?>
			<div class="post-featured-thumb">
				<?php the_post_thumbnail(); ?>
			</div>
	<?php } }?>

<!--	<div class="post-summary">
		<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'thatcampbase') ); ?>
	</div>
-->

	<div class="post-body">
		<?php the_content(); ?>
	</div>

	<footer class="post-meta">
		<div class="post-author">By <?php the_author_posts_link() ?> <?php thatcamp_add_friend_button( get_the_author_ID() ) ?></div>
		<div class="post-date"><?php echo get_the_date(); ?></div>
		<div class="post-categories">
			<?php _e( 'Categories: ', 'thatcampbase'); ?><?php the_category( ' ' ); ?>
		</div>
		<div class="post-tags">
			<?php $tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ): ?>
			<?php printf( __( 'Tags: %2$s', 'thatcampbase'), 'tag-links', $tags_list ); ?>
			<?php endif; ?>
		</div>
		<div class="post-edit">
				<?php edit_post_link( __( 'Edit &rarr;', 'thatcampbase'), ' <span class="edit-link">', '</span> | ' ); ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thatcampbase'), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php _e( 'Permalink', 'thatcampbase'); ?></a>
		</div>
	</footer>
</article>
