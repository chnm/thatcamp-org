      <!-- Footer -->
      <div id="footer">
        <span class="backtoTop"><a href="#container" title="<?php _e('Back to Top', 'painter'); ?>" class="backtotop"><?php _e('Back to Top', 'painter'); ?></a></span>
        <p><strong><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></strong> <?php _e("uses <em><a href='http://wordpress.org' title='WordPress' target='_blank'>WordPress</a></em> as content manager.", 'painter'); ?></p>
      </div>
    </div>
    
    <?php wp_footer(); ?>
    
    <!-- queries: <?php print get_num_queries(); ?> -->
    <!-- seconds: <?php print timer_stop(0, 3); ?> -->
  </body>
</html>
