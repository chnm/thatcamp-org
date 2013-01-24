<?php
/*
Template Name: Campers
*/
?>

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
					<p>THATCampers: to add your picture to this page, log in and upload a picture to your profile or sign up for <a href="http://en.gravatar.com/site/signup/">Gravatar.com</a> and upload an image there.</p>	
					<?php  $siteUsers = get_users('orderby=display_name&role=author&exclude=246'); 
					foreach ($siteUsers as $usr) { ?>			
				<div class="camper_info">	 			
					<div class="camper_avatar"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php echo get_avatar($usr->ID, 100); ?></a></div>
					<div class="camper_name"><a href="<?php echo get_author_posts_url($usr->ID); ?>"><?php the_author_meta('first_name', $usr->ID); ?>&nbsp;<?php the_author_meta('last_name', $usr->ID); ?></a></div>
					<div class="camper_posts"><a href="<?php echo get_author_posts_url($usr->ID); ?>">Posts (<?php echo get_usernumposts($usr->ID); ?>)</a></div>
				</div>	 
					<?php } ?>              
        </div>
        	<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
         <?php edit_post_link(__('Edit this entry', 'arclite')); ?>
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
