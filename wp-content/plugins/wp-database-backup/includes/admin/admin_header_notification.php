<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
$notifier_file_url = NOTIFIER_XML_FILE_WPDB; 
$changelogMsg='';

@$changeMessage="<strong><a href='http://www.wpseeds.com/product/wp-all-backup/' target='_blank'>WP All Backup Plugin.</a></strong> will backup and restore your entire site at will,
                        complete with FTP & S3 integration";
$coupon="Use Coupon code <strong>'WPDB30'</strong> and Get Flat 30% off on <strong><a href='http://www.wpseeds.com/product/wp-all-backup/' target='_blank'>WP All Backup Plugin.</a></strong>";
if (function_exists('curl_init')) { // if cURL is available, use it...
    $ch = curl_init($notifier_file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $notifier_data = curl_exec($ch);
    curl_close($ch);
} else {
    @$notifier_data = file_get_contents($notifier_file_url); // ...if not, use the common file_get_contents()
}
if (strpos((string) $notifier_data, '<notifier>') === false) {
    $notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog>';
}

// Load the remote XML data into a variable and return it
@$xml = simplexml_load_string($notifier_data);
if (!empty($xml)) {
     @$changelogMsg ='';
    if(!empty($xml->message)){
    
    @$changeMessage = $xml->message; 
    }
    if(!empty($xml->coupon)){
        $coupon=$xml->coupon;
        
    }
    if(!empty($xml->newrele)){
        $
        $changelogMsg.="<li class='list-group-item' >".$xml->newrelease."<li>";
    }
    if (WPDB_VERSION == $xml->latest) {
        $alert = '<strong>No Alert</strong><br/>';
        $changelog = '';
        
    } else {
        @$alert = '<strong><a href="http://www.wpseeds.com/blog/category/update/" title="Change Log" target="_blank">Plugin Updates</a></strong><br/>             
                <strong>There is a new version of the <br/>WP Database Backup plugin available.</strong>
                 You have version ' . WPDB_VERSION . ' Update to version ' . $xml->latest . '.';
        @$changelog = $xml->changelog;
        @$changelogMsg .= '<li class="list-group-item" ><strong>New Version Availabel</strong></li>';
        
           
        echo '<style>.glyphicon.glyphicon-bell {   
                    color: red !important;
                }</style>';
    }
} else {
    $alert = '<strong>No Alert</strong><br/>';
    $changelog = '';
    
        
}
        $changelogMsg.="<li class='list-group-item'>".$changeMessage."<li>";
        $changelogMsg.="<li class='list-group-item'>".$coupon."<li>";
?>
        <?php if (isset($_GET['notification'])) { ?>
    <div class="row">
        <div class="col-md-offset-4 col-xs-8 col-sm-8 col-md-8">
        <div class="alert alert-success alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <?php if ($_GET['notification'] == 'create') { 
             _e('Database Backup Created Successfully', 'wpdbbkp'); 
              } 
              else if ($_GET['notification'] == 'restore') { 
                _e('Database Backup Restore Successfully', 'wpdbbkp'); 
                 } 
              else if ($_GET['notification'] == 'delete') { 
                _e('Database Backup deleted Successfully', 'wpdbbkp'); 
                 } 
                 else if ($_GET['notification'] == 'clear_temp_db_backup_file') {
                  _e('Clear all old/temp database backup files Successfully', 'wpdbbkp'); 
             } 
             else if ($_GET['notification'] == 'Invalid') {
                  _e('Invalid Access!!!!', 'wpdbbkp'); 
             } else if ($_GET['notification'] == 'deleteauth') {
                 _e('Dropbox account unlink Successfully', 'wpdbbkp');
             }
            ?>
        </div>        
    </div>
 </div>
<?php } ?>
<div class="row">
    <div class="col-md-offset-8 col-xs-4 col-sm-4 col-md-4">

        <!-- Single button -->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php _e($changelogMsg, 'wpdbbkp'); ?>   
            </ul>
        </div>
        <!-- Single button -->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-bell" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu list-group">    
                <li  class="list-group-item "><?php _e($alert, 'wpdbbkp'); ?></li>  
                <?php if (!empty($changelog)) { ?>
                    <li  class="list-group-item "><?php _e($changelog, 'wpdbbkp'); ?></li>
<?php } ?>
            </ul>
        </div>

        <!-- Single button Setting-->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
               
                <li role="separator" class="divider"></li>
                <li>
                    <a href="#" >
                        <p><?php _e('Schedule :', 'wpdbbkp'); ?>
                            <?php
                            $settings = get_option('wp_db_backup_options');
                            if (isset($settings['enable_autobackups']) && $settings['enable_autobackups'] == '1') {
                                _e('Enabled -', 'wpdbbkp');
                                _e(ucfirst($settings['autobackup_frequency']), 'wpdbbkp');
                            } else {
                                _e('Disabled', 'wpdbbkp');
                            }
                            ?></p>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li >
                    <a href="#" >
                        <p><?php _e('Exclude Tables :', 'wpdbbkp'); ?></p>
<?php $wp_db_exclude_table=array();
	$wp_db_exclude_table=get_option('wp_db_exclude_table');
        if(!empty($wp_db_exclude_table))
        echo implode(',<br> ', $wp_db_exclude_table); ?></p>

                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li><a href="#">
                        <a href="#" >
                            <p><?php _e('Keep No of backup :', 'wpdbbkp'); ?>
                                <?php
                                if (get_option('wp_local_db_backup_count') == '0') {
                                    _e('Unlimited', 'wpdbbkp');
                                } else {
                                    echo get_option('wp_local_db_backup_count');
                                }
                                ?></p>
                        </a>
                    </a>
                </li>    
                <li role="separator" class="divider"></li>
                <li>
                    <a href="#" >
                        <p><?php _e('Backup Log :', 'wpdbbkp'); ?>
                            <?php
                            if (get_option('wp_db_log') == '1') {
                                _e('Enabled', 'wpdbbkp');
                            } else {
                                _e('Disabled', 'wpdbbkp');
                            }
                            ?></p>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="#db_setting" data-toggle="tab" title="<?php _e('Change Setting', 'wpdbbkp'); ?>"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?php _e('Change Setting', 'wpdbbkp'); ?></a>
                </li>
            </ul>
        </div>

        <!-- Single button Author-->
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="http://walkeprashant.in" target="_blank" >
                        <h5 ><?php _e('Plugin Author', 'wpdbbkp'); ?></h5>
                        <p><?php _e('Prashant Walke', 'wpdbbkp'); ?></p>
                        <p><?php _e('(Sr. PHP Developer)', 'wpdbbkp'); ?></p>
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li >
                    <a href="http://www.wpseeds.com/wp-database-backup/" target="_blank" >
                        <h5 ><?php _e('Plugin URL', 'wpdbbkp'); ?></h5>
                    </a>
                </li>
                <li >
                    <a href="http://www.wpseeds.com/blog/category/update/wp-database-backup/" target="_blank" >
                        <h5 ><?php _e('Change Log', 'wpdbbkp'); ?> </h5>
                    </a>
                </li>
                <li >
                    <a href="http://www.wpseeds.com/wp-database-backup/" target="_blank" >
                        <h5 ><?php _e('Documentation', 'wpdbbkp'); ?></h5>
                    </a>
                </li>
                <li >
                    <a href="http://www.wpseeds.com/support/" target="_blank" >
                        <h5 ><?php _e('Support', 'wpdbbkp'); ?></h5>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>