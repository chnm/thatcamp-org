<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); global $graphene_settings; ?>
    
    	<?php 
		/**
		 * Check if the post has a post format. Load a post-format specific loop file,
		 * if it has. Continue with standard loop otherwise.
		*/ 
		global $post_format;
		$post_format = get_post_format();
		
		// Get the post formats supported by the theme
		$supported_formats = get_theme_support( 'post-formats' );
		if (is_array($supported_formats)) $supported_formats = $supported_formats[0]; 
		
		if (in_array($post_format, $supported_formats)) {
			
			// Get the post format loop file
			get_template_part('loop-post-formats', $post_format);
			
			// Stop this default posts loop
			continue;
		}
		?>
    
    	<?php /* Posts navigation for single post pages, but not for Page post */ ?>
		<?php if ( is_single() && ! is_page() ) : ?>
        <div class="post-nav clearfix">
            <p id="previous"><?php previous_post_link(); ?></p>
            <p id="next-post"><?php next_post_link(); ?></p>
            <?php do_action('graphene_post_nav'); ?>
        </div>
        <?php endif; ?>

        <?php if (get_post_type($post) == 'page' && $graphene_settings['hide_parent_content_if_empty'] && $post->post_content == '') : ?>
        <h1 class="page-title">
            <?php if (get_the_title() == '') {_e('(No title)','graphene');} else {the_title();} ?>
        </h1>
        <?php else : ?>                
        <div id="post-<?php the_ID(); ?>" <?php post_class('clearfix post'); ?>>
            
            <?php /* Post date is not shown if this is a Page post */ ?>
            <?php if (( strpos($graphene_settings['post_date_display'], 'icon_') === 0 ) && get_post_type($post) != 'page') : ?>
            <div class="date updated">
                <p class="default_date"><?php the_time('M'); ?><br /><span><?php the_time('d') ?></span>
                    <?php if ($graphene_settings['post_date_display'] == 'icon_plus_year') : ?>
                    <br /><span class="year"><?php the_time('Y'); ?></span>
                    <?php endif; ?>
                </p>
                
                <?php do_action('graphene_post_date'); ?>

            </div>
            <?php endif; ?>
            
            <?php	/* Show the post author's gravatar if enabled */
			if ($graphene_settings['show_post_avatar'] && !is_page() && get_post_type($post) != 'page') {
				echo get_avatar(get_the_author_meta('user_email'), 40);
			} ?>
            
            <?php do_action('graphene_before_post'); ?>
            
            <div class="entry clearfix">                
                
                <?php /* Post title */ 
				$tag = (is_singular() && !is_front_page()) ? 'h1' : 'h2';
				echo '<'.$tag.' class="post-title entry-title">';
				?>
                    <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permalink to %s', 'graphene'), the_title_attribute('echo=0')); ?>"><?php if (get_the_title() == '') {_e('(No title)','graphene');} else {the_title();} ?></a>
                <?php do_action('graphene_post_title'); ?>
                <?php echo '</'.$tag.'>'; ?>
                
                <?php /* Post meta */ ?>
                <?php if (get_post_type($post) != 'page' || is_user_logged_in() || (is_singular() && $graphene_settings['print_css'] && $graphene_settings['print_button'])) : ?>
                <div class="post-meta clearfix">
                    
                    <?php /* Post category, not shown if this is a Page post or if admin decides to hide it */ ?>
                    <?php if (!is_page() && ($graphene_settings['hide_post_cat'] != true)) : ?>
                    <span class="printonly"><?php _e('Categories:', 'graphene'); ?> </span>
                    <ul class="meta_categories">
                        <li><?php the_category(",</li>\n<li>") ?></li>
                    </ul>
                    <?php endif; ?>
                    
                    <?php 
						/* Add a print button only for single pages/posts 
						 * and if the theme option is enabled.
						 */
						if (is_singular() && $graphene_settings['print_css'] && $graphene_settings['print_button']) : ?>
						<p class="print"><a href="javascript:print();" title="<?php esc_attr_e('Print this page', 'graphene'); ?>"><span><?php _e('Print this page', 'graphene'); ?></span></a></p>
					<?php endif; ?>
                    
                    <?php /* Add an email post icon if the WP-Email plugin is installed and activated */
					if(function_exists('wp_email') && is_singular()) {echo '<p class="email">'; email_link(); echo '</p>';}
					?>
                    
                    <?php /* Edit post link, if user is logged in */ ?>
                    <?php if (is_user_logged_in()) : ?>
                    <p class="edit-post">
                        <?php edit_post_link(__('Edit post','graphene'), ' (', ')'); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php /* Inline post date */ ?>
                    <?php if ($graphene_settings['post_date_display'] == 'text' && !is_page()) : ?>
                    <p class="post-date-inline updated">
                        <abbr class="published" title="<?php the_date('c'); ?>"><?php the_time(get_option('date_format')); ?></abbr>
                    </p>
                    <?php endif; ?>
                    
                    <?php /* Post author, not shown if this is a Page post or if admin decides to hide it */ ?>
                    <?php if ($graphene_settings['hide_post_author'] != true) : ?>
                    <p class="post-author author vcard">
                        <?php
                            if (!is_page() && get_post_type($post) != 'page') {
                                /* translators: this is for the author byline, such as 'by John Doe' */
                                _e('by','graphene'); echo ' <span class="fn nickname">'; the_author_posts_link(); echo '</span>';                    
                            }
                        ?>
                    </p>
                    <?php endif; ?>
                                        
                    <?php /* For printing: the date of the post */
                        if ($graphene_settings['print_css'] && !is_page() && $graphene_settings['post_date_display'] != 'hidden') {
                             echo graphene_print_only_text(get_the_time(get_option('date_format')));  
                        } 
                    ?>
                    
                    <?php do_action('graphene_post_meta'); ?>
                </div>
                <?php endif; ?>
                
                <?php /* Post content */ ?>
                <div class="entry-content clearfix">
                    <?php do_action('graphene_before_post_content'); ?>
                    
                    <?php if ((is_home() && !$graphene_settings['posts_show_excerpt']) || is_singular() || (!is_singular() && !is_home() && $graphene_settings['archive_full_content'])) : ?>
                    	
                        <?php /* Social sharing buttons at top of post */ ?>
                        <?php if (stripos($graphene_settings['addthis_location'], 'top') !== false) {graphene_addthis(get_the_ID());} ?>
                        
						<?php /* The full content */ ?>
						<?php the_content('<span class="block-button">'.__('Read the rest of this entry &raquo;','graphene').'</span>'); ?>
                    <?php else : ?>
                        <?php /* The post thumbnail */
                        if (has_post_thumbnail(get_the_ID())) { ?>
                            <div class="excerpt-thumb">
                            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permalink to %s', 'graphene'), the_title_attribute('echo=0')); ?>">
								<?php the_post_thumbnail(apply_filters('graphene_excerpt_thumbnail_size', 'thumbnail')); ?>
                            </a>
                            </div>
                            <?php
                        } else {
                            echo graphene_get_post_image(get_the_ID(), apply_filters('graphene_excerpt_thumbnail_size', 'thumbnail'), 'excerpt');	
                        }
                        ?>
                        <?php /* The excerpt */ ?>
                        <?php the_excerpt(); ?>
                    <?php endif; ?>
                    
                    <?php wp_link_pages(array('before' => __('<div class="link-pages"><p><strong>Pages:</strong> ','graphene'), 'after' => '</p></div>', 'next_or_number' => 'number')); ?>
                    
                    <?php do_action('graphene_after_post_content'); ?>
                    
                </div>
                
                <?php /* Post footer */ ?>
                <div class="entry-footer clearfix">
                    <?php /* Display the post's tags, if there is any */ ?>
                    <?php if (!is_page() && get_post_type($post) != 'page' && ($graphene_settings['hide_post_tags'] != true)) : ?>
                    <p class="post-tags"><?php if (has_tag()) {_e('Tags:','graphene'); the_tags(' ', ', ', '');} else {_e('This post has no tag','graphene');} ?></p>
                    <?php endif; ?>
                    
                    <?php 
                        /**
                         * Display AddThis social sharing button if single post page, comments popup link otherwise.
                         * See the graphene_addthis() function in functions.php
                        */ 
                    ?>
                    <?php if (is_single() || is_page()) : ?>
                        <?php if (stripos($graphene_settings['addthis_location'], 'bottom') !== false) {graphene_addthis(get_the_ID());} ?>
                    <?php elseif ($graphene_settings['hide_post_commentcount'] != true && comments_open() && graphene_should_show_comments() ) : ?>
                        <p class="comment-link"><?php comments_popup_link(__('Leave comment','graphene'), __('1 comment','graphene'), __("% comments",'graphene')); ?></p>
                    <?php endif; ?>
                    
                    <?php do_action('graphene_post_footer'); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php 
        /**
         * Display the post author's bio in single-post page if enabled
        */
        if (is_single() && $graphene_settings['show_post_author']) :
        ?>
        <h4 class="author_h4"><?php _e('About the author', 'graphene'); ?></h4>
        <div class="author-info clearfix">
            <?php
            if (get_the_author_meta('graphene_author_imgurl')) {
                echo '<img class="avatar" src="'.get_the_author_meta('graphene_author_imgurl').'" alt="" />';
            } else {
                echo get_avatar(get_the_author_meta('user_email'), 100); 
            }
            ?>
            <p class="author_name"><strong><?php the_author_meta('display_name'); ?></strong></p>
            <p class="author_bio"><?php the_author_meta('description'); ?></p>
            
        </div>
        <?php endif; ?>
        
         <?php /* For printing: the permalink */
			if ($graphene_settings['print_css']) {
				echo graphene_print_only_text('<span class="printonly url"><strong>'.__('Permanent link to this article:', 'graphene').' </strong><span>'. get_permalink().'</span></span>');
			} 
		?>
        
        <?php 
        /**
         * Display Adsense advertising for single post pages 
         * See graphene_adsense() function in functions.php
        */ 
        ?>
        <?php if (!is_front_page() || (is_front_page() && $graphene_settings['adsense_show_frontpage'])) {graphene_adsense();} ?>
        
        <?php /* List the child pages if this is a page */ ?>
        <?php if (is_page()) {get_template_part('loop', 'children');} ?>
        
        <?php /* Get the comments template for single post pages */ ?>
        <?php if (is_single() || is_page()) {comments_template();} ?>
        
        <?php do_action( 'graphene_loop_footer' ); ?>
            
	<?php endwhile; ?>
    
    <?php /* Display posts navigation if this is not a single post page */ ?>
    <?php if (!is_single()) : ?>
        <?php /* Posts navigation. See functions.php for the function definition */ ?>
    	<?php graphene_posts_nav(); ?>
    <?php endif; ?>
    
<?php /* If there is no post, display message and search form */ ?>
<?php else : ?>
    <div class="post page">
        <h2><?php _e('Not found','graphene'); ?></h2>
        <div class="entry-content">
            <p>
			<?php 
				if (!is_search())
					_e("Sorry, but you are looking for something that isn't here. Wanna try a search?","graphene"); 
				else
					_e("Sorry, but no results were found for that keyword. Wanna try an alternative keyword search?","graphene"); 
			?>
                
            </p>
            <?php get_search_form(); ?>
        </div>
    </div>
    
    <?php do_action('graphene_not_found'); ?>
<?php endif; ?>

<?php do_action('graphene_bottom_content'); ?>