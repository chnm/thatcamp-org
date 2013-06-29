=== Plugin Name ===
Contributors: mclarty
Donate link: http://www.inick.net
Tags: twitter, hashtag, sidebar, feed, widget
Requires at least: 2.8
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A sidebar widget that creates a simple, clean Twitter feed of a specified hashtag.

== Description ==

Twitter Hashtag Feed Widget creates a simple, clean Twitter feed of a specified hashtag.  This widget is specifically designed to render a lightweight widget that can be completely styled by the user through CSS.

== Installation ==

1. Upload the `twitter-hashtag-feed-widget` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the widget to the desired sidebar
4. Configure the options with your Twitter application data
5. Enjoy

== Changelog ==

= 1.0.2 =
* Removed widget-specific CSS in lieu of native WordPress CSS ID and class support.
* Added before/after_widget and before/after_title support.
* Changed API retrieval from Twitter to a 60-second transient.

= 1.0.1 =
* Replaced static HTML code in form with PHP foreach.

= 1.0.0 =
* First major release.

== Upgrade Notice ==

= 1.0 =
This is the first release.

== Custom Theming ==

All widgets have the CSS class .widget_twitter_hashtag_feed_widget for uniform styling across all widgets on the site.