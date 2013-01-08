<?php
/**
 * @package Add_Multiple_Users
 * @version 2.0.0
 * AJAX Functions
 */

//protect from direct call
if ( !function_exists( 'add_action' ) ) {
	echo "Access denied!";
	exit;
}

// <=========== AJAX FUNCTIONS ===============================================================>

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