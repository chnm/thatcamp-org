<?php
/**
 * The template for displaying the footer.
 *
 * Closes the <div> for #content, #content-main and #container, <body> and <html> tags.
 *
 * @package Graphene
 * @since Graphene 1.0
 */
global $graphene_settings;
?>  
<?php do_action( 'graphene_bottom_content' ); ?>
    </div><!-- #content-main -->
    
    <?php
    
        /* Sidebar 2 on the right side? */
        if ( graphene_column_mode() == 'three_col_left' ){
            get_sidebar( 'two' );
        }
		
		/* Sidebar 1 on the right side? */
        if ( in_array( graphene_column_mode(), array( 'two_col_left', 'three_col_left', 'three_col_center' ) ) ){
            get_sidebar();
        }
    
    ?>
    

</div><!-- #content -->

<?php /* Get the footer widget area */ ?>
<?php get_template_part('sidebar', 'footer'); ?>

<?php do_action('graphene_before_footer'); ?>

<div id="footer" class="clearfix">
    
 <div id="site-info">
        All original text, images, and code on <a href="<?php echo home_url() ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a> are freely available for you to use, copy, adapt and distribute under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a>  as long as you mention THATCamp and (if possible) link to <a href="http://thatcamp.org">THATCamp.org</a> and the <a xmlns:cc="http://creativecommons.org/ns#" href="http://chnm.gmu.edu" rel="cc:attributionURL">Center for History and New Media</a>. The name "THATCamp" and the THATCamp logo are trademarks of the <a xmlns:cc="http://creativecommons.org/ns#" href="http://chnm.gmu.edu" rel="cc:morePermissions">Center for History and New Media</a> at <a href="http://gmu.edu">George Mason University</a>. 
        			<?php
				$theme_data = get_theme_data(ABSPATH.'wp-content/themes/graphene/style.css');
			?>
            <?php /* translators: %1$s is the blog title, %2$s is the theme's name, %3$s is the theme's author */ ?>
			<?php printf(__('The %1$s theme is based on the %2$s theme by %3$s.','graphene'), '<a href="'.get_home_url().'">'.get_bloginfo('name').'</a>', '<a href="'.$theme_data['URI'].'">'.ucfirst(get_template()).'</a>', $theme_data['Author']); ?>
            
            <?php do_action('graphene_developer'); ?>
        </div>
             <div id="cclicense">
     <a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License" src="http://i.creativecommons.org/l/by/3.0/80x15.png" id="ccl" /></a>
     </div>

    </div>
<!-- #footer end -->
</div><!-- #container -->

<?php if (!get_theme_mod('background_image', false) && !get_theme_mod('background_color', false)) : ?>
    </div><!-- .bg-gradient -->
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>