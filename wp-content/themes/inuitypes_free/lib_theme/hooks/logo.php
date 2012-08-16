<?php 

add_action( 'bizz_logo', 'bizz_logo_area' );

add_action( 'bizz_logo_inside', 'bizz_logo_spot' );

function bizz_logo_area() { 

?>

<?php bizz_logo_before(); ?>

<div class="logo-area box clearfix">
	
	<div class="logo-spot">
    	<?php bizz_logo_inside(); ?>
	</div><!--/.logo-spot-->
		
</div><!-- /.logo-area -->

<?php bizz_logo_after(); ?>

<?php } ?>