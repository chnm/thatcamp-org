=== Multisite Maintenance Mode ===
Contributors: channeleaton
Tags: multisite, maintenance, login
Requires at least: 3.3.1
Tested up to: 4.2.2
Stable tag: 0.2.2
License: GPLv2 or later

Disables logins for all WordPress users except network administrators.

== Description ==

Multisite Maintenance Mode solves the problem of site users making content/database changes while performing maintenance on large multisite networks. It works by directing every user (except network admins) to their homepage when they attempt to log in. A message is displayed in the admin bar to direct them on where to find more information. Anonymous users can still view the site normally.

== Installation ==

* Unzip and upload to `wp-content/plugins`
* Network activate the plugin
* Go to Network Settings -> Multisite Maintenance Mode
* Turn on Multisite Maintenance Mode

== Screenshots ==

1. The MMM settings page.

== Changelog ==

= 0.2.2 =
* Added Español translation. Props to Andrew Kurtis of WebHostingHub

= 0.2.1 =
* Tested with WordPress 4.2.2

= 0.2.0 =
* Now ready for translation!
* Consolidated the PHP for easier maintenance.
* Added filter 'mmm_allow_user_with_capability' to allow lower-level users to access the WordPress admin.

= 0.1 =
* Initial code.

