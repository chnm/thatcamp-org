=== WP fail2ban ===
Contributors: invisnet
Donate link: https://paypal.me/invisnet/
Author URI: https://charles.lecklider.org/
Plugin URI: https://wp-fail2ban.com/
Tags: fail2ban, login, security, syslog, brute force, protection
Requires at least: 4.2
Tested up to: 5.3
Stable tag: 4.2.7.1
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Write a myriad of WordPress events to syslog for integration with fail2ban.

== Description ==

[fail2ban](http://www.fail2ban.org/) is one of the simplest and most effective security measures you can implement to prevent brute-force attacks.

*WP fail2ban* logs all login attempts - including via XML-RPC, whether successful or not, to syslog using LOG_AUTH. For example:

    Oct 17 20:59:54 foobar wordpress(www.example.com)[1234]: Authentication failure for admin from 192.168.0.1
    Oct 17 21:00:00 foobar wordpress(www.example.com)[2345]: Accepted password for admin from 192.168.0.1

*WPf2b* comes with three `fail2ban` filters: `wordpress-hard.conf`, `wordpress-soft.conf`, and `wordpress-extra.conf`. These are designed to allow a split between immediate banning (hard) and the traditional more graceful approach (soft), with extra rules for custom configurations.

= Features =

* **NEW - Remote Tools Add-on**
  The Remote Tools add-on provides extra features without adding bloat to the core plugin. For more details see the [add-on page](https://wp-fail2ban.com/add-ons/remote-tools/).

  **NB:** Requires PHP >= 5.6

* **NEW - Support for 3rd-party Plugins**
  Version 4.2 introduces a simple API for authors to integrate their plugins with *WPf2b*, with 2 *experimental* add-ons:
  * [Contact Form 7](https://wordpress.org/plugins/wp-fail2ban-addon-contact-form-7/)
  * [Gravity Forms](https://wordpress.org/plugins/wp-fail2ban-addon-gravity-forms/)

  **NB:** Requires PHP >= 5.6

* **CloudFlare and Proxy Servers**
  *WPf2b* can be configured to work with CloudFlare and other proxy servers. For an overview see [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-proxies).

* **Comments**
  *WPf2b* can log comments (see [`WP_FAIL2BAN_LOG_COMMENTS`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-log-comments)) and attempted comments (see [`WP_FAIL2BAN_LOG_COMMENTS_EXTRA`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-log-comments-extra)).

* **Pingbacks**
  *WPf2b* logs failed pingbacks, and can log all pingbacks. For an overview see [`WP_FAIL2BAN_LOG_PINGBACKS`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-log-pingbacks).

* **Spam**
  *WPf2b* can log comments marked as spam. See [`WP_FAIL2BAN_LOG_SPAM`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-log-spam).

* **Block User Enumeration**
  *WPf2b* can block user enumeration. See [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-block-user-enumeration).

* **Work-Arounds for Broken syslogd**
  *WPf2b* can be configured to work around most syslogd weirdness. For an overview see [`WP_FAIL2BAN_SYSLOG_SHORT_TAG`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-syslog-short-tag) and [`WP_FAIL2BAN_HTTP_HOST`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-http-host).

* **Blocking Users**
  *WPf2b* can be configured to short-cut the login process when the username matches a regex. For an overview see [`WP_FAIL2BAN_BLOCKED_USERS`](https://docs.wp-fail2ban.com/en/4.2/defines.html#wp-fail2ban-blocked-users).

* **`mu-plugins` Support**
  *WPf2b* can easily be configured as a must-use plugin - see [Configuration](https://docs.wp-fail2ban.com/en/4.2/configuration.html#mu-plugins-support).

== Installation ==

1. Install via the Plugin Directory, or upload to your plugins directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Edit `wp-config.php` to suit your needs - see [Configuration](https://docs.wp-fail2ban.com/en/4.2/configuration.html).

== Changelog ==

= 4.2.7.1 =
* Fix error when blocking user enumeration via `oembed` (h/t @wordpressfab).

= 4.2.7 =
* Fix error when blocking user enumeration via REST.
* Fix buttons on Settings tabs.

= 4.2.6 =
* Add support for [Remote Tools](https://wp-fail2ban.com/add-ons/remote-tools/) add-on.
* Add support for the new ClassicPress security page.
* Improved user enumeration blocking.

= 4.2.5.1 =
* Fix premium activation issue with PHP < 7.0.

= 4.2.5 =
* Properly fix PHP 5.3 support; tested on CentOS 6. Does not support any UI or Premium features.
* Fix potential issue with `WP_FAIL2BAN_BLOCK_USER_ENUMERATION` if calling REST API or XMLRPC from admin area.

= 4.2.4 =
* Add filter for login failed message.
* Fix logging spam comments from admin area.
* Fix Settings link from Plugins page.
* Update Freemius library

= 4.2.3 =
* Workaround for some versions of PHP 7.x that would cause `define()`s to be ignored.
* Add config note to settings tabs.
* Fix documentation links.

= 4.2.2 =
* Fix 5.3 compatibility.

= 4.2.1 =
* Completed support for [`WP_FAIL2BAN_COMMENT_EXTRA_LOG`](https://docs.wp-fail2ban.com/en/4.2/defines/WP_FAIL2BAN_COMMENT_EXTRA_LOG.html).
* Add support for 3rd-party plugins; see [Developers](https://docs.wp-fail2ban.com/en/4.2/developers.html).
  * Add-on for [Contact Form 7](https://wordpress.org/plugins/wp-fail2ban-addon-contact-form-7/) (experimental).
  * Add-on for [Gravity Forms](https://wordpress.org/plugins/wp-fail2ban-addon-gravity-forms/) (experimental).
* Change logging for known-user with incorrect password; previously logged as unknown user and matched by `hard` filters (due to limitations in older versions of WordPress), now logged as known user and matched by `soft`.
* Bugfix for email-as-username - now logged correctly and matched by `soft`, not `hard`, filters.
* Bugfix for regression in code to prevent Free/Premium conflict.

= 4.2.0 =
* Not released.

= 4.1.0 =
* Add separate logging for REST authentication.
* Fix conflict with earlier versions pre-installed in `mu-plugins`. See [Is *WPf2b* Already Installed?](https://docs.wp-fail2ban.com/en/4.1/installation.html#is-wp-fail2ban-already-installed).

= 4.0.5 =
* Add [`WP_FAIL2BAN_COMMENT_EXTRA_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_COMMENT_EXTRA_LOG.html).
* Add [`WP_FAIL2BAN_PINGBACK_ERROR_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PINGBACK_ERROR_LOG.html) (future functionality).
* Change `WP_FAIL2BAN_LOG_SPAM` to use `LOG_NOTICE`.
* Change `WP_FAIL2BAN_SPAM_LOG` to `LOG_AUTH`.
* Change `WP_FAIL2BAN_LOG_COMMENTS_EXTRA` events to use `LOG_NOTICE` by default.
* Fix conflict with 3.x in `mu-plugins`.

= 4.0.2 =
* Fix PHP 5.3 compatibility.
* Bugfix for `WP_FAIL2BAN_LOG_COMMENTS_EXTRA`.
* Bugfix for `WP_FAIL2BAN_REMOTE_ADDR` summary.

= 4.0.1 =
* Add extra features via Freemius. **This is entirely optional.** *WPf2b* works as before, including new features listed here.
* Add settings summary page (Settings -> WP fail2ban).
* Add [`WP_FAIL2BAN_PASSWORD_REQUEST_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PASSWORD_REQUEST_LOG.html).
* Add [`WP_FAIL2BAN_SPAM_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_SPAM_LOG.html).
* Add [`WP_FAIL2BAN_LOG_COMMENTS_EXTRA`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_LOG_COMMENTS_EXTRA.html) - enable logging for attempted comments on posts which are:
  * not found,
  * closed for commenting,
  * in the trash,
  * drafts,
  * password protected
* Block user enumeration via REST API.

= 4.0.0 =
* Not released.

= 3.6.0 =
* The [filter files](https://docs.wp-fail2ban.com/en/4.1/filters.html) are now generated from PHPDoc in the code. There were too many times when the filters were out of sync with the code (programmer error) - this should resolve that by bringing the patterns closer to the code that emits them.
* Added [PHPUnit tests](https://docs.wp-fail2ban.com/en/4.1/tests.html). Almost 100% code coverage, with the exception of [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PROXIES.html) which is quite hard to test properly.
* Bugfix for [`wordpress-soft.conf`](https://docs.wp-fail2ban.com/en/4.1/filters.html#wordpress-soft-conf).
* Add [`WP_FAIL2BAN_XMLRPC_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_XMLRPC_LOG.html).
* Add [`WP_FAIL2BAN_REMOTE_ADDR`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_REMOTE_ADDR.html).
* [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PROXIES.html) now supports an array of IPs with PHP 7.
* Moved all documentation to [https://docs.wp-fail2ban.com/](https://docs.wp-fail2ban.com/).

= 3.5.3 =
* Bugfix for [`wordpress-hard.conf`](https://docs.wp-fail2ban.com/en/4.1/filters.html#wordpress-hard-conf).

= 3.5.1 =
* Bugfix for [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_BLOCK_USER_ENUMERATION.html).

= 3.5.0 =
* Add [`WP_FAIL2BAN_OPENLOG_OPTIONS`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_OPENLOG_OPTIONS.html).
* Add [`WP_FAIL2BAN_LOG_COMMENTS`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_LOG_COMMENTS.html) and [`WP_FAIL2BAN_COMMENT_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_COMMENT_LOG.html).
* Add [`WP_FAIL2BAN_LOG_PASSWORD_REQUEST`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_LOG_PASSWORD_REQUEST.html).
* Add [`WP_FAIL2BAN_LOG_SPAM`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_LOG_SPAM.html).
* Add [`WP_FAIL2BAN_TRUNCATE_HOST`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_TRUNCATE_HOST.html).
* [`WP_FAIL2BAN_BLOCKED_USERS`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_BLOCKED_USERS.html) now supports an array of users with PHP 7.

= 3.0.3 =
* Fix regex in [`wordpress-hard.conf`](https://docs.wp-fail2ban.com/en/4.1/filters.html#wordpress-hard-conf).

= 3.0.2 =
* Prevent double logging in WP 4.5.x for XML-RPC authentication failure

= 3.0.1 =
* Fix regex in [`wordpress-hard.conf`](https://docs.wp-fail2ban.com/en/4.1/filters.html#wordpress-hard-conf).

= 3.0.0 =
* Add [`WP_FAIL2BAN_SYSLOG_SHORT_TAG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_SYSLOG_SHORT_TAG.html).
* Add [`WP_FAIL2BAN_HTTP_HOST`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_HTTP_HOST.html).
* Log XML-RPC authentication failure.
* Add better support for MU deployment.

= 2.3.2 =
* Bugfix [`WP_FAIL2BAN_BLOCKED_USERS`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_BLOCKED_USERS.html).

= 2.3.0 =
* Bugfix in *experimental* [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PROXIES.html) code (thanks to KyleCartmell).

= 2.2.1 =
* Fix stupid mistake with [`WP_FAIL2BAN_BLOCKED_USERS`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_BLOCKED_USERS.html).

= 2.2.0 =
* Custom authentication log is now called [`WP_FAIL2BAN_AUTH_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_AUTH_LOG.html).
* Add logging for pingbacks; see [`WP_FAIL2BAN_LOG_PINGBACKS`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_LOG_PINGBACKS.html).
* Custom pingback log is called [`WP_FAIL2BAN_PINGBACK_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PINGBACK_LOG.html).

= 2.1.1 =
* Minor bugfix.

= 2.1.0 =
* Add support for blocking user enumeration; see [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_BLOCK_USER_ENUMERATION.html).
* Add support for CIDR notation in [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PROXIES.html).

= 2.0.1 =
* Bugfix in *experimental* [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PROXIES.html) code.

= 2.0.0 =
* Add *experimental* support for X-Forwarded-For header; see [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PROXIES.html).
* Add *experimental* support for regex-based login blocking; see [`WP_FAIL2BAN_BLOCKED_USERS`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_BLOCKED_USERS.html).

= 1.2.1 =
* Update FAQ.

= 1.2 =
* Fix harmless warning.

= 1.1 =
* Minor cosmetic updates.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 4.2.7.1 =
This is a bugfix release. You do not need to update your filters from 4.1.0.

= 4.2.7 =
This is a bugfix release. You do not need to update your filters from 4.1.0.

= 4.2.6 =
This is a minor release. You do not need to update your filters from 4.1.0.

= 4.2.5.1 =
This is a premium-only patch release. If you are on PHP 7.0 or later you do not need to upgrade.

= 4.2.5 =
This is a minor release. You do not need to update your filters from 4.1.0.

= 4.2.4 =
This is a minor release. You do not need to update your filters from 4.1.0.

= 4.2.3 =
This is a bugfix release. You do not need to update your filters from 4.1.0.

= 4.2.2 =
You do not need to update your filters from 4.1.0.

= 4.2.1 =
You do not need to update your filters from 4.1.0.

= 4.1.0 =
To take advantage of the new features you will need up update your `fail2ban` filters; existing filters will continue to work as before.

= 4.0.5 =
This is a security fix (Freemius SDK): all 4.x users are strongly advised to upgrade immediately. You do not need to update your filters from 4.0.1.

= 4.0.4 =
This is a bugfix. You do not need to update your filters from 4.0.1.

= 4.0.3 =
This is a bugfix. You do not need to update your filters from 4.0.1.

= 4.0.2 =
This is a bugfix. You do not need to update your filters from 4.0.1.

= 4.0.1 =
To take advantage of the new features you will need up update your `fail2ban` filters; existing filters will continue to work as before.

= 3.6.0 =
You will need up update your `fail2ban` filters.

= 3.5.3 =
You will need up update your `fail2ban` filters.

= 3.5.1 =
Bugfix: disable [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_BLOCK_USER_ENUMERATION.html) in admin area....

= 3.5.0 =
You will need up update your `fail2ban` filters.

= 3.0.3 =
You will need up update your `fail2ban` filters.

= 3.0.0 =
BREAKING CHANGE: The `fail2ban` filters have been split into two files. You will need up update your `fail2ban` configuration.

= 2.3.0 =
Fix for [`WP_FAIL2BAN_PROXIES`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_PROXIES.html); if you're not using it you can safely skip this release.

= 2.2.1 =
Bugfix.

= 2.2.0 =
BREAKING CHANGE:  `WP_FAIL2BAN_LOG` has been renamed to [`WP_FAIL2BAN_AUTH_LOG`](https://docs.wp-fail2ban.com/en/4.1/defines/WP_FAIL2BAN_AUTH_LOG.html).

Pingbacks are getting a lot of attention recently, so *WPf2b* can now log them.
The `wordpress.conf` filter has been updated; you will need to update your `fail2ban` configuration.

= 2.1.0 =
The `wordpress.conf` filter has been updated; you will need to update your `fail2ban` configuration.

= 2.0.1 =
Bugfix in experimental code; still an experimental release.

= 2.0.0 =
This is an experimental release. If your current version is working and you're not interested in the new features, skip this version - wait for 2.1.0. For those that do want to test this release, note that `wordpress.conf` has changed - you'll need to copy it to `fail2ban/filters.d` again.
