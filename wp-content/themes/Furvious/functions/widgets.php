<?php

function kreative_category_widget()
{
	?>
	<div class="widgets">
	<h2 class="sideheading">Categories</h2>
	<ul class="sidebarlist"> 
		<?php wp_list_categories('title_li=&hierarchical=0&depth=0' ); ?> 
	</ul>
	<div class="clear"></div>
	</div>
	<?php 
}

function kreative_archive_widget()
{
	?>
	<div class="widgets">
	<h2 class="sideheading">Archives</h2>
	<ul class="sidebarlist">
		<p><?php wp_get_archives('type=monthly'); ?></p>
	</ul>
	<div class="clear"></div>
	</div>
	<?php 
}

register_sidebar_widget('Furvoius Archive', 'kreative_archive_widget');
register_sidebar_widget('Furvoius Category', 'kreative_category_widget');