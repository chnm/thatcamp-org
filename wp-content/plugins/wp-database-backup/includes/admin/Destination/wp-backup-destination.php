<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
include plugin_dir_path(__FILE__) . '/FTP/FTP_form.php';
include plugin_dir_path(__FILE__) . '/Email/Email_form.php';
include plugin_dir_path(__FILE__) . '/Google/Google_form.php';
include plugin_dir_path(__FILE__) . '/S3/S3_form.php';
include plugin_dir_path(__FILE__) . '/Dropbox/Dropbox_form.php';

