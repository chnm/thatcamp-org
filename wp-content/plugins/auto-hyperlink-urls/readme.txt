=== Auto-hyperlink URLs ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: links, link, URLs, url, auto-link, hyperlink, make_clickable, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.1
Tested up to: 4.5
Stable tag: 5.0

Automatically hyperlink text URLs and email addresses originally written only as plaintext.


== Description ==

Automatically hyperlink text URLs and email addresses originally written only as plaintext.

This plugin seeks to replace and extend WordPress's default auto-hyperlinking function. This plugin uses different pattern matching expressions than the WordPress default in order to prevent inappropriate adjacent characters from becoming part of the link (as WordPress has improved over the years, nowadays this is just a few edge cases like text links that are braced or bracketed) and it prevents invalid URIs (i.e. http://blah) from becoming links.

More significantly, this plugin adds configurability to the auto-hyperlinker such that you can configure:

* If you want text URLs to only show the hostname
* If you want text URLs truncated after N characters
* If you want auto-hyperlinked URLs to open in new browser window or not
* If you want the URI scheme (i.e. "http://") to be stripped for displayed links
* The text to come before and after the link text for truncated links
* If you want rel="nofollow" to be supported
* If you wish to support additional domain extensions not already configured into the plugin
* If you want certain domains to be omitted from auto-linking

This plugin will recognize any explicit URI scheme (http|https|ftp|news)://, etc, as well as email addresses. It also adds the new ability to recognize Class B domain references (i.e. "somesite.net", not just domains prepended with "www.") as valid links (i.e. "wordpress.org" would get auto-hyperlinked)

The following domain extensions (aka TLDs, Top-Level Domains) are recognized by the plugin: com, org, net, gov, edu, mil, us, info, biz, ws, name, mobi, cc, tv. These only comes into play when you have a plaintext URL that does not have an explicit URI scheme specified. If you need support for additional TLDs, you can add more via the plugin's admin options page or via filter.

This plugin also activates auto-hyperlinking of text links within post/page content.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/auto-hyperlink-urls/) | [Plugin Directory Page](https://wordpress.org/plugins/auto-hyperlink-urls/) | [Author Homepage](http://coffee2code.com/)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Unzip `auto-hyperlink-urls.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. (optional) Go to the Settings -> Autohyperlink admin settings page (which you can also get to via the Settings link next to the plugin on
the Manage Plugins page) and customize the settings.


== Screenshots ==

1. A screenshot of the plugin's admin options page.


== Examples ==

(when running with default configuration):

* "wordpress.org"
`<a href="http://wordpress.org" class="autohyperlink">wordpress.org</a>`

* "http://www.cnn.com"
`<a href="http://www.cnn.com" class="autohyperlink">www.cnn.com</a>`

* "person@example.com"
`<a href="mailto:person@example.com" class="autohyperlink">person@example.com</a>`

To better illustrate what results you might get using the various settings above, here are examples.
	
For the following, assume the following URL is appearing as plaintext in a post: `www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php`
	
And unless explicitly stated, the results are using default values (nofollow is false, hyperlink emails is true, Hyperlink Mode is 0)
	
* By default:
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" class="autohyperlink">www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php</a>`

* With Hyperlink Mode set to 1
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" class="autohyperlink">www.somelonghost.com</a>`

* With Hyperlink Mode set to 15
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" class="autohyperlink">www.somelonghos...</a>`

* With Hyperlink Mode set to 15, nofollow set to true, open in new window set to false, truncation before of "[", truncation after of "...]"
`<a href="http://www.somelonghost.com/with/some/long/URL/that/might/mess/up/your/theme/and/is/unsightly.php" class="autohyperlink" rel="nofollow">[www.somelonghos...]</a>`


== Filters ==

The plugin exposes seven filters for hooking. Typically, customizations utilizing these hooks would be put into your active theme's functions.php file, or used by another plugin.

= c2c_autohyperlink_urls_filters (filter) =

This hook allows you to customize which filters get processed by the plugin.

Arguments:

* $filters (array): The filters whose text should be auto-hyperlinked. Default `array( 'the_content', 'the_excerpt', 'widget_text' )`.

Example:

`
/**
 * Auto-hyperlink additional filters.
 *
 * @param array $filters
 * @return array
 */
function my_c2c_autohyperlink_urls_filters( $filters ) {
	// Add in another filter to process.
	$filters[] = 'my_custom_filter';
	return $filters;
}
add_filter( 'c2c_autohyperlink_urls_filters', 'my_c2c_autohyperlink_urls_filters' );
`

= autohyperlink_urls_class (filter) =

This hook allows you to customize the class added to links created by the plugin.

Arguments:

* $class (string): The class name. Default 'autohyperlink'.

Example:

`
// Set default class for links added by Auto-hyperlink URLs.
add_filter( 'autohyperlink_urls_class', function ( $class ) { return 'myclass'; } );
`

= autohyperlink_urls_link_attributes (filter) =

This hook allows you to add custom attributes to links created by the plugin.

Arguments:

* $attributes (array): The link attributes already created by the plugin.
* $context (string): The context for the link. Either 'url' or 'email'. Default 'url'.
* $title (string): The text for the link's title attribute.

Example:

`
/**
 * Output 'title' attribute for link, as done by plugin prior to v5.0.
 *
 * @param array  $attributes The attributes for the link tag.
 * @param string $context    The context for the link. Either 'url' or 'email'. Default 'url'.
 * @param string $title      The text for the link's title attribute.
 * @return array
 */
function add_title_attribute_for_autohyperlink_urls( $attributes, $context = 'url', $title = '' ) {
	if ( $title ) {
		$attributes['title'] = $title;
	}

	return $attributes;
}
add_filter( 'autohyperlink_urls_link_attributes', 'add_title_attribute_for_autohyperlink_urls' );
`

= autohyperlink_urls_tlds (filter) =

This hook allows you to custom the list of supported TLDs for non-URI scheme link auto-hyperlinking. Note that the value sent to the hook includes the default TLDs plus those added via the 'more_extensions' setting. Also note that the TLDs are defined as a '|'-separated string.

Arguments:

* $tlds (string): The supported TLDs. Default `'com|org|net|gov|edu|mil|us|info|biz|ws|name|mobi|cc|tv'`.

Example:

`
// Add support for more TLDs.
add_filter( 'autohyperlink_urls_tlds', function ( $tlds ) { return $tlds . '|in|io|tt'; } );
`

= autohyperlink_urls_truncate_link (filter) =

This hook allows you to custom how truncated links are displayed.

Arguments:

* $url (string): The potentially truncated URL.
* $original_url (string): The full, original URL.
* $context (string): The context for the link. Either 'url' or 'email'. Default 'url'.

= autohyperlink_urls_custom_exclusions (filter) =

This hook allows you to define custom logic to determine if a link should be hyperlinked.

Arguments:

* $should (bool): Should the link be hyperlinked? Default true.
* $url (string): The full URL.
* $domain (string): Just the domain/host part of the URL.

Example:

`
/**
 * Don't hyperlink links on the front page.
 *
 * @param  bool   $should
 * @param  string $url
 * @param  string $domain
 * @return bool
 */
function my_autohyperlink_urls_custom_exclusions( $should, $url, $domain ) {
	if ( is_front_page() ) {
		return false;
	} else {
		return $should;
	}
}
add_filter( 'autohyperlink_urls_custom_exclusions', 'my_autohyperlink_urls_custom_exclusions' );
`

= autohyperlink_urls_exclude_domains (filter) =

This hook allows you to specify domains that should not get auto-hyperlinked. Note that the value sent to the hook includes the value of the 'exclude_domains' setting. Note that only the domain (without URI scheme or trailing slash) should be specified.

Arguments:

* $excluded_domains (array): The domains already being excluded. Default empty array.

Example:

`
/**
 * Exclude certain domains from being auto-hyperlinked.
 *
 * @param  array $excluded_domains
 * @return array
 */
function my_autohyperlink_urls_exclude_domains( $excluded_domains ) {
	$excluded_domains[] = 'youtube.com';
	$excluded_domains[] = 'example.com';
	return $excluded_domains;
}
add_filter( 'autohyperlink_urls_exclude_domains', 'my_autohyperlink_urls_exclude_domains' );
`

== Changelog ==

= 5.0 (2016-01-26) =
Highlights:
This release revives active development of the plugin after many years and includes many, many changes. Backwards compatilibility has been maintained; it just handles things better and introduces a number of new features. Some notable changes:
* Introduced setting and filter to support for preventing specified domains from getting auto-linked.
* Introduced filter to support custom handlers to determine if and when text links should get auto-linked.
* Improved text link detection and handling.
* Links within `<code>`, `<pre>`, `<script>`, and `<style>` tags are no longer hyperlinked.
* Improved support for language packs.
* Fixed some minor bugs.
* Added a boatload of unit tests to ensure the plugin functions as intended.
* Changed to not open links in a new window by default.
* Changed to no longer output the 'title' attribute.

Details:
* New: Introduce setting to allow specifying domains that should not be automatically hyperlinked.
* New: Add filter 'autohyperlink_urls_exclude_domains' for specifying domains to exclude domains from hyperlinking.
* New: Add filter 'autohyperlink_urls_custom_exclusions' to support custom logic to determine if a link should be hyperlinked.
* Hardening: Sanitize return value of `get_tlds()` to ensure safe usage in regex.
* Change: Hyperlink links immediately preceeded by a comma, colon, semicolon, exclamation point, question mark, single quote, or double quotes.
* Change: Refactor `get_link_attributes()` to assemble attributes as an array.
* Bugfix: Prevent linking URLs and email addresses used within `<code>`, `<pre>`, `<script>`, and `<style>` tags.
* Bugfix: Properly unregister the 'make_clickable' hooking of 'comment_text'.
* New: Add help text to 'Auto-hyperlink comments?' settings checkbox to note that `make_clickable()` still runs if the checkbox is unchecked.
* Change: Remove `esc_attr()` call from return statement of `get_class()` (it gets escaped in `get_link_attributes()`).
* Change: Add `$context` arg to `get_link_attributes()` to handle either 'url' (default) or 'email' contexts.
* Change: Add `$context` arg to 'autohyperlink_urls_link_attributes' filter.
* Change: Add `$title` arg to 'autohyperlink_urls_link_attributes' filter.
* Bugfix: If detected link ends in a question mark, don't treat it as part of the link.
* Bugfix: Preserve original leading and trailing spaces in text throughout processing.
* Change: Update plugin framework to 041 (too many changes to list).
* Change: Remove support for 'autohyperlink_urls' global.
* New: Add `reset_options()` to override parent so it also unsets instance variable.
* Change: Better singleton implementation:
    * Add `get_instance()` static method for returning/creating singleton instance.
    * Make static variable 'instance' private.
    * Make constructor protected.
    * Make class final.
    * Additional related changes in plugin framework (protected constructor, erroring `__clone()` and `__wakeup()`).
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Remove .pot file and /lang subdirectory.
* New: Implement true unit tests, migrating the existing makeshift tests and adding many more.
* New: Adapt (with minimal changes) the entire unit test suite for core's `make_clickable()`.
* New: Add checks to prevent execution of code if file is directly accessed.
* Bugfix: Explicitly declare `activation()` and `uninstall()` static.
* Bugfix: Add parent-defined arg to overridden `options_page_description()` to avoid PHP warnings.
* Change: Re-license as GPLv2 or later (from X11).
* Change: Reformat plugin header.
* New: Add 'License' and 'License URI' header tags to readme.txt and plugin file.
* Change: Use explicit path for require_once().
* Deprecate: Discontinue use of PHP4-style constructor.
* Deprecate: Discontinue use of explicit pass-by-reference for objects.
* Change: Remove ending PHP close tag.
* Change: Minor documentation improvements.
* Change: Minor inline documentation reformatting.
* Change: Minor code reformatting (spacing, bracing).
* Change: Use https for links to wordpress.org.
* New: Add link to plugin directory page to readme.txt
* Change: Tweak installation instructions in readme.txt
* New: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Note compatibility through WP 4.5+.
* Change: Remove support for versions of WordPress older than 4.1.
* Change: Update copyright date (2016).
* Change: Update donate link.
* Change: Update screenshot.
* New: Add assets directory to plugin repository checkout.
    * Change: Move screenshot into repo's assets directory.
    * Add banner image.
    * Add icon image.

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
* Add strip_protocol setting to control if URI scheme should be stripped from auto-hyperlinks
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
* Moved Class B domain preg to after explicitly URI-schemed links
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
* No longer displays the URI scheme (the “http://” part) in the displayed link text
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

= 5.0 =
Recommended major update: new features; improved handling; hardening; minor bug fixes; added unit tests; improved localization; verified compatibility through WP 4.4; minimum WP support now 4.1; updated copyright date (2016); and more.

= 4.0 =
Recommended major update: major re-implementation and refactoring/fixes, localization support, support through WP 3.2, minimum WP support now 3.0, deprecation of all existing template tags (they've been renamed), misc non-functionality documentation and formatting tweaks; renamed class; and more.
