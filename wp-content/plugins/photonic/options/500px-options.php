<?php
global $photonic_500px_options;

$photonic_500px_options = array(
	array("name" => "500px settings",
		"desc" => "Control settings for 500px",
		"category" => "500px-settings",
		"type" => "section",),

	array("name" => "500px API Consumer Key",
		"desc" => "To make use of the 500px functionality you have to use your 500px API Consumer Key.
							You can <a href='http://developers.500px.com/oauth_clients'>register an application and obtain a key online</a> if you don't have one.
							Note that you are responsible for following all of the 500px API's <a href='http://developer.500px.com/docs/terms'>Terms of Service</a> and the users' copyright.",
		"id" => "500px_api_key",
		"grouping" => "500px-settings",
		"type" => "text",
		"std" => ""),

	array("name" => "500px API Consumer Secret",
		"desc" => "You have to enter the Customer Secret provided by 500px after you have registered your application.",
		"id" => "500px_api_secret",
		"grouping" => "500px-settings",
		"type" => "text",
		"std" => ""),

	array("name" => "Private Photos",
		"desc" => "Let visitors of your site login to 500px.com to see private photos for which they have permissions (will show a login button if they are not logged in)",
		"id" => "500px_allow_oauth",
		"grouping" => "500px-settings",
		"type" => "checkbox",
		"std" => ""),

	array("name" => "Login Box Text",
		"desc" => "If private photos are enabled, this is the text users will see before the login button (you can use HTML tags here)",
		"id" => "500px_login_box",
		"grouping" => "500px-settings",
		"type" => "textarea",
		"std" => "Some features that you are trying to access may be visible to logged in users of 500px.com only. Please login if you want to see them."),

	array("name" => "Login Button Text",
		"desc" => "If private photos are enabled, this is the text users will see before the login button (you can use HTML tags other than &lt;a&gt; here)",
		"id" => "500px_login_button",
		"grouping" => "500px-settings",
		"type" => "text",
		"std" => "Login"),

	array("name" => "500px Photos",
		"desc" => "Control settings for 500px Photos when displayed in your page",
		"category" => "500px-photos",
		"type" => "section",),

	array("name" => "What is this section?",
		"desc" => "Options in this section are in effect when you use the shortcode format <code>[gallery type='500px' feature='xyz']</code>.",
		"grouping" => "500px-photos",
		"type" => "blurb",),

	array("name" => "Photo Title Display",
		"desc" => "How do you want the title of the photos?",
		"id" => "500px_photo_title_display",
		"grouping" => "500px-photos",
		"type" => "select",
		"options" => array(
			"regular" => "Normal title display using the HTML \"title\" attribute",
			"below" => "Below the thumbnail",
			"tooltip" => "Using the <a href='http://bassistance.de/jquery-plugins/jquery-plugin-tooltip/'>JQuery Tooltip</a> plugin",
		),
		"std" => "tooltip"),

	array("name" => "Disable popup linking",
		"desc" => "Check this to disable linking the photo title in the popup to the original photo page on 500px.com.",
		"id" => "500px_disable_title_link",
		"grouping" => "500px-photos",
		"type" => "checkbox",
		"std" => ""),

	array("name" => "Constrain Photos Per Row",
		"desc" => "How do you want the control the number of photo thumbnails per row? This can be overridden by adding the '<code>columns</code>' parameter to the '<code>gallery</code>' shortcode.",
		"id" => "500px_photos_per_row_constraint",
		"grouping" => "500px-photos",
		"type" => "select",
		"options" => array("padding" => "Fix the padding around the thumbnails",
			"count" => "Fix the number of thumbnails per row",
		),
		"std" => "padding"),

	array("name" => "Constrain by padding",
		"desc" => " If you have constrained by padding above, enter the number of pixels here to pad the thumbs by",
		"id" => "500px_photos_constrain_by_padding",
		"grouping" => "500px-photos",
		"type" => "text",
		"hint" => "Enter the number of pixels here (don't enter 'px'). Non-integers will be ignored.",
		"std" => "15"),

	array("name" => "Constrain by number of thumbnails",
		"desc" => " If you have constrained by number of thumbnails per row above, enter the number of thumbnails",
		"id" => "500px_photos_constrain_by_count",
		"grouping" => "500px-photos",
		"type" => "slider",
		"options" => array("range" => "min", "min" => 1, "max" => 25, "step" => 1, "size" => "400px", "unit" => ""),
		"std" => 5),

	array("name" => "Photo Thumbnail Border",
		"desc" => "Setup the border of photo thumbnail when the photo is displayed as a part of a photoset or in a photo-stream. This is valid for the short-code usage <code>[gallery type='500px' photoset_id='xyz']</code>, or <code>[gallery type='500px' user_id='abc' view='photos']</code>.",
		"id" => "500px_photo_thumb_border",
		"grouping" => "500px-photos",
		"type" => "border",
		"options" => array(),
		"std" => array(
			'top' => array('colortype' => 'transparent', 'color' => '#c0c0c0', 'style' => 'none', 'border-width' => 0, 'border-width-type' => 'px'),
			'right' => array('colortype' => 'transparent', 'color' => '#c0c0c0', 'style' => 'none', 'border-width' => 0, 'border-width-type' => 'px'),
			'bottom' => array('colortype' => 'custom', 'color' => '#c0c0c0', 'style' => 'none', 'border-width' => 1, 'border-width-type' => 'px'),
			'left' => array('colortype' => 'transparent', 'color' => '#c0c0c0', 'style' => 'none', 'border-width' => 0, 'border-width-type' => 'px'),
		),
	),

	array("name" => "Photo Thumbnail - Padding between border and image",
		"desc" => "Setup the padding between the photo thumbnail and its border.",
		"id" => "500px_photo_thumb_padding",
		"grouping" => "500px-photos",
		"type" => "padding",
		"options" => array(),
		"std" => array(
			'top' => array('padding' => 0, 'padding-type' => 'px'),
			'right' => array('padding' => 0, 'padding-type' => 'px'),
			'bottom' => array('padding' => 0, 'padding-type' => 'px'),
			'left' => array('padding' => 0, 'padding-type' => 'px'),
		),
	),

);
?>