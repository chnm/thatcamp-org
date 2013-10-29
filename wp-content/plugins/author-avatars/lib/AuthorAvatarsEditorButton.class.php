<?php
/**
 * This class adds a button to the post editor for inserting author avatars
 * shortcodes
 */

class AuthorAvatarsEditorButton {
    /**
      * Constructor
      */
    function AuthorAvatarsEditorButton() {
        $this->register();
    }

   /**
      * Register init function
      */

    function register() {
		add_action('admin_init', array( &$this, 'init' ), 30);
		add_action('wp_ajax_author-avatars-editor-popup', array(&$this, 'render_tinymce_popup'));
    }
    /**
      * Register button filters and actions
      */

    function init() {
		global $pagenow;
		// we need to test that we are in the admin section bewfore we add the button to tinyMCE
		if ($pagenow != 'index.php'){
			// Don't bother adding the button if the current user lacks permissions
			if ( current_user_can('edit_posts') || current_user_can('edit_pages') ) {
				// Add only in Rich Editor mode
				if ( get_user_option('rich_editing') == 'true') {
					add_filter('mce_external_plugins', array(&$this, 'add_tinymce_plugin'));
					add_filter('mce_buttons', array(&$this, 'add_tinymce_button'));
				}
	
				
	
				// In wordpress < 2.7 only the POST parameter "action" is used in admin-ajax.php 
				// but we're using the GET parameter for the tinymce popup iframe, therefore manually
				// set the POST parameter for the popup calls.
				if (defined('DOING_AJAX') && DOING_AJAX == true) {
					$p = 'author-avatars-editor-popup';
					$action = isset($_GET['action']) ? $_GET['action'] : null;
					if ($action == $p && !isset($_POST['action'])) {
						$_POST['action'] = $action;
					}
				}
			}
		}
    }



    /**
      * Filter 'mce_external_plugins': add the authoravatars tinymce plugin
      */

    function add_tinymce_plugin($plugin_array) {
        $plugin_array['authoravatars'] = WP_PLUGIN_URL.'/author-avatars/tinymce/editor_plugin.js';
        return $plugin_array;
    }

    /**
      * Filter 'mce_buttons': add the authoravatars tinymce button
      */
    function add_tinymce_button($buttons) {
		array_push( $buttons, 'separator', 'authoravatars' );
        return $buttons;
    }

    /**
      * Renders the tinymce editor popup
      */
    function render_tinymce_popup() {
		echo '<html xmlns="http://www.w3.org/1999/xhtml">' ."\n";
		$this->render_tinymce_popup_head(); echo "\n";
        $this->render_tinymce_popup_body(); echo "\n";
        echo '</html>';
		exit();
    }

    /**
      * Builds the html head for the tinymce popup
      *
      * @access private
      * @return string Popup head
      */

    function render_tinymce_popup_head() {
        echo '<head>';
        echo "\n\t".'<title>'. __('Author avatars shortcodes', 'author-avatars') . '</title>';
        echo "\n\t".'<meta http-equiv="Content-Type" content="'. get_bloginfo('html_type').'; charset='. get_option('blog_charset').'" />';
        wp_print_scripts(array('jquery', 'jquery-ui-resizable', 'tinymce-popup', 'author-avatars-tinymce-popup'));
		wp_print_styles(array('admin-form'));
		echo "\n".'</head>';
    }

    /**
	 * Builds the html body for the tinymce popup
	 *
	 * @access private
	 * @return string Popup body
	 */

    function render_tinymce_popup_body() {
        require_once('AuthorAvatarsForm.class.php');
        $form = new AuthorAvatarsForm();
		// BASIC TAB
		$basic_left  = $form->renderFieldShortcodeType();
		$basic_left .= '<div class="fields_type_show_avatar">';
		$basic_left .= $form->renderFieldUsers();
		$basic_left .= $form->renderFieldEmail();
		$basic_left .= $form->renderFieldAlignment();
		$basic_left .= $form->renderFieldDisplayOptions();
		$basic_left .= $form->renderFieldUserLink('');
		$basic_left .= '</div>';
		$basic_left .= '<div class="fields_type_authoravatars">';
		$basic_left .= $form->renderFieldRoles(array('administrator', 'editor'));
		$basic_left .= $form->renderFieldDisplayOptions();
		$basic_left .= $form->renderFieldUserLink('authorpage');
		$basic_left .= '</div>';
		$basic_right = $form->renderFieldAvatarSize();
		$basic_tab  = $form->renderTabStart(__('Basic', 'author-avatars'));
		$basic_tab .= $form->renderColumns($basic_left, $basic_right);
		$basic_tab .= $form->renderTabEnd();

		// ADVANCED TAB
		$adv_left  = $form->renderFieldOrder('display_name');
		$adv_left .= $form->renderFieldSortDirection('asc');
		$adv_left .= $form->renderFieldLimit();
        $adv_left .= $form->renderPageLimit();
		$adv_left .= $form->renderFieldMinPostCount();
		$adv_left .= $form->renderFieldHiddenUsers();

		$adv_right = '';
		if (AA_is_wpmu()) {
			global $blog_id; // default value: current blog
			$adv_right .= $form->renderFieldBlogs($blog_id);
		}		
		$adv_right .= $form->renderFieldGroupBy();

		$advanced_tab  = $form->renderTabStart(__('Advanced', 'author-avatars'));
		$advanced_tab .= $form->renderColumns($adv_left, $adv_right);
		$advanced_tab .= $form->renderTabEnd();

		$tabs = $basic_tab . $advanced_tab;
		$html = '<div class="aa-tabs">'. $form->renderTabList() . $tabs .'</div>';
        $html .= "\n\t".'<div class="mceActionPanel"> '.AA_donateButton();
	    $html .= "\n\t".'<div style="float: left">';
	    $html .= "\n\t".'<input type="button" id="cancel" name="cancel" value="'. __("Cancel") .'" onclick="tinyMCEPopup.close();" />';
	    $html .= "\n\t".'</div>';
        $html .= "\n\t".'<div style="float: right">';
	    $html .= "\n\t".'<input type="submit" id="insert" name="insert" value="'. __("Insert") .'" onclick="insertAuthorAvatarsCode();" />';
	    $html .= "\n\t".'</div>';
        $html .= "\n\t".'</div>';		

        echo '<body class="tinymce_popup">'. $html . "\n" .'</body>';

    }

}

?>