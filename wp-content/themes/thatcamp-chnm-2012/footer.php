<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/* A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				if ( ! is_404() )
					get_sidebar( 'footer' );
			?>

	<div id="site-info">
		<p>All text and code on <a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
<?php bloginfo( 'name' ); ?></a> is freely available for you to use, copy, adapt and distribute under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a> as long as you link to <a href="http://thatcamp.org">THATCamp.org</a> and the <a xmlns:cc="http://creativecommons.org/ns#" href="http://chnm.gmu.edu" rel="cc:attributionURL">Roy Rosenzweig Center for History and New Media</a>. The name "THATCamp" and the THATCamp logo are trademarks of the <a xmlns:cc="http://creativecommons.org/ns#" href="http://chnm.gmu.edu" rel="cc:morePermissions">Roy Rosenzweig Center for History and New Media</a> at <a href="http://gmu.edu">George Mason University</a>. Theme by <a href="http://www.designtank.ws">Chris A. Raymond</a> based on the TwentyEleven theme by <a href="http://automattic.com">Automattic</a>. <br />

	<a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License" src="http://i.creativecommons.org/l/by/3.0/80x15.png" id="ccl" /></a></p>
	</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>