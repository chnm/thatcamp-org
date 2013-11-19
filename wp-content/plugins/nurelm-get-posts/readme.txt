=== Get Posts ===
Author URI: http://www.nurelm.com/themanual
Plugin URI: http://www.nurelm.com/themanual/2009/08/21/nurelm-get-posts/
Contributors: nurelm
Tags: get_posts, post, posts, formatting, list, shortcode
Requires at least: 2.5.1
Tested up to: 3.7
Stable tag: 0.5

Adds a shortcode tag [get_posts] to display a list of posts within any static page or post.

== Description ==

This plugin adds the ability to put a shortcode tag in any static page or post and have it display a list of posts using the get_posts template tag. For more detailed information about this plugin, please check out its [home page](http://www.nurelm.com/themanual/2009/08/21/nurelm-get-posts/).

By default it will show the 5 most recent posts ordered in reverse date order,
but it will accept any of the options provided by the [get_posts template tag](http://codex.wordpress.org/Template_Tags/get_posts).

To use it, just put the following into the HTML of any page or post, use as many times as you like on the same page:

	[get_posts]

This default usage will return the last 5 posts in reverse chronological order.
Along with all the options provided by the get_posts template tag, it will also
accept a few additional options:

- ul\_class. This is the class assigned to the bullet list, with a default of "get_posts_class".
- fields. This is a comma separated list of fields to show, taken right from the [wp_posts database table fields](http://codex.wordpress.org/Database_Description/2.7#Table:_wp_posts).  Some of the more interesting fields you can use in this list include post_title, post_date, and post_excerpt (you'll find more in the table description referenced above).  The default is "post_title".
- fields\_classes.  Another comma separated list that lets you assign a class to each of the fields specified above, which will be provided via a <span> tag wrapped around the field.  The default value for this list is "post_title_class".
- fields\_make\_link.  One more comma separated list that lets you choose which of your fields are links to the post.  It is a list of either "true" or "false" values ... values of "true" make the corresponding field a link. The default is "true".

A couple of examples:

	[get_posts tag="my favorite tag" numberposts=5 orderby="title]

Shows a bulleted list consisting of the titles of 5 posts with the tag "my favorite tag" ordered by title.

	[get_posts tag="my favorite tag" fields="post_title, post_date, post_excerpt" fields_classes="class1, class2, class3" fields_make_link="true, false, false"]

Shows a bulleted list consisting of the titles, post dates, and excerpts of posts with the tag "my favorite tag".  The title will be wrapped in a <span> tag with a class of "class1", the date with a <span> of class "class2", and the excerpt with "class3".  Only the title will provide a link to the post.

Check the [get_posts template tag](http://codex.wordpress.org/Template_Tags/get_posts) documentation for all of the possible options associated with the tag, and the [wp_posts database table fields](http://codex.wordpress.org/Database_Description/2.7#Table:_wp_posts) for all possible field names.

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory and unzip it.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Place `[get_posts]` in your pages. Check the [plugin homepage](http://www.nurelm.com/themanual/2009/08/21/nurelm-get-posts/) for detailed options and more complex uses.

== Changelog ==

= 0.5 =
* Update readme and copyright

= 0.4 =
* Tested with WP 3.7

= 0.3 =
* Tested with WP 3.0.1

= 0.2 =
* Added the fields, fields_classes, and fields_make_link attributes.

= 0.1 =
* Initial release.

== Frequently Asked Questions ==

Check the [plugin homepage](http://www.nurelm.com/themanual/2009/08/21/nurelm-get-posts/)
