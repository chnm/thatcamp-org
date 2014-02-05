<?php
$whitelist = array(
	'fancybox' => 'include/scripts/jquery.fancybox-1.3.4.pack.js',
	'colorbox' => 'include/scripts/jquery.colorbox.js',
	'prettyphoto' => 'include/scripts/jquery.prettyPhoto-min.js',
	'cycle' => 'include/scripts/jquery.cycle.all.min.js',
	'photonic' => 'include/scripts/photonic.js',
);

$modules = array();
if (isset($_GET['modules'])) {
	$modules = explode(',', $_GET['modules']);
}

if (!in_array('cycle', $modules)) {
	$modules[] = 'cycle';
}
if (!in_array('photonic', $modules)) {
	$modules[] = 'photonic';
}

$files = array();
foreach ($modules as $module) {
	if (array_key_exists($module, $whitelist)) {
		$files[] = $whitelist[$module];
	}
}

header("Content-type: application/x-javascript");
header("Cache-Control: must-revalidate");
$offset = 1209600 ;
$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($ExpStr);

foreach ($files as $file) {
	// connect to handle a file
	$file_handler = fopen($file, "r");

	// read the contents
	$contents = fread($file_handler, filesize($file));

	// close the file
	fclose($file_handler);

	echo $contents;
}