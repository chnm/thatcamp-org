<?php

/**
 * thatcamp.org modifications of Participad
 */

function thatcamp_participad_cookie_domain( $domain ) {
	return 'thatcamp.org';
}
add_filter( 'participad_cookie_domain', 'thatcamp_participad_cookie_domain' );
