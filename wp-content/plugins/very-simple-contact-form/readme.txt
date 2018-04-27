=== Very Simple Contact Form ===
Contributors: Guido07111975
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donation%40guidovanderleest%2enl
Version: 8.4
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 3.7
Tested up to: 4.9
Stable tag: trunk
Tags: simple, contact, form, contact form, email


This is a very simple contact form. Use shortcode [contact] to display form on page or use the widget.


== Description ==
= About =
This is a very simple responsive translatable contact form.

Form only contains fields for Name, Email, Subject and Message. And a simple captcha (random number).

Use a shortcode to display your form on a page or use the widget.

You can personalize your form via the settingspage or by adding attributes to the shortcode or the widget.

= How to use =
After installation add shortcode `[contact]` on your page to display the form.

Or go to Appearance > Widgets and add the widget to your sidebar.

By default form submissions will be send to your site's admin email address (set in Settings > General). It's also possible to send a confirmation email to sender.

= Settingspage =
Via Settings > VSCF you can:

* Keep form submissions and settings when uninstalling plugin
* Activate the listing of form submissions in your dashboard
* Activate confirmation email to sender
* Activate a privacy checkbox on form
* Disable the collection of IP address
* Change form labels and messages

Several settings can be overwritten when using the relevant (shortcode) attributes below.

= Shortcode attributes = 
* Change admin email address: `[contact email_to="your-email-here"]`
* Send to multiple email addresses: `[contact email_to="first-email-here, second-email-here"]`
* Change "From" email header: `[contact from_header="your-email-here"]`
* Hide subject field: `[contact hide_subject="true"]`
* Change email subject: `[contact subject="your subject here"]`
* Activate confirmation email to sender: `[contact auto_reply="true"]`
* Change "thank you" message in confirmation email: `[contact auto_reply_message="your message here"]`
* Change sending succeeded ("thank you") message: `[contact message_success="your message here"]`
* Scroll back to form location after submit: `[contact scroll_to_form="true"]`

You can also add multiple attributes. Use a single whitespace to separate multiple attributes.

* Example: `[contact email_to="your-email-here" subject="your subject here"]`

= Widget attributes =
The widget supports the same attributes. Enter them without shortcode itself and without brackets.

Example 1:

* If shortcode attribute is: `[contact email_to="your-email-here"]`
* Widget attribute will be: `email_to="your-email-here"`

Example 2:

* If shortcode attribute is: `[contact subject="your subject here"]`
* Widget attribute will be: `subject="your subject here"`

= List form submissions in dashboard =
Via Settings > VSCF you can activate the listing of form submissions in your dashboard.

After activation you will notice a new (translated) menu item called "Submissions".

Plugin [Contact Form DB](https://github.com/mdsimpson/contact-form-7-to-database-extension/releases) is also supported.

= SMTP =
SMTP (Simple Mail Transfer Protocol) is an internet standard for sending emails.

WordPress supports the PHP `mail()` function by default, but when using SMTP there's less chance your form submissions are being marked as spam.

You should install an additional plugin for this. You could install for example:

* [Gmail SMTP](https://wordpress.org/plugins/gmail-smtp/)
* [Easy WP SMTP](https://wordpress.org/plugins/easy-wp-smtp/)
* [WP mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/)
* [Post SMTP](https://wordpress.org/plugins/post-smtp/)

= Question? =
Please take a look at the FAQ section.

= Translation =
Not included but plugin supports WordPress language packs.

More [translations](https://translate.wordpress.org/projects/wp-plugins/very-simple-contact-form) are very welcome!

= Credits =
Without the WordPress codex and help from the WordPress community I was not able to develop this plugin, so: thank you!

Enjoy!


== Installation ==
Please check Description section for installation info.


== Frequently Asked Questions ==
= How do I set plugin language? =
Plugin will use the site language, set in Settings > General.

If plugin isn't translated into this language, language fallback will be English.

= What is the default email subject? =
By default the email subject contains the name of your site, followed by the subject that sender has filled in. If subject field is hidden it only contains the name of your site.

You can change this subject using an attribute.

This subject will also be used in the confirmation email to sender (if activated).

= Why is the "from" email address not from sender? =
I have used a default so called "From" email header to avoid form submissions being marked as spam.

Best practice is using a "From" email header (an email address) that ends with your site domain.

That's why the default "From" email header starts with "wordpress" and ends with your site domain.

You can change the "From" email header using an attribute.

Your reply to sender will use another email header, called "Reply-To", which is the email address that sender has filled in.

= What do you mean with "thank you" message? =
A "thank you" message is displayed after submitting the form and in the confirmation email to sender (if activated).

It's the (translated) message: Thank you! You will receive a response as soon as possible.

You can change this message via Settings > VSCF or using an attribute.

= Can I use multiple shortcodes? =
Do not use multiple shortcodes on the same page. This might cause a conflict.

But you can use the shortcode on a page and the widget in your sidebar.

= Why does form submission fail? =
An error message is displayed if plugin was unable to send form. This might be a server issue.

Your hosting provider might have disabled the PHP `mail()` function of your server. Ask them for more info about this.

They might advice you to install a SMTP plugin.

= Why am I not receiving form submissions? =
* Look also in your junk/spam folder.
* Check the Description section above and check shortcode (attributes) for mistakes.
* Install another contact form plugin (such as Contact Form 7) to determine whether it's caused by my plugin or something else.
* In case you're using a SMTP plugin, please check their settingspage for mistakes.

= Why does the captcha not display properly? =
The captcha (random number) uses a php session and cookie to temporary store the number.

Your hosting provider might have disabled the use of sessions. Ask them for more info about this.

Or your browser blocks cookies. You should enable cookies.

= Does this plugin has anti-spam features? =
Of course, the default WordPress sanitization and escaping functions are included.

It also contains 2 (invisible) honeypot fields (firstname and lastname) and a simple captcha (random number).

= Does VSCF meet the conditions of the GDPR? =
The General Data Protection Regulation (GDPR) is a regulation in EU law on data protection and privacy for all individuals within the European Union.

I did my best to meet the conditions of the GDPR:

* You can activate a privacy checkbox on form
* You can disable the collection of IP address
* The cookie (used for the captcha number) does not store personal data
* Form submissions are safely stored in database, similar to how the native posts and pages are stored
* You can easily delete form submissions from database

= How can I make a donation? =
You like my plugin and you're willing to make a donation? Nice! There's a PayPal donate link on the WordPress plugin page and my website.

= Other question or comment? =
Please open a topic in plugin forum.


== Changelog ==
= Version 8.4 =
* added bootstrap css class to privacy checkbox
* minor change in file vscf-style
* updated readme file

= Version 8.3 =
* new: disable the collection of ip address (thanks marie)
* updated most files

= Version 8.2 =
* removed the capability to create new submission in dashboard
* changed the id of name and email column on submissions page
* updated file vscf

= Version 8.1 =
* some textual changes on settingspage

= Version 8.0 =
* name and email column on submissions page are now sortable
* updated the changelog of last versions (hopefully more clear now)
* did this because of the many changes in last versions

For all versions please check file changelog.


== Screenshots == 
1. Very Simple Contact Form (Twenty Seventeen theme).
2. Very Simple Contact Form (Twenty Seventeen theme).
3. Very Simple Contact Form widget (Twenty Seventeen theme).
4. Very Simple Contact Form widget (dashboard).
5. Very Simple Contact Form settingspage (dashboard).
6. Very Simple Contact Form submissions (dashboard).