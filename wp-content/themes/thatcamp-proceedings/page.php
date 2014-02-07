<?php get_header(); ?>
 
<div class="main">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        
    <h1><?php the_title(); ?></h1>
    
    <div class="pagecontent">
    <?php the_content(); ?>
        
    <?php endwhile; else: ?>
    <p><?php _e('Sorry, no posts were found.'); ?></p>
   <?php endif; ?>
   </div>

</div>

<div class="sidebar">
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>