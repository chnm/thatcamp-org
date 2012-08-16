<?php
include ($functions . 'admin/css.php');
include ($functions . 'admin/header.php');
include ($functions . 'admin/options.php');

?>
<div class="wrap">
	<h3 class="title">
		<span>
			General Settings
		</span>
	</h3>
	<div id="ajax-response"></div>
	
	<form method="post" name="kt_option" target="_self">
		<?php echo kreative_form_builder('general', $options['general']); ?>
		<p class="submit">
			<input type="hidden" value="update" name="action" />
			<input class="button-primary" type="submit" name="submit" value="Update General" />
		</p>
	</form>
	
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
		<a href="?page=kreative_page_optimize">
			Optimization Settings
		</a>	
	</h3>
	

</div>

