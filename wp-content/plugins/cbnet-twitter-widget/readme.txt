=== cbnet Twitter Widget ===
Contributors: chipbennett
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QP3N9HUSYJPK6
Tags: cbnet, twitter, twitter profile, twitter list, twitter favorites, twitter search, twitter tools, widget
Requires at least: 2.9
Tested up to: 3.3
Stable tag: 1.2

Widget to add the Twitter Tools Profile, List, Faves, and Search Widgets, with all configurable options.

== Description ==

This plugin is a widget to add the Twitter Tools Widgets, including all configurable options, with no need to copy/paste code. The following Twitter Widgets are supported:
* <a href="http://twitter.com/goodies/widget_profile">Profile Widget</a>
* <a href="http://twitter.com/goodies/widget_list">List Widget</a>
* <a href="http://twitter.com/goodies/widget_faves">Favorites Widget</a>
* <a href="http://twitter.com/goodies/widget_search">Search Widget</a>

== Installation ==

Manual installation:

1. Upload the `cbnet-twitter-widget` folder to the `/wp-content/plugins/` directory

Installation using "Add New Plugin"

1. From your Admin UI (Dashboard), use the menu to select Plugins -> Add New
2. Search for 'cbnet Twitter Widget'
3. Click the 'Install' button to open the plugin's repository listing
4. Click the 'Install' button

Activiation and Use

1. Activate the plugin through the 'Plugins' menu in WordPress
2. From your Admin UI (Dashboard), use the menu to select Appearance -> Widgets 
3. Find the widget titled 'cbnet Twitter Widget'
4. Add the widget to the widgetized sidebar of your choice
5. Configure settings, and save

== Frequently Asked Questions ==

= What is the Type Setting? =

The Type Setting determines what type of Twitter Widget will be displayed:

* **Profile**: Display tweets from the specified Twitter user
* **List**: Display tweets from the specified Twitter user's specified list
* **Favorites**: Display tweets favorited by the specified Twitter user
* **Search**: Display tweets that match the specified search query

= What are the General Settings? =

The following are the General Settings:

* **Title (Heading)**: Heading that will appear above the displayed widget
* **Twitter User ID**: User Id for the Twitter user profile you wish to display.
** This setting applies to Widget types "Profile", "List", and "Favorites"
** (Note: you can use *any* Twitter profile, not just your own)
* **Twitter List**: List defined by the Twitter user specified above.
** This setting applies to Widget type "List"
** (Note: you can display Lists from *any* Twitter user, not just your own)
* **Search Query**: Search query for which you want to display Tweets
** This setting applies to Widget type "Search"
* **Title**: Title that will appear at the top of the the displayed widget
** This setting applies to Widget types "List", "Favorites", and "Search"
* **Caption**: A short description
** This setting applies to Widget types "List", "Favorites", and "Search"

= What are the Advanced Settings - Appearance? =

* **Poll for New Results**: If enabled, updates the displayed tweets in real-time. If disabled, a static list of tweets is displayed.
* **Scrollbar**: If enabled, displays a scrollbar. If disabled, no scrollbar is displayed.
* **Load All Tweets**: Loads all tweets (up to the limit specified in the **Number of Tweets** setting), regardless of time interval.
* **Timed Interval**: Loads tweets within the interval specified in the **Interval** setting (up to the limit specified in the **Number of Tweets** setting).
* **Loop Results?**: If **Timed Interval** behavior is selected (I'm not really sure what this setting does, but it somehow loops the displayed tweets).
* **Interval**: If **Timed Interval** behavior is selected, defines the interval (in ms?) for which tweets are displayed.
* **Number of Tweets**: Defines the maximum number of tweets to display.
* **Show Avatars?**: If enabled, displays user avatars for displayed tweets. If disabled, does not display user avatars.
* **Show Timestamps?**: If enabled, displays timestamps for each displayed tweet. If disabled, does not display timestamps.
* **Show Hashtags?**: If enabled, displays hashtags (as links?) in displayed tweets. if disabled, does not display hashtags (as links?).

= What are the Advanced Settings - Colors? =

*(Note: enter all colors as HEX values, e.g. #ffffff for 'white')*

* **Shell Background**: Color of the container surrounding the displayed tweets
* **Shell Text**: Color of the text inside the container surrounding the displayed tweets (Title and Caption)
* **Tweet Background**: Background color of displayed tweets
* **Tweet Text**: Color of text of displayed tweets
* **Tweet Links**: Color of links in displayed tweets

= What are the Advanced Settings - Dimensions? =

* **Width**: Width (in pixels) of the widget
* **Height**: Height (in pixels) of the widget
* **Auto-Width**: Automatically sets widtth


== Screenshots ==

1. This is how the Widget appears on your web site.
2. The **General Settings** (Twitter User ID, List Name, Widget Title, and Widget Caption)
3. The **General Settings** correspond to the Twitter Tools List Widget configuration *Settings*
4. The **Advanced Settings - Preferences** (Poll for new results, Scrollbar, Load All Tweets or Timed Interval, # Tweets, Show Avatars/Timestamps/Hashtags)
5. The **Advanced Settings - Preferences** correspond to the Twitter Tools List Widget configuration *Preferences*
6. The **Advanced Settings - Appearance** (color settings)
7. The **Advanced Settings - Appearance** correspond to the Twitter Tools List Widget configuration *Appearance*
8. The **Advanced Settings - Dimensions** (width/height settings)
9. The **Advanced Settings - Dimensions** correspond to the Twitter Tools List Widget configuration *Dimensions*


== Changelog ==

= 1.1 =
* Added notifications for users of the old cbnet Twitter Profile/List/Faves/Search Display Plugins
= 1.0 =
* Initial Release
* Initial merge of the following plugins:
** <a href="http://wordpress.org/extend/plugins/cbnet-twitter-profile-display">cbnet Twitter Profile Display</a>
** <a href="http://wordpress.org/extend/plugins/cbnet-twitter-list-display">cbnet Twitter List Display</a>
** <a href="http://wordpress.org/extend/plugins/cbnet-twitter-faves-display">cbnet Twitter Faves Display</a>
** <a href="http://wordpress.org/extend/plugins/cbnet-twitter-search-display plugins">cbnet Twitter Search Display</a>


== Upgrade Notice ==

= 1.1 =
Added notifications for users of the old cbnet Twitter Profile/List/Faves/Search Display Plugins
= 1.0 =
Initial Release