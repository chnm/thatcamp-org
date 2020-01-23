=== Auto-hyperlink URLs ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: links, link, URLs, url, auto-link, hyperlink, make_clickable, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.3
Stable tag: 5.4.1

Automatically turns plaintext URLs and email addresses into links.


== Description ==

Automatically turns plaintext URLs and email addresses into links.

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

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/auto-hyperlink-urls/) | [Plugin Directory Page](https://wordpress.org/plugins/auto-hyperlink-urls/) | [GitHub](https://github.com/coffee2code/auto-hyperlink-urls/) | [Author Homepage](http://coffee2code.com/)


== Installation ==

1. Whether installing or updating, whether this plugin or any other, it is always advisable to back-up your data before starting
1. Install via the built-in WordPress plugin installer. Or download and unzip `auto-hyperlink-urls.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
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


== Hooks ==

The plugin exposes a number of filters for hooking. Typically, code making use of filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain). Bear in mind that most of the features controlled by these filters are configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.


**c2c_autohyperlink_urls_filters (filter)**

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

**c2c_autohyperlink_urls_acf_filters (filter)**

This hook allows you to customize which Advanced Custom Field filters get processed by the plugin. Note: the results of this filter are then passed through the `c2c_autohyperlink_urls_filters` filter, so ACF-specific filters can be modified using either hook.

Arguments:

* $filters (array): The ACF-related filters whose text should be auto-hyperlinked. Default `array( 'acf/format_value/type=text', 'acf/format_value/type=textarea', 'acf/format_value/type=url', 'acf_the_content' )`.

Example:

`
/**
 * Stop autolinking ACF text fields.
 *
 * @param array $filters
 * @return array
 */
function my_c2c_autohyperlink_urls_acf_filters( $filters ) {
	unset( $filters[ 'acf/format_value/type=text' ] );
	return $filters;
}
add_filter( 'c2c_autohyperlink_urls_acf_filters', 'my_c2c_autohyperlink_urls_acf_filters' );
`

**autohyperlink_urls_class (filter)**

This hook allows you to customize the class added to links created by the plugin.

Arguments:

* $class (string): The class name. Default 'autohyperlink'.

Example:

`
// Set default class for links added by Auto-hyperlink URLs.
add_filter( 'autohyperlink_urls_class', function ( $class ) { return 'myclass'; } );
`

**autohyperlink_urls_link_attributes (filter)**

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
add_filter( 'autohyperlink_urls_link_attributes', 'add_title_attribute_for_autohyperlink_urls', 10, 3 );
`

**autohyperlink_urls_tlds (filter)**

This hook allows you to custom the list of supported TLDs for non-URI scheme link auto-hyperlinking. Note that the value sent to the hook includes the default TLDs plus those added via the 'more_extensions' setting. Also note that the TLDs are defined as a '|'-separated string.

Arguments:

* $tlds (string): The supported TLDs. Default `'com|org|net|gov|edu|mil|us|info|biz|ws|name|mobi|cc|tv'`.

Example:

`
// Add support for more TLDs.
add_filter( 'autohyperlink_urls_tlds', function ( $tlds ) { return $tlds . '|in|io|tt'; } );
`

**autohyperlink_urls_truncate_link (filter)**

This hook allows you to custom how truncated links are displayed.

Arguments:

* $url (string): The potentially truncated URL.
* $original_url (string): The full, original URL.
* $context (string): The context for the link. Either 'url' or 'email'. Default 'url'.

**autohyperlink_urls_custom_exclusions (filter)**

This hook allows you to define custom logic to determine if a link should be hyperlinked.

Arguments:

* $should (bool): Should the link be hyperlinked? Default true.
* $url (string): The URL to be hyperlinked.
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

**autohyperlink_urls_exclude_domains (filter)**

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

**autohyperlink_no_autolink_content_tags (filter)**

This hook allows you to specify which HTML tags won't get their content autolinked.

Arguments:

* $html_tags (array): The HTML tags that won't get autolinked. Default `[ 'code', 'pre', 'script', 'style' ]`.

Example:

`
/**
 * Allow text within the `pre` to get autolinked, but don't allow text within
 * `blockquote` to get autolinked.
 *
 * @param  array $html_tags The HTML tags not to autolink.
 * @return array
 */
function my_autohyperlink_no_autolink_content_tags( $html_tags ) {
		// Tag that should get content autolinked, but that would otherwise be by default.
		$html_tags = array_flip( $html_tags );
		unset( $html_tags['pre'] );
		$html_tags = array_flip( $html_tags );

		// Tag that should not get content autolinked.
		$html_tags[] = 'blockquote';

		return $html_tags;
}
add_filter( 'autohyperlink_no_autolink_content_tags', 'my_autohyperlink_no_autolink_content_tags' );
`


== Changelog ==

= 5.4.1 (2020-01-16) =
* Change: Disable Advanced Custom Field (ACF) support by default (it can be activated via new setting)
* Fix: Fix broken link to plugin help. Props neotrope.
* Fix: Fix typo in changelog for v5.4

= 5.4 (2019-11-07) =
Highlights:

* This release adds support for the Advanced Custom Fields plugin, adds a filter to customize which HTML tags get excluded from auto-linkification, and notes compatibility through WP 5.3.

Details:

* New: Add filter `autohyperlink_no_autolink_content_tags` for configuring which HTML tags don't get their content autolinked
* New: Add support for Advanced Custom Fields (ACF) plugin fields
    * Autolinks the following ACF field types: text, textarea, url, wysiwyg
    * Adds filter `c2c_autohyperlink_urls_acf_filters` for customizing which ACF-related filters to hook
* New: Unit tests: Add tests to verify default hooks get hooked
* Change: Note compatibility through WP 5.3+
* Change: Minor tweaks to descriptions of functions in inline documentation
* Change: Update copyright date (2020)

= 5.3 (2019-04-19) =
Highlights:

* This minor release improves some link handling and notes compatibility through WP 5.3+, but mostly improves upon plugin internals.

Details:

* Change: Linkify emails before URLs instead of after in order to avoid an email username potentially matching as a domain
* Change: Tweak regex used for fixing links within links
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Update plugin framework to 049
    * 049:
    * Correct last arg in call to `add_settings_field()` to be an array
    * Wrap help text for settings in `label` instead of `p`
    * Only use `label` for help text for checkboxes, otherwise use `p`
    * Ensure a `textarea` displays as a block to prevent orphaning of subsequent help text
    * Note compatibility through WP 5.1+
    * Update copyright date (2019)
    * 048:
    * When resetting options, delete the option rather than setting it with default values
    * Prevent double "Settings reset" admin notice upon settings reset
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* New: Add inline documentation for hooks
* New: Add inline comments to document each segment of the regex used for fixing embedded links
* Unit tests:
     * New: Add some failing unit tests for known edge cases to be addressed later
     * Change: Update unit test install script and bootstrap to use latest WP unit test repo
* New: Add a bunch of TODO considerations
* Change: Note compatibility through WP 5.1+
* Change: Add 'License' and 'License URI' to plugin header
* Change: Rename readme.txt section from 'Filters' to 'Hooks' and provide a better section intro
* Change: Update installation instruction to prefer built-in installer over .zip file
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/auto-hyperlink-urls/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 5.4.1 =
Recommended bugfix release: disabled Advanced Custom Field (ACF) support by default (it can be activated via new setting) and fixed broken link to plugin help.

= 5.4 =
Feature update: added support for the Advanced Custom Fields plugin, added a filter to customize which HTML tags get excluded from auto-linkification, noted compatibility through WP 5.3, and updated copyright date (2020).

= 5.3 =
Minor update: tweaked plugin initialization, updated plugin framework to v049, noted compatibility through WP 5.1+, created CHANGELOG.md to store historical changelog outside of readme.txt, and updated copyright date (2019).

= 5.2 =
Recommended update: improved handling of parentheses in URLs; fixed some minor bugs; updated plugin framework to version 047; added README.md; compatibility is now with WP 4.7-4.9+; updated copyright date (2018).

= 5.1 =
Feature release: added setting to require explicit URI scheme (e.g. "http://") for text to be auto-linked; made comparison for domains against the exclude list be case insensitive; verified compatibility through WP 4.5+.

= 5.0 =
Recommended major update: new features; improved handling; hardening; minor bug fixes; added unit tests; improved localization; verified compatibility through WP 4.4; minimum WP support now 4.1; updated copyright date (2016); and more.

= 4.0 =
Recommended major update: major re-implementation and refactoring/fixes, localization support, support through WP 3.2, minimum WP support now 3.0, deprecation of all existing template tags (they've been renamed), misc non-functionality documentation and formatting tweaks; renamed class; and more.
