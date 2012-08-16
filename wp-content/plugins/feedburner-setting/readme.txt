=== Plugin Name ===
Contributors: Jamc
Tags:FeedBurner, category, tag, FeedSmith
Requires at least: 3.0.0
Tested up to: 3.1.1
Stable tag: 1.0.0

== Description ==

This is a plugin originally authored by <a href="http://www.orderedlist.com/">Steve Smith</a>. It detects all ways to access your original WordPress feeds and redirects them to your FeedBurner feed. I enhanced it base on the FeedBurner FeedSmith and now it can redirects feeds for category and tag also.

== Installation ==

1.Download the plugin and expand it to the plugin folder(wp-content/plugins/).

2.Login into the WordPress administration area and go to the plugin page.

3.Click the activate link of the 'Feedburner feedsmith extend' plugin.

4.You can then go to Settings -> FeedBurner FeedSmith Extend to configure all your FeedBurner feeds.



== Frequently Asked Questions ==

= 1.How do I get links of feeds for tags =

Normally, the format should be like the link below:

http://yoursite/index.php/tag/tag_slug/feed/

or

http://yoursite/?feed=rss2&tag=tag_slug


It uses the tag_slug parameter to define which tag should it be.

= 2.How do I get links of feeds for category =

There is a native method provided by WordPress which allows you to get the category feed link easily. The only thing you need to input is the category id.

get_category_feed_link( $category->term_id )



