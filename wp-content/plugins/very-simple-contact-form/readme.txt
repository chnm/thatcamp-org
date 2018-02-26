=== Very Simple Contact Form ===
Contributors: Guido07111975
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donation%40guidovanderleest%2enl
Version: 7.4
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

Form only contains fields for Name, Email, Subject and Message. And a simple captcha sum.

It's designed to fulfill the needs of most websites that only require a basic contact form, with no additional fields.

Use a shortcode to display form on a page or use the widget.

While adding the shortcode or the widget you can add several attributes to personalize your form.

= How to use =
After installation add shortcode `[contact]` on your page to display the form.

Or go to Appearance > Widgets and add the widget to your sidebar.

By default form submissions will be send to your site's admin email address (set in Settings > General). It's also possible to send a confirmation email to sender.

= Shortcode attributes = 
* Change admin email address: `[contact email_to="your-email-here"]`
* Send to multiple email addresses: `[contact email_to="first-email-here, second-email-here"]`
* Change default "From" email header: `[contact from_header="your-email-here"]`
* Change default email subject: `[contact subject="your subject here"]`
* Activate confirmation email to sender: `[contact auto_reply="true"]`
* Change default "thank you" message in confirmation email: `[contact auto_reply_message="your message here"]`
* Hide subject field: `[contact hide_subject="true"]`
* Scroll back to form location after submit: `[contact scroll_to_form="true"]`

You can change field labels and messages using an attribute.

* Labels: label_name, label_email, label_subject, label_captcha, label_message, label_submit
* Labels when validation fails: error_name, error_email, error_subject, error_captcha, error_message
* Sending succeeded ("thank you") message: message_success
* Sending failed message: message_error

Examples:

* Change Name label: `[contact label_name="Your Name"]`
* Change captcha label: `[contact label_captcha="Please enter %s"]`
* Change captcha label: `[contact label_captcha="Please enter %s here"]`
* Change sending succeeded ("thank you") message: `[contact message_success="your message here"]`

You can also add multiple attributes. Use a single whitespace to separate multiple attributes.

* Example: `[contact email_to="your-email-here" subject="your subject here"]`

= Widget attributes =
The widget supports the same attributes. Enter them without shortcode itself and without brackets.

Example 1:

* If shortcode attribute is: `[contact email_to="your-email-here"]`
* Widget attribute will be: `email_to="your-email-here"`

Example 2:

* If shortcode attribute is: `[contact label_name="Your Name"]`
* Widget attribute will be: `label_name="Your Name"`

= List form submissions in dashboard =
With plugin [Contact Form DB](https://github.com/mdsimpson/contact-form-7-to-database-extension/releases) you can list form submissions in your dashboard.

= SMTP =
SMTP (Simple Mail Transfer Protocol) is an internet standard for sending emails. 

WordPress supports the PHP mail() function by default, but when using SMTP there's less chance your form submissions are being marked as spam.

You must install an additional plugin for this. I have tested my plugin with these SMTP plugins:

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
= Where is the settingspage? =
Plugin has no settingspage, use a shortcode with attributes or the widget with attributes to make it work.

= How do I set plugin language? =
Plugin will use the site language, set in Settings > General.

If plugin isn't translated into this language, language fallback will be English.

= What is the default email subject? =
By default the email subject contains the name of your site, followed by the subject that sender has filled in. If subject field is hidden it only contains the name of your site.

You can change this subject using an attribute. 

Note: this subject will also be used in the confirmation email to sender (if activated).

= Why is the "from" email not from sender? =
I have used a default so called "From" email header to avoid form submissions being marked as spam.

Best practice is using a "From" email header (an email address) that ends with your site domain.

That's why the default "From" email header starts with "wordpress" and ends with your site domain.

You can change the "From" email header using an attribute.

Your reply to sender will use another email header, called "Reply-To", which is the email address that sender has filled in.

= What do you mean with "thank you" message? =
A "thank you" message is displayed after submitting the form and in the confirmation email to sender (if activated). 

It's the (translated) message: Thank you! You will receive a response as soon as possible.

You can change this message using an attribute.

= Can I use multiple shortcodes? =
Do not use multiple shortcodes on the same page. This might cause a conflict.

But you can use the shortcode on a page and the widget in your sidebar.

= Are form submissions listed in my dashboard? =
No, my plugin only sends form submissions to the email address of your choice.

With plugin [Contact Form DB](https://github.com/mdsimpson/contact-form-7-to-database-extension/releases) you can list form submissions in your dashboard.

= Why does form submission fail? =
An error message is displayed if plugin was unable to send form. This might be a server issue.

Your hosting provider might have disabled the PHP mail() function of your server. Ask them for more info about this.

They might advice you to install a SMTP plugin.

= Why am I not receiving form submissions? =
* Look also in your junk/spam folder.
* Check the Description section above and check shortcode (attributes) for mistakes.
* Install another contact form plugin (such as Contact Form 7) to determine whether it's caused by my plugin or something else.
* In case you're using a SMTP plugin, please check their settingspage for mistakes.

= Why does the captcha number not display properly? =
The captcha (random number) uses a php session to temporary store the number and some hosting providers have disabled the use of sessions. Ask them for more info about this.

= Does this plugin has anti-spam features? =
Of course, the default WordPress sanitization and escaping functions are included.

It also contains 2 (invisible) honeypot fields (firstname and lastname) and a simple captcha sum.

= How can I make a donation? =
You like my plugin and you're willing to make a donation? Nice! There's a PayPal donate link on the WordPress plugin page and my website.

= Other question or comment? =
Please open a topic in plugin forum.


== Changelog ==
= Version 7.4 =
* file vscf-widget-form: fixed mistake in resetting captcha (thanks malae) 

= Version 7.3 =
* removed attribute to change default email subject prefix: prefix_subject
* from now on you can only change whole email subject using attribute: subject
* have removed this to avoid confusion while setting up a custom email subject

= Version 7.2 =
* updated file vscf-style
* files vscf-form and vscf-widget-form: added escaping to empty variables
* removed all faqs from readme file which refer back to the description section
* updated info about SMTP plugins in readme file

= Version 7.1 =
* new: attribute to change default email subject prefix: prefix_subject
* new: attribute to scroll back to form location after submit: scroll_to_form
* for more info please check readme file
* thanks startover909

= Version 7.0 =
* minor change in files vscf-form and vscf-widget-form

For all versions please check file changelog.


== Screenshots == 
1. Very Simple Contact Form (Twenty Seventeen theme).
2. Very Simple Contact Form (Twenty Seventeen theme).
3. Very Simple Contact Form widget (Twenty Seventeen theme).
4. Very Simple Contact Form widget (dashboard).