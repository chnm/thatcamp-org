<?php
/**
 * Backend Class for use in all amr plugins
 * Version 0.1
 */

//------------------------------------------------------------------------------------------------------------------	
if (!class_exists('amr_saw_plugin_admin')) {
	class amr_saw_plugin_admin {
		var $hook 		= 'amr_saw';
		var $filename	= 'amr_shortcode_any_widget/amr_shortcode_any_widget.php';
		var $longname	= 'Shortcode any widget';
		var $shortname	= 'Shortcode any widget';
		var $optionname = '';
		var $homepage	= '';
		var $parent_slug = 'plugin_listings_menu';
		var $accesslvl	= 'manage_options';
		
		function amr_saw_plugin_admin() {  
			add_action('admin_menu', array(&$this, 'register_settings_page') );
			add_filter('plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );			
		}		
		function register_settings_page() {
			add_options_page( $this->longname, $this->shortname, $this->accesslvl, $this->hook, array(&$this,'config_page'));
		}		
		function plugin_options_url() {
			return admin_url( 'options-general.php?page='.$this->hook );
		}		
		/**
		 * Add a link to the settings page to the plugins list
		 */
		function add_action_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) 
				$this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}

		function admin_heading($title)  {
		echo '<div class="wrap" >
			<div id="icon-options-general" class="icon32"><br />
			</div>
			<h2>'.$title.' </h2>';
	//		<form method="post" action="'
	//		.esc_url($_SERVER['PHP_SELF'])
	//		.'">';
	//		wp_nonce_field($this->hook); /* outputs hidden field */
	//		;
		}

		function admin_subheading($title)  {
			echo '<h2>'.$title.'</h2>';
		}		
		function config_page() {
			$this->admin_heading($this->longname); 
			echo '<ul>';
			echo '<li>';
			_e('Drag the widgets you want to use to the shortcodes sidebar.');

			echo '</li>';
			echo '<li>';
			_e('Set the widgets parameters if there are any.');
			echo '</li>';
			echo '<li>';
			_e('You could test them out in a displayable sidebar, then drag them to the shortcodes sidebar.');

			echo '</li>';
			echo '<li>';
			echo '<a title="Go to widget area" href="'.get_admin_url('','widgets.php').'"> ';
			_e('Go to widgets');
			echo '</a>';
			echo '</li>';
			echo '<li>';
			_e('Then add the shortcode [do_widget widgetname] to a page.');
			echo '</li>';
			echo '<li>';
			_e('Examples:[do_widget "tag cloud"] or [do_widget id=widgetid]');
			echo '</li>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.add_query_arg('content','[do_widget Archives]', get_admin_url('','post-new.php?post_type=page'))
			.'"> ';
			_e('Create the page'); 
			echo '</a>';
			echo '</li>';
			echo '<li>';
			echo 'You can add as many of these on one page as you like.  Use title=false to switch off the title.';
			echo '</li>';
			echo '<li>';
			echo '[do_widget pages title=false]';
			echo '</li>';
			echo '<li>';
			echo '[do_widget categories]';
			echo '</li>';
			echo '<li>';
			echo '[do_widget "tag cloud"]';
			echo '</li>';
			echo '<li>';
			echo '[do_widget "recent posts"]';
			echo '</li>';
			echo '<li>';
			echo '<li>';
			echo 'If the plugin cannot work out what you want, it will show a debug prompt
, click on the debug prompt and look for the name or id of your widget in the shortcodes sidebar (you may have to scroll through a lot of debug info). If the name does not work, try with the id.   Sometimes the widget name that wordpress calls it internally is not the same as what you see on the screen and you will need the debug to find the id.';
			echo '</li>';
			echo '<li>';
			echo '</ul>';
		}		

		
		
		/**
		 * Create a Checkbox input field
		 */
		function radiobutton($id, $label, $value, $selected) {
			$sel = checked($value,$selected, false); 
			return "<input type='radio' id='".$id."' name='".$id."' value='".$value."'"
			. $sel."/>&nbsp;".$label."<br />";
		}	
		/**
		 * Create a Checkbox input field
		 */
		function checkbox($id, $label, $value) {
			return '<input type="checkbox" id="'.$id.'" name="'.$id.'"'. checked($value,true,false).'/>&nbsp;<label for="'.$id.'">'.$label.'</label><br/>';
		}
		/**
		 * Create a Dropdown input field
		 */
		function dropdown($id, $label, $options, $selected) {
//			
			$html = '<label for="'.$id.'">'.$label.':</label><br/>'
			.'<select id=\''.$id.'\' name=\''.$id.'\'>';
			foreach ($options as $i => $option) {
//				
				$sel = selected($i, $selected, false); //wordpress function returns with single quotes, not double 
				$html .= '<OPTION '.$sel.' label=\''.$option.'\' value=\''.$i.'\'>'.$option.'</OPTION>';
			}
			$html .= '</select>';
			return ($html);
		}			
		/**
		 * Create a Text input field
		 */
		function textinput($id, $label, $value, $length='45') {
			return '<label for="'.$id.'">'.$label.':</label><br/><input size="'
			.$length.'" type="text" id="'.$id.'" name="'.$id.'" value="'.$value.'"/><br/><br/>';
		}
				/**
		 * Create a Text area field
		 */
		function textarea($id, $label, $value, $cols='45', $rows='10') {
			return '<label for="'.$id.'">'.$label.':</label><br/>'
			.'<textarea rows="'.$rows.'" cols="'.$cols
			.'" id="'.$id.'" name="'.$id.'"/>'.$value.'</TEXTAREA><br/><br/>';
		}
		/**
		 * Create a postbox widget
		 */
		function postbox($id, $title, $content) {
		?>
			<div id="<?php echo $id; ?>" class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span><?php echo $title; ?></span></h3>
				<div class="inside">
					<?php echo $content; ?>
				</div>
			</div>
		<?php
		}	
		/**
		 * Create a form table from an array of rows
		 */
		function form_table($rows) { //  array of rows () id, label, desc, content 
			$content = '<table class="form-table">';
			foreach ($rows as $row) {
				$content .= '<tr><th valign="top" scrope="row">';
				if (isset($row['id']) && $row['id'] != '')
					$content .= '<label for="'.$row['id'].'">'.$row['label'].':</label>';
				else
					$content .= $row['label'];
				if (isset($row['desc']) && $row['desc'] != '')
					$content .= '<br/><small>'.$row['desc'].'</small>';
				$content .= '</th><td valign="top">';
				$content .= $row['content'];
				$content .= '</td></tr>'; 
			}
			$content .= '</table>';
			return $content;
		}
	
		/**
		 * Info box with link to the support forums.
		 */
		function plugin_support() {
			$content = '<p>'.__('If you have any problems with this plugin or good ideas for improvements or new features, please talk about them in the','amrplugin').' <a href="http://wordpress.org/tags/'.$this->hook.'">'.__("Support forums",'amrplugin').'</a>.</p>';
			$this->postbox($this->hook.'support', 'Need support?', $content);
		}

		function text_limit( $text, $limit, $finish = ' [&hellip;]') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}
	}
}

?>