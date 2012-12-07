<?php 
/*
Plugin Name: User Avatar
Plugin URI: http://wordpress.org/extend/plugins/user-avatar/
Description: Allows users to associate photos with their accounts by accessing their "Your Profile" page that default as Gravatar or WordPress Default image (from Discussion Page). 
Version: 1.4.11
Author: Enej Bajgoric / Gagan Sandhu / CTLT DEV


GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action('init', 'user_avatar_core_set_avatar_constants', 8 );
add_action('show_user_profile', 'user_avatar_form');
add_action('edit_user_profile', 'user_avatar_form');
add_action('wp_ajax_user_avatar_add_photo', 'user_avatar_add_photo');
add_action('user_avatar_iframe_head','user_avatar_init');


add_action('admin_print_styles-user-edit.php', 'user_avatar_admin_print_styles');
add_action('admin_print_styles-profile.php', 'user_avatar_admin_print_styles');
function user_avatar_admin_print_styles() {
	global $hook_suffix;
	wp_enqueue_script("thickbox");
	wp_enqueue_style("thickbox");
	wp_enqueue_style('user-avatar', plugins_url('/user-avatar/css/user-avatar.css'), 'css');
}


/**
 * user_avatar_init function.
 * Description: Initializing user avatar style.
 * @access public
 * @return void
 */
function user_avatar_init(){
	
	wp_enqueue_style( 'global' );
	wp_enqueue_style( 'wp-admin' );
	wp_enqueue_style( 'colors' );
	wp_enqueue_style( 'ie' );
	wp_enqueue_style('user-avatar', plugins_url('/user-avatar/css/user-avatar.css'), 'css');
	wp_enqueue_style('imgareaselect');
	wp_enqueue_script('imgareaselect');
	do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');

}
/**
 * user_avatar_core_set_avatar_constants function.
 * Description: Establishing restraints on sizes of files and dimensions of images.
 * Sets the default constants 
 * @access public
 * @return void
 */
function user_avatar_core_set_avatar_constants() {
	
	global $bp;

	if ( !defined( 'USER_AVATAR_UPLOAD_PATH' ) )
		define( 'USER_AVATAR_UPLOAD_PATH', user_avatar_core_avatar_upload_path() );

	if ( !defined( 'USER_AVATAR_URL' ) )
		define( 'USER_AVATAR_URL', user_avatar_core_avatar_url() );

	if ( !defined( 'USER_AVATAR_THUMB_WIDTH' ) )
		define( 'USER_AVATAR_THUMB_WIDTH', 50 );

	if ( !defined( 'USER_AVATAR_THUMB_HEIGHT' ) )
		define( 'USER_AVATAR_THUMB_HEIGHT', 50 );

	if ( !defined( 'USER_AVATAR_FULL_WIDTH' ) )
		define( 'USER_AVATAR_FULL_WIDTH', 150 );

	if ( !defined( 'USER_AVATAR_FULL_HEIGHT' ) )
		define( 'USER_AVATAR_FULL_HEIGHT', 150 );

	if ( !defined( 'USER_AVATAR_ORIGINAL_MAX_FILESIZE' ) ) {
		if ( !get_site_option( 'fileupload_maxk', 1500 ) )
			define( 'USER_AVATAR_ORIGINAL_MAX_FILESIZE', 5120000 ); /* 5mb */
		else
			define( 'USER_AVATAR_ORIGINAL_MAX_FILESIZE', get_site_option( 'fileupload_maxk', 1500 ) * 1024 );
	}

	if ( !defined( 'USER_AVATAR_DEFAULT' ) )
		define( 'USER_AVATAR_DEFAULT', plugins_url('/user-avatar/images/mystery-man.jpg') );

	if ( !defined( 'USER_AVATAR_DEFAULT_THUMB' ) )
		define( 'USER_AVATAR_DEFAULT_THUMB', plugins_url('/user-avatar/images/mystery-man-50.jpg') );
		
		
	// set the language 
	load_plugin_textdomain( 'user-avatar', false , basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * user_avatar_core_avatar_upload_path function.
 * Description: Establishing upload path/area where images that are uploaded will be stored.
 * @access public
 * @return void
 */
function user_avatar_core_avatar_upload_path()
{
	if( !file_exists(WP_CONTENT_DIR."/uploads/avatars/") )
		mkdir(WP_CONTENT_DIR."/uploads/avatars/", 0777 ,true);
	
	return WP_CONTENT_DIR."/uploads/avatars/";
}

/**
 * user_avatar_core_avatar_url function.
 * Description: Establishing the path of the core content avatar area.
 * @access public
 * @return void
 */
function user_avatar_core_avatar_url()
{	
	return WP_CONTENT_URL."/uploads/avatars/";
}

/**
 * user_avatar_add_photo function.
 * The content inside the iframe 
 * Description: Creating panels for the different steps users take to upload a file and checking their uploads.
 * @access public
 * @return void
 */
function user_avatar_add_photo() {
	global $current_user;
	
	if(($_GET['uid'] == $current_user->ID || current_user_can('edit_users')) &&  is_numeric($_GET['uid'])) 
	{
		$uid = $_GET['uid'];
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Uploads'); ?> &#8212; <?php _e('WordPress'); ?></title>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var userSettings = {
		'url': '<?php echo SITECOOKIEPATH; ?>',
		'uid': '<?php if ( ! isset($current_user) ) $current_user = wp_get_current_user(); echo $current_user->ID; ?>',
		'time':'<?php echo time() ?>'
	},
	ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>',
	pagenow = '<?php echo $current_screen->id; ?>',
	typenow = '<?php if ( isset($current_screen->post_type) ) echo $current_screen->post_type; ?>',
	adminpage = '<?php echo $admin_body_class; ?>',
	thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>;
//]]>
</script>
<?php

	
	do_action('user_avatar_iframe_head');
	
	
?>

</head>
<body>
<?php
	switch($_GET['step'])
	{
		case 1:
			user_avatar_add_photo_step1($uid);
		break;
		
		case 2:
			user_avatar_add_photo_step2($uid);
		break;
		
		case 3:
			user_avatar_add_photo_step3($uid);
		break;
	}
		
	do_action('admin_print_footer_scripts');
?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
<?php
	}else {
		wp_die(__("You are not allowed to do that.",'user-avatar'));
	}
	die();
}

/**
 * user_avatar_add_photo_step1 function.
 * The First Step in the process 
 * Description: Displays the users photo and they can choose to upload another if they please.
 * @access public
 * @param mixed $uid
 * @return void
 */
function user_avatar_add_photo_step1($uid)
{
	?>
	<p id="step1-image" >
	<?php
	echo user_avatar_get_avatar( $uid , 150);
	?>
	</p>
	<div id="user-avatar-step1">
	<form enctype="multipart/form-data" id="uploadForm" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>?action=user_avatar_add_photo&step=2&uid=<?php echo $uid; ?>" >
		<label for="upload"><?php _e('Choose an image from your computer:','user-avatar'); ?></label><br /><input type="file" id="upload" name="uploadedfile" />

		<?php wp_nonce_field('user-avatar') ?>
		<p class="submit"><input type="submit" value="<?php esc_attr_e('Upload'); ?>" /></p>
	</form>
	</div>
	
	<?php
}

/**
 * user_avatar_add_photo_step2 function.
 * The Second Step in the process 
 * Description: Takes the uploaded photo and saves it to database.
 * @access public
 * @param mixed $uid
 * @return void
 */
function user_avatar_add_photo_step2($uid)
{
	
	
		if (!(($_FILES["uploadedfile"]["type"] == "image/gif") || ($_FILES["uploadedfile"]["type"] == "image/jpeg") || ($_FILES["uploadedfile"]["type"] == "image/png") || ($_FILES["uploadedfile"]["type"] == "image/pjpeg") || ($_FILES["uploadedfile"]["type"] == "image/x-png"))){
			echo "<div class='error'><p>".__("Please upload an image file (.jpeg, .gif, .png).",'user-avatar')."</p></div>";
			user_avatar_add_photo_step1($uid);
			die();
		}
		$overrides = array('test_form' => false);
		$file = wp_handle_upload($_FILES['uploadedfile'], $overrides);

		if ( isset($file['error']) ){
			die( $file['error'] );
		}
		
		$url = $file['url'];
		$type = $file['type'];
		$file = $file['file'];
		$filename = basename($file);
		
		set_transient( 'avatar_file_'.$uid, $file, 60 * 60 * 5 );
		// Construct the object array
		$object = array(
		'post_title' => $filename,
		'post_content' => $url,
		'post_mime_type' => $type,
		'guid' => $url);

		// Save the data
		list($width, $height, $type, $attr) = getimagesize( $file );
		
		if ( $width > 420 ) {
			$oitar = $width / 420;
			$image = wp_crop_image($file, 0, 0, $width, $height, 420, $height / $oitar, false, str_replace(basename($file), 'midsize-'.basename($file), $file));
			

			$url = str_replace(basename($url), basename($image), $url);
			$width = $width / $oitar;
			$height = $height / $oitar;
		} else {
			$oitar = 1;
		}
		?>
		<form id="iframe-crop-form" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>?action=user_avatar_add_photo&step=3&uid=<?php echo esc_attr($uid); ?>">
		
		<h4><?php _e('Choose the part of the image you want to use as your profile image.','user-avatar'); ?> <input type="submit" class="button" id="user-avatar-crop-button" value="<?php esc_attr_e('Crop Image','user-avatar'); ?>" /></h4>
		
		<div id="testWrap">
		<img src="<?php echo $url; ?>" id="upload" width="<?php echo esc_attr($width); ?>" height="<?php echo esc_attr($height); ?>" />
		</div>
		<div id="user-avatar-preview">
		<h4>Preview</h4>
		<div id="preview" style="width: <?php echo USER_AVATAR_FULL_WIDTH; ?>px; height: <?php echo USER_AVATAR_FULL_HEIGHT; ?>px; overflow: hidden;">
		<img src="<?php echo esc_url_raw($url); ?>" width="<?php echo esc_attr($width); ?>" height="<?php echo $height; ?>">
		</div>
		<p class="submit" >
		<input type="hidden" name="x1" id="x1" value="0" />
		<input type="hidden" name="y1" id="y1" value="0" />
		<input type="hidden" name="x2" id="x2" />
		<input type="hidden" name="y2" id="y2" />
		<input type="hidden" name="width" id="width" value="<?php echo esc_attr($width) ?>" />
		<input type="hidden" name="height" id="height" value="<?php echo esc_attr($height) ?>" />
		
		<input type="hidden" name="oitar" id="oitar" value="<?php echo esc_attr($oitar); ?>" />
		<?php wp_nonce_field('user-avatar'); ?>
		</p>
		</div>
		</form>
		
		<script type="text/javascript">

	function onEndCrop( coords ) {
		jQuery( '#x1' ).val(coords.x);
		jQuery( '#y1' ).val(coords.y);
		jQuery( '#width' ).val(coords.w);
		jQuery( '#height' ).val(coords.h);
	}

	jQuery(document).ready(function() {
		var xinit = <?php echo USER_AVATAR_FULL_WIDTH; ?>;
		var yinit = <?php echo USER_AVATAR_FULL_HEIGHT; ?>;
		var ratio = xinit / yinit;
		var ximg = jQuery('img#upload').width();
		var yimg = jQuery('img#upload').height();

		if ( yimg < yinit || ximg < xinit ) {
			if ( ximg / yimg > ratio ) {
				yinit = yimg;
				xinit = yinit * ratio;
			} else {
				xinit = ximg;
				yinit = xinit / ratio;
			}
		}

		jQuery('img#upload').imgAreaSelect({
			handles: true,
			keys: true,
			aspectRatio: xinit + ':' + yinit,
			show: true,
			x1: 0,
			y1: 0,
			x2: xinit,
			y2: yinit,
			//maxHeight: <?php echo USER_AVATAR_FULL_HEIGHT; ?>,
			//maxWidth: <?php echo USER_AVATAR_FULL_WIDTH; ?>,
			onInit: function () {
				jQuery('#width').val(xinit);
				jQuery('#height').val(yinit);
			},
			onSelectChange: function(img, c) {
				jQuery('#x1').val(c.x1);
				jQuery('#y1').val(c.y1);
				jQuery('#width').val(c.width);
				jQuery('#height').val(c.height);
				
				
				
				if (!c.width || !c.height)
        			return;
    
			    var scaleX = <?php echo USER_AVATAR_FULL_WIDTH; ?> / c.width;
			    var scaleY = <?php echo USER_AVATAR_FULL_HEIGHT; ?> / c.height;
				
			    jQuery('#preview img').css({
			        width: Math.round(scaleX * <?php echo $width; ?>),
			        height: Math.round(scaleY * <?php echo $height; ?>),
			        marginLeft: -Math.round(scaleX * c.x1),
			        marginTop: -Math.round(scaleY * c.y1)
			    });

			}
		});
	});
</script>
		<?php
}
/**
 * user_avatar_add_photo_step3 function.
 * The Third Step in the Process
 * Description: Deletes previous uploaded picture and creates a new cropped image in its place. 
 * @access public
 * @param mixed $uid
 * @return void
 */
function user_avatar_add_photo_step3($uid)
{
	
	
	if ( $_POST['oitar'] > 1 ) {
		$_POST['x1'] = $_POST['x1'] * $_POST['oitar'];
		$_POST['y1'] = $_POST['y1'] * $_POST['oitar'];
		$_POST['width'] = $_POST['width'] * $_POST['oitar'];
		$_POST['height'] = $_POST['height'] * $_POST['oitar'];
	}
	
	$original_file = get_transient( 'avatar_file_'.$uid );
					 delete_transient('avatar_file_'.$uid );
	if( !file_exists($original_file) ) {
		echo "<div class='error'><p>". __('Sorry, No file available','user-avatar')."</p></div>";
		return true;
	}
		
	$cropped_full = USER_AVATAR_UPLOAD_PATH."{$uid}/".time()."-bpfull.jpg";
	$cropped_thumb = USER_AVATAR_UPLOAD_PATH."{$uid}/".time()."-bpthumb.jpg";
	
	// delete the previous files
	user_avatar_delete_files($uid);
	
	if(!file_exists(USER_AVATAR_UPLOAD_PATH."{$uid}/"))
		mkdir(USER_AVATAR_UPLOAD_PATH."{$uid}/");
	
	// update the files 
	$cropped_full = wp_crop_image( $original_file, $_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height'], USER_AVATAR_FULL_WIDTH, USER_AVATAR_FULL_HEIGHT, false, $cropped_full );
	
	$cropped_thumb = wp_crop_image( $original_file, $_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height'], USER_AVATAR_THUMB_WIDTH, USER_AVATAR_THUMB_HEIGHT, false, $cropped_thumb );
	
	/* Remove the original */
	@unlink( $original_file );
		
	if ( is_wp_error( $cropped_full ) )
		wp_die( __( 'Image could not be processed.  Please go back and try again.' ), __( 'Image Processing Error' ) );		
	?>
	<script type="text/javascript">
		self.parent.user_avatar_refresh_image('<?php echo user_avatar_get_avatar($uid, 150); ?>');
		self.parent.add_remove_avatar_link();
	</script>
	<div id="user-avatar-step3">
		<h3><?php _e("Here's your new profile picture...",'user-avatar'); ?></h3>
		<span style="float:left;">
		<?php
		echo user_avatar_get_avatar( $uid, 150);
		?>
		</span>
		<a id="user-avatar-step3-close" class="button" onclick="self.parent.tb_remove();" ><?php _e('Close','user-avatar'); ?></a>
	</div>
<?php	
}	
/**
 * user_avatar_delete_files function.
 * Description: Deletes the avatar files based on the user id.
 * @access public
 * @param mixed $uid
 * @return void
 */
function user_avatar_delete_files($uid)
{
	$avatar_folder_dir = USER_AVATAR_UPLOAD_PATH."{$uid}/";
	if ( !file_exists( $avatar_folder_dir ) )
		return false;

	if ( is_dir( $avatar_folder_dir ) && $av_dir = opendir( $avatar_folder_dir ) ) {
		while ( false !== ( $avatar_file = readdir($av_dir) ) ) {
				@unlink( $avatar_folder_dir . '/' . $avatar_file );
		}
		closedir($av_dir);
	}

	@rmdir( $avatar_folder_dir );

}

/**
 * Based on the 
 * user_avatar_core_fetch_avatar_filter() 1.2.5 BP
 *
 * Description: Attempts to filter get_avatar function and let Word/BuddyPress have a go at  
 * 				finding an avatar that may have been uploaded locally.
 *
 * @global array $authordata
 * @param string $avatar The result of get_avatar from before-filter
 * @param int|string|object $user A user ID, email address, or comment object
 * @param int $size Size of the avatar image (thumb/full)
 * @param string $default URL to a default image to use if no avatar is available
 * @param string $alt Alternate text to use in image tag. Defaults to blank
 * @return <type>
 */
function user_avatar_fetch_avatar_filter( $avatar, $user, $size, $default, $alt ) {
	global $pagenow;
	
	//If user is on discussion page, return $avatar 
    if($pagenow == "options-discussion.php")
    	return $avatar;
    	
	// If passed an object, assume $user->user_id
	if ( is_object( $user ) )
		$id = $user->user_id;

	// If passed a number, assume it was a $user_id
	else if ( is_numeric( $user ) )
		$id = $user;

	// If passed a string and that string returns a user, get the $id
	else if ( is_string( $user ) && ( $user_by_email = get_user_by_email( $user ) ) )
		$id = $user_by_email->ID;

	// If somehow $id hasn't been assigned, return the result of get_avatar
	if ( empty( $id ) )
		return !empty( $avatar ) ? $avatar : $default;
		
	// check yo see if there is a file that was uploaded by the user
	if( user_avatar_avatar_exists($id) ):
	
		$user_avatar = user_avatar_fetch_avatar( array( 'item_id' => $id, 'width' => $size, 'height' => $size, 'alt' => $alt ) );
		if($user_avatar)
			return $user_avatar;
		else
			return !empty( $avatar ) ? $avatar : $default;
	else:
		return !empty( $avatar ) ? $avatar : $default;
	endif;
	// for good measure 
	return !empty( $avatar ) ? $avatar : $default;
}

add_filter( 'get_avatar', 'user_avatar_fetch_avatar_filter', 10, 5 );

/**
 * user_avatar_core_fetch_avatar()
 *
 * Description: Fetches an avatar from a BuddyPress object. Supports user/group/blog as
 * 				default, but can be extended to include your own custom components too.
 *
 * @global object $bp
 * @global object $current_blog
 * @param array $args Determine the output of this function
 * @return string Formatted HTML <img> element, or raw avatar URL based on $html arg
 */
function user_avatar_fetch_avatar( $args = '' ) {
	
	$defaults = array(
		'item_id'		=> false,
		'object'		=> "user",		// user/group/blog/custom type (if you use filters)
		'type'			=> 'full',		// thumb or full
		'avatar_dir'	=> false,		// Specify a custom avatar directory for your object
		'width'			=> false,		// Custom width (int)
		'height'		=> false,		// Custom height (int)
		'class'			=> '',			// Custom <img> class (string)
		'css_id'		=> false,		// Custom <img> ID (string)
		'alt'			=> '',	// Custom <img> alt (string)
		'email'			=> false,		// Pass the user email (for gravatar) to prevent querying the DB for it
		'no_grav'		=> false,		// If there is no avatar found, return false instead of a grav?
		'html'			=> true			// Wrap the return img URL in <img />
	);
	
	// Compare defaults to passed and extract
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$avatar_folder_dir = USER_AVATAR_UPLOAD_PATH."{$item_id}/";
	$avatar_folder_url = USER_AVATAR_URL."{$item_id}";
	
	if($width > 50)
		$type = "full";
		
	$avatar_size = ( 'full' == $type ) ? '-bpfull' : '-bpthumb';
	$class .= " avatar ";
	$class .= " avatar-". $width ." ";
	$class .= " photo";
	
	if ( false === $alt)
		$safe_alt = '';
	else
		$safe_alt = esc_attr( $alt );
	
	
	// Add an identifying class to each item
	$class .= ' ' . $object . '-' . $item_id . '-avatar';

	// Set CSS ID if passed
	if ( !empty( $css_id ) )
		$css_id = " id=\"".esc_attr($css_id)."\"";
	
	// Set avatar width
	if ( $width )
		$html_width = " width=\"".esc_attr($width)."\"";
	else
		$html_width = ( 'thumb' == $type ) ? ' width="' . esc_attr(USER_AVATAR_THUMB_WIDTH) . '"' : ' width="' . esc_attr(USER_AVATAR_FULL_WIDTH) . '"';

	// Set avatar height
	if ( $height )
		$html_height = " height=\"".esc_attr($height)."\"";
	else
		$html_height = ( 'thumb' == $type ) ? ' height="' . esc_attr(USER_AVATAR_THUMB_HEIGHT) . '"' : ' height="' . esc_attr(USER_AVATAR_FULL_HEIGHT) . '"';
	

	
	if( $avatar_img = user_avatar_avatar_exists( $item_id ) ):
		
		$avatar_src = get_site_url()."/wp-content/uploads/avatars/".$item_id."/".$avatar_img;
		if(function_exists('is_subdomain_install') && !is_subdomain_install())
			$avatar_src = "/wp-content/uploads/avatars/".$item_id."/".$avatar_img;
		
		$avatar_folder_dir = USER_AVATAR_UPLOAD_PATH."{$item_id}/";
		
		$file_time = filemtime ($avatar_folder_dir."/".$avatar_img);
		
		$avatar_url = plugins_url('/user-avatar/user-avatar-pic.php')."?src=".$avatar_src ."&w=".$width."&id=".$item_id."&random=".$file_time;
		
		// Return it wrapped in an <img> element
		if ( true === $html ) { // this helps validate stuff
			return '<img src="' . esc_url($avatar_url) . '" alt="' . esc_attr($alt) . '" class="' . esc_attr($class) . '"' . $css_id . $html_width . $html_height . ' />';
		// ...or only the URL
		} else {
			return  $avatar_url ;
		}
	else:
		return false;
	endif;
}
add_action("admin_init", "user_avatar_delete");
/**
 * user_avatar_delete function.
 * 
 * @access public
 * @return void
 */
function user_avatar_delete(){
		
		global $pagenow;
		
		$current_user = wp_get_current_user();
		
		// If user clicks the remove avatar button, in URL deleter_avatar=true
		if( isset($_GET['delete_avatar']) && wp_verify_nonce($_GET['_nononce'], 'user_avatar') && ( $_GET['u'] == $current_user->id || current_user_can('edit_users')) )
		{
			$user_id = $_GET['user_id'];
			if(is_numeric($user_id))
				$user_id = "?user_id=".$user_id;
				
			user_avatar_delete_files($_GET['u']);
			wp_redirect(get_option('siteurl') . '/wp-admin/'.$pagenow.$user_id);
			
		}		
}
/**
 * user_avatar_form function.
 * Description: Creation and calling of appropriate functions on the overlay form. 
 * @access public
 * @param mixed $profile
 * @return void
 */
function user_avatar_form($profile)
{
	global $current_user;
	
	// Check if it is current user or super admin role
	if( $profile->ID == $current_user->ID || current_user_can('edit_user', $current_user->ID) || is_super_admin($current_user->ID) )
	{
		$avatar_folder_dir = USER_AVATAR_UPLOAD_PATH."{$profile->ID}/";
	?>
	<div id="user-avatar-display" class="submitbox" >
	<h3 ><?php _e('Picture','user-avatar'); ?></h3>
	<p id="user-avatar-display-image"><?php echo user_avatar_get_avatar($profile->ID, 150); ?></p>
	<a id="user-avatar-link" class="button-primary thickbox" href="<?php echo admin_url('admin-ajax.php'); ?>?action=user_avatar_add_photo&step=1&uid=<?php echo $profile->ID; ?>&TB_iframe=true&width=720&height=450" title="<?php _e('Upload and Crop an Image to be Displayed','user-avatar'); ?>" ><?php _e('Update Picture','user-avatar'); ?></a> 
	
	<?php 
		// Remove the User-Avatar button if there is no uploaded image
		
		if(isset($_GET['user_id'])):
			$remove_url = admin_url('user-edit.php')."?user_id=".$_GET['user_id']."&delete_avatar=true&_nononce=". wp_create_nonce('user_avatar')."&u=".$profile->ID;
		else:
			$remove_url = admin_url('profile.php')."?delete_avatar=true&_nononce=". wp_create_nonce('user_avatar')."&u=".$profile->ID;
		
		endif;
		if ( user_avatar_avatar_exists($profile->ID) ):?>
			<a id="user-avatar-remove" class="submitdelete deleteaction" href="<?php echo esc_url_raw($remove_url); ?>" title="<?php _e('Remove User Avatar Image','user-avatar'); ?>" ><?php _e('Remove','user-avatar'); ?></a>
			<?php
		endif;
	?>
	</div>
	<script type="text/javascript">
	function user_avatar_refresh_image(img){
	 jQuery('#user-avatar-display-image').html(img);
	}
	function add_remove_avatar_link(){
		if(!jQuery("#user-avatar-remove").is('a')){
			jQuery('#user-avatar-link').after(" <a href='<?php echo $remove_url; ?>' class='submitdelete'  id='user-avatar-remove' ><?php _e('Remove','user-avatar'); ?></a>")
		}
			
	
	}
	
	</script>
	<?php
	}
} 

/*-- HELPER FUNCTIONS --*/
/**
 * user_avatar_avatar_exists function.
 * 
 * @access public
 * @param mixed $id
 * @return void
 */
function user_avatar_avatar_exists($id){
	
	$avatar_folder_dir = USER_AVATAR_UPLOAD_PATH."{$id}/";
	$return = false;
	
	if ( is_dir( $avatar_folder_dir ) && $av_dir = opendir( $avatar_folder_dir ) ) {
			
			// Stash files in an array once to check for one that matches
			$avatar_files = array();
			while ( false !== ( $avatar_file = readdir($av_dir) ) ) {
				// Only add files to the array (skip directories)
				if ( 2 < strlen( $avatar_file ) )
					$avatar_files[] = $avatar_file;
			}
			
			// Check for array
			if ( 0 < count( $avatar_files ) ) {
				// Check for current avatar
				if( is_array($avatar_files) ):
					foreach( $avatar_files as $key => $value ) {
						if(strpos($value, "-bpfull")):
							$return =  $value;
						endif;
					}
				endif;
				
			}

		// Close the avatar directory
		closedir( $av_dir );

	}
	
	return $return;
}
/**
 * user_avatar_get_avatar function.
 * 
 * @access public
 * @param mixed $id
 * @param mixed $width
 * @return void
 */
function user_avatar_get_avatar($id,$width) {
	
	if(! get_option('show_avatars')):
	
		if( user_avatar_avatar_exists($id) ):
	
			$user_avatar = user_avatar_fetch_avatar( array( 'item_id' => $id, 'width' => $width, 'height' => $width, 'alt' => '' ) );
			if($user_avatar):
				return $user_avatar;
			else:
				return '<img src="'.USER_AVATAR_DEFAULT.'" width="'.$width.'" height="'.$width.'" class="avatar" />';
			endif;
		else:
			return '<img src="'.USER_AVATAR_DEFAULT.'" width="'.$width.'" height="'.$width.'" class="avatar" />';
		endif;
	else:
		return get_avatar($id,$width);
	endif;
}
/* --- END OF FILE --- */

