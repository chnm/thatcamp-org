<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPDB_Admin {

	public function __construct() {
	        add_action('admin_init',  array( $this,'wp_db_backup_admin_init'));
                add_action( 'admin_init', array( $this, 'admin_scripts_style' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_filter('cron_schedules', array( $this,'wp_db_backup_cron_schedules'));
		add_action('wp_db_backup_event', array( $this,'wp_db_backup_event_process'));
	        add_action('wp', array( $this,'wp_db_backup_scheduler_activation'));

	
	}

	public function admin_menu()
	{
		
		$page = add_management_page('WP-DB Backup', 'WP-DB Backup ', 'manage_options', 'wp-database-backup', array( $this,'wp_db_backup_settings_page'));
		
		
	}
	function wp_db_backup_admin_init() {
		if(is_admin()){
                    if(isset($_POST['wpsetting'])){                        
                    if(isset($_POST['wp_local_db_backup_count'])){
                              update_option('wp_local_db_backup_count',sanitize_text_field($_POST['wp_local_db_backup_count']));                            
                          }
                    if(isset($_POST['wp_db_log'])){
                              update_option('wp_db_log',1);                            
                          }else{
                               update_option('wp_db_log',0);           
                          }
                    }
	        if(isset($_POST['wp_db_backup_email_id']))
		 {
		   
		   update_option('wp_db_backup_email_id',sanitize_text_field($_POST['wp_db_backup_email_id']));
		  }
		   if(isset($_POST['wp_db_backup_email_attachment']))
		 {
		   $email_attachment=sanitize_text_field($_POST['wp_db_backup_email_attachment']);
		   update_option('wp_db_backup_email_attachment',$email_attachment);
		  }
		   if(isset($_GET['page']) && $_GET['page']=="wp-database-backup" && isset($_GET['action']) && $_GET['action']=="unlink") 
		   {
		     // Specify the target directory and add forward slash
           $dir = plugin_dir_path(__FILE__)."Destination/Dropbox/tokens/"; 
           
            // Open the directory
            $dirHandle = opendir($dir); 
             // Loop over all of the files in the folder
           while ($file = readdir($dirHandle)) { 
              // If $file is NOT a directory remove it
               if(!is_dir($file)) { 
                      unlink ("$dir"."$file"); // unlink() deletes the files
                 }
            }
               // Close the directory
                closedir($dirHandle); 
                wp_redirect(get_bloginfo('url').'/wp-admin/tools.php?page=wp-database-backup');
		   	
		   }
	        if(isset($_GET['action']) && current_user_can('manage_options')) {
		switch((string)$_GET['action']) {
 
			case 'createdbbackup':
				$this->wp_db_backup_event_process();
				wp_redirect(get_bloginfo('url').'/wp-admin/tools.php?page=wp-database-backup');
				break;
			case 'removebackup':
				$index = (int)$_GET['index'];
				$options = get_option('wp_db_backup_backups');
				$newoptions = array();
				$count = 0;
				foreach($options as $option) {
					if($count != $index) {
						$newoptions[] = $option;
					}
					$count++;
				}
				
				unlink($options[$index]['dir']);
                                 $sqlFile=  explode('.', $options[$index]['dir']);                                  
                                 @unlink($sqlFile[0].'.sql');
				update_option('wp_db_backup_backups', $newoptions);
				wp_redirect(get_bloginfo('url').'/wp-admin/tools.php?page=wp-database-backup');
				break;
			case 'restorebackup':
				$index = (int)$_GET['index'];
				$options = get_option('wp_db_backup_backups');
				$newoptions = array();
				$count = 0;
				foreach($options as $option) {
					if($count != $index) {
						$newoptions[] = $option;
					}
					$count++;
				}
                                if(isset($options[$index]['sqlfile'])){ //Added for extract zip file V.3.3.0
                                        $database_file=($options[$index]['sqlfile']);         
                                }else{
                                    $database_file=($options[$index]['dir']);  
					 $sqlFile=  explode('.', $options[$index]['dir']);                       
                                          $database_file=($sqlFile[0].'.sql'); 
                                }
				$database_name=$this->wp_backup_get_config_db_name();
				$database_user=$this->wp_backup_get_config_data('DB_USER');				
				$datadase_password=$this->wp_backup_get_config_data('DB_PASSWORD');
				$database_host=$this->wp_backup_get_config_data('DB_HOST');
				$path_info = wp_upload_dir();
                                //Added for extract zip file V.3.3.0
                                 $ext_path_info=$path_info['basedir'].'/db-backup';
                                 $database_zip_file=$options[$index]['dir'];
                                  $zip = new ZipArchive;
                                  if ($zip->open($database_zip_file) === TRUE) {
                                      $zip->extractTo($ext_path_info);
                                      $zip->close();                                        
                                  }     
                                  //End for extract zip file V.3.3.0
				ini_set("max_execution_time", "5000"); 
				ini_set("max_input_time",     "5000");
				ini_set('memory_limit', '1000M');
				set_time_limit(0);
				
				
	  if((trim((string)$database_name) != '') && (trim((string)$database_user) != '') && (trim((string)$datadase_password) != '') && (trim((string)$database_host) != '') && ($conn = @mysql_connect((string)$database_host, (string)$database_user, (string)$datadase_password))) {
		/*BEGIN: Select the Database*/
		if(!mysql_select_db((string)$database_name, $conn)) {
			$sql = "CREATE DATABASE IF NOT EXISTS `".(string)$database_name."`";
			mysql_query($sql, $conn);
			mysql_select_db((string)$database_name, $conn);
		}
		/*END: Select the Database*/
		
		/*BEGIN: Remove All Tables from the Database*/
		$found_tables = null;
		if($result = mysql_query("SHOW TABLES FROM `{".(string)$database_name."}`", $conn)){
			while($row = mysql_fetch_row($result)){
				$found_tables[] = $row[0];
			}
			if (count($found_tables) > 0) {
				foreach($found_tables as $table_name){
					mysql_query("DROP TABLE `{".(string)$database_name."}`.{$table_name}", $conn);
				}
			}
		}
		/*END: Remove All Tables from the Database*/
		
		/*BEGIN: Restore Database Content*/
		if(isset($database_file))
		{
		
		$database_file=$database_file;
		$sql_file = @file_get_contents($database_file, true);
		
		$sql_queries = explode(";\n", $sql_file);
		
		
		for($i = 0; $i < count($sql_queries); $i++) {
			mysql_query($sql_queries[$i], $conn);
		}
		
		
		}
		}
                 if(isset($options[$index]['sqlfile']) && file_exists($options[$index]['sqlfile'])){ //Added for extract zip file V.3.3.0
                     @unlink($options[$index]['sqlfile']);
                 }else{
                                         $database_file=($options[$index]['dir']);  
					 $sqlFile=  explode('.', $options[$index]['dir']);                       
                                         $database_file=($sqlFile[0].'.sql');
					 @unlink( $database_file);
                                }
		break;
		
		/*END: Restore Database Content*/
				
		}
	}
	
	register_setting('wp_db_backup_options', 'wp_db_backup_options', array( $this,'wp_db_backup_validate'));
    @add_settings_section('wp_db_backup_main', '', 'wp_db_backup_section_text', array( $this,'wp-database-backup'));
}
}
function wp_db_backup_validate($input) {	
	return $input;
}
	public function wp_db_backup_settings_page(){
	        $options = get_option('wp_db_backup_backups');
	        $settings = get_option('wp_db_backup_options');
		?> <div class="panel panel-info">
			<div class="panel-heading">
                                 <h4><a href="http://walkeprashant.wordpress.com" target="blank"><img src="<?php echo WPDB_PLUGIN_URL.'/assets/images/wp-database-backup.png';?>" ></a>Database Backup Settings <a href="http://www.wpseeds.com/product/wp-all-backup/" target="_blank"><span style='float:right' class="label label-success">Get Pro 'WP All Backup' Plugin</span></a></h4>
                         </div>
                         <div class="panel-body">
			  <ul class="nav nav-tabs">
			    <li class=""><a href="#db_home" data-toggle="tab">Database Backups</a></li>
			    <li><a href="#db_schedul" data-toggle="tab">Scheduler</a></li>
                            <li><a href="#db_setting" data-toggle="tab">Settings</a></li>
			    <li><a href="#db_help" data-toggle="tab">Help</a></li>
                            <li><a href="#db_info" data-toggle="tab">Database Information</a></li>
			    <li><a href="#db_destination" data-toggle="tab">Destination</a></li>
                             <li><a href="#db_advanced" data-toggle="tab">Advance Feature</a></li>
		          </ul>
	                    
	                 <?php 
	                      echo '<div class="tab-content">';
                              echo '<div class="tab-pane active"  id="db_home">';
                              echo '<p class="submit">';
				echo '<a href="'.get_bloginfo('url').'/wp-admin/tools.php?page=wp-database-backup&action=createdbbackup" class="button-primary"><span class="glyphicon glyphicon-plus-sign"></span> Create New Database Backup</a>';
			      echo '</p>';
                              ?>
                             
                                  <?php
			if($options) {
				
					echo ' <div class="table-responsive">
                                <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline" role="grid">                               
                                
                                <table class="table table-striped table-bordered table-hover display" id="example">
                                    <thead>';
						echo '<tr class="wpdb-header">';
							echo '<th class="manage-column" scope="col" width="15%" style="text-align: center;">SL No</th>';
							echo '<th class="manage-column" scope="col" width="25%">Date</th>';
                                                        echo '<th class="manage-column" scope="col" width="5%"></th>';
							echo '<th class="manage-column" scope="col" width="15%">Backup File</th>';
							echo '<th class="manage-column" scope="col" width="10%">Size</th>';
							echo '<th class="manage-column" scope="col" width="15%"></th>';
							echo '<th class="manage-column" scope="col" width="15%"></th>';
						echo '</tr>';
					echo '</thead>';
					
					echo '<tbody>';
						$count = 1;
						foreach($options as $option) {
							echo '<tr '.((($count % 2) == 0)?' class="alternate"':'').'>';
								echo '<td style="text-align: center;">'.$count.'</td>';
								echo '<td>'.date('jS, F Y', $option['date']).'<br />'.date('h:i:s A', $option['date']).'</td>';
								echo '<td>';
                                                                if(!empty($option['log'])){
                                                                echo '<button id="popoverid" type="button" class="popoverid btn" data-toggle="popover" title="Log" data-content="'.$option['log'].'"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></button>';
                                                                }
                                                                echo '</td>';
                                                                echo '<td>';
                                                                echo '<a href="'.$option['url'].'" style="color: #21759B;">';
                                                                echo '<span class="glyphicon glyphicon-download-alt"></span> Download</a></td>';
								echo '<td>'.$this->wp_db_backup_format_bytes($option['size']).'</td>';
								echo '<td><a href="'.get_bloginfo('url').'/wp-admin/tools.php?page=wp-database-backup&action=removebackup&index='.($count - 1).'" class="button-secondary"><span style="color:red" class="glyphicon glyphicon-remove"></span> Remove Database Backup<a/></td>';
								echo '<td><a href="'.get_bloginfo('url').'/wp-admin/tools.php?page=wp-database-backup&action=restorebackup&index='.($count - 1).'" class="button-secondary"><span class="glyphicon glyphicon-refresh" style="color:blue"></span> Restore Database Backup<a/></td>';
							echo '</tr>';
							$count++;
						}
					echo '</tbody>';
				
                                        echo ' </table>     
                                </div>
                                  </div>';
			} else {
				echo '<p>No Database Backups Created!</p>';
			}
			echo "<div class='alert alert-success' role='alert'><h4>Get Flat 25% off on <a href='http://www.wpseeds.com/product/wp-all-backup/' target='_blank'>WP All Backup Plugin.</a> Use Coupon code 'WPDB25'</h4></div>";
		echo '</div>';
	
	echo '<div class="tab-pane" id="db_schedul">';
echo '<form method="post" action="options.php" name="wp_auto_commenter_form">';
			 settings_fields('wp_db_backup_options'); 
			do_settings_sections('wp-database-backup'); 
		
			echo '<p>Enable Auto Backups&nbsp;';
				echo '<input type="checkbox" name="wp_db_backup_options[enable_autobackups]" value="1" '.@checked(1, $settings['enable_autobackups'], false).'/>';
			echo '</p>';
			echo '<p>Auto Database Backup Frequency<br />';
				echo '<select name="wp_db_backup_options[autobackup_frequency]" style="width: 100%; margin: 5px 0 0;">';
					echo '<option value="hourly" '.selected('hourly', $settings['autobackup_frequency'], false).'>Hourly</option>';
                                        echo '<option value="daily" '.selected('daily', $settings['autobackup_frequency'], false).'>Daily</option>';
					echo '<option value="weekly" '.selected('weekly', $settings['autobackup_frequency'], false).'>Weekly</option>';
					echo '<option value="monthly" '.selected('monthly', $settings['autobackup_frequency'], false).'>Monthly</option>';
				echo '</select>';
			echo '</p>';
                        
			echo '<p class="submit">';
				echo '<input type="submit" name="Submit" class="button-primary" value="Save Settings" />';
			echo '</p>';
echo '</form>';
		echo '</div>';
		
	echo '<div class="tab-pane" id="db_help">';
			echo '<p>';
			?>
                             
                          <script>
                                          var $j = jQuery.noConflict();
                            $j(document).ready(function() {       
                                $j('.popoverid').popover();
                                    var table = $j('#example').DataTable();
                           } );
                           </script>
			<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Create Backup
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
      <p>Step 1) Click on Create New Database Backup</p>
      <p>Step 2) Download Database Backup file.</p>
	</div>		
        </div>
    </div>
  
  
  <div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Restore Backup
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse in">
      <div class="panel-body">
      <p>Click on Restore Database Backup </p><p>OR</p>
     
      <p>Step 1) Login to phpMyAdmin.</p>
      <p>Step 2) Click Databases and select the database that you will be importing your data into.</p>
      <p>Step 3) Across the top of the screen will be a row of tabs. Click the Import tab.</p>
      <p>Step 4) On the next screen will be a location of text file box, and next to that a button named Browse.</p>
      <p>Step 5) Click Browse. Locate the backup file stored on your computer.</p>
      <p>Step 6) Click the Go button.</p>
	</div>		
        </div>
    </div>

  
    <div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          Support
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse in">
      <div class="panel-body">
	  <button type="button" class="btn btn-default"><a href='http://www.wpseeds.com/support/'>Support</a></button>
          <button type="button" class="btn btn-default"><a href='http://www.wpseeds.com/wp-database-backup/'>Documentation</a></button>
     <p>If you want more feature or any suggestion then drop me mail we are try to implement in our wp-database-backup plugin and also try to make it more user friendly</p><p><span class="glyphicon glyphicon-envelope"></span> Drop Mail :walke.prashant28@gmail.com</p>
	 If you like this plugin then Give <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/wp-database-backup" title="Rating" sl-processed="1">rating </a>on <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/wp-database-backup" title="Rating" sl-processed="1">WordPress.org</a></p>
	 <p></br><a title="WP-DB-Backup" href="http://walkeprashant.wordpress.com/wp-database-backup/" target="_blank">More Information</a></p>
			<p >Support us to improve plugin. your idea and support are always welcome.<br>
                <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=387BZU5UNQ4LA" sl-processed="1"><img alt="donate" src="http://walkeprashant.files.wordpress.com/2014/09/donate.jpg?w=940" class="alignleft wp-image-304 size-full"></a></p>
			
	
	</div>		
        </div>
    </div>
  
  </div>
 </div></div>


	
 </div>

                    <div class="tab-pane" id="db_info">                        
                       <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapsedb">
                                  Database Information

                                </a>
                              </h4>
                            </div>
                           <div id="collapsedb" class="panel-collapse collapse in">
                               <div class="panel-body">
                                   <table class="table table-condensed">
                                     <tr class="success">
                                        <th>Setting</th>
                                        <th>Value</th>
			       </tr>
                                <tr>
                                    <td>Database Host</td><td><?php echo DB_HOST; ?></td>
                                </tr>
                                <tr class="default">
                                    <td>Database Name</td><td> <?php echo DB_NAME; ?></td>
                                </tr>
                                <tr>
                                    <td>Database User</td><td><?php echo DB_USER; ?></td></td>
                                </tr>
                                <tr>
                                    <td>Database Type</td><td>MYSQL</td>
                                </tr>
                                <tr>
                                    <?php
                                    // Get MYSQL Version
                                    global $wpdb;
                                    $mysqlversion = $wpdb->get_var("SELECT VERSION() AS version");
                                    ?>
                                    <td>Database Version</td><td>v<?php echo $mysqlversion; ?></td>
                                </tr>
                            </table>
                                  
                               </div>
                           </div>
                       </div>
                        
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapsedbtable">
                                  Tables Information

                                </a>
                              </h4>
                            </div>
                           <div id="collapsedbtable" class="panel-collapse collapse">
                               <div class="panel-body">
                                   <table class="table table-condensed">
                                     <tr class="success">                                        
                                            <th>No.</th>
                                            <th>Tables</th>
                                            <th>Records</th>
                                         		
                                    </tr>                                 
                                        <?php
                                                $no = 0;
                                                $row_usage = 0;
                                                $data_usage = 0;                                                
                                                $tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
                                                foreach($tablesstatus as  $tablestatus) {
                                                        if($no%2 == 0) {
                                                                $style = '';
                                                        } else {
                                                                $style = ' class="alternate"';
                                                        }
                                                        $no++;
                                                        echo "<tr$style>\n";
                                                        echo '<td>'.number_format_i18n($no).'</td>'."\n";
                                                        echo "<td>$tablestatus->Name</td>\n";
                                                        echo '<td>'.number_format_i18n($tablestatus->Rows).'</td>'."\n";                                                       
                                                       
                                                        $row_usage += $tablestatus->Rows;
                                                       
				
                                                        echo '</tr>'."\n";
                                                        }
                                                        echo '<tr class="thead">'."\n";
                                                        echo '<th>'.__('Total:', 'wp-dbmanager').'</th>'."\n";
                                                        echo '<th>'.sprintf(_n('%s Table', '%s Tables', $no, 'wp-dbmanager'), number_format_i18n($no)).'</th>'."\n";
                                                        echo '<th>'.sprintf(_n('%s Record', '%s Records', $row_usage, 'wp-dbmanager'), number_format_i18n($row_usage)).'</th>'."\n";
                                                                                                     
                                                        echo '</tr>';
                                                ?>
                            
                                
                            </table>
                                  
                               </div>
                           </div>
                       </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapsewp">
                                  WordPress Information

                                </a>
                              </h4>
                            </div>
                           <div id="collapsewp" class="panel-collapse collapse">
                               <div class="panel-body">
                                   <table class="table table-condensed">
                                     <tr class="success">                                        
                                            <th>Setting</th>
                                        <th>Value</th>
                                            			
                                    </tr>     
                                    <tr>
                                        <td>WordPress Version</td>
                                        <td><?php bloginfo('version'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Home URL</td>
                                        <td> <?php echo home_url(); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Site URL</td>
                                        <td><?php echo site_url(); ?></td>
                                    </tr>
                                     <tr>
                                        <td>Upload directory URL</td>
                                        <td><?php $upload_dir = wp_upload_dir(); ?>
                                        <?php echo $upload_dir['baseurl']; ?></td>
                                    </tr>
                            </table>
                                  
                               </div>
                           </div>
                       </div>
                        
                         <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapsewpsetting">
                                  WordPress Settings

                                </a>
                              </h4>
                            </div>
                           <div id="collapsewpsetting" class="panel-collapse collapse">
                               <div class="panel-body">
                                   <table class="table table-condensed">
                                     <tr class="success">                                        
                                          <th>Plugin Name</th>
                                          <th>Version</th>           
                                    </tr> 
                                    <?php   $plugins = get_plugins();                                     
                                    foreach ( $plugins as $plugin ) {
                                        echo "<tr>
                                           <td>".$plugin['Name']."</td>
                                           <td>".$plugin['Version']."</td>                                         
                                        </tr>";      
                                  }?>                                    
                            </table>    
                                   <table class="table table-condensed">
                                     <tr class="success">                                        
                                          <th>Active Theme Name</th>
                                          <th>Version</th>           
                                    </tr> 
                                    <?php   $my_theme = wp_get_theme();                                     
                                   
                                        echo "<tr>
                                           <td>".$my_theme->get('Name')."</td>
                                           <td>".$my_theme->get('Version')."</td>                                         
                                        </tr>";      
                                  ?>                                    
                            </table> 
                        <div class="row">
                         <button class="btn btn-primary" type="button">
                            Drafts Post Count <span class="badge"><?php $count_posts = wp_count_posts();echo $count_posts->draft;?></span>
                          </button>
                          <button class="btn btn-primary" type="button">
                            Publish Post Count <span class="badge"><?php ;echo $count_posts->publish;?></span>
                          </button>
                          <button class="btn btn-primary" type="button">
                            Drafts Pages Count <span class="badge"><?php $count_pages = wp_count_posts('page');echo $count_pages->draft;?></span>
                          </button>
                            <button class="btn btn-primary" type="button">
                            Publish Pages Count <span class="badge"><?php ;echo $count_pages->publish;?></span>
                          </button>
                          <button class="btn btn-primary" type="button">
                            Approved Comments Count <span class="badge"><?php $comments_count = wp_count_comments();echo $comments_count->approved ;?></span>
                          </button>
                        </div>
                               </div>
                           </div>
                       </div>
                        
                     
                    </div>
                    <div class="tab-pane" id="db_advanced">               
                        <h4>A 'WP ALL Backup' Plugin will backup and restore your entire site at will,
                        complete with FTP & S3 integration.</h4>
                        <h2>Pro Features </h2><h4>Get Flat 25% off on WP All Backup Plugin .Use Coupon code 'WPDB25'</h4>
                        <div class="row">
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Complete Backup</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Only Selected file Backup</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> ZipArchive</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> PclZip</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Scheduled backups</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Set backup interval</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Manual backup</div>                        
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Multisite compatible</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Backup entire site</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Include media files</div>                        
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Exclude specific files</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Downloadable log files</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Simple one-click restore</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Set number of backups to store</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Automatically remove oldest backup</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Amazon S3 integration</div>                        
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> FTP and SFTP integration</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Server info quick view</div>
                        <div class="col-md-3"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Support</div>
                       </div>
                        <h3>Key Features</h3>
                        <div class="row">
                        
                            <div class="col-md-3">
                                  <h4>Fast</h4>
                                 <p class="bg-success">                               
                                   This plugin can help you to rapidly create site backup.
                                   Capture your entire site, including media files, or pick and choose specific files and tables.
                                 </p>
                            </div>
                            <div class="col-md-3">
                                <h4>Scheduled Backups</h4>
                               <p class="bg-info">                                   
                                   Create manual backups, as needed, or schedule automated backups.
                                   Trigger monthly, daily or hourly backups that are there when you need them most.
                               </p>
                            </div>
                             <div class="col-md-3">
                                <h4>Essay to use</h4>
                               <p class="bg-info">                                   
                                   Create and store as many backups of your site as you want. 
                                   Get added protection and convenience with one-click restoration.
                                   Delete old backups options.
                               </p>
                            </div>
                            <div class="col-md-3">
                                  <h4>Integration</h4>
                                 <p class="bg-success">                               
                                  Tie directly into other destination.
                                Save directly to your favorite cloud services including  Amazon S3, 
                                 by FTP/SFTP for added security.                                   
                                 </p>
                            </div>
                        </div>
                       

                        <a href="http://www.wpseeds.com/product/wp-all-backup/" target="_blank"><h4><span class="label label-success">Get Pro 'WP All Backup' Plugin</span></h4></a>
</div>
                    <div class="tab-pane" id="db_setting">   
                        <div class="panel panel-default">
                        <div class="panel-body">
                            <?php
                            $wp_local_db_backup_count=get_option('wp_local_db_backup_count');     
                            $wp_db_log=get_option('wp_db_log');
                            if($wp_db_log==1){
                                $checked="checked";
                            }else{
                                $checked="";
                            }
                            ?>
                            <form action="" method="post">
                                      <div class="input-group">
                                                                  <span class="input-group-addon" id="sizing-addon2">Minimum Local Backups</span>
                                                                  <input type="number" name="wp_local_db_backup_count" value="<?php echo $wp_local_db_backup_count?>" class="form-control" placeholder="Minimum Local Backups" aria-describedby="sizing-addon2">

                                      </div>
                                      <div class="alert alert-default" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> The minimum number of Local Database Backups that should be kept, regardless of their size.</br>Leave blank for keep unlimited database backups.</div>

                                       <div class="input-group">
                                              <input type="checkbox" <?php echo $checked ?> name="wp_db_log"> Enable Log.
                                          </div>
                                      <hr>
                                                                  <input class="btn button-primary" type="submit" name="wpsetting" value="Save">
                                 </form>
                        </div>
                      </div>



                      </div>
 <?php
		 echo '<div class="tab-pane" id="db_destination">';
		 ?>
		 <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseI">
          <h2>FTP/sFTP </h2>
       
        </a>
      </h4>
    </div>
    <div id="collapseI" class="panel-collapse collapse in">
      <div class="panel-body">
      <p>FTP/sFTP Destination Define an FTP destination connection. You can define destination which use FTP.</p>
      <?php	 include plugin_dir_path(__FILE__).'Destination/FTP/ftp-form.php';?>
	</div>		
        </div>
    </div>
     <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseII">
          <h2>Email Notification</h2>
       
        </a>
      </h4>
    </div>
    <div id="collapseII" class="panel-collapse collapse in">
      <div class="panel-body">
      
     <?php echo '<form name="wp-email_form" method="post" action="" >';
		 
			$wp_db_backup_email_id="";
		        $wp_db_backup_email_id=get_option('wp_db_backup_email_id');
		        $wp_db_backup_email_attachment="";
		        $wp_db_backup_email_attachment=get_option('wp_db_backup_email_attachment');
		        echo '<p>';
		        echo '<span class="glyphicon glyphicon-envelope"></span> Send Email Notification</br></p>';
			echo '<p>Email Id : ';
			echo '<input type="text" name="wp_db_backup_email_id" value="'.$wp_db_backup_email_id.'" placeholder="Your Email Id">';
			echo '<p>Leave blank if you don\'t want use this feature</p>';
			echo '<p>Attach backup file : ';
			$selected_option=get_option( 'wp_db_backup_email_attachment' );
					
					if($selected_option=="yes") $selected_yes="selected=\"selected\"";
					else
					$selected_yes="";
					if($selected_option=="no") $selected_no="selected=\"selected\"";
					else
					$selected_no="";
                                 	echo '<select id="lead-theme" name="wp_db_backup_email_attachment">';
								echo '<option value="none">Select</option>';
								
									echo '<option  value="yes"'.$selected_yes.'>Yes</option>';
									echo '<option  value="no" '.$selected_no.'>No</option>';
									
								
							echo '</select></p>';

					echo '<p>If you want attache backup file to email then select "yes" (File attached only when backup file size <=25MB)</p>';
					
			echo '</p>';
			echo '<p class="submit">';
				echo '<input type="submit" name="Submit" class="button-primary" value="Save Settings" />';
			echo '</p>';
			echo '</form>';?>
	</div>		
        </div>
    </div>
	
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseIII">
          <h2>Dropbox </h2>
       
        </a>
      </h4>
    </div>
    <div id="collapseIII" class="panel-collapse collapse in">
      <div class="panel-body">
      
     <?php include plugin_dir_path(__FILE__).'Destination/Dropbox/dropboxupload.php';?>
	</div>		
        </div>
    </div>
		
		<?php
		echo '</div>';
		?>
	

		
 </div> 
	
 <div class="panel panel-footer">Thank you for using the <a href="http://walkeprashant.wordpress.com/wp-database-backup/" target="_blank">WP Database Backup</a>.</div>


                 <?php
                 
		
                                          
	}
	
	function wp_db_backup_format_bytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 
function wp_db_backup_create_mysql_backup() {
	global $wpdb;
	/*BEGIN : Prevent saving backup plugin settings in the database dump*/
	$options_backup = get_option('wp_db_backup_backups');
	$settings_backup = get_option('wp_db_backup_options');
	delete_option('wp_db_backup_backups');
	delete_option('wp_db_backup_options');
	/*END : Prevent saving backup plugin settings in the database dump*/
	
	$tables = $wpdb->get_col('SHOW TABLES');
	$output = '';
	foreach($tables as $table) {
		$result = $wpdb->get_results("SELECT * FROM {$table}", ARRAY_N);
		$row2 = $wpdb->get_row('SHOW CREATE TABLE '.$table, ARRAY_N); 
		$output .= "\n\n".$row2[1].";\n\n";
		for($i = 0; $i < count($result); $i++) {
			$row = $result[$i];
			$output .= 'INSERT INTO '.$table.' VALUES(';
			for($j=0; $j<count($result[0]); $j++) {
				$row[$j] = mysql_real_escape_string($row[$j]);
				$output .= (isset($row[$j])) ? '"'.$row[$j].'"'	: '""'; 
				if ($j < (count($result[0])-1)) {
					$output .= ',';
				}
			}
			$output .= ");\n";
		}
		$output .= "\n";
	}
	$wpdb->flush();
	/*BEGIN : Prevent saving backup plugin settings in the database dump*/
	add_option('wp_db_backup_backups', $options_backup);
	add_option('wp_db_backup_options', $settings_backup);
	/*END : Prevent saving backup plugin settings in the database dump*/
	return $output;
}



function wp_db_backup_create_archive() {	
	/*Begin : Setup Upload Directory, Secure it and generate a random file name*/
	
	$source_directory = $this->wp_db_backup_wp_config_path();
	
	$path_info = wp_upload_dir();
	
	wp_mkdir_p($path_info['basedir'].'/db-backup');
	fclose(fopen($path_info['basedir'].'/db-backup/index.php', 'w'));
        //added htaccess file 08-05-2015 for prevent directory listing
       // fclose(fopen($path_info['basedir'].'/db-backup/.htaccess', $htassesText));
          // $f = fopen($path_info['basedir'].'/db-backup/.htaccess', "w");
          //  fwrite($f, "IndexIgnore *");
          //  fclose($f);         
	/*Begin : Generate SQL DUMP and save to file database.sql*/
        $WPDBFileName=Date("Y_m_d").'_'.Time("H:M:S").rand(9, 9999).'_database';       
        $SQLfilename=$WPDBFileName.'.sql';
	$filename=$WPDBFileName.'.zip';
	$handle = fopen($path_info['basedir'].'/db-backup/'.$SQLfilename,'w+');
	fwrite($handle, $this->wp_db_backup_create_mysql_backup());
	fclose($handle);
	 
	/*End : Generate SQL DUMP and save to file database.sql*/
	$upload_path = array(
		'filename' => ($filename),
		'dir' => ($path_info['basedir'].'/db-backup/'.$filename),
		'url' => ($path_info['baseurl'].'/db-backup/'.$filename),
		'size' => 0
	);
        if ( class_exists( 'ZipArchive' ) )
		{
			$zip = new ZipArchive;
			$zip->open($path_info['basedir'].'/db-backup/'.$WPDBFileName.".zip", ZipArchive::CREATE);
                        $zip->addFile($path_info['basedir'].'/db-backup/'.$SQLfilename,$SQLfilename);                        
			$zip->close();
                    //    @unlink($path_info['basedir']."/db-backup/".$SQLfilename.".sql");
		
		}
		else
		{
			error_log("Class ZipArchive Not Present");
			
		}
                   
        $logMessage="Database File Name :".$filename; 
	$upload_path['size']=filesize($upload_path['dir']);
        $upload_path['sqlfile']=$path_info['basedir'].'/db-backup/'.$SQLfilename;
        $wp_db_log=get_option('wp_db_log');
      if($wp_db_log==1){
        $upload_path['log']=$logMessage;
      }
         $options = get_option('wp_db_backup_backups');
	$newoptions = array();
	$number_of_existing_backups=count($options);
         error_log("number_of_existing_backups");
         error_log($number_of_existing_backups);        
	$number_of_backups_from_user=get_option('wp_local_db_backup_count');
         error_log("number_of_backups_from_user");
         error_log($number_of_backups_from_user);   
         if(!empty($number_of_backups_from_user)){
        if(!($number_of_existing_backups < $number_of_backups_from_user))
	{
          $diff=$number_of_existing_backups-$number_of_backups_from_user;
		for ($i = 0; $i <= $diff; $i++)
		{
			$index=$i;
                        error_log($options[$index]['dir']);     
			@unlink($options[$index]['dir']);
                        $sqlFile=  explode('.', $options[$index]['dir']);                       
                          @unlink($sqlFile[0].'.sql');
		}
                for($i=($diff+1);$i <$number_of_existing_backups; $i++ )
		{
                    error_log($i);    
                    $index=$i;
                   
			$newoptions[] = $options[$index];
                         
		}               
                                
          	update_option('wp_db_backup_backups', $newoptions);
        }
         }
         @unlink($path_info['basedir'].'/db-backup/'.$SQLfilename);
	return $upload_path;
	
}


function wp_db_backup_wp_config_path() {
    $base = dirname(__FILE__);
    $path = false;
    if (@file_exists(dirname(dirname($base))."/wp-config.php")) {
        $path = dirname(dirname($base));
    } else {
		if (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php")) {
			$path = dirname(dirname(dirname($base)));
		} else {
			$path = false;
		}
	}
    if ($path != false) {
        $path = str_replace("\\", "/", $path);
    }
    return $path;
}
function wp_db_backup_event_process() {
	
	$details = $this->wp_db_backup_create_archive();
        $options = get_option('wp_db_backup_backups');

	if(!$options) {
		$options = array();
	}
        $wp_db_log=get_option('wp_db_log');
      if($wp_db_log==1){
	$logMessage=$details['log'];
      }else{
          $logMessage="";
      }
	//FTP
	include plugin_dir_path(__FILE__).'Destination/FTP/preflight.php';
	$filename = $details['filename'];
	$filename = $details['filename'];
	include plugin_dir_path(__FILE__).'Destination/FTP/sendaway.php';
	
	//Dropbox
	$dropb_autho=get_option('dropb_autho');
	if($dropb_autho=="yes")
	{
	include plugin_dir_path(__FILE__).'Destination/Dropbox/dropboxupload.php';
	
	
	$wp_upload_dir = wp_upload_dir();
	
	$wp_upload_dir['basedir'] = str_replace('\\', '/', $wp_upload_dir['basedir']);
	
	$localfile = trailingslashit($wp_upload_dir['basedir'].'/db-backup/').$filename;
	

	$dropbox->UploadFile($localfile, $filename);
	$logMessage.=" Upload Database Backup on Dropbox";
	 }
        
        //Email
	if(get_option('wp_db_backup_email_id'))
	{
	 $to=get_option('wp_db_backup_email_id');
	 $subject="Database Backup Created Successfully";
	 $filename=$details['filename'];
	 $filesze=$details['size'];
         $site_url= site_url(); 	 
         $message='<img src="http://www.wpseeds.com/wp-content/uploads/2015/07/wordpress-wp-database-backup.png" alt="WP Database Backup" width="750" height="200"/>
            <p>Dear WP Database Backup User,<br/></p>            
            <p>Database Backup Created Successfully.</p>
            <p>File Name :'.$filename.'</p>
            <p>File Size :'.$this->wp_db_backup_format_bytes($filesze).'</p>
            <p>You\'re receiving this email because you have active Email Notification on your site('.$site_url.').</p>
            <p>Best regards,<br/><br/>
            <a title="WPSeeds-WprdPress Products" href="http://www.wpseeds.com/shop/" target="_blank"><img src="http://www.wpseeds.com/wp-content/uploads/2015/06/wpseedslogo.png" alt="WPSeeds-WprdPress Products" /></a>
            <br/><a title="Pro-WP All Backup Plugin" href="www.wpseeds.com/product/wp-all-backup/" target="_blank">Get Pro WP All Backup Plugin</a></p>
            <p>Tech Support: http://www.wpseeds.com/support/</p>
            <p>Documentation: http://www.wpseeds.com/wp-database-backup/</p>
            <p>Pro Features: http://www.wpseeds.com/wp-all-backup/</p>';
         
	$headers = array('Content-Type: text/html; charset=UTF-8');
	  $wp_db_backup_email_attachment_file=get_option('wp_db_backup_email_attachment');
	  if($wp_db_backup_email_attachment_file=="yes" && $details['size']<=209700000)
	  {
	  $wp_upload_dir = wp_upload_dir();
	  $wp_upload_dir['basedir'] = str_replace('\\', '/', $wp_upload_dir['basedir']);
	  $filename = $details['filename'];
          $attachments = trailingslashit($wp_upload_dir['basedir'].'/db-backup'). $filename;
          $logMessageAttachment=" with attached backup file.";
	  }
	  else
	 $attachments="";
	 wp_mail( $to, $subject, $message, $headers, $attachments );
         $logMessage.=" Send Backup Mail to:".$to;
         $logMessage.=$logMessageAttachment;
	}
        $options[] = array(
		'date' => mktime(),
		'filename' => $details['filename'],
		'url' => $details['url'],
		'dir' => $details['dir'],
                'log' => $logMessage,
                'sqlfile' => $details['sqlfile'],
		'size' => $details['size']
	);
	update_option('wp_db_backup_backups', $options);
	
}
public function wp_db_backup_cron_schedules($schedules) {
	$schedules['weekly'] = array(
		'interval' => 604800,
		'display' => 'Once Weekly'
	);
	$schedules['monthly'] = array(
		'interval' => 2635200,
		'display' => 'Once a month'
	);
	return $schedules;
}
public function wp_db_backup_scheduler_activation() {
	$options= get_option('wp_db_backup_options');
	if ((!wp_next_scheduled('wp_db_backup_event')) && (@$options['enable_autobackups'])) {
		wp_schedule_event(time(), $options['autobackup_frequency'], 'wp_db_backup_event');
	}
}
function wp_backup_get_config_data($key) {
        $filepath=get_home_path().'/wp-config.php';
        $config_file = @file_get_contents("$filepath", true);
	switch($key) {
		case 'DB_NAME':
			preg_match("/'DB_NAME',\s*'(.*)?'/", $config_file, $matches);
			break;
		case 'DB_USER':
			preg_match("/'DB_USER',\s*'(.*)?'/", $config_file, $matches);
			break;
		case 'DB_PASSWORD':
			preg_match("/'DB_PASSWORD',\s*'(.*)?'/", $config_file, $matches);
			break;
		case 'DB_HOST':
			preg_match("/'DB_HOST',\s*'(.*)?'/", $config_file, $matches);
			break;
	}
	return $matches[1];
}

function wp_backup_get_config_db_name() {
	   $filepath=get_home_path().'/wp-config.php';
       	$config_file = @file_get_contents("$filepath", true);
	preg_match("/'DB_NAME',\s*'(.*)?'/", $config_file, $matches);
	return $matches[1];
}

	/**
	 * Enqueue scripts and style
	 */
	public function admin_scripts_style() {
		  
         if (isset($_GET['page'])) { 
            if ($_GET['page'] == "wp-database-backup") {
            
           wp_enqueue_script('jquery');   
           
           wp_enqueue_script('bootstrapjs',WPDB_PLUGIN_URL."/assets/js/bootstrap.min.js" );
           wp_enqueue_script('bootstrapjs');
          
           wp_enqueue_style('bootstrapcss',WPDB_PLUGIN_URL."/assets/css/bootstrap.min.css" );
           wp_enqueue_style('bootstrapcss');
           
           wp_enqueue_script('dataTables',WPDB_PLUGIN_URL."/assets/js/jquery.dataTables.js",array( 'jquery' ));
           wp_enqueue_script('dataTables');
          
           wp_enqueue_style('dataTablescss',WPDB_PLUGIN_URL."/assets/css/jquery.dataTables.css" );
           wp_enqueue_style('dataTablescss');
           
           wp_enqueue_style('wpdbcss',WPDB_PLUGIN_URL."/assets/css/wpdb_admin.css" );
           wp_enqueue_style('wpdbcss');
                       
                   
            }
        }
	}

	
}

return new WPDB_Admin();