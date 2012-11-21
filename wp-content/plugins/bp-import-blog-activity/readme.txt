=== Plugin Name ===
Contributors: boonebgorges
Tags: buddypress, activity, blog, comments, import
Requires at least: WPMU 2.8, BuddyPress 1.2
Tested up to: WP 3.4.1, BuddyPress 1.6.1
Donate link: http://teleogistic.net/donate/
Stable tag: 0.2

Updates BuddyPress activity streams with missing blog comments and posts

== Description ==

If you install BuddyPress on an already thriving WordPress installation, you'll notice that existing blog comments and posts are not inserted into the activity stream. This plugin fixes that.

Requires WordPress Multisite

== Installation ==

* Upload the directory '/bp-import-blog-activity/' to your WP plugins directory and activate from the Dashboard of the BP blog.
* Select Import Blog Activity from under the Network Admin > Settings Dashboard menu, and click Import.

*** Back up your database, or at least wp_bp_activity, BEFORE clicking Import!
*** Do not use this plugin with a version of BuddyPress earlier than 1.2 - recorded times and other stuff may not work!
*** On a big installation you might hit memory limits! If so, open bp-import-blog-activity-bp-functions.php, and find lines 38 ad 39. Uncomment them, and adjust the numbers (currently 12 and 30) to keep the plugin from looping through all the blogs on your system. Then change the numbers and repeat until you've looped through all your blogs

*** Generally speaking, I don't recommend you use this plugin unless you know what it does and what you're doing!!

== Changelog ==

= 0.2 =
* Updated to work with Network Admin
* Fixed some PHP warnings

= 0.1 =
* Initial release
