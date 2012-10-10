<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * TargetSettingFixture
 *
 */
class TargetSettingFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'TargetSetting';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'lock_annotations' => 0,
			'lock_comments' => 0,
			'sync_annotations' => 0
		),
		array(
			'id' => 2,
			'lock_annotations' => 1,
			'lock_comments' => 0,
			'sync_annotations' => 0
		),
		array(
			'id' => 3,
			'lock_annotations' => 0,
			'lock_comments' => 1,
			'sync_annotations' => 0
		),
		array(
			'id' => 4,
			'lock_annotations' => 1,
			'lock_comments' => 1,
			'sync_annotations' => 0
		),
		array(
			'id' => 5,
			'lock_annotations' => 0,
			'lock_comments' => 0,
			'sync_annotations' => 1
		),
		array(
			'id' => 6,
			'lock_annotations' => 1,
			'lock_comments' => 0,
			'sync_annotations' => 1
		),
		array(
			'id' => 7,
			'lock_annotations' => 0,
			'lock_comments' => 1,
			'sync_annotations' => 1
		),
		array(
			'id' => 8,
			'lock_annotations' => 1,
			'lock_comments' => 1,
			'sync_annotations' => 1
		)
	);
}
