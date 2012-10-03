<?php

/* useful stuff */

// show or hide the admin bar
if ( ! function_exists( 'thatcamp_adminbar_hide' ) ) :
function thatcamp_register_menus($hide) {
	if ($show == "yes"){
		add_filter( 'show_admin_bar', '__return_false' );
	}
}
endif;

// thatcamp special functions
// thatcamp fallback menu 
function thatcamp_fallback_menu() {
	echo '<ul class="topmenu">';
    wp_list_pages('sort_column=menu_order&title_li=');
	echo '</ul>';
};

?>