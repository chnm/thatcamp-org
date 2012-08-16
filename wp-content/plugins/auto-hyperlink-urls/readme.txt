=== Auto-hyperlink URLs ===
Contributors: coffee2code
Donate link: http://coffee2code.com
Tags: links, link, URLs, url, auto-link, hyperlink, make_clickable, coffee2code
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 4.0
Version: 4.0

Automatically hyperlink text URLs and email addresses originally written only as plaintext.


== Description ==

Automatically hyperlink text URLs and email addresses originally written only as plaintext.

This plugin seeks to replace and extend WordPress's default auto-hyperlinking function.  This plugin uses different pattern matching expressions than the WordPress default in order to prevent inappropriate adjacent characters from becoming part of the link (as WordPress has improved over the years, nowadays this is just a few edge cases like text links that are braced or bracketed) and it prevents invalid URIs (i.e. http://blah) from becoming links.

More significantly, this plugin adds configurability to the auto-hyperlinker such that you can configure:

* If you want text URLs to only show the hostname
* If you want text URLs truncated after N characters
* If you want auto-hyperlinked URLs to open in new browser window or not
* If you want the protocol (i.e. "http://") to be stripped for displayed links
* The text to come before and after the link text for truncated links
* If you want rel="nofollow" to be supported
* If you wish to support additional domain extensions not already configured into the plugin

This plugin will recognize any protocol-specified URI (http|https|ftp|news)://, etc, as well as email addresses.  It also adds the new ability to recognize Class B domain references (i.e. "somesite.net", not just domains prepended with "www.") as valid links (i.e. "wordpress.org" would get auto-hyperlinked)

The following domain extensions (aka TLDs, Top-Level Domains) are recognized by the plugin: com, org, net, gov, edu, mil, us, info, biz, ws, name, mobi, cc, tv.  These only comes into play when you have a plaintext URL that does not have an explicit protocol specified.  If you need support for additional TLDs, you can add more via the plugin's admin options page.

This plugin also activates auto-hyperlinking of text links within post/page content.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/auto-hyperlink-urls//) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `auto-hyperlink-urls.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. (optional) Go to the Settings -> Autohyperlink admin settings page (which you can also get to via the Settings link next to the plugin on
the Manage Plugins page) and customize the settings.


== Examples ==

(when running with default configuration):

* "wordpress.org"
`<a href="http://wordpress.org" title="http://wordpress.org" target="_blank" class="autohyperlink">wordpress.org</a>`

* "http://www.cnn.com"
`<a href="http://www.cnn.com" title"http://www.cnn.com" target="_blank" class="autohyperlink">www.cnn.com</a>`

* "person@example.com"
`<a href="mailto:person@example.com" title="mailto:person@example.com" class="autohyperlink">person@example.com</a>`

To better illustrate what results you might get using the various settings above, here are examples.
	
For the following, assume the following URL is appearing as plaintext in a post: `www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php`
	
And unless explicitly stated, the results are using default values (nofollow is false, hyperlink emails is true, Hyperlink Mode is 0)
	
* By default:
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" title="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php"  class="autohyperlink" target="_blank">www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php</a>`

* With Hyperlink Mode set to 1
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" title="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" class="autohyperlink" target="_blank">www.somelonghost.com</a>`

* With Hyperlink Mode set to 15
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" title="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" class="autohyperlink"target="_blank">www.somelonghos...</a>`

* With Hyperlink Mode set to 15, nofollow set to true, open in new window set to false, truncation before of "[", truncation after of "...]"
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" title="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" class="autohyperlink" rel="nofollow">[www.somelonghos...]</a>`


== Known Issues ==

* It will not auto-hyperlink text URLs that are immediately single- or double-quoted, i.e. `'http://example.com'` or `"http://example.com"`
* It will include (though with no ill effect) a sentence ending question mark in a text URL that end a sentence (and immediately precedes the question mark).


== Screenshots ==

1. A screenshot of the plugin's admin options page.


== Changelog ==

= 4.0 =
* Re-implementation by extending C2C_Plugin_025, which among other things adds support for:
    * Reset of options to default values
    * Better sanitization of input values
    * Offload of core/basic functionality to generic plugin framework
    * Additional hooks for various stages/places of plugin operation
    * Easier localization support
* Add c2c_test_autohyperlink_urls() to run suite of tests (currently 61 tests)
* Full localization support
* Improve text link detection
* Move autohyperlink_truncate_link() to c2c_autohyperlink_truncate_link()
* Deprecate autohyperlink_truncate_link(), but retain it (for now) for backwards compatibility
* Move autohyperlink_link_urls() to c2c_autohyperlink_link_urls()
* Deprecate autohyperlink_link_urls(), but retain it (for now) for backwards compatibility
* Add second argument to c2c_autohyperlink_link_urls() and class method hyperlink_urls() to allow override of plugin settings
* Ensure URLs get escaped prior to use in href attribute
* Fix bug that prevented proper link truncation
* Rename class from 'AutoHyperlinkURLs' to 'c2c_AutoHyperlinkURLs'
* Wrap global functions in if(!function_exists()) checks
* Explicitly declare class functions public
* Save a static version of itself in class variable $instance
* Deprecate use of global variable $autohyperlink_urls to store instance
* In global space functions: use new class instance variable to access instance instead of using global
* Note compatibility with WP 3.0+, 3.1+, 3.2+
* Drop compatibility with versions of WP older than 3.0
* Add 'Text Domain' header tag
* Add screenshot
* Add .pot file
* Code reformatting (spacing)
* Add PHPDoc documentation
* Add package info to top of plugin file
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Remove trailing whitespace in header docs
* Update copyright date (2011)
* Add Template Tags, Filters, and Upgrade Notice sections to readme.txt

= 3.5 (unreleased) =
* NEW:
* Extract functionality into clearly defined, single-tasked, and filterable functions
    * Add get_class() with filter 'autohyperlink_urls_class' to filter class assigned to auto-hyperlinks (default is 'autohyperlink')
    * Add get_link_attributes() with filter 'autohyperlink_urls_link_attributes' to filter all attributes for auto-hyperlink
    * Add get_tlds() with filter 'autohyperlink_urls_tlds' to filter TLDs recognized by the plugin (a '|' separated string of tlds)
* Add filter 'autohyperlink_urls_truncate_link' to truncate_link() to facilitate customized link truncation
* Add strip_protocol setting to control if protocol should be stripped from auto-hyperlinks
* Add 'Settings' link to plugin's plugin listing entry
* Add Changelog to readme.txt
* CHANGED:
* Move all global functions into class (except autohyperlink_truncate_link() and autohyperlink_link_urls(), which are now just single argument proxies to classed versions)
* Rewrite significant portions of all regular expressions
* Add hyphen to settings link text
* truncate_link() and hyperlink_urls() now pass arguments inline instead of setting temporary variables
* Memoize options in class
* Add class variable 'plugin_basename', which gets initialized in constructor, and use it instead of hardcoded path
* Update to current admin page markup conventions
* Improve options handling
* Add logo to settings page
* Minor reformatting
* Note compatibility through WP2.8+
* Drop support for versions of WP older than 2.6
* Change description
* Update copyright date
* Update screenshot
* FIXED:
* Change pattern matching code for email addresses to allow for emails to be preceded by non-space characters
* Change pattern matching code for all auto-hyperlinking to better prevent linking a link within tag attributes
* Use plugins_url() instead of hardcoded path

= 3.0 =
* Overhauled and added a bunch of new code
* Encapsulated a majority of functionality in a class
* Added admin options page for the plugin, under Options -> Autohyperlink (or in WP 2.5: Settings -> Autohyperlink)
* Added options so that default auto-hyperlinking can be easily configured
* Added option to allow for user-specified TLDs
* Added TLDs of mil, mobi, and cc
* Added option to conditionally auto-hyperlink comments
* Renamed existing functions
* "~" is a valid URL character
* Added class of "autohyperlink" to all links created by the plugin
* Removed the A-Z from regexp since they are case-insensitive
* Recoded some of the core functionality so as to execute only one preg_replace() call for everything (by passing patterns and replacements as arrays)
* Added a note about the known issue of the plugin linking URLs that appear within a longer string in a tag attribute's value
* trim() text before return instead of doing a substr()
* Added nofollow support
* Moved Class B domain preg to after explicitly protocoled links
* Tweaked description and installation instructions
* Updated copyright date and version to 3.0
* Added readme.txt and screenshot image to distribution zip
* Tested compatibility with WP 2.3+ and 2.5

= 2.01 =
* Fix to once again prevent linking already hyperlinked URL

= 2.0 =
* Plaintext URLs can now begin, end, or be all of the post and it will get auto-hyperlinked
* Incorporated some WP1.3 regular expression changes to make_clickable()
* Added “gov” and “edu” to the list of common domain extensions (for Class B domain support)
* No longer displays the protocol (the “http://” part) in the displayed link text
* Dropped support for auto-linking aim: and icq:
* Prepended function names with “c2c_”to avoid potential future conflict with other plugins or the WordPress core
* Changed license from BSD-new to MIT

= 1.01 =
* Slight tweak to prevent http://blah from becoming a link

= 1.0 =
* Complete rewrite

= 0.9 =
* Initial release


== Upgrade Notice ==

= 4.0 =
Recommended major update! Highlights: major re-implementation and refactoring/fixes, localization support, support through WP 3.2, dropped support for older versions of WP older than 3.0, deprecation of all existing template tags (they've been renamed), misc non-functionality documentation and formatting tweaks; renamed class; and more.