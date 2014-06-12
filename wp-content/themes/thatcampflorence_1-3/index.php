<?php // HOME PAGE ?>

<?php get_header(); ?>

<?php if (have_posts()) :?>
<?php while(have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">

<h2><span><?php the_time('F j, Y - H:i'); ?></span> <?php the_title(); ?></h2>

<?php the_content(); ?>

<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>

</div>

<?php endwhile; ?>

<p id="nav"><?php next_posts_link('&laquo; Old Posts') ?><?php previous_posts_link('New Posts &raquo;') ?></p>

<div class="both"></div>

<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>