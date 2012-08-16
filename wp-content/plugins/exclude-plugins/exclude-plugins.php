<?php
/*
Plugin Name: Exclude Plugins
Plugin URI: http://itx.web.id/wordpress/plugins/exclude-plugins/
Description: Exclude plugins from appearing in plugins menu for normal user in WordPress multisite. This plugin is useful if you want to use plugins only for Super Admins while enabling some other plugins for normal user.
Author: itx
Version: 1.1.3
Author URI: http://itx.web.id
Network: true

Some lines inspired by Blue Network Plugins by r-a-y
*/
/*  Copyright 2010 itx (http://itx.web.id/)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// filter plugins
add_filter( 'all_plugins', 'exclude_plugins' );
function exclude_plugins($plugins) {
	if (is_super_admin()) return $plugins;
	if (exclude_plugins_get_option('exclude_new')) return exclude_plugins_included($plugins);
	else return array_diff_assoc($plugins, exclude_plugins_excluded($plugins));
}

function exclude_plugins_excluded($plugins){
	$excluded_plugins = array();
	$excluding=exclude_plugins_get_option('excluded_plugins');
	if ($excluding){
		foreach ( (array) $plugins as $plugin => $plugin_data) {
			if (in_array($plugin,$excluding)){$excluded_plugins[$plugin]=$plugin_data;}
		}
	}
	return $excluded_plugins;
}

function exclude_plugins_included($plugins){
	$included_plugins = array();
	$including = exclude_plugins_get_option('included_plugins');
	if ($including){
		foreach ( (array) $plugins as $plugin => $plugin_data) {
			if (in_array($plugin,$including)){$included_plugins[$plugin]=$plugin_data;}
		}
	}
	return $included_plugins;
}

if (function_exists('print_plugins_table')) {
	add_action('admin_menu', 'exclude_plugins_menu');
} else {
	add_action('network_admin_menu', 'exclude_plugins_menu');

	function print_plugins_table($plugins, $context = '') {
		$checkbox = ! in_array( $context, array( 'mustuse', 'dropins' ) ) ? '<input type="checkbox" />' : '';
?>
<table class="widefat" cellspacing="0" id="<?php echo $context ?>-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column check-column"><?php echo $checkbox; ?></th>
		<th scope="col" class="manage-column"><?php _e('Plugin'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column check-column"><?php echo $checkbox; ?></th>
		<th scope="col" class="manage-column"><?php _e('Plugin'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
	</tr>
	</tfoot>

	<tbody class="plugins">
<?php

		if ( empty($plugins) ) {
			echo '<tr>
				<td colspan="3">' . __('No plugins to show') . '</td>
			</tr>';
		}
		foreach ( (array)$plugins as $plugin_file => $plugin_data) {

			$checkbox = "<input type='checkbox' name='checked[]' value='" . esc_attr($plugin_file) . "' />";
			$description = '<p>' . $plugin_data['Description'] . '</p>';
			$plugin_name = $plugin_data['Name'];

			echo "
	<tr class='$class'>
		<th scope='row' class='check-column'>$checkbox</th>
		<td class='plugin-title'><strong>$plugin_name</strong></td>
		<td class='desc'>$description</td>
	</tr>
	<tr class='$class second'>
		<td></td>
		<td class='plugin-title'>";
			echo '<div class="row-actions-visible">';
			foreach ( $actions as $action => $link ) {
				$sep = end($actions) == $link ? '' : ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
			echo "</div></td>
		<td class='desc'>";
			$plugin_meta = array();
			if ( !empty($plugin_data['Version']) )
				$plugin_meta[] = sprintf(__('Version %s'), $plugin_data['Version']);
			if ( !empty($plugin_data['Author']) ) {
				$author = $plugin_data['Author'];
				if ( !empty($plugin_data['AuthorURI']) )
					$author = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';
				$plugin_meta[] = sprintf( __('By %s'), $author );
			}
			if ( ! empty($plugin_data['PluginURI']) )
				$plugin_meta[] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin site' ) . '">' . __('Visit plugin site') . '</a>';

			$plugin_meta = apply_filters('plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $context);
			echo implode(' | ', $plugin_meta);
			echo "</td>
	</tr>\n";
		}
?>
	</tbody>
</table>
<?php
	} //End print_plugins_table()
}

function exclude_plugins_menu() {
	if (is_multisite()){
		if (is_super_admin()){
			$page=add_plugins_page('Exclude Plugins', 'Exclude Plugins', 'manage_options', 'exclude_plugins', 'exclude_plugins_options');
			add_action('admin_head-'.$page, 'exclude_plugins_colors');
		} elseif(exclude_plugins_get_option('force_deactivate')) {
			//this will force deactivation of currently excluded plugins for normal user
			if (exclude_plugins_get_option('exclude_new')) deactivate_plugins(exclude_plugins_switch(exclude_plugins_get_option('included_plugins')));
			else deactivate_plugins(exclude_plugins_get_option('excluded_plugins'));
		}
	} else {add_plugins_page('Exclude Plugins', 'Exclude Plugins', 'manage_options', 'exclude_plugins', 'exclude_plugins_no_multisite');}
}

function exclude_plugins_switch($from){
	$all_plugins=exclude_plugins_no_network();
	return array_diff(array_keys($all_plugins), (array)$from);
}

function exclude_plugins_no_network(){
	$plugins = get_plugins();
	$network_plugins = array();
	foreach ( (array) $plugins as $plugin => $plugin_data) {
		if ( is_network_only_plugin($plugin)) {
			$network_plugins[$plugin] = $plugin_data;
		}
	}
	return array_diff_assoc($plugins, $network_plugins);
}

function exclude_plugins_options() {
	if (!is_super_admin())  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if ( isset($_REQUEST['exclude_plugins_ex'])) {
		check_admin_referer('exclude_plugins_save_options');
		if ($_REQUEST['checked']){
			if (exclude_plugins_get_option('exclude_new'))
				exclude_plugins_update_db('included_plugins',array_diff((array)exclude_plugins_get_option('included_plugins'),$_REQUEST['checked']));
			else exclude_plugins_update_db('excluded_plugins',array_merge((array)exclude_plugins_get_option('excluded_plugins'),$_REQUEST['checked']));
		}
	}
	elseif ( isset($_REQUEST['exclude_plugins_in'])) {
		check_admin_referer('exclude_plugins_save_options');
		if ($_REQUEST['checked']){
			if (exclude_plugins_get_option('exclude_new'))
				exclude_plugins_update_db('included_plugins',array_merge((array)exclude_plugins_get_option('included_plugins'),$_REQUEST['checked']));
			else exclude_plugins_update_db('excluded_plugins',array_diff((array)exclude_plugins_get_option('excluded_plugins'),$_REQUEST['checked']));
		}
	}elseif ( isset($_REQUEST['exclude_plugins_save'])) {
		check_admin_referer('exclude_plugins_save_options');
		$force_deactivate=($_REQUEST['force_deactivate'])?1:0;
		exclude_plugins_update_db('force_deactivate',$force_deactivate);
		
		//check if switching between
		if (exclude_plugins_get_option('exclude_new')!=$_REQUEST['exclude_new']){
			if ($_REQUEST['exclude_new']){ //switch to exclude new
				exclude_plugins_update_db('included_plugins',exclude_plugins_switch(exclude_plugins_get_option('excluded_plugins')));
				exclude_plugins_update_db('excluded_plugins','');
				exclude_plugins_update_db('exclude_new',1);
			} else {
				exclude_plugins_update_db('excluded_plugins',exclude_plugins_switch(exclude_plugins_get_option('included_plugins')));
				exclude_plugins_update_db('included_plugins','');
				exclude_plugins_update_db('exclude_new',0);
			}
		}
	}

	$menu_perms = get_site_option( 'menu_items', array() );
	if ( empty($menu_perms['plugins']) ){
	$message = sprintf( __( 'These settings is useless if plugins menu not activated because all plugins are excluded by default. %s' ), '<a href="' . esc_url( admin_url( 'ms-options.php#menu' ) ) . '">' . __( 'Activate' ) . '</a>' );
	echo "<div class='error'><p>$message</p></div>";

	}

	$plugins=exclude_plugins_no_network();

	if (exclude_plugins_get_option('exclude_new')){
		$included_plugins=exclude_plugins_included($plugins);
		$excluded_plugins=array_diff_assoc($plugins, $included_plugins);
	}else{
		$excluded_plugins=exclude_plugins_excluded($plugins);
		$included_plugins=array_diff_assoc($plugins, $excluded_plugins);
	}

	if (exclude_plugins_get_option('force_deactivate'))$fd_checked='checked="checked"';
	if (exclude_plugins_get_option('exclude_new'))$en_checked='checked="checked"';
?>
<form method="post" action="">
	<?php wp_nonce_field('exclude_plugins_save_options');?>
	<hr />
	<h3>Included Plugins</h3>
	<p>Plugins in the list below are available for <strong>all sites</strong>.</p>
	<p><input class="button-secondary" type="submit" value="Exclude Checked"
			  name="exclude_plugins_ex" id="exclude_plugins_ex" /></p>
	<div id="included-plugins">
		<?php print_plugins_table($included_plugins); ?>
	</div>
	<p><input class="button-secondary" type="submit" value="Exclude Checked"
			  name="exclude_plugins_ex" id="exclude_plugins_ex" /></p>
	<hr />
	<h3>Excluded Plugins</h3>
	<p>Plugins in the list below are available only for <strong>Super Admin</strong>.</p>
	<p><input class="button-secondary" type="submit"	value="Include Checked"
			  name="exclude_plugins_in" id="exclude_plugins_in" /></p>
	<div id="excluded-plugins">
		<?php print_plugins_table($excluded_plugins); ?>
	</div>
	<p><input class="button-secondary" type="submit"	value="Include Checked"
			  name="exclude_plugins_in" id="exclude_plugins_in" /></p>
	<hr />
	<table class="form-table">
	<tr><th>Exclude Newly Installed Plugins</th><td>
	<input type="checkbox" name="exclude_new" <?php echo $en_checked?>> Treat as excluded (only visible by Super Admins).
	<p>This option allow you to treat newly installed/added plugins as excluded (if checked) or included (if unchecked).</p>
	</td></tr>
	<tr><th>Force Deactivate</th><td>
	<input type="checkbox" name="force_deactivate" <?php echo $fd_checked?>> Force Deactivate
	<p>This will force deactivating of all plugins that is excluded for normal user.</p>
	<p>If you don't exclude plugins from normal user, they can activate the plugins.
		Then if you exclude the plugins they still active although they can't deactivate it anymore because the plugins is not in the list.
		If you force deactivate, the active plugins that is excluded from plugin list become deactivated once the admin enter admin interface.</p>
	</td></tr>
	</table>
	<p><input class="button-primary" type="submit" value="Save Options"
			  name="exclude_plugins_save" id="exclude_plugins_save" /></p>
	<hr />
</form>
<?php
}

function exclude_plugins_no_multisite() {
?>
	<h3>No Multisite</h3>
	<p>Excluded Plugins is especially built for WordPress multisite. You can get a guide how to enabling the multisite option <a href="http://itx.web.id/wordpress/mengaktifkan-opsi-multisite-dalam-wordpress-3-0/">here</a>.</p>
<?php
}

function exclude_plugins_update_db($name,$value){
	global $wpdb;
	$value=maybe_serialize($value);
	$q="INSERT INTO ".$wpdb->base_prefix."exclude_plugins (option_name, option_value) VALUES ( %s, %s ) ON DUPLICATE KEY UPDATE option_value= %s ";
	$wpdb->query($wpdb->prepare($q,$name,$value,$value));
}

function exclude_plugins_get_option($what){
	global $wpdb;
	$rows = $wpdb->get_row( "SELECT * FROM ".$wpdb->base_prefix."exclude_plugins where option_name='$what'" );
	if($wpdb->last_error){
		if(is_super_admin())echo "<div class='updated fade'>Exclude Plugins is not functioning. You have error in database: $wpdb->last_error</div>";
		return;
	}
	$return=maybe_unserialize($rows->option_value);
	if ($what=='excluded_plugins' && is_string($return)) $return=(array) $return;
	return $return;
}

function exclude_plugins_colors() {
?>
	<style type="text/css">
	#excluded-plugins th, #excluded-plugins td {background-color:#FFF2F2 !important;}
	#included-plugins th, #included-plugins td {background-color:#F2F2FF !important;}
	#excluded-plugins .row-actions-visible ,#excluded-plugins .plugin-update-tr,
	#included-plugins .row-actions-visible ,#included-plugins .plugin-update-tr  {display: none;}
	</style>
<?php
}

function exclude_plugins_install () {
	global $wpdb;
	$table_name = $wpdb->base_prefix . "exclude_plugins";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		option_name VARCHAR(255) NOT NULL,
		option_value text NOT NULL,
		PRIMARY KEY (option_name)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		$q="INSERT INTO ".$table_name." (option_name, option_value) VALUES ('version', '1.1.3'),('force_deactivate','0'),('exclude_new','1')";
		$wpdb->query($wpdb->prepare($q));
	}
}
register_activation_hook(__FILE__,'exclude_plugins_install');
?>