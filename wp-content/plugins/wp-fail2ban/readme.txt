=== WP fail2ban ===
Contributors: invisnet
Author URI: https://charles.lecklider.org/
Plugin URI: https://github.com/invisnet/wp-fail2ban
Tags: fail2ban, login, security, syslog
Requires at least: 3.4
Tested up to: 4.9
Stable tag: 3.6.0
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Provides: FAIL2BAN

Write a myriad of WordPress events to syslog for integration with fail2ban.

== Description ==

[fail2ban](http://www.fail2ban.org/) is one of the simplest and most effective security measures you can implement to prevent brute-force password-guessing attacks.

*WP fail2ban* logs all login attempts - including via XML-RPC, whether successful or not, to syslog using LOG_AUTH. For example:

    Oct 17 20:59:54 foobar wordpress(www.example.com)[1234]: Authentication failure for admin from 192.168.0.1
    Oct 17 21:00:00 foobar wordpress(www.example.com)[2345]: Accepted password for admin from 192.168.0.1

*WPf2b* comes with two `fail2ban` filters, `wordpress-hard.conf` and `wordpress-soft.conf`, designed to allow a split between immediate banning and the traditional more graceful approach.

= Features =

**CloudFlare and Proxy Servers**

*WPf2b* can be configured to work with CloudFlare and other proxy servers. For an overview see [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies).

**Comments**

*WPf2b* can log comments. See [`WP_FAIL2BAN_LOG_COMMENTS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-log-comments).

**Pingbacks**

*WPf2b* logs failed pingbacks, and can log all pingbacks. For an overview see [`WP_FAIL2BAN_LOG_PINGBACKS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-log-pingbacks).

**Spam**

*WPf2b* can log comments marked as spam. See [`WP_FAIL2BAN_LOG_SPAM`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-log-spam).

**User Enumeration**

*WPf2b* can block user enumeration. See [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-block-user-enumeration).

**Work-Arounds for Broken syslogd**

*WPf2b* can be configured to work around most syslogd weirdness. For an overview see [`WP_FAIL2BAN_SYSLOG_SHORT_TAG`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-syslog-short-tag) and [`WP_FAIL2BAN_HTTP_HOST`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-http-host).

**Blocking Users**

*WPf2b* can be configured to short-cut the login process when the username matches a regex. For an overview see [`WP_FAIL2BAN_BLOCKED_USERS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-blocked-users).

**`mu-plugins` Support**

*WPf2b* can easily be configured as a must-use plugin - see [Configuration](https://wp-fail2ban.readthedocs.io/en/3.6/configuration.html#mu-plugins-support).



== Installation ==

1. Install via the Plugin Directory, or upload to your plugins directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Edit `wp-config.php` to suit your needs - see [Configuration](https://wp-fail2ban.readthedocs.io/en/3.6/configuration.html).

== Changelog ==

= 3.6.0 =
* The [filter files](https://wp-fail2ban.readthedocs.io/en/3.6/filters.html) are now generated from PHPDoc in the code. There were too many times when the filters were out of sync with the code (programmer error) - this should resolve that by bringing the patterns closer to the code that emits them.
* Added [PHPUnit tests](https://wp-fail2ban.readthedocs.io/en/3.6/tests.html). Almost 100% code coverage, with the exception of [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies) which is quite hard to test properly.
* Bugfix for [`wordpress-soft.conf`](https://wp-fail2ban.readthedocs.io/en/3.6/filters.html#wordpress-soft-conf).
* Add [`WP_FAIL2BAN_XMLRPC_LOG`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-xmlrpc-log).
* Add [`WP_FAIL2BAN_REMOTE_ADDR`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-remote-addr).
* [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies) now supports an array of IPs with PHP 7.
* Moved all documentation to [https://wp-fail2ban.readthedocs.io/](https://wp-fail2ban.readthedocs.io/).

= 3.5.3 =
* Bugfix for [`wordpress-hard.conf`](https://wp-fail2ban.readthedocs.io/en/3.6/filters.html#wordpress-hard-conf).

= 3.5.1 =
* Bugfix for [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-block-user-enumeration).

= 3.5.0 =
* Add [`WP_FAIL2BAN_OPENLOG_OPTIONS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-openlog-options).
* Add [`WP_FAIL2BAN_LOG_COMMENTS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-log-comments) and [`WP_FAIL2BAN_COMMENT_LOG`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-comment-log).
* Add [`WP_FAIL2BAN_LOG_PASSWORD_REQUEST`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-log-password-request).
* Add [`WP_FAIL2BAN_LOG_SPAM`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-log-spam).
* Add [`WP_FAIL2BAN_TRUNCATE_HOST`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-truncate-host).
* [`WP_FAIL2BAN_BLOCKED_USERS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-blocked-users) now supports an array of users with PHP 7.

= 3.0.3 =
* Fix regex in [`wordpress-hard.conf`](https://wp-fail2ban.readthedocs.io/en/3.6/filters.html#wordpress-hard-conf).

= 3.0.2 =
* Prevent double logging in WP 4.5.x for XML-RPC authentication failure

= 3.0.1 =
* Fix regex in [`wordpress-hard.conf`](https://wp-fail2ban.readthedocs.io/en/3.6/filters.html#wordpress-hard-conf).

= 3.0.0 =
* Add [`WP_FAIL2BAN_SYSLOG_SHORT_TAG`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-syslog-short-tag).
* Add [`WP_FAIL2BAN_HTTP_HOST`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-http-host).
* Log XML-RPC authentication failure.
* Add better support for MU deployment.

= 2.3.2 =
* Bugfix [`WP_FAIL2BAN_BLOCKED_USERS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-blocked-users).

= 2.3.0 =
* Bugfix in *experimental* [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies) code (thanks to KyleCartmell).

= 2.2.1 =
* Fix stupid mistake with [`WP_FAIL2BAN_BLOCKED_USERS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-blocked-users).

= 2.2.0 =
* Custom authentication log is now called [`WP_FAIL2BAN_AUTH_LOG`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-auth-log).
* Add logging for pingbacks; see [`WP_FAIL2BAN_LOG_PINGBACKS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-log-pingbacks).
* Custom pingback log is called [`WP_FAIL2BAN_PINGBACK_LOG`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-pingback-log).

= 2.1.1 =
* Minor bugfix.

= 2.1.0 =
* Add support for blocking user enumeration; see [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-block-user-enumeration).
* Add support for CIDR notation in [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies).

= 2.0.1 =
* Bugfix in *experimental* [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies) code.

= 2.0.0 =
* Add *experimental* support for X-Forwarded-For header; see [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies).
* Add *experimental* support for regex-based login blocking; see [`WP_FAIL2BAN_BLOCKED_USERS`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-blocked-users).

= 1.2.1 =
* Update FAQ.

= 1.2 =
* Fix harmless warning.

= 1.1 =
* Minor cosmetic updates.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 3.6.0 =
You will need up update your `fail2ban` filters.

= 3.5.3 =
You will need up update your `fail2ban` filters.

= 3.5.1 =
Bugfix: disable [`WP_FAIL2BAN_BLOCK_USER_ENUMERATION`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-block-user-enumeration) in admin area....

= 3.5.0 =
You will need up update your `fail2ban` filters.

= 3.0.3 =
You will need up update your `fail2ban` filters.

= 3.0.0 =
BREAKING CHANGE: The `fail2ban` filters have been split into two files. You will need up update your `fail2ban` configuration.

= 2.3.0 =
Fix for [`WP_FAIL2BAN_PROXIES`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-proxies); if you're not using it you can safely skip this release.

= 2.2.1 =
Bugfix.

= 2.2.0 =
BREAKING CHANGE:  `WP_FAIL2BAN_LOG` has been renamed to [`WP_FAIL2BAN_AUTH_LOG`](https://wp-fail2ban.readthedocs.io/en/3.6/defines.html#wp-fail2ban-auth-log).

Pingbacks are getting a lot of attention recently, so *WPf2b* can now log them.
The `wordpress.conf` filter has been updated; you will need to update your `fail2ban` configuration.

= 2.1.0 =
The `wordpress.conf` filter has been updated; you will need to update your `fail2ban` configuration.

= 2.0.1 =
Bugfix in experimental code; still an experimental release.

= 2.0.0 =
This is an experimental release. If your current version is working and you're not interested in the new features, skip this version - wait for 2.1.0. For those that do want to test this release, note that `wordpress.conf` has changed - you'll need to copy it to `fail2ban/filters.d` again.

