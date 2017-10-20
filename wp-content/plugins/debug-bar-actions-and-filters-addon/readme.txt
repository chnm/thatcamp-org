=== Debug Bar Actions and Filters Addon ===

Contributors: subharanjan, jrf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9N6KZQ2K8W7UJ
Tags: Debug Bar, Actions, Filters, Debug Bar Actions Display, Debug Bar Filters Display, List Hooks attached, List of Hooks Fired, Developer's tool for action and filter hooks
Requires at least: 3.3
Tested up to: 4.8.1
Stable tag: 1.5.4
Requires PHP: 5.2.4
License: GPLv2

Displays all the hooks( Actions and Filters ) for the current request in Debug Bar panel.


== Description ==

This plugin adds two more tabs in the Debug Bar to display hooks(Actions and Filters) attached to the current request. Actions tab displays the actions hooked to current request. Filters tab displays the filter tags along with the functions attached to it with respective priority.

= Important =

This plugin requires the [Debug Bar](https://wordpress.org/plugins/debug-bar/) plugin to be installed and activated.

Also note that this plugin should be used solely for debugging and/or in a development environment and is not intended for use on a production site.

If you like this plugin, please [rate and/or review](https://wordpress.org/support/view/plugin-reviews/debug-bar-actions-and-filters-addon) it. If you have ideas on how to make the plugin even better or if you have found any bugs, please report these in the [Support Forum](https://wordpress.org/support/plugin/debug-bar-actions-and-filters-addon) or in the [GitHub repository](https://github.com/subharanjanm/debug-bar-actions-and-filters-addon/issues).


== Installation ==

1. Install Debug Bar if not already installed (https://wordpress.org/plugins/debug-bar/)
2. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
3. Activate the plugin through the "Plugins" menu in WordPress.
  
**Note:**
[Debug Bar](https://wordpress.org/plugins/debug-bar/) plugin must be installed prior to this.

Don't use this on Live/Production site. This is only for development purpose.


== Screenshots ==

1. Debug Bar displaying Actions 
2. Debug Bar displaying Filters 


== Frequently Asked Questions ==

= Can it be used on live site ? =
This plugin is only meant to be used for development purposes, but shouldn't cause any issues if run on a production site.


== Changelog ==

= 1.5.4 =
* Improve the travis build [#22](https://github.com/subharanjanm/debug-bar-actions-and-filters-addon/pull/22)

= 1.5.3 =
* README: Add "Requires PHP" header
* Confirm WP 4.8.1 compatibility

= 1.5.2 =
* Fix compatibility with WP 4.7
* Add the plugin to `recently_active` plugins list if self-deactivating
* Defer to the `wp-content/languages` directory for the loading of translations
* Update all wordpress.org URLs to use `https`
* Defer to translation retrieved from GlotPress, leaner language loading and language loading now compatible with use of the plugin in the `must-use` plugins directory

= 1.5.1 =
* Leaner language loading
* Fix some layout issues

= 1.5 =
* Show total hooks run at the top of the action hooks panel
* Show various totals at the top of the filters panel
* Change layout of the filters panel to a table to make it more compact
* Show the filters sorted alphabetically
* Allow for localization of the plugin
* Fix compatibility with the [Plugin Dependencies](https://wordpress.org/plugins/plugin-dependencies/) plugin
* Add parent plugin requirement check.

= 1.4.1 =
* Bugfix: Make sure the plugin will not give a parse error on PHP < 5.3 for Closure check

= 1.4 =
* Bugfix: callbacks given as array were no longer showing
* Enhancement: clear distinction between object method calls and static class calls

= 1.3 =
* Fixed HTML Validation error: "Saw U+0000 in stream."
* Moved css to separate file

= 1.2 =
* Fix for a closure issue.

= 1.1 =
* Fix for a fatal error because of BuddyPress hooks
* Closed the ul tag.

= 1.0 =
Adding the initial plugin to Wordpress plugins directory.


== Upgrade Notice ==

= 1.4 =
Fixed serious bug - upgrade highly recommended.
