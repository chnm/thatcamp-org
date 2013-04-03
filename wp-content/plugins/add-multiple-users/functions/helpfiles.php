<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * Help Information
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

function showPluginInfo($infotype) {

	//show info on form fields
	if ($infotype == 'emaillisting') { ?>
    
		<div class="infosection">
            <h3><?php _e('Information about the Email List function','amulang'); ?></h3>
            <p><?php _e('This function takes a comma-delimited list of email addresses and converts them into new user information.','amulang'); ?></p>
            <p><?php _e('When adding email addresses to the field above, each address must be separated by a comma (,) character. Even if you put the next address on the next line, there should be a comma between each email address.','amulang'); ?></p>
            <p><strong><?php _e('Note','amulang'); ?>:</strong> <?php _e('All blank spaces are stripped from the email list (meaning spaces after commas are not necessary or detrimental, however email addresses that contain spaces will be compressed).','amulang'); ?></p>
                
            <p><strong><?php _e('Create User Information Form option','amulang'); ?></strong><br />
            <?php _e('This function takes your email address list and processes it to a Form interface where you may validate that all data is correct before adding the users. This function also allows you modify any data for each user before registering them. In addition, any standard or custom meta options you have specified in the Settings will be available to edit on this form.','amulang'); ?></p>
            
            <p><strong><?php _e('Skip Form and Add Users option','amulang'); ?></strong><br />
            <?php _e('This function sends your email list directly to the register function. Users will be immediately registered with only user_login, user_pass and user_email data. All other data will be automatically created by WordPress. If you wish to add all these users with a specific role, set the "Ignore individual User Role" option in the Settings to the role you wish to assign them.','amulang'); ?></p>
		</div>
		
	<?php 
	//show info on form fields
	} else if ($infotype == 'formfields') { ?>
    
		<div class="infosection">
			<h3><?php _e('Information on User Form Fields','amulang'); ?></h3>
			<p><strong>user_login:</strong><br />
				<?php _e('All new users must be given a unique user_login. Rows without a user_login are automatically skipped during the multiple registration process. User Logins cannot be changed once set. User_logins are automatically sanitized to strip out unsafe characters, but are only strictly sanitized if enabled in the Settings.','amulang'); ?></p>
			<p><strong>user_pass:</strong><br />
				<?php _e('The users login password. May be set for each user, or left blank to generate a random password for that user. For more information on password strength and security, please visit','amulang'); ?> <a href="http://codex.wordpress.org/Hardening_WordPress" target="_blank">http://codex.wordpress.org/Hardening_WordPress</a></p>
			<p><strong>user_email:</strong><br />
				<?php _e('A unique, valid email address for each user is required (or use Force Fill option in the Settings if emails are not available). Emails will be checked for uniqueness and, if enabled in the Settings, their validity as email addresses.','amulang'); ?></p>
			<p><strong>role:</strong><br />
				<?php _e('This list is automatically populated from the roles currently enabled on your site. Selection of role is ignored if Set All Roles in the Settings is set to override all role selections with a specific role.','amulang'); ?></p>
            <p><strong><?php _e('all other fields','amulang'); ?>:</strong><br />
				<?php _e('By default you will also see first_name and last_name for each user. These fields are optional. If you require additional standard or custom user meta options, enable them in the Settings before filling in your form.','amulang'); ?></p>
            <p><strong><?php _e('A note on user data','amulang'); ?>:</strong><br />
            	<?php _e('Several meta data options require specific values (such as "true" or "false" or a date written in the correct format) to correctly update. Only use additional fields if you know what you are doing. For more information on table column names, please see','amulang'); ?> <a href="http://codex.wordpress.org/Database_Description/3.3" target="_blank">http://codex.wordpress.org/Database_Description/3.3</a></p>
		</div>
	
	<?php
    //show upload file info
	} else if ($infotype == 'csvinputpage') { ?>
    
		<div class="infosection">
		<h3><?php _e('Information on Uploading CSV Information','amulang'); ?></h3>
		<p><?php _e('Choose either a .csv or .txt file to upload, or paste your CSV data in the text field. CSV information will be available on the next page for you to review and sort if necessary.','amulang'); ?></p>
		
		<h4><?php _e('CSV Column Names','amulang'); ?></h4>
			
            <p><?php _e('The Add Multiple Users plugin uses standard wp_user and wp_usermeta table column names for importing. In addition, the plugin recognizes the "role" column and an "ignore" column.','amulang'); ?></p>
            
			<p><strong><?php _e('Standard values are','amulang'); ?>:</strong></br>
				user_login, user_pass, user_email, user_nicename, user_url, user_registered, display_name, first_name, last_name, nickname, description, rich_editing, show_admin_bar_front, comment_shortcuts, admin_color, aim, yim, jabber</p>
			<p><strong>role:</strong></br>
				<?php _e('Use this column name to define the column that contains the user\'s role on the site. If you are using a custom role, create this role before adding users. Using this column name will create the necessary capabilities and user level for that user when they are registered.','amulang'); ?></p>
            <p><strong>ignore:</strong></br>
				<?php _e('You can use this column name (several times if necessary) to tell the plugin to skip over a certain column of information in your CSV data.','amulang'); ?></p>
			<p><strong><?php _e('Custom values','amulang'); ?>:</strong></br>
				<?php _e('Any column name you provide that does not match any of the above values will be assigned as an additional user meta data row.','amulang'); ?></p>
                
            <h4><?php _e('An example of a column order','amulang'); ?></h4>
            
            <p><?php _e('A single line of your CSV data may look like this','amulang'); ?>:<br />
            jeffk,kj478jR,jeffk@gmail.com,subscriber,Jeff,Keane,8798488934,879287958298398,false,fresh</p>
            
            <p><?php _e('Which in a real-world context relates to this order of data','amulang'); ?>:<br />
            <?php _e('Username, Password, Email Address, User Role, First Name, Last Name, Client Number, Credit Card Number, Show Admin Bar, Admin Theme','amulang'); ?></p>
            
            <p><?php _e('In this scenario, your column order (we ignore the CC number just for the sake of this example) should be','amulang'); ?>:<br />
            user_login,user_pass,user_email,role,first_name,last_name,client_number,ignore,show_admin_bar_front,admin_color</p>
            
            <p><?php _e('In this example a few things happen. First, we use standard WordPress table names for most standard values (ie. user_login). Second, where the user role is defined, we use the column name "role". Third, we use a non-standard value "client_number", which will be added as an additional user_meta field. Lastly, we use "ignore" in place of the credit card number column, because we do not want to import that data.','amulang'); ?></p>
            
            <h4><?php _e('Where to define your column order','amulang'); ?></h4>
            
            <p><?php _e('You can define your column order before or after importing your CSV data. There are four options in the Settings','amulang'); ?>:</p>
            <p><strong><?php _e('Dynamic Sorting on Import','amulang'); ?>:</strong> <?php _e('This provides a dynamic click and drag interface for choosing standard names and custom names after your data has been imported.','amulang'); ?></p>
            <p><strong><?php _e('Predefined Order','amulang'); ?>:</strong> <?php _e('You can predefine your column order using the "Predefined Below" option under the CSV Column Ordering subheading, and add your column order for use when importing data.','amulang'); ?></p>
            <p><strong><?php _e('First Line of CSV','amulang'); ?>:</strong> <?php _e('If your CSV data file has the column order as the first line of the CSV, choose the "Use First Line of CSV" option in the settings. Please make sure the column names in your CSV data adhere to the rules above.','amulang'); ?></p>
            <p><strong><?php _e('Manual Entry on Import','amulang'); ?>:</strong> <?php _e('This is the most basic option, simply providing a text box for you to type your column order into.','amulang'); ?></p>
		
		</div>
		
	<?php 
	//show add existing infor
	} else if ($infotype == 'addexistusers') { ?>
    
		<div class="infosection">
            <h3><?php _e('Information about Adding Existing Users','amulang'); ?></h3>
            <p><?php _e('On this page you will see a list of users taken from your Network list who are NOT already a part of this site. Firstly, set the two options as desired above the user list. You may then check the users you wish to add to this site and click the Add All Users button.','amulang'); ?></p>
            <h4><?php _e('Options for Adding Existing Users','amulang'); ?></h4>
            <p><strong><?php _e('Ignore individual roles and set all selected users to this role','amulang'); ?>:</strong> <br />
                <?php _e('You can assign each existing user you add to this site an individual Role within this site. Make a selection here if you want to add all existing users you choose with the Role defined here instead.','amulang'); ?></p>
            <p><strong><?php _e('Send each user a confimation email','amulang'); ?>:</strong> <br />
                <?php _e('If you leave this unchecked, users you select will be automatically added to this site. Check this option if you do not want this to happen. Instead, each user you select will be sent an email asking them to confirm their adding to this site. When they have confirmed, they will show up in the Users list for this site.','amulang'); ?></p>
		</div>
		
	<?php 
	//show column ordering instructions
	} else if ($infotype == 'dynamicsort') { ?>
		
		<div class="infosection">
		
			<h3><?php _e('How to use the Dynamic Sorter','amulang'); ?></h3>
			<p><?php _e('This function allows you to define how your CSV data is structured per line so that it can be read correctly by the plugin.','amulang'); ?></p>
			
			<h4><?php _e('Your Column Order','amulang'); ?></h4>
            <p><?php _e('This box shows the final order of your columns. Grab and drag column names around to reorder them if necessary. Drag and drop a column name outside of the box to remove it from the list.','amulang'); ?></p>
            
            <h4><?php _e('Add Standard Columm Names','amulang'); ?></h4>
			<p><?php _e('Click a column name to add it to the list. The item will disappear from the list and reappear if you remove it from the Column Order.','amulang'); ?></p>
            
            <h4><?php _e('Add Custom Column Names','amulang'); ?></h4>
			<p><?php _e('Type in a custom column name (lowercase, no spaces) and click the Add Custom Column button to add it to your Column Order. This should be used for non-standard user meta data you have in your CSV data.','amulang'); ?></p>
            
            <p><strong><?php _e('Ensure that you have read the information on Uploading CSV Information below before continuing.','amulang'); ?></strong></p>
		</div>
		
	<?php 
	//show settings info
	} else if ($infotype == 'settings') { ?>

	<div class="infosection">
		<h3><?php _e('Information about AMU Settings','amulang'); ?></h3>
		<div class="pluginfohide">
        	
			<h4><?php _e('Validation and Notifications','amulang'); ?></h4>
		
			<p><strong><?php _e('Send each new user a registration notification email?','amulang'); ?></strong>
			<br /><?php _e('If selected, automatically sends an email to each new registered user with the information provided in the Customise New User Notification Email settings. Users who have been added with a "forced" email address will not be emailed.','amulang'); ?></p>
			<p><strong><?php _e('Email me a complete list of new user account details?','amulang'); ?></strong>
			<br /><?php _e('Highly recommended. When you register multiple users using any of the plugin function, the results of your registrations will display on the screen. However, this information will not remain on the screen once you navigate away from the page. This option emails all new user information to your registered WordPress user account email.','amulang'); ?></p>
			<p><strong><?php _e('Validate entered user_email address format','amulang'); ?>:</strong>
			<br /><?php _e('This setting affects both the in-page form validation and the validity of email addresses during registration. Email addresses that are found to not be valid cause the user registration to fail. If you have trouble entering email addresses that you believe are valid, disable this option.','amulang'); ?> <a href="http://codex.wordpress.org/Function_Reference/is_email" target="_blank">http://codex.wordpress.org/Function_Reference/is_email</a>.</p>
			<p><strong><?php _e('Sanitize user_login using Strict method','amulang'); ?>:</strong>
			<br /><?php _e('Determines whether user_logins are sanitized with Strict method or not. Enabling this option disallows the use of many symbols that may be used in a user_login normally. Affects both the on-screen validation and sanitization of user_login during the registration process.','amulang'); ?> <a href="http://codex.wordpress.org/Function_Reference/sanitize_user" target="_blank">http://codex.wordpress.org/Function_Reference/sanitize_user</a></p>
			<p><strong><?php _e('Force Fill empty user_email addresses','amulang'); ?>:</strong>
			<br /><?php _e('Highly NOT recommended. This setting ignores empty user_email address fields that would normally cause that new user\'s registration to fail by creating a fake email address such as "temp_userlogin@temp_userlogin.fake". It is very much recommended that all new users have a valid email address, and this function should only be used in cases where you need to register new users that do not have active email accounts.','amulang'); ?></p>
            
            <h4><?php _e('New User Defaults and Overrides','amulang'); ?></h4>
            
			<p><strong><?php _e('Ignore individual User Role settings and set all new users to this role','amulang'); ?>:</strong>
			<br /><?php _e('Overrides any individual Role you select for each new and sets them to the role you choose here. This applies to all registration functions, overriding selections in the Manual Entry interface, CSV data importing, and can be used to set a default role for email list imports.','amulang'); ?></p>
			<p><strong><?php _e('Where not provided, set new user Display Name preferance as','amulang'); ?>:</strong>
			<br /><?php _e('If any user registered using the plugin does not have a display name specified (on either the Manual Entry form or in any imported data), the option here will be used to set it. If the data for your preference cannot be found, the plugin will fall back to user_login.','amulang'); ?></p>
			
			<h4><?php _e('CSV Column Ordering','amulang'); ?></h4>
            
			<p><?php _e('If you are importing users from a comma-separated values (CSV) file, you need to define in what order the data appears in the file so that the plugin knows which piece of data is used for what database field. Here you can predefine your column order or choose to define the order after uploading.','amulang'); ?></p>
			<p><strong><?php _e('Choose which method of column ordering you would like to use','amulang'); ?>:</strong>
			<br /><?php _e('The Dynamic Sorting on Import function and the Manual Entry on Import function allow you to define your column order after you import your CSV file. The Predefined Below option allows you to specify your column order in the Predefined Column Order box. The Use First Line of CSV function takes the first line of your CSV file to use as the column order for the CSV data.','amulang'); ?></p>
			<p><strong><?php _e('Important note on Column names','amulang'); ?>:</strong>
			<br /><?php _e('Your column names must match exactly the names of user data and user meta data in the WordPress database, with the exception of "role", where you just need to add the name of the custom role you have created. Column names that do not match existing WordPress values are saved as Custom user meta fields for each user.','amulang'); ?></p>
			
			<h4><?php _e('Manual Entry User Meta Data','amulang'); ?></h4>
			<p><strong><?php _e('Make additional WordPress Standard meta data fields available on Form interface','amulang'); ?>:</strong>
			<br /><?php _e('By default, the blank form shows only required fields (user_login, user_pass and user_email), the role option drop-down, and the user meta fields first_name and last_name. Enable the options here to display additional meta fields on the Manual Entry Form interface.','amulang'); ?></p>
			<p><strong><?php _e('Make additional custom meta fields available on Form interface','amulang'); ?>:</strong>
			<br /><?php _e('Add any additional user meta fields here that you wish to use on the Manual Entry Form interface. Add your custom meta options here, separated by commas. Your custom names should contain no spaces.','amulang'); ?></p>
			
			<h4><?php _e('Customise New User Notification Email','amulang'); ?></h4>
			<p><strong><?php _e('From/Reply Address','amulang'); ?>:</strong>
			<br /> <?php _e('By default, new users will see the From/Reply email address in their New User Notification email as the email address of the administrator that added them. You can change this email address by adding a different address here, such as a "no reply" email address. You may also use this email address in the email message using the [fromreply] shortcode.','amulang'); ?></p>
			<p><strong><?php _e('Site Login URL','amulang'); ?>:</strong>
			<br /> <?php _e('If you want to direct new users to a specific web address to log in, add the full URL here (including the http://). You may then add this to your email message using the [siteloginurl] shortcode. By default this setting is your main site URL.','amulang'); ?></p>
			<p><strong><?php _e('Email Subject','amulang'); ?>:</strong>
			<br /> <?php _e('This is your email subject line and can include any of the shortcodes to add additional information to the subject line.','amulang'); ?></p>
			<p><strong><?php _e('Email Message','amulang'); ?>:</strong>
			<br /> <?php _e('This is your main email content and must be written in HTML format using valid HTML tags (such as p and h1). Any HTML tag that can be understood by an email program can be used here. If you\'re not familiar with HTML markup, its probably best to stick to the default message, or you can play with it and use the Send Test Email button to send yourself an example notification email so you can check its formatting and content.','amulang'); ?></p>
			<p><strong><?php _e('Shortcodes','amulang'); ?>:</strong>
			<br /> <?php _e('The shortcodes [sitename] [siteurl] [siteloginurl] [username] [password] [useremail] [fromreply] can be used in the Email Subject and Email Message fields to add specific data to your user notification email. For example, if you want to add that specific user\'s password to the email, using the [password] shortcode will add the users newly created password in there. Use these shortcodes to structure your email body text as you require.','amulang'); ?></p>
			<p><strong><?php _e('Send Test Email','amulang'); ?>:</strong>
			<br /><?php _e('This sends an example New User Notification Email to your email address using the information you currently have in the settings fields. Note that this does not save your Settings - you must still click the Save Settings button to save your changes. This allows you to view the data and layout of the email that newly registered users will get when they are added to the site.','amulang'); ?></p>
		</div>
	</div>
    
	<?php 
	//show settings info
	} else if ($infotype == 'networksettings') { ?>

	<div class="infosection">
		<h3><?php _e('Information about Network Options','amulang'); ?></h3>
		<div class="pluginfohide">
			
			<p><strong><?php _e('Allow site administrators access to AMU plugin?','amulang'); ?></strong><br /><?php _e('By default, Administrators of network sites have full access to the Add Multiple Users plugin. Disable this option to remove all access to the plugin on networked sites. Super Administrators may still access the plugin on each site.','amulang'); ?></p>
			
			<p><strong><?php _e('Allow site administrators to add users from the Network users list?','amulang'); ?></strong><br /><?php _e('Administrators of network sites have access to the Add Existing function, which allows them to subscribe any user from the Network users list to their site. Disable this option if you wish to prevent site Admins from using this function.','amulang'); ?></p>
			
			<p><strong><?php _e('Email addresses to recieve copies of bulk registration details','amulang'); ?>:</strong><br /><?php _e('Leave blank to ignore this option. If you add email addresses in this field, these recipients will receive copies of bulk registration details when users are registered on any site within your network.','amulang'); ?></p>
			
		</div>
	</div>
	<?php	
	}
}

?>