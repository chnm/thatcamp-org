<?php 

add_action( 'bizz_head_grid', 'bizz_head_grid_area' );

function bizz_head_grid_area() { 
?>
<div class="container_12 containerbar">
    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { bizz_sidebar(); } ?>
    <div class="grid_8 mainbar <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignleft' ) { ?>mainbar_right<?php } ?> equalh">	
<?php }

add_action( 'bizz_foot_grid', 'bizz_foot_grid_area' );

function bizz_foot_grid_area() { 
?>
    </div><!-- /.grid_8 -->
    <?php if ( $GLOBALS['opt']['bizzthemes_sidebar_align'] == 'alignright' ) { bizz_sidebar(); } ?>
</div><!-- /.container_12 -->
<?php } ?>