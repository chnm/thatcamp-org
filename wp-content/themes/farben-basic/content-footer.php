<?php
/**
 * The template for displaying article footers
 *
 * @since 1.0.0
 */
 ?>
	<footer class="clearfix">
	    <?php
	    if ( is_singular() ) wp_link_pages( array( 'before' => '<p id="pages">' . __( 'Pages:', 'farben-basic' ) ) );
		the_tags( '<p class="tags"><span>' . __( 'Tags:', 'farben-basic' ) . '</span>', ' ', '</p>' );
	    edit_post_link( __( 'Edit', 'farben-basic' ), '<p class="edit-link">', '</p>' );
	    ?>
	</footer><!-- .entry -->