<?php
global $photonic_setup_options, $photonic_generic_options;

$photonic_generic_options = array(
	array("name" => "Generic settings",
		"desc" => "Control generic settings for the plugin",
		"category" => "generic-settings",
		"type" => "section",),

	array("name" => "Custom Shortcode",
		"desc" => "By default Photonic uses the <code>gallery</code> shortcode, so that your galleries stay safe if you stop using Photonic.
			But your theme or other plugins might be using the same shortcode too. In such a case define an explicit shortcode,
			and only this shortcode will show Photonic galleries",
		"id" => "alternative_shortcode",
		"grouping" => "generic-settings",
		"type" => "text",
		"std" => ""),

	array("name" => "Inbuilt Lightbox libraries",
		"desc" => "Photonic lets you choose from the following JS libraries for Lightbox effects:",
		"id" => "slideshow_library",
		"grouping" => "generic-settings",
		"type" => "radio",
		"options" => array(
			"fancybox" => "<a href='http://fancybox.net/'>FancyBox</a> &ndash; ~16KB: Released under MIT and GPL licenses.",
			"colorbox" => "<a href='http://colorpowered.com/colorbox/'>Colorbox</a> &ndash; ~10KB: Released under the MIT license",
			"prettyphoto" => "<a href='http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/'>PrettyPhoto</a> &ndash; ~23KB: Released under the GPL v2.0 license",
			"thickbox" => "Thickbox &ndash; ~12KB: Released under the MIT license",
			"none" => "None",
			"custom" => "Non-bundled (You have to provide the JS and CSS links)",
		),
		"std" => "fancybox"),

	array("name" => "Non-bundled Lightbox libraries",
		"desc" => "If you don't like the above libraries, you can try one of the following. These are not distributed with the theme for various reasons,
			predominant being licensing restrictions. <strong>Photonic doesn't support installation of these scripts</strong>. If you want to use them,
			you will need to specify their JS and CSS files in subsequent options, unless they come bundled with your theme.
			<em>Currently only FancyBox2 (the responsive version of FancyBox) is supported</em>.",
		"id" => "custom_lightbox",
		"grouping" => "generic-settings",
		"type" => "radio",
		"options" => array(
			"fancybox2" => "<a href='http://fancyapps.com/fancybox/'>FancyBox 2</a>",
//			"pirobox" => "<a href='http://www.pirolab.it/pirobox/'>Pirobox</a>",
		),
		"std" => "fancybox2"),

	array("name" => "Non-bundled Lightbox JS",
		"desc" => "If you have chosen a custom lightbox library from the above, enter the full URLs of the JS files for each of them.
			<strong>Please enter one URL per line</strong>. Note that your URL should start with <code>http://...</code> and you should be able to visit that entry in a browser",
		"id" => "custom_lightbox_js",
		"grouping" => "generic-settings",
		"type" => "textarea",
		"std" => ""),

	array("name" => "Custom Lightbox CSS",
		"desc" => "If you have chosen a custom lightbox library from the above, enter the full URLs of the CSS files for each of them.
			<strong>Please enter one URL per line</strong>. Note that your URL should start with <code>http://...</code> and you should be able to visit that entry in a browser",
		"id" => "custom_lightbox_css",
		"grouping" => "generic-settings",
		"type" => "textarea",
		"std" => ""),

	array("name" => "Slideshow mode",
		"desc" => "Selecting this will make your images launch in a slideshow mode automatically upon clicking",
		"id" => "slideshow_mode",
		"grouping" => "generic-settings",
		"type" => "checkbox",
		"std" => ""
	),

	array("name" => "Slideshow interval",
		"desc" => "If slideshows are on, this will control the interval between slides.",
		"id" => "slideshow_interval",
		"grouping" => "generic-settings",
		"type" => "text",
		"std" => "5000",
		"hint" => "Please enter a time in milliseconds",
	),

/*	array("name" => "Carousel mode",
		"desc" => "Selecting this will make your thumbnails on the page display in a carousel. This doesn't impact thumbnails displayed in a popup.",
		"id" => "carousel_mode",
		"grouping" => "generic-settings",
		"type" => "checkbox",
		"std" => ""
	),*/

	array("name" => "Nested Shortcodes in parameters",
		"desc" => "Allow parameters of the gallery shortcode to use shortcodes themselves",
		"id" => "nested_shortcodes",
		"grouping" => "generic-settings",
		"type" => "checkbox",
		"std" => ""
	),

	array("name" => "External Link Handling",
		"desc" => "Let the links to external sites (like Flickr or Instagram) open in a new tab/window.",
		"id" => "external_links_in_new_tab",
		"grouping" => "generic-settings",
		"type" => "checkbox",
		"std" => ""
	),

	array("name" => "Default Gallery Type",
		"desc" => "If no gallery type is specified, the following selection will be used:",
		"id" => "default_gallery_type",
		"grouping" => "generic-settings",
		"type" => "radio",
		"options" => array(
			"default" => "WordPress Galleries",
			"flickr" => "Flickr",
			"picasa" => "Picasa",
			"smugmug" => "SmugMug",
			"500px" => "500px.com",
			"instagram" => "Instagram",
		),
		"std" => "default"
	),

	array("name" => "Layouts",
		"desc" => "Set up your layouts",
		"category" => "layout-settings",
		"type" => "section",),

	array("name" => "Archive View Thumbnails",
		"desc" => "How many images do you want to show per gallery at the most on archive views (e.g. Blog page, Category, Date, Tag or Author views)? All thumbnails will be visible when the post is viewed in full.",
		"id" => "archive_thumbs",
		"grouping" => "layout-settings",
		"type" => "text",
		"std" => "",
		"hint" => "Leave blank or 0 to not restrict the number",
	),

	array("name" => "Link to see remaining photos",
		"desc" => "Hide the button to show remaining photos from the archive page",
		"id" => "archive_link_more",
		"grouping" => "layout-settings",
		"type" => "checkbox",
		"std" => "",
	),

	array("name" => "Native WP Galleries",
		"desc" => "Control settings for native WP gallieries, invoked by <code>[gallery id='abc']</code>",
		"category" => "wp-settings",
		"type" => "section",),

	array("name" => "Alignment of image in slideshow",
		"desc" => "If you pass the <code>style</code> parameter to the <code>gallery</code> shortcode and the style is <code>strip-above</code>, <code>strip-below</code> or <code>no-strip</code> the image in the slide will be centered if you select this.",
		"id" => "wp_slide_align",
		"grouping" => "wp-settings",
		"type" => "checkbox",
		"std" => ""
	),

	array("name" => "Thumbnail Title Display",
		"desc" => "How do you want the title of the Thumbnails displayed?",
		"id" => "wp_thumbnail_title_display",
		"grouping" => "wp-settings",
		"type" => "select",
		"options" => array(
			"regular" => "Normal title display using the HTML \"title\" attribute",
			"below" => "Below the thumbnail",
			"tooltip" => "Using the <a href='http://bassistance.de/jquery-plugins/jquery-plugin-tooltip/'>JQuery Tooltip</a> plugin",
		),
		"std" => "tooltip"),

	array("name" => "Lightbox Library settings",
		"desc" => "Control settings for the JS libraries distributed with the theme",
		"category" => "fbox-settings",
		"type" => "section",),

	array("name" => "Position of title in FancyBox slideshow",
		"desc" => "Fancybox lets you show the title of the image in different positions. Where do you want it?",
		"id" => "fbox_title_position",
		"grouping" => "fbox-settings",
		"type" => "radio",
		"options" => array(
			"outside" => "Outside the slide box",
			"inside" => "Inside the slide box",
			"over" => "Over the image in the slide box",
		),
		"std" => "inside"),

	array("name" => "Colorbox Theme",
		"desc" => "Colorbox lets you pick one of the following themes:",
		"id" => "cbox_theme",
		"grouping" => "fbox-settings",
		"type" => "radio",
		"options" => array(
			"1" => "Default",
			"2" => "Style 2",
			"3" => "Style 3",
			"4" => "Style 4",
			"5" => "Style 5",
			"theme" => "Use a skin defined in your theme (requires the files to be present in the <code>scripts/colorbox</code> folder within your theme directory).",
		),
		"std" => "1"),

	array("name" => "PrettyPhoto Theme",
		"desc" => "PrettyPhoto lets you pick one of the following themes:",
		"id" => "pphoto_theme",
		"grouping" => "fbox-settings",
		"type" => "radio",
		"options" => array(
			"pp_default" => "Default",
			"light_rounded" => "Light Rounded",
			"dark_rounded" => "Dark Rounded",
			"light_square" => "Light Square",
			"dark_square" => "Dark Square",
			"facebook" => "Facebook",
		),
		"std" => "pp_default"),

	array("name" => "Popup Panel",
		"desc" => "Control settings for popup panel",
		"category" => "photos-pop",
		"type" => "section",),

	array("name" => "What is this section?",
		"desc" => "Options in this section are in effect when you click on a Photoset/album thumbnail to launch an overlaid gallery.",
		"grouping" => "photos-pop",
		"type" => "blurb",),

	array("name" => "Overlaid (popup) Gallery Panel Width",
		"desc" => "When you click on a gallery (particularly for Flickr), it launches a panel on top of your page. What is the width you want to assign to this gallery?",
		"id" => "gallery_panel_width",
		"grouping" => "photos-pop",
		"type" => "text",
		"hint" => "Enter the number of pixels here (don't enter 'px').",
		"std" => "800"),

	array("name" => "Overlaid (popup) Gallery Panel background",
		"desc" => "Setup the background of the overlaid gallery (popup).",
		"id" => "flickr_gallery_panel_background",
		"grouping" => "photos-pop",
		"type" => "background",
		"options" => array(),
		"std" => array("color" => '#111111', "image" => "", "trans" => "0",
			"position" => "top left", "repeat" => "repeat", "attachment" => "scroll", "colortype" => "custom")),

	array("name" => "Overlaid (popup) Gallery Border",
		"desc" => "Setup the border of overlaid gallery (popup).",
		"id" => "flickr_set_popup_thumb_border",
		"grouping" => "photos-pop",
		"type" => "border",
		"options" => array(),
		"std" => array(
			'top' => array('colortype' => 'custom', 'color' => '#333333', 'style' => 'solid', 'border-width' => 1, 'border-width-type' => 'px'),
			'right' => array('colortype' => 'custom', 'color' => '#333333', 'style' => 'solid', 'border-width' => 1, 'border-width-type' => 'px'),
			'bottom' => array('colortype' => 'custom', 'color' => '#333333', 'style' => 'solid', 'border-width' => 1, 'border-width-type' => 'px'),
			'left' => array('colortype' => 'custom', 'color' => '#333333', 'style' => 'solid', 'border-width' => 1, 'border-width-type' => 'px'),
		),
	),

	array("name" => "Overlaid Gallery Panel number of items",
		"desc" => "How many thumbnails do you want to show in a gallery panel? The extra thumbnails can be accessed by previous and next links",
		"id" => "gallery_panel_items",
		"grouping" => "photos-pop",
		"type" => "slider",
		"options" => array("range" => "min", "min" => 1, "max" => 100, "step" => 1, "size" => "400px", "unit" => ""),
		"std" => "20"),
);

?>