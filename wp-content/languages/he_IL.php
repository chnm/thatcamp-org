<?php

/* ------------------------------------------------------------------------- 
Better wp-admin styles (No more Tahoma!)
------------------------------------------------------------------------- */

function wph_admin() {
	$url = content_url();
	$url = $url . '/languages/he_IL.css';
	echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}

add_action('admin_head', 'wph_admin');
add_action('login_head', 'wph_admin');

