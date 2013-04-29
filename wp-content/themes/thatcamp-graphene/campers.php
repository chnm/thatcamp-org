<?php
/*
 * Template Name: Campers
 *
 * A custom page template for making a Campers page on a THATCamp website
 */
 get_header(); ?>
 
<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); global $graphene_settings; ?>
    
    	<?php /* Posts navigation for single post pages, but not for Page post */ ?>
		<?php if (is_single() && !is_page()) : ?>
        <div class="post-nav clearfix">
            <p id="previous"><?php previous_post_link(); ?></p>
            <p id="next-post"><?php next_post_link(); ?></p>
            <?php do_action('graphene_post_nav'); ?>
        </div>
        <?php endif; ?>
        
        
        <div id="post-<?php the_ID(); ?>" <?php post_class('clearfix post'); ?>>
            
            <?php /* Post date is not shown if this is a Page post */ ?>
            <?php if (!is_page() && ( strpos($graphene_settings['post_date_display'], 'icon_') === 0 )) : ?>
            <div class="date">
                <p class="default_date"><?php the_time('M'); ?><br /><span><?php the_time('d') ?></span>
                    <?php if ($graphene_settings['post_date_display'] == 'icon_plus_year') : ?>
                    <br /><span class="year"><?php the_time('Y'); ?></span>
                    <?php endif; ?>
                </p>
                
                <?php do_action('graphene_post_date'); ?>
            </div>
            <?php endif; ?>
            
            <?php	/* Show the post author's gravatar if enabled */
			if ($graphene_settings['show_post_avatar'] && !is_page()) {
				echo get_avatar(get_the_author_meta('user_email'), 40);
			} ?>
            
            <?php do_action('graphene_before_post'); ?>
            
            <div class="entry clearfix">
            
                <?php /* Post title */ ?>
                <h2 class="post-title">
                    <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permalink to %s', 'graphene'), the_title_attribute('echo=0')); ?>"><?php if (get_the_title() == '') {_e('(No title)','graphene');} else {the_title();} ?></a>
                <?php do_action('graphene_post_title'); ?>
                </h2>
   
                <?php /* Post meta */ ?>
                <?php if (get_post_type($post) != 'page' || is_user_logged_in()) : ?>
                <div class="post-meta clearfix">
                    
                    <?php /* Post category, not shown if this is a Page post or if admin decides to hide it */ ?>
                    <?php if (!is_page() && ($graphene_settings['hide_post_cat'] != true)) : ?>
                    <ul class="meta_categories">
                        <li><?php the_category(",</li>\n<li>") ?></li>
                    </ul>
                    <?php endif; ?>
                    
                    <?php /* Edit post link, if user is logged in */ ?>
                    <?php if (is_user_logged_in()) : ?>
                    <p class="edit-post">
                        <?php edit_post_link(__('Edit post','graphene'), ' (', ')'); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php /* Inline post date */
                    	if ($graphene_settings['post_date_display'] == 'text' && !is_page()){
							echo '<p class="post-date-inline">';
                        	the_time(get_option('date_format'));
							echo '</p>';
						}
					?>
                    
                    <?php /* Post author, not not shown if this is a Page post or if admin decides to hide it */ ?>
                    <?php if ($graphene_settings['hide_post_author'] != true) : ?>
                    <p class="post-author">
                        <?php
                            if (!is_page()) {
                                /* translators: this is for the author byline, such as 'by John Doe' */
                                _e('by','graphene'); echo ' '; the_author_posts_link();                           
                            }
                        ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php do_action('graphene_post_meta'); ?>
                    
                </div>
                <?php endif; ?>
                
                <?php /* Post content */ ?>
                <div class="entry-content clearfix">                
                    <?php do_action('graphene_before_post_content'); ?>					
                    <?php if ((is_home() && !$graphene_settings['posts_show_excerpt']) || is_singular()) : ?>
                        <?php the_content('<span class="block-button">'.__('Read the rest of this entry &raquo;','graphene').'</span>'); ?>
                    <?php else : ?>
                        <?php /* The post thumbnail */
                        if (has_post_thumbnail(get_the_ID())) {
                            echo '<div class="excerpt-thumb">';
                            the_post_thumbnail(apply_filters('graphene_excerpt_thumbnail_size', 'thumbnail'));
                            echo '</div>';
                        } else {
                            echo graphene_get_post_image(get_the_ID(), apply_filters('graphene_excerpt_thumbnail_size', 'thumbnail'), 'excerpt');	
                        }
                        ?>
                        <?php the_excerpt(); ?>
                    <?php endif; ?>
                    					<p>THATCampers: to add your picture to this page, log in and upload a picture to your profile or sign up for <a href="http://en.gravatar.com/site/signup/">Gravatar.com</a> and upload an image there.</p>
                    <?php /* Display campers */ 
                    $siteUsers = get_users('orderby=display_name&who=authors&exclude=246'); 					
                 
					foreach ($siteUsers as $usr) { ?>
<div class="camper_info">
	<div class="camper_avatar">
		<a href="<?php echo get_author_posts_url($usr->ID); ?>">
<?php echo get_avatar($usr->ID, 100); ?></a>
	</div>
	<div class="camper_name">
		<a href="<?php echo get_author_posts_url($usr->ID); ?>">
<?php the_author_meta('first_name', $usr->ID); ?>&nbsp;<?php the_author_meta('last_name', $usr->ID); ?></a>
	</div>
	<div class="camper_posts">
		<a href="<?php echo get_author_posts_url($usr->ID); ?>">
			Posts (<?php echo get_usernumposts($usr->ID); ?>)</a>
	</div>
</div>
<?php } ?>

                    <?php /* Page links */ wp_link_pages(array('before' => __('<div class="link-pages"><p><strong>Pages:</strong> ','graphene'), 'after' => '</p></div>', 'next_or_number' => 'number')); ?>
                    
                    <?php do_action('graphene_after_post_content'); ?>
                    
                </div>
                
                <?php /* Post footer */ ?>
                <div class="entry-footer clearfix">
                    <?php /* Display the post's tags, if there is any */ ?>
                    <?php if (!is_page() && ($graphene_settings['hide_post_tags'] != true)) : ?>
                    <p class="post-tags"><?php if (has_tag()) {_e('Tags:','graphene'); the_tags(' ', ', ', '');} else {_e('This post has no tag','graphene');} ?></p>
                    <?php endif; ?>
                    
                    <?php 
                        /**
                         * Display AddThis social sharing button if single post page, comments popup link otherwise.
                         * See the graphene_addthis() function in functions.php
                        */ 
                    ?>
                    <?php if (is_single() || is_page()) : ?>
                        <?php graphene_addthis(get_the_ID()); ?>
                    <?php elseif ($graphene_settings['hide_post_commentcount'] != true && comments_open()) : ?>
                        <p class="comment-link"><?php comments_popup_link(__('Leave comment','graphene'), __('1 comment','graphene'), __("% comments",'graphene')); ?></p>
                    <?php endif; ?>
                    
                    <?php do_action('graphene_post_footer'); ?>
                </div>
            </div>
        </div>
        <?php 
        /**
         * Display the post author's bio in single-post page if enabled
        */
        if (is_single() && $graphene_settings['show_post_author']) :
        ?>
        <h4 class="author_h4"><?php _e('About the author', 'graphene'); ?></h4>
        <div class="author author-info clearfix">
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
        
        <?php 
        /**
         * Display Adsense advertising for single post pages 
         * See graphene_adsense() function in functions.php
        */ 
        ?>
        <?php if (is_single() || is_page() || (is_front_page() && $graphene_settings['adsense_show_frontpage']) || is_archive() || is_search()) {graphene_adsense();} ?>
        
        <?php /* List the child pages if this is a page */ ?>
        <?php if (is_page()) {get_template_part('loop', 'children');} ?>
        
        <?php /* Get the comments template for single post pages */ ?>
        <?php if (is_single() || is_page()) {comments_template();} ?>
            
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

<?php get_footer(); ?>
