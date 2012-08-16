<?php
 /* Arclite/digitalnature */
 get_header();
?>

<!-- main wrappers -->
<div id="main-wrap1">
 <div id="main-wrap2">

  <!-- main page block -->
  <div id="main" class="block-content clearfix">
   <div class="mask-main rightdiv">
    <div class="mask-left">

     <!-- first column -->
     <div class="col1">
      <div id="main-content">

       <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>
         <?php if (!get_post_meta($post->ID, 'hide_title', true)): ?><h2 class="post-title"><?php the_title(); ?></h2><?php endif; ?>
        <div class="post-content clearfix">
         <?php the_content(__('Read the rest of this page &raquo;', 'arclite')); ?>
         <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
         <?php edit_post_link(__('Edit this entry', 'arclite')); ?>
        </div>
        </div>
       <?php endwhile; endif; ?>

       <?php comments_template(); ?>

      </div>
     </div>
     <!-- /first column -->
     <?php
       if(!is_page_template('page-nosidebar.php')):
        get_sidebar();
        include(TEMPLATEPATH . '/sidebar-secondary.php');
       endif;
     ?>

    </div>
   </div>
  </div>
  <!-- /main page block -->

 </div>
</div>
<!-- /main wrappers -->

<?php get_footer(); ?>