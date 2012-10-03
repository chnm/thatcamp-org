=== Add Multiple Users for WordPress ===
Contributors: happynuclear
Donate link: http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php
Tags: users, registration, admin, multisite
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: trunk

Provides an way to bulk add user registrations to a site using manual input, CSV and email data, and add existing users
from a Multisite Network.

== Description ==

Add Multiple Users provides the ability for WordPress administrators bulk add WordPress user registrations 
using a range of tools available as an extra admin page under your Users tab in the administration section.

Each new user can have a custom username, a customised or randomly generated password, an email address, first name, last
name and website data added upon registration. A range of settings allow the customisation of user notifications, validation 
checks and more.

Functions include the ability to manually input new user information, convert a CSV or Text file to user information, and 
create users from a list of email addresses.

In a WordPress Multisite installation, the plugin also allows the bulk adding of existing users from the Network to the specified
site that the plugin is accessed from.

For more information please see the plugin page at http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php

== Installation ==

Installing and activating this plugin adds an additional menu option to your Users menu in the WordPress administration
section named 'Add Multiple Users'.

1. Upload 'add_multiple_users' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click the Add Multiple Users link in the Users section to access the plugin interface

== Frequently Asked Questions ==

= I've used this plugin in the last version and now I'm confused... where did the input form go? =

The manual input form can now be accessed with the 'Blank Form' option in the menu. You can now specify a number of rows
instead of being restricted to the previous 50. This blank form now compliments the other functions that were added, such
as the CSV upload, paste, and email address list converter.

= Can I import users from a CSV or list of email addresses? =

Yes. Version 1.2.0 adds three new methods for adding users to your WordPress site - a CSV file upload, CSV manual entry text box, 
and the ability to generate user accounts purely from email addresses.

= I'm trying to add lots of users at once and its not working! =
Adding over 500 users at a time can cause issues with memory in WordPress and in your browser - please try the method explained at 
http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php under the Memory Issues heading.

= How does the plugin work in Multisite? =

Access the Add Multiple Users plugin from the networked site to use its functions. Users added in a site are added to the network 
and assigned a role for the site they were added through. At the moment you can't add directly to the Network list without assigning 
the user a role - but that's on the to-do list.

= How do the CSV Upload, CSV Input and Email List functions work? =

These functions work by taking your data (whether you upload a file or paste data into the your CSV Input info or Email List functions) 
and translating it, essentially creating a form like the 'Blank Form' but with the fields already filled in. You can then review 
that information, make changes, then submit the user info for processing.

= What information per user is needed to register a new user on the site? =

A unique username, password and email address is needed for each user. Passwords can be generated automatically if the
field is left blank. Usernames can be generated automatically if using the "Email List" function. Dummy email addresses 
can also be created using the Force Fill Email Addresses option if necessary.

= What other user information can I add when I register a new user? =

For each user you can also specify a First Name, Last Name and Website, just like the regular WordPress Add New User form.
These fields are optional. You can also set each new user's Role. 

= Will each user I register be sent their login info? =

The Settings has an option allowing you to enable or disable new user notification emails. You can also customise the content
of notification emails, but the default is pretty solid. By default users will be emailed their info. At no point will a force-
created email address be sent an email.

= How does the 'Force Fill Email Addresses' work exactly and why shouldn't I use it? =

Force Fill Email Addresses works by creating a fake email for users whose email address field is left blank, utilising
the new user's Username and a .fake extension to create a non-functional, dummy email address. This is NOT a 
function I recommend using unless absolutely necessary. User email addresses in WordPress are highly recommended if only 
to retrieve a password if it is forgotten. In a particular real-world scenario I found I had to add a user who had no email 
address due to their lower socio-economic circumstance and it was unrealistic to deny access to a potential user because 
of this as the site was targetted specifically for that demographic of user. The function is there if you
need it, but its not a recommended solution (try getting your users free Gmail accounts or something in the first instance!).

= Can I customise the text in the new user email confirmation? =

Yes. Version 1.2.0 adds to the Settings the ability to customise your new user notification email. The text in the email sent to 
users to confirm their adding to a site within a Multisite environment is not yet editable (uses WordPress standard text).

= Can I delete multiple user accounts using this plugin? =

You can already bulk delete users in the Users section of WordPress administration, so this is not planned for development.
I am thinking of a bulk edit of existing user data though, but its not the point of the plugin at this time and whole other 
kettle of fish.

== Screenshots ==

1. The layout of the blank user input form.

== Changelog ==

= 1.2.2 =
* This update adds a feature intended to combat an issue related to attempting to add over 500 users in a single pass. 
An additional function has been added that allow you to skip the creation of a User Information form from uploaded or pasted 
CSV files, and pasted Email List data, and proceed directly to adding the users. Adding this many users at once can cause issues, 
so please see the information at http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php if you run into trouble.

= 1.2.1 =
* This version fixes several compatibility issues with WordPress Multisite and includes one new Bulk Add function for Multisite environments
* Added: ability to bulk add existing users to a site from the Network users list (visible in Multisite only).
* Added: settings may now be reset to default using the Reset to Default Settings button on Settings page
* Fixed: users added from a Multisite sites are now properly assigned a Role for that site
* Fixed: settings for sites in Multisite installations are now created properly for each site
* Several page notices and user information have been updated

= 1.2.0 =
* Major restructure of plugin layout and functionality
* Added: ability to create custom number of manual input fields (now called Blank Form)
* Added: additional user rows may now be added to the form dynamically
* Added: CSV/TXT file upload and paste function to read user data and customise structure
* Added: function to convert string of email addresses to new user information
* Settings (previously General Options) now set separately to other functions
* Added: settings for customising new user notification email with Test option
* Plugin Information updated. Relevant tool information is now accessible directly with each function
* Style and notification updates

= 1.1.0 =
* Password strength notifications added for password fields that are not blank
* Validation error notice for usernames that include unsafe characters
* Strict sanitization option added to general options
* Improved error notification hover state
* General options are now saved on form submission
* Email validation bug fix

= 1.0.1 =
* Bug fixes.

= 1.0.0 =
* Initial release.