=== AVH Extended Categories Widgets ===
Contributors: petervanderdoes
Donate link: http://blog.avirtualhome.com/wordpress-plugins/
Tags: extended, categories, widget, top categories
Requires at least: 2.3
Tested up to: 3.8
Stable tag: 3.9.3

The AVH Extended Categories Widgets gives you three widgets for displaying categories.
== Description ==

The AVH Extended Categories Widgets gives you three widgets for displaying categories.

1. Replacement of the default category widget to allow for greater customization.

1. A top categories widget. Shows the top X categories. This requires WordPress 2.8 or higher.

1. A Category Group widget. Shows categories from a group which is associated with the post/page that the visitor is looking at. Categories can be added to a group. Multiple groups can be made and every post/page can be associated with a different group. This requires WordPress 2.8 or higher.

The replacement widget gives you the following customizable options:

* Title of the widget.
* Display as List or Drop-down.
* Show number of posts (Count) after the category.
* Hide empty categories.
* Show categories hierarchical.
* Show categories up to a certain depth. (Requires WordPress 2.8 or higher).
* Sort by ID, Name, Count, Slug or manual order (Requires WordPress 3.3 or higher).
* Sort ascending or descending.
* Show RSS link after the category as text or image.
* Select which categories to show. (Requires WordPress 2.5.1 or higher).

The Top Categories widget gives you the following customizable options:

* Title of the widget.
* How many categories to show.
* Display as List or Drop-down.
* Show number of posts (Count) after the category.
* Sort by ID, Name, Count, Slug.
* Sort ascending or descending.
* Show RSS link after the category as text or image.
* Select which categories to show. (Requires WordPress 2.5.1 or higher).

The Category Group widget gives you the following customizable options:

* Title of the widget. Either per widget or per group.
* Display as List or Drop-down.
* Show number of posts (Count) after the category.
* Hide empty categories.
* Sort by ID, Name, Count, Slug.
* Sort ascending or descending.
* Show RSS link after the category as text or image.

You can set the following options for the Category Group Widget:

* Which group to show on the 'special' pages. The 'special' pages are: Home, Category archive, Tag archive, Daily/Monthly/Yearly archive, Author archive, Search results page.
* Which group to show when no group is associated with a post. Useful for older posts that don't have the association.
* Set the default group when editing or creating a post.

You can also select not to show the Category Group widget by selecting the group: None

Translations:

* Czech - Čeština (cs_CZ)
* Dutch - Nederlands (nl_NL)
* German - Deutsch (de_DE)
* Greek (el)
* French - Français (fr_FR)
* Indonesian - Bahasa Indonesia (id_ID)
* Italian - Italiano (it_IT)
* Russian — Русский (ru_RU)
* Spanish - Español (es_ES)
* Swedish - Svenska (sv_SE)
* Turkish - Türkçe (tr)

Some of the translations are incomplete. You can help translate all of the AVH Software by registering at our [translation site](http://translate.avirtualhome.com).

= Get involved =

Bugs and feature requests can be submitted at the [AVH Extended Categories GitHub Repository](https://github.com/petervanderdoes/AVH-Extended-Categories/blob/develop/Contributing.md).
Developers can checkout and contribute to the source code on the [AVH Extended Categories GitHub Repository](https://github.com/petervanderdoes/AVH-Extended-Categories/blob/develop/Contributing.md).

== Installation ==

The AVH Extended Categories Widgets can be installed in 3 easy steps:

1. Unzip the extended-categories-widget archive and put the directory "extended-categories-widget" into your "plug-ins" folder (wp-content/plugins/).

1. Activate the plug-in

1. Go to the Presentation/Appearance->Widgets page and drag the widget into the sidebar of your choice. Configuration of the widget is done like all other widgets.

== Support ==

= What about support? =
Support is done through WordPress.org

= Depth selection =
Starting with version 2.0 and WordPress 2.8 you can select how many levels deep you want to show your categories. This option only works when you select Show Hierarchy as well.

Here is how it works: Say you have 5 top level categories and each top level has a number of children. You could manually select all the Top Level categories you want to show but now you can do the following:
You select to display all categories, select to Show hierarchy and select how many levels you want to show, in this case Toplevel only.

= I want to help and translate the plug-in =
You can help translate all of the AVH Software by registering at our [translation site](http://translate.avirtualhome.com).

= Multiple Category Groups =
The following is an explanation how assigning multiple groups to page/post works.

Lets say you have the following groups:
Free Time
Theater
Movie
Music

Setup several Category Group widgets and associated each widget with one or more groups.
Widget 1 has association with Free Time
Widget 2 has association with Theater, Movie and Music
Widget 3 has association with Theater, Movie and Music

Page has associations the groups Free Time and Theater
* Widget 1: Shows categories of the Free Time group
* Widget 2: Shows categories of the Theater group.
* Widget 3: Not displayed

Page has associations the group Movie.
* Widget 1: Not displayed
* Widget 2: Shows categories of the Movie group.
* Widget 3: Not displayed


Page has associations the groups Free Time, Movie and Music
* Widget 1: Shows categories of the Free Time group
* Widget 2: Shows categories of the Movie or Music group.
* Widget 3: Shows categories of the Music or Movie group.
Whether Widget 2 shows Movie or Music depends on the creation order of groups. If Widget 2 shows Movie, Widget 3 will show Music but if Widget 2 shows Music, Widget 3 will show Movie.

== Screen shots ==
None

== Changelog ==
= Version 3.9.3 =
* Log errors as E_USER_NOTICE when creating category groups.

= Version 3.9.1 =
* When the category group home is deleted there are MySQL errors.

= Version 3.9.0 =
* Add CSS classes to output when on a single post.
* Added language: Serbian - Српски (sr) thanks to  Borisa Djuraskovic - http://www.webhostinghub.com
* Update language: Spanish - Español (es) thanks to Andrew Kurtis - http://www.webhostinghub.com

= Version 3.8.5 =
* Bugfix: Options of the group widgets are not obeyed.

= Version 3.8.4 =
* Bugfix: Options of the widgets are not obeyed.

= Version 3.8.3 =
* Bugfix: Widget would not keep checked categories
* Bugfix: Several PHP warnings and notices are shown.

= Version 3.8.2 =
* WordPress plugin page is no longer available.

= Version 3.8.1 =
* Category list doesn't show categories

= Version 3.8.0 =
* Add support for WordPress 3.6
* Dropped support for PHP4, from this version on it PHP 5 or higher.
* Fixed PHP Strict errors
* Fixed undefined variables errors.

= Version 3.7.1 =
* When updating a category a warning is can be shown.

= Version 3.7.0 =
* Fix uninstall

= Version 3.6.7 =
*  Fix error in SQL syntax

= Version 3.6.6 =
* Missed a translatable string.
* Add French translation.
* Fix for WordPress 3.5

= Version 3.6.5 =
* Bugfix: Can not delete category groups

= Version 3.6.4 =
* Bugfix: Problem with categories not showing up when showing dropdown categories.

= Version 3.6.3 =
* Bugfix: Problem with url's on Windows platform.

= Version 3.6.2 =
* Bugfix: Another problem related to WordPress prior 3.3

= Version 3.6.1 =
* Bugfix: Problem with a flat display of categories.
* Bugfix: Problem loading the plugin on WordPress 3.2.x

= Version 3.6 =
* Adds Manual Order option. No need for 3rd party plugin anymore. This only works in WordPress 3.3.x
* Speed up the creation of the categories checklist in the widgets.

= Version 3.5.1 =
* Bugfix: The columns in the admin section don't save, making certain columns disappear.

= Version 3.5 =
* RFC: Adds the ability for the widgets to sort the categories as set with plugin My Category Order. This plugin allows to order categories manually.

= Version 3.4.2 =
* Bugfix: Fails to display the Category Group Widget when the widget is to display any group.

= Version 3.4.1 =
* Bugfix: Problems with multiple category group widgets.
* Bugfix: The category group All sometimes does not contain all categories.

= Version 3.4 =
* RFC: Category Groups can be associated with categories. This enables the plugin to display the Category Group Widget for that category group on the category archive page.
* Bugfix: In combination with WP Supercache an error can occur.
* Bugfix: An error occurs if there are no categories present in WordPress.

= Version 3.3.5 =
* Bugfix: Category Groups would be created every time you saved a post.

= Version 3.3.4 =
* Bugfix: Problem with initializing the plugin.

= Version 3.3.3 =
* RFC: Changed selecting Category groups in posts from tag-like to checkboxes.
* Removed the Menu item Category groups under Posts and Pages.

= Version 3.3.2 =
* Added several new localizations.
* Bugfix: When using multiple Category Group widgets, all of them would show up on the special pages instead of just the one selected in the options.
* Bugfix: Localization didn't work.

= Version 3.3.1 =
* Bugfix: A PHP warning would show up when using multiple Category Group widgets.

= Version 3.3 =
* Ability to assign multiple Category Groups to a post/page.
* A Category Group can be assigned to 'special' pages. The 'special' pages are: Home, Category archive, Tag archive, Daily/Monthly/Yearly archive, Author archive, Search results page.
* Bugfix: When using SSL in the admin section save would redirect to non-SSL and not saving the options.
* Bugfix: Hierarchy in the dropdown with selected categories didn't work properly.
* Bugfix: The widget for the Groups was a different setup as the others breaking certain theme layouts.

= Version 3.2.2 =
* Bugfix: Problem with Chrome and saving the category group.
* Bugfix: Extra metabox displayed. The plugin uses it's own metabox for Category Group selection.

= Version 3.2.1 =
* Speed improvements in the admin section when there are a lot of categories and several Categories widgets.

= Version 3.2.0.1 =
* Bugfix: Forgot to add a directory into SVN.

= Version 3.2 =
* Compatibility issues with upcoming WordPress 3 resolved.
* Bugfix: Description of the groups didn't save.
* RFC: All widgets - Option to sort the categories by slug.
* RFC: Category Group widget - Ability to set the widget title per group.

= Version 3.1 =
* Wrap the group widget in a div with id = name of group. This enables CSS modification based on the group.
* Hierarchical now works with the option "Select Categories" as well.
* Bugfix: If the normal widget and group widget are displayed on the same page as dropdown, the selected option could be the wrong one.
* Bugfix: In a RTL-based theme the admin menu would flip back to the left, instead of staying right.
* Bugfix: Hierarchical works in the Category Group widget.
* Bugfix: Change div tags in widget from ID to class to comply to W3 validation.

= Version 3.0.2 =
* Bugfix: The plugin conflicts with the standard theme/plugin editor in WordPress

= Version 3.0.1 =
* Bugfix: Definition of the metabox for post/page in the wrong place

= Version 3.0 =
* Renamed the plug-in to AVH Extended Categories Widgets
* Added new widget: Category Group. You can create groups with categories and associate the group with a post. The widget will show the categories from the group.
* Added translation: Italian - Italiano (it_IT)
* Added css file for widgets. Using the CSS you can change the layout of the text.
* Redid settings. Settings are now a separate menu.
* Reduced memory foot print.
* Increased speed.
* Development improvements.
* Added uninstall capability.
* Bugfix: The hierarchy check mark in the widgets would disappear.
* Bugfix: In drop down list the hierarchy would appear despite the fact that "Show selected categories only" was selected.
* Note: Some of the translations are not yet complete with version 3.0.

= Version 2.3.3 =
* Bugfix: Validation error on drop down categories.

= Version 2.3.2 =
* Bugfix: Saving the option didn't work
* Added translation: Spanish - Español (es_ES)

= Version 2.3.1 =
* Bugfix: Undefined function

= Version 2.3 =
* RFC: You can change the text Select Category, without editing any translation file.
* Bugfix: Selecting one parent wouldn't show it's children.
* You can now display only selected categories. This didn't work properly in an hierachical category structure.
* Added Czech translation.

= Version 2.2 =
* RFC: Internationalization of the plug-in.
* Bugfix: W3 Validation Errors when using drop down categories.
* Added option to exclude categories from displaying instead of including them. (Compliments to: Jose Luis Moya - http://www.alsur.es )

= Version 2.1 =
* RFC: The path for the RSS image can be URI.

= Version 2.0.3 =
* Bugfix: When selecting hierarchy and showing of all levels, the hierarchy wouldn't be shown.

= Version 2.0.2 =
* Top categories widget caused error in PHP4.

= Version 2.0.1 =
* Reported problem with calling a class by self ()

= Version 2.0 =
* Updated for WordPress 2.8. Unlimited amount of Extended Categories widgets is now possible.
* In WordPress 2.8 you have the options to select depth when showing hierarchy. See FAQ for more information.
* With WordPress 2.8 there is a new widget, AVH Extended Categories Top. This will show the top categories based on amount of posts.

= Version 1.5.1 =
* Bugfix: Compatibility issue with the plug-in wp-security-scan

= Version 1.5 =
* RFC: Option to show RSS feed after categories

= Version 1.4.1 =
* Bugfix: Problem when using multiple widgets with the drop down option.

= Version 1.4 =
* Ability to have up to 9 widgets.

= Version 1.3 =
* You can select which categories to show (Requires WordPress 2.5.1 or higher).

= Version 1.2 =
* When no category or an empty category is selected the dropdown menu shows Select Category, like the default category widget.

= Version 1.1 =
* Drop down menu didn't work. Page wasn't refreshed with selected category.

= Version 1.0 =
* Initial version
