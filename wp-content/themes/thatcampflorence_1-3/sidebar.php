</div>
<!-- end content -->

<!-- sidebar -->
<div id="sidebar">

<ul>

	<?php if ( function_exists('thatcamp_twitter_stream')): ?>

		<li id="xmt_twitter" class="widget widget_xmt_twitter">
		<h2 class="widgettitle">THATCamp on Twitter</h2>

		<div id="xmt_twitter_wid" class="xmt xmt_twitter">

		<?php echo thatcamp_twitter_stream(); ?>

		</div>

		</li>
	</ul>
	<ul>

	<?php endif; ?>

	<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar() ) : else : ?>
	
	<?php get_links_list(); // LINKS ?>
	<li><h2><?php _e('Categories'); // CATEGORIES - in caso di elenco senza link il testo eccede a destra ?></h2>
		<ul>
			<?php wp_list_categories('orderby=name&title_li=0'); ?>
		</ul>
	</li>
	<li><h2><?php _e('Archives'); // ARCHIVES ?></h2>
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</li>
	<li><h2><?php _e('Meta'); // META ?></h2>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<?php wp_meta(); ?>
		</ul>
	</li>
	<li><h2><?php _e('Calendar'); // CALENDAR ?></h2>
		<?php get_calendar(); ?>
	</li>
	<?php endif; ?>
</ul>

</div>
<!-- end sidebar -->