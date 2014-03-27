=== Recent Posts with Excerpts ===
Contributors: sillybean
Tags: widgets, excerpts
Donate link: http://stephanieleary.com/code/wordpress/recent-posts-with-excerpts/
Text Domain: recent_posts_with_excerpts
Domain Path: /languages
Requires at least: 2.8
Tested up to: 3.8.1
Stable tag: 2.5.4


A widget that lists your most recent posts with optional excerpts.   

== Description ==

A widget that lists your most recent posts with excerpts, optionally limited to a category. The number of posts and excerpts is configurable; for example, you could show the titles of five posts but include the excerpt for only the two most recent. Supports <a href="http://robsnotebook.com/the-excerpt-reloaded/">the_excerpt Reloaded</a> and <a href="http://sparepencil.com/code/advanced-excerpt/">Advanced Excerpt</a> for excerpts with HTML formatting.

New! Supports displaying post thumbnails along with excerpts. Widget options let you place the thumbnails above the title, in between the title and the excerpt, or below the excerpt.

= Translations =

* Serbo-Croatian (sr-RS) by <a href="http://www.webhostinghub.com">Borisa Djuraskovic</a>

If you would like to send me a translation, please write to me through <a href="http://stephanieleary.com/about/contact/">my contact page</a>. Let me know which plugin you've translated and how you would like to be credited. I will write you back so you can attach the files in your reply.


== Installation ==

1. Upload the plugin directory to `/wp-content/plugins/` 
1. Activate the plugin through the 'Plugins' menu in WordPress

Go to Appearance &rarr; Widgets to add widgets to your sidebar in widget-ready themes.

== FAQ ==

To style your excerpts, add a declaration for `.recent_with_excerpt li blockquote` to your theme's stylesheet.

To add a link to the bottom of the widget's output, below the list, use the `recent_posts_with_excerpts_end` action. Example:

`
add_action( 'recent_posts_with_excerpts_end', 'my_recent_posts_more' );
function my_recent_posts_more() {
	echo '<a class="readmore" href="/news/archive">More</a>';
}
`

To add something above the list, use the `recent_posts_with_excerpts_begin` action instead.


== Changelog ==

= 2.5.3 =
* Added POT file.
= 2.5.2 =
* Added 'recent_posts_with_excerpts_query' filter to allow modifications to the widget's query.
= 2.5.1 =
* Changed the_time() to get_the_time(), which prevents the date from appearing outside its intended heading tag.
= 2.5 =
* Cleaned up empty classes.
* Added actions to the top (`recent_posts_with_excerpts_begin`) and bottom (`recent_posts_with_excerpts_end`) of the widget output, for developers to customize. See the FAQ tab for an example.
* Improved the thumbnail size dropdown.
= 2.4 =
* Added support for displaying post thumbnails with the excerpts.
* Localized strings for translators.
= 2.3.2 =
* Fixed a bug in which limiting the posts to a category and/or tag would override the setting for the number of posts to display.
= 2.3.1 =
* Fixing bugs and restoring "more" option broken in 2.3.
= 2.3 =
* Added option to display the date.
= 2.2 =
* Fixed HTML validation error. (Thanks, Cris!)
= 2.1 =
* Added tag limit option
* All widget input fields are now localized (September 11, 2009)
= 2.0 =
* Added widget title option
* had to jump version numbers (did the earlier ones wrong, apparently) (August 3, 2009)
= 1.12 =
* bugfix -- needed to reset the Loop after the widget runs (August 2, 2009)
= 1.11 =
* bugfix -- 'more' text did not change according to settings (July 26, 2009)
= 1.1 =
* Added category option (July 26, 2009)
= 1.0 =
* First release (July 24, 2009)


== Screenshots ==

1. The widget manager.
2. What is shown, given the settings shown in screenshot #1, using the default theme.