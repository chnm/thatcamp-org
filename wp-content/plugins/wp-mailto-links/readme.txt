=== WP Mailto Links - Protect Email Addresses ===
Contributors: ironikus
Tags: hide, email, email address, mailto, antispam, protect, spambot, encode, encrypt, obfuscate, email link, protection
Requires at least: 4.7
Tested up to: 5.2.3
Stable tag: 3.1.0
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Protect & encode email addresses safely from spambots & spamming. Easy to use - encodes emails out-of-the-box.

== Description ==
Protect and encode email addresses safely from spambots, spamming and other robots. Easy to use out-of-the-box without any configuration.

= Features =
* Full page protection for emails
* Instant results (No confiruation needed)
* Protects mailto links, plain emails, email input fields, RSS feeds and much more
* Autmoatic protection technique detection (Our plugin chooses automatically the best protection technique for each email)
* Exclude posts and pages from protection
* Automatically convert plain emails to mailto-links
* Automatically convert plain emails to png images
* Supports rot13 encoing, escape encoding, CSS directions, entity encoding and much more
* Deactivate CSS directions manually for backwards compatibility
* Shortcode support: `[wpml_mailto]`
* Template tag support: `wpml_mailto()` and `wpml_filter()`

The plugin combines the best email protection methods (CSS, PHP and JavaScript techniques).

= Free Website Check  =
We offer you a free tool to test if your website contains unprotected emails. You can use our website checker by [clicking here](https://ironikus.com/email-checker/)

= Easy to use =
The plugin works out-of-the-box to protect your email addresses. After activating the plugin, all options are already set for protecting your emails and mailto links.


== Installation ==

1. Go to `Plugins` in the Admin menu
1. Click on the button `Add new`
1. Search for `WP Mailto Links` and click 'Install Now' or click on the `upload` link to upload `wp-mailto-links.zip`
1. Click on `Activate plugin`
1. You will find the settings page under Settings -> WP Mailto Links or directly wihtin the menu.
1. The plugin sets by default the right options for protecting your emails. All mailto links will automatically be scanned and protected.

== Frequently Asked Questions ==

= How does it work? =
The plugin combines the best email protection methods (CSS and JavaScript techniques). All of them are explained in [this article](https://ironikus.com/docs/knowledge-base/what-will-be-protected/).

A webpage contains code on the backside. In the code all email addresses will be obfuscated, to hide them from spambots. Only when people see the page and click on the mailto link, the encoded email address will be translated to a normal email address.

= What's the best way to protect my email address(es) on my site? =
Off course by activating this plugin :) on your site. But even still it's not recommended to use email addresses directly in your posts or in your theme templates.
It would be better to use the shortcode `[wpml_mailto]` and template tag `wpml_mailto()`, because when the plugin isn't active anymore, the email addresses would not be shown on your site.

= How can I exclude pages? =
You can exclude pages within our settings page. Simply activate the button for advanced settings and you will see an input field. 
In there, simply separate the page/post ids with a comma and save. 

Here is an example: 12,455,19

= Shortcode does not work in widgets. How can I make it work? =
By default shortcodes are not applied to (text) widgets. To support that you can add it to the text widget filter ([for more](http://www.wprecipes.com/how-to-add-shortcodes-in-sidebar-widgets)).
If you are not a coder, then just activate [this plugin](http://wordpress.org/extend/plugins/shortcodes-in-sidebar-widgets/), which does the same thing.

Do you have a problem or found a bug, please [report it](https://wordpress.org/support/plugin/wp-mailto-links#postform).

== Screenshots ==

1. Admin Settings Page
2. Encoded Emails with Link Icon on the Site

== Documentation ==

The plugin works out-of-the-box to protect your email addresses. All settings are default set to protect your email addresses automatically with the best method available.
If you want to manually create protected mailto links, just use the shortcode (`[wpml_mailto]`) within your posts or use the template tags (`wpml_mailto()` or `wpml_filter()`) in your theme files.

= Shortcode `[wpml_mailto email="..."]...[/wpml_mailto]` =
Create a protected mailto link in your posts:
`[wpml_mailto email="info@myemail.com"]My Email[/wpml_mailto]`

It's also possible to add attributes to the mailto link, like a target:
`[wpml_mailto email="info@myemail.com" target="_blank"]My Email[/wpml_mailto]`

= Shortcode `[wpmt_protect]...[/wpmt_protect]` =
Protect content using our plugin that is not encodedby default (E.g. some ajax loaded values):
`[wpmt_protect]YOUR CONTENT YOU WANT TO CHECK FOR EMAILS[/wpmt_protect]`

It's also possible to customize the encoding type using "protect_using". Possible values: char_encode, strong_method, without_javascript, with_javascript:
`[wpmt_protect protect_using="..."]YOUR CONTENT YOU WANT TO CHECK FOR EMAILS[/wpmt_protect]`

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

== Changelog ==
= 3.1.0 =
* Feature: Exclude script tags from being encoded
* Fix: Revalidate and strip escape sequences for encode_escape function

= 3.0.9 =
* Feature: Optimize Jetpack integration to also filter against image attribute description tags
* Feature: Soft-filter html placeholder tags
* Feature: Allow template tags to work as well with the plugin settings se tto "Do nothing"
* Fix: Only one match of the soft attributes was soft encoded properly
* Fix: Fix fatal error with template tags
* Fix: The escape js function stripped away all zeros from emails

= 3.0.8 =
* Feature: Introduce settings item to load all scripts within the footer instead of the head section
* Tweak: Integration for Divi Theme included
* Tweak: Adjust element id prefix
* Tweak: Integration for Jetpack Carousel slider meta attributes
* Fix: WP CLI stopped working due to loaded buffer

= 3.0.7 =
* Feature: Website checker to search your site for unprotected emails. Follow this URL for more information: https://ironikus.com/email-checker/
* Tweak: Merge new marketing setting into advanced area
* Tweak: Optimize some code parts for the newest WordPress standard

= 3.0.6 =
* Feature: Add setting to remove shortcodes from RSS feeds
* Tweak: Add feature to disable marketing notifications (Thanks @ac1643 for mentioning - this was probably a bug)

= 3.0.5 =
* Feature: Introduce automatic rot13 encoding using javascript
* Feature: Introduce automatic escape encoding using javascript
* Feature: Add shortcode to protect additional content that may can't be reached by the plugin (e.g. certain ajax calls)
* Feature: Randomization for Javascript methods to offer a better and less predictible protection
* Tweak: Allow javscript mailto encoding as well if email png protection is activated and javascript is allowed
* Tweak: Optimize plain email filtering after protecting mailto links and email addresses
* Tweak: Optimize settings-flow in certain combinations
* Tweak: Optimize settings description
* Tweak: Avoid unnecesary email line breaks from wrongly applied display properties 
* Tweak: Extend shortcoe help tabs
* Dev: Added new random bool function

= 3.0.4 =
* Feature: Soft-encode for script tags to preserve the javascript logic
* Tweak: Set WordPress filter settings item to advanced (since it is not recommended)
* Fix: Fix ReferenceError: jQuery is not defined for the frontend scripts 

= 3.0.3 =
* Feature: Convert plain emails to PNG images (Custom settings item in advanced settings)
* Feature: Settings to customize the created PNG Email Images 
* Feature: New setting to deactivate CSS directions manually
* Tweak: Optimize automatically chosen methods and cross-setting usage
* Tweak: Updated minimum PHP version
* Tweak: Include collaboration with MailOptin
* Tweak: Correct certain text issues
* Fix: Prevent Woocommerce variation attributes containing emails from breaking
* Fix: Correct layout issue if multi-inout with text fields is chosen
* Dev: Optimize code for WordPress standards
* Dev: New filter: wpmt/validate/email_signature (https://ironikus.com/docs/knowledge-base/filter-email-to-image-url-signature/)
* Dev: New filter: wpmt/validate/generate_email_image_url (https://ironikus.com/docs/knowledge-base/filter-email-to-image-url/)
* Dev: New filter: wpmt/settings/get_soft_attribute_regex (https://ironikus.com/docs/knowledge-base/filter-regex-soft-attributes/)

= 3.0.2 =
* Fix: Widgets did not display in certain cases

= 3.0.1 =
* Tweak: Optimized PHP Docs
* Fix: Fatal Error due to a non available function

= 3.0.0 =
* PLEASE READ BEFORE UPATING: This plugin was completely rewritten and optimized. We also said goodbye to some features (mentioned below). Please test first.
* IN CASE YOU FACE ANY ISSUES, YOU CAN REACH OUT TO US http://wordpress.org/support/plugin/wp-mailto-links#postform
* Feature: Completely rewritten version of your beloved plugin
* Feature: Feature to automatically detect the best protection method
* Feature: Choose from four new settings to choose the strength of your protections
* Feature: Added admin security check icon to encoded input fields and encoded plain emails/texts
* Feature: Also protect every single shortcode content
* Feature: Choose converting plain emails to mailto links as an additional feature
* Feature: Change filter apply from "wp" to "init" (This allows you to also grab some ajax values to parse them directy encoded)
* Tweak: Backward compatibility to the new plugin settings
* Tweak: Remove custom icon functionality due to performance optimization (If you want it back, feel free to reach out)
* Tweak: Completely performance optimized (We removed everything that is not necessary, included a better object caching and much more)
* Tweak: Simplified settings (We cleaned the settings a lot, but you can still get some your old settings page back by activating the advanced checkbox :) )
* Tweak: Optimized filter combinations
* Tweak: Allow custom settings key to be also applied within the settings form dynamically
* Dev: Code rewritten on the newest WordPress standards
* Dev: Tons of new WordPress filters and actions (For a full list, please check https://ironikus.com/docs/article-categories/wp-mailto-links/)

= 2.2.1 =
* Fix: Show admin keylock icon relatively and not absolute
* Ironikus took over development

= 2.2 =
* 2019-03-26
* bug fixes
* 10,000 installations; 88,200 downloads

= 2.1.7 =
* Fixed bug copying emailaddress (credits to Martin Kreiner)

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
