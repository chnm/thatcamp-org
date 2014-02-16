<!-- Sidebar -->
<div id="sidebar">

  <?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar()) : ?>
    <!-- Categories -->
    <div id="widget_categories" class="widget">
      <h2 class="widget-title"><?php _e("Categories", "painter") ?></h2>
      <ul>
        <?php wp_list_categories('title_li=&use_desc_for_title=0'); ?>
      </ul>
    </div>
    
    <!-- Links -->
    <div id="widget_links" class="widget links">
      <h2 class="widget-title"><?php _e("Links", "painter") ?></h2>
      <ul>
        <?php wp_list_bookmarks('title_li=&categorize=0&before=<li>&after=</li>'); ?>
      </ul>
    </div>
  <?php endif; ?>

</div>
