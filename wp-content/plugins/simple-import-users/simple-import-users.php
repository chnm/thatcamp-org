<?php
/*
Plugin Name: Simple Import Users
Plugin URI: http://teleogistic.net/code/wordpress/simple-import-users
Description: Allows blog administrators to add multiple users to blogs at a time, using only a list of email addresses. When the specified email address matches an existing account, the account will be added as a user on the blog. Otherwise a new account is created and the new user added with the specified role. Based on DDImportUsers.
Version: 1.1
Requires at least: WordPress 3.0
Tested up to: WordPress 3.0.1
License: GPL v2
Author: Boone Gorges
Author URI: http://teleogistic.net
*/

/* Load the BP functions if BP is installed */
function ddiu_init_bp() {
	require( dirname( __FILE__ ) . '/siu-bp.php' );
}
add_action( 'bp_init', 'ddiu_init_bp' );

function ddiu2_add_management_pages() {
	if (function_exists('add_management_page')) {
		add_management_page('Import Users', 'Import Users', 8, __FILE__, 'ddiu2_management_page');
	}
}

#can specify how to parse submitted file by editing this function
function ddiu2_fileParseFunction($filename){
	return file($filename);
}

#modify this function to specify how to parse text in field
#could change format or add validation
function ddiu2_fieldParseFunction($text){
	return explode("\n", trim($text));
}

#specify format information to be displayed to the user
$formatinfo = '<p><strong>The data you enter MUST be in the following format:</strong><br />
			&nbsp;&nbsp;&nbsp;email<br />
			&nbsp;&nbsp;&nbsp;email<br />
			&nbsp;&nbsp;&nbsp;etc...<br />
		</p>';

function ddiu2_management_page() {

	global $wpdb, $wp_roles, $formatinfo, $the_role;

	$result = "";

	if (isset($_POST['info_update'])) {
		$new_defaults = array(
			'subject_new' => $_POST['email-subject-new'],
			'subject_existing' => $_POST['email-subject-existing'],
			'content_new' => $_POST['email-content-new'],
			'content_added' => $_POST['email-content-all']
		);
		
		update_option( 'ddui_email_defaults', $new_defaults );

		?><div id="message" class="updated fade"><p><strong><?php

		echo "Processing Complete - View Results Below";

	    ?></strong></p></div><?php


		//
		// START Processing
		//


		$the_role = (string)$_POST['ddui_role'];
		$delimiter = (string)$_POST['delimiter'];

		// get data from form and turn into array
		$u_temp = array();
		if(trim((string)$_POST["ddui_data"]) != ""){
			$u_temp = array_merge($u_temp, ddiu2_fieldParseFunction(((string) ($_POST["ddui_data"]))));
		}
		else{
			$result .= "<p>No names entered in field.</p>";
		}
		
		if ($_FILES['ddui_file']['error'] != UPLOAD_ERR_NO_FILE){#Earlier versions of PHP may use $HTTP_POST_FILES
			$file = $_FILES['ddui_file'];
			if($file['error']){
				$result .= '<h4 style="color: #FF0000;">Errors!</h4><p>';
				switch ($file['error']){
					case UPLOAD_ERR_INI_SIZE:
						$result .= "File of ".$file['size']."exceeds max size ".upload_max_filesize;
						break;
					case UPLOAD_ERR_FORM_SIZE:
						$result .= "File of ".$file['size']."exceeds max size ".upload_max_filesize;
						break;
					case UPLOAD_ERR_PARTIAL:
						$result .= "File not fully uploaded";
						break;
					default:
				}
				$result.='.</p>';
			}
			elseif(!is_uploaded_file($file['tmp_name'])){
				$result = "File ".$file['name']." was not uploaded via the form.";
			}
			else{ #should be ok to read the file now
				$u_temp = array_merge($u_temp, ddiu2_fileParseFunction($file['tmp_name']));
			}
		} else{
			$result .= "<p>No file submitted.</p>";
		}

		$u_data = array();
		$i = 0;

		foreach ($u_temp as $ut) {

			if (trim($ut) != '') {

				if (! (list($u_e )  = @split($delimiter, $ut, 6))){
					$result .= "<p>Regex ".$delimiter." not valid.</p>";
				}
				
				$u_e = trim($u_e);

				$u_data[$i]['email'] = $u_e;
				$i++;

			}

		}
		
		// process each user

		$errors = array();
		$complete = 0;
		$results = array();

		foreach ($u_data as $ud) {

			// check for errors
			$u_errors = 0;

			if (!is_email($ud['email'])) {
				$results[] = array( 'error' => 'Invalid email address: ' . $ud['email'], 'ud' => $ud );
				$u_errors++;
			} else {
				$results[] = ddiu2_process_user( $ud );
			}
		}

	} ?>

	<div class=wrap>

	<h2>Simple Import Users</h2>

	<?php
//		print_r($results);
		if ($results) {
			ddiu2_save_import( $results );
			echo '<div style="border: 1px solid #000000; padding: 10px;">';
			echo '<h4>Results</h4>';
			
			$has_errors = false;
			$has_new = false;
			$has_added = false;

			foreach ( $results as $r ) {
				if ( $r['error'] )
					$has_errors = true;
				if ( $r['create_success'] )
					$has_new = true;
				if ( $r['added_success'] )
					$has_added = true;
			}			
	
			
		?>
			<?php if ( $has_new ) : ?>
			<p><strong>The following new users have been created:</strong>
			<ol>
				<?php foreach( $results as $r ) : ?>
					<?php if ( $r['create_success'] ) : ?>
						<li><?php echo $r['create_success'] ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ol>
			<?php endif; ?>
		
			
			<?php if ( $has_added ) : ?>
			<p><strong>The following users were added to the blog:</strong>
			<ol>
				<?php foreach( $results as $r ) : ?>
					<?php if ( $r['added_success'] ) : ?>
						<li><?php echo $r['added_success'] ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ol>
			<?php endif; ?>
			
			<?php if ( $has_errors ) : ?>
			<div style="border: 2px solid #f00; padding: 5px;">
			<p><strong>These users could not be processed.</strong>
			<ol>
				<?php foreach( $results as $r ) : ?>
					<?php if ( $r['error'] ) : ?>
						<li><?php echo $r['error'] ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ol>
			<p><em>Data for these users has been placed back in the User Data box below. Please reconcile and errors and try submitting again.</em></p>
			</div>
			<?php endif; ?>
		
		<?php

			
			
			
			echo '</div>';
		}
	?>


	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>"  >
	<input type="hidden" name="info_update" id="info_update" value="true" />

	<?php
		$retry_text = '';
		if ( $results ) {
			foreach( $results as $result ) {
				if ( $result['error'] ) {
					//print_r( $result );
					$del = substr( $delimiter, 1, -1 );
					$retry_text .= implode( $del, $result['ud'] ) . "\n";
				}
			}
		}	
	
		$blog_name = get_bloginfo( 'name' );
		$blog_url = site_url();
		$admin_url = admin_url();
		if ( !$email_defaults = get_option( 'ddui_email_defaults' ) ) {
			$email_defaults = array(
				'subject_new' => sprintf( 'Your account has been created on %s', $blog_name ),
				'subject_existing' => sprintf( 'You have been added to %s', $blog_name ),
				'content_new' => apply_filters( 'ddiu_bp_filter_email_content', sprintf( 'Your %s account has been created. Here is your login info:

Username: [USERNAME]
Password: [PASSWORD]

', $blog_name ) ),
				'content_added' => sprintf( 'You have been added as a user on the blog %s at %s. 

Log into %s at %s', $blog_name, $blog_url, $blog_name, $admin_url )
			
			);		
		}
	
	?>
	
		


	<div style="padding: 0 0 15px 12px;">

		<input type="hidden" name="delimiter" value="[|]" />
		
		<?php $formatinfo = '<p><strong>The data you enter MUST be in the following format:</strong><br />
			&nbsp;&nbsp;&nbsp;email<br />
			&nbsp;&nbsp;&nbsp;email<br />
			&nbsp;&nbsp;&nbsp;etc...<br />
		</p>';
?>
		
		<h3>User Data</h3>
		<?php echo $formatinfo; ?>
		<br />
		<textarea name="ddui_data" cols="100" rows="12"><?php echo $retry_text ?></textarea>
		<br />
		
		<div style="margin: 6px 0 0 0;">
		<br /><b>Role for these users:</b>
		<select name="ddui_role">
		<?php
			if ( !isset($wp_roles) )
				$wp_roles = new WP_Roles();
			foreach ($wp_roles->get_names() as $role=>$roleName) {
				if ( $role == 'author' )
					echo '<option value="'.$role.'" selected="selected">'.$roleName.'</option>';
				else
					echo '<option value="'.$role.'">'.$roleName.'</option>';
			
			}
		?>
		</select>
		</div>
		
		<h3>Email content</h3>
		
		<p>You can edit the content of the email, but be sure to include the important bracketed information (like <strong>[USERNAME]</strong>), which will ensure that each member gets his or her personalized login information.</p>
		
		<label for="email-subject-new">Subject (sent to <strong>new</strong> accounts)</label><br />
		<input name="email-subject-new" type="text" size="100" value="<?php echo $email_defaults['subject_new'] ?>" /><br /><br />
		<label for="email-subject-existing">Subject (sent to <strong>existing</strong> accounts)</label><br />
		<input name="email-subject-existing" type="text" size="100" value="<?php echo $email_defaults['subject_existing'] ?>" /><br /><br />
		
		
		<label for="email-content-new">Content (sent to <strong>new</strong> accounts)</label><br />
		<textarea name="email-content-new" cols="75" rows="6"><?php echo $email_defaults['content_new'] ?></textarea><br /><br />
		<label for="email-content-all">Content (sent to <strong>all</strong> accounts)</label><br />
		<textarea name="email-content-all" cols="75" rows="6"><?php echo $email_defaults['content_added'] ?></textarea><br /><br />
	</div>


	<div class="submit">
		<input type="submit" name="info_update" value="<?php _e('Import Users'); ?> &raquo;" />
	</div>
	</form>
	</div><?php
}
add_action('admin_menu', 'ddiu2_add_management_pages');

function ddiu2_process_user( $ud ) {

	// Look up existing users by email and by name
	$user_by_email = get_user_by_email( $ud['email'] );
	
	if ( !$user_by_email->ID ) {
		// This is a new user and must be created
		$return = ddiu2_add_new_user( $ud );
		
	} else {
		// Add existing user
		$ud['username'] = $user_by_email->user_login;
		$return = ddiu2_add_existing_user( $user_by_email->ID, $ud, false );
	}
	
	$return['ud'] = $ud;

	return $return;
}

function ddiu2_add_new_user( $user ) {
	
	// generate passwords if none were provided in the import
	if ($user['password'] == '') {
		$user['password'] = substr(md5(uniqid(microtime())), 0, 7);
	} else {
		$password = $user['password'];
	}
	
	$uarray = split( '@', $user['email'] );
	$user['username'] = sanitize_user( $uarray[0] );
	
	$args = array(
		"user_login" => $user['username'],
		"user_pass" => $user['password'],
		"user_email" => $user['email']
	);

	// create user
	$user_id = wp_insert_user( $args );

	if (!$user_id) {
		$message = 'Could not create user <strong>' . $user['username'] . '</strong>';
		$return = array( 'error' => $message );
	} else {
		$message = 'Username <strong>' . $user['username'] . '</strong> was successfully created.';
		$return = array( 'create_success' => $message );
		
		$mailinfo = $args;
		
		$added = ddiu2_add_existing_user( $user_id, $user, $mailinfo );
		
		if ( isset( $added['added_success'] ) )
			$return['added_success'] = $added['added_success'];
		else
			$return['added_error'] = 'Could not be added';
	}
	
	return $return;
}


function ddiu2_add_existing_user( $user_id, $ud, $mailinfo = false ) {
	global $current_blog, $the_role;
	
	// set role
	if ( $ud['role'] )
		$role = $ud['role'];
	else
		$role = $the_role;
	
	add_user_to_blog( $current_blog->blog_id, $user_id, $role );

	// Send the welcome mail
	$blog_name = get_bloginfo( 'name' );
	$blog_url = get_bloginfo( 'url' );

	// Subjects
	// The subject of emails to newly created accounts
	$new_account_subject = $_POST['email-subject-new'];
	// The subject of emails to existing members who've been added to the blog
	$newly_added_subject = $_POST['email-subject-existing']; 
	
	$subject = ( $mailinfo ) ? $new_account_subject : $newly_added_subject;

	// The content of the mail
	
	$mail_message = '';
	
	// Newly created users get the following text at the top of their email
	if ( $mailinfo ) {
		$raw_mail_message = $_POST['email-content-new'];
		
		$search = array(
			'[USERNAME]',
			'[PASSWORD]'
		);
		
		$replace = array(
			$ud['username'],
			$ud['password'],
		);
		
		$mail_message .= str_replace( $search, $replace, $raw_mail_message );;

		$mail_message = apply_filters( 'ddiu_bp_filter', $mail_message, $user_id );
	}	
	
	// Both existing and newly created users get the following
	$newly_added_message = $_POST['email-content-all'];
	
	$mail_message .= $newly_added_message;

	$to = $ud['email'];
	
	wp_mail( $to, $subject, $mail_message );

	$message = 'Username <strong>' . $ud['username'] . '</strong> has been added successfully to the blog';
	return array( 'added_success' => $message );
}

function ddiu2_save_import( $results ) {
	if ( !$imports = get_option( 'ddiu2_imports' ) ) {
		add_option( 'ddiu2_imports', '', 'Previously imported users', 'no' );
		$imports = array();
	}
	
	$time = time();
	
	$imports[$time] = $results;
	
	update_option( 'ddiu2_imports', $imports );
}




?>
