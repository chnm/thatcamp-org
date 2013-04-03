=== Add Multiple Users for WordPress ===
Contributors: happynuclear
Donate link: http://addmultipleusers.happynuclear.com/
Tags: users, registration, admin, multisite
Requires at least: 3.4
Tested up to: 3.4.1
Stable tag: trunk

Bulk add user registrations to a WordPress site using a variety of methods.

== Description ==

Add Multiple Users provides the ability for WordPress administrators to bulk register users using a range of tools available under 
the AMU tab in your WordPress administration section.

Functions include the ability to manually input new user information, convert CSV data to user information, and 
create users from a list of email addresses. Each new user registration can include almost all available standard user meta fields 
plus any additional meta fields you require.

In a WordPress Multisite installation, the plugin also allows the bulk adding of existing users from the Network to the specified
site that the plugin is accessed from, plus additional Network settings to restrict user functions and monitor user registrations
across your sites.

For more information please see the plugin page at http://addmultipleusers.happynuclear.com/

== Installation ==

Installing and activating this plugin adds an additional menu option to your menu in the WordPress administration
section named 'AMU'.

1. Upload 'add-multiple-users' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click the AMU tab in the left menu of your admin menu to access plugin functions

== Frequently Asked Questions ==

= What's new in version 2.0.0? =

Lots of things, mostly inspired by user feedback. More options for specifying column order on imported data, custom user role support, just about 
every meta data option you could need, plus a pretty big rewrite of most of the standard functionality.

= It seems like I have to wait a long time for registrations to process. What's going on? =

AMU is designed using only WordPress standard functions with no direct database insert code. This is a slower but safer option. As such, 
each user processes separately, running checks along the way to make sure there are no conflict with current users or any bad data. If you're 
adding lots of users, just be patient. If you get time-out errors, check out the next question.

= I'm trying to add lots of users at once and its not working! =

In testing, adding over 500 users at a time can cause issues with memory in WordPress and in your browser - please try the method explained at 
http://addmultipleusers.happynuclear.com under Usage - Known Issues for a few workarounds. The simplest 
method is simply to split your CSV data into groups of about 300 or so, if its feasible for you to do so.

= Does the plugin handle custom roles? =

Yes. Version 2.0.0 introduces the ability to choose from all Roles you have defined in your site. AMU does not, however, create Roles 
for you. You should use a plugin such as User Role Editor to first create the roles you wish to use when bulk adding users. You can then
use a 'role' column in your CSV data to specify a role for a user, pick a default role in the Settings, or pick one from the drop-down 
list on the Form interface.

= How does the plugin work in Multisite? =

Access the Add Multiple Users plugin from the any site within the network to use its functions. Users added in a subsite are added to the network 
and assigned a role for the subsite they were added through. If you're a Super Admin, you can also change some settings for your Network 
(look under the Users tab in Network Admin) to limit accessibility to the plugin from subsites, plus receive copies of user registration details 
when the plugin is used on any of your network sites.

= How does the CSV Import function work? =

These functions work by taking your data (whether you upload a file or paste data into the your CSV Input info or Email List functions) 
and translating it to user accounts. You can either send them all to a Form, which lets you review each line of user information 
piece by piece before adding the users, or simply just go ahead and register them using the Skip button.

= How does the Import Email List function work? =

This function automatically creates new user names from the first part of the email, generates a password, and uses this information with the 
provided email address to create a new user account. You can even send this information to the Form interface to add additional information
before processing your new user accounts.

= How do I specify the CSV column order of the data I want to import? =

In the Settings you can choose to predefine your column order, use the first line of your CSV file as the column order, or define it after 
uploading your file either with the Manual Entry on Upload or Dynamic Sorting on Upload functions.

For standard WordPress user and user_meta fields, you must use the exact name of the column in the database (eg user_login or display_name). 
To define a role, use the column name "role" to define where the user's role appears in a line of data. For custom user_meta information, 
just use any name that isn't standard and it will be added as a new meta field for each user.

If you want the plugin to skip a column of information, just use the column name "ignore" (multiple times if necessary) and the plugin will skip this 
column of information.

= What information per user is needed to register a new user on the site? =

A user_login, user_pass and user_email is the base necessity of adding a new user. Passwords can be generated in left blank, and emails addresses 
can be created for you if you use the Force Create Email option.

= How does the 'Force Fill Email Addresses' work exactly and why shouldn't I use it? =

Force Fill Email Addresses works by creating a fake email for users whose email address field is left blank, utilising
the new user's Username and a .fake extension to create a non-functional, dummy email address. This is NOT a 
function I recommend using unless absolutely necessary. User email addresses in WordPress are highly recommended if only 
to retrieve a password if it is forgotten. In a particular real-world scenario I found I had to add a user who had no email 
address due to their lower socio-economic circumstance and it was unrealistic to deny access to a potential user because 
of this as the site was targetted specifically for that demographic of user. The function is there if you
need it, but its not a recommended solution (try getting your users free Gmail accounts or something in the first instance!).

= Can I bulk invite users rather than automatically registering them? =

Not yet. This seems trickier than I hoped, and any suggestions on how to achieve this are more than welcome. I'm working on this for 
inclusion in version 2.1. However, this function does exist when using the Add Existing function (if you're running Multisite).

== Screenshots ==

1. The layout of the blank user input form.

== Changelog ==

= 2.0.0 =
* Most primary functions have been rewritten from the ground up.
* All user data and meta data values are now referenced by their column names in the WordPress data structure. The only exceptions 
to this are "role" which automatically creates meta data for User Level and Capabilities based on the role name given, and "ignore" which 
causes the plugin to skip the specified column of information.
* A new Network Options section has been added for sites running a Multisite network. These options govern the use of the 
plugin across sub-sites. Email addresses to receive copies of user registrations from any networked site can also be specified here.
* Additional column ordering functions are now available in the Settings section.
* The Dynamic Sorter has been given a reboot to simplify its usage.
* Additional settings added for standard user meta information when bulk adding new users. These options can now be enabled 
in the Settings section.
* Custom meta data fields may now be set in the Settings section and when importing CSV data.
* User Display Name can now be set as either the user's login, nickname, first or last name (or both). Default is userlogin.
* Custom Roles have been enabled. Default Role may now be chosen in the Settings section and within other interfaces.
* Locaalization, ready for translation
* And a few functionality tweaks

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