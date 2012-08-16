<?php
/**
 * @package Storify-Embed
 * @version 0.1
 */
/*
Plugin Name: Storify Embed
Plugin URI: http://wordpress.org/extend/plugins/storify-embed/
Description: Embed Storify streams in blog posts just as you would YouTube videos or Flickr photos
Author: Flaming Tarball
Version: 0.1
Author URI: http://flamingtarball.com/
*/

function storify_embed_the_content($content) {
	$ex = "/\<p\>(https?\:\/\/(?:www\.)?storify\.com\/(?:[^\/]+)\/(?:[^\/]+))\/?\<\/p\>/i";
	$replacement = '<script type="text/javascript" src="${1}.js"></script>';
	
	return preg_replace($ex, $replacement, $content);
}

add_filter('the_content', 'storify_embed_the_content');