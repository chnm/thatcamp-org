=== Simply Exclude ===
Contributors: Paul Menard
Donate link: http://www.codehooligans.com
Tags: admin, posts, pages, categories, tags, Post Type, Taxonomy, exclude, include, is_front, is_archive, is_search, is_feed, is_author
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 2.0.6.1

== Description ==

Note to all used who tried the version 2.0 and 2.0.1 there were was bugs in the logic which upgrades the previous version of the Simply Exclude configuration to the version 2.0 format. If you lost your settings you can now reload them manually. Simply go into simply Exclude > Settings and at the very bottom is an option to reload the configuration again. 

Provides an interface to selectively exclude/include Post Types, Taxonomies and Authors 4 actions used by WordPress

is_front - When the user views the Front page. 
is_archive - When the user views an category or tags Archive.
is_search - When the user views a search result page.
is_feed - When a Feed is viewed/requested.

[Plugin Homepage](http://www.codehooligans.com/projects/wordpress/simply-exclude/ "SimplyExclude Plugin for WordPress")

== Installation ==

1. Upload the extracted plugin folder and contained files to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to you PostS/Pages/Category/Tags or any Post Type or Taxonomy sections. You will notice a new column allowing setting of the Taxonomy or Post type item in relation to selectively exclude/include the cat/tag/page for the given action is_front/is_archive/is_feed/is_search. 

== Frequently Asked Questions ==

= I've excluded all my categories and all tags why am I seeing my 404 page? =

Well you need to be careful when excluding both categories and tags. Since a post can be associated with both there is potential that you have excluded all your posts because they are either members of excluded categories or members or excluded tags. 

= I've excluded Pages but attachments (images) for those pages are showing up. Why? =

Only the parent Page itself is excluded from searches. By default WordPress does not yet include Pages in search. Make sure you have other search plugins correctly configured to not search attachments. 

= I've excluded a Page via the plugin but it still shows up in my sidebar when wp_list_pages is called. Why? =

At the time (version 1.6.1) the plugin only effects Pages included in the traditional Search feature on a site. It does not trap all selections of Pages via other internal WordPress functions...yet!


== Screenshots ==

1. Simply Exclude Admin interface showing Category exclusion options. 
2. Post listing showing new Simply Exclude options.
3. New Help section to answer some of your questions.

== Changelog == 

= 2.0.6.1 =
Corrected issue with reported PHP 5.4 related code.
Changes settings screen to split Taxonomies, Post Types and Users into tabbed screen. Better than one long screen.
Fixed issue with excluding categories on Home and Archive where it effects the pager. Note this is only for the default WordPress paging. Custom pagers may still have issue. 

= 2.0.6 =
2013-03-20
Bug fixes for Illegal string offset 'action' PHP Warnings.

= 2.0.5 =
2012-05-16
More tweaks to the widget exclude logic and cleanup logic on main query vs extra query.

= 2.0.4 =
2012-05-16
Added new Settings option to allow control of filtering the main page WPQuery only or all WPQuery requests. Added exclusion for common post_types. General bug fixed for reported issues where filter was either not occurring or that filter was causing a blank page or missing navigation. 

= 2.0.3 =
2012-03-18
Fixes to core filtering logic. 

= 2.0.2 = 
2012-03-05 
Fixed some issues when converting from the previous version of the Simply Exclude configurations. 

= 2.0.1 =
2012-03-04
Small bug. On the new Simply Exclude Help panel I user the jQuery UI Accordion package. Seems I failed to check this when setting the minimum WordPress version I was supporting (3.2). Seems jQuery UI Accordion is not available in core WordPress until version 3.3. So have added my own libraries to cover the older versions of WordPress. Sorry about that. And thanks to @biswajeet for bringing this up in the WordPress forums.

= 2.0 =
2012-03-03
Full rewrite of the plugin core code. Replace the growing list of panels and added functionality directly to the Post Type and Taxonomy panels. On both Post Types and Taxonomy panels you will see a new column allowing setting the exclude/include on Search, Archive, Feed and Home.

= 1.7.7 =
2010-05-12
Fixes: Mainly bug fixes and code cleanup. Most bugs discovered via using WP_DEBUG for uninitialized variables. 

= 1.7.6 =
2009-11-14 
Fixes: Issue with the Pages exclusion. Many users reporting a permissions issue. 
Additions: Added handler logic to interface with two other plugins. One of the often used Google XML Sitemaps. When setting Page or Category exclusions you now have the option to update the Google XML Sitemaps exclude pages and categories automatically. The other plugin is Search Unleashed. 

= 1.7.5 =
2009-07-15 Fixed some PHP warning by checking variable is set. Also added style for 2.8 interface. Very minor changes. 

= 1.7.2.1 =
2009-07-01 Fixed some PHP warning by checking variable is set. Also added style for 2.8 interface. Very minor changes. 

= 1.7.2 =
2009-02-05 Fixed some PHP warning by checking variable is set. Also added style to 2.7 interface. 

= 1.7.1 =
2008-07-16 Fixed an issue with WP 2.6 where it automatically decided to unserialize the option data structure. 

= 1.7 =
2008-05-29 Added Author to the Include/Exclude logic. Now you can exclude Author's Posts from Search, Home, RSS, Archive.

= 1.6 =
2008-05-22 Fixed various items. Added format display for Categories and Pages to reveal hierarchy, Disable plugin functions when searching in admin. This also corrected a display exclusion bug when showing categories and pages.

= 1.5 = 
20008-04-27 Fixed display issues. Changes 'List' to 'Archive'. Added tags inclusion/exclusion login. Works only with WP 2.3 and greater.

= 1.1 =
2008-12-15: Added logic to work with WP version greater than 2.2

= 1.0 =
2007-11-20: Initial release
