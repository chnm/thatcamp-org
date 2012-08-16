<?php 

add_action( 'bizz_single', 'bizz_single_area' );

add_action( 'bizz_headline_si_inside', 'bizz_headline' );
add_action( 'bizz_post_meta_si_inside', 'bizz_post_meta' );
add_action( 'bizz_breadcrumb_si_inside', 'bizz_breadcrumb' );
add_action( 'comments_template_si_inside', 'comments_template' );
add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_single_area() {

?>

<?php bizz_single_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline_si_inside(); ?>
</div><!-- /.title-area -->

<?php if ( $GLOBALS['opt']['bizzthemes_breadcrumbs'] == 'true') { ?>
<div class="breadcrumbs-area box clearfix">
	<?php bizz_breadcrumb_si_inside(); ?>
</div><!-- /.breadcrumbs-area -->
<?php } ?>

<div class="cbox-area box clearfix">
		
	<?php if (have_posts()) : $count = 0; ?>
	<?php while (have_posts()) : the_post(); $postcount++;?>
				
		<div class="single sing">
		    <div class="headline">
				<?php bizz_post_meta_si_inside(); ?>
			</div><!-- /.headline -->
			<?php if ($GLOBALS['opt']['bizzthemes_thumb_show'] == 'true' && $GLOBALS['opt']['bizzthemes_image_single'] == 'true') {
				bizz_get_image('image',$GLOBALS['opt']['bizzthemes_single_width'],$GLOBALS['opt']['bizzthemes_single_height'],'thumbnail '.$GLOBALS['opt']['bizzthemes_single_align']);
			} ?>
			<div class="format_text">
				<?php the_content(__(''.stripslashes($GLOBALS['opt']['bizzthemes_readmore_text']).'')); ?>
				<p class="meta">
				    <?php seo_post_tags(); ?>
					<?php seo_post_cats(); ?>
				</p>
			</div><!-- /.format_text -->
		</div><!-- /.single -->
				
	<?php endwhile; else: ?>
			
        <div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
				
	<?php endif; ?>
						
	<?php comments_template_si_inside(); ?>
		
</div><!-- /.cbox-area -->

<?php bizz_single_after(); ?>
		
<?php } ?>