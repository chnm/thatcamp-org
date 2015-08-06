<?php
/**
 * The template for displaying article headers
 *
 * @since 1.0.0
 */
?>
	<header class="entry-header">
		<h1 class="entry-title taggedlink">
			<?php if ( is_single() ) : ?>
				<?php the_title(); ?>
			<?php else : ?>
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			<?php endif; // is_single() ?>
		</h1>

		<?php bavotasan_display_post_thumbnail(); ?>

		<div class="entry-meta">
			<p class="post-cat"><?php the_category( ' ' ); ?></p>
			<?php
			printf( __( ' %s / %s', 'farben' ),
				'<span class="vcard author"><span class="fn"><a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . esc_attr( sprintf( __( 'Posts by %s', 'farben' ), get_the_author() ) ) . '" rel="author">' . get_the_author() . '</a></span></span>', '<a href="' . get_permalink() . '" class="time"><time class="date published updated" datetime="' . esc_attr( get_the_date( 'Y-m-d' ) ) . '">' . get_the_date() . '</time></a>'
				);

			if ( comments_open() )
				echo '&nbsp;/&nbsp;';

			comments_popup_link( __( '0 Comments', 'farben' ), __( '1 Comment', 'farben' ), __( '% Comments', 'farben' ), '', '' );
			?>
		</div>
	</header>