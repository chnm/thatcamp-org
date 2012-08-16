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
		<span>
			Layout Options
		</span>
	</h3>
	<div id="ajax-response"></div>
	
	<form method="post" name="kt_layout" target="_self">
		<?php echo kreative_form_builder('layout', $options['layout']); ?>
		<p class="submit">
			<input type="hidden" value="update" name="action" />
			<input class="button-primary" type="submit" name="submit" value="Update Layout" />
		</p>
	</form>
	
	<h3 class="title">
		<a href="?page=kreative_page_optimize">
			Optimization Settings
		</a>	
	</h3>
	

	
</div>