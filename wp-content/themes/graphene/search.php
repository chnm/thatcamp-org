<?php
/**
 * @package Graphene
 * @subpackage khairul-syahir.com-v2_Theme
 */
get_header(); ?>

<?php 
	if ( isset( $_GET['search_404'] ) ) {
		get_template_part('search', '404'); 
	} else {
		
		if ( have_posts() ) : ?>
        	<h1 class="page-title archive-title">
				<?php
                    global $wp_query;
                    /* translators: %1$s is the number of results found, %2$s is the search term */
                    printf( _n( 'Found %1$s search result for keyword: %2$s', 
                                'Found %1$s search results for keyword: %2$s', $wp_query->found_posts, 'graphene'), 
                            number_format_i18n( $wp_query->found_posts ), 
                            '<span>' . get_search_query() . '</span>' 
                    );
                ?>
            </h1>
        	<div class="entries-wrapper">
            <?php
				while ( have_posts() ) {
					the_post(); 
					get_template_part( 'loop', 'search' );
				}
			?>
            </div>
            <?php graphene_posts_nav(); ?>
            
		<?php else : ?>
			<?php get_template_part( 'loop', 'not-found' ); ?>
		<?php endif;
	}
?>

<?php get_footer(); ?>