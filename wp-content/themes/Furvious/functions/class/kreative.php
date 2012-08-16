<?php

require_once ('configurator.php');

class Kreative {
	
	var $db = NULL;
	var $name = '';
	var $db_pre = NULL;
	var $config = NULL;
	var $style = '';
	
	function Kreative()
	{
		global $wpdb, $table_prefix;
		
		$this->db = $wpdb;
		$this->db_pre = $table_prefix;
		$this->config = new Configurator();
		
		$this->name = $this->config->item('themename');
		
		if (isset($_GET['kreativestyle']))
		{
			$set = $_GET['kreativestyle'];
			setcookie ("kreative_".strtolower($this->name), $set, time()+31536000);
			$this->style = $set;
		}
		
		$this->load_option();
	}
	
	function load_option()
	{
		$options = array (
			'general' => get_option('kreativetheme_general'),
			'nav' => get_option('kreativetheme_nav'),
			'layout' => get_option('kreativetheme_layout'),
			'optimize' => get_option('kreativetheme_optimize'),
			'ads' => get_option('kreativetheme_ads'),
			'plugs' => get_option('kreativetheme_plugs')
		);
		$defaults = array();
		
		foreach ($options as $opt => $val) 
		{
			if ( $val === FALSE)
			{
				$options[$opt] = $defaults;
			}
			else 
			{
				$options[$opt] = unserialize($val);
			}
			
			$this->config->set($opt, $options[$opt]);
		}
	}
	
	function siteNavigation($depth = 1)
	{
		$kt =& get_instance();
		
		$output = '';
		if (strtolower($kt->config->item('home_link', 'nav')) === 'true') 
		{
			$output .= '<li class="';
			if ( !! is_home()) 
			{
				$output .= 'current_page_item';
			}
			
			$output .= ' page_item"><a href="' . get_option('home') . '/" title="' . htmlentities($kt->config->item('home_link_desc', 'nav')) . '">' . $kt->config->item('home_link_text', 'nav') . '</a></li>';
		}
		$output .= wp_list_pages(array('echo' => 0, 'title_li' => '', 'depth' => $depth));
		
		echo $output;
	}
	
	function siteStyle()
	{
		$kt =& get_instance();
		$alt = $kt->config->item('alt_stylesheet', 'general');
		
		$available = $kt->config->item('stylesheet', 'defaults');
		$default = $kt->config->item('default_stylesheet', 'defaults');
		
		if (trim($kt->style) !== '')
		{
			$alt = $kt->style;
		}
		elseif (isset($_COOKIE["kreative_".strtolower($kt->name)]))
		{
			$alt = $_COOKIE["kreative_".strtolower($kt->name)];
		}
		
		echo '<link rel="stylesheet" href="';
		echo get_bloginfo('template_url');
		
		
			
		if ( array_key_exists(trim($alt), $available) && $alt !== $default)
		{
			echo '/style/' . $alt . '/style-' . $alt . '.css';
		}
		else {
			echo '/style-default.css';
		}
		echo '" type="text/css" media="screen" />';
	}
	
	function siteTitle()
	{
		$kt =& get_instance();
		$logo = $kt->config->item('site_logo', 'general');
		
		echo '<h1 id="blogtitle"><a href="' . get_bloginfo('url') . '/">';
		
		if (trim($logo) === '')
		{
			echo bloginfo('name');
		}
		else {
			echo '<img src="' . $logo . '" alt="' . get_bloginfo('name') . '" />';
		}
		
		echo '</a></h1>';
		
	}
}

global $KreativeTheme;
$KreativeTheme = new Kreative();

function &get_instance()
{
	global $KreativeTheme;
	
	return $KreativeTheme;
}

function kreative_get_settings($group = 'defaults', $setting, $standard = FALSE) 
{
	$kt =& get_instance();
	$item = $kt->config->item($setting, $group);
	if ( ! $item) $item = $standard; 
	
	return $item;
}
