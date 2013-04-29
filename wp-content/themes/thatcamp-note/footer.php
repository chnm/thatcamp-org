<?php
/**
 * Footer
 *
 * @package notecamp
 * @since notecamp 1.0
 */
?>
<div id="logos-wrapper">
				<div id="logos"  class="wrapper">
					<a href="http://chnm.gmu.edu/" title="CHNM.gmu.edu">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/RCHN-logo.png" alt="RCHN"/>
					</a>

					<a href="http://mellon.org/" title="Mellon">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/mellon.png" alt="Mellon" class="nospace"/>
					</a>
					<a href="http://mellon.org/" title="Mellon">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/mellonlogo.png" alt="Mellon"/>
					</a>

				</div>
			</div>
		</div>
		<footer id="bottom-footer" role="contentinfo">
				<nav id="bottom-nav" role="navigation">
				<!-- add in themename call -->
					<div id="copyright">
<a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a>.					<?php wp_nav_menu( array(
						'theme_location' => 'bottom',
						'menu_class' => 'bottom_menu',
						'container' => ''
					)); ?>
				</nav>
			</footer>
		<?php wp_footer(); ?>
	</body>
</html>