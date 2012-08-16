<?php 

add_action( 'bizz_front', 'bizz_front_area' );

add_action( 'bizz_wp_pagenavi_fr_top', 'bizz_wp_pagenavi' );
add_action( 'bizz_wp_pagenavi_fr_bottom', 'bizz_wp_pagenavi' );
add_action( 'bizz_subheadline_fr_inside', 'bizz_subheadline' );
add_action( 'bizz_post_meta_fr_inside', 'bizz_post_meta' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_front_area() { 

?>

<?php bizz_front_before(); ?>

<div class="cbox-area box clearfix">

    <?php 
	    $sticky=get_option('sticky_posts');
		if ( $GLOBALS['opt']['bizzthemes_front_number'] == '0' ) { $inumber = '-1'; } else { $inumber = $GLOBALS['opt']['bizzthemes_front_number']; }
	    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$args=array( 'post__not_in' => $sticky, 'caller_get_posts' => '1', 'posts_per_page' => $inumber, 'paged' => $paged );
		query_posts($args); 
	?>
	
	<?php if (function_exists('bizz_wp_pagenavi')) { ?>
		<div class="lpagination clearfix">
		    <?php bizz_wp_pagenavi_fr_top(); ?>
		</div>
    <?php } ?>
	
	<?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
	
	<?php if (($GLOBALS['opt']['bizzthemes_box_display'] == '2') && ($postcount % 2)) { $even = 'odd'; } else { $even = 'even'; } ?>
		
		<div class="single bsize_<?php echo stripslashes($GLOBALS['opt']['bizzthemes_box_display']); ?> <?php echo $even; ?>">
		    <div class="headline">
				<?php bizz_subheadline_fr_inside(); ?>
				<?php bizz_post_meta_fr_inside(); ?>
			</div><!-- /.headline -->
			<?php if ($GLOBALS['opt']['bizzthemes_thumb_show'] == 'true') {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_fi_width'],$GLOBALS['opt']['bizzthemes_fi_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_fi_align']);
			} ?>
			<div class="format_text">
				<?php if ( $GLOBALS['opt']['bizzthemes_front_full'] == 'true' ) { ?>
				    <?php the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).'')); ?>
                <?php } else { ?>
					<?php the_excerpt(); ?>
					<?php if ( $GLOBALS['opt']['bizzthemes_freadmore'] == 'true' ) { ?>
						<span class="read-more"><a rel="nofollow" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php echo stripslashes($GLOBALS['opt']['bizzthemes_freadmore_text']); ?></a></span>
                    <?php } ?>
				<?php } ?>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
		
		<?php if ($GLOBALS['opt']['bizzthemes_box_display'] == '2') { 
		    $count++; if ($count == 2) { $count = 0; 
		?>
		    <div class="single-sep clearfix"><!----></div>
		<?php } } elseif ($GLOBALS['opt']['bizzthemes_box_display'] == '1') { 
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
		    <?php bizz_wp_pagenavi_fr_bottom(); ?>
		</div>
    <?php } ?>
			
	<?php wp_reset_query(); ?>
	
</div><!-- /.cbox-area -->	

<?php bizz_front_after(); ?>

<?php } ?>