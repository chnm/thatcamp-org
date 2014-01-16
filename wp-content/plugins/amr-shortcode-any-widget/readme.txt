=== amr shortcode any widget ===
Contributors: anmari
Tags: shortcode, widget, page, templates, page template
Tested up to: 3.8
Version: 1.6
Stable tag: trunk

== Description ==
This simple 'utility' plugin allows one to have any widget used in a page shortcode in any theme - no need to use the hybrid theme or create a special template. You could use the query posts widget in the page to create a archive within a page, or the rss widget to list feed content from other sites.  For more details see [anmari.com](http://webdesign.anmari.com/category/plugins/shortcode-any-widget/)

1. Test chosen widget works in normal sidebar first.  
2. Activate this plugin
3. Go to Appearance > widgets and find "shortcode" sidebar
4. Drag chosen widgets from configured sidebar to shortcodes sidebar. Save. 
5.  Go to page where you want configured widget to appear
6.  enter one of:
 [do_widget widgetname]   eg: [do_widget calendar]
 [do_widget "widget name"].   eg: [do_widget "tag cloud"]
 [do_widget id=widgetid] in a page or post
7. If the plugin cannot work out what you want, it will show a debug prompt.   Click on the link 'Try debug'.
It will produce a bunch of info. Look for the id of your widget in the shortcodes sidebar (you may have to scroll through a lot of debug nfo). Try with the id.   Sometimes the widget name that wordpress calls it internally is not the same as what you see on the screen and you will need the 'debug' to find the id.

Plugin has been tested with most standard widgets (rss feeds, tag cloud, pages, meta, search, and of course my own plugins widgets - upcoming events list, calendar and user lists.

If you use a widget more than once for different reasons, you may need to use the widget id to isolate which widget instance and it's settings to use.  ie: [do_widget id=categories-6] .  If you just use the name, it will display all widgets in the shortcode sidebar with that name (all instances). 

If you liked this plugin, you might also like my other plugins:
[icalevents.com](http://icalevents.com) - a ics compliant events plugin fully integrated with wordpress, so it will work with many other plugins (seo, maps, social)
[wpusersplugin.com](http://wpusersplugin.com) - a suite of plugins to help with membership sites. Major plugin is [amr users](http://wordpress.org/extend/plugins/amr-users/)


== Changelog ==
= Version 1.6 =
*  Add: added a settings page to help people out (not really settings)
*  Fix: changed a clashing function name

= Version 1.5 =
*  Fixed: a small bug which caused a warning if you had not saved the widgets in your shortcode sidebar

= Version 1.4 =
*  Updated readme - made very detailed steps and added some screen shots.
*  Tested on wp 3.3.1 and fixed some notices when bad parameters entered. 

= Version 1.3 =
*  Added debug link and retested. Added readme.

= 1.12=
*  Changed dummy shortcode sidebar so it appears after the theme sidebars to avoid taking over their widgets (this happened in numbered sidebars)  PLEASE note if you have upgraded,  you may appear to have "lost" your widgets due to this sidebar change.  You have not - they will be in your "inactive widgets" - please drag them to the new sidebar.  You may also find that you have 2 versions of the plugin, with slightly different folder names.  This will allow you to go back to the previous one to check what you had there if you need to.  Deactivate the old one and activate the new one.  Move your widgets back in.  Test then Delete the old one.    In one theme it also appeared that somehow due to this change, one of the sidebar widgets "moved" down to another sidebar.  I think that this may have had something to do with the fact that sidebars can be numbered or named, so please check all your sidebars before continuing.   As always make sure thath you know how to restore from a backup before doing any upgrades of any kind.
*  Tested in 2.9.2, can still use either the widget name "Categories" or id=categories-6.  Note widget must have been created in the dummy shortcode sidebar.

= 1.1 =

*  Allow for lowercase use of widget names for the those challenged by attention to detail!
*  Cleaned up debug mode to make it easier for people to identify which instance of a widget they are using.  Add &do_widget_debug to url string.

= 1.0 =
*  Launch of the plugin

== Installation ==

0. Activate plugin
1. Goto Appearance > widgets and find "shortcode" sidebar
1. Drag chosen widgets to shortcodes sidebar. Save. (note the names)
2. Add [do_widget id=widgetid] in a page or post
3. If it fails, click on the debug prompt and look for the id of your widget, use that.

Or  can use [do_widget widgetname] within the text in the page and save.  If the widget name has a space in it, use [do_widget "widget name"].

If you use a widget more than once for different shortcodes, you can use the widget id to isolate which widget instance (and of course associated settings to use).  ie: [do_widget id=categories-6]  
  

== Screenshots ==

1.  setting up widgets in page (must be in the shortcode sidebar)
2.  widgets in a page
3.  setting up widgets in the shortcode sidebar
4.  two rss feed widgets in shortcode sidebar - both will show if just name used
5.  Demonstration of two widgets being used via the do_widget short code.
6.  The Page or post with the do_widget shortcodes
7.  The shortcode sidebar.  The widget's user interface (UI) is used to provide a UI for the do_widget shortcode. 
8.  Debug prompt if one enters something like id=junk
9.  Debug messages - scroll down till you see the shortcodes sidebar - the widgets and their ids will be listed.  Use the id of the widet you want.
 