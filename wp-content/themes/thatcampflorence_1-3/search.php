<?php // PAGINA search ?>

<?php get_header(); ?>

<h2><span>Result for word</span> <?php echo wp_specialchars($s, 1); ?></h2>

<?php if (have_posts()) :?>
<?php while(have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">

<h3><span><?php the_date(); ?></span> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

<?php the_excerpt(); ?>

<p class="postmetadata">
<a href="<?php the_permalink(); ?>">Read More</a> <?php edit_post_link('Edit',' | ',''); ?>
</p>

</div>

<?php endwhile; ?>

<p id="nav"><?php posts_nav_link(' '); // Next and Back page ?></p>

<div class="both"></div>

<?php else : ?>

<p>No results for <em><?php echo wp_specialchars($s, 1); ?></em></p>

<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>