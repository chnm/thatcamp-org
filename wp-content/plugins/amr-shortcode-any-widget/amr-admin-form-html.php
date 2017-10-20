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
		
		function __construct() {  
			add_action('admin_menu', array(&$this, 'register_settings_page') );
		}		
		
		function register_settings_page() {
			add_options_page( $this->longname, $this->shortname, $this->accesslvl, $this->hook, array(&$this,'config_page'));
		}		
		
		function plugin_options_url() {
			return admin_url( 'options-general.php?page='.$this->hook );
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
			$this->where_shortcode();
			
			echo '<h2>Help:</h2>';
			echo '<h3>More detailed instructions at the wordpress plugin <a target="_new" href="http://wordpress.org/plugins/amr-shortcode-any-widget/installation">installation and faq pages.</a></h3>';
			echo '<ol>';
			echo '<li>';
			_e('Test your widget in a normal sidebar first.', 'amr-shortcode-any-widget');
			echo ' <a title="Go to widget area" href="'.get_admin_url('','widgets.php').'">';
			_e('Go to widgets', 'amr-shortcode-any-widget');
						echo '</a>';
			echo '<li>';
			_e('Drag the widgets you want to use to the shortcodes sidebar.', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			_e('Add a do_widget or do_widget_area shortcode to a page.', 'amr-shortcode-any-widget');
			echo ' <a title="Create a page" href="'
			.get_admin_url('','post-new.php?post_type=page&content=&#91;do_widget Archives &#93;')
			.'">';
			_e('Create a page with example do_widget shortcode', 'amr-shortcode-any-widget'); 
			echo '</a>';
			echo '</li>';
			echo '</ol>';


			echo '<h2>';
			_e('To add a single widget to a page', 'amr-shortcode-any-widget');
			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			_e('Add the shortcode [do_widget widgetname] to a page:', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			echo '<li>';
			echo '[do_widget categories] or [do_widget name=categories] '; 
			echo '</li>';
			echo '<li>';
			_e('[do_widget "tag cloud"] or [do_widget id=widgetid]', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			echo 'To see a list of your widgets in their sidebars, add <b>?do_widget_debug</b> to the url of page with the do_widget shortcode.';
			echo '</li>';
			echo '</ul>';
			
			echo '<br />';
			echo '<h2>';
			_e('More advanced options:','amr-shortcode-any-widget');
			echo '</h2>';
			echo '<ul><li>';
			echo 'Use title=false to hide a widget title. ';
			echo '</li>';
			echo '<li>';
			echo '[do_widget pages title=false]  will hide the widget title';
			echo '</li></ul>';
			echo '<h3>';
			_e('To change the style, change the html:','amr-shortcode-any-widget');
			echo '</h3>';
			echo '<ul>';

			echo '<li>';
			echo 'Use title=somehtmltag and wrap=somehtmltag  to change the html used.  This may change how your theme\'s css affects the widget when it is in  page.  It all depends what what html selectors your theme uses.';
			echo '</li>';
			echo '<li>';
			echo ' Use class=yourclassname to add a class - maybe to override your themes widget styling?  Obviously you must have css that applies to that class.';
			echo '</li>';

			echo '<li>';
			echo '[do_widget pages title=h3]  give the title a heading 3 html tag.';
			echo '</li>';
			echo '<li>';
			echo '[do_widget "tag cloud" wrap=aside]   will wrap the widget in an "aside" html tag.';
			echo '</li>';

			echo '</ul>';

			echo '<h4>';
			echo "Valid title html tags are : </h4><ul>";
			echo '<li>h1</li>';
			echo '<li>h2</li>';
			echo '<li>h3</li>'; 
			echo '<li>h4</li>';
			echo '<li>h5</li>';
			echo '<li>header</li>';
			echo '<li>strong</li>';
			echo '<li>em</li>';
			echo '</ul>';

			echo "<h4>Valid html wrap tags are :</h4><ul>";
			echo '<li>div</li>';
			echo '<li>p</li>';
			echo '<li>aside</li>'; 
			echo '<li>section</li>';
			echo '</ul>';
						
			echo '<h2>';
			_e('To add multiple instances of the same widget:', 'amr-shortcode-any-widget');

			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			echo '[do_widget id=widgetid1] [do_widget id=widgetid2]';
			echo '</li>';
			echo '</ul>';
						
			echo '<h2>';
			_e('To add a widget area - all widgets in the widget area:', 'amr-shortcode-any-widget');

			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.get_admin_url('','post-new.php?post_type=page&content=&#91;do_widget_area&#93;')
			.'"> ';
			_e('Create a page with do_widget_area shortcode', 'amr-shortcode-any-widget'); 
			echo '</a> Hoping to use theme styling.';
			echo '</li>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.get_admin_url('',
			'post-new.php?post_type=page&content=&#91;do_widget_area widget_area_class=none&#93;')
			.'"> ';
			_e('Create a page with do_widget_area shortcode  without the widget_area class', 'amr-shortcode-any-widget'); 
			echo '</a> Hoping to avoid theme sidebar styling.';
			echo '</li>';
			echo '<li>';
			_e('Examples:', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			_e('[do_widget_area] or [do_widget_area widget_area=sidebar-1]', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			_e('NB: Using something like the twenty-fourteen theme? you might end up with white text on a white background.  Tweak the widget classes or the html of the wrap or title. If that fails, adjust your css.', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '</ul>';
			echo '<br />';
			

		}		

		function plugin_support() {
			$content = '<p>'.__('If you have any problems with this plugin or good ideas for improvements or new features, please talk about them in the','amr-shortcode-any-widget').' <a href="http://wordpress.org/tags/'.$this->hook.'">'.__("Support forums",'amr-shortcode-any-widget').'</a>.</p>';
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
		
		function where_shortcode() {
			global $wpdb;
			//$pattern = get_shortcode_regex(array('do_widget'));
			
			echo '<h2>'.__('This site is using do_widget shortcodes in the following:','amr-shortcode-any-widget').'</h2>';
			$results 	= array();
			$query  	= "SELECT * FROM $wpdb->posts WHERE post_status IN ( 'publish', 'future') and post_content LIKE '%[do_widget%]%' ORDER BY post_date DESC;" ;
			//$query  	= "SELECT * FROM $wpdb->posts WHERE post_status IN ( 'publish', 'future') and post_content REGEXP '".$pattern."' ORDER BY post_date DESC;" ;
			$results 	= $wpdb->get_results($query);

			echo '<table class="widefat wp-list-table striped"><thead><tr><th>';
			_e('Post');
			echo '</th><th>';
			_e('Published');
			echo '</th><th>';
			_e('Shortcodes');
			echo '</th></tr></thead><tbody>';
			foreach($results as $i => $result) {
				echo '<tr><td>';
				edit_post_link($result->post_title.' ',' ',' ',$result->ID);
				echo '</td><td>'.substr($result->post_date,0,11);
				if (!($result->post_status == 'publish')) _e($result->post_status);
				echo '</td><td>';
				
				preg_match_all("^\[(.*)\]^",$result->post_content,$matches, PREG_PATTERN_ORDER);
				
				foreach ($matches[0] as $j=> $m) {
					if (!empty($matches[1][$j])) {
						
						echo $m;
						echo '<br />';
					}						
				};
				echo '<td></tr>';
				
			}
			echo '</tbody></table>';
				

		}
			
			
		
	}
}

