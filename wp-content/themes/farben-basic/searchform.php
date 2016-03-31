<?php
/**
 * Form to replace the default WordPress search form
 *
 * @since 1.0.0
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url() ); ?>">
	<label>
		<span class="sr-only"><?php _ex( 'Search for:', 'label', 'farben-basic' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'farben-basic' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
</form>