<?php
/**
 * The template for displaying article headers
 *
 * @since 1.0.0
 */
?>
	<header class="entry-header">
		<?php
		if ( is_single() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( sprintf( '<h2 class="entry-title taggedlink"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		endif;
		?>

		<?php bavotasan_display_post_thumbnail(); ?>

		<div class="entry-meta">
			<p class="post-cat"><?php the_category( ' ' ); ?></p>
			<?php
			printf( __( ' %s / %s', 'farben-basic' ),
				'<span class="vcard author"><span class="fn"><a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . esc_attr( sprintf( __( 'Posts by %s', 'farben-basic' ), get_the_author() ) ) . '" rel="author">' . get_the_author() . '</a></span></span>', '<a href="' . get_permalink() . '" class="time"><time class="date published updated" datetime="' . esc_attr( get_the_date( 'Y-m-d' ) ) . '">' . get_the_date() . '</time></a>'
				);

			if ( comments_open() )
				echo '&nbsp;/&nbsp;';

			comments_popup_link( __( '0 Comments', 'farben-basic' ), __( '1 Comment', 'farben-basic' ), __( '% Comments', 'farben-basic' ), '', '' );
			?>
		</div>
	</header>