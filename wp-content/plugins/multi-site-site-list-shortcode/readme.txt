
=== Multi-Site Site List Shortcode ===
Contributors: Bit51, ChrisWiegman
Donate link: http://bit51.com/software/multi-site-site-list-shortcode/
Tags: Multisite, index, site list
Requires at least: 3.0
Tested up to: 3.6.1
Stable tag: 5.4.2


== License ==  
Released under the terms of the GNU General Public License. 

== Description ==

Adds a shortcode allowing multisite users to display a list of all the sites in the current installation. Sites can be sorted alphabetically or by site creation date and individual sites can be specified to be removed from the list.

== Long Description ==

Makes displaying a list of all the sites on a multi-site installation easy by using a [site-list] shortcode. User can also configure which sites (if any) to exclude from the list on the options page.

== Installation ==

1. Upload all files to the `/wp-content/plugins/multi-site-site-list-shortcode` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the [site-list] shortcode to and page or post where you would like a current list of sites on the wordpress installation
4. To remove sites simply check them on the options page.

= Translations =
*Lithuanian by <a href="http://www.host1free.com/">Vincent G</a>


== Changelog ==

= 5.4.2 =
* Remove pass by reference (should now work with PHP 5.4)
* Reformat for better WordPress code standards
* Stop using dynamic text domain
* Works with WordPress 3.6.1
* Updated .pot file

= 5.4.1 =
* Fixed Bit51 feed with improved error handling

= 5.4 =
* Added Hindi translation by Love Chandel of http://outshinesolutions.com/

= 5.3.2 =
* Whoops! Replaced the translation I accidentally overwrote with 5.3.1

= 5.3.1 =

* Updated Bit51 library
* Updated support forums and Changelog for WordPress.org

= 5.3 =

* Added Lithuanian translation by Vincent G of http://www.host1free.com/

= 5.2.1 =

* Fixed bug that prevented excluded sites from saving correctly.

= 5.2 =

* Minor refactoring
* Fixed PHP 5.2 bug
* Various typo and other corrections

= 5.1 =

* Minor bugfixes
* Added ability to use shortcode in text widgets
* Added ability to display site descriptions (taglines)
* Ability to override default options using attributes entered directly into the shortcode to make it easier to reuse
* Added ability to limit site count to a fixed number

= 5.0 =

* Uses the Bit51 plugin library for consistency across all my plugins
* No longer relies on deprecated "get_blog_list" method
* Cleaner code
* Automatically excludes blogs marked as spam, archived, or mature

= 4.0 =

* Now supported by Bit51.com

= 3.3 =

* Changed echo to return statement in list output

= 3.2 =

* Added option to open links in new window or same window

= 3.1 =

* Fixes an issue preventing links from working correctly

= 3.0 =

* Added option to sort either alphabetically or by site creation date

= 2.4 =

* Removed plugin maintainer request

= 2.3 =

* Updated WordPress compliance

= 2.2.3 =

* Updated Homepage

= 2.2.2 =

* Spelling correction

= 2.2.1 =

* Request for project maintainers.

= 2.2 =

* Cosmetic changes to the options page.

= 2.1 =

* Corrected Donation Form.

= 2.0 =

* Added options page to remove unwanted pages from the list.

= 1.1 =

* New plugin homepage

= 1.0 =

* First release