<?php

/* useful stuff */

// thatcamp special functions
// thatcamp fallback menu 
function thatcamp_fallback_menu() {
	echo '<ul class="topmenu">';
    wp_list_pages('sort_column=menu_order&title_li=');
	echo '</ul>';
};

?>