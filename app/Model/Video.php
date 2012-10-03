<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('Target', 'Model');

/**
 * User Model
 *
 * @package       app.Model
 */
class Video extends Target {

/**
 * useTable
 *
 * @var string
 */
	public $useTable = 'targets';
/**
 * targetType
 *
 * @var string
 */
	public $targetType = 'video';

}

