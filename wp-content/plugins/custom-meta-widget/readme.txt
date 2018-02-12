=== Custom Meta Widget ===
Plugin URI: http://shinraholdings.com/plugins/custom-meta-widget/
Contributors: bitacre
Donate link: http://shinraholdings.com/donate
Tags: meta,hide,link,admin,simple,widget,default,wordpress.org,change,rss,remove,login
Requires at least: 2.8
Tested up to: 4.9.4
Stable tag: 1.5.1

Clone of the standard Meta widget plus options to hide log in/out, admin, feed and WordPress.org/custom links.

== Description ==
This plugin acts in exactly the same way as the standard Meta widget except that it adds options which allow you control which of the 5 standard Meta widget links should be displayed, with the additional option to add your own custom link.

The standard Meta widget that comes with WordPress offers no customization and requires you to show all 5 of the standard links (register, login, entries RSS, comments RSS, and WordPress.org, as well as any links added by themes or other plugins).

== Installation ==
1. Download the latest zip file and extract the `custom-meta-widget` directory.
2. Upload this directory inside your `/wp-content/plugins/` directory.
3. Activate the 'Custom Meta Widget' on the 'Plugins' menu in WordPress.
4. Add the 'Custom Meta' widget to your sidebar (using the Appearance > Widgets menu) and choose which customizations you would like.

== Frequently Asked Questions ==
= Why does my Meta widget look the same? =
This plugin does not modify the standard WordPress Meta widget. It is a completely separate widget titled 'Custom Meta.' If you are currently using the standard Meta widget, you will have to remove it from your sidebar and replace it with the Custom Meta widget using the Appearance > Widgets menu.

It also looks identical to the standard Meta Widget unless you opt to change which links are shown/hidden in the Appearance > Widgets menu, under the expanded 'Custom Meta' widget.

= Which links can I hide/show? =
You can remove any, all, or none of the links which appear on the standard Meta widget. You are also able to add a custom link to the list if you wish.

= Why did you make this? =
I didn't like having the 'Comments RSS' link on the sidebar, but did like having the rest of them. I couldn't find a plugin to do that and manually editing the code to accomplish this, while simple, is a dirty hack that breaks with every WordPress update, so I created a crude plugin to remove just the 'Comments RSS' link. I then cleaned this up and added an options page for all the links so that no one else would have to do this in the future.

= Can you add this feature I just thought of? =
Can I? Yes. Will I? Yes, if I think it would be a helpful addition. I'm trying to keep things clean and simple, but there's always room for improvement, so let me know if you think a feature is lacking!

= Will vanilla WordPress ever implement some variation of this? =
I don't know man. They haven't yet, so it doesn't look very likely. But I think they should and I would be glad if they did.

= Why did you add an option to link to the plugin site, you horrible sellout? =
Most people, myself included, are understandably reluctant to donate real currency for free stuff. However, putting a link to my site from your site is just as helpful and a much more comfortable and forthcoming mode of charity, especially in the webmaster community. Also, because I'm a horrible sellout.

== Screenshots ==
1. The widget's options page on the Appearance > Widgets menu.
2. The widget displayed on the sidebar with 3 links hidden and a custom link added.
3. Comparison of options between the standard and Custom Meta Widget.

== Changelog ==
= 1.5.1 =
* Fixed the "show custom link" functionality. My bad.

= 1.5.0 =
* Some formatting changes to bring closer alignment with the current vanilla WordPress Meta Widget.
* Added ability for links added to the meta widget by other themes/plugins as introduced in 4.4 via `wp_meta()`.
* Added compatibility with some new filters introduced since the last major update.
* General code clean-up and modernization.

= 1.4.8 =
* Updated deprecated __construct() method
* Thank you to [dsmiller](https://wordpress.org/support/topic/updated-constructor?replies=4#post-8147479)

= 1.4.7 =
* Purely cosmetic update for WordPress 4.4

= 1.4.6 = 
* WordPress 3.6.1 update
* Added fr_FR (French) translation
* Added nl_NL (Dutch) translation
* Added pl_PL (Polish) translation
* Added sr_RS (Serbian) translation

= 1.4.5 =
* WordPress 3.5.1 update
* Added sk_SK (Slovak) translation

= 1.4.4 =
* Added de_DE (German) translation
* General update to language files
* Major code efficiency upgrades

= 1.4.3 =
* Small correction to the ru_RU (Russian) translation

= 1.4.2 =
* WordPress 3.5 update
* Added translations for ru_RU

= 1.4.1 =
* Added missed textdomains for more complete i18n
* Added translations for da_DK, en_NZ, en_PH, and en_NZ
* Updated/fixed all translation files

= 1.4.0 =
* Code efficiency improvements (better loops)
* Now removes default Meta Widget (to avoid confusion)

= 1.3.6 =
* Small metadata update

= 1.3.5_a =
* Updated for WordPress version 3.3.2
* Fixed the 1 missed textdomain for i18n

= 1.3.5 =
* Fixed typo introduced in i18n transition.
* Adds translation files for en-AU, en-CA, en-GB, en-IE, and es-MX.

= 1.3.4 =
* Fixed minor issues with i18n text domains.

= 1.3.3 =
* Internationalized all displayed text.

= 1.3.2 =
* Added additional error handling for the custom link url input.
* Added an option to beg you for link love (disabled by default, and not annoying).
* Slightly changed widget options to look more like the vanilla widget's.

= 1.3.1 =
* Added error handling to prevent blank custom links from being inserted on the sidebar.
* Cleaned up code formatting.

= 1.3 =
* Added a custom link option (disabled by default).
* Updated screenshots 1-3.
* Changed plugin name on Appearance > Widgets menu to 'Custom Meta' instead of 'Custom Meta Widget' which was redundant.
* Added additional code commenting.

= 1.2 =
* Fixed reference to non-existent instance error.

= 1.1 =
* Minor improvements to code architecture.
* Increased compliance with WordPress style standards.
* Significantly more code commenting.

= 1.0 =
* First released version. 
* There may still be bugs, but I can't find any. 

== Upgrade Notice ==
= 1.5.1 =
Restores the "show custom link" functionality.

= 1.5.0 =
This update implements new functionality implemented after WordPress 4.4. If you are, amazingly, somehow, still using a version of WordPress older than 4.4, this may cause problems. (But if your install is *that* old, you already have problems :D)

= 1.4.8 =
Replaces deprecated function throwing debug error message

= 1.4.7 =
Purely cosmetic update for WordPress 4.4

= 1.4.6 =
Optional upgrade, new translations pl_PL (Polish), fr_FR (French), sr_RS (Serbian), and nl_NL (Dutch).

= 1.4.5 =
Optional upgrade, new translation sk_SK (Slovak).

= 1.4.4 =
Recommended upgrade, new languages and performance improvement.

= 1.4.3 =
Small correction to ru_RU (Russian) language translation files, recommended upgrade.

= 1.4.2 =
Russian language files and WordPress 3.5 compatibility update, not a critical upgrade.

= 1.4.1 =
Fixes some i18n issues and adds additional languages, recommended upgrade.

= 1.4.0 =
Improves code efficiency, new features, recommended upgrade.

= 1.3.6 =
Updated metadata, not an essential upgrade.

= 1.3.5_a =
Fixes one textdomain missed in the internationalization update and update for WordPress 3.3.2.

= 1.3.5 =
Adds translations for several languages.

= 1.3.4 =
Minor improvements to i18n for translators.

= 1.3.3 =
Adds internationalization, helpful for non-English users.

= 1.3.2 =
Adds some more error handling, small cosmetic tweaks, and shameless self-promotion.

= 1.3.1 =
Adds error handling if a custom link with no url/text is added.

= 1.3 =
Adds new custom link functionality.

= 1.2 =
Fixes a code error.

= 1.1 =
Not essential, no bug fixes just minor structural code changes. (Mostly a test of the SVN repository system which I am new at.)

== Translation Help ==
If you would like to help make this plugin available in your native language, please send your `.po` file to [plugins@shinraholdings.com](mailto:plugins@shinraholdings.com), and I will include it in the next version. The all the language files are in the `lang` subdirectory of the plugin.

* en_XX - [bitacre](mailto:lang@shinraholdings.com)
* da_DK - [Kris Thomsen](mailto:mail@kristhomsen.dk)
* ru_RU - VadDevArt
* de_DE - [Dr. Paolo Colombani](mailto:paolo.colombani@gmail.com)
* sk_SK - [Branco Radenovich](http://webhostinggeeks.com/user-reviews/)
* fr_FR - [Sylvain Taufour](mailto:sylvain.taufour@gmail.com)
* pl_PL - [Waldemar Jastalski](http://waldzias.p.ht/)
* nl_NL - [Niko Strijbol](mailto:strijbol.niko@gmail.com)
* sr_RS - [Diana S](http://wpdiscounts.com)

== Readme Generator ==
* This plugin's readme.txt file was generated by the [bitacre Readme Generator](http://shinraholdings.com/tools/readme-gen/) for WordPress Plugins.

== Support ==
* [Plugin Homepage](http://shinraholdings.com/plugins/custom-meta-widget/)
* [plugins@shinraholdings.com](mailto:plugins@shinraholdings.com)

== Donations ==
[Donations](http://shinraholdings.com/donate) are graciously accepted to support the continued development and maintenance of this and other plugins. We currently accept PayPal, link backs, and kind words. Also, checking the 'show plugin link' option on the widget helps us out greatly!
