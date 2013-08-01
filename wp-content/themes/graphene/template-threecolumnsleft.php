<?php
/**
 * Template Name: Three columns, sidebars on the right
 *
 * A custom page template with the main content on 
 * the left side and one sidebar on the right side.
 *
 * @package Graphene
 * @since Graphene 1.1.5
 */
 
 /* translators: For RTL languages, translate "right" as "left" */
 __( 'Three columns, sidebars on the right', 'graphene' );
 
 get_header(); ?>
 
    <?php
    /* Run the loop to output the pages.
	 * If you want to overload this in a child theme then include a file
	 * called loop-page.php and that will be used instead.
	*/
	the_post();
    get_template_part( 'loop', 'page' );
    ?>

<?php get_footer(); ?>