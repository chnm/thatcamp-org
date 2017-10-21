<?php
add_action('wp_db_backup_completed', array('WPDBBackupDropbox', 'wp_db_backup_completed'));

class WPDBBackupDropbox
{

    public static function wp_db_backup_completed(&$args)
    {
        include plugin_dir_path(__FILE__) . 'DropboxClient.php';
        $dropbox = new WPDBBackup_Destination_Dropbox_API('dropbox');
        $wpdb_dropboxtoken=get_option('wpdb_dropboxtoken');
        $dropboxtoken = (!empty($wpdb_dropboxtoken)) ? maybe_unserialize($wpdb_dropboxtoken) : array();
        if (isset($dropboxtoken['access_token']) && !empty($dropboxtoken['access_token'])) {
            $dropbox->setOAuthTokens($dropboxtoken);
            $wpdb_dropbbox_dir=get_option('wpdb_dropbbox_dir');
            $wpdb_dropbbox_dir=!empty($wpdb_dropbbox_dir) ? '/'.get_option('wpdb_dropbbox_dir').'/' : '';
            $response = $dropbox->upload($args[1], $wpdb_dropbbox_dir . $args[0]);
            if ($response)
                $args[2] = $args[2] . "<br> Upload Database Backup on Dropbox";
        }

    }

}