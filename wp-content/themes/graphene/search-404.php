<div class="post clearfix post_404">
    <div class="entry clearfix">
        <h2><?php _e( 'Error 404 - Page Not Found', 'graphene' ); ?></h2>
        <div class="entry-content clearfix">
            <p><?php _e( "Sorry, I've looked everywhere but I can't find the page you're looking for.", 'graphene' ); ?></p>
            <p><?php _e( "If you follow the link from another website, I may have removed or renamed the page some time ago. You may want to try searching for the page:", 'graphene' ); ?></p>
            
            <?php get_search_form(); ?>
        </div>
    </div>
</div>

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

<div class="search-results-header">
    <h2><?php _e( 'Suggested results', 'graphene' ); ?></h2>   
    <p>
        <?php 
		global $wp_query; 
		/* translators: %1$s is the search term, %2$s is the number of results found */
		printf( _n( 'I\'ve done a courtesy search for the term %1$s for you and found a total of %2$s result. See if you can find what you\'re looking for below.', 
					'I\'ve done a courtesy search for the term %1$s for you and found a total of %2$s results. See if you can find what you\'re looking for below.', 
					$wp_query->found_posts, 'graphene' ), 
				'<code>' . get_search_query() . '</code>', 
				number_format_i18n( $wp_query->found_posts ) 
		); 
		?>
    </p>
</div>

<?php if ( have_posts() ) : ?>    
    <div class="search-404-results entries-wrapper">
    <?php 
        while ( have_posts() ) {
            the_post(); 
            get_template_part( 'loop', 'search' );
        }
    ?>
    </div>
    <?php graphene_posts_nav(); ?>
<?php else : ?>
  <p><?php _e("<strong>Sorry, couldn't find anything.</strong> Try searching for alternative terms using the search form above.", 'graphene' ); ?></p>
<?php endif; ?>