<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * Common Functions and Classes
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}


/*
	* FORM CREATOR
	* Creates form interface from submitted data (absorbs old linePrinter function)
*/

class amuFormCreator{
		
	function __construct($source, $userData) {
		
		//required settings options
		global $wpdb, $wp_roles;
		$this->lineroles = $wp_roles->get_names();
		$this->defrole = get_option('amu_setallroles');
		//define imported data
		$this->source = $source;
		$this->userData = json_decode($userData);
	}
	
	function amuCreateFormInterface() {
		
		//PRINT FORM INTRO TEXT
		
		echo '<div class="toolintro">';
			//print appropriate source message
			if($this->source == 'email') {
				echo '<p><strong>'.__('Your email list was successfully converted to user listings.','amulang').'</strong></p>';
				echo '<p>'.__('Please review the user information below to correct any errors or to add additional information for each user and click the Add All Users button to process your new user accounts.','amulang').'</p>';
			} else if($this->source == 'csvlist') {
				echo '<p><strong>'.__('Your CSV data was successfully converted to user listings.','amulang').'</strong></p>';
				echo '<p>'.__('Please review the user information below to correct any errors or to add additional information for each user and click the Add All Users button to process your new user accounts.','amulang').'</p>';
			} else if($this->source == 'manual') {
				echo '<p><strong>'.__('Enter your new user information in the form below and click the Add All Users button to process your new user registrations.','amulang').'</strong>
					<br />'.__('Note that entries without user_logins will be skipped during the registration process.','amulang').'</p>';
			}
			
		echo '</div>';
		
		//START FORM
		echo '<form method="post" enctype="multipart/form-data" class="amuform">';
			
			//FORM HEAD
			echo '<div class="buttonline">';
				echo '<input type="submit" name="addnewusers" class="button-primary" value="'.__('Add All Users','amulang').'" />';
				if($this->source == 'manual') {
					echo '	<input type="reset" name="clearform" class="button-primary formresetter" value="'.__('Reset All Fields','amulang').'" />';
					echo '	<input type="submit" name="createnewform" class="button-primary" value="'.__('Make New Form','amulang').'" />';
				}
			echo '</div>';
			
			//PRINT FORM LINES
			$counter = 0;
			echo '<div class="fieldsetwrap">';
				
				foreach($this->userData as $userLine) {
					
					$counter++;
					
					//start print
					if ($counter & 1) {
						echo '<div class="formwrap formwrapnum'.$counter.' wrapwhite">';
					} else {
						echo '<div class="formwrap formwrapnum'.$counter.' wrapgrey">';
					}
					
					echo '<span class="countline">'.$counter.'.</span>';
					
					$linecounter = 0;
		
					//start new form line
					echo '<div class="formline">';
						
					foreach($userLine as $stdKeyName=>$stdValue) {
						
						$linecounter++;
						
						//start new line every four vars
						if (($linecounter % 4 == 1) && ($linecounter != 1)) {
							echo '</div>';
							echo '<div class="formline">';
						}
						
						//retrieve data name and value
						$stdKeyName = strtolower($stdKeyName);
							
						//change display based on keyname if necessary
						
						if($stdKeyName == 'role') {
							
							echo '	<label for="role'.$counter.'" title="role">role</label>';
							echo '	<select name="role'.$counter.'" id="role'.$counter.'">';
							
							if($stdValue !== '') {
								$defrole = strtolower($stdValue);
							}
							
							foreach($this->lineroles as $eachrole) {
								$thisrole = $eachrole;
								echo '<option value="'.strtolower($thisrole).'"'; if(strtolower($thisrole)==strtolower($defrole)){echo ' selected="selected" ';} echo '>'.$thisrole.'</option>';
							}
							
							echo '	</select>';
							
						//all other fields display as text fields
						} else {
							
							if($stdKeyName == 'user_login') {
								$classes = ' class="valusername validatefield"';
							} else if($stdKeyName == 'user_pass') {
								$classes = ' class="valpassword validatefield"';
							} else if($stdKeyName == 'user_email') {
								$classes = ' class="valemail validatefield"';
							} else {
								$classes == '';
							}
							
							echo '	<label for="'.$stdKeyName.$counter.'" title="'.$stdKeyName.'">'.$stdKeyName.'</label>';
							echo '	<input type="text" name="'.$stdKeyName.$counter.'" id="'.$stdKeyName.$counter.'" value="'.$stdValue.'" '.$classes.'/>';
							
							unset($classes);
						}
					}
					echo '</div>';
				echo '</div>';
				}
				
			echo '</div>';
		
			//FORM TAIL
			echo '	<input type="hidden" readonly="readonly" name="processes" id="processes" value="'.$counter.'" />';
			
			//create proclist
			$procfields = '';
			foreach($this->userData[0] as $key => $param){
				$procfields .= $key.' ';
			}
			echo '	<input type="hidden" readonly="readonly" name="proclist" id="proclist" value="'.$procfields.'" />';

			echo '<div class="buttonline addextrasbutton">';
				echo '<input type="submit" name="addnewusers" class="button-primary" value="'.__('Add All Users','amulang').'" />';
				echo '<input type="button" name="addNewRow" class="button-primary button-right addNewRow" value="'.__('Add Extra Row','amulang').'" />';
			echo '</div>';
		
		//END FORM
		echo '</form>';
	}
	
}

/*
	* REGISTER USERS FROM FORM
	* takes input data from last step and creates new registrations
*/

function amuRegisterFromForm() {
	global $wpdb, $current_user;
	
	$procs = (int)$_POST['processes'];
	$proclist = explode(' ', trim($_POST['proclist']));
	$confirmationStack = array();
	
	if($procs > 0) {
		
		$line = 1;
		
		while($line <= $procs){
			
			$userInfoArray = array();
			
			foreach($proclist as $linevalue) {
				$userInfoArray[$linevalue] = trim($_POST[$linevalue.$line]);
			}
			
			//if username line is not blank
			if($userInfoArray['user_login'] !== '') {
				
				//create new user object
				$newUser = new amuUserObject($userInfoArray);
				
				//register initial user info
				$newid = $newUser->amuRegisterUser();
				
				if(is_int($newid)) {
					
					//update users data based on input
					$newUser->amuUpdateUserData();
					
					//send user a notification email
					$newUser->amuSendUserEmail();
					
					//add any additional meta data fields
					$newUser->amuUpdateUserMeta($userInfoArray);
					
					//set confirmation message
					$confirmUpdate = '<p><strong>'.$line.'</strong>. '.__('New user successfully registered.','amulang').' <strong>'.__('Login','amulang').':</strong> '.$newUser->user_login.' <strong>'.__('Password','amulang').':</strong> '.$newUser->user_pass.' <strong>'.__('Email','amulang').':</strong> '.$newUser->user_email.'</p>';
					
				} else {
					
					//return failure message
					$confirmUpdate = '<p>'.$line.'. '.$newid.'</p>';
					
				}
				
				//add success or failure message to stack
				$confirmationStack[] = $confirmUpdate;
				
				//kill reusable objects and arrays
				unset($newUser);
				unset($userInfoArray);
			}
			
			//increment line number
			$line++;
		}
		
		echo '<h3>'.__('Results of your new user registrations','amulang').'</h3>';
		
		//admin notifications
		$adminUser = new amuAdminObject();
		$sendRegResults = $adminUser->amuAdminConfirmation($confirmationStack);
		$stackDisplay = $adminUser->amuShowStack($confirmationStack);
		
		//print notifications to screen
		echo $sendRegResults;
		echo '<div class="stackwrap">'.$stackDisplay.'</div>';
		
	} else {
		echo '<p>'.__('Error retrieving processes. Please try again.','amulang').'</p>';
	}
}

/*
	* CREATE USER REGISTRATION
	* Creates user object from provided data and registers the user
*/

class amuUserObject{
		
	function __construct($userInfoArray) {
		
		//get amu options
		global $wpdb;
		$setAllRoles = get_option('amu_setallroles');
		$validateStrict = get_option( 'amu_validatestrict');
		$forceEmail = get_option('amu_forcefill');
		$dispnamePref = get_option('amu_dispnamedef');
				
		//create initial data
		foreach($userInfoArray as $key=>$value) {
			$this->$key = $value;
		}
		
		//sanitize user login
		if($validateStrict == 'yes') {
			$tempUserLogin = $this->user_login;
			$this->user_login = sanitize_user($tempUserLogin);
		}
		
		//create password if blank
		if($this->user_pass == '') {
			$this->user_pass = wp_generate_password();
		}
		
		//create forced email
		if($forceEmail == 'yes') {
			if($this->user_email == '') {
				$this->user_email = 'temp_'.$this->user_login.'@temp'.$this->user_login.'.fake';
			}
		}
		
		//override role attribute if in effect
		if($setAllRoles !== 'notset') {
			$this->role = $setAllRoles;
		}
		
		//set display name option
		if(!array_key_exists('display_name', $userInfoArray)) {
			
			if($dispnamePref == 'firstname') {
				$this->display_name = $this->first_name;
			} else if($dispnamePref == 'lastname') {
				$this->display_name = $this->last_name;
			} else if($dispnamePref == 'nickname') {
				$this->display_name = $this->nickname;
			} else if($dispnamePref == 'firstlast') {
				$this->display_name = $this->first_name.' '.$this->last_name;
			} else if($dispnamePref == 'lastfirst') {
				$this->display_name = $this->last_name.' '.$this->first_name;
			} else {
				$this->display_name = $this->user_login;
			}
			
			//final check to make sure display_name is not blank
			$tempdispname = str_replace (' ', '', $this->display_name);
			if($tempdispname == '') {
				$this->display_name = $this->user_login;
			}	
		}	
	}
	
	//REGISTER USER FUNCTION
	
	function amuRegisterUser(){
		
		global $wpdb;
		$validateEmail = get_option('amu_validatemail');
		
		//verify user_login and email are unique and exist
		if(username_exists($this->user_login)) {
			$newid = __('Error: a user with this user_login already exists. This user was not registered.','amulang');
		} else if($this->user_email == '') {
			$newid = __('Error: an email address for the user','amulang').' '.$this->user_login.' '.__('was not provided. This user was not registered.','amulang');
		} else if(email_exists($this->user_email)) {
			$newid = __('Error: a user with the user_email address','amulang').' '.$this->user_email.' '.__('already exists. This user was not registered.','amulang');
		} else if($validateEmail == 'yes' && !is_email($this->user_email)) {
			$newid = __('Error: The user_email provided','amulang').' '.$this->user_email.' '.__('was not valid. This user was not registered.','amulang');
		
		//passes all checks, create new user
		} else {
			$addnewuser = wp_create_user($this->user_login, $this->user_pass, $this->user_email);
			$wp_user_object = new WP_User($this->user_login);
			
			//set user role
			if($this->role !== '') {
				if ( is_multisite() ) {
					add_existing_user_to_blog( array( 'user_id' => $wp_user_object->ID, 'role' => $this->role ) );
				} else {
					$wp_user_object->set_role($this->role);
				}
			}
			//set response as new user id
			$newid = $wp_user_object->ID;
			//kill the user object
			unset($wp_user_object);
		}
		//return message
		return $newid;
	}
	
	//UPDATE STANDARD DATA FUNCTION
	
	function amuUpdateUserData() {
		
		global $wpdb;
		$wp_user_object = new WP_User($this->user_login);		
		$wpuservalues = array();
		
		//package user_data updates
		$wpuservalues['ID'] = $wp_user_object->ID;
		if($this->user_url && $this->user_url !== '') {
			$wpuservalues['user_url'] = $this->user_url;
		}
		if($this->user_registered && $this->user_registered !== '') {
			$wpuservalues['user_registered'] = $this->user_registered;
		}
		if($this->user_nicename && $this->user_nicename !== '') {
			$wpuservalues['user_nicename'] = $this->user_nicename;
		}
		if($this->display_name && $this->display_name !== '') {
			$wpuservalues['display_name'] = $this->display_name;
		}
		
		//run updates
		wp_update_user($wpuservalues);
		//kill wp_user_object
		unset($wp_user_object);
	}
	
	//UDPATE ADDITIONAL USER META DATA
		
	function amuUpdateUserMeta($userInfoArray) {
		
		global $wpdb;
		$wp_user_object = new WP_User($this->user_login);
		$metadataArray = array();
		
		//create initial data
		foreach($userInfoArray as $key=>$value) {
			$metadataArray[$key] = $value;
		}
		
		unset($metadataArray['user_login']);
		unset($metadataArray['user_pass']);
		unset($metadataArray['user_email']);	
		unset($metadataArray['role']);
		unset($metadataArray['display_name']);
		unset($metadataArray['user_url']);
		unset($metadataArray['user_registered']);
		unset($metadataArray['user_nicename']);
		unset($metadataArray['ignore']);
		
		//fix nickname if submitted but empty - nickname is a required meta field and must be filled
		if(isset($metadataArray['nickname'])) {
			if($metadataArray['nickname'] == '') {
				$metadataArray['nickname'] = $this->user_login;
			}
		}
		
		//run through remaining data that can be updated with update_user_meta
		foreach ($metadataArray as $key=>$value) {
			update_user_meta( $wp_user_object->ID, $key, $value );
		}
		
		//kill wp_user_object
		unset($wp_user_object);
		//kill user info array
		unset($metadataArray);
		
	}
	
	//SEND NOTIFICATION EMAIL
	
	function amuSendUserEmail() {
		
		global $wpdb;
		$thisBlogName = get_bloginfo('name');
		$thisBlogUrl = site_url();
		$thisLoginUrl = get_option('amu_siteloginurl');
		$userEmailSubject = get_option('amu_useremailhead');
		$userEmailMsg = get_option('amu_useremailtext');
		$userfromreply = get_option('amu_defadminemail');	
		
		//if notify users is active and email is not forced. run notification function
		if(get_option( 'amu_usernotify') == 'yes') {
			$isFakeEmail = substr($this->user_email, -4);
			if($isFakeEmail !== 'fake') {
				//set up email
				$to = $this->user_email;
				$emailkeywords = array('[sitename]', '[siteurl]', '[siteloginurl]', '[username]', '[password]', '[useremail]', '[fromreply]');
				$emailreplaces = array($thisBlogName, '<a href="'.$thisBlogUrl.'">'.$thisBlogUrl.'</a>','<a href="'.$thisLoginUrl.'">'.$thisLoginUrl.'</a>', $this->user_login, $this->user_pass, $to, $userfromreply);
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

} //end of userObject class


/*
	* ADMINSTRATOR FUNCTIONS
	* Various functions necesary to Administrator using the plugin
*/

class amuAdminObject{
		
	function __construct() {
		
		global $wpdb, $current_user;
		get_currentuserinfo();
		$this->adminEmail = $current_user->user_email;		
		$this->blogName = get_bloginfo('name');
		$this->yesConfirm = get_option('amu_confirmation');
		
		if(is_multisite()) {
			$this->ccEmails = get_site_option('amu_emailcopies');
		}
	}
	
	//SENDS ADMIN FULL LIST OF USER REGISTRATIONS
	
	function amuAdminConfirmation($confirmationStack) {
		
		//set up confirmation email
		$confirmTo = $this->adminEmail;
		$confirmSubject = __('New User Account Information for','amulang').' '.$this->blogName;
		$confirmMessage = '<p><strong>'.__('This email is to confirm new user accounts for your website generated using the Add Multiple Users plugin.','amulang').'</strong></p>
		<p>'.__('All errors have also been included for reference when re-entering failed registrations.','amulang').'</p>';

		if(empty($confirmationStack)) {
			$confirmMessage .= '<p>'.__('No user registration information was submitted.','amulang').'</p>';
		} else {
			$confirmMessage .= implode(' ', $confirmationStack);
		}
		//finish confirmation message
		$confirmMessage .= '<p><strong>'.__('End of message.','amulang').'</strong></p>';
		$confirmHeaders = 'From: '.$this->adminEmail.' <'.$this->adminEmail.'>' . "\r\n";
		add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
		
		//copy to emails in network copies list
		if(is_multisite()) {
			if($this->ccEmails !== '') {
				$copiesString = str_replace(' ', '', $this->ccEmails);
				$copyAddresses = explode(',', $copiesString);
				foreach($copyAddresses as $copyMail) {
					wp_mail($copyMail, $confirmSubject, $confirmMessage, $confirmHeaders);
				}
			}
		}
		
		//send email to site Admin if requested
		if ($this->yesConfirm == 'yes') {
			wp_mail($confirmTo, $confirmSubject, $confirmMessage, $confirmHeaders);
			return '<p class="important">'.__('This information has been emailed to your WordPress administrator email','amulang').' <'.$confirmTo.'></p>';
		} else {
			return '<p class="important">'.__('This information has not been emailed to you. If you need to keep this information for future use, please copy the data below and save it now.','amulang').'</p>';;
		}
		
	}
	
	//PRINTS STACK TO THE SCREEN
	
	function amuShowStack($confirmationStack) {
		
		$totalStack = '';
		
		if(empty($confirmationStack)) {
			return '<p>'.__('No user registration information was submitted.','amulang').'</p>';
		} else {
			$totalStack = implode(' ', $confirmationStack);
			return $totalStack;
		}
	}
	
}


/*
	* SETTING NOTIFCATIONS
	* Provides feedback on current plugin Settings
*/

class amuSettingsObject{
		
	function __construct() {
		
		global $wpdb;
		
		$this->amu_usernotify = get_option('amu_usernotify');
		$this->amu_confirmation = get_option('amu_confirmation');
		$this->amu_setallroles = get_option('amu_setallroles');
		$this->amu_validatestrict = get_option('amu_validatestrict');
		$this->amu_validatemail = get_option('amu_validatemail');
		$this->amu_forcefill = get_option('amu_forcefill');
		$this->amu_showblankmeta = get_option('amu_showblankmeta');
		$this->amu_dispnamedef = get_option('amu_dispnamedef');
		$this->amu_extrameta = get_option('amu_extrameta');
		$this->amu_colorderpref = get_option('amu_colorderpref');
		$this->amu_colorderpredef = get_option('amu_colorderpredef');
		
	}
	
	//is user notifications enabled
	function get_amu_usernotify() {
		if($this->amu_usernotify == 'yes') {
			return '<span><strong> &raquo; '.__('New user notifications are enabled.','amulang').'</strong> '.__('Each user will be sent a registration email.','amulang').'</span>';
		} else {
			return '<span><strong> &raquo; '.__('New user notifications are disabled.','amulang').'</strong> '.__('New users will not be sent a registration email.','amulang').'</span>';
		}
	}
	
	//is new account confirmation enabled
	function get_amu_confirmation() {
		if($this->amu_confirmation == 'yes') {
			return '<span><strong> &raquo; '.__('New user account confirmation email is enabled.','amulang').'</strong> '.__('You will be sent an email with all new user information.','amulang').'</span>';
		} else {
			return '<span><strong> &raquo; '.__('New user account confirmation email is disabled.','amulang').'</strong> '.__('You will not be sent an email with all new user information.','amulang').'</span>';
		}
	}
	
	//is set all roles enabled
	function get_amu_setallroles() {
		if($this->amu_setallroles == 'notset') {
			return '<span><strong> &raquo; '.__('You have not set a default role.','amulang').'</strong> '.__('Each user will be added with the role that appears in your data.','amulang').'</span>';
		} else {
			return '<span><strong> &raquo; '.__('Set All Roles is currently set to','amulang').' '.$this->amu_setallroles.'</strong> '.__('which will override all user roles.','amulang').'</span>';
		}
	}
	
	//is set all roles enabled
	function get_amu_emailsetallroles() {
		if($this->amu_setallroles == 'notset') {
			return '<span><strong> &raquo; '.__('You have not set a default role.','amulang').'</strong> '.__('Each user will be added with the default role set in your Site Settings.','amulang').'</span>';
		} else {
			return '<span><strong> &raquo; '.__('Set All Roles is currently set to','amulang').' '.$this->amu_setallroles.'</strong>. '.__('All users will be added with this role.','amulang').'</span>';
		}
	}
	
	//what display name prefernce is set
	function get_amu_dispnamedef() {
		return '<span><strong> &raquo; '.__('User display names will be set as','amulang').' '.$this->amu_dispnamedef.'</strong> '.__('where not specified in your data.','amulang').'</span>';
	}
	
	//is set all roles enabled
	function get_amu_colorderpref() {
		if($this->amu_colorderpref == 'dynamic') {
			return '<span><strong> &raquo; '.__('You have chosen to use the dynamic column ordering function.','amulang').'</strong> '.__('This function will be available in the next step.','amulang').'</span>';
		} else if($this->amu_colorderpref == 'predefined') {
			return '<span><strong> &raquo; '.__('You have predefined your CSV column order in the Settings.','amulang').'</strong> '.__('Ensure your column order is correct before continuing.','amulang').'</span>';
		} else if ($this->amu_colorderpref == 'static') {
			return '<span><strong> &raquo; '.__('You have chosen to define your column order on the next page.','amulang').'</strong> '.__('You will have to manually type in your column order in the next step.','amulang').'</span>';
		} else if ($this->amu_colorderpref == 'firstline') {
			return '<span> &raquo; '.__('You have chosen to read the first line of your CSV data as your default column order.','amulang').'</span>';
		}
	}
	
	//what display name prefernce is set
	function get_updatenow() {
		return '<span><strong> &raquo; '.__('Adjust your settings before continuing','amulang').'</strong> '.__('if you wish to change these settings.','amulang').'</span>';
	}
	
}

/*
	* COLUMN NAMES QUICK REFERENCE
	* Adds visible/invisible column name helper
*/

function amuColumnNamesHint() {
	
	echo '<p class="amuQuickRefHint"><span class="important"><a class="quickref" href="#">'.__('View the the Quick Reference','amulang').'</a> '.__('for a list of standard and non-standard column names.','amulang').'</span></p>';
	
	echo '<div id="amuQuickRef">';
		echo '<h3>'.__('Column Name Quick Reference','amulang').'</h3>
		
				<p>'.__('The following column names are standard WordPress database values in both the wp_user and wp_usermeta tables.','amulang').'<br>
				<strong>user_login, user_pass, user_email, user_url, user_nicename, user_registered, display_name, first_name, last_name, nickname, description, rich_editing, comment_shortcuts, admin_color, show_admin_bar_front, aim, yim, jabber</strong></p>
				
				<p>'.__('The column name','amulang').' <strong>role</strong> '.__('should be used where a user role is defined in your data. User levels and capabilities will be automatically created for that user.','amulang').'</p>
				
				<p>'.__('The column name','amulang').' <strong>ignore</strong> '.__('should be used where you want the plugin to ignore a column of data. You can use this several times if necessary.','amulang').'</p>
				
				<p>'.__('Any column name that does not match either standard, role or ignore column names will be added as an additional custom user meta data field for each user.','amulang').'</p>
				
			  <p><a class="quickref" href="#">'.__('Close the Quick Reference','amulang').'</a></p>';
	echo '</div>';
}

?>