<?php

$kt =& get_instance();

$kt_counter = array (
	'0' => 'Select a number:'
);

for ($i = 1; $i <= 20; $i++ ) :
	$kt_counter[$i] = $i;
endfor;

$options= array (
	'general' => array (
		array (
			"name" => "Site Logo",
			"desc" => "Replace your site title with a logo, please provide a full path of the image including http://",
			"id" => "site_logo",
			"standard" => "",
			"type" => "text",
			"class" => "long"
		),
		array (
			"name" => "Cufón",
			"desc" => "Enable Cufón Fast text replacement",
			"id" => "enable_cufon",
			"standard" => "false",
			"type" => "checkbox"
		),
		array (
			"name" => "Analytics",
			"desc" => "Please paste your Google Analytics (or other) tracking code here.",
			"id" => "analytics",
			"standard" => "",
			"type" => "textarea",
			"class" => "long"
		),
		array (
			"name" => "Theme Stylesheet",
			"desc" => "Please select your colour scheme here.",
			"id" => "alt_stylesheet",
			"standard" => $kt->config->item('default_stylesheet', 'defaults'),
			"type" => "select",
			"options" => $kt->config->item('stylesheet', 'defaults'),
			"class" => "medium"
		)
	),
	'nav' => array (
		array (
			"name" => "Menu Home Link",
			"desc" => "Display a home link in the category menu.",
			"id" => "home_link",
			"standard" => "true",
			"type" => "checkbox"
		), 
		array (
			"name" => "Menu Home Link Description",
			"desc" => "Enter the text to use as home link.",
			"id" => "home_link_text",
			"standard" => "Home",
			"type" => "text",
			"class" => "medium"
		), 
		array (
			"name" => "Menu Home Link Description",
			"desc" => "Add a description to show under your home link, or leave blank to disable.",
			"id" => "home_link_desc",
			"standard" => "",
			"type" => "text",
			"class" => "long"
		)
	),
	'layout' => array (
		array (
			"name" => "Featured Category",
			"desc" => "Select the category to use with your Featured Widget listed in the Home Page.",
			"id" => "featured_category",
			"standard" => "0",
			"type" => "select_wpcat",
			"class" => "medium",
			"args" => array ()
		),
		array (
			"name" => "Number of Featured Post",
			"desc" => "Select total featured post you to be listed.",
			"id" => "featured_total",
			"standard" => "0",
			"type" => "select",
			"options" => $kt_counter,
			"class" => "medium",
			"args" => array ()
		)
	),
	'optimize' => array (
		array (
			"name" => "jQuery Source",
			"desc" => "Select whether you want to load jQuery from local host or Google CDN.",
			"id" => "jquery_source",
			"standard" => "0",
			"type" => "select",
			"options" => array (
				'0' => 'Local',
				'cdn-google' => 'Google CDN'
			),
			"class" => "medium",
			"args" => array ()
		)
	)
);