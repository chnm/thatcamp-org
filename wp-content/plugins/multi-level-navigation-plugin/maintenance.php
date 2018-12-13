<?php
/****** Maintenance Mode *********/
session_start();

// Turns ON test CSS. Used for bug fixing to check if CSS used is
if ($_REQUEST['mln'] == 'testcss_on') {$_SESSION['mln_testcss'] = 'on';}
// Turns OFF test CSS. Used for bug fixing to check if CSS used is
if ($_REQUEST['mln'] == 'testcss_off') {$_SESSION['mln_testcss'] = '';}

/*
// Turns keyboard accessibility on and off
if ($_REQUEST['mln'] == 'keyboard_on') {update_option('suckerfish_keyboard', 'on');}
elseif ($_REQUEST['mln'] == 'keyboard_off') {update_option('suckerfish_keyboard', '');}

// Sets Superfish speed
if ($_REQUEST['mln'] == 'superfish_instant') {update_option('suckerfish_superfish_speed', 'instant');}
elseif ($_REQUEST['mln'] == 'superfish_fast') {update_option('suckerfish_superfish_speed', 'fast');}
elseif ($_REQUEST['mln'] == 'superfish_normal') {update_option('suckerfish_superfish_speed', 'normal');}
elseif ($_REQUEST['mln'] == 'superfish_slow') {update_option('suckerfish_superfish_speed', 'slow');}

// Disable and enable built in CSS file
if ($_REQUEST['mln'] == 'disablecss_on') {update_option('suckerfish_disablecss', 'on');}
elseif ($_REQUEST['mln'] == 'disablecss_off') {update_option('suckerfish_disablecss', '');}

// Turns inline CSS on and off
if ($_REQUEST['mln'] == 'inlinecss_on') {update_option('suckerfish_inlinecss', 'on');}
elseif ($_REQUEST['mln'] == 'inlinecss_off') {update_option('suckerfish_inlinecss', '');}
*/

// If menu manually turned off while in maintenance mode then switches off plugin
if ($_REQUEST['mln'] == 'off') {$_SESSION['mln'] = '';}

// If menu manually activated while in maintenance mode then loads plugin
if ($_REQUEST['mln'] == 'on' || $_SESSION['mln'] == 'on') {$_SESSION['mln'] = 'on';require('core.php');}

?>
