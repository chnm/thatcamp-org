=== Multi-level Navigation Plugin ===
Contributors: ryanhellyer
Donate link: https://geek.hellyer.kiwi/donations/
Tags: dropdown, menu, flyout, navigation, suckerfish
Requires at least: 3.7
Tested up to: 4.2
Stable tag: 2.3.8

Adds a CSS based dropdown/flyout/slider menu to your WordPress blog.

== Description ==

This plugin is maintained with security patches, but is not actively developed. It's functionality has been replaced by the built-in menu system in WordPress core. I recommend you simply use a theme which utilzies the WordPress core menu system instead of this plugin.

Adds an SEO friendly, accessible dropdown/flyout/slider menu to your WordPress blog. Visit the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation Plugin page</a> for more information about the plugin.

Visit the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation Plugin page</a> for more information about the plugin.

= Features =

The plugin produces W3C valid XHTML and CSS. The options page allows you to choose from a range of options including:

* What content will be displayed in the menu<br />
* Animation speed (how fast the dropdowns appear)<br />
* Mouseover delay<br />
* Hide delay<br />
* Add a second menu<br />
* Hover sensitivity<br />
* Keyboard accessibility<br />

= Changing the look of your menu =

You can style the menu by copy and pasting the code from the <a href="https://geek.hellyer.kiwi/suckerfish_css/">Suckerfish CSS Generator page</a>.

The Multi-level Navigation Plugin can be implemented into any theme design.

Special thanks to <a href="http://transientmonkey.com/">malcalevak</a> who was a massive help with optomising the code for this plugin.


== Installation ==

Visit the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation Plugin page</a> for more information about the plugin.

After you've downloaded and extracted the files:

1.  Upload the complete `multi-level-navigation-plugin` folder to the `/wp-content/plugins/` directory<br />
2.  Activate the plugin through the 'Plugins' menu in WordPress<br />
3. Either: Add `&lt;?php if (function_exists('pixopoint_menu')) {pixopoint_menu();} ?&gt;` to your theme wherever you want the menu to appear (usually your header.php file)<br />
4.  Copy the CSS from the <a href="https://geek.hellyer.kiwi/suckerfish_css/">Multi-level menu CSS Generator page</a><br />
5.  Visit the "Multi-level Navigation" page in your WordPress options menu<br />

= Easy installation =
The easiest way to setup the PixoPoint Multi-level Navigation Plugin is to use it with a theme specifically designed to support the plugin.

If you don't take this route then read on for simple instructions on how to integrate it into other themes ...

= Support =
Sorry, but we no longer provide support for menu related issues.

== Frequently Asked Questions ==

= Why should I use this plugin? =

If you are having trouble making your posts easily accessible to your users without them having to rifle endlessly through pages of posts.

If you have been using a menu which requires Javascript to work, then this plugin will allow more of your visitors to access your site.

If you want to have an easy way to style your dropdown menu. This plugin is 100% compatible with the code generated on the <a href="https://geek.hellyer.kiwi/suckerfish_css/">Suckerfish Dropdown CSS Generator page</a>.

= Does it work for WordPress version x.x.x? =

We only provide support for the latest version of WordPress. We do not provide backward support.

== Screenshots ==

Visit the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation Plugin page</a> for more information about the plugin.

1. The <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation plugin</a> integrated with the <a href="https://geek.hellyer.kiwi/revolution-generated/">Revolution Generated theme</a>.
2. The administration page for the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation plugin</a>, where you can modify the plugins' settings.
3. The <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation plugin</a> integrated with the Aqua Vaccinium theme.

== Changelog ==

= 2.3.8 =
* Added deprecated notice
* Changed domain names
= 2.3.7 =
* Serbo-Croatian language added
= 2.3.6 =
* Switched ereg_replace() with preg_replace()
= 2.3.5 =
* No code changes
* Working around major problem which prevented the folders being uploaded via SVN
= 2.3.4 =
* Changed to uncompressed JS to meet WordPress.org guidelines
= 2.3.3 =
* Fixed image URL parsing bug
* Fixed CSS file loading setting bug
= 2.3.2 =
* Bug fixes
* Fixed relative URLs CSS bug
* Fixed exclude pages bug
* Fixed exclude categories bug
= 2.3.1 =
* Bug fix for custom code boxes
= 2.3 =
* Massive update
* Removed maintainmance mode
* Improved performance enormously
* Security hardening
= 2.2.3 =
* Update to plugin documentation
= 2.2.2 =
* Added support for Hebrew translation
= 2.2.1 =
* Security update - Added whitelisting of options
= 2.2 =
* Added Russian translation courtesy of http://fatcow.com/
* Confirmed support for WordPress 2.8.4
= 2.1.12 =
* Fixed category order bug
= 2.1.11 =
* Fixed haschildren class added in 2.1.11 (wasn't working as required)
* Confirmed support for WordPress version 2.8.4
= 2.1.10 =
* Added haschildren class to categories (single dropdown) for premium support member
* WP 2.8.2 support confirmed
= 2.1.9 =
* WP 2.8.2 support confirmed
= 2.1.8 =
* Improved Changelog in readme.txt file
* WP 2.8 support confirmed
= 2.1.7 =
* Adjusted IE conditional comments to target IE8 compatibility mode
= 2.1.6 =
* Polish translation added
= 2.1.5 =
* Corrected another error which prevented translation of a paragraph in admin_page.php
= 2.1.4 =
* Corrected an error which prevented translation of a paragraph in admin_page.php
= 2.1.3 =
* Removed support for 2.6.x and older versions of WordPress and confirmed support for WordPress 2.8 Beta 1
= 2.1.2 =
* Removed comment tags from HTML in suckerfish() function - hack for one of our premium support members<zzz>
= 2.1.1 =
* Fixed bug which prevented suckerfish_ie.js file from activating
= 2.1 =
* Added option to modify number of recent posts and comments
= 2.0.1 =
* Fixed bug with Gengko and added screenshots to plugin repository
= 2.0 =
* Added upgrade fix for hover delay time and added support for removing the title attribute from the menus<zzz>
= 1.9.8 =
* Added ability to control hover delay time (was fixed at 600 ms previously)
= 1.9.7 =
* Fixed hover delay bug
= 1.9.6 =
* Added option to show or not show empty categories
= 1.9.5 =
* Added bugstomp_home.php script to attempt to correct for upgrading bug and confirmed 2.7.1 support
= 1.9.4 =
* Added support for hide_empty=0 so that empty categories are still displayed and updated Spanish language files
= 1.9.3 =
* No reports on Alpha release so changes added to core
= 1.9.2 Alpha =
* Changed PHP generated javascript' header
= 1.9.1 Alpha =
* Changed stylesheets' header
= 1.9 =
* Added option to modify URL's for links in single dropdowns and added option to modify the page and category depths
= 1.8.1 =
* Added improved Suckerfish script - works with class instead of ID now
= 1.8 =
* Added localization support, including Spanish language
= 1.7.7 =
* Fixed Include/Exclude Pages and Categories feature
= 1.7.6 =
* Fixed another IE problem
= 1.7.5 =
* Correcting error in subversion upload
= 1.7.4 =
* Fixed IE problem
= 1.7.3 =
* Added Category order option to admin panel
= 1.7.2 =
* Fixed bug which prevented Links menu item from appearing
= 1.7.1 =
* Added PO file
= 1.7 =
* Added localization support (untested)
= 1.6.7 =
* Added version number as variable (so is consistent across the whole plugin - was often incorrect in some files in previous versions)
= 1.6.6 =
* Added pages_excludechildren() and pagesdropdown_excludechildren() functions - not yet fully implemented
= 1.6.5 =
* Added missing options from uninstall script (wasn't totally uninstalling)
= 1.6.4 =
* Added support for two more custom code boxes
= 1.6.3 =
* Bug fix, recent Posts dropdown was not working
= 1.6.2 =
* Bug fix, added background images back in.
= 1.6.1 =
* Critical update. Backwards compatibility bug fixed
= 1.6 =
* Official release. Replaces the 1.0.x line which is no longer supported
= 1.6 RC2 =
* Improved IE6 support
= 1.6 RC1 =
* No changes, simply changed label to RC1 ready for official release
= 1.5.17 Beta =
* Removed maintenance mode controls which stored information in database to avoid any potential security risks
= 1.5.16 Beta =
* Fixed test CSS bug
= 1.5.15 Beta =
* Added test CSS file, controllable when in maintenance mode. Useful for check if bugs are caused by poor CSS
= 1.5.14 Beta =
* New maintenance mode controls: Control inline CSS, disable CSS, Superfish speed and keyboard accessibility
= 1.5.13 Beta =
* File name changes. Simplified index.php file code
= 1.5.12 Beta =
* Added 'maintenance mode' which allows the menu to be accessed only when ?mln=on is added to the URL. Should be useful for testing purposes.
= 1.5.11 Beta =
* Added warning that keyboard navigation doesn't work. Removed shadow option as that should be controlled by the CSS, not by a script
= 1.5.10 Beta =
* Improved sensitivity option
= 1.5.9 Beta =
* Added mousein delay and sensitivity options
= 1.5.8 Beta =
* Added conditional to change HTML output depending on if theme or plugin CSS is used - don't want theme CSS overlaying the plugin CSS
= 1.5.7 Beta =
* Added FAQ section in admin panel
= 1.5.6 Beta =
* Added custom code tip, idea courtesy of eddieb
= 1.5.5 Beta =
* Add 'recommended plugins' section
= 1.5.4 Beta =
* Changed function calls to avoid the term 'suckerfish' which seems to confuse users who don't know what a suckerfish is
= 1.5.3 Beta =
* Added initial test code for drag and drop of menu items (not activated yet though)
= 1.5.2 Beta =
* Removed uninstall link in plugin page - security issues
= 1.5.1 Beta =
* Added uninstall link in plugin page
= 1.5.2 Beta =
* Added test widget system
= 1.5.1 Beta =
* Fixed IE menu bug
= 1.5.0 Beta =
* Tested, tested, tested and only bug found was that the delay script doesn't work on the second menu
= 1.4.14 Alpha =
* Modified suckerfish script to support second menu
= 1.4.13 Alpha =
* Moved clauses for the Superfish script into the settings file on advice from malcalevak
= 1.4.12 Alpha =
* Added improved keyboard accessibility script which uses Query. Script courtesy of malcalevak
= 1.4.11 Alpha =
* Removed need to tick enable animations box
= 1.4.10 Alpha =
* Fixed script issues so that plugin actually works.
= 1.4.9 Alpha =
* Added uninstaller
= 1.4.8 Alpha =
* Moved all CSS to an external file. Idea courtesy of malcalevak
= 1.4.7 Alpha =
* Improved the admin page menu
= 1.4.6 Alpha =
* Added support for hover delay
= 1.4.5 Alpha =
* Added more/better information on the admin page about how to receive support
= 1.4.4 Alpha =
* Stripped out admin page code from the index.php file and moved to includes
= 1.4.3 Alpha =
* Rearranged admin page content again  ... hopefully the last time!
= 1.4.2 Alpha =
* Rearranged admin page content again
= 1.4.1 Alpha =
* Rearranged admin page content
= 1.4 Alpha =
* Total rewrite of the admin page
= 1.3.6 Beta =
* Added option in admin panel to allow users to change between their own themes CSS and the plugin CSS (for themes designed for the Multi-level Navigation Plugin.
= 1.3.5 Beta =
* Added support for themes developed specifically for the Multi-level Navigation Plugin.
= 1.3.4 Beta =
* Major code rework.
= 1.3.3 Beta =
* Major rebuild by malcalevak.
= 1.3.2 Beta =
* Added support for changing wp-content/plugins in WP. Broke support for EP 2.5.x and older.
= 1.3.1 Beta =
* Added backward compatibility for newly renamed functions.
= 1.3 Beta =
* Redesigned admin interface.
= 1.2.7 Beta =
* Added include/exclude featured.
= 1.2.6 Beta =
* Readded Superfish script - removing it was a stupid idea.
= 1.2.5 Beta =
* Removed Suckerfish jQuery script.
= 1.2.4 Beta =
* Added Suckerfish jQuery script and removed Superfish jQuery script.
= 1.2.3 Beta =
* Changed labels for blogroll to 'links'.
= 1.2.2 Beta =
* Added non categories to blogroll.
= 1.2 Beta =
* Added categories to blogroll. No negative reports during testing, so now released as beta.<br Version 1.2.1 Beta: Added support for a new suckerfish jQuery script to replace the buggy and bloated Superfish one.
= 1.1.0 Alpha =
* Code improvements
= 1.0.8 =
* Minor bug fix. Final version in 1.0.x line before massive upgrade
= 1.0.7 =
* custom: Custom plugin for WebFadds.com; introduces categories to blogroll
= 1.0.6 =
* Refixed infernal IE Javascript bug AGAIN!!!!
= 1.0.5 =
* Refixed infernal IE Javascript bug
= 1.0.4 =
* Fixed W3C validation error
= 1.0.3 =
* Stupid WP repository packed a wobbly and wouldn't upload the images folder so resorted to releasing new version
= 1.0.2 =
* Removed random file from plugin and confirmed support for WordPress 2.6
= 1.0.1 =
* Corrected VERY MAJOR bug which prevented menu from functioning in IE6 - how did nobody notice this?
= 1.0 =
* Official release to WordPress plugins repository
= 0.99 RC =
* Final test version before stable release
= 0.98 Beta =
* Added inline CSS option
= 0.97 Beta =
* Corrected 'Custom 1' bug
= 0.96 Beta =
* Fixed the "There is a new version of ..." bug
= 0.95 Beta =
* Upgrade from 'Ryans Suckerfish Dropdown Menu'
= S1.6.6 =
* Minor bug fix. Superceded by the Multi-level Navigation Plugin.
= S1.6.4 =
* Minor bug fix.
= S1.6.3 =
* Major bug fix. 'Category' option was not working, now fixed
= S1.6.2 =
* Major bug fix. An error in the Javascript file was corrected.
= S1.6.1 =
* New stable release. Minor change: added monthly/yearly options for archives
= S1.6 Beta =
* Moved style information from HTML to a dynamically generated external CSS file
= S1.5 Beta =
* Added keyboard accessibility support.
= S1.4 Beta =
* Major code addition. Added selection boxes to admin panel to allow control over what appears in the dropdown menu directly via the admin panel.
= S1.3 Beta =
* Major recode. Replaced built in plugin style generator with the Suckerfish Dropdown Generator.
= S1.2 Beta =
* Added many new options
= S1.1.4 Beta =
* Re-included suckerfish2, suckerfish3, suckerfish4 and suckerfish5 functions from version 1.0.4
= S1.1.3 Beta =
* Major bugfix due to incorrect function names
= S1.1.2 Beta =
* Removed menu from wp_footer
= S1.1.1 Beta =
* Corrected folder name
= S1.1 Beta =
* Initial Beta version featuring user customisable design
= S1.05 =
* Bug fixed thanks to Cj B
= Version S1.04 =
* Added one new function to enable users to display a different type of content in the navigation bar
= S1.03 =
* Added four new functions to enable users to display a range of different types of content in the navigation bar
= S1.02 =
* Added Single function to improve usability
= S1.01 =
* Improved CSS styling
= S1.0 =
* Initial plugin design

Visit the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation Plugin page</a> for more information about the plugin.

== Credits ==

Thanks to the following (in no particular order) for help with the development of this plugin:<br />

* <a href="http://www.webhostinghub.com/">WebHosting hub</a> - Provided the Serbo-Croation translation files<br />
* <a href="http://www.fatcow.com/">Fat Cow</a> - Provided the Russian translation files<br />
* <a href="http://www.samburdge.co.uk/author/admin/">Sam Burdge</a> - Wrote article explaining how to remove title attributes<br />
* <a href="http://www.designfederation.net/">Klaus Kinski</a> - Found Sam Burdge's article about title attributes</br />
* <a href="http://www.simplemachines.org/community/index.php?action=profile;u=6160">metallica48423</a> - Pointed out mouseover delay was WAY too long</br />
* <a href="http://www.mindf.org/">haggis</a> - haschildren code improvement</br />
* <a href="http://www.mattermedia.com/">EddieB</a> - Tips for documentation<br />
* <a href="http://cubegames.net/">Ryan McCue</a> - Help with SVN update<br />
* <a href="http://www.vcsvu.nl/">vcsvu</a> - Code contribution (improvements to exclude pages feature)<br />
* <a href="http://nv1962.net/">nv1962</a> - Bug reporting<br />
* <a href="http://test.gruenejugend-sh.de/">fabs</a> - Bug reporting<br />
* <a href="http://www.mattermedia.com/">Eddie</a> - Gave tip on how to make custom code option more understandable for non-coders<br />
* <a href="http://www.wp-fun.co.uk/">Andrew Rickman</a> - Helped integrate a drag and drop widget style interface for testing purposes<br />
* <a href="http://wpengineer.com/how-to-improve-wordpress-plugins/">Frank B�ltge</a> - Wrote a article explaining how to add action links to the plugin page<br />
* <a href="http://www.santosj.name/general/wordpress-27-plugin-uninstall-methods/">Jacob Santos</a> - Wrote an article about the uninstallation of WordPress plugins<br />
* <a href="http://blog.peter-ryan.co.uk/">Peter Ryan</a> - created the suckerfish hover delay script<br />
* <a href="http://www.barelyfitz.com/projects/tabber/">Patrick Fitzgerald</a> - creation of the tabber.js script used<br />
* <a href="http://be.twixt.us/jquery/suckerFish.php">Myles Angell</a> - creation of the suckerfish jQuery script used during development<br />
* <a href="http://transientmonkey.com/">malcalevak</a> - Massive help with coding<br />
* Ande - Reported W3C validation bug<br />
* Kevin M. Russell - helped with the style.php bug<br />
* <a href="http://access-bydesign.com/">Clive Loseby</a> - discovered strict doctype validation error<br />
* <a href="http://www.fether.net/">Paula</a> - Feature suggestions and donation<br />
* <a href="http://www.marucchi.com/">Karim  A. Marucchi</a> - Bug reporting, feature suggestions and donations</br />
* <a href="http://www.acooldryplacephotography.com/">Mark Gooding</a> - created a new version featuring a custom menu option. Although this menu system hasn't been used directly in this plugin, an almost identical system has since been installed based on the concept that Mark created.<br />
* <a href="http://www.veterinaryparasite.com/">BigAlReturns</a> - helped programming the pages and categories exclusions<br />
* <a href="http://wordpressgarage.com/">Miriam Schwab</a> - brought lack of WP dropdown plugins to my attention<br />
* <a href="http://wpcandy.com/author/admin/">Michael Castilla</a><br />
* CjB - Bug reporting<br />
* <a href="http://www.clearvisionpartners.com/">Troy</a> - Beta testing feedback<br />
* <a href="http://www.michaelmitchell.co.nz/">Michael Mitchell</a> - programming assistance<br />
* <a href="http://www.carroll.org.uk/">Matthew Carroll</a> - designed code for accessible keyboard dropdown<br />
* <a href="http://chrislaing.net/">Chris Laing</a> - Beta feedback<br />
* <a href="http://jquery.com/">John Resig</a> - jQuery Javascript plugin<br />
* <a href="http://users.tpg.com.au/j_birch/plugins/superfish/">Joel Birch</a> - jQuery menu widget<br />
* <a href="http://brandonaaron.net">Brandon Aaron</a> - jQuery bgiframe plugin<br />
* <a href="http://cherne.net/brian/resources/jquery.hoverIntent.html">Brian Cherne</a> - jQuery Hover Intent plugin<br />
* <a href="http://abundantharvest.biz/">Karynn</a> - Bug reporting</br />
* <a href="http://abundantharvest.biz/">Karynn</a> - Bug reporting</br />
* <a href="http://www.tcbarrett.com/">Tom Barrett</a> - Assistance with regex function</br />
* <a href="http://code.johnpbloch.com/">John Bloch</a> - Assistance with regex function</br />

Visit the <a href="https://geek.hellyer.kiwi/multi-level-navigation/">Multi-level Navigation Plugin page</a> for more information about the plugin.
