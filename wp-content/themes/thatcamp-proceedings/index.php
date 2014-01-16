<?php get_header(); ?>

<div class="sidebar four columns alpha">
    <?php get_sidebar(); ?>
</div>
    
<div id="article" class="ten columns offset-by-two omega">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

<?php the_content(); ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

<?php get_footer(); ?>