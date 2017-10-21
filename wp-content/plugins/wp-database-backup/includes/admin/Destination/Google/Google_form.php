<?php
ob_start();
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$updateMsg = '';
if (isset($_POST['wpdb_google_drive']) && $_POST['wpdb_google_drive'] == 'Y') {
    //Validate that the contents of the form request came from the current site and not somewhere else added 21-08-15 V.3.4
    if (!isset($_POST['wpdbbackup_update_google_setting']))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site!");
    if (!wp_verify_nonce($_POST['wpdbbackup_update_google_setting'], 'wpdbbackup-update-google-setting'))
        die("<br><br>Invalid form data. form request came from the somewhere else not current site! ");
    if (isset($_POST['Save']) && $_POST['Save'] == 'Save') {
        $clientId = sanitize_text_field($_POST['wpdb_dest_google_client_key']);
        $clientSecret = sanitize_text_field($_POST['wpdb_dest_google_secret_key']);
        update_option('wpdb_dest_google_client_key', $clientId);
        update_option('wpdb_dest_google_secret_key', $clientSecret);
    } else if (isset($_POST['Submit']) && $_POST['Submit'] == 'Allow Access') {
        // Save the posted value in the database
        $clientId = sanitize_text_field($_POST['wpdb_dest_google_client_key']);
        $clientSecret = sanitize_text_field($_POST['wpdb_dest_google_secret_key']);
        update_option('wpdb_dest_google_client_key', $clientId);
        update_option('wpdb_dest_google_secret_key', $clientSecret);

        require_once("google-api-php-client/src/Google_Client.php");
        require_once("google-api-php-client/src/contrib/Google_DriveService.php");

        $client = new Google_Client();
        // Get your credentials from the APIs Console
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri(site_url() . '/wp-admin/tools.php?page=wp-database-backup&action=auth');
        $client->setScopes(array('https://www.googleapis.com/auth/drive'));

        $service = new Google_DriveService($client);

        $authUrl = $client->createAuthUrl();
        if (isset($_GET['code'])) {
            update_option('wpdb_dest_google_authCode', $_GET['code']);
        } else {
            if (isset($_POST['wpdb_dest_google_client_key']) && !empty($_POST['wpdb_dest_google_client_key']) && isset($_POST['wpdb_dest_google_secret_key']) && !empty($_POST['wpdb_dest_google_secret_key']))
                wp_redirect(filter_var($authUrl, FILTER_SANITIZE_URL));
        }
    }else if (isset($_POST['reset']) && $_POST['reset'] == 'Reset Configure') {
        update_option('wpdb_dest_google_authCode', '');
        wp_redirect(site_url() . '/wp-admin/tools.php?page=wp-database-backup');
    }

    // Put a "settings updated" message on the screen
    $updateMsg = '<div class="updated"><p><strong>Your google drive setting has been saved.</strong></p></div>';
}
if (isset($_GET['code'])) {
    update_option('wpdb_dest_google_authCode', $_GET['code']);
}
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapsegoogle">
                <h2>Google drive</h2>
            </a>
        </h4>
    </div>
    <div id="collapsegoogle" class="panel-collapse collapse in">
        <div class="panel-body">
            <?php echo $updateMsg ?>
            <form  class="form-group" name="googledrive" method="post" action="">
                <input type="hidden" name="wpdb_google_drive" value="Y">
                <input name="wpdbbackup_update_google_setting" type="hidden" value="<?php echo wp_create_nonce('wpdbbackup-update-google-setting'); ?>" />
                <?php
                 wp_nonce_field('wp-database-backup'); 
                $wpdb_dest_google_authCode = get_option('wpdb_dest_google_authCode');
                $wpdb_dest_google_client_key = get_option('wpdb_dest_google_client_key');
                $wpdb_dest_google_secret_key = get_option('wpdb_dest_google_secret_key');
                if (!empty($wpdb_dest_google_authCode) && !empty($wpdb_dest_google_client_key) && !empty($wpdb_dest_google_secret_key)) {
                    ?>
                    <p class="text-success">Configuration to Google Drive Access has been done successfully</p>
                    <p>By clicking reset, you can reconfigure Google Account</p>
                    <p>For local backup click on Reset Configure</p>
                    <p><input type="submit" name="reset" class="btn btn-primary" value="<?php esc_attr_e('Reset Configure') ?>" />&nbsp;       
                    </p>
                <?php } else { ?>

                    <p><a href="http://www.wpseeds.com/wp-database-backup/#google" target="_blank"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> Back up WordPress database to google drive.</p>
                    <p>Configure google account, you need to create Client ID &amp; Client secret from the API section '<a href="https://code.google.com/apis/console/" target="_blank">Google API Console</a>' also use authorization redirecting url as <br>        
                        <strong> <?php echo site_url() . '/wp-admin/tools.php?page=wp-database-backup&action=auth'; ?></strong></p>
                    <p>For local backup leave the setting as it is</p>

                    <div class="row form-group">
                        <label class="col-sm-2" for="wpdb_dest_google_client_key">Client ID</label>
                        <div class="col-sm-6">
                            <input type="text" id="wpdb_dest_google_client_key" class="form-control" name="wpdb_dest_google_client_key" value="<?php echo get_option('wpdb_dest_google_client_key'); ?>" size="25" placeholder="your client id">
                        </div>
                    </div>

                    <div class="row form-group">
                        <label class="col-sm-2" for="wpdb_dest_google_secret_key">Client secret:</label>
                        <div class="col-sm-6">
                            <input type="text" id="wpdb_dest_google_secret_key" class="form-control" name="wpdb_dest_google_secret_key" value="<?php echo get_option('wpdb_dest_google_secret_key'); ?>" size="25" placeholder="your client secret key">
                        </div>
                    </div>

                    <p><input type="submit" name="Submit" class="btn btn-primary" value="<?php esc_attr_e('Allow Access') ?>" />&nbsp;       
                        <input type="submit" name="Save" class="btn btn-secondary" value="<?php esc_attr_e('Save') ?>" />&nbsp;       
                    </p>
                <?php } ?>
            </form>
        </div>		
    </div>
</div>