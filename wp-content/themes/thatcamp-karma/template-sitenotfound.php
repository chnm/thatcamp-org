<?php
/**
 * Documents template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 *
 * Template Name: Site Not Found
 */
?>
<?php get_header(); ?>
<div id="primary-documents" class="main-content">
	<div id="content" class="clearfix feature-box" role="main">
		<div id="page" role="main">
			<h2>Site Not Found</h2>

			<?php $referer = isset( $_GET['referer'] ) ? urldecode( $_GET['referer'] ) : '' ?>

			<p>The THATCamp site you attempted to visit<?php if ( $referer ) : ?>, <strong><?php echo $referer ?></strong>,<?php endif ?> does not exist.</p>

			<p>Visit the <a href="<?php echo home_url( 'camps' ) ?>">THATCamp Directory</a> to find a THATCamp.</p>
		</div>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer() ?>
