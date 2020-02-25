=== Compact Archives ===
Contributors: smub, noumaan, deb255
Tags: archive, archives, yearly archive, montly archive, yearly, monthly, annually, archive by month, archive by year
Requires at least: 3.0
Tested up to: 5.2.2
Stable tag: 3.0.9

Displays a smart monthly archive of posts in a more compact form rather than the default long archive widget.

== Description ==

The built-in WordPress archives widget is great for new blogs, but it doesn't look as good for more established blogs like <a href="https://www.wpbeginner.com/" rel="friend">WPBeginner</a>. Compact Archives displays the monthly archive of posts in a more compact form rather than the usual long list. It can be shown as a compact block suitable for the body of an <a href="https://www.wpbeginner.com/wp-tutorials/how-to-create-an-archives-page-in-wordpress/" rel="friend">archives page</a>:

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

Compact Archive plugin works seamlessly with <a href="https://www.wpbeginner.com/beginners-guide/how-to-use-the-new-wordpress-block-editor/" rel="friend">Gutenberg Block Editor</a>, <a href="https://www.wpbeginner.com/plugins/how-to-disable-gutenberg-and-keep-the-classic-editor-in-wordpress/" rel="friend">Classic Editor</a>, and WordPress widgets.

= What's Next =

If you like this plugin, then consider checking out our other projects:

* <a href="https://optinmonster.com/" rel="friend" title="OptinMonster">OptinMonster</a> - Get More Email Subscribers with the most popular conversion optimization plugin for WordPress.
* <a href="https://wpforms.com/" rel="friend" title="WPForms">WPForms</a> - Best Drag & Drop WordPress Form plugin (over 1 million active installs).
* <a href="https://www.monsterinsights.com/" rel="friend" title="MonsterInsights">MonsterInsights</a> - See the Stats that Matter and Grow Your Business with Confidence. Best Google Analytics Plugin for WordPress.
* <a href="https://www.seedprod.com/" rel="friend" title="SeedProd">SeedProd</a> - Jumpstart your website with the #1 Coming Soon & Maintenance Mode Plugin for WordPress.
* <a href="https://wordpress.org/plugins/wp-mail-smtp/">WP Mail SMTP</a> - Improve email deliverability for your contact form with the most popular SMTP plugin for WordPress.

Visit <a href="http://www.wpbeginner.com/" rel="friend" title="WPBeginner">WPBeginner</a> to learn from our <a href="http://www.wpbeginner.com/category/wp-tutorials/" rel="friend" title="WordPress Tutorials">WordPress Tutorials</a> and find out about other <a href="http://www.wpbeginner.com/category/plugins/" rel="friend" title="Best WordPress Plugins">best WordPress plugins</a>.

Like all plugins, Compact Archives is only available for self-hosted WordPress sites. So YES you need to [switch from WordPress.com to WordPress.org](http://www.wpbeginner.com/wp-tutorials/how-to-properly-move-your-blog-from-wordpress-com-to-wordpress-org/ "switch from WordPress.com to WordPress.org") in order to use this plugin on your WordPress site. For more details, see the infographic on [Self hosted WordPress.org vs Free WordPress.com](http://www.wpbeginner.com/beginners-guide/self-hosted-wordpress-org-vs-free-wordpress-com-infograph/ "Self Hosted WordPress.org vs free WordPress.com")

If you like this plugin, then please leave a good rating. For support just ask the questions here in the support forum.

= Credits =

This plugin was originally created by [Rob Marsh](http://profiles.wordpress.org/robmarsh/) and [Aldolat](http://profiles.wordpress.org/aldolat/) added a widget for it. WPBeginner adopted the plugin and took the responsibility of keeping it updated.

Plugin Header Photo Credit: [Mr. T in DC](http://www.flickr.com/photos/mr_t_in_dc/4249887012/ "Scroll photo by Mr T in DC's Flickr")

== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder.

2. Go to the **Plugins** page and activate the plugin.

3. Put `<?php compact_archive(); ?>` at the place in your template
where you want it to appear, e.g., in your sidebar:

`	<ul>
		<?php if ( function_exists( 'compact_archive' ) ) compact_archive(); ?>
	</ul>`

4. You can also use shortcode `[compact_archive]` in a page or post to display compact archives. See FAQ for shortcode parameters.

5. There is also a Compact Archives Widget which can be used to display compact archives in sidebar or any widgetized area.

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
2. Gutenberg block

== Changelog ==
= 3.0.9 =
* Update Readme.txt
* Add a Gutenberg block for inserting archives inside the Post editor
= 3.0.8 =
* Update Readme.txt
* Update path for AM Dashboard
= 3.0.7 =
* Update code example in Readme.txt
* Verify compatibility with WordPress 5.1.1
= 3.0.6 =
* Code cleanups
= 3.0.5 =
* WordPress 4.8 compatibility
= 3.0.4 =
* WordPress 4.3 compat
= 3.0.3 =
* Minor bug fix
= 3.0.2 =
* Resolved shortcode output bug.
= 3.0.1 =
* Resolved function naming conflict.
* Added a check to see if compct archives widget plugin is installed. If it is installed then ask the user to deactivate it.