=== Plugin Name ===
Contributors: automattic, skeltoac, briancolinger, nickmomrik, johnjamesjacoby
Donate link: 
Tags: WordPress.com, custom, css
Requires at least: 2.9
Tested up to: 3.1
Stable tag: 1.5

All CSS code is stored in wp_posts using a custom post_type of customcss. This allows us to use the built in revision engine.

== Description ==

All CSS code is stored in wp_posts using a custom post_type of customcss. This allows us to use the built in revision engine.
The CSS is filtered through CSS Tidy and KSES for security.

<a href="http://wordpress.org/extend/plugins/art-direction">Art Direction Plug-in</a>

== Installation ==

1. Upload the `safecss` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.5 =
* Fixes debug notices
= 1.0 =
* Initial release
= 1.1 =
* Sync with WPCOM
= 1.3 =
* Add text domain for localization
= 1.4 =
* properly sanitize URL and use preg_replace_callback instead of preg_replace
