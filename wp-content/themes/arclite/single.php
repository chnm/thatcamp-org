<?php /* Arclite/digitalnature */ ?>
<?php get_header(); ?>

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
        <div class="navigation clearfix">
          <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
          <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
        </div>


        <!-- post -->
        <div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>
           <?php if (!get_post_meta($post->ID, 'hide_title', true)): ?><h2 class="post-title"><?php the_title(); ?></h2><?php endif; ?>

              <div class="post-content clearfix">
    	       <?php the_content(__('Read the rest of this entry &raquo;', 'arclite')); ?>
              </div>
              <?php wp_link_pages(array('before' => '<p><strong>Pages: </strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
              <?php
              $posttags = get_the_tags();
              if ($posttags): ?>
              <p class="tags clearfix"> <?php the_tags(__('Tags:','arclite').' ', ', ', ''); ?></p>
              <?php endif; ?>
              <p class="post-metadata">
                    <?php
                      printf(__('This entry was posted on %s and is filed under %s. You can follow any responses to this entry through %s.', 'arclite'), get_the_time(get_option('date_format').', '.get_option('time_format')), get_the_category_list(', '), '<a href="'.get_post_comments_feed_link($post->ID).'" title="RSS 2.0">RSS 2.0</a>');  ?>

                    <?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
            		  // Both Comments and Pings are open
                      printf(__('You can <a href="#respond">leave a response</a>, or <a href="%s" rel="trackback">trackback</a> from your own site.', 'arclite'), trackback_url('',false));

            		 } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
            		  // Only Pings are Open
                      printf(__('Responses are currently closed, but you can <a href="%s" rel="trackback">trackback</a> from your own site.', 'arclite'), trackback_url('',false));

            		 } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
            		  // Comments are open, Pings are not
            		  _e('You can skip to the end and leave a response. Pinging is currently not allowed.','arclite');

            		 } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
            		  // Neither Comments, nor Pings are open
            		  _e('Both comments and pings are currently closed.','arclite');
            		} ?>
                    <?php edit_post_link(__('Edit this entry', 'arclite')); ?>
    		  </p>

        </div>
        <!-- /post -->

    	<?php comments_template(); ?>
      <?php endwhile; else: ?>
        <p><?php _e("Sorry, no posts matched your criteria.","arclite"); ?></p>
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
