=== WP-GeSHi-Highlight -- simple syntax highlighting based on GeSHi ===
Contributors: jgehrcke
Donate link: http://gehrcke.de/donate/
Tags: syntax, highlight, geshi, highlighting, valid, clean, fast, wp-geshi-highlight
Tested up to: 3.6
Stable tag: 1.1.0.2
License: GPLv2

Fast syntax highlighting for many languages. Simple usage. Based on GeSHi (well-established & award-winning PHP highlighter). Valid HTML output.

== Description ==
**I) Features:**

* Syntax highlighting for [**many** languages](http://gehrcke.de/files/perm/wp-geshi-highlight/wp-geshi-highlight_languages_1_0_8.txt).
* Stability, performance and security inherited from [GeSHi](http://qbnz.com/highlighter/).
* Line numbering (optional). Code-number displacements do not occur. Numbers are not copied in most browsers.
* Simple usage.
* Per-block styles: each code block on a single web page can be designed on its own.
* Clean, small and valid HTML output.
* Minimal waste of CPU cycles when there is nothing to highlight.
* Clean and well-documented source code.

WP-GeSHi-Highlight is a largely rewritten version of [WP-Syntax](http://wordpress.org/extend/plugins/wp-syntax/). Compared to WP-Syntax, WP-GeSHi-Highlight

* creates valid HTML when line numbering is activated (via GeSHi's [GESHI_HEADER_PRE_VALID](http://qbnz.com/highlighter/geshi-doc.html#the-code-container) setting).
* creates less HTML source code.
* delivers a default style sheet making use of modern CSS properties.
* has more styling flexibility.
* has the cleaner source code.
* makes usage of up-to-date WordPress API calls.

**II) Usage:**
Bear in mind: don't use the visual post editor. Then, it is very simple. Insert code blocks like this:

`<pre lang="languagestring">
    CODE
</pre>`

A short example:

`<pre lang="bash">
    $ dd if=/dev/zero of=image.ext3 bs=1M count=10000 oflag=append conv=notrunc
</pre>`

All available options are listed and explained on the [plugin's website](http://gehrcke.de/wp-geshi-highlight).

**III) How does it look in action?**

Examples can be found on the [plugin's homepage](http://gehrcke.de/wp-geshi-highlight/#examples).

**IV) Issues:**

Let me know if you find one: write a [mail](mailto:jgehrcke@googlemail.com) or leave a [comment](http://gehrcke.de/wp-geshi-highlight).


== Installation ==
1. Upload the `wp-geshi-highlight` directory to the `/wp-content/plugins` directory.
1. Activate the plugin through the plugins menu in WordPress.
1. Use it.


== Frequently Asked Questions ==
Please have a look at the [plugin's website](http://gehrcke.de/wp-geshi-highlight/#faq).


== Screenshots ==
1. This screenshot shows WP-GeSHi-Highlight in action. Furthermore, it visualizes how code blocks on the same page can have different styles (very easy to accomplish using WP-GeSHi-Highlight). Possibly this screenshot is out of date regarding the styling details.


== Changelog ==
= 1.1.0 (2013-06-22) =
* Adjust default style for compatibility with Twentythirteen theme.
* Remove GeSHi's hard-coded font-size and line-height code styles.
* Reduce box shadow and border radius in default style.
* Slightly increase top and bottom padding in default style.

= 1.0.8 (2013-01-17) =
* Improve default stylesheet: make use of CSS3 box shadows, several tweaks.
* If the code block style file is found in the [theme style directory](http://codex.wordpress.org/Function_Reference/get_stylesheet_directory), it now has priority over the one in the plugin directory.
* Update GeSHi to 1.0.8.11 (numerous language file updates).
* Include GeSHi language file for nginx configuration files (taken from GeSHi SVN revision r2572, to be released with 1.0.8.12).
* Use [wp_enqueue_style](http://codex.wordpress.org/Function_Reference/wp_enqueue_style) method for style sheet inclusion.
* Deactivate GeSHi economic mode when printing style sheet.
* Do not print credits to HTML source anymore.

= 1.0.7 (2012-05-12) =
* Fix collision with other plugins including their own version of GeSHi (thanks to Bas for reporting).

= 1.0.6 (2012-05-12) =
* Fix line-spacing bug when displaying code blocks with different line numbering settings on the same page (thanks to Bas ten Berge for reporting).

= 1.0.5 (2011-02-27) =
* Update GeSHi to 1.0.8.10 ("Some minor parser tweaks and fixes to existing language files. It adds 15 more languages.").

= 1.0.4 (2011-01-12) =
* Optimize: now, CSS code is only printed once if the same language is used for multiple code blocks on the same page.
* Minor code changes.

= 1.0.3 (2011-01-06) =
* Fix: comments are not always showing up (thanks to Uli for reporting).

= 1.0.2 (2011-01-04) =
* Minor code changes.
* Remove beta tag.

= 1.0.1-beta (2010-12-18) =
* Fix: highlight in comments not always showing up.

= 1.0.0-beta (2010-11-22) =
* Initial release.
