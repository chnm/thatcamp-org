=== Compact Archives ===
Contributors: smub, noumaan
Tags: posts, archive, archives
Requires at least: 3.0
Tested up to: 4.3
Stable tag: trunk

Displays a monthly archive of posts in a more compact form than the usual long list.

== Description ==

The built-in WordPress archives widget is great for new blogs, but it doesn't look as good for more established blogs like [WPBeginner](http://www.wpbeginner.com "WPBeginner"). Compact Archives displays the monthly archive of posts in a more compact form than the usual long list. It can be shown as a compact block suitable for the body of an archives page:

`2009: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
2008: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
2007: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec`

or in an even more compact form to fit a sidebar:

`2009: J F M A M J J A S O N D 
2008: J F M A M J J A S O N D 
2007: J F M A M J J A S O N D` 
				
or something in between:

`2009: 01 02 03 04 05 06 07 08 09 10 11 12
2008: 01 02 03 04 05 06 07 08 09 10 11 12
2007: 01 02 03 04 05 06 07 08 09 10 11 12`

If you like this plugin, and want to learn more about WordPress, then I suggest that you visit [WPBeginner](http://www.wpbeginner.com "WPBeginner"). It is one of the largest free WordPress resource site with tons of [WordPress tutorials](http://www.wpbeginner.com/category/wp-tutorials/ "WordPress tutorials"), and I'm proud to say that I created it. 

We also have [free WordPress video tutorials](http://videos.wpbeginner.com "Checkout out WPBeginner's WordPress tutorial videos"), and numerous other guides such as [how to choose the best WordPress hosting](http://www.wpbeginner.com/wordpress-hosting/ "How to Choose the Best WordPress Web Hosting"), [how to install WordPress](http://www.wpbeginner.com/how-to-install-wordpress/ "How to install WordPress") and many more.

I often get asked what are the [best WordPress plugins](http://www.wpbeginner.com/category/plugins/ "best WordPress plugins")? Which plugins should I use on my site? What are the must have plugins? I have created a blueprint of all the plugins and tools that I'm using. [Take a look at plugins I'm using at WPBeginner](http://www.wpbeginner.com/blueprint/ "Take a look at plugins I'm using at WPBeginner")

Note for Beginners: Like all plugins, Compact Archives is only available for self-hosted WordPress sites. So YES you need to [switch from WordPress.com to WordPress.org](http://www.wpbeginner.com/wp-tutorials/how-to-properly-move-your-blog-from-wordpress-com-to-wordpress-org/ "switch from WordPress.com to WordPress.org") in order to use this plugin on your WordPress site. For more details, see my infographic on [Self hosted WordPress.org vs Free WordPress.com](http://www.wpbeginner.com/beginners-guide/self-hosted-wordpress-org-vs-free-wordpress-com-infograph/ "Self Hosted WordPress.org vs free WordPress.com")

If you like this plugin, then please leave a good rating. For support just ask the questions here in the support forum.

Lastly, if you like this plugin then follow WPBeginner on [Twitter](http://twitter.com/wpbeginner "Twitter"), [Facebook](http://facebook.com/wpbeginner "Facebook"), and [Google+](https://plus.google.com/101634180904808003404/ "Google+")


Plugin Header Photo Credit: [Mr. T in DC](http://www.flickr.com/photos/mr_t_in_dc/4249887012/ "Scroll photo by Mr T in DC's Flickr")

Original Author Credit: This plugin was originally created by [Rob Marsh](http://profiles.wordpress.org/robmarsh/) and [Aldolat](http://profiles.wordpress.org/aldolat/) added a widget for it. WPBeginner adopted the plugin and took the responsibility to keep it updated and going.


== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder.

1. Go to the **Plugins** page and activate the plugin.

1. Put `<?php compact_archive(); ?>` at the place in your template 
where you want it to appear, e.g., in your sidebar:

`	<ul>
		<?php compact_archive(); ?>
	</ul>`

1. You can also use shortcode `[compact_archive]` in a page or post to display compact archives. See FAQ for shortcode parameters. 

1. There is also a Compact Archives Widget which can be used to display compact archives in sidebar or any widgetized area. 

== Frequently Asked Questions ==

= How Do I Get the Different Layouts? =

The template tag, `compact_archive`, has some parameters:

`	compact_archive($style='initial', $before='<li>', $after='</li>');`
	
If $style == 'initial' (the default) the display will fit into a sidebar:

`	2009: J F M A M J J A S O N D 
	2008: J F M A M J J A S O N D 
	2007: J F M A M J J A S O N D` 

If $style == 'block' the display will be wide enough to fill the main column of a page:

`	2009: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
	2008: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec 
	2007: Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec`

If $style == 'numeric' the display will use month numbers:

`	2009: 01 02 03 04 05 06 07 08 09 10 11 12
	2008: 01 02 03 04 05 06 07 08 09 10 11 12
	2007: 01 02 03 04 05 06 07 08 09 10 11 12`

$before and $after wrap each line of output. The default values make 
each line of the archive into a list item:

`	<ul>
		<?php compact_archive(); ?>
	</ul>`

= How do I get different layouts using shortcode? =

The shortcode `[compact_archive]` works just like the template tag. It accepts three parameters which are style, before, and after. 

Using shortcode `[compact_archive style="block"]` will display compact archives in block. 

Using shortcode `[compact_archive style="numeric" before="<p>" after="</p>"]` will display compact archive in numeric form, wrapped in a paragraph tag.  

= How do I get different layouts using Compact Archive Widget in Sidebar? =

Compact Archive comes with a sidebar widget located Appearance -> Widgets. Drag and drop Compact Archives Widget to your sidebar. The widget has three option fields. You can provide your widget a title if you want or leave it blank. You can choose a display layout from Select the Style option. It has three layout choices to choose from which are initials, block, and numeric. Tansform text option can be left to No Transformation or you can tansform text style to UPPERCASE or Capitalize.  

= Is There Any Scope for CSS Styling? =

The year links at the start of each line are wrapped in <strong></strong> tags while months with no posts are wrapped with <span class="emptymonth"></span> so you can differentiate them visually using your style sheet.

= What if My Site is in Another Language? =

The plugin chooses month names and abbreviations according to the language locale, WPLANG, set in wp-config.php.

Compact Archives also honours whatever kind of permalink pattern you have set.

== Screenshots ==

1. Compact archives displayed in block layout

== Changelog ==
= 3.0.4 =
* WordPress 4.3 compat
= 3.0.3 =
* Minor bug fix
= 3.0.2 =
* Resolved shortcode output bug.
= 3.0.1 =
* Resolved function naming conflict.
* Added a check to see if compct archives widget plugin is installed. If it is installed then ask the user to deactivate it. 
= 3.0.0 =
* Added shortcode to be used in posts and pages
* Added widget based on Compact Archives Widget plugin by [aldolat](http://profiles.wordpress.org/aldolat/)
= 2.0.0 =
* 3-10 x faster depending on number of posts
* makes the most of [Plugin Output Cache v.4.x](http://rmarsh.com/plugins/poc-cache/)
= 1.0.6 = 
* Introduces numeric display
= 1.0.5 = 
* Fixes a display bug for locales with accented characters
= 1.0.4 = 
* Makes use of the Plugin Output Cache to speed things up
= 1.0.3 =
* Fixes a small problem with undated posts 
= 1.0.1 = 
* Speeds up the plugin for WordPress 2.1+ 