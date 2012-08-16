<!-- begin l_sidebar -->

<div id="l_sidebar">

	<ul id="l_sidebarwidgeted">
	<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
	
	<li>
	<h5>Recently Written</h5>
		<ul>
			<?php get_archives('postbypost', 10); ?>
		</ul>
	</li>

	<li>
	<h5>Categories</h5>
		<ul>
			<?php wp_list_cats('sort_column=name'); ?>
		</ul>
	</li>
		
	<li>
	<h5>Archives</h5>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>

	
	
	<?php endif; ?>
	</ul>
	
</div>

<!-- end l_sidebar -->