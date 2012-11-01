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
			'hash' => '7a9b4cf81dbc165538c09738d6066a7b2aad31d0'
		),
		array(
			'id' => 2,
			'instance_count' => 1,
			'tag_count' => 2,
			'hash' => 'b1cb774dc4534166b52d4c22d6cd85c1dec54bdc'
		),
		array(
			'id' => 3,
			'instance_count' => 1,
			'tag_count' => 2,
			'hash' => '76731c481cfc8d9a8c43abd4327388a2fe091c7e'
		),
		array(
			'id' => 4,
			'instance_count' => 1,
			'tag_count' => 1,
			'hash' => 'd172ff6d331e021724a8deeeccb9bfc4efb77417'
		),
	);
}
