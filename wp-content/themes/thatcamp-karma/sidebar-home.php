<?php
/**
 * Sidebar Home
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php do_action( 'bp_before_sidebar' ); ?>
<div id="sidebar" role="complementary">
	<?php do_action( 'bp_inside_before_sidebar' ); ?>
	<!-- demo sidebar content -->

	<a href="<?php echo site_url(); ?>/help/plan" class="button campbutton">
		<span class="button-inner"><?php _e( 'Organize a THATCamp', 'thatcamp' ); ?></span>
	</a>

<a href="http://feedburner.google.com/fb/a/mailverify?uri=NewTHATCamps&amp;loc=en_US" class="email-thatcamplink"><?php _e( 'New THATCamps by Email', 'thatcamp' ); ?></a>

	<a href="<?php echo site_url(); ?>/camps/feed" class="rss-thatcamplink"><?php _e( 'New THATCamps by RSS', 'thatcamp' ); ?></a>

	<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar' ); ?>
	<?php endif; ?>

	<div id="twitterwidget-wrapper">
		<?php if ( is_active_sidebar( 'sidebar-twitter' ) ) : ?>
			<?php dynamic_sidebar( 'sidebar-twitter' ); ?>
		<?php endif; ?>
		<a href="https://twitter.com/thatcamp" class="button socialbutton">
			<span class="button-inner"><?php _e( 'Follow @thatcamp', 'thatcamp' ); ?></span>
		</a>
	</div>
	<?php do_action( 'bp_inside_after_sidebar' ); ?>

</div>
<?php do_action( 'bp_after_sidebar' ); ?>
