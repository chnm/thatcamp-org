<?php
/* wp-config.php modifications */
$wp_default_secret_key = 'wprowadz tutaj unikatowa fraze';

/* filters */
add_filter( 'date_i18n', 'date_declension' );

/* functions */
function date_declension( $date )
{

	$month_names = array( '|Styczeń|', '|Luty|', '|Marzec|', '|Kwiecień|', '|Maj|', '|Czerwiec|', '|Lipiec|', '|Sierpień|', '|Wrzesień|', '|Październik|', '|Listopad|', '|Grudzień|' );
	$month_names_genitive = array( 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia' );
	$month_names_locative = array( 'styczniu', 'lutym', 'marcu', 'kwietniu', 'maju', 'czerwcu', 'lipcu', 'sierpniu', 'wrześniu', 'październiku', 'listopadzie', 'grudniu' );

	/* "1 Styczeń..." -> "1 stycznia..." */
	$pattern = '|^\d{1,2}\ \w+|';
	if ( preg_match( $pattern, $date ) == 1 ) return preg_replace( $month_names, $month_names_genitive, $date );

	return $date;
}
?>
