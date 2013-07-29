<?php
/**
 * The THATCamp Twenty Ten template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>
	</div><!-- #main -->

	<div id="footer" role="contentinfo">
		<div id="colophon">

<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	get_sidebar( 'footer' );
?>

			<div id="site-info">
				  All text and code on <a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a> is freely available for you to use, copy, adapt and distribute under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a> as long as you link to <a href="http://thatcamp.org">THATCamp.org</a> and the <a xmlns:cc="http://creativecommons.org/ns#" href="http://chnm.gmu.edu" rel="cc:attributionURL">Center for History and New Media</a>. The name "THATCamp" and the THATCamp logo are trademarks of the <a xmlns:cc="http://creativecommons.org/ns#" href="http://chnm.gmu.edu" rel="cc:morePermissions">Center for History and New Media</a> at <a href="http://gmu.edu">George Mason University</a>.<br /><br />
			</div><!-- #site-info -->

			<div id="site-generator">
				<?php do_action( 'twentyten_credits' ); ?>
				<a href="<?php echo esc_url( __('http://wordpress.org/', 'twentyten') ); ?>"
						title="<?php esc_attr_e('Semantic Personal Publishing Platform', 'twentyten'); ?>" rel="generator">
					<?php printf( __('Proudly powered by %s.', 'twentyten'), 'WordPress' ); ?>
				</a>
			</div><!-- #site-generator -->
                              <div id="cclicense">
                                        <a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License"  src="http://i.creativecommons.org/l/by/3.0/80x15.png" id="ccl" /></a>
                               </div>         
		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
