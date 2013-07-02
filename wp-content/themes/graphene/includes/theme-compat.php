<?php
if ( ! function_exists( 'the_remaining_content' ) ) :
/**
 * the_remaining_content() added in WP 3.6
 *
 * @package Graphene
 * @since 1.9
 */
function the_remaining_content(){
	the_content();
}
endif;