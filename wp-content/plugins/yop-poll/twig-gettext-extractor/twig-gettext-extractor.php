#!/usr/bin/env php
<?php

	/**
	* This file is part of the Twig Gettext utility.
	*
	*  (c) Саша Стаменковић <umpirsky@gmail.com>
	*
	* For the full copyright and license information, please view the LICENSE
	* file that was distributed with this source code.
	*/

	/**
	* Extracts translations from twig templates.
	*
	* @author Саша Стаменковић <umpirsky@gmail.com>
	*/
	define ( 'YOP_POLL_PLUGIN_PATH', rtrim( dirname( dirname( dirname( __FILE__ ) ) ), '/'). PATH_SEPARATOR );
	define ( 'YOP_POLL_PATH', rtrim( dirname( dirname( __FILE__ ) ), '/') . '/' );

	require_once (YOP_POLL_PATH . 'lib/Twig/AutoloaderYOP.php' );
	Yop_Twig_Autoloader::register();

	require_once YOP_POLL_PATH . 'twig-gettext-extractor/composer/autoload_real.php';
	ComposerAutoloaderInit031aac423df037c03110742e23a42464::getLoader();

	$twig = new Twig_Environment(new Twig\Gettext\Loader\Filesystem('/'), array(
		'cache'       => '/tmp/cache/'.uniqid(),
		'auto_reload' => true
	));

	$twig->addExtension( new Twig_Extension_YopPoll() );

	array_shift($_SERVER['argv']);
	$addTemplate = false;

	$extractor = new Twig\Gettext\Extractor($twig);
	foreach ($_SERVER['argv'] as $arg) {
		if ('--files' == $arg) {
			$addTemplate = true;
		} else if ($addTemplate) {
			
			$extractor->addTemplate( $arg );
		} else {
			$extractor->addGettextParameter( $arg );
		}
	}

	$extractor->extract();
