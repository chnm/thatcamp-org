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
		var $longname	= 'Shortcode any widget - insert widgets or widget areas into a page.';
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
			echo '<h3><a href="http://wordpress.org/plugins/amr-shortcode-any-widget/">More detailed instructions at the wordpress plugin page.</a></h3>';
			echo '<ul>';
			echo '<li>';
			_e('Drag the widgets you want to use to the shortcodes sidebar.');

			echo '</li>';
			echo '<li>';
			_e('Set the widgets parameters if there are any.');
			echo '</li>';
			echo '<li>';
			_e('You could test the widgets out in a displayable widget area (sidebar/footer), then drag them to the widgets_for_shortcodes sidebar.');

			echo '</li>';
			echo '<li>';
			echo '<a title="Go to widget area" href="'.get_admin_url('','widgets.php').'"> ';
			_e('Go to widgets');
			echo '</a>';
			echo '</li>';
			echo '</ul>';
			
			echo '<h2>';
			_e('To add a widget area - all widgets in the widget area:');

			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.add_query_arg('content','[do_widget_area]', get_admin_url('','post-new.php?post_type=page'))
			.'"> ';
			_e('Create a page with do_widget_area shortcode without the widget_area class'); 
			echo '</a> Hoping to avoid theme styling.';
			echo '</li>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.add_query_arg('content','[do_widget_area widget_area_class=none]', get_admin_url('','post-new.php?post_type=page'))
			.'"> ';
			_e('Create a page with do_widget_area shortcode'); 
			echo '</a> Hoping to use theme styling.';
			echo '</li>';
			echo '<li>';
			_e('Examples:');
			echo '</li>';
			echo '<li>';
			_e('[do_widget_area] or [do_widget_area widget_area=sidebar-1]');
			echo '</li>';
			echo '<li>';
			_e('NB: Using something like the twenty-fourteen theme? you might end up with white text on a white background.  Tweak the widget classes or the html of the wrap or title. If that fails, adjust your css.');
			echo '</li>';
			echo '</ul>';
			echo '<br />';

			echo '<h2>';
			_e('To add a single widget to a page');
			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			_e('Add the shortcode [do_widget widgetname] to a page.');
			_e('Examples:');
			echo '</li>';
			echo '<li>';
			_e('[do_widget "tag cloud"] or [do_widget id=widgetid]');
			echo '</li>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.add_query_arg('content','[do_widget Archives widget_classes=none]', get_admin_url('','post-new.php?post_type=page'))
			.'"> ';
			_e('Create a page with do_widget shortcode and remove widget_classes'); 
			echo '</a>';
			echo '</li>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.add_query_arg('content','[do_widget Archives]', get_admin_url('','post-new.php?post_type=page'))
			.'"> ';
			_e('Create a page with do_widget shortcode'); 
			echo '</a>';
			echo '</li>';
			echo '<li>';
			echo 'Use title=false to hide a widget title. ';
			echo 'Use title=somehtmltag and wrap=somehtmltag  to change the html used.';
			echo ' Use class=yourclassname to add a class - maybe to override your themes widget styling?';
			echo '</li>';
			echo '<li>';
			echo '[do_widget pages title=false]  will hide the widget title';
			echo '</li>';
			echo '<li>';
			echo '[do_widget pages title=h3]  give the title a heading 3 html tag.';
			echo '</li>';
			echo '<li>';
			echo '[do_widget categories] or [do_widget name=categories] will do the same thing: display the categories widget.';
			echo '</li>';
			echo '<li>';
			echo '[do_widget "tag cloud" wrap=aside]   will wrap the widget in an "aside" html tag.';
			echo '</li>';
			echo '<li>';
			echo '[do_widget "recent posts"]';
			echo '</li>';
			echo '</ul>';

			echo '<p>';
			echo 'If the plugin cannot work out what you want, it will show a debug prompt
, click on the debug prompt and look for the name or id of your widget in the shortcodes sidebar (you may have to scroll through a lot of debug info). If the name does not work, try with the id.   Sometimes the widget name that wordpress calls it internally is not the same as what you see on the screen and you will need the debug to find the id.';
			echo '</p><p><b>';
			echo "Valid title html tags are : h1, h2, h3, h4, h5, header, strong, em ";
			echo '</b></p><p><b>';
			echo "Valid html wrap tags are : div, p, aside , section";
			echo '</b></p>';

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