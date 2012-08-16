=== Twitter for Wordpress Extended ===
Contributors: Marc Schieferdecker, raufaser
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=m_schieferdecker%40hotmail%2ecom&item_name=wp%20plugin&no_shipping=0&no_note=1&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: twitter, widget
Requires at least: 2.1
Tested up to: 2.8
Stable tag: 1.0.3

Twitter for WordPress Extended shows your tweets, tweets for a search term (e.g. a tag), your friends timeline or the current twitter trends.

== Description ==

Twitter for WordPress displays yours tweets, your friends timeline, a Twitter search result or the current twitter trends in your WordPress blog. This is done by using the built in widget or by using PHP functions in your theme.

= Features =

	* Simply
	* Customizable
	* Widget support
	* No options page (yes, its a feature)
	* Uses Wordpress resources (no extra files needed)
	* Detects URLs, e-mail address and @username replies


= Usage =

If you use WordPress widgets, just drag the widget into your sidebar and configure. If widgets are not your thing, use the following php code to display Twitter messages:

`<?php twitter_messages("username"); ?>`


If you want to display tweets to a special search result, use the following code:

`<?php twitter_searchstring("#twitaly"); ?>`


If you want to display the tweets of your friends timeline, use this:

`<?php twitter_friends( "username", "password" ); ?>`


If you want to display the current twitter trends, use that snippet:

`<?php twitter_trends(); ?>`


= Customization =

The plug in provides the following CSS classes:

	* ul.twitter: the main ul (if list is activated)
	* li.twitter-item: the ul items (if list is activated)
	* p.twitter-message: each one of the paragraphs (if msgs > 1)
	* .twitter-timestamp: the timestamp span class
	* a.twitter-link: the tweet link class
	* a.twitter-user: the @username reply link class
	* li.twitter-item img: format the avatar icon on search results


== Installation ==

Drop "twitter-for-wordpress-extended" folder (or even twitter_extended.php) into /wp-content/plugins/ and activate the plug in the Wordpress admin area.

If you use the "Twitter for Wordpress" plugin please deactivate it before activating "Twitter for WordPress Extended". The extended plugin is designed to replace the original.

== Credits ==

[Ricardo González](http://rick.jinlabs.com/) - The plugin is highly based on the plugin "Twitter for Wordpress", so the major part of the credits goes to him.

I only added three functions for getting the public timeline, tweets for a search query and the current twitter trends.


== Contact ==

Problems? Suggestions? Anything else? Contact me: m_schieferdecker[at]hotmail.com | http://www.das-motorrad-blog.de/meine-wordpress-plugins/
