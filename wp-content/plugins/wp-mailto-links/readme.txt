=== WP Mailto Links - Manage Email Links ===
Contributors: freelancephp
Tags: hide, email, email address, mailto, link, antispam, protect, spambot, encode, encrypt, obfuscate, email link, protection
Requires at least: 3.6.0
Tested up to: 4.7.3
Stable tag: 2.1.6

Protect email addresses and mailto links from spambots and spamming. Easy to use without configuration.

== Description ==
Protect email addresses and mailto links from spambots and spamming. Easy to use without configuration.

http://www.youtube.com/watch?v=NxHnJWQnyuY

= Features =
* Protect mailto links automatically
* Protect plain email addresses or convert them to mailto links
* Protect RSS feed
* Set icon for all mailto links, also supports Font Awesome Icons and Dashicons
* Shortcode support: `[wpml_mailto]`
* Template tag support: `wpml_mailto()` and `wpml_filter()`

The plugin combines the best email protection methods (css and javascript techniques).

= Easy to use =
The WPML plugin works out-of-the-box. After activating the plugin all options are already set for protecting your emails and mailto links. Optionally you can also set some style options, like adding an icon.

= Sources =
* [Documentation](http://wordpress.org/extend/plugins/wp-mailto-links/other_notes/)
* [FAQ](http://wordpress.org/extend/plugins/wp-mailto-links/faq/)
* [Github](https://github.com/freelancephp/WP-Mailto-Links)

> <strong>Like this plugin?</strong><br>
> [Rate it](http://wordpress.org/support/view/plugin-reviews/wp-mailto-links) to support the development of this plugin.


== Installation ==

1. Go to `Plugins` in the Admin menu
1. Click on the button `Add new`
1. Search for `WP Mailto Links` and click 'Install Now' or click on the `upload` link to upload `wp-mailto-links.zip`
1. Click on `Activate plugin`
1. The plugin sets by default the right options for protecting your emails. All mailto links will automatically be scanned and protected.

== Frequently Asked Questions ==

= How does it work? =
The plugin combines the best email protection methods (css and javascript techniques). Some of them are explained in [this article](http://perishablepress.com/press/2010/08/01/best-method-for-email-obfuscation/) by Jeff Starr.

A webpage contains code on the backside. In the code all email addresses will be obfuscated, to hide them from spambots. Only when people see the page and click on the mailto link, the encoded email address will be translated to a normal email address.

= What's the best way to protect my email address(es) on my site? =
Offcourse by activating this plugin on your site. But even still it's not recommended to use email addresses directly in your posts or in your theme templates.
It would be better to use the shortcode `[wpml_mailto]` and template tag `wpml_mailto()`, because when the plugin isn't active anymore, the email addresses would not be shown on your site.

= How can I exclude pages? =
You can exclude pages from being processed by adding a condition to the filter. Put this code in the `functions.php` and replace the values of `$exclude_pages` to the page ID's you would like to exclude.

`function special_mailto($link, $display, $email, $attrs) {
    global $post;
    $exclude_pages = array(18, 22);

    if (in_array($post->ID, $exclude_pages)) {
        // use html entity to prevent replacing email by protection text
        return str_replace('@', '@', $email);
    }

    return $link;
}

add_filter('wpml_mailto', 'special_mailto', 10, 4);`

= Shortcode does not work in widgets. How can I make it work? =
By default shortcodes are not applied to (text) widgets. To support that you can add it to the text widget filter ([for more](http://www.wprecipes.com/how-to-add-shortcodes-in-sidebar-widgets)).
If you are not a coder, then just activate [this plugin](http://wordpress.org/extend/plugins/shortcodes-in-sidebar-widgets/), which does the same thing.

Do you have a problem or found a bug, please [report it](https://wordpress.org/support/plugin/wp-mailto-links#postform).

== Screenshots ==

1. Link Icon on the Site
1. Admin Settings Page

== Documentation ==

The WPML plugin works out-of-the-box. All settings are default set to protect your email addresses automatically.
If you want to manually create protected mailto links, just use the shortcode (`[wpml_mailto]`) within your posts or use the template tags (`wpml_mailto()` or `wpml_filter()`) in your theme files.

= Shortcode `[wpml_mailto email="..."]...[/wpml_mailto]` =
Create a protected mailto link in your posts:
`[wpml_mailto email="info@myemail.com"]My Email[/wpml_mailto]`

It's also possible to add attributes to the mailto link, like a target:
`[wpml_mailto email="info@myemail.com" target="_blank"]My Email[/wpml_mailto]`

= Template tag `wpml_mailto( $email [, $display] [, $attrs] )` =
Create a protected mailto link in your template like:
`<?php
    if (function_exists('wpml_mailto')) {
        echo wpml_mailto('info@somedomain.com', 'Mail Me');
    }
?>`

= Template tag `wpml_filter( $content )` =
Filter given content to protect mailto links, shortcodes and plain emails (according to the settings in admin):
`<?php
    if (function_exists('wpml_filter')) {
        echo wpml_filter('Filter some content to protect an emailaddress like info@somedomein.com.');
    }
?>`

= Filter hook (deprecated) =
The wpml_mailto filter gives you the possibility to manipulate output of the mailto created by the plugin. F.e. make all mailto links bold:
`// define filter function
public function special_mailto($link, $display, $email, $attrs) {
    return '<b>'. $link .'</b>';
}

// hook it to "wpml_mailto" filter
add_filter('wpml_mailto', 'special_mailto', 10, 4);`

Now all mailto links will be wrapped around a `<b>`-tag.

== Changelog ==

= 2.1.6 =
* Fixed bug retina png or gif images

= 2.1.5 =
* Fixed bug responsive image names containing @

= 2.1.4 =
* Fixed script conflicts on other admin pages
* Added data-attribute for WP External Links plugin to ignore WPML links

= 2.1.3 =
 * Solved CSS overwrite
 * Solved double filtering for final output and widget output

= 2.1.2 =
 * Fixed bug only effecting PHP5.3 ($this reference within a closure)

= 2.1.1 =
 * Bug fixed in wpml_mailto() and wpml_filter() template tags

= 2.1.0 =
 * Removed "wpml_ready" action
 * Code refactor using WPRun library

= 2.0.1 =
 * Removed realpath(), causing errors on existing installs
 * Fixed only load js on wpml admin page
 * Security check default off

= 2.0.0 =
 * Needs PHP version 5.3+
 * Complete refactor
 * Added Font Awesome Icons and Dashicons
 * Added security check for admin users
 * Deprecated "wpml_ready" action
 * Deprecated "wpml_mailto" filter

= 1.6.0 =
* Added option strong protection for emails in input fields
* JavaScript depends on jQuery
* Solved $wp_version missing error

= 1.5.0 =
* Secure JS encoding for input fields

= 1.4.1 =
* Solved JS only loading on plugin admin page

= 1.4.0 =
* Solved bug printing emails
* Complete refactoring (OO, views, general plugin classes)

= 1.3.3 =
* Solved bug email in input values, will now be encoded with html entities
* Solved bug @ sign when adding CC and BCC to mailto
* Solved bug layout wrap when using - sign in email

= 1.3.2 =
* Set protected email address in the title attribute

= 1.3.1 =
* Solved css rtl direction bug

= 1.3.0 =
* Only support for WP 3.4+
* Removed stylesheet file to save extra request
* Fixed bug saving metaboxes settings
* Added prefix to css class names

= 1.2.2 =
* Fixed bug PHP fatal error include path

= 1.2.1 =
* Fixed bug opening mailto links in iOS (iphone)
* Fixed bug deleting setting values when unregister (will now be deleted on uninstall)
* Fixed bug for option "Yes, convert plain emails to mailto links"

= 1.2.0 =
* Added option to skip icons on mailtos containing image(s)
* Added email attribute to mailto shortcode

= 1.1.0 =
* Added apply filter "wmpl_mailto"
* Changed filter_page method using only one regular expressions
* Added ob_end_flush to the wp_footer filter
* Fixed bug replacing plain email addresses

= 1.0.3 =
* Fixed php warning "call_user_func_array() expects parameter 1.."
* Removed ob_end_flush to the wp_footer filter, does not always work properly

= 1.0.2 =
* Fixed blank page bug on post edit (admin)
* Added ob_end_flush to the wp_footer filter

= 1.0.1 =
* Fixed bug in regexp plain email
* Fixed bug shortcode not working
* Fixed bug - sign in email addresses
* Fixed defined var $protected
* Fixed icon in admin menu
* Fixed update message in admin

= 1.0.0 =
* Added shortcode [wpml_mailto]
* Added template functions wpml_mailto() and wpml_filter()
* Added action hook "wpml_ready"
* Added registered metaboxes with screen settings
* Refactored code and reorganized files
* Changed to semantic versioning from 1.0.0

= 0.30 =
* Improved regular expressions
* Fixed keep lettercase in mailto params
* Fixed convert plain emails without dot on the end
* Replaced code from pre_get_posts to wp action

= 0.24 =
* Fixed IE layout problem (WP 3.3+)

= 0.23 =
* Fixed blank page bug (WP 3.2+)
* Fixed setting default option values

= 0.22 =
* Added support for widget_content filter of the Logic Widget plugin
* Changed script attribute `language` to `type`
* Displayed name will only be encrypted when containing email address(es)

= 0.21 =
* Fixed problem of not showing the first letter
* Fixed rtl css problem
* Fixed PHP / WP notices

= 0.20 =
* Fixed bug of changing `<abbr>` tag
* Added protection text for replacing emails in head-section and RSS feed
* Better RSS protection
* Improved JS method
* Improved regular expressions
* Solved bug using "&" for extra params (subject, cc, bcc, body) on mailto links
* Small cosmetical adjustments

= 0.10 =
* First release, features: protect mailto links and plain emails , set link icon, set no-icon class and additional classes
