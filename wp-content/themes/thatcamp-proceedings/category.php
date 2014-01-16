<?php get_header(); ?>
    
<div id="article" class="twelve columns offset-by-two">

<h1>You are viewing entries marked '<?php single_cat_title(); ?>'.</h1>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    
    <div class="post">
    
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        
        <?php the_excerpt(); ?>
        
    </div>
    
    <?php endwhile; else: ?>
    <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

<div class="navigation"><?php posts_nav_link('<span class="break"> </span>', "Previous results", "Older Next"); ?></div>

<?php get_footer(); ?>