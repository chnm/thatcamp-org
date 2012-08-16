
<?php
// check if we have widgets
if (is_sidebar_active('sidebar-2')){ ?>
<!-- footer widgets -->
<ul id="footer-widgets" class="widgetcount-<?php  $sidebars_widgets = wp_get_sidebars_widgets(); $wcount=count($sidebars_widgets['sidebar-2']); print $wcount;  ?> clearfix">
 <?php
  if (function_exists('dynamic_sidebar') && dynamic_sidebar('Footer')) : else : ?>
 <?php endif; ?>
</ul>
<!-- /footer widgets -->
<?php } ?>
