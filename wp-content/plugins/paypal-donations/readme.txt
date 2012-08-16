=== PayPal Donations ===
Contributors: artstorm
Tags: paypal, donation, shortcode, widget, donate, button, sidebar
Requires at least: 2.7
Tested up to: 3.3.1
Stable tag: 1.6

Easy, simple setup to add a PayPal Donation button as a Widget or with a shortcode.

== Description ==

Adds a PayPal donation shortcode and sidebar Widget to WordPress. The options menu lets you setup you PayPal ID and a few other optional settings. You can choose which donation button you want to use or if you want to use your own button. You can also set an optional default purpose and reference which can be overridden on each inserted instance with the shortcode options or in the Widget settings. There is also options available for currency, localization of the button, custom payment page style and the return page.

= Widget =

In the Appearance -> Widgets in WordPress 2.8 or newer you'll find the PayPal Donations widget. After adding it to your sidebar you can enter a title for the Widget, some descriptive text that will appear above the button and specify an optional purpose and reference for the donation button to override the default settings.

= Shortcode =

Insert the button in your pages or posts with this shortcode

`[paypal-donation]`

Which is the simplest option, and uses all default and optional settings. If you want to make a specific button for a specific purpose you can add these options:

`[paypal-donation purpose="Spline Importer" reference="3D Plugins"]`

as an example.

This donation plugin generates valid XHTML Transitional and Strict code.

For complete usage instructions see: [PayPal Donations](http://wpstorm.net/wordpress-plugins/paypal-donations/ "Complete Usage Instructions for PayPal Donations")

See the [Changelog](http://wordpress.org/extend/plugins/paypal-donations/changelog/) for what's new. Available [Translations](http://wpstorm.net/wordpress-plugins/paypal-donations/#translations).


== Installation ==

= Install =

1. Upload the 'paypal-donations' folder  to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -> PayPal Donations and start entering your info.

= Uninstall =

1. Deactivate PayPal Donations in the 'Plugins' menu in Wordpress.
2. After Deactivation a 'Delete' link appears below the plugin name, follow the link and confim with 'Yes, Delete these files'.
3. This will delete all the plugin files from the server as well as erasing all options the plugin has stored in the database.

== Frequently Asked Questions ==

Please visit [PayPal Donations' Comments](http://wpstorm.net/wordpress-plugins/paypal-donations/#comments) for questions and answers.

== Screenshots ==

1. Admin page where you setup the plugin
1. Sidebar Widget Admin Options

== Changelog ==

= Version 1.6 - 13 Mar 2012 =
 * Added an option under extras to set a PayPal checkout language.
 * Added filter `paypal_donations_purpose_html`, to allow replacing the default
   hidden form element for purpose with some other form input type.
 * Included Romanian translation by Marko Pozner.

= Version 1.5 - 9 Jan 2012 =
 * Added an option to disable PayPal statistics tracking image.
 * Added an option to center the button by overriding the theme CSS.

= Version 1.4.9.6 - 23 Oct 2011 =
 * Included Malay – Bahasa Melayu translation by Amir Shariff.

= Version 1.4.9.5 - 3 Sep 2011 =
 * Added filter 'paypal_donations_url', to hook into the URL used, to be able to
   try the button against PayPal's Sandbox for instance.
 * Added filter 'paypal_donations_amount', to hook into the set amount before
   rendering the button. 
 * Included Lithuanian translation by Nata Strazda.
 * Changed the plugin class name from paypal_donations to Paypal_Donations.

= Version 1.4.9.4 - 22 May 2011 =
 * Included Norwegian translations by Tore Johnny Bråtveit.

= Version 1.4.9.3 - 18 Apr 2011 =
 * Added Turkish Lira (TRY) to the list of currencies.
 
= Version 1.4.9.2 - 02 Apr 2011 =
 * Added four new currencies that PayPal supports for donations to choose from:
   Brazilian Real (BRL), Taiwan New Dollar (TWD), Philippine Peso (PHP), Thai
   Baht (THB)

= Version 1.4.9.1 - 08 Jan 2011 =
 * Included Turkish translation by Ersan Özdil.

= Version 1.4.9 - 08 Jul 2010 =
 * Included Dutch translation by WP webshop.

= Version 1.4.8 - 29 Jun 2010 =
 * Added shortcode for button_url.
 * Removed a few deprecated functions from the code.
 * Included German translation by Rian Kremer.

= Version 1.4.7 - 30 Apr 2010 =
 * Included Spanish translation by Neoshinji.

= Version 1.4.6 - 19 Feb 2010 =
 * Included Italian translation by Gianni Diurno.
 * Updated the .pot file to include the latest strings for translations.

= Version 1.4.5 - 28 Nov 2009 =
 * Added shortcode for the return_page.

= Version 1.4.4 - 4 Nov 2009 =
 * Included Hebrew translation by Daniel Chcouri.

= Version 1.4.3 - 15 Oct 2009 =
 * Included Danish translation by Georg S. Adamsen.

= Version 1.4.2 - 14 Oct 2009 =
 * Included Albanian translation by Romeo Shuka.

= Version 1.4.1 - 6 Sep 2009 =
 * Corrected available language translations for the button graphics.

= Version 1.4 - 6 Sep 2009 =
 * Added a dropdown menu to select country and language to localize the graphics
   of the donation button.

= Version 1.3.3 - 2 Sep 2009 =
 * Included French translation by Thomas Cailhe (Oyabi).

= Version 1.3.2 - 28 Aug 2009 =
 * Included Russian translation by FatCow.
 
= Version 1.3.1 - 25 August 2009 =
 * Added Swedish translation.
 * Added the .pot file and textdomained all strings to allow translations.

= Version 1.3 - 8 July 2009 =
 * Added an option to set a default donation amount.

= Version 1.2 - 18 June 2009 =
 * Added a sidebar Widget (WordPress 2.8 or newer)

= Version 1.1 - 23 May 2009 =
 * Added an option to select the currency to make the donations in.
 * Changed the default button in a new install from a custom button to the large
   button.

= Version 1.0 - 7 May 2009 =
 * Initial Release