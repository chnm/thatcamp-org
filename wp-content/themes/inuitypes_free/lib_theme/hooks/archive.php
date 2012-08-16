<?php 

add_action( 'bizz_archive', 'bizz_archive_area' );

add_action( 'bizz_headline_a_inside', 'bizz_headline' );
add_action( 'bizz_breadcrumb_a_inside', 'bizz_breadcrumb' );
add_action( 'bizz_wp_pagenavi_a_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_a_bottom', 'bizz_wp_pagenavi' );
add_action( 'bizz_subheadline_a_inside', 'bizz_subheadline' );
add_action( 'bizz_post_meta_a_inside', 'bizz_post_meta' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_archive_area() { 

?>

<?php if (is_paged()) $is_paged = true; ?>

<?php bizz_archive_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline_a_inside(); ?>
</div><!-- /.title-area -->

<?php if ( $GLOBALS['opt']['bizzthemes_breadcrumbs'] == 'true') { ?>
<div class="breadcrumbs-area box clearfix">
	<?php bizz_breadcrumb_a_inside(); ?>
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area box clearfix">
	
	<?php if (function_exists('bizz_wp_pagenavi') && is_paged()) { ?>
		<div class="lpagination clearfix">
		    <?php bizz_wp_pagenavi_a_top(); ?>
		</div>
    <?php } ?>
	
	<?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
	
	    <?php if (($GLOBALS['opt']['bizzthemes_gbox_display'] == '2') && ($postcount % 2)) { $even = 'odd'; } else { $even = 'even'; } ?>
		<div class="single bsize_<?php echo stripslashes($GLOBALS['opt']['bizzthemes_gbox_display']); ?> <?php echo $even; ?>">
		    <div class="headline">
				<?php bizz_subheadline_a_inside(); ?>
				<?php bizz_post_meta_a_inside(); ?>
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
		
		<?php if ($GLOBALS['opt']['bizzthemes_gbox_display'] == '2') { 
		    $count++; if ($count == 2) { $count = 0; 
		?>
		    <div class="single-sep clearfix"><!----></div>
		<?php } } elseif ($GLOBALS['opt']['bizzthemes_gbox_display'] == '1') { 
		    $count++; if ($count == 1) { $count = 0; 
		?>
		    <div class="single-sep clearfix"><!----></div>
		<?php } } ?>
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
	
	<?php if (function_exists('bizz_wp_pagenavi')) { ?>
	    <div class="fix"><!----></div>
		<div class="lpagination fpagination clearfix">
		    <?php bizz_wp_pagenavi_a_bottom(); ?>
		</div>
    <?php } ?>
	
</div><!-- /.cbox-area -->	

<?php bizz_archive_after(); ?>
		
<?php } ?>