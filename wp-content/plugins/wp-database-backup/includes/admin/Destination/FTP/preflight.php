<?php

function backupbreeze_preflight_problem($trouble) {
    error_log('<div class="error"><h3>Houston, we have a problem: </h3>' . $trouble . '<br /><br /></div>');

   // exit;
}

// now let's see if we can connect to the FTP repo
// set up variables
$host = get_option('backupbreeze_ftp_host');
$user = get_option('backupbreeze_ftp_user');
$pass = get_option('backupbreeze_ftp_pass');
$subdir = get_option('backupbreeze_ftp_subdir');
if ($subdir == '') {
    $subdir = '/';
}
@$remotefile = $subdir . '/' . $filename;

// @since 1.6.1
// only check FTP Connection if we have details
// otherwise skip this and do a local backup
//

if ($host) {
// connect to host
// extra security
// @since 2.1
// If in WP Dashboard or Admin Panels
    if (is_admin()) {
// If user has WP manage options permissions
        if (current_user_can('manage_options')) {
// connect to host ONLY if the 2 security conditions are valid / met
            $conn = ftp_connect($host);
            if (!$conn) {
                $trouble = 'I could not connect to your FTP server.<br />Please check your FTP Host settings and try again (leave FTP Host BLANK for local backups).';
                backupbreeze_preflight_problem($trouble);
            }
// can we log in?
            $result = ftp_login($conn, $user, $pass);
            if (!$result) {
                $trouble = 'I could not log in to your FTP server.<br />Please check your FTP Username and Password, then try again.<br />For local backups, please leave the FTP Host option BLANK.';
                backupbreeze_preflight_problem($trouble);
            }
// and does the remote directory exist?
            $success = ftp_chdir($conn, $subdir);
            if (!$success) {
                $trouble = 'I cannot change into the FTP subdirectory you specified. Does it exist?<br />You must create it first using an FTP client like FileZilla.<br />Please check and try again.';
                backupbreeze_preflight_problem($trouble);
            }
// and is it writeable?
// ah... I don't know how to test that :-(
// end if
        }
    }
} else {
   // error_log ("The FTP Details are missing or not complete. This will be a local backup only.<br />");
}

//error_log("All good - let's Backup!<br />");
?>
