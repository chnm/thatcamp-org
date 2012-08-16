<?php 

if(function_exists('register_sidebar'))
{
	$theme = $KreativeTheme->config->item('themename');
	
	register_sidebar(array(
        'before_widget' => '<div class="widgets">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="sideheading">',
        'after_title' => '</h2>',
    ));
}