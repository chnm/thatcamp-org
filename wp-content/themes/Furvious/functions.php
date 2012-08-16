<?php

$functions 	= TEMPLATEPATH . '/functions/';

require_once ($functions . 'class/kreative.php'); 

include_once ($functions . 'install.php');

if ( ! is_admin())
{
	include_once ($functions . 'header.php');
	include_once ($functions . 'footer.php');
}

if (is_admin()) {
	include_once ($functions . 'admin.php');
	include_once ($functions . 'form.php');
}


include_once ($functions . 'dynamic_sidebar.php');
include_once ($functions . 'widgets.php');

/* Theme Related Plugs */
include_once ($functions . 'furvious.php');
include_once ($functions . 'furvious-custom.php');
include_once ($functions . 'wp-related.php');

function kreative_pagenavi()
{
	include (TEMPLATEPATH . '/functions/wp-pagenavi.php');
}
