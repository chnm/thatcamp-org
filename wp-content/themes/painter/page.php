<?php get_header(); ?>

<?php get_sidebar(); ?>

<!-- Content -->
<div id="content">
  
  <!-- Posts -->
  <?php if(have_posts()) : ?>
    <?php while(have_posts()) : the_post(); ?>
      <div class="post">
        <div class="options">
          <?php edit_post_link(__("Edit", "painter")); ?>
          <span class="post-print"><a href="javascript:print()" title="<?php _e('Print', 'painter'); ?>"><?php _e('Print', 'painter'); ?></a></span>
        </div>
    		<h2 class="post-title"><?php the_title(); ?></h2>
        <div class="entry"><?php the_content(); ?></div>
        <hr class="clear" />
        <div class="info">
          <p class="post-date"><strong><?php _e('Date', 'painter'); ?>:</strong> <?php the_time(__('F jS, Y @ H:i', 'painter')); ?></p>
          <p class="post-author"><strong><?php _e('Author', 'painter'); ?>:</strong> <?php the_author_posts_link(); ?></p>
        </div>
    	</div>
    <?php endwhile; ?>
	<?php endif; ?>
	
	<?php comments_template(); ?>
</div>

<?php get_footer(); ?>
