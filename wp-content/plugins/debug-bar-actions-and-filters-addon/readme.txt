=== Debug Bar Actions and Filters Addon ===

Contributors: subharanjan
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9N6KZQ2K8W7UJ
Tags: Debug Bar, Actions, Filters, Debug Bar Actions Display, Debug Bar Filters Display, List Hooks attached, List of Hooks Fired, Developer's tool for action and filter hooks
Requires at least: 3.3
Tested up to: 4.4
Stable tag: 1.5.1
License: GPLv2

Displays all the hooks( Actions and Filters ) for the current request in Debug Bar panel.


== Description ==

This plugin adds two more tabs in the Debug Bar to display hooks(Actions and Filters) attached to the current request. Actions tab displays the actions hooked to current request. Filters tab displays the filter tags along with the functions attached to it with respective priority.

= Important =

This plugin requires the [Debug Bar](http://wordpress.org/plugins/debug-bar/) plugin to be installed and activated.

Also note that this plugin should be used solely for debugging and/or in a development environment and is not intended for use on a production site.

If you like this plugin, please [rate and/or review](https://wordpress.org/support/view/plugin-reviews/debug-bar-actions-and-filters-addon) it. If you have ideas on how to make the plugin even better or if you have found any bugs, please report these in the [Support Forum](https://wordpress.org/support/plugin/debug-bar-actions-and-filters-addon) or in the [GitHub repository](https://github.com/subharanjanm/debug-bar-actions-and-filters-addon/issues).


== Installation ==

1. Install Debug Bar if not already installed (http://wordpress.org/extend/plugins/debug-bar/)
2. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
3. Activate the plugin through the "Plugins" menu in WordPress.
  
**Note:**
[Debug Bar](http://wordpress.org/extend/plugins/debug-bar/) plugin must be installed prior to this.   

Don't use this on Live/Production site. This is only for development purpose.


== Screenshots ==

1. Debug Bar displaying Actions 
2. Debug Bar displaying Filters 


== Frequently Asked Questions ==

= Can it be used on live site ? =
This plugin is only meant to be used for development purposes, but shouldn't cause any issues if run on a production site.


== Changelog ==

= 1.5.1 =
* Leaner language loading.
* Fix some layout issues.

= 1.5 =
* Show total hooks run at the top of the action hooks panel - props [Jrf](http://profiles.wordpress.org/jrf).
* Show various totals at the top of the filters panel - props [Jrf](http://profiles.wordpress.org/jrf).
* Change layout of the filters panel to a table to make it more compact - props [Jrf](http://profiles.wordpress.org/jrf).
* Show the filters sorted alphabetically - props [Jrf](http://profiles.wordpress.org/jrf).
* Allow for localization of the plugin - props [Jrf](http://profiles.wordpress.org/jrf).
* Fix compatibility with the [Plugin Dependencies](http://wordpress.org/plugins/plugin-dependencies/) plugin
* Add parent plugin requirement check.

= 1.4.1 =
* Bugfix: Make sure the plugin will not give a parse error on PHP < 5.3 for Closure check - props [Jrf](http://profiles.wordpress.org/jrf)

= 1.4 =
* Bugfix: callbacks given as array were no longer showing - props [Jrf](http://profiles.wordpress.org/jrf)
* Enhancement: clear distinction between object method calls and static class calls - props [Jrf](http://profiles.wordpress.org/jrf)

= 1.3 =
* Fixed HTML Validation error: "Saw U+0000 in stream." - props [Jrf](http://profiles.wordpress.org/jrf)
* Moved css to separate file - props [Jrf](http://profiles.wordpress.org/jrf)

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
