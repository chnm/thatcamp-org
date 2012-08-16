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


	   <?php if (have_posts()) : ?>
   	    <h1><?php _e("Search Results","arclite"); ?></h1>
	    <div class="navigation clearfix" id="pagenavi">
         <?php if(function_exists('wp_pagenavi')) : ?>
	      <?php wp_pagenavi() ?>
         <?php else : ?>
	      <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','arclite')) ?></div>
	      <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','arclite')) ?></div>
         <?php endif; ?>
	    </div>

        <?php while (have_posts()) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>

  	  	 <h3 id="post-<?php the_ID(); ?>" class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>

     	 <small><?php the_time(get_option('date_format').' - '.get_option('time_format')) ?></small>
     	 <p class="postmetadata"><?php the_tags(__('Tags:','arclite').' ', ', ', '<br />'); ?> <?php printf(__('Posted in %s','arclite'), get_the_category_list(', '));?> | <?php edit_post_link(__('Edit','arclite'), '', ' | '); ?>  <?php comments_popup_link(__('No Comments','arclite'), __('1 Comment','arclite'), __('% Comments','arclite'), 'comments', __('Comments off', 'arclite')); ?></p>
        </div>
        <?php endwhile; ?>

        <div class="navigation clearfix" id="pagenavi">
         <?php if(function_exists('wp_pagenavi')) : ?>
          <?php wp_pagenavi() ?>
         <?php else : ?>
          <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','arclite')) ?></div>
          <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','arclite')) ?></div>
         <?php endif; ?>
        </div>
	   <?php else : ?>
  	    <h2 class="center"><?php _e('No posts found. Try a different search?','arclite'); ?></h2>
        <?php if (function_exists("get_search_form")) get_search_form(); ?>
       <?php endif; ?>

      </div>
     </div>
     <!-- /first column -->
     <?php get_sidebar(); ?>
     <?php include(TEMPLATEPATH . '/sidebar-secondary.php'); ?>

    </div>
   </div>
  </div>
  <!-- /main page block -->

 </div>
</div>
<!-- /main wrappers -->

<?php get_footer(); ?>