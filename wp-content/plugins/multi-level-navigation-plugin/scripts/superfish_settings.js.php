<?php
require_once("../../../../wp-load.php");
header('Content-Type: text/javascript');

echo 'jQuery(document).ready(function() {
	jQuery("ul.sf-menu").superfish({
		animation:     {opacity:"show",height:"show"},  // fade-in and slide-down animation';

/*
if (get_option('suckerfish_delay') == 'on') {echo '
		delay:         1000,  // the delay in milliseconds that the mouse can remain outside a submenu without it closing';
}
*/

echo '
		delay:        ' . get_option('suckerfish_delay') . ',                            // delay on mouseout
		speed:        ';

if (get_option('suckerfish_superfish_speed') == 'instant') {echo '1';}
else {echo '"'.get_option('suckerfish_superfish_speed').'"';}

echo ',  // animation speed
		autoArrows:   "'.get_option('suckerfish_superfish_arrows').'",  // enable generation of arrow mark-up
		dropShadows:  "'.get_option('suckerfish_superfish_shadows').'"  // enable drop shadows
	});
});';
?>
