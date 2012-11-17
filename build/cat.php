#!/usr/bin/env php
<?php
// This script reads the build configuration file, which groups JS and CSS
// files by key (the grouping can be arbitrary), and concatenates and outputs
// the contents of each file in the group. 
//
// Usage: 
// 	php cat.php [build-key]
//	php cat.php js-lib|js-app|css-app

$root = dirname(dirname(__FILE__));

$build = require($root.DIRECTORY_SEPARATOR.'build'.DIRECTORY_SEPARATOR.'build.php');

$key = $argv[1]; 
if(isset($build[$key])) {
	list($asset_type, $asset_group) = explode('-', $key, 2);
} else {
	die("Invalid build key: $key");
}

echo_comment("### build ### $key ### " . date('r'));
foreach($build[$key] as $file_name) {
	echo_comment("### file ### $file_name");

	$file_path = $root.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'webroot'.DIRECTORY_SEPARATOR.$asset_type.DIRECTORY_SEPARATOR.$file_name;
	if(readfile($file_path) === FALSE) {
		die("*** BUILD ERROR: file [$file_name] ***");
	}
}

function echo_comment($str = '') {
	global $asset_type;
	if($asset_type === 'css') {
		echo "\n/* $str */\n";
	} else if($asset_type === 'js') {
		echo "\n// $str\n";
	} else {
		die("Unknown asset type for printing comment");
	}
}
