<?php
/*
Plugin Name: Limit Post Revisions Network Option
Plugin URI: http://dsader.snowotherway.org/wordpress-plugins/limit-post-revisions-network-option/
Description: Adds Network SuperAdmin-->Options to limit, or disable, the number of post revisions and autosave interval.
Author: D. Sader
Version: 3.0.1
Author URI: http://dsader.snowotherway.org
Network: true

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

*/
class ds_revisions_limit {

	function ds_revisions_limit() {
	}

	function limit($limit) {
		$ds_revisions_number = get_site_option('ds_revisions_limit');
		if ( !$ds_revisions_number ) {
			$ds_revisions_limit = true;
		}
		if ( $ds_revisions_number == 0) {
			$ds_revisions_limit = false;
		} else {	
			$ds_revisions_limit = $ds_revisions_number;
		}
	define('WP_POST_REVISIONS', $ds_revisions_limit);
		$ds_autosave_limit = get_site_option('ds_autosave_limit');
		if ( !$ds_autosave_limit ) {
			$ds_autosave_limit = 300;
		}
	define('AUTOSAVE_INTERVAL', $ds_autosave_limit);
	}

	function options_page() {
		$ds_revisions_limit = get_site_option('ds_revisions_limit');
		if ( !$ds_revisions_limit ) {
			$ds_revisions_limit = 3;
		}
		$ds_autosave_limit = get_site_option('ds_autosave_limit');
		if ( !$ds_autosave_limit ) {
			//300s = 5 mins
			$ds_autosave_limit = 300;
		}
		
		echo '<h3>Limit Post Revisions</h3>';
		echo '<table class="form-table">
			<tr valign="top"> 
			<th scope="row">' . __('Post Revisions') . '</th>
			<td><input type="text" name="ds_revisions_limit" id="ds_revisions_limit" style="width: 15%" value="'.$ds_revisions_limit.'" /><br /><small>' . __('Maximum number of revisions per post. Enter 0 to disable post revisions(3 recommended).') . '</small></td>
			</tr>
			<tr valign="top"> 
			<th scope="row">' . __('Autosave Interval') . '</th>
			<td><input type="text" name="ds_autosave_limit" id="ds_autosave_limit" style="width: 15%" value="'.$ds_autosave_limit.'" /><br /><small>' . __('Autosave interval in seconds. Enter 0 to disable autosave(300 recommended, 60 is WP default).') . '</small></td>
			</tr>
			</table>'; 
	}

	function update() {
		update_site_option('ds_revisions_limit', $_POST['ds_revisions_limit']);
		update_site_option('ds_autosave_limit', $_POST['ds_autosave_limit']);
	}
}

if (class_exists("ds_revisions_limit")) {
	$ds_revisions_limit = new ds_revisions_limit();	
	}

if (isset($ds_revisions_limit)) {
	add_filter( 'plugins_loaded', array(&$ds_revisions_limit, 'limit'));
	add_action( 'update_wpmu_options', array(&$ds_revisions_limit, 'update'));
	add_action( 'wpmu_options', array(&$ds_revisions_limit, 'options_page'));
}
?>