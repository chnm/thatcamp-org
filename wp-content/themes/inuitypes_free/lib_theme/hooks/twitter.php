<?php 

add_action( 'bizz_twitter', 'bizz_twitter_area' );

function bizz_twitter_area() {

?>

<?php bizz_twitter_before(); ?> 

<div class="twitter-area box clearfix">
	
	<div class="twitter-icon">
	    <a href="http://www.twitter.com/<?php echo stripslashes($GLOBALS['opt']['bizzthemes_twitter_uname']); ?>" title="<?php echo stripslashes(__('Follow us on Twitter', 'bizzthemes')); ?>" class="twitter-logo" >
		    <img src="<?php if ( $GLOBALS['opt']['bizzthemes_twitter_ico'] <> "" ) { echo $GLOBALS['opt']['bizzthemes_twitter_ico']; } else { echo BIZZ_THEME_IMAGES .'/twittermoby-trans.png'; } ?>" alt="<?php echo stripslashes(__('Follow us on Twitter', 'bizzthemes')); ?>" />
		</a> 
	</div><!-- /.twitter-icon -->
	<div class="twitter-content">
	    <div class="twitter-spot-outer">
		<div class="twitter-spot-inner clearfix">
			<?php hosted_twitter_script('',$GLOBALS['opt']['bizzthemes_twitter_uname'],$GLOBALS['opt']['bizzthemes_twitter_count']); ?>
		</div><!-- /.twitter-spot-inner -->
		</div><!-- /.twitter-spot-outer -->
	</div><!-- /.twitter-content -->
	
</div><!-- /.twitter-area -->

<?php bizz_twitter_after(); ?>

<?php } ?>