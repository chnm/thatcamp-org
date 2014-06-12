<?php // PAGINA ?>

<?php get_header(); ?>

<?php if (have_posts()) :?>
<?php while(have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">

<h2><?php the_title(); ?></h2>

<?php the_content(); ?>

<p class="postmetadata">
<?php edit_post_link('Edit','',''); ?>
</p>

</div>

<br/>

<?php endwhile; ?>

<?php else : ?>

<?php include(TEMPLATEPATH . '/404.php'); ?>

<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>