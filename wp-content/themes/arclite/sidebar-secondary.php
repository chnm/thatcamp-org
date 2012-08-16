
<?php 
 if((get_arclite_option('threecol')) || (is_page_template('page-3col.php'))) {
?>
<!-- secondary sidebar -->
<div class="col3">
 <ul id="sidebar-secondary">
  <?php
   if (function_exists('dynamic_sidebar') && dynamic_sidebar('Secondary sidebar')) : else : ?>
   <li class="block">
    <div class="info-text">
     <p><?php _e("You enabled the secondary sidebar. Add widgets here from the Dashboard","arclite"); ?></p>
    </div>
   </li>
  <?php endif; ?>
 </ul>
</div>
<!-- /secondary sidebar -->
<?php } ?>