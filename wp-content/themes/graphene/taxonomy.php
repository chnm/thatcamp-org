<?php
/**
 * The taxonomy archive template file
 *
 * @package Graphene
 * @since Graphene 1.7.1
 */
get_header();
?>

<h1 class="page-title archive-title">
    <?php
		$tax = get_taxonomy( get_query_var( 'taxonomy' ) );
		/* translators: %1$s will be replaced by the taxonomy singular name, %2$s will be replaced by the term title */
        printf( __( '%1$s Archive: %2$s', 'graphene' ), $tax->labels->singular_name, '<span>' . single_term_title( '', false ) . '</span>' );
    ?>
</h1>

<?php graphene_tax_description(); ?>

<?php
    /**
	 * Run the loop for the category page to output the posts.
     * If you want to overload this in a child theme then include a file
     * called loop-category.php and that will be used instead.
    */
    while ( have_posts() ) {
		the_post(); 
		get_template_part( 'loop', 'taxonomy' );
	}
	
	/* Posts navigation. */ 
    graphene_posts_nav();
?>

<?php get_footer(); ?>