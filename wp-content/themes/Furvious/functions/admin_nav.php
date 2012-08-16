<?php
include ($includes . 'admin/css.php');
include ($includes . 'admin/header.php');
include ($includes . 'admin/options.php');

?>
<div class="wrap">
	<h3 class="title">
		<a href="?page=kreative_page_general">
			General Settings
		</a>
	</h3>
	
	<h3 class="title">
		<span>
			Navigation Settings
		</span>
	</h3>
	<div id="ajax-response"></div>
	
	<form method="post" name="kt_nav" target="_self">
		<?php echo kreative_form_builder('nav', $options['nav']); ?>
		<p class="submit">
			<input type="hidden" value="update" name="action" />
			<input class="button-primary" type="submit" name="submit" value="Update Navigation" />
		</p>
	</form>
	
	<h3 class="title">
		<a href="?page=kreative_page_layout">
			Layout Options
		</a>	
	</h3>
	
	<h3 class="title">
		<a href="?page=kreative_page_optimize">
			Optimization Settings
		</a>	
	</h3>
	

	
</div>