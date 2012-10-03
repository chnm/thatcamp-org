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
			<?php do_action( 'bp_before_footer'   ); ?>
			<div id="footer-wrapper">
			<footer id="bottom-footer" class="wrapper" role="contentinfo">
				<nav id="bottom-nav" role="navigation">
				<!-- add in themename call -->
					<div id="copyright">&copy; <?php the_time('Y')?>  </div>
					<div id="credits"><?php do_action( 'bp_dtheme_credits' ); ?></div>
					<?php /*wp_nav_menu( array(
						'theme_location' => 'bottom', 
						'menu_class' => 'bottom_menu',
						'container' => ''
					));*/ ?>
				</nav>
				<?php do_action( 'bp_footer' ); ?>
			</footer>
		</div>
		<?php do_action( 'bp_after_footer' ); ?>
		<?php wp_footer(); ?>
	</body>
</html>