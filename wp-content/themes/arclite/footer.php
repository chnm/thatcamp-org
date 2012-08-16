<?php /* Arclite/digitalnature */ ?>

 <!-- footer -->
 <div id="footer">

  <!-- page block -->
  <div class="block-content">

    <?php include(TEMPLATEPATH . '/footer-widgets.php'); ?>

    <?php
     $footer = get_arclite_option('footer_content');
     if ($footer):
     ?>
    <div class="add-content">
      <?php echo $footer ?>
    </div>
     <?php endif; ?>

    <div class="copyright">
     <p>
     <!-- please do not remove this. respect the authors :) -->
     <?php
      printf(__('Arclite theme by %s', 'arclite'), '<a href="http://digitalnature.ro">digitalnature</a>');
      print ' | ';
      printf(__('powered by %s', 'arclite'), '<a href="http://wordpress.org/">WordPress</a>');
     ?>
     </p>
     <p>
     <a class="rss" href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)','arclite'); ?></a> <?php _e('and','arclite');?> <a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments (RSS)','arclite'); ?></a> <a href="javascript:void(0);" class="toplink">TOP</a>
     <!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
     </p>
    </div>

  </div>
  <!-- /page block -->

 </div>
 <!-- /footer -->

</div>
<!-- /page -->

  <script type="text/javascript">
  /* <![CDATA[ */
    var isIE6 = false; /* <- do not change! */
    var isIE = false; /* <- do not change! */
    var lightbox = <?php echo get_arclite_option('lightbox'); ?>;/* <- do not change! */
  /* ]]> */
  </script>
  <!--[if lte IE 6]> <script type="text/javascript"> isIE6 = true; isIE = true; </script> <![endif]-->
  <!--[if gte IE 7]> <script type="text/javascript"> isIE = true; </script> <![endif]-->


<?php wp_footer(); ?>
</body>
</html>

