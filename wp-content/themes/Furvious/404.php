<?php get_header(); ?>
<div id="bigg">
	<div id="contentcontainer">
		<span class="contenttop"></span> 
		<div class="clear"></div>
		<ul id="content">
			<?php include (TEMPLATEPATH . '/notfound.php'); ?>
		</ul>
		<?php get_sidebar(); ?>
	</div>
	<span class="contentbottom"></span>
	<div class="clear"></div>	
	<div class="paginationbar">
		<?php
		include('wp-pagenavi.php');
		if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
		?>
	</div>
<?php get_footer(); ?>