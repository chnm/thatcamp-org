=== Multisite Global Search ===
Contributors: aliciagh
Tags: search, multisite, buddypress, widget, multilingual, global, shortcode
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.2.9

Adds the ability to search through blogs into your WordPress Multisite installation.

== Description ==

Easily search through all blogs into your WordPress Multisite by post title, post content or post author.
Multisite Global Search doesn't work with single WordPress installation and it must be activated for all sites using "network activate" in the Administration Panel.
Currently in the following languages:

* English
* German (de_DE) by Dennis Vorpahl
* French (fr_FR) by Fabrice Perrinel
* Norwegian Bokmal (nb_NO) by Tore Johnny Bråtveit
* Portuguese (pt_PT) by Jose Anjos
* Russian (ru_RU) by Sam Savinov
* Romanian (ro_RO) by Alexander Ovsov [Web Geek Science](http://webhostinggeeks.com/science/)
* Slovak (sk_SK) by Branco [WebHostingGeeks.com](http://webhostinggeeks.com/blog/)
* Spanish (es_ES)

If you have created your own language pack, or have an update of an existing one, you can send [gettext .po and .mo files](http://codex.wordpress.org/Translating_WordPress) to me so that I can bundle it into Multisite Global Search.

** Features **

* Multisite Global Search Widget. Show a search form in your sidebar.
* Search across all network blogs or only in your blogs if you are logged.
* Search on pages.
* Administrative choice to perform searches by default on pages.
* Administrative choice to disable search options.
* Search results are showed in a page which contents the shortcode: `[multisite_search_result]`
* Show excerpted results `[multisite_search_result excerpt="yes"]`
* Entries on every site across your installation appear in search results immediately after publication.
* Receive results from your complete blog network, even sites you do not own or control.
* Customizable style sheet for widget and results page.
* Two different form types, vertical and horizontal.
* Put search form into your code with `Multisite_Global_Search::ms_global_search_vertical_form(your_results_page)` or `Multisite_Global_Search::ms_global_search_horizontal_form(your_results_page)`
* Insert search form in templates using the shortcode: `[multisite_search_form]`. [See the plugin page for more information](http://grial.usal.es/agora/pfcgrial/multisite-search).

== Installation ==

**Requeriments**

* WordPress Multisite Installation.
* MySQL 5.0.1 or greater.
* The MySQL user needs to be assigned the ability to "create views" and "drop views" in WordPress database.
* Permalink structure must be diferent to default when widget is activated in a blog of your network.

**Installation is easy**

1. Upload `multisite-global-search` folder to the `wp-content/plugins` directory in your WordPress multisite installation.
2. Activate the plugin in your Administration Panel.
3. Create a new page in your main site with the permalink URL: `http//yoursite/globalsearch/.`.
4. Place `[multisite_search_result]` in the post content area.
5. Activate widget `Multisite Global Search`.

**Upgrade instructions**

1. Deactivate the plugin in your Administration Panel.
2. Earlier version to 1.2.2 needs drop tables from the database: drop view yourdatabaseprefix_v_posts; drop view yourdatabaseprefix_v_comments; drop view yourdatabaseprefix_v_postmeta;
3. Upgrade the plugin.
4. Activate the plugin in your Administration Panel.
5. Activate widget `Multisite Global Search`.

== Frequently Asked Questions ==

If you have any further questions, please submit them.

= Can the search form be used with a shortcode in templates versus the widget? =

Insert search form in templates using the shortcode: `[multisite_search_form]`

= How can show horizontal form using the shortcode? =

Use `type` attribute to select vertical form or horizontal form. For example: `[multisite_search_form type="horizontal"]`
Default attribute value is `vertical`.

= How can change results page URI when you insert search form with the shortcode? =

Use `page` attribute to change results page URI. For example: `[multisite_search_form page="multisite-search"]`. Search results will be showed in http://your_blog_URL/multisite-search.
Default attribute value is `globalsearch`. 

= Can I put search form into PHP files using a function? =

Yes. For example, this is a results page for word 'e-learning': http://grial.usal.es/agora/busqueda/?mssearch=e-learning&msp=1&mswhere=all

In this case, the name of the results page is 'busqueda' so the code that it will have to put in the PHP template must be:

`<?php Multisite_Global_Search::ms_global_search_vertical_form('busqueda') ?>`
or
`<?php Multisite_Global_Search::ms_global_search_horizontal_form('busqueda') ?>`

= Get error "check you have create views privilege in your WordPress database. Illegal mix of collations for operation 'UNION'". =

The instruction means that you may not have given the necessary priviledges to your MySQL user. The user needs to be assigned the ability to "create views".

= Limit results to just the title and the excerpt =

Edit results page and place `[multisite_search_result excerpt="yes"]` in the post content area instead of `[multisite_search_result]`

= Set the plugin to always perform searches also in pages =

Check option "Search by default on pages" when you configure Multisite Global Search Widget.

If you use shortcode `[multisite_search_form]` use `search_on_pages` attribute to search by default on pages. For example: `[multisite_search_form search_on_pages="1"]`.
Default attribute value is `0`.

= Customizing search form =

You have to copy the CSS code from the stylesheet in the Multisite Global Search directory, paste it in your own stylesheet and modify it.

== Screenshots ==

1. Widget configuration.
2. Vertical Global Search widget.
3. Horizontal Global Search widget.

== Changelog ==

= 1.2.9 =
* Added: Slovak language pack

= 1.2.8 =
* Added: search multiple keywords regardless of the order

= 1.2.7 =
* Fixed: languages problem
* Added: Romanian language pack

= 1.2.6 =
* Fixed: SQL injection and XSS vulnerability
* Added: Portuguese language pack
* Added: German language pack
* Added: Administrative choice to disable search options

= 1.2.5 =
* Fixed: error message "The used SELECT statements have a different number of columns"
* Fixed: show up private posts
* Added: French language pack
* Added: Norwegian Bokmal language pack
* Changed: Administrative choice to perform searches by default on pages

= 1.2.4 =
* Fixed: empty excerpts
* Fixed: problems when you have upgraded from WPMU to WP3.0 Multisite
* Added: Russian language pack

= 1.2.3 =
* Fixed: windows paths
* Fixed: problemns with blog url
* Added: search on pages
* Changed: radio button to "search only blogs where I'm a member" is showed when user are logged in

= 1.2.2 =
* Fixed: Fatal error redeclared functions
* Added: deactivation hook. Clean database when the plugin is deactivated
* Changed: search form can be used into PHP files

= 1.2.1 =
* Fixed: database prefix problem
* Changed: translation files

= 1.2 =
* Added: error message when plugin installation faults
* Added: error message when permalink structure is "default"
* Fixed: error message when plugin is activated in a WordPress single installation
* Fixed: use constant BLOG_ID_CURRENT_SITE instead of 1

= 1.1 =
* Added: shortcode for search form
* Added: shortcode attribute that enable excerpted results
* Added: new strings to translation files
* Changed: order results
* Fixed: style for results page

== Upgrade Notice ==

= 1.2.8 =
Add support to search multiple keywords regardless of the order.

= 1.2.7 =
Include languages files and Romanian language pack.

= 1.2.6 =
IMPORTANT!! Fixing a vulnerability. Update your plugin.

= 1.2.5 =
Important! Fixing error message "The used SELECT statements have a different number of columns". French and Norwegian Bokmal translation.

= 1.2.4 =
Important! Fixing some problems with WPMU upgraded to WP3.0 Multisite.

= 1.2.3 =
Now search on pages.

= 1.2.2 =
Fixing a fatal error and adding new features. Sorry for the inconveniences.

= 1.2.1 =
This version fixes a bug with database prefix.

= 1.2 =
This version check that your installation satisfy all plugin requeriments. Should upgrade if you can't get any search results with this plugin.