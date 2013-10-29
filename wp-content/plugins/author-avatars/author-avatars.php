<?php
/*
Plugin Name: Author Avatars List
Plugin URI: http://authoravatars.wordpress.com/
Description: Display lists of user avatars using <a href="widgets.php">widgets</a> or <a href="https://authoravatars.wordpress.com/documentation/">shortcodes</a>.
Version: 1.8.0
Author: <a href="http://bearne.ca">Paul Bearne</a>, <a href="http://mind2.de">Benedikt Forchhammer</a>
Text Domain: author-avatars
Domain Path: /translations/
*/

// The current version of the author avatars plugin. Needs to be updated every time we do a version step.
define('AUTHOR_AVATARS_VERSION', '1.8.0');

// List of all version, used during update check. (Append new version to the end and write an update__10_11 method on AuthorAvatars class if needed)
define( 'AUTHOR_AVATARS_VERSION_HISTORY', serialize( array( '0.1', '0.2', '0.3', '0.4', '0.5', '0.5.1', '0.6', '0.6.1', '0.6.2', '0.7', '0.7.1', '0.7.2', '0.7.3', '0.7.4', '0.8', '0.9', '1.0', '1.1', '1.2' ,'1.4.1', '1.5.0', '1.5.1','1.6.0','1.6.1','1.6.2','1.6.3','1.7.0','1.7.1','1.8.0' ) ) );

require_once('lib/AuthorAvatars.class.php');
$aa = new AuthorAvatars();

// can't add this in the class as it not found
	add_action('wp_ajax_AA_shortcode_paging', 'AA_shortcode_paging');
	add_action('wp_ajax_nopriv_AA_shortcode_paging', 'AA_shortcode_paging');
	function AA_shortcode_paging() {	
	   $nonce = $_POST['postCommentNonce'];
    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( ! wp_verify_nonce( $nonce, 'author-avatars-shortcode-paging-nonce' ) )
        die ( 'Busted!');
		// need to create class in the function scope
		$aaa = new AuthorAvatars();
		$aaa->init_shortcodes(); 
		echo substr(str_replace('<div class="shortcode-author-avatars">','',$aaa->author_avatars_shortcode->shortcode_handler($_POST)),0,-6);
	// echo	$aaa->author_avatars_shortcode->userlist->ajax_output();//. $aa->userlist->content .$aa->userlist->pagingHTML ;
	die(); 	
	}
?>