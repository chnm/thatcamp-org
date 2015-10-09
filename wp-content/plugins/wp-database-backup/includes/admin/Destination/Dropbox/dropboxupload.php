<?php
include plugin_dir_path(__FILE__).'DropboxClient.php';
$dropbox = new DropboxClient(array(
'app_key' => "cv3o964lig1qrga",
'app_secret' => "7g05tjesk5fgqjk",
'app_full_access' => false,
),'en');

handle_dropbox_auth($dropbox); // see below
// if there is no upload, show the form


// store_token, load_token, delete_token are SAMPLE functions! please replace with your own!



function store_token($token, $name)
{

file_put_contents(plugin_dir_path(__FILE__)."tokens/$name.token", serialize($token));
}


function load_token($name)
{
if(!file_exists(plugin_dir_path(__FILE__)."tokens/$name.token")) return null;
return @unserialize(@file_get_contents(plugin_dir_path(__FILE__)."tokens/$name.token"));
}

function delete_token($name)
{
@unlink(plugin_dir_path(__FILE__)."tokens/$name.token");
}




  
 function handle_dropbox_auth($dropbox)
{
// first try to load existing access token
$access_token = load_token("access");

if(!empty($access_token)) {

$dropbox->SetAccessToken($access_token);
}
elseif(!empty($_GET['auth_callback'])) // are we coming from dropbox's oauth page?
{

// then load our previosly created request token
$request_token = load_token($_GET['oauth_token']);


if(empty($request_token)) die('Request token not found!');
// get & store access token, the request token is not needed anymore
$access_token = $dropbox->GetAccessToken($request_token);
store_token($access_token, "access");
delete_token($_GET['oauth_token']);
}
// checks if access token is required


if($dropbox->IsAuthorized())
{
$dropb_autho="yes";
update_option('dropb_autho', $dropb_autho );
 echo '<h3>Dropbox Account Details</h3>';
$account_info = $dropbox->GetAccountInfo();
 $used = round(($account_info->quota_info->quota - ($account_info->quota_info->normal + $account_info->quota_info->shared)) / 1073741824, 1);
        $quota = round($account_info->quota_info->quota / 1073741824, 1);
        echo $account_info->display_name . ', ' .'you have'. ' ' .$used .'GB' .'of'. ' ' . $quota . 'GB (' . round(($used / $quota) * 100, 0) .'%) ' .'free';
    
     echo '</br><p>Unlink Account for local backups</p></br>';
     echo '<td><a href="'.get_bloginfo('url').'/wp-admin/tools.php?page=wp-database-backup&action=unlink" class="button-primary">Unlink Account<a/>';
      
             
   
      
}

else
{
$dropb_autho="no";
update_option('dropb_autho', $dropb_autho );
// redirect user to dropbox oauth page
$return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?page=wp-database-backup&auth_callback=1";
$auth_url = $dropbox->BuildAuthorizeUrl($return_url);
$request_token = $dropbox->GetRequestToken();
store_token($request_token, $request_token['t']);

    ?>
    <style>
    #adminmenuwrap {
        padding-bottom: 838px;
    }
</style>
    <h3>Dropbox</h3>
    <p>Define an Dropbox destination connection.</p>
    <p>In order to use Dropbox destination you will need to authorized it with your Dropbox account</p>
    <p>Please click the authorize button below and follow the instructions inside the pop up window</p>
    <p>For local backup leave the setting as it is</p>
          <p>
    <form action="" method="get">
        <a href="<?php echo $auth_url?>"><input type="button" name="authorize" id="authorize" value="Authorize"
               class="button-primary" /></a><br/>
         </form>
    </p>
    <?php
    
    die();
//die("Authentication required".$auth_url);
}
}
 
?>