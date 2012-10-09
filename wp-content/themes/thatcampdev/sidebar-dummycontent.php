<?php
/**
 * Sidebar BuddyPress
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>	
<?php do_action( 'bp_before_sidebar' ); ?>
<div id="sidebar" role="complementary">	
	<?php do_action( 'bp_inside_before_sidebar' ); ?>
	<!-- demo sidebar content -->
	<a class="rss-thatcamplink">RSS</a>
	<aside id="dummytext" class="widget">
		<h3 class="widgettitle">Widget title here</h3>
		<p>
			Donec semper quam scelerisque tortor dictum gravida. In hac habitasse platea dictumst. Nam pulvinar, odio sed rhoncus suscipit, sem diam ultrices mauris, eu consequat purus metus eu velit. Proin metus odio, aliquam eget molestie nec, gravida ut sapien. Phasellus quis est sed turpis sollicitudin venenatis sed eu odio. Praesent eget neque eu eros interdum malesuada non vel leo.
		</p>
	</aside>
	<aside id="dummylist" class="widget">
		<h3 class="widgettitle">Widget title here</h3>
		<ul>
			<li>List item for dummy content</li>
			<li>List item for dummy content</li>
			<li>List item for dummy content</li>
			<li>List item for dummy content</li>
			<li>List item for dummy content</li>
			<li>List item for dummy content</li>
			<li>List item for dummy content</li>
		</ul>
	</aside>
	<aside id="dummytexttwo" class="widget">
		<h3 class="widgettitle">Widget title here</h3>
		<p>
			Donec semper quam scelerisque tortor dictum gravida. In hac habitasse platea dictumst. Nam pulvinar, odio sed rhoncus suscipit, sem diam ultrices mauris, eu consequat purus metus eu velit. Proin metus odio, aliquam eget molestie nec, gravida ut sapien. Phasellus quis est sed turpis sollicitudin venenatis sed eu odio. Praesent eget neque eu eros interdum malesuada non vel leo.
		</p>
	</aside>
	
	<?php do_action( 'bp_inside_after_sidebar' ); ?>
</div>
<?php do_action( 'bp_after_sidebar' ); ?>