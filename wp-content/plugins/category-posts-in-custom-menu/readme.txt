=== Category Posts in Custom Menu ===
Contributors: anaid
Tags: menu, category, post tag, tag, posts, dynamic, automatic, custom, taxonomy, custom taxonomy
Requires at least: 3.2.1
Tested up to: 3.4.1
Stable tag: 0.6

Dynamic menus: This plugin automatically replaces selected Category links / Post Tag links / Custom Taxonomy links in a Custom Menu by a list of their posts.

== Description ==

The custom menu only allows adding a link that leads to a category or tag page. I wanted to insert all posts from a certain category/tag themselves into the menu, so I wrote a little plug-in. If you want to list posts the posts <b>themselves</b> in the menu and if you want Wordpress to do this automatically for you, then I hope you will find this plugin useful. 

<strong>Now also works for custom post taxonomies!</strong>

= Using it =
Enable the plugin and go to Appearance > Menus. Create your Custom Menu. If you use Categories or Post Tags in your Custom Menu, be sure to <b>save the menu first</b>. Next, you can choose whether you would like to list the original link, or if you would like to replace it by the posts in that taxonomy (Category/Post Tag). Note that custom post types are not supported at the moment. 

For each Category or Post Tag item in the menu, you now have the following extra configuration options: 

* replace the link by a list of posts in the menu y/n; 
* specify the (maximum) number of posts you want to show for this item (-1 = all);
* their ordering (ascending/descending by none, ID, author, title, date, modified, parent, rand or comment_count); 
* specify the title for the menu items. For this you can use the following wildcards: %post_title, %post_author, %post_feat_image (url only), %post_excerpt, %post_url, %post_date, %post_date_gmt, %post_status, %post_modified, %post_modified_gmt, %post_comment_count or wildcards for custom fields, like %post_my_field, where 'my field' or 'my_field' is a custom field;

= Details =
This plugin uses the wp_nav_menu_objects filter hook to remove and replace category items in the menu by the posts in that category. It extends Walker_Nav_Menu_Edit to add input fields to Appearance > Menus and adds some CSS to style this properly.

== Screenshots ==

1. Go to Appearance > Menu to enable/disable and set the options for the replacement of Category / Post Tag links by their posts.

== Changelog ==

0.6

* Added a bunch of wildcards: %post_feat_image (url only), %post_excerpt, %post_url, %post_date, %post_date_gmt, %post_status, %post_modified, %post_modified_gmt, %post_comment_count

0.5

* Enable custom taxonomies.
* Change plugin URL.

0.4

* Fix plugin bug for Tags, probably related to 3.2.2 update.
* Edit documentation
* Change some CSS to show checkbox next to label instead of above it.
* Edit text in menu item block to clarify use of custom fields.

0.3

* Extended the plugin for use of custom fields. Use a custom field with name 'my field' with wildcard %post_my_field. Note that if the post does not have this custom field, the wildcard is removed (it is not replaced by anything).

0.2

* Extended the plugin to post tags. Note that the plugin name has unfortunately _not_ been changed to reflect this extension. 

Added checkboxes, dropdowns and text fields to Appearance > Menu. It is now possible to:

* Enable/disable per taxonomy item whether you want to replace it by its posts
* Specify the (maximum) number of posts that should be displayed.
* Specify the ordering (Descending/Ascending) and the field to order by (ID, title, etc..)
* Set a title for the menu items. For this you can use the wildcards %post_title and %post_author.

== Installation ==

1. Upload `category-posts-in-custom-menu.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Can't see it working in any way =

Problem: "I can't see what effect it has had. Nothing has changed (not that I can see) in the Appearance > Menu page. Just like before." 

Two possible causes:
<ul>
<li>Issue 14527 (http://core.trac.wordpress.org/ticket/14527) is the cause. When adding a menu item but <i>before</i> saving it, you will indeed see none of the promised checkboxes. So be sure to <b>save your menu</b> after you add a Category/Tag to your menu. <i>Then</i> you'll see the added functionality.

The reason is because the hook that is used to extend the functionality is not being applied until you've saved the item. A patch is approved for 3.4, after which I can fix this :)</li>
<li>You're already using another plugin that uses the wp_nav_menu_edit hook and has a higher priority than mine. This is by design: My plugin is not critical and therefore I don't claim a high priority on the hooks. Plugins/Themes that indicate to be more important will be given priority.</li>
</ul>