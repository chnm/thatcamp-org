<?php
/**
 * Search form
 *
 * @package thatcamp
 * @subpackage Template
 * @since thatcamp 1.0
 */
?>
<?php do_action( 'bp_before_blog_search_form' ); ?>
<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<label for="s" class="assistive-text"><?php _e( 'Search', 'testtheme' ); ?></label>
	<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search &hellip;', 'testtheme' ); ?>" />
	<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'testtheme' ); ?>" />
	<?php do_action( 'bp_blog_search_form' ); ?>
</form>
<?php do_action( 'bp_after_blog_search_form' ); ?>