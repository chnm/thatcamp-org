<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * CSV IMPORT INTERFACES AND FUNCTIONS
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

/*
	* CSV IMPORT FUNCTION MAIN INTERFACE
	* provides basic information and help links
*/

function amu_csvimport() {
	
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
		
			echo '<h2>'.__('Add Multiple Users - Import CSV Data','amulang').'</h2>';
			
			
			//STEP ONE - PASTE DATA OR CHOOSE FILE
			if (empty($_POST) || isset($_POST['cancel'])) {
				$csvinputerror = '';
				amuCSVInputMain($csvinputerror);
				
			//STEP TWO - READ AND CLEAN DATA
			} else if ( isset($_POST['readandclean'] ) ) {
				amuCSVShowData();
			
			//OPTION - PRINT TO FORM
			} else if ( isset($_POST['sendcsvtoform'] ) ) {
				amuCSVToForm();
			
			//OPTION - DIRECT REGISTER
			} else if ( isset($_POST['sendcsvtoreg'] ) ) {
				amuCSVToRegister();
				
			// REGISTER FROM FORM
			} else if( isset($_POST['addnewusers'] ) ){
				echo '<div id="message" class="updated">';
					echo '<p><strong>'.__('New User Accounts Processed.','amulang').'</strong></p>';
				echo '</div>';
				amuRegisterFromForm();
			
			//OTHERWISE, UNKNOWN REQUEST HANDLER
			} else {
				echo '<p>'.__('Unknown request. Please choose the CSV Import option from the menu.','amulang').'</p>';
			}
		
		//end wrap divs and function
		echo '</div>';
	echo '</div>';
}

/*
	* CSV IMPORT MAIN INTERFACE
	* provides content input and upload methods
*/

function amuCSVInputMain($csvinputerror){
	
	global $wpdb;
	
	echo '<div class="toolintro">';
		echo $csvinputerror;
		echo '<p><strong>'.__('Converts a list of comma-separated values (CSV) into new user registration information.','amulang').'</strong><br>
				'.__('Your information will be extracted and available to view and sort in the next step.','amulang').'</p>';
	echo '</div>';
	
	echo '<form method="post" enctype="multipart/form-data" class="amuform">';
	
		echo '<h3>'.__('Choose a CSV/TXT file to upload','amulang').'</h3>';
		
		echo '<div class="buttonline">';
			echo '<input name="csvuploader" id="csvuploader" type="file" />';
		echo '</div>';
		
		echo '<p><span class="important">'.__('Information in the text box below will be ignored if a file is found in the File browser above.','amulang').'</span></p>';
		
		echo '<h3>'.__('Or paste your CSV data in the box below','amulang').'</h3>';
		
		echo '<div class="formline">';
			echo '	<textarea name="csvpastebox" cols="50" rows="10" id="csvpastebox" class="textfillbox"></textarea>';
		echo '</div>';
		
		echo '<p class="informational">';
			$csvinfo = new amuSettingsObject();
			echo $csvinfo->get_amu_setallroles();
			echo $csvinfo->get_amu_dispnamedef();
			echo $csvinfo->get_amu_colorderpref();
			echo $csvinfo->get_updatenow();
		echo '</p>';
		
		echo '<div class="buttonline">';
			echo '<p><span class="important">'.__('Please ensure you have read the information on CSV importing functions and updated your Settings before proceeding.','amulang').'</span></p>';
			echo '<input type="submit" name="readandclean" id="readandclean" class="button-primary" value="'.__('Next Step','amulang').'" />';
			echo '<input type="reset" name="reset" id="reset" class="button-primary" value="'.__('Clear All','amulang').'" />';
		echo '</div>';
	
	echo '</form>';
	
	$infotype = 'csvinputpage';
	showPluginInfo($infotype);
}

/*
	* CSV IMPORT COLUMN ORDERING INTERFACE
	* displays uploaded/pasted data and provides column ordering method
*/

function amuCSVShowData(){
	
	global $wpdb;
	$proceed = true;
	
	//if both options are empty
	if($_FILES['csvuploader']['name'] == '' && $_POST['csvpastebox'] == '') {
		$csvinputerror = '<p class="amu_error">'.__('No information was found. Please choose a file or enter your information in the text box.','amulang').'</p>';
		$proceed = false;
		
	}
	//verify file is legit
	if($_FILES['csvuploader']['name'] !== '') {
		if (is_uploaded_file($_FILES['csvuploader']['tmp_name'])) {
			$allowedExtensions = array("txt","TXT","csv","CSV");
			if (!in_array(end(explode(".", strtolower($_FILES['csvuploader']['name']))), $allowedExtensions)) {
				$csvinputerror = '<p class="amu_error">'.__('Error: Not a valid file type! Only .csv and .txt files may be uploaded.','amulang').'</p>';
				$proceed = false;
			}
		}
	}
	
	//if all good, show next part
	if($proceed) {		
				
		//add contents of form if exists
		if($_FILES['csvuploader']['name'] !== '') {
			
			//set ini for mac-created files
			ini_set('auto_detect_line_endings',true);
			
			$thefiledata = file_get_contents($_FILES['csvuploader']['tmp_name']);
			$linecount = count(file($_FILES['csvuploader']['tmp_name']));
			
		//otherwise show pastebox data
		} else {
			$thefiledata = $_POST['csvpastebox'];
			$linecount = count($thefiledata);
		}
		
		//begin interface
		echo '<div class="toolintro">';
			echo '<p><strong>'.__('The following user information has been extracted from your data.','amulang').'</strong><br>'.__('Please ensure your column order matches the data order and click one of the registration buttons below to continue.','amulang').'</p>';
		echo '</div>';
		
		//start form
		echo '<form method="post" enctype="multipart/form-data" class="amuform">';
			
			echo '<h3>'.__('Data Column Ordering','amulang').'</h3>';
			amuAddFileSort();
			
			echo '<h3>'.__('Your CSV Data','amulang').'</h3>';
			echo '<div class="formline">';
				echo '<textarea name="filecontents" cols="50" rows="10" id="filecontents" class="textfillbox">'.$thefiledata.'</textarea>';
				echo '<p>'.__('Total lines found in this document','amulang').': '.$linecount.'</p>';
			echo '</div>';
		
			echo '<div class="buttonline">';
				echo '	<p><span class="important">'.__('Click the Create User Information Form button to convert this user information into a Form, allowing you to review and customise specific information.','amulang').'</span></p>';
				echo '	<input type="submit" name="sendcsvtoform" id="sendcsvtoform" class="button-primary" value="'.__('Create User Information Form','amulang').'" />';
				
				echo '	<p><span class="important">'.__('Alternatively, choose the Skip Form and Add Users option if you want to immediately add all users.','amulang').' <strong>'.__('Important','amulang').':</strong> '.__('If you are adding more than 100 users in one pass, it is recommended you use this option. Please see the information at the bottom of the screen for more info.','amulang').'</span></p>';
				echo '	<input type="submit" name="sendcsvtoreg" id="sendcsvtoreg" class="button-primary" value="'.__('Skip Form and Add Users','amulang').'" />';
			echo '</div>';
			
		echo '</form>';
		
		//show info
		if(get_option('amu_colorderpref') == 'dynamic') {
			$infotype = 'dynamicsort';
			showPluginInfo($infotype);
		}
		$infotype = 'csvinputpage';
		showPluginInfo($infotype);
		
	//otherwise, backtrack with errors
	} else {
		amuCSVInputMain($csvinputerror);
	}
}

/*
	* CSV IMPORT CONVERT DATA TO FORM
	* sends csv data to the form interface
*/

function amuCSVToForm(){
	
	global $wpdb;
	$rawCSVdata = $_POST['filecontents'];
	$parsedData = parse_csv($rawCSVdata);
	$userData = reorder_csv($parsedData);
	$source = 'csvlist';
	$newForm = new amuFormCreator($source,$userData);
	$newForm->amuCreateFormInterface();
	
	//show form field info
	$infotype = 'formfields';
	showPluginInfo($infotype);
}

/*
	* CSV IMPORT DIRECT REGISTER
	* sends csv data directly to register users
*/

function amuCSVToRegister(){
	
	global $wpdb;
	$rawCSVdata = $_POST['filecontents'];
	$parsedData = parse_csv($rawCSVdata);
	$reorderedData = reorder_csv($parsedData);
	$userData = json_decode($reorderedData);
	
	$confirmationStack = array();
	$line = 0;
	
	//pull out each user data array and process individually
	
	foreach($userData as $userRow) {
		
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
				
				//add any additional meta data fields
				$newUser->amuUpdateUserMeta($userRow);
				
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
	
}

/*
	* READ AND CLEAN CSV DATA
	* cleans junk from csv data, establishes lines, makes it all useable
*/

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

/*
	* REORDER CSV DATA
	* creates indexed json array of imported csv data
*/

function reorder_csv($pasteddata) {
	
	global $wpdb;
	$stardardMetaOpts = get_option('amu_showblankmeta');
	$extraMetaOpts = get_option('amu_extrameta');
	
	//CREATE CUSTOM ORDER BASED ON ORDER SHOWING METHOD
	$theColPrefUsed = get_option('amu_colorderpref');
	//customised order array
	$customorder = array();
	
	//get order from dynamic sorter
	if($theColPrefUsed == 'dynamic') {
		
		$dynamicfield = trim($_POST['finalsort']);
		$customorder = explode(',',$dynamicfield);
		
	//or get order from predefined list
	} else if($theColPrefUsed == 'predefined') {
		
		$definedOptions = json_decode(get_option('amu_colorderpredef'));
		foreach($definedOptions as $defoption) {
			array_push($customorder, $defoption->keyname);
		}
	
	//or get order from static box
	} else if($theColPrefUsed == 'static') {
		
		$postedOptions = trim($_POST['loadorderpref']);
		$strippedOptions = str_replace(' ','',$postedOptions);		
		$definedOptions = explode(',',$strippedOptions);
		
		foreach($definedOptions as $defoption) {
			array_push($customorder, $defoption);
		}
		
	//or get order from first line of csv data, below...
	} else if($theColPrefUsed == 'firstline') {
		foreach($pasteddata[0] as $orderpiece) {
			array_push($customorder, $orderpiece);
		}
		unset($pasteddata[0]);
	}
	
	//create empty array to store lines
	$datastore = array();
	$entryCount = 0;
	
	//loop through each line, print to array, add to master
	foreach($pasteddata as $thisline) {
		
		//array to store line data
		$newarrayline = array();
		
		foreach($customorder as $stdOption) {
			
			if($stdOption !== 'ignore') {
			
				$keyname = $stdOption.'_index';
				
				$$keyname = array_search($stdOption, $customorder);
				if($$keyname === false) {
					$newarrayline[$stdOption] = '';
				} else {
					$newarrayline[$stdOption] = $thisline[$$keyname];
				}
			}
		}
		
		//increment counter
		$entryCount++;
		
		//add new line to main array
		array_push($datastore, $newarrayline);
		
		//kill the array for reuse
		unset($newarrayline);	
	}
	
	//return the encoded data
	$data_rev = json_encode($datastore);
	return $data_rev;
}

/*
	* SHOW SORTING INTERFACE
	* Shows preferenced sorting mechanism
*/

function amuAddFileSort() {
	global $wpdb;
	
	//show data appropriate to chosen sorting method
	$getSortOrderPref = get_option('amu_colorderpref');
	
	//if dynamic, show box to fill with jquery stuff
	if($getSortOrderPref == 'dynamic') {
		include 'dynamicsorter.php';
		
	//if predefined, show predefined structure
	} else if($getSortOrderPref == 'predefined') {
		echo '<p><span class="important">'.__('You have predefined your CSV data column order as the following','amulang').':<br /><strong>';
		$sortpredef = get_option('amu_colorderpredef');
		if($sortpredef !== '') {
			$sortpredeffields = json_decode($sortpredef);
			$newSortPrefArr = array();
			foreach($sortpredeffields as $pso) {
				$newSortPrefArr[] = $pso->keyname;
			}
			$printablePSO = implode(',',$newSortPrefArr);
			echo $printablePSO;
		} else {
			_e('No column order has been defined! Please see the Settings page to specify your column order.','amulang');
		}
		echo '</strong><br />'.__('Ensure that where standard WordPress values are being used that your column names match the wp_user or wp_usermeta tables.','amulang').'</p>';
	
	//if static, provide box to type in structure
	} else if($getSortOrderPref == 'static') {
		echo '<div class="genoptionwrap">';
			echo '<div class="optionbox lastoption">';
				echo '<label for="loadorderpref">'.__('Specify your CSV column order here','amulang').' <em>('.__('no spaces, separate with commas','amulang').')</em>:</label>';
				echo '<input type="text" name="loadorderpref" id="loadorderpref" value="" />';
				amuColumnNamesHint();
			echo '</div>';	
		echo '</div>';
	
	//if using firstline, notify of choice
	} else if($getSortOrderPref == 'firstline') {
		echo '<p><span class="important"><strong>'.__('Your Column Order Preference is currently set to read the first line of your CSV data as the column order. This line has been included in your CSV data to modify if necessary.','amulang').'</strong>';
			amuColumnNamesHint();
	}
}

?>