<?php
/**
 * This config file defines the order for for combining and compressing 
 * application js.
 * 
 * It is used in the following places:
 * 		1) Makefile in this directory for building/minifying.
 * 		2) Cakephp Jstest plugin (i.e. qunit)
 *
 * To check file syntax: 
 * 		php -l build.php 
 **/

return array(
	// package of 3rd party libraries and extensions/plugins
	'lib' => array(
		'lib/jquery.js',
		'lib/jquery.dataTables.js',
		'lib/bootstrap.js',
		'lib/underscore.js',
		'lib/backbone.js',
		'lib/moment.js',
		'lib/video.js',
		'app/lib/video.rangeslider.js'
	),
	// package of application
	'app' => array(
		'app/core.js',
		'app/utils.js',
		'app/models.js',
		'app/views.js',
		'app/scripts.js'
	)
);
