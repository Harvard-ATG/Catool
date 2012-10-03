<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Helper for dealing with Targets.
 */
class TargetLinkHelper extends AppHelper {
	public $helpers = array('Html');
	public static $controller_for = array();
	public static $icon_for = array('video' => 'film');

/**
 * Look up the controller name for a Target.type. The
 * controller name should always be the plural form of the
 * type in lowercase. 
 *
 * Since we don't want to hard code the
 * types ahead of time, we'll compute and then cache the
 * result for later lookups.
 *
 * @param string $type the Target.type 
 * @return string
 */
	public function controllerName($type = '') {
		if(isset(self::$controller_for[$type])) {
			return self::$controller_for[$type];
		}

		$controller = Inflector::pluralize(strtolower($type));
		self::$controller_for[$type] = $controller;

		return $controller;
	}

/**
 * Look up the icon for a Target.type.
 *
 * @param string $type the Target.type
 * @return string
 */
	public function iconClass($type = '') {
		return self::$icon_for[strtolower($type)];
	}
}
