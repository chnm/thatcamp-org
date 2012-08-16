<!-- begin sidebar -->
    <div id="sidebar">
		<?php
        if(function_exists('wp_nav_menu')) {
        	wp_nav_menu( array( 'theme_location' => 'main', 'menu_class' => 'cats','sort_column' => 'menu_order', 'container' => 'false', 'fallback_cb' => 'display_none' ) );
        } else {
        	echo '<ul class="cats"><li><a href="'.get_bloginfo('url').'">'.__('Home', "feed-me-seymour").'</a></li>';
       		wp_list_categories('title_li=');
       		wp_list_pages('title_li=');
			echo '</ul>';
        }
		?>
        <?php 	/* Widgetized sidebar, if you have the plugin installed. */
            if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Sidebar One") ) : ?>
            <div class="side-widget">
                 <h2><?php _e('Archive', "feed-me-seymour"); ?></h2>
                    <ul>
                        <?php wp_get_archives('type=monthly'); ?>
                    </ul>
            </div>
         <div class="side-widget">
		   <h2><?php _e('Tags', "feed-me-seymour"); ?></h2>
            <ul>
            <li><?php wp_tag_cloud(); ?></li>
            </ul>
        </div>
        <?php endif; ?>
        <?php if(theme_option('logo_location') == "aligncenter" && theme_option('rss_button') != 2) { ?>
        <div class="sidebox">
            <a href="<?php bloginfo('rss2_url'); ?>"><img src="<?php echo THEME_URL; ?>/images/rss.png" alt="<?php _e('Subscribe to RSS', "feed-me-seymour"); ?>" /></a><p><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Subscribe to RSS', "feed-me-seymour"); ?></a></p>
        </div>
		<?php } ?>
    </div>
<!-- end sidebar -->