<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * AMU Intro Screen
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

/*
	* PLUGIN MAIN INTRO SCREEN
	* provides basic information and help links
*/

function add_multiple_users() {
	
	//test again for admin priviledges
	if (!current_user_can('manage_options') )  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	//test if disabled by superadmin
	if ( is_multisite() ) {
		if (get_site_option('amu_subadminaccess')) {
			if (get_site_option('amu_subadminaccess') == 'no') {
				if (!current_user_can('manage_network') )  {
					wp_die( __('Access to AMU functions have been disabled by the Network Administrator.') );
				}
			}
		}
	}
	
	//when accessing amu, set options if they don't exist
	amu_set_defaultoptions();
	?>
    
	<div class="wrap">
		<div id="amu">
		
			<h2><?php _e('Add Multiple Users','amulang'); ?></h2>
		
			<p><strong><?php _e('Add Multiple Users allows Administrators to bulk add user registrations to their WordPress site using a variety of tools.','amulang'); ?></strong><br />
			<?php _e('It is recommended that you modify your Settings before using one of the New User tools.','amulang'); ?></strong></p>
						
            <h3><?php _e('Functions provided by the Add Multiple Users plugin','amulang'); ?></h3>
            
            <p><strong><?php _e('Manual Entry','amulang'); ?></strong><br>
            <?php _e('Create a blank form of any size to manually input your new user information.','amulang'); ?></p>
            
            <p><strong><?php _e('Import CSV Data','amulang'); ?></strong><br>
            <?php _e('Import your CSV information and translate it into new user registrations.','amulang'); ?></p>
            
            <p><strong><?php _e('Import Email list','amulang'); ?></strong><br>
            <?php _e('Convert a list of email addresses into new user registrations.','amulang'); ?></p>
            
            <?php if(is_multisite()) { ?>
            
                <p><strong><?php _e('Add from Network','amulang'); ?></strong><br>
                <?php _e('Add existing users from a Multisite Network to this site.','amulang'); ?></p>
            
                <p><strong><?php _e('Network Options','amulang'); ?></strong><br>
                <?php _e('Modify network-wide options under the Users menu in your network dashboard.','amulang'); ?></p>
            
            <?php } ?>
            
            <p><span class="important"><?php _e('Please read the plugin information provided on each page regarding the use of each function.','amulang'); ?></span></p>
            
            <div class="infowrap">
            
                <h3><?php _e('Further Information','amulang'); ?></h3>
                
                <h4><?php _e('New Column and Field Naming Standard','amulang'); ?></h4>
                                
                <p><?php _e('Add Multiple Users now makes use of WordPress standard column names as field names in both the Form and Import interfaces. These column names match the "wp-user" and "wp_usermeta" tables in your site database. The only exceptions to this rule are the "role" column, which automatically creates User Levels and Capabilities based on the role you choose for each user, and "ignore", which tells the plugin to skip a column of data.','amulang'); ?></p>
                
                <h4><?php _e('Setting Roles and Capabilities of New Users','amulang'); ?></h4>
                
                <p><?php _e('Add Multiple Users now allows you to add users with roles you have defined yourself in your site. This plugin does not, however, create roles for you. Please create your custom roles using another plugin such as User Role Editor before attempting to add users with a custom role.','amulang'); ?></p>
                
                <ul>
                    <li><strong>&raquo; <?php _e('Settings and Form Interface','amulang'); ?>:</strong> <?php _e('roles can be selected from a drop-down list that contains all the roles currently active in your site.','amulang'); ?></li>
                    <li><strong>&raquo; <?php _e('CSV Importing','amulang'); ?>:</strong> <?php _e('make sure you define which column defines the role for each user. This column should be called "role".','amulang'); ?></li>
                </ul>
            
                <h4><?php _e('Plugin Help','amulang'); ?></h4>
                
                <p><?php _e('While the functionality of this plugin is designed to be as intuitive as possible, there are some rules that must be followed when using various functions. More information about each function is provided at the bottom of each page.','amulang'); ?></p>
                
                <h4><?php _e('WordPress and Browser Memory Issues when adding a large number of users','amulang'); ?></h4>
                
                <p><?php _e('Adding an exceptionally high number of users at any time is possible, however in the case of adding hundreds or even thousands of users at a time, depending on your server capacity, you may have to modify your wp-config file to turn off the time limit that can cause the adding function to time out before it is complete. Further information can be found on the plugin page at','amulang'); ?> <a href="http://addmultipleusers.happynuclear.com/" target="_blank">http://addmultipleusers.happynuclear.com/</a>.</p>
        
            </div>
        </div>
    </div>
<?php 
} 
?>