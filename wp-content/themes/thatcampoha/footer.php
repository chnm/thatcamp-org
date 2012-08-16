<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */
?>
		<div class="clearfix"></div>
		</section><!-- #main -->
		<footer role="contentinfo">
<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	get_sidebar( 'footer' );
?>

		<div class="clearfix"></div>	
		</footer><!-- footer -->
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	wp_footer();
?>

	<script>

	//toggle the menu widgets for mobile devices
	jQuery("#content #menu").addClass("offscreen-widgets").append("<div id='close-menu'><a>Close Menu</a></div>");
	
	jQuery("#menu-top a").click(function () {
		jQuery("#content #menu").toggleClass("offscreen-widgets").toggleClass("onscreen-widgets");
    	});
	
	jQuery("#close-menu a").click(function () {
		jQuery("#content #menu").toggleClass("offscreen-widgets").toggleClass("onscreen-widgets");
    	});
    	
	</script>	
	
	
	</div> <!-- #outer -->
	</body>
</html>