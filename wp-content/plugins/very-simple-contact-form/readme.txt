=== Very Simple Contact Form ===
Contributors: Guido07111975
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donation%40guidovanderleest%2enl
Version: 7.1
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 3.7
Tested up to: 4.8
Stable tag: trunk
Tags: simple, contact, form, contact form, email


This is a very simple contact form. Use shortcode [contact] to display form on page or use the widget.


== Description ==
= About =
This is a very simple responsive translatable contact form.

Form only contains Name, Email, Subject and Message. And a simple captcha sum.

It's designed to fulfill the needs of most websites that only require a basic contact form, with no additional fields.

= How to use =
After installation add shortcode `[contact]` on your page to display the form.

Or go to Appearance > Widgets and add the widget to your sidebar.

By default form submissions will be send to your site's admin email address (set in Settings > General). It's also possible to send a confirmation mail to sender.

While adding the shortcode or the widget you can add several attributes to personalize your form.

While adding the widget you can add some additional information above your form.

= Shortcode attributes = 
* Change email from admin: `[contact email_to="your-email-here"]`
* Send to multiple email: `[contact email_to="first-email-here, second-email-here"]`
* Change default "From" email header: `[contact from_header="your-email-here"]`
* Activate confirmation mail to sender: `[contact auto_reply="true"]`
* Change default message in mail: `[contact auto_reply_message="your message here"]`
* Hide subject field: `[contact hide_subject="true"]`
* Change default mail subject: `[contact subject="your subject here"]`
* Change default mail subject prefix: `[contact prefix_subject="your prefix here"]`
* Scroll back to form location after submit: `[contact scroll_to_form="true"]`

You can change labels and messages using an attribute.

* Labels: label_name, label_email, label_subject, label_captcha, label_message, label_submit
* Labels in case of error: error_name, error_email, error_subject, error_captcha, error_message
* Sending succeeded: message_success
* Sending failed: message_error

Examples:

* Change Name label: `[contact label_name="Your Name"]`
* Change Submit label: `[contact label_submit="Send"]`
* Change captcha label: `[contact label_captcha="Please enter %s"]`
* Change captcha label: `[contact label_captcha="Please enter %s here"]`
* Change sending succeeded message: `[contact message_success="your message here"]`

You can also add multiple attributes.

* Example: `[contact email_to="your-email-here" subject="your subject here"]`

= Widget attributes =
The widget supports the same attributes. Enter them without shortcode itself and without brackets.

Examples:

* Change email from admin: `email_to="your-email-here"`
* Send to multiple email: `email_to="first-email-here, second-email-here"`
* Change default "From" email header: `from_header="your-email-here"`
* Activate confirmation mail to sender: `auto_reply="true"`
* Change default message in mail: `auto_reply_message="your message here"`
* Hide subject field: `hide_subject="true"`
* Change default mail subject: `subject="your subject here"`
* Change default mail subject prefix: `prefix_subject="your prefix here"`
* Scroll back to form location after submit: `scroll_to_form="true"`
* Change Name label: `label_name="Your Name"`
* Change Submit label: `label_submit="Send"`
* Change captcha label: `label_captcha="Please enter %s"`
* Change captcha label: `label_captcha="Please enter %s here"`
* Change sending succeeded message: `message_success="your message here"`
* Multiple attributes: `email_to="your-email-here" subject="your subject here"`

= List form submissions in dashboard =
With plugin [Contact Form DB](https://wordpress.org/plugins/contact-form-7-to-database-extension) you can list form submissions in your dashboard.

Note: Contact Form DB is currently only available on [GitHub](https://github.com/mdsimpson/contact-form-7-to-database-extension/releases).

= SMTP =
SMTP (Simple Mail Transfer Protocol) is an internet standard for sending emails. 

WordPress supports the PHP mail() function by default, but when using SMTP there's less chance your form submissions are being marked as spam.

You must install an additional plugin for this. I have tested my plugin with these SMTP plugins:

* [Gmail SMTP](https://wordpress.org/plugins/gmail-smtp/)
* [Easy WP SMTP](https://wordpress.org/plugins/easy-wp-smtp/)
* [WP mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/)
* [Postman SMTP](https://wordpress.org/plugins/postman-smtp/)

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
Plugin uses the WP Dashboard language, set in Settings > General.

If plugin language pack is not available, language fallback will be English.

= How do I add attributes? =
You can find more info about this at the Description section.

= Does sender receive a confirmation after submitting form? =
Yes, this is possible.

You can find more info about this at the Description section.

= Can I add a personal message in the confirmation mail? =
Yes, this is possible.

You can find more info about this at the Description section.

Note: the default message in the confirmation mail is the same as the default message that is displayed after submitting the form.

= Why is the "from" email not from sender? =
I have used a default so called "From" email header to avoid form submissions being marked as spam.

Best practice is using a "From" email header (an email address) that ends with your site domain.

That's why the default "From" email header starts with "wordpress" and ends with your site domain.

Your reply to sender will use another email header, called "Reply-To", which is the email address that sender has filled in.

= Can I change the "From" email header? =
Yes, this is possible.

You can find more info about this at the Description section.

= Can I hide Subject field? =
Yes, this is possible.

You can find more info about this at the Description section.

= Can I change the mail subject? =
Yes, this is possible.

By default the mail subject consists of a prefix (name of your site) followed by the subject that user has entered. And if subject field is hidden it only consists of a prefix.

You can change the prefix or whole subject using an attribute.

You can find more info about this at the Description section.

Note: this subject will also be used in the confirmation to sender (if activated).

= Can user enter HTML in form? =
Yes, save HTML is allowed in message field and widget info field.

= Can I use multiple shortcodes? =
Do not use multiple shortcodes on the same page. This might cause a conflict.

But you can use the shortcode on a page and the widget in your sidebar.

= Are form submissions listed in my dashboard? =
No, my plugin only sends form submissions to the email address of your choice.

With plugin [Contact Form DB](https://wordpress.org/plugins/contact-form-7-to-database-extension) you can list form submissions in your dashboard.

Note: Contact Form DB is currently only available on [GitHub](https://github.com/mdsimpson/contact-form-7-to-database-extension/releases).

= Why does form submission fail? =
An error message is displayed if plugin was unable to send form. This might be a server issue.

Your hosting provider might have disabled the PHP mail() function of your server. Ask them for more info about this.

They might advice you to install a SMTP plugin.

You can find more info about this at the Description section.

= Why am I not receiving form submissions? =
* Look also in your junk/spam folder.
* Check the Description section above and check shortcode (attributes) for mistakes.
* Install another contact form plugin (such as Contact Form 7) to determine whether it's caused by my plugin or something else.
* In case you're using a SMTP plugin, please check their settingspage for mistakes.

= Why does the captcha number not display properly? =
The captcha (random number) uses a php session to temporary store the number and some hostingproviders have disabled the use of sessions. Ask them for more info about this.

= Does this plugin has anti-spam features? =
Of course, the default WordPress sanitization and escaping functions are included.

It also contains 2 (invisible) honeypot fields (firstname and lastname) and a simple captcha sum.

= How can I make a donation? =
You like my plugin and you're willing to make a donation? Nice! There's a PayPal donate link on the WordPress plugin page and my website.

= Other question or comment? =
Please open a topic in plugin forum.


== Changelog ==
= Version 7.1 =
* new: attribute to change default mail subject prefix: prefix_subject
* new: attribute to scroll back to form location after submit: scroll_to_form
* for more info please check readme file
* thanks startover909

= Version 7.0 =
* minor change in files vscf-form and vscf-widget-form

= Version 6.9 =
* form now supports bootstrap css
* this means I have added bootstrap css classes
* updated files vscf-form and vscf-widget-form
* updated file vscf-style

= Version 6.8 =
* best practice: removed closing PHP tag from most files
* best practice: added newline at end of most files

For all versions please check file changelog.


== Screenshots == 
1. Very Simple Contact Form (Twenty Seventeen theme).
2. Very Simple Contact Form (Twenty Seventeen theme).
3. Very Simple Contact Form widget (Twenty Seventeen theme).
4. Very Simple Contact Form widget (dashboard).