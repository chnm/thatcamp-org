 <?php
 
// Direct calls to this file are Forbidden when core files are not present
// Thanks to Ed from ait-pro.com for this  code 
// @since 2.1

if ( !function_exists('add_action') ){
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}

if ( !current_user_can('manage_options') ){
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}

// 
//

function backupbreeze_test_ftp() {

// now let's see if we can connect to the FTP repo
// set up variables
$host = get_option('backupbreeze_ftp_host');
$user = get_option('backupbreeze_ftp_user');
$pass = get_option('backupbreeze_ftp_pass');
$subdir = get_option('backupbreeze_ftp_subdir');
if ($subdir =='') {
	$subdir = '/';
}
@$remotefile = $subdir . '/' . $filename;

// @since 2.0
// checking FTP Details
// extra security @since 2.1
// If in WP Dashboard or Admin Panels
if ( is_admin() ) {
// If user has WP manage options permissions
if ( current_user_can('manage_options')) {
// connect to host ONLY if the 2 security conditions are valid / met
@$conn = ftp_connect($host);
}
}

if (!$conn)
{
  $trouble = "I could not connect to your FTP server.<br />Please check your FTP Host and try again.";
  return $trouble;
}
// can we log in?
$result = ftp_login($conn, $user, $pass);
if (!$result) {
$trouble = "I could connect to the FTP server but I could not log in.<br />Please check your credentials and try again.";
  return $trouble;
}
// and does the remote directory exist?
$success = ftp_chdir($conn, $subdir);
if (!$success) {
$trouble = "I can connect to the FTP server, but I cannot change into the FTP subdirectory you specified. <br />Is the path correct? Does the directory exist? Is it wrritable?<br />Please check using an FTP client like FileZilla.";
  return $trouble;
}

// and is it writeable?

// got til here? Wow - everything must be fine then
$trouble = 'OK';

// lose this connection
ftp_close($conn);
return $trouble;

} // end of function


?>