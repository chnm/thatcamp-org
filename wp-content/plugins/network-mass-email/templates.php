<?php
// Template Editing Network Admin Page for Network Mass Email Plugin


function nmef_add_menu() {
	// reference: add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function ); http://codex.wordpress.org/Function_Reference/add_submenu_page
	add_submenu_page( 'nme_menupage', 'Network Mass Email Templates', 'Add/Edit Templates', 'manage_network', 'nme_templatespage', 'nmef_loader' );
}

function nmef_loader() {
	// check if page was submitted with load template
	if( isset($_POST['loadtemplate']) ) { 
		nmef_loadonetemplate();
	}
	// check if page was submitted with save template
	if ( isset( $_POST['savetemplate'])) { 
		nmef_savetemplate();
	}
	if ( isset( $_POST['deletetemplate'])) {
		nmef_deletetemplate();
	}
	//load page normally if neither of those are the case
	else if (!isset($_POST['loadtemplate']) && !isset( $_POST['savetemplate'])) { 
		$nmef_errmsg = ""; $nmef_update = ""; $subject = ""; $templatename = ""; $message = ""; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message); 
	}
}

function nmef_loadonetemplate() {
	global $wpdb;
	$table_name = $wpdb->prefix . "networkmassemail";	
	//see if template name exists in wpdb	
	if ( $_POST['selecttemplate'] == "" ) {
		$nmef_errmsg = ""; $nmef_update = ""; $subject = ""; $templatename = ""; $message = ""; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message); 
	}
	else if ($wpdb->get_var("SELECT name FROM {$table_name} WHERE name='{$_POST['selecttemplate']}'") == $_POST['selecttemplate']) {		
		//select template row from db			
		$templateresult = $wpdb->get_row("SELECT * FROM $table_name WHERE name='" . $_POST['selecttemplate'] . "'");
		$templatename = $templateresult->name;
		$subject = $templateresult->subject;
		$message = $templateresult->message;		
		//load nmef_pagecontent
		$nmef_errmsg = "";
		$nmef_update = "Template " . $_POST['selecttemplate'] . " loaded!";		
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message);		
	}
	else { 
		$nmef_errmsg = "The template name didn't exist in the database! Uh oh!";
		$nmef_update = "";
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message);
	}
}

function nmef_savetemplate() {
	global $wpdb;
	$table_name = $wpdb->prefix . "networkmassemail";	
	$templatename = stripslashes(html_entity_decode($_POST['templatename'], ENT_NOQUOTES, 'UTF-8'));
	$subject = stripslashes(html_entity_decode($_POST['subject'], ENT_NOQUOTES, 'UTF-8'));
	$message = stripslashes(html_entity_decode($_POST['message'], ENT_NOQUOTES, 'UTF-8'));	
	if ($templatename == "") { 
		$nmef_errmsg = "Your template name cannot be blank! Template NOT saved."; 
		$nmef_update = ""; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message); 
	}
	else if ($subject == "") { 
		$nmef_errmsg = "Your subject cannot be blank! Template NOT saved."; 
		$nmef_update = ""; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message); 
	}
	else if ($message == "") { 
		$nmef_errmsg = "Your message body cannot be blank! Template NOT saved."; 
		$nmef_update = ""; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message); 
	}
	if ( ($subject != "") && ($templatename != "") && ($message != "") ) {
		//save template to DB
		if ( $wpdb->get_var("SELECT name FROM {$table_name} WHERE name='{$_POST['templatename']}'") == $templatename) {
			// update the entry using $wpdb->update( $table, $data, $where, $format = null, $where_format = null )
			$wpdb->update( 
				$table_name,
				array( 
					'name' => $templatename, 
					'subject' => $subject, 
					'message' => $message
				),
				array( 'name' => $templatename )
			);				
		}
		else {
			// add the entry
			$wpdb->insert( $table_name, array( 'name' => $templatename, 'subject' => $subject, 'message' => $message) );
		}
		// load page with update message
		$nmef_update = "Template name: " . $templatename . " successfully updated!"; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message);		
	}	
}

function nmef_deletetemplate() {
	global $wpdb;
	$table_name = $wpdb->prefix . "networkmassemail";	
	$templatename = $_POST['templatename'];
	if ( $wpdb->get_var("SELECT name FROM {$table_name} WHERE name='{$_POST['templatename']}'") == $templatename) {
		//its a match, delete it.
		$wpdb->query( 
			$wpdb->prepare(
			"
			DELETE FROM $table_name
			WHERE name = %s
			",
			$templatename 
			)
		);
		// load page with update message
		$nmef_errmsg = ""; $subject = ""; $templatename = ""; $message = ""; 
		$nmef_update = "Template name: " . $templatename . " successfully deleted. The template is filled in for you below in case you didn't mean to do that..."; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message);
	}
	else {
		$nmef_update = ""; $subject = ""; $templatename = ""; $message = ""; 
		$nmef_errmsg = "Template name: " . $templatename . " not found. Template was not deleted."; 
		nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message);
	}
}

add_action('network_admin_menu', 'nmef_add_menu');

//page header function
function nmef_pageheader() {
		screen_icon('options-general');
		?><h2>Network Mass Email - Add/Edit Templates</h2><?php
}

// Displays error messages
function nmef_error_msg($nmef_errmsg) {
	echo '<div id="nmeerror" class="error" align="left" style="margin-left:0px"><p>' . $nmef_errmsg. '</p></div>';
}

//update message
function nmef_update_msg($nmef_update) {
	echo '<div id="nmeupdate" class="updated" align="left" style="margin-left:0px"><p>' . $nmef_update. '</p></div>';
}

function nmef_pagecontent($nmef_errmsg, $nmef_update, $templatename, $subject, $message) {
	nmef_pageheader();
	if($nmef_errmsg != "") { nmef_error_msg($nmef_errmsg); }
	if($nmef_update != "") { nmef_update_msg($nmef_update); }
	nmef_install();
	?><p><form name="nme_templateform" action="" method="post"><p>Select Template: <select name="selecttemplate">	
	<?php nmef_loadalltemplates(); ?>
	</select>&nbsp;<input type="submit" value="Load Template" id="loadtemplate" name="loadtemplate"></p>
	<?php if(isset($_POST['loadtemplate']) && $nmef_errmsg == "" && $templatename != "") { ?>
	<p><b>Template Name:</b><br /><input type="text" id="templatename" name="templatename" size="40" value="<?php echo $templatename; ?>"/><input type="submit" value="Delete Template" id="deletetemplate" name="deletetemplate"></p>
	<p><b>Subject:</b><br /><input type="text" id="subject" name="subject" size="128" value="<?php echo $subject; ?>"/></p>
	<p><b>Message:</b><br /><textarea cols="125" rows="15" id="message" name="message"><?php echo $message; ?></textarea></p>
	<?php } 
	else { ?>
	<p><b>Template Name:</b><br /><input type="text" id="templatename" name="templatename" size="40" value="<?php if(isset($_POST['templatename'])) { echo stripslashes(html_entity_decode($_POST['templatename'], ENT_NOQUOTES, 'UTF-8')); } else { echo ""; } ?>"/>  <input type="submit" value="Delete Template" id="deletetemplate" name="deletetemplate"></p>
	<p><b>Subject:</b><br /><input type="text" id="subject" name="subject" size="128" value="<?php if(isset($_POST['subject'])) { echo stripslashes(html_entity_decode($_POST['subject'], ENT_NOQUOTES, 'UTF-8')); } else { echo "WordPress Network Email"; } ?>"/></p>
	<p><b>Message:</b><br /><textarea cols="125" rows="15" id="message" name="message"><?php if(isset($_POST['message'])) { echo stripslashes(html_entity_decode($_POST['message'], ENT_NOQUOTES, 'UTF-8')); } else { echo "Enter message text"; } ?></textarea></p>
	<?php } ?>
	<p><input type="submit" value="Save Template" name="savetemplate"></p>
	</form></p><?php
}

// loads the templates from wordpress db and adds them to a select box
function nmef_loadalltemplates() {
	global $wpdb;	
	?><option value="">Select a template...</option><?php
	//load the templates from wordpress db with a select query
	$table_name = $wpdb->prefix . "networkmassemail";
	$wp_query->request =
			"
			SELECT name, subject, message 
			FROM " . $table_name . "
			";
	$nmef_results = $wpdb->get_results($wp_query->request, OBJECT);
	// output them to a select box
	foreach ( $nmef_results as $templateresult )	{
		$tempresultstrip = stripslashes(html_entity_decode($templateresult->name, ENT_NOQUOTES, 'UTF-8'));
		echo "<option value=\"" . $tempresultstrip . "\">" . $tempresultstrip . "</option>";
	}	
}

// adds a template the user wants to save to the wpdb
function nmef_addupdate_template($name, $subject, $message) {
	$table_name = $wpdb->prefix . "networkmassemail";
	//check and see if the template exists
	$checker = $wpdb->get_var( "SELECT name FROM $wpdb->$table_name WHERE name=\'$name\'" );
	if ($checker == NULL) { 
		// this is a new template, save it for the first time
		$wpdb->query( $wpdb->prepare( 
			"
				INSERT INTO $wpdb->$table_name
				( name, subject, message )
				VALUES ( %s, %s, %s _
			",
			$name,
			$subject,
			$message
		) );			
	}
	else { 
		// there already exists a template with this name, update it.
		$nme_data = array( 'subject' => $subject, 'message' => $message );
		$nme_where = array( 'name' => $name );
		$wpdb->update( $table_name, $nme_data, $nme_where );		
	}
}

function nmef_install() {
	global $wpdb;	
	$table_name = $wpdb->prefix . "networkmassemail";	
	if ($wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'") != $table_name) {
		echo '<p>Second part called</p>';
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