=== HidePost ===
Contributors: Funnnny
Tags: protect, hidepost, post
Requires at least: 1.5
Tested up to: 3.0
Stable tag: 2.3.8

Protect your post or your link by using [hidepost] and [/hidepost] between the protected content.

== Description ==

A Wordpress plugin that protect a part of your blog posts (maybe links, images or texts), that only registered and logged in members can see it. Simply put your content to be hidden with [hidepost] and [/hidepost].

If you want to protect your post to some user with low level, you can use [hidepost=level][/hidepost]. User level can be found on Codex page.

== Installation ==
= New install =
1. Upload the folder hidepost to wp-content/plugin directory
2. Activate it
3. Go to Option -> Hidepost and check "Enable HidePost"
4.1 Use [hidepost=level] tag to protect the content
4.2 Or put the function <?php hidethis($string,$level); ?> in the template

= Upgrade =
*2.3 to lastest

1. Deactive HidePost and Upload the folder hidepost to wp-content/plugin directory

2. Active Hidepost

3. Go to Option page and take a look to the new feature

*Previous 2.2 to lastest

1. Deactive HidePost and Upload the folder hidepost to wp-content/plugin directory

2. Active Hidepost

3. Go to Option -> Hidepost and replace your old tag with new [hidepost] tag

== Frequently Asked Questions ==
=The content still display after I put [hidepost] and [/hidepost] =

Double check your tag, it must be [hidepost] and [/hidepost], I tested it and it work perfectly. If you think its a bug, feel free to drop me a email or comment on plugin's page.

=Hard to Custom text...=

Goto Option -> Hidepost, and change Show Text to whatever you want. Note that %login% is the link to login page, and %register% is the link to register page. You should leave this unless you know what you are doing.

=Using the previous version?=

If your version less than 2.2 and you are using the [protect] tag, you must change all your tag to [hidepost] and upgrade to lastest version to have many new feature.

=The text "Please login or register not display" but the content still be hidden=

Go to Option page and uncheck "Hide the text".

==Change log==
=1.0 (25/03/2007)= 

Initial release

=1.1 (27/03/2007)= 

Change <!--protect--> and <!--/protect--> to [hidepost] and [/hidepost].
Some code and typos fixed.

=1.2 (28/03/2007)=

Remade the code for the new feature and future use.

=1.3 (31/03/2007)=

Fixed a bug with two or more protect tag

Fixed a bug with close tag ([/hidepost]) before open tag ([hidepost]).

=2.0 (05/08/2007)=

Added a Option page

The Protect Tag and Show text is now customable.

=2.1 (08/08/2007)=

Minor bugs fixed.

Some tooltips added.

=2.2 (09/08/2007)=

Added Protect link.

Typos fixed.

=2.3 (16/12/2007)=

Added hide post with user level.

User now has the right to disable HidePost without Deactive it.

Bugs fixed.

Added option to allow bot like Google can view hidden content

=2.3.1 (23/12/2007)=

Typos fixed

Added a function to replace old tag with new tag

=2.3.2 (24/12/2007)=

Some bug fixed

=2.3.3 (28/02/2008)=

Fixed some wrong XHTML code

Protected text with User level is now customable

=2.3.4 (07/08/2008)=

Removed allow bot

More compatible with WP2.5

Some improve

=2.3.5 (08/09/2008)=

Redirect user to previous page when click on login link

=2.3.6 (11/02/2009)=

Fixed error with exceprt and wrong user level.

Some changes in option page 

=2.3.7 (19/05/2009)=

Add quick button to add Hidepost's tag in TinyMCE editor

=2.3.8 (16/01/2010)=

Fixed bug with RSS and Readmore link

Added a template call
