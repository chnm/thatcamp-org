=== bbPress Notify (No Spam) ===
Contributors: useStrict
Author URI: http://www.usestrict.net/
Plugin URI: http://usestrict.net/2013/02/bbpress-notify-nospam/
Tags: bbpress, email notification, no spam
Requires at least: 3.1
Tested up to: 4.9.2
Text Domain: bbpress_notify
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.18
Requires PHP: 5.3
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VLQU2MMXKB6S2

== Description ==
This is started as a fork of the original bbPress-Notify plugin, after several failed attempts to contact the author requesting that he add the no-spam code to it. I don't like spam. Do you?

It integrates into bbPress and sends notifications via e-mail when new topics or replies are posted. It is fully configurable in the bbPress settings.

= Features include =

 * Send notifications in HTML, Plain text, or Multipart format, with full image support.
 * Override bbPress' core subscription messages with your own; 
 * Set Notification recipient roles for new topics;
 * Set Notification recipient roles for new replies; 
 * Set Notification e-mail's subject and body for both new topics and replies;
 * Send Background Notifications (to avoid delays in loading pages for large user databases);
 * Extensible through almost 40 handy actions and filters;

<blockquote>
= Premium Add-Ons =

Take your notifications to the next level with one or more of these add-ons. 

<ul>
    <li><a href="https://usestrict.net/product/bbpress-notify-no-spam-reply-email/" target="_new">Reply by Email Add-on</a>: Users can reply directly from their mailbox. No need to click links or open the forums in a browser.</li>
    <li><a href="https://usestrict.net/product/bbpress-notify-no-spam-opt-out-add-on/" target="_new">Opt-out Add-on</a>: Users can choose not to receive any notifications, or simply unsubscribe from the thread with a single click. A must-have for CAN-SPAM and CASL laws!</li>
    <li><a href="https://usestrict.net/product/bbpress-notify-no-spam-digests/" target="_new">Digest Add-on</a>: Users can choose to receive daily, weekly, or monthly digests.</li>
    <li><a href="https://usestrict.net/product/bbpress-moderation-plugin-add-on/" target="_new">bbPress Moderation Integration</a>: Make bbpnns work with <a href="https://wordpress.org/plugins/bbpressmoderation/" target="_new">bbPress Moderation</a>.</li>
    <li><a href="https://usestrict.net/product/bbpress-notify-no-spam-private-groups-bridge/" target="_new">bbPress Private Groups Integration</a>: Make bbpnns respect <a href="https://wordpress.org/plugins/bbp-private-groups/" target="_new">bbPress Private Groups</a> rules.</li>
    <li><a href="https://usestrict.net/product/bbpress-notify-no-spam-buddypress-bridge/" target="_new">BuddyPress Integration</a>: Notify BuddyPress Group members of new Group Forum topics and replies. It also shows individual Opt Out and Digest settings in each user's BuddyPress profile screen.</li>
    <li><a href="https://usestrict.net/product/bbpress-notify-no-spam-memberpress-bridge/" target="_new">MemberPress Integration</a>: Make sure your members have access to Opt Out and Digest settings in their MemberPress profile screens.</li>
</ul>

= Partnerships =
We've made a parnership with ISIPP.com, securing their SuretyMail Email certification (to make sure the email you send gets delivered to the inbox instead of the junk folder) for a fraction of the full price, and without the need to have a dedicated IP. Learn more about it <a href="https://usestrict.net/go/suretymail4wp" target="_new">here</a>.  

</blockquote>


== Installation ==

1. Upload the entire plugin folder via FTP to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the <strong>Settings -> Forums</strong> and select which roles should get notifications. 

== Frequently Asked Questions ==
= Why am I not receiving notifications of a topic/reply that I created? =
 * As of version 1.9.4, topic/reply authors no longer get notified of their own posts by default.
 * Version 1.12 and up have settings to decide whether authors get notified or not.

= Where are the settings? =
 * All settings are under Admin -> Settings -> Forums -> Email Notifications

= Can a user turn off notifications? =
 * Originally, this plugin was developed to alert Administrators of new topics and replies. After a few iterations, users requested the ability to send
messages to other roles, which then could be characterized as spam. To allow your users to opt-out from receiving notifications, please consider purchasing 
the [Opt-out Add-on](http://usestrict.net/product/bbpress-notify-no-spam-opt-out-add-on/).
 * As of version 1.12, you can use this plugin with bbPress Forum/Topic subscribers, instead of- or in addition to roles. Just turn on the Override option for Forums or Topics.

= Does this plugin integrate with BuddyPress Groups? =
 * Out of the box, no. However, you can get BuddyPress Group notification functionality using our premium [bbpnns/BuddyPress Bridge plugin](http://usestrict.net/product/bbpress-notify-no-spam-buddypress-bridge/).

== Screenshots ==
1. The settings page
2. Ability to send notification when managing topics/replies in the admin UI 


== Changelog ==
= 1.18 =
* Added support for topic-content and topic-excerpt tags in replies.
* Added check and warning of needed bridge plugins to play nicely with supported membership/permission plugins.

= 1.17 =
* Fix: notify_on_save was not handling future dated publishing at all.

= 1.16.2 =
* Fix DOMDocument to work with non-UTF8 characters. Thanks to @yinbit for the testing environment.

= 1.16.1 =
* Don't assume UTF-8 loading the text in DOMDocument to process image CIDs and convert links. 

= 1.16 =
* Add support for embedded images in notifications.
* Capture case when topic_id does not get passed to notify_new_reply()

= 1.15.11 =
* Adjust parameters for send_notification().

= 1.15.10 =
* Add post type, topic/reply id and forum id to send_notification() so they can be used in bbpnns_filter_email_body_for_user and bbpnns_filter_email_subject_for_user filters. 

= 1.15.9.1 =
* Fix: Removed debugging left behind in 1.15.9

= 1.15.9 =
* Decode quotes in topics and body.

= 1.15.8 =
* Refactor topic-url code in reply notifications to improve performance.

= 1.15.7 =
* Added support for topic-title, topic-author, and topic-author-email tags in the reply subject.

= 1.15.6 =
* Remove surety message.

= 1.15.5 =
* Fix: apply bbpnns_topic_url filter when processing topic_url inside a reply as well.

= 1.15.4 =
* Added: bbpnns_core_subscribers filter.

= 1.15.3 =
* Added: topic-title, topic-author, and topic-author-email tags are now available in replies.

= 1.15.2 =
* Fix: unchecked iconv function was breaking some installs.

= 1.15.1 =
* Fix: Plain text mailouts had broken UTF-8 characters.

= 1.15 =
* Added: bbpnns_is_in_effect filter to help identify if Core Overrides are on or if a user belongs to a notifiable role.

= 1.14.3 =
* Fix: Correctly handling encoded entities.
* Fix: Check that iconv_mime_encode is available before trying to use it.
* Added: bbpnns signature in email headers to help with troubleshooting. 

= 1.14.2 =
* Fix: Multipart messages are now working nicely with Mailgun and regular wp_mail calls.
* Added: HTML to text converter is now handling images, replacing the html with their alt value.

= 1.14.1 =
* Fix: Mailgun is replacing our multipart/alternative header boundary, so now admins can chose whether to send HTML, Plain Text, or Multipart messages.

= 1.14 =
* New: WYSIWYG emails, complete with automatic multipart text version for non HTML clients.
* New: Added user-name tags support.

= 1.13.1 =
* Fix: Bad copy/paste on previous commit, which replaced the body with the subject line.

= 1.13 =
* New: Added tags to get topic and reply author email.

= 1.12 =
* New: Take over notifications for bbPress' Core Subscriptions
* New: Decide whether authors must receive their own notifications or not

= 1.11.1 =
* ISIPP/SuretyMail partnership announcement.

= 1.11 =
* Added: calling set_time_out(0) if doing cron. This should help people who are not getting all mailouts sent due to too many recipients.

= 1.10 =
* Minor bug fix: [topic-forum] and [reply-forum] tags were missing from list of available tags, although functionality was fine.
* Add: [topic-url] is now available in replies, too.

= 1.9.4 =
* New Feature: No longer add topic/reply author to the recipient list.

= 1.9.3 =
* Fix: Replace <code>mb_internal_encoding()</code> with <code>iconv_get_encoding()</code> as at least one host didn't have <code>mb_string</code> enabled.
* Add: Admin option to enable or disable Subject line encoding. Admin -> Settings -> Forums -> E-mail Notifications -> Encode Topic and Reply Subject line.
* Add: uninstaller. 


= 1.9.2 =
* Fix filters bbpnns_filter_email_subject_in_build and bbpnns_filter_email_body_in_build to pass $type and $post_id

= 1.9.1 =
* New action: bbpnns_email_failed_single_user, allows for better handling of failed emails. Params: $user_info, $filtered_subject, $filtered_body, $recipient_headers
* New action: bbpnns_before_wp_mail, executed immediately before wp_mail() call. Params: $user_info, $filtered_subject, $filtered_body, $recipient_headers
* New action: bbpnns_after_wp_mail, executed immediately after wp_mail() call. Params: $user_info, $filtered_subject, $filtered_body, $recipient_headers

= 1.9 =
* New Filter: bbpnns_skip_notification
* New Filter: bbpnns_available_tags
* New Action: bbpnns_after_email_sent_single_user
* New Action: bbpnns_after_email_sent_all_users
* Change: Only filter subject and body if user is OK to receive message
* Change: Reduce DB calls by one per user
* Change: stop using PHP4-style pass-by-reference. PHP5 always passes by reference now.
* Change: Improve Encoding of subject line

= 1.8.2.1 =
* Fix: added a workaround for emails with UTF-8 Characters in the subject line that weren't being sent.

= 1.8.2 =
* Added: support for people using wpMandrill and getting emails without newlines. We turn on nl2br momentarily while sending out our emails. 
This option can be overridden by using the filter 'bbpnns_handle_mandrill_nl2br'.

= 1.8.1 =
* Fix: no longer return if wp_mail fails for a given email address. This was an issue for people using wpMandrill with an address in the blacklist.

= 1.8 =
* New Filter: bbpnns_post_status_blacklist
* New Filter: bbpnns_post_status_whitelist
* New Action: bbpnns_before_topic_settings
* New Action: bbpnns_after_topic_settings
* New Action: bbpnns_after_reply_settings
* New Action: bbpnns_register_settings  

= 1.7.3 =
* Remove admin message as it's not getting dismissed properly.
* Update tested up to.

= 1.7.2 =
* Fix parameters for 'bbp_new_reply' filter
* Added call to 'bbp_get_reply_forum_id()' in case the forum_id was blank (should no longer happen with 'bbp_new_reply' filter fix)

= 1.7.1 =
* Notify about existence of Opt-Out add-on

= 1.7 =
* Added support for Opt-Out add-on
* Added labels to all input fields

= 1.6.7 =
* Added support for tags [topic-forum], and [reply-forum]. ([Towfiq I.](https://wordpress.org/support/topic/feature-forum-name-in-email))

= 1.6.6.1 =
* Removed Pro message.

= 1.6.6 =
* Added subject filter in _build_email: bbpnns_filter_email_subject_in_build
* Added body filter in _build_email: bbpnns_filter_email_body_in_build
* Renamed filter: bbpnns-filter-recipients => bbpnns_filter_recipients_before_send
* Renamed filter: bbpnns-filter-email-subject => bbpnns_filter_email_subject_for_user
* Renamed filter: bbpnns-filter-email-body => bbpnns_filter_email_body_for_user

= 1.6.5 =
* Added user-contributed filters: bbpress_reply_notify_recipients, and bbpress_topic_notify_recipients

= 1.6.4 =
* Added filters: bbpnns-filter-recipients, bbpnns-filter-email-subject, and bbpnns-filter-email-body

= 1.6.3.1 =
* Fixed: buggy dismiss link in previous commit.

= 1.6.3 =
* Added notice about bbPress Notify Pro project at Kickstarter.

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
* Added: Enforce replacement of <br> tags for newlines.

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
* No-spam version forked. 


== Upgrade Notice ==
= 1.11 =
Added code to help stop timeouts during cron for people who have huge recipient lists. 
