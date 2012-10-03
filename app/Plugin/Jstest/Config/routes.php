<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * Add a shortcute route so the test runner can be accessed via the url:
 *
 * URL: /qunit
 */

Router::connect('/qunit', array(
	'plugin' => 'jstest', 
	'controller' => 'jstests', 
	'action' => 'run'
));

