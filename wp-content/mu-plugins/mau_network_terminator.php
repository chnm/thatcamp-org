<?php
/*
Plugin Name: Network Terminator
Plugin URI: http://wphug.com/plugins/network-terminator/
Description: Simple WordPress plugin to bulk add terms to taxonomies across the mutisite network.
Version: 0.0.1
Author: Mau
Author URI: http://wphug.com/
License: GPL2
*/

/*
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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
*/

	// add the class to WP
	add_action( 'plugins_loaded', 'mau_network_terminator_init' );
	function mau_network_terminator_init() {                                                           
	    new MauNetworkTerminator();   
	}         

	class MauNetworkTerminator {
	
		var $prefix = 'mau_network_terminator';
		var $title = 'Network Terminator';
		var $ver = '0.0.1';
	
	    function __construct() {
   			# Add a menu for our option page
			add_action('admin_menu', array( $this, 'add_settings_page' ) );
			# Register and define the settings
			add_action('admin_init', array( $this, 'admin_init' ) ); 
			

	    }


		/**
		* Add a menu for our option page
		*
		*/
		function add_settings_page() {
			add_submenu_page( 'tools.php', $this->title, $this->title, 'manage_categories', $this->prefix, array($this, 'option_page') );
		}
		
		/**
		* Draw the option page & process form submission
		*
		*/
		function option_page() {
				
				// no multisite no fun
				if ( !is_multisite() ) {
					echo '
					<div class="wrap" >
					<h2>This is not a multisite WordPress installation</h2>
					<p><em>Sorry, but this plugin makes sense only in the multisite environment.<br/>It feels sad and lonely here and will not do anything useful for you.<br/> Please come back with multisite enabled.</em></p>
					</div></div>';
				} else {
				
			
				
				// process form submission
				if ( isset($_POST['Submit']) ) {
				
					$input_error_flag = 0;	
					
					// check input
					if ( empty($_POST['sites']) || !is_array($_POST['sites']) )
						$input_error_flag = 1;
					else						
						$sites = array_map( 'absint', $_POST['sites'] );
						
					if ( empty($_POST['taxonomies']) || !is_array($_POST['taxonomies']) )
						$input_error_flag = 2;
					else
						$taxonomies = $_POST['taxonomies'];
					
					$test_run = ( $_POST['Submit'] == 'TEST RUN' ) ? true : false;
					
					// check intention
					
					// TODO
					
					// prepare input
					$terms_to_add = array();
					foreach ($taxonomies as $tax => $terms) {
						if ( !empty($terms) )						
							$terms_to_add[$tax] = array_map( 'trim', explode(',',$terms) );
					}
					if ( empty($terms_to_add) || !is_array($terms_to_add) )
						$input_error_flag = 3;
					
					// terminator in action!
					if ($input_error_flag == 0)
						$log = $this->mau_add_network_terms($terms_to_add, $sites, $test_run); 	
						
				} ?>

				<style>.notice {color: red;} #ntlog li {padding-left:10px;} div.error,div.updated{padding: 5px;text-align: center;} #results {margin-top: 20px;border-top:1px dashed #dadada;} #results h2{padding-top: 10px;} a.reset-form {position: relative;top:-20px;left: 235px;} .wrap input{padding: 10px;} .nt-add-terms{font-weight: bold;} .nt-submit{cursor: pointer;}</style>

				<div class="wrap" id="nt-top" >
					<?php screen_icon(); ?>
					<h2><?php echo $this->title; ?></h2>
					
					<?php
					if ( isset($input_error_flag) && $input_error_flag > 0 )
						echo "<div class='error'>Uh-oh! Network Terminator doesn't understand you. Please check your input.</div>";
					elseif ( isset($input_error_flag) && $input_error_flag == 0)
						echo "<div class='updated'>Job finished. Please check the <a href='".admin_url( 'tools.php?page='.$this->prefix )."#results'>Results</a> below &darr;</div>";
					?>
					
					<form action="tools.php?page=<?php echo $this->prefix; ?>" method="post">
						<?php settings_fields($this->prefix.'_options'); ?>
						<?php do_settings_sections($this->prefix); ?>
						You can do a 
						<input class="nt-submit" name="Submit" type="submit" value="TEST RUN" /> 
						to preview the results.
						<br/><br/>
						<b>When you are ready press</b> 
						<input class="nt-submit nt-add-terms" name="Submit" type="submit" value="ADD TERMS" />
					</form>
					
					<?php if ( !empty($log) ) echo '<div id="results"><h2>Results:</h2>'.$log.'</div><a href="#nt-top">Back to top &uarr;</a>'; ?>
					
				</div>
				<?php
			}		
		}
		
		/**
		* Register and define the settings
		*
		*/
		function admin_init(){
			register_setting(
				$this->prefix.'_options',
				$this->prefix.'_options'
			);
			add_settings_section(
				$this->prefix.'_main',
				'Add terms to your network',
				array( $this, 'section_text_main' ) ,
				$this->prefix
			);
			$setting_sites_text = '<p><em>'.__('Please choose which sites in your network will be affected by this plugin.',$this->prefix).'</em></p>';
			add_settings_field(
				$this->prefix.'_setting_sites',
				$setting_sites_text,
				array( $this, 'setting_sites' ) ,
				$this->prefix,
				$this->prefix.'_main'
			);
			$setting_taxonomies_text = '<p><em>'.__('Enter the terms you want to add separated by commas.',$this->prefix).'</em></p><p><em>Example:<br/><small>cat, dog, string theory, horse </small></em></p><p><em>Be aware there is no limit to the number of terms added. Use with care.</em></p>';
			add_settings_field(
				$this->prefix.'_setting_taxonomies',
				$setting_taxonomies_text,
				array( $this, 'setting_taxonomies' ) ,
				$this->prefix,
				$this->prefix.'_main'
			);

		}
		
		/** 
		* Draw the section header
		*
		*/
		function section_text_main() {
			$out ='';
			echo $out;
		}

		
		/** 
		* Display checkbox for each site in the network.
		*
		*/
		function setting_sites() {
			global $wpdb;
			
			// get an array of blog ids
			$sql = "SELECT blog_id FROM $wpdb->blogs 
				WHERE archived = '0' AND mature = '0' 
				AND spam = '0' AND deleted = '0' ";
			$blogs = $wpdb->get_col( $wpdb->prepare( $sql ) );
			
			// check user submitted data
			$sites_input = ( isset($_POST['sites']) && is_array($_POST['sites']) ) ? array_map('absint',$_POST['sites']) : array();
			
			if ( is_array( $blogs ) ) {
				echo '<p>';
				//loop through the site IDs
				foreach ($blogs as $blog) {
					//display each site as an checkbox
					$checked = (in_array($blog,$sites_input)) ? 'checked':'';
					echo '<input type="checkbox" name="sites[]" value="' .$blog. '" '.$checked.'/> ';
					echo get_blog_details( $blog )->blogname. '<br/>';
				}
				echo '</p>';
			}
		}
		
		/*
		* Display text input with label for each available taxonomy
		*
		*/
		function setting_taxonomies() {
			$taxonomies=get_taxonomies(array('public' => true),'objects'); 
			unset($taxonomies['post_format']); // we don't want to mess with this
			echo '<p>';
			foreach ($taxonomies as $tax) {
				$tax_input = isset($_POST['taxonomies'][$tax->name]) ? esc_attr($_POST['taxonomies'][$tax->name]) : '';
				echo '<label for="'.$tax->name.'">'.$tax->labels->name.'</label><br/>';
				echo '<input name="taxonomies['.$tax->name.']" type="text" value="'.$tax_input.'" size="50"/><br/><br/>';
				}
			echo '</p>';
			echo '<a class="reset-form" href="'. admin_url( 'tools.php?page='.$this->prefix ).'" >reset form</a>';
		}
		
		
		
		
		/**
		* Add network terms
		*
		* Hey, This is where the party is!
		*
		* @param array $terms_to_add
		* @param array $siteids
		* @param bool $testrun
		*
		* @return string list formatted log | errors
		*/
		function mau_add_network_terms($terms_to_add, $siteids, $testrun = false) {
		
			// check if this is multisite install
			if ( !is_multisite() )
				return 'This is not a multisite WordPress installation.';
		
			// very basic input check
			if ( empty($terms_to_add) || empty($siteids) || !is_array($terms_to_add) || !is_array($siteids) )
				return 'Nah, I eat only arrays!';

			if ($testrun) $log = '<p><em>No need to get excited. This is just a test run.</em></p>';
			else $log = '';

			// loop thru blogs
			foreach ($siteids as $blog_id) :
				
				switch_to_blog( absint($blog_id) );
				
				$log .= '<h4>'.get_blog_details(  $blog_id  )->blogname.':</h4>';
				$log .= '<ul id="ntlog">';
				
				// loop thru taxonomies
				foreach ( $terms_to_add as $taxonomy => $terms ) {
				
					// check if taxonomy exists
					if ( taxonomy_exists($taxonomy) ) {
						// get taxonomy name
						$tax_name = get_taxonomy($taxonomy);
						$tax_name = $tax_name->labels->name;
						
						//loop thru terms	
						foreach ( $terms as $term ) {
							
							// check if term exists
							if ( term_exists($term, $taxonomy) ) {
								$log .= "<li class='notice' ><em>$term already exists in the $tax_name taxonomy - not added!</em></li>";
								
							} else {
								
								// if it doesn't exist insert the $term to $taxonomy
								$term = strip_tags($term);
								$taxonomy = strip_tags($taxonomy);
								if (!$testrun)
									wp_insert_term( $term, $taxonomy );
								$log .= "<li><b>$term</b> successfully added to the <b>$tax_name</b> taxonomy</li>"; 
							}
						}
					} else {
						// tell our log that taxonomy doesn't exists
						$log .= "<li class='notice'><em>The $tax_name taxonomy doesn't exist! Skipping...</em></li>"; 
					}
				}
			
				$log .= '</ul>';	
			
				// we're done here
				restore_current_blog();
				
			endforeach;
			if ($testrun) $log .= '<p><em>No need to get excited. This was just the test run.</em></p>';
			return $log;
		}

		
}	// end of class MauPlugin

?>