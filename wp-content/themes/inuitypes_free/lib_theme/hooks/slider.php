<?php 

add_action( 'bizz_slider', 'bizz_slider_area' );

function bizz_slider_area() {

?>

<?php 
    // Which post is last in array and which before last one	
	    $counting = 0;
		$allsticky = get_option( 'sticky_posts' );
		if ( $allsticky < 1) { $allsticky = get_posts('numberposts=-1'); } else { $allsticky = get_option( 'sticky_posts' ); }
		foreach ( $allsticky as $sticky ) { $counting++; }
		$item_before_last = $counting-1; 
	    $last_item = $counting;
?>

<?php bizz_slider_before(); ?> 

<div class="slider-area box clearfix">

<div id="loopedSlider">

	<div class="lpagination clearfix">
	    <ul class="lpag">
		    <li class="name"><span><?php echo stripslashes($GLOBALS['opt']['bizzthemes_slider_name']); ?></span></li>
	        <li><a href="#" class="previous">&laquo;</a></li>
	    </ul><!-- /.lpag -->
		<ul class="lpag pagination">
			<?php for ( $count = 1; $count <= round($last_item); $count++ ) { ?>
				<li><a href="#"><?php echo $count; ?></a></li>
			<?php } ?>
		</ul><!-- /.pagination -->
		<ul class="lpag">
	        <li><a href="#" class="next">&raquo;</a></li>
	    </ul><!-- /.lpag -->
	</div><!-- /.lpagination -->

	<?php 
	    $sticky=get_option('sticky_posts');
	    $args=array( 'post__in' => $sticky, 'caller_get_posts' => '1' );
		query_posts($args); 
	?>
	<?php if (have_posts()) : $count = 0; ?>
	    <div class="container" style="height:<?php echo stripslashes($GLOBALS['opt']['bizzthemes_slider_height']); ?>px;">
		<div class="slides">
		<?php while (have_posts()) : the_post(); $count++; ?>
		    <div id="slide-<?php echo $count; ?>" class="slide ssize_<?php echo stripslashes($GLOBALS['opt']['bizzthemes_slider_display']); ?>" style="min-height:<?php echo stripslashes($GLOBALS['opt']['bizzthemes_slider_height']); ?>px;">
			<div class="format_text">
			    <div class="fix"></div>
                <?php bizz_get_image('image',$GLOBALS['opt']['bizzthemes_si_width'],$GLOBALS['opt']['bizzthemes_si_height'],'thumbnail'); ?>
                <span class="cat">
				    <?php if ($GLOBALS['opt']['bizzthemes_nofollow_author'] == 'true') { $nofollow = 'rel="nofollow"'; } 
					    if ($GLOBALS['opt']['bizzthemes_slmeta_auth'] == 'true') {
						    echo '<a href="' . get_author_posts_url(get_the_author_ID()) . '" class="auth" '. $nofollow .'>' . get_the_author() . '</a>'; 
						}
					?>
					<?php if ($GLOBALS['opt']['bizzthemes_slmeta_cat'] == 'true') { ?>
				        <?php $category = get_the_category(); ?>
					    <a title="<?php echo $category[0]->cat_name; ?>" href="<?php echo get_category_link( $category[0]->cat_ID ); ?>" class="cat"><?php echo $category[0]->cat_name; ?></a>
					<?php } ?>
					<?php if ($GLOBALS['opt']['bizzthemes_slmeta_com'] == 'true') { ?>
					    <?php comments_popup_link('0', '1', '%'); ?>
					<?php } ?>
				</span>  
				<h2><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			</div><!-- /.format_text -->
			</div><!-- /.slide -->
		<?php endwhile; ?>
		</div><!-- /.slides -->
		</div><!-- /.container -->	
	<?php endif; ?>	
	<?php wp_reset_query(); ?>
	
</div><!-- /#loopedSlider -->

</div><!-- /.slider-area -->

<?php bizz_slider_after(); ?>

<?php } ?>