<?php get_header(); ?>
   
<div class="main">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

<?php the_excerpt(); ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>
</div>

<div class="sidebar">
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>