<?php
/**
 * 404 page
 *
 * @package thatcamp
 * @since thatcamp 1.0
 */
?>
<?php get_header('signup'); ?>
<div id="primary" class="main-content">
	<div id="content" role="main" >
		<?php do_action( 'bp_before_404' ); ?>
			<article id="post-0" class="post error404 not-found">
				<header class="post-header">
					<h1 class="post-title"><?php _e( 'Error 404 - Not Found.', 'thatcamp' ); ?></h1>
				</header>

				<div class="post-content">
					<p><?php _e( 'See thatcamp.org for a list of THATCamps', 'thatcamp' ); ?></p>
					<p><a href="http://thatcamp.org/registry"><?php _e( 'Register a new THATCamp', 'thatcamp' ); ?></a></p>
					<?php do_action( 'bp_404' ); ?>
				</div>
			</article>>
			<?php do_action( 'bp_after_404' ); ?>
	</div>
</div>
<?php get_footer(); ?>