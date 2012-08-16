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
		<a href="?page=kreative_page_nav">
			Navigation Settings
		</a>
	</h3>
	
	<h3 class="title">
		<a href="?page=kreative_page_layout">
			Layout Options
		</a>	
	</h3>
	
	<h3 class="title">
		<span>
			Optimization Settings
		</span>
	</h3>
	<div id="ajax-response"></div>
	
	<form method="post" name="kt_optimize" target="_self">
		<?php echo kreative_form_builder('optimize', $options['optimize']); ?>
		<p class="submit">
			<input type="hidden" value="update" name="action" />
			<input class="button-primary" type="submit" name="submit" value="Update Optimization" />
		</p>
	</form>
	

	
</div>