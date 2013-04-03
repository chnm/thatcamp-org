<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * Import Email List Interface and Functions
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

/*
	* EMAIL LIST IMPORT INTERFACE
	* provides basic information and help links
*/

function amu_emaillist() {
	
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
		
			<h2><?php _e('Add Multiple Users - Import Email List','amulang'); ?></h2>
			
            <?php
			//STEP ONE - SHOW EMAIL INPUT FIELDS
			if (empty($_POST) ) {
				$emailListError = '';
				addByEmailList($emailListError);
				$infotype = 'emaillisting';
				showPluginInfo($infotype);

			//OPTION - CREATE FORM FROM EMAIL LIST
			} else if ( isset($_POST['formshow_email'] ) ) {
				amuProcessEmailInput('form');
			
			//OPTION - REGISTER DIRECT FROM EMAIL LIST
			} else if( isset($_POST['skiprun_csvprocess'] ) ){
				echo '<div id="message" class="updated">';
					echo '<p><strong>'.__('New User Accounts Processed.','amulang').'</strong></p>';
				echo '</div>';
				amuProcessEmailInput('direct');
				
			// REGISTER FROM FORM
			} else if( isset($_POST['addnewusers'] ) ){
				echo '<div id="message" class="updated">';
					echo '<p><strong>'.__('New User Accounts Processed.','amulang').'</strong></p>';
				echo '</div>';
				amuRegisterFromForm();
			
			//OTHERWISE, UNKNOWN REQUEST HANDLER
			} else {
				echo '<p>'.__('Unknown request. Please choose the Import Email List option from the menu.','amulang').'</p>';
			}
		
		?>
		</div>
    </div>
    
<?php }

/*
	* PROCESS EMAIL INPUT
	* prints email list to form interface or directly registers
*/

function amuProcessEmailInput($regType) {
	
	global $wpdb;
	
	//clean up input
	$rawEmailList = trim($_POST['emailfillbox']);
	$order = array('\r\n', '\n', '\r', ' ');
	$replace = '';
	$cleanEmailList = str_replace($order, $replace, $rawEmailList);
	
	//verify array is not empty
	if($cleanEmailList !== '') {
		$verifySymbol = strpos($cleanEmailList, '@');
		if ($verifySymbol == false) {
			$emailListError = '<p class="amu_error">'.__('Error: No valid email addresses were found in your input data! Please try again.','amulang').'</p>';
			addByEmailList($emailListError);
			$infotype = 'emaillisting';
			showPluginInfo($infotype);
		} else {
			
			//new array for user data
			$userArray = array();
			$emailListArray = explode(',',$cleanEmailList);
			
			foreach($emailListArray as $userEntry) {
				
				//create username/email vars
				$em_useremail = $userEntry;
				$pos = strpos($em_useremail, '@');
				$em_username = substr($em_useremail, 0, $pos);
				
				//verify is email address
				if($pos !== false) {
					
					$userEntryArray = array();
					
					//push some values plus some dummy ones					
					$userEntryArray['user_login'] = $em_username;
					$userEntryArray['user_pass'] = '';
					$userEntryArray['user_email'] = $em_useremail;
					
					//push role variable if it exists
					$theDefRole = get_option('amu_setallroles');
					if($theDefRole == 'notset') {
						$userEntryArray['role'] = '';
					} else {
						$userEntryArray['role'] = $theDefRole;
					}
					
					if($regType == 'form') {
					
						//send some other ones
						$userEntryArray['first_name'] = '';
						$userEntryArray['last_name'] = '';
						
						//add selected standard values
						$stdamumetaoption = get_option('amu_showblankmeta');
						if($stdamumetaoption !== '') {
							$stdMetaOptions = json_decode(get_option('amu_showblankmeta'));
							foreach($stdMetaOptions as $stdmeta) {
								$userEntryArray[$stdmeta->keyname] = '';
							}
						}
						//add custom meta options
						$extraamumetaoptions = get_option('amu_extrameta');
						if($extraamumetaoptions !== '') {
							$extraMetaOptions = json_decode(get_option('amu_extrameta'));
							foreach($extraMetaOptions as $extmeta) {
								$userEntryArray[$extmeta->keyname] = '';
							}
						}
					}
					
					//add this entry array to full user data array
					$userArray[] = $userEntryArray;
					
					//kill it for reuse
					unset($userEntryArray);
				}
			
			}
			
			//encode full user array
			$userData = json_encode($userArray);
			
			//send to form creator
			if($regType == 'form') {
				$source = 'email';
				$newForm = new amuFormCreator($source,$userData);
				$newForm->amuCreateFormInterface();
				
				
				//show form field info
				$infotype = 'formfields';
				showPluginInfo($infotype);
			
			//or process direct
			} else if($regType == 'direct') {
				
				$eachUserData = json_decode($userData);
				$confirmationStack = array();
				$line = 0;
				
				foreach($eachUserData as $userRow) {
					
					$line++;
		
					if($userRow->user_login !== '') {
					
						//create new user object
						$newUser = new amuUserObject($userRow);
							
						//register initial user info
						$newid = $newUser->amuRegisterUser();
						
						if(is_int($newid)) {
								
							//update users data based on input
							$newUser->amuUpdateUserData();
							
							//send user a notification email
							$newUser->amuSendUserEmail();
							
							//set confirmation message
							$confirmUpdate = '<p><strong>'.$line.'</strong>. '.__('New user successfully registered.','amulang').' <strong>'.__('Login','amulang').':</strong> '.$newUser->user_login.' <strong>'.__('Password','amulang').':</strong> '.$newUser->user_pass.' <strong>'.__('Email','amulang').':</strong> '.$newUser->user_email.'</p>';
							
						} else {
							
							//return failure message
							$confirmUpdate = '<p>'.$line.'. '.$newid.'</p>';
							
						}
						
						//kill reusable object
						unset($newUser);
						
					} else {
						
						//return failure message
						$confirmUpdate = '<p>'.$line.'. '.__('No user_login was found. This line was skipped.','amulang').'</p>';
					}
					
					//add success or failure message to stack
					$confirmationStack[] = $confirmUpdate;
				}
				
				echo '<h3>'.__('Results of your new user registrations','amulang').'</h3>';
				//admin notifications
				$adminUser = new amuAdminObject();
				$sendRegResults = $adminUser->amuAdminConfirmation($confirmationStack);
				$stackDisplay = $adminUser->amuShowStack($confirmationStack);
				
				//print notifications to screen
				echo $sendRegResults;
				echo '<div class="stackwrap">'.$stackDisplay.'</div>';
				
			//or throw error
			} else {
				echo '<p>'.__('Unknown request.','amulang').'</p>';
			}
			
		}
	
	} else {
		$emailListError = '<p class="amu_error">'.__('Error: No valid email addresses were found in your input data! Please try again.','amulang').'</p>';
		addByEmailList($emailListError);
		$infotype = 'emaillisting';
		showPluginInfo($infotype);
	}
}

/*
	* EMAIL LIST SUBMISSION INTERFACE
	* prints interface for adding email data and choosing reg options
*/

function addByEmailList($emailListError) { ?>

	<div class="toolintro">
		<?php echo $emailListError; ?>
		<p><strong><?php _e('Enter the email addresses of the users you wish to add to your WordPress site, separated by commas.','amulang'); ?></strong></p>
		<p><?php _e('User Logins are automatically created from the first part of each email address you enter.','amulang'); ?></p>
	</div>
	
	<p class="informational">
    	<?php
			$csvinfo = new amuSettingsObject();
			echo $csvinfo->get_amu_emailsetallroles();
			echo $csvinfo->get_updatenow();
		?>
	</p>
	
	<form method="post" enctype="multipart/form-data" class="amuform">
	
		<div class="formline">
			<textarea name="emailfillbox" cols="50" rows="10" id="emailfillbox" class="textfillbox"></textarea>
		</div>
	
		<div class="buttonline">
		
			<p><span class="important"><?php _e('Click the Create User Information Form button below to convert this user information into a Form, allowing you to review and customise specific information.','amulang'); ?></span></p>
			<input type="submit" name="formshow_email" id="formshow_email" class="button-primary" value="<?php esc_attr_e('Create User Information Form','amulang'); ?>" />
		
			<p><span class="important"><?php _e('Alternatively, choose the Skip Form and Add Users option if you want to immediately add all users. If you are adding more than 100 users in one pass, it is recommended you use this option. Please see the information at the bottom of the screen for more info.','amulang'); ?></span></p>
			<input type="submit" name="skiprun_csvprocess" id="skiprun_csvprocess" class="button-primary" value="<?php esc_attr_e('Skip Form and Add Users','amulang'); ?>" />
		</div>
	
	</form>
    
<?php } ?>