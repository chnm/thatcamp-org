<?php
/**
 * Stream template
 *
 * @package thatcamp
 * @since thatcamp 1.0
 *
 * Template Name: Registry Template
 */

add_action( 'wp_enqueue_scripts', 'thatcamp_admin_scripts' );

?>

<?php get_header(); ?>

<div id="primary" class="main-content">
	<div id="content" class="clearfix feature-box thatcamp-registry" role="main">
		<form>
		    <?php thatcamp_country_picker() ?>
		    <input type="submit" value="Submit">
		  </form>
	</div>
</div>

<?php get_sidebar( 'stream' ); ?>
<?php get_footer() ?>
