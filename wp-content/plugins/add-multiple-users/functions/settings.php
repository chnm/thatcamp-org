<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * Plugin Information screens
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

/*
	* PLUGIN SETTINGS INTERFACE
	* Review and set multisite network options
*/

function amu_settings() {
	
	//test again for admin priviledges
	if (!current_user_can('manage_options') )  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	//set network options if they don't exist
	amu_set_default_network_options();
	
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
					echo '<p><strong>'.__('Settings have been saved.','amulang').'</strong></p>';
				echo '</div>';
			}
			
			//if resetting settings
			if ( isset($_POST['resetsettings'] ) ) {
				amu_reset_default_options();
				echo '<div id="message" class="updated">';
					echo '<p><strong>'.__('Settings have been reset to default.','amulang').'</strong></p>';
				echo '</div>';
			}
			
			addGeneralOptions();
			
			$infotype = 'settings';
			showPluginInfo($infotype);
	
		echo '</div>';
	echo '</div>';
}

/*
	* SHOW SETTINGS INTERFACE
	* Show options for modifying settings
*/

function addGeneralOptions() {
	global $current_user, $wpdb, $wp_roles;
    get_currentuserinfo();
	
	echo '<div class="toolintro">';
		echo '<h2>'.__('Add Multiple Users - Plugin Settings','amulang').'</h2>';
		echo '<p>'.__('Update settings that affect various plugin functions. Please check the information section at the bottom of this page for more about each setting.','amulang').'</p>';
	echo '</div>';
	
	echo '<form method="post" enctype="multipart/form-data" class="amuform">';
	
		echo '<h3>'.__('Validation and Notifications','amulang').'</h3>';
		
		echo '<div class="genoptionwrap">';
		
			//send emails...
			echo '<div class="optionbox">';
			echo '	<label for="sendpswemails">'.__('Send each new user a registration notification email?','amulang').'</label>';
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
			echo '	<label for="confirmEmail">'.__('Email me a complete list of new user account details?','amulang').'</label>';
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
			echo '	<label for="validatemail">'.__('Validate entered user_email address format?','amulang').' <em>('.__('uses WordPress "is_email" validation method','amulang').')</em></label>';
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
			echo '	<label for="validateStrict">'.__('Sanitize user_login using Strict method?','amulang').' <em>('.__('allows only alphanumeric and _, space, ., -, *, and @ characters','amulang').')</em></label>';
			echo '	<input name="validateStrict" id="validateStrict" type="checkbox" ';
			//retrieve option if set
			if (get_option('amu_validatestrict')) {
				if ( get_option('amu_validatestrict') == 'yes') {
					echo 'checked="checked"';
				}
			}
			echo ' value="userstrict" />';
			echo '</div>';
			
			////auto-create user names - for version 2.1
//			echo '<div class="optionbox">';
//			echo '	<label for="createUserLogin">Auto-create user_login where empty? <em>(prevents registration failure if user_login is missing)</em></label>';
//			echo '	<input name="createUserLogins" id="createUserLogins" type="checkbox" ';
//			//retrieve option if set
//			if (get_option('amu_createuserlogins')) {
//				if ( get_option('amu_createuserlogins') == 'yes') {
//					echo 'checked="checked"';
//				}
//			}
//			echo ' value="usercreatelogin" />';
//			echo '</div>';
				
			//force fill emails...
			echo '<div class="optionbox lastoption">';
			echo '	<label for="forcefillemail">'.__('Force Fill empty user_email addresses?','amulang').' <em>('.__('not recommended - see bottom of page for details','amulang').')</em></label>';
			echo '	<input name="forcefillemail" id="forcefillemail" type="checkbox" ';
			//retrieve option if set
			if (get_option('amu_forcefill')) {
				if ( get_option('amu_forcefill') == 'yes') {
					echo 'checked="checked"';
				}
			}
			echo ' value="fill" />';
			echo '</div>';
				
		echo '</div>';
		
		//set default user role option
		echo '<h3>'.__('New User Defaults and Overrides','amulang').'</h3>';
		
		echo '<div class="genoptionwrap">';
			echo '<div class="optionbox">';
				//set all users to this role
			if (get_option('amu_setallroles')) {
				$rolesel = get_option('amu_setallroles');
			} else {
				$rolesel = 'notset';
			}
			$roles = $wp_roles->get_names();
			echo '	<label for="allToRole">'.__('Ignore individual User Role settings and set all new users to this role','amulang').': </label>';
			echo '	<select name="allToRole" id="allToRole">';
			echo '		<option value="notset"'; if($rolesel=='notset'){echo ' selected="selected" ';} echo '>'.__('no, set individually...','amulang').'</option>';

			foreach($roles as $role) {
				$thisrole = $role;
				echo '<option value="'.strtolower($thisrole).'"'; if(strtolower($rolesel)==strtolower($thisrole)){echo ' selected="selected" ';} echo '>'.$thisrole.'</option>';
			}
			echo '	</select>';
			echo '</div>';
			
			//set display name option
			if (get_option('amu_dispnamedef')) {
				$defaultDispName = get_option('amu_dispnamedef');
			} else {
				$defaultDispName = 'userlogin';
			}
			echo '<div class="optionbox lastoption">';
			echo '	<label for="defaultdispname">'.__('Where not provided, set new user Display Name preference as','amulang').':</label>';
			echo '	<select name="defaultdispname" id="defaultdispname">';
			echo '		<option value="userlogin"'; if($defaultDispName=='userlogin'){echo ' selected="selected" ';} echo '>'.__('User Login','amulang').'</option>';
			echo '		<option value="firstname"'; if($defaultDispName=='firstname'){echo ' selected="selected" ';} echo '>'.__('First Name','amulang').'</option>';
			echo '		<option value="lastname"'; if($defaultDispName=='lastname'){echo ' selected="selected" ';} echo '>'.__('Last Name','amulang').'</option>';
			echo '		<option value="firstlast"'; if($defaultDispName=='firstlast'){echo ' selected="selected" ';} echo '>'.__('First then Last Name','amulang').'</option>';
			echo '		<option value="lastfirst"'; if($defaultDispName=='lastfirst'){echo ' selected="selected" ';} echo '>'.__('Last then First Name','amulang').'</option>';
			echo '		<option value="nickname"'; if($defaultDispName=='nickname'){echo ' selected="selected" ';} echo '>'.__('Nickname','amulang').'</option>';
			echo '	</select>';
			echo '</div>';
			
		echo '</div>';
		
		//set default sorter preference
		echo '<h3>'.__('CSV Column Ordering','amulang').'</h3>';
		
		echo '<div class="genoptionwrap">';
		
			echo '<div class="optionbox">';
				//column ordering preference
				$colpref = get_option('amu_colorderpref');
				
				echo '	<label for="sorterpreference">'.__('Choose which method of column ordering you would like to use','amulang').': </label>';
				echo '	<select name="sorterpreference" id="sorterpreference">';
				echo '		<option value="dynamic"'; if($colpref=='dynamic'){echo ' selected="selected" ';} echo '>'.__('Dynamic Sorting on Import','amulang').'</option>';
				echo '		<option value="predefined"'; if($colpref=='predefined'){echo ' selected="selected" ';} echo '>'.__('Predefined Below','amulang').'</option>';
				echo '		<option value="static"'; if($colpref=='static'){echo ' selected="selected" ';} echo '>'.__('Manual Entry on Import','amulang').'</option>';
				echo '		<option value="firstline"'; if($colpref=='firstline'){echo ' selected="selected" ';} echo '>'.__('Use First Line of CSV','amulang').'</option>';
				echo '	</select>';
			echo '</div>';
			
			//predefined sort order value
			$sortpredef = get_option('amu_colorderpredef');
			
			echo '<div class="optionbox lastoption">';
			echo '	<label for="sortorderpredef">'.__('Predefined column order to use when importing CSV data','amulang').' <em>('.__('no spaces, separate with commas','amulang').')</em>: </label>';
			echo '	<input type="text" name="sortorderpredef" id="sortorderpredef" value="';
			
			if($sortpredef !== '') {
				
				$sortpredeffields = json_decode($sortpredef);
				$newSortPrefArr = array();
				foreach($sortpredeffields as $pso) {
					$newSortPrefArr[] = $pso->keyname;
				}
				$printablePSO = implode(',',$newSortPrefArr);
				echo $printablePSO;
			}
			
			echo '" />';
			
			amuColumnNamesHint();
			
			echo '</div>';
		
		echo '</div>';
		
		//meta data customisation
		echo '<h3>'.__('Manual Entry User Meta Data','amulang').'</h3>';
		
		echo '<div class="genoptionwrap">';
		
			//show additional meta options on blank form
			echo '<div class="optionbox metaoptionbox">';
			echo '	<p>'.__('Make additional WordPress Standard meta data fields available on Form interface','amulang').':</p>';
			
			$blankmeta = get_option('amu_showblankmeta');
			if($blankmeta !== '') {
				$blanksettings = json_decode(get_option('amu_showblankmeta'));
			} else {
				$blanksettings = array('none'=>'noextras');
			}
			
			echo '	<span>'.__('User Url', 'amulang').': <input name="meta_user_url" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'user_url') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Nicename', 'amulang').': <input name="meta_user_nicename" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'user_nicename') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Nickname', 'amulang').': <input name="meta_nickname" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'nickname') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Display Name', 'amulang').': <input name="meta_displayname" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'display_name') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('User Registered', 'amulang').': <input name="meta_userregistered" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'user_registered') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Description', 'amulang').': <input name="meta_description" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'description') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Admin Bar Front', 'amulang').': <input name="meta_barfront" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'show_admin_bar_front') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Rich Editing', 'amulang').': <input name="meta_richedit" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'rich_editing') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Comment Shortcuts', 'amulang').': <input name="meta_comshort" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'comment_shortcuts') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Admin Color', 'amulang').': <input name="meta_admincolor" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'admin_color') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('AIM', 'amulang').': <input name="meta_aim" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'aim') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Yahoo IM', 'amulang').': <input name="meta_yim" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'yim') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			echo '	<span>'.__('Jabber', 'amulang').': <input name="meta_jabber" type="checkbox"'; foreach($blanksettings as $setting) { if($setting->keyname == 'jabber') { echo 'checked="checked"';}} echo ' value="fill" /></span>';
			
			echo '</div>';
			
			//additional meta fields
			$extrameta = get_option('amu_extrameta');
			
			echo '<div class="optionbox lastoption">';
			echo '	<label for="additionalmeta">'.__('Make additional custom meta fields available on Form interface', 'amulang').' <em>('.__('no spaces, separate with commas', 'amulang').')</em>: </label>';
			echo '	<input type="text" name="additionalmeta" id="additionalmeta" value="';
			
			if($extrameta !== '') {
				
				$extraMetaFields = json_decode($extrameta);
				$newExtraMetaArr = array();
				foreach($extraMetaFields as $emf) {
					$newExtraMetaArr[] = $emf->keyname;
				}
				$printableEMA = implode(',',$newExtraMetaArr);
				echo $printableEMA;
			}
			echo '" />';
			
			echo '</div>';
			
			echo '<p><span class="important">'.__('Standard and Custom meta options you define here will be available on the Form using the Manual Entry function or when creating a user information form from the Import Email List function.', 'amulang').'</span></p>';
		
		echo '</div>';
		
		echo '<h3>'.__('Customize New User Notification Email', 'amulang').'</h3>';
		
		echo '<div class="genoptionwrap emailcustbox">';
			//customize new user delivered email
			echo '<div class="optionbox lastoption">';
				
				$msghead = get_option('amu_useremailhead');
				$msgtext = get_option('amu_useremailtext');
				$sendemailsto = get_option('amu_defadminemail');
				$siteloginurl = get_option('amu_siteloginurl');
				
				echo '<div class="optioninstructions">';
				echo '	<p><strong>'.__('Use the following settings to modify the notification email that new users receive when added via the plugin.', 'amulang').'</strong></p>';
				echo '	<p>'.__('Please refer to the Information about Settings options at the bottom of this page for more information about these settings.', 'amulang').'</p>';
				echo '	<p>'.__('Valid shortcodes are', 'amulang').': [sitename] [siteurl] [siteloginurl] [username] [password] [useremail] [fromreply]</p>';
				echo '</div>';
				
				//from email
				echo '<div class="optionbox">';
					echo '<label for="custademail" class="custheadlabel">'.__('From/Reply Address', 'amulang').': </label>';
					echo '<input type="text" name="custademail" id="custademail" value="'.$sendemailsto.'" class="custheadfield" />';
				echo '</div>';
				
				//custom login url
				echo '<div class="optionbox">';
					echo '<label for="custlogurl" class="custheadlabel">'.__('Site Login URL', 'amulang').': </label>';
					echo '<input type="text" name="custlogurl" id="custlogurl" value="'.$siteloginurl.'" class="custheadfield" />';
				echo '</div>';
				
				//message header
				echo '<div class="optionbox">';
					echo '<label for="custemailhead" class="custheadlabel">'.__('Email Subject', 'amulang').': </label>';
					echo '<input type="text" name="custemailhead" id="custemailhead" value="'.$msghead.'" class="custheadfield" />';
				echo '</div>';
				
				//message text
				echo '<div class="optionbox lastoption">';
					echo '<label for="customemailtext" class="custheadlabel">'.__('Email Message (HTML format)', 'amulang').': </label>';
					echo '<textarea name="customemailtext" cols="50" rows="10" id="customemailtext" class="textfillbox">'.$msgtext.'</textarea>';
				echo '</div>';
			
			echo '</div>';
			
			//add dynamic emailer button to customisation box for testing email
			echo '<input type="button" name="testCustomEmail" id="testCustomEmail" class="button-primary button-right" value="'.__('Send Test Email','amulang').'" />';
			echo '<p><span class="important">'.__('Test emails will be sent to your administrator email.','amulang').'</span></p>';
	
					
		echo '</div>';
		echo '<div class="buttonline">';
		echo '	<input type="submit" name="setgenopt" id="setgenopt" class="button-primary" value="'.__('Save Settings', 'amulang').'" />';
		echo '	<input type="submit" name="resetsettings" id="resetsettings" class="button-primary" value="'.__('Reset to Default Settings', 'amulang').'" />';
		echo '</div>';
	echo '</form>';
}

/*
	* SAVE SETTINGS FUNCTION
	* Update settings in wp_options table
*/

function setGeneralOptions() {
	global $current_user, $wpdb;
    get_currentuserinfo();
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
	if ( isset($_POST['showblankmeta'] ) ) {
		$showblankmeta = 'yes';
	} else {
		$showblankmeta = 'no';
	}
	if ( isset($_POST['defaultdispname'] ) ) {
		$defDispName = $_POST['defaultdispname'];
	} else {
		$defDispName = 'userlogin';
	}
	$emailCustomHead = $_POST['custemailhead'];
	$emailCustomText = stripslashes($_POST['customemailtext']);
	$emailFromAddr = $_POST['custademail'];
	$emailsiteLog = $_POST['custlogurl'];
	$colOrderPref = $_POST['sorterpreference'];
	
	//create json string of predefined sorting order
	$rawColOrderPreDef = trim($_POST['sortorderpredef']);
	$tempColOrderPreDef = str_replace(' ', '', $rawColOrderPreDef);
	
	if ($tempColOrderPreDef == '') {
		$colOrderPreDef = '';
	} else {
		$colOrderPrefArray = explode(',',$tempColOrderPreDef);
		$fullColDefArray = array();
		foreach($colOrderPrefArray as $fpso) {
			array_push($fullColDefArray, array('keyname'=>$fpso));
		}
		$colOrderPreDef = json_encode($fullColDefArray);
	}
	
	//create json string of custom meta fields
	$rawAddMetaFields = trim($_POST['additionalmeta']);
	$tempAddMetaFields = str_replace(' ', '', $rawAddMetaFields);
	if ($tempAddMetaFields == '') {
		$additionalMetaFields = '';
	} else {
		$addMetaExpArray = explode(',',$tempAddMetaFields);
		$fullAddMetaArray = array();
		foreach($addMetaExpArray as $cmv) {
			array_push($fullAddMetaArray, array('keyname'=>$cmv));
		}
		$additionalMetaFields = json_encode($fullAddMetaArray);
	}
	
	//show meta options array
	$additionalMeta = array();
	//add options
	if ( isset($_POST['meta_user_url'] ) ) {
		array_push($additionalMeta, array('keyname'=>'user_url', 'dispname'=>'User URL', 'type'=>'text'));
	}
	if ( isset($_POST['meta_user_nicename'] ) ) {
		array_push($additionalMeta, array('keyname'=>'user_nicename', 'dispname'=>'Nicename', 'type'=>'text'));
	}
	if ( isset($_POST['meta_nickname'] ) ) {
		array_push($additionalMeta, array('keyname'=>'nickname', 'dispname'=>'Nickname', 'type'=>'text'));
	}
	if ( isset($_POST['meta_displayname'] ) ) {
		array_push($additionalMeta, array('keyname'=>'display_name', 'dispname'=>'Display Name', 'type'=>'text'));
	}
	if ( isset($_POST['meta_userregistered'] ) ) {
		array_push($additionalMeta, array('keyname'=>'user_registered', 'dispname'=>'Registered', 'type'=>'date'));
	}
	if ( isset($_POST['meta_description'] ) ) {
		array_push($additionalMeta, array('keyname'=>'description', 'dispname'=>'Description', 'type'=>'textbox'));
	}
	if ( isset($_POST['meta_barfront'] ) ) {
		array_push($additionalMeta, array('keyname'=>'show_admin_bar_front', 'dispname'=>'Admin Bar', 'type'=>'check'));
	}
	if ( isset($_POST['meta_richedit'] ) ) {
		array_push($additionalMeta, array('keyname'=>'rich_editing', 'dispname'=>'Rich Editing', 'type'=>'check'));
	}
	if ( isset($_POST['meta_comshort'] ) ) {
		array_push($additionalMeta, array('keyname'=>'comment_shortcuts', 'dispname'=>'Comment Shortcuts', 'type'=>'check'));
	}
	if ( isset($_POST['meta_admincolor'] ) ) {
		array_push($additionalMeta, array('keyname'=>'admin_color', 'dispname'=>'Admin Color', 'type'=>'select'));
	}
	if ( isset($_POST['meta_aim'] ) ) {
		array_push($additionalMeta, array('keyname'=>'aim', 'dispname'=>'AIM', 'type'=>'text'));
	}
	if ( isset($_POST['meta_yim'] ) ) {
		array_push($additionalMeta, array('keyname'=>'yim', 'dispname'=>'Yahoo IM', 'type'=>'text'));
	}
	if ( isset($_POST['meta_jabber'] ) ) {
		array_push($additionalMeta, array('keyname'=>'jabber', 'dispname'=>'Jabber', 'type'=>'text'));
	}

	//encode array
	if(empty($additionalMeta)) {
		$showMetaFields = '';
	} else {
		$showMetaFields = json_encode($additionalMeta);
	}
	//update amu options
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
	update_option( 'amu_showblankmeta', $showMetaFields );
	update_option( 'amu_dispnamedef', $defDispName );
	update_option( 'amu_extrameta', $additionalMetaFields );
	update_option( 'amu_colorderpref', $colOrderPref );
	update_option( 'amu_colorderpredef', $colOrderPreDef );
}

/*
	* RESET SETTINGS FUNCTION
	* Set options back to default installation
*/

function amu_reset_default_options() {
	global $current_user, $wpdb;
    get_currentuserinfo();
	$defaultAdminEmail = $current_user->user_email;
	$sitelogurl = site_url();
	$defaultUserEmailHead = __('Your New User Account Information on', 'amulang').' [sitename]';
	$defaultUserEmailText = '<h1>'.__('You have been registered as a user on', 'amulang').' [sitename]</h1>
<p>'.__('You may now log into the site at', 'amulang').' [siteloginurl]</p>
<p>'.__('Your username is', 'amulang').' [username] '.__('and your password is', 'amulang').' [password]</p>
<p>'.__('Regards', 'amulang').',<br>
[sitename] '.__('Admin', 'amulang').'</p>
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
	update_option( 'amu_showblankmeta', '' );
	update_option( 'amu_dispnamedef', 'userlogin' );
	update_option( 'amu_extrameta', '' );
	update_option( 'amu_colorderpref', 'dynamic' );
	update_option( 'amu_colorderpredef', '' );
}

?>