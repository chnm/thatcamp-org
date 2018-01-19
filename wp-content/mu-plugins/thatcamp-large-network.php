<?php

/**
 * Try to make sure that user counts and site counts are accurate when users > 10,000. 
 */

function thatcamp_large_network( $is_large_network, $component, $count, $network_id ) {
    return ( $count > 30000);
}
add_filter( 'wp_is_large_network', 'thatcamp_large_network', 10, 4 );
