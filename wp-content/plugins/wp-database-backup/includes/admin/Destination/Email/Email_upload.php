<?php

add_action('wp_db_backup_completed', array('WPDBBackupEmail', 'wp_db_backup_completed'),11);

class WPDBBackupEmail {

    public static function wp_db_backup_completed(&$args) {
        $destination_Email=get_option('wp_db_backup_destination_Email');
        if (isset($destination_Email) && $destination_Email==1 && get_option('wp_db_backup_email_id')) {
            $to = get_option('wp_db_backup_email_id');
            $subject = "Database Backup Created Successfully";
            $filename = $args[0];
            $filesze = $args[3];
            $site_url = site_url();
            $logMessageAttachment="";
            $message="";
            error_log("in mail send function".$args[2]);

            include('template_email_notification.php');         


            $headers = array('Content-Type: text/html; charset=UTF-8');
            $wp_db_backup_email_attachment_file = get_option('wp_db_backup_email_attachment');
            if ($wp_db_backup_email_attachment_file == "yes" && $filesze <= 209700000) {               
                $attachments = $args[1];
                $logMessageAttachment = " with attached backup file.";                
            } else
                $attachments = "";
            if(wp_mail($to, $subject, $message, $headers, $attachments)){
                error_log("mail send");
            }
            $logMessage=" Send Backup Mail to:" . $to;
            $logMessage.=$logMessageAttachment;
            $wp_db_remove_local_backup = get_option('wp_db_remove_local_backup');
                if ($wp_db_remove_local_backup == 1) {
                   $logMessage.= " Removed local backup file.";
                }
        }
    }
    
     public static function wp_db_backup_format_bytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}
