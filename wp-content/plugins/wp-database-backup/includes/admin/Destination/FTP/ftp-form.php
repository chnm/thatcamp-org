<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
/*
 * @since 1.0
 * FTP FORM SETTINGS
 */

// Direct calls to this file are Forbidden when core files are not present
// Thanks to Ed from ait-pro.com for this  code 
// @since 2.1

if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!current_user_can('manage_options')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// 
//
// variables for the field and option names 
$opt_name = 'backupbreeze_ftp_host';
$opt_name2 = 'backupbreeze_ftp_user';
$opt_name3 = 'backupbreeze_ftp_pass';
$opt_name4 = 'backupbreeze_ftp_subdir';
$opt_name5 = 'backupbreeze_ftp_prefix';
$opt_name6 = 'backupbreeze_add_dir1';
$opt_name7 = 'backupbreeze_auto_interval';
$opt_name8 = 'backupbreeze_auto_email';
$opt_name9 = 'backupbreeze_ftp_port';

$hidden_field_name = 'backupbreeze_ftp_hidden';
$hidden_field_name2 = 'backupbreeze_backup_hidden';
$hidden_field_name3 = 'backupbreeze_check_repo';
$data_field_name = 'backupbreeze_ftp_host';
$data_field_name2 = 'backupbreeze_ftp_user';
$data_field_name3 = 'backupbreeze_ftp_pass';
$data_field_name4 = 'backupbreeze_ftp_subdir';
$data_field_name5 = 'backupbreeze_ftp_prefix';
$data_field_name6 = 'backupbreeze_add_dir1';
$data_field_name7 = 'backupbreeze_auto_interval';
$data_field_name8 = 'backupbreeze_auto_email';
$data_field_name9 = 'backupbreeze_ftp_port';

// Read in existing option value from database
$opt_val = get_option($opt_name);
$opt_val2 = get_option($opt_name2);
$opt_val3 = get_option($opt_name3);
$opt_val4 = get_option($opt_name4);
$opt_val5 = get_option($opt_name5);
$opt_val6 = get_option($opt_name6);
$opt_val7 = get_option($opt_name7);
$opt_val8 = get_option($opt_name8);
$opt_val9 = get_option($opt_name9);
$wp_db_backup_destination_FTP=get_option('wp_db_backup_destination_FTP');

// BUTTON 3: 
// UPDATE DIRECTORY
// If user pressed this button, this hidden field will be set to 'Y'
if (isset($_POST[$hidden_field_name3]) && $_POST[$hidden_field_name3] == 'Y') {
    //Validate that the contents of the form request came from the current site and not somewhere else added 21-08-15 V.3.4
    if (!isset($_POST['wpdbbackup_update_setting']))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site!");
    if (!wp_verify_nonce($_POST['wpdbbackup_update_setting'], 'wpdbbackup-update-setting'))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site! ");
    // Read their posted value
    $opt_val6 = sanitize_text_field($_POST[$data_field_name6]);
    // Save the posted value in the database
    update_option($opt_name6, $opt_val6);
    // Put a "settings updated" message on the screen
    ?>
    <div class="updated"><p><strong><?php echo 'Your additional directory has been saved.'; ?></strong></p></div>
    <?php
}

// BUTTON 1: 
// SAVE SETTINGS
// If user pressed this button, this hidden field will be set to 'Y'
if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
    //Validate that the contents of the form request came from the current site and not somewhere else added 21-08-15 V.3.4
    if (!isset($_POST['wpdbbackup_update_setting']))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site! ");
    if (!wp_verify_nonce($_POST['wpdbbackup_update_setting'], 'wpdbbackup-update-setting'))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site! ");
    // Read their posted value
    @$opt_val = sanitize_text_field($_POST[$data_field_name]);
    @$opt_val2 = sanitize_text_field($_POST[$data_field_name2]);
     @$opt_val3 = sanitize_text_field($_POST[$data_field_name3]);
    @$opt_val4 = sanitize_text_field($_POST[$data_field_name4]);
    if (isset($_POST[$data_field_name5])) {
        @$opt_val5 = sanitize_text_field($_POST[$data_field_name5]);
    }
    @$opt_val9 = sanitize_text_field($_POST[$data_field_name9]);

    // Save the posted value in the database
    update_option($opt_name, $opt_val);
    update_option($opt_name2, $opt_val2);
    update_option($opt_name3, $opt_val3);
    update_option($opt_name4, $opt_val4);
    if(isset($_POST['wp_db_backup_destination_FTP'])){
     update_option('wp_db_backup_destination_FTP',1);
   }else{
     update_option('wp_db_backup_destination_FTP',0);
   }
   $wp_db_backup_destination_FTP=get_option('wp_db_backup_destination_FTP');
    if (isset($_POST[$data_field_name5])) {
        update_option($opt_name5, $opt_val5);
    }
    update_option($opt_name9, $opt_val9);

    // Put a "settings updated" message on the screen
    ?>
    <div class="updated"><p><strong><?php _e('Your FTP details have been saved.', 'backupbreeze-menu'); ?></strong></p></div>
    <?php
} // end if
//
	// BUTTON 2: 
// TEST SETTINGS
// If user pressed this button, this hidden field will be set to 'Y'

if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Test Connection') {
    //Validate that the contents of the form request came from the current site and not somewhere else added 21-08-15 V.3.4
    if (!isset($_POST['wpdbbackup_update_setting']))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site! ");
    if (!wp_verify_nonce($_POST['wpdbbackup_update_setting'], 'wpdbbackup-update-setting'))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site! ");
    include plugin_dir_path(__FILE__) . 'test-ftp.php';
    //
    // update all options while we're at it
    // @since 2.1
    $opt_val = sanitize_text_field($_POST[$data_field_name]);
    $opt_val2 = sanitize_text_field($_POST[$data_field_name2]);
    $opt_val3 = sanitize_text_field($_POST[$data_field_name3]);
    $opt_val4 = sanitize_text_field($_POST[$data_field_name4]);
    if (isset($_POST[$data_field_name5])) {
        $opt_val5 = sanitize_text_field($_POST[$data_field_name5]);
    }
    $opt_val9 = sanitize_text_field($_POST[$data_field_name9]);

    // Save the posted value in the database
    update_option($opt_name, $opt_val);
    update_option($opt_name2, $opt_val2);
    update_option($opt_name3, $opt_val3);
    update_option($opt_name4, $opt_val4);
    if (isset($_POST[$data_field_name5])) {
        update_option($opt_name5, $opt_val5);
    }
    update_option($opt_name9, $opt_val9);
    $result = backupbreeze_test_ftp();
    // echo "<h2>$result</h2>";

    if ($result != 'OK') {
        ?>
        <div class="error"><p><strong>connection has failed!<br /></strong></p>
            <?php echo $result . '<br /><br />'; ?>
        </div>
    <?php } else { ?>

        <div class="updated"><p><strong>Connected to <?php echo $opt_val; ?>, for user <?php echo $opt_val2; ?></strong></p></div>
        <?php
    } // end if 
} // end if
?>
<style>td, th {
        padding: 5px;
    }</style>
<p>Enter your FTP details for your offsite backup repository. Leave these blank for local backups or Disable FTP Destination.</p>		
<form  class="form-group" name="form1" method="post" action="">
    <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    <input name="wpdbbackup_update_setting" type="hidden" value="<?php echo wp_create_nonce('wpdbbackup-update-setting'); ?>" />
<?php  wp_nonce_field('wp-database-backup'); ?>

    <div class="row form-group">
        <label class="col-sm-2" for="wp_db_backup_destination_FTP">Enable FTP Destination:</label>
        <div class="col-sm-6">
            <input type="checkbox" id="wp_db_backup_destination_FTP" <?php echo (isset($wp_db_backup_destination_FTP) && $wp_db_backup_destination_FTP==1) ? 'checked' : '' ?> name="wp_db_backup_destination_FTP">
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-2" for="FTP_host">FTP Host:</label>
        <div class="col-sm-6">
            <input type="text" id="FTP_host" class="form-control" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="25" placeholder="e.g. ftp.yoursite.com">
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-2" for="FTP_port">FTP Port:</label>
        <div class="col-sm-2">
            <input type="text" id="FTP_port" class="form-control" name="<?php echo $data_field_name9; ?>" value="<?php echo $opt_val9; ?>" size="4">
        </div>
        <div class="col-sm-4">
            <em>defaults to 21 if left blank </em>
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-2" for="FTP_user">FTP User:</label>
        <div class="col-sm-6">
            <input type="text" id="FTP_user" class="form-control" name="<?php echo $data_field_name2; ?>" value="<?php echo $opt_val2; ?>" size="25">
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-2" for="FTP_password">FTP Password:</label>
        <div class="col-sm-6">
            <input type="password" id="FTP_password" class="form-control" name="<?php echo $data_field_name3; ?>" value="<?php echo $opt_val3; ?>" size="25">
        </div>
    </div>

    <div class="row form-group">
        <label class="col-sm-2" for="FTP_dir">Subdirectory:</label>
        <div class="col-sm-6">
            <input type="text" id="FTP_dir" placeholder="e.g. /httpdocs/backups" class="form-control" name="<?php echo $data_field_name4; ?>" value="<?php echo $opt_val4; ?>" size="25">
        </div>
        <div class="col-sm-4"> 
            <em>e.g. /httpdocs/backups or leave blank</em> 
        </div>
    </div>

    <p><input type="submit" name="Submit" class="btn btn-primary" value="<?php esc_attr_e('Save') ?>" />&nbsp;
        <input type="submit" name="<?php echo $hidden_field_name; ?>" class="btn btn-secondary" value="Test Connection" />

        <br />
    </p>
</form>
<hr />
<br />
