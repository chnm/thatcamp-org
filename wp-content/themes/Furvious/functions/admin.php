<?php

function kreative_page_menu() 
{
	global $_wp_real_parent_file;
	
	$kt =& get_instance();
	$name = $kt->config->item('themename', 'defaults');
	
	add_menu_page(__('KreativeThemes: ' . $name . ' Theme'), __('KreativeThemes'), 6, 'kreative_page_general', 'kreative_page_general');
	
	add_submenu_page('kreative_page_general', __('General Settings'), __('General'), 6, 'kreative_page_general', 'kreative_page_general');
	add_submenu_page('kreative_page_general', __('Navigation Settings'), __('Navigation'), 6, 'kreative_page_nav', 'kreative_page_nav');
	add_submenu_page('kreative_page_general', __('Layout Options'), __('Layout'), 6, 'kreative_page_layout', 'kreative_page_layout');
	add_submenu_page('kreative_page_general', __('Optimization Settings'), __('Optimization'), 6, 'kreative_page_optimize', 'kreative_page_optimize');
	add_submenu_page('kreative_page_general', __('Widgets Settings'), __('Widgets'), 6, 'widgets.php', '');
}

function kreative_init_option($options = NULL)
{
	global $functions;
	
	$kt =& get_instance();
	
	if ($options == NULL) :
		$options = ((isset($_GET['options']) && trim($_GET['options']) !== '') ? $_GET['options'] : 'general');
	endif;
	
	include_once ($functions . 'admin_' . $options . '.php');
}

function kreative_page_general() 
{
	kreative_init_option('general');
}
function kreative_page_nav() 
{
	kreative_init_option('nav');
}
function kreative_page_layout() 
{
	kreative_init_option('layout');
}
function kreative_page_optimize() 
{
	kreative_init_option('optimize');
}

add_action('admin_menu', 'kreative_page_menu');