<?php

add_action('wp_db_backup_completed', array('WPDBBackupGoogle', 'wp_db_backup_completed'));

class WPDBBackupGoogle {

    public static function wp_db_backup_completed(&$args) {

        $authCode = get_option('wpdb_dest_google_authCode');
        $clientId = get_option('wpdb_dest_google_client_key');
        $clientSecret = get_option('wpdb_dest_google_secret_key');

        if (!empty($authCode) && !empty($clientId) && !empty($clientSecret)) {
            set_time_limit(0);
            require_once("google-api-php-client/src/Google_Client.php");
            require_once("google-api-php-client/src/contrib/Google_DriveService.php");
            $client = new Google_Client();
            // Get your credentials from the APIs Console
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri(site_url() . '/wp-admin/tools.php?page=wp-database-backup&action=auth');
            $client->setScopes(array("https://www.googleapis.com/auth/drive"));
            $service = new Google_DriveService($client);
            // Exchange authorisation code for access token
            if (!file_exists("token.json")) {
                // Save token for future use
                $accessToken = $client->authenticate($authCode);
                file_put_contents("token.json", $accessToken);
            } else
                $accessToken = file_get_contents("token.json");
            $client->setAccessToken($accessToken);
            // Upload file to Google Drive  
            $file = new Google_DriveFile();
            $file->setTitle($args[0]);
            $file->setDescription("WP Database Backup : database backup file-".site_url());
            $file->setMimeType("application/gzip");
            $data = file_get_contents($args[1]);
            $createdFile = $service->files->insert($file, array('data' => $data, 'mimeType' => "application/gzip",));
            $args[2] =$args[2]. '<br> Upload Database Backup on google drive';
            // Process response here....            
        }
    }

}
