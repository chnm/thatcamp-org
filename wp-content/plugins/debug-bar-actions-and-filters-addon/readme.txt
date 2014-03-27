=== Debug Bar Actions and Filters Addon ===

Contributors: subharanjan
Tags: Debug Bar, Actions, Filters, Debug Bar Actions Display, Debug Bar Filters Display, List Hooks attached, List of Hooks Fired, Developer's tool for action and filter hooks
Requires at least: 3.3
Tested up to: 3.8
Stable tag: 1.4.1
License: GPLv2

This plugin adds two more tabs in the Debug Bar to display all the hooks(Actions and Filters) for the current request. Requires "Debug Bar" plugin.

== Description ==

This plugin adds two more tabs in the Debug Bar to display hooks(Actions and Filters) attached to the current request. Actions tab displays the actions hooked to current request. Filters tab displays the filter tags along with the functions attached to it with respective priority.  
  
**Note:**
[Debug Bar](http://wordpress.org/extend/plugins/debug-bar/) plugin must be installed prior to this.   

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.   
  
**Note:**
[Debug Bar](http://wordpress.org/extend/plugins/debug-bar/) plugin must be installed prior to this.   

Don't use this on Live site. This is only for development purpose.

== Screenshots ==
1. Debug Bar displaying Actions 

2. Debug Bar displaying Filters 

== Frequently Asked Questions ==
1. Can it be used on live site ?

Answer: Please don't use this on live site. This is only for development purpose.

== Changelog ==

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

= 1.4.1 =
Bug fix for users still on PHP 5.2.

= 1.4 =
Fixed serious bug - upgrade highly recommended.

= 1.3 =
Some bug fixes

= 1.0 =
New Installation
