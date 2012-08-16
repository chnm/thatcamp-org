=== Simple Contact Form Revisited Widget ===
Contributors: alexanderzagniotov
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CWNZ5P4Z8RTQ8
Tags: contact form, email form, simple contact form, shortcode contact form, short code, shortcode, simple email form, widget email form, widget contact form
Requires at least: 3.0.0
Tested up to: 3.2.1
Stable tag: 2.0.9

A simple, yet elegant email contact form plugin that installs as a widget.

== Description ==

A simple, yet elegant email contact form plugin that installs as a widget. The contact form widget is using jQuery for validation. In addition, the widget makes it difficult for email bots to harvest your email address by encrypting it before rendering the HTML form. This sidebar widget is particularly useful when you want to allow your visitors to contact you without forcing them to navigate away from the current page. 

In addition to the widget, the plugin also installs contact form short code that can be used in pages, posts and text widgets. The short code generates a contact form that functions just like the form in the widget.

Please note: 
Although I try my best to release a bug-free code, one or two may slip through. It would be so cool and I would really appreciate it if you would report any bugs to me first at http://initbinder.com/plugins, before reporting the plugin to be broken. I am quick to respond. 
Thanks!

== Installation ==

Install this plugin by downloading and unzipping the ZIP archive into your plugins directory (/wp-content/plugins). Alternatively, you can upload the ZIP archive using Wordpress upload function.

Activate the plugin in order to start using it

To use, simply drag the 'AZ :: Contact Form' widget into a sidebar. Please note, you theme has to be widget-enabled

To change the styles of the contact form, open style.css file in plugin editor in Wordpress.

In order to use short code in a post, page or a text widget, please note the following example: [contactform email=" PUT YOUR EMAIL ADDRESS HERE "]

If no email address specified, the blog admin email is going to be used.

IMPORTANT: Please update to recent version if you are running version 2.0

== Frequently Asked Questions ==

N/A

== Screenshots ==

1. Widget editing interface.
2. Configured widget in the theme
3. Widget validation in action
4. Recipient email encrypted
5. Confirmation message on success
6. Contact form short code in the post editor
7. Generated contact form in the post

== Changelog ==

= 2.0.9 =
* Enhancement: Tabbing

= 2.0.8 =
* Enhancement: JS now running in its own eco environment

= 2.0.7 =
* Enhancement: CSS changes

= 2.0.6 =
* Enhancement: UI changes, plus email validation fix

= 2.0.5 =
* Enhancement: Disabling submit button on submit, to prevent double submission

= 2.0.4 =
* Enhancement: Made plugin CSS no to be dependant on the default WP CSS classes

= 2.0.3 =
* Bug: When parsing short code, now using get_option('admin_email'), instead of get_bloginfo('admin_email) in order to get admin email from DB.

= 2.0.2 =
* Bug: Forcing JS scripts to be printed in the footer when registering shortcode

= 2.0.1 =
* Bug: JS scripts not printing when using short code without active widget

= 2.0 =
* Added contact form shortcode that can be used in pages, posts and text widgets

= 1.0 =
* Initial release

== Upgrade Notice ==

Remove the old version of the plugin and install the most recent one. No additional configuration is required.
