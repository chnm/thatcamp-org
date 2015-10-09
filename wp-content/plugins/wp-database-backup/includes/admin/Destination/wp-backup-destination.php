<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}?>
<h2>Destination</h2>
		
<!-- Nav tabs -->
<ul class="nav nav-tabs" style="width: 640px;">
  <li><a href="#FTP" data-toggle="tab">FTP</a></li>
  <li><a href="#Dropbox" data-toggle="tab">Dropbox</a></li>
 
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="FTP">
       <h2>FTP/sFTP </h2>
       <p>FTP/sFTP Destination Define an FTP destination connection. You can define destination which use FTP.</p>
       <?php 
         // call FTP Details form
        include WPDB_PLUGIN_URL.'/Destination/FTP/ftp-form.php';
       ?>
  
  </div>
  
  <div class="tab-pane" id="Dropbox">
      <?php include plugin_dir_path(__FILE__).'Dropbox/dropboxupload.php';?>
 </div>
 
</div>