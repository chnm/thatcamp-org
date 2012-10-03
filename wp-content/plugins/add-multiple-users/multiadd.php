<?php
/**
 * @package Add_Multiple_Users
 * @version 1.2.2
 */
/*
Plugin Name: Add Multiple Users
Plugin URI: http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php
Description: This plugin allows you to add multiple user accounts to your Wordpress blog using a range of tools. When activated you should see the Add Multiple Users link under your Users menu.
Version: 1.2.2
Author: HappyNuclear
Author URI: http://www.happynuclear.com
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

define('AMU_VERSION', '1.2.2');

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

function amu_menu() {
	// add the admin options page
	add_users_page('Add Multiple', 'Add Multiple Users', 'manage_options', 'addmultiple', 'add_multiple_users');
}

//coming in 1.2.3
//function amu_network_menu() {
//	// add the super admin options page
//	add_menu_page('Add Multiple Users Network Version', 'AMU Network', 'manage_network', 'addmultiple', 'amu_networksite');
//}

function on_screen_validation() {
	wp_enqueue_script( "field_validation", plugins_url( "field-validation.js", __FILE__ ), array( 'jquery' ) );
	wp_localize_script( "field_validation", "MySecureAjax", array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function multiadd_actions() {
	wp_enqueue_script( "multiadd_actions", plugins_url( "multiadd-actions.js", __FILE__ ), array( 'jquery' ) );
	wp_localize_script( "multiadd_actions", "MySecureAjax", array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function addmultiuser_style() {
	wp_register_style($handle = 'amu_css_style', $src = plugins_url('amustyle.css', __FILE__), $deps = array(), $ver = '1.2.1', $media = 'all');
    wp_enqueue_style('amu_css_style');
}

function set_default_options() {
	global $current_user, $wpdb;
    get_currentuserinfo();
	$defaultAdminEmail = $current_user->user_email;
	$sitelogurl = site_url();
	$defaultUserEmailHead = 'Your New User Account Information on [sitename]';
	$defaultUserEmailText = '<h1>You have been registered as a user on [sitename]</h1>
<p>You may now log into the site at [siteloginurl]</p>
<p>Your username is [username] and your password is [password]</p>
<p>Regards,<br>
[sitename] Admin</p>
<p>[siteurl]</p>';
	
	//update options
	update_option( 'amu_usernotify', 'yes' );
	update_option( 'amu_confirmation', 'yes' );
	update_option( 'amu_setallroles', 'notset' );
	update_option( 'amu_validatestrict', 'no' );
	update_option( 'amu_validatemail', 'yes' );
	update_option( 'amu_forcefill', 'no' );
	update_option( 'amu_defadminemail', $defaultAdminEmail );
	update_option( 'amu_siteloginurl', $sitelogurl );
	update_option( 'amu_useremailhead', $defaultUserEmailHead );
	update_option( 'amu_useremailtext', $defaultUserEmailText );
}

//coming in 1.2.3
//function amu_networksite() {
//	if (!current_user_can('manage_network') )  {
//		wp_die( __('You do not have sufficient permissions to access this page.') );
//	}	
//}

//MAIN FUNCTION
function add_multiple_users() {
	//test again for admin priviledges
	if (!current_user_can('manage_options') )  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	//when accessing amu, set options if they don't exist
	if(!get_option('amu_usernotify')) {
		set_default_options();
	}
	
	//globals for functions
	global $current_user, $wpdb;
    get_currentuserinfo();
	$thisUserEmail = $current_user->user_email;
	$thisBlogName = get_bloginfo('name');
	$thisBlogUrl = site_url();
	$thisLoginUrl = get_option('amu_siteloginurl');
	
	//begin wrap class
	echo '<div class="wrap">';
	echo '<div id="amu">';

	//if saving options...
	if ( isset($_POST['setgenopt'] ) ) {
		setGeneralOptions();
		echo '<div id="message" class="updated">';
			echo '<p><strong>Settings have been saved.</strong></p>';
		echo '</div>';
	}
	
	//if resetting settings
	if ( isset($_POST['resetsettings'] ) ) {
		set_default_options();
		echo '<div id="message" class="updated">';
			echo '<p><strong>Settings have been reset.</strong></p>';
		echo '</div>';
	}
	
	//if submitted
    if ( isset($_POST['addnewusers'] ) ) {
		//new users added message
		echo '<div id="message" class="updated">';
		echo '<p><strong>New User User Accounts Processed.</strong></p>';
		echo '</div>';
	}
	
	//SHOW ADDING FORM
	echo '<h2>Add Multiple Users</h2>';
	echo '<p><strong>It is recommended that you modify your Settings before using one of the New User tools.</strong><br />';
	echo '<span class="important">Please read the plugin information provided on each page regarding the use of each function.</span></p>';
	
	echo '<form method="post" enctype="multipart/form-data" class="amuform">';
	
	//show toolbar
	showToolbar();
	
	//if no post settings made
	if (empty($_POST)) {
		$infotype = 'general';
		showPluginInfo($infotype);
	}
	
	// <=========== CREATE NEW USER ACCOUNTS FROM CSV UPLOAD SKIPPER ==================================================>

    if ( isset($_POST['skiprun_csvprocess'] ) ) {
		
		if (isset($_POST['filecontents'])) {
			$file = $_POST['filecontents'];
			$pasteddata = parse_csv($file);
			$data_rev = reorder_csv($pasteddata);
		}
		if (isset($_POST['csvpastebox'])) {
			$file = $_POST['csvpastebox'];
			$pasteddata = parse_csv($file);
			$data_rev = reorder_csv($pasteddata);
		}
		if (isset($_POST['emailfillbox'])) {
			$rawEmailList = trim($_POST['emailfillbox']);
			$order = array('\r\n', '\n', '\r', ' ');
			$replace = '';
			$file = str_replace($order, $replace, $rawEmailList);
			$data_rev = explode(',',$file);
		}
		
		$skipcounter = 0;
		
		//GET GENERAL OPTION SETTINGS		
		$sendEmail = get_option( 'amu_usernotify');
		$yesConfirm = get_option('amu_confirmation');
		$setAllRoles = get_option('amu_setallroles');
		$validateStrict = get_option( 'amu_validatestrict');
		$validateEmail = get_option('amu_validatemail');
		$forceEmail = get_option('amu_forcefill');
		$userEmailSubject = get_option('amu_useremailhead');
		$userEmailMsg = get_option('amu_useremailtext');
		$userfromreply = get_option('amu_defadminemail');
		$confirmationStack = '';
		
		//information feedback
		echo '<div class="toolintro">';
			echo '<h3>New User Information Added</h3>';
			echo '<p><span class="important"><strong>Important:</strong> If you did not request this information to be emailed to you wish to save it for future reference, please copy the New User Information section below IMMEDIATELY and save it for your records. It will not be available again if you navigate away from this page.</span></p>';
			echo '<p><span class="important">If any of your users have not been added due to an error, please note these errors and try adding the users again.</span></p>';		
		echo '</div>';
		echo '<div class="regnotice">';
		echo '<h4>New User Account Registration Details:</h4>';

		foreach($data_rev as $dataline) {
			$skipcounter++;
			
			if (isset($_POST['emailfillbox'])) {
				$email = $dataline;
				$pos = strpos($email, '@');
				if($pos !== false) {
					$tempusername = substr($email, 0, $pos);
				} else {
					$tempusername = '';
				}
				$password = '';
				$userRole = '';
				$firstname = '';
				$lastname = '';
				$website = '';
				
			} else {			
				//get username/password/email/additional vars
				$tempusername = $dataline[0];
				$password = $dataline[1];
				$email = $dataline[2];
				$userRole = $dataline[3];
				$firstname = $dataline[4];
				$lastname = $dataline[5];
				$website = $dataline[6];
			}
			$emailGen = 'valid';
			
			//if username is not blank
			if ($tempusername != '') {
				
				//process username
				if ($validateStrict == 'yes') {
					$username = sanitize_user( $tempusername, true );
				} else {
					$username = sanitize_user( $tempusername, false );
				}
			
				//check if username exists
				if ( username_exists( $username ) ) {
					
					$fail_userexists = '<p class="amu_error"><strong>'.$skipcounter.'. Error:</strong> The user <strong>'.$username. '</strong> already exists or has already been added on a previous line. Please try adding this user again with a different username.</p>';
					$confirmationStack = $confirmationStack.$fail_userexists;
					echo $fail_userexists;
					
				//check if email exists
				} else if ( email_exists( $email ) ) {
					$fail_emailexists = '<p class="amu_error"><strong>'.$skipcounter.'. Error:</strong>: The email address entered <strong><'.$email.'></strong> for the user <strong>'.$username.'</strong> already exists or has already been added on a previous line. Please try adding this user again with a unique email.</p>';
					$confirmationStack = $confirmationStack.$fail_emailexists;
					echo $fail_emailexists;
				
				} else {
					
					//process password
					if ( $password == '' ) {
						//generate random password if blank
						$password = wp_generate_password();
					}
					
					//process email
					if ( $email == '' ) {
						
						if ( $forceEmail == 'no' ) {
							$fail_noemailadded = '<p class="amu_error"><strong>'.$skipcounter.'. Error:</strong>: No email address for the user <strong>'.$username.'</strong> was included. Please try adding this user again with a valid email address.</p>';
							$email = '';
							$confirmationStack = $confirmationStack.$fail_noemailadded;
							echo $fail_noemailadded;
						} else {
							//generate random email address
							$email = 'temp_'.$username.'@temp'.$username.'.fake';
							$emailGen = 'generated';
						 }
					} else {
						//validate entered password if set
						if ( $validateEmail == 'yes' ) {
							if ( !is_email( $email ) ) {
								$fail_emailnotvalid = '<p class="amu_error"><strong>'.$skipcounter.'. Error:</strong>: New user '.$username.'</strong> was not added because the email address provided <strong>'.$email.'</strong> for this user was not valid. Please try again using a valid email address or disable email verification.</p>';
								$email = '';
								$confirmationStack = $confirmationStack.$fail_emailnotvalid;
								echo $fail_emailnotvalid;
							}
						}
					}
					
					//VERIFY ALL DATA EXISTS THEN PROCESS
					if ( ( $username != '' ) && ( $password != '' ) && ( $email != '' ) ) {
						
						//confirmation of addition of user			
						$userSuccess = '<p>'.$skipcounter.'. <strong>Success!</strong> The user <strong>'.$username.'</strong> has been added. Email Address for this user is <strong>'.$email.'</strong>. Password for this user is <strong>'.$password.'</strong></p>';
						$confirmationStack = $confirmationStack.$userSuccess;
						echo $userSuccess;
						
						//create new wordpress user
						$newuser = wp_create_user( $username, $password, $email );
						$wp_user_object = new WP_User($newuser);
						
						//id of new user
						$newuserID = $wp_user_object->ID;
						
						//set user first name
						if ($firstname != '') {
							update_user_meta( $newuserID, 'first_name', $firstname );
						}
						
						//set user last name
						if ($lastname != '') {
							update_user_meta( $newuserID, 'last_name', $lastname );
						}
						
						//set user web site
						if ($website != '') {
							wp_update_user( array ('ID' => $newuserID, 'user_url' => $website) ) ;
						}
						
						//set user role
						//multisite compatibility added 1.2.1 
						if ( is_multisite() ) {
							if ($setAllRoles == 'notset') {
								add_existing_user_to_blog( array( 'user_id' => $newuserID, 'role' => $userRole ) );
							} else {
								add_existing_user_to_blog( array( 'user_id' => $newuserID, 'role' => $setAllRoles ) );
							}
						} else {
							if ($setAllRoles == 'notset') {
								$wp_user_object->set_role($userRole);
							} else {
								$wp_user_object->set_role($setAllRoles);
							}
						}
						
						//destroy user object
						unset($wp_user_object);

						//send password to new user?
						if (($sendEmail == 'yes') && ($emailGen == 'valid')) {
							//set up email
							$to = $email;
							//replace instances of shortcodes
							$emailkeywords = array('[sitename]', '[siteurl]', '[siteloginurl]', '[username]', '[password]', '[useremail]', '[fromreply]');
							$emailreplaces = array($thisBlogName, '<a href="'.$thisBlogUrl.'">'.$thisBlogUrl.'</a>','<a href="'.$thisLoginUrl.'">'.$thisLoginUrl.'</a>', $username, $password, $to, $thisUserEmail);
							$subject = str_replace($emailkeywords, $emailreplaces, $userEmailSubject);
							$message = str_replace($emailkeywords, $emailreplaces, $userEmailMsg);
							//create valid header
							$headers = 'From: '.$userfromreply.' <'.$userfromreply.'>' . "\r\n";
							//filter to create html email
							add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
							//send email
							wp_mail($to, $subject, $message, $headers);
						}

					} // end if user,email,pass is not blank

				} //end if username/email exists
				
			} else { 
				//was already added, move on..
			}
		} //end for loop
		
		//SEND CONFIRMATION EMAIL TO LOGGED IN USER
		if ($yesConfirm == 'yes') {
			//set up confirmation email
			$confirmTo = $thisUserEmail;
			$confirmSubject = 'New User Account Information for '.$thisBlogName;
			$confirmMessage = '<p><strong>This email is to confirm new user accounts for your website generated using the Add Multiple Users plugin.</strong></p>
			<p>All errors have also been included for reference when re-entering failed registrations.</p>
			'.$confirmationStack.'
			<p><strong>End of message.</strong></p>';
			
			$confirmHeaders = 'From: '.$thisUserEmail.' <'.$thisUserEmail.'>' . "\r\n";
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			wp_mail($confirmTo, $confirmSubject, $confirmMessage, $confirmHeaders);
			echo '<p class="important">This information has been emailed to your WordPress administrator email <'.$thisUserEmail.'> as requested.</span></p>';
		}
			
		//on-screen confirmation
		if ($sendEmail == 'yes') {
			echo '<p  class="important">All users have been sent their login information (except where random emails were force-created).</span></p>';
		}
		echo '</div>';
	}
	
	// <=========== CREATE NEW USER ACCOUNTS ===============================================================>
	
    if ( isset($_POST['addnewusers'] ) ) {
		
		//GET GENERAL OPTION SETTINGS		
		$sendEmail = get_option( 'amu_usernotify');
		$yesConfirm = get_option('amu_confirmation');
		$setAllRoles = get_option('amu_setallroles');
		$validateStrict = get_option( 'amu_validatestrict');
		$validateEmail = get_option('amu_validatemail');
		$forceEmail = get_option('amu_forcefill');
		$procs = $_POST['processes'];		
		$userEmailSubject = get_option('amu_useremailhead');
		$userEmailMsg = get_option('amu_useremailtext');
		$userfromreply = get_option('amu_defadminemail');
		$confirmationStack = '';
		
		//information feedback
		echo '<div class="toolintro">';
			echo '<h3>New User Information Added</h3>';
			echo '<p><span class="important"><strong>Important:</strong> If you did not request this information to be emailed to you wish to save it for future reference, please copy the New User Information section below IMMEDIATELY and save it for your records. It will not be available again if you navigate away from this page.</span></p>';
			echo '<p><span class="important">If any of your users have not been added due to an error, please note these errors and try adding the users again.</span></p>';		
		echo '</div>';
		echo '<div class="regnotice">';
		echo '<h4>New User Account Registration Details:</h4>';
		
		//run through registration lines by email
		for ( $icounter = 1; $icounter <= $procs; $icounter += 1 ) {
			
			//get username/password/email/additional vars
			$tempusername = trim($_POST['username'.$icounter]);
			$password = trim($_POST['password'.$icounter]);
			$email = trim($_POST['email'.$icounter]);
			$userRole = $_POST['roleSetter'.$icounter];
			$firstname = $_POST['firstname'.$icounter];
			$lastname = $_POST['lastname'.$icounter];
			$website = $_POST['website'.$icounter];
			$emailGen = 'valid';
			
			//if username is not blank
			if ($tempusername != '') {
				
				//process username
				if ($validateStrict == 'yes') {
					$username = sanitize_user( $tempusername, true );
				} else {
					$username = sanitize_user( $tempusername, false );
				}
				
				//check if username exists
				if ( username_exists( $username ) ) {
					
					$fail_userexists = '<p class="amu_error"><strong>'.$icounter.'. Error:</strong> The user <strong>'.$username. '</strong> already exists. Please try adding this user again with a different username.</p>';
					$confirmationStack = $confirmationStack.$fail_userexists;
					echo $fail_userexists;
					
				//check if email exists
				} else if ( email_exists( $email ) ) {
					$fail_emailexists = '<p class="amu_error"><strong>'.$icounter.'. Error:</strong>: The email address entered <strong><'.$email.'></strong> for the user <strong>'.$username.'</strong> already exists. Please try adding this user again with a unique email.</p>';
					$confirmationStack = $confirmationStack.$fail_emailexists;
					echo $fail_emailexists;
				
				} else {
					
					//process password
					if ( $password == '' ) {
						//generate random password if blank
						$password = wp_generate_password();
					}
					
					//process email
					if ( $email == '' ) {
						
						if ( $forceEmail == 'no' ) {
							$fail_noemailadded = '<p class="amu_error"><strong>'.$icounter.'. Error:</strong>: No email address for the user <strong>'.$username.'</strong> was included. Please try adding this user again with a valid email address.</p>';
							$email = '';
							$confirmationStack = $confirmationStack.$fail_noemailadded;
							echo $fail_noemailadded;
						} else {
							//generate random email address
							$email = 'temp_'.$username.'@temp'.$username.'.fake';
							$emailGen = 'generated';
						 }
					} else {
						//validate entered password if set
						if ( $validateEmail == 'yes' ) {
							if ( !is_email( $email ) ) {
								$fail_emailnotvalid = '<p class="amu_error"><strong>'.$icounter.'. Error:</strong>: New user '.$username.'</strong> was not added because the email address provided <strong>'.$email.'</strong> for this user was not valid. Please try again using a valid email address or disable email verification.</p>';
								$email = '';
								$confirmationStack = $confirmationStack.$fail_emailnotvalid;
								echo $fail_emailnotvalid;
							}
						}
					}
					
					//VERIFY ALL DATA EXISTS THEN PROCESS
					if ( ( $username != '' ) && ( $password != '' ) && ( $email != '' ) ) {
						
						//confirmation of addition of user			
						$userSuccess = '<p>'.$icounter.'. <strong>Success!</strong> The user <strong>'.$username.'</strong> has been added. Email Address for this user is <strong>'.$email.'</strong>. Password for this user is <strong>'.$password.'</strong></p>';
						$confirmationStack = $confirmationStack.$userSuccess;
						echo $userSuccess;
						
						//create new wordpress user
						$newuser = wp_create_user( $username, $password, $email );
						$wp_user_object = new WP_User($newuser);
						
						//id of new user
						$newuserID = $wp_user_object->ID;
						
						//set user first name
						if ($firstname != '') {
							update_user_meta( $newuserID, 'first_name', $firstname );
						}
						
						//set user last name
						if ($lastname != '') {
							update_user_meta( $newuserID, 'last_name', $lastname );
						}
						
						//set user web site
						if ($website != '') {
							wp_update_user( array ('ID' => $newuserID, 'user_url' => $website) ) ;
						}
						
						//set user role
						//multisite compatibility added 1.2.1 
						if ( is_multisite() ) {
							if ($setAllRoles == 'notset') {
								add_existing_user_to_blog( array( 'user_id' => $newuserID, 'role' => $userRole ) );
							} else {
								add_existing_user_to_blog( array( 'user_id' => $newuserID, 'role' => $setAllRoles ) );
							}
						} else {
							if ($setAllRoles == 'notset') {
								$wp_user_object->set_role($userRole);
							} else {
								$wp_user_object->set_role($setAllRoles);
							}
						}
						//destroy user object
						unset($wp_user_object);

						//send password to new user?
						if (($sendEmail == 'yes') && ($emailGen == 'valid')) {
							//set up email
							$to = $email;
							//replace instances of shortcodes
							$emailkeywords = array('[sitename]', '[siteurl]', '[siteloginurl]', '[username]', '[password]', '[useremail]', '[fromreply]');
							$emailreplaces = array($thisBlogName, '<a href="'.$thisBlogUrl.'">'.$thisBlogUrl.'</a>','<a href="'.$thisLoginUrl.'">'.$thisLoginUrl.'</a>', $username, $password, $to, $thisUserEmail);
							$subject = str_replace($emailkeywords, $emailreplaces, $userEmailSubject);
							$message = str_replace($emailkeywords, $emailreplaces, $userEmailMsg);
							//create valid header
							$headers = 'From: '.$userfromreply.' <'.$userfromreply.'>' . "\r\n";
							//filter to create html email
							add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
							//send email
							wp_mail($to, $subject, $message, $headers);
						}
					}
				}
				
			} else { 
			//no username entered in this line, skip and move on
			}
		}
		
		//SEND CONFIRMATION EMAIL TO LOGGED IN USER
		if ($yesConfirm == 'yes') {
			//set up confirmation email
			$confirmTo = $thisUserEmail;
			$confirmSubject = 'New User Account Information for '.$thisBlogName;
			$confirmMessage = '<p><strong>This email is to confirm new user accounts for your website generated using the Add Multiple Users plugin.</strong></p>
			<p>All errors have also been included for reference when re-entering failed registrations.</p>
			'.$confirmationStack.'
			<p><strong>End of message.</strong></p>';
			
			$confirmHeaders = 'From: '.$thisUserEmail.' <'.$thisUserEmail.'>' . "\r\n";
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			wp_mail($confirmTo, $confirmSubject, $confirmMessage, $confirmHeaders);
			echo '<p class="important">This information has been emailed to your WordPress administrator email <'.$thisUserEmail.'> as requested.</span></p>';
		}
			
		//on-screen confirmation
		if ($sendEmail == 'yes') {
			echo '<p  class="important">All users have been sent their login information (except where random emails were force-created).</span></p>';
		}
		echo '</div>';
	}
	
	
	
	// <=========== OPEN SETTINGS ===========================================================================>
	if ( isset($_POST['openg_genopt'] ) || isset($_POST['setgenopt'] ) || isset($_POST['resetsettings'] )) {
		//show options
		addGeneralOptions();
		$infotype = 'settings';
		showPluginInfo($infotype);
	} else {
		//general options are added in the background for reference by js
		echo '<div class="hidegenopt">';
			addGeneralOptions();
		echo '</div>';
	}
	
	// <=========== REDISTRIBUTE PROCESSES ==================================================================>
	//added 1.2.2
	//selected from toolbar
	if ( isset($_POST['redistribute'] ) ) {
		
		echo '<h2>Redistribute Users</h2>';
		
		echo '<p>Add Function Here</p>';
	}
	
	
	// <=========== ADD EXISTING PROCESSES ==================================================================>
	//added 1.2.1
	//selected from toolbar
	if ( isset($_POST['add_existing'] ) ) {
		$userListError = '';
		getUserList($userListError);
		
		//get this blogs id
		global $blog_id;
		$mainsite = SITE_ID_CURRENT_SITE;
		$check_capabilities = 'wp_'.$blog_id.'_capabilities';		
		$lastuser = '';
		$usertotal = 0;
		
		//show users list
		$allusers = $wpdb->get_results( "SELECT ID, user_login, user_email FROM $wpdb->users ORDER BY ID ASC");
		if ($wpdb->num_rows == 0) {
			echo '<p>You have no available users to add.</p>';
		} else {
			//show multisite options wrapped in genoption
			echo '<div class="genoptionwrap">';
			
			//set all users to this role?
			echo '<div class="optionbox">';
			echo '	<label for="existingToRole">Ignore individual roles and set all selected users to this role: </label>';
			echo '	<select name="existingToRole" id="existingToRole">';
			echo '		<option value="notset" selected="selected">no, set individually...</option>';
			echo '		<option value="subscriber">subscriber</option>';
			echo '		<option value="contributor">contributor</option>';
			echo '		<option value="author">author</option>';
			echo '		<option value="editor">editor</option>';
			echo '		<option value="administrator">administrator</option>';
			echo '	</select>';
			echo '</div>';
			
			//username strict validation option...
			echo '<div class="optionbox lastoption">';
			echo '	<label for="notifyExistingUser">Send each user a confirmation email? <span class="important">(if selected, sends user standard WordPress confirmation email)</span></label>';
			echo '	<input name="notifyExistingUser" id="notifyExistingUser" type="checkbox" value="sendnotification" />';
			echo '</div>';
			
			//end multisite options wrap
			echo '</div>';
			
			echo '<div class="toolintro">';
			echo '	<p><strong>Select network users to add to this site:</strong></p>';
			echo '</div>';
			
			//start fieldset wrap
			echo '<div class="fieldsetwrap">';
			
			//show user rows
			foreach ( $allusers as $user ) {
				//if on main site
				if($blog_id == $mainsite) {
					if(!get_user_meta($user->ID, 'wp_capabilities')) {
						echo '<div class="userline">';
						echo '	<input name="adduser_'.$user->ID.'" id="adduser_'.$user->ID.'" type="checkbox" value="userchecked" />';
						echo '	<label for="adduser_'.$user->ID.'"><span class="eu_userid"><strong>User ID:</strong> '.$user->ID.'</span><span class="eu_userlogin"><strong>User Login:</strong> '.$user->user_login.'</span><span class="eu_useremail"><strong>User Email:</strong> '.$user->user_email.'</span></label>';
						echo '	<select name="setrole_'.$user->ID.'" id="setrole_'.$user->ID.'">';
						echo '		<option value="subscriber" selected="selected">subscriber</option>';
						echo '		<option value="contributor">contributor</option>';
						echo '		<option value="author">author</option>';
						echo '		<option value="editor">editor</option>';
						echo '		<option value="administrator">administrator</option>';
						echo '	</select>';
						echo '</div>';
						$lastuser = $user->ID;
						$usertotal++;
					}
				} else {
					//if on subsite
					if(!get_user_meta($user->ID, $check_capabilities)) {
						echo '<div class="userline">';
						echo '	<input name="adduser_'.$user->ID.'" id="adduser_'.$user->ID.'" type="checkbox" value="userchecked" />';
						echo '	<label for="adduser_'.$user->ID.'"><span class="eu_userid"><strong>User ID:</strong> '.$user->ID.'</span><span class="eu_userlogin"><strong>User Login:</strong> '.$user->user_login.'</span><span class="eu_useremail"><strong>User Email:</strong> '.$user->user_email.'</span></label>';
						echo '	<select name="setrole_'.$user->ID.'" id="setrole_'.$user->ID.'">';
						echo '		<option value="subscriber" selected="selected">subscriber</option>';
						echo '		<option value="contributor">contributor</option>';
						echo '		<option value="author">author</option>';
						echo '		<option value="editor">editor</option>';
						echo '		<option value="administrator">administrator</option>';
						echo '	</select>';
						echo '</div>';
						$lastuser = $user->ID;
						$usertotal++;
					}
				}
				
			}
			if($usertotal == 0) {
				echo '</div>';
				echo '<div class="toolintro">';
				echo '<p class="amu_error">All users on your Network are already assigned a role on this site.</p>';
				echo '</div>';
			} else {
				echo '<input type="hidden" readonly="readonly" name="existprocs" id="existprocs" value="'.$lastuser.'" />';
				echo '</div>';
				//show add button
				echo '<div class="buttonline">';
					echo '	<input type="submit" name="addexistingusers" class="button-primary" value="Add All Users" />';
				echo '</div>';
			}
		}
		$infotype = 'addexistusers';
		showPluginInfo($infotype);
	}
	if ( isset($_POST['addexistingusers'] ) ) {
		global $blog_id;
		//times it should loop based on highest user's id
		$existing_procs = intval($_POST['existprocs']);
		
		//set overall role value
		$allExistingToRole = $_POST['existingToRole'];
		
		echo '<div class="toolintro">';
		echo '<h3>New Users Added</h3>';	
		echo '</div>';
		
		echo '<div class="regnotice">';
		echo '<h4>New User Account Registration Details:</h4>';
		
		//run loop as many times as necessary
		for ($i = 1; $i <= $existing_procs; $i++) {
    		if(isset($_POST['adduser_'.$i])) {
				//send confirmation if set...
				if (isset($_POST['notifyExistingUser']) ) {
					//get user's info
					$user_details = get_userdata($i);
					if($allExistingToRole == 'notset') {
						$thisAddRole = $_POST['setrole_'.$i];
					} else {
						$thisAddRole = $_POST['existingToRole'];
					}				
					$newuser_key = substr( md5( $blog_id.$user_details->ID ), 0, 10 );
					add_option( 'new_user_' . $newuser_key, array( 'user_id' => $user_details->ID, 'email' => $user_details->user_email, 'role' => $thisAddRole ) );
					$message = __( 'Hi,
	
You\'ve been invited to join \'%1$s\' at
%2$s with the role of %3$s.

Please click the following link to confirm the invite:
%4$s' );
					wp_mail( $user_details->user_email, sprintf( __( '[%s] Joining confirmation' ), get_option( 'blogname' ) ),  sprintf($message, get_option('blogname'), site_url(), $thisAddRole, site_url("/newbloguser/$newuser_key/")));
					//notification line
					echo '<p>User '.$user_details->user_login.' has been sent a confirmation email.</p>';
					
				} else {
					$user_details = get_userdata($i);
					//process user addition
					if($allExistingToRole == 'notset') {
						add_existing_user_to_blog( array( 'user_id' => $i, 'role' => $_POST['setrole_'.$i] ) );
					} else {
						add_existing_user_to_blog( array( 'user_id' => $i, 'role' => $_POST['existingToRole'] ) );
					}
					//notification line
					echo '<p>User '.$user_details->user_login.' has been added to the site.</p>';
				}
			}
		}
		echo '</div>';
	}
	
	// <=========== MANUAL INPUT PROCESSES ==================================================================>
	//selected from toolbar
	if ( isset($_POST['input_manual'] ) ) {
		$manualInputError = '';
		generateManualForm($manualInputError);
	}
	//defining number of rows required
	if ( isset($_POST['formshow_manual'] ) ) {
		//get number of lines
		$manprocs = $_POST['manualprocs'];
		//confirm post is not empty, is a number and is larger than zero
		if ($manprocs !== '' && ctype_digit($manprocs) && $manprocs > 0) {
			echo '<div class="toolintro">';
			echo '	<h3>Add New Users by Manual Input</h3>';
			echo '	<p>Please enter your new user information in the form below and click the <strong>Add All Users</strong> button to process your new user registrations.</p>';
			echo '</div>';
			
			//write form
			echo '<div class="buttonline addextrasbutton">';
			echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
			echo '	<input type="reset" name="clearform" class="button-primary formresetter" value="Reset All Fields" />';
			echo '	<input type="submit" name="input_manual" class="button-primary" value="Make New Form" />';
			echo '</div>';
			echo '<div class="fieldsetwrap">';
				for ( $mancounter = 1; $mancounter <= $manprocs; $mancounter += 1 ) {
					formLinePrinter($mancounter, '', '', '', '', '', '', '');
				}
				$mancounter -= 1;
				echo '	<input type="hidden" readonly="readonly" name="processes" id="processes" value="'.$mancounter.'" />';
			echo '</div>';
			
			echo '<div class="buttonline addextrasbutton">';
			echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
			echo '	<input type="reset" name="clearform" class="button-primary formresetter" value="Reset All Fields" />';
			echo '	<input type="submit" name="input_manual" class="button-primary" value="Make New Form" />';
			echo '</div>';
			$infotype = 'formfields';
		showPluginInfo($infotype);
		} else {
			$manualInputError = '<p class="amu_error">Error: either the number you entered was zero, empty, or a non-numeric character was entered. Please try again.</p>';
			generateManualForm($manualInputError);
		}
	}
	
	// <=========== CSV/TXT UPLOAD PROCESSES ================================================================>
	//select from toolbar
	if ( isset($_POST['upload_csvtxt'] ) ) {
		$uploadFileError = '';
		functionUploadFile($uploadFileError);
		$infotype = 'uploadfile';
		showPluginInfo($infotype);
	}
	
	//add file text to textbox
	if ( isset($_POST['formshow_csvupload'] ) ) {
		//set ini for mac-created files
		ini_set('auto_detect_line_endings',true);
		if (is_uploaded_file($_FILES['csvuploader']['tmp_name'])) {
			$allowedExtensions = array("txt","TXT","csv","CSV");
			if (!in_array(end(explode(".", strtolower($_FILES['csvuploader']['name']))), $allowedExtensions)) {
				$uploadFileError = '<p class="amu_error">Error: Not a valid file type! Only .csv and .txt files may be uploaded.</p>';
				functionUploadFile($uploadFileError);
				$infotype = 'uploadfile';
				showPluginInfo($infotype);
      		} else { 
				//SUCCESS
				$thefiledata = file_get_contents($_FILES['csvuploader']['tmp_name']);
				$linecount = count(file($_FILES['csvuploader']['tmp_name']));
				echo '<div class="toolintro">';
				echo '<h3>Add New Users by CSV Upload</h3>';
				echo '<p><strong>The following user information has been extracted from your uploaded file. Total entries found in this document: '.$linecount.'</strong></p>';
				echo '<p>Please review your extracted CSV data in the text field below and, if necessary, customize the column order appropriate to your CSV data structure (see Custom Column Order information below).</p>';
				echo '</div>';
				addFileSort();
				echo '<div class="formline">';
				echo '	<textarea name="filecontents" cols="50" rows="10" id="filecontents" class="textfillbox">'.$thefiledata.'</textarea>';
				echo '</div>';
				echo '<div class="formline">';
				echo '	<p>Click the <strong>Create User Information Form</strong> button below to convert this user information into a form to customise specific information. Alternatively, choose the <strong>Skip Form and Add Users</strong> option if you want to immediately add the users (duplicate entries will be skipped if they are found).</p>';
				echo '	<p><strong>Important:</strong> If you are adding more than 100 users in one pass, it is recommended you use the Skip option, as you will likely exceed your PHP memory limit. Please see the information at the bottom of the screen for more info.</p>';
				echo '</div>';
				echo '<div class="buttonline">';
				echo '	<input type="submit" name="formshow_csvprocess" id="formshow_csvprocess" class="button-primary" value="Create User Information Form" />';
				echo '	<input type="submit" name="skiprun_csvprocess" id="skiprun_csvprocess" class="button-primary" value="Skip Form and Add Users" />';
				echo '</div>';
				$infotype = 'ordercolumns';
				showPluginInfo($infotype);
				showMemLimitInfo();
			}
		} else {
			$uploadFileError = '<p class="amu_error">Error: Either you did not select a file to upload or there was an error getting the file! Please try again.</p>';
			functionUploadFile($uploadFileError);
			$infotype = 'uploadfile';
			showPluginInfo($infotype);
			showMemLimitInfo();
		}
	}
	//process into form
	if ( isset($_POST['formshow_csvprocess'] ) ) {
		
		$file = $_POST['filecontents'];
		$pasteddata = parse_csv($file);
		$data_rev = reorder_csv($pasteddata);
		$counter = 0;
		
		echo '<div class="toolintro">';
			echo '<h3>Add New Users by CSV Upload</h3>';
			echo '<p>Please review the user information below to correct any errors or to add additional information for each user and click the </strong>Add All Users</strong> button to process your new user accounts.</p>';
		echo '</div>';
		
		echo '<div class="buttonline">';
		echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
		echo '</div>';
		echo '<div class="fieldsetwrap">';
			foreach($data_rev as $dataline) {
				$counter++;
				formLinePrinter($counter, $dataline[0], $dataline[1], $dataline[2], $dataline[3], $dataline[4], $dataline[5], $dataline[6]);
			}
			echo '<input type="hidden" readonly="readonly" name="processes" id="processes" value="'.$counter.'" />';
		echo '</div>';
		
		//add submit button
		echo '<div class="buttonline addextrasbutton">';
		echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
		echo '</div>';
		
		$infotype = 'formfields';
		showPluginInfo($infotype);
		
	}
	
	// <=========== CSV/TXT MANUAL PROCESSES ================================================================>
	
	//select from toolbar
	if ( isset($_POST['input_csvbox'] ) ) {
		
		$csvinputerror = '';
		addCSVInput($csvinputerror);
		$infotype = 'pastecsvtext';
		showPluginInfo($infotype);
		$infotype = 'ordercolumns';
		showPluginInfo($infotype);
		showMemLimitInfo();
	}
	
	//process text input from csv paste
	if ( isset($_POST['formshow_csvpaste'] ) ) {
		//get contents of csv input field
		$file = $_POST['csvpastebox'];
		//generate error if empty
		if($file == '') {
			$csvinputerror = '<p class="amu_error">No data was found in the CSV input field. Please try again!</p>';
			addCSVInput($csvinputerror);
			$infotype = 'pastecsvtext';
			showPluginInfo($infotype);
			$infotype = 'ordercolumns';
			showPluginInfo($infotype);
		} else {
			//SUCCESS
			echo '<div class="toolintro">';
				echo '<h3>Add New Users by CSV Input</h3>';
				echo '<p>Please review the user information below to correct any errors or to add additional information for each user and click the <strong>Add All Users</strong> button to process your new user accounts.</p>';
			echo '</div>';
			
			$pasteddata = parse_csv($file);
			$data_rev = reorder_csv($pasteddata);
			$counter = 0;
			echo '<div class="buttonline">';
			echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
			echo '</div>';
			echo '<div class="fieldsetwrap">';
				foreach($data_rev as $dataline) {
					$counter++;
					formLinePrinter($counter, $dataline[0], $dataline[1], $dataline[2], $dataline[3], $dataline[4], $dataline[5], $dataline[6]);	
				}
				echo '	<input type="hidden" readonly="readonly" name="processes" id="processes" value="'.$counter.'" />';
			echo '</div>';
			
			echo '<div class="buttonline addextrasbutton">';
			echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
			echo '</div>';
			$infotype = 'formfields';
			showPluginInfo($infotype);
		}
	}
	
	// <=========== EMAIL LIST PROCESSES ====================================================================>
	
	//select from toolbar
	if ( isset($_POST['input_email'] ) ) {
			$emailListError = '';
			addByEmailList($emailListError);
			$infotype = 'emaillisting';
			showPluginInfo($infotype);
			showMemLimitInfo();
	}
	
	//process text input into form lines
	if ( isset($_POST['formshow_email'] ) ) {
		//clean up input
		$rawEmailList = trim($_POST['emailfillbox']);
		$order = array('\r\n', '\n', '\r', ' ');
		$replace = '';
		$cleanEmailList = str_replace($order, $replace, $rawEmailList);
		
		//if array is not empty
		if($cleanEmailList !== '') {
			$verifySymbol = strpos($cleanEmailList, '@');
			if ($verifySymbol == false) {
				$emailListError = '<p class="amu_error">Error: No valid email addresses were found in your input data! Please try again.</p>';
				addByEmailList($emailListError);
			} else {
				//SUCCESS
				echo '<div class="toolintro">';
				echo '<h3>Add New Users by Email List</h3>';
				echo '<p><strong>Your email list was successfully converted to user listings.</strong></p>';
				echo '<p>Please review the user information below to correct any errors or to add additional information for each user and click the <strong>Add All Users</strong> button to process your new user accounts.</p>';
				echo '</div>';
				
				//create array
				$emailListArray = explode(',',$cleanEmailList);
				$counter = 0;
				echo '<div class="buttonline">';
				echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
				echo '</div>';
				echo '<div class="fieldsetwrap">';
					//create new listing for array
					foreach($emailListArray as $userEntry) {
						$em_useremail = $userEntry;
						$pos = strpos($em_useremail, '@');
						//changed to position and substr to be compatible pre php 5.3 (strstr function removed)
						if($pos !== false) {
							$counter++;
							$em_username = substr($em_useremail, 0, $pos);
							formLinePrinter($counter, $em_username, '', $em_useremail, '', '', '', '');	
						}
					}
					echo '	<input type="hidden" readonly="readonly" name="processes" id="processes" value="'.$counter.'" />';
				echo '</div>';
				//add submit button
				echo '<div class="buttonline addextrasbutton">';
				echo '	<input type="submit" name="addnewusers" class="button-primary" value="Add All Users" />';
				echo '</div>';
				$infotype = 'formfields';
				showPluginInfo($infotype);
			}
		} else {
			$emailListError = '<p class="amu_error">Error: No valid email addresses were found in your input data! Please try again.</p>';
			addByEmailList($emailListError);
		}
	}
	if ( isset($_POST['show_amuinfo'] ) ) {
		$infotype = 'general';
		showPluginInfo($infotype);
	}
	echo '</form>';
	echo '</div><!-- end wrap -->';
}

//MAIN FUNCTIONS
//get list of users from network
function getUserList($userListError) {
	
	echo '<div class="toolintro">';
	echo '	<h3>Add Existing Users from Multisite Network</h3>';
	echo $userListError;
	echo '	<p><strong>Select the users you wish to add to this site from your Network Users list and click the Add All Users button.</strong></p>';
	echo '	<p><span class="important">Only users from your Network that are not already added to this site will appear in the list below.</span></p>';
	echo '</div>';
	
}
//create form lines
function generateManualForm($manualInputError) {
	echo '<div class="toolintro">';
	echo '	<h3>Add New Users by Manual Input</h3>';
	echo $manualInputError;
	echo '	<p><strong>Please enter the number of users you wish to add and press the Create Blank Form button. It is recommended you stay under 500 so as to avoid exceeding PHP memory limit.</strong></p>';
	echo '	<p>You don\'t have to be too specific. You can add more as you go.</p>';
	echo '</div>';
	
	echo '<div class="buttonline">';
	echo '	<label for="manualprocs">Number of new users: </label>';
	echo '	<input type="text" name="manualprocs" id="manualprocs" value="10" />';
	echo '</div>';
	
	echo '<div class="buttonline">';
	echo '	<input type="submit" name="formshow_manual" id="formshow_manual" class="button-primary" value="Create Blank Form" />';
	echo '</div>';
}		

//add input via csv input
function addCSVInput($csvinputerror) {
	echo '<div class="toolintro">';
			echo '<h3>Add New Users by CSV Input</h3>';
			echo $csvinputerror;
			echo '<p>Paste your CSV info in the box below. Please read the Information on CSV Data below for information about your CSV data structure.</p>';
			echo '<p><span class="important">You will have the chance to review and customize user information in the next step before adding these users.</span></p>';
		echo '</div>';
		
		addFileSort();
		
		echo '<div class="formline">';
		echo '	<textarea name="csvpastebox" cols="50" rows="10" id="csvpastebox" class="textfillbox"></textarea>';
		echo '</div>';
		echo '<div class="formline">';
		echo '	<p>Click the <strong>Create User Information Form</strong> button below to convert this user information into a form to customise specific information. Alternatively, choose the <strong>Skip Form and Add Users</strong> option if you want to immediately add the users (duplicate entries will be skipped if they are found).</p>';
		echo '	<p><strong>Important:</strong> If you are adding more than 100 users in one pass, it is recommended you use the Skip option, as you will likely exceed your PHP memory limit. Please see the information at the bottom of the screen for more info.</p>';
		echo '</div>';
		echo '<div class="buttonline">';
		echo '	<input type="submit" name="formshow_csvpaste" id="formshow_csvpaste" class="button-primary" value="Create User Information Form" />';
		echo '	<input type="submit" name="skiprun_csvprocess" id="skiprun_csvprocess" class="button-primary" value="Skip Form and Add Users" />';
		echo '</div>';
}

//add email list adding form with optional error
function addByEmailList($emailListError) {
	echo '<div class="toolintro">';
		echo '<h3>Add New Users by Email List</h3>';
		echo $emailListError;
		echo '<p><strong>Enter the email addresses of the users you wish to add to your WordPress site, separated by commas.</strong></p>';
		echo '<p>Usernames are automatically created from the first part of each email address you enter. All other information will be left blank.</p>';
		echo '<p><span class="important">You will have the chance to review and customize user information in the next step before adding these users.</span></p>';
	echo '</div>';
	
	echo '<div class="formline">';
	echo '	<textarea name="emailfillbox" cols="50" rows="10" id="emailfillbox" class="textfillbox"></textarea>';
	echo '</div>';
	echo '<div class="formline">';
		echo '	<p>Click the <strong>Create User Information Form</strong> button below to convert this user information into a form to customise specific information. Alternatively, choose the <strong>Skip Form and Add Users</strong> option if you want to immediately add the users (duplicate entries will be skipped if they are found).</p>';
		echo '	<p><strong>Important:</strong> If you are adding more than 100 users in one pass, it is recommended you use the Skip option, as you will likely exceed your PHP memory limit. Please see the information at the bottom of the screen for more info.</p>';
		echo '</div>';
	echo '<div class="buttonline">';
	echo '	<input type="submit" name="formshow_email" id="formshow_email" class="button-primary" value="Create User Information Form" />';
	echo '	<input type="submit" name="skiprun_csvprocess" id="skiprun_csvprocess" class="button-primary" value="Skip Form and Add Users" />';
	echo '</div>';
}

//add sorting mechanism
function addFileSort() {
	echo '<div id="sortfield">';
	echo '</div>';
}

//add upload function form
function functionUploadFile($uploadFileError) {
	echo '<div class="toolintro">';
	echo '<h3>Add New Users by CSV Upload</h3>';
	echo $uploadFileError;
	echo '<p><strong>Choose a CSV or TXT file to upload using the file browser below.</strong></p>';
	echo '<p>Please read the Information on Uploading CSV and Text Files below for restrictions regarding the formatting of CSV information.</p>';
	echo '<p><span class="important">You will have the chance to review and customize user information in the next step before adding these users.</span></p>';
	echo '</div>';
	
	echo '<div class="buttonline">';
	echo '<input name="csvuploader" id="csvuploader" type="file" />';
	echo '</div>';
	echo '<div class="buttonline">';
	echo '	<input type="submit" name="formshow_csvupload" id="formshow_csvupload" class="button-primary" value="Upload File" />';
	echo '</div>';
}

//create form lines from user input
function formLinePrinter($fm_order, $fm_username, $fm_password, $fm_email, $fm_role, $fm_firstname, $fm_lastname, $fm_website) {
	global $current_user, $wpdb;
    get_currentuserinfo();
	$fm_role = strtolower($fm_role);
	if ($fm_order & 1) {
		echo '<div class="formwrap wrapwhite">';
	} else {
		echo '<div class="formwrap wrapgrey">';
	}
		echo '<div class="formline">';
		echo '	<span class="countline">'.$fm_order.'.</span>';
		echo '	<label for="username'.$fm_order.'">Username</label>';
		echo '	<input type="text" name="username'.$fm_order.'" id="username'.$fm_order.'" class="valusername validatefield" value="'.$fm_username.'" />';
		echo '	<label for="password'.$fm_order.'">Password</label>';
		echo '	<input type="text" name="password'.$fm_order.'" id="password'.$fm_order.'" class="valpassword validatefield" value="'.$fm_password.'" />';
		echo '	<label for="email'.$fm_order.'">Email</label>';
		echo '	<input type="text" name="email'.$fm_order.'" id="email'.$fm_order.'" class="valemail validatefield" value="'.$fm_email.'" />';
		echo '	<label for="roleSetter'.$fm_order.'">UserRole</label>';
		echo '	<select name="roleSetter'.$fm_order.'" id="roleSetter'.$fm_order.'">';
		echo '		<option value="subscriber"'; if($fm_role=='subscriber' || $fm_role=='' ){echo ' selected="selected" ';} echo'>subscriber</option>';
		echo '		<option value="contributor"'; if($fm_role=='contributor'){echo ' selected="selected" ';} echo'>contributor</option>';
		echo '		<option value="author"'; if($fm_role=='author'){echo ' selected="selected" ';} echo'>author</option>';
		echo '		<option value="editor"'; if($fm_role=='editor'){echo ' selected="selected" ';} echo'>editor</option>';
		echo '		<option value="administrator"'; if($fm_role=='administrator'){echo ' selected="selected" ';} echo'>administrator</option>';
		echo '	</select>';
		echo '</div>';
		echo '<div class="formline">';
		echo '	<span class="countline">&nbsp;</span>';
		echo '	<label for="firstname'.$fm_order.'">FirstName</label>';
		echo '	<input type="text" name="firstname'.$fm_order.'" id="firstname'.$fm_order.'" value="'.$fm_firstname.'" />';
		echo '	<label for="lastname'.$fm_order.'">LastName</label>';
		echo '	<input type="text" name="lastname'.$fm_order.'" id="lastname'.$fm_order.'" value="'.$fm_lastname.'" />';
		echo '	<label for="website'.$fm_order.'">Website</label>';
		echo '	<input type="text" name="website'.$fm_order.'" id="website'.$fm_order.'" value="'.$fm_website.'" />';
		echo '</div>';
	echo '</div>';
}

// <=========== PLUGIN TOOLBAR =================================================================>
function showToolbar() {
	echo '<div class="formhead toolbar">';
		echo '	<input type="submit" name="openg_genopt" id="openg_genopt" class="button-toolbar" value="Settings" title="Modify your General Settings" />';
		echo '	<input type="submit" name="input_manual" id="input_manual" class="button-toolbar" value="Blank Form" title="Create a new user input form" />';
		echo '	<input type="submit" name="upload_csvtxt" id="upload_csvtxt" class="button-toolbar" value="CSV/TXT Upload" title="Upload a file of your new user data" />';
		echo '	<input type="submit" name="input_csvbox" id="input_csvbox" class="button-toolbar" value="CSV Input" title="Paste in your CSV new user data" />';
		echo '	<input type="submit" name="input_email" id="input_email" class="button-toolbar" value="Email List" title="Create users from a list of email addresses" />';
		//added 1.2.1
		if ( is_multisite() ) {
			echo '	<input type="submit" name="add_existing" id="add_existing" class="button-toolbar" value="Add Existing" title="Add existing users on your network to this site" />';
		}
		echo '	<input type="submit" name="show_amuinfo" id="show_amuinfo" class="button-toolbar" value="Plugin Info" title="See the general plugin information" />';
	echo '</div>';
}

// <=========== PAGE INFORMATION ===============================================================>
function showPluginInfo($infotype) {
	
	//memory limit discussion
	//http://wordpress.org/support/topic/unable-to-update-fatal-error-maximum-execution-time-of-30-seconds-exceeded-help?replies=4
	
	//general information
	if ($infotype == 'general') {
		echo '<div class="toolintro">';
		echo '<h3>Add Multiple Users - General Plugin Information</h3>';
		echo '<p><strong>This plugin enables an administrator to bulk add user registrations to a WordPress blog by using a variety of tools.</strong></p>';
		echo '<p>Please choose an option from the menu above to start registering users.</p>';
		echo '</div>';
		echo '<div class="moreinfosection">';
			echo '<h4>Functions provided by the Add Multiple Users plugin</h4>
			<ul>
				<li><strong>&raquo; Settings:</strong> modify the general settings for when new users are added to your site using the plugin</li>
				<li><strong>&raquo; Blank Form:</strong> create a new form to manually input new user information</li>
				<li><strong>&raquo; CSV/TXT Upload:</strong> upload a CSV or TXT (comma-separated values) file containing your new user information</li>
				<li><strong>&raquo; CSV Input:</strong> Paste your CSV data containing your new user information instead of uploading it</li>
				<li><strong>&raquo; Email List:</strong> Convert a simple list of email addresses into new user information</li>';
				if ( is_multisite() ) {
					echo '<li><strong>&raquo; Add Existing:</strong> Add users to this site from the Network users list (Multisite only)</li>';
				}
				echo '<li><strong>&raquo; Plugin Info:</strong> Return to this page of general plugin information</li>
			</ul>';
			echo '<h4>Further help regarding individual plugin functions</h4>';
			echo '<p>While the functionality of this plugin is designed to be as intuitive as possible, there are some rules that must be followed 
					when using the plugin\'s various functions.</p>';
			echo '<p>More information about each function is provided at the bottom of each tool\'s page.</p>';
			echo '<h4>WordPress/Browser Memory Issues when adding a large number of users</h4>';
			echo '<p>Adding an exceptionally high number of users at any time is possible, however in the case of adding hundred or even thousands of users at a time, depending on your server capacity, you may have to modify your wp-config file to turn off the time limit that can cause the adding function to time out before it is complete. Further information can be found on the plugin page at <a href="http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php" target="_blank">http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php</a>.</p>';
		echo '</div>';
		echo '<div class="moreinfosection">';
			echo '<h4>More Information</h4>';
			echo '<p>Visit the plugin page at <a href="http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php" target="_blank">
					http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php</a> for more information on the Add Multiple Users plugin.</p>';
		echo '</div>';
	
	//show info on form fields
	} else if ($infotype == 'emaillisting') {
		echo '<div class="infosection">';
		echo '<h3>Information about the Email List function</h3>';
		echo '<p>This function takes a normal list of email addresses and converts them into new user information.</p>';
		echo '<p><strong>Note: Users are not immediately registered on the site when you click the Create User Information button.</strong> The email addresses you provide here will be used to create a form containing user information that you can review before registering these users on your site, allowing you to find errors and add additional information for each user if desired.</p>';
		echo '<h4>Adding your email list</h4>';
		echo '<p>When adding email addresses to the field above, each address must be separated by a comma (,) character. Even if you put the next address on the next line, there should be a comma between each email address.</p>';
		echo '<p><strong>Note:</strong> All blank spaces are stripped from the email list (meaning spaces after commas are not necessary or detrimental, however email addresses that contain spaces will be compressed).</p>';
	echo '</div>';
		
	//show info on form fields
	} else if ($infotype == 'formfields') {
		echo '<div class="infosection">';
		echo '<h3>Information on form fields and settings</h3>
			<p><strong>Usernames:</strong><br />
			All new users must be given a unique username. <em>Rows without a username are automatically skipped during the multiple registration process.</em> Usernames cannot be changed once set. Usernames are automatically sanitized to strip out unsafe characters, but are not strictly sanitized.</p>
			<p><strong>Password:</strong><br />
			May be set for each user, or left blank to generate a random password for that user. For more information on password strength and security, please visit the <a href="http://codex.wordpress.org/Hardening_WordPress" target="_blank">Hardening Wordpress</a> page.</p>
			<p><strong>Email:</strong><br />
			An unique, valid email address for each user is required (or use Force Fill option if emails are not available, see below). Emails will be checked for uniqueness and, if selected, validity.</p>
			<p><strong>FirstName, LastName, Website:</strong><br />
			These parameters per user are optional and will be left blank if not filled in. Users or Administrators may update this information using the regular Wordpress user profile settings later. These fields are not validated.</p>
		';
	echo '</div>';
	
	//show upload file info
	} else if ($infotype == 'uploadfile') {
		echo '<div class="infosection">';
		echo '<h3>Information on Uploading CSV and Text Files</h3>';
		echo '<p>Please choose either a .csv or .txt file to upload. All other file types are disallowed.</p>';
		echo '<h4>Recommended CSV Column Order</h4>';
		echo '<p>The plugin translates each line of your CSV (comma-separated values) data into seven variables: username, password, email, role, firstname, lastname and website, specifically in that order.</p>';
		echo '<p>The best way to ensure your file is read properly is to structure your CSV data in this way if possible, separated by commas, using empty values in place of data you do not want to add. If you can export your CSV data in this format for upload here it will make importing your data quick and painless. If not, you can use the Customize function to specify your own line order.</p>';
		echo '<p><strong>Good Example 1: username,password,email,role,firstname,lastname,website</strong> - in this example the file is structured properly with all values added.</p>';
		echo '<p><strong>Good Example 2: username,,email,,firstname,lastname,website</strong> - in this example the "password" and "role" have been left blank, but the empty values are placed in the right order so the file will be read properly.</p>';
		echo '<p><strong>Bad Example 1: username,password,email,firstname,lastname,website</strong> - this example omits the "role" value and will translate incorrectly.</p>';
		echo '<p><strong>Bad Example 2: username,password,email,role,lastname,firstname,website</strong> - this example has the first and last name in the wrong order and will translate incorrectly.</p>';
		echo '<h4><em>Customizing the Column Order</em></h4>';
		echo '<p>If your CSV data is structured in a different way, you may upload your file now and use the Customize Column Order function on the next page.</p>';
		echo '</div>';
	
	//show upload file info
	} else if ($infotype == 'pastecsvtext') {
		echo '<div class="infosection">';
		echo '<h3>Information on CSV Data</h3>';
		echo '<h4>Recommended CSV Column Order</h4>';
		echo '<p>The plugin translates each line of your CSV (comma-separated values) data into seven variables: username, password, email, role, firstname, lastname and website, specifically in that order.</p>';
		echo '<p>The best way to ensure your data is read properly is to structure your CSV data in this way if possible, separated by commas, using empty values in place of data you do not want to add. If you can export your CSV data in this format for use here it will make importing your data quick and painless. If not, you can use the Customize function to specify your own line order.</p>';
		echo '<p><strong>Good Example 1: username,password,email,role,firstname,lastname,website</strong> - in this example the file is structured properly with all values added.</p>';
		echo '<p><strong>Good Example 2: username,,email,,firstname,lastname,website</strong> - in this example the "password" and "role" have been left blank, but the empty values are placed in the right order so the file will be read properly.</p>';
		echo '<p><strong>Bad Example 1: username,password,email,firstname,lastname,website</strong> - this example omits the "role" value and will translate incorrectly.</p>';
		echo '<p><strong>Bad Example 2: username,password,email,role,lastname,firstname,website</strong> - this example has the first and last name in the wrong order and will translate incorrectly.</p>';
		
		echo '</div>';
		
	//show add existing infor
	} else if ($infotype == 'addexistusers') {
		echo '<div class="infosection">';
		echo '<h3>Information about Adding Existing Users</h3>';
		echo '<p>On this page you will see a list of users taken from your Network list who are NOT already a part of this site. Firstly, <strong>set the two options as desired above the user list</strong>. You may then <strong>check the users you wish to add to this site and click the Add All Users button.</strong></p>';
		echo '<h4>Options for Adding Existing Users</h4>';
		echo '<p><strong>Ignore individual roles and set all selected users to this role:</strong> <br />
				You can assign each existing user you add to this site an individual Role within this site. Make a selection here if you want to add all existing users you choose with the Role defined here instead.</p>';
		echo '<p><strong>Send each user a confimation email:</strong> <br />
				If you leave this unchecked, users you select will be automatically added to this site. Check this option if you do not want this to happen. Instead, each user you select will be sent an email asking them to confirm their adding to this site. When they have confirmed, they will show up in the Users list for this site.</p>';
		echo '</div>';
		
	//show column ordering instructions
	} else if ($infotype == 'ordercolumns') {
		
		echo '<div class="infosection">';
		
			echo '<h3>How to use the Custom Column Order function</h3>';
			echo '<p><strong>Check the Customise Column Order box to enable custom ordering.</strong></p>';
			echo '<p>The Custom Order function allows you to define how your CSV data is structured per line so that it can be read correctly by the plugin. By default, the order of CSV values is: username, password, email, role, firstname, lastname, website.</p>';
			
			echo '<h4>Setting a custom order</h4>';
			echo '<p><strong>The order you set in the Custom Order section should match exactly the order of your CSV data structure <em>per line.</em></strong></p>';
			echo '<p>If your CSV data takes a different structure than the default, and maybe has additional data not required to be used for new registrations, for example:</p>';
			echo '<p><strong>email, state, username, role, lastname, firstname, phone, age</strong></p>';
			echo '<p>you can set the Custom Order appropriate to how the data should be read (left to right), using the "ignore" option to tell the program to skip a redundant data column. In this case, you would set up the Custom Column drop-boxes in this order:</p>';
			echo '<p><strong>email, ignore, username, role, lastname, firstname, ignore, ignore</strong></p>';
			
			echo '<h4>Column actions</h4>';
			echo '<p>Each column in the Custom Order box also contains buttons for reordering, adding and deleting columns:</p>';
			echo '<ul>';
				echo '<li><strong>&larr;</strong> shifts the column left</li>';
				echo '<li><strong>&rarr;</strong> shifts the column right</li>';
				echo '<li><strong>x</strong> removes the column</li>';
				echo '<li><strong>+</strong> adds a new column to the right</li>';
			echo '<ul>';
		echo '</div>';
		
	//show settings info
	} else if ($infotype == 'settings') {

	echo '<div class="infosection">';
		echo '<h3>Information about Settings options</h3>';
		echo '<div class="pluginfohide">';
			echo '<h4>General Options</h4>
			
			<p><strong>Send each new user a registration notification email?</strong><br />If selected, automatically sends an email to each new registered user with the information provided in the Customise New User Notification Email settings. Users who have been added with a "forced" email address will not be emailed.</p>
				
			<p><strong>Send me a complete list of new user account details:</strong><br /><em>Highly recommended.</em> When you submit the multiple registration form, the results of your registration will display on the screen. However, this information will not remain on the screen once you navigate away from the page or submit the form again. This option emails all this information to your registered WordPress user account email.</p>
				
			<p><strong>Validate entered email addresses:</strong><br />This setting affects both the in-page validation and the on-submit validation. It uses <a href="http://codex.wordpress.org/Function_Reference/is_email" target="_blank">WordPress "is_email" verification</a>. If you have trouble entering email addresses that you believe are valid, disable this option.</p>
			
			<p><strong>Sanitize usernames using Strict method:</strong><br />Determines whether usernames are sanitized with Strict method or not. Enabling this option disallows the use of many symbols that may be used in usernames normally. Affects both the on-screen validation and the on-submit validation. Get more info on <a href="http://codex.wordpress.org/Function_Reference/sanitize_user" target="_blank">user sanitization</a>.</p>
			
			<p><strong>Force Fill empty email addresses:</strong><br /><em>Highly NOT recommended.</em> This setting ignores empty email address fields that would normally cause that new user\'s registration to fail by creating a fake email address such as "temp_username@temp_username.fake". It is very much recommended that all new users have a valid email address, and this function should only be used in cases where you need to register new users that do not have active email accounts.</p>
			
			<p><strong>Ignore individual User Role settings and set all new users to this role:</strong><br />Overrides any individual Role you select for each new and sets them to the role you choose here.</p>';
			
			echo '<h4>Customise New User Notification Email</h4>
			
				<p><strong>From/Reply Address:</strong><br /> By default, new users will see the From/Reply email address in their New User Notification email as the email address of the administrator that added them. You can change this email address by adding a different address here, such as a "no reply" email address. You may also use this email address in the email message using the [fromreply] shortcode.</p>
				
				<p><strong>Site Login URL:</strong><br /> If you want to direct new users to a specific web address to log in, add the full URL here (including the http://). You may then add this to your email message using the [siteloginurl] shortcode. By default this setting is your main site URL.</p>
				
				<p><strong>Email Subject:</strong><br /> This is your email subject line and can include any of the shortcodes to add additional information to the subject line. </p>
				
				<p><strong>Email Message:</strong><br /> This is your main email content and must be written in HTML format using valid HTML tags (such as p and h1). Any HTML tag that can be understood by an email program can be used here. If you\'re not familiar with HTML markup, its probably best to stick to the default message, or you can play with it and use the Send Test Email button to send yourself an example notification email so you can check its formatting and content.</p>
				
				<p><strong>Shortcodes:</strong><br /> The shortcodes [sitename] [siteurl] [siteloginurl] [username] [password] [useremail] [fromreply] can be used in the Email Subject and Email Message fields to add specific data to your user notification email. For example, if you want to add that specific user\'s password to the email, using the [password] shortcode will add the users newly created password in there. Use these shortcodes to structure your email body text as you require.</p>
				
				<p><strong>Send Test Email:</strong><br /> <em>Only available with Javascript enabled.</em> This sends an example New User Notification Email to your email address using the information you currently have in the settings fields. Note that this does not save your Settings - you must still click the Save Options button to save your changes. This allows you to view the data and layout of the email that newly registered users will get when they are added to the site.</p>';
		echo '</div>';
	echo '</div>';
	}	
}
function showMemLimitInfo() {
	echo '<div class="infosection">';
	echo '<h3>Create User Information Form versus Skip Form and Add Users</h3>';
		echo '<p>If you are using this function to add more than 100 users it is recommended that you use the Skip button. This function bypasses the Form option, which displays each new user on a separate line, as it can cause issues with memory usage at high volumes. With the skip option you lose the ability to customise user information before adding your users, but it is more stable when adding many users.</p>';
		echo '<p><strong>Note:</strong>In the case of adding hundred or even thousands of users at a time, depending on your server capacity, you may have to modify your wp-config file to turn off the time limit that can cause the adding function to time out before it is complete. Further information can be found on the plugin page at <a href="http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php" target="_blank">http://www.happynuclear.com/sandbox/amu/add-multiple-users-for-wordpress.php</a>.</p>';
		echo '</div>';
}
// <=========== SHOW AND SAVE SETTINGS ===============================================================>
function addGeneralOptions() {
	global $current_user, $wpdb;
    get_currentuserinfo();
	
	//coming in 1.2.3
	//if ($genoptAdminType == 'network')  {
//		echo '<div class="toolintro">';
//			echo '<h3>Network Administrator Options</h3>';
//			echo '	<p><span class="important">Please refer to the Information about Network options at the bottom of this page for more information about these settings.</span></p>';
//		echo '</div>';
//	
//		echo '<div class="genoptionwrap">';
//		
//			//notify of subsite usage
//			echo '<div class="optionbox">';
//			echo '	<label for="subsiteallow">Allow sub-site administrators access to Add Multiple Users plugin?</label>';
//			echo '	<input name="subsiteaddexisting" id="subsiteaddexisting" type="checkbox" ';
//			if (get_site_option('amu_ss_allow')) {
//				if ( get_site_option('amu_ss_allow') == 'yes') {
//					echo 'checked="checked"';
//				}
//			} else {
//				echo 'checked="checked"';
//			}
//			echo ' value="send" />';
//			echo '</div>';
//		
//			//notify of subsite usage
//			echo '<div class="optionbox">';
//			echo '	<label for="subsiteaddexisting">Allow sub-site administrators to add users from Network?</label>';
//			echo '	<input name="subsiteaddexisting" id="subsiteaddexisting" type="checkbox" ';
//			if (get_site_option('amu_ss_ae')) {
//				if ( get_site_option('amu_ss_ae') == 'yes') {
//					echo 'checked="checked"';
//				}
//			} else {
//				echo 'checked="checked"';
//			}
//			echo ' value="send" />';
//			echo '</div>';
//		
//			//notify of subsite usage
//			echo '<div class="optionbox">';
//			echo '	<label for="subsitenotify">Send SuperAdmin copies of all user registration information from sub-sites?</label>';
//			echo '	<input name="subsitenotify" id="subsitenotify" type="checkbox" ';
//			if (get_site_option('amu_subsite_notify')) {
//				if ( get_site_option('amu_subsite_notify') == 'yes') {
//					echo 'checked="checked"';
//				}
//			} else {
//				echo 'checked="checked"';
//			}
//			echo ' value="send" />';
//			echo '</div>';
//			
//			//alter superadmin email
//			echo '<div class="optionbox">';
//			echo '	<label for="superadminemail">Super Admin email address to receive copies?</label>';
//			echo '	<input name="superadminemail" id="superadminemail" type="text" ';
//			if (get_site_option('admin_email')) {
//				///if ( get_site_option('amu_ssae') == 'yes') {
//				//	echo 'checked="checked"';
//				//}
//				echo 'value = "'.get_site_option('admin_email').'"';
//			} else {
//				echo 'value="no email found"';
//			}
//			echo ' />';
//			echo '</div>';
//			
//			echo '<div class="optionbox">';
//				
//			echo '</div>';
//			
//			//enable/disable subscribing users added through Network panel auto subscription to main site
//			echo '<div class="optionbox lastoption">';
//			echo '	<label for="networkaddpref">Prevent users added from Network dashboard from being automatically subscribed on main site?<br />stuff</label>';
//			echo '	<input name="networkaddpref" id="networkaddpref" type="checkbox" ';
//			if (get_site_option('amu_network_roledef')) {
//				if ( get_site_option('amu_network_roledef') == 'yes') {
//					echo 'checked="checked"';
//				}
//			} else {
//				echo 'checked="checked"';
//			}
//			echo ' value="send" />';
//			echo '</div>';
//		
//		echo '</div>';
//		
//	}
	
	echo '<div class="toolintro">';
		echo '<h3>General Options</h3>';
		echo '	<p><span class="important">Please refer to the Information about Settings options at the bottom of this page for more information about these settings.</span></p>';
	echo '</div>';
	
	echo '<div class="genoptionwrap">';
			
		//send emails...
		echo '<div class="optionbox">';
		echo '	<label for="sendpswemails">Send each new user a registration notification email?</label>';
		echo '	<input name="sendpswemails" id="sendpswemails" type="checkbox" ';
		//retrieve option if set
		if (get_option('amu_usernotify')) {
			if ( get_option('amu_usernotify') == 'yes') {
				echo 'checked="checked"';
			}
		} else {
			echo 'checked="checked"';
		}
		echo ' value="send" />';
		echo '</div>';
		
		//send confirmation email
		echo '<div class="optionbox">';
		echo '	<label for="confirmEmail">Email me a complete list of new user account details?</label>';
		echo '	<input name="confirmEmail" id="confirmEmail" type="checkbox" ';
		//retrieve option if set
		if (get_option('amu_confirmation')) {
			if ( get_option('amu_confirmation') == 'yes') {
				echo 'checked="checked"';
			}
		} else {
			echo 'checked="checked"';
		}
		echo ' value="yesConfirm" />';
		echo '</div>';
			
		//validate emails...
		echo '<div class="optionbox">';
		echo '	<label for="validatemail">Validate entered email address format? <em>(uses WordPress "is_email" validation method)</em></label>';
		echo '	<input name="validatemail" id="validatemail" type="checkbox" ';
		//retrieve option if set
		if (get_option('amu_validatemail')) {
			if ( get_option('amu_validatemail') == 'yes') {
				echo 'checked="checked"';
			}
		} else {
			echo 'checked="checked"';
		}
		echo ' value="validate" />';
		echo '</div>';
			
		//username strict validation option...
		echo '<div class="optionbox">';
		echo '	<label for="validateStrict">Sanitize usernames using Strict method <em>(allows only alphanumeric and _, space, ., -, *, and @ characters)</em></label>';
		echo '	<input name="validateStrict" id="validateStrict" type="checkbox" ';
		//retrieve option if set
		if (get_option('amu_validatestrict')) {
			if ( get_option('amu_validatestrict') == 'yes') {
				echo 'checked="checked"';
			}
		}
		echo ' value="userstrict" />';
		echo '</div>';
			
		//force fill emails...
		echo '<div class="optionbox">';
		echo '	<label for="forcefillemail">Force Fill empty email addresses? <em>(not recommended - see bottom of page for details)</em></label>';
		echo '	<input name="forcefillemail" id="forcefillemail" type="checkbox" ';
		//retrieve option if set
		if (get_option('amu_forcefill')) {
			if ( get_option('amu_forcefill') == 'yes') {
				echo 'checked="checked"';
			}
		}
		echo ' value="fill" />';
		echo '</div>';
			
		//set all users to this role
		//retrieve set option value
		if (get_option('amu_setallroles')) {
			$rolesel = get_option('amu_setallroles');
		} else {
			$rolesel = 'notset';
		}
		echo '<div class="optionbox lastoption">';
		echo '	<label for="allToRole">Ignore individual User Role settings and set all new users to this role: </label>';
		echo '	<select name="allToRole" id="allToRole">';
		echo '		<option value="notset"'; if($rolesel=='notset'){echo ' selected="selected" ';} echo '>no, set individually...</option>';
		echo '		<option value="subscriber"'; if($rolesel=='subscriber'){echo ' selected="selected" ';} echo '>subscriber</option>';
		echo '		<option value="contributor"'; if($rolesel=='contributor'){echo ' selected="selected" ';} echo '>contributor</option>';
		echo '		<option value="author"'; if($rolesel=='author'){echo ' selected="selected" ';} echo '>author</option>';
		echo '		<option value="editor"'; if($rolesel=='editor'){echo ' selected="selected" ';} echo '>editor</option>';
		echo '		<option value="administrator"'; if($rolesel=='administrator'){echo ' selected="selected" ';} echo '>administrator</option>';
		echo '	</select>';
		echo '</div>';
			
	echo '</div>';
		
	echo '<div class="toolintro">';
	echo '<h3>Customize New User Notification Email</h3>';
	echo '</div>';
	
	echo '<div class="genoptionwrap emailcustbox">';
		//customize new user delivered email
		echo '<div class="optionbox lastoption">';
			
			$msghead = get_option('amu_useremailhead');
			$msgtext = get_option('amu_useremailtext');
			$sendemailsto = get_option('amu_defadminemail');
			$siteloginurl = get_option('amu_siteloginurl');
			
			echo '<div class="optioninstructions">';
			echo '	<p><strong>Use the following settings to modify the notification email that new users receive when added via the plugin.</strong></p>';
			echo '	<p>Please refer to the Information about Settings options at the bottom of this page for more information about these settings.</p>';
			echo '	<p>Valid shortcodes are: [sitename] [siteurl] [siteloginurl] [username] [password] [useremail] [fromreply]</p>';
			echo '</div>';
			
			//from email
			echo '<div class="optionbox">';
				echo '<label for="custademail" class="custheadlabel">From/Reply Address: </label>';
				echo '<input type="text" name="custademail" id="custademail" value="'.$sendemailsto.'" class="custheadfield" />';
			echo '</div>';
			
			//custom login url
			echo '<div class="optionbox">';
				echo '<label for="custlogurl" class="custheadlabel">Site Login URL: </label>';
				echo '<input type="text" name="custlogurl" id="custlogurl" value="'.$siteloginurl.'" class="custheadfield" />';
			echo '</div>';
			
			//message header
			echo '<div class="optionbox">';
				echo '<label for="custemailhead" class="custheadlabel">Email Subject: </label>';
				echo '<input type="text" name="custemailhead" id="custemailhead" value="'.$msghead.'" class="custheadfield" />';
			echo '</div>';
			
			//message text
			echo '<div class="optionbox lastoption">';
				echo '<label for="customemailtext" class="custheadlabel">Email Message (HTML format): </label>';
				echo '<textarea name="customemailtext" cols="50" rows="10" id="customemailtext" class="textfillbox">'.$msgtext.'</textarea>';
			echo '</div>';
		
		echo '</div>';		
	echo '</div>';
	echo '<div class="buttonline">';
	echo '	<input type="submit" name="setgenopt" id="setgenopt" class="button-primary" value="Save Options" />';
	echo '	<input type="submit" name="resetsettings" id="resetsettings" class="button-primary" value="Reset to Default Settings" />';
	echo '</div>';
}

//set general options
function setGeneralOptions() {
	global $current_user, $wpdb;
    get_currentuserinfo();
	//get posted option info
	if ( isset($_POST['sendpswemails'] ) ) {
		$sendEmail = 'yes';
	} else {
		$sendEmail = 'no';
	}
	if ( isset($_POST['confirmEmail'] ) ) {
		$yesConfirm = 'yes';
	} else {
		$yesConfirm = 'no';
	}
	$setAllRoles = $_POST['allToRole'];
	if ( isset($_POST['forcefillemail'] ) ) {
		$forceEmail = 'yes';
	} else {
		$forceEmail = 'no';
	}
	if ( isset($_POST['validatemail'] ) ) {
		$validateEmail = 'yes';
	} else {
		$validateEmail = 'no';
	}
	if ( isset($_POST['validateStrict'] ) ) {
		$validateStrict = 'yes';
	} else {
		$validateStrict = 'no';
	}
	$emailCustomHead = $_POST['custemailhead'];
	$emailCustomText = $_POST['customemailtext'];
	$emailFromAddr = $_POST['custademail'];
	$emailsiteLog = $_POST['custlogurl'];
	
	//SAVE GENERAL OPTIONS
	update_option( 'amu_usernotify', $sendEmail );
	update_option( 'amu_confirmation', $yesConfirm );
	update_option( 'amu_setallroles', $setAllRoles );
	update_option( 'amu_validatestrict', $validateStrict );
	update_option( 'amu_validatemail', $validateEmail );
	update_option( 'amu_forcefill', $forceEmail );
	update_option( 'amu_useremailhead', $emailCustomHead );
	update_option( 'amu_useremailtext', $emailCustomText );
	update_option( 'amu_defadminemail', $emailFromAddr );
	update_option( 'amu_siteloginurl', $emailsiteLog );
}
// <=========== READ/CLEAN CSV DATA ===============================================================>

//function for parsing csv
function parse_csv($file,$comma=',',$quote='"',$newline="\n") {
    $db_quote = $quote . $quote;
    //clean up data
    $file = trim($file);
    $file = str_replace("\r\n",$newline,$file);
    $file = str_replace($db_quote,'&quot;',$file);
    $file = str_replace(',&quot;,',',,',$file);
    $file .= $comma;
    $inquotes = false;
    $start_point = 0;
    $row = 0;
	//run for each line
    for($i=0; $i<strlen($file); $i++) {
        $char = $file[$i];
        if ($char == $quote) {
            if ($inquotes) {
                $inquotes = false;
                }
            else {
                $inquotes = true;
            }
        }
		//process each line
        if (($char == $comma or $char == $newline) and !$inquotes) {
            $cell = substr($file,$start_point,$i-$start_point);
            $cell = str_replace($quote,'',$cell); // Remove delimiter quotes
            $cell = str_replace('&quot;',$quote,$cell); // Add in data quotes
            $pasteddata[$row][] = $cell;
            $start_point = $i + 1;
            if ($char == $newline) {
                $row ++;
                }
            }
        }
    return $pasteddata;
}

// <=========== REORDER CSV DATA ===============================================================>

function reorder_csv($pasteddata) {
	//if sorting of text is selected
	if (isset($_POST['opentextsorter'])) {
		//first create user-defined order into array
		$customorder = array();
		$i = 1;
		while(isset($_POST[$i])) {
			array_push($customorder, $_POST[$i]);
			$i++;
		}
		//create empty array to store lines
		$data_rev = array();
		
		//get positions of vars in array
		$username_index = array_search('username', $customorder);
		$password_index = array_search('password', $customorder);
		$email_index = array_search('email', $customorder);
		$role_index = array_search('role', $customorder);
		$firstname_index = array_search('firstname', $customorder);
		$lastname_index = array_search('lastname', $customorder);
		$website_index = array_search('website', $customorder);
		
		//loop through each dataline array
		foreach($pasteddata as $thisline) {
			$newarrayline = array();
			//get position of custom structure and value of line
			if($username_index === false) {
				array_push($newarrayline,'');
			} else {
				array_push($newarrayline,$thisline[$username_index]);
			}
			if($password_index === false) {
				array_push($newarrayline,'');
			} else {
				array_push($newarrayline,$thisline[$password_index]);
			}
			if($email_index === false) {
				array_push($newarrayline,'');
			} else {
				array_push($newarrayline,$thisline[$email_index]);
			}
			if($role_index === false) {
				array_push($newarrayline,'');
			} else {
				array_push($newarrayline,$thisline[$role_index]);
			}
			if($firstname_index === false) {
				array_push($newarrayline,'');
			} else {
				array_push($newarrayline,$thisline[$firstname_index]);
			}
			if($lastname_index === false) {
				array_push($newarrayline,'');
			} else {
				array_push($newarrayline,$thisline[$lastname_index]);
			}
			if($website_index === false) {
				array_push($newarrayline,'');
			} else {
				array_push($newarrayline,$thisline[$website_index]);
			}
			//add to main array
			array_push($data_rev, $newarrayline);
			//kill the array for reuse
			unset($newarrayline);
		}
		//return the data reformatted
		return $data_rev;
	} else {
		//if not, return the data unformatted
		$data_rev = $pasteddata;
		return $data_rev;
	}
}

//network admin coming in 1.2.3
//if ( is_multisite() ) {
//     add_action( 'network_admin_menu', 'amu_network_menu' );
//}
add_action( 'admin_menu', 'amu_menu' );
add_action( 'admin_print_styles', 'addmultiuser_style' );
add_action( 'admin_print_scripts', 'on_screen_validation' );
add_action( 'admin_print_scripts', 'multiadd_actions' );
add_action( 'wp_ajax_UserNameValidation', 'validateUserName' );
add_action( 'wp_ajax_EmailValidation', 'validateEmail' );
add_action( 'wp_ajax_OptionTestEmail', 'sendTestEmail' );

//validation functions
function validateUserName() {
    $username = trim($_POST['thevars']);
	$sanstrict = $_POST['sanstrict'];
	if ($sanstrict == 'yes') {
		$sanUsername = sanitize_user( $username, true );
	} else {
		$sanUsername = sanitize_user( $username, false );
	}
    if ( username_exists( $sanUsername ) ) {
		echo 'exists'; 
    } else {
		if ($sanUsername != $username) {
			echo 'badchars';
		} else {
			echo 'spare';
		}
    }
    exit;
}
function validateEmail() {
    $email = $_POST['thevars'];
	$shouldValidate = $_POST['isValidated'];
    if ($shouldValidate == 'yes') {
		if ( !is_email($email) ) {
			echo 'emailinvalid';
			exit;
		}
	}
	if ( email_exists($email) ) {
		echo 'exists'; 
    } else {
		echo 'spare';
    }
    exit;
}
function sendTestEmail() {
	global $current_user, $wpdb;
    get_currentuserinfo();
	//from site settings
	$thisBlogName = get_bloginfo('name');
	$thisBlogUrl = site_url();
	$test_adminmail = $current_user->user_email;
	//fabricated for testing
	$test_username = 'test_username';
	$test_password = 'test_password';
	//posted vars from ajax
	$test_fromreply = $_POST['test_email'];
	$test_loginurl = $_POST['test_loginurl'];
	$test_mailsubject = $_POST['test_mailhead'];
	$test_mailtext = $_POST['test_mailtext'];
	
	//replace instances of shortcodes
	$emailkeywords = array('[sitename]', '[siteurl]', '[siteloginurl]', '[username]', '[password]', '[useremail]', '[fromreply]');
	$emailreplaces = array($thisBlogName, '<a href="'.$thisBlogUrl.'">'.$thisBlogUrl.'</a>','<a href="'.$test_loginurl.'">'.$test_loginurl.'</a>', $test_username, $test_password, $test_fromreply, $test_fromreply);
	$subject = str_replace($emailkeywords, $emailreplaces, $test_mailsubject);
	$message = str_replace($emailkeywords, $emailreplaces, $test_mailtext);
	//create valid header
	$headers = 'From: '.$test_fromreply.' <'.$test_fromreply.'>' . "\r\n";
	//filter to create html email
	add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
	//send email
	wp_mail($test_adminmail, $subject, $message, $headers);
	exit;
}
?>