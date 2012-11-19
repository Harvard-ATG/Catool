<?php
/**
 * This config file defines the order for for combining and compressing 
 * application assets.
 * 
 * To check file syntax: 
 * 		php -l build.php 
 **/

return array(
	// javascript 3rd party libraries, extensions, etc
	'js-lib' => array(
		'lib/jquery.js',
		'lib/jquery-ui-1.9.1.custom.js',
		'lib/jquery.tagit.js',
		'lib/jquery.dataTables.js',
		'lib/wysihtml5/parser_rules/advanced.js',
		'lib/wysihtml5-0.3.0.js',
		'lib/underscore.js',
		'lib/backbone.js',
		'lib/moment.js',
		'lib/video.js',
		'app/lib/video.rangeslider.js',
		'lib/bootstrap.js',
		'lib/bootstrap-wysihtml5.js'
	),
	// javascript custom app libraries
	'js-app' => array(
		'app/core.js',
		'app/settings.js',
		'app/utils.js',
		'app/models.js',
		'app/views.js',
		'app/scripts.js'
	),
	// css files 
	'css-app' => array(
		'jquery-ui/jquery-ui-1.9.1.custom.css',
		'jquery.dataTables.css',
		'jquery.tagit.css',
		'video-js.css',
		'video-js.rangeslider.css',
		'bootstrap-wysihtml5.css',
		'bootstrap.css',
		'bootstrap-responsive.css'
	)
);
