<?php
global $photonic_setup_options, $photonic_generic_options, $photonic_flickr_options, $photonic_picasa_options, $photonic_smugmug_options, $photonic_500px_options, $photonic_instagram_options, $photonic_zenfolio_options;

$photonic_setup_options = array();

require_once(plugin_dir_path(__FILE__) . "/generic-options.php");
foreach ($photonic_generic_options as $option) {
	$photonic_setup_options[] = $option;
}

require_once(plugin_dir_path(__FILE__) . "/flickr-options.php");
foreach ($photonic_flickr_options as $option) {
	$photonic_setup_options[] = $option;
}

require_once(plugin_dir_path(__FILE__) . "/picasa-options.php");
foreach ($photonic_picasa_options as $option) {
	$photonic_setup_options[] = $option;
}

require_once(plugin_dir_path(__FILE__) . "/500px-options.php");
foreach ($photonic_500px_options as $option) {
	$photonic_setup_options[] = $option;
}

require_once(plugin_dir_path(__FILE__) . "/smugmug-options.php");
foreach ($photonic_smugmug_options as $option) {
	$photonic_setup_options[] = $option;
}

require_once(plugin_dir_path(__FILE__) . "/instagram-options.php");
foreach ($photonic_instagram_options as $option) {
	$photonic_setup_options[] = $option;
}

require_once(plugin_dir_path(__FILE__) . "/zenfolio-options.php");
foreach ($photonic_zenfolio_options as $option) {
	$photonic_setup_options[] = $option;
}
