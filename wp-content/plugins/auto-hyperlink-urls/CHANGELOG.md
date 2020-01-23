# Changelog

## 5.4.1 _(2020-01-16)_
* Change: Disable Advanced Custom Field (ACF) support by default (it can be activated via new setting)
* Fix: Fix broken link to plugin help. Props neotrope.
* Fix: Fix typo in changelog for v5.4

## 5.4 _(2019-11-07)_

### Highlights:

This release adds support for the Advanced Custom Fields plugin, adds a filter to customize which HTML tags get excluded from auto-linkification, and notes compatibility through WP 5.3.

### Details:

* New: Add filter `autohyperlink_no_autolink_content_tags` for configuring which HTML tags don't get their content autolinked
* New: Add support for Advanced Custom Fields (ACF) plugin fields
    * Autolinks the following ACF field types: text, textarea, url, wysiwyg
    * Adds filter `c2c_autohyperlink_urls_acf_filters` for customizing which ACF-related filters to hook
* New: Unit tests: Add tests to verify default hooks get hooked
* Change: Note compatibility through WP 5.3+
* Change: Minor tweaks to descriptions of functions in inline documentation
* Change: Update copyright date (2020)

## 5.3 _(2019-04-19)_

### Highlights:

This minor release improves some link handling, but mostly improves upon plugin internals.

### Details:

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

## 5.2 _(2018-05-03)_

### Highlights:

This release consists of fixes for some minor bugs, improved handling of URLs containing parentheses, drops compatibility with versions of WordPress older than 4.7, and some behind-the-scenes changes.

### Details:

* Fix: Fix and improve handling of parentheses in URLs
* Fix: Prevent error when `can_do_hyperlink()` is passed an invalid URL
* Change: Reformat code (minor) for `hyperlink_urls()` to sync with core coding standards
* Change: Update plugin framework to 047
    * 047:
    * Don't save default setting values to database on install
    * Change "Cheatin', huh?" error messages to "Something went wrong.", consistent with WP core
    * Note compatibility through WP 4.9+
    * Drop compatibility with version of WP older than 4.7
    * 046:
    * Fix `reset_options()` to reference instance variable `$options`
    * Note compatibility through WP 4.7+
    * Update copyright date (2017)
    * 045:
    * Ensure `reset_options()` resets values saved in the database
* New: Add README.md
* Change: Store setting name in constant
* Change: Unit tests:
    * Sync changes to `Tests_Formatting_MakeClickable` with core's version (largely code formatting changes)
    * Revamp handling and testing of settings
    * Simplify implementations of `set_option()`
    * Add explicit tests for 'strip_protocol' set as true
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Tweak plugin description
* Change: Add GitHub link to readme
* Change: Fix code example in readme
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Note compatibility through WP 4.9+
* Change: Drop compatibility with versions of WP older than 4.7
* Change: Update copyright date (2018)

## 5.1 _(2016-06-19)_
* New: Add setting `require_scheme` to allow preventing plugin from auto-linking URIs without explicit schemes (i.e. 'http://').
* Change: Make the comparison for domains against the exclude list case insensitive. Props mqudsi.
* Change: Update plugin framework to 044:
    * 044
    * Add `reset_caches()` to clear caches and memoized data. Use it in `reset_options()` and `verify_config()`.
    * Add `verify_options()` with logic extracted from `verify_config()` for initializing default option attributes.
    * Add  `add_option()` to add a new option to the plugin's configuration.
    * Add filter 'sanitized_option_names' to allow modifying the list of whitelisted option names.
    * Change: Refactor `get_option_names()`.
    * 043
    * Disregard invalid lines supplied as part of hash option value.
    * 042
    * Update `disable_update_check()` to check for HTTP and HTTPS for plugin update check API URL.
    * Translate "Donate" in footer message.
    * Note compatibility through WP 4.5.
* Change: Construct strings in a cleaner way with `sprintf()` rather than piecing strings and variables together.
* Change: Minor code reformatting.
* Change: Prevent web invocation of unit test bootstrap.php.
* Change: Note compatibility through WP 4.5+.
* Bugfix: Add appropriate spacing so v5.0's changelog entry gets properly parsed.

## 5.0 _(2016-01-26)_

### Highlights:

This release revives active development of the plugin after many years and includes many, many changes. Backwards compatilibility has been maintained; it just handles things better and introduces a number of new features. Some notable changes:

* Introduced setting and filter to add support for preventing specified domains from getting auto-linked.
* Introduced filter to support custom handlers to determine if and when text links should get auto-linked.
* Improved text link detection and handling.
* Links within `<code>`, `<pre>`, `<script>`, and `<style>` tags are no longer hyperlinked.
* Improved support for language packs.
* Fixed some minor bugs.
* Added a boatload of unit tests to ensure the plugin functions as intended.
* Changed to not open links in a new window by default.
* Changed to no longer output the 'title' attribute.

### Details:

* New: Introduce setting to allow specifying domains that should not be automatically hyperlinked.
* New: Add filter `autohyperlink_urls_exclude_domains` for specifying domains to exclude domains from hyperlinking.
* New: Add filter `autohyperlink_urls_custom_exclusions` to support custom logic to determine if a link should be hyperlinked.
* Hardening: Sanitize return value of `get_tlds()` to ensure safe usage in regex.
* Change: Hyperlink links immediately preceeded by a comma, colon, semicolon, exclamation point, question mark, single quote, or double quotes.
* Change: Refactor `get_link_attributes()` to assemble attributes as an array.
* Bugfix: Prevent linking URLs and email addresses used within `<code>`, `<pre>`, `<script>`, and `<style>` tags.
* Bugfix: Properly unregister the `make_clickable` hooking of `comment_text`.
* New: Add help text to 'Auto-hyperlink comments?' settings checkbox to note that `make_clickable()` still runs if the checkbox is unchecked.
* Change: Remove `esc_attr()` call from return statement of `get_class()` (it gets escaped in `get_link_attributes()`).
* Change: Add `$context` arg to `get_link_attributes()` to handle either 'url' (default) or 'email' contexts.
* Change: Add `$context` arg to `autohyperlink_urls_link_attributes` filter.
* Change: Add `$title` arg to `autohyperlink_urls_link_attributes` filter.
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
* Change: Note compatibility through WP 4.4+.
* Change: Remove support for versions of WordPress older than 4.1.
* Change: Update copyright date (2016).
* Change: Update donate link.
* Change: Update screenshot.
* New: Add assets directory to plugin repository checkout.
    * Change: Move screenshot into repo's assets directory.
    * Add banner image.
    * Add icon image.

## 4.0
* Re-implementation by extending `C2C_Plugin_025`, which among other things adds support for:
    * Reset of options to default values
    * Better sanitization of input values
    * Offload of core/basic functionality to generic plugin framework
    * Additional hooks for various stages/places of plugin operation
    * Easier localization support
* Add `c2c_test_autohyperlink_urls()` to run suite of tests (currently 61 tests)
* Full localization support
* Improve text link detection
* Move `autohyperlink_truncate_link()` to `c2c_autohyperlink_truncate_link()`
* Deprecate `autohyperlink_truncate_link()`, but retain it (for now) for backwards compatibility
* Move `autohyperlink_link_urls()` to `c2c_autohyperlink_link_urls()`
* Deprecate `autohyperlink_link_urls()`, but retain it (for now) for backwards compatibility
* Add second argument to `c2c_autohyperlink_link_urls()` and class method `hyperlink_urls()` to allow override of plugin settings
* Ensure URLs get escaped prior to use in href attribute
* Fix bug that prevented proper link truncation
* Rename class from `AutoHyperlinkURLs` to `c2c_AutoHyperlinkURLs`
* Wrap global functions in `if(!function_exists())` checks
* Explicitly declare class functions public
* Save a static version of itself in class variable $instance
* Deprecate use of global variable `$autohyperlink_urls` to store instance
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

## 3.5 _(unreleased)_
* NEW:
* Extract functionality into clearly defined, single-tasked, and filterable functions
    * Add `get_class()` with filter `autohyperlink_urls_class` to filter class assigned to auto-hyperlinks (default is 'autohyperlink')
    * Add `get_link_attributes()` with filter `autohyperlink_urls_link_attributes` to filter all attributes for auto-hyperlink
    * Add `get_tlds()` with filter `autohyperlink_urls_tlds` to filter TLDs recognized by the plugin (a '|' separated string of tlds)
* Add filter `autohyperlink_urls_truncate_link` to `truncate_link()` to facilitate customized link truncation
* Add strip_protocol setting to control if URI scheme should be stripped from auto-hyperlinks
* Add 'Settings' link to plugin's plugin listing entry
* Add Changelog to readme.txt
* CHANGED:
* Move all global functions into class (except `autohyperlink_truncate_link()` and `autohyperlink_link_urls()`, which are now just single argument proxies to classed versions)
* Rewrite significant portions of all regular expressions
* Add hyphen to settings link text
* `truncate_link()` and `hyperlink_urls()` now pass arguments inline instead of setting temporary variables
* Memoize options in class
* Add class variable `plugin_basename`, which gets initialized in constructor, and use it instead of hardcoded path
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
* Use `plugins_url()` instead of hardcoded path

## 3.0
* Overhauled and added a bunch of new code
* Encapsulated a majority of functionality in a class
* Added admin options page for the plugin, under Options -> Autohyperlink (or in WP 2.5: Settings -> Autohyperlink)
* Added options so that default auto-hyperlinking can be easily configured
* Added option to allow for user-specified TLDs
* Added TLDs of mil, mobi, and cc
* Added option to conditionally auto-hyperlink comments
* Renamed existing functions
* `~` is a valid URL character
* Added class of `autohyperlink` to all links created by the plugin
* Removed the A-Z from regexp since they are case-insensitive
* Recoded some of the core functionality so as to execute only one `preg_replace()` call for everything (by passing patterns and replacements as arrays)
* Added a note about the known issue of the plugin linking URLs that appear within a longer string in a tag attribute's value
* `trim()` text before return instead of doing a `substr()`
* Added nofollow support
* Moved Class B domain preg to after explicitly URI-schemed links
* Tweaked description and installation instructions
* Updated copyright date and version to 3.0
* Added readme.txt and screenshot image to distribution zip
* Tested compatibility with WP 2.3+ and 2.5

# 2.01
* Fix to once again prevent linking already hyperlinked URL

# 2.0
* Plaintext URLs can now begin, end, or be all of the post and it will get auto-hyperlinked
* Incorporated some WP1.3 regular expression changes to `make_clickable()`
* Added “gov” and “edu” to the list of common domain extensions (for Class B domain support)
* No longer displays the URI scheme (the “http://” part) in the displayed link text
* Dropped support for auto-linking 'aim:' and 'icq:'
* Prepended function names with `c2c_` to avoid potential future conflict with other plugins or the WordPress core
* Changed license from BSD-new to MIT

## 1.01
* Slight tweak to prevent http://blah from becoming a link

## 1.0
* Complete rewrite

## 0.9
* Initial release
