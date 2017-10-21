<?php
include plugin_dir_path(__FILE__) . 'DropboxClient.php';
if (isset($_GET['action']) && $_GET['action'] == 'deleteauth') {
    //disable token on dropbox
    try {
        $dropbox = new WPDBBackup_Destination_Dropbox_API();
        $dropbox->setOAuthTokens(maybe_unserialize(get_option('wpdb_dropboxtoken')));
        $dropbox->authTokenRevoke();
    } catch (Exception $e) {
        echo '<div id="message" class="error"><p>' . sprintf(__('Dropbox API: %s', 'wpdbbkp'), $e->getMessage()) . '</p></div>';
    }
    update_option('wpdb_dropboxtoken', '');
    wp_redirect(site_url() . '/wp-admin/tools.php?page=wp-database-backup&notification=deleteauth');

}

$dropbox = new WPDBBackup_Destination_Dropbox_API('dropbox');
$dropbox_auth_url = $dropbox->oAuthAuthorize();
if (isset($_POST['wpdb_dropbbox_code']) && !empty($_POST['wpdb_dropbbox_code'])) {
    $dropboxtoken = $dropbox->oAuthToken(sanitize_text_field($_POST['wpdb_dropbbox_code']));
    $dropboxtoken = update_option('wpdb_dropboxtoken', maybe_serialize($dropboxtoken));
}

if (isset($_POST['wpdb_dropbbox_dir'])) {
    $dropboxtoken = update_option('wpdb_dropbbox_dir', sanitize_text_field($_POST['wpdb_dropbbox_dir']));
}

$wpdb_dropboxtoken=get_option('wpdb_dropboxtoken');
$dropboxtoken = !empty($wpdb_dropboxtoken) ? maybe_unserialize($wpdb_dropboxtoken) : array();


?>
<form class="form-group" name="form2" method="post" action="">

    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e('Authentication', 'wpdbbkp'); ?></th>
            <td><?php if (empty($dropboxtoken['access_token'])) { ?>
                    <span style="color:red;"><?php esc_html_e('Not authenticated!', 'wpdbbkp'); ?></span><br/>&nbsp;
                    <br/>
                    <a class="button secondary"
                       href="http://db.tt/8irM1vQ0"
                       target="_blank"><?php esc_html_e('Create Account', 'wpdbbkp'); ?></a><br/><br/>
                <?php } else { ?>
                    <span style="color:green;"><?php esc_html_e('Authenticated!', 'wpdbbkp'); ?></span>
                    <?php
                    $dropbox->setOAuthTokens($dropboxtoken);
                    $info = $dropbox->usersGetCurrentAccount();
                    if (!empty($info['account_id'])) {

                        $user = $info['name']['display_name'];

                        _e(' with Dropbox of user ', 'wpdbbkp');
                        echo $user . '<br/>';
                        //Quota
                        $quota = $dropbox->usersGetSpaceUsage();
                        $dropboxfreespase = $quota['allocation']['allocated'] - $quota['used'];
                        echo size_format($dropboxfreespase, 2);
                        _e(' available on your Dropbox', 'wpdbbkp');

                    }
                    ?>
                    <br><br>
                    <a class="button secondary"
                       href="<?php echo site_url() . '/wp-admin/tools.php?page=wp-database-backup&action=deleteauth&_wpnonce=' . $nonce ?>"
                       title="<?php esc_html_e('Unlink Dropbox Account', 'wpdbbkp'); ?>"><?php esc_html_e('Unlink Dropbox Account', 'wpdbbkp'); ?></a>
                    <p>Unlink Dropbox Account for local backups.</p>
                <?php } ?>
            </td>
        </tr>

        <?php if (empty($dropboxtoken['access_token'])) { ?>
            <tr>
                <th scope="row"><label
                        for="id_dropbbox_code"><?php esc_html_e('Access to Dropbox', 'wpdbbkp'); ?></label></th>
                <td>
                    <input id="id_dropbbox_code" name="wpdb_dropbbox_code" type="text" value=""
                           class="regular-text code"/>&nbsp;
                    <a class="button secondary" href="<?php echo esc_attr($dropbox_auth_url); ?>"
                       target="_blank"><?php esc_html_e('Get Dropbox auth code ', 'wpdbbkp'); ?></a>
                    <p>In order to use Dropbox destination you will need to Get Dropbox auth code with your Dropbox
                        account on click 'Get Dropbox auth code' button</p>
                    <p>Enter Dropbox auth code in text box and save changes</p>
                    <p>For local backup leave the setting as it is</p>
                </td>
            </tr>
        <?php } ?>
    </table>

    <p></p>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="iddropboxdir"><?php esc_html_e('Destination Folder', 'wpdbbkp'); ?></label></th>
            <td>
                <input id="wpdb_dropbbox_dir" name="wpdb_dropbbox_dir" type="text"
                       value="<?php echo get_option('wpdb_dropbbox_dir'); ?>" class="regular-text"/>
                <p class="description">
                    <?php esc_attr_e('Specify a subfolder where your backup archives will be stored. It will be created at the Apps › WP-Database-Backup of your Dropbox. Already exisiting folders with the same name will not be overriden.', 'wpdbbkp'); ?>

                </p>
                <p>E.g. backup</p>
            </td>
        </tr>
    </table>
    <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    <input name="wpdbbackup_update_setting" type="hidden"
           value="<?php echo wp_create_nonce('wpdbbackup-update-setting'); ?>"/>
    <?php wp_nonce_field('wp-database-backup'); ?>

    <input type="submit" name="Submit" class="btn btn-primary" value="<?php esc_attr_e('Save') ?>"/>&nbsp;
</form>