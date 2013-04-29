<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<footer id="colophon" role="contentinfo" style="margin:0 auto; width:90%;">
	<div id="logos" style="margin:0 auto;">
		<a href="http://chnm.gmu.edu/" title="chnm.gmu.edu" style="text-decoration:none;">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/rrchnm-logo.png" alt="Roy Rosenzweig Center for History and New Media - CHNM"/>
		</a>
		<a href="http://mellon.org/" title="The Andrew W. Mellon Foundation" style="text-decoration:none;">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/mellon.png" alt="Mellon" class="nospace" style="margin-left:50px;" />
		</a>
		<a href="http://mellon.org/" title="The Andrew W. Mellon Foundation" style="text-decoration:none;">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/mellonlogo.png" alt="Mellon" />
		</a>
	</div>
		<div class="site-info" style="opacity:0.5;margin-top:30px;">
		<a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a>.
			<!--<?php do_action( 'twentytwelve_credits' ); ?>
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentytwelve' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentytwelve' ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentytwelve' ), 'WordPress' ); ?></a>-->
		</div>	
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>