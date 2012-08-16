<?php
/**
 * Init functions
 */

function ms_global_search_install_multisite_notice() {
	echo '<div id="message" class="error fade"><p>';
	_e('<strong>Multisite Global Search</strong></a> requires multisite installation. Please <a href="http://codex.wordpress.org/Create_A_Network">create a network</a> first, or <a href="plugins.php">deactivate Multisite Global Search</a>.', 'ms-global-search' );
	echo '</p></div>';
}

function ms_global_search_active_widget_notice() {
	echo '<div id="message" class="error fade"><p>';
	_e( '<strong>Multisite Global Search Widget</strong></a> not support default permalinks. Please <a target="_blank" href="options-permalink.php">Change Permalinks</a> first.', 'ms-global-search' );
	echo '</p></div>';
}

/**
 * Add style file if it exists.
 */
function ms_global_search_style() {
	$styleurl = MSGLOBALSEARCH_URL . '/style.css';
	$styledir = MSGLOBALSEARCH_DIR . '/style.css';
	
	if( file_exists( $styledir ) )
		wp_enqueue_style( 'ms_global_search_css_style', $styleurl );
}

/**
 * Init search variables.
 */
function ms_global_search_queryvars( $qvars ) {
  $qvars[] = 'mssearch';
  $qvars[] = 'mswhere';
  $qvars[] = 'msp';

  return $qvars;
}
