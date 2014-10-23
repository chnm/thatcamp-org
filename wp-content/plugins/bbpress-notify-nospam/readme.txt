=== bbPress Notify (No Spam) ===
Contributors: useStrict
Author URI: http://www.usestrict.net/
Plugin URI: http://usestrict.net/2013/02/bbpress-notify-nospam/
Tags: bbpress, email notification, no spam
Requires at least: 3.1
Tested up to: 4.0
Text Domain: bbpress_notify
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.6.2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VLQU2MMXKB6S2

== Description ==
This is a modification of the original bbPress-Notify plugin, after several failed attempts to contact the author requesting that he add the no-spam code to it. I don't like spam. Do you?

This plugin integrates into bbPress and sends a notification via e-mail when new topics or replies are posted. It is fully configurable in the bbPress settings.

Settings include:
* Notification recipients for new topics, 
* Notification recipients for new replies, 
* Notification e-mail's subject and body for both new topics and replies
* Set Background Notifications (no longer causes delays in loading pages for large user databases)


== Installation ==

1. Upload the entire plugin folder via FTP to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the Settings -> Forums and select which group our groups should get notifications. 

== Frequently Asked Questions ==
= Did you write this plugin? =
No, I simply added a spam filter and a couple of other improvements.

= Why did you do this? =
Because the original author never answered the WP support forums (or any emails, for that matter).

= Do you plan on improving the plugin? =
Not really. I just want to stop receiving spam from my bbPress install. However, if you want an improvement badly enough, contact me through vinny [at] usestrict [dot] net and we'll discuss it.


== Screenshots ==
1. The settings page
2. Ability to send notification when managing topics/replies in the admin UI 


== Changelog ==
= 1.6.2 =
* Fix bug where topic and reply post_types were not set in time to send post.
* Only send notification if post_status is publish, besides not being spam.
* Adjustments to notify_on_save
* Added tests for notify_on_save

= 1.6.1 =
* Passing $post_id and $title variables to filters added in 1.6.

= 1.6 =
* Added support for filters 'bbpnns_topic_url', 'bbpnns_reply_url', and 'bbpnns_topic_reply'

= 1.5.5 =
* Improved Tests
* Renamed some variables.

= 1.5.4 =
* Fix: Make sure bbPress is installed and avoid race conditions when loading.

= 1.5.3 =
* Fix: corrected missing newlines in topic/reply content email. 

= 1.5.2 =
* Fix: admin-only emails not working due to missed boolean casting.

= 1.5.1 =
* Fixed bug, 'hidden forum override reply' setting not registered
* Added filters: bbpnns_skip_topic_notification, bbpnns_skip_reply_notification, bpnns_excerpt_size, bbpnns_extra_headers

= 1.5 =
* Added override option to only send emails to Admins in case a Forum is hidden.
* Added tests

= 1.4.2 =
* Tweak: make sure we have unique recipients. In some installs, duplicate emails were being sent.

= 1.4.1 =
* Fixed: preg_replace error in some installs.

= 1.4 =
* Fixed: Strict notices.
* Added: Settings link in Plugins page.
* Added: Logging failed wp_mail call.
* Added: Option to send notifications when adding/updating a topic or reply in the admin.
* Added: Enforce replacement of &lt;br&gt; tags for newlines.

= 1.3 =
* New: Added background notifications

= 1.2.2 =
* Fixed: bug that was sending emails to everyone if no role was saved.
* Fixed: no longer using 'blogadmin' as default, but 'administrator' upon install.

= 1.2.1 =
* Added back old plugin deactivation
* Bug fix for topic author not displaying when anonymous by Rick Tuttle

= 1.2 =
* Improved role handling by Paul Schroeder.

= 1.1.2 =
* Fixed edge case where user doesn't select any checkbox in recipients list.
* Array casting in foreach blocks. 

= 1.1.1 =
* Fixed load_plugin_textdomain call.

= 1.1 =
* Fixed methods called as functions.

= 1.0 =
* No-spam version created. 

= 0.2.1 =
* Added template tags "[topic-replyurl]" and "[reply-replyurl]"

= 0.2 =
* Improved selection of e-mail recipients; now it is possible to select multiple user roles

= 0.1 =
* First alpha version

== Upgrade Notice ==
= 1.4.2 =
In some installs, people were getting duplicate emails. We're making sure that only one email is sent per user.

= 1.4.1 =
Fixes an error in preg_replace. Update is strongly recommended.

= 1.4 =
Fixes a couple of strict notices, and adds Settings action link, swaps &lt;br&gt; tags for newlines and enables sending notifications when creating a topic or reply from the admin UI. 
