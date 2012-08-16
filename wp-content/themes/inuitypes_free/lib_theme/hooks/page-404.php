<?php 

add_action( 'bizz_page_404', 'bizz_page_404_area' );

add_action( 'bizz_404_error_inside', 'bizz_404_error' );

function bizz_page_404_area() { 


?>

<?php bizz_page_404_before(); ?>

<div class="title-area box clearfix">
    <?php bizz_headline(); ?>
</div><!-- /.title-area -->

<div class="cbox-area box clearfix">
				
		<div class="single clearfix">
			<?php bizz_404_error_inside(); ?>
		</div><!-- /.single -->
	
</div><!-- /.cbox-area -->

<?php bizz_page_404_after(); ?>
		
<?php } ?>