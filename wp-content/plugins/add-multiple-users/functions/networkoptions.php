<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * Network Options and Add Existing functions
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

/*
	* NETWORK OPTIONS INTERFACE
	* Review and set multisite network options
*/

function amu_networksite() {
	if (!current_user_can('manage_network') )  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	//set network options if they don't exist
	amu_set_default_network_options();
	
	global $wpdb;
	
	//begin wrap class
	echo '<div class="wrap">';
		echo '<div id="amu">';
		
			//if saving network options...
			if ( isset($_POST['setnetworkopt'] ) ) {
				setNetworkOptions();
				echo '<div id="message" class="updated">';
					echo '<p><strong>'.__('Network settings have been updated.','amulang').'</strong></p>';
				echo '</div>';
			}
			//if resetting network options...
			if ( isset($_POST['resetnetworksettings'] ) ) {
				amu_reset_network_options();
				echo '<div id="message" class="updated">';
					echo '<p><strong>'.__('Network settings have been reset.','amulang').'</strong></p>';
				echo '</div>';
			}
			
			//show network settings options
			echo '<h2>'.__('Add Multiple Users (Network)','amulang').'</h2>';
			
			echo '<p>'.__('These options allow you to control global access, functions and options on subsites of your current network.','amulang').'</p>';
			
			echo '<div class="toolintro">';
				echo '<h3>'.__('Network Settings','amulang').'</h3>';
				echo '<p><span class="important">'.__('Please refer to the Information at the bottom of this page for more information about these settings.','amulang').'</span></p>';
				echo '<h4>'.__('Subsite Plugin Access','amulang').'</h4>';
			echo '</div>';
			
			echo '<form method="post" class="amuform">';
			
				echo '<div class="genoptionwrap">';
						
					//allow subsite access to plugin
					echo '<div class="optionbox">';
					echo '	<label for="net_allowadmins">'.__('Allow site Administrators access to AMU plugin?','amulang').'</label>';
					echo '	<input name="net_allowadmins" id="net_allowadmins" type="checkbox" ';
					if (get_site_option('amu_subadminaccess')) {
						if ( get_site_option('amu_subadminaccess') == 'yes') {
							echo 'checked="checked"';
						}
					} else {
						echo 'checked="checked"';
					}
					echo ' value="yes" />';
					echo '</div>';
					
					//allow access to Add Existing option
					echo '<div class="optionbox">';
					echo '	<label for="net_addexist">'.__('Allow site Administrators to add users from the Network users list?','amulang').'</label>';
					echo '	<input name="net_addexist" id="net_addexist" type="checkbox" ';
					if (get_site_option('amu_addexistingaccess')) {
						if ( get_site_option('amu_addexistingaccess') == 'yes') {
							echo 'checked="checked"';
						}
					} else {
						echo 'checked="checked"';
					}
					echo ' value="yes" />';
					echo '</div>';
					
				echo '</div>';
				
				echo '<h4>'.__('Notifications').'</h4>';
				
				echo '<div class="genoptionwrap">';
					
					//superadmin get copies of added users
					echo '<div class="optionbox">';
					echo '	<label for="net_getcopies">'.__('Email addresses to receive copies of bulk registration details','amulang').': <em>('.__('no spaces, separated by commas','amulang').')</em></label>';
					echo '	<input name="net_getcopies" id="net_getcopies" type="text" ';
					if (get_site_option('amu_emailcopies')) {
							echo 'value="'.get_site_option('amu_emailcopies').'" />';
					} else {
						echo 'value="" />';
					}
					echo '</div>';
									
				echo '</div>';
					
				echo '<div class="buttonline">';
				echo '	<input type="submit" name="setnetworkopt" id="setnetworkopt" class="button-primary" value="'.__('Save Network Options','amulang').'" />';
				echo '	<input type="submit" name="resetnetworksettings" id="resetnetworksettings" class="button-primary" value="'.__('Reset Default Network Options','amulang').'" />';
				echo '</div>';
				
			echo '</form>';
			
			$infotype = 'networksettings';
			showPluginInfo($infotype);
			
			echo '</div>';
		echo '</div>';
}

/*
	* RESET NETWORK OPTIONS FUNCTION
	* Resets network options to default
*/

function amu_reset_network_options() {
	global $wpdb;
	update_site_option( 'amu_is_network', 'yes' );
	update_site_option( 'amu_subadminaccess', 'yes' );
	update_site_option( 'amu_addexistingaccess', 'yes' );
	update_site_option( 'amu_emailcopies', '' );
}

/*
	* SAVE NETWORK OPTIONS FUNCTION
	* Saves network options in site options
*/

function setNetworkOptions() {
	
	global $wpdb;
	
	if ( isset($_POST['net_allowadmins'] ) ) {
		$netAllowAdmins = 'yes';
	} else {
		$netAllowAdmins = 'no';
	}
	if ( isset($_POST['net_addexist'] ) ) {
		$netAllowAddEx = 'yes';
	} else {
		$netAllowAddEx = 'no';
	}
	$netgetcopies = trim($_POST['net_getcopies']);
	
	update_site_option( 'amu_subadminaccess', $netAllowAdmins );
	update_site_option( 'amu_addexistingaccess', $netAllowAddEx );
	update_site_option( 'amu_emailcopies', $netgetcopies );
}

/*
	* ADD EXISTING USERS TO SITE INTERFACE AND FUNCTION
	* interface to add users to site from network list
*/

function amu_addfromnet() {
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
	
	//test if disabled by superadmin
	if ( is_multisite() ) {
		if (get_site_option('amu_addexistingaccess')) {
			if (get_site_option('amu_addexistingaccess') == 'no') {
				if (!current_user_can('manage_network') )  {
					wp_die( __('Access to this function has been disabled by the Network Administrator.') );
				}
			}
		}
	}
	
	//when accessing amu, set options if they don't exist
	amu_set_defaultoptions();
	
	//begin wrap class
	echo '<div class="wrap">';
		echo '<div id="amu">';
		
			echo '<h2>'.__('Add Multiple Users - Add from Network','amulang').'</h2>';
		
			//if no post made, show interface and helpers
			if (empty($_POST) ) {
				
				$userListError = '';
				amuGetUserListHead($userListError);
				
				amuShowNetworkUsers();
				
				$infotype = 'addexistusers';
				showPluginInfo($infotype);
				
			//otherwise, run add existing function
			} else if ( isset($_POST['addexistingusers'] ) ) {
				
				amuAddNetworkUsers();
				
			//else throw error
			} else {
				echo '<p>'.__('Unknown request. Please select the Add from Network option to try again.','amulang').'<p>';
			}
			
		echo '</div>';
	echo '</div>';
	
}

function amuAddNetworkUsers() {
				
	global $wpdb, $blog_id;
	$mainsite = SITE_ID_CURRENT_SITE;
	//times it should loop based on highest user's id
	$existing_procs = intval($_POST['existprocs']);
	
	//set overall role value
	$allExistingToRole = $_POST['existingToRole'];
	
	echo '<h3>'.__('Results of your new user registrations','amulang').'</h3>';
	
	echo '<div class="stackwrap">';
		
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
%4$s','amulang' );
				wp_mail( $user_details->user_email, sprintf( __( '[%s] Joining confirmation' ), get_option( 'blogname' ) ),  sprintf($message, get_option('blogname'), site_url(), $thisAddRole, site_url("/newbloguser/$newuser_key/")));
				//notification line
				echo '<p>'.__('User','amulang').' '.$user_details->user_login.' '.__('has been sent a confirmation email.','amulang').'</p>';
				
			} else {
				
				$user_details = get_userdata($i);
				
				//if is main site just give them a role
				if($blog_id == $mainsite) {
					
					$wp_user_object = new WP_User($i);
					
					if($allExistingToRole == 'notset') {
						$wp_user_object->set_role($_POST['setrole_'.$i]);
					} else {
						$wp_user_object->set_role($_POST['existingToRole']);
					}
					
					unset($wp_user_object);
					
				//or if subsite, add them to blog
				} else {
					if($allExistingToRole == 'notset') {
						add_existing_user_to_blog( array( 'user_id' => $i, 'role' => $_POST['setrole_'.$i] ) );
					} else {
						add_existing_user_to_blog( array( 'user_id' => $i, 'role' => $_POST['existingToRole'] ) );
					}
					
				}
				
				//notification line
				echo '<p>'.__('User','amulang').' '.$user_details->user_login.' '.__('has been added to the site.','amulang').'</p>';
			}
		}
	}
	echo '</div>';
		
}

/*
	* SHOW NETWORK USERS
	* interface to select users to add to site
*/

function amuShowNetworkUsers() {
	
	echo '<form method="post" enctype="multipart/form-data" class="amuform">';
			
		//get this blogs id
		global $wpdb, $blog_id, $wp_roles;
		$mainsite = SITE_ID_CURRENT_SITE;
		$check_capabilities = 'wp_'.$blog_id.'_capabilities';		
		$lastuser = '';
		$usertotal = 0;
		
		echo '<h3>'.__('Add Network Users Options').'</h3>';
		
		//show users list
		$allusers = $wpdb->get_results( "SELECT ID, user_login, user_email FROM $wpdb->users ORDER BY ID ASC");
		if ($wpdb->num_rows == 0) {
			echo '<p>'.__('You have no available users to add.','amulang').'</p>';
		} else {
			//show multisite options wrapped in genoption
			echo '<div class="genoptionwrap">';
			
			if (get_option('amu_setallroles')) {
				$rolesel = get_option('amu_setallroles');
			} else {
				$rolesel = 'notset';
			}
			$roles = $wp_roles->get_names();
			//set all users to this role?
			echo '<div class="optionbox">';
			echo '	<label for="existingToRole">'.__('Ignore individual roles and set all selected users to this role','amulang').': </label>';
			echo '	<select name="existingToRole" id="existingToRole">';
			echo '		<option value="notset"'; if($rolesel=='notset'){echo ' selected="selected" ';} echo '>'.__('no, set individually...','amulang').'</option>';

				foreach($roles as $role) {
					$thisrole = $role;
					echo '<option value="'.strtolower($thisrole).'"'; if(strtolower($rolesel)==strtolower($thisrole)){echo ' selected="selected" ';} echo '>'.$thisrole.'</option>';
				}
			echo '	</select>';
			echo '</div>';
			
			//username strict validation option...
			echo '<div class="optionbox lastoption">';
			echo '	<label for="notifyExistingUser">'.__('Send each user a confirmation email?','amulang').' <span class="important">('.__('if selected, sends user standard WordPress confirmation email','amulang').')</span></label>';
			echo '	<input name="notifyExistingUser" id="notifyExistingUser" type="checkbox" value="sendnotification" />';
			echo '</div>';
			
			//end multisite options wrap
			echo '</div>';
			
			echo '	<h3><strong>'.__('Select network users to add to this site','amulang').':</strong></h3>';
			
			
			
			//start fieldset wrap
			echo '<div class="fieldsetwrap">';
			
			//show check all option
			echo '<div class="userline wrapwhite checkallex">';
				echo '<input name="checkallexisting" id="checkallexisting" type="checkbox" value="goforall" />';
				echo '<label for="checkallexisting">'.__('Select All','amulang').'</label>';
			echo '</div>';
							
			//show user rows
			foreach ( $allusers as $user ) {
				//if on main site
				if($blog_id == $mainsite) {
					if(!get_user_meta($user->ID, 'wp_capabilities')) {
						
						//start print
						if ($usertotal & 1) {
							echo '<div class="userline wrapwhite">';
						} else {
							echo '<div class="userline wrapgrey">';
						}
						
						echo '	<input name="adduser_'.$user->ID.'" id="adduser_'.$user->ID.'" class="userbox" type="checkbox" value="userchecked" />';
						echo '	<label for="adduser_'.$user->ID.'"><span class="eu_userid"><strong>'.__('User ID','amulang').':</strong> '.$user->ID.'</span><span class="eu_userlogin"><strong>'.__('User Login','amulang').':</strong> '.$user->user_login.'</span><span class="eu_useremail"><strong>'.__('User Email','amulang').':</strong> '.$user->user_email.'</span></label>';
						echo '	<select name="setrole_'.$user->ID.'" id="setrole_'.$user->ID.'">';
						foreach($roles as $role) {
							$thisrole = $role;
							echo '<option value="'.strtolower($thisrole).'">'.$thisrole.'</option>';
						}
						echo '	</select>';
						echo '</div>';
						$lastuser = $user->ID;
						$usertotal++;
					}
				} else {
					//if on subsite
					if(!get_user_meta($user->ID, $check_capabilities)) {
						
						//start print
						if ($usertotal & 1) {
							echo '<div class="userline wrapwhite">';
						} else {
							echo '<div class="userline wrapgrey">';
						}
						
						echo '	<input name="adduser_'.$user->ID.'" id="adduser_'.$user->ID.'" class="userbox" type="checkbox" value="userchecked" />';
						echo '	<label for="adduser_'.$user->ID.'"><span class="eu_userid"><strong>'.__('User ID','amulang').':</strong> '.$user->ID.'</span><span class="eu_userlogin"><strong>'.__('User Login','amulang').':</strong> '.$user->user_login.'</span><span class="eu_useremail"><strong>'.__('User Email','amulang').':</strong> '.$user->user_email.'</span></label>';
						echo '	<select name="setrole_'.$user->ID.'" id="setrole_'.$user->ID.'">';
						foreach($roles as $role) {
							$thisrole = $role;
							echo '<option value="'.strtolower($thisrole).'">'.$thisrole.'</option>';
						}
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
				echo '<p class="amu_error">'.__('All users on your Network are already assigned a role on this site.','amulang').'</p>';
				echo '</div>';
			} else {
				echo '<input type="hidden" readonly="readonly" name="existprocs" id="existprocs" value="'.$lastuser.'" />';
				echo '</div>';
				//show add button
				echo '<div class="buttonline">';
					echo '	<input type="submit" name="addexistingusers" class="button-primary" value="'.__('Add All Users','amulang').'" />';
				echo '</div>';
			}
		}
		
	echo '</form>';
			
}

/*
	* HELPER - ADDS HEADING INFORMATION AND ERROR IF EXISTS
	* interface to add users to site from network list
*/

function amuGetUserListHead($userListError) {
	
	echo '<div class="toolintro">';
		echo $userListError;
		echo '<p><strong>'.__('Select the users you wish to add to this site from your Network Users list and click the Add All Users button.','amulang').'</strong> <span class="important">'.__('Only users from your Network that are not already added to this site will appear in the list below.','amulang').'</span></p>';
	echo '</div>';
}
?>