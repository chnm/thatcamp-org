<?php
/**
 * Footer
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
				</div>
			</div>
			<?php do_action( 'bp_after_container' ); ?>
			<div id="logos-wrapper">
				<div id="logos">
					<a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/cc-logo.png" alt="Creative Commons"/>
					</a>
					<a href="http://mellon.org/" title="Mellon">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/mellon.png" alt="Mellon"/>
					</a>
					<a href="http://mellon.org/" title="Mellon">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/mellonlogo.png" alt="Mellon"/>
					</a>
					<a href="http://chnm.gmu.edu/" title="CHNM.gmu.edu">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/images/RCHN-logo.png" alt="RCHN"/>
					</a>
				</div>
			</div>
			<?php do_action( 'bp_before_footer'   ); ?>
			<div id="footer-wrapper">
			<footer id="bottom-footer" class="wrapper" role="contentinfo">
				<nav id="bottom-nav" role="navigation">
					<div id="copyright">&copy; <?php the_time('Y')?>  </div>
					<div id="credits"><?php do_action( 'bp_dtheme_credits' ); ?></div>
					<?php wp_nav_menu( array(
						'theme_location' => 'bottom', 
						'menu_class' => 'bottom_menu',
						'container' => ''
					));?>
				</nav>
				<?php do_action( 'bp_footer' ); ?>
			</footer>
		</div>
		<?php do_action( 'bp_after_footer' ); ?>
		<?php wp_footer(); ?>
	</body>
</html>