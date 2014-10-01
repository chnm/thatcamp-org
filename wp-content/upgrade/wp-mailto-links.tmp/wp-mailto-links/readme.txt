=== WP Mailto Links - Manage Email Links ===
Contributors: freelancephp
Tags: hide, email, email address, mailto, link, antispam, protect, spambot, encode, encrypt, obfuscate, email icon, javascript
Requires at least: 3.4.0
Tested up to: 4.0.0
Stable tag: 1.3.3

Protect email addresses and mailto links from spambots and being used for spamming. Easy to use without configuration.

== Description ==
Protect your email addresses and manage mailto links on your site, set mail icon, styling and more.

= Features =
* Protect mailto links (automatically or shortcode)
* Protect plain email addresses or convert them to mailto links
* Set mail icon
* RSS feed protection
* And more...

The plugin combines the best email protection methods explained in [this article](http://perishablepress.com/press/2010/08/01/best-method-for-email-obfuscation/) by Jeff Starr.

= Easy to use =
After activating the plugin all options are already set for protecting your emails and mailto links. Optionally you can also set some style options, like adding an icon.

Although the plugin can scan mailto links automatically, you could also use the shortcode:
`[wpml_mailto email="info@myemail.com"]My Email[/wpml_mailto]`

[See more documentation](http://wordpress.org/extend/plugins/wp-mailto-links/other_notes/).

= Support =
This plugin has the same [requirements](http://wordpress.org/about/requirements/) as WordPress.
If you are experiencing any problems, just take a look at the [FAQ](http://wordpress.org/extend/plugins/wp-mailto-links/faq/) or report it in the [support section](http://wordpress.org/support/plugin/wp-mailto-links). You can also send me a mail with [this contactform](http://www.freelancephp.net/contact/).

= Github =
I published the code on [Github](https://github.com/freelancephp/WP-Mailto-Links) so anybody can commit code changes.

= Like this plugin? =
Please [rate this plugin](http://wordpress.org/support/view/plugin-reviews/wp-mailto-links) or [post a comment](http://www.freelancephp.net/wp-mailto-links/) on my blog.

== Installation ==

1. Go to `Plugins` in the Admin menu
1. Click on the button `Add new`
1. Search for `WP Mailto Links` and click 'Install Now' or click on the `upload` link to upload `wp-mailto-links.zip`
1. Click on `Activate plugin`
1. The plugin sets by default the right options for protecting your emails. All mailto links will automatically be scanned and protected.

== Frequently Asked Questions ==

= Prefilled form field for emailaddress contains weird value. How to solve? =

A prefilled form field that contains an emailaddress is also being protected by the plugin. The protection code is causing a weird value.
This can be solved by excluding that particular page from being processed by the plugin. See answer of the next question how to do that.

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

= I got a blank page on my site. What to do about it? =
There have been some reports of blank pages, sometimes in combination with other plugins. It has probably to do with filtering the whole html page. Try disable in the admin panel the option "Options have effect" > "All contents"  (keep in mind that not the whole page will be scanned for protecting emails). Please report it.

An alternative is using my other plugin for protecting your email addresses [Email Encoder Bundle](http://wordpress.org/extend/plugins/email-encoder-bundle/). This plugin has a different approach and does not have the option to scan the whole page, which makes it more stable.

[Do you have a question? Please ask me](http://www.freelancephp.net/contact/)

== Screenshots ==

1. Link Icon on the Site
1. Admin Settings Page

= Credits =
* Title icon on Admin Options Page was made by [Aha-Soft Team](http://www.aha-soft.com/) taken form [iconfinder](http://findicons.com/icon/219920/e_mail)
* Technics used in this plugin is inspired by the methods Jeff Starr explained in [this article](http://perishablepress.com/press/2010/08/01/best-method-for-email-obfuscation/)

== Documentation ==

= Shortcode =
Create a protected mailto link in your posts:
`[wpml_mailto email="info@myemail.com"]My Email[/wpml_mailto]`

It's also possible to add attributes to the mailto link, like a target:
`[wpml_mailto email="info@myemail.com" target="_blank"]My Email[/wpml_mailto]`

= Template functions =
Create a protected mailto link in your template use:
`<?php if (function_exists('wpml_mailto')) { echo wpml_mailto($display, $attrs); } ?>`

Filter given content to protect mailto links, shortcodes and plain emails (according to the settings in admin):
`<?php if (function_exists('wpml_filter')) { echo wpml_filter($content); } ?>`

= Action hook =
The plugin also has a hook when ready, f.e. to add extra filters:
`function extra_filters($filter_callback, $object) {
    add_filter('some_filter', $filter_callback);
}
add_action('wpml_ready', 'extra_filters');`

= Filter hook =
The wpml_mailto filter gives you the possibility to manipulate output of the mailto created by the plugin. F.e. make all mailto links bold:
`public function special_mailto($link, $display, $email, $attrs) {
    return '<b>'. $link .'</b>';
}
add_filter('wpml_mailto', 'special_mailto', 10, 4);`

Now all mailto links will be wrapped around a `<b>`-tag.

== Changelog ==

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
