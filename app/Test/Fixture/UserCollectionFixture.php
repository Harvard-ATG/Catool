<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * UserCollectionFixture
 *
 */
class UserCollectionFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'UserCollection';
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'collection_id' => 1,
			'role_id' => 2,
		),
		array(
			'id' => 2,
			'user_id' => 2,
			'collection_id' => 1,
			'role_id' => 2
		),
		array(
			'id' => 3,
			'user_id' => 3,
			'collection_id' => 1,
			'role_id' => 3
		),
		array(
			'id' => 4,
			'user_id' => 4,
			'collection_id' => 1,
			'role_id' => 4
		),
		array(
			'id' => 5,
			'user_id' => 2,
			'collection_id' => 2,
			'role_id' => 4
		)
	);
}
