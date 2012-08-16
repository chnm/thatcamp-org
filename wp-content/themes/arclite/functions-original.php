<?php
/* Arclite/digitalnature */


$arclite_theme_data = get_theme_data(TEMPLATEPATH.'/style.css');
define('THEME_VERSION', trim($arclite_theme_data['Version']) );

if(!defined("PHP_EOL")) define("PHP_EOL", strtoupper(substr(PHP_OS,0,3) == "WIN") ? "\r\n" : "\n");



function setup_options() {
  remove_options();

  update_option( 'arclite' , apply_filters('theme_default_settings', array(
    'theme_version' => THEME_VERSION,
    'imageless' => '0',
    'threecol' => '0',
    'left_sidebar' => '1',
    'jquery' => '1',
    'logo' => '',
    'header' => 'default',
    'header1' => '',
    'header2' => '',
    'header_color' => '',
    'widget_background' => 'default',
    'content_background' => 'default',
    'post_preview' => 'full',
    'navigation' => 'pages',
    'navigation_exclude' => '',
    'search' => '1',
    'sidebar_categories' => '1',
    'footer_content' => '',
    'user_css' => '',
    'lightbox' => '1',
    'remove_settings' => '0'))
  );
}

function remove_options() {
  delete_option('arclite');
}

function get_arclite_option( $option ) {
  $get_arclite_options = get_option('arclite');
  return $get_arclite_options[$option];
}

function print_arclite_option( $option ) {
  $get_arclite_options = get_option('arclite');
  echo $get_arclite_options[$option];
}

function get_upload_dir($dir) {
  $uploadpath = wp_upload_dir();
  if ($uploadpath['baseurl']=='') $uploadpath['baseurl'] = get_bloginfo('siteurl').'/wp-content/uploads';
  return $uploadpath[$dir];
}


function arclite_update_options() {
  check_admin_referer('theme-settings');
  if (!current_user_can('edit_themes')) wp_die(__('You are not authorised to perform this operation.', 'arclite'));
  $options = get_option('arclite');
  if (isset($_POST['imageless'])) $options['imageless'] = 1; else $options['imageless'] = 0;
  if (isset($_POST['threecol'])) $options['threecol'] = 1; else $options['threecol'] = 0;
  if (isset($_POST['left_sidebar'])) $options['left_sidebar'] = $_POST['left_sidebar'];
  if (isset($_POST['jquery'])) $options['jquery'] = 1; else $options['jquery'] = 0;
  if (isset($_POST['lightbox'])) $options['lightbox'] = 1; else $options['lightbox'] = 0;
  if (isset($_POST['header'])) $options['header'] = $_POST['header'];
  if (isset($_POST['header_color'])) $options['header_color'] = $_POST['header_color'];
  if (isset($_POST['sidebar'])) $options['sidebar'] = $_POST['sidebar'];
  if (isset($_POST['widget_background'])) $options['widget_background'] = $_POST['widget_background'];
  if (isset($_POST['content_background'])) $options['content_background'] = $_POST['content_background'];
  if (isset($_POST['post_preview'])) $options['post_preview'] = $_POST['post_preview'];
  if (isset($_POST['navigation'])) $options['navigation'] = $_POST['navigation'];
  if (isset($_POST['navigation_exclude'])) $options['navigation_exclude'] = $_POST['navigation_exclude'];
  if (isset($_POST['search'])) $options['search'] = 1; else $options['search'] = 0;
  if (isset($_POST['sidebar_categories'])) $options['sidebar_categories'] = 1; else $options['sidebar_categories'] = 0;
  if (isset($_POST['footer_content'])) $options['footer_content'] = stripslashes($_POST['footer_content']);
  if (isset($_POST['user_css'])) $options['user_css'] = stripslashes($_POST['user_css']);
  if (isset($_POST['remove_settings'])) $options['remove_settings'] = 1; else $options['remove_settings'] = 0;

  if (isset($_POST['remove-logo'])):
    $options['logo'] = '';
  else:
    if ($_FILES["file-logo"]["type"]):
      $directory = get_upload_dir('basedir').'/';
      move_uploaded_file($_FILES["file-logo"]["tmp_name"],
      $directory . $_FILES["file-logo"]["name"]);
      $options['logo'] = get_upload_dir('baseurl'). "/". $_FILES["file-logo"]["name"];
    endif;
  endif;

  if (isset($_POST['remove-header1'])):
    $options['header1'] = '';
  else:
    if ($_FILES["file-header1"]["type"]):
      $directory = get_upload_dir('basedir').'/';
      move_uploaded_file($_FILES["file-header1"]["tmp_name"],
      $directory . $_FILES["file-header1"]["name"]);
      $options['header1'] = get_upload_dir('baseurl'). "/". $_FILES["file-header1"]["name"];
    endif;
  endif;


  if (isset($_POST['remove-header2'])):
    $options['header2'] = '';
  else:
    if ($_FILES["file-header2"]["type"]):
      $directory = get_upload_dir('basedir').'/';
      move_uploaded_file($_FILES["file-header2"]["tmp_name"],
      $directory . $_FILES["file-header2"]["name"]);
      $options['header2'] = get_upload_dir('baseurl'). "/". $_FILES["file-header2"]["name"];
    endif;
  endif;


  update_option('arclite', $options);
  wp_redirect(admin_url('themes.php?page=theme-settings&updated=true'));
}


function arclite_theme_settings() {
  if (current_user_can('edit_themes')): ?>

  <style type="text/css">
   #theme-settings input.radio{border:0;}
   #theme-settings label{width: 100px;display:block;float:left;}
   #theme-settings table{margin:2em 0;}
   #theme-settings th p{line-height:150%;font-weight:normal;padding:0;margin:0;}
   #theme-settings th p span{color:#999;font-weight:normal;font-style:italic;display:block}
   .support{background:#eee;padding:.6em 1em;float:right;font-style:italic;}
   #colorpicker{position:relative;}
  .farbtastic{background: #FFF; border: 1px solid #8CBDD5; position: relative; }
  .farbtastic *{cursor:crosshair;position:absolute;}
  .farbtastic,.farbtastic .wheel{height:195px;width:195px;}
  .farbtastic .color,.farbtastic .overlay{height:101px;left:47px;top:47px;width:101px; }
  .farbtastic .wheel{background: url(<?php echo get_bloginfo('template_url'); ?>/images/admin/wheel.png) no-repeat; height: 195px; width: 195px;}
  .farbtastic .overlay{background: url(<?php echo get_bloginfo('template_url'); ?>/images/admin/mask.png) no-repeat; }
   .farbtastic .marker{background: url(<?php echo get_bloginfo('template_url'); ?>/images/admin/marker.png) no-repeat; height: 17px; margin: -8px 0 0 -8px; overflow: hidden; width: 17px;}

  </style>

  <script type="text/javascript" src="<?php echo get_bloginfo('template_url').'/js/admin/jquery.farbtastic.min.js' ?>"></script>
  <script type="text/javascript">
  /* <![CDATA[ */

  jQuery.fn.appendVal = function(txt) {
    return this.each(function(){
      this.value += txt;
   });
  };

  // init
  jQuery(document).ready(function () {

    // enable/disable fields based on active theme settings

    jQuery('input#opt-imageless').change(function() {
     jQuery('select#opt-header,select#opt-widget_background,select#opt-content_background').attr("disabled", false);
     if(jQuery('select#opt-header').find('option:selected').attr('value')=='user-image') jQuery('#header-upload').show();
     if(jQuery('select#opt-header').find('option:selected').attr('value')=='user-color') jQuery('#header-color').show();
     if (jQuery(this).is(":checked")) {
       jQuery('select#opt-header,select#opt-widget_background,select#opt-content_background').attr("disabled", true);
       jQuery('#header-upload,#header-color').hide();
     }
    });
    jQuery("input#opt-imageless").change();


    jQuery('input#opt-jquery').change(function() {
     jQuery('input#opt-lightbox').attr("disabled", true);
     if (jQuery(this).is(":checked")) {
       jQuery('input#opt-lightbox').attr("disabled", false);
     }
    });
    jQuery("input#opt-jquery").change();

    jQuery('select#opt-header').change(function() {
     jQuery('#header-upload,#header-color').hide();
     if(jQuery(this).find('option:selected').attr('value')=='user-image') jQuery('#header-upload').show();
     if(jQuery(this).find('option:selected').attr('value')=='user-color') jQuery('#header-color').show();
    });
    jQuery("select#opt-header").change();

    jQuery('#colorpicker').farbtastic('input#opt-header_color');
	jQuery('#colorpicker').hide();
	jQuery('input#opt-header_color').focus( function() { jQuery('#colorpicker').show('fast'); });
	jQuery('input#opt-header_color').blur( function() { jQuery('#colorpicker').hide('fast'); });

  });
  /* ]]> */
  </script>

  <div id="theme-settings" class="wrap">
   <?php screen_icon(); ?>
   <h2><?php _e('Arclite settings','arclite'); ?></h2>

   <form action="<?php echo admin_url('admin-post.php?action=arclite_update'); ?>" method="post" enctype="multipart/form-data">
   <?php wp_nonce_field('theme-settings'); ?>
   <?php if (isset($_GET['updated'])): ?>
   <div class="updated fade below-h2">
    <p><?php printf( __('Settings saved. %s', 'arclite'),'<a href="' . user_trailingslashit(get_bloginfo('url')) . '">' . __('View site','arclite') . '</a>' ); ?></p>
   </div>
   <?php endif; ?>

   <!-- theme-settings -->
   <div id="theme-settings">

      <table class="form-table" style="width: auto">

       <tr>
        <th scope="row"><p><?php _e("Imageless layout","arclite") ?><span><?php _e("(no background images; reduces pages to just a few KB, with the cost of less graphic details)","arclite"); ?></span></p></th>
        <td><input name="imageless" id="opt-imageless" type="checkbox" value="1" <?php checked( '1', get_arclite_option('imageless') ) ?> /></td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Enable 3rd column on all pages","arclite") ?><span><?php _e("(apply the 3-column template if you only want it on certain pages)","arclite"); ?></span></p></th>
        <td><input name="threecol" id="opt-threecol" type="checkbox" value="1" <?php checked( '1', get_arclite_option('threecol') ) ?> /></td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Sidebar position","arclite"); ?></p></th>
        <td>
         <select name="left_sidebar" id="opt-left_sidebar">
          <option value="1" <?php if(get_arclite_option('left_sidebar')==1) echo 'selected="selected" '; ?>><?php _e('Left', 'arclite'); ?></option>
          <option value="0" <?php if(get_arclite_option('left_sidebar')!=1) echo 'selected="selected" '; ?>><?php _e('Right', 'arclite'); ?></option>
         </select>
        </td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Top navigation shows","arclite"); ?></p></th>
        <td>
         <select name="navigation" id="opt-navigation">
          <option value="pages" <?php if(get_arclite_option('navigation')=='pages') echo 'selected="selected" '; ?>><?php _e('Pages', 'arclite'); ?></option>
          <option value="categories" <?php if(get_arclite_option('navigation')=='categories') echo 'selected="selected" '; ?>><?php _e('Categories', 'arclite'); ?></option>
         </select>
        </td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Exclude from main navigation","arclite"); ?><span><?php _e("(Type page or category IDs. Separate with commas)","arclite"); ?></span></p></th>
        <td>
         <input class="text" type="text" size="40" name="navigation_exclude" value="<?php esc_attr(print_arclite_option('navigation_exclude')) ?>" />
        </td>
        </tr>

        <tr>
        <th scope="row"><p><?php _e("Footer","arclite"); ?><span><?php _e("Add content",'arclite') ?></span></p></th>
        <td>
         <textarea rows="4" cols="50" name="footer_content" class="code"><?php echo get_arclite_option('footer_content') ?></textarea>
        </td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Logo image","arclite"); ?></p></th>
        <td>


       <?php if(is_writable(get_upload_dir('basedir'))): ?>

        <?php _e('Upload a custom logo image','arclite'); ?><br />
        <input type="file" name="file-logo" id="file-logo" />
        <br />
        <?php if(get_arclite_option('logo')): ?>
        <div style="background: #000;margin-top:10px;overflow:hidden;"><img src="<?php echo get_arclite_option('logo'); ?>" style="padding:10px;" /></div>
        <button type="submit" class="button" name="remove-logo" value="0"><?php _e("Remove current image","arclite"); ?></button>
        <?php endif; ?>


       <?php else: ?>
        <p class="error" style="padding: 4px;"><?php printf(__('Can\'t upload! Directory %s is not writable!<br />Change write permissions with CHMOD 755 or 777','arclite'), $uploadpath['baseurl']);  ?></p>
       <?php endif; ?>

        </td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Show search","arclite") ?></p></th>
        <td><input name="search" id="opt-search" type="checkbox" value="1" <?php checked( '1', get_arclite_option('search') ) ?> /></td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Show theme-default category block","arclite") ?></p></th>
        <td><input name="sidebar_categories" id="opt-sidebar_categories" type="checkbox" value="1" <?php checked( '1', get_arclite_option('sidebar_categories') ) ?> /></td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Header background","arclite"); ?></p></th>
        <td>
         <select name="header" id="opt-header">
              <option <?php if(get_arclite_option('header')=='default') echo 'selected="selected" '; ?> value="default">Texture: Dark brown (default)</option>
              <option <?php if(get_arclite_option('header')=='green') echo 'selected="selected" '; ?> value="green">Texture: Dark green</option>
              <option <?php if(get_arclite_option('header')=='red') echo 'selected="selected" '; ?> value="red">Texture: Dark red</option>
              <option <?php if(get_arclite_option('header')=='blue') echo 'selected="selected" '; ?> value="blue">Texture: Dark blue</option>
              <option <?php if(get_arclite_option('header')=='field') echo 'selected="selected" '; ?> value="field">Texture: Green Field</option>
              <option <?php if(get_arclite_option('header')=='fire') echo 'selected="selected" '; ?> value="fire">Texture: Burning</option>
              <option <?php if(get_arclite_option('header')=='wall') echo 'selected="selected" '; ?> value="wall">Texture: Dirty Wall</option>
              <option <?php if(get_arclite_option('header')=='wood') echo 'selected="selected" '; ?> value="wood">Texture: Wood</option>
              <option style="color: #ed1f24" <?php if(get_arclite_option('header')=='user-image') echo 'selected="selected" '; ?> value="user-image"><?php _e('User defined image (upload)','arclite'); ?></option>
              <option style="color: #ed1f24" <?php if(get_arclite_option('header')=='user-color') echo 'selected="selected" '; ?> value="user-color"><?php _e('User defined color','arclite'); ?></option>
         </select>

         <div id="header-upload">
         <?php if(is_writable(get_upload_dir('basedir'))): ?>

          <?php _e('Centered image (upload a 960x190 image for best fit):','arclite'); ?><br />
          <input type="file" name="file-header1" id="file-header1" />
          <br />
          <?php if(get_arclite_option('header1')): ?>
          <div style="background: #000;margin-top:10px;overflow:hidden;"><img src="<?php echo get_arclite_option('header1'); ?>" style="padding:10px;" /></div>
          <button type="submit" class="button" name="remove-header1" value="0"><?php _e("Remove current image","arclite"); ?></button><br />
          <?php endif; ?>
          <br />
          <?php _e('Tiled image, repeats itself across the entire header (centered image will show on top of it, if specified):','arclite'); ?><br />
          <input type="file" name="file-header2" id="file-header2" />
          <br />
          <?php if(get_arclite_option('header2')): ?>
          <div style="background: #000;margin-top:10px;overflow:hidden;"><img src="<?php echo get_arclite_option('header2'); ?>" style="padding:10px;" /></div>
          <button type="submit" class="button" name="remove-header2" value="0"><?php _e("Remove current image","arclite"); ?></button><br />
          <?php endif; ?>

         <?php else: ?>
          <p class="error" style="padding: 4px;"><?php printf(__('Can\'t upload! Directory %s is not writable!<br />Change write permissions with CHMOD 755 or 777','arclite'), $uploadpath['baseurl']);  ?></p>
         <?php endif; ?>
         </div>

         <div id="header-color">
           <?php _e('Pick a color by entering a hexadecimal code such as #FF00AA.','arclite'); ?><br />
           <input type="text" id="opt-header_color" name="header_color" style="background: #<?php esc_attr(print_arclite_option('header_color')); ?>;" value="<?php esc_attr(print_arclite_option('header_color')); ?>" />
           <div id="colorpicker"></div>

         </div>

        </td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Widget title background","arclite"); ?></p></th>
        <td>
          <select name="widget_background" id="opt-widget_background">
              <option <?php if(get_arclite_option('widget_background')=='default') echo 'selected="selected" '; ?> value="default">Pink (default)</option>
              <option <?php if(get_arclite_option('widget_background')=='green') echo 'selected="selected" '; ?> value="green">Green</option>
              <option <?php if(get_arclite_option('widget_background')=='blue') echo 'selected="selected" '; ?> value="blue">Blue</option>
              <option <?php if(get_arclite_option('widget_background')=='gray') echo 'selected="selected" '; ?> value="gray">Gray</option>
          </select>
        </td>
       </tr>

       <tr>
        <th scope="row"><p><?php _e("Content background","arclite"); ?></p></th>
        <td>
          <select name="content_background" id="opt-content_background">
              <option <?php if(get_arclite_option('content_background')=='default') echo 'selected="selected" '; ?> value="default">Texture: Light brown + noise (default)</option>
              <option <?php if(get_arclite_option('content_background')=='grunge') echo 'selected="selected" '; ?> value="grunge">Texture: Grunge</option>
              <option <?php if(get_arclite_option('content_background')=='white') echo 'selected="selected" '; ?> value="white">White color</option>
          </select>
        </td>
       </tr>



       <tr>
        <th scope="row"><p><?php _e("Index page/Archives show:","arclite"); ?></p></th>
        <td>
         <select name="post_preview">
          <option value="full" <?php if(get_arclite_option('post_preview')=='full') echo 'selected="selected" '; ?>><?php _e('Full posts', 'arclite'); ?></option>
          <option value="excerpt" <?php if(get_arclite_option('post_preview')=='excerpt') echo 'selected="selected" '; ?>><?php _e('Excerpts only', 'arclite'); ?></option>
         </select>
        </td>
       </tr>


       <tr>
        <th scope="row"><p><?php _e("User CSS code","arclite"); ?><span><?php _e("Modify anything related to design using simple CSS","arclite"); ?><br /><br /><span style="color: #ed1f24"><?php _e("Avoid modifying theme files and use this option instead to preserve changes after update","arclite"); ?></span></span></p></th>
        <td valign="top">
         <textarea rows="10" cols="50" name="user_css" id="opt-user_css" class="code alignleft"><?php echo get_arclite_option('user_css') ?></textarea>

         <div class="alignleft" style="padding: 1em;">
          <button class="button" onclick="jQuery('#opt-user_css').appendVal('.block-content{width:960px;max-width:960px;}\n');" type="button"><?php _e("Set a fixed page width (960px)","arclite"); ?></button><br />
          <button class="button" onclick="jQuery('#opt-user_css').appendVal('.block-content{width:95%;max-width:95%;}\n');" type="button"><?php _e("Set fluid page width (not recommended)","arclite"); ?></button><br />
          <button class="button" onclick="jQuery('#opt-user_css').appendVal('.post p.post-date,.post p.post-author{display:none;}\n');" type="button"><?php _e("Hide post information bar","arclite"); ?></button><br />
          <button class="button" onclick="jQuery('#opt-user_css').appendVal('body,input,textarea,select,h1,h2,h6,.post h3,.box .titlewrap h4{font-family:Arial, Helvetica;}\n');" type="button"><?php _e("Arial type fonts","arclite"); ?></button><br />
          <button class="button" onclick="jQuery('#opt-user_css').appendVal('#pagetitle{font-size:75%;}\n');" type="button"><?php _e("Make text logo/headline smaller","arclite"); ?></button><br />
         </div>
        </td>
        </tr>

        <tr>
         <th scope="row"><p><?php _e("Use jQuery","arclite"); ?><span><?php _e("(for testing purposes only, you shouldnt change this)","arclite"); ?></span></p></th>
         <td><input id="opt-jquery" name="jquery" type="checkbox" value="1" <?php checked( '1', get_arclite_option('jquery') ) ?> /></td>
        </tr>

        <tr>
         <th scope="row"><p><?php _e("Use theme lightbox","arclite"); ?><span><?php _e("(Uncheck if you're using a plugin)","arclite"); ?></span></p></th>
         <td><input id="opt-lightbox" name="lightbox" type="checkbox" value="1" <?php checked( '1', get_arclite_option('lightbox') ) ?> /></td>
        </tr>

        <tr>
        <th scope="row"><p><?php _e("Remove Arclite settings from the database after theme switch","arclite"); ?><span><?php _e("Leave this unchecked if you plan to keep Arclite as your default theme","arclite"); ?></span></p></th>      <td>
         <input name="remove_settings" id="opt-remove_settings" type="checkbox" value="1" <?php checked( '1', get_arclite_option('remove_settings') ) ?> />
        </td>
       </tr>


      </table>

   </div>
   <!-- /theme-settings -->

   <p><input type="submit" class="button-primary" name="submit" value="<?php _e("Save Changes","arclite"); ?>" /></p>
   </form>
   <hr />
   <div class="support">
    <form class="alignleft" action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="4605915" /> <input alt="Donate" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" type="image" /> <img src="https://www.paypal.com/en_US/i/scr/pixel.gif" border="0" alt="" width="1" height="1" /></form>
     <a href="http://digitalnature.ro/projects/arclite">Arclite</a> is a free theme developed by <a href="http://digitalnature.ro">digitalnature</a>.<br />You can support this project by donating.
   </div>
   <div class="clear"></div>

  </div>
  <?php endif;
}

function arclite_addmenu() {
  $page = add_theme_page(
    __('Arclite settings','arclite'),
    __('Arclite settings','arclite'),
       'edit_themes',
       'theme-settings',
       'arclite_theme_settings'
  );
}


// Add Settings Page
add_action('admin_menu', 'arclite_addmenu');

// Options handler for Settings Page
add_action('admin_post_arclite_update', 'arclite_update_options');

// Delete all Options if Theme is switched
if(get_arclite_option('remove_settings')) add_action('switch_theme', 'remove_options');


function setup_css() {
 echo '<style type="text/css">'.PHP_EOL;
 $arclite_options = get_option('arclite');

 if($arclite_options['imageless']):
  echo '@import "'.get_bloginfo('template_url').'/style-imageless.css";'.PHP_EOL;
  if($arclite_options['left_sidebar']): echo '@import "'.get_bloginfo('template_url').'/options/leftsidebar.css";'.PHP_EOL;endif;
 else:
  echo '@import "'.get_bloginfo('stylesheet_url').'";'.PHP_EOL;
  echo '@import "'.get_bloginfo('template_url').'/options/side-'.$arclite_options['widget_background'].'.css";'.PHP_EOL;
  echo '@import "'.get_bloginfo('template_url').'/options/content-'.$arclite_options['content_background'].'.css";'.PHP_EOL;
  if($arclite_options['left_sidebar']) echo '@import "'.get_bloginfo('template_url').'/options/leftsidebar.css";'.PHP_EOL;
  switch($arclite_options['header']):
   case 'user-image':
    if($arclite_options['header1']<>'')
       echo '#header{ background: transparent url("'.$arclite_options['header1'].'") no-repeat center top; }'.PHP_EOL;
    if($arclite_options['header2']<>'')
       echo '#header-wrap{ background: transparent url("'.$arclite_options['header2'].'") repeat center top; }'.PHP_EOL;
    break;
   case 'user-color': echo '#header, #header-wrap{ background-color: '.$arclite_options['header_color'].'; }'.PHP_EOL;break;
   default: echo '@import "'.get_bloginfo('template_url').'/options/header-'.$arclite_options['header'].'.css";'.PHP_EOL;
  endswitch;
 endif;
 if ($arclite_options['user_css']): echo $arclite_options['user_css'].PHP_EOL;endif;
 echo '</style>'.PHP_EOL; ?>
<!--[if lte IE 6]>
<style type="text/css" media="screen">
 @import "<?php bloginfo('template_url'); ?>/ie6.css";
</style>
<![endif]-->

 <?php
}
add_action('wp_head', 'setup_css',2);


//if (version_compare(get_arclite_option('theme_version'), THEME_VERSION, '!=')) setup_options();
if (!get_option('arclite')) setup_options();


function queryposts($atts){
  extract( shortcode_atts( array(
   'category_id' => '',
   'category_name' => '',
   'tag' => '',
   'day' => '',
   'month' => '',
   'year' => '',
   'count' => '5',
   'author_id' => '',
   'author_name' => '',
   'order_by' => 'date',
  ), $atts) );

  $output = '';
  $query = array();

  if ($category_id != '') $query[] = 'cat=' .$category_id;
  if ($category_name != '') $query[] = 'category_name=' .$category_name;
  if ($tag != '') $query[] = 'tag=' . $tag;
  if ($day != '') $query[] = 'day=' . $day;
  if ($month != '') $query[] = 'monthnum=' . $month;
  if ($year != '') $query[] = 'year=' . $year;
  if ($count) $query[] = 'posts_per_page=' .$count;
  if ($author_id != '') $query[] = 'author=' . $author_id;
  if ($author_name != '') $query[] = 'author_name=' . $author_name;
  if ($order_by) $query[] = 'orderby=' . $order_by;

  $posts = new WP_Query(implode('&',$query));
  while ($posts->have_posts()): $posts->the_post();

    $output .= '<div class="post">';
    $output .= '<div class="post-header"><h3 class="post-title"><a href="'.get_permalink().'" rel="bookmark" title="'.__('Permanent Link:','arclite').' '.get_the_title().'">'.get_the_title().'</a></h3>';
    $output .= '<p class="post-date"><span class="month">'.get_the_time(__('M','arclite')).'</span><span class="day">'.get_the_time(__('j','arclite')).'</span></p>';
    $output .= '<p class="post-author"><span class="info">'.sprintf(__('Posted by %s in %s','arclite'),'<a href="'. get_author_posts_url(get_the_author_ID()) .'" title="'. sprintf(__("Posts by %s","arclite"), attribute_escape(get_the_author())).' ">'. get_the_author() .'</a>',get_the_category_list(', ')).' | ';

    if (comments_open()):
    // global $id, $comment;
     $comments_number = get_comments_number();
     $output .= '<a href="'.get_permalink().'#comments" class="';
     if(($comments_number)==0) $output .= 'no ';
     $output .= 'comments">';
     if ($comments_number>1) $output .= sprintf(__('%s comments'),$comments_number);
     else if ($comments_number==1) $output .= __('1 comment');
     else  $output .= __('No comments');
     $output .= '</a>';
    else: $output .= __("Comments Off","arclite");
    endif;

    $output .= '</span></p></div>';

    $output .= '<div class="post-content clearfix">';
    $post_preview = get_arclite_option('post_preview');
    if($post_preview=='excerpt') $output .= get_the_excerpt(); else $output .= get_the_content(__('Read the rest of this entry &raquo;', 'arclite'));
    $output .= '</div>';

    $post_tags = get_the_tags();
    if ($post_tags):
        $output .= '<p class="tags">';
        $tags = array();
        $i = 0;
        foreach($post_tags as $tag):
         $tags[$i] .=  '<a href="'.get_tag_link($tag->term_id).'" rel="tag" title="'.sprintf(__('%s (%s topics)'),$tag->name,$tag->count).'">'.$tag->name.'</a>';
         $i++;
        endforeach;
        $output .= implode(', ',$tags);
        $output .= '</p>';
    endif;
    $output .= '</div>';
  endwhile;
  return $output;
}
add_shortcode('query', 'queryposts');

add_filter( 'widget_text', 'do_shortcode' ); // Allow [SHORTCODES] in Widgets

// xili-language plugin check
function init_language(){
	if (class_exists('xili_language')) {
		define('THEME_TEXTDOMAIN','arclite');
		define('THEME_LANGS_FOLDER','/lang');
	} else {
	   load_theme_textdomain('arclite', get_template_directory() . '/lang');
	}
}
add_action ('init', 'init_language');


// check if sidebar has widgets
function is_sidebar_active($index = 1) {
  global $wp_registered_sidebars;

  if (is_int($index)): $index = "sidebar-$index";
  else :
  	$index = sanitize_title($index);
  	foreach ((array) $wp_registered_sidebars as $key => $value):
    	if ( sanitize_title($value['name']) == $index):
		 $index = $key;
	     break;
		endif;
	endforeach;
  endif;
  $sidebars_widgets = wp_get_sidebars_widgets();
  if (empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]))
    return false;
  else
  	return true;
}


// register sidebars
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Default sidebar',
        'id' => 'sidebar-1',
		'before_widget' => '<li class="block widget %2$s" id="%1$s"><div class="box"> <div class="wrapleft"><div class="wrapright"><div class="tr"><div class="bl"><div class="tl"><div class="br the-content">',
		'after_widget' => '</div></div></div></div></div></div> </div></li>',
		'before_title' => '<div class="titlewrap"><h4><span>',
		'after_title' => '</span></h4></div>'
    ));

    register_sidebar(array(
        'name' => 'Footer',
        'id' => 'sidebar-2',
		'before_widget' => '<li class="block widget %2$s" id="%1$s"><div class="the-content">',
		'after_widget' => '</div></li>',
		'before_title' => '<h6 class="title">',
		'after_title' => '</h6>'
    ));

    register_sidebar(array(
        'name' => 'Secondary sidebar',
        'id' => 'sidebar-3',
		'before_widget' => '<li class="block widget %2$s" id="%1$s"><div class="box"> <div class="wrapleft"><div class="wrapright"><div class="tr"><div class="bl"><div class="tl"><div class="br the-content">',
		'after_widget' => '</div></div></div></div></div></div> </div></li>',
		'before_title' => '<div class="titlewrap"><h4><span>',
		'after_title' => '</span></h4></div>'
    ));
}

// list pings
function list_pings($comment, $args, $depth) {
 $GLOBALS['comment'] = $comment;
 ?>
 <li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php
}

// list comments
function list_comments($comment, $args, $depth) {
 $GLOBALS['comment'] = $comment;
 global $commentcount;
 if(!$commentcount) { $commentcount = 0; }

 if($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback') { ?>
  <li class="trackback">
   <div class="comment-mask">
    <div class="comment-main">
     <div class="comment-wrap1">
      <div class="comment-wrap2">
       <div class="comment-head">
         <p class="with-tooltip"><span><?php if ($comment->comment_type == 'trackback') _e("Trackback:","arclite"); else _e("Pingback:","arclite"); ?></span> <?php comment_author_link(); ?></p>
        </div>
      </div>
     </div>
    </div>
   </div>


 <?php
 }
 else { ?>

  <!-- comment entry -->
  <li <?php if (function_exists('comment_class')) { if (function_exists('get_avatar') && get_option('show_avatars')) echo comment_class('with-avatar'); else comment_class(); } else { print 'class="comment';if (function_exists('get_avatar') && get_option('show_avatars')) print ' with-avatar'; print '"';  } ?> id="comment-<?php comment_ID() ?>">
   <div class="comment-mask<?php if($comment->user_id == 1) echo ' admincomment'; else echo ' regularcomment'; // <- thanks to Jiri! ?>">
    <div class="comment-main">
     <div class="comment-wrap1">
      <div class="comment-wrap2">
       <div class="comment-head tiptrigger">
        <p>
          <?php
           if (get_comment_author_url()):
            $authorlink='<span class="with-tooltip"><a class="comment-author" id="commentauthor-'.get_comment_ID().'" href="'.get_comment_author_url().'">'.get_comment_author().'</a></span>';
           else:
            $authorlink='<b id="commentauthor-'.get_comment_ID().'">'.get_comment_author().'</b>';
           endif;
           printf(__('%s by %s on %s', 'arclite'), '<a class="comment-id" href="#comment-'.get_comment_ID().'">#'.++$commentcount.'</a>', $authorlink, get_comment_time(get_option('date_format')).' - '.get_comment_time(get_option('time_format')));
          ?>
        </p>

        <?php if(comments_open()) { ?>
        <p class="controls tip">
             <?php
              if (function_exists('comment_reply_link')) {
               comment_reply_link(array_merge( $args, array('add_below' => 'comment-reply', 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<span>'.__('Reply','arclite').'</span>'.$my_comment_count)));
              } ?>
              <a class="quote" title="<?php _e('Quote','arclite'); ?>" href="javascript:void(0);"><span><?php _e('Quote','arclite'); ?></span></a> <?php edit_comment_link('Edit','',''); ?>

        </p>
        <?php } ?>
       </div>
       <div class="comment-body clearfix" id="comment-body-<?php comment_ID() ?>">
         <?php if (function_exists('get_avatar') && get_option('show_avatars')) { ?>
         <div class="avatar"><?php echo get_avatar($comment, 64); ?></div>
         <?php } ?>

         <?php if ($comment->comment_approved == '0') : ?>
	     <p class="error"><small><?php _e('Your comment is awaiting moderation.','arclite'); ?></small></p>
	     <?php endif; ?>

         <div class="comment-text"><?php comment_text(); ?></div>
         <a id="comment-reply-<?php comment_ID() ?>"></a>
       </div>
      </div>
     </div>
    </div>
   </div>
<?php  // </li> is added automatically
  } }
?>
