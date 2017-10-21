<?php

add_action('wp_db_backup_completed', array('WPDatabaseBackupS3', 'wp_db_backup_completed'));

class WPDatabaseBackupS3 {

    public static function wp_db_backup_completed(&$args) {
        $destination_s3=get_option('wp_db_backup_destination_s3');
        if (isset($destination_s3) && $destination_s3==1 && get_option('wpdb_dest_amazon_s3_bucket') && get_option('wpdb_dest_amazon_s3_bucket_key') && get_option('wpdb_dest_amazon_s3_bucket_secret')) {
            try {
                if (!class_exists('S3'))
                    require_once 'S3.php';
// AWS access info
                if (!defined('awsAccessKey'))
                    define('awsAccessKey', get_option('wpdb_dest_amazon_s3_bucket_key'));
                if (!defined('awsSecretKey'))
                    define('awsSecretKey', get_option('wpdb_dest_amazon_s3_bucket_secret'));

// Check for CURL
                if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
                    error_log("ERROR: CURL extension not loaded");

                $s3 = new S3(awsAccessKey, awsSecretKey);
                $bucketName = get_option('wpdb_dest_amazon_s3_bucket');
                $result = $s3->listBuckets();
                if (get_option('wpdb_dest_amazon_s3_bucket')) {
                    if (in_array(get_option('wpdb_dest_amazon_s3_bucket'), $result)) {
                        if ($s3->putObjectFile($args[1], $bucketName, baseName($args[1]), S3::ACL_PUBLIC_READ)) {

                            error_log("S3::$args[0] upload in bucket {$bucketName}");
                            $args[2] = $args[2] . '<br> Upload Database Backup on s3 bucket ' . $bucketName;
                        } else {
                            error_log("S3::Failed to upload $args[0]");
                            $args[2] = $args[2] . '<br>Failed to upload Database Backup on s3 bucket ' . $bucketName;
                        }
                    } else {
                        error_log("Invalid bucket name or AWS details");
                        $args[2] = $args[2] . '<br>Invalid bucket name or AWS details';
                    }
                }
            } catch (Exception $e) {
                // echo ($e->getMessage());
                error_log("Invalid AWS details");
            }
        }
    }

}
