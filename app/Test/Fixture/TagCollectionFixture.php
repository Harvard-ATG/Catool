<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * TagCollectionFixture
 *
 */
class TagCollectionFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'TagCollection';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1, 
			'instance_count' => 0,
			'tag_count' => 3,
			'hash' => '6b8d87d1ce485d5aa360dded97e0609dd3d0287c'
		),
		array(
			'id' => 2,
			'instance_count' => 1,
			'tag_count' => 3,
			'hash' => '6b8d87d1ce485d5aa360dded97e0609dd3d0287c'
		),
		array(
			'id' => 3,
			'instance_count' => 0,
			'tag_count' => 1,
			'hash' => '0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33'
		)
	);
}
