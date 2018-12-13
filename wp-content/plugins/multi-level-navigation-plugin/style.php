<?php
// CODE IMPROVEMENT COPIED FROM http://www.wp-fun.co.uk/fun-with-widget-structures-01-beta/
require_once("../../../wp-load.php");
header('Content-Type: text/css; charset='.get_option('blog_charset').'');

/*
ORIGINAL CODE USED - CAUSED BUGS WITH SOME SERVERS
require('../../../wp-blog-header.php');
header('Content-type: text/css');*/

echo '/*
  CSS generated via the Multi-level Navigation Plugin ... https://geek.hellyer.kiwi/multi-level-navigation/

  If you would like a similar menu for your own site, then please try the PixoPoint Web Development
  CSS generator for creating flyout, dropdown and horizontal slider menus ... https://geek.hellyer.kiwi/suckerfish_css/


*** Main menu CSS code ***/
'.get_option('suckerfish_css');

$suckerfish_2_css = get_option('suckerfish_2_css');
if ($suckerfish_2_css != '') {echo '


/*** Second menu CSS code ***/
'.get_option('suckerfish_2_css');
}

?>
