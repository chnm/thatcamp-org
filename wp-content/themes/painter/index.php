<?php get_header(); ?>

<?php get_sidebar(); ?>

<?php $useds = array(); ?>

<!-- Content -->
<div id="content">
  
  <!-- Highlights -->
  <?php if(is_home() && !is_paged()) : ?>
    <?php $highlights = new WP_Query("category_name=highlights&showposts=5"); ?>
    <?php if($highlights->have_posts()) : ?>
      <h2 class="content-title"><span id="highlight-pager"></span><?php _e('Highlights', 'painter'); ?></h2>
      <div id="highlight">
        <?php while($highlights->have_posts()) : $highlights->the_post(); ?>
          <?php array_push($useds, $post->ID); ?>
          <div>
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_thumb('thumbnail'); ?></a>
            <h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
            <div class="info">
              <?php _e('Published', 'painter'); ?>
              <span><?php _e('on', 'painter'); ?> <?php the_time(__('F jS, Y @ H:i', 'painter')); ?></span>
              <span><?php _e('by', 'painter'); ?> <?php the_author_posts_link(); ?></span>
            </div>
            <div class="entry"><?php the_excerpt(); ?></div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  
  <!-- Posts -->
  <?php if(have_posts()) : ?>
    <?php if(is_category()) : ?>
    <h2 class="content-title"><?php _e('Category', 'painter'); ?> &raquo; <?php single_cat_title(); ?></h2>
    
    <?php elseif(is_tag()) : ?>
    <h2 class="content-title"><?php _e('Tag', 'painter'); ?> &raquo; <?php single_tag_title(); ?></h2>
    
    <?php elseif(is_day()) : ?>
    <h2 class="content-title"><?php _e('Archives from day', 'painter'); ?> &raquo; <?php print get_the_time('d, F Y'); ?></h2>
    
    <?php elseif(is_month()) : ?>
    <h2 class="content-title"><?php _e('Archives from month', 'painter'); ?> &raquo; <?php print get_the_time('F, Y'); ?></h2>
    
    <?php elseif(is_year()) : ?>
    <h2 class="content-title"><?php _e('Archives from year', 'painter'); ?> &raquo; <?php print get_the_time('Y'); ?></h2>
    
    <?php elseif(is_author()) : ?>
    <?php $current_author = get_userdata(intval($author)); ?>
    <h2 class="content-title"><?php _e('Archives from author', 'painter'); ?> &raquo; <?php print $current_author->user_nicename; ?></h2>
    
    <?php elseif(is_search()) : ?>
    <h2 class="content-title"><?php _e('Results for', 'painter') ?> &quot;<?php the_search_query(); ?>&quot;</h2>
    
    <?php else : ?>
    <h2 class="content-title"><?php _e('News', 'painter'); ?></h2>
    <?php endif; ?>
    
    <?php $show_date = get_option('painter_show_date'); ?>
    <?php $show_author = get_option('painter_show_author'); ?>
    <?php $show_category = get_option('painter_show_category'); ?>
    <?php $show_tags = get_option('painter_show_tags'); ?>
    <?php $show_comments = get_option('painter_show_comments'); ?>
    
    <?php while(have_posts()) : the_post(); ?>
      <?php if(in_array($post->ID, $useds)) continue; update_post_caches($posts); ?>
      <div class="post">
        <div class="options">
          <?php edit_post_link(__("Edit", "painter")); ?>
        </div>
        <h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
        <div class="info">
          <?php if(!empty($post->post_excerpt)) print $post->post_excerpt; ?>
        </div>
        <div class="entry"><?php the_content(__("Read more &raquo;", "painter")); ?></div>
        <hr class="clear" />
        <div class="info">
          <?php if($show_date == 1) : ?><p class="post-date"><strong><?php _e('Date', 'painter'); ?>:</strong> <?php the_time(__('F jS, Y @ H:i', 'painter')); ?></p><?php endif; ?>
          <?php if($show_author == 1) : ?><p class="post-author"><strong><?php _e('Author', 'painter'); ?>:</strong> <?php the_author_posts_link(); ?></p><?php endif; ?>
          <?php if($show_category == 1) : ?><p class="post-category"><strong><?php _e('Categories', 'painter'); ?>:</strong> <?php the_category(', '); ?></p><?php endif; ?>
          <?php if($show_tags == 1) : ?><p class="post-tags"><strong><?php _e('Tags', 'painter'); ?>:</strong> <?php the_tags(' ', ', '); ?></p><?php endif; ?>
          <?php if($show_comments == 1) : ?><p class="post-comments"><?php comments_popup_link(__('Do your comment', 'painter'), __('1 comment', 'painter'), __('% comments', 'painter')); ?></a></p><?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
    
  <?php else : ?>
    <h2 class="content-title">404</h2>
    <div class="post">
      <div class="entry"><?php _e('Sorry! Page not found.', 'painter'); ?></div>
    </div>
  <?php endif; ?>
  
  <div class="navigation">
    <div class="alignleft next"><?php next_posts_link(__('&laquo; Older', 'painter')); ?></div>
    <div class="alignright preview"><?php previous_posts_link(__('Newer &raquo;', 'painter')) ?></div>
  </div>
  
</div>

<?php get_footer(); ?>
