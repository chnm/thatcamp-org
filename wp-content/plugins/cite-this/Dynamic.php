<?php
/*
 * Copyright 2007, 2008 Yu-Jie Lin
 * 
 * This file is part of Cite this.
 * 
 * Cite this is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 * 
 * Cite this is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * Author       : Yu-Jie Lin
 * Email        : lb08 AT livibetter DOT com
 * Website      : http://www.livibetter.com
 * Creation Date: 7/19/2007 01:09 UTC+8
 */

function CTerrorPage($msg) {
	header('Content-Type: text/html; charset=' . get_option('blog_charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="<?php echo get_option('blog_charset') ?>" />
	<title>Unable to get the citation texts!</title>
	<link rel="stylesheet" href="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/CiteThis/CiteThis.css" type="text/css"/>
</head>
<body>
	<div class="citations">
		<p>Unable to get the citation texts!</p>
<?php if ($msg) echo '<p>', $msg, '</p>'; ?>
		<p>Visit <a href="<?php echo CT_WEBSITE ?>">Cite This project's website</a>.</p>
	</div>
</body>
</html>
<?php
	}

// Get variables
global $post;
if (!isset($post)) {
	$postID = intval($_GET['p']);
	$post = get_post($postID);
	}

if ($post === null || $post->post_status != 'publish' || !empty($post->post_password)) {
	// There is no such post or no permission to check.
	CTerrorPage();
	die('');
	}

$postID = $post->ID;
$isFull =  $_GET['mode'] == 'full';

if ($isFull) {
	header('Content-Type: text/html; charset=' . get_option('blog_charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php echo get_option('blog_charset') ?>" />
<title>Citations styles for "<?php echo $post->post_title ?>"</title>
<link rel="stylesheet" href="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/CiteThis/CiteThis.css" type="text/css"/>
</head>
<body>
<?php
	}

$styles = null;			
$postMeta = get_post_meta($post->ID, 'CT', true); // Only first 'CT' affects
if ($postMeta!='') {
	// overriding styles
	$options = array();
	parse_str($postMeta, $options);
	if (array_key_exists('styles', $options))
		$styles = explode(',', $options['styles']);
	}

$details = GetDetails($post);
$citations = GetCitations($details, $styles);
$html = GetCitationsHTML($citations);

if ($isFull)
	echo "<div class='citations'>$html</div>";
else
	echo "jQuery('#citations-$postID').empty(); jQuery('$html').appendTo(jQuery('#citations-$postID'));";

if ($isFull)
	echo '</body></html>';
?>
