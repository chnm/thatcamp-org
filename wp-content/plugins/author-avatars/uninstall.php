<?php
// exit if we are not uninstalling the plugin...
if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

// delete plugin options
delete_option('author_avatars_version');
delete_option('author_avatars_settings');
if (function_exists('delete_site_option')) {
	delete_site_option('author_avatars_wpmu_settings');
}
delete_option('multiwidget_author_avatars');

?>