<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * TagCollectionTagFixture
 *
 */
class TagCollectionTagFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'TagCollectionTag';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'tag_collection_id' => 1,
			'tag_id' => 1
		),
		array(
			'id' => 2,
			'tag_collection_id' => 1,
			'tag_id' => 2
		),
		array(
			'id' => 3,
			'tag_collection_id' => 1,
			'tag_id' => 3
		),
		array(
			'id' => 4,
			'tag_collection_id' => 2,
			'tag_id' => 1
		),
		array(
			'id' => 5,
			'tag_collection_id' => 2,
			'tag_id' => 2
		),
		array(
			'id' => 6,
			'tag_collection_id' => 2,
			'tag_id' => 3
		),
		array(
			'id' => 7,
			'tag_collection_id' => 3,
			'tag_id' => 4
		),
		array(
			'id' => 8,
			'tag_collection_id' => 3,
			'tag_id' => 5
		),
		array(
			'id' => 9,
			'tag_collection_id' => 4,
			'tag_id' => 5
		)
	);
}
