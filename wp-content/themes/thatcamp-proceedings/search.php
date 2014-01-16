<?php get_header(); ?>

<?php 
global $wp_query;
$total_results = $wp_query->found_posts;
?>
    
<div id="article" class="twelve columns offset-by-two">

<h1>Results for "<?php the_search_query() ?>". (<?php echo $total_results; ?> found)</h1>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    
    <div class="post">
    
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        
        <?php the_excerpt(); ?>
        
    </div>
    
    <?php endwhile; else: ?>
    <p><?php _e("Sorry, we couldn't find anything!"); ?></p>

<?php endif; ?>

<div class="navigation"><?php posts_nav_link('<span class="break"> </span>', "Previous results", "Next results"); ?></div>

<?php get_footer(); ?>