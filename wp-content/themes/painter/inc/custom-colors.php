<?php
/*
Function Name: Custom Colors
Description: Allow the user to chose the colors of the blog
Version: 0.2
Author: Marcelo Mesquita
Author URI: http://www.marcelomesquita.com/
*/

class custom_colors
{
  // ATRIBUTES ////////////////////////////////////////////////////////////////////////////////////
  var $default_color = "Blue";
  
  var $color_schemes = array(
    
    // Blue
    'Blue' => array(
      'general_border' => '#3f6e9d',
      'general_background' => '#ffffff',
      'general_navigation_link' => '#3f6e9d',
      'dater_background' => '#15314e',
      'dater_text' => '#eff7ff',
      'dater_link' => '#eff7ff',
      'header_background' => '#27537e',
      'header_title' => '#ffffff',
      'header_description' => '#d1ebfa',
      'menu_border' => '#416991',
      'menu_background' => '#14212e',
      'menu_background_active' => '#1a344d',
      'menu_link' => '#ffffff',
      'menu_link_active' => '#d1ebfa',
      'submenu_border' => '#bed1e4',
      'submenu_background' => '#496991',
      'submenu_background_active' => '#27537e',
      'submenu_link' => '#ffffff',
      'submenu_link_active' => '#e0e9fc',
      'breadcrumb_text' => '#416991',
      'breadcrumb_link' => '#27537e',
      'content_background' => '#eff7ff',
      'content_background_odd' => '#dfedfb',
      'content_session_background' => '#416991',
      'content_session_title' => '#ffffff',
      'content_slide' => '#6499ce',
      'content_slide_active' => '#cedff8',
      'content_title' => '#416991',
      'content_text' => '#4e8cb1',
      'content_link' => '#3f6e9d',
      'content_info' => '#3f6e9d',
      'content_box_border' => '#7298bd',
      'content_box_background' => '#7298bd',
      'content_box_text' => '#ffffff',
      'content_box_link' => '#cedff8',
      'content_input_border' => '#f2f2f2',
      'content_input_background' => '#ffffff',
      'content_input_text' => '#4e8cb1',
      'content_button_border' => '#f2f2f2',
      'content_button_background' => '#ffffff',
      'content_button_text' => '#416991',
      'sidebar_border' => '#cadbed',
      'sidebar_background' => '#eff7ff',
      'sidebar_background_odd' => '#e2ecf7',
      'sidebar_session_background' => '#496991',
      'sidebar_session_title' => '#ffffff',
      'sidebar_text' => '#355b82',
      'sidebar_link' => '#355b82',
      'sidebar_input_border' => '#3f6e9d',
      'sidebar_input_background' => '#ffffff',
      'sidebar_input_text' => '#114973',
      'sidebar_button_border' => '##f2f2f2',
      'sidebar_button_background' => '#4e8cb1',
      'sidebar_button_text' => '#114973',
      'footer_background' => '#416991',
      'footer_text' => '#b3ceea',
      'footer_link' => '#ffffff'
    ),
    
    // Red
    'Red' => array(
      'general_border' => '#c92d12',
      'general_background' => '#ffffff',
      'general_navigation_link' => '#c92d12',
      'dater_background' => '#7a1b0b',
      'dater_text' => '#f9eeec',
      'dater_link' => '#f9eeec',
      'header_background' => '#c92d12',
      'header_title' => '#ffffff',
      'header_description' => '#fbb3a7',
      'menu_border' => '#c92d12',
      'menu_background' => '#14212e',
      'menu_background_active' => '#c92d12',
      'menu_link' => '#ffffff',
      'menu_link_active' => '#ffffff',
      'submenu_border' => '#fbb3a7',
      'submenu_background' => '#cc4730',
      'submenu_background_active' => '#c92d12',
      'submenu_link' => '#ffffff',
      'submenu_link_active' => '#fffccd',
      'breadcrumb_text' => '#c92d12',
      'breadcrumb_link' => '#c92d12',
      'content_background' => '#f9eeec',
      'content_background_odd' => '#fff6f4',
      'content_session_background' => '#c92d12',
      'content_session_title' => '#ffffff',
      'content_slide' => '#ef5c34',
      'content_slide_active' => '#ffffff',
      'content_title' => '#c92d12',
      'content_text' => '#000000',
      'content_link' => '#c92d12',
      'content_info' => '#ef5c34',
      'content_box_border' => '#ef5c34',
      'content_box_background' => '#ef5c34',
      'content_box_text' => '#ffffff',
      'content_box_link' => '#fbb3a7',
      'content_input_border' => '#f2f2f2',
      'content_input_background' => '#ffffff',
      'content_input_text' => '#c92d12',
      'content_button_border' => '#c92d12',
      'content_button_background' => '#b5331c',
      'content_button_text' => '#ffffff',
      'sidebar_border' => '#ecc7c1',
      'sidebar_background' => '#f9eeec',
      'sidebar_background_odd' => '#fff6f4',
      'sidebar_session_background' => '#c92d12',
      'sidebar_session_title' => '#ffffff',
      'sidebar_text' => '#d95031',
      'sidebar_link' => '#c92d12',
      'sidebar_input_border' => '#f2f2f2',
      'sidebar_input_background' => '#ffffff',
      'sidebar_input_text' => '#c92d12',
      'sidebar_button_border' => '#d1cccc',
      'sidebar_button_background' => '#fa0f0f',
      'sidebar_button_text' => '#ffffff',
      'footer_background' => '#c92d12',
      'footer_text' => '#ffffff',
      'footer_link' => '#ffe4df'
    ),
    
    // Pastel
    'Pastel' => array(
      'general_border' => '#95ab30',
      'general_background' => '#f9fdea',
      'general_navigation_link' => '#c92d12',
      'dater_background' => '#2f3708',
      'dater_text' => '#f4f9dd',
      'dater_link' => '#f4f9dd',
      'header_background' => '#95ab30',
      'header_title' => '#ffffff',
      'header_description' => '#d1dca4',
      'menu_border' => '#d4dcad',
      'menu_background' => '#b5331c',
      'menu_background_active' => '#742415',
      'menu_link' => '#ffffff',
      'menu_link_active' => '#ffffff',
      'submenu_border' => '#b5331c',
      'submenu_background' => '#742415',
      'submenu_background_active' => '#441a13',
      'submenu_link' => '#f3a091',
      'submenu_link_active' => '#ffffff',
      'breadcrumb_text' => '#5d6831',
      'breadcrumb_link' => '#475024',
      'content_background' => '#f0fcbe',
      'content_background_odd' => '#edf4d1',
      'content_session_background' => '#ddeb99',
      'content_session_title' => '#b5331c',
      'content_slide' => '#b5331c',
      'content_slide_active' => '#ffffff',
      'content_title' => '#b5331c',
      'content_text' => '#000000',
      'content_link' => '#b5331c',
      'content_info' => '#ef5c34',
      'content_box_border' => '#c3cf94',
      'content_box_background' => '#c3cf94',
      'content_box_text' => '#000000',
      'content_box_link' => '#000000',
      'content_input_border' => '#f2f2f2',
      'content_input_background' => '#ffffff',
      'content_input_text' => '#ffffff',
      'content_button_border' => '#f2f2f2',
      'content_button_background' => '#b5331c',
      'content_button_text' => '#ffffff',
      'sidebar_border' => '#d1dca4',
      'sidebar_background' => '#f0fcbe',
      'sidebar_background_odd' => '#edf4d1',
      'sidebar_session_background' => '#ddeb99',
      'sidebar_session_title' => '#b5331c',
      'sidebar_text' => '#d95031',
      'sidebar_link' => '#b5331c',
      'sidebar_input_border' => '#f2f2f2',
      'sidebar_input_background' => '#ffffff',
      'sidebar_input_text' => '#b5331c',
      'sidebar_button_border' => '#d1cccc',
      'sidebar_button_background' => '#fa0f0f',
      'sidebar_button_text' => '#b5331c',
      'footer_background' => '#95ab30',
      'footer_text' => '#ffffff',
      'footer_link' => '#f1fac7'
    ),
  );
  
  var $parts = array(
    
    // General
    'General' => array(
      'general_border' => array(
        'title' => 'Container border',
        'description' => ' '
      ),
      'general_background' => array(
        'title' => 'Container background',
        'description' => ' '
      ),
      'general_navigation_link' => array(
        'title' => 'Navigator link',
        'description' => 'Links that control the pagination'
      )
    ),
    
    // Dater
    'Dater' => array(
      'dater_background' => array(
        'title' => 'Dater background',
        'description' => ' '
      ),
      'dater_text' => array(
        'title' => 'Dater link',
        'description' => ' '
      ),
      'dater_link' => array(
        'title' => 'Dater link',
        'description' => ' '
      )
    ),
    
    // Header
    'Header' => array(
      'header_background' => array(
        'title' => 'Header background',
        'description' => 'only if any image is used'
      ),
      'header_title' => array(
        'title' => 'Site title',
        'description' => ' '
      ),
      'header_description' => array(
        'title' => 'Site description',
        'description' => ' '
      )
    ),
    
    // Menu
    'Menu' => array(
      'menu_border' => array(
        'title' => 'Menu border',
        'description' => ' '
      ),
      'menu_background' => array(
        'title' => 'Menu background',
        'description' => ' '
      ),
      'menu_background_active' => array(
        'title' => 'Active menu background',
        'description' => 'Only when the mouse is over'
      ),
      'menu_link' => array(
        'title' => 'Menu link',
        'description' => ' '
      ),
      'menu_link_active' => array(
        'title' => 'Active menu link',
        'description' => 'Only when the mouse is over'
      ),
      'submenu_border' => array(
        'title' => 'Submenu border',
        'description' => ' '
      ),
      'submenu_background' => array(
        'title' => 'Submenu background',
        'description' => ' '
      ),
      'submenu_background_active' => array(
        'title' => 'Active submenu background',
        'description' => 'Only when the mouse is over'
      ),
      'submenu_link' => array(
        'title' => 'Submenu link',
        'description' => ' '
      ),
      'submenu_link_active' => array(
        'title' => 'Active submenu link',
        'description' => 'Only when the mouse is over'
      )
    ),
    
    // Breadcrumb
    'BreadCrumb' => array(
      'breadcrumb_text' => array(
        'title' => 'BreadCrumb text',
        'description' => ' '
      ),
      'breadcrumb_link' => array(
        'title' => 'BreadCrumb link',
        'description' => ' '
      )
    ),
    
    // Content
    'Content' => array(
      'content_background' => array(
        'title' => 'Post background',
        'description' => ' '
      ),
      'content_background_odd' => array(
        'title' => 'Alternate post background',
        'description' => ' '
      ),
      'content_session_background' => array(
        'title' => 'Session title background',
        'description' => ' '
      ),
      'content_session_title' => array(
        'title' => 'Session title',
        'description' => ' '
      ),
      'content_slide' => array(
        'title' => 'Slide background',
        'description' => ' '
      ),
      'content_slide_active' => array(
        'title' => 'Active slide background',
        'description' => ' '
      ),
      'content_title' => array(
        'title' => 'Post title',
        'description' => ' '
      ),
      'content_text' => array(
        'title' => 'Post text',
        'description' => ' '
      ),
      'content_link' => array(
        'title' => 'Post links',
        'description' => ' '
      ),
      'content_info' => array(
        'title' => 'Post information',
        'description' => 'Information like published date, author and post categories'
      ),
      'content_box_border' => array(
        'title' => 'Box border',
        'description' => 'Boxes like image descriptions, cites and codes'
      ),
      'content_box_background' => array(
        'title' => 'Box background',
        'description' => 'Boxes like image descriptions, cites and codes'
      ),
      'content_box_text' => array(
        'title' => 'Box text',
        'description' => 'Boxes like image descriptions, cites and codes'
      ),
      'content_box_link' => array(
        'title' => 'Box links',
        'description' => 'Boxes like image descriptions, cites and codes'
      ),
      'content_input_border' => array(
        'title' => 'Input border',
        'description' => 'Input as text fields'
      ),
      'content_input_background' => array(
        'title' => 'Input background',
        'description' => 'Input as text fields'
      ),
      'content_input_text' => array(
        'title' => 'Input text',
        'description' => 'Input as text fields'
      ),
      'content_button_border' => array(
        'title' => 'Button border',
        'description' => ' '
      ),
      'content_button_background' => array(
        'title' => 'Button background',
        'description' => ' '
      ),
      'content_button_text' => array(
        'title' => 'Button text',
        'description' => ' '
      )
    ),
    
    // Sidebar
    'Sidebar' => array(
      'sidebar_border' => array(
        'title' => 'Sidebar border',
        'description' => ' '
      ),
      'sidebar_background' => array(
        'title' => 'Sidebar background',
        'description' => ' '
      ),
      'sidebar_background_odd' => array(
        'title' => 'Alternate sidebar background',
        'description' => ' '
      ),
      'sidebar_session_background' => array(
        'title' => 'Session title background',
        'description' => ' '
      ),
      'sidebar_session_title' => array(
        'title' => 'Session title',
        'description' => ' '
      ),
      'sidebar_text' => array(
        'title' => 'Sidebar text',
        'description' => ' '
      ),
      'sidebar_link' => array(
        'title' => 'Sidebar links',
        'description' => ' '
      ),
      'sidebar_input_border' => array(
        'title' => 'Input border',
        'description' => ' '
      ),
      'sidebar_input_background' => array(
        'title' => 'Input background',
        'description' => ' '
      ),
      'sidebar_input_text' => array(
        'title' => 'Input text',
        'description' => ' '
      ),
      'sidebar_button_border' => array(
        'title' => 'Button border',
        'description' => ' '
      ),
      'sidebar_button_background' => array(
        'title' => 'Button background',
        'description' => ' '
      ),
      'sidebar_button_text' => array(
        'title' => 'Button text',
        'description' => ' '
      )
    ),
    
    // Footer
    'Footer' => array(
      'footer_background' => array(
        'title' => 'Footer background',
        'description' => ' '
      ),
      'footer_text' => array(
        'title' => 'Footer text',
        'description' => ' '
      ),
      'footer_link' => array(
        'title' => 'Footer links',
        'description' => ' '
      )
    )
  );
  
  // METHODS //////////////////////////////////////////////////////////////////////////////////////
  /************************************************************************************************
    Color Style
  ************************************************************************************************/
  function color_style()
  {
    // Recover the color scheme
    $color_scheme = get_option('painter_color_scheme');
    
    // If theres no color scheme, pick the default
    if(!is_array($color_scheme))
      $color_scheme = $this->color_schemes[$this->default_color];
    
    // Extract the colors
    extract($color_scheme);
    
    // Mount the CSS
    ?>
      <style type="text/css" media="screen">
        #container
        {
          border-color:<?php print $general_border; ?>;
          background-color:<?php print $general_background; ?>;
        }

        #dater
        {
          color:<?php print $dater_text; ?>;
          background-color:<?php print $dater_background; ?>;
        }
        
        #dater a
        {
          color:<?php print $dater_link; ?>;
        }

        #header
        {
          background-color:<?php print $header_background; ?>;
        }

        #header .blog-title, #header .blog-title a
        {
          color:<?php print $header_title; ?>;
        }

        #header .blog-description, #header .blog-description a
        {
          color:<?php print $header_description; ?>;
        }

        #menu
        {
          background-color:<?php print $menu_background; ?>;
        }

        #menu li, #menu li a
        {
          color:<?php print $menu_link; ?>;
          border-color:<?php print $menu_border; ?>;
          background-color:<?php print $menu_background; ?>;
        }

        #menu li a:hover
        {
          color:<?php print $menu_link_active; ?>;
          background-color:<?php print $menu_background_active; ?>;
        }

        #menu li li, #menu li li a
        {
          color:<?php print $submenu_link; ?>;
          border-color:<?php print $submenu_border; ?>;
          background-color:<?php print $submenu_background; ?>;
        }

        #menu li li a:hover
        {
          color:<?php print $submenu_link_active; ?>;
          background-color:<?php print $submenu_background_active; ?>;
        }

        #breadcrumb
        {
          color:<?php print $breadcrumb_text ?>;
        }

        #breadcrumb a
        {
          color:<?php print $breadcrumb_link ?>;
        }

        .content-title, .content-title a
        {
          color:<?php print $content_session_title; ?> !important;
          background-color:<?php print $content_session_background; ?>;
        }

        #highlight, .post, .comment, .comment-form
        {
          color:<?php print $content_text; ?>;
          border-color:<?php print $content_background_odd; ?>;
          background-color:<?php print $content_background; ?>;
        }
        
        #highlight.odd, .post.odd, .comment.odd
        {
          background-color:<?php print $content_background_odd; ?>;
        }
        
        #highlight a, .post a, .comment a, .comment-form a
        {
          color:<?php print $content_link; ?>;
        }

        #highlight input, #highlight select, #highlight textarea, .post input, .post select, .post textarea, .comment input, .comment select, .comment textarea, .comment-form input, .comment-form select, .comment-form textarea
        {
          color:<?php print $content_input_text; ?>;
          border-color:<?php print $content_input_border; ?>;
          background-color:<?php print $content_input_background; ?>
        }

        #highlight button, .post button, .comment button, .comment-form button
        {
          color:<?php print $content_button_text; ?>;
          border-color:<?php print $content_button_border; ?>;
          background-color:<?php print $content_button_background; ?>
        }

        #highlight-pager a
        {
          background:<?php print $content_slide; ?> !important;
        }

        #highlight-pager a.activeSlide
        {
          background-color:<?php print $content_slide_active; ?> !important;
        }

        .post-title, .post-title a
        {
          color:<?php print $content_title; ?>;
        }

        .post-info, .post-info a, .options, .options a, .comment-rss, .comment-rss a
        {
          color:<?php print $content_info; ?>;
        }

        .entry hr
        {
          border-color:<?php print $content_box_border; ?>;
        }

        .entry .wp-caption, .entry blockquote, .entry code, .entry pre
        {
          color:<?php print $content_box_text; ?>;
          border-color:<?php print $content_box_border; ?>;
          background-color:<?php print $content_box_background; ?>;
        }

        .navigation a
        {
          color:<?php print $general_navigation_link; ?>;
        }

        #sidebar .widget
        {
          color:<?php print $sidebar_text; ?>;
          border-color:<?php print $sidebar_border; ?> !important;
          background-color:<?php print $sidebar_background; ?>;
        }

        #sidebar .widget .odd
        {
          background-color:<?php print $sidebar_background_odd; ?>;
        }

        #sidebar .widget a
        {
          color:<?php print $sidebar_link; ?> !important;
          border-color:<?php print $sidebar_border; ?> !important;
        }

        #sidebar .widget li
        {
          border-color:<?php print $sidebar_border; ?> !important;
          background-color:<?php print $sidebar_background; ?>;
        }

        #sidebar .widget input, #sidebar .widget select, #sidebar .widget textarea
        {
          color:<?php print $sidebar_input_text; ?> !important;
          border-color:<?php print $sidebar_input_border; ?> !important;
          background-color:<?php print $sidebar_input_background; ?> !important;
        }

        #sidebar .widget button
        {
          color:<?php print $sidebar_button_text; ?> !important;
          border-color:<?php print $sidebar_button_border; ?> !important;
          background-color:<?php print $sidebar_button_background; ?> !important;
        }

        #sidebar .widget-title, #sidebar .widget-title a
        {
          color:<?php print $sidebar_session_title; ?> !important;
          background-color:<?php print $sidebar_session_background; ?> !important;
        }

        #footer
        {
          color:<?php print $footer_text; ?>;
          background-color:<?php print $footer_background; ?>;
        }

        #footer a
        {
          color:<?php print $footer_link; ?>;
        }
      </style>
    <?php
  }

  /************************************************************************************************
    Menu
  ************************************************************************************************/
  function painter_custom_colors()
  {
    add_theme_page(__("Custom colors", "painter"), __("Custom colors", "painter"), "edit_themes", basename(__FILE__), array(&$this, 'admin_color_style'));
  }

  /************************************************************************************************
    Scripts
  ************************************************************************************************/
  function painter_custom_colors_scripts()
  {
    // Load the required js on the correct pages
    if('custom-colors.php' !== $_GET['page'])
      return false;
    
    // scripts
    wp_enqueue_script('jquery');
    wp_enqueue_style('farbtastic', get_bloginfo('stylesheet_directory') . '/css/jquery.farbtastic-1.2.css', array(), '1.2');
    wp_enqueue_script('farbtastic', get_bloginfo('stylesheet_directory') . '/js/jquery.farbtastic-1.2.js', array('jquery'), '1.2');
  }

  /************************************************************************************************
    Manage colors
  ************************************************************************************************/
  function admin_color_style()
  {
    // Save the color scheme
    if(!empty($_POST['action'])) :
      
      foreach($this->color_schemes[$this->default_color] as $key => $value)
        $color_scheme[$key] = $_POST[$key];
      
      update_option("painter_color_scheme", $color_scheme);
      
      printf("<div style='background-color:rgb(207, 235, 247);' id='message' class='updated fade'><p><strong>%s</strong></p></div>", __("Colors updated", "painter"));
    endif;
    
    // Recover the color scheme
    $color_scheme = get_option('painter_color_scheme');
    
    // If theres no color scheme, pick the default
    if(!is_array($color_scheme))
      $color_scheme = $this->color_schemes[$this->default_color];
    
    // Extract colors
    extract($color_scheme);
    
    // Form
    ?>
      <script type="text/javascript">
        jQuery(function() {
          // Load the existent color schemes
          <?php foreach($this->color_schemes as $color_name => $preview) : ?>
            <?php $colors = ""; ?>
            <?php foreach($preview as $color) : if(!empty($colors)) $colors .= ', '; $colors .= "'{$color}'"; endforeach; ?>
            var <?php print $color_name; ?> = new Array(<?php print $colors; ?>);
          <?php endforeach; ?>
          
          // Load the selected color scheme
          jQuery("select[@name = 'color_scheme']").change(function(){
            var color = eval(jQuery(this).val());
            
            for(var i = 0; i < color.length; i++)
              jQuery('.wrap input.color:eq('+ i +')').val(color[i]).css({ 'backgroundColor': color[i] }); //jQuery.farbtastic('.wrap input.color:eq('+ i +')').updateValue(color[i]);
              
          });
          
          // Show the color picker
          jQuery('.color').focus(function(){
            jQuery(this).siblings('.color_picker').show();
          });
          
          // Hide the color picker
          jQuery('.color').blur(function(){
            jQuery(this).siblings('.color_picker').hide();
          });
        });
      </script>
      
      <div class="wrap">
        <h2><?php _e('Custom colors', 'painter'); ?></h2>
        
        <div class="tablenav">
          <div class="alignleft actions">
            <select name="color_scheme">
              <option><?php _e('Load a pre-defined color scheme', 'painter'); ?></option>
              <?php foreach($this->color_schemes as $color_name => $preview) : ?>
                <option><?php print $color_name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        
        <form method="post" action="">
          
          <?php foreach($this->parts as $title => $part) : ?>
            <h3><?php _e($title, 'painter'); ?></h3>
            
            <table class="form-table">
              <tbody>
                <?php foreach($part as $key => $values) : ?>
                  <script type="text/javascript">jQuery(function(){ jQuery('#<?php print $key; ?>_picker').farbtastic('#<?php print $key; ?>'); });</script>
                  <tr valign="top">
                    <th scope="row"><label for="<?php print $key; ?>"><?php _e("{$values['title']}", "painter"); ?></label></th>
                    <td>
                      <input type="text" id="<?php print $key; ?>" name="<?php print $key; ?>" value="<?php print (empty($$key)) ? " " : $$key; ?>" maxlength="7" class="small-text color">
                      <div id="<?php print $key; ?>_picker" class="color_picker" style="z-index:100; background:#eee; border:1px solid #ccc; position:absolute; display:none; margin-left:100px;"></div>
                      <span class="setting-description"><?php _e("{$values['description']}", "painter"); ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            
            <p class="submit">
              <input type="submit" name="action" class="button-primary" value="<?php _e('Save'); ?>" />
            </p>
          <?php endforeach; ?>
          
        </form>
        
      </div>
    <?php
  }
  
  // CONSTRUCTOR //////////////////////////////////////////////////////////////////////////////////
  /************************************************************************************************
    Custom colors constructor
  ************************************************************************************************/
  function custom_colors()
  {
    // ativar as cores personalizadas
    add_action('wp_head', array(&$this, 'color_style'));
    add_action('admin_menu', array(&$this, 'painter_custom_colors'));
    add_action('init', array(&$this, 'painter_custom_colors_scripts'));
  }
  
  // DESTRUCTOR ///////////////////////////////////////////////////////////////////////////////////
  
}

$custom_colors = new custom_colors();

?>
