<?php
/*
 Plugin Name: Network Mass Email
 Plugin URI: http://wordpress.org/extend/plugins/network-mass-email/
 Description: Allows network admins to send a manually created notification email to all registered users based on user role.
 Version: 1.5
 Author: Kenny Zaron
 Author URI: http://www.kennyzaron.com/
 License: GPL2
*/

/*  Copyright 2012  Kenny Zaron (email: kzaron@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Mail Icon(s) courtesy of: http://www.iconhot.com/icon/android-style-icons-r1/mail-64.html

//error variables
$nmewassuberror = false;
$nmewasmsgerror = false;
$nmewasfromerror = false;
$nmenoemailserror = false;
$nmenoroleserror = false;
$nmeloadtemplateserr = false;
$message = "";
$subject = "";

// Determines on pageload whether to load the compose email function or the send email function
function nme_decider() {
	if (isset($_POST['listbuttonsubmit'])) { nme_error_checker(); return; }
	// Run some checks on the submitted form; if everything is OK, send our message
	elseif (isset($_POST['submitsendemail'])) { nme_error_checker(); }
	elseif (isset($_POST['loadtemplate'])) {		
		global $wpdb;
		$table_name = $wpdb->prefix . "networkmassemail";	
		//see if template name exists in wpdb	
		if ( $_POST['selecttemplate'] == "" ) {
			// it was blank, just load the page
			nme_menu_page($nmewassuberror, $nmewasmsgerror, $nmewasfromerror, $nmenoemailserror, $nmenoroleserror, $nmeloadtemplateserr, $subject, $message);
		}
		else if ($wpdb->get_var("SELECT name FROM {$table_name} WHERE name='{$_POST['selecttemplate']}'") == $_POST['selecttemplate']) {		
			//select template row from db			
			$templateresult = $wpdb->get_row("SELECT * FROM $table_name WHERE name='" . $_POST['selecttemplate'] . "'");			
			$subject = $templateresult->subject;
			$message = $templateresult->message;							
		}
		else {
			$nmeloadtemplateserr = true;			
		}
		// check if the list was already loaded
		
		
		nme_menu_page($nmewassuberror, $nmewasmsgerror, $nmewasfromerror, $nmenoemailserror, $nmenoroleserror, $nmeloadtemplateserr, $subject, $message);
	}
	else { nme_menu_page($nmewassuberror, $nmewasmsgerror, $nmewasfromerror, $nmenoemailserror, $nmenoroleserror, $nmeloadtemplateserr, $subject, $message); }
}

//checks for procedural errors in user's created messages
function nme_error_checker() {
	if(isset( $_POST['emailmessage']) && isset( $_POST['emailsubject'])) {
	if( $_POST['emailsubject'] == "" ) { $nmewassuberror = true; }
	if( $_POST['emailmessage'] == "" ) { $nmewasmsgerror = true; }
	if( $_POST['emailfrom'] == "" ) { $nmewasfromerror = true; }
	//error check when loading list of users
	if( (isset($_POST['admins']) == false) && (isset($_POST['authors']) == false) && (isset($_POST['editors']) == false) && (isset($_POST['contributors']) == false) && (isset($_POST['subscribers']) == false)) {
	$nmenoroleserror = true;
	}
	//error check if emails list exists on form submit
	if( ($_POST['emaillist'] == "") && (isset($_POST['submitsendemail'])) ) { $nmenoemailserror = true; }
	if (isset($_POST['submitsendemail']) && $nmewassuberror == false && $nmewasmsgerror == false && $nmewasfromerror == false && $nmenoemailserror == false) { nme_sendmail(); }
	else { nme_menu_page($nmewassuberror, $nmewasmsgerror, $nmewasfromerror, $nmenoemailserror, $nmenoroleserror, $nmeloadtemplateserr, $subject, $message); }
	}
}

//page header function
function nme_pageheader() {
		screen_icon('users');
		?><h2>Network Mass Email</h2><?php
}

//sends the messages to the users selected after form is submitted
function nme_sendmail() {
		$subject = stripslashes(html_entity_decode($_POST['emailsubject'], ENT_NOQUOTES, 'UTF-8'));
		$body = stripslashes(html_entity_decode($_POST['emailmessage'], ENT_NOQUOTES, 'UTF-8'));
		$from = $_POST['emailfrom']; 
		$headers = "From:" . $from;
		$emails = $_POST['emaillist'];
		nme_pageheader();?>
		<p>
		</p>
		<p><h3>Email Sent Successfully!</h3></p>
		<p>
		Your Email:<p>
		<b>Subject:</b> <?php echo "$subject"; ?>
		<br/><b>Message:</b> <?php echo "$body"; ?></p>
		<p>Was successfully sent to the following recipients:</p>
		<p>
		<ol name="emailssent" id="emailssent">
		<?php 
		$counter = 0;
		foreach($emails as $num => $emailaddress) {
			if (mail($emailaddress, $subject, $body, $headers)) {
			echo "<li>"; echo "$emailaddress"; echo "</li>";
			$counter = $counter + 1;
			}			
		}
		
		//send a CC to the sender 
		$subject2 = "Copy of Your Email: $subject";
		mail($from, $subject2, $body, $headers)
		?></ol>
		Total Number of messages sent:&nbsp;<?php echo $counter; 
		return;
}

// Hooks the function which adds a page under network users
add_action('network_admin_menu', 'nme_add_menu');

// Adds the submenu page under users in network admin
function nme_add_menu() {
    // reference: add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position ); http://codex.wordpress.org/Function_Reference/add_menu_page
	add_menu_page( 'Network Mass Email', 'Mass Email', 'manage_network', 'nme_menupage', 'nme_decider', plugins_url('network-mass-email/icon.png'), 99 );
	add_submenu_page( 'nme_menupage', 'Network Mass Email', 'Send Email', 'manage_network', 'nme_menupage', 'nme_decider' );
}

define( 'NETWORKMASSEMAIL_PATH', plugin_dir_path(__FILE__) );
require NETWORKMASSEMAIL_PATH .'/templates.php';

// Displays error messages
function nme_error_msg($nmeerrmessage) {
	echo '<div id="nmeerror" class="error" align="left" style="margin-left:0px"><p>' . $nmeerrmessage . '</p></div>';
}

// Creates the part of the page where users compose their email
function nme_menu_page($nmewassuberror, $nmewasmsgerror, $nmewasfromerror, $nmenoemailserror, $nmenoroleserror, $nmeloadtemplateserr, $subject, $message) {
	nme_pageheader(); 
	nme_install();
	if($nmewasfromerror == true) { $nmeerrmessage = "ERROR: Your from email address cannot be blank."; nme_error_msg($nmeerrmessage); }
	if($nmewassuberror == true) { $nmeerrmessage = "ERROR: Your subject cannot be blank."; nme_error_msg($nmeerrmessage); }
	if($nmewasmsgerror == true) { $nmeerrmessage = "ERROR: Your message body cannot be blank."; nme_error_msg($nmeerrmessage); }
	if($nmeloadtemplateserr == true) { $nmeerrmessage = "ERROR: Could not load template."; nme_error_msg($nmeerrmessage); }
	if($nmenoroleserror == true && isset($_POST['listbuttonsubmit'])) { $nmeerrmessage = "ERROR: You need to select at least one group of users in order to load the list."; nme_error_msg($nmeerrmessage); }
	if($nmenoemailserror == true && isset($_POST['submitsendemail'])) { $nmeerrmessage = "ERROR: You need to load users before sending an email."; nme_error_msg($nmeerrmessage); }
	if ($nmewassuberror == false && $nmewasmsgerror == false && $nmewasfromerror == false && $nmenoemailserror == false && $nmenoroleserror == false) { echo "<br />"; }	?>
	<h2>Audience</h2>
	<p>Select the user levels you wish to send this message to:</p>
	<form name="massemailform" action="" method="post">
	<input type="checkbox" name="allincsubs" id="allincsubs" <?php if($_POST['allincsubs']) echo 'checked="checked"'; else echo ""; ?> onclick="selectAllIncSubs(this);"/> <label for="allincsubs">All Users</label><br />
    <input type="checkbox" name="allbutsubs" id="allbutsubs" <?php if($_POST['allbutsubs']) echo 'checked="checked"'; else echo ""; ?> onclick="selectAllButSubs(this);"/> <label for="allbutsubs">All Users EXCEPT Subscribers</label><br />
    <input type="checkbox" name="admins" id="admins" <?php if($_POST['admins']) echo 'checked="checked"'; else echo ""; ?> /> <label for="admins">Administrators</label>  &nbsp;
    <input type="checkbox" name="editors" id="editors" <?php if($_POST['editors']) echo 'checked="checked"'; else echo ""; ?> /> <label for="editors">Editors</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="checkbox" name="authors" id="authors" <?php if($_POST['authors']) echo 'checked="checked"'; else echo ""; ?> /> <label for="authors">Authors</label><br />
    <input type="checkbox" name="contributors" id="contributors" <?php if($_POST['contributors']) echo 'checked="checked"'; else echo ""; ?> /> <label for="contributors">Contributors</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="checkbox" name="subscribers" id="subscribers" <?php if($_POST['subscribers']) echo 'checked="checked"'; else echo ""; ?> /> <label for="subscribers">Subscribers</label><br /><br />
    <input type="submit" name="listbuttonsubmit" style="width: 200px, height: 30px" value="&nbsp;&nbsp;&nbsp;Load the List&nbsp;&nbsp;&nbsp;" />	
	<script type="text/javascript" charset="utf-8">
		function selectAllButSubs(chkObj){			
			if(chkObj.checked) {
				document.getElementById('admins').checked=true;
				document.getElementById('editors').checked=true;
				document.getElementById('authors').checked=true;
				document.getElementById('contributors').checked=true;
				document.getElementById('allincsubs').checked=false;
				document.getElementById('subscribers').checked=false;
				}
			else {
				document.getElementById('admins').checked=false;
				document.getElementById('editors').checked=false;
				document.getElementById('authors').checked=false;
				document.getElementById('contributors').checked=false;	
				document.getElementById('allincsubs').checked=false;
				document.getElementById('subscribers').checked=false;				
				}
		}
	</script>
	<script type="text/javascript" charset="utf-8">
		function selectAllIncSubs(chkObj){			
			if(chkObj.checked) {
				document.getElementById('admins').checked=true;
				document.getElementById('editors').checked=true;
				document.getElementById('authors').checked=true;
				document.getElementById('contributors').checked=true;
				document.getElementById('subscribers').checked=true;
				document.getElementById('allbutsubs').checked=false;
				}
			else {
				document.getElementById('admins').checked=false;
				document.getElementById('editors').checked=false;
				document.getElementById('authors').checked=false;
				document.getElementById('contributors').checked=false;				
				document.getElementById('subscribers').checked=false;				
				document.getElementById('allbutsubs').checked=false;
				}
		}
	</script>
	<h2>Message</h2>
	<?php $current_user = wp_get_current_user(); ?>
	<p><b>From:</b><br /><input type="text" id="emailfrom" name="emailfrom" size="128" value="<?php if(isset($_POST['emailfrom'])) { echo $_POST['emailfrom']; } else { echo $current_user->user_email; } ?>"/></p>
	<p><b>Load a Template:&nbsp;&nbsp;</b>
	<select name="selecttemplate">
	<?php nmef_loadalltemplates() ?>
	<?php //read from wordpress table to get templates stored in database -- foreach( ?>
	</select>&nbsp;&nbsp;<input type="submit" value="Load Template" name="loadtemplate" id="loadtemplate"> &nbsp;&nbsp;<INPUT Type="BUTTON" VALUE="Add/Edit Templates" ONCLICK="window.location.href='<?php echo network_admin_url( 'admin.php?page=nme_templatespage' ); ?>'">
	</p>
	<?php if (isset($_POST['loadtemplate'])) { ?>
	<p><b>Subject:</b><br /><input type="text" id="emailsubject" name="emailsubject" size="128" value="<?php echo $subject; ?>"/></p>
	<p><b>Message:</b><br /><textarea cols="125" rows="15" id="emailmessage" name="emailmessage"><?php echo $message; ?></textarea></p>	
	<?php }
	else { ?>
	<p><b>Subject:</b><br /><input type="text" id="emailsubject" name="emailsubject" size="128" value="<?php if(isset($_POST['emailsubject'])) { echo stripslashes(html_entity_decode($_POST['emailsubject'], ENT_NOQUOTES, 'UTF-8')); } else { echo "WordPress Network Email"; } ?>"/></p>
	<p><b>Message:</b><br /><textarea cols="125" rows="15" id="emailmessage" name="emailmessage"><?php if(isset($_POST['emailmessage'])) { echo stripslashes(html_entity_decode($_POST['emailmessage'], ENT_NOQUOTES, 'UTF-8')); } else { echo "Enter message text"; } ?></textarea></p>	
	<?php
	} ?>
	<p><input type="submit" name="submitsendemail" style="width: 199px, height: 31px" text="Send Email" value="Send This Email" onClick="return selectAllEmails();" /> (This may take some time, please do not click twice!)</p>
	<?php
	if( isset($_POST['listbuttonsubmit']) || isset($_POST['submitsendemail']) || isset($_POST['listsubmitted']) ) {
		?> <input type="hidden" id="listsubmitted" name="listsubmitted" value="listsubmitted"><?php
		if($nmenoroleserror == false) {
			global $wpdb;
			$nmeblogids = $wpdb->get_results("
				SELECT blog_id
				FROM {$wpdb->blogs}
				WHERE site_id = '{$wpdb->siteid}'
				AND spam = '0'
				AND deleted = '0'
				AND archived = '0'
				AND blog_id != 0
				AND blog_id != 1
			");
			$blogusers;
			$isbuempty = true;
			foreach ($nmeblogids as $nmeblog) {
				switch_to_blog( $nmeblog->blog_id );				
				if(isset($_POST['admins'])) { 
					$adminusers = get_users('role=administrator');
					if ($isbuempty == true) { $blogusers = $adminusers; $isbuempty = false; }
					else { foreach( $adminusers as $user ) { array_push($blogusers, $user); } }
					}
				if(isset($_POST['editors'])) { 
					$editorusers = get_users('role=editor'); 
					if ($isbuempty == true) { $blogusers = $editorusers; $isbuempty = false; }					
					else { foreach( $editorusers as $user ) { array_push($blogusers, $user); } }
					}
				if(isset($_POST['authors'])) { 
					$authorusers = get_users('role=author');
					if ($isbuempty == true) { $blogusers = $authorusers; $isbuempty = false; }						
					else { foreach( $authorusers as $user ) { array_push($blogusers, $user); } }
					}
				if(isset($_POST['contributors'])) { 
					$contribusers = get_users('role=contributor');
					if ($isbuempty == true) { $blogusers = $contribusers; $isbuempty = false; }					
					else { foreach( $contribusers as $user ) { array_push($blogusers, $user); } }
					}
				if(isset($_POST['subscribers'])) { 
					$subscribeusers = get_users('role=subscriber'); 
					if ($isbuempty == true) { $blogusers = $subscribeusers; $isbuempty = false; }						
					else { foreach( $subscribeusers as $user ) { array_push($blogusers, $user); } }
					}
			}
			//remove dupes			
			foreach($blogusers as $user => $value) {
				foreach($blogusers as $u => $v) {
					if($u != $user && $v->user_email == $value->user_email) {
						unset($blogusers[$user]);
					}
				}
			}
			//sort the users by email address
			usort($blogusers, "nme_sort");
			?><h2>Recipients</h2>
			Total Number of Recipients:<?php echo count($blogusers, COUNT_RECURSIVE);
			?>
			<ol class="emaillistclass">
			<?php
			//add users to the recipients list
			nme_addtolistbox($blogusers);
		?>
		</ol>	
		<?php 
		}
	}
	if ( ($nmenoroleserror == true) || ((isset($_POST['listbuttonsubmit']) == false) && ( isset($_POST['submitsendemail']) ==false)) ) { 
		if (!isset($_POST['listsubmitted'])) {
			echo "<h2>Recipients</h2>"; 
			echo "Select your user role types from Audience above to build a recipient list."; 
		}
	}
	?>
	</form>
	<?php
}
//sorts the user list alphabetically by email address
function nme_sort($a, $b){  
	return strcmp($a->user_email, $b->user_email);
}  
//adds users to the recipients list
function nme_addtolistbox($blogusers) {
	foreach ($blogusers as $user) {
		if($user->user_email != "") {
			?><li><input type="checkbox" name="emaillist[]" id="<?php echo $user->user_email; ?>" value="<?php echo $user->user_email ?>" <?php if( isset($_POST['emaillist']) ) { if(in_array($user->user_email, $_POST['emaillist'])) { echo 'checked'; } } else { echo 'checked'; }?>>&nbsp;&nbsp;<label for="<?php echo $user->user_email; ?>"><?php echo $user->user_email; ?></li><?php		
		}
	}
	return;
}

// stuff for message templates in wpdb

// create the table of stored message templates for the nme plugin for the first time
function nme_install() {
	global $wpdb;	
	$table_name = $wpdb->prefix . "networkmassemail";	
	if ($wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'") != $table_name) {		
		$sql = "CREATE TABLE " . $table_name . " ( id mediumint(9) NOT NULL AUTO_INCREMENT, name text NOT NULL, subject text NOT NULL, message text NOT NULL, UNIQUE KEY id (id) );";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		add_option("nme_db_version", "1.5");
		
		$firsttemplatename = "Example";
		$firsttemplatesubject = "Example Subject";
		$firsttemplatemessage = "This is an example message.\nYou can store message templates that you would like quick access to by typing up a message and clicking \'Save Template\'.";
		$rows_affected = $wpdb->insert( $table_name, array( 'name' => $firsttemplatename, 'subject' => $firsttemplatesubject, 'message' => $firsttemplatemessage ) );	
	}
	if ($wpdb->get_var( "SELECT name FROM " . $table_name . " WHERE name='Example'") != "Example" ) {		
		$firsttemplatename = "Example";
		$firsttemplatesubject = "Example Subject";
		$firsttemplatemessage = "This is an example message.
		
		You can store message templates that you would like quick access to by typing up a message and clicking 'Save Template'.";
		$rows_affected = $wpdb->insert( $table_name, array( 'name' => $firsttemplatename, 'subject' => $firsttemplatesubject, 'message' => $firsttemplatemessage ) );	
	}
}

?>