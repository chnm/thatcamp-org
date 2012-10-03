=== Improved user search in backend ===
Contributors: blackbam
Tags: user, search, backend, user search
Requires at least: 3.0
Tested up to: 3.3.2
License: GPLv2
Stable tag: 1.2.3
 
Improves the search for users in the backend significantly: Search for first name, last, email and more of users instead of only nicename.

== Description ==

This Plugin improves the search for users in the WordPress backend significantly. It empowers admins to search for the first name, last name and email address of users instead of only their nicknames/nicenames. 

It is also possible to search for custom user meta fields now.

Plugin Homepage: http://www.blackbam.at/blackbams-blog/2011/06/27/wordpress-improved-user-search-first-name-last-name-email-in-backend/

== Installation ==

1. Upload the "improved-user-search-in-backend" directory into the /wp-content/plugins/ of WordPress or use the auto-installer.

2. Activate the Plugin through the Plugins page.

3. Maybe you want to change the defaults in Settings -> User Search.

4. Enjoy.

== Other notes ==

Special characters like quotes are escaped, problems with the query may apper when trying to search for other non-alphanumeric characters.

== Changelog ==

== 1.2.3 ==

- Database compatibility improved.

== 1.2.2 ==

- Re-allowed special characters in keys, but strings are still escaped for safe queries.

== 1.2.1 ==

- Filter and test custom meta fields. Remove unallowed characters.

== 1.2.0 ==

- Added Case-insensitive matching.
- Added Multisite support.

== 1.1.2 ==

Fix issue where attempting to activate the plugin would throw an "Invalid Header" error

== 1.1.1 ==

Minor changes.

== 1.1 ==

Added options page to include custom user meta fields in the search.

== 1.0.1 ==

Little code improvements.

= 1.0 =

Initial release to the Plugins directory.


