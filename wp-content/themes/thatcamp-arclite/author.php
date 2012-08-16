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

       <?php
       // global $wp_query;
       // $curauth = $wp_query->get_queried_object();

       if(isset($_GET['author_name'])) : $curauth = get_userdatabylogin($author_name); else : $curauth = get_userdata(intval($author)); endif;
       ?>

       <h1><?php echo $curauth->first_name; ?>&nbsp;<?php echo $curauth->last_name; ?></h1>

       <div class="profile clearfix">
        <div class="avatar left"><?php echo get_avatar($curauth->user_email, '128', $avatar); ?></div>
        <div class="info">
        <p>
                		<ul>
        		    <?php if ( $title = $curauth->user_title ): ?>
        			<li class="title">Title / Position: <?php echo $curauth->user_title; ?></li>
        			<?php endif; ?>
        			<?php if ($institution = $curauth->user_organization): ?>
        			<li class="user_organization">Organization: <?php echo $curauth->user_organization; ?></li>
        			<?php endif; ?>
        			<?php
        			// Checks to see if the user_url isn't empty and if its length is greater than 7 (so as not to write http:// only links) 
        			if(!empty($curauth->user_url) && strlen($curauth->user_url) > 7): ?>
        			<li>Website: <a href="<?php echo $curauth->user_url; ?>" class="url"><?php echo str_replace("http://","",$curauth->user_url); ?></a></li>
        			<?php endif; ?>
        			<?php if(!empty($curauth->user_twitter)): ?>
        			<li>Twitter: <a href="http://twitter.com/<?php echo $curauth->user_twitter; ?>/"><?php echo $curauth->user_twitter; ?></a></li>
        			<?php endif; ?>
        		</ul>
        </p>
        
        <p>
        <?php
         if($curauth->user_description<>''): echo $curauth->user_description;
         else: _e("This user hasn't shared any biographical information","arclite");
         endif;
        ?>
        </p>

        </div>
       </div>
       <br />

       <?php if (have_posts()): ?>
        <h1><?php printf(__('Posts by %s', 'arclite'), $curauth->display_name); ?></h1>
        <?php while (have_posts()) : the_post(); ?>
         <div id="post-<?php the_ID(); ?>" <?php if (function_exists("post_class")) post_class(); else print 'class="post"'; ?>>
  	   	   <h3 id="post-<?php the_ID(); ?>" class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
           <small><?php the_time(__('l, F jS, Y','arclite')) ?></small>
  		   <p><?php the_tags(__('Tags:','arclite').' ', ', ', '<br />'); ?> <?php printf(__('Posted in %s','arclite'), get_the_category_list(', '));?>  | <?php edit_post_link(__('Edit','arclite'), '', ' | '); ?>  <?php comments_popup_link(__('No Comments','arclite'), __('1 Comment','arclite'), __('% Comments','arclite')); ?></p>
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
        <p class="error"><?php _e('No posts found by this author.','arclite'); ?></p>
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