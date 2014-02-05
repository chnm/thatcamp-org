<?php
// This is an include file, all normal WordPress functions will still work.
// Because the plugin is already deactivated it won't recognize any class declarations.


if (! defined('ABSPATH') && ! defined('WP_UNINSTALL_PLUGIN'))
	exit();

global $wpdb;
if ('extended-categories-widget' == dirname($file)) {
	delete_option('avhec');
	delete_option('avhec-tax_meta');
	$db__used_by_plugin[] = $wpdb->prefix . 'avhec_category_groups';
	foreach($db__used_by_plugin as $table) {
		$wpdb->query('DROP TABLE IF EXISTS `' . $table . '`');
	}
	$result = $wpdb->query("ALTER TABLE $wpdb->terms DROP `avhec_term_order`");
	$cat_terms = get_terms('avhec_catgroup', array('hide_empty'=>false));
	foreach($cat_terms as $cat_term){
		wp_delete_term($cat_term->term_id, 'avhec_catgroup');
	}
}