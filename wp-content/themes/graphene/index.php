<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Graphene
 * @since Graphene 1.0
 */
global $graphene_settings;
get_header(); ?>

	<?php	
	do_action('graphene_index_pre_loop');
	
    /* Run the loop to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-index.php and that will be used instead.
	*/	
	while ( have_posts() ) {
		the_post(); 
		get_template_part( 'loop', 'index' );
	}
	
	/* Posts navigation. */ 
    graphene_posts_nav();
    ?>
            
<?php get_footer(); ?>