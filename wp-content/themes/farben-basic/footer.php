<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the main and #page div elements.
 *
 * @since 1.0.0
 */
?>
		</main><!-- main -->

		<?php if ( is_active_sidebar( 'extended-footer' ) ) { ?>
		<div id="extended-footer">
			<div class="container">
				<div class="row">
					<?php dynamic_sidebar( 'extended-footer' ); ?>
				</div><!-- .row -->
			</div>
		</div>
		<?php } ?>

		<footer id="footer" role="contentinfo">
			<div id="footer-content" class="container">
				<div class="row">
					<div class="copyright col-lg-12">
						<p class="pull-left">Copyright &copy; <?php echo date( 'Y' ); ?> <a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>. All Rights Reserved.</p>
						<p class="pull-right">
							<?php printf( __( 'The %s Theme by %s.', 'farben-basic' ), BAVOTASAN_THEME_NAME, '<a href="https://themes.bavotasan.com/themes/farben-wordpress-theme/">bavotasan.com</a>' ); ?>
						</p>
					</div><!-- .col-lg-12 -->
				</div><!-- .row -->
			</div><!-- #footer-content.container -->
		</footer><!-- #footer -->

	</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>