=== YOP Poll ===
Contributors: yourownprogrammer
Donate Link: http://www.yop-poll.com
Tags: booth, create poll, poll plugin, poll, polls, wordpress poll, vote, voting, survey, polling, yop poll, yop
Requires at least: 3.3
Tested up to: 4.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.htm

Use a full option polling solution to get the answers you need.

YOP Poll is the perfect, easy to use poll plugin for your wordpress site.

== Description ==

YOP Poll plugin allows you to easily integrate a survey in your blog post/page and to manage the polls from within your WordPress dashboard but if offers so much more than other similar products.  Simply put, it doesn't lose sight of your needs and ensures that no detail is left unaccounted for.

To name just a few improvements, you can create polls to include both single or multiple answers, work with a wide variety of options and settings to decide how you wish to sort your poll information, how to manage the results, what details to display and what to keep private, whether you want to view the total votes or the total voters, to set vote permissions or block voters etc.

Scheduling your polls is no longer a problem. YOP Poll can simultaneously run multiple polls (no limit included) or you can schedule your polls to start one after another. Also, keeping track of your polls is easy, you have various sorting functions and you can access older versions at any time.

Designed to intuitive and easy to use, this plugin allows shortcodes and includes a widget functionality that fits perfectly with your WordPress website. For more details on the included features, please refer to the description below.

Current poll features:

   *  Create/ Edit / Clone/Delete poll - allows you to create or intervene in your poll at any time, if you consider it necessary.

   *  Poll scheduling:  programs each poll to start/end on a certain date. You can simultaneously run multiple polls. This option can be used to schedule your polls one after another.

   *  Display polls: you can choose to display one or more polls on your website by simply adding the corresponding poll ID. You can also decide for a random display of your active polls.

   *  View all polls: lists all your polls that you can sort by number of votes or voters, by question or by date. It also includes a search option.

   *  Poll answers - allows other answers, multiple answers and includes a sorting module by various criteria: in exact order, in alphabetical order, by number of votes, ascending, descending etc.

   *  Poll results - offers a great flexibility when displaying the results: before/after vote, after poll's end date, on a custom date or never. The results can also be displayed by vote numbers, percentages or both. You can choose to include a view results link, view number of votes or number of voters.

   *  Add new custom fields - is a complex option that you can use to ask for additional information from your voters, information that you can then export and use for.

   *  Reset stats - proves useful when you wish to restart a poll.

   *  Vote permissions: - limits the voting accessibility to guests, registered users or both, or blocks user access by cookie, IP and username.

   *  Archive options - allows the users of the website to access former polls statistics. You can choose which polls to display according to their start/end date.

   *  Edit/delete/clone templates - allows you to customize the poll using either the html or the visual modes. You can also customize the result bar.

   *  Display Options - displays answers and results tabulated, vertically or horizontally.

   *  Logs and bans - user logs and bans can be seen in the admin section. You can ban users by email, username and IP and you can set the limitation preferences for all your polls or for just one of them.

== Installation ==

1. Upload 'plugin-name.php' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

This plugin was especially designed for flexibility and it is very easy to use. We don't expect you to encounter serious issues, but we included a list with some logical questions that you may find useful.

= How can I create a poll? =

Go to your YOP Poll menu and select the "Add New" option.
Fill the required information according to the examples we included: name, question, answers (add additional ones if you need), select the start/end date for your poll, and decide on the advanced settings for results, votes, voters, accessibility etc.
Once you decided on all your poll details, click on "Save".
To view your new poll access "All Polls" from your main menu and choose the corresponding entry from the list.

= How can I link a poll to my webpage? =

Under "All Polls", each poll has an option called "Get Code".
Clicking on that will display a popup that generates the code you need to place in your page or post.
This is it. Check your page or post now.

= Do you have some predefined shortcodes that I can use? =

Yes
Current Active Poll ID = -1:   [yop_poll id="-1"]
Latest Poll id = -2:           [yop_poll id="-2"]
Random Poll id = -3:           [yop_poll id="-3"]

= Can I have more than one poll active? =

Yes, you can run multiple polls at the same time or you can schedule them to begin one after another using the "Select start/end date" option.

= Can I ask for additional information from my voters? =

Yes, you can customize your poll to request additional information. Eg. name, email, age, profession.
To include this, when you create your poll using the "Add New" form, expand "Custom Fields" section and include as many requests as you need.

= How can I create/modify a template? =

Access the "Templates" menu.
If you want to create a new template use the "Add new" option and include the corresponding HTML/visual code.
If you want to modify an existing template, select it from the Templates list and choose "Edit". You will access the HTML/visual code you want to edit.

= How do I check the results? =

Locate the poll you want to evaluate by accessing "All Polls".
Below the name of the poll you have several options.
Use the "View Votes" link to track the results of the poll,
or access the "Logs" for a more detailed evaluation.

= What is the difference between Options and Poll Options for each poll? =

Options (located under plugin menu) is the way to specifify general settings for all your polls.
If you want to go further and customize each poll, these settings will take precedence over Options settings.

= How can I edit access to YOP Poll for administrators, editors, authors? =

To do this, in your wordpress go to Plugins->Editor.
On the right choose Yop Poll as the plugin to be edited.
The file you need to edit is yop-poll/inc/admin.php.
The file you need to edit is yop-poll/inc/admin.php.
Once you open the file, do a search for function current_user_can.
In that function you can find the options you need to edit.

= How can I see the results after the poll ends? =

Edit your poll and in "View Results:" choose "After Poll End Date" and save.

= How can I add a Poll Archive page on my website? =

From your WordPress menu create a new page that contains [yop_poll_archive] and has the permalink http://www.yourwebsite.com/polls/

= How can I add a hyperlink in the poll question or add a photo as an answer? =

To add a link to your question you can use <a href="[your link]" target="_blank">[link text]</a>
To add a photo as an answer you can use <img src=[photo_url] title="[photo_title]" alt="[photo_description]"/>

= Can I add more than one question to a poll? =

You can have only one question per poll. If you want to ask more than one question, you have to create a poll for each one.

== Screenshots ==

1. Add New
2. Templates
3. View All
4. YOP Poll as a widget with a custom field defined
5. YOP Poll on a page with a custom field defined

== Changelog ==

= 5.7.9 =
* start date and end date easier to read on the front end
* Fixed issue with showing results before vote

= 5.7.8 =
* Fixed issue with reset stats
* Fixed security issue
* Fixed issue with automatically reset stats
* Fixed issue with custom loading image
* Fixed display issues
* Updated Get Code with more options

= 5.7.7 =
* Fixed issue with translations

= 5.7.6 =
* Fixed issues with cloning poll
* Fixed conflicts with different plugins
* Fixed issue with pagination on archive page
* Fixed issue with logs page
* Fixed issue with facebook voting
* Added new shortcuts for email notifications
* Added new column for username in view votes page

= 5.7.5 =
* Fixed issue with vote button not showing up
* Other minor fixes

= 5.7.4 =
* Fixed security issue. A big thank you to [g0blin Research](https://twitter.com/g0blinResearch) for his help in getting this issue fixed

= 5.7.3 =
* Fixed display poll issue

= 5.7.2 =
* Display poll improvements

= 5.7.1 =
* Fixed issue with polls not being displayed

= 5.7 =
* Fixed issue with random polls
* Fixed issue with tabulated display
* Removed autoscroll after a failed vote
* Fixed issue with inserted code when using html editor
* Fixed issue with blocking voters option
* Fixed issue with in_array causing errors
* Fixed twig compatibility
* Added Print Votes page

= 5.6 =
* Fixed issue with login popup
* Fixed issue with vote button
* Fixed issue with html

= 5.5 =
* Fixed issue with clone poll
* Fixed issue with archive page
* Fixed issue with captcha

= 5.3 =
* Fixed issue with links color being overwritten
* Fixed issue with start date and end date not displaying corectly
* Fixed issue with widget
* Added email notifications customization per poll

= 5.2 =
* Complete new design
* Wizard to guide you when creating a poll
* You can now change the order answers are being displayed

= 4.9.3 =
* Fixed security issue. Many thanks to Antonio Sanchez for all his help.

= 4.9.2 =
* Fixed security issue

= 4.9.1 =
* Fixed issue with Template preview not working in IE8
* Fixed issue with wpautop filter
* Redefined admin area allowed tags: a(href, title, target), img( src, title), br
* Fixed issue with Other answers

= 4.9 =
* Added templates preview when adding/editing a poll
* Added sidebar scroll
* Typos fixes
* CSS and Javascript improvements
* Various bugs fixes

= 4.8 =
* Re-added ability to use html tags
* Added new tags: %POLL-SUCCESS-MSG% and %POLL-ERROR-MSG%
* Various bug fixes

= 4.7 =
* Fixed bug with Other answers. Html code is no longer allowed

= 4.6 =
* Added ability to send email notifications when a vote is recorded
* Various bug fixes

= 4.5 =
* Added ability to choose date format when displaying polls
* Added ability to limit viewing results only for logged in users
* Added ability to add custom answers to poll answers
* Added new shortcode [yop_poll id="-4"] that displays latest closed poll
* Added an offset for shortcodes. [yop_poll id="-1" offset="0"] displays the first active poll found, [yop_poll id="-1" offset="1"] displays the second one
* Added WPML compatibility
* Various bugs fixes

= 4.4 =
* Added ability to reset polls
* Added ability to to add a custom message to be displayed after voting
* Added ability to allow users to vote multiple times on the same poll
* Various bugs fixes

= 4.3 =
* Added multisite support
* Added ability to redirect to a custom url after voting
* Added ability to edit polls and templates author
* Added ability to set a response as default
* Improvements on View Results
* Added ability to edit number of votes (very usefull when migrating polls)
* Added tracking capabilities
* Various improvements on logs

= 4.2 =
* Added captcha
* Fixed issue with start date and end date when adding/editing a poll
* Fixed issue with the message displayed when editing a poll

= 4.1 =
* Fixed js issue causing the widget poll not to work

= 4.0 =
* Added ability to use custom loading animation
* Added capabilities and roles
* Fixed issue with update overwritting settings

= 3.9 =
* Fixed display issue with IE7 and IE8

= 3.8 =
* Fixed compatibility issue with Restore jQuery plugin
* Added ability to link poll answers

= 3.7 =
* Fixed issue with Loading text displayed above the polls
* Fixed issue with deleting answers from polls

= 3.6 =
* Fixed issue with missing files

= 3.5 =
* Added french language pack
* Added loading animation when vote button is clicked
* Fixed issue with characters encoding

= 3.4 =
* Fixed issue with menu items in admin area
* Fixed issue with language packs

= 3.3 =
* Added option to auto generate a page when a poll is created
* Fixed compatibility issues with IE
* Fixed issues with custom fields

= 3.2 =
* Fixed bug that was causing issues with TinyMCE Editor

= 3.1 =
* Various bugs fixed

= 3.0 =
* Added export ability for logs
* Added date filter option for logs
* Added option to view logs grouped by vote or by answer
* Various bugs fixed

= 2.0 =
* Fixed various bugs with templates

= 1.9 =
* Fixed various bugs with templates

= 1.8 =
* Fixed bug with wordpress editor

= 1.7 =
* Fixed bug that was causing poll not to update it's settings

= 1.6 =
* Added ability to change the text for Vote button
* Added ability to display the answers for Others field

= 1.5 =
* Fixed sort_answers_by_votes_asc_callback() bug

= 1.4 =
* Fixed compatibility issues with other plugins

= 1.3 =
* Fixed bug that was causing widgets text not to display

= 1.2 =
* Fixed do_shortcode() with missing argument bug

= 1.1 =
* Fixed call_user_func_array() bug
