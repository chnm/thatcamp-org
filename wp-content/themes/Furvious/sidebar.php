<div id="sidebar">
	<div class="searchbox">
		<form method="get" id="searchform" class="fleft" action="<?php bloginfo('home'); ?>/">
 		<input type="text" value="" name="s" id="searchformfield" class="fleft"/>
 		<button type="submit" id="searchformsubmit" class="fright">Find!</button>
 		</form>
	</div>
	<div class="clear"></div>
	<div class="feedbar">
		<p class="feedbaricon"><a href="<?php bloginfo('rss2_url'); ?>">Subscribe to our RSS feed</a></p>
	</div>
	<div class="pr30 pl30">
	
	<?php  include (TEMPLATEPATH . '/functions/wp-featured.php'); ?>
	 
		 	
		 	 
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			
			
			<h2 class="sideheading">Categories</h2>
			<ul class="sidebarlist"> 
				<?php wp_list_categories('title_li=&hierarchical=0&depth=0' ); ?> 
			</ul>
		 
			
			<h2 class="sideheading">Archives</h2>
			<ul class="sidebarlist">
				<?php wp_get_archives('type=monthly'); ?>
			</ul>
			
			 
		<?php endif; ?>
		 
	</div>  
</div>  