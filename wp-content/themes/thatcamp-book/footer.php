<?php
/**
 * Footer
 *
 * @package bookcamp
 * @since bookcamp 1.0
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
			<footer id="bottom-footer" role="contentinfo">
				<nav id="bottom-nav" role="navigation">
				<!-- add in themename call -->
					<div id="copyright">&copy; <?php the_time('Y')?>  </div>
					<?php wp_nav_menu( array(
						'theme_location' => 'bottom',
						'menu_class' => 'bottom_menu',
						'container' => ''
					)); ?>
				</nav>
			</footer>
		</div>
	</div>
		<?php wp_footer(); ?>
	</body>
</html>
