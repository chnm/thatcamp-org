<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * Manual Entry Interface and Functions
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

/*
	* Main Manual Entry Interface
	* provides basic information and help links
*/

function amu_manual() {
	
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
	
	//begin wrap class
	echo '<div class="wrap">';
		echo '<div id="amu">';
		
			echo '<h2>'.__('Add Multiple Users - Manual Entry Form','amulang').'</h2>';
			
			//STEP ONE - SET LINES
			if (empty($_POST) || isset($_POST['createnewform'] ) ) {
				$manualInputError = '';
				amuGenerateManualForm($manualInputError);
				
			//STEP TWO - CREATE FORM
			} else if ( isset($_POST['formshow_manual'] ) ) {
				amuCreateManualForm();
			
			//STEP THREE - REGISTER USERS
			} else if( isset($_POST['addnewusers'] ) ){
				echo '<div id="message" class="updated">';
					echo '<p><strong>'.__('New User Accounts Processed.','amulang').'</strong></p>';
				echo '</div>';
				amuRegisterFromForm();
			
			//OTHERWISE, UNKNOWN REQUEST HANDLER
			} else {
				echo '<p>'.__('Unknown request. Please choose the Manual Entry option from the menu.','amulang').'</p>';
			}
		
		//end wrap divs and function
		echo '</div>';
	echo '</div>';
}

/*
	* GENERATE FIRST STEP DISPLAY
	* provides line number chooser and errors
*/

function amuGenerateManualForm($manualInputError) {
	
	echo '<div class="toolintro">';
		echo $manualInputError;
		echo '<p><strong>'.__('Please enter the number of users you wish to add and press the Create Blank Form button.','amulang').'</strong><br />
		'.__('If you need additional user meta fields in this form, please modify your Settings to include additional standard and custom options.','amulang').'</p>';
		echo '<p><span class="important">'.__('You don\'t have to be too specific here. You can add more as you go.','amulang').'</span></p>';
	echo '</div>';
	
	//begin form
	echo '<form method="post" enctype="multipart/form-data" class="amuform">';
	
		echo '<div class="buttonline">';
			echo '<label for="manualprocs">'.__('Number of new users','amulang').': </label>';
			echo '<input type="text" name="manualprocs" id="manualprocs" value="10" />';
		echo '</div>';
		
		echo '<p class="informational">';
			$csvinfo = new amuSettingsObject();
			echo $csvinfo->get_amu_setallroles();
			echo $csvinfo->get_amu_dispnamedef();
			echo $csvinfo->get_updatenow();
		echo '</p>';
		
		echo '<div class="buttonline">';
			echo '<input type="submit" name="formshow_manual" id="formshow_manual" class="button-primary" value="'.__('Create Blank Form','amulang').'" />';
		echo '</div>';
	
	echo '</form>';
}

/*
	* GENERATE MANUAL INPUT FORM
	* creates the form for manual input
*/

function amuCreateManualForm() {
	global $wpdb;

	//get number of lines
	$manprocs = $_POST['manualprocs'];
	
	//confirm post is not empty, is a number and is larger than zero
	if ($manprocs !== '' && ctype_digit($manprocs) && $manprocs > 0) {
		
		//create dummy arrays
		$userArray = array();
		$userEntryArray = array();
					
		//push standard values
		$userEntryArray['user_login'] = '';
		$userEntryArray['user_pass'] = '';
		$userEntryArray['user_email'] = '';
				
		//push role value, set if chosen in settings
		$theDefRole = get_option('amu_setallroles');
		if($theDefRole == 'notset') {
			$userEntryArray['role'] = '';
		} else {
			$userEntryArray['role'] = $theDefRole;
		}
				
		//send remaining standard values
		$userEntryArray['first_name'] = '';
		$userEntryArray['last_name'] = '';
		
		//push remaining standard meta options
		$stdamumetaoption = get_option('amu_showblankmeta');
		if($stdamumetaoption !== '') {
			$stdMetaOptions = json_decode(get_option('amu_showblankmeta'));
			foreach($stdMetaOptions as $stdmeta) {
				$userEntryArray[$stdmeta->keyname] = '';
			}
		}
		//push custom meta options
		$extraamumetaoptions = get_option('amu_extrameta');
		if($extraamumetaoptions !== '') {
			$extraMetaOptions = json_decode(get_option('amu_extrameta'));
			foreach($extraMetaOptions as $extmeta) {
				$userEntryArray[$extmeta->keyname] = '';
			}
		}
		
		//create array of empty arrays
		while($manprocs > 0) {
			//add blank array as many times as needed
			$userArray[] = $userEntryArray;
			//decrement
			$manprocs--;
		}
		
		//send to form creator
		$userData = json_encode($userArray);
		$source = 'manual';
		$newForm = new amuFormCreator($source,$userData);
		$newForm->amuCreateFormInterface();
		
		//show form field info
		$infotype = 'formfields';
		showPluginInfo($infotype);
		
	//error message
	} else {
		$manualInputError = '<p class="amu_error">'.__('Error: either the number you entered was zero, empty, or a non-numeric character was entered. Please try again.','amulang').'</p>';
		amuGenerateManualForm($manualInputError);
	}
}

?>