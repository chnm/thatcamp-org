<?php 

/* Blog Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_blog', 'bizz_blog_area' );

add_action( 'bizz_headline_cb_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cb_inside', 'bizz_breadcrumb' );
add_action( 'bizz_wp_pagenavi_cb_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_cb_bottom', 'bizz_wp_pagenavi' );
add_action( 'bizz_subheadline_cb_inside', 'bizz_subheadline' );
add_action( 'bizz_post_meta_cb_inside', 'bizz_post_meta' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_blog_area() {

?>

<?php if (is_paged()) $is_paged = true; ?>

<?php bizz_blog_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline_cb_inside(); ?>
</div><!-- /.title-area -->

<?php if ( $GLOBALS['opt']['bizzthemes_breadcrumbs'] == 'true') { ?>
<div class="breadcrumbs-area box clearfix">
	<?php bizz_breadcrumb_cb_inside(); ?>
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area box clearfix">

    <?php 
		if (is_front_page()){ 
			global $query_string; 
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$args=array( 'paged'=>$paged, 'ignore_sticky_posts'=>1 );
			query_posts($query_string . '&'.$args);
		} else {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$args=array( 'paged'=>$paged, 'ignore_sticky_posts'=>1 );
			query_posts($args);
		}
	?>
	
	<?php if (function_exists('bizz_wp_pagenavi') && is_paged()) { ?>
		<div class="lpagination clearfix">
		    <?php bizz_wp_pagenavi_cb_top(); ?>
		</div>
    <?php } ?>
	
	<?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
	
	    <?php if (($GLOBALS['opt']['bizzthemes_gbox_display'] == '2') && ($postcount % 2)) { $even = 'odd'; } else { $even = 'even'; } ?>
		<div class="single bsize_<?php echo stripslashes($GLOBALS['opt']['bizzthemes_gbox_display']); ?> <?php echo $even; ?>">
		    <div class="headline">
				<?php bizz_subheadline_cb_inside(); ?>
				<?php bizz_post_meta_cb_inside(); ?>
			</div><!-- /.headline -->
			<?php if ($GLOBALS['opt']['bizzthemes_thumb_show'] == 'true') {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_thumb_width'],$GLOBALS['opt']['bizzthemes_thumb_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_thumb_align']);
			} ?>
			<div class="format_text">
				<?php if ( $GLOBALS['opt']['bizzthemes_archive_full'] == 'true' ) { ?>
				    <?php the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).'')); ?>
                <?php } else { ?>
					<?php the_excerpt(); ?>
					<?php if ( $GLOBALS['opt']['bizzthemes_readmore'] == 'true' ) { ?>
						<span class="read-more"><a rel="nofollow" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php echo stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']); ?></a></span>
                    <?php } ?>
				<?php } ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
	
	<?php if (function_exists('bizz_wp_pagenavi')) { ?>
	    <div class="fix"><!----></div>
		<div class="lpagination fpagination clearfix">
		    <?php bizz_wp_pagenavi_cb_bottom(); ?>
		</div>
    <?php } ?>
			
	<?php wp_reset_query(); ?>

</div><!-- /.cbox-area -->	

<?php bizz_blog_after(); ?>
		
<?php }

/* FAQs Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_faqs', 'bizz_faqs_area' );

add_action( 'bizz_headline_cf_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cf_inside', 'bizz_breadcrumb' );
add_action( 'bizz_wp_pagenavi_cf_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_cf_bottom', 'bizz_wp_pagenavi' );
add_action( 'bizz_subheadline_cf_inside', 'bizz_subheadline' );
add_action( 'bizz_post_meta_cf_inside', 'bizz_post_meta' );
add_action( 'bizz_search_form_cf_inside', 'bizz_search_form' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );
add_action( 'bizz_faq_popular_inside', 'bizz_faqs_popular_list' );

function bizz_faqs_area() { 

?>

<?php if (is_paged()) $is_paged = true; ?>

<?php bizz_faqs_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline_cf_inside(); ?>
</div><!-- /.title-area -->

<?php if ( $GLOBALS['opt']['bizzthemes_breadcrumbs'] == 'true') { ?>
<div class="breadcrumbs-area box clearfix">
	<?php bizz_breadcrumb_cf_inside(); ?>
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area box clearfix">

	<div class="faqs">
	
	<?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<div class="single clearfix">
			<div class="format_text">
				<?php the_content(); ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>

	<?php bizz_search_form_cf_inside(); ?>
	
	<?php bizz_faq_popular_inside(); ?>
	
	<ul class="faq-section">	
	    <?php bizz_faqs_list(); ?>
	</ul>
	
    </div><!-- /.box -->

</div><!-- /.cbox-area -->	

<?php bizz_faqs_after(); ?>
		
<?php } 

/* Custom Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_custom', 'bizz_custom_area' );

add_action( 'bizz_headline_c_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_c_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_c_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_custom_area() { 

?>

<?php bizz_custom_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline_c_inside(); ?>
</div><!-- /.title-area -->

<?php if ( $GLOBALS['opt']['bizzthemes_breadcrumbs'] == 'true') { ?>
<div class="breadcrumbs-area box clearfix">
	<?php bizz_breadcrumb_c_inside(); ?>
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area box clearfix">
		
    <?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<div class="single clearfix">
			<div class="format_text">
				<?php the_content(); ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
						
	<div class="single clearfix">
		<?php bizz_custom_inside(); ?>
	</div><!-- /.single -->

</div><!-- /.cbox-area -->	

<?php bizz_custom_after(); ?>
		
<?php } 

/* Sitemap Template */
/*------------------------------------------------------------------*/

add_action( 'bizz_sitemap', 'bizz_sitemap_area' );

add_action( 'bizz_headline_cs_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cs_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_cs_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_sitemap_area() {

?>

<?php bizz_sitemap_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline_cs_inside(); ?>
</div><!-- /.title-area -->

<?php if ( $GLOBALS['opt']['bizzthemes_breadcrumbs'] == 'true') { ?>
<div class="breadcrumbs-area box clearfix">
	<?php bizz_breadcrumb_cs_inside(); ?>
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area  boxclearfix">

	<?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<div class="single sing clearfix">
			<div class="format_text">
				<h3><?php echo stripslashes($GLOBALS['opt']['bizzthemes_pages_name']); ?></h3>
                <ul><?php wp_list_pages('sort_column=menu_order&title_li=' ); ?></ul>				
                <h3><?php echo stripslashes($GLOBALS['opt']['bizzthemes_categories_name']); ?></h3>
                <ul><?php wp_list_categories('title_li=&show_count=1') ?></ul>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
						
	<?php if (comments_open() && $GLOBALS['opt']['bizzthemes_comments_pag']=='true') : ?>
		<?php comments_template_cs_inside(); ?>
	<?php endif; ?>

</div><!-- /.cbox-area -->	

<?php bizz_sitemap_after(); ?>
		
<?php }

/* Portfolio Template */
/*------------------------------------------------------------------*/
add_action( 'bizz_portfolio', 'bizz_portfolio_area' );

add_action( 'bizz_headline_cp_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_cp_inside', 'bizz_breadcrumb' );
add_action( 'bizz_wp_pagenavi_cp_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_cp_bottom', 'bizz_wp_pagenavi' );
add_action( 'comments_template_cp_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_portfolio_area() { 

	if ( get_query_var('paged') )
		$paged = get_query_var('paged');
	elseif ( get_query_var('page') )
		$paged = get_query_var('page');
	else
		$paged = 1;
?>

<?php bizz_portfolio_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline_cp_inside(); ?>
</div><!-- /.title-area -->

<?php if ( $GLOBALS['opt']['bizzthemes_breadcrumbs'] == 'true') { ?>
<div class="breadcrumbs-area box clearfix">
	<?php bizz_breadcrumb_cp_inside(); ?>
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area box clearfix">
	
	<?php 
		if (is_front_page()){ 
			$args = 'posts_per_page='.$GLOBALS['opt']['bizzthemes_portfolio_number'].'&ignore_sticky_posts=1&cat='.isset($GLOBALS['opt']['bizzthemes_portfolio_cat']).'&paged='.$paged.''; 
			global $query_string;
			$posts = query_posts($query_string . '&'.$args);
		} else {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$custom_cat = ( isset($GLOBALS['opt']['bizzthemes_portfolio_cat']) ) ? $GLOBALS['opt']['bizzthemes_portfolio_cat'] : '';
			$args = array( 'posts_per_page'=> $GLOBALS['opt']['bizzthemes_portfolio_number'], 'ignore_sticky_posts'=> '1', 'cat' => $custom_cat, 'paged'=> $paged );
			query_posts($args);
		}
	?>
	
	<?php if (function_exists('bizz_wp_pagenavi') && is_paged()) { ?>
		<div class="lpagination clearfix">
		    <?php bizz_wp_pagenavi_cp_top(); ?>
		</div>
    <?php } ?>
		
	<?php if (have_posts()) : $postcount = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<?php if ($postcount % 2) { $even = 'odd'; } else { $even = 'even'; } ?>
		<div class="portfolio <?php echo $even; ?>">
			<?php bizz_get_image('image',280,150,'alignleft','90','','src'); ?>
			<div class="headline">
				<?php if ($GLOBALS['opt']['bizzthemes_portfolio_title'] == 'true') { ?>
				    <h2 class="ptitle"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
				<?php } ?>
				<p class="meta">
				<?php if ($GLOBALS['opt']['bizzthemes_portfolio_date'] == 'true') { ?>
					<span class="date"><abbr class="published" title="<?php the_time('F j, Y'); ?>"><?php the_time('F j, Y'); ?></abbr></span>
				<?php } ?>
				<?php if ($GLOBALS['opt']['bizzthemes_portfolio_cats'] == 'true') { ?>
				    <span class="tag"><?php the_category(', ') ?></span>
				<?php } ?>
				</p>
			</div><!-- /.headline -->
		</div><!-- /.portfolio -->
		
		<?php if( ( $postcount%2 ) == 0 ) { // 0, 2, 4...  ?>
			<div class="fix"><!----></div>
		<?php } ?>
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
			
	<?php if (function_exists('bizz_wp_pagenavi')) { ?>
	    <div class="fix"><!----></div>
		<div class="lpagination fpagination clearfix">
		    <?php bizz_wp_pagenavi_cp_bottom(); ?>
		</div>
    <?php } ?>
			
	<?php wp_reset_query(); ?>

</div><!-- /.cbox-area -->

<?php bizz_portfolio_after(); ?>
		
<?php } ?>