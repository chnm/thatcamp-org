=== amr shortcode any widget ===
Contributors: anmari
Tags: shortcode, widget, page, templates, page template, widget_area, sidebar
Tested up to: 3.8.1
Version: 2.2
Stable tag: trunk

== Description ==
Insert separate widgets or a entire widget area (sidebar) into a page using a shortcode. 

The Widgets settings are specified in a specially created "widgets for shortcode" sidebar, just like you normally setuop a widget.

The Plugin will call that widget instance from the do_widget shortcode, or the chosen sidebar from the do_widget_area shortcode. 

For example: You could use the query posts widget in the page to create a archive within a page, or the rss widget to list feed content from other sites.  For more details see [anmari.com](http://webdesign.anmari.com/category/plugins/shortcode-any-widget/)

The plugins default action is that it will magically find the settings for your themes first sidebar and use them to control the widget's output.  In most cases this would mean that the widget will be styled as per the rest of your theme.  

If that does not look good, parameters exist to override this behaviour.

Change your theme? No problem, the plugin will save and restore the widgets_for_shortcode settings.  On display it will then pick up the new themes sidebar settings.  Check this still looks nice please! in some themes you may ned up with white text on a white background and will then have to override or change css.

**Instructions:**

1. Test your chosen widget works in a normal sidebar or widget area first. 
2. Then Activate this plugin
3. Go to Appearance > widgets and find the " widgets for shortcode" sidebar or widget area
4. Drag your chosen widgets from to the shortcodes sidebar. Save. 
5. Go the shortcode any widget settings.  Click on one of the create page links to help you setup the shortcode.
6. OR go to an existing page and enter a shortcode:

 [do_widget widgetname]   eg: [do_widget calendar]
 [do_widget "widget name"].   eg: [do_widget "tag cloud"]
 [do_widget id=widgetid] 
 
 [do_widget_area]  (will use the  "widgets in shortcodes" widget area / sidebar
 [do_widget_area sidebarname]  for another sidebar or widget area - eg: to maximise likelihood of getting your theme's widget css to apply.  
 

 
7. If the plugin cannot work out what you want and you are logged in as an administrator, it will show a debug prompt to you, the logged in admin only.   
Click on the link 'Try debug'.  It will produce a bunch of info. Look for the id of your widget in the shortcodes sidebar (you may have to scroll through a lot of debug info). Try using the widget id.   Sometimes the widget name that wordpress calls it internally is not the same as what you see on the screen and you will need the 'debug' to find the id.

**[do_widget ...] Parameters:**

*  *nameofwidget* or name="*nameofwidget*"  This is NOT the title of the widget.  THis is the name that you see in the widgets menu page even when unassigned to a sidebar.  For existing users , you can also use just the name of the widget without name=.  It must bethe first parameter then.
*  id=*wordpress assigned id of widget*
*  title=false to hide the widgets title OR to override your themes first sidebar html settings for widget titles, use title=*htmltag* where *htmltag* is one of h1,h2,h3,h4,h5,header,strong,em.  If not entered plugin will use the same title html that your themes first sidebar uses - yes even if you change themes, it will switch to the new themes html.
*  wrap=*htmltag* where *htmltag* is one of div,p,main,aside,section.  This will override your themes first sidebar html settings for widget wrapping html.
*  widget_classes=none  This will remove the class "widget" from the wrapping html and the class "widget_title" from the title html.  This may be enough to remove any unwanted css styling from your theme.
*  class=*yourclassname*  By default the plugin will add a class of amr-widget tto the wrapping html.  You can use this to add any special css.  Add css either by editing your themes stylesheet if it is a custom theme, or using something like the wordpress custom css plugin.
*  sidebar="*sidebarname*"  will default to widgets_for_shortcode sidebar.  Only use if you want to re-use a widget already beingused in another sidebar.  THis is theme dependent and WILL break if you change themes.

**[do_widget_area ...] Parameters:**

*  widget_area=*yourwidgetarea*  defaults to 'widgets_for_shortcodes' if nothing entered
*  widget_area_class=none    /* option to remove theme styling by removing the widget_area class from the sidebar html */
*  widget_classes=none     /* option to remove the widget class from the widget wrappinghtml
*  class=*yourclassname*   default is amr_widget_area'


See the settings page for links to help your create the shortcodes in a page.

To 'remove debug mode' 
remove ?do_widget_debug=1 from the url you are looking at (NOTE it only debugs if you are logged in and an administrator, so this is really NOT a problem.)

The plugin has been tested with most standard widgets (rss feeds, tag cloud, pages, meta, search, and of course my own plugins widgets - upcoming events list, calendar and user lists.

If you use a widget more than once for different reasons, you may need to use the widget id to isolate which widget instance and it's settings to use.  ie: [do_widget id=categories-6] .  If you just use the name, it will display all widgets in the shortcode sidebar with that name (all instances). 

If you liked this plugin, you might also like my other plugins:
[icalevents.com](http://icalevents.com) - a ics compliant events plugin fully integrated with wordpress, so it will work with many other plugins (seo, maps, social)
[wpusersplugin.com](http://wpusersplugin.com) - a suite of plugins to help with membership sites. Major plugin is [amr users](http://wordpress.org/extend/plugins/amr-users/)



== Changelog ==
= Version 2.2 =
*  Attempt to add more styling control.  
*  Add widget_classes=none to reverse out widget type class.  NOte to totally override wordpress widget 
*  Add widget_area_class=none

= Version 2.1 =
* Bug Fix - last sidebar registered by theme was being overwritten by the attempt to copy the themes sidebar arguments so that cleverly (hopefully) the shortcode widgets would pick up the same styling.  Fixed now.  Now it really does pickup the first sidebars styling - with devasting consquence in twenty-fourteen theme - yes white text on white background is not fun to read.  But on other themes it works a treat.


= Version 2 =
* Shortcode widget id changed so that wordpress will save the shortcode settings per theme.  
* Added Code to auto upgrade, but just in case please check your widgets page.  Look at the inactive widgets if the widgets are not in your widgets for shortcodes sidebar.
* Logic added on theme switching to save shortcode widget settings and restore them after the theme switch, so you can play with themes and not have to set up your widgets again.  WP kept putting them into an 'inactive sidebar'.
* Added, as requested by a few of you, the ability to add a class and control the html around the widget without having to enter html around the shortcode has been added.  See the settings page for instructions.
* Added the ability to specify what html should be used for the title and the widget wrap, so that you can more easily either match your theme or get away from your thesmes widget styling.
* Added ability to insert a whole widget area into the page.  The default will be the "widgets for shortcode" sidebar/widget area.  You can specify others.  Note Specifying other widget areas may be theme dependent, ie: switch and you'll lose that page.
* Added classes amr-widget and amr-widget-area so one can target the widgets in the page to adjust css.

= Version 1.8 =
*  Whoops - had renamed the main file and forgot to delete it from the svn. Forcing a version number change to ensure files get cleaned up for everyone

= Version 1.7 =
*  Change: Changed so that debugs and debug prompt will only show to a logged in administrator.

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

= 1.12 =
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
2. Add [do_widget widgetname] in a page or post  or [do_widget_area]
3. If it fails, click on the debug prompt and look for the id of your widget, use that.

Or  can use [do_widget widgetname] within the text in the page and save.  If the widget name has a space in it, use [do_widget "widget name"].

If you use a widget more than once for different shortcodes, you can use the widget id to isolate which widget instance (and of course associated settings to use).  ie: [do_widget id=categories-6]  
 
do_widget parameters:

* title=false to hide a title
* title= one of h1,h2,h3,h4,h5,strong,em
* class=yourclassname  (else it will add amr_widget)
* wrap=one of div, section, p, aside
* widget_classes=none  (will take the widget class out and the widget-title class out)

do_widget_area parameters:

* widget_classes=none  (will take the 'widget' class out and the widget-title class out)
* widget_area_class=none (will take out the 'widget_area' class )

**Styling**

Check your styling.  The effects are very dependent on how your theme has specified the css that may apply to widgets and sidebars/widget areas.  It may work beautifully and have the content looking like it belongs with your theme.
You may have undesired effects applying that do not work in the main content area.
You may have desired effects not applying because the css is specific to a themes sidebar.

Via the plugin you can do the following to affect styling:
* Remove the general wordpress 'widget' class from the widgets wrapping html and the 'widget_title' from the title html.  ([do_widget *widgetname* widget_classes=none] 
* Hardcode away from your themes html for widgets and widget title.  EG: if your theme uses aside and h2, you could specify: [do_widget *widgetname* widget_classes=none wrap=div title=h3].  By default the plugin will use whatever your first sidebar uses.  This will change if you change themes, but only if you have NOT overridden the html with wrap and title.
* Apply an existing class in your theme.  Use [do_widget *widgetname* class=*yourclass*].
* Use the addition class 'amr-widget' provided to specify alternate css in your themes stylesheet or with something like wordpress custom css.

 
 

== Screenshots ==

1.  setting up widgets in the widgets for shortcode sidebar. The widget's user interface (UI) is used to provide a UI for the do_widget shortcode.
2.  add shortcode for widgets in a page (must be in the widgets for shortcode sidebar)
3.  widgets in a page 
4.  with the default widget class and with widget_classes=none
two rss feed widgets in shortcode sidebar - both will show if just name used
5.  Debug messages - scroll down till you see the shortcodes sidebar - the widgets and their ids will be listed.  Use the id of the widget you want.
6.  Examples of a page with several do_widgets: query_posts and tag cloud 
7.  Example of rss widgets in a custom theme


 