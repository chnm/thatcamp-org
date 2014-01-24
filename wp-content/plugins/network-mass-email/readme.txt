=== Plugin Name ===
Contributors: kzaron
Tags: multisite, network, email
Requires at least: 3.3
Tested up to: 3.5
Stable tag: 1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows network admins to send a manually created notification email to all registered users based on user role.

== Description ==

This plugin allows for network administrators on WordPress multisite environments to send an email to users that 
they select based on the users' roles in individual sites. For example, checking only "editors" will go through
each active site and add anyone with the role of "editor" to your list of emails to send to.

To use the plugin after installation & activation, find Users / Mass Email in the network admin dashboard in your
multisite install. Then, select the user types you wish to email and click on the button for "Load the List". This
will load the list of users to email. Then you may compose your email in the boxes provided and click the send
button at the bottom of the screen. If all goes well you will be presented with a confirmation page indicating
that your email was sent successfully.

This plugin is NOT intended for administrators to be sending unsolicited spam to their users. In fact, it was
created with more formal environments in mind. One example would be a University setting where administrators 
of the network may need to notify students and faculty of potential downtime. With the plugin's implementation 
I would imagine it would be a highly inefficient way of sending spam anyway.

== Installation ==

1. Unzip the contents of the plugin, directory included, to the `/wp-content/plugins/` directory.
2. Network activate the plugin through the 'Plugins' menu in the Network Admin dashboard in WordPress.


== Frequently Asked Questions ==

= Will this work on a regular WordPress install?  =

No. This plugin only works (and would only make sense) in a multisite install.

= Where do I send emails from? =

The plugin now has its own menu in the network admin dashboard.

= Won't someone use this for spam? =

Potentially, but as I stated in the description, I would imagine it would be a pretty inefficient way to spam users.

= Will this work on an earlier version of WordPress? =

Possibly, but I haven't tested it myself. I don't see why it wouldn't work, but I can't vouch for it personally.

== Screenshots ==

1. Found in the network admin dashboard, under Users / Mass Email.


== Changelog ==

= 1.5 =
* Big Update - Lots of stuff!
* Added message templates! You can now save message templates to re-use at a later date!
* Added checkboxes to all users when they are loaded with 'load the list'. This means you can now manually uncheck users that you would like not to receive an email you are sending and vice-versa.
* Moved the menu for the plguin to the main Network Admin menu. It now has it's own icon and submenus instead of being listed under 'Users'.
* Updated the plugin compatibility for 'compatible up to' and 'requires'.
* Added checkbox labels to all checkboxes that appear. I don't know why they weren't there in the first place...
* Added more tweaks to variable formatting so that messages are stored and are sent as intended when you compose them.

= 1.4.2 =
* Applied a fix (again) for escaping slashes and special characters in email body contents.

= 1.4.1 =
* Applied a fix for escaping slashes in email body contents. UPDATE: This fix did not work, was patched in 1.4.2.

= 1.4 =
* Apparently I was temporarily dyslexic and had the WordPress compatible version numbers reversed in the readme. This has been fixed.
There are no other changes in this update.

= 1.3 =
* Release